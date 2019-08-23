<?php
class Tool
{
    public static function shortName($name)
    {
        return explode(":", $name)[1];
    }
}