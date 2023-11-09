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

namespace qtype_ordering\output;

/**
 * Renderable class for the statement of how many sub-parts of the question the student got correct|partial|incorrect.
 *
 * @package    qtype_ordering
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class num_parts_correct extends renderable_base {

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output The output renderer.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {

        list($numright, $numpartial, $numincorrect) = $this->qa->get_question()->get_num_parts_right(
            $this->qa->get_last_qt_data());

        return [
                'numcorrect' => $numright,
                'numpartial' => $numpartial,
                'numincorrect' => $numincorrect,
        ];
    }
}
