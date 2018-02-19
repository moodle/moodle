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
 * Question related functions.
 *
 * This file was created just because Fragment API expects callbacks to be defined on lib.php.
 *
 * Please, do not add new functions to this file.
 *
 * @package   core_question
 * @copyright 2018 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Question tags fragment callback.
 *
 * @param array $args Arguments to the form.
 * @return null|string The rendered form.
 */
function core_question_output_fragment_tags_form($args) {

    if (!empty($args['id'])) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/question/type/tags_form.php');
        require_once($CFG->libdir . '/questionlib.php');
        $id = clean_param($args['id'], PARAM_INT);

        $question = $DB->get_record('question', ['id' => $id]);
        $category = $DB->get_record('question_categories', array('id' => $question->category));
        $context = \context::instance_by_id($category->contextid);

        $toform = new stdClass();
        $toform->id = $question->id;
        $toform->questioncategory = $category->name;
        $toform->questionname = $question->name;
        $toform->categoryid = $category->id;
        $toform->contextid = $category->contextid;
        $toform->context = $context->get_context_name();

        if (core_tag_tag::is_enabled('core_question', 'question')) {
            $toform->tags = core_tag_tag::get_item_tags_array('core_question', 'question', $question->id);
        }

        $cantag = question_has_capability_on($question, 'tag');
        $mform = new \core_question\form\tags(null, null, 'post', '', null, $cantag, $toform);
        $mform->set_data($toform);

        return $mform->render();
    }
}
