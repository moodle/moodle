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
 * Private folder module utility functions
 *
 * @package   mod-folder
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/mod/folder/lib.php");
require_once("$CFG->libdir/file/file_browser.php");
require_once("$CFG->libdir/filelib.php");

/**
 * Prints file folder tree view
 * @param object $folder instance
 * @param object $cm instance
 * @param object $course
 * @return void
 */
function folder_print_tree($folder, $cm, $course) {
    global $PAGE;

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $fs = get_file_storage();
    $dir = $fs->get_area_tree($context->id, 'folder_content', 0);
    echo '<div id="folder_tree">';
    echo folder_htmllize_tree($dir, $folder, $context);
    echo '</div>';
    $PAGE->requires->js_init_call('M.mod_folder.init_tree', array(true));
}

/**
 * Internal function - creates htmls structure suitable for YUI tree.
 */
function folder_htmllize_tree($dir, $folder, $context) {
    if (empty($dir['subdirs']) and empty($dir['files'])) {
        return '';
    }
    $result = '<ul>';
    foreach ($dir['subdirs'] as $subdir) {
        $result .= '<li>'.s($subdir['dirname']).' '.folder_htmllize_tree($subdir, $folder, $context).'</li>';
    }
    foreach ($dir['files'] as $file) {
        $result .= '<li><span>'.folder_get_file_link($file, $folder, $context).'</span></li>';
    }
    $result .= '</ul>';

    return $result;
}

/**
 * Returns file link
 * @param object $file
 * @param object $folder
 * @param object $context
 * @return string html link
 */
function folder_get_file_link($file, $folder, $context) {
    global $CFG, $OUTPUT;

    $strfile     = get_string('file');
    $strdownload = get_string('download');
    $urlbase     = "$CFG->wwwroot/pluginfile.php";
    $path        = '/'.$context->id.'/folder_content/'.$folder->revision.$file->get_filepath().$file->get_filename();
    $viewurl     = file_encode_url($urlbase, $path, false);
    $downloadurl = file_encode_url($urlbase, $path, true);
    $downicon    = $OUTPUT->pix_url('t/down');
    $mimeicon    = $OUTPUT->pix_url(file_mimetype_icon($file->get_mimetype()));

    $downloadurl = "&nbsp;<a href=\"$downloadurl\" title=\"" . get_string('downloadfile') . "\"><img src=\"$downicon\" class=\"iconsmall\" alt=\"$strdownload\" /></a>";
    return "<a href=\"$viewurl\" title=\"\"><img src=\"$mimeicon\" class=\"icon\" alt=\"$strfile\" />&nbsp;".s($file->get_filename()).'</a>'.$downloadurl;
}

/**
 * File browsing support class
 */
class folder_content_file_info extends file_info_stored {
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }
    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}
