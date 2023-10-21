<?php

namespace Teletone;

/** Class that provides convenient functions for Update */
class Update
{
    private $bot;
    private $update;

    public function __construct($bot, $update)
    {
        $this->bot = $bot;
        $this->update = $update;
    }

    public function __get($name)
    {
        if ($name == 'bot')
            return $this->bot;
        return $this->update->$name;
    }

    /** Used to return a function to handle the next matching handler */
    public function next()
    {
        return 302;
    }

    /** Get update data as array */
    public function asArray()
    {
        return json_decode(json_encode($this->update), true);
    }

    /** Message answer */
    public function answer($text, $params = [])
    {
        $this->bot->_execute('sendMessage', array_merge([
            'chat_id' => $this->update->message->from->id,
            'text' => $text
        ], $params));
    }

    /** Message reply */
    public function reply($text, $params = [])
    {
        $this->bot->_execute('sendMessage', array_merge([
            'chat_id' => $this->update->message->from->id,
            'text' => $text,
            'reply_to_message_id' => $this->update->message->message_id
        ], $params));
    }
}