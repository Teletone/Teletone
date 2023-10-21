<?php

namespace Teletone\Types;

class ReplyKeyboardRemove
{
    private $params;

    function __construct($params = [])
    {
        $this->params = $params;
    }

    function getJSON()
    {
        return json_encode(array_merge([
            'remove_keyboard' => true
        ], $this->params));
    }
}