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

namespace mod_quiz\question\bank;

/**
 * A column type for the add this question to the quiz action.
 *
 * @package    mod_quiz
 * @category   question
 * @copyright  2009 Tim Hunt
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_action_column extends \core_question\local\bank\column_base {

    /** @var string caches a lang string used repeatedly. */
    protected $stradd;

    public function init(): void {
        parent::init();
        $this->stradd = get_string('addtoquiz', 'quiz');
    }

    public function get_extra_classes(): array {
        return ['iconcol'];
    }

    public function get_title(): string {
        return '&#160;';
    }

    public function get_name() {
        return 'addtoquizaction';
    }

    protected function display_content($question, $rowclasses) {
        global $OUTPUT;
        if (!question_has_capability_on($question, 'use')) {
            return;
        }
        $link = new \action_link(
                $this->qbank->add_to_quiz_url($question->id),
                '',
                null,
                ['title' => $this->stradd],
                new \pix_icon('t/add', $this->stradd));
        echo $OUTPUT->render($link);
    }
}
