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
 * Contains class mod_questionnaire\output\renderer
 *
 * @package    mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_questionnaire\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {
    /**
     * Main view page.
     * @param \templateable $page
     * @return string | boolean
     */
    public function render_viewpage($page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_questionnaire/viewpage', $data);
    }

    /**
     * Fill out the questionnaire (complete) page.
     * @param \templateable $page
     * @return string | boolean
     */
    public function render_completepage($page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_questionnaire/completepage', $data);
    }

    /**
     * Fill out the report page.
     * @param \templateable $page
     * @return string | boolean
     */
    public function render_reportpage($page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_questionnaire/reportpage', $data);
    }

    /**
     * Fill out the qsettings page.
     * @param \templateable $page
     * @return string | boolean
     */
    public function render_qsettingspage($page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_questionnaire/qsettingspage', $data);
    }

    /**
     * Fill out the questions page.
     * @param \templateable $page
     * @return string | boolean
     */
    public function render_questionspage($page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_questionnaire/questionspage', $data);
    }

    /**
     * Fill out the preview page.
     * @param \templateable $page
     * @return string | boolean
     */
    public function render_previewpage($page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_questionnaire/previewpage', $data);
    }

    /**
     * Fill out the non-respondents page.
     * @param \templateable $page
     * @return string | boolean
     */
    public function render_nonrespondentspage($page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_questionnaire/nonrespondentspage', $data);
    }

    /**
     * Fill out the fbsections page.
     * @param \templateable $page
     * @return string | boolean
     */
    public function render_fbsectionspage($page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('mod_questionnaire/fbsectionspage', $data);
    }

    /**
     * Render the respondent information line.
     * @param string $text The respondent information.
     */
    public function respondent_info($text) {
        return \html_writer::tag('span', $text, ['class' => 'respondentinfo']);
    }

    /**
     * Render the completion form start HTML.
     * @param string $action The action URL.
     * @param array $hiddeninputs Name/value pairs of hidden inputs used by the form.
     * @return string The output for the page.
     */
    public function complete_formstart($action, $hiddeninputs=[]) {
        $output = '';
        $output .= \html_writer::start_tag('form', ['id' => 'phpesp_response', 'method' => 'post', 'action' => $action]) . "\n";
        foreach ($hiddeninputs as $name => $value) {
            $output .= \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => $name, 'value' => $value]) . "\n";
        }
        return $output;
    }

    /**
     * Render the completion form end HTML.
     * @param array $inputs Type/attribute array of inputs and values used by the form.
     * @return string The output for the page.
     */
    public function complete_formend($inputs=[]) {
        $output = '';
        foreach ($inputs as $type => $attributes) {
            $output .= \html_writer::empty_tag('input', array_merge(['type' => $type], $attributes)) . "\n";
        }
        $output .= \html_writer::end_tag('form') . "\n";
        return $output;
    }

    /**
     * Render the completion form control buttons.
     * @param array | string $inputs Name/(Type/attribute) array of input types and values used by the form.
     * @return string The output for the page.
     */
    public function complete_controlbuttons($inputs=null) {
        $output = '';
        if (is_array($inputs)) {
            foreach ($inputs as $name => $attributes) {
                $output .= \html_writer::empty_tag('input', array_merge(['name' => $name], $attributes));
            }
        } else if (is_string($inputs)) {
            $output .= \html_writer::tag('p', $inputs);
        }
        return $output;
    }

    /**
     * Render a question for a survey.
     * @param mod_questionnaire\question\base $question The question object.
     * @param array $formdata Any returned form data.
     * @param array $dependants Array of all questions/choices depending on $question.
     * @param int $qnum The question number.
     * @param boolean $blankquestionnaire Used for printing a blank one.
     * @return string The output for the page.
     */
    public function question_output($question, $formdata, $dependants=[], $qnum, $blankquestionnaire) {

        $pagetags = $question->question_output($formdata, $dependants, $qnum, $blankquestionnaire);

        // If the question has a template, then render it from the 'qformelement' context. If no template, then 'qformelement'
        // already contains HTML.
        if (($template = $question->question_template())) {
            $pagetags->qformelement = $this->render_from_template($template, $pagetags->qformelement);
        }

        // Calling "question_output" may generate per question notifications. If present, add them to the question output.
        if (($notifications = $question->get_notifications()) !== false) {
            foreach ($notifications as $notification) {
                $pagetags->notifications = $this->notification($notification, \core\output\notification::NOTIFY_ERROR);
            }
        }

        return $this->render_from_template('mod_questionnaire/question_container', $pagetags);
    }

    /**
     * Render a question response.
     * @param mod_questionnaire\question\base $question The question object.
     * @param stdClass $data All of the response data.
     * @param int $qnum The question number.
     * @return string The output for the page.
     */
    public function response_output($question, $data, $qnum=null) {
        $pagetags = $question->response_output($data, $qnum);

        // If the response has a template, then render it from the 'qformelement' context. If no template, then 'qformelement'
        // already contains HTML.
        if (($template = $question->response_template())) {
            $pagetags->qformelement = $this->render_from_template($template, $pagetags->qformelement);
        }

        // Calling "question_output" may generate per question notifications. If present, add them to the question output.
        if (($notifications = $question->get_notifications()) !== false) {
            foreach ($notifications as $notification) {
                $pagetags->notifications = $this->notification($notification, \core\output\notification::NOTIFY_ERROR);
            }
        }
        return $this->render_from_template('mod_questionnaire/question_container', $pagetags);
    }

    /**
     * Render all responses for a question.
     * @param stdClass $data All of the response data.
     * @return string The output for the page.
     */
    public function all_response_output($data=null) {
        $output = '';
        if (is_string($data)) {
            $output .= $data;
        } else {
            foreach ($data as $qnum => $responses) {
                $question = $responses['question'];
                $pagetags = $question->questionstart_survey_display($qnum);
                foreach ($responses as $item => $response) {
                    if ($item !== 'question') {
                        $resptags = $question->response_output($response['respdata']);
                        // If the response has a template, then render it from the 'qformelement' context.
                        // If no template, then 'qformelement' already contains HTML.
                        if (($template = $question->response_template())) {
                            $resptags->qformelement = $this->render_from_template($template, $resptags->qformelement);
                        }
                        $resptags->respdate = $response['respdate'];
                        $pagetags->responses[] = $resptags;
                    }
                }
                $output .= $this->render_from_template('mod_questionnaire/response_container', $pagetags);
            }
        }
        return $output;
    }

    /**
     * Render a question results summary.
     * @param mod_questionnaire\question\base $question The question object.
     * @param array $rids The response ids.
     * @param string $sort The sort order being used.
     * @param string $anonymous The value of the anonymous setting.
     * @return string The output for the page.
     */
    public function results_output($question, $rids, $sort, $anonymous) {
        return $question->display_results($rids, $sort, $anonymous);
    }

    /**
     * Render the reporting navigation bar.
     * @param array $navbar All of the data needed for the template.
     * @return string The rendered HTML.
     */
    public function navigationbar($navbar) {
        return $this->render_from_template('mod_questionnaire/navbaralpha', $navbar);
    }

    /**
     * Render the reporting navigation bar for one user.
     * @param array $navbar All of the data needed for the template.
     * @return string The rendered HTML.
     */
    public function usernavigationbar($navbar) {
        return $this->render_from_template('mod_questionnaire/navbaruser', $navbar);
    }

    /**
     * Render the response list for a number of users.
     * @param array $navbar All of the data needed for the template.
     * @return string The rendered HTML.
     */
    public function responselist($navbar) {
        return $this->render_from_template('mod_questionnaire/responselist', $navbar);
    }

    /**
     * Render a print/preview page number line.
     * @param string $content The content to render.
     * @return string The rendered HTML.
     */
    public function print_preview_pagenumber($content) {
        return \html_writer::tag('div', $content, ['class' => 'surveyPage']);
    }

    /**
     * Render the print/preview completion form end HTML.
     * @param string $url The url to call.
     * @param string $submitstr The submit text.
     * @param string $resetstr The reset text.
     * @return string The output for the page.
     */
    public function print_preview_formend($url, $submitstr, $resetstr) {
        $output = '';
        $output .= \html_writer::start_tag('div');
        $output .= \html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'submit', 'value' => $submitstr]);
        $output .= ' ';
        $output .= \html_writer::tag('a', $resetstr, ['href' => $url]);
        $output .= \html_writer::end_tag('div') . "\n";
        $output .= \html_writer::end_tag('form') . "\n";
        return $output;
    }

    /**
     * Render the back to home link on the save page.
     * @param string $url The url to link to.
     * @param string $text The text to apply the link to.
     * @return string The rendered HTML.
     */
    public function homelink($url, $text) {
        $output = '';
        $output .= \html_writer::start_tag('div', ['class' => 'homelink']);
        $output .= \html_writer::tag('a', $text, ['href' => $url]);
        $output .= \html_writer::end_tag('div');
        return $output;
    }


    /**
     * @param $children
     * @param $langstring
     * @param $strnum
     * @return string
     */
    public function dependency_warnings($children, $langstring, $strnum) {
        $msg = '<div class="warning">' . get_string($langstring, 'questionnaire') . '</div><br />';
        foreach ($children as $child) {
            $loopindicator = array();
            foreach ($child as $subchild) {
                $childname = '';
                if ($subchild->name) {
                    $childname = ' ('.$subchild->name.')';
                }

                // Add conditions.
                switch ($subchild->dependlogic) {
                    case 0:
                        $logic = get_string('notset', 'questionnaire');
                        break;
                    case 1:
                        $logic = get_string('set', 'questionnaire');
                        break;
                    default:
                        $logic = '';
                }

                // Different colouring for and/or.
                switch ($subchild->dependandor) {
                    case 'or':
                        $color = 'qdepend-or';
                        break;
                    case 'and':
                        $color = "qdepend";
                        break;
                    default:
                        $color = "";
                }

                if (!in_array($subchild->qdependquestion, $loopindicator)) {
                    $msg .= '<div class = "qn-container">'.$strnum.' '.$subchild->position.$childname.
                        '<br/><span class="'.$color.'"><strong>'.
                        get_string('dependquestion', 'questionnaire').'</strong>'.
                        ' ('.$strnum.' '.$subchild->parentposition.') '.
                        '&nbsp;:&nbsp;'.$subchild->parent.' '.$logic.'</span>';
                } else {
                    $msg .= '<br/><span class="'.$color.'"><strong>'.
                        get_string('dependquestion', 'questionnaire').'</strong>'.
                        ' ('.$strnum.' '.$subchild->parentposition.') '.
                        '&nbsp;:&nbsp;'.$subchild->parent.' '.$logic.'</span>';
                }
                $loopindicator[] = $subchild->qdependquestion;
            }
            $msg .= '<div class="qn-question">'.
                $subchild->content.
                '</div></div>';
        }
        return $msg;
    }

    /**
     * Get displayable list of parents for the question in questions_form.
     * @param $qid The question id.
     * @param $dependencies Array of dependency records for a question.
     * @return string
     */
    public function get_dependency_html($qid, $dependencies) {
        $html = '';
        foreach ($dependencies as $dependency) {
            switch ($dependency->dependlogic) {
                case 0:
                    $logic = get_string('notset', 'questionnaire');
                    break;
                case 1:
                    $logic = get_string('set', 'questionnaire');
                    break;
                default:
                    $logic = '';
            }

            // TODO - Move the HTML generation to the renderer.
            if ($dependency->dependandor == "and") {
                $html .= '<div id="qdepend_' . $qid . '_' . $dependency->dependquestionid . '_' .
                    $dependency->dependchoiceid . '" class="qdepend">' . '<strong>' .
                    get_string('dependquestion', 'questionnaire') . '</strong> : '. get_string('position', 'questionnaire') . ' ' .
                    $dependency->parentposition . ' (' . $dependency->parent . ') ' . $logic . '</div>';
            } else {
                $html .= '<div id="qdepend_or_' . $qid . '_' . $dependency->dependquestionid . '_' .
                    $dependency->dependchoiceid . '" class="qdepend-or">' . '<strong>' .
                    get_string('dependquestion', 'questionnaire') . '</strong> : '. get_string('position', 'questionnaire') . ' ' .
                    $dependency->parentposition . ' (' . $dependency->parent . ') ' . $logic . '</div>';
            }
        }
        return $html;
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of flexible_table
     *
     * @param flexible_table $table
     * @param boolean $buffering True if already buffering.
     * @return string HTML
     */
    public function flexible_table(\flexible_table $table, $buffering = false) {

        $o = '';
        if (!$buffering) {
            ob_start();
        }
        $table->finish_output();
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }
}