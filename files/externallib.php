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
 * @package    core_files
 * @category   external
 * @copyright  2010 Dongsheng Cai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/filelib.php");

/**
 * Files external functions
 *
 * @package    core_files
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class core_files_external extends external_api {

    /**
     * Returns description of get_files parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function get_files_parameters() {
        return new external_function_parameters(
            array(
                'contextid'    => new external_value(PARAM_INT, 'context id Set to -1 to use contextlevel and instanceid.'),
                'component'    => new external_value(PARAM_TEXT, 'component'),
                'filearea'     => new external_value(PARAM_TEXT, 'file area'),
                'itemid'       => new external_value(PARAM_INT, 'associated id'),
                'filepath'     => new external_value(PARAM_PATH, 'file path'),
                'filename'     => new external_value(PARAM_TEXT, 'file name'),
                'modified'     => new external_value(PARAM_INT, 'timestamp to return files changed after this time.', VALUE_DEFAULT, null),
                'contextlevel' => new external_value(PARAM_ALPHA, 'The context level for the file location.', VALUE_DEFAULT, null),
                'instanceid'   => new external_value(PARAM_INT, 'The instance id for where the file is located.', VALUE_DEFAULT, null)

            )
        );
    }

    /**
     * Return moodle files listing
     *
     * @param int $contextid context id
     * @param int $component component
     * @param int $filearea file area
     * @param int $itemid item id
     * @param string $filepath file path
     * @param string $filename file name
     * @param int $modified timestamp to return files changed after this time.
     * @param string $contextlevel The context level for the file location.
     * @param int $instanceid The instance id for where the file is located.
     * @return array
     * @since Moodle 2.9 Returns additional fields (timecreated, filesize, author, license)
     * @since Moodle 2.2
     */
    public static function get_files($contextid, $component, $filearea, $itemid, $filepath, $filename, $modified = null,
                                     $contextlevel = null, $instanceid = null) {

        $parameters = array(
            'contextid'    => $contextid,
            'component'    => $component,
            'filearea'     => $filearea,
            'itemid'       => $itemid,
            'filepath'     => $filepath,
            'filename'     => $filename,
            'modified'     => $modified,
            'contextlevel' => $contextlevel,
            'instanceid'   => $instanceid);
        $fileinfo = self::validate_parameters(self::get_files_parameters(), $parameters);

        $browser = get_file_browser();

        // We need to preserve backwards compatibility. Zero will use the system context and minus one will
        // use the addtional parameters to determine the context.
        // TODO MDL-40489 get_context_from_params should handle this logic.
        if ($fileinfo['contextid'] == 0) {
            $context = context_system::instance();
        } else {
            if ($fileinfo['contextid'] == -1) {
                $fileinfo['contextid'] = null;
            }
            $context = self::get_context_from_params($fileinfo);
        }
        self::validate_context($context);

        if (empty($fileinfo['component'])) {
            $fileinfo['component'] = null;
        }
        if (empty($fileinfo['filearea'])) {
            $fileinfo['filearea'] = null;
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
        $list = array();

        if ($file = $browser->get_file_info(
            $context, $fileinfo['component'], $fileinfo['filearea'], $fileinfo['itemid'],
                $fileinfo['filepath'], $fileinfo['filename'])) {
            $level = $file->get_parent();
            while ($level) {
                $params = $level->get_params();
                $params['filename'] = $level->get_visible_name();
                array_unshift($return['parents'], $params);
                $level = $level->get_parent();
            }
            $children = $file->get_children();
            foreach ($children as $child) {

                $params = $child->get_params();
                $timemodified = $child->get_timemodified();
                $timecreated = $child->get_timecreated();

                if ($child->is_directory()) {
                    if ((is_null($modified)) or ($modified < $timemodified)) {
                        $node = array(
                            'contextid' => $params['contextid'],
                            'component' => $params['component'],
                            'filearea'  => $params['filearea'],
                            'itemid'    => $params['itemid'],
                            'filepath'  => $params['filepath'],
                            'filename'  => $child->get_visible_name(),
                            'url'       => null,
                            'isdir'     => true,
                            'timemodified' => $timemodified,
                            'timecreated' => $timecreated,
                            'filesize' => 0,
                            'author' => null,
                            'license' => null
                           );
                           $list[] = $node;
                    }
                } else {
                    if ((is_null($modified)) or ($modified < $timemodified)) {
                        $node = array(
                            'contextid' => $params['contextid'],
                            'component' => $params['component'],
                            'filearea'  => $params['filearea'],
                            'itemid'    => $params['itemid'],
                            'filepath'  => $params['filepath'],
                            'filename'  => $child->get_visible_name(),
                            'url'       => $child->get_url(),
                            'isdir'     => false,
                            'timemodified' => $timemodified,
                            'timecreated' => $timecreated,
                            'filesize' => $child->get_filesize(),
                            'author' => $child->get_author(),
                            'license' => $child->get_license()
                        );
                           $list[] = $node;
                    }
                }
            }
        }
        $return['files'] = $list;
        return $return;
    }

    /**
     * Returns description of get_files returns
     *
     * @return external_single_structure
     * @since Moodle 2.9 Returns additional fields for files (timecreated, filesize, author, license)
     * @since Moodle 2.2
     */
    public static function get_files_returns() {
        return new external_single_structure(
            array(
                'parents' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'contextid' => new external_value(PARAM_INT, ''),
                            'component' => new external_value(PARAM_COMPONENT, ''),
                            'filearea'  => new external_value(PARAM_AREA, ''),
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
                            'component' => new external_value(PARAM_COMPONENT, ''),
                            'filearea'  => new external_value(PARAM_AREA, ''),
                            'itemid'   => new external_value(PARAM_INT, ''),
                            'filepath' => new external_value(PARAM_TEXT, ''),
                            'filename' => new external_value(PARAM_TEXT, ''),
                            'isdir'    => new external_value(PARAM_BOOL, ''),
                            'url'      => new external_value(PARAM_TEXT, ''),
                            'timemodified' => new external_value(PARAM_INT, ''),
                            'timecreated' => new external_value(PARAM_INT, 'Time created', VALUE_OPTIONAL),
                            'filesize' => new external_value(PARAM_INT, 'File size', VALUE_OPTIONAL),
                            'author' => new external_value(PARAM_TEXT, 'File owner', VALUE_OPTIONAL),
                            'license' => new external_value(PARAM_TEXT, 'File license', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Returns description of upload parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function upload_parameters() {
        return new external_function_parameters(
            array(
                'contextid' => new external_value(PARAM_INT, 'context id', VALUE_DEFAULT, null),
                'component' => new external_value(PARAM_COMPONENT, 'component'),
                'filearea'  => new external_value(PARAM_AREA, 'file area'),
                'itemid'    => new external_value(PARAM_INT, 'associated id'),
                'filepath'  => new external_value(PARAM_PATH, 'file path'),
                'filename'  => new external_value(PARAM_FILE, 'file name'),
                'filecontent' => new external_value(PARAM_TEXT, 'file content'),
                'contextlevel' => new external_value(PARAM_ALPHA, 'The context level to put the file in,
                        (block, course, coursecat, system, user, module)', VALUE_DEFAULT, null),
                'instanceid' => new external_value(PARAM_INT, 'The Instance id of item associated
                         with the context level', VALUE_DEFAULT, null)
            )
        );
    }

    /**
     * Uploading a file to moodle
     *
     * @param int    $contextid    context id
     * @param string $component    component
     * @param string $filearea     file area
     * @param int    $itemid       item id
     * @param string $filepath     file path
     * @param string $filename     file name
     * @param string $filecontent  file content
     * @param string $contextlevel Context level (block, course, coursecat, system, user or module)
     * @param int    $instanceid   Instance id of the item associated with the context level
     * @return array
     * @since Moodle 2.2
     */
    public static function upload($contextid, $component, $filearea, $itemid, $filepath, $filename, $filecontent, $contextlevel, $instanceid) {
        global $USER, $CFG;

        $fileinfo = self::validate_parameters(self::upload_parameters(), array(
                'contextid' => $contextid, 'component' => $component, 'filearea' => $filearea, 'itemid' => $itemid,
                'filepath' => $filepath, 'filename' => $filename, 'filecontent' => $filecontent, 'contextlevel' => $contextlevel,
                'instanceid' => $instanceid));

        if (!isset($fileinfo['filecontent'])) {
            throw new moodle_exception('nofile');
        }
        // Saving file.
        $dir = make_temp_directory('wsupload');

        if (empty($fileinfo['filename'])) {
            $filename = uniqid('wsupload', true).'_'.time().'.tmp';
        } else {
            $filename = $fileinfo['filename'];
        }

        if (file_exists($dir.$filename)) {
            $savedfilepath = $dir.uniqid('m').$filename;
        } else {
            $savedfilepath = $dir.$filename;
        }

        file_put_contents($savedfilepath, base64_decode($fileinfo['filecontent']));
        @chmod($savedfilepath, $CFG->filepermissions);
        unset($fileinfo['filecontent']);

        if (!empty($fileinfo['filepath'])) {
            $filepath = $fileinfo['filepath'];
        } else {
            $filepath = '/';
        }

        // Only allow uploads to draft area
        if (!($fileinfo['component'] == 'user' and $fileinfo['filearea'] == 'draft')) {
            throw new coding_exception('File can be uploaded to user draft area only');
        } else {
            $component = 'user';
            $filearea = $fileinfo['filearea'];
        }

        $itemid = 0;
        if (isset($fileinfo['itemid'])) {
            $itemid = $fileinfo['itemid'];
        }
        if ($filearea == 'draft' && $itemid <= 0) {
            // Generate a draft area for the files.
            $itemid = file_get_unused_draft_itemid();
        } else if ($filearea == 'private') {
            // TODO MDL-31116 in user private area, itemid is always 0.
            $itemid = 0;
        }

        // We need to preserve backword compatibility. Context id is no more a required.
        if (empty($fileinfo['contextid'])) {
            unset($fileinfo['contextid']);
        }

        // Get and validate context.
        $context = self::get_context_from_params($fileinfo);
        self::validate_context($context);
        if (($fileinfo['component'] == 'user' and $fileinfo['filearea'] == 'private')) {
            throw new moodle_exception('privatefilesupload');
        }

        $browser = get_file_browser();

        // Check existing file.
        if ($file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename)) {
            throw new moodle_exception('fileexist');
        }

        // Move file to filepool.
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
     *
     * @return external_single_structure
     * @since Moodle 2.2
     */
    public static function upload_returns() {
        return new external_single_structure(
             array(
                 'contextid' => new external_value(PARAM_INT, ''),
                 'component' => new external_value(PARAM_COMPONENT, ''),
                 'filearea'  => new external_value(PARAM_AREA, ''),
                 'itemid'   => new external_value(PARAM_INT, ''),
                 'filepath' => new external_value(PARAM_TEXT, ''),
                 'filename' => new external_value(PARAM_FILE, ''),
                 'url'      => new external_value(PARAM_TEXT, ''),
             )
        );
    }
}
