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

defined('MOODLE_INTERNAL') || die;

use \core_grades\output\action_bar;
use core_message\helper;
use core_message\api;

/**
 * Renderer class for the grade pages.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_grades_renderer extends plugin_renderer_base {

    /**
     * Renders the action bar for a given page.
     *
     * @param action_bar $actionbar
     * @return string The HTML output
     */
    public function render_action_bar(action_bar $actionbar): string {
        $data = $actionbar->export_for_template($this);
        return $this->render_from_template($actionbar->get_template(), $data);
    }

    /**
     * Renders the group selector trigger element.
     *
     * @param object $course The course object.
     * @param string|null $groupactionbaseurl The base URL for the group action.
     * @return string|null The raw HTML to render.
     */
    public function group_selector(object $course, ?string $groupactionbaseurl = null): ?string {
        global $USER;

        // Make sure that group mode is enabled.
        if (!$groupmode = $course->groupmode) {
            return null;
        }

        $label = $groupmode == VISIBLEGROUPS ? get_string('selectgroupsvisible') :
            get_string('selectgroupsseparate');

        $data = [
            'name' => 'group',
            'label' => $label,
            'courseid' => $course->id,
            'groupactionbaseurl' => $groupactionbaseurl
        ];

        $context = context_course::instance($course->id);

        if ($groupmode == VISIBLEGROUPS || has_capability('moodle/site:accessallgroups', $context)) {
            $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
        } else {
            $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
        }

        $activegroup = groups_get_course_group($course, true, $allowedgroups);
        $data['group'] = $activegroup;

        if ($activegroup) {
            $group = groups_get_group($activegroup);
            $data['selectedgroup'] = format_string($group->name, true, ['context' => $context]);
        } else if ($activegroup === 0) {
            $data['selectedgroup'] = get_string('allparticipants');
        }

        $this->page->requires->js_call_amd('core_grades/searchwidget/group', 'init');
        return $this->render_from_template('core_grades/group_selector', $data);
    }

    /**
     * Build the data to render the initials bar filter within the gradebook.
     * Using this initials selector means you'll have to retain the use of the templates & JS to handle form submission.
     * If a simple redirect on each selection is desired the standard user_search() within the user renderer is what you are after.
     *
     * @param object $course The course object.
     * @param context $context Our current context.
     * @param string $slug The slug for the report that called this function.
     * @return stdClass The data to output.
     */
    public function initials_selector(
        object $course,
        context $context,
        string $slug
    ): stdClass {
        global $SESSION, $COURSE;
        // User search.
        $url = new moodle_url($slug, ['id' => $course->id]);
        $firstinitial = $SESSION->gradereport["filterfirstname-{$context->id}"] ?? '';
        $lastinitial  = $SESSION->gradereport["filtersurname-{$context->id}"] ?? '';

        $renderer = $this->page->get_renderer('core_user');
        $initialsbar = $renderer->partial_user_search($url, $firstinitial, $lastinitial, true);

        $currentfilter = '';
        if ($firstinitial !== '' && $lastinitial !== '') {
            $currentfilter = get_string('filterbothactive', 'grades', ['first' => $firstinitial, 'last' => $lastinitial]);
        } else if ($firstinitial !== '') {
            $currentfilter = get_string('filterfirstactive', 'grades', ['first' => $firstinitial]);
        } else if ($lastinitial !== '') {
            $currentfilter = get_string('filterlastactive', 'grades', ['last' => $lastinitial]);
        }

        $this->page->requires->js_call_amd('core_grades/searchwidget/initials', 'init', [$slug]);

        $formdata = (object) [
            'courseid' => $COURSE->id,
            'initialsbars' => $initialsbar,
        ];
        $dropdowncontent = $this->render_from_template('core_grades/initials_dropdown_form', $formdata);

        return (object) [
             'buttoncontent' => $currentfilter !== '' ? $currentfilter : get_string('filterbyname', 'core_grades'),
             'buttonheader' => $currentfilter !== '' ? get_string('name') : null,
             'dropdowncontent' => $dropdowncontent,
        ];
    }

    /**
     * Creates and renders a custom user heading.
     *
     * @param stdClass $user The user object.
     * @param int $courseid The course ID.
     * @param bool $showbuttons Whether to display buttons (message, add to contacts) within the heading.
     * @return string The raw HTML to render.
     */
    public function user_heading(stdClass $user, int $courseid, bool $showbuttons = true) : string {
        global $USER;

        $headingdata = [
            'userprofileurl' => (new moodle_url('/user/view.php', ['id' => $user->id, 'course' => $courseid]))->out(false),
            'name' => fullname($user),
            'image' => $this->user_picture($user, ['size' => 50, 'link' => false])
        ];

        if ($showbuttons) {
            // Generate the data for the 'message' button.
            $messagelinkattributes = array_map(function($name, $value) {
                return ['name' => $name, 'value' => $value];
            }, array_keys(helper::messageuser_link_params($user->id)), helper::messageuser_link_params($user->id));
            $messagelinkattributes[] = ['name' => 'class', 'value' => 'btn px-0'];

            $headingdata['buttons'][] = [
                'title' => get_string('message', 'message'),
                'url' => (new moodle_url('/message/index.php', ['id' => $user->id]))->out(false),
                'icon' => ['name' => 't/message', 'component' => 'core'],
                'linkattributes' => $messagelinkattributes
            ];
            // Include js for messaging.
            helper::messageuser_requirejs();

            if ($USER->id != $user->id) {
                // Generate the data for the 'contact' button.
                $iscontact = api::is_contact($USER->id, $user->id);
                $contacttitle = $iscontact ? 'removefromyourcontacts' : 'addtoyourcontacts';
                $contacturlaction = $iscontact ? 'removecontact' : 'addcontact';
                $contacticon = $iscontact ? 't/removecontact' : 't/addcontact';

                $togglelinkparams = helper::togglecontact_link_params($user, $iscontact, false);
                $togglecontactlinkattributes = array_map(function($name, $value) {
                    if ($name === 'class') {
                        $value .= ' btn px-0';
                    }
                    return ['name' => $name, 'value' => $value];
                }, array_keys($togglelinkparams), $togglelinkparams);

                $headingdata['buttons'][] = [
                    'title' => get_string($contacttitle, 'message'),
                    'url' => (new moodle_url('/message/index.php', ['user1' => $USER->id, 'user2' => $user->id,
                        $contacturlaction => $user->id, 'sesskey' => sesskey()]))->out(false),
                    'icon' => ['name' => $contacticon, 'component' => 'core'],
                    'linkattributes' => $togglecontactlinkattributes
                ];
                // Include js for contact toggle.
                helper::togglecontact_requirejs();
            }
        }

        return $this->render_from_template('core_grades/user_heading', $headingdata);
    }
}
