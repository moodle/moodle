<?php

namespace Box\Spout\Writer\XLSX\Creator;

use Box\Spout\Common\Manager\OptionsManagerInterface;
use Box\Spout\Writer\Common\Creator\InternalEntityFactory;
use Box\Spout\Writer\Common\Creator\ManagerFactoryInterface;
use Box\Spout\Writer\Common\Entity\Options;
use Box\Spout\Writer\Common\Manager\RowManager;
use Box\Spout\Writer\Common\Manager\SheetManager;
use Box\Spout\Writer\Common\Manager\Style\StyleMerger;
use Box\Spout\Writer\XLSX\Manager\SharedStringsManager;
use Box\Spout\Writer\XLSX\Manager\Style\StyleManager;
use Box\Spout\Writer\XLSX\Manager\Style\StyleRegistry;
use Box\Spout\Writer\XLSX\Manager\WorkbookManager;
use Box\Spout\Writer\XLSX\Manager\WorksheetManager;

/**
 * Class ManagerFactory
 * Factory for managers needed by the XLSX Writer
 */
class ManagerFactory implements ManagerFactoryInterface
{
    /** @var InternalEntityFactory */
    protected $entityFactory;

    /** @var HelperFactory $helperFactory */
    protected $helperFactory;

    /**
     * @param InternalEntityFactory $entityFactory
     * @param HelperFactory $helperFactory
     */
    public function __construct(InternalEntityFactory $entityFactory, HelperFactory $helperFactory)
    {
        $this->entityFactory = $entityFactory;
        $this->helperFactory = $helperFactory;
    }

    /**
     * @param OptionsManagerInterface $optionsManager
     * @return WorkbookManager
     */
    public function createWorkbookManager(OptionsManagerInterface $optionsManager)
    {
        $workbook = $this->entityFactory->createWorkbook();

        $fileSystemHelper = $this->helperFactory->createSpecificFileSystemHelper($optionsManager, $this->entityFactory);
        $fileSystemHelper->createBaseFilesAndFolders();

        $xlFolder = $fileSystemHelper->getXlFolder();
        $sharedStringsManager = $this->createSharedStringsManager($xlFolder);

        $styleMerger = $this->createStyleMerger();
        $styleManager = $this->createStyleManager($optionsManager);
        $worksheetManager = $this->createWorksheetManager($optionsManager, $styleManager, $styleMerger, $sharedStringsManager);

        return new WorkbookManager(
            $workbook,
            $optionsManager,
            $worksheetManager,
            $styleManager,
            $styleMerger,
            $fileSystemHelper,
            $this->entityFactory,
            $this
        );
    }

    /**
     * @param OptionsManagerInterface $optionsManager
     * @param StyleManager $styleManager
     * @param StyleMerger $styleMerger
     * @param SharedStringsManager $sharedStringsManager
     * @return WorksheetManager
     */
    private function createWorksheetManager(
        OptionsManagerInterface $optionsManager,
        StyleManager $styleManager,
        StyleMerger $styleMerger,
        SharedStringsManager $sharedStringsManager
    ) {
        $rowManager = $this->createRowManager();
        $stringsEscaper = $this->helperFactory->createStringsEscaper();
        $stringsHelper = $this->helperFactory->createStringHelper();

        return new WorksheetManager(
            $optionsManager,
            $rowManager,
            $styleManager,
            $styleMerger,
            $sharedStringsManager,
            $stringsEscaper,
            $stringsHelper,
            $this->entityFactory
        );
    }

    /**
     * @return SheetManager
     */
    public function createSheetManager()
    {
        $stringHelper = $this->helperFactory->createStringHelper();

        return new SheetManager($stringHelper);
    }

    /**
     * @return RowManager
     */
    public function createRowManager()
    {
        return new RowManager();
    }

    /**
     * @param OptionsManagerInterface $optionsManager
     * @return StyleManager
     */
    private function createStyleManager(OptionsManagerInterface $optionsManager)
    {
        $styleRegistry = $this->createStyleRegistry($optionsManager);

        return new StyleManager($styleRegistry);
    }

    /**
     * @param OptionsManagerInterface $optionsManager
     * @return StyleRegistry
     */
    private function createStyleRegistry(OptionsManagerInterface $optionsManager)
    {
        $defaultRowStyle = $optionsManager->getOption(Options::DEFAULT_ROW_STYLE);

        return new StyleRegistry($defaultRowStyle);
    }

    /**
     * @return StyleMerger
     */
    private function createStyleMerger()
    {
        return new StyleMerger();
    }

    /**
     * @param string $xlFolder Path to the "xl" folder
     * @return SharedStringsManager
     */
    private function createSharedStringsManager($xlFolder)
    {
        $stringEscaper = $this->helperFactory->createStringsEscaper();

        return new SharedStringsManager($xlFolder, $stringEscaper);
    }
}
