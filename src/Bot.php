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

    /**
     * Constructs a bot class
     * 
     * @param string $token     Telegram bot token
     * @param array $options    Additional bot options
     */
    public function __construct($token, $options = [])
    {
        $this->token = $token;
        $this->options = $options;
        $this->client = new Client([
            'base_uri' => "https://api.telegram.org/bot{$token}/"
        ]);
        $this->router = new Router($this);
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
        {
            $offset = $this->handleUpdates($offset);
        }
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

    public function handleWebhook()
    {
        $data = json_decode(file_get_contents('php://input'));
        if (!is_null($data))
            $this->router->_handle($data);
    }

    public function debug($message)
    {
        if (isset($this->options['debug']) && $this->options['debug'])
            echo $message."\n";
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

    /*
     * Magic method for processing requests in telegram api
     */
    public function __call($name, $arguments = [])
    {
        return $this->_execute($name, ...$arguments);
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

        $res = $this->client->post($method, [
            'multipart' => $this->toMultiPart($params)
        ]);
        $data = json_decode($res->getBody());
        return $data;
    }
}