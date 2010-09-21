<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Implementation of zip packer.
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filestorage/file_packer.php");
require_once("$CFG->libdir/filestorage/zip_archive.php");

/**
 * Utility class - handles all zipping and unzipping operations.
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class zip_packer extends file_packer {

    /**
     * Zip files and store the result in file storage
     * @param array $files array with full zip paths (including directory information)
     *              as keys (archivepath=>ospathname or archivepath/subdir=>stored_file or archivepath=>array('content_as_string'))
     * @param int $contextid
     * @param string $component
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @param string $filename
     * @return mixed false if error stored file instance if ok
     */
    public function archive_to_storage($files, $contextid, $component, $filearea, $itemid, $filepath, $filename, $userid = NULL) {
        global $CFG;

        $fs = get_file_storage();

        check_dir_exists($CFG->dataroot.'/temp/zip');
        $tmpfile = tempnam($CFG->dataroot.'/temp/zip', 'zipstor');

        if ($result = $this->archive_to_pathname($files, $tmpfile)) {
            if ($file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename)) {
                if (!$file->delete()) {
                    @unlink($tmpfile);
                    return false;
                }
            }
            $file_record = new stdClass();
            $file_record->contextid = $contextid;
            $file_record->component = $component;
            $file_record->filearea  = $filearea;
            $file_record->itemid    = $itemid;
            $file_record->filepath  = $filepath;
            $file_record->filename  = $filename;
            $file_record->userid    = $userid;
            $file_record->mimetype  = 'application/zip';

            $result = $fs->create_file_from_pathname($file_record, $tmpfile);
        }
        @unlink($tmpfile);
        return $result;
    }

    /**
     * Zip files and store the result in os file
     * @param array $files array with zip paths as keys (archivepath=>ospathname or archivepath=>stored_file or archivepath=>array('content_as_string'))
     * @param string $archivefile path to target zip file
     * @return bool success
     */
    public function archive_to_pathname($files, $archivefile) {
        if (!is_array($files)) {
            return false;
        }

        $ziparch = new zip_archive();
        if (!$ziparch->open($archivefile, file_archive::OVERWRITE)) {
            return false;
        }

        foreach ($files as $archivepath => $file) {
            $archivepath = trim($archivepath, '/');

            if (is_null($file)) {
                // empty directories have null as content
                $ziparch->add_directory($archivepath.'/');

            } else if (is_string($file)) {
                $this->archive_pathname($ziparch, $archivepath, $file);

            } else if (is_array($file)) {
                $content = reset($file);
                $ziparch->add_file_from_string($archivepath, $content);

            } else {
                $this->archive_stored($ziparch, $archivepath, $file);
            }
        }

        return $ziparch->close();
    }

    private function archive_stored($ziparch, $archivepath, $file) {
        $file->archive_file($ziparch, $archivepath);

        if (!$file->is_directory()) {
            return;
        }

        $baselength = strlen($file->get_filepath());
        $fs = get_file_storage();
        $files = $fs->get_directory_files($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(),
                                          $file->get_filepath(), true, true);
        foreach ($files as $file) {
            $path = $file->get_filepath();
            $path = substr($path, $baselength);
            $path = $archivepath.'/'.$path;
            if (!$file->is_directory()) {
                $path = $path.$file->get_filename();
            }
            $file->archive_file($ziparch, $path);
        }
    }

    private function archive_pathname($ziparch, $archivepath, $file) {
        if (!file_exists($file)) {
            return;
        }

        if (is_file($file)) {
            if (!is_readable($file)) {
                return;
            }
            $ziparch->add_file_from_pathname($archivepath, $file);
            return;
        }
        if (is_dir($file)) {
            if ($archivepath !== '') {
                $ziparch->add_directory($archivepath);
            }
            $files = new DirectoryIterator($file);
            foreach ($files as $file) {
                if ($file->isDot()) {
                    continue;
                }
                $newpath = $archivepath.'/'.$file->getFilename();
                $this->archive_pathname($ziparch, $newpath, $file->getPathname());
            }
            unset($files); //release file handles
            return;
        }
    }

    /**
     * Unzip file to given file path (real OS filesystem), existing files are overwrited
     * @param mixed $archivefile full pathname of zip file or stored_file instance
     * @param string $pathname target directory
     * @return mixed list of processed files; false if error
     */
    public function extract_to_pathname($archivefile, $pathname) {
        global $CFG;

        if (!is_string($archivefile)) {
            return $archivefile->extract_to_pathname($this, $pathname);
        }

        $processed = array();

        $pathname = rtrim($pathname, '/');
        if (!is_readable($archivefile)) {
            return false;
        }
       $ziparch = new zip_archive();
        if (!$ziparch->open($archivefile, file_archive::OPEN)) {
            return false;
        }

        foreach ($ziparch as $info) {
            $size = $info->size;
            $name = $info->pathname;

            if ($name === '' or array_key_exists($name, $processed)) {
                //probably filename collisions caused by filename cleaning/conversion
                continue;
            }

            if ($info->is_directory) {
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
            if (!$fz = $ziparch->get_stream($info->index)) {
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
     * @param mixed $archivefile full pathname of zip file or stored_file instance
     * @param int $contextid
     * @param string $component
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @return mixed list of processed files; false if error
     */
    public function extract_to_storage($archivefile, $contextid, $component, $filearea, $itemid, $pathbase, $userid = NULL) {
        global $CFG;

        if (!is_string($archivefile)) {
            return $archivefile->extract_to_pathname($this, $contextid, $component, $filearea, $itemid, $pathbase, $userid);
        }

        check_dir_exists($CFG->dataroot.'/temp/zip');

        $pathbase = trim($pathbase, '/');
        $pathbase = ($pathbase === '') ? '/' : '/'.$pathbase.'/';
        $fs = get_file_storage();

        $processed = array();

        $ziparch = new zip_archive();
        if (!$ziparch->open($archivefile, file_archive::OPEN)) {
            return false;
        }

        foreach ($ziparch as $info) {
            $size = $info->size;
            $name = $info->pathname;

            if ($name === '' or array_key_exists($name, $processed)) {
                //probably filename collisions caused by filename cleaning/conversion
                continue;
            }

            if ($info->is_directory) {
                $newfilepath = $pathbase.$name.'/';
                $fs->create_directory($contextid, $component, $filearea, $itemid, $newfilepath, $userid);
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
                if (!$fz = $ziparch->get_stream($info->index)) {
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

                if ($file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename)) {
                    if (!$file->delete()) {
                        $processed[$name] = 'Can not delete existing file'; // TODO: localise
                        continue;
                    }
                }
                $file_record = new stdClass();
                $file_record->contextid = $contextid;
                $file_record->component = $component;
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
                if (!$fz = $ziparch->get_stream($info->index)) {
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

                if ($file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename)) {
                    if (!$file->delete()) {
                        @unlink($tmpfile);
                        $processed[$name] = 'Can not delete existing file'; // TODO: localise
                        continue;
                    }
                }
                $file_record = new stdClass();
                $file_record->contextid = $contextid;
                $file_record->component = $component;
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

    /**
     * Returns array of info about all files in archive
     * @return array of file infos
     */
    public function list_files($archivefile) {
        if (!is_string($archivefile)) {
            return $archivefile->list_files();
        }

        $ziparch = new zip_archive();
        if (!$ziparch->open($archivefile, file_archive::OPEN)) {
            return false;
        }
        $list = $ziparch->list_files();
        $ziparch->close();
        return $list;
    }

}
