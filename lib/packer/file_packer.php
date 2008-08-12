<?php  //$Id$

/**
 * Abstract class for archiving of files.
 */
abstract class file_packer {

    /**
     * archive files and store the result in file storage
     * @param array $files array with zip paths as keys (archivepath=>ospathname or archivepath=>stored_file) 
     * @param int $contextid
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @param string $filename
     * @return mixed false if error stored file instance if ok
     */
    public abstract function archive_to_storage($files, $contextid, $filearea, $itemid, $filepath, $filename, $userid=null);

    /**
     * Archive files and store the result in os file
     * @param array $files array with zip paths as keys (archivepath=>ospathname or archivepath=>stored_file) 
     * @param string $archivefile path to target zip file
     * @return bool success
     */
    public abstract function archive_to_pathname($files, $archivefile);

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwrited
     * @param mixed $archivefile full pathname of zip file or stored_file instance
     * @param string $pathname target directory
     * @return mixed list of processed files; false if error
     */
    public abstract function extract_to_pathname($archivefile, $pathname);

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwrited
     * @param mixed $archivefile full pathname of zip file or stored_file instance
     * @param int $contextid
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @return mixed list of processed files; false if error
     */
    public abstract function extract_to_storage($archivefile, $contextid, $filearea, $itemid, $pathbase, $userid=null);

}