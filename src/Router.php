<?php

namespace Teletone;

use Teletone\Types;

/** Router class that processes all incoming updates */
class Router
{
    private $bot;
    private $routes = [
        'message' => []
    ];

    public function __construct($bot)
    {
        $this->bot = $bot;
    }

    /**
     * Registers a new message handler
     * 
     * @param string $text          Message text
     * @param string $callback      Callback
     * @param string $regex         Set to true to check text as a regular expression
     * @param string $types         Any combination of Teletone\Types, joined with the binary OR (|) operator
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

    /** Update handler */
    public function _handle($update)
    {
        $this->bot->debug(json_encode($update));
        $call = false; // variable to understand whether callback needs to be called
        if (isset($update->message))
        {
            // we pass through the handlers
            foreach ($this->routes['message'] as $route)
            {
                if (isset($update->message->text) && ($route['types'] & Types::TEXT))
                {
                    // not a command
                    if ($update->message->text[0] != '/')
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
                    $c_update = new Update($this->bot, $update);
                    $ret = $route['callback']($c_update);
                    if ($ret !== $c_update->next())
                        break;
                    $call = false;
                }
            }
        }
    }
}