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
 * All hotpot module renderers are defined here
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Hotpot module renderer class
 *
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_hotpot_renderer extends plugin_renderer_base {

    /////////////////////////////////////////////////////////////////////
    // functions to generate common html snippets                      //
    /////////////////////////////////////////////////////////////////////

    /**
     * form_start
     *
     * @param xxx $hotpotscriptname
     * @param xxx $params
     * @param xxx $attributes (optional, default=array)
     * @return xxx
     */
    function form_start($hotpotscriptname, $params, $attributes=array())  {
        $output = '';

        if (empty($attributes['method'])) {
            $attributes['method'] = 'post';
        }
        if (empty($attributes['action'])) {
            $url = new moodle_url('/mod/hotpot/'.$hotpotscriptname);
            $attributes['action'] = $url->out();
        }
        $output .= html_writer::start_tag('form', $attributes)."\n";

        $hiddenfields = '';
        foreach ($params as $name => $value) {
            $hiddenfields .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>$name, 'value'=>$value))."\n";
        }
        if ($hiddenfields) {
            // xhtml strict requires a container for the hidden input elements
            $output .= html_writer::start_tag('fieldset', array('style'=>'display:none'))."\n";
            $output .= $hiddenfields;
            $output .= html_writer::end_tag('fieldset')."\n";
        }

        // xhtml strict requires a container for the contents of the <form>
        $output .= html_writer::start_tag('div')."\n";

        return $output;
    }

    /**
     * form_end
     *
     * @return xxx
     */
    function form_end()  {
        $output = '';
        $output .= html_writer::end_tag('div')."\n";
        $output .= html_writer::end_tag('form')."\n";
        return $output;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Helper methods                                                         //
    ////////////////////////////////////////////////////////////////////////////

    /**
     * format_url
     *
     * @param xxx $url
     * @param xxx $id
     * @param xxx $params
     * @param xxx $more_params (optional, default=false)
     * @return xxx
     */
    function format_url($url, $id, $params, $more_params=false)  {
        global $CFG;

        // convert relative URL to absolute URL
        if (! preg_match('/^(https?:\/)?\//', $url)) {
            $url = $CFG->wwwroot.'/mod/hotpot/'.$url;
        }

        // merge parameters into a single array
        $all_params = array_merge($params, $more_params);

        // rename the $id parameter, if necesary
        if ($id && isset($all_params[$id])) {
            $all_params['id'] = $all_params[$id];
            unset($all_params[$id]);
        }

        $join = '?';
        foreach ($all_params as $name=>$value) {
            if ($value) {
                $url .= $join.$name.'='.$value;
                $join = '&amp;';
            }
        }
        return $url;
    }

    /**
     * print_commands
     *
     * @param xxx $types
     * @param xxx $hotpotscriptname
     * @param xxx $id
     * @param xxx $params
     * @param xxx $popup (optional, default=false)
     * @param xxx $return (optional, default=false)
     * @return xxx
     */
    function print_commands($types, $hotpotscriptname, $id, $params, $popup=false, $return=false)  {
        // $types : array('add', 'update', 'delete', 'deleteall')
        // $params : array('name' => 'value') for url query string
        // $popup : true, false or array('name' => 'something', 'width' => 999, 'height' => 999)

        $commands = html_writer::start_tag('span', array('class'=>'commands'))."\n";
        foreach ($types as $type) {
            $commands .= $this->print_command($type, $hotpotscriptname, $id, $params, $popup, $return);
        }
        $commands .= html_writer::end_tag('form')."\n";

        if ($return) {
            return $commands;
        } else {
            echo $commands;
        }
    }

    /**
     * print_command
     *
     * @param xxx $type
     * @param xxx $hotpotscriptname
     * @param xxx $id
     * @param xxx $params
     * @param xxx $popup (optional, default=false)
     * @param xxx $return (optional, default=false)
     * @return xxx
     */
    function print_command($type, $hotpotscriptname, $id, $params, $popup=false, $return=false)  {
        global $CFG;

        static $str;
        if (! isset($str)) {
            $str = new stdClass();
        }
        if (! isset($str->$type)) {
            $str->$type = get_string($type);
        }

        switch ($type) {
            case 'add':
                $icon = '';
                break;
            case 'edit':
            case 'update':
                $icon = 't/edit';
                break;
            case 'delete':
                $icon = 't/delete.gif';
                break;
            case 'deleteall':
                $icon = '';
                break;
            default:
                // unknown command type !!
                return '';
        }

        foreach ($params as $key => $value) {
            if (empty($value)) {
                unset($params[$key]);
            }
        }
        $params['action'] = $type;
        $url = new moodle_url('/mod/hotpot/'.$hotpotscriptname, $params);

        if ($icon) {
            $linktext = $this->action_icon($url, new pix_icon($icon, get_string($type)));
        } else {
            $linktext = $str->$type;
        }

        if ($popup) {
            if (is_bool($popup)) {
                $popup = array();
            } else if (is_string($popup)) {
                $popup = array('name' => $popup);
            }
            $name  = (isset($popup['name']) ? $popup['name'] : '');
            $width  = (isset($popup['width']) ? $popup['width'] : 650);
            $height = (isset($popup['height']) ? $popup['height'] : 400);
            $command = element_to_popup_window(
                // $type, $url, $name, $linktext, $height, $width, $title, $options, $return, $id, $class
                'link', $url, $name, $linktext, $height, $width, $str->$type, '', true, '', ''
            );
        } else {
            $command = html_writer::link($url, $linktext, array('title' => $str->$type))."\n";

        }

        if (! $icon) {
            // add white space between text commands
            $command .= ' &nbsp; ';
        }

        if ($return) {
            return ' '.$command;
        } else {
            echo ' '.$command;
        }
    }

    /**
     * heading
     *
     * @global object $hotpot
     * @return string
     */
    public function heading($hotpot) {
        $text = format_string($hotpot->name);
        if ($hotpot->can_manage()) {
            $text .= $this->modedit_icon($hotpot);
        }
        return parent::heading($text);
    }

    /**
     * modedit_icon
     *
     * @param $hotpot
     * @return xxx
     * @todo Finish documenting this function
     */
    public function modedit_icon($hotpot) {
        $params = array('update' => $hotpot->cm->id,
                        'return' => 1,
                        'sesskey' => sesskey());
        $url = new moodle_url('/course/modedit.php', $params);
        $img = $this->pix_icon('t/edit', get_string('edit'));
        return ' '.html_writer::link($url, $img);
    }

    /**
     * Formats hotpot entry/exit description text
     *
     * @global object $CFG
     * @param object $hotpot instance of activity
     * @param string $type of page, either "entry" or "exit"
     * @return string
     */
    public function description_box($hotpot, $type='') {
        global $CFG;
        require_once($CFG->dirroot.'/lib/filelib.php');

        if ($type) {
            $textfield = $type.'text';
            $formatfield = $type.'format';
        } else {
            $type = 'intro';
            $textfield = 'intro';
            $formatfield = 'introformat';
        }

        $text = '';
        if (trim(strip_tags($hotpot->$textfield))) {
            $options = (object)array('noclean'=>true, 'para'=>false, 'filter'=>true, 'context'=>$hotpot->context);
            $text = file_rewrite_pluginfile_urls($hotpot->$textfield, 'pluginfile.php', $hotpot->context->id, 'mod_hotpot', $type, null);
            $text = trim(format_text($text, $hotpot->$formatfield, $options, null));
        }

        if ($text) {
            return $this->box($text, 'generalbox', 'intro');
        } else {
            return '';
        }
    }

    /**
     * entryoptions
     *
     * @param xxx $hotpot
     * @return xxx
     */
    public function entryoptions($hotpot)  {
        $output = '';
        $table = new html_table();

        // define the date format - can be one of the following:
        // strftimerecentfull, strftimedaydatetime, strftimedatetime
        $dateformat = get_string('strftimedaydatetime');

        // show open / close dates
        if ($hotpot->entryoptions & hotpot::ENTRYOPTIONS_DATES) {

            if ($hotpot->timeopen) {
                $table->data[] = new html_table_row(array(
                    new html_table_cell(get_string('timeopen', 'mod_hotpot').':'),
                    new html_table_cell(userdate($hotpot->timeopen, $dateformat))
                ));
            }

            if ($hotpot->timeclose) {
                $table->data[] = new html_table_row(array(
                    new html_table_cell(get_string('timeclose', 'mod_hotpot').':'),
                    new html_table_cell(userdate($hotpot->timeclose, $dateformat))
                ));
            }
        }

        // show grading info
        if ($hotpot->entryoptions & hotpot::ENTRYOPTIONS_GRADING) {

            if ($hotpot->attemptlimit > 1) {
                $table->data[] = new html_table_row(array(
                    new html_table_cell(get_string('attemptsallowed', 'quiz').':'),
                    new html_table_cell($hotpot->attemptlimit)
                ));
            }

            if ($hotpot->timelimit > 0) {
                $table->data[] = new html_table_row(array(
                    new html_table_cell(get_string('timelimit', 'mod_hotpot').':'),
                    new html_table_cell(format_time($hotpot->timelimit))
                ));
            }

            if ($hotpot->gradeweighting && $hotpot->attemptlimit != 1) {
                $table->data[] = new html_table_row(array(
                    new html_table_cell(get_string('grademethod', 'mod_hotpot').':'),
                    new html_table_cell($hotpot->format_grademethod())
                ));
            }
        }

        if (count($table->data)) {
            $table->attributes['class'] = 'hotpotentryoptions';
            $output .= html_writer::table($table);
        }

        // print summary of attempts by this user at this unit
        if ($hotpot->entryoptions & hotpot::ENTRYOPTIONS_ATTEMPTS) {
            $output .= $this->attemptssummary($hotpot);
        }

        return $output;
    }

    /**
     * entrywarnings
     *
     * @param xxx $hotpot
     * @return xxx
     */
    public function entrywarnings($hotpot)  {
        $warnings = array();
        $canstart = true;
        if (! $hotpot->can_preview()) {
            if ($error = $hotpot->require_subnet()) {
                // IP-address is not in allowable range
                $warnings[] = $error;
                $canstart = false;
            }
            if ($error = $hotpot->require_isopen()) {
                // hotpot is not (yet) open
                $warnings[] = $error;
                $canstart = false;
            }
            if ($error = $hotpot->require_notclosed()) {
                // hotpot is (already) closed
                $warnings[] = $error;
                $canstart = false;
            }
            if ($error = $hotpot->require_entrycm()) {
                // minimum grade for previous activity not satisfied
                $warnings[] = $error;
                $canstart = false;
            }
            if ($error = $hotpot->require_delay('delay1')) {
                // delay1 has not expired yet
                $warnings[] = $error;
                $canstart = false;
            }
            if ($error = $hotpot->require_delay('delay2')) {
                // delay2 has not expired yet
                $warnings[] = $error;
                $canstart = false;
            }
            if ($error = $hotpot->require_moreattempts(true)) {
                // maximum number of attempts reached
                $warnings[] = $error;
                $canstart = false;
            }
            if ($canstart) {
                if ($error = $hotpot->require_password()) {
                    // password not given yet
                    $warnings[] = $error;
                    $canstart = false;
                }
            }
        }

        // cache the boolean flags in case they are needed later - see $this->view_attempt_button()
        $hotpot->can_start($canstart);

        if (count($warnings)) {
            return $this->box(html_writer::alist($warnings), 'generalbox', 'hotpotwarnings');
        } else {
            return '';
        }
    }

    /**
     * attemptssummary
     *
     * @param xxx $hotpot
     * @return xxx
     */
    public function attemptssummary($hotpot)  {
        global $CFG;

        if (! $countattempts = $hotpot->get_attempts()) {
            return '';
        }

        $output = '';

        // array to store attemptids of certain kinds of attempts
        $attemptids = array(
            'all' => array(),
            'inprogress' => array(),
            'timedout'   => array(),
            'abandoned'  => array(),
            'completed'  => array(),
            'zeroduration' => array(),
            'zeroscore' => array()
        );

        $dateformat = get_string('strftimerecentfull');

        // cache selectcolumn switch
        if ($hotpot->can_deleteattempts()) {
            $showselectcolumn = true;
        } else {
            $showselectcolumn = false;
        }

        // cache report links flag
        if ($hotpot->can_reviewattempts()) {
            $showreportlinks = true;
        } else {
            $showreportlinks = false;
        }

        // set resume tab text
        if ($hotpot->can_preview()) {
            $resumetab = 'preview';
        } else if ($hotpot->can_view()) {
            $resumetab = 'info';
        } else {
            $resumetab = '';
        }

        // start attempts table (info + resume buttons)
        $table = new html_table();
        $table->attributes['class'] = 'hotpotattemptssummary';
        $table->head = array(
            get_string('attemptnumber', 'mod_hotpot'),
            get_string('status', 'mod_hotpot'),
            get_string('duration', 'mod_hotpot'),
            get_string('lastaccess', 'mod_hotpot')
        );
        $table->align = array('center', 'center', 'left', 'left');
        $table->size = array('', '', '', '');
        if ($hotpot->gradeweighting) {
            // insert grade column
            array_splice($table->head, 1, 0, array(get_string('score', 'mod_hotpot')));
            array_splice($table->align, 1, 0, array('center'));
            array_splice($table->size, 1, 0, array(''));
        }
        if ($showselectcolumn) {
            // prepend select column
            array_splice($table->head, 0, 0, '&nbsp;');
            array_splice($table->align, 0, 0, array('center'));
            array_splice($table->size, 0, 0, array(''));
        }

        // echo rows of attempt info
        foreach ($hotpot->attempts as $attempt) {
            $row = new html_table_row();

            // set duration
            if ($attempt->timestart && $attempt->timefinish) {
                $duration = $attempt->timefinish - $attempt->timestart;
            } else if ($attempt->starttime && $attempt->endtime) {
                $duration = $attempt->endtime - $attempt->starttime;
            } else if ($attempt->timestart && $attempt->timemodified) {
                $duration = $attempt->timemodified - $attempt->timestart;
            } else {
                $duration = 0;
            }

            if ($showselectcolumn) {
                $id = '['.$attempt->id.']';
                $row->cells[] = new html_table_cell(html_writer::checkbox('selected'.$id, 1, false));

                switch ($attempt->status) {
                    case hotpot::STATUS_INPROGRESS: $attemptids['inprogress'][] = $id; break;
                    case hotpot::STATUS_TIMEDOUT:   $attemptids['timedout'][]   = $id; break;
                    case hotpot::STATUS_ABANDONED:  $attemptids['abandoned'][]  = $id; break;
                    case hotpot::STATUS_COMPLETED:  $attemptids['completed'][]  = $id; break;
                }
                if ($attempt->score==0) {
                    $attemptids['zeroscore'][] = $id;
                }
                if ($duration==0) {
                    $attemptids['zeroduration'][] = $id;
                }
                $attemptids['all'][] = $id;
            }

            $row->cells[] = new html_table_cell($attempt->attempt);

            if ($hotpot->gradeweighting) {
                $text = $attempt->score.'%';
                if ($showreportlinks) {
                    $url = $hotpot->review_url($attempt);
                    $text = html_writer::link($url, $text);
                }
                $row->cells[] = new html_table_cell($text);
            }

            $row->cells[] = new html_table_cell($hotpot->format_status($attempt->status));
            $row->cells[] = new html_table_cell($hotpot->format_time($duration));
            $row->cells[] = new html_table_cell(userdate($attempt->timemodified, $dateformat));

            $table->data[] = $row;
        }

        // start form if necessary
        if ($showselectcolumn) {
            $onsubmit = ''
                ."var x=false;"
                ."var obj=document.getElementsByTagName('input');"
                ."if(obj){"
                    ."for(var i in obj){"
                        ."if(obj[i].name && obj[i].name.substr(0,9)=='selected[' && obj[i].checked){"
                            ."x=true;"
                            ."break;"
                        ."}"
                    ."}"
                    ."if(!x){"
                        ."alert('".get_string('checksomeboxes', 'mod_hotpot')."');"
                    ."}"
                ."}"
                ."if(x){"
                    ."x=confirm('".get_string('confirmdeleteattempts', 'mod_hotpot')."');"
                ."}"
                ."if(this.elements['confirmed']){"
                    ."this.elements['confirmed'].value=(x?1:0);"
                ."}"
                ."return x;"
            ;
            $params = array(
                'id' => $hotpot->cm->id, 'sesskey' => sesskey(), 'confirmed' => 0, 'action' => 'deleteselected'
            );
            $output .= $this->form_start('view.php', $params, array('onsubmit' => $onsubmit))."\n";
       }

        // echo the summary of attempts
        $output .= html_writer::table($table);

        // end form if necessary
        if ($showselectcolumn) {
            $output .= $this->box_start('generalbox', 'hotpotdeleteattempts');
            $output .= ''
                .'<script type="text/javascript">'."\n"
                .'//<!CDATA['."\n"
                ."function hotpot_set_checked(nameFilter, indexFilter, checkedValue) {\n"
                ."	var partMatchName = new RegExp(nameFilter);\n"
                ."	var fullMatchName = new RegExp(nameFilter+indexFilter);\n"
                ."	var inputs = document.getElementsByTagName('input');\n"
                ."	if (inputs) {\n"
                ."		var i_max = inputs.length;\n"
                ."	} else {\n"
                ."		var i_max = 0;\n"
                ."	}\n"
                ."	for (var i=0; i<i_max; i++) {\n"
                ."		if (inputs[i].type=='checkbox' && inputs[i].name.match(partMatchName)) {\n"
                ."			if (inputs[i].name.match(fullMatchName)) {\n"
                ."				inputs[i].checked = checkedValue;\n"
                ."			} else {\n"
                ."				inputs[i].checked = false;\n"
                ."			}\n"
                ."		}\n"
                ."	}\n"
                ."	return true;\n"
                ."}\n"
                ."function hotpot_set_checked_attempts(obj) {\n"
                ."	var indexFilter = obj.options[obj.selectedIndex].value;\n"
                ."	if (indexFilter=='none') {\n"
                ."		checkedValue = 0;\n"
                ."	} else {\n"
                ."		checkedValue = 1;\n"
                ."	}\n"
                ."	if (indexFilter=='none' || indexFilter=='all') {\n"
                ."		indexFilter = '\\\\[\\\\d+\\\\]';\n"
                ."	} else {\n"
                ."		indexFilter = indexFilter.replace(new RegExp('^[^:]*:'), '');\n"
                ."		indexFilter = indexFilter.replace(new RegExp(',', 'g'), '|');\n"
                ."		indexFilter = indexFilter.replace(new RegExp('\\\\[', 'g'), '\\\\[');\n"
                ."		indexFilter = indexFilter.replace(new RegExp('\\\\]', 'g'), '\\\\]');\n"
                ."	}\n"
                ."	hotpot_set_checked('selected', indexFilter, checkedValue);"
                ."}\n"
                .'//]]>'."\n"
                .'</script>'."\n"
            ;
            $onchange = 'return hotpot_set_checked_attempts(this)';


            // set up attempt status drop down menu
            $options = array(
                'none' => get_string('none')
            );
            foreach($attemptids as $type=>$ids) {
                if ($total = count($ids)) {
                    if ($type=='all') {
                        $options['all'] = get_string('all');
                        if ($total > 1) {
                            $options['all'] .= " ($total)";
                        }
                    } else {
                        $options[$type.':'.implode(',', $ids)] = get_string($type, 'mod_hotpot')." ($total)";
                    }
                }
            }

            // add attempt selection/deletion form controls
            $table = new html_table();
            $table->attributes['class'] = 'hotpotdeleteattempts';

            $table->data[] = new html_table_row(array(
                new html_table_cell(get_string('selectattempts', 'mod_hotpot').':'),
                new html_table_cell(html_writer::select($options, 'selectattempts', null, array(''=>'choosedots'), array('onchange'=>$onchange)))
            ));
            // original onselect was 'return hotpot_set_checked_attempts(this)'
            // Moodle 2.0 uses component_actions, some thing like this ...
            // actually this doesn't work, but it is close - maybe :-)
            // $action = new component_action('select', 'hotpot_set_checked_attempts', array('this'));
            // $this->add_action_handler($action, 'menuselectattempts');

            $table->data[] = new html_table_row(array(
                new html_table_cell('&nbsp;'),
                new html_table_cell(html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('deleteattempts', 'mod_hotpot'))))
            ));

            $output .= html_writer::table($table);

            $output .= $this->box_end();
            $output .= $this->form_end();
        }
        return $output;
    }

    /**
     * view_attempt_button
     *
     * @param xxx $hotpot
     * @return xxx
     */
    function view_attempt_button($hotpot)  {
        $output = '';

        // Initialize button text. This will be set something
        // if as start/continue attempt button should appear.
        $buttontext = '';

        if ($hotpot->can_preview()) {
            $buttontext = get_string('preview');
        } else if ($hotpot->can_start()) {
            if ($hotpot->count_distinct_clickreportids()) {
                $buttontext = get_string('reattemptquiz', 'quiz');
            } else {
                $buttontext = get_string('attemptquiznow', 'quiz');
            }
        }

        $output .= $this->box_start('hotpotviewbutton');

        if ($buttontext) {
            $url = $hotpot->attempt_url();
            $button = new single_button($url, $buttontext);
            $button->class .= ' hotpotviewbutton';
            $output .= $this->render($button);
        } else {
            $url = new moodle_url('/course/view.php', array('id' => $hotpot->course->id));
            $output .= $this->continue_button($url);
        }

        $output .= $this->box_end();
        return $output;
    }

    /**
     * whatnext
     *
     * @param xxx $str
     * @return xxx
     */
    public function whatnext($str='') {
        switch ($str) {
            case '':
                $whatnext = get_string('exit_whatnext_default', 'mod_hotpot');
                break;

            case 'exit_whatnext':
                switch (mt_rand(0,1)) { // random 0 or 1. You can add more if you like
                    case 0: $whatnext = get_string('exit_whatnext_0', 'mod_hotpot'); break;
                    case 1: $whatnext = get_string('exit_whatnext_1', 'mod_hotpot'); break;
                }
                break;

            default:
                $whatnext = get_string($str, 'mod_hotpot');
        }

        return html_writer::tag('h3', $whatnext, array('class'=>'hotpotwhatnext'));
    }

    /**
     * exitoptions
     *
     * @param xxx $hotpot
     * @return xxx
     */
    public function exitfeedback($hotpot) {
        global $CFG;

        $percentsign = '%';

        $feedback = array();

        if ($hotpot->gradeweighting==0) {
            if ($hotpot->exitoptions & hotpot::EXITOPTIONS_ATTEMPTSCORE || $hotpot->exitoptions & hotpot::EXITOPTIONS_HOTPOTGRADE) {
                $text = get_string('exit_noscore', 'mod_hotpot');
                $feedback[] = html_writer::tag('li', $text);
            }
        } else if ($hotpot->get_gradeitem() && $hotpot->get_attempt()) {
            if ($hotpot->exitoptions & hotpot::EXITOPTIONS_ENCOURAGEMENT) {
                switch (true) {
                    case $hotpot->attempt->score >= 90:
                        $text = get_string('exit_excellent', 'mod_hotpot');
                        break;
                    case $hotpot->attempt->score >= 60:
                        $text = get_string('exit_welldone', 'mod_hotpot');
                        break;
                    case $hotpot->attempt->score > 0:
                        $text = get_string('exit_goodtry', 'mod_hotpot');
                        break;
                    default:
                        $text = get_string('exit_areyouok', 'mod_hotpot');
                }
                $feedback[] = html_writer::tag('li', $text, array('class' => 'hotpotexitencouragement'));
            }
            if ($hotpot->exitoptions & hotpot::EXITOPTIONS_ATTEMPTSCORE) {
                $text = get_string('exit_attemptscore', 'mod_hotpot', $hotpot->attempt->score.$percentsign);
                $feedback[] = html_writer::tag('li', $text);
            }
            if ($hotpot->exitoptions & hotpot::EXITOPTIONS_HOTPOTGRADE) {
                switch ($hotpot->grademethod) {
                    case hotpot::GRADEMETHOD_HIGHEST:
                        if ($hotpot->attempt->score < $hotpot->gradeitem->percent) {
                            // current attempt is less than the highest so far
                            $text = get_string('exit_hotpotgrade_highest', 'mod_hotpot', $hotpot->gradeitem->percent.$percentsign);
                            $feedback[] = html_writer::tag('li', $text);
                        } else if ($hotpot->attempt->score==0) {
                            // zero score is best so far
                            $text = get_string('exit_hotpotgrade_highest_zero', 'mod_hotpot', $hotpot->attempt->score.$percentsign);
                            $feedback[] = html_writer::tag('li', $text);
                        } else if ($hotpot->get_attempts()) {
                            // current attempt is highest so far
                            $maxscore = null;
                            foreach ($hotpot->attempts as $attempt) {
                                if ($attempt->id==$hotpot->attempt->id) {
                                    continue; // skip current attempt
                                }
                                if (is_null($maxscore) || $maxscore<$attempt->score) {
                                    $maxscore = $attempt->score;
                                }
                            }
                            if (is_null($maxscore)) {
                                // do nothing (no previous attempt)
                            } else if ($maxscore==$hotpot->attempt->score) {
                                // attempt grade equals previous best
                                $text = get_string('exit_hotpotgrade_highest_equal', 'mod_hotpot');
                                $feedback[] = html_writer::tag('li', $text);
                            } else {
                                $text = get_string('exit_hotpotgrade_highest_previous', 'mod_hotpot', $maxscore.$percentsign);
                                $feedback[] = html_writer::tag('li', $text);
                            }
                        } else {
                            die('oops, no attempts');
                        }
                        break;
                    case hotpot::GRADEMETHOD_AVERAGE:
                        $text = get_string('exit_hotpotgrade_average', 'mod_hotpot', $hotpot->gradeitem->percent.$percentsign);
                        $feedback[] = html_writer::tag('li', $text);
                        break;
                    // case hotpot::GRADEMETHOD_TOTAL:
                    // case hotpot::GRADEMETHOD_FIRST:
                    // case hotpot::GRADEMETHOD_LAST:
                    default:
                        $text = get_string('exit_hotpotgrade', 'mod_hotpot', $hotpot->gradeitem->percent.$percentsign);
                        $feedback[] = html_writer::tag('li', $text);
                        break;
                }
            }
        }

        if (count($feedback)) {
            $feedback = html_writer::tag('ul', implode('', $feedback), array('class' => 'hotpotexitfeedback'));
            return $this->box($feedback);
        } else {
            return '';
        }
    }

    /**
     * exitlinks
     *
     * @param xxx $hotpot
     * @return xxx
     */
    public function exitlinks($hotpot)  {
        $table = new html_table();
        $table->attributes['class'] = 'hotpotexitlinks';

        if ($hotpot->attempt->status==hotpot::STATUS_COMPLETED) {
            if ($hotpot->require_exitgrade() && $hotpot->attempt->score < $hotpot->exitgrade) {
                // insufficient grade to show link to next activity
                $cm = false;
            } else {
                // get next activity, if there is one
                $cm = $hotpot->get_cm('exit');
            }
            if ($cm) {
                $url = new moodle_url('/mod/'.$cm->modname.'/view.php', array('id' => $cm->id));
                $table->data[] = new html_table_row(array(
                    new html_table_cell(html_writer::link($url, get_string('exit_next', 'mod_hotpot'))),
                    new html_table_cell(html_writer::link($url, format_string(urldecode($cm->name))))
                ));
            }
        }

        if ($hotpot->exitoptions & hotpot::EXITOPTIONS_RETRY) {
            // retry this hotpot, if allowed
            if ($hotpot->attemptlimit==0 || empty($hotpot->attempts) || $hotpot->attemptlimit < count($hotpot->attempts)) {
                $table->data[] = new html_table_row(array(
                    new html_table_cell(html_writer::link($hotpot->view_url(), get_string('exit_retry', 'mod_hotpot'))),
                    new html_table_cell(html_writer::link($hotpot->view_url(), format_string($hotpot->name))),
                ));
            }
        }

        if ($hotpot->exitoptions & hotpot::EXITOPTIONS_INDEX) {
            $table->data[] = new html_table_row(array(
                new html_table_cell(html_writer::link($hotpot->index_url(), get_string('exit_index', 'mod_hotpot'))),
                new html_table_cell(html_writer::link($hotpot->index_url(), get_string('exit_index_text', 'mod_hotpot')))
            ));
        }

        if ($hotpot->exitoptions & hotpot::EXITOPTIONS_COURSE) {
            $table->data[] = new html_table_row(array(
                new html_table_cell(html_writer::link($hotpot->course_url(), get_string('exit_course', 'mod_hotpot'))),
                new html_table_cell(html_writer::link($hotpot->course_url(), get_string('exit_course_text', 'mod_hotpot')))
            ));
        }

        if ($hotpot->exitoptions & hotpot::EXITOPTIONS_GRADES) {
            if ($hotpot->course->showgrades && $hotpot->gradeweighting) {
                $url = new moodle_url($hotpot->grades_url());
                $table->data[] = new html_table_row(array(
                    new html_table_cell(html_writer::link($url, get_string('exit_grades', 'mod_hotpot'))),
                    new html_table_cell(html_writer::link($url, get_string('exit_grades_text', 'mod_hotpot')))
                ));
            }
        }

        $output = '';
        if ($count = count($table->data)) {
            if ($count>1) {
                $output .= $this->whatnext('exit_whatnext');
            }
            $output .= html_writer::table($table);
        }

        return $output;
    }
}
