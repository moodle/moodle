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
 * @package    enrol_reenroller
 * @copyright  2025 Onwards LSU Online & Continuing Education
 * @author     2025 Onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_reenroller;

defined('MOODLE_INTERNAL') || die();

// We need this.
require_once($CFG->libdir.'/adminlib.php');

// Degfine the configdate class.
class admin_setting_configdate extends \admin_setting {
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    public function get_setting() {
        return $this->config_read($this->name);
    }

    public function write_setting($data) {
        return $this->config_write($this->name, strtotime($data)) ? '' : get_string('errorsetting', 'admin');
    }

    public function output_html($data, $query='') {
        global $OUTPUT;

        $elementname = $this->get_full_name();
        $id = str_replace(':', '_', $elementname);

        $calendar = \html_writer::empty_tag('input', [
            'type' => 'date',
            'id' => $id,
            'name' => $elementname,
            'value' => $data ? date('Y-m-d', $data) : ''
        ]);

        return format_admin_setting($this, $this->visiblename, $calendar, $this->description, true, '', $query);
    }
}
