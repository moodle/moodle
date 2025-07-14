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
use core_course\output\activity_icon;

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
        global $PAGE;
        $coursemodule = $event->get_course_module();
        $category = $event->get_category();
        $categoryid = $category ? $category->get('id') : null;
        $course = $event->get_course();
        $courseid = $course ? $course->get('id') : null;
        $group = $event->get_group();
        $groupid = $group ? $group->get('id') : null;
        $user = $event->get_user();
        $userid = $user ? $user->get('id') : null;
        $isactivityevent = !empty($coursemodule);
        $issiteevent = ($course && $courseid == SITEID);
        $iscategoryevent = ($category && !empty($categoryid));
        $iscourseevent = ($course && !empty($courseid) && $courseid != SITEID && empty($groupid));
        $isgroupevent = ($group && !empty($groupid));
        $isuserevent = ($user && !empty($userid));
        $iconurl = '';
        $iconclass = '';
        $purpose = '';

        if ($isactivityevent) {
            $key = 'monologo';
            $component = $coursemodule->get('modname');

            if (get_string_manager()->string_exists($event->get_type(), $component)) {
                $alttext = get_string($event->get_type(), $component);
            } else {
                $alttext = get_string('activityevent', 'calendar');
            }

            $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();
            $activityicon = activity_icon::from_cm_info($coursemodule->get_proxied_instance())
                ->set_title($alttext);

            $iconurl = $activityicon->get_icon_url($renderer)->out(false);
            $iconclass = $activityicon->get_icon_classes($renderer);
            $purpose = $activityicon->get_purpose();
        } else if ($event->get_component()) {
            // Guess the icon and the title for the component event. By default display calendar icon and the
            // plugin name as the alttext.
            if ($PAGE->theme->resolve_image_location($event->get_type(), $event->get_component())) {
                $key = $event->get_type();
                $component = $event->get_component();
            } else {
                $key = 'i/otherevent';
                $component = 'core';
            }

            if (get_string_manager()->string_exists($event->get_type(), $event->get_component())) {
                $alttext = get_string($event->get_type(), $event->get_component());
            } else {
                $alttext = get_string('pluginname', $event->get_component());
            }
        } else if ($issiteevent) {
            $key = 'i/siteevent';
            $component = 'core';
            $alttext = get_string('typesite', 'calendar');
        } else if ($iscategoryevent) {
            $key = 'i/categoryevent';
            $component = 'core';
            $alttext = get_string('typecategory', 'calendar');
        } else if ($iscourseevent) {
            $key = 'i/courseevent';
            $component = 'core';
            $alttext = get_string('typecourse', 'calendar');
        } else if ($isgroupevent) {
            $key = 'i/groupevent';
            $component = 'core';
            $alttext = get_string('typegroup', 'calendar');
        } else if ($isuserevent) {
            $key = 'i/userevent';
            $component = 'core';
            $alttext = get_string('typeuser', 'calendar');
        } else {
            // Default to site event icon?
            $key = 'i/siteevent';
            $component = 'core';
            $alttext = get_string('typesite', 'calendar');
        }

        $data = new \stdClass();
        $data->key = $key;
        $data->component = $component;
        $data->alttext = $alttext;
        $data->iconurl = $iconurl;
        $data->iconclass = $iconclass;
        $data->purpose = $purpose;

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
            'iconurl' => ['type' => PARAM_TEXT],
            'iconclass' => ['type' => PARAM_TEXT],
            'purpose' => ['type' => PARAM_TEXT],
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
