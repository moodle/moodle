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
 * The chooser_section renderable.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use lang_string;
use stdClass;

/**
 * The chooser_section renderable class.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chooser_section implements renderable, templatable {
    /** @var string $id An identifier for the section. */
    public $id;
    /** @var lang_string $label The label of the section. */
    public $label;
    /** @var chooser_item[] $items The items in this section. */
    public $items;

    /**
     * Constructor.
     *
     * @param string $id An identifier for the section.
     * @param lang_string $label The label of the section.
     * @param chooser_item[] $items The items in this section.
     */
    public function __construct($id, lang_string $label, array $items) {
        $this->id = $id;
        $this->label = $label;
        $this->items = $items;
    }

    /**
     * Export for template.
     *
     * @param renderer_base The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->id = $this->id;
        $data->label = (string) $this->label;
        $data->items = array_map(function ($item) use ($output) {
            return $item->export_for_template($output);
        }, array_values($this->items));
        return $data;
    }
}
