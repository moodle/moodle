<?php

namespace OpenSpout\Writer\XLSX\Entity;

use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Reader\XLSX\Helper\CellHelper;

class SheetView
{
    /** @var bool */
    protected $showFormulas = false;

    /** @var bool */
    protected $showGridLines = true;

    /** @var bool */
    protected $showRowColHeaders = true;

    /** @var bool */
    protected $showZeroes = true;

    /** @var bool */
    protected $rightToLeft = false;

    /** @var bool */
    protected $tabSelected = false;

    /** @var bool */
    protected $showOutlineSymbols = true;

    /** @var bool */
    protected $defaultGridColor = true;

    /** @var string */
    protected $view = 'normal';

    /** @var string */
    protected $topLeftCell = 'A1';

    /** @var int */
    protected $colorId = 64;

    /** @var int */
    protected $zoomScale = 100;

    /** @var int */
    protected $zoomScaleNormal = 100;

    /** @var int */
    protected $zoomScalePageLayoutView = 100;

    /** @var int */
    protected $workbookViewId = 0;

    /** @var int */
    protected $freezeRow = 0;

    /** @var string */
    protected $freezeColumn = 'A';

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
     * @param int $freezeRow Set to 2 to fix the first row
     *
     * @return $this
     */
    public function setFreezeRow(int $freezeRow): self
    {
        if ($freezeRow < 1) {
            throw new InvalidArgumentException('Freeze row must be a positive integer', 1589543073);
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

    protected function getSheetViewAttributes(): string
    {
        // Get class properties
        $propertyValues = get_object_vars($this);
        unset($propertyValues['freezeRow'], $propertyValues['freezeColumn']);

        return $this->generateAttributes($propertyValues);
    }

    protected function getFreezeCellPaneXml(): string
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
     * @param array $data with key containing the attribute name and value containing the attribute value
     */
    protected function generateAttributes(array $data): string
    {
        // Create attribute for each key
        $attributes = array_map(function ($key, $value) {
            if (\is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            return $key.'="'.$value.'"';
        }, array_keys($data), $data);

        // Append all attributes
        return ' '.implode(' ', $attributes);
    }
}
