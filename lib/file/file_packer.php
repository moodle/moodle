<?php  //$Id$

require_once("$CFG->libdir/file/zip_archive.php");

/**
 * Utility class - handles all zipping and unzipping operations.
 */
class file_packer {

    /**
     * Zip files and store the result in file storage
     * @param array $archivepath=>$pathanme or stored file instance
     * @param int $contextid
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @param string $filename
     * @return mixed false if error stored file instance if ok
     */
    public function zip_files_to_storage($files, $contextid, $filearea, $itemid, $filepath, $filename, $userid=null) {
        global $CFG;

        $fs = get_file_storage();

        check_dir_exists($CFG->dataroot.'/temp/zip', true, true);
        $tmpfile = tempnam($CFG->dataroot.'/temp/zip', 'zipstor');

        if ($result = $this->zip_files_to_pathname($files, $tmpfile)) {
            if ($file = $fs->get_file($contextid, $filearea, $itemid, $filepath, $filename)) {
                if (!$file->delete()) {
                    @unlink($tmpfile);
                    return false;
                }
            }
            $file_record = new object();
            $file_record->contextid = $contextid;
            $file_record->filearea  = $filearea;
            $file_record->itemid    = $itemid;
            $file_record->filepath  = $filepath;
            $file_record->filename  = $filename;
            $file_record->userid    = $userid;
            $result = $fs->create_file_from_pathname($file_record, $tmpfile);
        }
        @unlink($tmpfile);
        return $result;
    }

    /**
     * Zip files and store the result in os file
     * @param array $archivepath=>$pathanme or stored file instance
     * @param string $zipfile
     * @return bool success
     */
    public function zip_files_to_pathname($files, $zipfile) {
        if (!is_array($files)) {
            return false;
        }

        $ziparch = new zip_archive();
        if (!$ziparch->open($zipfile, ZIPARCHIVE::OVERWRITE)) {
            return false;
        }

        foreach ($files as $archivepath => $file) {
            $archivepath = trim($archivepath, '/');

            if (is_null($file)) {
                // empty directories have null as content
                $ziparch->addEmptyDir($archivepath.'/');

            } else if (is_string($file)) {
                $this->add_os_file_to_zip($ziparch, $archivepath, $file);

            } else {
                $this->add_stored_file_to_zip($ziparch, $archivepath, $file);
            }
        }

        return $ziparch->close();
    }

    protected function add_stored_file_to_zip($ziparch, $archivepath, $file) {
        $file->add_to_ziparchive($ziparch, $archivepath);

        if (!$file->is_directory()) {
            return;
        }

        $baselength = strlen($file->get_filepath());
        $fs = get_file_storage();
        $files = $fs->get_directory_files($file->get_contextid(), $file->get_filearea(), $file->get_itemid(),
                                          $file->get_filepath(), true, true);
        foreach ($files as $file) {
            $path = $file->get_filepath();
            $path = substr($path, $baselength);
            $path = $archivepath.'/'.$path;
            if (!$file->is_directory()) {
                $path = $path.$file->get_filename();
            }
            $file->add_to_ziparchive($ziparch, $path);
        }
    }

    protected function add_os_file_to_zip( $ziparch, $archivepath, $file) {
        if (!file_exists($file)) {
            return;
        }

        if (is_file($file)) {
            if (!is_readable($file)) {
                return;
            }
            $ziparch->addFile($file, $archivepath);
            return;
        }
        if (is_dir($file)) {
            if ($archivepath !== '') {
                $archivepath = $archivepath.'/';
                $ziparch->addEmptyDir($archivepath);
            }
            $files = new DirectoryIterator($file);
            foreach ($files as $file) {
                if ($file->isDot()) {
                    continue;
                }
                $newpath = $archivepath.$file->getFilename();
                $this->add_os_file_to_zip($ziparch, $newpath, $file->getPathname());
            }
            unset($files); //release file handles
            return;
        }
    }

    /**
     * Unzip file to given file path (real OS filesystem), existing files are overwrited
     * @param mixed $zipfile full pathname of zip file or stored_file instance
     * @param string $pathname target directory
     * @return mixed list of processed files; false if error
     */
    public function unzip_files_to_pathname($zipfile, $pathname) {
        global $CFG;

        if (!is_string($zipfile)) {
            return $zipfile->unzip_files_to_pathname($pathname);
        }

        $processed = array();

        $pathname = rtrim($pathname, '/');
        if (!is_readable($zipfile)) {
            return false;
        }
        $ziparch = new zip_archive();
        if (!$ziparch->open($zipfile, ZIPARCHIVE::FL_NOCASE)) {
            return false;
        }

        for ($i=0; $i<$ziparch->numFiles; $i++) {
            $index = $ziparch->statIndex($i);

            $size = clean_param($index['size'], PARAM_INT);
            $name = clean_param($index['name'], PARAM_PATH);
            $name = ltrim($name, '/');

            if ($name === '' or array_key_exists($name, $processed)) {
                //probably filename collisions caused by filename cleaning/conversion
                continue;
            }

            if ($size === 0 and $name[strlen($name)-1] === '/') {
                $newdir = "$pathname/$name";
                // directory
                if (is_file($newdir) and !unlink($newdir)) {
                    $processed[$name] = 'Can not create directory, file already exists'; // TODO: localise
                    continue;
                }
                if (is_dir($newdir)) {
                    //dir already there
                    $processed[$name] = true;
                } else {
                    if (mkdir($newdir, $CFG->directorypermissions, true)) {
                        $processed[$name] = true;
                    } else {
                        $processed[$name] = 'Can not create directory'; // TODO: localise
                    }
                }
                continue;
            }

            $parts = explode('/', trim($name, '/'));
            $filename = array_pop($parts);
            $newdir = rtrim($pathname.'/'.implode('/', $parts), '/');

            if (!is_dir($newdir)) {
                if (!mkdir($newdir, $CFG->directorypermissions, true)) {
                    $processed[$name] = 'Can not create directory'; // TODO: localise
                    continue;
                }
            }

            $newfile = "$newdir/$filename";
            if (!$fp = fopen($newfile, 'wb')) {
                $processed[$name] = 'Can not write target file'; // TODO: localise
                continue;
            }
            if (!$fz = $ziparch->getStream($index['name'])) {
                $processed[$name] = 'Can not read file from zip archive'; // TODO: localise
                fclose($fp);
                continue;
            }

            while (!feof($fz)) {
                $content = fread($fz, 262143);
                fwrite($fp, $content);
            }
            fclose($fz);
            fclose($fp);
            if (filesize($newfile) !== $size) {
                $processed[$name] = 'Unknown error during zip extraction'; // TODO: localise
                // something went wrong :-(
                @unlink($newfile);
                continue;
            }
            $processed[$name] = true;
        }
        $ziparch->close();
        return $processed;
    }

    /**
     * Unzip file to given file path (real OS filesystem), existing files are overwrited
     * @param mixed $zipfile full pathname of zip file or stored_file instance
     * @param int $contextid
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @return mixed list of processed files; false if error
     */
    public function unzip_files_to_storage($zipfile, $contextid, $filearea, $itemid, $pathbase, $userid=null) {
        global $CFG;

        if (!is_string($zipfile)) {
            return $zipfile->unzip_files_to_pathname($contextid, $filearea, $itemid, $pathbase, $userid);
        }

        check_dir_exists($CFG->dataroot.'/temp/zip', true, true);

        $pathbase = trim($pathbase, '/');
        $pathbase = ($pathbase === '') ? '/' : '/'.$pathbase.'/';
        $fs = get_file_storage();

        $processed = array();

        $ziparch = new zip_archive();
        if (!$ziparch->open($zipfile, ZIPARCHIVE::FL_NOCASE)) {
            return false;
        }

        for ($i=0; $i<$ziparch->numFiles; $i++) {
            $index = $ziparch->statIndex($i);

            $size = clean_param($index['size'], PARAM_INT);
            $name = clean_param($index['name'], PARAM_PATH);
            $name = ltrim($name, '/');


            if ($name === '' or array_key_exists($name, $processed)) {
                //probably filename collisions caused by filename cleaning/conversion
                continue;
            }

            if ($size === 0 and $name[strlen($name)-1] === '/') {
                $newfilepath = $pathbase.$name.'/';
                $fs->create_directory($contextid, $filearea, $itemid, $newfilepath, $userid);
                $processed[$name] = true;
                continue;
            }

            $parts = explode('/', trim($name, '/'));
            $filename = array_pop($parts);
            $filepath = $pathbase;
            if ($parts) {
                $filepath .= implode('/', $parts).'/';
            }

            if ($size < 2097151) {
                // small file
                if (!$fz = $ziparch->getStream($index['name'])) {
                    $processed[$name] = 'Can not read file from zip archive'; // TODO: localise
                    continue;
                }
                $content = '';
                while (!feof($fz)) {
                    $content .= fread($fz, 262143);
                }
                fclose($fz);
                if (strlen($content) !== $size) {
                    $processed[$name] = 'Unknown error during zip extraction'; // TODO: localise
                    // something went wrong :-(
                    unset($content);
                    continue;
                }

                if ($file = $fs->get_file($contextid, $filearea, $itemid, $filepath, $filename)) {
                    if (!$file->delete()) {
                        $processed[$name] = 'Can not delete existing file'; // TODO: localise
                        continue;
                    }
                }
                $file_record = new object();
                $file_record->contextid = $contextid;
                $file_record->filearea  = $filearea;
                $file_record->itemid    = $itemid;
                $file_record->filepath  = $filepath;
                $file_record->filename  = $filename;
                $file_record->userid    = $userid;
                if ($fs->create_file_from_string($file_record, $content)) {
                    $processed[$name] = true;
                } else {
                    $processed[$name] = 'Unknown error during zip extraction'; // TODO: localise
                }
                unset($content);
                continue;

            } else {
                // large file, would not fit into memory :-(
                $tmpfile = tempnam($CFG->dataroot.'/temp/zip', 'unzip');
                if (!$fp = fopen($tmpfile, 'wb')) {
                    @unlink($tmpfile);
                    $processed[$name] = 'Can not write temp file'; // TODO: localise
                    continue;
                }
                if (!$fz = $ziparch->getStream($index['name'])) {
                    @unlink($tmpfile);
                    $processed[$name] = 'Can not read file from zip archive'; // TODO: localise
                    continue;
                }
                while (!feof($fz)) {
                    $content = fread($fz, 262143);
                    fwrite($fp, $content);
                }
                fclose($fz);
                fclose($fp);
                if (filesize($tmpfile) !== $size) {
                    $processed[$name] = 'Unknown error during zip extraction'; // TODO: localise
                    // something went wrong :-(
                    @unlink($tmpfile);
                    continue;
                }

                if ($file = $fs->get_file($contextid, $filearea, $itemid, $filepath, $filename)) {
                    if (!$file->delete()) {
                        @unlink($tmpfile);
                        $processed[$name] = 'Can not delete existing file'; // TODO: localise
                        continue;
                    }
                }
                $file_record = new object();
                $file_record->contextid = $contextid;
                $file_record->filearea  = $filearea;
                $file_record->itemid    = $itemid;
                $file_record->filepath  = $filepath;
                $file_record->filename  = $filename;
                $file_record->userid    = $userid;
                if ($fs->create_file_from_pathname($file_record, $tmpfile)) {
                    $processed[$name] = true;
                } else {
                    $processed[$name] = 'Unknown error during zip extraction'; // TODO: localise
                }
                @unlink($tmpfile);
                continue;
            }
        }
        $ziparch->close();
        return $processed;
    }
}