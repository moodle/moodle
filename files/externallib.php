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
                'contextid' => new external_value(PARAM_INT, 'context id'),
                'component' => new external_value(PARAM_TEXT, 'component'),
                'filearea'  => new external_value(PARAM_TEXT, 'file area'),
                'itemid'    => new external_value(PARAM_INT, 'associated id'),
                'filepath'  => new external_value(PARAM_PATH, 'file path'),
                'filename'  => new external_value(PARAM_FILE, 'file name')
            )
        );
    }

    /**
     * Return moodle files listing
     * @param int $contextid
     * @param int $component
     * @param int $filearea
     * @param int $itemid
     * @param string $filepath
     * @param string $filename
     * @return array
     */
    public static function get_files($contextid, $component, $filearea, $itemid, $filepath, $filename) {
        global $CFG, $USER, $OUTPUT;
        $fileinfo = self::validate_parameters(self::get_files_parameters(), array('contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'filepath'=>$filepath, 'filename'=>$filename));

        $browser = get_file_browser();

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

        $return = array();
        $return['parents'] = array();
        $return['files'] = array();
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
                        'contextid' => $params['contextid'],
                        'component' => $params['component'],
                        'filearea'  => $params['filearea'],
                        'itemid'    => $params['itemid'],
                        'filepath'  => $params['filepath'],
                        'filename'  => $child->get_visible_name(),
                        'url'       => null,
                        'isdir'     => true
                    );
                    $list[] = $node;
                } else {
                    $node = array(
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
                            'filename' => new external_value(PARAM_FILE, ''),
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
                'contextid' => new external_value(PARAM_INT, 'context id'),
                'component' => new external_value(PARAM_ALPHAEXT, 'component'),
                'filearea'  => new external_value(PARAM_ALPHAEXT, 'file area'),
                'itemid'    => new external_value(PARAM_INT, 'associated id'),
                'filepath'  => new external_value(PARAM_PATH, 'file path'),
                'filename'  => new external_value(PARAM_FILE, 'file name'),
                'filecontent' => new external_value(PARAM_TEXT, 'file content')
            )
        );
    }

    /**
     * Uploading a file to moodle
     *
     * @param int $contextid
     * @param string $component
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @param string $filename
     * @param string $filecontent
     * @return array
     */
    public static function upload($contextid, $component, $filearea, $itemid, $filepath, $filename, $filecontent) {
        global $USER, $CFG;

        $fileinfo = self::validate_parameters(self::upload_parameters(), array('contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'filepath'=>$filepath, 'filename'=>$filename, 'filecontent'=>$filecontent));

        if (!isset($fileinfo['filecontent'])) {
            throw new moodle_exception('nofile');
        }
        // saving file
        $dir = make_upload_directory('temp/wsupload');

        if (empty($fileinfo['filename'])) {
            $filename = uniqid('wsupload').'_'.time().'.tmp';
        } else {
            $filename = $fileinfo['filename'];
        }

        if (file_exists($dir.$filename)) {
            $savedfilepath = $dir.uniqid('m').$filename;
        } else {
            $savedfilepath = $dir.$filename;
        }


        file_put_contents($savedfilepath, base64_decode($fileinfo['filecontent']));
        unset($fileinfo['filecontent']);

        if (!empty($fileinfo['filepath'])) {
            $filepath = $fileinfo['filepath'];
        } else {
            $filepath = '/';
        }

        if (isset($fileinfo['itemid'])) {
            // TODO: in user private area, itemid is always 0
            $itemid = 0;
        } else {
            throw new coding_exception('itemid cannot be empty');
        }

        if (!empty($fileinfo['contextid'])) {
            $context = get_context_instance_by_id($fileinfo['contextid']);
        } else {
            $context = get_system_context();
        }

        if (!($fileinfo['component'] == 'user' and $fileinfo['filearea'] == 'private')) {
            throw new coding_exception('File can be uploaded to user private area only');
        } else {
            // TODO: hard-coded to use user_private area
            $component = 'user';
            $filearea = 'private';
        }

        $browser = get_file_browser();

        // check existing file
        if ($file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename)) {
            throw new moodle_exception('fileexist');
        }

        // move file to filepool
        if ($dir = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, '.')) {
            $info = $dir->create_file_from_pathname($filename, $savedfilepath);
            $params = $info->get_params();
            unlink($savedfilepath);
            return array(
                'contextid'=>$params['contextid'],
                'component'=>$params['component'],
                'filearea'=>$params['filearea'],
                'itemid'=>$params['itemid'],
                'filepath'=>$params['filepath'],
                'filename'=>$params['filename'],
                'url'=>$info->get_url()
                );
        } else {
            throw new moodle_exception('nofile');
        }
    }

    /**
     * Returns description of upload returns
     * @return external_multiple_structure
     */
    public static function upload_returns() {
        return new external_single_structure(
             array(
                 'contextid' => new external_value(PARAM_INT, ''),
                 'component' => new external_value(PARAM_ALPHAEXT, ''),
                 'filearea'  => new external_value(PARAM_ALPHAEXT, ''),
                 'itemid'   => new external_value(PARAM_INT, ''),
                 'filepath' => new external_value(PARAM_TEXT, ''),
                 'filename' => new external_value(PARAM_FILE, ''),
                 'url'      => new external_value(PARAM_TEXT, ''),
             )
        );
    }
}
