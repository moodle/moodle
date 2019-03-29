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
 * @package    moodlecore
 * @subpackage questionbank
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
     * @return string
     */
    public function extra_horizontal_navigation() {
        // Horizontal navigation for question bank.
        if ($questionnode = $this->page->settingsnav->find("questionbank", \navigation_node::TYPE_CONTAINER)) {
            if ($children = $questionnode->children) {
                $tabs = [];
                foreach ($children as $key => $node) {
                    $tabs[] = new \tabobject($node->key, $node->action, $node->text);
                }
                $active = $questionnode->find_active_node()->key;
                return \html_writer::div(print_tabs([$tabs], $active, null, null, true), 'questionbank-navigation');
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
     * Render a qbank_chooser.
     *
     * @param renderable $qbankchooser The chooser.
     * @return string
     */
    public function render_qbank_chooser(renderable $qbankchooser) {
        return $this->render_from_template('core_question/qbank_chooser', $qbankchooser->export_for_template($this));
    }

    /**
     * Build the HTML for the question chooser javascript popup.
     *
     * @param array $real A set of real question types
     * @param array $fake A set of fake question types
     * @param object $course The course that will be displayed
     * @param array $hiddenparams Any hidden parameters to add to the form
     * @return string The composed HTML for the questionbank chooser
     */
    public function qbank_chooser($real, $fake, $course, $hiddenparams) {
        debugging('Method core_question_bank_renderer::qbank_chooser() is deprecated, ' .
            'see core_question_bank_renderer::render_qbank_chooser().', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Build the HTML for a specified set of question types.
     *
     * @param array $types A set of question types as used by the qbank_chooser_module function
     * @return string The composed HTML for the module
     */
    protected function qbank_chooser_types($types) {
        debugging('Method core_question_bank_renderer::qbank_chooser_types() is deprecated, ' .
            'see core_question_bank_renderer::render_qbank_chooser().', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Return the HTML for the specified question type, adding any required classes.
     *
     * @param object $qtype An object containing the title, and link. An icon, and help text may optionally be specified.
     * If the module contains subtypes in the types option, then these will also be displayed.
     * @param array $classes Additional classes to add to the encompassing div element
     * @return string The composed HTML for the question type
     */
    protected function qbank_chooser_qtype($qtype, $classes = array()) {
        debugging('Method core_question_bank_renderer::qbank_chooser_qtype() is deprecated, ' .
            'see core_question_bank_renderer::render_qbank_chooser().', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Return the title for the question bank chooser.
     *
     * @param string $title The language string identifier
     * @param string $identifier The component identifier
     * @return string The composed HTML for the title
     */
    protected function qbank_chooser_title($title, $identifier = null) {
        debugging('Method core_question_bank_renderer::qbank_chooser_title() is deprecated, ' .
            'see core_question_bank_renderer::render_qbank_chooser().', DEBUG_DEVELOPER);
        return '';
    }
}
