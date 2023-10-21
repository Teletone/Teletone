<?php

namespace Teletone\Types;

class InputFile
{
    private $filename;

    function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}