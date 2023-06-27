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
 * The format_tiles label converted to page event.
 *
 * @package format_tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_tiles\event;


defined('MOODLE_INTERNAL') || die();

/**
 * The format_tiles label converted to page event.
 *
 * @package    format_tiles
 * @since      Moodle 3.3
 * @copyright  2018 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class label_converted extends \core\event\course_module_created  {

    /**
     * Init method.
     */
    protected function init() {

        $this->data['objecttable'] = 'course_modules';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;

    }

    /**
     * Returns localised general event name.
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_name() {
        return get_string('converttopage', 'format_tiles');
    }
    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id {$this->userid} converted a label to a page with id {$this->contextinstanceid}.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     * @throws \moodle_exception
     */
    public function get_url() {
        return new \moodle_url('/mod/page/view.php', array('id' => $this->objectid) );
    }
}
