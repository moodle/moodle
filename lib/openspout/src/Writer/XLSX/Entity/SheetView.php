<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Entity;

use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Reader\XLSX\Helper\CellHelper;

final class SheetView
{
    private bool $showFormulas = false;
    private bool $showGridLines = true;
    private bool $showRowColHeaders = true;
    private bool $showZeroes = true;
    private bool $rightToLeft = false;
    private bool $tabSelected = false;
    private bool $showOutlineSymbols = true;
    private bool $defaultGridColor = true;
    private string $view = 'normal';
    private string $topLeftCell = 'A1';
    private int $colorId = 64;
    private int $zoomScale = 100;
    private int $zoomScaleNormal = 100;
    private int $zoomScalePageLayoutView = 100;
    private int $workbookViewId = 0;
    private int $freezeRow = 0;
    private string $freezeColumn = 'A';

    /**
     * @return $this
     */
    public function setShowFormulas(bool $showFormulas): self
    {
        $this->showFormulas = $showFormulas;

        return $this;
    }

    /**
     * @return $this
     */
    public function setShowGridLines(bool $showGridLines): self
    {
        $this->showGridLines = $showGridLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function setShowRowColHeaders(bool $showRowColHeaders): self
    {
        $this->showRowColHeaders = $showRowColHeaders;

        return $this;
    }

    /**
     * @return $this
     */
    public function setShowZeroes(bool $showZeroes): self
    {
        $this->showZeroes = $showZeroes;

        return $this;
    }

    /**
     * @return $this
     */
    public function setRightToLeft(bool $rightToLeft): self
    {
        $this->rightToLeft = $rightToLeft;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTabSelected(bool $tabSelected): self
    {
        $this->tabSelected = $tabSelected;

        return $this;
    }

    /**
     * @return $this
     */
    public function setShowOutlineSymbols(bool $showOutlineSymbols): self
    {
        $this->showOutlineSymbols = $showOutlineSymbols;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDefaultGridColor(bool $defaultGridColor): self
    {
        $this->defaultGridColor = $defaultGridColor;

        return $this;
    }

    /**
     * @return $this
     */
    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTopLeftCell(string $topLeftCell): self
    {
        $this->topLeftCell = $topLeftCell;

        return $this;
    }

    /**
     * @return $this
     */
    public function setColorId(int $colorId): self
    {
        $this->colorId = $colorId;

        return $this;
    }

    /**
     * @return $this
     */
    public function setZoomScale(int $zoomScale): self
    {
        $this->zoomScale = $zoomScale;

        return $this;
    }

    /**
     * @return $this
     */
    public function setZoomScaleNormal(int $zoomScaleNormal): self
    {
        $this->zoomScaleNormal = $zoomScaleNormal;

        return $this;
    }

    /**
     * @return $this
     */
    public function setZoomScalePageLayoutView(int $zoomScalePageLayoutView): self
    {
        $this->zoomScalePageLayoutView = $zoomScalePageLayoutView;

        return $this;
    }

    /**
     * @return $this
     */
    public function setWorkbookViewId(int $workbookViewId): self
    {
        $this->workbookViewId = $workbookViewId;

        return $this;
    }

    /**
     * @param positive-int $freezeRow Set to 2 to fix the first row
     *
     * @return $this
     */
    public function setFreezeRow(int $freezeRow): self
    {
        if ($freezeRow < 1) {
            throw new InvalidArgumentException('Freeze row must be a positive integer');
        }

        $this->freezeRow = $freezeRow;

        return $this;
    }

    /**
     * @param string $freezeColumn Set to B to fix the first column
     *
     * @return $this
     */
    public function setFreezeColumn(string $freezeColumn): self
    {
        $this->freezeColumn = strtoupper($freezeColumn);

        return $this;
    }

    public function getXml(): string
    {
        return '<sheetView'.$this->getSheetViewAttributes().'>'.
        $this->getFreezeCellPaneXml().
        '</sheetView>';
    }

    private function getSheetViewAttributes(): string
    {
        return $this->generateAttributes([
            'showFormulas' => $this->showFormulas,
            'showGridLines' => $this->showGridLines,
            'showRowColHeaders' => $this->showRowColHeaders,
            'showZeroes' => $this->showZeroes,
            'rightToLeft' => $this->rightToLeft,
            'tabSelected' => $this->tabSelected,
            'showOutlineSymbols' => $this->showOutlineSymbols,
            'defaultGridColor' => $this->defaultGridColor,
            'view' => $this->view,
            'topLeftCell' => $this->topLeftCell,
            'colorId' => $this->colorId,
            'zoomScale' => $this->zoomScale,
            'zoomScaleNormal' => $this->zoomScaleNormal,
            'zoomScalePageLayoutView' => $this->zoomScalePageLayoutView,
            'workbookViewId' => $this->workbookViewId,
        ]);
    }

    private function getFreezeCellPaneXml(): string
    {
        if ($this->freezeRow < 2 && 'A' === $this->freezeColumn) {
            return '';
        }

        $columnIndex = CellHelper::getColumnIndexFromCellIndex($this->freezeColumn.'1');

        return '<pane'.$this->generateAttributes([
            'xSplit' => $columnIndex,
            'ySplit' => $this->freezeRow - 1,
            'topLeftCell' => $this->freezeColumn.$this->freezeRow,
            'activePane' => 'bottomRight',
            'state' => 'frozen',
        ]).'/>';
    }

    /**
     * @param array<string, bool|int|string> $data with key containing the attribute name and value containing the attribute value
     */
    private function generateAttributes(array $data): string
    {
        // Create attribute for each key
        $attributes = array_map(static function (string $key, bool|int|string $value): string {
            if (\is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            return $key.'="'.$value.'"';
        }, array_keys($data), $data);

        // Append all attributes
        return ' '.implode(' ', $attributes);
    }
}
