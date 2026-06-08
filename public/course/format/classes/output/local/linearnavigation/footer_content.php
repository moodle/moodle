<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_courseformat\output\local\linearnavigation;

use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;

/**
 * Sticky footer class for linear navigation in course format.
 *
 * @package    core_courseformat
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class footer_content implements named_templatable, renderable {
    /**
     * Constructor.
     *
     * @param int $courseid The course ID.
     */
    public function __construct(
        /** @var int The course ID. */
        private int $courseid
    ) {
    }

    #[\Override]
    public function export_for_template(renderer_base $output) {
        $returnbutton = new \single_button(
            new \moodle_url('/course/view.php', ['id' => $this->courseid]),
            get_string('back'),
            'get'
        );
        return [
            'returnbutton' => $returnbutton->export_for_template($output),
        ];
    }

    #[\Override]
    public function get_template_name(\renderer_base $renderer): string {
        return 'core_courseformat/local/linearnavigation/footer_content';
    }
}
