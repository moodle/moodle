<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Comment;

/**
 * This class defines rich text in a fluent interface that can be added to a comment.
 */
final class TextRun
{
    public string $text;
    public int $fontSize = 10;
    public string $fontColor = '000000';
    public string $fontName = 'Tahoma';
    public bool $bold = false;
    public bool $italic = false;

    public function __construct(string $text)
    {
        $this->text = $text;
    }
}
