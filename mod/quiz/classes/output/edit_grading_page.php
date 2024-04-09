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

namespace mod_quiz\output;

use mod_quiz\structure;
use renderable;
use renderer_base;
use templatable;

/**
 * Represents the page where teachers can set up additional grade items.
 *
 * @package   mod_quiz
 * @category  output
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_grading_page implements renderable, templatable {

    /**
     * Constructor.
     *
     * @param structure $structure
     */
    public function __construct(

        /** @var structure structure of the quiz for which to display the grade edit page. */
        protected readonly structure $structure,

    ) {
    }

    public function export_for_template(renderer_base $output) {
        global $PAGE;
        /** @var edit_renderer $editrenderer */
        $editrenderer = $PAGE->get_renderer('mod_quiz', 'edit');

        // Get the list of grade items, but to be the choices for a slot, and the list to be edited.
        $gradeitemchoices = [
            0 => [
                'id' => 0,
                'choice' => get_string('gradeitemnoneselected', 'quiz'),
                'isselected' => false,
            ],
        ];
        $selectdgradeitemchoices = [];
        $gradeitems = [];
        foreach ($this->structure->get_grade_items() as $gradeitem) {
            $gradeitem = clone($gradeitem);
            unset($gradeitem->quizid);
            $gradeitem->displayname = format_string($gradeitem->name);
            $gradeitem->isused = $this->structure->is_grade_item_used($gradeitem->id);
            $gradeitem->summarks = $gradeitem->isused ?
                    $this->structure->formatted_grade_item_sum_marks($gradeitem->id) :
                    '-';

            $gradeitems[] = $gradeitem;

            $gradeitemchoices[$gradeitem->id] = (object) [
                'id' => $gradeitem->id,
                'choice' => $gradeitem->displayname,
                'isselected' => false,
            ];
            $selectdgradeitemchoices[$gradeitem->id] = clone($gradeitemchoices[$gradeitem->id]);
            $selectdgradeitemchoices[$gradeitem->id]->isselected = true;
        }

        // Get the list of quiz sections.
        $sections = [];
        foreach ($this->structure->get_sections() as $section) {
            $sections[$section->id] = (object) [
                'displayname' => $section->heading ? format_string($section->heading) : get_string('sectionnoname', 'quiz'),
                'slots' => [],
            ];
        }

        // Add the relevant slots ot each section.
        foreach ($this->structure->get_slots() as $slot) {
            if (!$this->structure->is_real_question($slot->slot)) {
                continue;
            }
            // Mark the right choice as selected.
            $choices = $gradeitemchoices;
            if ($slot->quizgradeitemid) {
                $choices[$slot->quizgradeitemid] = $selectdgradeitemchoices[$slot->quizgradeitemid];
            }

            $sections[$slot->section->id]->slots[] = (object) [
                'id' => $slot->id,
                'displaynumber' => $this->structure->get_displayed_number_for_slot($slot->slot),
                'displayname' => $editrenderer->get_question_name_for_slot(
                        $this->structure, $slot->slot, $PAGE->url),
                'maxmark' => $this->structure->formatted_question_grade($slot->slot),
                'choices' => array_values($choices),
            ];
        }

        return [
            'quizid' => $this->structure->get_quizid(),
            'hasgradeitems' => !empty($gradeitems),
            'gradeitems' => $gradeitems,
            'hasslots' => $this->structure->has_questions(),
            'sections' => array_values($sections),
            'hasmultiplesections' => count($sections) > 1,
            'nogradeitems' => ['message' => get_string('gradeitemsnoneyet', 'quiz')],
            'noslots' => ['message' => get_string('gradeitemnoslots', 'quiz')],
        ];
    }
}
