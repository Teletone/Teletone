<?php

namespace Teletone;

const COMMAND = 1;
const MESSAGE = 2;
const CALLBACK_QUERY = 3;
const OTHER = 4;

/** Class that provides convenient functions for Update */
class Update
{
    private $bot;
    private $update;
    private $update_type;
    private $params = [];
    private $params_text = '';

    public function __construct($bot, $update)
    {
        $this->bot = $bot;
        $this->update = $update;
        if (isset($update->message) && isset($update->message->text))
        {
            if ($update->message->text[0] === '/')
                $this->update_type = COMMAND;
            else
                $this->update_type = MESSAGE;
        }
        else if (isset($update->callback_query))
            $this->update_type = CALLBACK_QUERY;
        else
            $this->update_type = OTHER;

        if ($this->update_type == COMMAND)
        {
            $params = explode(' ', $update->message->text);
            array_shift($params);
            $this->params = $params;
            $this->params_text = implode(' ', $params);
        }
    }

    public function __get($name)
    {
        if ($name == 'bot')
            return $this->bot;
        if ($name == 'params')
            return $this->params;
        if (empty($this->update->$name))
            return NULL;
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
    public function answer($text = '', $params = [])
    {
        if ($this->update_type == COMMAND || $this->update_type == MESSAGE)
            $this->bot->_execute('sendMessage', array_merge([
                'chat_id' => $this->update->message->from->id,
                'text' => $text
            ], $params));
        if ($this->update_type == CALLBACK_QUERY)
            $this->bot->_execute('answerCallbackQuery', array_merge([
                'callback_query_id' => $this->update->callback_query->id,
                'text' => $text
            ], $params));
    }

    /** Message reply */
    public function reply($text, $params = [])
    {
        if ($this->update_type == MESSAGE)
            $this->bot->_execute('sendMessage', array_merge([
                'chat_id' => $this->update->message->from->id,
                'text' => $text,
                'reply_to_message_id' => $this->update->message->message_id
            ], $params));
        if ($this->update_type == CALLBACK_QUERY)
            $this->bot->_execute('sendMessage', array_merge([
                'chat_id' => $this->update->callback_query->from->id,
                'text' => $text,
                'reply_to_message_id' => $this->update->callback_query->message->message_id
            ], $params));
    }

    /** Download any file */
    public function download($path)
    {
        if (!empty($this->update->message->photo))
        {
            $photos = $this->update->message->photo;
            $photo = array_pop($photos);
            $file_id = $photo->file_id;
        }
        $client = $this->bot->getClient();
        $res = $client->post('getFile', [
            'form_params' => [
                'file_id' => $file_id
            ]
        ], [
            'base_uri' => "https://api.telegram.org/file/bot{$this->bot->getToken()}/"
        ]);
        $data = json_decode($res->getBody());
        if (!$data->ok)
            return false;
        $file_path = $data->result->file_path;
        $res = $client->get("https://api.telegram.org/file/bot{$this->bot->getToken()}/".$file_path, [
            'sink' => $path
        ], [
            'base_uri' => '' // bug guzzle base_uri
        ]);
        return true;
    }
}