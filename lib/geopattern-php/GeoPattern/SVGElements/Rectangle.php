<?php namespace RedeyeVentures\GeoPattern\SVGElements;

class Rectangle extends Base
{
    protected $tag = 'rect';

    function __construct($x, $y, $width, $height, $args=array())
    {
        $this->elements = [
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
        ];
        parent::__construct($args);
    }
}