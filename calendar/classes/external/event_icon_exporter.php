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
 * Contains event class for displaying a calendar event's icon.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use \core\external\exporter;
use \core_calendar\local\event\entities\event_interface;

/**
 * Class for displaying a calendar event's icon.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_icon_exporter extends exporter {

    /**
     * Constructor.
     *
     * @param event_interface $event
     * @param array $related The related data.
     */
    public function __construct(event_interface $event, $related = []) {
        $coursemodule = $event->get_course_module();
        $course = $event->get_course();
        $courseid = $course ? $course->get('id') : null;
        $group = $event->get_group();
        $groupid = $group ? $group->get('id') : null;
        $user = $event->get_user();
        $userid = $user ? $user->get('id') : null;
        $isactivityevent = !empty($coursemodule);
        $isglobalevent = ($course && $courseid == SITEID);
        $iscourseevent = ($course && !empty($courseid) && $courseid != SITEID && empty($groupid));
        $isgroupevent = ($group && !empty($groupid));
        $isuserevent = ($user && !empty($userid));

        if ($isactivityevent) {
            $key = 'icon';
            $component = $coursemodule->get('modname');

            if (get_string_manager()->string_exists($event->get_type(), $component)) {
                $alttext = get_string($event->get_type(), $component);
            } else {
                $alttext = get_string('activityevent', 'calendar');
            }
        } else if ($isglobalevent) {
            $key = 'i/siteevent';
            $component = 'core';
            $alttext = get_string('globalevent', 'calendar');
        } else if ($iscourseevent) {
            $key = 'i/courseevent';
            $component = 'core';
            $alttext = get_string('courseevent', 'calendar');
        } else if ($isgroupevent) {
            $key = 'i/groupevent';
            $component = 'core';
            $alttext = get_string('groupevent', 'calendar');
        } else if ($isuserevent) {
            $key = 'i/userevent';
            $component = 'core';
            $alttext = get_string('userevent', 'calendar');
        } else {
            // Default to site event icon?
            $key = 'i/siteevent';
            $component = 'core';
            $alttext = get_string('globalevent', 'calendar');
        }

        $data = new \stdClass();
        $data->key = $key;
        $data->component = $component;
        $data->alttext = $alttext;

        parent::__construct($data, $related);
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'key' => ['type' => PARAM_TEXT],
            'component' => ['type' => PARAM_TEXT],
            'alttext' => ['type' => PARAM_TEXT],
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }
}
