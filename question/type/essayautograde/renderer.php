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
 * Essay question renderer class.
 *
 * @package    qtype
 * @subpackage essayautograde
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/essay/renderer.php');

/**
 * Generates the output for essayautograde questions.
 *
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayautograde_renderer extends qtype_with_combined_feedback_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE;

        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();

        // Generate all the stats for this response.
        $question->update_current_response($response, $options);
        // This is probably rather wasteful.  A better solution
        // would be to store the values using $step->set_qt_var()
        // in question/type/essayautograde/question.php.

        // format question text
        $qtext = $question->format_questiontext($qa);

        // cache read-only flag
        $readonly = ($options->readonly ? 1 : 0);

        // Cache the label separator string, usually a colon ": "
        $separator = trim(get_string('labelsep', 'langconfig'));

        // Answer field.
        $step = $qa->get_last_step_with_qt_var('answer');

        if (! $step->has_qt_var('answer') && empty($options->readonly)) {
            // Question has never been answered, fill it with response template.
            $step = new question_attempt_step(array('answer' => $question->responsetemplate));
        }

        $renderer = $question->get_format_renderer($this->page);
        if (method_exists($renderer, 'set_displayoptions')) {
            $renderer->set_displayoptions($options); // Moodle 4.x and later
        }

        $linecount = $question->responsefieldlines;

        if ($readonly) {
            $answer = $renderer->response_area_read_only('answer', $qa, $step, $linecount, $options->context);
            $answer = preg_replace('/<a[^>]*class="[^">]*autolink[^">]*"[^>]*>(.*?)<\/a>/ius', '$1', $answer);
            $answer = preg_replace('/ *min-height: [0-9.]+em;/ius', '$1', $answer);
            if ($question->errorcmid) {
                $start = strpos($answer, '>');
                if ($start === false) {
                    $start = 0;
                } else {
                    $start += 1;
                }
                $end = strrpos($answer, '<');
                if ($end === false) {
                    $end = strlen($answer);
                }
                $currentresponse = $question->get_current_response();
                if ($currentresponse->errortext) {
                    $answer = substr_replace($answer, $currentresponse->errortext, $start, $end - $start);
                }
            }
        } else {
            $answer = $renderer->response_area_input('answer', $qa, $step, $linecount, $options->context);
        }

        $countitems = $question->get_current_response('count');
        $minitems = 0;
        $maxitems = 0;

        $itemtype = '';
        $itemcount =''; // html string for <div class="itemcount" ...>

        switch ($question->itemtype) {
            case $question->plugin_constant('ITEM_TYPE_CHARS'): $itemtype = 'chars'; break;
            case $question->plugin_constant('ITEM_TYPE_WORDS'): $itemtype = 'words'; break;
            case $question->plugin_constant('ITEM_TYPE_SENTENCES'): $itemtype = 'sentences'; break;
            case $question->plugin_constant('ITEM_TYPE_PARAGRAPHS'): $itemtype = 'paragraphs'; break;
            case $question->plugin_constant('ITEM_TYPE_FILES'): $itemtype = 'files'; break;
        }

        // We only count chars or words, because counting sentences
        // and paragraphs is unreliable and not so meaningful.
        if ($itemtype == 'chars' || $itemtype == 'words') {

            $warning = '';
            if ($itemtype == 'words') {
                $minitems = (empty($question->minwordlimit) ? 0 : $question->minwordlimit);
                $maxitems = (empty($question->maxwordlimit) ? 0 : $question->maxwordlimit);

                $params = array('class' => 'warning rounded bg-danger text-light ml-2 px-2 py-1');
                switch (true) {

                    case $maxitems && $countitems && ($maxitems < $countitems):
                        $warning = ($readonly ? 'maxwordserror' : 'maxwordswarning');
                        $warning = get_string($warning, $this->plugin_name());
                        break;

                    case $minitems && $countitems && ($minitems > $countitems):
                        $warning = ($readonly ? 'minwordserror' : 'minwordswarning');
                        $warning = get_string($warning, $this->plugin_name());
                        break;

                    default:
                        $params['class'] .= ' d-none';
                }
                $warning = ' '.html_writer::tag('span', $warning, $params);
            }

            // The strings below have to come from the essayautograde plugin.
            $plugin = 'qtype_essayautograde';

            $label = 'count'.$itemtype.'label';
            $label = get_string($label, $plugin).$separator;
            $label = html_writer::tag('b', $label, array('class' => 'labeltext'));
            $value = html_writer::tag('i', $countitems, array('class' => 'value')).$warning;
            $itemcount .= html_writer::tag('p', $label.' '.$value, array('class' => 'countitems my-0'));

            if ($minitems) {
                $label = 'min'.$itemtype.'label';
                $label = get_string($label, $plugin).$separator;
                $label = html_writer::tag('b', $label, array('class' => 'labeltext'));
                $value = html_writer::tag('i', $minitems, array('class' => 'value'));
                $itemcount .= html_writer::tag('p', $label.' '.$value, array('class' => 'minitems my-0'));
            }

            if ($maxitems) {
                $label = 'max'.$itemtype.'label';
                $label = get_string($label, $plugin).$separator;
                $label = html_writer::tag('b', $label, array('class' => 'labeltext'));
                $value = html_writer::tag('i', $maxitems, array('class' => 'value'));
                $itemcount .= html_writer::tag('p', $label.' '.$value, array('class' => 'maxitems my-0'));
            }
        }

        $files = '';
        if ($question->attachments) {
            if ($readonly) {
                $files = $this->files_read_only($qa, $options);
            } else {
                $files = $this->files_input($qa, $options);
            }
        }

        $result = '';
        $result .= html_writer::tag('div', $qtext, array('class' => 'qtext'));
        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::tag('div', $answer, array('class' => 'answer'));
        if ($itemcount && $this->show_itemcount()) {
            // Mimic the id created by "response_area_input()" in "essay/renderer.php".
            // The data-xxx values are needed by the javascript in "mobile/mobile.js".
            $params = array('id' => 'id_'.$qa->get_qt_field_name('answer_itemcount'),
                            'class' => 'itemcount rounded border bg-secondary text-dark my-2 px-3 py-2',
                            'data-itemtype' => $itemtype,
                            'data-minitems' => $minitems,
                            'data-maxitems' => $maxitems);
            $result .= html_writer::tag('div', $itemcount, $params);
        }

        if ($qa->get_state() == question_state::$gradedwrong) {
            if ($error = $question->get_validation_error($step->get_qt_data())) {
                $result .= html_writer::tag('div', $error, array('class' => 'validationerror'));
            }
        }

        $result .= html_writer::tag('div', $files, array('class' => 'attachments'));
        $result .= html_writer::end_tag('div'); // div.ablock

        $editor = $this->get_editor_type($question);
        $sample = question_utils::to_plain_text($question->responsesample,
                                                $question->responsesampleformat,
                                                array('para' => false));

        $params = array($readonly, $itemtype, $minitems, $maxitems, $editor, $sample);
        $PAGE->requires->js_call_amd('qtype_essayautograde/essayautograde', 'init', $params);

        return $result;
    }

    /**
     * Specify whether to show (TRUE) the item count DIV, or not (FALSE).
     *
     * @return bool
     */
    public function show_itemcount() {
        return true;
    }

    /**
     * Specify the short name for the editor used to input the response.
     * This is used to locate where on the page to insert the sample response.
     * For Essay questions, the editor type will be "atto", "tinymce" or "textarea".
     * For Speak questions, the editor will be one of the speech recorders.
     *
     * @param object $question
     * @return string The short name of the editor.
     */
    public function get_editor_type($question) {
        // extract editor name from full editor class by remove the trailing"_texteditor"
        // e.g. textarea, atto, tinymce
        $editor = editors_get_preferred_editor();
        return substr(get_class($editor), 0, -11);
    }

    /**
     * Displays any attached files when the question is in read-only mode.
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should
     *      not be displayed. Used to get the context.
     */
    public function files_read_only(question_attempt $qa, question_display_options $options) {
        $output = array();

        $files = $qa->get_last_qt_files('attachments', $options->context->id);
        foreach ($files as $file) {

            $url = $qa->get_response_file_url($file);
            $url = preg_replace('/(\?|\&|\&amp;)forcedownload=1/', '', $url);

            $mimetype = $file->get_mimetype();
            $mimetext = get_mimetype_description($file);

            $filetype = substr($mimetype, 0, strpos($mimetype, '/'));
            switch ($filetype) {

                case 'image':
                    // Use a Bootstrap class to prevent <img> extending outside the <p> element.
                    // Same effect as style="max-width: 100%; height: auto; width: auto;"
                    $params = array('src' => $url,
                                    'alt' => $mimetext,
                                    'class' => 'img-responsive');
                    $file = html_writer::empty_tag('img', $params);
                    break;

                case 'audio':
                    $file = html_writer::empty_tag('source', array('src' => $url));
                    $params = array('controls' => 'true');
                    $file = html_writer::tag('audio', $file.$url, $params);
                    break;

                case 'video':
                    $file = html_writer::empty_tag('source', array('src' => $url));
                    $params = array('controls' => 'true',
                                    'playsinline' => 'true');
                    $file = html_writer::tag('video', $file.$url, $params);
                    break;

                default:
                    $icon = file_file_icon($file);
                    $icon = $this->output->pix_icon($icon, $mimetext, 'moodle', array('class' => 'icon'));
                    $file = html_writer::link($qa->get_response_file_url($file), $icon.' '.s($file->get_filename()));
            }
            $params = array('class'=> "read-only-file $filetype",
                            'style' => 'width: 100%; max-width: 480px;');
            $output[] = html_writer::tag('p', $file, $params);
        }
        return implode($output);
    }

    /**
     * Displays the input control for when the student should upload a single file.
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should
     *      not be displayed. Used to get the context.
     */
    public function files_input(question_attempt $qa, question_display_options $options) {
        global $CFG, $PAGE;
        require_once($CFG->dirroot.'/lib/form/filemanager.php');

        // cache reference to $question
        $question = $qa->get_question();

        $name = 'attachments';
        $itemid = $qa->prepare_response_files_draft_itemid($name, $options->context->id);
        $pickeroptions = (object)array('mainfile' => null,
                                       'maxfiles' => $question->attachments,
                                       'itemid'   => $itemid,
                                       'context'  => $options->context,
                                       'return_types' => FILE_INTERNAL);

        if ($filetypes = $question->filetypeslist) {
            $pickeroptions->accepted_types = $filetypes;
        }

        $filemanager = new form_filemanager($pickeroptions);
        $filemanager->options->maxbytes = $qa->get_question()->maxbytes;
        $filesrenderer = $this->page->get_renderer('core', 'files');
        $params = array('type'  => 'hidden',
                        'value' => $itemid,
                        'name'  => $qa->get_qt_field_name($name));
        $output = $filesrenderer->render($filemanager).html_writer::empty_tag('input', $params);

        // Remove restrictions (this is done  with CSS)
        // $output = preg_replace('/(?<=<div class="fp-restrictions">)\s*<span>.*?<\/span>/s', '', $output);

        $restrictions = array();

        // Append warning about required number of attachments.
        if ($question->attachments) {
            if ($question->attachments == $question->attachmentsrequired) {
                $restrictions[] = get_string('requiredfilecount', $this->plugin_name(), $question->attachments);
            } else {
                if ($question->attachmentsrequired > 0) {
                    $restrictions[] = get_string('minimumfilecount', $this->plugin_name(), $question->attachmentsrequired);
                }
                if ($question->attachments > 0) {
                    $restrictions[] = get_string('maximumfilecount', $this->plugin_name(), $question->attachments);
                }
            }
        }

        list($context, $course, $cm) = get_context_info_array($options->context->id);
        if ($course) {
            $maxbytes = $course->maxbytes;
        } else {
            $maxbytes = $PAGE->course->maxbytes;
        }
        $maxbytes = get_user_max_upload_file_size($context, $CFG->maxbytes, $maxbytes);
        if ($maxbytes == USER_CAN_IGNORE_FILE_SIZE_LIMITS) {
            // $maxbytes = get_string('unlimited');
        } else {
            $restrictions[] = get_string('maximumfilesize', $this->plugin_name(), $maxbytes);
        }

        // Append details of accepted file types.
        if ($filetypes) {
            if (class_exists('\\core_form\\filetypes_util')) {
                // Moodle >= 3.4
                $util = new \core_form\filetypes_util();
                $filetypes = $util->describe_file_types($filetypes);
                $filetypes = $this->render_from_template('core_form/filetypes-descriptions', $filetypes);
                $filetypes = get_string('acceptedfiletypes', 'qtype_essay').get_string('labelsep', 'langconfig').$filetypes;
                $restrictions[] = $filetypes;
            } else {
                // Moodle <= 3.3
                $filetypes = strtolower($filetypes);
                $filetypes = preg_split('/[\s,;:"\']+/', $filetypes, null, PREG_SPLIT_NO_EMPTY);
                foreach ($filetypes as $i => $filetype) {
                    $filetype = str_replace('*.', '', $filetype);
                    $filetypes[$i] = trim(ltrim($filetype, '.'));
                }
                $filetypes = array_filter($filetypes);
                $filetypes = implode(', ', $filetypes);
                $restrictions[] = get_string('acceptedfiletypes', 'qtype_essay').get_string('labelsep', 'langconfig').$filetypes;
            }
        }

        if (count($restrictions)) {
            $output .= html_writer::alist($restrictions);
        }

        return $output;
    }

    public function manual_comment(question_attempt $qa, question_display_options $options) {
        if ($options->manualcomment==question_display_options::EDITABLE) {
            $plugin = $this->plugin_name();
            $question = $qa->get_question();
            $comment = $question->graderinfo;
            $comment = $question->format_text($comment, $question->graderinfoformat, $qa, $plugin, 'graderinfo', $question->id);
            $comment = html_writer::nonempty_tag('div', $comment, array('class' => 'graderinfo'));
        } else {
            $comment = ''; // comment is not currently editable
        }
        return $comment;
    }

    /**
     * Generate the specific feedback. This is feedback that varies according to
     * the response the student gave.
     *
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    public function specific_feedback(question_attempt $qa) {
        global $DB;

        $output = '';

        // Decide if we should show grade explanation.
        if ($step = $qa->get_last_step()) {
            // We are only interested in (mangr|graded)(right|partial|wrong)
            // For a full list of states, see question/engine/states.php
            $show = preg_match('/(right|partial|wrong)$/', $step->get_state());
        } else {
            $show = false;
        }

        // If required, show explanation of grade calculation.
        if ($show) {

            $plugin = 'qtype_essayautograde';
            $question = $qa->get_question();

            // Get the current response text and information
            $currentresponse = $question->get_current_response();

            // Specify decision for decimal numbers.
            $options = $currentresponse->displayoptions;
            if ($options && isset($options->markdp)) {
                $precision = $options->markdp;
            } else {
                $precision = 0;
            }

            // cache the maximum grade for this question
            $maxgrade = $qa->get_max_mark(); // float number
            $maxgradetext = $qa->format_max_mark($precision);

            $gradeband = array_values($currentresponse->bands); // percents
            $gradeband = array_search($currentresponse->completepercent, $gradeband);
            $gradeband++;

            if ($question->ai && $question->ai->grademax && $question->ai->feedback) {
                $aigrade = $question->ai->grade;
                $aigrademax = $question->ai->grademax;
                $aifeedback = $question->ai->feedback;
            } else {
                $aigrade = $qa->get_last_qt_var('_aigrade', 0);
                $aigrademax = $qa->get_last_qt_var('_aigrademax', 100);
                $aifeedback = $qa->get_last_qt_var('_aifeedback', '');
            }

            $itemtype = '';
            switch ($question->itemtype) {
                case $question->plugin_constant('ITEM_TYPE_CHARS'): $itemtype = get_string('chars', $plugin); break;
                case $question->plugin_constant('ITEM_TYPE_WORDS'): $itemtype = get_string('words', $plugin); break;
                case $question->plugin_constant('ITEM_TYPE_SENTENCES'): $itemtype = get_string('sentences', $plugin); break;
                case $question->plugin_constant('ITEM_TYPE_PARAGRAPHS'): $itemtype = get_string('paragraphs', $plugin); break;
                case $question->plugin_constant('ITEM_TYPE_FILES'): $itemtype = get_string('files', $plugin); break;
            }
            $itemtype = core_text::strtolower($itemtype);

            $context = $this->get_best_context($options, $question);
            if ($showteacher = has_capability('mod/quiz:grade', $context)) {
                $showstudent = false;
            } else {
                $showstudent = has_capability('mod/quiz:attempt', $context);
            }

            $show = array(
                $this->plugin_constant('SHOW_NONE') => false,
                $this->plugin_constant('SHOW_STUDENTS_ONLY') => $showstudent,
                $this->plugin_constant('SHOW_TEACHERS_ONLY') => $showteacher,
                $this->plugin_constant('SHOW_TEACHERS_AND_STUDENTS') => ($showstudent || $showteacher),
            );

            if ($question->itemtype == $question->plugin_constant('ITEM_TYPE_FILES')) {
                $showgradebands = false;
                $showtargetphrases = false;
                $showtextstatitems = preg_match('/\bfiles\b/', $question->textstatitems);
            } else {
                $showgradebands = ($show[$question->showgradebands] && count($currentresponse->bands));
                $showtargetphrases = $show[$question->showtargetphrases] && count($currentresponse->phrases);
                $showtextstatitems = ($show[$question->showtextstats] && strlen(trim($question->textstatitems)));
            }

            if ($showtextstatitems) {
                $strman = get_string_manager();

                $table = new html_table();
                $table->attributes['class'] = 'generaltable essayautograde review stats';

                $names = explode(',', $question->textstatitems);
                $names = array_filter($names);
                foreach ($names as $name) {
                    $label = get_string($name, $plugin);
                    if ($strman->string_exists($name.'_help', $plugin)) {
                        $label .= $this->help_icon($name, $plugin);
                    }
                    if (isset($currentresponse->stats->$name)) {
                        $value = $currentresponse->stats->$name;
                    } else {
                        $value = '';
                    }
                    if (is_int($value)) {
                        $value = number_format($value);
                    }
                    $cells = array(new html_table_cell($label),
                                   new html_table_cell($value));
                    $table->data[] = new html_table_row($cells);
                }
                $output .= html_writer::tag('h5', get_string('textstatistics', $plugin));
                $output .= html_writer::table($table);
            }

            // show explanation of calculation, if required
            if ($show[$question->showcalculation]) {

                $details = array();

                // Partial grade bands.
                if ($currentresponse->completecount) {
                    $a = (object)array('percent'   => $currentresponse->completepercent,
                                       'count'     => $currentresponse->completecount,
                                       'gradeband' => $gradeband,
                                       'itemtype'  => $itemtype);
                    if ($showgradebands) {
                        $name = 'explanationcompleteband';
                    } else {
                        $name = 'explanationfirstitems';
                    }
                    $details[] = $this->get_calculation_detail($name, $plugin, $a);
                }

                // Partial grade bands.
                if ($currentresponse->partialcount) {
                    $a = (object)array('percent'   => $currentresponse->partialpercent,
                                       'count'     => $currentresponse->partialcount,
                                       'gradeband' => ($gradeband + 1),
                                       'itemtype'  => $itemtype);
                    if ($showgradebands) {
                        $name = 'explanationpartialband';
                    } else if (count($details)) {
                        $name = 'explanationremainingitems';
                    } else if ($currentresponse->partialpercent) {
                        $name = 'explanationitems';
                    } else {
                        $name = '';
                    }
                    if ($name) {
                        $details[] = $this->get_calculation_detail($name, $plugin, $a);
                    }
                }

                // Target phrases.
                foreach ($currentresponse->myphrases as $myphrase => $phrase) {
                    $grade = $currentresponse->phrases[$phrase];
                    $a = (object)array('percent' => $grade.'%',
                                       'phrase'  => $myphrase);
                    $details[] = $this->get_calculation_detail('explanationtargetphrase', $plugin, $a);
                }

                // AI generated grade
                if ($question->aipercent && $aigrademax) {
                    $aigrade = round($question->aipercent * ($aigrade / $aigrademax), 1);
                    $details[] = "+ ({$aigrade}% for AI-generated grade: ($aigrade / $aigrademax) x {$question->aipercent}%)";
                }

                // Common errors.
                foreach ($currentresponse->errors as $error => $link) {
                    $a = (object)array('percent' => $question->errorpercent,
                                       'error'   => $error);
                    $details[] = $this->get_calculation_detail('explanationcommonerror', $plugin, $a, '- ');
                }

                // Files.
                if ($question->itemtype == $question->plugin_constant('ITEM_TYPE_FILES')) {
                    $a = (object)array('percent' => $currentresponse->rawpercent,
                                       'filecount' => $currentresponse->count,
                                       'itemcount' => $question->itemcount);
                    $details[] = get_string('explanationfiles', $plugin, $a);
                }

                // Plagiarism links, if any.
                foreach ($currentresponse->plagiarism as $plagiarism) {
                    $details[] = html_writer::tag('p', $plagiarism);
                }

                if (empty($details) && $currentresponse->count) {
                    $a = (object)array('count'    => $currentresponse->count,
                                       'itemtype' => $itemtype);
                    $details[] = $this->get_calculation_detail('explanationnotenough', $plugin, $a);
                }

                if ($count = count($details)) {

                    $details = implode(html_writer::empty_tag('br'), $details);
                    if ($count >= 2) {
                        $details = "($details)";
                    }

                    $step = $qa->get_last_step_with_behaviour_var('finish');
                    if ($step->get_id()) {
                        $rawgrade = format_float($step->get_fraction() * $maxgrade, $precision);
                    } else {
                        $rawgrade = $qa->format_mark($precision);
                    }
                    $rawpercent = $currentresponse->rawpercent;

                    $autopercent = $currentresponse->autopercent;
                    $autograde = $currentresponse->autofraction * $maxgrade;

                    if ($trypenalty = $question->penalty) {
                        // A "try" is actually a click of the "Check" button
                        // in "interactive" mode with a less-than-perfect response.
                        // A "Check" of a correct response does not count as a "try".
                        $trycount = $qa->get_step(0)->get_behaviour_var('_triesleft');
                        $trycount -= $qa->get_last_behaviour_var('_triesleft');
                        $penalty = max(0, $trypenalty * $trycount);
                    } else {
                        $trypenalty = 0;
                        $trycount = 0;
                        $penalty = 0;
                    }

                    if ($penalty) {
                        $penaltygrade = format_float($penalty * $maxgrade, $precision);
                        $penaltypercent = ($penalty * 100);
                        if (fmod($penaltypercent, 1)==0) {
                            $penaltypercent = intval($penaltypercent);
                        } else {
                            $penaltypercent = format_float($penaltypercent, $precision);
                        }
                        $penaltytext = $penaltypercent.'%';
                        if ($trycount > 1) {
                            $trypenaltypercent = ($trypenalty * 100);
                            if (fmod($trypenaltypercent, 1)==0) {
                                $trypenaltypercent = intval($trypenaltypercent);
                            } else {
                                $trypenaltypercent = format_float($trypenaltypercent, $precision);
                            }
                            $penaltytext .= ' = ('.$trycount.' x '.$trypenaltypercent.'%)';
                        }
                    } else {
                        $penaltytext = '';
                        $penaltygrade = 0;
                        $penaltypercent = 0;
                    }

                    $finalgrade = max(0.0, $autograde - $penaltygrade);
                    $finalpercent = max(0, $autopercent - $penaltypercent);

                    // numeric values used by explanation strings
                    $a = (object)array('maxgrade' => $maxgradetext,
                                       'rawpercent' => $rawpercent,
                                       'autopercent' => $autopercent,
                                       'penaltytext' => $penaltytext,
                                       'finalgrade' => format_float($finalgrade, $precision),
                                       'finalpercent' => $finalpercent,
                                       'details' => $details);

                    $output .= html_writer::tag('h5', get_string('gradecalculation', $plugin));
                    $output .= html_writer::tag('p', get_string('explanationmaxgrade', $plugin, $a));
                    $output .= html_writer::tag('p', get_string('explanationrawpercent', $plugin, $a));
                    if ($rawpercent != $autopercent) {
                        $output .= html_writer::tag('p', get_string('explanationautopercent', $plugin, $a));
                    }
                    if ($penalty) {
                        $output .= html_writer::tag('p', get_string('explanationpenalty', $plugin, $a));
                    }
                    $output .= html_writer::tag('p', get_string('explanationgrade', $plugin, $a));

                    // add details of most recent manual override, if any
                    $step = $qa->get_last_step_with_behaviour_var('mark');
                    if ($step->get_id()) {
                        $a = (object)array(
                            'datetime' => userdate($step->get_timecreated(), get_string('explanationdatetime', $plugin)),
                            'manualgrade' => format_float($step->get_behaviour_var('mark'), $precision),
                        );
                        $output .= html_writer::tag('p', get_string('explanationoverride', $plugin, $a));

                        // add manual override details
                        $details = array();

                        // add manual comment, if any
                        $comment = $step->get_behaviour_var('comment');
                        $commentformat  = $step->get_behaviour_var('commentformat');
                        $commentoptions = (object)array('noclean' => true, 'para' => false);
                        if (is_null($comment)) {
                            list($comment, $commentformat) = $qa->get_manual_comment();
                        }
                        if ($comment = format_text($comment, $commentformat, $commentoptions)) {
                            $comment = shorten_text(html_to_text($comment), 80);
                            $comment = html_writer::tag('i', $comment);
                            $header = get_string('comment', 'quiz');
                            $details[] = html_writer::tag('b', $header.': ').$comment;
                        }

                        // add manual grader (user who manually graded the essay) info, if available
                        //if ($grader = $step->get_user_id()) {
                        //    if ($grader = $DB->get_record('user', array('id' => $grader))) {
                        //        $grader = fullname($grader);
                        //        $header = get_string('grader', 'gradereport_history');
                        //        $details[] = html_writer::tag('b', $header.': ').$grader;
                        //    }
                        //}

                        if (count($details)) {
                            $output .= html_writer::alist($details);
                        }
                    }
                }
            }

            // Show grade bands, if required.
            if ($showgradebands) {
                $details = array();
                $i = 1; // grade band index
                foreach ($currentresponse->bands as $count => $grade) {
                    $detail = get_string('gradeband', $plugin);
                    $detail = str_replace('{no}', $i++, $detail);
                    $details[] = html_writer::tag('dt', $detail);
                    $a = (object)array('count' => $count, 'percent' => $grade.'%');
                    $detail = get_string('bandtext', $plugin, $a);
                    $details[] = html_writer::tag('dd', $detail);
                }
                $output .= html_writer::tag('h5', get_string('gradebands', $plugin));
                $output .= html_writer::tag('dl', implode('', $details), array('class' => 'gradebands'));
            }

            // Show target phrases, if required.
            if ($showtargetphrases) {
                $details = array();
                foreach ($currentresponse->phrases as $match => $grade) {
                    $a = (object)[
                        'phrase' => '"'.$match.'"',
                        'percent' => $grade.'%',
                    ];
                    $details[] = get_string('phrasetext', $plugin, $a);
                }
                $output .= html_writer::tag('h5', get_string('targetphrases', $plugin));
                $output .= html_writer::alist($details);
            }

            // Show actionable feedback, if required.
            if ($show[$question->showfeedback]) {
                $hints = array();

                $output .= html_writer::tag('h5', get_string('feedback', $plugin));
                $output .= html_writer::start_tag('table', array('class' => 'generaltable essayautograde review feedback'));

                // Overall grade
                $step = $qa->get_last_step_with_behaviour_var('finish');
                if ($step->get_id()) {
                    $rawgrade = format_float($step->get_fraction() * $maxgrade, $precision);
                } else {
                    $rawgrade = $qa->format_mark($precision);
                }
                $maxgrade = $qa->format_max_mark($precision);

                $output .= html_writer::start_tag('tr');
                $output .= html_writer::tag('th', get_string('gradeforthisquestion', $plugin), array('class' => 'cell c0'));
                $output .= html_writer::tag('td', html_writer::tag('b', $rawgrade.' / '.$maxgradetext), array('class' => 'cell c1'));
                $output .= html_writer::end_tag('tr');

                // Item count
                if ($maxcount = $question->itemcount) {
                    $count = $currentresponse->count;
                    switch ($question->itemtype) {
                        case $question->plugin_constant('ITEM_TYPE_CHARS'):
                            $type = 'chars';
                            $hint = 'feedbackhintchars';
                            break;
                        case $question->plugin_constant('ITEM_TYPE_WORDS'):
                            $type = 'words';
                            $hint = 'feedbackhintwords';
                            break;
                        case $question->plugin_constant('ITEM_TYPE_SENTENCES'):
                            $type = 'sentences';
                            $hint = 'feedbackhintsentences';
                            break;
                        case $question->plugin_constant('ITEM_TYPE_PARAGRAPHS'):
                            $type = 'paragraphs';
                            $hint = 'feedbackhintparagraphs';
                            break;
                        case $question->plugin_constant('ITEM_TYPE_FILES'):
                            $type = 'files';
                            $hint = 'feedbackhintfiles';
                            break;
                        default:
                            // shouldn't happen !!
                            $type = $question->itemtype;
                            $hint = '';
                    }
                    $output .= html_writer::start_tag('tr', array('class' => 'items'));
                    $output .= html_writer::tag('th', get_string($type, $plugin), array('class' => 'cell c0'));
                    $output .= html_writer::tag('td', $count.' / '.$maxcount, array('class' => 'cell c1'));
                    $output .= html_writer::end_tag('tr');
                    if ($count < $maxcount && $hint) {
                        $hints[$type] = get_string($hint, $plugin);
                    }
                }

                // Target phrases
                if ($showtargetphrases) {
                    $maxcount = count($currentresponse->phrases);
                } else {
                    $maxcount = 0;
                }
                if ($maxcount) {
                    $count = count($currentresponse->myphrases);
                    if ($count < $maxcount) {
                        $hints['phrases'] = get_string('feedbackhintphrases', $plugin);
                    }
                    if ($currentresponse->breaks) {
                        $hints['breaks'] = get_string('feedbackhintbreaks', $plugin);
                    }
                    $output .= html_writer::start_tag('tr', array('class' => 'phrases'));
                    $output .= html_writer::tag('th', get_string('targetphrases', $plugin), array('class' => 'cell c0'));
                    $output .= html_writer::tag('td', $count.' / '.$maxcount, array('class' => 'cell c1'));
                    $output .= html_writer::end_tag('tr');
                    $i = 0;
                    foreach ($currentresponse->phrases as $phrase => $grade) {
                        if (in_array($phrase, $currentresponse->myphrases)) {
                            $status = 'present';
                            $img = $this->feedback_image(100.00);
                        } else {
                            $status = 'missing';
                            $img = $this->feedback_image(0.00);
                        }
                        $phrase = html_writer::alist(array($phrase), array('start' => (++$i)), 'ol');
                        $status = html_writer::tag('span', $img.get_string($status, $plugin), array('class' => $status));
                        $output .= html_writer::start_tag('tr', array('class' => 'phrase'));
                        $output .= html_writer::tag('td', $phrase, array('class' => 'cell c0'));
                        $output .= html_writer::tag('td', $status, array('class' => 'cell c1'));
                        $output .= html_writer::end_tag('tr');
                    }
                }

                // Errors
                if ($maxcount = count($currentresponse->errors)) {
                    $hints['errors'] = get_string('feedbackhinterrors', $plugin);
                    $output .= html_writer::start_tag('tr', array('class' => 'errors'));
                    $output .= html_writer::tag('th', get_string('commonerrors', $plugin), array('class' => 'cell c0'));
                    $output .= html_writer::tag('td', $maxcount, array('class' => 'cell c1'));
                    $output .= html_writer::end_tag('tr');
                    $i = 0;
                    foreach ($currentresponse->errors as $error => $link) {
                        $status = $this->feedback_image(0.00).get_string('commonerror', $plugin);
                        $status = html_writer::tag('span', $status, array('class' => 'error'));
                        if ($maxcount == 1) {
                            $error = $link;
                        } else if ($maxcount < 10) {
                            $error = html_writer::alist(array($link));
                        } else {
                            $error = html_writer::alist(array($link), array('start' => (++$i)), 'ol');
                        }
                        $output .= html_writer::start_tag('tr', array('class' => 'commonerror'));
                        $output .= html_writer::tag('td', $error, array('class' => 'cell c0'));
                        $output .= html_writer::tag('td', $status, array('class' => 'cell c1'));
                        $output .= html_writer::end_tag('tr');
                    }
                }

                // Hints
                if (count($hints)) {
                    $name = 'rewriteresubmit';
                    $hint = array();
                    foreach (array_keys($hints) as $type) {
                        $hint[] = get_string($name.$type, $plugin);
                    }
                    if ($hint = implode(get_string($name.'join', $plugin), $hint)) {
                        $hints[$name] = ucfirst($hint).get_string($name, $plugin);
                    }
                    $output .= html_writer::start_tag('tr');
                    $output .= html_writer::tag('th', get_string('feedbackhints', $plugin), array('class' => 'cell c0'));
                    $output .= html_writer::tag('td', html_writer::alist($hints), array('class' => 'cell c1'));
                    $output .= html_writer::end_tag('tr');
                }

                $output .= html_writer::end_tag('table');
            }

            if ($aifeedback) {
                $output .= html_writer::tag('h5', 'AI Feedback');
                $output .= html_writer::tag('p', $aifeedback);
                if ($aigrademax) {
                    $output .= html_writer::tag('p', html_writer::tag('b', 'AI Grade:')." {$aigrade} / {$aigrademax}");
                }
            }
        }

        if ($feedback = $this->combined_feedback($qa)) {
            $output .= html_writer::tag('h5', get_string('generalfeedback', 'question'));
            $output .= html_writer::tag('p', $feedback);
        }

        return $output;
    }

    /**
     * Find the best context for detecting capabilities
     * for a teacher "mod_quiz:/grade" or student "mod_quiz:attempt"
     *
     * @param array $options of display options for this question
     * @param object $question
     * @return object of the best context for checking capabilities
     */
    protected function get_best_context($options, $question) {
        global $PAGE;

        // These are the context levels that we are interested in.
        $levels = [CONTEXT_COURSE, CONTEXT_MODULE];

        if ($options && $options->context) {
            if (in_array($options->context->contextlevel, $levels)) {
                return $options->context;
            }
        }

        if ($PAGE && $PAGE->context) {
            if (in_array($PAGE->context->contextlevel, $levels)) {
                return $PAGE->context;
            }
        }

        if ($question && $question->contextid) {
            $context = context::instance_by_id($question->contextid);
            if (in_array($context->contextlevel, $levels)) {
                return $context;
            }
        }

        // Otherwise we have another kind of context, i.e.
        // CONTEXT_USER, CONTEXT_COURSECAT, CONTEXT_SYSTEM.
        if ($options && $options->context) {
            return $options->context;
        }
        if ($PAGE && $PAGE->context) {
            return $PAGE->context;
        }
        return null; // Shouldn't happen !!
    }

    protected function get_calculation_detail($name, $plugin, $a, $prefix='+ ') {
        static $addprefix = false;
        if ($addprefix==false) {
            $addprefix = true;
            $prefix = '';
        }
        return $prefix.'('.get_string($name, $plugin, $a).')';
    }

    /**
     * Generate an automatic description of the correct response to this question.
     *
     * This method is called when either of the following conditions are met:
     * (1) "Show right answer" is checked in Quiz settings during live quiz
     * (2) "Right answer" is set to "Shown" when previewing a questions
     *
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    public function correct_response(question_attempt $qa) {
        global $DB;

        $output = '';
        $plugin = 'qtype_essayautograde';
        $question = $qa->get_question();

        $show = false;
        if (empty($question->showfeedback)) {
            if ($step = $qa->get_last_step()) {
                $show = preg_match('/(partial|wrong)$/', $step->get_state());
            }
        }

        if ($show) {

            // cache plugin constants
            $ANSWER_TYPE_BAND = $this->plugin_constant('ANSWER_TYPE_BAND');
            $ANSWER_TYPE_PHRASE = $this->plugin_constant('ANSWER_TYPE_PHRASE');

            $bands = array();
            $phrases = array();

            // we only want the grade band for the highest percent (usually 100%)
            $percent = 0;

            $answers = $question->get_answers();
            foreach ($answers as $answer) {

                switch ($answer->type) {

                    case $ANSWER_TYPE_BAND:
                        if ($percent <= $answer->answerformat) {
                            $percent = $answer->answerformat;
                            $a = (object)[
                                'count' => $answer->answer,
                                'percent' => $answer->answerformat.'%'
                            ];
                            $bands = array(get_string('bandtext', $plugin, $a));
                        }
                        break;

                    case $ANSWER_TYPE_PHRASE:
                        $a = (object)[
                            'phrase' => $answer->feedback,
                            'percent' => round($answer->realpercent, 2).'%',
                        ];
                        $phrases[] = get_string('phrasetext', $plugin, $a);
                        break;
                }
            }

            if (count($bands)) {
                $output .= html_writer::alist($bands, array('class' => 'gradebands'));
            }
            if (count($phrases)) {
                $output .= html_writer::alist($phrases, array('class' => 'targetphrases'));
            }

            if ($question->errorcmid && ($cm = get_coursemodule_from_id('', $question->errorcmid))) {
                $url = new moodle_url("/mod/{$cm->modname}/view.php?id={$cm->id}");
                $a = (object)array(
                    'href' => $url->out(),
                    'name' => strip_tags(format_text($cm->name)),
                );
                $msg = array(get_string('excludecommonerrors', $plugin, $a));
                $output .= html_writer::alist($msg, array('class' => 'commonerrors'));
            }

            if ($output) {
                $name = 'correctresponse';
                // "corrresp", "quiz" is available in Moodle >= 2.0
                // "rightanswer", "question" is available in Moodle >= 2.1
                $output = html_writer::tag('h5', get_string('feedbackhints', $plugin)).
                          html_writer::tag('p', get_string($name, $plugin), array('class' => $name)).
                          $output;
            }
        }

        return $output;
    }

    ///////////////////////////////////////////////////////
    // non-standard methods (used only in this class)
    ///////////////////////////////////////////////////////

    /**
     * qtype is plugin name without leading "qtype_"
     */
    protected function qtype() {
        return substr($this->plugin_name(), 6);
        // = $qa->get_question()->qtype->name();
    }

    /**
     * Plugin name is class name without trailing "_renderer"
     */
    protected function plugin_name() {
        return substr(get_class($this), 0, -9);
        // = $qa->get_question()->qtype->plugin_name();
    }

    /**
     * Fetch a constant from the plugin class in "questiontype.php".
     */
    protected function plugin_constant($name) {
        $plugin = $this->plugin_name();
        return constant($plugin.'::'.$name);
    }
}

/**
 * An essayautograde format renderer for essayautogrades where the student should not enter
 * any inline response.
 *
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2013 Binghamton University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayautograde_format_noinline_renderer extends qtype_essay_format_noinline_renderer {
    protected function class_name() {
        return 'qtype_essayautograde_noinline';
    }
}

/**
 * An essayautograde format renderer for essayautogrades where the student should use the HTML
 * editor without the file picker.
 *
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayautograde_format_editor_renderer extends qtype_essay_format_editor_renderer {
    protected function class_name() {
        return 'qtype_essayautograde_editor';
    }
}

/**
 * An essayautograde format renderer for essayautogrades where the student should use the HTML
 * editor with the file picker.
 *
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayautograde_format_editorfilepicker_renderer extends qtype_essay_format_editorfilepicker_renderer {
    protected function class_name() {
        return 'qtype_essayautograde_editorfilepicker';
    }
}

/**
 * An essayautograde format renderer for essayautogrades where the student should use a plain
 * input box, but with a normal, proportional font.
 *
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayautograde_format_plain_renderer extends qtype_essay_format_plain_renderer {
    protected function class_name() {
        return 'qtype_essayautograde_plain';
    }
}

/**
 * An essayautograde format renderer for essayautogrades where the student should use a plain
 * input box with a monospaced font. You might use this, for example, for a
 * question where the students should type computer code.
 *
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essayautograde_format_monospaced_renderer extends qtype_essay_format_plain_renderer {
    protected function class_name() {
        return 'qtype_essayautograde_monospaced';
    }
}
