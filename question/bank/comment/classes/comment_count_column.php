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

namespace qbank_comment;

use core_question\local\bank\column_base;
use question_bank;

/**
 * A column to show the number of comments.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comment_count_column extends column_base {

    /**
     * @var bool Comments enabled or not from config.
     */
    protected $commentsenabled = true;

    /**
     * Load javascript module if enabled.
     *
     * @return void
     */
    public function init(): void {
        parent::init();
        $this->check_comments_status();
        if ($this->commentsenabled) {
            global $PAGE;
            $PAGE->requires->js_call_amd('qbank_comment/comment', 'init');
        }
    }

    /**
     * Check if comments is turned on in the system or not.
     */
    protected function check_comments_status(): void {
        global $CFG;
        if (!$CFG->usecomments) {
            $this->commentsenabled = false;
        }
    }

    /**
     * Get the name of the column, used internally.
     *
     * @return string
     */
    public function get_name(): string {
        return 'commentcount';
    }

    /**
     * Get the title of the column that will be displayed.
     *
     * @return string
     */
    public function get_title(): string {
        return get_string('commentplural', 'qbank_comment');
    }

    /**
     * Generate the content to be displayed.
     *
     * @param object $question The question object.
     * @param string $rowclasses Classes that can be added.
     */
    protected function display_content($question, $rowclasses): void {
        global $DB;

        $syscontext = \context_system::instance();

        $args = new \stdClass;
        $args->contextid = $syscontext->id;
        $args->courseid  = $this->qbank->course->id;
        $args->area      = 'question';
        $args->itemid    = $question->id;
        $args->component = 'qbank_comment';

        $params = [
            'component' => $args->component,
            'commentarea' => $args->area,
            'itemid' => $args->itemid,
            'contextid' => $args->contextid,
        ];
        $commentcount = $DB->count_records('comments', $params);
        $attributes = [];

        // Build up the comment object to see if we have correct permissions to post.
        $comment = new \comment($args);
        if (question_has_capability_on($question, 'comment') && $comment->can_post()) {
            $tag = 'a';
            $target = 'questioncommentpreview_' . $question->id;
            $attributes = [
                'href' => '#',
                'data-target' => $target,
                'data-questionid' => $question->id,
                'data-courseid' => $this->qbank->course->id,
                'data-contextid' => $syscontext->id,
            ];
        } else {
            $tag = 'span';
        }
        echo \html_writer::tag($tag, $commentcount, $attributes);
    }

    public function get_extra_classes(): array {
        return ['pe-3'];
    }

    public function get_default_width(): int {
        return 150;
    }
}
