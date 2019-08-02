<?php

namespace Box\Spout\Writer\Common\Creator;

use Box\Spout\Common\Manager\OptionsManagerInterface;
use Box\Spout\Writer\Common\Manager\SheetManager;
use Box\Spout\Writer\Common\Manager\WorkbookManagerInterface;

/**
 * Interface ManagerFactoryInterface
 */
interface ManagerFactoryInterface
{
    /**
     * @param OptionsManagerInterface $optionsManager
     * @return WorkbookManagerInterface
     */
    public function createWorkbookManager(OptionsManagerInterface $optionsManager);

    /**
     * @return SheetManager
     */
    public function createSheetManager();
}
