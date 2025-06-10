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
 * Renderable class for the action bar elements in the zero state (no fields created) pages in the database activity.
 *
 * @package    mod_data
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class zero_state_action_bar implements templatable, renderable {

    /** @var manager The manager instance. */
    protected $manager;

    /**
     * The class constructor.
     *
     * @param manager $manager The manager instance.
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

        $data = [];
        if ($this->manager->can_manage_templates()) {
            $cm = $this->manager->get_coursemodule();
            $instance = $this->manager->get_instance();
            $params = ['id' => $cm->id, 'backto' => $PAGE->url->out(false)];

            $usepresetlink = new moodle_url('/mod/data/preset.php', $params);
            $usepresetbutton = new \single_button($usepresetlink,
                get_string('usestandard', 'mod_data'), 'get', true);
            $data['usepresetbutton'] = $usepresetbutton->export_for_template($output);

            $actionbar = new \mod_data\output\action_bar($instance->id, $PAGE->url);
            $createfieldbutton = $actionbar->get_create_fields();
            $data['createfieldbutton'] = $createfieldbutton->export_for_template($output);

            $importpresetlink = new moodle_url('/mod/data/preset.php', $params);
            $importpresetbutton = new \single_button($importpresetlink,
                get_string('importapreset', 'mod_data'), 'get', false, [
                    'data-action' => 'importpresets',
                    'data-dataid' => $cm->id,
                ]);
            $data['importpresetbutton'] = $importpresetbutton->export_for_template($output);
        }

        return $data;
    }
}
