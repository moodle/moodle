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
 * Contains functions called by core.
 *
 * @package    block_reporttiles
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_reporttiles_reporttiles {

    /**
     * [reporttiles_log description]
     * @param  [int] $itemid [Item ID]
     * @return [String]      [url]
     */
    public function reporttiles_icon($itemid, $blockinstanceid, $reportname) {
        global $DB, $CFG, $USER, $OUTPUT;
        $reportname = str_replace(' ', '', $reportname);
        $filesql = "SELECT * FROM {files} WHERE itemid = $itemid AND component = :component
                    AND filearea = :filearea AND filesize <> :filesize";
        $file = $DB->get_record_sql($filesql, ['itemid' => $itemid,
            'component' => 'block_reporttiles', 'filearea' => 'reporttiles', 'filesize' => 0]);
        if (empty($file)) {
            $defaultlogoexists = $CFG->dirroot . '/blocks/reporttiles/pix/' . $reportname.'.png';
            if (file_exists($defaultlogoexists)) {
                $defaultlogo = $OUTPUT->image_url($reportname, 'block_reporttiles');
            } else {
                $defaultlogo = $OUTPUT->image_url('sample_reporttile', 'block_reporttiles');
            }
            $logo = $defaultlogo;
        } else {
            $context = context_block::instance($blockinstanceid);
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'block_reporttiles', 'reporttiles', $file->itemid, 'filename', false);
            $url = array();
            if (!empty($files)) {
                foreach ($files as $file) {
                    $isimage = $file->is_valid_image();
                    $filename = $file->get_filename();
                    $ctxid = $file->get_contextid();
                    $component = $file->get_component();
                    $itemid = $file->get_itemid();
                    if ($isimage) {
                        $url[] = $CFG->wwwroot."/pluginfile.php/$ctxid/block_reporttiles/reporttiles/$itemid/$filename";
                    }
                }
                if (!empty($url[0])) {
                    $logo = $url[0];
                } else {
                    $defaultlogo = $OUTPUT->image_url('sample_reporttile', 'block_reporttiles');
                    $logo = $defaultlogo;
                }
            } else {
                return $OUTPUT->image_url('sample_reporttile', 'block_reporttiles');
            }
        }
        return  $logo;
    }
}
