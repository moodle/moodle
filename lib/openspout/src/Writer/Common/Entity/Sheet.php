<?php

namespace OpenSpout\Writer\Common\Entity;

use OpenSpout\Writer\Common\Manager\SheetManager;
use OpenSpout\Writer\XLSX\Entity\SheetView;

/**
 * External representation of a worksheet.
 */
class Sheet
{
    public const DEFAULT_SHEET_NAME_PREFIX = 'Sheet';

    /** @var int Index of the sheet, based on order in the workbook (zero-based) */
    private $index;

    /** @var string ID of the sheet's associated workbook. Used to restrict sheet name uniqueness enforcement to a single workbook */
    private $associatedWorkbookId;

    /** @var string Name of the sheet */
    private $name;

    /** @var bool Visibility of the sheet */
    private $isVisible;

    /** @var SheetManager Sheet manager */
    private $sheetManager;

    /** @var SheetView */
    private $sheetView;

    /**
     * @param int          $sheetIndex           Index of the sheet, based on order in the workbook (zero-based)
     * @param string       $associatedWorkbookId ID of the sheet's associated workbook
     * @param SheetManager $sheetManager         To manage sheets
     */
    public function __construct($sheetIndex, $associatedWorkbookId, SheetManager $sheetManager)
    {
        $this->index = $sheetIndex;
        $this->associatedWorkbookId = $associatedWorkbookId;

        $this->sheetManager = $sheetManager;
        $this->sheetManager->markWorkbookIdAsUsed($associatedWorkbookId);

        $this->setName(self::DEFAULT_SHEET_NAME_PREFIX.($sheetIndex + 1));
        $this->setIsVisible(true);
    }

    /**
     * @return int Index of the sheet, based on order in the workbook (zero-based)
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getAssociatedWorkbookId()
    {
        return $this->associatedWorkbookId;
    }

    /**
     * @return string Name of the sheet
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the sheet. Note that Excel has some restrictions on the name:
     *  - it should not be blank
     *  - it should not exceed 31 characters
     *  - it should not contain these characters: \ / ? * : [ or ]
     *  - it should be unique.
     *
     * @param string $name Name of the sheet
     *
     * @throws \OpenSpout\Writer\Exception\InvalidSheetNameException if the sheet's name is invalid
     *
     * @return Sheet
     */
    public function setName($name)
    {
        $this->sheetManager->throwIfNameIsInvalid($name, $this);

        $this->name = $name;

        $this->sheetManager->markSheetNameAsUsed($this);

        return $this;
    }

    /**
     * @return bool isVisible Visibility of the sheet
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * @param bool $isVisible Visibility of the sheet
     *
     * @return Sheet
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getSheetView(): ?SheetView
    {
        return $this->sheetView;
    }

    /**
     * @return $this
     */
    public function setSheetView(SheetView $sheetView)
    {
        $this->sheetView = $sheetView;

        return $this;
    }

    public function hasSheetView(): bool
    {
        return $this->sheetView instanceof SheetView;
    }
}
