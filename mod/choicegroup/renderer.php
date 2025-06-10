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
 * Version information
 *
 * @package    mod
 * @subpackage choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define ('CHOICEGROUP_DISPLAY_HORIZONTAL_LAYOUT', 0);
define ('CHOICEGROUP_DISPLAY_VERTICAL_LAYOUT', 1);

class mod_choicegroup_renderer extends plugin_renderer_base {

    /**
     * @param      $options
     * @param      $coursemoduleid
     * @param bool $vertical
     * @param bool $publish
     * @param bool $limitanswers
     * @param bool $showresults
     * @param bool $current
     * @param bool $choicegroupopen
     * @param bool $disabled
     * @param bool $multipleenrollmentspossible
     * @param bool $onlyactive
     *
     * @return string
     */
    public function display_options($options, $coursemoduleid, $vertical = true, $publish = false, $limitanswers = false, $showresults = false, $current = false, $choicegroupopen = false, $disabled = false, $multipleenrollmentspossible = false, $onlyactive = false) {
        global $DB, $PAGE, $choicegroup_groups;

        $target = new moodle_url('/mod/choicegroup/view.php');
        $attributes = array('method'=>'POST', 'action'=>$target, 'class'=> 'tableform');

        $html = html_writer::start_tag('form', $attributes);
        $html .= html_writer::start_tag('div', array('class'=>'tablecontainer'));
        $html .= html_writer::start_tag('table', array('class'=>'choicegroups' ));

        $html .= html_writer::start_tag('tr');
        $html .= html_writer::tag('th', get_string('choice', 'choicegroup'), array('class'=>'width10'));

        $group = get_string('group').' ';
        $group .= html_writer::tag('a', get_string('showdescription', 'choicegroup'), array('role' => 'button','class' => 'choicegroup-descriptiondisplay choicegroup-descriptionshow btn btn-secondary ml-1', 'href' => '#'));
        $html .= html_writer::tag('th', $group, array('class'=>'width40'));

        if ( $showresults == CHOICEGROUP_SHOWRESULTS_ALWAYS or
        ($showresults == CHOICEGROUP_SHOWRESULTS_AFTER_ANSWER and $current) or
        ($showresults == CHOICEGROUP_SHOWRESULTS_AFTER_CLOSE and !$choicegroupopen)) {
            if ($limitanswers) {
                $html .= html_writer::tag('th', get_string('members/max', 'choicegroup'), array('class'=>'width10'));
            }
            else {
                $html .= html_writer::tag('th', get_string('members/', 'choicegroup'), array('class'=>'width10'));
            }
            if ($publish == CHOICEGROUP_PUBLISH_NAMES) {
                $membersdisplay_html = html_writer::tag('a', get_string('showgroupmembers','mod_choicegroup'), array('role' => 'button','class' => 'choicegroup-memberdisplay choicegroup-membershow btn btn-secondary ml-1', 'href' => '#'));
                $html .= html_writer::tag('th', get_string('groupmembers', 'choicegroup') .' '. $membersdisplay_html, array('class'=>'width40'));
            }
        }
        $html .= html_writer::end_tag('tr');

        $availableoption = count($options['options']);
        if ($multipleenrollmentspossible == 1) {
            $i=0;
            $answer_to_groupid_mappings = '';
        }
        $initiallyHideSubmitButton = false;
        foreach ($options['options'] as $option) {
            $group = (isset($choicegroup_groups[$option->groupid])) ? ($choicegroup_groups[$option->groupid]) : (false);
            if (!$group) {
                $colspan = 2;
                if ( $showresults == CHOICEGROUP_SHOWRESULTS_ALWAYS or ($showresults == CHOICEGROUP_SHOWRESULTS_AFTER_ANSWER and $current) or ($showresults == CHOICEGROUP_SHOWRESULTS_AFTER_CLOSE and !$choicegroupopen)) {
                    $colspan++;
                    if ($publish == CHOICEGROUP_PUBLISH_NAMES) {
                        $colspan++;
                    }
                }
                $cell = html_writer::tag('td', get_string('groupdoesntexist', 'choicegroup'), array('colspan' => $colspan));
                $html .= html_writer::tag('tr', $cell);
                break;
            }
            $html .= html_writer::start_tag('tr', array('class'=>'option'));
            $html .= html_writer::start_tag('td', array('class'=>'center'));

            if ($multipleenrollmentspossible == 1) {
                $option->attributes->name = 'answer_'.$i;
                $option->attributes->type = 'checkbox';
                $answer_to_groupid_mappings .= '<input type="hidden" name="answer_'.$i.'_groupid" value="'.$option->groupid.'">';
                $i++;
            } else {
                $option->attributes->name = 'answer';
                $option->attributes->type = 'radio';
                if (property_exists($option, 'attributes') && property_exists($option->attributes, 'checked') && $option->attributes->checked == true) {
                    $initiallyHideSubmitButton = true;
                }
            }

            $context = \context_course::instance($group->courseid);
            $labeltext = html_writer::tag('label', format_string($group->name), array('for' => 'choiceid_' . $option->attributes->value));
            $group_members = get_enrolled_users($context, '', $group->id, 'u.*', 'u.lastname, u.firstname', 0, 0, $onlyactive);
            $group_members_names = array();
            foreach ($group_members as $group_member) {
                $group_members_names[] = fullname($group_member);
            }
            if (!empty($option->attributes->disabled) || ($limitanswers && sizeof($group_members) >= $option->maxanswers) && empty($option->attributes->checked)) {
                $labeltext .= ' ' . html_writer::tag('em', get_string('full', 'choicegroup'));
                $option->attributes->disabled=true;
                $availableoption--;
            }
            $labeltext .= html_writer::tag('div', format_text(file_rewrite_pluginfile_urls($group->description,
            'pluginfile.php',
                $context->id,
                'group',
                'description',
                $group->id)),
                array('class' => 'choicegroups-descriptions hidden'));
            if ($disabled) {
                $option->attributes->disabled=true;
            }
            $attributes = (array) $option->attributes;
            $attributes['id'] = 'choiceid_' . $option->attributes->value;
            $html .= html_writer::empty_tag('input', $attributes);
            $html .= html_writer::end_tag('td');
            $html .= html_writer::tag('td', $labeltext);


            if ( $showresults == CHOICEGROUP_SHOWRESULTS_ALWAYS or
            ($showresults == CHOICEGROUP_SHOWRESULTS_AFTER_ANSWER and $current) or
            ($showresults == CHOICEGROUP_SHOWRESULTS_AFTER_CLOSE and !$choicegroupopen)) {

                $maxanswers = ($limitanswers) ? (' / '.$option->maxanswers) : ('');
                $html .= html_writer::tag('td', sizeof($group_members_names).$maxanswers, array('class' => 'center'));
                if ($publish == CHOICEGROUP_PUBLISH_NAMES) {
                    $group_members_html = html_writer::tag('div', implode('<br />', $group_members_names), array('class' => 'choicegroups-membersnames hidden', 'id' => 'choicegroup_'.$option->attributes->value));
                    $html .= html_writer::tag('td', $group_members_html, array('class' => 'center'));
                }
            }
            $html .= html_writer::end_tag('tr');
        }
        $html .= html_writer::end_tag('table');
        $html .= html_writer::end_tag('div');
        if ($multipleenrollmentspossible == 1) {
            $html .= '<input type="hidden" name="number_of_groups" value="'.$i.'">' . $answer_to_groupid_mappings;
        }
        $html .= html_writer::tag('div', '', array('class'=>'clearfloat'));
        $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));
        $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'id', 'value'=>$coursemoduleid));

        if (!empty($options['hascapability']) && ($options['hascapability'])) {
            if ($availableoption < 1) {
               $html .= html_writer::tag('p', get_string('choicegroupfull', 'choicegroup'));
            } else {
                if (!$disabled) {
                    $html .= html_writer::empty_tag('input', array(
                        'type'=>'submit',
                        'value'=>get_string('savemychoicegroup','choicegroup'),
                        'class'=>'btn btn-primary',
                        'style' => $initiallyHideSubmitButton?'display: none':''
                    ));
                }
            }

            if (!empty($options['allowupdate']) && ($options['allowupdate']) && !($multipleenrollmentspossible == 1) && !$disabled) {
                $url = new moodle_url('view.php', array('id'=>$coursemoduleid, 'action'=>'delchoicegroup', 'sesskey'=>sesskey()));
                $html .= ' ' . html_writer::link($url, get_string('removemychoicegroup','choicegroup'));
            }
        } elseif (!isloggedin() || isguestuser()) { // Only display message if user is not logged in or is a guest user.
            $html .= ' '.html_writer::tag('p', get_string('havetologin', 'choicegroup'));
        }

        $html .= html_writer::end_tag('form');

        return $html;
    }

    /**
     * Returns HTML to display choicegroups result
     * @param object $choicegroups
     * @param bool $forcepublish
     * @return string
     */
    public function display_result($choicegroups, $forcepublish = false) {
        if (empty($forcepublish)) { //allow the publish setting to be overridden
            $forcepublish = $choicegroups->publish;
        }

        $displaylayout = ($choicegroups) ? ($choicegroups->display) : (CHOICEGROUP_DISPLAY_HORIZONTAL);

        if ($forcepublish) {  //CHOICEGROUP_PUBLISH_NAMES
            return $this->display_publish_name_vertical($choicegroups);
        } else { //CHOICEGROUP_PUBLISH_ANONYMOUS';
            if ($displaylayout == CHOICEGROUP_DISPLAY_HORIZONTAL_LAYOUT) {
                return $this->display_publish_anonymous_horizontal($choicegroups);
            }
            return $this->display_publish_anonymous_vertical($choicegroups);
        }
    }

    /**
     * Returns HTML to display choicegroups result
     * @param object $choicegroups
     * @param bool $forcepublish
     * @return string
     */
    public function display_publish_name_vertical($choicegroups) {
        global $PAGE;
        global $DB;
        global $context;

        if (!has_capability('mod/choicegroup:downloadresponses', $context)) {
            return; // only the (editing)teacher can see the diagram
        }
        if (!$choicegroups) {
            return; // no answers yet, so don't bother
        }

        $html ='';
        $html .= html_writer::tag('h3',format_string(get_string("responses", "choicegroup")));

        $attributes = array('method'=>'POST');
        $attributes['action'] = new moodle_url($PAGE->url);
        $attributes['id'] = 'attemptsform';
        $attributes['class'] = 'tableform';

        if ($choicegroups->viewresponsecapability) {
            $html .= html_writer::start_tag('form', $attributes);
            $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'id', 'value'=> $choicegroups->coursemoduleid));
            $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=> sesskey()));
            $html .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'mode', 'value'=>'overview'));
        }

        $table = new html_table();
        $table->cellpadding = 0;
        $table->cellspacing = 0;
        $table->attributes['class'] = 'results names ';
        $table->tablealign = 'center';
        $table->data = array();

        $count = 0;
        ksort($choicegroups->options);

        $columns = array();
        foreach ($choicegroups->options as $optionid => $options) {
            $coldata = '';
            if ($choicegroups->showunanswered && $optionid == 0) {
                $coldata .= html_writer::tag('div', format_string(get_string('notanswered', 'choicegroup')), array('class'=>'option'));
            } else if ($optionid > 0) {
                $coldata .= html_writer::tag('div', format_string(choicegroup_get_option_text($choicegroups, $choicegroups->options[$optionid]->groupid)), array('class'=>'option'));
            }
            $numberofuser = 0;
            if (!empty($options->user) && count($options->user) > 0) {
                $numberofuser = count($options->user);
            }

            $coldata .= html_writer::tag('div', ' ('.$numberofuser. ')', array('class'=>'numberofuser', 'title' => get_string('numberofuser', 'choicegroup')));
            $columns[] = $coldata;
        }

        $table->head = $columns;

        $coldata = '';
        $columns = array();
        foreach ($choicegroups->options as $optionid => $options) {
            $coldata = '';
            if ($choicegroups->showunanswered || $optionid > 0) {
                if (!empty($options->user)) {
                    foreach ($options->user as $user) {
                        $data = '';
                        if (empty($user->imagealt)){
                            $user->imagealt = '';
                        }

                        if ($choicegroups->viewresponsecapability && $choicegroups->deleterepsonsecapability  && $optionid > 0) {
                            $attemptaction = html_writer::checkbox('grpsmemberid[]', $user->grpsmemberid,'');
                            $data .= html_writer::tag('div', $attemptaction, array('class'=>'attemptaction'));
                        }
                        $userimage = $this->output->user_picture($user, array('courseid'=>$choicegroups->courseid));
                        $data .= html_writer::tag('div', $userimage, array('class'=>'image'));

                        $userlink = new moodle_url('/user/view.php', array('id'=>$user->id,'course'=>$choicegroups->courseid));
                        $name = html_writer::tag('a', fullname($user, $choicegroups->fullnamecapability), array('href'=>$userlink, 'class'=>'username'));
                        $data .= html_writer::tag('div', $name, array('class'=>'fullname'));
                        $data .= html_writer::tag('div','', array('class'=>'clearfloat'));
                        $coldata .= html_writer::tag('div', $data, array('class'=>'user'));
                    }
                }
            }

            $columns[] = $coldata;
            $count++;
        }

        $table->data[] = $columns;
        foreach ($columns as $d) {
            $table->colclasses[] = 'data';
        }
        $html .= html_writer::tag('div', html_writer::table($table), array('class'=>'response tablecontainer'));

        $actiondata = '';
        if ($choicegroups->viewresponsecapability && $choicegroups->deleterepsonsecapability) {
            $selecturl = new moodle_url('#');
            $actiondata .= html_writer::start_div('selectallnone');
            $actiondata .= html_writer::link($selecturl, get_string('selectall'), ['data-select-info' => true]) . ' / ';

            $actiondata .= html_writer::link($selecturl, get_string('deselectall'), ['data-select-info' => false]);
            $actiondata .= html_writer::end_div();
            $actiondata .= html_writer::tag('label', ' ' . get_string('withselected', 'choice') . ' ', array('for'=>'menuaction', 'class' => 'mr-1'));

            $actionurl = new moodle_url($PAGE->url, array('sesskey'=>sesskey(), 'action'=>'delete_confirmation()'));
            $select = new single_select($actionurl, 'action', array('delete'=>get_string('delete')), null, array(''=>get_string('chooseaction', 'choicegroup')), 'attemptsform');

            $PAGE->requires->js_call_amd('mod_choicegroup/select_all_choices', 'init');
            $actiondata .= $this->output->render($select);
        }
        $html .= html_writer::tag('div', $actiondata, array('class'=>'responseaction'));

        if ($choicegroups->viewresponsecapability) {
            $html .= html_writer::end_tag('form');
        }

        return $html;
    }


    /**
     * Returns HTML to display choicegroups result
     * @param object $choicegroups
     * @return string
     */
    public function display_publish_anonymous_horizontal($choicegroups) {
        global $context, $DB, $CHOICEGROUP_COLUMN_WIDTH;

        if (!has_capability('mod/choicegroup:downloadresponses', $context)) {
            return; // only the (editing)teacher can see the diagram
        }

        $table = new html_table();
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->attributes['class'] = 'results anonymous ';
        $table->data = array();

        $count = 0;
        ksort($choicegroups->options);

        $rows = array();
        foreach ($choicegroups->options as $optionid => $options) {
            $numberofuser = 0;
            $graphcell = new html_table_cell();
            if (!empty($options->user)) {
               $numberofuser = count($options->user);
            }

            $width = 0;
            $percentageamount = 0;
            $columndata = '';
            if($choicegroups->numberofuser > 0) {
               $width = ($CHOICEGROUP_COLUMN_WIDTH * ((float)$numberofuser / (float)$choicegroups->numberofuser));
               $percentageamount = ((float)$numberofuser/(float)$choicegroups->numberofuser)*100.0;
            }
            $displaydiagram = html_writer::tag('img','', array('style'=>'height:50px; width:'.$width.'px', 'alt'=>'', 'src'=>$this->output->pix_url('row', 'choicegroup')));

            $skiplink = html_writer::tag('a', get_string('skipresultgraph', 'choicegroup'), array('href'=>'#skipresultgraph'. $optionid, 'class'=>'skip-block'));
            $skiphandler = html_writer::tag('span', '', array('class'=>'skip-block-to', 'id'=>'skipresultgraph'.$optionid));

            $graphcell->text = $skiplink . $displaydiagram . $skiphandler;
            $graphcell->attributes = array('class'=>'graph horizontal');

            $datacell = new html_table_cell();
            if ($choicegroups->showunanswered && $optionid == 0) {
                $columndata .= html_writer::tag('div', format_string(get_string('notanswered', 'choicegroup')), array('class'=>'option'));
            } else if ($optionid > 0) {
                $columndata .= html_writer::tag('div', format_string(choicegroup_get_option_text($choicegroups, $choicegroups->options[$optionid]->groupid)), array('class'=>'option'));
            }
            $columndata .= html_writer::tag('div', ' ('.$numberofuser.')', array('title'=> get_string('numberofuser', 'choicegroup'), 'class'=>'numberofuser'));

            if($choicegroups->numberofuser > 0) {
               $percentageamount = ((float)$numberofuser/(float)$choicegroups->numberofuser)*100.0;
            }
            $columndata .= html_writer::tag('div', format_float($percentageamount,1). '%', array('class'=>'percentage'));

            $datacell->text = $columndata;
            $datacell->attributes = array('class'=>'header');

            $row = new html_table_row();
            $row->cells = array($datacell, $graphcell);
            $rows[] = $row;
        }

        $table->data = $rows;

        $html = '';
        $header = html_writer::tag('h3',format_string(get_string("responses", "choicegroup")));
        $html .= html_writer::tag('div', $header, array('class'=>'responseheader'));
        $html .= html_writer::table($table);

        return $html;
    }

}
