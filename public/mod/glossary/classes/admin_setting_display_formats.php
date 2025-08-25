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

use core_admin\admin_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for glossary display formats management.
 *
 * @package mod_glossary
 * @copyright 2021 Andrew Davis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_glossary_admin_setting_display_formats extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('glossarydisplayformats', get_string('displayformatssetup', 'glossary'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @param string $data Unused
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available display formats
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        global $DB;

        if (parent::is_related($query)) {
            return true;
        }

        $query = core_text::strtolower($query);
        $formats = $DB->get_records("glossary_formats");
        foreach ($formats as $format) {
            if (strpos(core_text::strtolower($format->name), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
            $localised = get_string("displayformat$format->name", "glossary");
            if (strpos(core_text::strtolower($localised), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT, $DB;

        $stredit = get_string("edit");
        $strhide = get_string("hide");
        $strshow = get_string("show");

        $str = $OUTPUT->heading(get_string('displayformatssetup', 'glossary'), 3, 'main', true);

        $recformats = $DB->get_records("glossary_formats");
        $formats = array();

        // Build alphabetized list of formats.
        foreach ($recformats as $format) {
            $formats[get_string("displayformat$format->name", "glossary")] = $format;
        }
        ksort($formats);

        $table = new html_table();
        $table->align = array('left', 'center');
        foreach ($formats as $formatname => $format) {
            $editicon = html_writer::link(
                new moodle_url(
                    '/mod/glossary/formats.php',
                    array('id' => $format->id, 'mode' => 'edit')
                ),
                $OUTPUT->pix_icon('t/edit', $stredit),
                array('title' => $stredit));

            if ( $format->visible ) {
                $vtitle = $strhide;
                $vicon  = "t/hide";
            } else {
                $vtitle = $strshow;
                $vicon  = "t/show";
            }

            $visibleicon = html_writer::link(
                new moodle_url(
                    '/mod/glossary/formats.php',
                    array('id' => $format->id, 'mode' => 'visible', 'sesskey' => sesskey())
                ),
                $OUTPUT->pix_icon($vicon, $vtitle),
                array('title' => $vtitle)
            );

            $table->data[] = array(
                $formatname,
                $editicon . '&nbsp;&nbsp;' . $visibleicon
            );
        }
        $str .= html_writer::table($table);

        return highlight($query, $str);
    }
}
