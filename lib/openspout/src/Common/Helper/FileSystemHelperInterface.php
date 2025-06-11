<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper;

use OpenSpout\Common\Exception\IOException;

/**
 * @internal
 */
interface FileSystemHelperInterface
{
    /**
     * Creates an empty folder with the given name under the given parent folder.
     *
     * @param string $parentFolderPath The parent folder path under which the folder is going to be created
     * @param string $folderName       The name of the folder to create
     *
     * @return string Path of the created folder
     *
     * @throws IOException If unable to create the folder or if the folder path is not inside of the base folder
     */
    public function createFolder(string $parentFolderPath, string $folderName): string;

    /**
     * Creates a file with the given name and content in the given folder.
     * The parent folder must exist.
     *
     * @param string $parentFolderPath The parent folder path where the file is going to be created
     * @param string $fileName         The name of the file to create
     * @param string $fileContents     The contents of the file to create
     *
     * @return string Path of the created file
     *
     * @throws IOException If unable to create the file or if the file path is not inside of the base folder
     */
    public function createFileWithContents(string $parentFolderPath, string $fileName, string $fileContents): string;

    /**
     * Delete the file at the given path.
     *
     * @param string $filePath Path of the file to delete
     *
     * @throws IOException If the file path is not inside of the base folder
     */
    public function deleteFile(string $filePath): void;

    /**
     * Delete the folder at the given path as well as all its contents.
     *
     * @param string $folderPath Path of the folder to delete
     *
     * @throws IOException If the folder path is not inside of the base folder
     */
    public function deleteFolderRecursively(string $folderPath): void;
}
