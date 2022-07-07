<?php namespace RedeyeVentures\GeoPattern\SVGElements;

class Polyline extends Base
{
    protected $tag = 'polyline';

    function __construct($points, $args=array())
    {
        $this->elements = [
            'points' => $points,
        ];
        parent::__construct($args);
    }
}