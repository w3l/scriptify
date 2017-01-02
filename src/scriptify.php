<?php
namespace w3l;

class scriptify
{
    public function __construct()
    {
    }
    static function encode($input)
    {
        return strrev($input);
    }
    static function decode($input)
    {
        return strrev($input);
    }
}
