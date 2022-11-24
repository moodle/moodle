<?php

namespace OpenSpout\Writer\Common\Creator;

use OpenSpout\Common\Manager\OptionsManagerInterface;
use OpenSpout\Writer\Common\Manager\SheetManager;
use OpenSpout\Writer\Common\Manager\WorkbookManagerInterface;

/**
 * Interface ManagerFactoryInterface.
 */
interface ManagerFactoryInterface
{
    /**
     * @return WorkbookManagerInterface
     */
    public function createWorkbookManager(OptionsManagerInterface $optionsManager);

    /**
     * @return SheetManager
     */
    public function createSheetManager();
}
