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
 * This plugin is used to upload files
 *
 * @since Moodle 2.0
 * @package    repository_upload
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * A repository plugin to allow user uploading files
 *
 * @since Moodle 2.0
 * @package    repository_upload
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_upload extends repository {
    private $mimetypes = array();

    /**
     * Print a upload form
     * @return array
     */
    public function print_login() {
        return $this->get_listing();
    }

    /**
     * Process uploaded file
     * @return array|bool
     */
    public function upload($saveasfilename, $maxbytes) {
        global $CFG;

        $types = optional_param_array('accepted_types', '*', PARAM_RAW);
        $savepath = optional_param('savepath', '/', PARAM_PATH);
        $itemid   = optional_param('itemid', 0, PARAM_INT);
        $license  = optional_param('license', $CFG->sitedefaultlicense, PARAM_TEXT);
        $author   = optional_param('author', '', PARAM_TEXT);
        $areamaxbytes = optional_param('areamaxbytes', FILE_AREA_MAX_BYTES_UNLIMITED, PARAM_INT);
        $overwriteexisting = optional_param('overwrite', false, PARAM_BOOL);

        return $this->process_upload($saveasfilename, $maxbytes, $types, $savepath, $itemid, $license, $author,
                         $overwriteexisting, $areamaxbytes);
    }

    /**
     * Do the actual processing of the uploaded file
     * @param string $saveasfilename name to give to the file
     * @param int $maxbytes maximum file size
     * @param mixed $types optional array of file extensions that are allowed or '*' for all
     * @param string $savepath optional path to save the file to
     * @param int $itemid optional the ID for this item within the file area
     * @param string $license optional the license to use for this file
     * @param string $author optional the name of the author of this file
     * @param bool $overwriteexisting optional user has asked to overwrite the existing file
     * @param int $areamaxbytes maximum size of the file area.
     * @return object containing details of the file uploaded
     */
    public function process_upload($saveasfilename, $maxbytes, $types = '*', $savepath = '/', $itemid = 0,
            $license = null, $author = '', $overwriteexisting = false, $areamaxbytes = FILE_AREA_MAX_BYTES_UNLIMITED) {
        global $USER, $CFG;

        \core\session\manager::write_close();

        if ((is_array($types) and in_array('*', $types)) or $types == '*') {
            $this->mimetypes = '*';
        } else {
            foreach ($types as $type) {
                $this->mimetypes[] = mimeinfo('type', $type);
            }
        }

        if ($license == null) {
            $license = $CFG->sitedefaultlicense;
        }

        $record = new stdClass();
        $record->filearea = 'draft';
        $record->component = 'user';
        $record->filepath = $savepath;
        $record->itemid   = $itemid;
        $record->license  = $license;
        $record->author   = $author;

        $context = context_user::instance($USER->id);
        $elname = 'repo_upload_file';

        $fs = get_file_storage();
        $sm = get_string_manager();

        if ($record->filepath !== '/') {
            $record->filepath = file_correct_filepath($record->filepath);
        }

        if (!isset($_FILES[$elname])) {
            throw new moodle_exception('nofile');
        }
        if (!empty($_FILES[$elname]['error'])) {
            switch ($_FILES[$elname]['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    throw new moodle_exception('upload_error_ini_size', 'repository_upload');
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    throw new moodle_exception('upload_error_form_size', 'repository_upload');
                    break;
                case UPLOAD_ERR_PARTIAL:
                    throw new moodle_exception('upload_error_partial', 'repository_upload');
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new moodle_exception('upload_error_no_file', 'repository_upload');
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    throw new moodle_exception('upload_error_no_tmp_dir', 'repository_upload');
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    throw new moodle_exception('upload_error_cant_write', 'repository_upload');
                    break;
                case UPLOAD_ERR_EXTENSION:
                    throw new moodle_exception('upload_error_extension', 'repository_upload');
                    break;
                default:
                    throw new moodle_exception('nofile');
            }
        }

        \core\antivirus\manager::scan_file($_FILES[$elname]['tmp_name'], $_FILES[$elname]['name'], true);

        // {@link repository::build_source_field()}
        $sourcefield = $this->get_file_source_info($_FILES[$elname]['name']);
        $record->source = self::build_source_field($sourcefield);

        if (empty($saveasfilename)) {
            $record->filename = clean_param($_FILES[$elname]['name'], PARAM_FILE);
        } else {
            $ext = '';
            $match = array();
            $filename = clean_param($_FILES[$elname]['name'], PARAM_FILE);
            if (strpos($filename, '.') === false) {
                // File has no extension at all - do not add a dot.
                $record->filename = $saveasfilename;
            } else {
                if (preg_match('/\.([a-z0-9]+)$/i', $filename, $match)) {
                    if (isset($match[1])) {
                        $ext = $match[1];
                    }
                }
                $ext = !empty($ext) ? $ext : '';
                if (preg_match('#\.(' . $ext . ')$#i', $saveasfilename)) {
                    // The saveas filename contains file extension already.
                    $record->filename = $saveasfilename;
                } else {
                    $record->filename = $saveasfilename . '.' . $ext;
                }
            }
        }

        // Check the file has some non-null contents - usually an indication that a user has
        // tried to upload a folder by mistake.
        if (!$this->check_valid_contents($_FILES[$elname]['tmp_name'])) {
            throw new moodle_exception('upload_error_invalid_file', 'repository_upload', '', $record->filename);
        }

        if ($this->mimetypes != '*') {
            // Check filetype.
            $filemimetype = file_storage::mimetype($_FILES[$elname]['tmp_name'], $record->filename);
            if (!in_array($filemimetype, $this->mimetypes)) {
                throw new moodle_exception('invalidfiletype', 'repository', '',
                    get_mimetype_description(array('filename' => $_FILES[$elname]['name'])));
            }
        }

        if (empty($record->itemid)) {
            $record->itemid = 0;
        }

        $filesize = filesize($_FILES[$elname]['tmp_name']);
        if (($maxbytes !== -1) && ($filesize > $maxbytes)) {
            $maxbytesdisplay = display_size($maxbytes, 0);
            throw new file_exception('maxbytesfile', (object) array('file' => $record->filename,
                                                                    'size' => $maxbytesdisplay));
        }

        if (file_is_draft_area_limit_reached($record->itemid, $areamaxbytes, $filesize)) {
            throw new file_exception('maxareabytes');
        }
        // Ensure the user does not upload too many draft files in a short period.
        if (file_is_draft_areas_limit_reached($USER->id)) {
            throw new file_exception('maxdraftitemids');
        }

        $record->contextid = $context->id;
        $record->userid    = $USER->id;

        if (repository::draftfile_exists($record->itemid, $record->filepath, $record->filename)) {
            $existingfilename = $record->filename;
            $unusedfilename = repository::get_unused_filename($record->itemid, $record->filepath, $record->filename);
            $record->filename = $unusedfilename;
            $storedfile = $fs->create_file_from_pathname($record, $_FILES[$elname]['tmp_name']);
            if ($overwriteexisting) {
                repository::overwrite_existing_draftfile($record->itemid, $record->filepath, $existingfilename,
                    $record->filepath, $record->filename);
                $record->filename = $existingfilename;
            } else {
                $event = array();
                $event['event'] = 'fileexists';
                $event['newfile'] = new stdClass;
                $event['newfile']->filepath = $record->filepath;
                $event['newfile']->filename = $unusedfilename;
                $event['newfile']->url = moodle_url::make_draftfile_url($record->itemid, $record->filepath,
                    $unusedfilename)->out(false);

                $event['existingfile'] = new stdClass;
                $event['existingfile']->filepath = $record->filepath;
                $event['existingfile']->filename = $existingfilename;
                $event['existingfile']->url = moodle_url::make_draftfile_url($record->itemid, $record->filepath,
                    $existingfilename)->out(false);
                return $event;
            }
        } else {
            $storedfile = $fs->create_file_from_pathname($record, $_FILES[$elname]['tmp_name']);
        }

        $logevent = \core\event\draft_file_added::create([
                'objectid' => $storedfile->get_id(),
                'context' => $context,
                'other' => [
                        'itemid' => $record->itemid,
                        'filename' => $record->filename,
                        'filesize' => $filesize,
                        'filepath' => $record->filepath,
                        'contenthash' => $storedfile->get_contenthash(),
                ],
        ]);
        $logevent->trigger();

        return array(
            'url' => moodle_url::make_draftfile_url($record->itemid, $record->filepath, $record->filename)->out(false),
            'id' => $record->itemid,
            'file' => $record->filename);
    }


    /**
     * Checks the contents of the given file is not completely NULL - this can happen if a
     * user drags & drops a folder onto a filemanager / filepicker element
     * @param string $filepath full path (including filename) to file to check
     * @return true if file has at least one non-null byte within it
     */
    protected function check_valid_contents($filepath) {
        $buffersize = 4096;

        $fp = fopen($filepath, 'r');
        if (!$fp) {
            return false; // Cannot read the file - something has gone wrong.
        }
        while (!feof($fp)) {
            // Read the file 4k at a time.
            $data = fread($fp, $buffersize);
            if (preg_match('/[^\0]+/', $data)) {
                fclose($fp);
                return true; // Return as soon as a non-null byte is found.
            }
        }
        // Entire file is NULL.
        fclose($fp);
        return false;
    }

    /**
     * Return a upload form
     * @return array
     */
    public function get_listing($path = '', $page = '') {
        global $CFG;
        $ret = array();
        $ret['nologin']  = true;
        $ret['nosearch'] = true;
        $ret['norefresh'] = true;
        $ret['list'] = array();
        $ret['dynload'] = false;
        $ret['upload'] = array('label' => get_string('attachment', 'repository'), 'id' => 'repo-form');
        $ret['allowcaching'] = true; // Indicates that result of get_listing() can be cached in filepicker.js.
        return $ret;
    }

    /**
     * supported return types
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }
}
