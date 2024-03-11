<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Helper;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Helper\FileSystemHelperInterface;

/**
 * @internal
 */
interface FileSystemWithRootFolderHelperInterface extends FileSystemHelperInterface
{
    /**
     * Creates all the folders needed to create a spreadsheet, as well as the files that won't change.
     *
     * @throws IOException If unable to create at least one of the base folders
     */
    public function createBaseFilesAndFolders(): void;

    public function getRootFolder(): string;

    public function getSheetsContentTempFolder(): string;
}
