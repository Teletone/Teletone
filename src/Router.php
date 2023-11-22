<?php

namespace Teletone;

use Teletone\Types;
use Teletone\Statuses;

/** Router class that processes all incoming updates */
class Router
{
    private $bot;
    private $routes = [
        'any' => [],
        'command' => [],
        'message' => [],
        'callback_query' => [],
        'chat_member' => []
    ];
    private $before_funcs = [];

    public function __construct($bot)
    {
        $this->bot = $bot;
        $this->before_funcs = [
            'any' => static function($u) { return true; },
            'command' => static function($u) { return true; },
            'message' => static function($u) { return true; },
            'callback_query' => static function($u) { return true; },
            'chat_member' => static function($u) { return true; }
        ];
    }

    /**
     * Specifies a function that will fire before processing the callback and decide whether it should be called. The function must return true or false
     * 
     * @param string $type      Type, value key from $before_funcs
     * @param object $func      Function. The first parameter of the function is passed $update, this is class Update
     */
    public function registerBeforeFunc($type, $func)
    {
        $this->before_funcs[$type] = $func;
    }

    /**
     * Registers a new callback for all incoming updates
     * 
     * @param string $callback      Callback
     * 
     * @return NULL
     */
    public function any($callback)
    {
        $this->routes['any'][] = [
            'callback' => $callback
        ];
    }

    /**
     * Registers a new command handler
     * 
     * @param string    $text           Command text
     * @param string    $callback       Callback
     * @param boolean   $regex          Set to true to check command as a regular expression
     * 
     * @return NULL
     */
    public function command($text, $callback, $regex = false)
    {
        $this->routes['command'][] = [
            'text' => $text,
            'callback' => $callback,
            'regex' => $regex
        ];
    }

    /**
     * Registers a new message handler
     * 
     * @param string    $text           Message text
     * @param string    $callback       Callback
     * @param boolean   $regex          Set to true to check text as a regular expression
     * @param string    $types          Any combination of Teletone\Types, joined with the binary OR (|) operator
     * 
     * @return NULL
     */
    public function message($text, $callback, $regex = false, $types = Types::TEXT, $for_groups = false)
    {
        $this->routes['message'][] = [
            'text' => $text,
            'callback' => $callback,
            'regex' => $regex,
            'types' => $types,
            'for_groups' => $for_groups
        ];
    }

    /**
     * Registers a new callback query handler
     * 
     * @param string    $text           Text callback data
     * @param string    $callback       Callback
     * @param boolean   $regex          Set to true to check text data as a regular expression
     * 
     * @return NULL
     */
    public function callbackQuery($text, $callback, $regex = false)
    {
        $this->routes['callback_query'][] = [
            'text' => $text,
            'callback' => $callback,
            'regex' => $regex
        ];
    }

    /**
     * Registers a new chat member handler
     * 
     * @param string $callback      Callback
     * @param string $statuses      Any combination of Teletone\Statuses, joined with the binary OR (|) operator
     * 
     * @return NULL
     */
    public function chatMember($callback, $statuses)
    {
        $this->routes['chat_member'][] = [
            'callback' => $callback,
            'statuses' => $statuses
        ];
    }

    /** Update handler */
    public function _handle($update)
    {
        $this->bot->debug(json_encode($update, JSON_UNESCAPED_LINE_TERMINATORS|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

        $call = false; // variable to understand whether callback needs to be called
        $call_array = [];

        // Here we scan the routes and fill the array of calls and then execute them
        foreach ($this->routes as $router_key => $router_values)
        {
            if ($router_key == 'any')
            {
                if (!$this->bot->router->before_funcs['any'](new Update($this->bot, $update)))
                    continue;
                foreach ($router_values as $route)
                    $call_array[] = $route['callback'];
            }

            if (isset($update->message) && isset($update->message->text) && $update->message->text[0] === '/' && $router_key == 'command')
            {
                if (!$this->bot->router->before_funcs['command'](new Update($this->bot, $update)))
                    continue;
                foreach ($router_values as $route)
                {
                    if (is_null($route['text']))
                        $call = true;
                    else
                    {
                        $params = explode(' ', $update->message->text);
                        $command = substr($params[0], 1);
                        if ($route['regex'])
                            $call = preg_match($route['text'], $command) === 1;
                        else
                            $call = ($route['text'] === $command);
                    }

                    if ($call)
                    {
                        $call_array[] = $route['callback'];
                        $call = false;
                    }
                }
            }

            if ((isset($update->message) && (!isset($update->message->entities) || (isset($update->message->entities) && $update->message->entities[0]->type != 'bot_command'))) && $router_key == 'message')
            {
                // Checking whether a callback needs to be executed
                if (!$this->bot->router->before_funcs['message'](new Update($this->bot, $update)))
                    continue;
                foreach ($router_values as $route)
                {
                    if (isset($update->message->text) && ($route['types'] & Types::TEXT))
                    {
                        // not a command
                        if ($update->message->text[0] !== '/')
                        {
                            // message in group
                            if ($route['for_groups'] && $update->message->chat->type == 'private')
                                $call = false;
                            // here the processing of messages for which the for_groups flag is not set is checked
                            elseif (!$this->bot->options['all_groups'] && !$route['for_groups'] && $update->message->chat->type != 'private')
                                $call = false;
                            else
                            {
                                // handle all
                                if (is_null($route['text']))
                                    $call = true;
                                else
                                    // handle if regex
                                    if ($route['regex'])
                                        $call = preg_match($route['text'], $update->message->text) === 1;
                                    else
                                        $call = ($route['text'] === $update->message->text);
                            }

                            if ($call)
                            {
                                $call_array[] = $route['callback'];
                                $call = false;
                            }
                        }
                    }

                    if (isset($update->message->left_chat_participant))
                        $call = ($route['types'] & Types::LEFT_CHAT_PARTICIPANT);

                    if (isset($update->message->new_chat_participant))
                        $call = ($route['types'] & Types::NEW_CHAT_PARTICIPANT);

                    if (isset($update->message->animation))
                        $call = ($route['types'] & Types::ANIMATION);

                    if (isset($update->message->audio))
                        $call = ($route['types'] & Types::AUDIO);

                    if (isset($update->message->document) && !isset($update->message->animation))
                        $call = ($route['types'] & Types::DOCUMENT);

                    if (isset($update->message->photo))
                        $call = ($route['types'] & Types::PHOTO);

                    if (isset($update->message->sticker))
                        $call = ($route['types'] & Types::STICKER);

                    if (isset($update->message->video))
                        $call = ($route['types'] & Types::VIDEO);

                    if (isset($update->message->video_note))
                        $call = ($route['types'] & Types::VIDEONOTE);

                    if (isset($update->message->voice))
                        $call = ($route['types'] & Types::VOICE);

                    if (isset($update->message->contact))
                        $call = ($route['types'] & Types::CONTACT);

                    if (isset($update->message->dice))
                        $call = ($route['types'] & Types::DICE);

                    if (isset($update->message->game))
                        $call = ($route['types'] & Types::GAME);

                    if (isset($update->message->poll))
                        $call = ($route['types'] & Types::POLL);

                    if (isset($update->message->venue))
                        $call = ($route['types'] & Types::VENUE);

                    if (isset($update->message->location) && !isset($update->message->venue))
                        $call = ($route['types'] & Types::LOCATION);

                    if (isset($update->message->invoice))
                        $call = ($route['types'] & Types::INVOICE);

                    if (isset($update->message->passport_data))
                        $call = ($route['types'] & Types::PASSPORTDATA);

                    if ($call)
                    {
                        $call_array[] = $route['callback'];
                        $call = false;
                    }
                }
            }

            if (isset($update->callback_query) && $router_key == 'callback_query')
            {
                if (!$this->bot->router->before_funcs['callback_query'](new Update($this->bot, $update)))
                    continue;
                foreach ($this->routes['callback_query'] as $route)
                {
                    if ($route['regex'])
                        $call = preg_match($route['text'], $update->callback_query->data) === 1;
                    else
                        $call = $update->callback_query->data === $route['text'];

                    if ($call)
                    {
                        $call_array[] = $route['callback'];
                        $call = false;
                    }
                }
            }

            if ((isset($update->my_chat_member) || isset($update->chat_member)) && $router_key == 'chat_member')
            {
                if (!$this->bot->router->before_funcs['chat_member'](new Update($this->bot, $update)))
                    continue;
                $chat_member = (isset($update->my_chat_member)) ? $update->my_chat_member : ((isset($update->chat_member)) ? $update->chat_member : $update->message);
                foreach ($this->routes['chat_member'] as $route)
                {
                    if (isset($chat_member->new_chat_member))
                    {
                        if ($route['statuses'] & Statuses::CREATOR && $chat_member->new_chat_member->status == 'creator')
                            $call = true;

                        if ($route['statuses'] & Statuses::ADMINISTRATOR && $chat_member->new_chat_member->status == 'administrator')
                            $call = true;

                        if ($route['statuses'] & Statuses::MEMBER && $chat_member->new_chat_member->status == 'member')
                            $call = true;

                        if ($route['statuses'] & Statuses::RESTRICTED && $chat_member->new_chat_member->status == 'restricted')
                            $call = true;

                        if (($route['statuses'] & Statuses::LEFT) && $chat_member->new_chat_member->status == 'left')
                            $call = true;

                        if ($route['statuses'] & Statuses::KICKED && $chat_member->new_chat_member->status == 'kicked')
                            $call = true;
                    }

                    if ($call)
                    {
                        $call_array[] = $route['callback'];
                        $call = false;
                    }
                }
            }
        }

        foreach ($call_array as $func)
        {
            $c_update = new Update($this->bot, $update);
            if (gettype($func) == 'string')
            {
                // Checking whether a class needs to be created
                $check_call_class = explode('->', $func);
                if (count($check_call_class) == 2)
                {
                    $class_name = $check_call_class[0];
                    $method = $check_call_class[1];
                    if (!class_exists($class_name))
                        throw new \Exception("Unable to load class: $class_name");
                    $class = new $class_name;
                    if (!method_exists($class, $method))
                        throw new \Exception("Method $method not found in class $class_name");
                    $ret = $class->$method($c_update);
                }
                else
                {
                    $check_call_static_class = explode('::', $func);
                    if (count($check_call_static_class) == 2)
                    {
                        $class_name = $check_call_static_class[0];
                        $method = $check_call_static_class[1];
                        if (!class_exists($class_name))
                            throw new \Exception("Unable to load class: $class_name");
                        if (!method_exists($class_name, $method))
                            throw new \Exception("Method $method not found in class $class_name");
                        $ret = $func($c_update);
                    }
                    else
                        $ret = $func($c_update);
                }
            }
            else
                $ret = $func($c_update);
            if ($ret !== $c_update->next())
                break;
        }
    }
}