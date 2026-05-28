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
use core\router\util;
use core_course\route\controller\course_navigation;

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
     * @param int $cmid The course module ID.
     */
    public function __construct(
        /** @var int The course module ID. */
        private int $cmid
    ) {
    }

    #[\Override]
    public function export_for_template(renderer_base $output) {
        $previousurl = util::get_path_for_callable(
            [course_navigation::class, 'cm_previous_element'],
            ['cm' => $this->cmid],
        );
        $nexturl = util::get_path_for_callable(
            [course_navigation::class, 'cm_next_element'],
            ['cm' => $this->cmid],
        );

        return [
            'previousurl' => $previousurl->out(false),
            'nexturl' => $nexturl->out(false),
        ];
    }

    #[\Override]
    public function get_template_name(\renderer_base $renderer): string {
        return 'core_courseformat/local/linearnavigation/footer_content';
    }
}
