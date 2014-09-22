<?php

namespace Sumeko\Phalcon\Oauth2\Server;


class Response {

    protected static $content = [];

    public static function set($data)
    {
        static::$content = $data;
    }

    // function defination to convert array to xml
    private static function arrayToXml($content, \SimpleXMLElement &$xmlRoot) {
        foreach($content as $key => $value) {
            if(is_array($value)) {
                $key = is_numeric($key) ? "item$key" : $key;
                $sub = $xmlRoot->addChild("$key");
                static::arrayToXml($value, $sub);
            }
            else {
                $key = is_numeric($key) ? "item$key" : $key;
                $xmlRoot->addChild("$key","$value");
            }
        }
    }

    public static function toXml($root = "response")
    {
        $xmlRoot = new \SimpleXMLElement("<?xml version=\"1.0\"?><$root></$root>");
        static::arrayToXml(static::$content, $xmlRoot);
        return $xmlRoot->asXML();
    }

    public static function merge($data, $before = false)
    {
        static::$content = ($before) ? array_merge($data, static::$content) : array_merge(static::$content, $data);
    }

    public static function exception($app, $exception)
    {

    }

    public static function clean()
    {
        static::$content = [];
    }
} 