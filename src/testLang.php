<?php


function read($node,$sub_node,$lang)
{
    $xml = simplexml_load_file(__DIR__."/../config/lang.xml");
    var_dump($xml);
    echo($xml->{$node}[0]->{$sub_node}[$lang=="en"?0:1]);
}

read("addCommand","schemaCRON","en");
