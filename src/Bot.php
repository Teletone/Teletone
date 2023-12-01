<?php

namespace Teletone;

use GuzzleHttp\Client;
use Teletone\Types\ReplyKeyboardMarkup;
use Teletone\Types\InlineKeyboardMarkup;
use Teletone\Types\ReplyKeyboardRemove;
use Teletone\Types\ForceReply;
use Teletone\Types\InputFile;

class Bot
{
    /** @var string Telegram bot token */
    private $token;

    /** @var array Additional bot options */
    private $options;

    /** @var Router Router class */
    private $router;

    /** @var GuzzleHttp\Client */
    private $client;

    /** @var string Bot run type - polling or webhook */
    private $run_type;

    /**
     * Constructs a bot class
     * 
     * @param string $token     Telegram bot token
     * @param array $options    Additional bot options
     * - parse_mode = Mode for parsing entities in the message text
     * - debug = Set true to enable debug mode
     * - all_groups = Whether to allow processing of messages from chats by default, regardless of the for_groups parameter. Default is false
     */
    public function __construct($token, $options = [])
    {
        $this->token = $token;
        if (!isset($options['all_groups']))
            $options['all_groups'] = false;
        $this->options = $options;
        $this->client = new Client([
            'base_uri' => "https://api.telegram.org/bot{$token}/",
            'connect_timeout' => 10.0,
            'timeout' => 10.0
        ]);
        $this->router = new Router($this);
        if (php_sapi_name() == 'cli' || strpos(php_sapi_name(), 'cgi') !== false)
            $this->run_type = 'polling';
        else
            $this->run_type = 'webhook';
    }

    /**
     * Get a bot router
     * 
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Starts update polling and processing
     * 
     * @return NULL
     */
    public function polling()
    {
        $client = new Client();
        $offset = 0;
        while (true)
            $offset = $this->handleUpdates($offset);
    }

    public function setWebhook($url, $params = [])
    {
        return $this->_execute('setWebhook', array_merge([
            'url' => $url
        ], $params));
    }

    public function deleteWebhook($drop_pending_updates = false)
    {
        return $this->_execute('deleteWebhook', [
            'drop_pending_updates' => $drop_pending_updates
        ]);
    }

    /** Checks whether the IP address is in the address range. You can specify a mask */
    public function ipInNet($ip, $net)
    {
        $cidr_to_mask = static function($mask) {
            return long2ip(pow(2, 32)-pow(2, (32-$mask)));
        };
        $ip = ip2long($ip);
        $p = explode('/', $net);
        $ip_net = ip2long($p[0]);
        $ip_mask = (isset($p[1])) ? $p[1] : 0;
        $mask = ip2long($cidr_to_mask($ip_mask));
        if ($ip_mask == 0 && $ip == $ip_net)
            return true;
        if (($ip & $mask) == $ip_net)
            return true;
        else
            return false;
    }

    public function handleWebhook($check_ip = true, $ip = NULL)
    {
        if ($check_ip)
        {
            if (is_null($ip))
            {
                if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
                    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
                else
                    $ip = $_SERVER['REMOTE_ADDR'];
            }
            if (!$this->ipInNet($ip, '149.154.160.0/20') && !$this->ipInNet($ip, '91.108.4.0/22'))
            {
                $this->debug("IP $ip does not match trusted IPs");
                return false;
            }
        }
        $data = json_decode(file_get_contents('php://input'));
        if (!is_null($data))
            $this->router->_handle($data);
        return true;
    }

    public function debug($message)
    {
        if (isset($this->options['debug']) && $this->options['debug'])
        {
            if ($this->run_type == 'polling')
                echo $message."\n";
            if (!empty($this->options['debug_in_file']))
                $this->_log($message, $this->options['debug_in_file']);
        }
    }

    public function _log($text, $file)
    {
        file_put_contents($file, '['.date('Y-m-d H:i:s')."]\n".$text."\n\n", FILE_APPEND);
    }

    /*
     * Processes polling updates
     */
    public function handleUpdates($offset = 0, $drop = false)
    {
        $res = $this->client->post('getUpdates', [
            'form_params' => [
                'offset' => $offset
            ]
        ]);
        $data = json_decode($res->getBody()->getContents());
        if (!$drop)
            $this->debug('Updates received: '.count($data->result));
        if (!$drop && count($data->result) > 0)
        {
            foreach ($data->result as $item)
                $this->router->_handle($item);
        }
        if (count($data->result) > 0)
            $offset = $data->result[count($data->result)-1]->update_id + 1;
        return $offset;
    }

    /*
     * Discards all pending updates without processing
     */
    public function dropPendingUpdates()
    {
        $offset = $this->handleUpdates(0, true);
        $this->handleUpdates($offset, true);
    }

    /**
     * Processes updates and then drops them
     */
    public function handleUpdatesAndDrop()
    {
        $offset = $this->handleUpdates();
        $this->handleUpdates($offset, true);
    }

    /*
     * Magic method for processing requests in telegram api
     */
    public function __call($name, $arguments = [])
    {
        return $this->_execute($name, ...$arguments);
    }

    public function stdToArray($std)
    {
        return json_decode(json_encode($std), true);
    }

    private function toMultiPart($arr)
    {
        $result = [];
        array_walk($arr, function($value, $key) use(&$result) {
            $result[] = [ 'name' => $key, 'contents' => $value ];
        });
        return $result;
    }

    /**
     * Executes a request in the telegram api
     * 
     * @param string $method     Method name
     * @param array  $params     Method params
     * 
     * @return object $data
     */
    public function _execute($method, $params = [])
    {
        if (!isset($params['parse_mode']) && isset($this->options['parse_mode']))
            $params['parse_mode'] = $this->options['parse_mode'];
        if (isset($params['reply_markup']))
        {
            if ($params['reply_markup'] instanceof ReplyKeyboardMarkup ||
                $params['reply_markup'] instanceof InlineKeyboardMarkup ||
                $params['reply_markup'] instanceof ReplyKeyboardRemove ||
                $params['reply_markup'] instanceof ForceReply)
                $params['reply_markup'] = $params['reply_markup']->getJSON();
        }

        foreach ($params as $key => $param)
            if ($param instanceof InputFile)
                $params[$key] = fopen($params[$key]->getFilename(), 'r');

        $is_split = false;
        if ($method == 'sendMessage')
        {
            // Splitting a message larger than 4096 bytes into several
            if (mb_strlen($params['text']) > 4096)
            {
                $split_text = mb_substr($params['text'], 4096);
                $params['text'] = mb_substr($params['text'], 0, 4096);
                $is_split = true;
            }
        }

        $res = $this->client->post($method, [
            'multipart' => $this->toMultiPart($params)
        ]);

        if ($is_split)
        {
            $params['text'] = $split_text;
            // recursion
            self::_execute($method, $params);
        }

        $data = json_decode($res->getBody());
        return $data;
    }

    function getToken()
    {
        return $this->token;
    }

    function getClient()
    {
        return $this->client;
    }

    public function __get($name)
    {
        if (isset($this->$name))
            return $this->$name;
    }
}