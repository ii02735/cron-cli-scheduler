<?php


namespace CronScheduler;


class XMLReader
{
    private $xml;
    private $lang;

    public function __construct($lang)
    {
        $this->xml = simplexml_load_file(__DIR__."/../config/lang.xml");
        $this->lang = ($lang=="en")?0:1;
    }

    public function out($node,$sub_node)
    {
        $array = $this->xml2array($this->xml->{$node}[0]);
        return ($array[$sub_node][$this->lang]);
    }

    private function xml2array ( $xmlObject, $out = [])
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

        return $out;
    }
}