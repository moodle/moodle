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
 * Question bank columns for the preview action icon.
 *
 * @package   core_question
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\bank;
defined('MOODLE_INTERNAL') || die();


/**
 * Question bank columns for the preview action icon.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preview_action_column extends action_column_base implements menuable_action {
    /**
     * @var string store this lang string for performance.
     */
    protected $strpreview;

    public function init() {
        parent::init();
        $this->strpreview = get_string('preview');
    }

    public function get_name() {
        return 'previewaction';
    }

    protected function display_content($question, $rowclasses) {
        global $PAGE;

        if (!\question_bank::is_qtype_installed($question->qtype)) {
            // It sometimes happens that people end up with junk questions
            // in their question bank of a type that is no longer installed.
            // We cannot do most actions on them, because that leads to errors.
            return;
        }

        if (question_has_capability_on($question, 'use')) {
            echo $PAGE->get_renderer('core_question')->question_preview_link(
                    $question->id, $this->qbank->get_most_specific_context(), false);
        }
    }

    public function get_action_menu_link(\stdClass $question): ?\action_menu_link {
        if (!\question_bank::is_qtype_installed($question->qtype)) {
            // It sometimes happens that people end up with junk questions
            // in their question bank of a type that is no longer installed.
            // We cannot do most actions on them, because that leads to errors.
            return null;
        }

        if (!question_has_capability_on($question, 'use')) {
            return null;
        }

        $context = $this->qbank->get_most_specific_context();
        $url = question_preview_url($question->id, null, null, null, null, $context);
        return new \action_menu_link_secondary($url, new \pix_icon('t/preview', ''),
                $this->strpreview, ['target' => 'questionpreview']);
    }
}
