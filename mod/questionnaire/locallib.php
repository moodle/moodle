<?php // $Id$

/**
 * This library replaces the phpESP application with Moodle specific code. It will eventually
 * replace all of the phpESP application, removing the dependency on that.
 */

/**
 * Updates the contents of the survey with the provided data. If no data is provided,
 * it checks for posted data.
 * 
 * @param int $survey_id The id of the survey to update.
 * @param string $old_tab The function that was being executed.
 * @param object $sdata The data to update the survey with.
 * 
 * @return string|boolean The function to go to, or false on error.
 * 
 */

/// Constants

/// Specify the date format for 'date' fields.
/// Use the formats frm 'strftime' (http://www.php.net/manual/en/function.strftime.php).
/// Use %m for month, %d for day of month, %Y for 4 digit year, %y for 2 digit year.
define('QUESTIONNAIREDATEFORMAT', '%d/%m/%Y'); 

// Allow phpESP to send email (BOOLEAN)
$ESPCONFIG['allow_email'] = true;

// Send human readable email, rather than machine readable (BOOLEAN)
$ESPCONFIG['human_email'] = false;

// Use authentication for designer interface (BOOLEAN)
$ESPCONFIG['auth_design'] = true;

// Use authentication for survey responders (BOOLEAN)
$ESPCONFIG['auth_response'] = true;

// Default number of option lines for new questions
$ESPCONFIG['default_num_choices'] = 10;

// Colors used by phpESP
$ESPCONFIG['main_bgcolor']      = '#FFFFFF';
$ESPCONFIG['link_color']        = '#0000CC';
$ESPCONFIG['vlink_color']       = '#0000CC';
$ESPCONFIG['alink_color']       = '#0000CC';
$ESPCONFIG['table_bgcolor']     = '#0099FF';
$ESPCONFIG['active_bgcolor']    = '#FFFFFF';
$ESPCONFIG['dim_bgcolor']       = '#3399CC';
$ESPCONFIG['error_color']       = '#FF0000';
$ESPCONFIG['warn_color']        = '#FF0000';
$ESPCONFIG['reqd_color']        = '#FF0000';
$ESPCONFIG['bgalt_color1']      = '#FFFFFF';
$ESPCONFIG['bgalt_color2']      = '#EEEEEE';

// phpESP css path
$ESPCONFIG['css_path'] = $CFG->dirroot.'/mod/questionnaire/css/';


require_once('questiontypes/questiontypes.class.php');

define ('QUESTIONNAIREDEFAULTNUMCHOICES', 10);

class questionnaire {

/// Class Properties
    /**
     * The survey record.
     * @var object $survey
     */
     var $survey;

/// Class Methods

    /**
     * The class constructor
     *
     */
    function questionnaire($id = 0, $questionnaire = null, &$course, &$cm, $addquestions = true) {

        if ($id) {
            $questionnaire = get_record('questionnaire', 'id', $id);
        }

        if (is_object($questionnaire)) {
            $properties = get_object_vars($questionnaire);
            foreach ($properties as $property => $value) {
                $this->$property = $value;
            }
        }

        $this->add_survey($this->sid);

        $this->course = $course;
        $this->cm = $cm;

        if ($addquestions) {
            $this->add_questions($this->sid);
        }

        $this->usehtmleditor = can_use_html_editor();
    }

    /**
     * Fake constructor to keep PHP5 happy
     *
     */
    function __construct($id = 0, $questionnaire = null, &$course, &$cm, $addquestions = true) {
        $this->questionnaire($id, $questionnaire, $course, $cm, $addquestions);
    }

    /**
     * Adding a survey record to the object.
     *
     */
    function add_survey($sid = 0, $survey = null) {

        if ($sid) {
            $this->survey = get_record('questionnaire_survey', 'id', $sid);
        } else if (is_object($survey)) {
            $this->survey = clone($survey);
        }
    }

    /**
     * Adding questions to the object.
     */
    function add_questions($sid = false, $section = false) {

        if ($sid === false) {
            $sid = $this->sid;
        }

        $select = 'survey_id = '.$sid.' AND deleted != \'Y\'';
        if ($records = get_records_select('questionnaire_question', $select, 'position')) {
            $sec = 1;
            foreach ($records as $record) {
                $this->questions[$record->id] = new questionnaire_question(0, $record);
//                if ($record->type_id != 100) {
                    if ($record->type_id != 99) {
                        $this->questionsbysec[$sec][$record->id] = &$this->questions[$record->id];
                    } else {
                        $sec++;
                    }
//                }
            }
        }
    }

    function view() {
        global $CFG, $USER, $QUESTIONNAIRE_STUDENTVIEWRESPONSE;

        if ($this->course->category) {
            $navigation = "<A HREF=\"../../course/view.php?id=$this->course->id\">$this->course->shortname</A> ->";
        }

        print_header_simple($this->name, "",
                     '<a href="index.php?id='.$this->course->id.'">'.$this->strquestionnaires.'</a> -> '.
                     $this->name, '', '', true, 
                     update_module_button($this->cm->id, $this->course->id, $this->strquestionnaire), 
                     navmenu($this->course, $this->cm));
    
        if (isguest() && !$this->allows_guests()) {
            print_heading(get_string("guestsno", "questionnaire"));
            print_footer($this->course);
            exit;
        }
    
        $select = 'survey_id = '.$this->sid.' AND username = '.$USER->id;
        if ($responses = get_records_select('questionnaire_response', $select, 'id DESC', 'id,complete')) {
            $response = reset($responses);
        } else {
            $response->id = 0;
        }
    
        echo '<table align="right" cellspacing="0" cellpadding="4" border="0"><tr><td>';
        echo '<form name="print" method="post" action="print.php" target="_new">'."\n";
        echo '<input type="hidden" name="qid" value="'.$this->id.'" />'."\n";
        echo '<input type="hidden" name="rid" value="'.$response->id.'" />'."\n";
        echo '<input type="hidden" name="sec" value="1" />'."\n";
        echo '<span style="font-size:75%;">Open a printable window</span> ';
        echo '<input type="submit" value="Print" name="print" /> ';
        echo '</form>';
        echo '</td>';

        if (isteacher($this->course->id)) {
            echo '<td align="right">';
            if ($numresp = $this->count_submissions($USER->id)) {
                $strviewresponses = get_string('viewyourresponses', 'questionnaire', $numresp);
                echo '<a href="myreport.php?instance='.$this->id.
                     '&user='.$USER->id.'">'.$strviewresponses.'</a><br />';
            }
            if ($this->is_survey_owner()) {
            	if ($numresp = $this->count_submissions()) {
                    $strviewresponses = get_string('viewresponses', 'questionnaire', $numresp);
                    echo '<a href="report.php?instance='.$this->id.
                         '&sid='.$this->sid.'&qact=vresp">'.$strviewresponses.'</a>';
                } else {
                    echo get_string('noresponses', 'questionnaire');
                }
            }
            echo '</td>';
        } else if ($this->resp_view == $QUESTIONNAIRE_STUDENTVIEWRESPONSE) {
        	echo '<td>';
            if ($numresp = $this->count_submissions($USER->id)) {
                $strviewresponses = get_string('viewresponses', 'questionnaire', $numresp);
                echo '<p align="right"><a href="myreport.php?instance='.$this->id.
                     '&user='.$USER->id.'">'.$strviewresponses.'</a></p>';
            } else {
                echo '<p align="right">'.get_string('noresponses', 'questionnaire').'</p>';
            }
            echo '</td>';
    
        } else if (!$this->cm->visible) {
            notice(get_string("activityiscurrentlyhidden"));
        }
    
        echo '</tr></table>';
    
    /// Print the main part of the page
    
    //    echo "<pre>".print_r($course, true).print_r($cm, true).print_r($this, true)."</pre>";
    
        if (!$this->is_active()) {
            print_string('notavail', 'questionnaire');
        }
        else if (!$this->is_open()) {
            print_string('notopen', 'questionnaire', userdate($this->opendate));
        }
        else if ($this->is_closed()) {
            print_string('closed', 'questionnaire', userdate($this->closedate));
        }
        else if (!$this->user_is_eligible($USER->id)) {
            print_string('noteligible', 'questionnaire');
        }
        else if ($this->user_can_take($USER->id)) {
            $sid=$this->sid; 
    
    ///     If the respondent type is set to fullname, pass the userid.
            switch ($this->respondenttype) {
            case 'fullname':
                $quser = $USER->id;
                break;
    
    ///     If the respondent type is set to anonymous, hide the user's name.
            case 'anonymous':
            default:
                $quser = 'Anonymous';
                break;
            }
    
            if ($this->survey->realm == 'template') {
                print_string('templatenotviewable', 'questionnaire');
                print_footer($this->course);
                exit();
            } else if (!empty($this->survey->theme)) {
                echo '<link rel="stylesheet" href="'.
                 $CFG->wwwroot.'/mod/questionnaire/css/'.$this->survey->theme.'" type="text/css">'."\n";
            }
    
            $this->print_survey($USER->id, $quser);

            $viewform = data_submitted($CFG->wwwroot."/mod/questionnaire/view.php");
    
    ///     If Survey was submitted with all required fields completed ($msg is empty),
    ///     then record the submittal.
            if (isset($viewform->submit) && isset($viewform->submittype) && 
                ($viewform->submittype == "Submit Survey") && empty($msg)) {
                /// If its not an anonymous questionnaire, store the response id.
                if ($this->respondenttype != 'anonymous') {
                /// If it was a previous save, rid is in the form...
                    if (!empty($viewform->rid) && is_numeric($viewform->rid)) {
                        $rid = $viewform->rid;
    
                /// Otherwise its in this object.
                    } else {
                        $rid = $this->rid;
                    }
                } else {
                    $rid = 0;
                }
                questionnaire_record_submission($this, $USER->id, $rid);
                add_to_log($this->course->id, "questionnaire", "submit", "view.php?id=$this->cm->id", "$this->id", $this->cm->id, $USER->id);
            }
            
        } else {
            switch ($this->qtype) {
                case QUESTIONNAIREDAILY:
                    $msgstring = ' '.get_string('today', 'questionnaire');
                    break;
                case QUESTIONNAIREWEEKLY:
                    $msgstring = ' '.get_string('thisweek', 'questionnaire');
                    break;
                case QUESTIONNAIREMONTHLY:
                    $msgstring = ' '.get_string('thismonth', 'questionnaire');
                    break;
                default:
                    $msgstring = '';
                    break;
            }
            print_string("alreadyfilled", "questionnaire", $msgstring);
        }
    
    /// Finish the page
        if ($this->usehtmleditor) {
            use_html_editor();
        }
        print_footer($this->course);
    }

   /**
    * Function to view an entire responses data.
    * 
    */
    function view_response($rid) {
        global $CFG;

        if (!empty($this->survey->theme)) {
            echo '<link rel="stylesheet" href="'.
             $CFG->wwwroot.'/mod/questionnaire/css/default.css" type="text/css">'."\n";
        }

        print_simple_box_start();
    	$this->print_survey_start('', 1, 1, 0);

        $data = new Object();
        $i = 1;
        echo '<div class="mainTable">';
        $this->response_import_all($rid, &$data);
        foreach ($this->questions as $question) {
            if ($question->type_id < QUESPAGEBREAK) {
                $question->response_display($data, $i++);
            }
        }
        echo '</div>';

        $this->print_survey_end(1, 1);
        print_simple_box_end();
    }

   /**
    * Function to view an entire responses data.
    * 
    */
    function view_all_responses($resps) {
        global $CFG, $QTYPENAMES;

        if (!empty($this->survey->theme)) {
            echo '<link rel="stylesheet" href="'.
             $CFG->wwwroot.'/mod/questionnaire/css/default.css" type="text/css">'."\n";
        }

        print_simple_box_start();
        $this->print_survey_start('', 1, 1, 0);

        foreach ($resps as $resp) {
        	$data[$resp->id] = new Object();
            $this->response_import_all($resp->id, &$data[$resp->id]);
        }

        $i = 1;
        echo '<div class="mainTable">';
        foreach ($this->questions as $question) {
            if ($question->type_id < QUESPAGEBREAK) {
                $method = $QTYPENAMES[$question->type_id].'_response_display';
                if (method_exists($question, $method)) {
                    $question->questionstart_survey_display($i);
                    foreach ($data as $respid => $respdata) {
                        echo '<div class="respdate">'.userdate($resps[$respid]->submitted).':</div>';
                        $question->$method($respdata);
                        echo '<hr />';
                    }
                    $question->questionend_survey_display($i);
                } else {
                    error('Display method not defined for question.');
                }
            }
            $i++;
        }
        echo '</div>';

        $this->print_survey_end(1, 1);
        print_simple_box_end();
    }

/// Access Methods
    function is_active() {
        return (!empty($this->survey));
    }
    
    function is_open() {
        return ($this->opendate > 0) ? ($this->opendate < time()) : true;
    }
    
    function is_closed() {
        return ($this->closedate > 0) ? ($this->closedate < time()) : false;
    }
    
    function user_can_take($userid) {
    
        if (!$this->is_active() || !$this->user_is_eligible($userid)) {
            return false;
        }
        else if ($this->qtype == QUESTIONNAIREUNLIMITED) {
            return true;
        }
        else if ($userid > 0){
        	return $this->user_time_for_new_attempt($userid);
        }
        else {
            return false;
        }
    }
    
    function user_is_eligible($userid) {
        return ($this->resp_eligible == 'all') ||
               (($this->resp_eligible == 'students') && isstudent($this->course->id, $userid)) ||
               (($this->resp_eligible == 'teachers') && isteacher($this->course->id, $userid));
    }

    function user_time_for_new_attempt($userid) {

        $select = 'qid = '.$this->id.' AND userid = '.$userid;
    	if (!($attempts = get_records_select('questionnaire_attempts', $select, 'timemodified DESC'))) {
    		return true;
    	}

        $attempt = reset($attempts);
        $timenow = time();
        
        switch ($this->qtype) {

        	case QUESTIONNAIREUNLIMITED:
                $cantake = true;
                break;

            case QUESTIONNAIREONCE:
                $cantake = false;
                break;

            case QUESTIONNAIREDAILY:
                $attemptyear = date('Y', $attempt->timemodified);
                $currentyear = date('Y', $timenow);
                $attemptdayofyear = date('z', $attempt->timemodified);
                $currentdayofyear = date('z', $timenow);
                $cantake = (($attemptyear < $currentyear) ||
                            (($attemptyear == $currentyear) && ($attemptdayofyear < $currentdayofyear)));
                break;

            case QUESTIONNAIREWEEKLY:
                $attemptyear = date('Y', $attempt->timemodified);
                $currentyear = date('Y', $timenow);
                $attemptweekofyear = date('W', $attempt->timemodified);
                $currentweekofyear = date('W', $timenow);
                $cantake = (($attemptyear < $currentyear) ||
                            (($attemptyear == $currentyear) && ($attemptweekofyear < $currentweekofyear)));
                break;

            case QUESTIONNAIREMONTHLY:
                $attemptyear = date('Y', $attempt->timemodified);
                $currentyear = date('Y', $timenow);
                $attemptmonthofyear = date('n', $attempt->timemodified);
                $currentmonthofyear = date('n', $timenow);
                $cantake = (($attemptyear < $currentyear) ||
                            (($attemptyear == $currentyear) && ($attemptmonthofyear < $currentmonthofyear)));
                break;

            default:
                $cantake = false;
                break;
        }

        return $cantake;
    }
    
    function is_survey_owner() {
        return ($this->course->id == $this->survey->owner);
    }
    
    function allows_guests() {
        return ($this->respondenttype == 'anonymous') && ($this->qtype == 'unlimited') &&
               ($this->resp_eligible == 'all');
    }

    function count_submissions($userid=false) {
    	if (!$userid) {
            return count_records('questionnaire_response', 'survey_id', $this->sid, 'complete', 'Y');
        } else {
        	return count_records('questionnaire_response', 'survey_id', $this->sid, 'username', $userid, 
                                 'complete', 'Y');
        }
    }

    function has_required($section = 0) {
        if (empty($this->questions)) {
            return false;
        } else if ($section <= 0) {
            foreach ($this->questions as $question) {
                if ($question->required == 'Y') {
                    return true;
                }
            }
        } else {
            foreach ($this->questionsbysec[$section] as $question) {
                if ($question->required == 'Y') {
                    return true;
                }
            }
        }
        return false;
    }
/// Display Methods

    function print_survey($userid=false, $quser) {
        global $USER, $PAGE, $CFG;
    
        if (!$userid) {
            $userid = $USER->id;
        }
    
        $formdata = data_submitted('nomatch');
    
        if ($this->resume) {
            $formdata->rid = $this->get_response($quser, $formdata->rid);
            if (!empty($formdata->rid) && (empty($formdata->sec) || intval($formdata->sec) < 1)) {
                $formdata->sec = $this->response_select_max_sec($formdata->rid);
            }
        }
    
        if (empty($formdata->sec)) {
            $formdata->sec = 1;
        } else {
            $formdata->sec = (intval($formdata->sec) > 0) ? intval($formdata->sec) : 1;
        }
    
        $num_sections = count($this->questionsbysec);    /// indexed by section.
        $msg = '';
        $action = $CFG->wwwroot.'/mod/questionnaire/view.php?id='.$this->cm->id;
    
        if(!empty($formdata->submit)) {
            $msg = $this->response_check_required($formdata->sec, $formdata);
            if(empty($msg)) {
                if ($this->resume) {
                    $this->response_delete($formdata->rid, $formdata->sec);
                }
                $this->rid = $this->response_insert($this->survey->id, $formdata->sec, $formdata->rid, $quser, $formdata);
                $this->response_commit($this->rid);
                $this->response_send_email($this->rid);
                $this->response_goto_thankyou();
                return;
            }
        }
    
        if(!empty($formdata->resume) && ($this->resume)) {
            $this->response_delete($formdata->rid, $formdata->sec);
            $formdata->rid = $this->response_insert($this->survey->id, $formdata->sec, $formdata->rid, $quser, $formdata);
            if ($action == $ESPCONFIG['autopub_url'])
                $this->response_goto_saved("$action?name=$name");
            else
                $this->response_goto_saved($action);
            return;
        }
    
        if(!empty($formdata->next)) {
            $msg = $this->response_check_required($formdata->sec, $formdata);
            if(empty($msg)) {
                if ($this->resume) {
                    $this->response_delete($formdata->rid, $formdata->sec);
                }
                $formdata->rid = $this->response_insert($this->survey->id, $formdata->sec, $formdata->rid, $quser, $formdata);
                $formdata->sec++;
            }
        }
        
        if (!empty($formdata->prev) && ($this->navigate)) {
            if(empty($msg)) {
                if ($this->resume) {
                    $this->response_delete($formdata->rid, $formdata->sec);
                }
                $formdata->rid = $this->response_insert($this->survey->id, $formdata->sec, $formdata->rid, $quser, $formdata);
                $formdata->sec--;
            }
        }
        
        if ($this->resume) {
            $this->response_import_sec($formdata->rid, $formdata->sec, $formdata);
        }
    
        echo '
    <script language="JavaScript">
    <!-- // Begin
    function other_check(name)
    {
      other = name.split("_");
      var f = document.phpesp_response;
      for (var i=0; i<=f.elements.length; i++) {
        if (f.elements[i].value == "other_"+other[1]) {
          f.elements[i].checked=true;
          break;
        }
      }
    }
    // End -->
    </script>
            ';
    ?>
    <form method="post" name="phpesp_response" action="<?php echo($action); ?>">
    <input type="hidden" name="referer" value="<?php echo htmlspecialchars($formdata->referer); ?>">
    <input type="hidden" name="userid" value="<?php echo($formdata->userid); ?>">
    <input type="hidden" name="a" value="<?php echo($this->id); ?>">
    <input type="hidden" name="sid" value="<?php echo($this->survey->id); ?>">
    <input type="hidden" name="rid" value="<?php echo (isset($formdata->rid) ? $formdata->rid : '0'); ?>">
    <input type="hidden" name="sec" value="<?php echo($formdata->sec); ?>">
    
    <?php
        $this->survey_render($formdata->sec, $msg, $formdata);
        if (($this->navigate) && ($formdata->sec > 1)) {
            echo '<input type="submit" name="prev" value="'.get_string('previouspage', 'questionnaire').'" /> ';
        }
        if ($this->resume) {
            echo '<input type="submit" name="resume" value="'.get_string('save', 'questionnaire').'" />';
        }
    //  Add a 'hidden' variable for the mod's 'view.php', and use a language variable for the submit button.
        if($formdata->sec == $num_sections) {
            echo '
        <input type="hidden" name="submittype" value="Submit Survey" />
        <input type="submit" name="submit" value="'.get_string('submitsurvey', 'questionnaire').'" />';
        } else {
            echo ' <input type="submit" name="next" value="'.get_string('nextpage', 'questionnaire').'" />';
        }
        echo '</form>';
    }
    
    function survey_render($section = 1, $message = '', &$formdata) {
    
        $usehtmleditor = can_use_html_editor();
    
        if(empty($section)) {
            $section = 1;
        }
    
        $has_choices = $this->type_has_choices();
    
    // load survey title (and other globals)
    //    if (!($survey = get_record('questionnaire_survey', 'id', $survey->id))) {
    //        return(false);
    //    }
    
        $num_sections = count($this->questionsbysec);    /// indexed by section.
        if($section > $num_sections) {
            return(false);  // invalid section
        }
    
    // check to see if there are required questions
        $has_required = $this->has_required($section);
    
    // find out what question number we are on $i
        $i = 1;
        for($j = 2; $j<=$section; $j++) {
            $i += count($this->questionsbysec[$j-1]);
        }
    
        $this->print_survey_start($message, $section, $num_sections, $has_required);

        foreach ($this->questionsbysec[$section] as $question) {
            $question->survey_display($formdata, $i++, $usehtmleditor);
            // process each question
        }
        // end of questions
    
        $this->print_survey_end($section, $num_sections);
    
        return;
    }
    
    function print_survey_start($message, $section, $num_sections, $has_required) {
        echo '
    <table class="headerGraphic">
    <tr>
        <td class="image"></td>
    </tr>
    </table>
    <h2 class="surveyTitle">'.$this->survey->title.'</h2>
    <h3 class="surveySubtitle">'.$this->survey->subtitle.'</h3>';
        if($num_sections>1) {
            $a->page = $section;
            $a->totpages = $num_sections;
            echo '
        <font size="-1" class="surveyPage">'.get_string('pageof', 'questionnaire', $a).'</font>';
        }
        echo '
    <blockquote class="addInfo">'.$this->survey->info.'</blockquote>
    <blockquote class="message">'.$message.'</blockquote>';
        if($has_required) {
            echo '
        <p class="reqQuestion"><font size="-1">'.
                get_string('requiredquestions', 'questionnaire', '<font color="#FF0000">*</font>').'</font></p>';
        }
        echo '
    <div class="mainTable">';
    }
    
    function print_survey_end($section, $num_sections) {
        echo '
    </div>';
        if($num_sections>1) {
            $a->page = $section;
            $a->totpages = $num_sections;
            echo '
        <font size="-1" class="surveyPage">'.get_string('pageof', 'questionnaire', $a).'</font><br>';
        }
    }
    
    function survey_print_render($message = '') {
        $text_input_add = ' readonly="true"';
        $radio_input_add = ' onClick="this.checked=this.defaultChecked;"';
        $sid = $this->sid;
        $usehtmleditor = can_use_html_editor();
        $formdata = data_submitted();
    
        if(empty($section))
            $section = 1;
    
        $has_choices = $this->type_has_choices();
    
        $num_sections = count($this->questionsbysec);
        if($section > $num_sections)
            return(false);  // invalid section
    
        $has_required = $this->has_required();
    
    // find out what question number we are on $i
        $i = 1;
        for($j = 2; $j<=$section; $j++) {
            $i += count($this->questionsbysec[$j-1]);
        }

        print_simple_box_start();
        $this->print_survey_start($message, 1, 1, $has_required);

        /// Print all sections:
        foreach ($this->questionsbysec as $section) {
            foreach ($section as $question) {
                $question->survey_display($formdata, $i++, $usehtmleditor);
            }
        }
        // end of questions
    
        $this->print_survey_end(1, 1);
        print_simple_box_end();
    
        return;
    }

    function survey_update($old_tab, $sdata) {
        global $CFG, $SESSION;
    
        $errstr = '';
    
        // do not need update
        if(empty($old_tab)) {
            return(false);
        }
    
        if (empty($sdata)) {
            $sdata = data_submitted('nomatch');
        }
    
        $f_arr = array();
        $v_arr = array();
    
        // new survey
        if(empty($this->survey->id)) {
            if (isset($sdata->name)) {
            $sdata->name = eregi_replace(
                "[^A-Z0-9]+", "_", trim($sdata->name) );
            $sdata->name = ereg_replace('_$',"",$sdata->name);
            }
    
            // need to fill out at least some info on 1st tab before proceeding
            if(empty($sdata->name) || empty($sdata->title) || empty($sdata->realm)) {
                $tab = "general";
                $errstr = get_string('errrequiredfields', 'questionnaire');
                return(false);
            }
    
            // create a new survey in the database
            $fields = array('name','realm','title','subtitle','email','theme','thanks_page','thank_head','thank_body','info');
            $record = new Object();
            $record->id = 0;
            $record->owner = $sdata->owner;
            foreach($fields as $f) {
                if(isset($sdata->$f)) {
                    $record->$f = $sdata->$f;
                }
            }

            $this->survey->id = insert_record('questionnaire_survey', $record);
            $this->add_survey($this->survey->id);
    
            if(!$this->survey->id) {
                $tab = "general";
                $errstr = get_string('errnewname', 'questionnaire') .' [ :  ]';
                return(false);
            }
    
            return($this->survey->id);
        }
    
        // survey already started
        switch($old_tab) {
            // coming from the general tab ...
            case "general":
                if (isset($sdata->name)) {
                $sdata->name = eregi_replace(
                    "[^A-Z0-9]+", "_", trim($sdata->name) );
                $sdata->name = ereg_replace('_$',"",$sdata->name);
                }
    
                if(empty($sdata->name) || empty($sdata->title)
                        || empty($sdata->realm)) {
                    $errstr = get_string('errrequiredfields', 'questionnaire');
                    return(false);
                }
    
                $fields = array('name','realm','title','subtitle','email','theme','thanks_page','thank_head','thank_body','info');
    
                $name = get_field('questionnaire_survey', 'name', 'id', $this->survey->id);
    
                // trying to change survey name
                if(trim($name) != trim($sdata->name)) {
                    $count = count_records('questionnaire_survey', 'name', $sdata->name);
                    if($count != 0) {
                        $errstr = get_string('errnewname', 'questionnaire');
                        return(false);
                    }
                }
    
                // UPDATE the row in the DB with current values
                $survey_record = new Object();
                $survey_record->id = $this->survey->id;
                foreach($fields as $f) {
                    $survey_record->$f = trim($sdata->$f);
                }
    
                $result = update_record('questionnaire_survey', $survey_record);
                if(!$result) {
                    $errstr = 'Warning, error encountered.' .' [ :  ]';
                    return(false);
                }
                return($this->survey->id);
    
            // coming from the questions tab ...
            case "questions":
                // if the question box is empty ... ignore everything else
                if(empty($sdata->content) && empty($sdata->name)) {
                    return($this->survey->id);
                }
    
                if(empty($sdata->content)) {
                    $dont_clear = 1;
                    $errstr = get_string('errquestiontext', 'questionnaire');
                    return(false);
                }
    
                // constraint: fieldname must be not empty
                //   generate it from the content if empty
                //   validate/repair fieldname
                if(empty($sdata->name)) {
                    $str1 = $sdata->content;
                    do {
                        $str2 = $str1;
                        $str1 = eregi_replace(
                        "(^| )(what|which|why|how|who|where|how|is|are|were|the|a|it|of|do|you|your|please|enter)[ ?]",
                        " ", $str2);
                    } while ($str1 != $str2);
                    $sdata->name = $str1;
                }
                $sdata->name = strtoupper(substr( eregi_replace(
                    "[^A-Z0-9]+", "_", trim($sdata->name)), 0, 30));
                $sdata->name = ereg_replace('_$',"",$sdata->name);
    
                // constraint: question type required
                if(empty($sdata->type_id)) {
                    $dont_clear = 1;
                    $errstr= get_string('errquestiontype', 'questionnaire');
                    return(false);
                }
    
                // constraint: qid must be int or empty
                if($sdata->id == get_string('newfield', 'questionnaire')) {
                    $qid = '';
                }
                $qid = intval($sdata->id);   # curr_q_id
    
                // constraint: can not change between question w/ answer choices and one w/o
                $has_choices = $this->type_has_choices();
                if(!empty($qid)) {
                    $old_type_id = get_field('questionnaire_question', 'type_id', 'survey_id', $this->survey->id, 'id', $qid);
                    if($has_choices[$sdata->type_id] != $has_choices[$old_type_id]) { // trying to change between incompatible question types
                        $tab = "questions";
                        $sdata->type_id = $old_type_id;
                        $dont_clear = 1;
                        $errstr = get_string('errquestionchange', 'questionnaire');
                        return(false);
                    }
                }
    
                // constraint: length must be int
                $sdata->length  = intval($sdata->length) or 0;
    
                // constraint: precise must be int
                $sdata->precise = intval($sdata->precise) or 0;
    
                // defaults for length field
                if(empty($sdata->length) && $sdata->type_id < 50) {
                    $arr = array(
                        0,      // 0: unused
                        0,      // 1: Yes/No
                        20,     // 2: Text Box  (width)
                        60,     // 3: Essay Box (width)
                        0,      // 4: Radio Buttons
                        0,      // 5: Check Boxes (minumum)
                        0,      // 6: Dropdown Box (length)
                        5,      // 7: Rating (# cols)
                        5,      // 8: Rate (# cols)
                        0,      // 9: Date
                        10      // 10: Numeric (digits)
                        );
                    $sdata->length = $arr[$sdata->type_id];
                }
    
                // defaults for precision field
                if(empty($sdata->precise) && $sdata->type_id < 50) {
                    $arr = array(
                        0,      // 0: unused
                        0,      // 1: Yes/No
                        0,      // 2: Text Box
                        5,      // 3: Essay Box (height)
                        10,     // 4: Radio Buttons
                        0,      // 5: Check Boxes (maximum)
                        0,      // 6: Dropdown Box
                        0,      // 7: Rating (use N/A)
                        0,      // 8: Rate (use N/A)
                        0,      // 9: Date
                        0       // 10: Numeric (decimal)
                        );
                    $sdata->precise = $arr[$sdata->type_id];
                }
    
                // UPDATE row in the DB for the current question
                if($qid != '') {
                    $fields = array('name','type_id','length','precise','required','content');
                    $question_record = new Object();
                    $question_record->id = $qid;
                    foreach($fields as $f) {
                        if(isset($sdata->$f))
                            $question_record->$f = trim($sdata->$f);
                    }
                    $result = update_record('questionnaire_question', $question_record);
    
                // INSERT row in the DB for new question
                } else {
                    // set the position to the end
                    $sql = 'SELECT MAX(position) as maxpos FROM '.$CFG->prefix.'questionnaire_question '.
                           'WHERE survey_id = '.$this->survey->id;
                    if ($record = get_record_sql($sql)) {
                        $sdata->position = $record->maxpos + 1;
                    } else {
                        $sdata->position = 1;
                    }
    
                    $sdata->survey_id = $this->survey->id;
                    $fields = array('survey_id','name','type_id','length','precise','required','content','position');
                    $question_record = new Object();
                    foreach($fields as $f) {
                        if(isset($sdata->$f)) {
                            $question_record->$f = trim($sdata->$f);
                        }
                    }
                    $result = insert_record('questionnaire_question', $question_record);
                }
                if($qid == '')
                    $qid = $result;
                $sdata->id = $qid;
                if(!$result) {
                    $dont_clear = 1;
                    $errstr = 'Warning, error encountered.' .' [  ]';
                    return(false);
                }
    
                // UPDATE or INSERT rows for each of the question choices for this question
                if($has_choices[$sdata->type_id]) {
                    $cids = array();
                    $sql = 'SELECT c.id, c.question_id FROM '.$CFG->prefix.'questionnaire_question q, '.$CFG->prefix.'questionnaire_question_choice c WHERE q.id=c.question_id AND q.survey_id='.$this->survey->id;
                    if (!($records = get_records_sql($sql))) {
                        $records = array();
                    }
                    foreach ($records as $record) {
                        array_push($cids, $record->id);
                    }
                    $count = 0;
                    for($i = 1; $i < $sdata->num_choices+1; $i++) {
                        $choice_id      = intval($sdata->{"choice_id_${i}"});
                        $choice_content = trim($sdata->{"choice_content_${i}"});
                        // each of the submitted choices
                        if($choice_id=='' && $choice_content!='') {
                            // insert (new)
                            $choice_record = new Object();
                            $choice_record->question_id = $qid;
                            $choice_record->content = $choice_content;
                            $result = insert_record('questionnaire_question_choice', $choice_record);
                            ++$count;
                        } elseif($choice_id!='' && $choice_content=='') {
                            // delete (old)
                            $result = delete_records('questionnaire_question_choice', 'id', $choice_id);
                        } elseif($choice_id!='' && $choice_content!='' && in_array($choice_id, $cids)) {
                            // update (old)
                            $rsult = set_field('questionnaire_question_choice', 'content', $choice_content, 'id', $choice_id);
                            ++$count;
                        }
                        if($sql != '') {
                            if(!$result) {
                                $dont_clear = 1;
                                $errstr = 'Warning, error encountered.' .' [ :  ]';
                                return(false);
                            }
                        }
                    }
                    if(!$count && !isset($sdata->extra_choices)) {
                        $dont_clear = 1;
                        $errstr = get_string('errquestionoption', 'questionnaire') .
                            ' [ '. get_string('id', 'questionnaire') .': '. $sdata->type_id .' ]';
                        return(false);
                    }
                }
                return($this->survey->id);
    
            case "preview":
                // can not change anything here yet, so no need to update DB.
                return($this->survey->id);
    
            case "order":
                // it updates the DB itself
                return($this->survey->id);
        }
        return($this->survey->id);
    }
    
    /* Creates an editable copy of a survey. */
    function survey_copy($owner) {

        // clear the sid, clear the creation date, change the name, and clear the status
        // Since we're copying a data record, addslashes.
        $survey = addslashes_object($this->survey);

        unset($survey->id);
        $survey->changed = time();
        $survey->owner = $owner;
        $survey->name .= '_copy';
        $survey->status = 0;
    
        // check for 'name' conflict, and resolve
        $i=0;
        $name = $survey->name;
        while (count_records('questionnaire_survey', 'name', $name) > 0) {
            $name = $survey->name.(++$i);
        }
        if($i) {
            $survey->name .= $i;
        }
    
        // create new survey
        if (!($new_sid = insert_record('questionnaire_survey', $survey))) {
            return(false);
        }

        // make copies of all the questions
        $pos=1;
        foreach ($this->questions as $question) {
            $tid = $question->type_id;
            $qid = $question->id;
    
            // fix some fields first
            unset($question->id);
            $question->survey_id = $new_sid;
            $question->position = $pos++;
            $question->name = addslashes($question->name);
            $question->content = addslashes($question->content);
    
            // copy question to new survey
            if (!($new_qid = insert_record('questionnaire_question', $question))) {
                return(false);
            }
    
            foreach ($question->choices as $choice) {
                unset($choice->id);
                $choice->question_id = $new_qid;
                $choice->content = addslashes($choice->content);
                $choice->value = addslashes($choice->value);
                if (!insert_record('questionnaire_question_choice', $choice)) {
                    return(false);
                }
            }
        }
    
        return($new_sid);
    }

    function type_has_choices() {
        $has_choices = array();
    
        if ($records = get_records('questionnaire_question_type', '', '', 'id', 'id,has_choices')) {
            foreach ($records as $record) {
                if($record->has_choices == 'Y') {
                    $has_choices[$record->id]=1;
                } else {
                    $has_choices[$record->id]=0;
                }
            }
        } else {
            $has_choices = array();
        }
    
        return($has_choices);
    }
    
    function array_to_insql($array) {
        if (count($array))
            return("IN (".ereg_replace("([^,]+)","'\\1'",join(",",$array)).")");
        return 'IS NULL';
    }
    
    // ---- RESPONSE LIBRARY
    
    function response_check_required($section, &$formdata) {
        $missing = array(); // array of missing questions
    
        foreach ($this->questionsbysec[$section] as $record) {
          if (($record->required == 'Y') && ($record->deleted == 'N')) {
            $qid = $record->id;
            $tid = $record->type_id;
            $content = $record->content;
    
            if($tid == 8) { // Rank
                foreach ($record->choices as $cid => $choice) {
                    if(!isset($formdata->{'q'.$qid.'_'.$cid})) {
                        $missing[$qid] = $content;
                        break;
                    }
                }
                continue;
            }
            if ($tid == 10 && $formdata->{'q'.$qid} === '0') // Numeric
                continue;
            if(empty($formdata->{'q'.$qid})) {
                $missing[$qid] = $content;
            }
          }
        }
    
        if(count($missing) > 0) {
            // missing required variables
            $message = get_string('missingquestions', 'questionnaire').":<br>\n";
            while(list($qid,$content)=each($missing)) {
//                if($ESPCONFIG['DEBUG'])
//                    $message .= "<!-- ${qid} -->";
                $message .= "${content}<br>\n";
            }
            return($message);
        }
        return('');
    }
    
    function response_delete($rid, $sec = null) {
        if (empty($rid)) {
            return;
        }
    
        if ($sec != null) {
            if ($sec < 1) {
                return;
            }
    
            /* get question_id's in this section */
            $qids = '';
            foreach ($this->questionsbysec[$sec] as $question) {
                if (empty($qids)) {
                    $qids .= ' AND question_id IN ('.$question->id;
                } else {
                    $qids .= ','.$question->id;
                }
            }
            if (!empty($qids)) {
                $qids .= ')';
            } else {
                return;
            }
        } else {
            /* delete all */
            $qids = '';
        }
    
        /* delete values */
        $select = 'response_id = \''.$rid.'\' '.$qids;
        foreach (array('bool', 'single', 'multiple', 'rank', 'text', 'other', 'date') as $tbl) {
            delete_records_select('questionnaire_response_'.$tbl, $select);
        }
    }
    
    function response_import_sec($rid, $sec, &$varr) {

        if ($sec < 1 || !isset($this->questionsbysec[$sec])) {
            return;
        }
        /* get question_id's in this section */
        $ids = array();
        foreach ($this->questionsbysec[$sec] as $question) {
            $ids[] = $question->id;
        }

        $vals = $this->response_select($rid, 'content', $ids);
    
        reset($vals);
        foreach ($vals as $id => $arr) {
            if (isset($arr[0]) && is_array($arr[0])) {
                // multiple
                $varr->{'q'.$id} = array_map('array_pop', $arr);
            } else {
                $varr->{'q'.$id} = array_pop($arr);
            }
        }    
    }
    
    function response_import_all($rid, &$varr) {

        /* get question_id's in this section */
        $ids = array();
        foreach ($this->questions as $question) {
            $ids[] = $question->id;
        }

        $vals = $this->response_select($rid, 'content', $ids);
    
        reset($vals);
        foreach ($vals as $id => $arr) {
            if (isset($arr[0]) && is_array($arr[0])) {
                // multiple
                $varr->{'q'.$id} = array_map('array_pop', $arr);
            } else {
                $varr->{'q'.$id} = array_pop($arr);
            }
        }    
    }
    
    function response_commit($rid) {
        $record = new object;
        $record->id = $rid;
        $record->complete = 'Y';
        $record->submitted = time();
        return update_record('questionnaire_response', $record);
    }
    
    function get_response($username, $rid = 0) {
        $rid = intval($rid);
        if ($rid != 0) {
            // check for valid rid
            $fields = 'id, username';
            $select = 'id = '.$rid.' AND survey_id = '.$this->sid.' AND username = \''.$username.'\' AND complete = \'N\'';
            return (get_record_select('questionnaire_response', $select, $fields) !== false) ? $rid : '';
    
        } else {
            // find latest in progress rid
            $select = 'survey_id = '.$this->sid.' AND complete = \'N\' AND username = \''.$username.'\'';
            if ($records = get_records_select('questionnaire_response', $select, 'submitted DESC',
                                              'id,survey_id', 0, 1)) {
                $rec = reset($records);
                return $rec->id;
            } else {
                return '';
            }
        }
    }
    
    function response_select_max_sec($rid) {
        $pos = $this->response_select_max_pos($rid);
        $select = 'survey_id = \''.$this->sid.'\' AND type_id = 99 AND position < '.$pos.' AND deleted = \'N\'';
        $max = count_records_select('questionnaire_question', $select) + 1;
    
        return $max;
    }
    
    function response_select_max_pos($rid) {
        $max = 0;
        
        global $CFG;
        foreach (array('bool', 'single', 'multiple', 'rank', 'text', 'other', 'date') as $tbl) {
            $sql = 'SELECT MAX(q.position) as num FROM '.$CFG->prefix.'questionnaire_response_'.$tbl.' a, '.
                                                         $CFG->prefix.'questionnaire_question q '.
                   'WHERE a.response_id = \''.$rid.'\' AND '.
                   'q.id = a.question_id AND '.
                   'q.survey_id = \''.$this->sid.'\' AND '.
                   'q.deleted = \'N\'';
            if ($record = get_record_sql($sql)) {
                $max = (int)$record->num;
            }
        }
        return $max;
    }
    
/* {{{ proto array response_select_name(int survey_id, int response_id, array question_ids)
   A wrapper around response_select(), that returns an array of
   key/value pairs using the field name as the key.
 */
    function response_select_name($rid, $qids = null) {
        $res = $this->response_select($rid, 'type_id,name', $qids);
        $nam = array();
        reset($res);
        while(list($qid, $arr) = each($res)) {
            $key = null;
            $val = null;
            if (strstr($qid, '_')) {
                // rank or other
                list($qid, $sub) = explode('_', $qid);
                if ($arr[0] != 8)
                    continue; // other
    
                // rank
                $str1 = $arr[2];
                do {
                    $str2 = $str1;
                    $str1 = eregi_replace(
                        "(^| )(what|which|why|how|who|where|how|is|are|were|the|a|it|of|do|you|your|please|enter)[ ?]",
                        " ", $str2);
                } while ($str1 != $str2);
                $str1 = trim(strtoupper(eregi_replace(
                    "[^A-Z0-9]+", " ", $str1)));
                $str1 = ereg_replace(' +','_',$str1);
                $arr[1] .= "_$str1";
                $nam[$arr[1]] = $arr[3];
                continue;
            }
            if (is_array($arr[0])) {
                // mutiple
                $key = $arr[0][1];
                $val = array();
                foreach ($arr as $subarr) {
                    if (ereg("^!other", $subarr[2])) {
                        $tmpv = preg_replace(array("/^!other=/","/^!other/"),
                                array('', 'Other'), $subarr[2]);
                        $tmp = preg_replace("/^other/", $qid, $subarr[3]);
                        if (isset($res[$tmp]))
                            $tmpv .= ': '. $res[$tmp][2];
                        array_push($val, $tmpv);
                    } else {
                        array_push($val, $subarr[2]);
                    }
                }
            } else {
                $key = $arr[1];
                if (ereg("^!other", $arr[2])) {
                    $val = preg_replace(array("/^!other=/","/^!other/"),
                            array('', 'Other'), $arr[2]);
                    $tmp = preg_replace("/^other/", $qid, $arr[3]);
                    if (isset($res[$tmp]))
                        $val .= ': '. $res[$tmp][2];
                } else {
                    $val = $arr[2];
                }
            }
            $nam[$key] = $val;
        }
        return $nam;
    }

    function response_send_email($rid, $userid=false) {
        global $CFG;
        require_once($CFG->libdir.'/phpmailer/class.phpmailer.php');
    
        if (!$userid) {
            $userid = isset($this->survey->username) ? $this->survey->username : get_string('anonymous', 'questionnaire');
        }
    
        if ($record = get_record('questionnaire_survey', 'id', $this->survey->id)) {
            $name = $record->name;
            $email = $record->email;
        } else {
            $name = '';
            $email = '';
        }
    
        if(empty($email)) {
            return(false);
        }
    
        $answers = $this->response_select_compact($rid);
        $qsep = ' : ';
        $isep = ',';
        $end = "\n";
    
        $user = array(
                'survey.id' => $this->survey->id,
                'survey.name' => $name,
                'survey.response' => $rid,
            );
    
        $user['user.username'] = $userid;
    
        $subject = get_string('surveyresponse', 'questionnaire') .": $name [$rid]";
        $body = '';
        reset($user);
        while (list($k, $v) = each($user))
            $body .= $k . $qsep . $v . $end;
    
        $headers = "From: \"phpESP ".
            addslashes($ESPCONFIG['version']) .
            "\" <phpesp@". $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME'] .">\n";
        $headers .= "X-Sender: <phpesp@". $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME'] .">\n";
        $headers .= "X-Mailer: phpESP\n";
        $headers .= "Return-Path: <". $GLOBALS['HTTP_SERVER_VARS']['SERVER_ADMIN'] ."@".
            $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME'] . ">\n";
    
        reset($answers);
        while($arr = array_shift($answers)) {
            unset($x);
            if (count($arr) > 2)
                list($k, $v, $x) = $arr;
            else
                list($k, $v) = $arr;
            if (is_array($v))
                $v = implode($isep, $v);
            if (isset($x)) {
                if (is_array($x))
                    $v .= ' (' . implode($isep, $x) . ')';
                else
                    $v .= ' = ' . $x;
            }
            $body .= $k . $qsep . $v . $end;
        }
    
        $mail = new PHPMailer();
    
        $mail->Version = "Moodle $CFG->version";           // mailer version
        $mail->PluginDir = "$CFG->libdir/phpmailer/";      // plugin directory (eg smtp plugin)
    
        if (current_language() != "en") {
            $mail->CharSet = get_string("thischarset");
        }
    
        if ($CFG->smtphosts == "qmail") {
            $mail->IsQmail();                              // use Qmail system
    
        } else if (empty($CFG->smtphosts)) {
            $mail->IsMail();                               // use PHP mail() = sendmail
    
        } else {
            $mail->IsSMTP();                               // use SMTP directly
            if ($CFG->debug > 7) {
                echo "<pre>\n";
                $mail->SMTPDebug = true;
            }
            $mail->Host = "$CFG->smtphosts";               // specify main and backup servers
    
            if ($CFG->smtpuser) {                          // Use SMTP authentication
                $mail->SMTPAuth = true;
                $mail->Username = $CFG->smtpuser;
                $mail->Password = $CFG->smtppass;
            }
        }
    
        $mail->Sender   = $CFG->noreplyaddress;
        $mail->From     = $CFG->noreplyaddress;
        $mail->FromName = $name;
        $mail->Subject  =  $subject;
        $mail->AddAddress($email);
        $mail->WordWrap = 79;                               // set word wrap
    
        $mail->IsHTML(true);
        $mail->Encoding = "quoted-printable";           // Encoding to use
        $mail->Body    =  $body;
        $mail->AltBody =  "\n$body\n";
    
        if ($mail->Send()) {
            return true;
        } else {
            return false;
        }
    }
    
    function response_select_compact($rid, $qids = null) {
        $res = $this->response_select($rid, 'type_id', $qids);
        $cpq = array();
        reset($res);
        while(list($qid, $arr) = each($res)) {
            if (strstr($qid, '_')) {
                // rank or other
                if ($arr[0] == 8) {
                    // rank
                    $cpq[] = array($qid, $arr[2], array($arr[1]));
                } else {
                    // other
                    $cpq[] = array($qid, $arr[1]);
                }
            } elseif (is_array($arr[0])) {
                // multiple
                $cpq[] = array($qid,
                        array_map(create_function('$a', 'return $a[2];'), $arr),
                        array_map(create_function('$b', 'return $b[1];'), $arr));
            } else {
                if ($arr[0] == 4 || $arr[0] == 6)
                    $cpq[] = array($qid, $arr[2], array($arr[1]));
                else
                    $cpq[] = array($qid, $arr[2]);
            }
        }
        return $cpq;
    }
    
    function response_insert($sid, $section, $rid, $userid, &$formdata) {
        global $CFG;
    
        if(empty($rid)) {
            // create a uniqe id for this response
            $record = new object;
            $record->survey_id = $sid;
            $record->username = $userid;
            $rid = insert_record('questionnaire_response', $record);
        }
    
        if (!empty($this->questionsbysec[$section])) {
            foreach ($this->questionsbysec[$section] as $question) {
                $question->insert_response($rid, $formdata);
            }
        }
        return($rid);
    }
    
    function response_select($rid, $col = null, $qids = null) {
        global $CFG;

        $sid = $this->survey->id;    
        $values = array();
    
        if ($col == null) {
            $col = '';
        }
        if (!is_array($col) && !empty($col)) {
            $col = explode(',', preg_replace("/\s/",'', $col));
        }
        if (is_array($col) && count($col) > 0) {
            $col = ',' . implode(',', array_map(create_function('$a','return "q.$a";'), $col));
        }
    
        if ($qids == null) {
            $qids = '';
        } elseif (is_array($qids)) {
            $qids = 'AND a.question_id ' . $this->array_to_insql($qids);
        } elseif (intval($qids) > 0) {
            $qids = 'AND a.question_id = ' . intval($qids);
        } else {
            $qids = '';
        }
    
        // --------------------- response_bool ---------------------
        $sql = 'SELECT q.id '.$col.', a.choice_id '.
               'FROM '.$CFG->prefix.'questionnaire_response_bool a, '.$CFG->prefix.'questionnaire_question q '.
               'WHERE a.response_id=\''.$rid.'\' AND a.question_id=q.id '.$qids;
        if ($records = get_records_sql($sql)) {
            foreach ($records as $row) {
                /// Change the data object into a numerically indexed array.
    ///***  Because of the way the following code works, we need to remove the non-numeric keys of the array.
    ///***  The problem is that the Moodle db calls returns hask and numeric keys for each value (2 per).
                $row = (array)$row;
                $qid = array_shift($row);
                foreach ($row as $key => $val) {
                    if (!is_numeric($key)) unset($row[$key]);
                }
                $val = array_pop($row);
                $values[$qid] = $row;
                array_push($values["$qid"], ($val == 'Y') ? get_string('yes') : get_string('no'), $val);
            }
        }
    
        // --------------------- response_single ---------------------
        $sql = 'SELECT q.id '.$col.',c.content as ccontent,c.id as cid '.
               'FROM '.$CFG->prefix.'questionnaire_response_single a, '.
                       $CFG->prefix.'questionnaire_question q, '.
                       $CFG->prefix.'questionnaire_question_choice c '.
               'WHERE a.response_id=\''.$rid.'\' AND a.question_id=q.id AND a.choice_id=c.id '.$qids;
        if ($records = get_records_sql($sql)) {
            foreach ($records as $row) {
                /// Change the data object into a numerically indexed array.
    ///***  Because of the way the following code works, we need to remove the non-numeric keys of the array.
    ///***  The problem is that the Moodle db calls returns hask and numeric keys for each value (2 per).
                $row = (array)$row;
                $qid = array_shift($row);
                foreach ($row as $key => $val) {
                    if (!is_numeric($key)) unset($row[$key]);
                }
                $c = count($row);
                $val = $row[$c - 2];
                if (ereg('^!other', $val))
                    $row[$c - 1] = 'other_' . $row[$c - 1];
                else
                    settype($row[$c - 1], 'integer');
                $values[$qid] = $row;
            }
        }
    
        // --------------------- response_multiple ---------------------
        $sql = 'SELECT a.id as aid, q.id as qid '.$col.',c.content as ccontent,c.id as cid '.
               'FROM '.$CFG->prefix.'questionnaire_response_multiple a, '.
                       $CFG->prefix.'questionnaire_question q, '.$CFG->prefix.'questionnaire_question_choice c '.
               'WHERE a.response_id=\''.$rid.'\' AND a.question_id=q.id AND a.choice_id=c.id '.$qids.' '.
               'ORDER BY a.id,a.question_id,c.id';
        $arr = array();
        $tmp = null;
        if ($records = get_records_sql($sql)) {
            foreach ($records as $row) {
                /// Change the data object into a numerically indexed array.
    ///***  Because of the way the following code works, we need to remove the non-numeric keys of the array.
    ///***  The problem is that the Moodle db calls returns hask and numeric keys for each value (2 per).
                $row = (array)$row;
                array_shift($row); // get rid of the answer id.
                $qid = array_shift($row);
                foreach ($row as $key => $val) {
                    if (!is_numeric($key)) unset($row[$key]);
                }
                $c = count($row);
                $val = $row[$c - 2];
                if (ereg('^!other', $val))
                    $row[$c - 1] = 'other_' . $row[$c - 1];
                else
                    settype($row[$c - 1], 'integer');
                if($tmp == $qid) {
                    $arr[] = $row;
                    continue;
                }
                if($tmp != null)
                    $values["$tmp"]=$arr;
                $tmp = $qid;
                $arr = array($row);
            }
        }
        if($tmp != null)
            $values["$tmp"]=$arr;
        unset($arr);
        unset($tmp);
        unset($row);
    
        // --------------------- response_other ---------------------
        $sql = 'SELECT q.id,c.id as cid '.$col.',a.response as aresponse '.
               'FROM '.$CFG->prefix.'questionnaire_response_other a, '.$CFG->prefix.'questionnaire_question q, '.
                       $CFG->prefix.'questionnaire_question_choice c '.
               'WHERE a.response_id=\''.$rid.'\' AND a.question_id=q.id AND a.choice_id=c.id '.$qids.' '.
               'ORDER BY a.question_id,c.id';
        if ($records = get_records_sql($sql)) {
            foreach ($records as $row) {
                /// Change the data object into a numerically indexed array.
    ///***  Because of the way the following code works, we need to remove the non-numeric keys of the array.
    ///***  The problem is that the Moodle db calls returns hask and numeric keys for each value (2 per).
                $row = (array)$row;
                $qid = array_shift($row);
                foreach ($row as $key => $val) {
                    if (!is_numeric($key)) unset($row[$key]);
                }
                $cid = array_shift($row);
                array_push($row, $row[count($row) - 1]);
                $values["${qid}_${cid}"] = $row;
            }
        }
    
            // --------------------- response_rank ---------------------
        $sql = 'SELECT a.id as aid, q.id AS qid, c.id AS cid '.$col.',c.content as ccontent,a.rank as arank '.
               'FROM '.$CFG->prefix.'questionnaire_response_rank a, '.
                       $CFG->prefix.'questionnaire_question q, '.
                       $CFG->prefix.'questionnaire_question_choice c '.
               'WHERE a.response_id=\''.$rid.'\' AND a.question_id=q.id AND a.choice_id=c.id '.$qids.' '.
               'ORDER BY aid, a.question_id,c.id';
        if ($records = get_records_sql($sql)) {
            foreach ($records as $row) {
                /// Change the data object into a numerically indexed array.
    ///***  Because of the way the following code works, we need to remove the non-numeric keys of the array.
    ///***  The problem is that the Moodle db calls returns hash and numeric keys for each value (2 per).
                $row = (array)$row;
                array_shift($row); // get rid of the answer id.
    
                /// Next two are 'qid' and 'cid', each with numeric and hash keys.
                $qid = array_shift($row);
                array_shift($row);
                $qid .= '_'.array_shift($row);
                array_shift($row);
    
                foreach ($row as $key => $val) {
                    if (!is_numeric($key)) unset($row[$key]);
                }
                settype($row[count($row) - 1], 'integer');
                $values[$qid] = $row;
            }
        }
    
            // --------------------- response_text ---------------------
        $sql = 'SELECT q.id '.$col.',a.response as aresponse '.
               'FROM '.$CFG->prefix.'questionnaire_response_text a, '.$CFG->prefix.'questionnaire_question q '.
               'WHERE a.response_id=\''.$rid.'\' AND a.question_id=q.id '.$qids;
        if ($records = get_records_sql($sql)) {
            foreach ($records as $row) {
                /// Change the data object into a numerically indexed array.
    ///***  Because of the way the following code works, we need to remove the non-numeric keys of the array.
    ///***  The problem is that the Moodle db calls returns hask and numeric keys for each value (2 per).
                $row = (array)$row;
                $qid = array_shift($row);
                foreach ($row as $key => $val) {
                    if (!is_numeric($key)) unset($row[$key]);
                }
                $values["$qid"]=$row;
                $val = array_pop($values["$qid"]);
                array_push($values["$qid"], $val, $val);
            }
        }
    
            // --------------------- response_date ---------------------
        $sql = 'SELECT q.id '.$col.',a.response as aresponse '.
               'FROM '.$CFG->prefix.'questionnaire_response_date a, '.$CFG->prefix.'questionnaire_question q '.
               'WHERE a.response_id=\''.$rid.'\' AND a.question_id=q.id '.$qids;
        if ($records = get_records_sql($sql)) {
            foreach ($records as $row) {
                /// Change the data object into a numerically indexed array.
    ///***  Because of the way the following code works, we need to remove the non-numeric keys of the array.
    ///***  The problem is that the Moodle db calls returns hask and numeric keys for each value (2 per).
                $row = (array)$row;
                $qid = array_shift($row);
                foreach ($row as $key => $val) {
                    if (!is_numeric($key)) unset($row[$key]);
                }
                $values["$qid"]=$row;
                $val = array_pop($values["$qid"]);
                array_push($values["$qid"], $val, $val);
            }
        }
    
            // --------------------- return ---------------------
            uksort($values, 'questionnaire_response_key_cmp');
            return($values);
    }
    
    function response_goto_thankyou() {
        global $CFG;

        $select = 'id = '.$this->survey->id;
        $fields = 'thanks_page,thank_head,thank_body';
        if ($result = get_record_select('questionnaire_survey', $select, $fields)) {
            $thank_url = $result->thanks_page;
            $thank_head = $result->thank_head;
            $thank_body = $result->thank_body;
        } else {
            $thank_url = '';
            $thank_head = '';
            $thank_body = '';
        }
        if(!empty($thank_url)) {
            if(!headers_sent()) {
                header("Location: $thank_url");
                exit;
            }
    ?>
    <script language="JavaScript">
    <!--
    window.location="<?php echo($thank_url); ?>"
    //-->
    </script>
    <noscript>
    <h2 class="thankhead">Thank You for completing this survey.</h2>
    <blockquote class="thankbody">Please click
    <a class="returnlink" href="<?php echo($thank_url); ?>">here</a>
    to continue.</blockquote>
    </noscript>
    <?php
            exit;
        }
    
        if(empty($thank_body) && empty($thank_head)) {
            $thank_body = $ESPCONFIG['thank_body'];
            $thank_head = $ESPCONFIG['thank_head'];
        }
    ?>
    <h2 class="thankhead"><?php echo($thank_head); ?></h2>
    <blockquote class="thankbody"><?php echo($thank_body); ?></blockquote>
    <a class="returnlink" href="<?php echo($CFG->wwwroot.'/course/view.php?id='.$this->course->id); ?>">Return</a>
    <?php
        return;
    }
    
    function response_goto_saved($url) {
    ?>
    <blockquote class="thankbody">
    <?php print_string('savedprogress', 'questionnaire'); ?></blockquote>
    <a class="returnlink" href="<?php echo $url; ?>"><?php print_string('resumesurvey', 'questionnaire'); ?></a>
    <?php
        return;
    }


    /// Survey Results Methods

    function survey_results_navbar($curr_rid, $userid=false) {
        global $CFG;

        $select = 'survey_id='.$this->survey->id.' AND complete = \'Y\'';
        if ($userid !== false) {
        	$select .= ' AND username = \''.$userid.'\'';
        }
        if (!($responses = get_records_select('questionnaire_response', $select, 'id', 'id,survey_id'))) {
            return;
        }
        $total = count($responses);
    
        $rids = array();
        $i = 0;
        $curr_pos = -1;
        foreach ($responses as $response) {
            array_push($rids, $response->id);
            if ($response->id == $curr_rid) {
                $curr_pos = $i;
            }
            $i++;
        }
    
        $prev_rid = ($curr_pos > 0) ? $rids[$curr_pos - 1] : null;
        $next_rid = ($curr_pos < $total - 1) ? $rids[$curr_pos + 1] : null;
        $rows_per_page = 1;
        $pages = ceil($total / $rows_per_page);
    
        $url = $CFG->wwwroot.'/mod/questionnaire/report.php?where=results&sid='.$this->survey->id;
    
        $mlink = create_function('$i,$r', 'return "<a href=\"'.$url.'&rid=$r\">$i</a>";');
    
        $linkarr = array();
    
        $display_pos = 1;
        if ($prev_rid != null) {
            array_push($linkarr, "<a href=\"$url&rid=$prev_rid\">".get_string('previous', 'questionnaire').'</a>');
        }
        for ($i = 0; $i < $curr_pos; $i++) {
            array_push($linkarr, "<a href=\"$url&rid=".$rids[$i]."\">$display_pos</a>");
            $display_pos++;
        }
        array_push($linkarr, "<a href=\"$url&rid=$curr_rid\"><b>$display_pos</b></a>");
        for (++$i; $i < $total; $i++) {
            $display_pos++;
            array_push($linkarr, "<a href=\"$url&rid=".$rids[$i]."\">$display_pos</a>");
        }
        if ($next_rid != null)
            array_push($linkarr, "<a href=\"$url&rid=$next_rid\">".get_string('next', 'questionnaire').'</a>');
    
        ?>
            <center>
            <h2><?php print_string('navigateirs', 'questionnaire'); ?></h2>
            <br />
            <?php echo implode(' | ', $linkarr); ?>
            <br />
            </center>
            <?php
    }
    /* }}} */
    
    /* {{{ proto string survey_results(int survey_id, int precision, bool show_totals, int question_id, array choice_ids, int response_id)
        Builds HTML for the results for the survey. If a
        question id and choice id(s) are given, then the results
        are only calculated for respodants who chose from the
        choice ids for the given question id.
        Returns empty string on sucess, else returns an error
        string. */
    function survey_results($precision = 1, $showTotals = 1, $qid = '', $cids = '', $rid = '', $guicross='', $uid=false) {
        global $CFG;
    
        $bg = '';
        if(empty($precision)) {
            $precision  = 1;
        }
        if($showTotals === '') {
            $showTotals = 1;
        }
            
        if(is_int($cids)) {
            $cids = array($cids);
        }
        if(is_string($cids)) {
            $cids = split(" ",$cids); // turn space seperated list into array
        }
    
        // set up things differently for cross analysis
        $cross = !empty($qid);
        if($cross) {
            if(is_array($cids) && count($cids)>0) {
                $cidstr = $this->array_to_insql($cids);
            } else {
                $cidstr = '';
            }
        }
    
        // build associative array holding whether each question
        // type has answer choices or not and the table the answers are in
        /// TO DO - FIX BELOW TO USE STANDARD FUNCTIONS
        $has_choices = array();
        $response_table = array();
        if (!($types = get_records('questionnaire_question_type', '', '', 'id', 'id,has_choices,response_table'))) {
            $errmsg = sprintf('%s [ %s: question_type ]',
                    'Error system table corrupt.', 'Table');
            return($errmsg);
        }
        foreach ($types as $type) {
            $has_choices[$type->id]=$type->has_choices;
            $response_table[$type->id]=$type->response_table;
        }

        // load survey title (and other globals)
        if (empty($this->survey)) {
            $errmsg = 'Error opening survey.' ." [ ID:${sid} R:";
            return($errmsg);
        }
    
        if (empty($this->questions)) {
            $errmsg = 'Error opening survey.' .' '. 'No questions found.' ." [ ID:${sid} ]";
            return($errmsg);
        }
    
        // find out more about the question we are cross analyzing on (if any)
        if($cross) {
            $crossTable = $response_table[get_field('questionnaire_question', 'type_id', 'id', $qid)];
            if(!in_array($crossTable, array('response_single','response_bool','response_multiple'))) {
                $errmsg = 'Error cross-analyzing. Question not valid type.' .' [ '. 'Table' .": ${crossTable} ]";
                return($errmsg);
            }
        }
    
    // find total number of survey responses
    // and relevant response ID's
        if (!empty($rid)) {
            $rids = $rid;
            if (is_array($rids)) {
                $ridstr = "IN (" . ereg_replace("([^,]+)","'\\1'", join(",", $rids)) .")";
                $navbar = false;
            } else {
                $ridstr = "= '${rid}'";
                $navbar = true;
            }
            $total = 1;
        } else {
        	$navbar = false;
            $sql = "";
            if($cross) {
                if(!empty($cidstr))
                    $sql = "SELECT A.response_id, R.id
                              FROM ".$CFG->prefix.'questionnaire_'.$crossTable." A,
                                   ".$CFG->prefix."questionnaire_response R
                             WHERE A.response_id=R.id AND
                                   R.complete='Y' AND
                                   A.question_id='${qid}' AND
                                   A.choice_id ${cidstr}
                             ORDER BY A.response_id";
                else
                    $sql = "SELECT A.response_id, R.id
                              FROM ".$CFG->prefix.'questionnaire_'.$crossTable." A,
                                   ".$CFG->prefix."questionnaire_response R
                             WHERE A.response_id=R.id AND
                                   R.complete='Y' AND
                                   A.question_id='${qid}' AND
                                   A.choice_id = 0
                             ORDER BY A.response_id";
            } else if ($uid !== false) {
                $sql = "SELECT r.id, r.survey_id
                          FROM ".$CFG->prefix."questionnaire_response r
                         WHERE r.survey_id='{$this->survey->id}' AND
                               r.username = $uid AND
                               r.complete='Y'
                         ORDER BY r.id";
            } else {
                $sql = "SELECT R.id, R.survey_id
                          FROM ".$CFG->prefix."questionnaire_response R
                         WHERE R.survey_id='{$this->survey->id}' AND
                               R.complete='Y'
                         ORDER BY R.id";
            }
            if (!($rows = get_records_sql($sql))) {
                $errmsg = 'Error opening survey.' ." [ ID:${sid} ] [ ";
                return($errmsg);
            }
            $total = count($rows);
            if($total < 1) {
                $errmsg = 'Error opening survey.' .' '. 'No responses found.' ." [ ID:${sid} ]";
                    return($errmsg);
            }
    
            $rids = array();
            foreach ($rows as $row) {
                array_push($rids, $row->id);
            }
            // create a string suitable for inclusion in a SQL statement
            // containing the desired Response IDs
            // ex: "('304','311','317','345','365','370','403','439')"
            $ridstr = "IN (" . ereg_replace("([^,]+)","'\\1'", join(",", $rids)) .")";
        }
        
        if ($navbar) {
            // show response navigation bar
            $this->survey_results_navbar($rid);
        }
    
    ?>
    <h2><?php echo($this->survey->title); ?></h2>
    <h3><?php echo($this->survey->subtitle); ?></h3>
    <blockquote><?php echo($this->survey->info); ?></blockquote>
    <?php
        if($cross) {
            echo("<blockquote>" ._('Cross analysis on QID:') ." ${qid}</blockquote>\n");
        }
    ?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <?php
        $i=0; // question number counter
        foreach ($this->questions as $question) {
            // process each question
            $totals = $showTotals;
    
            if ($question->type_id == 99) {
                echo("<tr><td><hr></td></tr>\n");
                continue;
            }
            if ($question->type_id == 100) {
                echo("<tr><td>". $question->content ."</td></tr>\n");
                continue;
            }
    
            if($bg != '#eeeeee')    $bg = '#eeeeee';
            else                    $bg = '#ffffff';
    
    ?>
            <tr xbgcolor="<?php echo($bg); ?>">  
            <td>
    <?php
            if ($question->type_id < 50) {
                if (!empty($guicross)){
                    echo ('<input type="hidden" name="where" value="results" />');
                    echo ('<input type="hidden" name="sid" value="'.$this->survey->id.'" />');
                    echo ("\n<table width=\"90%\" border=\"0\">\n");
                    echo ("<tbody>\n");
                    echo ("   <tr>\n");
                    echo ("      <td width=\"34\" height=\"31\" bgcolor=\"khaki\">\n");
                    if ($question->type_id ==1 || $question->type_id ==4 || $question->type_id ==5 || $question->type_id ==6){
                        echo ("<div align=\"center\">\n");
                        echo ("   <input type=\"radio\" name=\"qid\" value=\"".$qid."\" />\n");
                        echo ("</div>\n");
                    }
                    echo ("</td>\n");
                    echo ("<td width=\"429\" bgcolor=\"#CCCCCC\">\n");
                } //end if empty($guicross)
                echo ("<A NAME=\"Q".++$i."\"><b>".$i.".</b></A>\n");
                echo ("<b>".$question->content."</b>\n");
                if (!empty($guicross)){
                    echo ("</td>\n");
                    echo ("<td width=\"33\" bgcolor=\"#CC0000\">\n");
                    if ($question->type_id ==1 || $question->type_id ==4 || $question->type_id ==5 || $question->type_id ==6){
                        echo ("<div align=\"center\">\n");
                        echo ("<input type=\"radio\" name=\"qidr\" value=\"".$qid."\" />\n");
                        echo ("</div>\n");
                    }
                    echo ("</td>\n");
                    echo ("<td width=\"32\" bgcolor=\"#0099FF\">\n");
                    if ($question->type_id ==1 || $question->type_id ==4 || $question->type_id ==5 || $question->type_id ==6){
                        echo ("<div align=\"center\">\n");
                        echo ("<input type=\"radio\" name=\"qidc\" value=\"".$qid."\" />\n");
                        echo ("</div>\n");
                    }
                    echo ("</td>\n");
                    echo ("</tr>\n");
                    echo ("</tbody>\n");
                    echo ("</table>\n");
                } //end if empty($guicross)
            } //end if ($question->type_id  < 50)
    
    $counts = array();
    
    // ---------------------------------------------------------------------------
    if (!empty($guicross) && $question["result_id"] == 1){
        $this->mkcrossformat($counts,$qid,$question->type_id );
    } else {
        $question->display_results($rids);
    } //end if
    ?>
            </td>
        </tr>
    <?php } // end while ?>
    </table>
    <?php
        return;
    }

/* {{{ proto array survey_generate_csv(int survey_id)
    Exports the results of a survey to an array.
    */
    function generate_csv() {
        global $CFG;
    
        $output = array();
    
        $columns = array(
                'RESPONSE',
                'SUBMITTED',
                'COURSENAME',
                'GROUPNAME',
                'UID',
                'FULLNAME',
                'USERNAME',
            );
    
        $types = array(
                0,
                0,
                1,
                1,
                0,
                1,
                1,
            );
    
        $numcols = 7;
    
        $arr = array();
    
        $id_to_csv_map = array(
            '0',    // 0: unused
            '1',    // 1: bool -> boolean
            '1',    // 2: text -> string 
            '1',    // 3: essay -> string
            '1',    // 4: radio -> string
            '1',    // 5: check -> string
            '1',    // 6: dropdn -> string
            '0',    // 7: rating -> number
            '2',    // 8: rate -> number
            '1',    // 9: date -> string
            '0'     // 10: numeric -> number
        );
    
        if (!$survey = get_record('questionnaire_survey', 'id', $this->survey->id)) {
            error ('Survey not found!');
        }
    
        $select = 'survey_id = '.$this->survey->id.' AND deleted = \'N\' AND type_id < 50';
        $fields = 'id,name,type_id';
        if (!($records = get_records_select('questionnaire_question', $select, 'position', $fields))) {
            $records = array();
        }
        $numcols += count($records);
        global $CFG;
        foreach ($records as $record) {
            $qid = $record->id;
            $col = $record->name;
            $type = $record->type_id;
            if ($type == 8) {
                /* rate */
                $sql = "SELECT c.id as cid, q.id, q.name, c.content 
                FROM ".$CFG->prefix."questionnaire_question q ".
                'LEFT JOIN '.$CFG->prefix."questionnaire_question_choice c ON question_id = q.id ".
                'WHERE q.id = '.$qid;
                if (!($records2 = get_records_sql($sql))) {
                    $records2 = array();
                }
                $numcols += count($records2) - 1;
                foreach ($records2 as $record2) {
                    $col = $record2->name.' '.$record2->content;
                    $str1 = $col;
                    do {
                        $str2 = $str1;
                        $str1 = eregi_replace(
                        "(^| )(what|which|why|how|who|where|how|is|are|were|the|a|it|of|do|you|your|please|enter)[ ?]",
                        " ", $str2);
                    } while ($str1 != $str2);
                    $col = $str1;
                    $col = trim(strtoupper(eregi_replace(
                        "[^A-Z0-9]+", " ", $col)));
                    $col = ereg_replace(' +','_',$col);
                    array_push($columns, $col);
                    array_push($types, $id_to_csv_map[$type]);
                }
            } else {
                array_push($columns, $col);
                array_push($types, $id_to_csv_map[$type]);
            }
        }
    
        $num = 0;
    
        array_push($output, $columns);
    
        $select = 'survey_id = '.$this->survey->id.' AND complete=\'Y\'';
        $fields = 'id,submitted,username';
        if (!($records = get_records_select('questionnaire_response', $select, 'submitted', $fields))) {
            $records = array();
        }
        foreach ($records as $record) {
            // get the response
            $response = $this->response_select_name($record->id);
    
            $qid       = $record->id;
            $submitted = $record->submitted;
            $username  = $record->username;
    
            /// Moodle:
            //  Get the course name that this questionnaire belongs to.
            if ($survey->realm != 'public') {
                $courseid = $this->course->id;
                $coursename = $this->course->fullname;
            } else {
                /// For a public questionnaire, look for the course that used it.
                $sql = 'SELECT q.id, q.course, c.fullname '.
                       'FROM '.$CFG->prefix.'questionnaire q, '.$CFG->prefix.'questionnaire_attempts qa, '.
                            $CFG->prefix.'course c '.
                       'WHERE qa.rid = '.$qid.' AND q.id = qa.qid AND c.id = q.course';
                if ($record = get_record_sql($sql)) {
                    $courseid = $record->course;
                    $coursename = $record->fullname;
                } else {
                    $courseid = $this->course->id;
                    $coursename = $this->course->fullname;
                }
            }
    
            /// Moodle:
            //  If the username is numeric, try it as a Moodle user id.
            if (is_numeric($username)) {
                if ($user = get_record('user', 'id', $username)) {
                    $uid = $username;
                    $fullname = fullname($user);
                    $username = $user->username;
                } else {
                    $uid = '';
                    $fullname = '- Unknown -';
                    $username = '';
                }
            } else {
                $uid = '';
                $fullname = '- Unknown -';
                $username = '';
            }
    
            /// Moodle:
            //  Determine if the user is a member of a group in this course or not.
            if ($groups = get_groups($courseid, $uid)) {
                $group = current($groups);
                $groupname = $group->name;
            } else {
                $groupname = get_string('no') . ' ' . get_string('group');
            }
    
            $arr = array();
            array_push($arr, $qid);
            array_push($arr, $submitted);
            array_push($arr, $coursename);
            array_push($arr, $groupname);
            array_push($arr, $uid);
            array_push($arr, $fullname);
            array_push($arr, $username);
    
            // merge it
            for($i = 7; $i < $numcols; $i++) {
                if (isset($response[$columns[$i]]) &&
                        is_array($response[$columns[$i]]))
                    $response[$columns[$i]] = join(',', $response[$columns[$i]]);
                switch ($types[$i]) {
                case 2: /* rate */
                    /// Need to add '1' to each response value, or 'n/a' if -1.
                    if (isset($response[$columns[$i]])) {
                        if ($response[$columns[$i]] == -1) {
                            $response[$columns[$i]] = 'N/A';
                        } else {
                            $response[$columns[$i]]++;
                        }
                        array_push($arr, $response[$columns[$i]]);
                    } else {
                        array_push($arr, '');
                    }
                    break;
                case 1: /* string */
                    if (isset($response[$columns[$i]])) {
                        /* Excel seems to allow "\n" inside a quoted string, but
                         * "\r\n" is used as a record separator and so "\r" may
                         * not occur within a cell. So if one would like to preserve
                         * new-lines in a response, remove the "\n" from the
                         * regex below.
                         */
                        $response[$columns[$i]] = ereg_replace("[\r\n\t]", ' ', $response[$columns[$i]]);
                        $response[$columns[$i]] = ereg_replace('"', '""', $response[$columns[$i]]);
                        $response[$columns[$i]] = '"'. $response[$columns[$i]] .'"';
                    }
                    /* fall through */
                case 0: /* number */
                    if (isset($response[$columns[$i]]))
                        array_push($arr, $response[$columns[$i]]);
                    else
                        array_push($arr, '');
                    break;
                }
            }
            array_push($output, $arr);
        }
    
        return $output;
    }

    /* {{{ proto bool survey_export_csv(int survey_id, string filename)
        Exports the results of a survey to a CSV file.
        Returns true on success.
        */
    function export_csv($filename) {
        $umask = umask(0077);
        $fh = fopen($filename, 'w');
        umask($umask);
        if(!$fh)
            return 0;
    
        $data = survey_generate_csv();
    
        foreach ($data as $row) {
            fputs($fh, join(',', $row) . "\n");
        }
    
        fflush($fh);
        fclose($fh);
    
        return 1;
    }

    /* {{{ proto void mkcrossformat (array weights, integer qid)
       Builds HTML to allow for cross tabulation/analysis reporting.
     */
    function mkcrossformat($counts, $qid, $tid) {
        global $ESPCONFIG;

        $cids = array();
        $cidCount = 0;
    
        // let's grab the cid values for each of the questions
        // that we allow cross analysis on.
        if ($tid == 1) {
            $cids = array('Y', 'N');
        } else if ($records = get_records('questionnaire_question_choice', 'question_id', $qid, 'id')) {
            foreach ($records as $record) {
                array_push($cids, $record->id);
            }
        }
    
        $bg = $ESPCONFIG['bgalt_color1'];
    
    ?>
    <table width="90%" border="0">
    <tbody>
    <?php
    
        while(list($content,$num) = each($counts)) {
            if($bg != $ESPCONFIG['bgalt_color1']) {
                $bg = $ESPCONFIG['bgalt_color1'];
            } else {
                $bg = $ESPCONFIG['bgalt_color2'];
            }
    
            if ($cidCount >= count($cids)) {
                $cidCount = count($cids) - 1;
            }
    ?>
    <tr bgcolor="<?php echo $bg; ?>">
    <td width="34" height="23" align="left" valign="top" bgcolor="#0099FF">
      <div align="center">
        <input type="checkbox" name="cids[]" value="<?php echo $cids[$cidCount++]; ?>" />
      </div>
    </td>
    <td width="506" align="left"><?php echo $content; ?></td>
    </tr>
    <?php
        }
    ?>
    </tbody></table>
    <?php
    }
}

function questionnaire_response_key_cmp($l, $r) {
    $lx = explode('_', $l);
    $rx = explode('_', $r);
    $lc = intval($lx[0]);
    $rc = intval($rx[0]);
    if ($lc == $rc) {
        if (count($lx) > 1 && count($rx) > 1) {
            $lc = intval($lx[1]);
            $rc = intval($rx[1]);
        } else if (count($lx) > 1) {
            $lc++;
        } else if (count($rx) > 1) {
            $rc++;
        }
    }
    if ($lc == $rc)
        return 0;
    return ($lc > $rc) ? 1 : -1;
}
?>