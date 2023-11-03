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

    public function __construct($bot)
    {
        $this->bot = $bot;
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
    public function message($text, $callback, $regex = false, $types = Types::TEXT)
    {
        $this->routes['message'][] = [
            'text' => $text,
            'callback' => $callback,
            'regex' => $regex,
            'types' => $types
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
                foreach ($router_values as $route)
                    $call_array[] = $route['callback'];
            }

            if (isset($update->message) && isset($update->message->text) && $router_key == 'command')
            {
                if ($update->message->text[0] === '/')
                {
                    foreach ($router_values as $route)
                    {
                        if (is_null($route['text']))
                            $call = true;
                        else
                        {
                            $command = substr($update->message->text, 1);
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
            }

            if (isset($update->message) && $router_key == 'message')
            {
                foreach ($router_values as $route)
                {
                    if (isset($update->message->text) && ($route['types'] & Types::TEXT))
                    {
                        // not a command
                        if ($update->message->text[0] !== '/')
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

                            if ($call)
                            {
                                $call_array[] = $route['callback'];
                                $call = false;
                            }
                        }
                    }

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
            $ret = $func($c_update);
            if ($ret !== $c_update->next())
                break;
        }
    }
}