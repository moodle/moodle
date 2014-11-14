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
 * Renderer outputting the quiz editing UI.
 *
 * @package mod_quiz
 * @copyright 2013 The Open University.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quiz\output;
defined('MOODLE_INTERNAL') || die();

use \mod_quiz\structure;
use \html_writer;

/**
 * Renderer outputting the quiz editing UI.
 *
 * @copyright 2013 The Open University.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.7
 */
class edit_renderer extends \plugin_renderer_base {

    /**
     * Render the edit page
     *
     * @param \quiz $quizobj object containing all the quiz settings information.
     * @param structure $structure object containing the structure of the quiz.
     * @param \question_edit_contexts $contexts the relevant question bank contexts.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @param array $pagevars the variables from {@link question_edit_setup()}.
     * @return string HTML to output.
     */
    public function edit_page(\quiz $quizobj, structure $structure,
            \question_edit_contexts $contexts, \moodle_url $pageurl, array $pagevars) {
        $output = '';

        // Page title.
        $output .= $this->heading_with_help(get_string('editingquizx', 'quiz',
                format_string($quizobj->get_quiz_name())), 'editingquiz', 'quiz', '',
                get_string('basicideasofquiz', 'quiz'), 2);

        // Information at the top.
        $output .= $this->quiz_state_warnings($structure);
        $output .= $this->quiz_information($structure);
        $output .= $this->maximum_grade_input($quizobj->get_quiz(), $this->page->url);
        $output .= $this->repaginate_button($structure, $pageurl);
        $output .= $this->total_marks($quizobj->get_quiz());

        // Show the questions organised into sections and pages.
        $output .= $this->start_section_list();

        $sections = $structure->get_quiz_sections();
        $lastsection = end($sections);
        foreach ($sections as $section) {
            $output .= $this->start_section($section);
            $output .= $this->questions_in_section($structure, $section, $contexts, $pagevars, $pageurl);
            if ($section === $lastsection) {
                $output .= \html_writer::start_div('last-add-menu');
                $output .= html_writer::tag('span', $this->add_menu_actions($structure, 0,
                        $pageurl, $contexts, $pagevars), array('class' => 'add-menu-outer'));
                $output .= \html_writer::end_div();
            }
            $output .= $this->end_section();
        }

        $output .= $this->end_section_list();

        // Inialise the JavaScript.
        $this->initialise_editing_javascript($quizobj->get_course(), $quizobj->get_quiz(),
                $structure, $contexts, $pagevars, $pageurl);

        // Include the contents of any other popups required.
        if ($structure->can_be_edited()) {
            $popups = '';

            $popups .= $this->question_bank_loading();
            $this->page->requires->yui_module('moodle-mod_quiz-quizquestionbank',
                    'M.mod_quiz.quizquestionbank.init',
                    array('class' => 'questionbank', 'cmid' => $structure->get_cmid()));

            $popups .= $this->random_question_form($pageurl, $contexts, $pagevars);
            $this->page->requires->yui_module('moodle-mod_quiz-randomquestion',
                    'M.mod_quiz.randomquestion.init');

            $output .= html_writer::div($popups, 'mod_quiz_edit_forms');

            // Include the question chooser.
            $output .= $this->question_chooser();
            $this->page->requires->yui_module('moodle-mod_quiz-questionchooser', 'M.mod_quiz.init_questionchooser');
        }

        return $output;
    }

    /**
     * Render any warnings that might be required about the state of the quiz,
     * e.g. if it has been attempted, or if the shuffle questions option is
     * turned on.
     *
     * @param structure $structure the quiz structure.
     * @return string HTML to output.
     */
    public function quiz_state_warnings(structure $structure) {
        $warnings = $structure->get_edit_page_warnings();

        if (empty($warnings)) {
            return '';
        }

        $output = array();
        foreach ($warnings as $warning) {
            $output[] = \html_writer::tag('p', $warning);
        }
        return $this->box(implode("\n", $output), 'statusdisplay');
    }

    /**
     * Render the status bar.
     *
     * @param structure $structure the quiz structure.
     * @return string HTML to output.
     */
    public function quiz_information(structure $structure) {
        list($currentstatus, $explanation) = $structure->get_dates_summary();

        $output = html_writer::span(
                    get_string('numquestionsx', 'quiz', $structure->get_question_count()),
                    'numberofquestions') . ' | ' .
                html_writer::span($currentstatus, 'quizopeningstatus',
                    array('title' => $explanation));

        return html_writer::div($output, 'statusbar');
    }

    /**
     * Render the form for setting a quiz' overall grade
     *
     * @param \stdClass $quiz the quiz settings from the database.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML to output.
     */
    public function maximum_grade_input($quiz, \moodle_url $pageurl) {
        $output = '';
        $output .= html_writer::start_div('maxgrade');
        $output .= html_writer::start_tag('form', array('method' => 'post', 'action' => 'edit.php',
                'class' => 'quizsavegradesform'));
        $output .= html_writer::start_tag('fieldset', array('class' => 'invisiblefieldset'));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
        $output .= html_writer::input_hidden_params($pageurl);
        $a = html_writer::empty_tag('input', array('type' => 'text', 'id' => 'inputmaxgrade',
                'name' => 'maxgrade', 'size' => ($quiz->decimalpoints + 2),
                'value' => quiz_format_grade($quiz, $quiz->grade)));
        $output .= html_writer::tag('label', get_string('maximumgradex', '', $a),
                array('for' => 'inputmaxgrade'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit',
                'name' => 'savechanges', 'value' => get_string('save', 'quiz')));
        $output .= html_writer::end_tag('fieldset');
        $output .= html_writer::end_tag('form');
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Return the repaginate button
     * @param structure $structure the structure of the quiz being edited.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML to output.
     */
    protected function repaginate_button(structure $structure, \moodle_url $pageurl) {

        $header = html_writer::tag('span', get_string('repaginatecommand', 'quiz'), array('class' => 'repaginatecommand'));
        $form = $this->repaginate_form($structure, $pageurl);
        $containeroptions = array(
                'class'  => 'rpcontainerclass',
                'cmid'   => $structure->get_cmid(),
                'header' => $header,
                'form'   => $form,
        );

        $buttonoptions = array(
            'type'  => 'submit',
            'name'  => 'repaginate',
            'id'    => 'repaginatecommand',
            'value' => get_string('repaginatecommand', 'quiz'),
        );
        if (!$structure->can_be_repaginated()) {
            $buttonoptions['disabled'] = 'disabled';
        } else {
            $this->page->requires->yui_module('moodle-mod_quiz-repaginate', 'M.mod_quiz.repaginate.init');
        }

        return html_writer::tag('div',
                html_writer::empty_tag('input', $buttonoptions), $containeroptions);
    }

    /**
     * Return the repaginate form
     * @param structure $structure the structure of the quiz being edited.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML to output.
     */
    protected function repaginate_form(structure $structure, \moodle_url $pageurl) {
        $perpage = array();
        $perpage[0] = get_string('allinone', 'quiz');
        for ($i = 1; $i <= 50; ++$i) {
            $perpage[$i] = $i;
        }

        $hiddenurl = clone($pageurl);
        $hiddenurl->param('sesskey', sesskey());

        $select = html_writer::select($perpage, 'questionsperpage',
                $structure->get_questions_per_page(), false);

        $buttonattributes = array('type' => 'submit', 'name' => 'repaginate', 'value' => get_string('go'));

        $formcontent = html_writer::tag('form', html_writer::div(
                    html_writer::input_hidden_params($hiddenurl) .
                    get_string('repaginate', 'quiz', $select) .
                    html_writer::empty_tag('input', $buttonattributes)
                ), array('action' => 'edit.php', 'method' => 'post'));

        return html_writer::div($formcontent, '', array('id' => 'repaginatedialog'));
    }

    /**
     * Render the total marks available for the quiz.
     *
     * @param \stdClass $quiz the quiz settings from the database.
     * @return string HTML to output.
     */
    public function total_marks($quiz) {
        $totalmark = html_writer::span(quiz_format_grade($quiz, $quiz->sumgrades), 'mod_quiz_summarks');
        return html_writer::tag('span',
                get_string('totalmarksx', 'quiz', $totalmark),
                array('class' => 'totalpoints'));
    }

    /**
     * Generate the starting container html for the start of a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'slots'));
    }

    /**
     * Generate the closing container html for the end of a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Display the start of a section, before the questions.
     *
     * @param \stdClass $section The quiz_section entry from DB
     * @return string HTML to output.
     */
    protected function start_section($section) {

        $output = '';
        $sectionstyle = '';

        $output .= html_writer::start_tag('li', array('id' => 'section-'.$section->id,
            'class' => 'section main clearfix'.$sectionstyle, 'role' => 'region',
            'aria-label' => $section->heading));

        $leftcontent = $this->section_left_content($section);
        $output .= html_writer::div($leftcontent, 'left side');

        $rightcontent = $this->section_right_content($section);
        $output .= html_writer::div($rightcontent, 'right side');
        $output .= html_writer::start_div('content');

        return $output;
    }

    /**
     * Display the end of a section, after the questions.
     *
     * @return string HTML to output.
     */
    protected function end_section() {
        $output = html_writer::end_tag('div');
        $output .= html_writer::end_tag('li');

        return $output;
    }

    /**
     * Generate the content to be displayed on the left part of a section.
     *
     * @param \stdClass $section The quiz_section entry from DB
     * @return string HTML to output.
     */
    protected function section_left_content($section) {
        return $this->output->spacer();
    }

    /**
     * Generate the content to displayed on the right part of a section.
     *
     * @param \stdClass $section The quiz_section entry from DB
     * @return string HTML to output.
     */
    protected function section_right_content($section) {
        return $this->output->spacer();
    }

    /**
     * Renders HTML to display the questions in a section of the quiz.
     *
     * This function calls {@link core_course_renderer::quiz_section_question()}
     *
     * @param structure $structure object containing the structure of the quiz.
     * @param \stdClass $section information about the section.
     * @param \question_edit_contexts $contexts the relevant question bank contexts.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML to output.
     */
    public function questions_in_section(structure $structure, $section,
            $contexts, $pagevars, $pageurl) {

        $output = '';
        foreach ($structure->get_questions_in_section($section->id) as $question) {
            $output .= $this->question_row($structure, $question, $contexts, $pagevars, $pageurl);
        }

        return html_writer::tag('ul', $output, array('class' => 'section img-text'));
    }

    /**
     * Displays one question with the surrounding controls.
     *
     * @param structure $structure object containing the structure of the quiz.
     * @param \stdClass $question data from the question and quiz_slots tables.
     * @param \question_edit_contexts $contexts the relevant question bank contexts.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML to output.
     */
    public function question_row(structure $structure, $question, $contexts, $pagevars, $pageurl) {
        $output = '';

        $output .= $this->page_row($structure, $question, $contexts, $pagevars, $pageurl);

        // Page split/join icon.
        $joinhtml = '';
        if ($structure->can_be_edited() && !$structure->is_last_slot_in_quiz($question->slot)) {
            $joinhtml = $this->page_split_join_button($structure->get_quiz(),
                    $question, !$structure->is_last_slot_on_page($question->slot));
        }

        // Question HTML.
        $questionhtml = $this->question($structure, $question, $pageurl);
        $questionclasses = 'activity ' . $question->qtype . ' qtype_' . $question->qtype . ' slot';

        $output .= html_writer::tag('li', $questionhtml . $joinhtml,
                array('class' => $questionclasses, 'id' => 'slot-' . $question->slotid));

        return $output;
    }

    /**
     * Displays one question with the surrounding controls.
     *
     * @param structure $structure object containing the structure of the quiz.
     * @param \stdClass $question data from the question and quiz_slots tables.
     * @param \question_edit_contexts $contexts the relevant question bank contexts.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML to output.
     */
    public function page_row(structure $structure, $question, $contexts, $pagevars, $pageurl) {
        $output = '';

        // Put page in a span for easier styling.
        $page = html_writer::tag('span', get_string('page') . ' ' . $question->page,
                array('class' => 'text'));

        if ($structure->is_first_slot_on_page($question->slot)) {
            // Add the add-menu at the page level.
            $addmenu = html_writer::tag('span', $this->add_menu_actions($structure,
                    $question->page, $pageurl, $contexts, $pagevars),
                    array('class' => 'add-menu-outer'));

            $addquestionform = $this->add_question_form($structure,
                    $question->page, $pageurl, $pagevars);

            $output .= html_writer::tag('li', $page . $addmenu . $addquestionform,
                    array('class' => 'pagenumber activity yui3-dd-drop page', 'id' => 'page-' . $question->page));
        }

        return $output;
    }

    /**
     * Returns the add menu that is output once per page.
     * @param structure $structure object containing the structure of the quiz.
     * @param int $page the page number that this menu will add to.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @param \question_edit_contexts $contexts the relevant question bank contexts.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @return string HTML to output.
     */
    public function add_menu_actions(structure $structure, $page, \moodle_url $pageurl,
            \question_edit_contexts $contexts, array $pagevars) {

        $actions = $this->edit_menu_actions($structure, $page, $pageurl, $pagevars);
        if (empty($actions)) {
            return '';
        }
        $menu = new \action_menu();
        $menu->set_alignment(\action_menu::TR, \action_menu::BR);
        $menu->set_constraint('.mod-quiz-edit-content');
        $trigger = html_writer::tag('span', get_string('add', 'quiz'), array('class' => 'add-menu'));
        $menu->set_menu_trigger($trigger);
        // The menu appears within an absolutely positioned element causing width problems.
        // Make sure no-wrap is set so that we don't get a squashed menu.
        $menu->set_nowrap_on_items(true);

        // Disable the link if quiz has attempts.
        if (!$structure->can_be_edited()) {
            return $this->render($menu);
        }

        foreach ($actions as $action) {
            if ($action instanceof \action_menu_link) {
                $action->add_class('add-menu');
            }
            $menu->add($action);
        }
        $menu->attributes['class'] .= ' page-add-actions commands';

        // Prioritise the menu ahead of all other actions.
        $menu->prioritise = true;

        return $this->render($menu);
    }

    /**
     * Returns the list of actions to go in the add menu.
     * @param structure $structure object containing the structure of the quiz.
     * @param int $page the page number that this menu will add to.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @return array the actions.
     */
    public function edit_menu_actions(structure $structure, $page,
            \moodle_url $pageurl, array $pagevars) {
        $questioncategoryid = question_get_category_id_from_pagevars($pagevars);
        static $str;
        if (!isset($str)) {
            $str = get_strings(array('addaquestion', 'addarandomquestion',
                    'addarandomselectedquestion', 'questionbank'), 'quiz');
        }

        // Get section, page, slotnumber and maxmark.
        $actions = array();

        // Add a new question to the quiz.
        $returnurl = new \moodle_url($pageurl, array('addonpage' => $page));
        $params = array('returnurl' => $returnurl->out_as_local_url(false),
                'cmid' => $structure->get_cmid(), 'category' => $questioncategoryid,
                'addonpage' => $page, 'appendqnumstring' => 'addquestion');

        $actions['addaquestion'] = new \action_menu_link_secondary(
            new \moodle_url('/question/addquestion.php', $params),
            new \pix_icon('t/add', $str->addaquestion, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            $str->addaquestion, array('class' => 'cm-edit-action addquestion', 'data-action' => 'addquestion')
        );

        // Call question bank.
        $icon = new \pix_icon('t/add', $str->questionbank, 'moodle', array('class' => 'iconsmall', 'title' => ''));
        $title = get_string('addquestionfrombanktopage', 'quiz', $page);
        $attributes = array('class' => 'cm-edit-action questionbank',
                'data-header' => $title, 'data-action' => 'questionbank', 'data-addonpage' => $page);
        $actions['questionbank'] = new \action_menu_link_secondary($pageurl, $icon, $str->questionbank, $attributes);

        // Add a random question.
        $returnurl = new \moodle_url('/mod/quiz/edit.php', array('cmid' => $structure->get_cmid(), 'data-addonpage' => $page));
        $params = array('returnurl' => $returnurl, 'cmid' => $structure->get_cmid(), 'appendqnumstring' => 'addarandomquestion');
        $url = new \moodle_url('/mod/quiz/addrandom.php', $params);
        $icon = new \pix_icon('t/add', $str->addarandomquestion, 'moodle', array('class' => 'iconsmall', 'title' => ''));
        $attributes = array('class' => 'cm-edit-action addarandomquestion', 'data-action' => 'addarandomquestion');
        $title = get_string('addrandomquestiontopage', 'quiz', $page);
        $attributes = array_merge(array('data-header' => $title, 'data-addonpage' => $page), $attributes);
        $actions['addarandomquestion'] = new \action_menu_link_secondary($url, $icon, $str->addarandomquestion, $attributes);

        return $actions;
    }

    /**
     * Render the form that contains the data for adding a new question to the quiz.
     *
     * @param structure $structure object containing the structure of the quiz.
     * @param int $page the page number that this menu will add to.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @return string HTML to output.
     */
    protected function add_question_form(structure $structure, $page, \moodle_url $pageurl, array $pagevars) {

        $questioncategoryid = question_get_category_id_from_pagevars($pagevars);

        $output = html_writer::tag('input', null,
                array('type' => 'hidden', 'name' => 'returnurl',
                        'value' => $pageurl->out_as_local_url(false, array('addonpage' => $page))));
        $output .= html_writer::tag('input', null,
                array('type' => 'hidden', 'name' => 'cmid', 'value' => $structure->get_cmid()));
        $output .= html_writer::tag('input', null,
                array('type' => 'hidden', 'name' => 'appendqnumstring', 'value' => 'addquestion'));
        $output .= html_writer::tag('input', null,
                array('type' => 'hidden', 'name' => 'category', 'value' => $questioncategoryid));

        return html_writer::tag('form', html_writer::div($output),
                array('class' => 'addnewquestion', 'method' => 'post',
                        'action' => new \moodle_url('/question/addquestion.php')));
    }

    /**
     * Display a question.
     *
     * @param structure $structure object containing the structure of the quiz.
     * @param \stdClass $question data from the question and quiz_slots tables.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML to output.
     */
    public function question(structure $structure, $question, \moodle_url $pageurl) {
        $output = '';

        $output .= html_writer::start_tag('div');

        if ($structure->can_be_edited()) {
            $output .= $this->question_move_icon($question);
        }

        $output .= html_writer::start_div('mod-indent-outer');
        $output .= $this->question_number($question->displayednumber);

        // This div is used to indent the content.
        $output .= html_writer::div('', 'mod-indent');

        // Display the link to the question (or do nothing if question has no url).
        if ($question->qtype == 'random') {
            $questionname = $this->random_question($structure, $question, $pageurl);
        } else {
            $questionname = $this->question_name($structure, $question, $pageurl);
        }

        // Start the div for the activity title, excluding the edit icons.
        $output .= html_writer::start_div('activityinstance');
        $output .= $questionname;

        // Closing the tag which contains everything but edit icons. Content part of the module should not be part of this.
        $output .= html_writer::end_tag('div'); // .activityinstance.

        // Action icons.
        $questionicons = '';
        $questionicons .= $this->question_preview_icon($structure->get_quiz(), $question);
        if ($structure->can_be_edited()) {
            $questionicons .= $this->question_remove_icon($question, $pageurl);
        }
        $questionicons .= $this->marked_out_of_field($structure->get_quiz(), $question);
        $output .= html_writer::span($questionicons, 'actions'); // Required to add js spinner icon.

        // End of indentation div.
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Render the move icon.
     *
     * @param \stdClass $question data from the question and quiz_slots tables.
     * @return string The markup for the move action, or an empty string if not available.
     */
    public function question_move_icon($question) {
        return html_writer::link(new \moodle_url('#'),
            $this->pix_icon('i/dragdrop', get_string('move'), 'moodle', array('class' => 'iconsmall', 'title' => '')),
            array('class' => 'editing_move', 'data-action' => 'move')
        );
    }

    /**
     * Output the question number.
     * @param string $number The number, or 'i'.
     * @return string HTML to output.
     */
    public function question_number($number) {
        if (is_numeric($number)) {
            $number = html_writer::span(get_string('question'), 'accesshide') .
                    ' ' . $number;
        }
        return html_writer::tag('span', $number, array('class' => 'slotnumber'));
    }

    /**
     * Render the preview icon.
     *
     * @param \stdClass $quiz the quiz settings from the database.
     * @param \stdClass $question data from the question and quiz_slots tables.
     * @param bool $label if true, show the preview question label after the icon
     * @return string HTML to output.
     */
    public function question_preview_icon($quiz, $question, $label = null) {
        $url = quiz_question_preview_url($quiz, $question);

        // Do we want a label?
        $strpreviewlabel = '';
        if ($label) {
            $strpreviewlabel = ' ' . get_string('preview', 'quiz');
        }

        // Build the icon.
        $strpreviewquestion = get_string('previewquestion', 'quiz');
        $image = $this->pix_icon('t/preview', $strpreviewquestion);

        $action = new \popup_action('click', $url, 'questionpreview',
                                        question_preview_popup_params());

        return $this->action_link($url, $image . $strpreviewlabel, $action,
                array('title' => $strpreviewquestion, 'class' => 'preview'));
    }

    /**
     * Render an icon to remove a question from the quiz.
     *
     * @param object $question The module to produce a move button for.
     * @param \moodle_url $pageurl the canonical URL of the edit page.
     * @return string HTML to output.
     */
    public function question_remove_icon($question, $pageurl) {
        $url = new \moodle_url($pageurl, array('sesskey' => sesskey(), 'remove' => $question->slot));
        $strdelete = get_string('delete');

        $image = $this->pix_icon('t/delete', $strdelete);

        return $this->action_link($url, $image, null, array('title' => $strdelete,
                    'class' => 'cm-edit-action editing_delete', 'data-action' => 'delete'));
    }

    /**
     * Display an icon to split or join two pages of the quiz.
     *
     * @param \stdClass $quiz the quiz settings from the database.
     * @param \stdClass $question data from the question and quiz_slots tables.
     * @param bool $insertpagebreak if true, show an insert page break icon.
     *      else show a join pages icon.
     * @return string HTML to output.
     */
    public function page_split_join_button($quiz, $question, $insertpagebreak) {
        $url = new \moodle_url('repaginate.php', array('cmid' => $quiz->cmid, 'quizid' => $quiz->id,
                    'slot' => $question->slot, 'repag' => $insertpagebreak ? 2 : 1, 'sesskey' => sesskey()));

        if ($insertpagebreak) {
            $title = get_string('addpagebreak', 'quiz');
            $image = $this->pix_icon('e/insert_page_break', $title);
            $action = 'addpagebreak';
        } else {
            $title = get_string('removepagebreak', 'quiz');
            $image = $this->pix_icon('e/remove_page_break', $title);
            $action = 'removepagebreak';
        }

        // Disable the link if quiz has attempts.
        $disabled = null;
        if (quiz_has_attempts($quiz->id)) {
            $disabled = "disabled";
        }
        return html_writer::span($this->action_link($url, $image, null, array('title' => $title,
                    'class' => 'page_split_join cm-edit-action', 'disabled' => $disabled, 'data-action' => $action)),
                'page_split_join_wrapper');
    }

    /**
     * Renders html to display a name with the link to the question on a quiz edit page
     *
     * If the user does not have permission to edi the question, it is rendered
     * without a link
     *
     * @param structure $structure object containing the structure of the quiz.
     * @param \stdClass $question data from the question and quiz_slots tables.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML to output.
     */
    public function question_name(structure $structure, $question, $pageurl) {
        $output = '';

        $editurl = new \moodle_url('/question/question.php', array(
                'returnurl' => $pageurl->out_as_local_url(),
                'cmid' => $structure->get_cmid(), 'id' => $question->id));

        $instancename = quiz_question_tostring($question);

        $qtype = \question_bank::get_qtype($question->qtype, false);
        $namestr = $qtype->local_name();

        $icon = $this->pix_icon('icon', $namestr, $qtype->plugin_name(), array('title' => $namestr,
                'class' => 'icon activityicon', 'alt' => ' ', 'role' => 'presentation'));

        $editicon = $this->pix_icon('t/edit', '', 'moodle', array('title' => ''));

        // Need plain question name without html tags for link title.
        $title = shorten_text(format_string($question->name), 100);

        // Display the link itself.
        $activitylink = $icon . html_writer::tag('span', $editicon . $instancename, array('class' => 'instancename'));
        $output .= html_writer::link($editurl, $activitylink,
                array('title' => get_string('editquestion', 'quiz').' '.$title));

        return $output;
    }

    /**
     * Renders html to display a random question the link to edit the configuration
     * and also to see that category in the question bank.
     *
     * @param structure $structure object containing the structure of the quiz.
     * @param \stdClass $question data from the question and quiz_slots tables.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML to output.
     */
    public function random_question(structure $structure, $question, $pageurl) {

        $editurl = new \moodle_url('/question/question.php', array(
                'returnurl' => $pageurl->out_as_local_url(),
                'cmid' => $structure->get_cmid(), 'id' => $question->id));

        $temp = clone($question);
        $temp->questiontext = '';
        $instancename = quiz_question_tostring($temp);

        $configuretitle = get_string('configurerandomquestion', 'quiz');
        $qtype = \question_bank::get_qtype($question->qtype, false);
        $namestr = $qtype->local_name();
        $icon = $this->pix_icon('icon', $namestr, $qtype->plugin_name(), array('title' => $namestr,
                'class' => 'icon activityicon', 'alt' => ' ', 'role' => 'presentation'));

        $editicon = $this->pix_icon('t/edit', $configuretitle, 'moodle', array('title' => ''));

        // If this is a random question, display a link to show the questions
        // selected from in the question bank.
        $qbankurl = new \moodle_url('/question/edit.php', array(
                'cmid' => $structure->get_cmid(),
                'cat' => $question->category . ',' . $question->contextid,
                'recurse' => !empty($question->questiontext)));
        $qbanklink = ' ' . \html_writer::link($qbankurl,
                get_string('seequestions', 'quiz'), array('class' => 'mod_quiz_random_qbank_link'));

        return html_writer::link($editurl, $icon . $editicon, array('title' => $configuretitle)) .
                ' ' . $instancename . ' ' . $qbanklink;
    }

    /**
     * Display the 'marked out of' information for a question.
     * Along with the regrade action.
     * @param \stdClass $quiz the quiz settings from the database.
     * @param \stdClass $question data from the question and quiz_slots tables.
     * @return string HTML to output.
     */
    public function marked_out_of_field($quiz, $question) {
        if ($question->length == 0) {
            $output = html_writer::span('',
                    'instancemaxmark decimalplaces_' . quiz_get_grade_format($quiz));

            $output .= html_writer::span(
                    $this->pix_icon('spacer', '', 'moodle', array('class' => 'editicon visibleifjs', 'title' => '')),
                    'editing_maxmark');
            return html_writer::span($output, 'instancemaxmarkcontainer infoitem');
        }

        $output = html_writer::span(quiz_format_question_grade($quiz, $question->maxmark),
                'instancemaxmark decimalplaces_' . quiz_get_grade_format($quiz),
                array('title' => get_string('maxmark', 'quiz')));

        $output .= html_writer::span(
            html_writer::link(
                new \moodle_url('#'),
                $this->pix_icon('t/editstring', '', 'moodle', array('class' => 'editicon visibleifjs', 'title' => '')),
                array(
                    'class' => 'editing_maxmark',
                    'data-action' => 'editmaxmark',
                    'title' => get_string('editmaxmark', 'quiz'),
                )
            )
        );
        return html_writer::span($output, 'instancemaxmarkcontainer');
    }

    /**
     * Render the question type chooser dialogue.
     * @return string HTML to output.
     */
    public function question_chooser() {
        $container = html_writer::div(print_choose_qtype_to_add_form(array(), null, false), '',
                array('id' => 'qtypechoicecontainer'));
        return html_writer::div($container, 'createnewquestion');
    }

    /**
     * Render the contents of the question bank pop-up in its initial state,
     * when it just contains a loading progress indicator.
     * @return string HTML to output.
     */
    public function question_bank_loading() {
        return html_writer::div(html_writer::empty_tag('img',
                array('alt' => 'loading', 'class' => 'loading-icon', 'src' => $this->pix_url('i/loading'))),
                'questionbankloading');
    }

    /**
     * Return random question form.
     * @param \moodle_url $thispageurl the canonical URL of this page.
     * @param \question_edit_contexts $contexts the relevant question bank contexts.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @return string HTML to output.
     */
    protected function random_question_form(\moodle_url $thispageurl, \question_edit_contexts $contexts, array $pagevars) {

        if (!$contexts->have_cap('moodle/question:useall')) {
            return '';
        }
        $randomform = new \quiz_add_random_form(new \moodle_url('/mod/quiz/addrandom.php'),
                                 array('contexts' => $contexts, 'cat' => $pagevars['cat']));
        $randomform->set_data(array(
                'category' => $pagevars['cat'],
                'returnurl' => $thispageurl->out_as_local_url(true),
                'randomnumber' => 1,
                'cmid' => $thispageurl->param('cmid'),
        ));
        return html_writer::div($randomform->render(), 'randomquestionformforpopup');
    }

    /**
     * Initialise the JavaScript for the general editing. (JavaScript for popups
     * is handled with the specific code for those.)
     *
     * @param \stdClass $course the course settings from the database.
     * @param \stdClass $quiz the quiz settings from the database.
     * @param structure $structure object containing the structure of the quiz.
     * @param \question_edit_contexts $contexts the relevant question bank contexts.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return bool Always returns true
     */
    protected function initialise_editing_javascript($course, $quiz, structure $structure,
            \question_edit_contexts $contexts, array $pagevars, \moodle_url $pageurl) {

        $config = new \stdClass();
        $config->resourceurl = '/mod/quiz/edit_rest.php';
        $config->sectionurl = '/mod/quiz/edit_rest.php';
        $config->pageparams = array();
        $config->questiondecimalpoints = $quiz->questiondecimalpoints;
        $config->pagehtml = $this->new_page_template($structure, $contexts, $pagevars, $pageurl);
        $config->addpageiconhtml = $this->add_page_icon_template($structure, $quiz);

        $this->page->requires->yui_module('moodle-mod_quiz-toolboxes',
                'M.mod_quiz.init_resource_toolbox',
                array(array(
                        'courseid' => $course->id,
                        'quizid' => $quiz->id,
                        'ajaxurl' => $config->resourceurl,
                        'config' => $config,
                ))
        );
        unset($config->pagehtml);
        unset($config->addpageiconhtml);

        $this->page->requires->yui_module('moodle-mod_quiz-toolboxes',
                'M.mod_quiz.init_section_toolbox',
                array(array(
                        'courseid' => $course->id,
                        'quizid' => $quiz->id,
                        'format' => $course->format,
                        'ajaxurl' => $config->sectionurl,
                        'config' => $config,
                ))
        );

        $this->page->requires->yui_module('moodle-mod_quiz-dragdrop', 'M.mod_quiz.init_section_dragdrop',
                array(array(
                        'courseid' => $course->id,
                        'quizid' => $quiz->id,
                        'ajaxurl' => $config->sectionurl,
                        'config' => $config,
                )), null, true);

        $this->page->requires->yui_module('moodle-mod_quiz-dragdrop', 'M.mod_quiz.init_resource_dragdrop',
                array(array(
                        'courseid' => $course->id,
                        'quizid' => $quiz->id,
                        'ajaxurl' => $config->resourceurl,
                        'config' => $config,
                )), null, true);

        // Require various strings for the command toolbox.
        $this->page->requires->strings_for_js(array(
                'clicktohideshow',
                'deletechecktype',
                'deletechecktypename',
                'edittitle',
                'edittitleinstructions',
                'emptydragdropregion',
                'hide',
                'markedthistopic',
                'markthistopic',
                'move',
                'movecontent',
                'moveleft',
                'movesection',
                'page',
                'question',
                'selectall',
                'show',
                'tocontent',
        ), 'moodle');

        $this->page->requires->strings_for_js(array(
                'addpagebreak',
                'confirmremovequestion',
                'dragtoafter',
                'dragtostart',
                'numquestionsx',
                'removepagebreak',
        ), 'quiz');

        foreach (\question_bank::get_all_qtypes() as $qtype => $notused) {
            $this->page->requires->string_for_js('pluginname', 'qtype_' . $qtype);
        }

        return true;
    }

    /**
     * HTML for a page, with ids stripped, so it can be used as a javascript template.
     *
     * @param structure $structure object containing the structure of the quiz.
     * @param \question_edit_contexts $contexts the relevant question bank contexts.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @param \moodle_url $pageurl the canonical URL of this page.
     * @return string HTML for a new page.
     */
    protected function new_page_template(structure $structure,
            \question_edit_contexts $contexts, array $pagevars, \moodle_url $pageurl) {
        if (!$structure->has_questions()) {
            return '';
        }

        $question = $structure->get_question_in_slot(1);
        $pagehtml = $this->page_row($structure, $question, $contexts, $pagevars, $pageurl);

        // Normalise the page number.
        $pagenumber = $question->page;
        $strcontexts = array();
        $strcontexts[] = 'page-';
        $strcontexts[] = get_string('page') . ' ';
        $strcontexts[] = 'addonpage%3D';
        $strcontexts[] = 'addonpage=';
        $strcontexts[] = 'addonpage="';
        $strcontexts[] = get_string('addquestionfrombanktopage', 'quiz', '');
        $strcontexts[] = 'data-addonpage%3D';
        $strcontexts[] = 'action-menu-';

        foreach ($strcontexts as $strcontext) {
            $pagehtml = str_replace($strcontext . $pagenumber, $strcontext . '%%PAGENUMBER%%', $pagehtml);
        }

        return $pagehtml;
    }

    /**
     * HTML for a page, with ids stripped, so it can be used as a javascript template.
     *
     * @param structure $structure object containing the structure of the quiz.
     * @param \stdClass $quiz the quiz settings.
     * @return string HTML for a new icon
     */
    protected function add_page_icon_template(structure $structure, $quiz) {

        if (!$structure->has_questions()) {
            return '';
        }

        $question = $structure->get_question_in_slot(1);
        $html = $this->page_split_join_button($quiz, $question, true);
        return str_replace('&amp;slot=1&amp;', '&amp;slot=%%SLOT%%&amp;', $html);
    }

    /**
     * Return the contents of the question bank, to be displayed in the question-bank pop-up.
     *
     * @param \mod_quiz\question\bank\custom_view $questionbank the question bank view object.
     * @param array $pagevars the variables from {@link \question_edit_setup()}.
     * @return string HTML to output / send back in response to an AJAX request.
     */
    public function question_bank_contents(\mod_quiz\question\bank\custom_view $questionbank, array $pagevars) {

        $qbank = $questionbank->render('editq', $pagevars['qpage'], $pagevars['qperpage'],
                $pagevars['cat'], $pagevars['recurse'], $pagevars['showhidden'], $pagevars['qbshowtext']);
        return html_writer::div(html_writer::div($qbank, 'bd'), 'questionbankformforpopup');
    }
}
