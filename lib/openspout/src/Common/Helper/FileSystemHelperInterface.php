<?php

namespace OpenSpout\Common\Helper;

/**
 * This interface describes helper functions to help with the file system operations
 * like files/folders creation & deletion.
 */
interface FileSystemHelperInterface
{
    /**
     * Creates an empty folder with the given name under the given parent folder.
     *
     * @param string $parentFolderPath The parent folder path under which the folder is going to be created
     * @param string $folderName       The name of the folder to create
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the folder or if the folder path is not inside of the base folder
     *
     * @return string Path of the created folder
     */
    public function createFolder($parentFolderPath, $folderName);

    /**
     * Creates a file with the given name and content in the given folder.
     * The parent folder must exist.
     *
     * @param string $parentFolderPath The parent folder path where the file is going to be created
     * @param string $fileName         The name of the file to create
     * @param string $fileContents     The contents of the file to create
     *
     * @throws \OpenSpout\Common\Exception\IOException If unable to create the file or if the file path is not inside of the base folder
     *
     * @return string Path of the created file
     */
    public function createFileWithContents($parentFolderPath, $fileName, $fileContents);

    /**
     * Delete the file at the given path.
     *
     * @param string $filePath Path of the file to delete
     *
     * @throws \OpenSpout\Common\Exception\IOException If the file path is not inside of the base folder
     */
    public function deleteFile($filePath);

    /**
     * Delete the folder at the given path as well as all its contents.
     *
     * @param string $folderPath Path of the folder to delete
     *
     * @throws \OpenSpout\Common\Exception\IOException If the folder path is not inside of the base folder
     */
    public function deleteFolderRecursively($folderPath);
}
