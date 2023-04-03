<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Comment;

/**
 * This class defines a comment that can be added to a cell.
 */
final class Comment
{
    /** Comment height (CSS style, i.e. XXpx or YYpt). */
    public string $height = '55.5pt';

    /** Comment width (CSS style, i.e. XXpx or YYpt). */
    public string $width = '96pt';

    /** Left margin (CSS style, i.e. XXpx or YYpt). */
    public string $marginLeft = '59.25pt';

    /** Top margin (CSS style, i.e. XXpx or YYpt). */
    public string $marginTop = '1.5pt';

    /** Visible. */
    public bool $visible = false;

    /** Comment fill color. */
    public string $fillColor = '#FFFFE1';

    /** @var TextRun[] */
    private array $textRuns = [];

    public function addTextRun(?TextRun $textRun): void
    {
        $this->textRuns[] = $textRun;
    }

    /**
     * The TextRuns for this comment.
     *
     * @return TextRun[]
     */
    public function getTextRuns(): array
    {
        return $this->textRuns;
    }
}
