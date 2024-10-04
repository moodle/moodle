<?php

declare(strict_types=1);

namespace OpenSpout\Writer\ODS;

use OpenSpout\Common\Helper\Escaper\ODS;
use OpenSpout\Writer\AbstractWriterMultiSheets;
use OpenSpout\Writer\Common\Entity\Workbook;
use OpenSpout\Writer\Common\Helper\ZipHelper;
use OpenSpout\Writer\Common\Manager\Style\StyleMerger;
use OpenSpout\Writer\ODS\Helper\FileSystemHelper;
use OpenSpout\Writer\ODS\Manager\Style\StyleManager;
use OpenSpout\Writer\ODS\Manager\Style\StyleRegistry;
use OpenSpout\Writer\ODS\Manager\WorkbookManager;
use OpenSpout\Writer\ODS\Manager\WorksheetManager;

final class Writer extends AbstractWriterMultiSheets
{
    /** @var string Content-Type value for the header */
    protected static string $headerContentType = 'application/vnd.oasis.opendocument.spreadsheet';

    /** @var string document creator */
    protected string $creator = 'OpenSpout';
    private readonly Options $options;

    public function __construct(?Options $options = null)
    {
        $this->options = $options ?? new Options();
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function setCreator(string $creator): void
    {
        $this->creator = $creator;
    }

    protected function createWorkbookManager(): WorkbookManager
    {
        $workbook = new Workbook();

        $fileSystemHelper = new FileSystemHelper($this->options->getTempFolder(), new ZipHelper(), $this->creator);
        $fileSystemHelper->createBaseFilesAndFolders();

        $styleMerger = new StyleMerger();
        $styleManager = new StyleManager(new StyleRegistry($this->options->DEFAULT_ROW_STYLE), $this->options);
        $worksheetManager = new WorksheetManager($styleManager, $styleMerger, new ODS());

        return new WorkbookManager(
            $workbook,
            $this->options,
            $worksheetManager,
            $styleManager,
            $styleMerger,
            $fileSystemHelper
        );
    }
}
