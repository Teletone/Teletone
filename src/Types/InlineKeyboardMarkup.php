<?php

namespace Teletone\Types;

class InlineKeyboardMarkup
{
    private $inline_keyboard;

    function __construct($inline_keyboard)
    {
        $this->inline_keyboard = $inline_keyboard;
    }

    function getJSON()
    {
        return json_encode([
            'inline_keyboard' => $this->inline_keyboard
        ]);
    }
}