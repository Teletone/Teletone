<?php

namespace Teletone\Types;

class ReplyKeyboardMarkup
{
    private $keyboard;
    private $params;

    function __construct($keyboard, $params = [])
    {
        $this->keyboard = $keyboard;
        $this->params = $params;
    }

    function getJSON()
    {
        return json_encode(array_merge([
            'keyboard' => $this->keyboard
        ], $this->params));
    }
}