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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_cps extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_cps');
    }

    public function applicable_formats() {
        return array('site' => true, 'my' => true, 'course' => false);
    }

    public function has_config() {
        return true;
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $CFG, $OUTPUT, $USER;

        require_once($CFG->dirroot . '/blocks/cps/classes/lib.php');

        $sections = ues_user::sections(true);
        $semesters = ues_semester::merge_sections($sections);

        $content = new stdClass;

        $content->items = array();
        $content->icons = array();
        $content->footer = '';

        $preferences = cps_preferences::settings();

        foreach ($preferences as $setting => $name) {
            $url = new moodle_url("/blocks/cps/$setting.php");

            $obj = 'cps_' . $setting;

            if (!$obj::is_valid($semesters) && $setting !== 'setting') {
                continue;
            }

            $content->items[] = html_writer::link($url, $OUTPUT->pix_icon($setting, $name,
                'block_cps', array('class' => 'icon')) . $name) . ' ' .
                $OUTPUT->help_icon($setting, 'block_cps');
        }

        $this->content = $content;
        return $content;
    }
}
