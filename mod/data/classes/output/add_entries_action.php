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

use moodle_url;
use templatable;
use renderable;

/**
 * Renderable class for the Add entries button in the database activity.
 *
 * @package    mod_data
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_entries_action implements templatable, renderable {

    /** @var int $id The database module id. */
    private $id;

    /**
     * The class constructor.
     *
     * @param int $id The database module id.
     * @param bool $hasentries Whether entries exist.
     */
    public function __construct(int $id) {
        $this->id = $id;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output The renderer to be used to render the add entries button.
     * @return \stdClass or null if the user has no permission to add new entries.
     */
    public function export_for_template(\renderer_base $output): ?\stdClass {
        global $PAGE, $DB;

        $database = $DB->get_record('data', ['id' => $this->id]);
        $cm = get_coursemodule_from_instance('data', $this->id);
        $currentgroup = groups_get_activity_group($cm);
        $groupmode = groups_get_activity_groupmode($cm);

        if (data_user_can_add_entry($database, $currentgroup, $groupmode, $PAGE->context)) {
            $addentrylink = new moodle_url('/mod/data/edit.php', ['d' => $this->id, 'backto' => $PAGE->url->out(false)]);
            $button = new \single_button($addentrylink, get_string('add', 'mod_data'), 'get', \single_button::BUTTON_PRIMARY);
            return $button->export_for_template($output);
        }

        return null;
    }
}
