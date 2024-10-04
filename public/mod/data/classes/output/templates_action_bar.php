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

use core\output\select_menu;
use templatable;
use renderable;

/**
 * Renderable class for the action bar elements in the template pages in the database activity.
 *
 * @package    mod_data
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class templates_action_bar implements templatable, renderable {

    /** @var int $id The database module id. */
    private $id;

    /** @var select_menu $selectmenu The URL selector object. */
    private $selectmenu;

    /** @var \action_menu $actionsselect The presets actions selector object. */
    private $actionsselect;

    /**
     * The class constructor.
     *
     * @param int $id The database module id.
     * @param select_menu $selectmenu The URL selector object.
     * @param null $unused1 This parameter has been deprecated since 4.1 and should not be used anymore.
     * @param null $unused2 This parameter has been deprecated since 4.1 and should not be used anymore.
     * @param \action_menu $actionsselect The presets actions selector object.
     */
    public function __construct(int $id, select_menu $selectmenu, $unused1, $unused2, \action_menu $actionsselect) {
        $this->id = $id;
        $this->selectmenu = $selectmenu;
        $this->actionsselect = $actionsselect;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {

        return [
            'd' => $this->id,
            'selectmenu' => $this->selectmenu->export_for_template($output),
            'actionsselect' => $this->actionsselect->export_for_template($output),
        ];
    }
}
