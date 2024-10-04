<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\TempFolderOptionTrait;

abstract class AbstractOptions
{
    use TempFolderOptionTrait;

    public Style $DEFAULT_ROW_STYLE;
    public bool $SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY = true;
    public ?float $DEFAULT_COLUMN_WIDTH = null;
    public ?float $DEFAULT_ROW_HEIGHT = null;

    /** @var ColumnWidth[] Array of min-max-width arrays */
    private array $COLUMN_WIDTHS = [];

    public function __construct()
    {
        $this->DEFAULT_ROW_STYLE = new Style();
    }

    /**
     * @param positive-int ...$columns One or more columns with this width
     */
    final public function setColumnWidth(float $width, int ...$columns): void
    {
        // Gather sequences
        $sequence = [];
        foreach ($columns as $column) {
            $sequenceLength = \count($sequence);
            if ($sequenceLength > 0) {
                $previousValue = $sequence[$sequenceLength - 1];
                if ($column !== $previousValue + 1) {
                    $this->setColumnWidthForRange($width, $sequence[0], $previousValue);
                    $sequence = [];
                }
            }
            $sequence[] = $column;
        }
        $this->setColumnWidthForRange($width, $sequence[0], $sequence[\count($sequence) - 1]);
    }

    /**
     * @param float        $width The width to set
     * @param positive-int $start First column index of the range
     * @param positive-int $end   Last column index of the range
     */
    final public function setColumnWidthForRange(float $width, int $start, int $end): void
    {
        $this->COLUMN_WIDTHS[] = new ColumnWidth($start, $end, $width);
    }

    /**
     * @internal
     *
     * @return ColumnWidth[]
     */
    final public function getColumnWidths(): array
    {
        return $this->COLUMN_WIDTHS;
    }
}
