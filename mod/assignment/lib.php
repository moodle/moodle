<?PHP  // $Id$
/**
 * assignment_base is the base class for assignment types
 *
 * This class provides all the functionality for an assignment
 */

DEFINE ('ASSIGNMENT_COUNT_WORDS', 1);
DEFINE ('ASSIGNMENT_COUNT_LETTERS', 2);

if (!isset($CFG->assignment_maxbytes)) {
    set_config("assignment_maxbytes", 1024000);  // Default maximum size for all assignments
}
if (!isset($CFG->assignment_itemstocount)) {
    set_config("assignment_itemstocount", ASSIGNMENT_COUNT_WORDS);  // Default item to count
}

/**
 * Standard base class for all assignment submodules (assignment types).
 */
class assignment_base {

    var $cm;
    var $course;
    var $assignment;
    var $strassignment;
    var $strassignments;
    var $strsubmissions;
    var $strlastmodified;
    var $navigation;
    var $pagetitle;
    var $currentgroup;
    var $usehtmleditor;
    var $defaultformat;
    var $context;

    /**
     * Constructor for the base assignment class
     *
     * Constructor for the base assignment class.
     * If cmid is set create the cm, course, assignment objects.
     * If the assignment is hidden and the user is not a teacher then
     * this prints a page header and notice.
     *
     * @param cmid   integer, the current course module id - not set for new assignments
     * @param assignment   object, usually null, but if we have it we pass it to save db access
     * @param cm   object, usually null, but if we have it we pass it to save db access
     * @param course   object, usually null, but if we have it we pass it to save db access
     */
    function assignment_base($cmid=0, $assignment=NULL, $cm=NULL, $course=NULL) {

        global $CFG;

        if ($cmid) {
            if ($cm) {
                $this->cm = $cm;
            } else if (! $this->cm = get_coursemodule_from_id('assignment', $cmid)) {
                error('Course Module ID was incorrect');
            }

            $this->context = get_context_instance(CONTEXT_MODULE,$this->cm->id);

            if ($course) {
                $this->course = $course;
            } else if (! $this->course = get_record('course', 'id', $this->cm->course)) {
                error('Course is misconfigured');
            }

            if ($assignment) {
                $this->assignment = $assignment;
            } else if (! $this->assignment = get_record('assignment', 'id', $this->cm->instance)) {
                error('assignment ID was incorrect');
            }

            $this->strassignment = get_string('modulename', 'assignment');
            $this->strassignments = get_string('modulenameplural', 'assignment');
            $this->strsubmissions = get_string('submissions', 'assignment');
            $this->strlastmodified = get_string('lastmodified');

            if ($this->course->category) {
                $this->navigation = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/course/view.php?id={$this->course->id}\">{$this->course->shortname}</a> -> ".
                                    "<a target=\"{$CFG->framename}\" href=\"index.php?id={$this->course->id}\">$this->strassignments</a> ->";
            } else {
                $this->navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id={$this->course->id}\">$this->strassignments</a> ->";
            }

            $this->pagetitle = strip_tags($this->course->shortname.': '.$this->strassignment.': '.format_string($this->assignment->name,true));

            // visibility
            $context = get_context_instance(CONTEXT_MODULE, $cmid);
            if (!$this->cm->visible and !has_capability('moodle/course:viewhiddenactivities', $context)) {
                $pagetitle = strip_tags($this->course->shortname.': '.$this->strassignment);
                print_header($pagetitle, $this->course->fullname, "$this->navigation $this->strassignment", 
                             "", "", true, '', navmenu($this->course, $this->cm));
                notice(get_string("activityiscurrentlyhidden"), "$CFG->wwwroot/course/view.php?id={$this->course->id}");
            }
            $this->currentgroup = get_current_group($this->course->id);
        }

    /// Set up things for a HTML editor if it's needed
        if ($this->usehtmleditor = can_use_html_editor()) {
            $this->defaultformat = FORMAT_HTML;
        } else {
            $this->defaultformat = FORMAT_MOODLE;
        }
    }

    /**
     * Display the assignment, used by view.php
     *
     * This in turn calls the methods producing individual parts of the page
     */
    function view() {
      
        $context = get_context_instance(CONTEXT_MODULE,$this->cm->id);
        require_capability('mod/assignment:view', $context);
        
        add_to_log($this->course->id, "assignment", "view", "view.php?id={$this->cm->id}", 
                   $this->assignment->id, $this->cm->id);

        $this->view_header();

        $this->view_intro();

        $this->view_dates();

        $this->view_feedback();

        $this->view_footer();
    }

    /**
     * Display the header and top of a page
     *
     * (this doesn't change much for assignment types)
     * This is used by the view() method to print the header of view.php but
     * it can be used on other pages in which case the string to denote the
     * page in the navigation trail should be passed as an argument
     *
     * @param $subpage string Description of subpage to be used in navigation trail
     */
    function view_header($subpage='') {

        global $CFG;

        if ($subpage) {
            $extranav = '<a target="'.$CFG->framename.'" href="view.php?id='.$this->cm->id.'">'.
                          format_string($this->assignment->name,true).'</a> -> '.$subpage;
        } else {
            $extranav = ' '.format_string($this->assignment->name,true);
        }

        print_header($this->pagetitle, $this->course->fullname, $this->navigation.$extranav, '', '', 
                     true, update_module_button($this->cm->id, $this->course->id, $this->strassignment), 
                     navmenu($this->course, $this->cm));

        echo '<div class="reportlink">'.$this->submittedlink().'</div>';
    }


    /**
     * Display the assignment intro
     *
     * This will most likely be extended by assignment type plug-ins
     * The default implementation prints the assignment description in a box
     */
    function view_intro() {
        print_simple_box_start('center', '', '', '', 'generalbox', 'intro');
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        echo format_text($this->assignment->description, $this->assignment->format, $formatoptions);
        print_simple_box_end();
    }

    /**
     * Display the assignment dates
     *
     * Prints the assignment start and end dates in a box.
     * This will be suitable for most assignment types
     */
    function view_dates() {
        if (!$this->assignment->timeavailable && !$this->assignment->timedue) {
            return;
        }

        print_simple_box_start('center', '', '', '', 'generalbox', 'dates');
        echo '<table>';
        if ($this->assignment->timeavailable) {
            echo '<tr><td class="c0">'.get_string('availabledate','assignment').':</td>';
            echo '    <td class="c1">'.userdate($this->assignment->timeavailable).'</td></tr>';
        }
        if ($this->assignment->timedue) {
            echo '<tr><td class="c0">'.get_string('duedate','assignment').':</td>';
            echo '    <td class="c1">'.userdate($this->assignment->timedue).'</td></tr>';
        }
        echo '</table>';
        print_simple_box_end();
    }


    /**
     * Display the bottom and footer of a page
     *
     * This default method just prints the footer.
     * This will be suitable for most assignment types
     */
    function view_footer() {
        print_footer($this->course);
    }

    /**
     * Display the feedback to the student
     *
     * This default method prints the teacher picture and name, date when marked,
     * grade and teacher submissioncomment.
     *
     * @param $submission object The submission object or NULL in which case it will be loaded
     */
    function view_feedback($submission=NULL) {
        global $USER;

        if (!$submission) { /// Get submission for this assignment
            $submission = $this->get_submission($USER->id);
        }

        if (empty($submission->timemarked)) {   /// Nothing to show, so print nothing
            return;
        }

    /// We need the teacher info
        if (! $teacher = get_record('user', 'id', $submission->teacher)) {
            error('Could not find the teacher');
        }

    /// Print the feedback
        print_heading(get_string('feedbackfromteacher', 'assignment', $this->course->teacher));

        echo '<table cellspacing="0" class="feedback">';

        echo '<tr>';
        echo '<td class="left picture">';
        print_user_picture($teacher->id, $this->course->id, $teacher->picture);
        echo '</td>';
        echo '<td class="topic">';
        echo '<div class="from">';
        echo '<div class="fullname">'.fullname($teacher).'</div>';
        echo '<div class="time">'.userdate($submission->timemarked).'</div>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td class="left side">&nbsp;</td>';
        echo '<td class="content">';
        if ($this->assignment->grade) {
            echo '<div class="grade">';
            echo get_string("grade").': '.$this->display_grade($submission->grade);
            echo '</div>';
            echo '<div class="clearer"></div>';
        }

        echo '<div class="comment">';
        echo format_text($submission->submissioncomment, $submission->format);
        echo '</div>';
        echo '</tr>';

        echo '</table>';
    }

    /** 
     * Returns a link with info about the state of the assignment submissions
     *
     * This is used by view_header to put this link at the top right of the page.
     * For teachers it gives the number of submitted assignments with a link
     * For students it gives the time of their submission.
     * This will be suitable for most assignment types.
     * @return string
     */
    function submittedlink() {
        global $USER;

        $submitted = '';

        $context = get_context_instance(CONTEXT_MODULE,$this->cm->id);
        if (has_capability('mod/assignment:grade', $context)) {

        // if this user can mark and is put in a group
        // then he can only see/mark submission in his own groups
            if (!has_capability('moodle/course:managegroups', $context) and (groupmode($this->course, $this->cm) == SEPARATEGROUPS)) {
                $count = $this->count_real_submissions($this->currentgroup);  // Only their groups
            } else {
                $count = $this->count_real_submissions();                     // Everyone
            }
            $submitted = '<a href="submissions.php?id='.$this->cm->id.'">'.
                         get_string('viewsubmissions', 'assignment', $count).'</a>';
        } else {
            if (!empty($USER->id)) {
                if ($submission = $this->get_submission($USER->id)) {
                    if ($submission->timemodified) {
                        if ($submission->timemodified <= $this->assignment->timedue || empty($this->assignment->timedue)) {
                            $submitted = '<span class="early">'.userdate($submission->timemodified).'</span>';
                        } else {
                            $submitted = '<span class="late">'.userdate($submission->timemodified).'</span>';
                        }
                    }
                }
            }
        }

        return $submitted;
    }


    /**
     * Print the setup form for the current assignment type
     *
     * Includes common.html and the assignment type's mod.html
     * This will be suitable for all assignment types
     *
     * @param $form object The object used to fill the form
     * @param $action url Default destination for this form
     */
    function setup(&$form, $action='') {
        global $CFG, $THEME;

        if (empty($this->course)) {
            if (! $this->course = get_record("course", "id", $form->course)) {
                error("Course is misconfigured");
            }
        }

        if (empty($action)) {   // Default destination for this form
            $action = $CFG->wwwroot.'/course/mod.php';
        }

        if (empty($form->assignmenttype)) {
            $form->assignmenttype = '';
        } else {
            $form->assignmenttype = clean_param($form->assignmenttype, PARAM_SAFEDIR);
        }

        if (empty($form->name)) {
            $form->name = '';
        } else {
            $form->name = stripslashes($form->name);
        }

        if (empty($form->description)) {
            $form->description = '';
        } else {
            $form->description = stripslashes($form->description);
        }

        $strname    = get_string('name');
        $strassignments = get_string('modulenameplural', 'assignment');
        $strheading = empty($form->name) ? get_string("type$form->assignmenttype",'assignment') : s(format_string(stripslashes($form->name),true));

        print_header($this->course->shortname.': '.$strheading, $this->course->fullname,
                "<a href=\"$CFG->wwwroot/course/view.php?id={$this->course->id}\">{$this->course->shortname} </a> -> ".
                "<a href=\"$CFG->wwwroot/mod/assignment/index.php?id={$this->course->id}\">$strassignments</a> -> $strheading");

        print_simple_box_start('center', '70%');
        print_heading(get_string('type'.$form->assignmenttype,'assignment'));
        print_simple_box(get_string('help'.$form->assignmenttype, 'assignment'), 'center');
        include("$CFG->dirroot/mod/assignment/type/common.html");

        include("$CFG->dirroot/mod/assignment/type/".$form->assignmenttype."/mod.html");
        $this->setup_end(); 
    }

    /**
     * Print the end of the setup form for the current assignment type
     *
     * Includes common_end.html
     */
    function setup_end() {
        global $CFG;

        include($CFG->dirroot.'/mod/assignment/type/common_end.html');

        print_simple_box_end();

        if ($this->usehtmleditor) {
            use_html_editor();
        }

        print_footer($this->course);
    }

    /**
     * Create a new assignment activity
     *
     * Given an object containing all the necessary data,
     * (defined by the form in mod.html) this function
     * will create a new instance and return the id number
     * of the new instance.
     * The due data is added to the calendar
     * This is common to all assignment types.
     *
     * @param $assignment object The data from the form on mod.html
     * @return int The id of the assignment
     */
    function add_instance($assignment) {

        $assignment->timemodified = time();
        if (empty($assignment->dueenable)) {
            $assignment->timedue = 0;
            $assignment->preventlate = 0;
        } else {
            $assignment->timedue = make_timestamp($assignment->dueyear, $assignment->duemonth, 
                                                  $assignment->dueday, $assignment->duehour, 
                                                  $assignment->dueminute);
        }
        if (empty($assignment->availableenable)) {
            $assignment->timeavailable = 0;
        } else {
            $assignment->timeavailable = make_timestamp($assignment->availableyear, $assignment->availablemonth, 
                                                        $assignment->availableday, $assignment->availablehour, 
                                                        $assignment->availableminute);
        }

        if ($returnid = insert_record("assignment", $assignment)) {

            if ($assignment->timedue) {
                $event = NULL;
                $event->name        = $assignment->name;
                $event->description = $assignment->description;
                $event->courseid    = $assignment->course;
                $event->groupid     = 0;
                $event->userid      = 0;
                $event->modulename  = 'assignment';
                $event->instance    = $returnid;
                $event->eventtype   = 'due';
                $event->timestart   = $assignment->timedue;
                $event->timeduration = 0;

                add_event($event);
            }
        }

        return $returnid;
    }

    /**
     * Deletes an assignment activity
     *
     * Deletes all database records, files and calendar events for this assignment.
     * @param $assignment object The assignment to be deleted
     * @return boolean False indicates error
     */
    function delete_instance($assignment) {
        global $CFG;

        $result = true;

        if (! delete_records('assignment_submissions', 'assignment', $assignment->id)) {
            $result = false;
        }

        if (! delete_records('assignment', 'id', $assignment->id)) {
            $result = false;
        }

        if (! delete_records('event', 'modulename', 'assignment', 'instance', $assignment->id)) {
            $result = false;
        }
        
        // Get the cm id to properly clean up the grade_items for this assignment
        // bug 4976
        if (! $cm = get_record('modules', 'name', 'assignment')) {
            $result = false;
        } else {
            if (! delete_records('grade_item', 'modid', $cm->id, 'cminstance', $assignment->id)) {
                $result = false;
            }
        }

        // delete file area with all attachments - ignore errors
        require_once($CFG->libdir.'/filelib.php');
        fulldelete($CFG->dataroot.'/'.$assignment->course.'/'.$CFG->moddata.'/assignment/'.$assignment->id);

        return $result;
    }

    /**
     * Updates a new assignment activity
     *
     * Given an object containing all the necessary data,
     * (defined by the form in mod.html) this function
     * will update the assignment instance and return the id number
     * The due date is updated in the calendar
     * This is common to all assignment types.
     *
     * @param $assignment object The data from the form on mod.html
     * @return int The assignment id
     */
    function update_instance($assignment) {

        $assignment->timemodified = time();
        if (empty($assignment->dueenable)) {
            $assignment->timedue = 0;
            $assignment->preventlate = 0;
        } else {
            $assignment->timedue = make_timestamp($assignment->dueyear, $assignment->duemonth, 
                                                  $assignment->dueday, $assignment->duehour, 
                                                  $assignment->dueminute);
        }
        if (empty($assignment->availableenable)) {
            $assignment->timeavailable = 0;
        } else {
            $assignment->timeavailable = make_timestamp($assignment->availableyear, $assignment->availablemonth, 
                                                        $assignment->availableday, $assignment->availablehour, 
                                                        $assignment->availableminute);
        }

        $assignment->id = $assignment->instance;

        if ($returnid = update_record('assignment', $assignment)) {

            if ($assignment->timedue) {
                $event = NULL;

                if ($event->id = get_field('event', 'id', 'modulename', 'assignment', 'instance', $assignment->id)) {

                    $event->name        = $assignment->name;
                    $event->description = $assignment->description;
                    $event->timestart   = $assignment->timedue;

                    update_event($event);
                } else {
                    $event = NULL;
                    $event->name        = $assignment->name;
                    $event->description = $assignment->description;
                    $event->courseid    = $assignment->course;
                    $event->groupid     = 0;
                    $event->userid      = 0;
                    $event->modulename  = 'assignment';
                    $event->instance    = $assignment->id;
                    $event->eventtype   = 'due';
                    $event->timestart   = $assignment->timedue;
                    $event->timeduration = 0;

                    add_event($event);
                }
            } else {
                delete_records('event', 'modulename', 'assignment', 'instance', $assignment->id);
            }
        }

        return $returnid;
    }

    /**
     * Top-level function for handling of submissions called by submissions.php
     *
     * This is for handling the teacher interaction with the grading interface
     * This should be suitable for most assignment types.
     *
     * @param $mode string Specifies the kind of teacher interaction taking place
     */
    function submissions($mode) {
        ///The main switch is changed to facilitate
        ///1) Batch fast grading
        ///2) Skip to the next one on the popup
        ///3) Save and Skip to the next one on the popup
        
        //make user global so we can use the id
        global $USER;
        
        switch ($mode) {
            case 'grade':                         // We are in a popup window grading
                if ($submission = $this->process_feedback()) {
                    //IE needs proper header with encoding
                    print_header(get_string('feedback', 'assignment').':'.format_string($this->assignment->name));
                    print_heading(get_string('changessaved'));
                    print $this->update_main_listing($submission);
                }
                close_window();
                break;

            case 'single':                        // We are in a popup window displaying submission
                $this->display_submission();
                break;

            case 'all':                          // Main window, display everything
                $this->display_submissions();
                break;

            case 'fastgrade':
                ///do the fast grading stuff  - this process should work for all 3 subclasses
                $grading    = false;
                $commenting = false;
                $col        = false;
                if (isset($_POST['submissioncomment'])) {
                    $col = 'submissioncomment';
                    $commenting = true;
                }
                if (isset($_POST['menu'])) {
                    $col = 'menu';
                    $grading = true;
                }
                if (!$col) {
                    //both submissioncomment and grade columns collapsed..
                    $this->display_submissions();            
                    break;
                }
                foreach ($_POST[$col] as $id => $unusedvalue){

                    $id = (int)$id; //clean parameter name
                    if (!$submission = $this->get_submission($id)) {
                        $submission = $this->prepare_new_submission($id);
                        $newsubmission = true;
                    } else {
                        $newsubmission = false;
                    }
                    unset($submission->data1);  // Don't need to update this.
                    unset($submission->data2);  // Don't need to update this.

                    //for fast grade, we need to check if any changes take place
                    $updatedb = false;

                    if ($grading) {
                        $grade = $_POST['menu'][$id];
                        $updatedb = $updatedb || ($submission->grade != $grade);
                        $submission->grade = $grade;
                    } else {
                        if (!$newsubmission) {
                            unset($submission->grade);  // Don't need to update this.
                        }
                    }
                    if ($commenting) {
                        $commentvalue = trim($_POST['submissioncomment'][$id]);
                        $updatedb = $updatedb || ($submission->submissioncomment != stripslashes($commentvalue));
                        $submission->submissioncomment = $commentvalue;
                    } else {
                        unset($submission->submissioncomment);  // Don't need to update this.
                    }

                    $submission->teacher    = $USER->id;
                    $submission->mailed     = $updatedb?0:$submission->mailed;//only change if it's an update
                    $submission->timemarked = time();

                    //if it is not an update, we don't change the last modified time etc.
                    //this will also not write into database if no submissioncomment and grade is entered.

                    if ($updatedb){
                        if ($newsubmission) {
                            if (!insert_record('assignment_submissions', $submission)) {
                                return false;
                            }
                        } else {
                            if (!update_record('assignment_submissions', $submission)) {
                                return false;
                            }
                        }            
                        //add to log only if updating
                        add_to_log($this->course->id, 'assignment', 'update grades', 
                                   'submissions.php?id='.$this->assignment->id.'&user='.$submission->userid, 
                                   $submission->userid, $this->cm->id);             
                    }
                        
                } 
                print_heading(get_string('changessaved'));
                $this->display_submissions();            
                break;


            case 'next':
                /// We are currently in pop up, but we want to skip to next one without saving.
                ///    This turns out to be similar to a single case
                /// The URL used is for the next submission.
                
                $this->display_submission();
                break;
                
            case 'saveandnext':
                ///We are in pop up. save the current one and go to the next one.
                //first we save the current changes
                if ($submission = $this->process_feedback()) {
                    //print_heading(get_string('changessaved'));
                    $extra_javascript = $this->update_main_listing($submission);
                }
                
                //then we display the next submission
                $this->display_submission($extra_javascript);
                break;
            
            default:
                echo "something seriously is wrong!!";
                break;                    
        }
    }
    
    /**
    * Helper method updating the listing on the main script from popup using javascript
    *
    * @param $submission object The submission whose data is to be updated on the main page
    */
    function update_main_listing($submission) {
        global $SESSION;
        
        $output = '';

        $perpage = get_user_preferences('assignment_perpage', 10);

        $quickgrade = get_user_preferences('assignment_quickgrade', 0);
        
        /// Run some Javascript to try and update the parent page
        $output .= '<script type="text/javascript">'."\n<!--\n";
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['submissioncomment'])) {
            if ($quickgrade){
                $output.= 'opener.document.getElementById("submissioncomment['.$submission->userid.']").value="'
                .trim($submission->submissioncomment).'";'."\n";
             } else {
                $output.= 'opener.document.getElementById("com'.$submission->userid.
                '").innerHTML="'.shorten_text(trim(strip_tags($submission->submissioncomment)), 15)."\";\n";
            }
        }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['grade'])) {
            //echo optional_param('menuindex');
            if ($quickgrade){
                $output.= 'opener.document.getElementById("menumenu['.$submission->userid.
                ']").selectedIndex="'.optional_param('menuindex', 0, PARAM_INT).'";'."\n";
            } else {
                $output.= 'opener.document.getElementById("g'.$submission->userid.'").innerHTML="'.
                $this->display_grade($submission->grade)."\";\n";
            }            
        }    
        //need to add student's assignments in there too.
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['timemodified']) &&
            $submission->timemodified) {
            $output.= 'opener.document.getElementById("ts'.$submission->userid.
                 '").innerHTML="'.addslashes($this->print_student_answer($submission->userid)).userdate($submission->timemodified)."\";\n";
        }
        
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['timemarked']) &&
            $submission->timemarked) {
            $output.= 'opener.document.getElementById("tt'.$submission->userid.
                 '").innerHTML="'.userdate($submission->timemarked)."\";\n";
        }
        
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['status'])) {
            $output.= 'opener.document.getElementById("up'.$submission->userid.'").className="s1";';
            $buttontext = get_string('update');
            $button = link_to_popup_window ('/mod/assignment/submissions.php?id='.$this->cm->id.'&amp;userid='.$submission->userid.'&amp;mode=single'.'&amp;offset='.optional_param('offset', '', PARAM_INT), 
                      'grade'.$submission->userid, $buttontext, 450, 700, $buttontext, 'none', true, 'button'.$submission->userid);
            $output.= 'opener.document.getElementById("up'.$submission->userid.'").innerHTML="'.addslashes($button).'";';
        }        
        $output .= "\n-->\n</script>";
        return $output;
    }

    /**
     *  Return a grade in user-friendly form, whether it's a scale or not
     *  
     * @param $grade
     * @return string User-friendly representation of grade
     */
    function display_grade($grade) {

        static $scalegrades = array();   // Cache scales for each assignment - they might have different scales!!

        if ($this->assignment->grade >= 0) {    // Normal number
            if ($grade == -1) {
                return '-';
            } else {
                return $grade.' / '.$this->assignment->grade;
            }

        } else {                                // Scale
            if (empty($scalegrades[$this->assignment->id])) {
                if ($scale = get_record('scale', 'id', -($this->assignment->grade))) {
                    $scalegrades[$this->assignment->id] = make_menu_from_list($scale->scale);
                } else {
                    return '-';
                }
            }
            if (isset($scalegrades[$this->assignment->id][$grade])) {
                return $scalegrades[$this->assignment->id][$grade];
            }
            return '-';
        }
    }

    /**
     *  Display a single submission, ready for grading on a popup window
     *
     * This default method prints the teacher info and submissioncomment box at the top and
     * the student info and submission at the bottom.
     * This method also fetches the necessary data in order to be able to
     * provide a "Next submission" button.
     * Calls preprocess_submission() to give assignment type plug-ins a chance
     * to process submissions before they are graded
     * This method gets its arguments from the page parameters userid and offset
     */
    function display_submission($extra_javascript = '') {
    
        global $CFG;
        
        $userid = required_param('userid', PARAM_INT);
        $offset = required_param('offset', PARAM_INT);//offset for where to start looking for student.

        if (!$user = get_record('user', 'id', $userid)) {
            error('No such user!');
        }

        if (!$submission = $this->get_submission($user->id)) {
            $submission = $this->prepare_new_submission($userid);
        }

        if ($submission->timemodified > $submission->timemarked) {
            $subtype = 'assignmentnew';
        } else {
            $subtype = 'assignmentold';
        }

    /// construct SQL, using current offset to find the data of the next student
        $course     = $this->course;
        $assignment = $this->assignment;
        $cm         = $this->cm;
    

    /// Get all teachers and students

        $currentgroup = get_current_group($course->id);

        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        } else {
            $users = get_course_users($course->id);
        }

        $select = 'SELECT u.id, u.id, u.firstname, u.lastname, u.picture,
                          s.id AS submissionid, s.grade, s.submissioncomment, 
                          s.timemodified, s.timemarked ';
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'assignment_submissions s ON u.id = s.userid 
                                                                  AND s.assignment = '.$this->assignment->id.' '.
               'WHERE u.id IN ('.implode(',', array_keys($users)).') ';
               
        require_once($CFG->libdir.'/tablelib.php');

        if ($sort = flexible_table::get_sql_sort('mod-assignment-submissions')) {
            $sort = 'ORDER BY '.$sort.' ';
        }

        $nextid = 0;
        if (($auser = get_records_sql($select.$sql.$sort, $offset+1, 1)) !== false) {
            $nextuser = array_shift($auser);
        /// Calculate user status
            $nextuser->status = ($nextuser->timemarked > 0) && ($nextuser->timemarked >= $nextuser->timemodified);
            $nextid = $nextuser->id;
        }

        print_header(get_string('feedback', 'assignment').':'.fullname($user, true).':'.format_string($this->assignment->name));

        /// Print any extra javascript needed for saveandnext
        echo $extra_javascript;

        ///SOme javascript to help with setting up >.>
        
        echo '<script type="text/javascript">'."\n";
        echo 'function setNext(){'."\n";
        echo 'document.submitform.mode.value=\'next\';'."\n";
        echo 'document.submitform.userid.value="'.$nextid.'";'."\n";
        echo '}'."\n";
        
        echo 'function saveNext(){'."\n";
        echo 'document.submitform.mode.value=\'saveandnext\';'."\n";
        echo 'document.submitform.userid.value="'.$nextid.'";'."\n";
        echo 'document.submitform.saveuserid.value="'.$userid.'";'."\n";
        echo 'document.submitform.menuindex.value = document.submitform.grade.selectedIndex;'."\n";
        echo '}'."\n";
            
        echo '</script>'."\n";
        echo '<table cellspacing="0" class="feedback '.$subtype.'" >';

        ///Start of teacher info row

        echo '<tr>';
        echo '<td width="35" valign="top" class="picture teacher">';
        if ($submission->teacher) {
            $teacher = get_record('user', 'id', $submission->teacher);
        } else {
            global $USER;
            $teacher = $USER;
        }
        print_user_picture($teacher->id, $this->course->id, $teacher->picture);
        echo '</td>';
        echo '<td class="content">';
        echo '<form name="submitform" action="submissions.php" method="post">';
        echo '<input type="hidden" name="offset" value="'.++$offset.'">';
        echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        echo '<input type="hidden" name="mode" value="grade" />';
        echo '<input type="hidden" name="menuindex" value="0" />';//selected menu index
        
        //new hidden field, initialized to -1.
        echo '<input type="hidden" name="saveuserid" value="-1" />';
        if ($submission->timemarked) {
            echo '<div class="from">';
            echo '<div class="fullname">'.fullname($teacher, true).'</div>';
            echo '<div class="time">'.userdate($submission->timemarked).'</div>';
            echo '</div>';
        }
        echo '<div class="grade">'.get_string('grade').':';
        choose_from_menu(make_grades_menu($this->assignment->grade), 'grade', $submission->grade, get_string('nograde'), '', -1);
        echo '</div>';
        echo '<div class="clearer"></div>';

        $this->preprocess_submission($submission);

        echo '<br />';
        print_textarea($this->usehtmleditor, 14, 58, 0, 0, 'submissioncomment', $submission->submissioncomment, $this->course->id);

        if ($this->usehtmleditor) { 
            echo '<input type="hidden" name="format" value="'.FORMAT_HTML.'" />';
        } else {
            echo '<div align="right" class="format">';
            choose_from_menu(format_text_menu(), "format", $submission->format, "");
            helpbutton("textformat", get_string("helpformatting"));
            echo '</div>';
        }

        ///Print Buttons in Single View
        echo '<div class="buttons" align="center">';
        echo '<input type="submit" name="submit" value="'.get_string('savechanges').'" onclick = "document.submitform.menuindex.value = document.submitform.grade.selectedIndex" />';
        echo '<input type="submit" name="cancel" value="'.get_string('cancel').'" />';
        //if there are more to be graded.
        if ($nextid) {
            echo '<input type="submit" name="saveandnext" value="'.get_string('saveandnext').'" onclick="saveNext()" />';
            echo '<input type="submit" name="next" value="'.get_string('next').'" onclick="setNext();" />';
        }
        echo '</div>';
        echo '</form>';

        $customfeedback = $this->custom_feedbackform($submission, true);
        if (!empty($customfeedback)) {
            echo $customfeedback; 
        }

        echo '</td></tr>';
        
        ///End of teacher info row, Start of student info row
        echo '<tr>';
        echo '<td width="35" valign="top" class="picture user">';
        print_user_picture($user->id, $this->course->id, $user->picture);
        echo '</td>';
        echo '<td class="topic">';
        echo '<div class="from">';
        echo '<div class="fullname">'.fullname($user, true).'</div>';
        if ($submission->timemodified) {
            echo '<div class="time">'.userdate($submission->timemodified).
                                     $this->display_lateness($submission->timemodified).'</div>';
        }
        echo '</div>';
        $this->print_user_files($user->id);
        echo '</td>';
        echo '</tr>';
        
        ///End of student info row
        
        echo '</table>';

        if ($this->usehtmleditor) {
            use_html_editor();
        }

        print_footer('none');
    }

    /**
     *  Preprocess submission before grading
     *
     * Called by display_submission()
     * The default type does nothing here.
     * @param $submission object The submission object
     */
    function preprocess_submission(&$submission) {
    }

    /**
     *  Display all the submissions ready for grading
     */
    function display_submissions() {

        global $CFG, $db, $USER;

        /* first we check to see if the form has just been submitted
         * to request user_preference updates
         */
         
        if (isset($_POST['updatepref'])){
            $perpage = optional_param('perpage', 10, PARAM_INT);
            $perpage = ($perpage <= 0) ? 10 : $perpage ;
            set_user_preference('assignment_perpage', $perpage);
            set_user_preference('assignment_quickgrade', optional_param('quickgrade',0, PARAM_BOOL));
        }

        /* next we get perpage and quickgrade (allow quick grade) params 
         * from database
         */
        $perpage    = get_user_preferences('assignment_perpage', 10);
        $quickgrade = get_user_preferences('assignment_quickgrade', 0);
        
        $teacherattempts = true; /// Temporary measure
        $page    = optional_param('page', 0, PARAM_INT);
        $strsaveallfeedback = get_string('saveallfeedback', 'assignment');

    /// Some shortcuts to make the code read better
        
        $course     = $this->course;
        $assignment = $this->assignment;
        $cm         = $this->cm;
        
        $tabindex = 1; //tabindex for quick grading tabbing; Not working for dropdowns yet

        add_to_log($course->id, 'assignment', 'view submission', 'submissions.php?id='.$this->assignment->id, $this->assignment->id, $this->cm->id);
        
        print_header_simple(format_string($this->assignment->name,true), "", '<a href="index.php?id='.$course->id.'">'.$this->strassignments.'</a> -> <a href="view.php?a='.$this->assignment->id.'">'.format_string($this->assignment->name,true).'</a> -> '. $this->strsubmissions, '', '', true, update_module_button($cm->id, $course->id, $this->strassignment), navmenu($course, $cm));
    
    ///Position swapped
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, 'submissions.php?id='.$this->cm->id);
        } else {
            $currentgroup = false;
        }

    /// Get all teachers and students
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        } else {
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            $users = get_users_by_capability($context, 'mod/assignment:submit'); // everyone with this capability set to non-prohibit
        }

        $tablecolumns = array('picture', 'fullname', 'grade', 'submissioncomment', 'timemodified', 'timemarked', 'status');
        $tableheaders = array('', get_string('fullname'), get_string('grade'), get_string('comment', 'assignment'), get_string('lastmodified').' ('.$course->student.')', get_string('lastmodified').' ('.$course->teacher.')', get_string('status'));

        require_once($CFG->libdir.'/tablelib.php');
        $table = new flexible_table('mod-assignment-submissions');
                        
        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot.'/mod/assignment/submissions.php?id='.$this->cm->id.'&amp;currentgroup='.$currentgroup);
                
        $table->sortable(true, 'lastname');//sorted by lastname by default
        $table->collapsible(true);
        $table->initialbars(true);
        
        $table->column_suppress('picture');
        $table->column_suppress('fullname');
        
        $table->column_class('picture', 'picture');
        $table->column_class('fullname', 'fullname');
        $table->column_class('grade', 'grade');
        $table->column_class('submissioncomment', 'comment');
        $table->column_class('timemodified', 'timemodified');
        $table->column_class('timemarked', 'timemarked');
        $table->column_class('status', 'status');
        
        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'attempts');
        $table->set_attribute('class', 'submissions');
        $table->set_attribute('width', '90%');
        $table->set_attribute('align', 'center');
            
        // Start working -- this is necessary as soon as the niceties are over
        $table->setup();

    /// Check to see if groups are being used in this assignment

        if (!$teacherattempts) {
            $teachers = get_course_teachers($course->id);
            if (!empty($teachers)) {
                $keys = array_keys($teachers);
            }
            foreach ($keys as $key) {
                unset($users[$key]);
            }
        }
        
        if (empty($users)) {
            print_heading(get_string('noattempts','assignment'));
            return true;
        }

    /// Construct the SQL

        if ($where = $table->get_sql_where()) {
            $where .= ' AND ';
        }

        if ($sort = $table->get_sql_sort()) {
            $sort = ' ORDER BY '.$sort;
        }

        $select = 'SELECT u.id, u.id, u.firstname, u.lastname, u.picture, 
                          s.id AS submissionid, s.grade, s.submissioncomment, 
                          s.timemodified, s.timemarked ';
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'assignment_submissions s ON u.id = s.userid 
                                                                  AND s.assignment = '.$this->assignment->id.' '.
               'WHERE '.$where.'u.id IN ('.implode(',', array_keys($users)).') ';
    
        $table->pagesize($perpage, count($users));
        
        ///offset used to calculate index of student in that particular query, needed for the pop up to know who's next
        $offset = $page * $perpage;
        
        $strupdate = get_string('update');
        $strgrade  = get_string('grade');
        $grademenu = make_grades_menu($this->assignment->grade);

        if (($ausers = get_records_sql($select.$sql.$sort, $table->get_page_start(), $table->get_page_size())) !== false) {
            
            foreach ($ausers as $auser) {
            /// Calculate user status
                $auser->status = ($auser->timemarked > 0) && ($auser->timemarked >= $auser->timemodified);
                $picture = print_user_picture($auser->id, $course->id, $auser->picture, false, true);
                
                if (empty($auser->submissionid)) {
                    $auser->grade = -1; //no submission yet
                }
                    
                if (!empty($auser->submissionid)) {
                ///Prints student answer and student modified date
                ///attach file or print link to student answer, depending on the type of the assignment.
                ///Refer to print_student_answer in inherited classes.     
                    if ($auser->timemodified > 0) {            
                        $studentmodified = '<div id="ts'.$auser->id.'">'.$this->print_student_answer($auser->id).userdate($auser->timemodified).'</div>';
                    } else {
                        $studentmodified = '<div id="ts'.$auser->id.'">&nbsp;</div>';
                    }
                ///Print grade, dropdown or text
                    if ($auser->timemarked > 0) {
                        $teachermodified = '<div id="tt'.$auser->id.'">'.userdate($auser->timemarked).'</div>';
                        
                        if ($quickgrade) {
                            $grade = '<div id="g'.$auser->id.'">'.choose_from_menu(make_grades_menu($this->assignment->grade), 
                            'menu['.$auser->id.']', $auser->grade, get_string('nograde'),'',-1,true,false,$tabindex++).'</div>';
                        } else {
                            $grade = '<div id="g'.$auser->id.'">'.$this->display_grade($auser->grade).'</div>';
                        }

                    } else {
                        $teachermodified = '<div id="tt'.$auser->id.'">&nbsp;</div>';
                        if ($quickgrade){                    
                            $grade = '<div id="g'.$auser->id.'">'.choose_from_menu(make_grades_menu($this->assignment->grade), 
                            'menu['.$auser->id.']', $auser->grade, get_string('nograde'),'',-1,true,false,$tabindex++).'</div>';
                        } else {
                            $grade = '<div id="g'.$auser->id.'">'.$this->display_grade($auser->grade).'</div>';
                        }
                    }
                ///Print Comment
                    if ($quickgrade){
                        $comment = '<div id="com'.$auser->id.'"><textarea tabindex="'.$tabindex++.'" name="submissioncomment['.$auser->id.']" id="submissioncomment['.$auser->id.']">'.($auser->submissioncomment).'</textarea></div>';
                    } else {
                        $comment = '<div id="com'.$auser->id.'">'.shorten_text(strip_tags($auser->submissioncomment),15).'</div>';
                    }
                } else {
                    $studentmodified = '<div id="ts'.$auser->id.'">&nbsp;</div>';
                    $teachermodified = '<div id="tt'.$auser->id.'">&nbsp;</div>';
                    $status          = '<div id="st'.$auser->id.'">&nbsp;</div>';
                    if ($quickgrade){   // allow editing
                        $grade = '<div id="g'.$auser->id.'">'.choose_from_menu(make_grades_menu($this->assignment->grade), 
                                 'menu['.$auser->id.']', $auser->grade, get_string('nograde'),'',-1,true,false,$tabindex++).'</div>';
                    } else {
                        $grade = '<div id="g'.$auser->id.'">-</div>';
                    }
                    if ($quickgrade){
                        $comment = '<div id="com'.$auser->id.'"><textarea tabindex="'.$tabindex++.'" name="submissioncomment['.$auser->id.']" id="submissioncomment['.$auser->id.']">'.($auser->submissioncomment).'</textarea></div>';
                    } else {
                        $comment = '<div id="com'.$auser->id.'">&nbsp;</div>';
                    }
                }

                if (empty($auser->status)) { /// Confirm we have exclusively 0 or 1
                    $auser->status = 0;
                } else {
                    $auser->status = 1;
                }

                $buttontext = ($auser->status == 1) ? $strupdate : $strgrade;
                                   
                ///No more buttons, we use popups ;-).
                $button = link_to_popup_window ('/mod/assignment/submissions.php?id='.$this->cm->id.'&amp;userid='.$auser->id.'&amp;mode=single'.'&amp;offset='.$offset++, 
                                                'grade'.$auser->id, $buttontext, 500, 780, $buttontext, 'none', true, 'button'.$auser->id);

                $status  = '<div id="up'.$auser->id.'" class="s'.$auser->status.'">'.$button.'</div>';
                
                $row = array($picture, fullname($auser), $grade, $comment, $studentmodified, $teachermodified, $status);
                $table->add_data($row);
            }
        }
        
        /// Print quickgrade form around the table
        if ($quickgrade){
            echo '<form action="submissions.php" name="fastg" method="post">';
            echo '<input type="hidden" name="id" value="'.$this->cm->id.'">';
            echo '<input type="hidden" name="mode" value="fastgrade">';
            echo '<input type="hidden" name="page" value="'.$page.'">';
            echo '<p align="center"><input type="submit" name="fastg" value="'.get_string('saveallfeedback', 'assignment').'" /></p>';
        }

        $table->print_html();  /// Print the whole table

        if ($quickgrade){
            echo '<p align="center"><input type="submit" name="fastg" value="'.get_string('saveallfeedback', 'assignment').'" /></p>';
            echo '</form>';
        }
        /// End of fast grading form
        
        /// Mini form for setting user preference
        echo '<br />';
        echo '<form name="options" action="submissions.php?id='.$this->cm->id.'" method="post">';
        echo '<input type="hidden" id="updatepref" name="updatepref" value="1" />';
        echo '<table id="optiontable" align="center">';
        echo '<tr align="right"><td>';
        echo '<label for="perpage">'.get_string('pagesize','assignment').'</label>';
        echo ':</td>';
        echo '<td align="left">';
        echo '<input type="text" id="perpage" name="perpage" size="1" value="'.$perpage.'" />';
        helpbutton('pagesize', get_string('pagesize','assignment'), 'assignment');
        echo '</td></tr>';
        echo '<tr align="right">';
        echo '<td>';
        print_string('quickgrade','assignment');
        echo ':</td>';
        echo '<td align="left">';
        if ($quickgrade){
            echo '<input type="checkbox" name="quickgrade" value="1" checked="checked" />';
        } else {
            echo '<input type="checkbox" name="quickgrade" value="1" />';
        }
        helpbutton('quickgrade', get_string('quickgrade', 'assignment'), 'assignment').'</p></div>';
        echo '</td></tr>';
        echo '<tr>';
        echo '<td colspan="2" align="right">';
        echo '<input type="submit" value="'.get_string('savepreferences').'" />';
        echo '</td></tr></table>';
        echo '</form>';
        ///End of mini form
        print_footer($this->course);
    }

    /**
     *  Process teacher feedback submission
     *
     * This is called by submissions() when a grading even has taken place.
     * It gets its data from the submitted form.
     * @return object The updated submission object
     */
    function process_feedback() {

        global $USER;

        if (!$feedback = data_submitted()) {      // No incoming data?
            return false;
        }

        ///For save and next, we need to know the userid to save, and the userid to go
        ///We use a new hidden field in the form, and set it to -1. If it's set, we use this
        ///as the userid to store
        if ((int)$feedback->saveuserid !== -1){
            $feedback->userid = $feedback->saveuserid;
        }

        if (!empty($feedback->cancel)) {          // User hit cancel button
            return false;
        }

        $submission = $this->get_submission($feedback->userid, true);  // Get or make one

        $submission->grade      = $feedback->grade;
        $submission->submissioncomment    = $feedback->submissioncomment;
        $submission->format     = $feedback->format;
        $submission->teacher    = $USER->id;
        $submission->mailed     = 0;       // Make sure mail goes out (again, even)
        $submission->timemarked = time();

        unset($submission->data1);  // Don't need to update this.
        unset($submission->data2);  // Don't need to update this.

        if (empty($submission->timemodified)) {   // eg for offline assignments
            $submission->timemodified = time();
        }

        if (! update_record('assignment_submissions', $submission)) {
            return false;
        }

        add_to_log($this->course->id, 'assignment', 'update grades', 
                   'submissions.php?id='.$this->assignment->id.'&user='.$feedback->userid, $feedback->userid, $this->cm->id);
        
        return $submission;

    }

    /**
     * Load the submission object for a particular user
     *
     * @param $userid int The id of the user whose submission we want or 0 in which case USER->id is used
     * @param $createnew boolean optional Defaults to false. If set to true a new submission object will be created in the database
     * @return object The submission
     */
    function get_submission($userid=0, $createnew=false) {
        global $USER;

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $submission = get_record('assignment_submissions', 'assignment', $this->assignment->id, 'userid', $userid);

        if ($submission || !$createnew) {
            return $submission;
        }
        $newsubmission = $this->prepare_new_submission($userid);
        if (!insert_record("assignment_submissions", $newsubmission)) {
            error("Could not insert a new empty submission");
        }

        return get_record('assignment_submissions', 'assignment', $this->assignment->id, 'userid', $userid);
    }

    /**
     * Instantiates a new submission object for a given user
     *
     * Sets the assignment, userid and times, everything else is set to default values.
     * @param $userid int The userid for which we want a submission object
     * @return object The submission
     */
    function prepare_new_submission($userid) {
        $submission = new Object; 
        $submission->assignment   = $this->assignment->id;
        $submission->userid       = $userid;
        $submission->timecreated  = time();
        $submission->timemodified = $submission->timecreated;
        $submission->numfiles     = 0;
        $submission->data1        = '';
        $submission->data2        = '';
        $submission->grade        = -1;
        $submission->submissioncomment      = '';
        $submission->format       = 0;
        $submission->teacher      = 0;
        $submission->timemarked   = 0;
        $submission->mailed       = 0;
        return $submission;
    }

    /**
     * Return all assignment submissions by ENROLLED students (even empty)
     *
     * @param $sort string optional field names for the ORDER BY in the sql query
     * @param $dir string optional specifying the sort direction, defaults to DESC
     * @return array The submission objects indexed by id
     */
    function get_submissions($sort='', $dir='DESC') {
        return assignment_get_all_submissions($this->assignment, $sort, $dir);
    }

    /**
     * Counts all real assignment submissions by ENROLLED students (not empty ones)
     *
     * @param $groupid int optional If nonzero then count is restricted to this group
     * @return int The number of submissions
     */
    function count_real_submissions($groupid=0) {
        return assignment_count_real_submissions($this->assignment, $groupid);
    }

    /**
     * Alerts teachers by email of new or changed assignments that need grading
     *
     * First checks whether the option to email teachers is set for this assignment.
     * Sends an email to ALL teachers in the course (or in the group if using separate groups).
     * Uses the methods email_teachers_text() and email_teachers_html() to construct the content.
     * @param $submission object The submission that has changed
     */
    function email_teachers($submission) {
        global $CFG;

        if (empty($this->assignment->emailteachers)) {          // No need to do anything
            return;
        }

        $user = get_record('user', 'id', $submission->userid);

        if (groupmode($this->course, $this->cm) == SEPARATEGROUPS) {   // Separate groups are being used
            if ($groups = user_group($this->course->id, $user->id)) {  // Try to find groups
                $teachers = array();
                foreach ($groups as $group) {
                    $teachers = array_merge($teachers, get_group_teachers($this->course->id, $group->id));
                }
            } else {
                $teachers = get_group_teachers($this->course->id, 0);   // Works even if not in group
            }
        } else {
            $teachers = get_course_teachers($this->course->id);
        }

        if ($teachers) {

            $strassignments = get_string('modulenameplural', 'assignment');
            $strassignment  = get_string('modulename', 'assignment');
            $strsubmitted  = get_string('submitted', 'assignment');

            foreach ($teachers as $teacher) {
                unset($info);
                $info->username = fullname($user);
                $info->assignment = format_string($this->assignment->name,true);
                $info->url = $CFG->wwwroot.'/mod/assignment/submissions.php?id='.$this->cm->id;

                $postsubject = $strsubmitted.': '.$info->username.' -> '.$this->assignment->name;
                $posttext = $this->email_teachers_text($info);
                $posthtml = ($teacher->mailformat == 1) ? $this->email_teachers_html($info) : '';

                @email_to_user($teacher, $user, $postsubject, $posttext, $posthtml);  // If it fails, oh well, too bad.
            }
        }
    }

    /**
     * Creates the text content for emails to teachers
     *
     * @param $info object The info used by the 'emailteachermail' language string
     * @return string
     */
    function email_teachers_text($info) {
        $posttext  = $this->course->shortname.' -> '.$this->strassignments.' -> '.
                     format_string($this->assignment->name, true)."\n";
        $posttext .= '---------------------------------------------------------------------'."\n";
        $posttext .= get_string("emailteachermail", "assignment", $info)."\n";
        $posttext .= "\n---------------------------------------------------------------------\n";
        return $posttext;
    }

     /**
     * Creates the html content for emails to teachers
     *
     * @param $info object The info used by the 'emailteachermailhtml' language string
     * @return string
     */
    function email_teachers_html($info) {
        global $CFG;
        $posthtml  = '<p><font face="sans-serif">'.
                     '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'">'.$this->course->shortname.'</a> ->'.
                     '<a href="'.$CFG->wwwroot.'/mod/assignment/index.php?id='.$this->course->id.'">'.$this->strassignments.'</a> ->'.
                     '<a href="'.$CFG->wwwroot.'/mod/assignment/view.php?id='.$this->cm->id.'">'.format_string($this->assignment->name,true).'</a></font></p>';
        $posthtml .= '<hr /><font face="sans-serif">';
        $posthtml .= '<p>'.get_string('emailteachermailhtml', 'assignment', $info).'</p>';
        $posthtml .= '</font><hr />';
        return $posthtml;
    }

    /**
     * Produces a list of links to the files uploaded by a user
     *
     * @param $userid int optional id of the user. If 0 then $USER->id is used.
     * @param $return boolean optional defaults to false. If true the list is returned rather than printed
     * @return string optional
     */
    function print_user_files($userid=0, $return=false) {
        global $CFG, $USER;
    
        if (!$userid) {
            if (!isloggedin()) {
                return '';
            }
            $userid = $USER->id;
        }
    
        $filearea = $this->file_area_name($userid);

        $output = '';
    
        if ($basedir = $this->file_area($userid)) {
            if ($files = get_directory_list($basedir)) {
                require_once($CFG->libdir.'/filelib.php');
                foreach ($files as $key => $file) {
                    
                    $icon = mimeinfo('icon', $file);
                    
                    if ($CFG->slasharguments) {
                        $ffurl = "$CFG->wwwroot/file.php/$filearea/$file";
                    } else {
                        $ffurl = "$CFG->wwwroot/file.php?file=/$filearea/$file";
                    }
                
                    $output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                            '<a href="'.$ffurl.'" >'.$file.'</a><br />';
                }
            }
        }

        $output = '<div class="files">'.$output.'</div>';

        if ($return) {
            return $output;
        }
        echo $output;
    }

    /**
     * Count the files uploaded by a given user
     *
     * @param $userid int The user id
     * @return int
     */
    function count_user_files($userid) {
        global $CFG;

        $filearea = $this->file_area_name($userid);

        if ( is_dir($CFG->dataroot.'/'.$filearea) && $basedir = $this->file_area($userid)) {
            if ($files = get_directory_list($basedir)) {
                return count($files);
            }
        }
        return 0;
    }

    /**
     * Creates a directory file name, suitable for make_upload_directory()
     *
     * @param $userid int The user id
     * @return string path to file area
     */
    function file_area_name($userid) {
        global $CFG;
    
        return $this->course->id.'/'.$CFG->moddata.'/assignment/'.$this->assignment->id.'/'.$userid;
    }

    /**
     * Makes an upload directory
     *
     * @param $userid int The user id
     * @return string path to file area.
     */
    function file_area($userid) {
        return make_upload_directory( $this->file_area_name($userid) );
    }

    /**
     * Returns true if the student is allowed to submit
     *
     * Checks that the assignment has started and, if the option to prevent late
     * submissions is set, also checks that the assignment has not yet closed.
     * @return boolean
     */
    function isopen() {
        $time = time();
        if ($this->assignment->preventlate && $this->assignment->timedue) {
            return ($this->assignment->timeavailable <= $time && $time <= $this->assignment->timedue);
        } else {
            return ($this->assignment->timeavailable <= $time);
        }
    }

    /**
     * Return an outline of the user's interaction with the assignment
     *
     * The default method prints the grade and timemodified
     * @param $user object
     * @return object with properties ->info and ->time
     */
    function user_outline($user) {
        if ($submission = $this->get_submission($user->id)) {

            $result->info = get_string('grade').': '.$this->display_grade($submission->grade);
            $result->time = $submission->timemodified;
            return $result;
        }
        return NULL;
    }

    /**
     * Print complete information about the user's interaction with the assignment
     *
     * @param $user object
     */
    function user_complete($user) {
        if ($submission = $this->get_submission($user->id)) {
            if ($basedir = $this->file_area($user->id)) {
                if ($files = get_directory_list($basedir)) {
                    $countfiles = count($files)." ".get_string("uploadedfiles", "assignment");
                    foreach ($files as $file) {
                        $countfiles .= "; $file";
                    }
                }
            }
    
            print_simple_box_start();
            echo get_string("lastmodified").": ";
            echo userdate($submission->timemodified);
            echo $this->display_lateness($submission->timemodified);
    
            $this->print_user_files($user->id);
    
            echo '<br />';
    
            if (empty($submission->timemarked)) {
                print_string("notgradedyet", "assignment");
            } else {
                $this->view_feedback($submission);
            }
    
            print_simple_box_end();
    
        } else {
            print_string("notsubmittedyet", "assignment");
        }
    }

    /**
     * Return a string indicating how late a submission is
     *
     * @param $timesubmitted int 
     * @return string
     */
    function display_lateness($timesubmitted) {
        return assignment_display_lateness($timesubmitted, $this->assignment->timedue);
    }

    /**
     * Empty method stub for all delete actions.
     */
    function delete() {
        //nothing by default
        redirect('view.php?id='.$this->cm->id);
    }

    /**
     * Empty custom feedback grading form.
     */
    function custom_feedbackform($submission, $return=false) {
        //nothing by default
        return '';
    }

} ////// End of the assignment_base class



/// OTHER STANDARD FUNCTIONS ////////////////////////////////////////////////////////

/**
 * Deletes an assignment instance
 *
 * This is done by calling the delete_instance() method of the assignment type class
 */
function assignment_delete_instance($id){
    global $CFG;

    if (! $assignment = get_record('assignment', 'id', $id)) {
        return false;
    }

    require_once("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $ass = new $assignmentclass();
    return $ass->delete_instance($assignment);
}


/**
 * Updates an assignment instance
 *
 * This is done by calling the update_instance() method of the assignment type class
 */
function assignment_update_instance($assignment){
    global $CFG;

    $assignment->assignmenttype = clean_param($assignment->assignmenttype, PARAM_SAFEDIR);

    require_once("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $ass = new $assignmentclass();
    return $ass->update_instance($assignment);
}    


/**
 * Adds an assignment instance
 *
 * This is done by calling the add_instance() method of the assignment type class
 */
function assignment_add_instance($assignment) {
    global $CFG;

    $assignment->assignmenttype = clean_param($assignment->assignmenttype, PARAM_SAFEDIR);

    require_once("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $ass = new $assignmentclass();
    return $ass->add_instance($assignment);
}


/**
 * Returns an outline of a user interaction with an assignment
 *
 * This is done by calling the user_outline() method of the assignment type class
 */
function assignment_user_outline($course, $user, $mod, $assignment) {
    global $CFG;

    require_once("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $ass = new $assignmentclass($mod->id, $assignment, $mod, $course);
    return $ass->user_outline($user);
}

/**
 * Prints the complete info about a user's interaction with an assignment
 *
 * This is done by calling the user_complete() method of the assignment type class
 */
function assignment_user_complete($course, $user, $mod, $assignment) {
    global $CFG;

    require_once("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $ass = new $assignmentclass($mod->id, $assignment, $mod, $course);
    return $ass->user_complete($user);
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * Finds all assignment notifications that have yet to be mailed out, and mails them
 */
function assignment_cron () {

    global $CFG, $USER;

    /// Notices older than 1 day will not be mailed.  This is to avoid the problem where
    /// cron has not been running for a long time, and then suddenly people are flooded
    /// with mail from the past few weeks or months

    $timenow   = time();
    $endtime   = $timenow - $CFG->maxeditingtime;
    $starttime = $endtime - 24 * 3600;   /// One day earlier

    if ($submissions = assignment_get_unmailed_submissions($starttime, $endtime)) {

        foreach ($submissions as $key => $submission) {
            if (! set_field("assignment_submissions", "mailed", "1", "id", "$submission->id")) {
                echo "Could not update the mailed field for id $submission->id.  Not mailed.\n";
                unset($submissions[$key]);
            }
        }

        $timenow = time();

        foreach ($submissions as $submission) {

            echo "Processing assignment submission $submission->id\n";

            if (! $user = get_record("user", "id", "$submission->userid")) {
                echo "Could not find user $post->userid\n";
                continue;
            }

            $USER->lang = $user->lang;

            if (! $course = get_record("course", "id", "$submission->course")) {
                echo "Could not find course $submission->course\n";
                continue;
            }
            
            if (!has_capability('moodle/course:view', get_context_instance(CONTEXT_COURSE, $submission->course))) {
                echo fullname($user)." not an active participant in $course->shortname\n";
                continue;
            }

            if (! $teacher = get_record("user", "id", "$submission->teacher")) {
                echo "Could not find teacher $submission->teacher\n";
                continue;
            }

            if (! $mod = get_coursemodule_from_instance("assignment", $submission->assignment, $course->id)) {
                echo "Could not find course module for assignment id $submission->assignment\n";
                continue;
            }

            if (! $mod->visible) {    /// Hold mail notification for hidden assignments until later
                continue;
            }

            $strassignments = get_string("modulenameplural", "assignment");
            $strassignment  = get_string("modulename", "assignment");

            unset($assignmentinfo);
            $assignmentinfo->teacher = fullname($teacher);
            $assignmentinfo->assignment = format_string($submission->name,true);
            $assignmentinfo->url = "$CFG->wwwroot/mod/assignment/view.php?id=$mod->id";

            $postsubject = "$course->shortname: $strassignments: ".format_string($submission->name,true);
            $posttext  = "$course->shortname -> $strassignments -> ".format_string($submission->name,true)."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= get_string("assignmentmail", "assignment", $assignmentinfo)."\n";
            $posttext .= "---------------------------------------------------------------------\n";

            if ($user->mailformat == 1) {  // HTML
                $posthtml = "<p><font face=\"sans-serif\">".
                "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                "<a href=\"$CFG->wwwroot/mod/assignment/index.php?id=$course->id\">$strassignments</a> ->".
                "<a href=\"$CFG->wwwroot/mod/assignment/view.php?id=$mod->id\">".format_string($submission->name,true)."</a></font></p>";
                $posthtml .= "<hr /><font face=\"sans-serif\">";
                $posthtml .= "<p>".get_string("assignmentmailhtml", "assignment", $assignmentinfo)."</p>";
                $posthtml .= "</font><hr />";
            } else {
                $posthtml = "";
            }

            if (! email_to_user($user, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: assignment cron: Could not send out mail for id $submission->id to user $user->id ($user->email)\n";
            }
        }
    }

    return true;
}

/**
 * Return an array of grades, indexed by user, and a max grade.
 *
 * @param $assignmentid int
 * @return object with properties ->grades (an array of grades) and ->maxgrade.
 */
function assignment_grades($assignmentid) {

    if (!$assignment = get_record('assignment', 'id', $assignmentid)) {
        return NULL;
    }
    if ($assignment->grade == 0) { // No grading
        return NULL;
    }

    $grades = get_records_menu('assignment_submissions', 'assignment',
                               $assignment->id, '', 'userid,grade');

    if ($assignment->grade > 0) {
        if ($grades) {
            foreach ($grades as $userid => $grade) {
                if ($grade == -1) {
                    $grades[$userid] = '-';
                }
            }
        }
        $return->grades = $grades;
        $return->maxgrade = $assignment->grade;

    } else { // Scale
        if ($grades) {
            $scaleid = - ($assignment->grade);
            $maxgrade = "";
            if ($scale = get_record('scale', 'id', $scaleid)) {
                $scalegrades = make_menu_from_list($scale->scale);
                foreach ($grades as $userid => $grade) {
                    if (empty($scalegrades[$grade])) {
                        $grades[$userid] = '-';
                    } else {
                        $grades[$userid] = $scalegrades[$grade];
                    }
                }
                $maxgrade = $scale->name;
            }
        }
        $return->grades = $grades;
        $return->maxgrade = $maxgrade;
    }

    return $return;
}

/**
 * Returns the users with data in one assignment (students and teachers)
 *
 * @param $assignmentid int
 * @return array of user objects
 */
function assignment_get_participants($assignmentid) {

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}assignment_submissions a
                                 WHERE a.assignment = '$assignmentid' and
                                       u.id = a.userid");
    //Get teachers
    $teachers = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}assignment_submissions a
                                 WHERE a.assignment = '$assignmentid' and
                                       u.id = a.teacher");

    //Add teachers to students
    if ($teachers) {
        foreach ($teachers as $teacher) {
            $students[$teacher->id] = $teacher;
        }
    }
    //Return students array (it contains an array of unique users)
    return ($students);
}

/**
 * Checks if a scale is being used by an assignment
 *
 * This is used by the backup code to decide whether to back up a scale
 * @param $assignmentid int
 * @param $scaleid int
 * @return boolean True if the scale is used by the assignment
 */
function assignment_scale_used ($assignmentid, $scaleid) {

    $return = false;

    $rec = get_record('assignment','id',$assignmentid,'grade',-$scaleid);

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Make sure up-to-date events are created for all assignment instances
 *
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every assignment event in the site is checked, else
 * only assignment events belonging to the course specified are checked.
 * This function is used, in its new format, by restore_refresh_events()
 *
 * @param $courseid int optional If zero then all assignments for all courses are covered
 * @return boolean Always returns true
 */
function assignment_refresh_events($courseid = 0) {

    if ($courseid == 0) {
        if (! $assignments = get_records("assignment")) {
            return true;
        }
    } else {
        if (! $assignments = get_records("assignment", "course", $courseid)) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'assignment');

    foreach ($assignments as $assignment) {
        $event = NULL;
        $event->name        = addslashes($assignment->name);
        $event->description = addslashes($assignment->description);
        $event->timestart   = $assignment->timedue;

        if ($event->id = get_field('event', 'id', 'modulename', 'assignment', 'instance', $assignment->id)) {
            update_event($event);

        } else {
            $event->courseid    = $assignment->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'assignment';
            $event->instance    = $assignment->id;
            $event->eventtype   = 'due';
            $event->timeduration = 0;
            $event->visible     = get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $assignment->id);
            add_event($event);
        }

    }
    return true;
}

/**
 * Print recent activity from all assignments in a given course
 *
 * This is used by the recent activity block
 */
function assignment_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    $content = false;
    $assignments = array();

    if (!$logs = get_records_select('log', 'time > \''.$timestart.'\' AND '.
                                           'course = \''.$course->id.'\' AND '.
                                           'module = \'assignment\' AND '.
                                           'action = \'upload\' ', 'time ASC')) {
        return false;
    }

    foreach ($logs as $log) {
        //Create a temp valid module structure (course,id)
        $tempmod = new object();
        $tempmod->course = $log->course;
        $tempmod->id = $log->info;
        //Obtain the visible property from the instance
        $modvisible = instance_is_visible($log->module,$tempmod);

        //Only if the mod is visible
        if ($modvisible) {
            if ($info = assignment_log_info($log)) {
                $assignments[$log->info] = $info;
                $assignments[$log->info]->time = $log->time;
                $assignments[$log->info]->url  = str_replace('&', '&amp;', $log->url);
            }
        }
    }

    if (!empty($assignments)) {
        print_headline(get_string('newsubmissions', 'assignment').':');
        foreach ($assignments as $assignment) {
            print_recent_activity_note($assignment->time, $assignment, $assignment->name,
                                       $CFG->wwwroot.'/mod/assignment/'.$assignment->url);
        }
        $content = true;
    }

    return $content;
}


/**
 * Returns all assignments since a given time.
 *
 * If assignment is specified then this restricts the results
 */
function assignment_get_recent_mod_activity(&$activities, &$index, $sincetime, $courseid, $assignment="0", $user="", $groupid="")  {

    global $CFG;

    if ($assignment) {
        $assignmentselect = " AND cm.id = '$assignment'";
    } else {
        $assignmentselect = "";
    }
    if ($user) {
        $userselect = " AND u.id = '$user'";
    } else {
        $userselect = "";
    }

    $assignments = get_records_sql("SELECT asub.*, u.firstname, u.lastname, u.picture, u.id as userid,
                                           a.grade as maxgrade, name, cm.instance, cm.section, a.assignmenttype
                                  FROM {$CFG->prefix}assignment_submissions asub,
                                       {$CFG->prefix}user u,
                                       {$CFG->prefix}assignment a,
                                       {$CFG->prefix}course_modules cm
                                 WHERE asub.timemodified > '$sincetime'
                                   AND asub.userid = u.id $userselect
                                   AND a.id = asub.assignment $assignmentselect
                                   AND cm.course = '$courseid'
                                   AND cm.instance = a.id
                                 ORDER BY asub.timemodified ASC");

    if (empty($assignments))
      return;

    foreach ($assignments as $assignment) {
        if (empty($groupid) || ismember($groupid, $assignment->userid)) {

          $tmpactivity = new Object;

          $tmpactivity->type = "assignment";
          $tmpactivity->defaultindex = $index;
          $tmpactivity->instance = $assignment->instance;
          $tmpactivity->name = $assignment->name;
          $tmpactivity->section = $assignment->section;

          $tmpactivity->content->grade = $assignment->grade;
          $tmpactivity->content->maxgrade = $assignment->maxgrade;
          $tmpactivity->content->type = $assignment->assignmenttype;

          $tmpactivity->user->userid = $assignment->userid;
          $tmpactivity->user->fullname = fullname($assignment);
          $tmpactivity->user->picture = $assignment->picture;

          $tmpactivity->timestamp = $assignment->timemodified;

          $activities[] = $tmpactivity;

          $index++;
        }
    }

    return;
}

/**
 * Print recent activity from all assignments in a given course
 *
 * This is used by course/recent.php
 */
function assignment_print_recent_mod_activity($activity, $course, $detail=false)  {
    global $CFG;

    echo '<table border="0" cellpadding="3" cellspacing="0">';

    echo "<tr><td class=\"userpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($activity->user->userid, $course, $activity->user->picture);
    echo "</td><td width=\"100%\"><font size=2>";

    if ($detail) {
        echo "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
             "height=16 width=16 alt=\"$activity->type\">  ";
        echo "<a href=\"$CFG->wwwroot/mod/assignment/view.php?id=" . $activity->instance . "\">"
             . format_string($activity->name,true) . "</a> - ";

    }

    if (has_capability('moodle/course:viewrecent', get_context_instance(CONTEXT_COURSE, $course))) {
        $grades = "(" .  $activity->content->grade . " / " . $activity->content->maxgrade . ") ";

        $assignment->id = $activity->instance;
        $assignment->course = $course;
        $user->id = $activity->user->userid;

        echo $grades;
        echo "<br />";
    }
    echo "<a href=\"$CFG->wwwroot/user/view.php?id="
         . $activity->user->userid . "&amp;course=$course\">"
         . $activity->user->fullname . "</a> ";

    echo " - " . userdate($activity->timestamp);

    echo "</font></td></tr>";
    echo "</table>";
}

/// GENERIC SQL FUNCTIONS

/**
 * Fetch info from logs
 *
 * @param $log object with properties ->info (the assignment id) and ->userid
 * @return array with assignment name and user firstname and lastname
 */
function assignment_log_info($log) {
    global $CFG;
    return get_record_sql("SELECT a.name, u.firstname, u.lastname
                             FROM {$CFG->prefix}assignment a, 
                                  {$CFG->prefix}user u
                            WHERE a.id = '$log->info' 
                              AND u.id = '$log->userid'");
}

/**
 * Return list of marked submissions that have not been mailed out for currently enrolled students
 *
 * @return array
 */
function assignment_get_unmailed_submissions($starttime, $endtime) {

    global $CFG;
    
    return get_records_sql("SELECT s.*, a.course, a.name
                              FROM {$CFG->prefix}assignment_submissions s, 
                                   {$CFG->prefix}assignment a
                             WHERE s.mailed = 0 
                               AND s.timemarked <= $endtime 
                               AND s.timemarked >= $starttime
                               AND s.assignment = a.id");

    /* return get_records_sql("SELECT s.*, a.course, a.name
                              FROM {$CFG->prefix}assignment_submissions s, 
                                   {$CFG->prefix}assignment a,
                                   {$CFG->prefix}user_students us
                             WHERE s.mailed = 0 
                               AND s.timemarked <= $endtime 
                               AND s.timemarked >= $starttime
                               AND s.assignment = a.id
                               AND s.userid = us.userid
                               AND a.course = us.course");
    */
}

/**
 * Counts all real assignment submissions by ENROLLED students (not empty ones)
 *
 * There are also assignment type methods count_real_submissions() wich in the default
 * implementation simply call this function.
 * @param $groupid int optional If nonzero then count is restricted to this group
 * @return int The number of submissions
 */
function assignment_count_real_submissions($assignment, $groupid=0) {
    global $CFG;

    if ($groupid) {     /// How many in a particular group?
        return count_records_sql("SELECT COUNT(DISTINCT g.userid, g.groupid)
                                     FROM {$CFG->prefix}assignment_submissions a,
                                          {$CFG->prefix}groups_members g
                                    WHERE a.assignment = $assignment->id 
                                      AND a.timemodified > 0
                                      AND g.groupid = '$groupid' 
                                      AND a.userid = g.userid ");
    } else {
        $cm = get_coursemodule_from_instance('assignment', $assignment->id);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        // this is all the users with this capability set, in this context or higher
        if ($users = get_users_by_capability($context, 'mod/assignment:submit')) {
            foreach ($users as $user) {
                $array[] = $user->id;
            }

            $userlists = '('.implode(',',$array).')';

            return count_records_sql("SELECT COUNT(*)
                                      FROM {$CFG->prefix}assignment_submissions
                                     WHERE assignment = '$assignment->id' 
                                       AND timemodified > 0
                                       AND userid IN $userlists ");
        } else {
            return 0; // no users enroled in course
        }
    }
}


/**
 * Return all assignment submissions by ENROLLED students (even empty)
 *
 * There are also assignment type methods get_submissions() wich in the default
 * implementation simply call this function.
 * @param $sort string optional field names for the ORDER BY in the sql query
 * @param $dir string optional specifying the sort direction, defaults to DESC
 * @return array The submission objects indexed by id
 */
function assignment_get_all_submissions($assignment, $sort="", $dir="DESC") {
/// Return all assignment submissions by ENROLLED students (even empty)
    global $CFG;

    if ($sort == "lastname" or $sort == "firstname") {
        $sort = "u.$sort $dir";
    } else if (empty($sort)) {
        $sort = "a.timemodified DESC";
    } else {
        $sort = "a.$sort $dir";
    }

    /* not sure this is needed at all since assignmenet already has a course define, so this join?
    $select = "s.course = '$assignment->course' AND";
    if ($assignment->course == SITEID) {
        $select = '';
    }*/
    
    return get_records_sql("SELECT a.* 
                              FROM {$CFG->prefix}assignment_submissions a, 
                                   {$CFG->prefix}user u
                             WHERE u.id = a.userid
                               AND a.assignment = '$assignment->id' 
                          ORDER BY $sort");
    
    /* return get_records_sql("SELECT a.* 
                              FROM {$CFG->prefix}assignment_submissions a, 
                                   {$CFG->prefix}user_students s,
                                   {$CFG->prefix}user u
                             WHERE a.userid = s.userid
                               AND u.id = a.userid
                               AND $select a.assignment = '$assignment->id' 
                          ORDER BY $sort");
    */
}




/// OTHER GENERAL FUNCTIONS FOR ASSIGNMENTS  ///////////////////////////////////////

/**
 * Returns an array of installed assignment types indexed and sorted by name
 *
 * @return array The index is the name of the assignment type, the value its full name from the language strings
 */
function assignment_types() {
    $types = array();
    $names = get_list_of_plugins('mod/assignment/type');
    foreach ($names as $name) {
        $types[$name] = get_string('type'.$name, 'assignment');
    }
    asort($types);
    return $types;
}

/**
 * Executes upgrade scripts for assignment types when necessary
 */
function assignment_upgrade_submodules() {
    global $CFG;

    $types = assignment_types();

    include($CFG->dirroot.'/mod/assignment/version.php');  // defines $module with version etc

    foreach ($types as $type => $typename) {

        $fullpath = $CFG->dirroot.'/mod/assignment/type/'.$type;

    /// Check for an external version file (defines $submodule)

        if (!is_readable($fullpath .'/version.php')) {
            continue;
        }
        include_once($fullpath .'/version.php');

    /// Check whether we need to upgrade

        if (!isset($submodule->version)) {
            continue;
        }

    /// Make sure this submodule will work with this assignment version

        if (isset($submodule->requires) and ($submodule->requires > $module->version)) {
            notify("Assignment submodule '$type' is too new for your assignment");
            continue;
        }

    /// If the submodule is new, then let's install it!

        $currentversion = 'assignment_'.$type.'_version';

        if (!isset($CFG->$currentversion)) {   // First install!
            set_config($currentversion, $submodule->version);  // Must keep track of version

            if (!is_readable($fullpath .'/db/'.$CFG->dbtype.'.sql')) {
                continue;
            }

            upgrade_log_start();
            $db->debug=true;
            if (!modify_database($fullpath .'/db/'.$CFG->dbtype.'.sql')) {
                notify("Error installing tables for submodule '$type'!");
            }
            $db->debug=false;
            continue;
        }

    /// See if we need to upgrade
        
        if ($submodule->version <= $CFG->$currentversion) {
            continue;
        }

    /// Look for the upgrade file

        if (!is_readable($fullpath .'/db/'.$CFG->dbtype.'.php')) {
            continue;
        }

        include_once($fullpath .'/db/'. $CFG->dbtype .'.php');  // defines assignment_xxx_upgrade

    /// Perform the upgrade

        $upgrade_function = 'assignment_'.$type.'_upgrade';
        if (function_exists($upgrade_function)) {
            upgrade_log_start();
            $db->debug=true;
            if ($upgrade_function($CFG->$currentversion)) {
                $db->debug=false;
                set_config($currentversion, $submodule->version);
            }
            $db->debug=false;
        }
    }
}

function assignment_print_overview($courses, &$htmlarray) {

    global $USER, $CFG;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$assignments = get_all_instances_in_courses('assignment',$courses)) {
        return;
    }

    // Do assignment_base::isopen() here without loading the whole thing for speed
    foreach ($assignments as $key => $assignment) {
        $time = time();
        if ($assignment->timedue) {
            if ($assignment->preventlate) {
                $isopen = ($assignment->timeavailable <= $time && $time <= $assignment->timedue);
            } else {
                $isopen = ($assignment->timeavailable <= $time);
            }
        }
        if (empty($isopen) || empty($assignment->timedue)) {
            unset($assignments[$key]);
        }
    }

    $strduedate = get_string('duedate', 'assignment');
    $strduedateno = get_string('duedateno', 'assignment');
    $strgraded = get_string('graded', 'assignment');
    $strnotgradedyet = get_string('notgradedyet', 'assignment');
    $strnotsubmittedyet = get_string('notsubmittedyet', 'assignment');
    $strsubmitted = get_string('submitted', 'assignment');
    $strassignment = get_string('modulename', 'assignment');
    $strreviewed = get_string('reviewed','assignment');

    foreach ($assignments as $assignment) {
        $str = '<div class="assignment overview"><div class="name">'.$strassignment. ': '.
               '<a '.($assignment->visible ? '':' class="dimmed"').
               'title="'.$strassignment.'" href="'.$CFG->wwwroot.
               '/mod/assignment/view.php?id='.$assignment->coursemodule.'">'.
               $assignment->name.'</a></div>';
        if ($assignment->timedue) {
            $str .= '<div class="info">'.$strduedate.': '.userdate($assignment->timedue).'</div>';
        } else {
            $str .= '<div class="info">'.$strduedateno.'</div>';
        }
        $context = get_context_instance(CONTEXT_MODULE, $assignment->coursemodule);
        if (has_capability('mod/assignment:grade', $context)) {
            
            // count how many people can submit
            $submissions = 0; // init
            if ($students = get_users_by_capability($context, 'mod/assignment:submit')) {
                foreach ($students as $student) {
                    if (get_records_sql("SELECT id,id FROM {$CFG->prefix}assignment_submissions
                                         WHERE assignment = $assignment->id AND
                                               userid = $student->id AND
                                               teacher = 0 AND
                                               timemarked = 0")) {
                        $submissions++;  
                    }
                }
            }
            
            if ($submissions) {
                $str .= get_string('submissionsnotgraded', 'assignment', $submissions);
            }
        } else {
            $sql = "SELECT *
                      FROM {$CFG->prefix}assignment_submissions
                     WHERE userid = '$USER->id'
                       AND assignment = '{$assignment->id}'";
            if ($submission = get_record_sql($sql)) {
                if ($submission->teacher == 0 && $submission->timemarked == 0) {
                    $str .= $strsubmitted . ', ' . $strnotgradedyet;
                } else if ($submission->grade <= 0) {
                    $str .= $strsubmitted . ', ' . $strreviewed;
                } else {
                    $str .= $strsubmitted . ', ' . $strgraded;
                }
            } else {
                $str .= $strnotsubmittedyet . ' ' . assignment_display_lateness(time(), $assignment->timedue);
            }
        }
        $str .= '</div>';
        if (empty($htmlarray[$assignment->course]['assignment'])) {
            $htmlarray[$assignment->course]['assignment'] = $str;
        } else {
            $htmlarray[$assignment->course]['assignment'] .= $str;
        }
    }
}

function assignment_display_lateness($timesubmitted, $timedue) {
    if (!$timedue) {
        return '';
    }
    $time = $timedue - $timesubmitted;
    if ($time < 0) {
        $timetext = get_string('late', 'assignment', format_time($time));
        return ' (<span class="late">'.$timetext.'</span>)';
    } else {
        $timetext = get_string('early', 'assignment', format_time($time));
        return ' (<span class="early">'.$timetext.'</span>)';
    }
}

function assignment_get_view_actions() {
    return array('view');
}

function assignment_get_post_actions() {
    return array('upload');
}

?>
