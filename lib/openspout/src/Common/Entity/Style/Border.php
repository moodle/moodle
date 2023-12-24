<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Style;

final class Border
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

    /** @var array<string, BorderPart> */
    private array $parts;

    public function __construct(BorderPart ...$borderParts)
    {
        foreach ($borderParts as $borderPart) {
            $this->parts[$borderPart->getName()] = $borderPart;
        }
    }

    public function getPart(string $name): ?BorderPart
    {
        return $this->parts[$name] ?? null;
    }

    /**
     * @return array<string, BorderPart>
     */
    public function getParts(): array
    {
        return $this->parts;
    }
}
