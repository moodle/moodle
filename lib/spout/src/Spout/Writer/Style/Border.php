<?php

namespace Box\Spout\Writer\Style;

/**
 * Class Border
 */
class Border
{
    const LEFT = 'left';
    const RIGHT = 'right';
    const TOP = 'top';
    const BOTTOM = 'bottom';

    const STYLE_NONE = 'none';
    const STYLE_SOLID = 'solid';
    const STYLE_DASHED = 'dashed';
    const STYLE_DOTTED = 'dotted';
    const STYLE_DOUBLE = 'double';

    const WIDTH_THIN = 'thin';
    const WIDTH_MEDIUM = 'medium';
    const WIDTH_THICK = 'thick';

    /**
     * @var array A list of BorderPart objects for this border.
     */
    protected $parts = [];

    /**
     * @param array|void $borderParts
     */
    public function __construct(array $borderParts = [])
    {
        $this->setParts($borderParts);
    }

    /**
     * @param $name The name of the border part
     * @return null|BorderPart
     */
    public function getPart($name)
    {
        return $this->hasPart($name) ? $this->parts[$name] : null;
    }

    /**
     * @param $name The name of the border part
     * @return bool
     */
    public function hasPart($name)
    {
        return isset($this->parts[$name]);
    }

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Set BorderParts
     * @param array $parts
     */
    public function setParts($parts)
    {
        unset($this->parts);
        foreach ($parts as $part) {
            $this->addPart($part);
        }
    }

    /**
     * @param BorderPart $borderPart
     * @return self
     */
    public function addPart(BorderPart $borderPart)
    {
        $this->parts[$borderPart->getName()] = $borderPart;
        return $this;
    }
}
