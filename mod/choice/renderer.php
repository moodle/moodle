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
 * Moodle renderer used to display special elements of the lesson module
 *
 * @package   mod_choice
 * @copyright 2010 Rossiani Wijaya
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class mod_choice_renderer extends plugin_renderer_base {

    /**
     * Returns HTML to display choices of option
     * @param object $options
     * @param int  $coursemoduleid
     * @param bool $vertical
     * @return string
     */
    public function display_options($options, $coursemoduleid, $vertical = false, $multiple = false) {
        $layoutclass = 'horizontal';
        if ($vertical) {
            $layoutclass = 'vertical';
        }
        $target = new moodle_url('/mod/choice/view.php');
        $attributes = array('method'=>'POST', 'action'=>$target, 'class'=> $layoutclass);
        $disabled = empty($options['previewonly']) ? array() : array('disabled' => 'disabled');

        $html = html_writer::start_tag('form', $attributes);
        $html .= html_writer::start_tag('ul', array('class' => 'choices list-unstyled unstyled'));

        $availableoption = count($options['options']);
        $choicecount = 0;
        foreach ($options['options'] as $option) {
            $choicecount++;
            $html .= html_writer::start_tag('li', array('class' => 'option mr-3'));
            if ($multiple) {
                $option->attributes->name = 'answer[]';
                $option->attributes->type = 'checkbox';
            } else {
                $option->attributes->name = 'answer';
                $option->attributes->type = 'radio';
            }
            $option->attributes->id = 'choice_'.$choicecount;
            $option->attributes->class = 'mx-1';

            $labeltext = $option->text;
            if (!empty($option->attributes->disabled)) {
                $labeltext .= ' ' . get_string('full', 'choice');
                $availableoption--;
            }

            if (!empty($options['limitanswers']) && !empty($options['showavailable'])) {
                $labeltext .= html_writer::empty_tag('br');
                $labeltext .= get_string("responsesa", "choice", $option->countanswers);
                $labeltext .= html_writer::empty_tag('br');
                $labeltext .= get_string("limita", "choice", $option->maxanswers);
            }

            $html .= html_writer::empty_tag('input', (array)$option->attributes + $disabled);
            $html .= html_writer::tag('label', $labeltext, array('for'=>$option->attributes->id));
            $html .= html_writer::end_tag('li');
        }
        $html .= html_writer::tag('li','', array('class'=>'clearfloat'));
        $html .= html_writer::end_tag('ul');
        $html .= html_writer::tag('div', '', array('class'=>'clearfloat'));
        $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));
        $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'action', 'value'=>'makechoice'));
        $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'id', 'value'=>$coursemoduleid));

        if (empty($options['previewonly'])) {
            if (!empty($options['hascapability']) && ($options['hascapability'])) {
                if ($availableoption < 1) {
                    $html .= html_writer::tag('label', get_string('choicefull', 'choice'));
                } else {
                    $html .= html_writer::empty_tag('input', array(
                        'type' => 'submit',
                        'value' => get_string('savemychoice', 'choice'),
                        'class' => 'btn btn-primary'
                    ));
                }

                if (!empty($options['allowupdate']) && ($options['allowupdate'])) {
                    $url = new moodle_url('view.php',
                            array('id' => $coursemoduleid, 'action' => 'delchoice', 'sesskey' => sesskey()));
                    $html .= html_writer::link($url, get_string('removemychoice', 'choice'), array('class' => 'ml-1'));
                }
            } else {
                $html .= html_writer::tag('label', get_string('havetologin', 'choice'));
            }
        }

        $html .= html_writer::end_tag('ul');
        $html .= html_writer::end_tag('form');

        return $html;
    }

    /**
     * Returns HTML to display choices result
     * @param object $choices
     * @param bool $forcepublish
     * @return string
     */
    public function display_result($choices, $forcepublish = false) {
        if (empty($forcepublish)) { //allow the publish setting to be overridden
            $forcepublish = $choices->publish;
        }

        $displaylayout = $choices->display;

        if ($forcepublish) {  //CHOICE_PUBLISH_NAMES
            return $this->display_publish_name_vertical($choices);
        } else {
            return $this->display_publish_anonymous($choices, $displaylayout);
        }
    }

    /**
     * Returns HTML to display choices result
     * @param object $choices
     * @return string
     */
    public function display_publish_name_vertical($choices) {
        $html ='';

        $attributes = array('method'=>'POST');
        $attributes['action'] = new moodle_url($this->page->url);
        $attributes['id'] = 'attemptsform';

        if ($choices->viewresponsecapability) {
            $html .= html_writer::start_tag('form', $attributes);
            $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'id', 'value'=> $choices->coursemoduleid));
            $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=> sesskey()));
            $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'mode', 'value'=>'overview'));
        }

        $table = new html_table();
        $table->cellpadding = 0;
        $table->cellspacing = 0;
        $table->attributes['class'] = 'results names table table-bordered';
        $table->tablealign = 'center';
        $table->summary = get_string('responsesto', 'choice', format_string($choices->name));
        $table->data = array();

        $count = 0;
        ksort($choices->options);

        $columns = array();
        $celldefault = new html_table_cell();
        $celldefault->attributes['class'] = 'data';

        // This extra cell is needed in order to support accessibility for screenreader. MDL-30816
        $accessiblecell = new html_table_cell();
        $accessiblecell->scope = 'row';
        $accessiblecell->text = get_string('choiceoptions', 'choice');
        $columns['options'][] = $accessiblecell;

        $usernumberheader = clone($celldefault);
        $usernumberheader->header = true;
        $usernumberheader->attributes['class'] = 'header data';
        $usernumberheader->text = get_string('numberofuser', 'choice');
        $columns['usernumber'][] = $usernumberheader;

        $optionsnames = [];
        foreach ($choices->options as $optionid => $options) {
            $celloption = clone($celldefault);
            $cellusernumber = clone($celldefault);

            if ($choices->showunanswered && $optionid == 0) {
                $headertitle = get_string('notanswered', 'choice');
            } else if ($optionid > 0) {
                $headertitle = format_string($choices->options[$optionid]->text);
                if (!empty($choices->options[$optionid]->user) && count($choices->options[$optionid]->user) > 0) {
                    if (
                        $choices->limitanswers &&
                        (count($choices->options[$optionid]->user) == $choices->options[$optionid]->maxanswer)
                    ) {
                        $headertitle .= ' ' . get_string('full', 'choice');
                    }
                }
            }
            $celltext = $headertitle;

            // Render select/deselect all checkbox for this option.
            if ($choices->viewresponsecapability && $choices->deleterepsonsecapability) {

                // Build the select/deselect all for this option.
                $selectallid = 'select-response-option-' . $optionid;
                $togglegroup = 'responses response-option-' . $optionid;
                $selectalltext = get_string('selectalloption', 'choice', $headertitle);
                $deselectalltext = get_string('deselectalloption', 'choice', $headertitle);
                $mastercheckbox = new \core\output\checkbox_toggleall($togglegroup, true, [
                    'id' => $selectallid,
                    'name' => $selectallid,
                    'value' => 1,
                    'selectall' => $selectalltext,
                    'deselectall' => $deselectalltext,
                    'label' => $selectalltext,
                    'labelclasses' => 'accesshide',
                ]);

                $celltext .= html_writer::div($this->output->render($mastercheckbox));
            }
            $numberofuser = 0;
            if (!empty($options->user) && count($options->user) > 0) {
                $numberofuser = count($options->user);
            }
            if (($choices->limitanswers) && ($choices->showavailable)) {
                $numberofuser .= html_writer::empty_tag('br');
                $numberofuser .= get_string("limita", "choice", $options->maxanswer);
            }
            $celloption->text = html_writer::div($celltext, 'text-center');
            $optionsnames[$optionid] = $celltext;
            $cellusernumber->text = html_writer::div($numberofuser, 'text-center');

            $columns['options'][] = $celloption;
            $columns['usernumber'][] = $cellusernumber;
        }

        $table->head = $columns['options'];
        $table->data[] = new html_table_row($columns['usernumber']);

        $columns = array();

        // This extra cell is needed in order to support accessibility for screenreader. MDL-30816
        $accessiblecell = new html_table_cell();
        $accessiblecell->text = get_string('userchoosethisoption', 'choice');
        $accessiblecell->header = true;
        $accessiblecell->scope = 'row';
        $accessiblecell->attributes['class'] = 'header data';
        $columns[] = $accessiblecell;

        foreach ($choices->options as $optionid => $options) {
            $cell = new html_table_cell();
            $cell->attributes['class'] = 'data';

            if ($choices->showunanswered || $optionid > 0) {
                if (!empty($options->user)) {
                    $optionusers = '';
                    foreach ($options->user as $user) {
                        $data = '';
                        if (empty($user->imagealt)) {
                            $user->imagealt = '';
                        }

                        $userfullname = fullname($user, $choices->fullnamecapability);
                        $checkbox = '';
                        if ($choices->viewresponsecapability && $choices->deleterepsonsecapability) {
                            $checkboxid = 'attempt-user' . $user->id . '-option' . $optionid;
                            if ($optionid > 0) {
                                $checkboxname = 'attemptid[]';
                                $checkboxvalue = $user->answerid;
                            } else {
                                $checkboxname = 'userid[]';
                                $checkboxvalue = $user->id;
                            }

                            $togglegroup = 'responses response-option-' . $optionid;
                            $slavecheckbox = new \core\output\checkbox_toggleall($togglegroup, false, [
                                'id' => $checkboxid,
                                'name' => $checkboxname,
                                'classes' => 'mr-1',
                                'value' => $checkboxvalue,
                                'label' => $userfullname . ' ' . $options->text,
                                'labelclasses' => 'accesshide',
                            ]);
                            $checkbox = $this->output->render($slavecheckbox);
                        }
                        $userimage = $this->output->user_picture($user, array('courseid' => $choices->courseid, 'link' => false));
                        $profileurl = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $choices->courseid));
                        $profilelink = html_writer::link($profileurl, $userimage . $userfullname);
                        $data .= html_writer::div($checkbox . $profilelink, 'mb-1');

                        $optionusers .= $data;
                    }
                    $cell->text = $optionusers;
                }
            }
            $columns[] = $cell;
            $count++;
        }
        $row = new html_table_row($columns);
        $table->data[] = $row;

        $html .= html_writer::tag('div', html_writer::table($table), array('class'=>'response'));

        $actiondata = '';
        if ($choices->viewresponsecapability && $choices->deleterepsonsecapability) {
            // Build the select/deselect all for all of options.
            $selectallid = 'select-all-responses';
            $togglegroup = 'responses';
            $selectallcheckbox = new \core\output\checkbox_toggleall($togglegroup, true, [
                'id' => $selectallid,
                'name' => $selectallid,
                'value' => 1,
                'label' => get_string('selectall'),
                'classes' => 'btn-secondary mr-1'
            ], true);
            $actiondata .= $this->output->render($selectallcheckbox);

            $actionurl = new moodle_url($this->page->url,
                    ['sesskey' => sesskey(), 'action' => 'delete_confirmation()']);
            $actionoptions = array('delete' => get_string('delete'));
            foreach ($choices->options as $optionid => $option) {
                if ($optionid > 0) {
                    $actionoptions['choose_'.$optionid] = get_string('chooseoption', 'choice', $option->text);
                }
            }
            $selectattributes = [
                'data-action' => 'toggle',
                'data-togglegroup' => 'responses',
                'data-toggle' => 'action',
            ];
            $selectnothing = ['' => get_string('chooseaction', 'choice')];
            $select = new single_select($actionurl, 'action', $actionoptions, null, $selectnothing, 'attemptsform');
            $select->set_label(get_string('withselected', 'choice'));
            $select->disabled = true;
            $select->attributes = $selectattributes;

            $actiondata .= $this->output->render($select);
        }
        $html .= html_writer::tag('div', $actiondata, array('class'=>'responseaction'));

        if ($choices->viewresponsecapability) {
            $html .= html_writer::end_tag('form');
        }

        return $html;
    }


    /**
     * Returns HTML to display choices result
     * @deprecated since 3.2
     * @param object $choices
     * @return string
     */
    public function display_publish_anonymous_horizontal($choices) {
        debugging(__FUNCTION__.'() is deprecated. Please use mod_choice_renderer::display_publish_anonymous() instead.',
                DEBUG_DEVELOPER);
        return $this->display_publish_anonymous($choices, CHOICE_DISPLAY_VERTICAL);
    }

    /**
     * Returns HTML to display choices result
     * @deprecated since 3.2
     * @param object $choices
     * @return string
     */
    public function display_publish_anonymous_vertical($choices) {
        debugging(__FUNCTION__.'() is deprecated. Please use mod_choice_renderer::display_publish_anonymous() instead.',
                DEBUG_DEVELOPER);
        return $this->display_publish_anonymous($choices, CHOICE_DISPLAY_HORIZONTAL);
    }

    /**
     * Generate the choice result chart.
     *
     * Can be displayed either in the vertical or horizontal position.
     *
     * @param stdClass $choices Choices responses object.
     * @param int $displaylayout The constants CHOICE_DISPLAY_HORIZONTAL or CHOICE_DISPLAY_VERTICAL.
     * @return string the rendered chart.
     */
    public function display_publish_anonymous($choices, $displaylayout) {
        $count = 0;
        $data = [];
        $numberofuser = 0;
        $percentageamount = 0;
        foreach ($choices->options as $optionid => $option) {
            if (!empty($option->user)) {
                $numberofuser = count($option->user);
            }
            if($choices->numberofuser > 0) {
                $percentageamount = ((float)$numberofuser / (float)$choices->numberofuser) * 100.0;
            }
            $data['labels'][$count] = $option->text;
            $data['series'][$count] = $numberofuser;
            $data['series_labels'][$count] = $numberofuser . ' (' . format_float($percentageamount, 1) . '%)';
            $count++;
            $numberofuser = 0;
        }

        $chart = new \core\chart_bar();
        if ($displaylayout == CHOICE_DISPLAY_VERTICAL) {
            $chart->set_horizontal(true); // Horizontal bars when choices are vertical.
        }
        $series = new \core\chart_series(format_string(get_string("responses", "choice")), $data['series']);
        $series->set_labels($data['series_labels']);
        $chart->add_series($series);
        $chart->set_labels($data['labels']);
        $yaxis = $chart->get_yaxis(0, true);
        $yaxis->set_stepsize(max(1, round(max($data['series']) / 10)));
        return $this->output->render($chart);
    }
}

