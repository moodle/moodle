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
 * External files API
 *
 * @package    moodlecore
 * @subpackage webservice
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/filelib.php");

class moodle_file_external extends external_api {

    /**
     * Returns description of get_files parameters
     * @return external_function_parameters
     */
    public static function get_files_parameters() {
        return new external_function_parameters(
            array(
                'params' => new external_single_structure(array(
                        'contextid' => new external_value(PARAM_INT, 'context id'),
                        'component' => new external_value(PARAM_TEXT, 'component'),
                        'filearea'  => new external_value(PARAM_TEXT, 'file area'),
                        'itemid'    => new external_value(PARAM_INT, 'associated id'),
                        'filepath'  => new external_value(PARAM_RAW, 'file path'),
                        'filename'  => new external_value(PARAM_TEXT, 'file name'),
                    )
                )
            )
        );
    }

    /**
     * Return moodle files listing
     * @param array $fileinfo
     * @return array
     */
    public static function get_files($fileinfo) {

throw new coding_exception('File browsing api function is not implemented yet, sorry');

        global $CFG, $USER, $OUTPUT;
        if (empty($fileinfo['contextid'])) {
            $context  = get_system_context();
        } else {
            $context  = get_context_instance_by_id($fileinfo['contextid']);
        }
        if (empty($fileinfo['component'])) {
            $fileinfo['component'] = null;
        }
        if (empty($fileinfo['filearea'])) {
            $fileinfo['filearea'] = null;
        }
        if (empty($fileinfo['itemid'])) {
            $fileinfo['itemid'] = null;
        }
        if (empty($fileinfo['filename'])) {
            $fileinfo['filename'] = null;
        }
        if (empty($fileinfo['filepath'])) {
            $fileinfo['filepath'] = null;
        }
        try {
            $browser = get_file_browser();

            $return = array();
            $return['parents'] = array();
            $return['files'] = array();
            $file = $browser->get_file_info($context, null, null, null, null);
            if ($file = $browser->get_file_info($context, $fileinfo['component'], $fileinfo['filearea'], $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename'])) {
                $level = $file->get_parent();
                while ($level) {
                    $params = $level->get_params();
                    $params['filename'] = $level->get_visible_name();
                    array_unshift($return['parents'], $params);
                    $level = $level->get_parent();
                }
                $list = array();
                $children = $file->get_children();
                foreach ($children as $child) {
                    $params = $child->get_params();
                    if ($child->is_directory()) {
                        $node = array(
                            //TODO: this is wrong, you need to fetch info from the child node!!!!
                            'contextid' => $params['contextid'],
                            'component' => $params['component'],
                            'filearea'  => $params['filearea'],
                            'itemid'    => $params['itemid'],
                            'filepath'  => $params['filepath'],
                            'filename'  => $child->get_visible_name(),
                            'url'       => null,
                            'isdir'     =>true
                        );
                        $list[] = $node;
                    } else {
                        $node = array(
                            //TODO: this is wrong, you need to fetch info from the child node!!!!
                            'contextid' => $params['contextid'],
                            'component' => $params['component'],
                            'filearea'  => $params['filearea'],
                            'itemid'    => $params['itemid'],
                            'filepath'  => $params['filepath'],
                            'filename'  => $child->get_visible_name(),
                            'url'       => $child->get_url(),
                            'isdir'     => false
                        );
                        $list[] = $node;
                    }
                }
            }
            $return['files'] = $list;
        } catch (Exception $e) {
            throw $e;
        }
        return $return;
    }

    /**
     * Returns description of get_files returns
     * @return external_multiple_structure
     */
    public static function get_files_returns() {
        return new external_single_structure(
            array(
                'parents' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'contextid' => new external_value(PARAM_INT, ''),
                            'component' => new external_value(PARAM_ALPHAEXT, ''),
                            'filearea'  => new external_value(PARAM_ALPHAEXT, ''),
                            'itemid'    => new external_value(PARAM_INT, ''),
                            'filepath'  => new external_value(PARAM_TEXT, ''),
                            'filename'  => new external_value(PARAM_TEXT, ''),
                        )
                    )
                ),
                'files' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'contextid' => new external_value(PARAM_INT, ''),
                            'component' => new external_value(PARAM_ALPHAEXT, ''),
                            'filearea'  => new external_value(PARAM_ALPHAEXT, ''),
                            'itemid'   => new external_value(PARAM_INT, ''),
                            'filepath' => new external_value(PARAM_TEXT, ''),
                            'filename' => new external_value(PARAM_TEXT, ''),
                            'isdir'    => new external_value(PARAM_BOOL, ''),
                            'url'      => new external_value(PARAM_TEXT, ''),
                        )
                    )
                )
            )
        );
    }

    /**
     * Returns description of upload parameters
     * @return external_function_parameters
     */
    public static function upload_parameters() {
        return new external_function_parameters(
            array(
                'params' => new external_single_structure(array(
                        'contextid' => new external_value(PARAM_INT, 'context id'),
                        'filearea'  => new external_value(PARAM_ALPHAEXT, 'file area'),
                        'component' => new external_value(PARAM_ALPHAEXT, 'component'),
                        'itemid'    => new external_value(PARAM_INT, 'associated id'),
                        'filepath'  => new external_value(PARAM_RAW, 'file path'),
                        'filename'  => new external_value(PARAM_TEXT, 'file name'),
                        'filecontent' => new external_value(PARAM_TEXT, 'file content')
                    )
                )
            )
        );
    }

    /**
     * Uploading a file to moodle
     *
     * @param array $fileinfo
     * @return array
     */
    public static function upload($fileinfo) {
        global $USER, $CFG;
        debug('testing');

        if (!isset($fileinfo['filecontent'])) {
            throw new moodle_exception('nofile');
        }
        // saving file
        if (!file_exists($CFG->dataroot.'/temp/wsupload')) {
            mkdir($CFG->dataroot.'/temp/wsupload/', 0777, true);
        }

        if (is_dir($CFG->dataroot.'/temp/wsupload')) {
            $dir = $CFG->dataroot.'/temp/wsupload/';
        }

        if (empty($fileinfo['filename'])) {
            $filename = uniqid('wsupload').'_'.time().'.tmp';
        } else {
            $filename = $fileinfo['filename'];
        }

        if (file_exists($dir.$filename)) {
            $filename = uniqid('m').$filename;
        }

        $savedfilepath = $dir.$filename;

        file_put_contents($savedfilepath, base64_decode($fileinfo['filecontent']));
        unset($fileinfo['filecontent']);

        $component = $fileinfo['component'];

        //TODO: mandatory!!!
        if (!empty($fileinfo['filearea'])) {
            $filearea = $fileinfo['filearea'];
        } else {
            $filearea = null;
        }

        if (!empty($fileinfo['filepath'])) {
            $filepath = $fileinfo['filepath'];
        } else {
            $filepath = '';
        }

        if (isset($fileinfo['itemid'])) {
            $itemid = $fileinfo['itemid'];
        } else {
            $itemid = (int)substr(hexdec(uniqid()), 0, 9)+rand(1,100);
        }
        if (!empty($fileinfo['contextid'])) {
            $context = get_context_instance_by_id($fileinfo['contextid']);
        } else {
            $context = get_system_context();
        }


// TODO: we MUST obey access control restrictions here, no messing with file_storage here, the only allowed way is to use file_browser here!!!!!!!!!!!!!!!!!!!!!!!!
throw new coding_exception('File upload ext api needs to be made secure first!!!!');


        $browser = get_file_browser();

        // check existing file
        if ($file = $fs->get_file($context->id, $component, $filearea, $itemid, $filepath, $filename)) {
            throw new moodle_exception('fileexist');
        }

        $file_record = new object();
        $file_record->contextid = $context->id;
        $file_record->component = $component;
        $file_record->filearea  = $filearea;
        $file_record->itemid    = $itemid;
        $file_record->filepath  = $filepath;
        $file_record->filename  = $filename;
        $file_record->userid    = $USER->id;

        // move file to filepool
        try {
            $file = $fs->create_file_from_pathname($file_record, $savedfilepath);
            unlink($savedfilepath);
        } catch (Exception $ex) {
            throw $ex;
        }
        $info = $browser->get_file_info($context, $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());

        return array(
            'filename'=>$file->get_filename(),
            'filepath'=>$file->get_filepath(),
            'filearea'=>$file->get_filearea(),
            'url'=>$info->get_url()
            );
    }

    /**
     * Returns description of upload returns
     * @return external_multiple_structure
     */
    public static function upload_returns() {
        return new external_single_structure(
             array(
                 'filename' => new external_value(PARAM_TEXT, ''),
                 'filepath' => new external_value(PARAM_TEXT, ''),
                 'filearea' => new external_value(PARAM_TEXT, ''),
                 'url' => new external_value(PARAM_TEXT, ''),
             )
        );
    }

}
