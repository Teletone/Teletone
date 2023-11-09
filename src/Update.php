<?php

namespace Teletone;

const COMMAND = 1;
const MESSAGE = 2;
const CALLBACK_QUERY = 3;
const CHAT_MEMBER = 4;
const OTHER = 5;

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
        elseif (isset($update->callback_query))
            $this->update_type = CALLBACK_QUERY;
        elseif (isset($update->my_chat_member))
            $this->update_type = CHAT_MEMBER;
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

    public function getObjName()
    {
        if ($this->update_type == COMMAND || $this->update_type == MESSAGE)
            $obj_name = 'message';
        elseif ($this->update_type == CALLBACK_QUERY)
            $obj_name = 'callback_query';
        elseif ($this->update_type == CHAT_MEMBER)
            if (isset($this->update->chat_member))
                $obj_name = 'chat_member';
            else
                $obj_name = 'my_chat_member';
        else
            $obj_name = 'message';
        return $obj_name;
    }

    public function __get($name)
    {
        if (isset($this->$name))
            return $this->$name;

        $obj_name = $this->getObjName();
        if (empty($this->update->$obj_name->$name))
            return NULL;
        return $this->update->$obj_name->$name;
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
        $obj_name = $this->getObjName();
        $this->bot->_execute('sendMessage', array_merge([
            'chat_id' => $this->update->$obj_name->from->id,
            'text' => $text
        ], $params));
    }

    /** Answer on callback */
    public function answerCallback($text = '', $params = [])
    {
        $this->bot->_execute('answerCallbackQuery', array_merge([
            'callback_query_id' => $this->update->callback_query->id,
            'text' => $text
        ], $params));
    }

    /** Send a new message or edit the current one on which the inline button was clicked. Useful for menus */
    public function answerOrEdit($text, $params = [])
    {
        if ($this->update_type == CALLBACK_QUERY)
            $this->bot->editMessageText(array_merge([
                'chat_id' => $this->update->callback_query->message->chat->id,
                'message_id' => $this->update->callback_query->message->message_id,
                'text' => $text
            ], $params));
        else
            $this->answer($text, $params);
    }

    /** Allows you to simply edit the message on which the inline button was clicked */
    public function edit($text, $params = [])
    {
        if ($this->update_type == CALLBACK_QUERY)
            $this->bot->editMessageText(array_merge([
                'chat_id' => $this->update->callback_query->message->chat->id,
                'message_id' => $this->update->callback_query->message->message_id,
                'text' => $text
            ], $params));
    }

    /** Delete current message, user message in bot, in chat, or message from a bot */
    public function delete()
    {
        $obj_name = $this->getObjName();
        if (isset($this->update->$obj_name->message_id))
            $message_id = $this->update->$obj_name->message_id;
        else
            $message_id = $this->update->$obj_name->message->message_id;
        $this->bot->deleteMessage([
            'chat_id' => $this->update->$obj_name->from->id,
            'message_id' => $message_id
        ]);
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