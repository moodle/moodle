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
 * Renderable class for the action bar elements in the presets page in the database activity.
 *
 * @package    mod_data
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class presets_action_bar implements templatable, renderable {

    /** @var int $id The database module id. */
    private $cmid;

    /**
     * The class constructor.
     *
     * @param int $cmid The database module id
     */
    public function __construct(int $cmid) {
        $this->cmid = $cmid;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output The renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $importpresetlink = new moodle_url('/mod/data/preset.php', [
                'id' => $this->cmid, 'action' => 'import'
        ]);
        return [
            'id' => $this->cmid,
            'importpreseturl' => $importpresetlink->out(false),
        ];
    }
}
