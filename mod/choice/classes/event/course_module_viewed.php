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
 * This file contains an event for when a choice activity is viewed.
 *
 * @package mod_choice
 * @copyright 2013 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_choice\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event for when a choice activity is viewed.
 *
 * @package    mod_choice
 * @since      Moodle 2.6
 * @copyright 2013 Adrian Greeve
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\content_viewed {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['level'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'choice';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_choice_viewed', 'choice');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'User with id ' . $this->userid . ' viewed choice activity with instance id ' . $this->objectid;
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        $url = '/mod/choice/view.php';
        return new \moodle_url($url, array('id' => $this->context->instanceid));
    }

    /**
     * replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        $url = new \moodle_url('view.php', array('id' => $this->context->instanceid));
        return array($this->courseid, 'choice', 'view', $url->out(), $this->objectid, $this->context->instanceid);
    }
}
