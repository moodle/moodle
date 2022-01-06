<?php namespace RedeyeVentures\GeoPattern\SVGElements;

class Path extends Base
{
    protected $tag = 'path';

    function __construct($d, $args=array())
    {
        $this->elements = [
            'd' => $d,
        ];
        parent::__construct($args);
    }
}