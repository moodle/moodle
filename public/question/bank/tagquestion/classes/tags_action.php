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

namespace qbank_tagquestion;

use core_question\local\bank\question_action_base;

/**
 * Action to add and remove tags to questions.
 *
 * @package   qbank_tagquestion
 * @copyright 2018 Simey Lameze <simey@moodle.com>
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags_action extends question_action_base {

    /**
     * @var string store this lang string for performance.
     */
    protected $managetags;

    /**
     * @var bool tags enabled or not from config.
     */
    protected $tagsenabled = true;

    public function init(): void {
        parent::init();
        $this->check_tags_status();
        if ($this->tagsenabled) {
            global $PAGE;
            $PAGE->requires->js_call_amd('qbank_tagquestion/edit_tags', 'init', ['#questionscontainer']);
        }
        $this->managetags = get_string('managetags', 'tag');
    }

    public function get_menu_position(): int {
        return 300;
    }

    protected function check_tags_status(): void {
        global $CFG;
        if (!$CFG->usetags) {
            $this->tagsenabled = false;
        }
    }

    protected function display_content($question, $rowclasses): void {
        global $OUTPUT;

        if (\core_tag_tag::is_enabled('core_question', 'question') &&
                question_has_capability_on($question, 'view') && $this->tagsenabled) {

            [$url, $attributes] = $this->get_link_url_and_attributes($question);
            echo \html_writer::link($url, $OUTPUT->pix_icon('t/tags',
                    $this->managetags), $attributes);
        }
    }

    protected function get_link_url_and_attributes($question): array {
        $url = new \moodle_url($this->qbank->returnurl);

        $attributes = [
                'data-action' => 'edittags',
                'data-cantag' => question_has_capability_on($question, 'tag'),
                'data-contextid' => $this->qbank->get_most_specific_context()->id,
                'data-questionid' => $question->id
        ];

        return [$url, $attributes];
    }

    public function get_action_menu_link(\stdClass $question): ?\action_menu_link {
        if (!\core_tag_tag::is_enabled('core_question', 'question') ||
                !question_has_capability_on($question, 'view') || !$this->tagsenabled) {
            return null;
        }

        [$url, $attributes] = $this->get_link_url_and_attributes($question);
        return new \action_menu_link_secondary($url, new \pix_icon('t/tags', ''),
                $this->managetags, $attributes);
    }
}
