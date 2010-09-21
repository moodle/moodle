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
 * File manager support
 *
 * @package    core
 * @subpackage file
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require('../config.php');
require_once($CFG->libdir.'/filelib.php');

$action = optional_param('action', 'list', PARAM_ALPHA);

$PAGE->set_context(get_system_context());
require_login();

echo $OUTPUT->header(); // send headers

$err = new stdClass();
if (isguestuser()) {
    $err->error = get_string('noguest');
    die(json_encode($err));
}

switch ($action) {
    // used by course file tree viewer
    case 'getfiletree':
        $contextid  = required_param('contextid', PARAM_INT);
        $component  = required_param('component', PARAM_ALPHAEXT);
        $filearea   = required_param('filearea', PARAM_ALPHAEXT);
        $itemid     = required_param('itemid', PARAM_INT);
        $filepath   = required_param('filepath', PARAM_PATH);

        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info(get_context_instance_by_id($contextid), $component, $filearea, $itemid, $filepath);
        $children = $fileinfo->get_children();
        $tree = array();
        foreach ($children as $child) {
            $filedate = $child->get_timemodified();
            $filesize = $child->get_filesize();
            $mimetype = $child->get_mimetype();
            $params = $child->get_params();
            $url = new moodle_url('/files/index.php', $params);
            $fileitem = array(
                    'params'=>$params,
                    'filename'=>$child->get_visible_name(),
                    'filedate'=>$filedate ? userdate($filedate) : '',
                    'filesize'=>$filesize ? display_size($filesize) : '',
                    );
            if ($child->is_directory()) {
                $fileitem['isdir'] = true;
                $fileitem['url'] = $url->out(false);
                $fileitem['icon'] = $OUTPUT->pix_icon('f/folder', get_string('icon'));
            } else {
                $fileitem['url'] = $child->get_url();
                $fileitem['icon'] = $OUTPUT->pix_icon('f/'.mimeinfo('icon', $child->get_visible_name()), get_string('icon'));
            }
            $tree[] = $fileitem;
        }
        echo json_encode($tree);
        break;

    default:
        break;
}
