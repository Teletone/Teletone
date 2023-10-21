<?php

namespace Teletone\Types;

class ForceReply
{
    private $params;

    function __construct($params = [])
    {
        $this->params = $params;
    }

    function getJSON()
    {
        return json_encode(array_merge([
            'force_reply' => true
        ], $this->params));
    }
}