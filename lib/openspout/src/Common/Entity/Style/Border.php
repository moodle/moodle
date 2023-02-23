<?php

namespace OpenSpout\Common\Entity\Style;

class Border
{
    public const LEFT = 'left';
    public const RIGHT = 'right';
    public const TOP = 'top';
    public const BOTTOM = 'bottom';

    public const STYLE_NONE = 'none';
    public const STYLE_SOLID = 'solid';
    public const STYLE_DASHED = 'dashed';
    public const STYLE_DOTTED = 'dotted';
    public const STYLE_DOUBLE = 'double';

    public const WIDTH_THIN = 'thin';
    public const WIDTH_MEDIUM = 'medium';
    public const WIDTH_THICK = 'thick';

    /** @var array A list of BorderPart objects for this border. */
    private $parts = [];

    public function __construct(array $borderParts = [])
    {
        $this->setParts($borderParts);
    }

    /**
     * @param string $name The name of the border part
     *
     * @return null|BorderPart
     */
    public function getPart($name)
    {
        return $this->hasPart($name) ? $this->parts[$name] : null;
    }

    /**
     * @param string $name The name of the border part
     *
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
     * Set BorderParts.
     *
     * @param array $parts
     */
    public function setParts($parts)
    {
        $this->parts = [];
        foreach ($parts as $part) {
            $this->addPart($part);
        }
    }

    /**
     * @return Border
     */
    public function addPart(BorderPart $borderPart)
    {
        $this->parts[$borderPart->getName()] = $borderPart;

        return $this;
    }
}
