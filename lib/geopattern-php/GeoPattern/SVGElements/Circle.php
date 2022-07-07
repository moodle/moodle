<?php namespace RedeyeVentures\GeoPattern\SVGElements;

class Circle extends Base
{
    protected $tag = 'circle';

    function __construct($cx, $cy, $r, $args=array())
    {
        $this->elements = [
            'cx' => $cx,
            'cy' => $cy,
            'r' => $r,
        ];
        parent::__construct($args);
    }
}