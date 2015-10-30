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
     * Output the icon for a question type.
     *
     * @param string $qtype the question type.
     * @return string HTML fragment.
     */
    public function qtype_icon($qtype) {
        $qtype = question_bank::get_qtype($qtype, false);
        $namestr = $qtype->local_name();

        return $this->pix_icon('icon', $namestr, $qtype->plugin_name(), array('title' => $namestr));
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
        global $OUTPUT;

        // Start the form content.
        $formcontent = html_writer::start_tag('form', array('action' => new moodle_url('/question/question.php'),
                'id' => 'chooserform', 'method' => 'get'));

        // Add the hidden fields.
        $hiddenfields = '';
        $hiddenfields .= html_writer::tag('input', '', array('type' => 'hidden', 'name' => 'category', 'id' => 'qbankcategory'));
        $hiddenfields .= html_writer::tag('input', '', array('type' => 'hidden', 'name' => 'courseid', 'value' => $course->id));
        foreach ($hiddenparams as $k => $v) {
            $hiddenfields .= html_writer::tag('input', '', array('type' => 'hidden', 'name' => $k, 'value' => $v));
        }
        $hiddenfields .= html_writer::tag('input', '', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
        $formcontent .= html_writer::div($hiddenfields, '', array('id' => 'typeformdiv'));

        // Put everything into one tag 'options'.
        $formcontent .= html_writer::start_tag('div', array('class' => 'options'));
        $formcontent .= html_writer::div(get_string('selectaqtypefordescription', 'question'), 'instruction');

        // Put all options into one tag 'qoptions' to allow us to handle scrolling.
        $formcontent .= html_writer::start_tag('div', array('class' => 'alloptions'));

        // First display real questions.
        $formcontent .= $this->qbank_chooser_title('questions', 'question');
        $formcontent .= $this->qbank_chooser_types($real);

        $formcontent .= html_writer::div('', 'separator');

        // Then fake questions.
        $formcontent .= $this->qbank_chooser_title('other');
        $formcontent .= $this->qbank_chooser_types($fake);

        // Options.
        $formcontent .= html_writer::end_tag('div');

        // Types.
        $formcontent .= html_writer::end_tag('div');

        // Add the form submission buttons.
        $submitbuttons = '';
        $submitbuttons .= html_writer::tag('input', '',
                array('type' => 'submit', 'name' => 'submitbutton', 'class' => 'submitbutton', 'value' => get_string('add')));
        $submitbuttons .= html_writer::tag('input', '',
                array('type' => 'submit', 'name' => 'addcancel', 'class' => 'addcancel', 'value' => get_string('cancel')));
        $formcontent .= html_writer::div($submitbuttons, 'submitbuttons');

        $formcontent .= html_writer::end_tag('form');

        // Wrap the whole form in a div.
        $formcontent = html_writer::tag('div', $formcontent, array('id' => 'chooseform'));

        // Generate the header and return the whole form.
        $header = html_writer::div(get_string('chooseqtypetoadd', 'question'), 'choosertitle hd');
        return $header . html_writer::div(html_writer::div($formcontent, 'choosercontainer'), 'chooserdialogue');
    }

    /**
     * Build the HTML for a specified set of question types.
     *
     * @param array $types A set of question types as used by the qbank_chooser_module function
     * @return string The composed HTML for the module
     */
    protected function qbank_chooser_types($types) {
        $return = '';
        foreach ($types as $type) {
            $return .= $this->qbank_chooser_qtype($type);
        }
        return $return;
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
        $output = '';
        $classes[] = 'option';
        $output .= html_writer::start_tag('div', array('class' => implode(' ', $classes)));
        $output .= html_writer::start_tag('label', array('for' => 'qtype_' . $qtype->plugin_name()));
        $output .= html_writer::tag('input', '', array('type' => 'radio',
                'name' => 'qtype', 'id' => 'qtype_' . $qtype->plugin_name(), 'value' => $qtype->name()));

        $output .= html_writer::start_tag('span', array('class' => 'modicon'));
        // Add an icon if we have one.
        $output .= $this->pix_icon('icon', $qtype->local_name(), $qtype->plugin_name(),
                array('title' => $qtype->local_name(), 'class' => 'icon'));
        $output .= html_writer::end_tag('span');

        $output .= html_writer::span($qtype->menu_name(), 'typename');

        // Format the help text using markdown with the following options.
        $options = new stdClass();
        $options->trusted = false;
        $options->noclean = false;
        $options->smiley = false;
        $options->filter = false;
        $options->para = true;
        $options->newlines = false;
        $options->overflowdiv = false;
        $qtype->help = format_text(get_string('pluginnamesummary', $qtype->plugin_name()), FORMAT_MARKDOWN, $options);

        $output .= html_writer::span($qtype->help, 'typesummary');
        $output .= html_writer::end_tag('label');
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Return the title for the question bank chooser.
     *
     * @param string $title The language string identifier
     * @param string $identifier The component identifier
     * @return string The composed HTML for the title
     */
    protected function qbank_chooser_title($title, $identifier = null) {
        $span = html_writer::span('', 'modicon');
        $span .= html_writer::span(get_string($title, $identifier), 'typename');

        return html_writer::div($span, 'option moduletypetitle');
    }
}
