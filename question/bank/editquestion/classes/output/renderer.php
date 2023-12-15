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
 * Renderer for adding/editing a question.
 *
 * This code is based on question/renderer.php by The Open University.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbank_editquestion\output;

/**
 * Renderer for add/edit/copy
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render a qbank_chooser.
     *
     * @param \renderable $qbankchooser The chooser.
     * @return string
     */
    public function render_qbank_chooser (\renderable $qbankchooser) {
        return $this->render_from_template('qbank_editquestion/qbank_chooser', $qbankchooser->export_for_template($this));
    }

    /**
     * Render add question button.
     *
     * @param array $addquestiondata
     * @return bool|string
     * @deprecated since Moodle 4.3. Use {@see add_new_question} renderable instead
     * @todo Final deprecation in Moodle 4.7
     */
    public function render_create_new_question_button ($addquestiondata) {
        debugging('render_create_new_question_button() is deprecated. '
                . 'Pass the add_new_question renderable to render() instead.');
        return $this->render_from_template('qbank_editquestion/add_new_question', $addquestiondata);
    }

    /**
     * Render question information for edit form.
     *
     * @param array $questiondata
     * @return bool|string
     */
    public function render_question_info($questiondata) {
        return $this->render_from_template('qbank_editquestion/question_info', $questiondata);
    }

    /**
     * Render status dropdown.
     *
     * @param array $dropdownoptions
     * @return bool|string
     */
    public function render_status_dropdown($dropdownoptions) {
        return $this->render_from_template('qbank_editquestion/question_status_dropdown', $dropdownoptions);
    }
}
