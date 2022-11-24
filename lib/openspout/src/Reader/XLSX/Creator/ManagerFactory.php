<?php

namespace OpenSpout\Reader\XLSX\Creator;

use OpenSpout\Reader\Common\Manager\RowManager;
use OpenSpout\Reader\XLSX\Manager\SharedStringsCaching\CachingStrategyFactory;
use OpenSpout\Reader\XLSX\Manager\SharedStringsManager;
use OpenSpout\Reader\XLSX\Manager\SheetManager;
use OpenSpout\Reader\XLSX\Manager\StyleManager;
use OpenSpout\Reader\XLSX\Manager\WorkbookRelationshipsManager;

/**
 * Factory to create managers.
 */
class ManagerFactory
{
    /** @var HelperFactory */
    private $helperFactory;

    /** @var CachingStrategyFactory */
    private $cachingStrategyFactory;

    /** @var null|WorkbookRelationshipsManager */
    private $cachedWorkbookRelationshipsManager;

    /**
     * @param HelperFactory          $helperFactory          Factory to create helpers
     * @param CachingStrategyFactory $cachingStrategyFactory Factory to create shared strings caching strategies
     */
    public function __construct(HelperFactory $helperFactory, CachingStrategyFactory $cachingStrategyFactory)
    {
        $this->helperFactory = $helperFactory;
        $this->cachingStrategyFactory = $cachingStrategyFactory;
    }

    /**
     * @param string                $filePath      Path of the XLSX file being read
     * @param string                $tempFolder    Temporary folder where the temporary files to store shared strings will be stored
     * @param InternalEntityFactory $entityFactory Factory to create entities
     *
     * @return SharedStringsManager
     */
    public function createSharedStringsManager($filePath, $tempFolder, $entityFactory)
    {
        $workbookRelationshipsManager = $this->createWorkbookRelationshipsManager($filePath, $entityFactory);

        return new SharedStringsManager(
            $filePath,
            $tempFolder,
            $workbookRelationshipsManager,
            $entityFactory,
            $this->helperFactory,
            $this->cachingStrategyFactory
        );
    }

    /**
     * @param string                                              $filePath             Path of the XLSX file being read
     * @param \OpenSpout\Common\Manager\OptionsManagerInterface   $optionsManager       Reader's options manager
     * @param \OpenSpout\Reader\XLSX\Manager\SharedStringsManager $sharedStringsManager Manages shared strings
     * @param InternalEntityFactory                               $entityFactory        Factory to create entities
     *
     * @return SheetManager
     */
    public function createSheetManager($filePath, $optionsManager, $sharedStringsManager, $entityFactory)
    {
        $escaper = $this->helperFactory->createStringsEscaper();

        return new SheetManager($filePath, $optionsManager, $sharedStringsManager, $escaper, $entityFactory);
    }

    /**
     * @param string                $filePath      Path of the XLSX file being read
     * @param InternalEntityFactory $entityFactory Factory to create entities
     *
     * @return StyleManager
     */
    public function createStyleManager($filePath, $entityFactory)
    {
        $workbookRelationshipsManager = $this->createWorkbookRelationshipsManager($filePath, $entityFactory);

        return new StyleManager($filePath, $workbookRelationshipsManager, $entityFactory);
    }

    /**
     * @param InternalEntityFactory $entityFactory Factory to create entities
     *
     * @return RowManager
     */
    public function createRowManager($entityFactory)
    {
        return new RowManager($entityFactory);
    }

    /**
     * @param string                $filePath      Path of the XLSX file being read
     * @param InternalEntityFactory $entityFactory Factory to create entities
     *
     * @return WorkbookRelationshipsManager
     */
    private function createWorkbookRelationshipsManager($filePath, $entityFactory)
    {
        if (!isset($this->cachedWorkbookRelationshipsManager)) {
            $this->cachedWorkbookRelationshipsManager = new WorkbookRelationshipsManager($filePath, $entityFactory);
        }

        return $this->cachedWorkbookRelationshipsManager;
    }
}
