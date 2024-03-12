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
 * Renderers for outputting parts of the question bank.
 *
 * @package    core_question
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * This renderer outputs parts of the question bank.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_bank_renderer extends plugin_renderer_base {

    /**
     * Display additional navigation if needed.
     *
     * @param string $active
     * @return string
     */
    public function extra_horizontal_navigation($active = null) {
        // Horizontal navigation for question bank.
        if ($questionnode = $this->page->settingsnav->find("questionbank", \navigation_node::TYPE_CONTAINER)) {
            if ($children = $questionnode->children) {
                $tabs = [];
                foreach ($children as $key => $node) {
                    $tabs[] = new \tabobject($node->key, $node->action, $node->text);
                }
                if (empty($active) && $questionnode->find_active_node()) {
                    $active = $questionnode->find_active_node()->key;
                }
                return \html_writer::div(print_tabs([$tabs], $active, null, null, true),
                        'questionbank-navigation');
            }
        }
        return '';
    }

    /**
     * Output the icon for a question type.
     *
     * @param string $qtype the question type.
     * @return string HTML fragment.
     */
    public function qtype_icon($qtype) {
        $qtype = question_bank::get_qtype($qtype, false);
        $namestr = $qtype->local_name();

        return $this->image_icon('icon', $namestr, $qtype->plugin_name(), array('title' => $namestr));
    }

    /**
     * Render the column headers.
     *
     * @param array $qbankheaderdata
     * @return bool|string
     */
    public function render_column_header($qbankheaderdata) {
        return $this->render_from_template('core_question/column_header', $qbankheaderdata);
    }

    /**
     * Render the column sort elements.
     *
     * @param array $sortdata
     * @return bool|string
     */
    public function render_column_sort($sortdata) {
        return $this->render_from_template('core_question/column_sort', $sortdata);
    }

    /**
     * @deprecated since Moodle 4.0
     */
    public function render_qbank_chooser() {
        throw new coding_exception(__FUNCTION__ . '() has been removed.');
    }

    /**
     * Render category condition.
     *
     * @param array $displaydata
     * @return bool|string
     * @deprecated since Moodle 4.3
     * @todo Final deprecation on Moodle 4.7 MDL-78090
     */
    public function render_category_condition($displaydata) {
        debugging(
            'Function render_category_condition() has been deprecated and moved to qbank_managecategories plugin, ' .
                'Please use qbank_managecategories\output\renderer::render_category_condition() instead.',
            DEBUG_DEVELOPER
        );
        return $this->render_from_template('qbank_managecategories/category_condition', $displaydata);
    }

    /**
     * Render category condition advanced.
     *
     * @param array $displaydata
     * @return bool|string
     * @deprecated since Moodle 4.3
     * @todo Final deprecation on Moodle 4.7 MDL-78090
     */
    public function render_category_condition_advanced($displaydata) {
        debugging(
            'Function render_category_condition_advanced() has been deprecated',
            DEBUG_DEVELOPER
        );
        // The template category_condition_advanced should also be deleted with this function.
        return $this->render_from_template('qbank_managecategories/category_condition_advanced', $displaydata);
    }

    /**
     * Render hidden condition advanced.
     *
     * @param array $displaydata
     * @return bool|string
     * @see qbank_deletequestion\output\renderer
     * @deprecated since Moodle 4.3
     * @todo Final deprecation on Moodle 4.7 MDL-78090
     */
    public function render_hidden_condition_advanced($displaydata) {
        debugging(
            'Function render_hidden_condition_advanced() has been deprecated and moved to qbank_deletequestion plugin, ' .
                'Please use qbank_deletequestion\output\renderer::render_hidden_condition_advanced() instead.',
            DEBUG_DEVELOPER
        );
        return $this->render_from_template('qbank_deletequestion/hidden_condition_advanced', $displaydata);
    }

    /**
     * Render question pagination.
     *
     * @param array $displaydata
     * @return bool|string
     */
    public function render_question_pagination($displaydata) {
        return $this->render_from_template('core_question/question_pagination', $displaydata);
    }

    /**
     * Render the showtext option.
     *
     * It's not a checkbox any more! [Name your API after the purpose, not the implementation!]
     *
     * @param array $displaydata
     * @return string
     */
    public function render_showtext_checkbox($displaydata) {
        return $this->render_from_template('core_question/showtext_option',
                ['selected' . $displaydata['checked'] => true]);
    }

    /**
     * Render bulk actions ui.
     *
     * @param array $displaydata
     * @return bool|string
     */
    public function render_bulk_actions_ui($displaydata) {
        return $this->render_from_template('core_question/bulk_actions_ui', $displaydata);
    }

    /**
     * @deprecated since Moodle 4.0
     */
    public function qbank_chooser() {
        throw new coding_exception(__FUNCTION__ . '() has been removed.');
    }

    /**
     * @deprecated since Moodle 4.0
     */
    protected function qbank_chooser_types() {
        throw new coding_exception(__FUNCTION__ . '() has been removed.');
    }

    /**
     * @deprecated since Moodle 4.0
     */
    protected function qbank_chooser_qtype() {
        throw new coding_exception(__FUNCTION__ . '() has been removed.');
    }

    /**
     * @deprecated since Moodle 4.0
     */
    protected function qbank_chooser_title() {
        throw new coding_exception(__FUNCTION__ . '() has been removed.');
    }

}
