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
namespace mod_bigbluebuttonbn\output;

use html_table;
use html_writer;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\meeting;
use mod_bigbluebuttonbn\plugin;
use renderable;
use renderer_base;
use stdClass;

/**
 * Renderer for the Index page.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index implements renderable {

    /** @var stdClass */
    protected $course;

    /** @var stdClass[] */
    protected $instances;

    /**
     * Constructor for the index renderable.
     *
     * @param stdClass $course
     * @param instance[] List of bbbbn instances
     */
    public function __construct(stdClass $course, array $instances) {
        $this->course = $course;
        $this->instances = $instances;
    }

    /**
     * Get the table for the index page.
     *
     * @param renderer_base $output
     * @return html_table
     */
    public function get_table(renderer_base $output): html_table {
        // Print the list of instances.
        $table = new html_table();
        $table->head = [
            get_string('week'),
            get_string('index_heading_name', plugin::COMPONENT),
            get_string('index_heading_group', plugin::COMPONENT),
            get_string('index_heading_users', plugin::COMPONENT),
            get_string('index_heading_viewer', plugin::COMPONENT),
            get_string('index_heading_moderator', plugin::COMPONENT),
            get_string('index_heading_recording', plugin::COMPONENT),
            get_string('index_heading_actions', plugin::COMPONENT),
        ];
        $table->align = ['center', 'left', 'center', 'center', 'center', 'center', 'center'];

        foreach ($this->instances as $instance) {
            $this->add_instance_to_table($output, $table, $instance);
        }

        return $table;
    }

    /**
     * Add details of the bigbluebuttonbn instance to the table.
     *
     * @param renderer_base $output
     * @param html_table $table
     * @param instance $instance
     */
    protected function add_instance_to_table(renderer_base $output, html_table $table, instance $instance): void {
        $cm = $instance->get_cm();
        if (!$cm->uservisible) {
            return;
        }
        if (groups_get_activity_groupmode($cm) == 0) {
            $table->data[] = $this->add_room_row_to_table($output, $instance);
        } else {
            // Add 'All participants' room information.
            $table->data[] = $this->add_room_row_to_table($output, $instance, 0);

            // Add data for the groups belonging to the bbb instance, if any.
            $groups = groups_get_activity_allowed_groups($cm);
            foreach ($groups as $group) {
                $table->data[] = $this->add_room_row_to_table($output, $instance, $group->id);
            }
        }
    }

    /**
     * Displays the general view.
     *
     * @param renderer_base $output
     * @param instance $instance
     * @param int|null $group
     * @return array
     */
    protected function add_room_row_to_table(renderer_base $output, instance $instance, ?int $group = null): array {
        if ($group) {
            $instance = instance::get_group_instance_from_instance($instance, $group);
        }
        $meeting = new meeting($instance);

        $viewurl = $instance->get_view_url();
        if ($groupid = $instance->get_group_id()) {
            $viewurl->param('group', $groupid);
        }

        $joinurl = html_writer::link($viewurl, format_string($instance->get_meeting_name()));

        // The meeting info was returned.
        if ($meeting->is_running()) {
            return [
                $instance->get_cm()->sectionnum,
                $joinurl,
                $instance->get_group_name(),
                $this->get_room_usercount($meeting),
                $this->get_room_attendee_list($meeting, 'VIEWER'),
                $this->get_room_attendee_list($meeting, 'MODERATOR'),
                $this->get_room_record_info($output, $instance),
                $this->get_room_actions($output, $instance, $meeting),
            ];
        }

        return [$instance->get_cm()->sectionnum, $joinurl, $instance->get_group_name(), '', '', '', '', ''];
    }

    /**
     * Count the number of users in the meeting.
     *
     * @param meeting $meeting
     * @return int
     */
    protected function get_room_usercount(meeting $meeting): int {
        return count($meeting->get_attendees());
    }

    /**
     * Returns attendee list.
     *
     * @param meeting $meeting
     * @param string $role
     * @return string
     */
    protected function get_room_attendee_list(meeting $meeting, string $role): string {
        $attendees = [];

        foreach ($meeting->get_attendees() as $attendee) {
            if ((string) $attendee->role == $role) {
                $attendees[] = $attendee->fullName;
            }
        }

        return implode(', ', $attendees);
    }

    /**
     * Returns indication of recording enabled.
     *
     * @param renderer_base $output
     * @param instance $instance
     * @return string
     */
    protected function get_room_record_info(renderer_base $output, instance $instance): string {
        if ($instance->is_recorded()) {
            // If it has been set when meeting created, set the variable on/off.
            return get_string('index_enabled', 'bigbluebuttonbn');
        }
        return '';
    }

    /**
     * Returns room actions.
     *
     * @param renderer_base $output
     * @param instance $instance
     * @param meeting $meeting
     * @return string
     */
    protected function get_room_actions(renderer_base $output, instance $instance, meeting $meeting): string {
        if ($instance->is_moderator()) {
            return $output->render_from_template('mod_bigbluebuttonbn/end_session_button', (object) [
                'bigbluebuttonbnid' => $instance->get_instance_id(),
                'groupid' => $instance->get_group_id(),
                'statusrunning' => $meeting->is_running(),
            ]);
        }

        return '';
    }
}
