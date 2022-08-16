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

namespace mod_data\output;

use mod_data\manager;
use moodle_url;
use templatable;
use renderable;

/**
 * Renderable class for the action bar elements for an empty database activity.
 *
 * @package    mod_data
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class empty_database_action_bar implements templatable, renderable {

    /** @var manager The manager instance. */
    protected $manager;

    /**
     * The class constructor.
     *
     * @param int $id The database module id.
     */
    public function __construct(manager $manager) {
        $this->manager = $manager;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output The renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        global $PAGE;

        $instance = $this->manager->get_instance();
        $addentrybutton = new add_entries_action($instance->id);
        $data = ['addentrybutton' => $addentrybutton->export_for_template($output)];

        if (has_capability('mod/data:manageentries', $PAGE->context)) {
            $params = ['d' => $instance->id, 'backto' => $PAGE->url->out(false)];

            $importentrieslink = new moodle_url('/mod/data/import.php', $params);
            $importentriesbutton = new \single_button($importentrieslink,
                get_string('importentries', 'mod_data'), 'get', false);
            $data['importentriesbutton'] = $importentriesbutton->export_for_template($output);
        }

        return $data;
    }
}

