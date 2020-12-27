<?php namespace RedeyeVentures\GeoPattern;

use RedeyeVentures\GeoPattern\SVGElements\Group;
use RedeyeVentures\GeoPattern\SVGElements\Polyline;
use RedeyeVentures\GeoPattern\SVGElements\Rectangle;
use RedeyeVentures\GeoPattern\SVGElements\Circle;
use RedeyeVentures\GeoPattern\SVGElements\Path;

class SVG {

    protected $width;
    protected $height;
    protected $svgString;

    function __construct($options=array())
    {
        $this->width = 100;
        $this->height = 100;
        $this->svgString = '';
    }

    public function setWidth($width)
    {
        $this->width = floor($width);
        return $this;
    }

    public function setHeight($height)
    {
        $this->height = floor($height);
        return $this;
    }

    protected function getSvgHeader()
    {
        return "<?xml version=\"1.0\"?><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$this->width}\" height=\"{$this->height}\">";
    }

    protected function getSvgFooter()
    {
        return '</svg>';
    }

    public function addRectangle($x, $y, $width, $height, $args=array())
    {
        $rectangle = new Rectangle($x, $y, $width, $height, $args);
        $this->svgString .= $rectangle;
        return $this;
    }

    public function addCircle($cx, $cy, $r, $args=array())
    {
        $circle = new Circle($cx, $cy, $r, $args);
        $this->svgString .= $circle;
        return $this;
    }

    public function addPath($d, $args=array())
    {
        $path = new Path($d, $args);
        $this->svgString .= $path;
        return $this;
    }

    public function addPolyline($points, $args=array())
    {
        $polyline = new Polyline($points, $args);
        $this->svgString .= $polyline;
        return $this;
    }

    public function addGroup($group, $args=array())
    {
        if ($group instanceof Group)
        {
            $group->setArgs($args);
            $this->svgString .= $group;
            return $this;
        }
        throw new \InvalidArgumentException("The group provided is not a valid instance of Group.");
    }

    public function getString()
    {
        return $this->getSvgHeader().$this->svgString.$this->getSvgFooter();
    }

    public function __toString()
    {
        return $this->getString();
    }


}