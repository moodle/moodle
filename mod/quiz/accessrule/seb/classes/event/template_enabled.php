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
 * Event for when a template is enabled.
 *
 * @package    quizaccess_seb
 * @author     Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb\event;

use context_system;
use core\event\base;
use quizaccess_seb\template;

defined('MOODLE_INTERNAL') || die();

/**
 * Event for when a template is enabled.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_enabled extends base {

    /**
     * Create event with strict parameters.
     *
     * Define strict parameters to create event with instead of relying on internal validation of array. Better code practice.
     * Easier for consumers of this class to know what data must be supplied and observers can have more trust in event data.
     *
     * @param template $template SEB template.
     * @param context_system $context Context system.
     * @return base
     */
    public static function create_strict(template $template, context_system $context) : base {
        global $USER;
        $tid = $template->get('id');

        return self::create([
            'userid' => $USER->id,
            'objectid' => $tid,
            'context' => $context,
        ]);
    }

    /**
     * Initialize the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'quizaccess_seb_template';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Get the name of the event.
     *
     * @return string Name of event.
     */
    public static function get_name() {
        return get_string('event:templateenabled', 'quizaccess_seb');
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        $params = [
            'id' => $this->objectid,
            'action' => 'edit',
        ];
        return new \moodle_url('/mod/quiz/accessrule/seb/template.php', $params);
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string Description.
     */
    public function get_description() {
        return "The user with id '$this->userid' has enabled a template with id '$this->objectid'.";
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the objectid to it's new value in the new course.
     *
     * @return array Mapping of object id.
     */
    public static function get_objectid_mapping() : array {
        return array('db' => 'quizaccess_seb_template', 'restore' => 'quizaccess_seb_template');
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the information in 'other' to it's new value in the new course.
     *
     * @return array List of mapping of other ids.
     */
    public static function get_other_mapping() : array {
        return [];
    }
}
