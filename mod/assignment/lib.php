<?PHP  // $Id$
/**
 * assignment_base is the base class for assignment types
 *
 * This class provides all the functionality for an assignment
 */

DEFINE ('ASSIGNMENT_COUNT_WORDS', 1);
DEFINE ('ASSIGNMENT_COUNT_LETTERS', 2);

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
    var $pagetitle;
    var $usehtmleditor;
    var $defaultformat;
    var $context;
    var $type;

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
    function assignment_base($cmid='staticonly', $assignment=NULL, $cm=NULL, $course=NULL) {
        global $COURSE;

        if ($cmid == 'staticonly') {
            //use static functions only!
            return;
        }

        global $CFG;

        if ($cm) {
            $this->cm = $cm;
        } else if (! $this->cm = get_coursemodule_from_id('assignment', $cmid)) {
            error('Course Module ID was incorrect');
        }

        $this->context = get_context_instance(CONTEXT_MODULE, $this->cm->id);

        if ($course) {
            $this->course = $course;
        } else if ($this->cm->course == $COURSE->id) {
            $this->course = $COURSE;
        } else if (! $this->course = get_record('course', 'id', $this->cm->course)) {
            error('Course is misconfigured');
        }

        if ($assignment) {
            $this->assignment = $assignment;
        } else if (! $this->assignment = get_record('assignment', 'id', $this->cm->instance)) {
            error('assignment ID was incorrect');
        }

        $this->assignment->cmidnumber = $this->cm->id;     // compatibility with modedit assignment obj
        $this->assignment->courseid   = $this->course->id; // compatibility with modedit assignment obj

        $this->strassignment = get_string('modulename', 'assignment');
        $this->strassignments = get_string('modulenameplural', 'assignment');
        $this->strsubmissions = get_string('submissions', 'assignment');
        $this->strlastmodified = get_string('lastmodified');
        $this->pagetitle = strip_tags($this->course->shortname.': '.$this->strassignment.': '.format_string($this->assignment->name,true));

        // visibility handled by require_login() with $cm parameter
        // get current group only when really needed

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
            $navigation = build_navigation($subpage, $this->cm);
        } else {
            $navigation = build_navigation('', $this->cm);
        }

        print_header($this->pagetitle, $this->course->fullname, $navigation, '', '',
                     true, update_module_button($this->cm->id, $this->course->id, $this->strassignment),
                     navmenu($this->course, $this->cm));

        groups_print_activity_menu($this->cm, $CFG->wwwroot . '/mod/assignment/view.php?id=' . $this->cm->id);

        echo '<div class="reportlink">'.$this->submittedlink().'</div>';
        echo '<div class="clearer"></div>';
    }


    /**
     * Display the assignment intro
     *
     * This will most likely be extended by assignment type plug-ins
     * The default implementation prints the assignment description in a box
     */
    function view_intro() {
        print_simple_box_start('center', '', '', 0, 'generalbox', 'intro');
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

        print_simple_box_start('center', '', '', 0, 'generalbox', 'dates');
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
        global $USER, $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        if (!has_capability('mod/assignment:submit', $this->context, $USER->id, false)) {
            // can not submit assignments -> no feedback
            return;
        }

        if (!$submission) { /// Get submission for this assignment
            $submission = $this->get_submission($USER->id);
        }

        $grading_info = grade_get_grades($this->course->id, 'mod', 'assignment', $this->assignment->id, $USER->id);
        $item = $grading_info->items[0];
        $grade = $item->grades[$USER->id];

        if ($grade->hidden or $grade->grade === false) { // hidden or error
            return;
        }

        if ($grade->grade === null and empty($grade->str_feedback)) {   /// Nothing to show yet
            return;
        }

        $graded_date = $grade->dategraded;
        $graded_by   = $grade->usermodified;

    /// We need the teacher info
        if (!$teacher = get_record('user', 'id', $graded_by)) {
            error('Could not find the teacher');
        }

    /// Print the feedback
        print_heading(get_string('feedbackfromteacher', 'assignment', $this->course->teacher)); // TODO: fix teacher string

        echo '<table cellspacing="0" class="feedback">';

        echo '<tr>';
        echo '<td class="left picture">';
        if ($teacher) {
            print_user_picture($teacher, $this->course->id, $teacher->picture);
        }
        echo '</td>';
        echo '<td class="topic">';
        echo '<div class="from">';
        if ($teacher) {
            echo '<div class="fullname">'.fullname($teacher).'</div>';
        }
        echo '<div class="time">'.userdate($graded_date).'</div>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td class="left side">&nbsp;</td>';
        echo '<td class="content">';
        echo '<div class="grade">';
        echo get_string("grade").': '.$grade->str_long_grade;
        echo '</div>';
        echo '<div class="clearer"></div>';

        echo '<div class="comment">';
        echo $grade->str_feedback;
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
     * @param bool $allgroup print all groups info if user can access all groups, suitable for index.php
     * @return string
     */
    function submittedlink($allgroups=false) {
        global $USER;

        $submitted = '';

        $context = get_context_instance(CONTEXT_MODULE,$this->cm->id);
        if (has_capability('mod/assignment:grade', $context)) {
            if ($allgroups and has_capability('moodle/site:accessallgroups', $context)) {
                $group = 0;
            } else {
                $group = groups_get_activity_group($this->cm);
            }
            if ($count = $this->count_real_submissions($group)) {
                $submitted = '<a href="submissions.php?id='.$this->cm->id.'">'.
                             get_string('viewsubmissions', 'assignment', $count).'</a>';
            } else {
                $submitted = '<a href="submissions.php?id='.$this->cm->id.'">'.
                             get_string('noattempts', 'assignment').'</a>';
            }
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


    function setup_elements(&$mform) {

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
        global $COURSE;

        $assignment->timemodified = time();
        $assignment->courseid = $assignment->course;

        if ($returnid = insert_record("assignment", $assignment)) {
            $assignment->id = $returnid;

            if ($assignment->timedue) {
                $event = new object();
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

            $assignment = stripslashes_recursive($assignment);
            assignment_grade_item_update($assignment);

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

        $assignment->courseid = $assignment->course;

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

        // delete file area with all attachments - ignore errors
        require_once($CFG->libdir.'/filelib.php');
        fulldelete($CFG->dataroot.'/'.$assignment->course.'/'.$CFG->moddata.'/assignment/'.$assignment->id);

        assignment_grade_item_delete($assignment);

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
        global $COURSE;

        $assignment->timemodified = time();

        $assignment->id = $assignment->instance;
        $assignment->courseid = $assignment->course;

        if (!update_record('assignment', $assignment)) {
            return false;
        }

        if ($assignment->timedue) {
            $event = new object();

            if ($event->id = get_field('event', 'id', 'modulename', 'assignment', 'instance', $assignment->id)) {

                $event->name        = $assignment->name;
                $event->description = $assignment->description;
                $event->timestart   = $assignment->timedue;

                update_event($event);
            } else {
                $event = new object();
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

        // get existing grade item
        $assignment = stripslashes_recursive($assignment);

        assignment_grade_item_update($assignment);

        return true;
    }

    /**
     * Update grade item for this submission.
     */
    function update_grade($submission) {
        assignment_update_grades($this->assignment, $submission->userid);
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

        $mailinfo = optional_param('mailinfo', null, PARAM_BOOL);
        if (is_null($mailinfo)) {
            $mailinfo = get_user_preferences('assignment_mailinfo', 0);
        } else {
            set_user_preference('assignment_mailinfo', $mailinfo);
        }

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

                    $this->process_outcomes($id);

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
                    if ($updatedb) {
                        $submission->mailed = (int)(!$mailinfo);
                    }

                    $submission->timemarked = time();

                    //if it is not an update, we don't change the last modified time etc.
                    //this will also not write into database if no submissioncomment and grade is entered.

                    if ($updatedb){
                        if ($newsubmission) {
                            if (!isset($submission->submissioncomment)) {
                                $submission->submissioncomment = '';
                            }
                            if (!$sid = insert_record('assignment_submissions', $submission)) {
                                return false;
                            }
                            $submission->id = $sid;
                        } else {
                            if (!update_record('assignment_submissions', $submission)) {
                                return false;
                            }
                        }

                        // triger grade event
                        $this->update_grade($submission);

                        //add to log only if updating
                        add_to_log($this->course->id, 'assignment', 'update grades',
                                   'submissions.php?id='.$this->assignment->id.'&user='.$submission->userid,
                                   $submission->userid, $this->cm->id);
                    }

                }

                $message = notify(get_string('changessaved'), 'notifysuccess', 'center', true);

                $this->display_submissions($message);
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
        global $SESSION, $CFG;

        $output = '';

        $perpage = get_user_preferences('assignment_perpage', 10);

        $quickgrade = get_user_preferences('assignment_quickgrade', 0);

        /// Run some Javascript to try and update the parent page
        $output .= '<script type="text/javascript">'."\n<!--\n";
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['submissioncomment'])) {
            if ($quickgrade){
                $output.= 'opener.document.getElementById("submissioncomment'.$submission->userid.'").value="'
                .trim($submission->submissioncomment).'";'."\n";
             } else {
                $output.= 'opener.document.getElementById("com'.$submission->userid.
                '").innerHTML="'.shorten_text(trim(strip_tags($submission->submissioncomment)), 15)."\";\n";
            }
        }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['grade'])) {
            //echo optional_param('menuindex');
            if ($quickgrade){
                $output.= 'opener.document.getElementById("menumenu'.$submission->userid.
                '").selectedIndex="'.optional_param('menuindex', 0, PARAM_INT).'";'."\n";
            } else {
                $output.= 'opener.document.getElementById("g'.$submission->userid.'").innerHTML="'.
                $this->display_grade($submission->grade)."\";\n";
            }
        }
        //need to add student's assignments in there too.
        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['timemodified']) &&
            $submission->timemodified) {
            $output.= 'opener.document.getElementById("ts'.$submission->userid.
                 '").innerHTML="'.addslashes_js($this->print_student_answer($submission->userid)).userdate($submission->timemodified)."\";\n";
        }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['timemarked']) &&
            $submission->timemarked) {
            $output.= 'opener.document.getElementById("tt'.$submission->userid.
                 '").innerHTML="'.userdate($submission->timemarked)."\";\n";
        }

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['status'])) {
            $output.= 'opener.document.getElementById("up'.$submission->userid.'").className="s1";';
            $buttontext = get_string('update');
            $button = link_to_popup_window ('/mod/assignment/submissions.php?id='.$this->cm->id.'&amp;userid='.$submission->userid.'&amp;mode=single'.'&amp;offset='.(optional_param('offset', '', PARAM_INT)-1),
                      'grade'.$submission->userid, $buttontext, 450, 700, $buttontext, 'none', true, 'button'.$submission->userid);
            $output.= 'opener.document.getElementById("up'.$submission->userid.'").innerHTML="'.addslashes_js($button).'";';
        }

        $grading_info = grade_get_grades($this->course->id, 'mod', 'assignment', $this->assignment->id, $submission->userid);

        if (empty($SESSION->flextable['mod-assignment-submissions']->collapse['finalgrade'])) {
            $output.= 'opener.document.getElementById("finalgrade_'.$submission->userid.
            '").innerHTML="'.$grading_info->items[0]->grades[$submission->userid]->str_grade.'";'."\n";
        }

        if (!empty($CFG->enableoutcomes) and empty($SESSION->flextable['mod-assignment-submissions']->collapse['outcome'])) {

            if (!empty($grading_info->outcomes)) {
                foreach($grading_info->outcomes as $n=>$outcome) {
                    if ($outcome->grades[$submission->userid]->locked) {
                        continue;
                    }

                    if ($quickgrade){
                        $output.= 'opener.document.getElementById("outcome_'.$n.'_'.$submission->userid.
                        '").selectedIndex="'.$outcome->grades[$submission->userid]->grade.'";'."\n";

                    } else {
                        $options = make_grades_menu(-$outcome->scaleid);
                        $options[0] = get_string('nooutcome', 'grades');
                        $output.= 'opener.document.getElementById("outcome_'.$n.'_'.$submission->userid.'").innerHTML="'.$options[$outcome->grades[$submission->userid]->grade]."\";\n";
                    }

                }
            }
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
        require_once($CFG->libdir.'/gradelib.php');
        require_once($CFG->libdir.'/tablelib.php');

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

        $grading_info = grade_get_grades($this->course->id, 'mod', 'assignment', $this->assignment->id, array($user->id));
        $disabled = $grading_info->items[0]->grades[$userid]->locked || $grading_info->items[0]->grades[$userid]->overridden;

    /// construct SQL, using current offset to find the data of the next student
        $course     = $this->course;
        $assignment = $this->assignment;
        $cm         = $this->cm;
        $context    = get_context_instance(CONTEXT_MODULE, $cm->id);

        /// Get all ppl that can submit assignments

        $currentgroup = groups_get_activity_group($cm);
        if ($users = get_users_by_capability($context, 'mod/assignment:submit', 'u.id', '', '', '', $currentgroup, '', false)) {
            $users = array_keys($users);
        }

        // if groupmembersonly used, remove users who are not in any group
        if ($users and !empty($CFG->enablegroupings) and $cm->groupmembersonly) {
            if ($groupingusers = groups_get_grouping_members($cm->groupingid, 'u.id', 'u.id')) {
                $users = array_intersect($users, array_keys($groupingusers));
            }
        }

        $nextid = 0;

        if ($users) {
            $select = 'SELECT u.id, u.firstname, u.lastname, u.picture, u.imagealt,
                              s.id AS submissionid, s.grade, s.submissioncomment,
                              s.timemodified, s.timemarked,
                              COALESCE(SIGN(SIGN(s.timemarked) + SIGN(s.timemarked - s.timemodified)), 0) AS status ';
            $sql = 'FROM '.$CFG->prefix.'user u '.
                   'LEFT JOIN '.$CFG->prefix.'assignment_submissions s ON u.id = s.userid
                                                                      AND s.assignment = '.$this->assignment->id.' '.
                   'WHERE u.id IN ('.implode(',', $users).') ';

            if ($sort = flexible_table::get_sql_sort('mod-assignment-submissions')) {
                $sort = 'ORDER BY '.$sort.' ';
            }

            if (($auser = get_records_sql($select.$sql.$sort, $offset+1, 1)) !== false) {
                $nextuser = array_shift($auser);
            /// Calculate user status
                $nextuser->status = ($nextuser->timemarked > 0) && ($nextuser->timemarked >= $nextuser->timemodified);
                $nextid = $nextuser->id;
            }
        }

        print_header(get_string('feedback', 'assignment').':'.fullname($user, true).':'.format_string($this->assignment->name));

        /// Print any extra javascript needed for saveandnext
        echo $extra_javascript;

        ///SOme javascript to help with setting up >.>

        echo '<script type="text/javascript">'."\n";
        echo 'function setNext(){'."\n";
        echo 'document.getElementById(\'submitform\').mode.value=\'next\';'."\n";
        echo 'document.getElementById(\'submitform\').userid.value="'.$nextid.'";'."\n";
        echo '}'."\n";

        echo 'function saveNext(){'."\n";
        echo 'document.getElementById(\'submitform\').mode.value=\'saveandnext\';'."\n";
        echo 'document.getElementById(\'submitform\').userid.value="'.$nextid.'";'."\n";
        echo 'document.getElementById(\'submitform\').saveuserid.value="'.$userid.'";'."\n";
        echo 'document.getElementById(\'submitform\').menuindex.value = document.getElementById(\'submitform\').grade.selectedIndex;'."\n";
        echo '}'."\n";

        echo '</script>'."\n";
        echo '<table cellspacing="0" class="feedback '.$subtype.'" >';

        ///Start of teacher info row

        echo '<tr>';
        echo '<td class="picture teacher">';
        if ($submission->teacher) {
            $teacher = get_record('user', 'id', $submission->teacher);
        } else {
            global $USER;
            $teacher = $USER;
        }
        print_user_picture($teacher, $this->course->id, $teacher->picture);
        echo '</td>';
        echo '<td class="content">';
        echo '<form id="submitform" action="submissions.php" method="post">';
        echo '<div>'; // xhtml compatibility - invisiblefieldset was breaking layout here
        echo '<input type="hidden" name="offset" value="'.($offset+1).'" />';
        echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
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
        echo '<div class="grade"><label for="menugrade">'.get_string('grade').'</label> ';
        choose_from_menu(make_grades_menu($this->assignment->grade), 'grade', $submission->grade, get_string('nograde'), '', -1, false, $disabled);
        echo '</div>';

        echo '<div class="clearer"></div>';
        echo '<div class="finalgrade">'.get_string('finalgrade', 'grades').': '.$grading_info->items[0]->grades[$userid]->str_grade.'</div>';
        echo '<div class="clearer"></div>';

        if (!empty($CFG->enableoutcomes)) {
            foreach($grading_info->outcomes as $n=>$outcome) {
                echo '<div class="outcome"><label for="menuoutcome_'.$n.'">'.$outcome->name.'</label> ';
                $options = make_grades_menu(-$outcome->scaleid);
                if ($outcome->grades[$submission->userid]->locked) {
                    $options[0] = get_string('nooutcome', 'grades');
                    echo $options[$outcome->grades[$submission->userid]->grade];
                } else {
                    choose_from_menu($options, 'outcome_'.$n.'['.$userid.']', $outcome->grades[$submission->userid]->grade, get_string('nooutcome', 'grades'), '', 0, false, false, 0, 'menuoutcome_'.$n);
                }
                echo '</div>';
                echo '<div class="clearer"></div>';
            }
        }


        $this->preprocess_submission($submission);

        if ($disabled) {
            echo '<div class="disabledfeedback">'.$grading_info->items[0]->grades[$userid]->str_feedback.'</div>';

        } else {
            print_textarea($this->usehtmleditor, 14, 58, 0, 0, 'submissioncomment', $submission->submissioncomment, $this->course->id);
            if ($this->usehtmleditor) {
                echo '<input type="hidden" name="format" value="'.FORMAT_HTML.'" />';
            } else {
                echo '<div class="format">';
                choose_from_menu(format_text_menu(), "format", $submission->format, "");
                helpbutton("textformat", get_string("helpformatting"));
                echo '</div>';
            }
        }

        $lastmailinfo = get_user_preferences('assignment_mailinfo', 1) ? 'checked="checked"' : '';

        ///Print Buttons in Single View
        echo '<input type="hidden" name="mailinfo" value="0" />';
        echo '<input type="checkbox" id="mailinfo" name="mailinfo" value="1" '.$lastmailinfo.' /><label for="mailinfo">'.get_string('enableemailnotification','assignment').'</label>';
        echo '<div class="buttons">';
        echo '<input type="submit" name="submit" value="'.get_string('savechanges').'" onclick = "document.getElementById(\'submitform\').menuindex.value = document.getElementById(\'submitform\').grade.selectedIndex" />';
        echo '<input type="submit" name="cancel" value="'.get_string('cancel').'" />';
        //if there are more to be graded.
        if ($nextid) {
            echo '<input type="submit" name="saveandnext" value="'.get_string('saveandnext').'" onclick="saveNext()" />';
            echo '<input type="submit" name="next" value="'.get_string('next').'" onclick="setNext();" />';
        }
        echo '</div>';
        echo '</div></form>';

        $customfeedback = $this->custom_feedbackform($submission, true);
        if (!empty($customfeedback)) {
            echo $customfeedback;
        }

        echo '</td></tr>';

        ///End of teacher info row, Start of student info row
        echo '<tr>';
        echo '<td class="picture user">';
        print_user_picture($user, $this->course->id, $user->picture);
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

        if (!$disabled and $this->usehtmleditor) {
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
    function display_submissions($message='') {
        global $CFG, $db, $USER;
        require_once($CFG->libdir.'/gradelib.php');

        /* first we check to see if the form has just been submitted
         * to request user_preference updates
         */

        if (isset($_POST['updatepref'])){
            $perpage = optional_param('perpage', 10, PARAM_INT);
            $perpage = ($perpage <= 0) ? 10 : $perpage ;
            set_user_preference('assignment_perpage', $perpage);
            set_user_preference('assignment_quickgrade', optional_param('quickgrade', 0, PARAM_BOOL));
        }

        /* next we get perpage and quickgrade (allow quick grade) params
         * from database
         */
        $perpage    = get_user_preferences('assignment_perpage', 10);

        $quickgrade = get_user_preferences('assignment_quickgrade', 0);

        $grading_info = grade_get_grades($this->course->id, 'mod', 'assignment', $this->assignment->id);

        if (!empty($CFG->enableoutcomes) and !empty($grading_info->outcomes)) {
            $uses_outcomes = true;
        } else {
            $uses_outcomes = false;
        }

        $page    = optional_param('page', 0, PARAM_INT);
        $strsaveallfeedback = get_string('saveallfeedback', 'assignment');

    /// Some shortcuts to make the code read better

        $course     = $this->course;
        $assignment = $this->assignment;
        $cm         = $this->cm;

        $tabindex = 1; //tabindex for quick grading tabbing; Not working for dropdowns yet
        add_to_log($course->id, 'assignment', 'view submission', 'submissions.php?id='.$this->cm->id, $this->assignment->id, $this->cm->id);
        $navigation = build_navigation($this->strsubmissions, $this->cm);
        print_header_simple(format_string($this->assignment->name,true), "", $navigation,
                '', '', true, update_module_button($cm->id, $course->id, $this->strassignment), navmenu($course, $cm));

        $course_context = get_context_instance(CONTEXT_COURSE, $course->id);
        if (has_capability('gradereport/grader:view', $course_context) && has_capability('moodle/grade:viewall', $course_context)) {
            echo '<div class="allcoursegrades"><a href="' . $CFG->wwwroot . '/grade/report/grader/index.php?id=' . $course->id . '">'
                . get_string('seeallcoursegrades', 'grades') . '</a></div>';
        }

        if (!empty($message)) {
            echo $message;   // display messages here if any
        }

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    /// Check to see if groups are being used in this assignment

        /// find out current groups mode
        $groupmode = groups_get_activity_groupmode($cm);
        $currentgroup = groups_get_activity_group($cm, true);
        groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/assignment/submissions.php?id=' . $this->cm->id);

        /// Get all ppl that are allowed to submit assignments
        if ($users = get_users_by_capability($context, 'mod/assignment:submit', 'u.id', '', '', '', $currentgroup, '', false)) {
            $users = array_keys($users);
        }

        // if groupmembersonly used, remove users who are not in any group
        if ($users and !empty($CFG->enablegroupings) and $cm->groupmembersonly) {
            if ($groupingusers = groups_get_grouping_members($cm->groupingid, 'u.id', 'u.id')) {
                $users = array_intersect($users, array_keys($groupingusers));
            }
        }

        $tablecolumns = array('picture', 'fullname', 'grade', 'submissioncomment', 'timemodified', 'timemarked', 'status', 'finalgrade');
        if ($uses_outcomes) {
            $tablecolumns[] = 'outcome'; // no sorting based on outcomes column
        }

        $tableheaders = array('',
                              get_string('fullname'),
                              get_string('grade'),
                              get_string('comment', 'assignment'),
                              get_string('lastmodified').' ('.$course->student.')',
                              get_string('lastmodified').' ('.$course->teacher.')',
                              get_string('status'),
                              get_string('finalgrade', 'grades'));
        if ($uses_outcomes) {
            $tableheaders[] = get_string('outcome', 'grades');
        }

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
        $table->column_class('finalgrade', 'finalgrade');
        if ($uses_outcomes) {
            $table->column_class('outcome', 'outcome');
        }

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'attempts');
        $table->set_attribute('class', 'submissions');
        $table->set_attribute('width', '100%');
        //$table->set_attribute('align', 'center');

        $table->no_sorting('finalgrade');
        $table->no_sorting('outcome');

        // Start working -- this is necessary as soon as the niceties are over
        $table->setup();

        if (empty($users)) {
            print_heading(get_string('nosubmitusers','assignment'));
            return true;
        }

    /// Construct the SQL

        if ($where = $table->get_sql_where()) {
            $where .= ' AND ';
        }

        if ($sort = $table->get_sql_sort()) {
            $sort = ' ORDER BY '.$sort;
        }

        $select = 'SELECT u.id, u.firstname, u.lastname, u.picture, u.imagealt,
                          s.id AS submissionid, s.grade, s.submissioncomment,
                          s.timemodified, s.timemarked,
                          COALESCE(SIGN(SIGN(s.timemarked) + SIGN(s.timemarked - s.timemodified)), 0) AS status ';
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'assignment_submissions s ON u.id = s.userid
                                                                  AND s.assignment = '.$this->assignment->id.' '.
               'WHERE '.$where.'u.id IN ('.implode(',',$users).') ';

        $table->pagesize($perpage, count($users));

        ///offset used to calculate index of student in that particular query, needed for the pop up to know who's next
        $offset = $page * $perpage;

        $strupdate = get_string('update');
        $strgrade  = get_string('grade');
        $grademenu = make_grades_menu($this->assignment->grade);

        if (($ausers = get_records_sql($select.$sql.$sort, $table->get_page_start(), $table->get_page_size())) !== false) {
            $grading_info = grade_get_grades($this->course->id, 'mod', 'assignment', $this->assignment->id, array_keys($ausers));
            foreach ($ausers as $auser) {
                $final_grade = $grading_info->items[0]->grades[$auser->id];
                $grademax = $grading_info->items[0]->grademax;
                $final_grade->formatted_grade = round($final_grade->grade,2) .' / ' . round($grademax,2);
                $locked_overridden = 'locked';
                if ($final_grade->overridden) {
                    $locked_overridden = 'overridden';
                }

            /// Calculate user status
                $auser->status = ($auser->timemarked > 0) && ($auser->timemarked >= $auser->timemodified);
                $picture = print_user_picture($auser, $course->id, $auser->picture, false, true);

                if (empty($auser->submissionid)) {
                    $auser->grade = -1; //no submission yet
                }

                if (!empty($auser->submissionid)) {
                ///Prints student answer and student modified date
                ///attach file or print link to student answer, depending on the type of the assignment.
                ///Refer to print_student_answer in inherited classes.
                    if ($auser->timemodified > 0) {
                        $studentmodified = '<div id="ts'.$auser->id.'">'.$this->print_student_answer($auser->id)
                                         . userdate($auser->timemodified).'</div>';
                    } else {
                        $studentmodified = '<div id="ts'.$auser->id.'">&nbsp;</div>';
                    }
                ///Print grade, dropdown or text
                    if ($auser->timemarked > 0) {
                        $teachermodified = '<div id="tt'.$auser->id.'">'.userdate($auser->timemarked).'</div>';

                        if ($final_grade->locked or $final_grade->overridden) {
                            $grade = '<div id="g'.$auser->id.'" class="'. $locked_overridden .'">'.$final_grade->formatted_grade.'</div>';
                        } else if ($quickgrade) {
                            $menu = choose_from_menu(make_grades_menu($this->assignment->grade),
                                                     'menu['.$auser->id.']', $auser->grade,
                                                     get_string('nograde'),'',-1,true,false,$tabindex++);
                            $grade = '<div id="g'.$auser->id.'">'. $menu .'</div>';
                        } else {
                            $grade = '<div id="g'.$auser->id.'">'.$this->display_grade($auser->grade).'</div>';
                        }

                    } else {
                        $teachermodified = '<div id="tt'.$auser->id.'">&nbsp;</div>';
                        if ($final_grade->locked or $final_grade->overridden) {
                            $grade = '<div id="g'.$auser->id.'" class="'. $locked_overridden .'">'.$final_grade->formatted_grade.'</div>';
                        } else if ($quickgrade) {
                            $menu = choose_from_menu(make_grades_menu($this->assignment->grade),
                                                     'menu['.$auser->id.']', $auser->grade,
                                                     get_string('nograde'),'',-1,true,false,$tabindex++);
                            $grade = '<div id="g'.$auser->id.'">'.$menu.'</div>';
                        } else {
                            $grade = '<div id="g'.$auser->id.'">'.$this->display_grade($auser->grade).'</div>';
                        }
                    }
                ///Print Comment
                    if ($final_grade->locked or $final_grade->overridden) {
                        $comment = '<div id="com'.$auser->id.'">'.shorten_text(strip_tags($final_grade->str_feedback),15).'</div>';

                    } else if ($quickgrade) {
                        $comment = '<div id="com'.$auser->id.'">'
                                 . '<textarea tabindex="'.$tabindex++.'" name="submissioncomment['.$auser->id.']" id="submissioncomment'
                                 . $auser->id.'" rows="2" cols="20">'.($auser->submissioncomment).'</textarea></div>';
                    } else {
                        $comment = '<div id="com'.$auser->id.'">'.shorten_text(strip_tags($auser->submissioncomment),15).'</div>';
                    }
                } else {
                    $studentmodified = '<div id="ts'.$auser->id.'">&nbsp;</div>';
                    $teachermodified = '<div id="tt'.$auser->id.'">&nbsp;</div>';
                    $status          = '<div id="st'.$auser->id.'">&nbsp;</div>';

                    if ($final_grade->locked or $final_grade->overridden) {
                        $grade = '<div id="g'.$auser->id.'">'.$final_grade->formatted_grade . '</div>';
                    } else if ($quickgrade) {   // allow editing
                        $menu = choose_from_menu(make_grades_menu($this->assignment->grade),
                                                 'menu['.$auser->id.']', $auser->grade,
                                                 get_string('nograde'),'',-1,true,false,$tabindex++);
                        $grade = '<div id="g'.$auser->id.'">'.$menu.'</div>';
                    } else {
                        $grade = '<div id="g'.$auser->id.'">-</div>';
                    }

                    if ($final_grade->locked or $final_grade->overridden) {
                        $comment = '<div id="com'.$auser->id.'">'.$final_grade->str_feedback.'</div>';
                    } else if ($quickgrade) {
                        $comment = '<div id="com'.$auser->id.'">'
                                 . '<textarea tabindex="'.$tabindex++.'" name="submissioncomment['.$auser->id.']" id="submissioncomment'
                                 . $auser->id.'" rows="2" cols="20">'.($auser->submissioncomment).'</textarea></div>';
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
                $popup_url = '/mod/assignment/submissions.php?id='.$this->cm->id
                           . '&amp;userid='.$auser->id.'&amp;mode=single'.'&amp;offset='.$offset++;
                $button = link_to_popup_window ($popup_url, 'grade'.$auser->id, $buttontext, 600, 780,
                                                $buttontext, 'none', true, 'button'.$auser->id);

                $status  = '<div id="up'.$auser->id.'" class="s'.$auser->status.'">'.$button.'</div>';

                $finalgrade = '<span id="finalgrade_'.$auser->id.'">'.$final_grade->str_grade.'</span>';

                $outcomes = '';

                if ($uses_outcomes) {

                    foreach($grading_info->outcomes as $n=>$outcome) {
                        $outcomes .= '<div class="outcome"><label>'.$outcome->name.'</label>';
                        $options = make_grades_menu(-$outcome->scaleid);

                        if ($outcome->grades[$auser->id]->locked or !$quickgrade) {
                            $options[0] = get_string('nooutcome', 'grades');
                            $outcomes .= ': <span id="outcome_'.$n.'_'.$auser->id.'">'.$options[$outcome->grades[$auser->id]->grade].'</span>';
                        } else {
                            $outcomes .= ' ';
                            $outcomes .= choose_from_menu($options, 'outcome_'.$n.'['.$auser->id.']',
                                        $outcome->grades[$auser->id]->grade, get_string('nooutcome', 'grades'), '', 0, true, false, 0, 'outcome_'.$n.'_'.$auser->id);
                        }
                        $outcomes .= '</div>';
                    }
                }

				$userlink = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $auser->id . '&amp;course=' . $course->id . '">' . fullname($auser) . '</a>';
                $row = array($picture, $userlink, $grade, $comment, $studentmodified, $teachermodified, $status, $finalgrade);
                if ($uses_outcomes) {
                    $row[] = $outcomes;
                }

                $table->add_data($row);
            }
        }

        /// Print quickgrade form around the table
        if ($quickgrade){
            echo '<form action="submissions.php" id="fastg" method="post">';
            echo '<div>';
            echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
            echo '<input type="hidden" name="mode" value="fastgrade" />';
            echo '<input type="hidden" name="page" value="'.$page.'" />';
            echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
            echo '</div>';
        }

        $table->print_html();  /// Print the whole table

        if ($quickgrade){
            $lastmailinfo = get_user_preferences('assignment_mailinfo', 1) ? 'checked="checked"' : '';
            echo '<div class="fgcontrols">';
            echo '<div class="emailnotification">';
            echo '<label for="mailinfo">'.get_string('enableemailnotification','assignment').'</label>';
            echo '<input type="hidden" name="mailinfo" value="0" />';
            echo '<input type="checkbox" id="mailinfo" name="mailinfo" value="1" '.$lastmailinfo.' />';
            helpbutton('emailnotification', get_string('enableemailnotification', 'assignment'), 'assignment').'</p></div>';
            echo '</div>';
            echo '<div class="fastgbutton"><input type="submit" name="fastg" value="'.get_string('saveallfeedback', 'assignment').'" /></div>';
            echo '</div>';
            echo '</form>';
        }
        /// End of fast grading form

        /// Mini form for setting user preference
        echo '<div class="qgprefs">';
        echo '<form id="options" action="submissions.php?id='.$this->cm->id.'" method="post"><div>';
        echo '<input type="hidden" name="updatepref" value="1" />';
        echo '<table id="optiontable">';
        echo '<tr><td>';
        echo '<label for="perpage">'.get_string('pagesize','assignment').'</label>';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" id="perpage" name="perpage" size="1" value="'.$perpage.'" />';
        helpbutton('pagesize', get_string('pagesize','assignment'), 'assignment');
        echo '</td></tr>';
        echo '<tr><td>';
        echo '<label for="quickgrade">'.get_string('quickgrade','assignment').'</label>';
        echo '</td>';
        echo '<td>';
        $checked = $quickgrade ? 'checked="checked"' : '';
        echo '<input type="checkbox" id="quickgrade" name="quickgrade" value="1" '.$checked.' />';
        helpbutton('quickgrade', get_string('quickgrade', 'assignment'), 'assignment').'</p></div>';
        echo '</td></tr>';
        echo '<tr><td colspan="2">';
        echo '<input type="submit" value="'.get_string('savepreferences').'" />';
        echo '</td></tr></table>';
        echo '</div></form></div>';
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
        global $CFG, $USER;
        require_once($CFG->libdir.'/gradelib.php');

        if (!$feedback = data_submitted() or !confirm_sesskey()) {      // No incoming data?
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

        $grading_info = grade_get_grades($this->course->id, 'mod', 'assignment', $this->assignment->id, $feedback->userid);

        // store outcomes if needed
        $this->process_outcomes($feedback->userid);

        $submission = $this->get_submission($feedback->userid, true);  // Get or make one

        if (!$grading_info->items[0]->grades[$feedback->userid]->locked and
            !$grading_info->items[0]->grades[$feedback->userid]->overridden) {

            $submission->grade      = $feedback->grade;
            $submission->submissioncomment    = $feedback->submissioncomment;
            $submission->format     = $feedback->format;
            $submission->teacher    = $USER->id;
            $mailinfo = get_user_preferences('assignment_mailinfo', 0);
            if (!$mailinfo) {
                $submission->mailed = 1;       // treat as already mailed
            } else {
                $submission->mailed = 0;       // Make sure mail goes out (again, even)
            }
            $submission->timemarked = time();

            unset($submission->data1);  // Don't need to update this.
            unset($submission->data2);  // Don't need to update this.

            if (empty($submission->timemodified)) {   // eg for offline assignments
                // $submission->timemodified = time();
            }

            if (! update_record('assignment_submissions', $submission)) {
                return false;
            }

            // triger grade event
            $this->update_grade($submission);

            add_to_log($this->course->id, 'assignment', 'update grades',
                       'submissions.php?id='.$this->assignment->id.'&user='.$feedback->userid, $feedback->userid, $this->cm->id);
        }

        return $submission;

    }

    function process_outcomes($userid) {
        global $CFG, $USER;

        if (empty($CFG->enableoutcomes)) {
            return;
        }

        require_once($CFG->libdir.'/gradelib.php');

        if (!$formdata = data_submitted() or !confirm_sesskey()) {
            return;
        }

        $data = array();
        $grading_info = grade_get_grades($this->course->id, 'mod', 'assignment', $this->assignment->id, $userid);

        if (!empty($grading_info->outcomes)) {
            foreach($grading_info->outcomes as $n=>$old) {
                $name = 'outcome_'.$n;
                if (isset($formdata->{$name}[$userid]) and $old->grades[$userid]->grade != $formdata->{$name}[$userid]) {
                    $data[$n] = $formdata->{$name}[$userid];
                }
            }
        }
        if (count($data) > 0) {
            grade_update_outcomes('mod/assignment', $this->course->id, 'mod', 'assignment', $this->assignment->id, $userid, $data);
        }

    }

    /**
     * Load the submission object for a particular user
     *
     * @param $userid int The id of the user whose submission we want or 0 in which case USER->id is used
     * @param $createnew boolean optional Defaults to false. If set to true a new submission object will be created in the database
     * @param bool $teachermodified student submission set if false
     * @return object The submission
     */
    function get_submission($userid=0, $createnew=false, $teachermodified=false) {
        global $USER;

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $submission = get_record('assignment_submissions', 'assignment', $this->assignment->id, 'userid', $userid);

        if ($submission || !$createnew) {
            return $submission;
        }
        $newsubmission = $this->prepare_new_submission($userid, $teachermodified);
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
     * @param bool $teachermodified student submission set if false
     * @return object The submission
     */
    function prepare_new_submission($userid, $teachermodified=false) {
        $submission = new Object;
        $submission->assignment   = $this->assignment->id;
        $submission->userid       = $userid;
        //$submission->timecreated  = time();
        $submission->timecreated = '';
        // teachers should not be modifying modified date, except offline assignments
        if ($teachermodified) {
            $submission->timemodified = 0;
        } else {
            $submission->timemodified = $submission->timecreated;
        }
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
        return assignment_count_real_submissions($this->cm, $groupid);
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

        if ($teachers = $this->get_graders($user)) {

            $strassignments = get_string('modulenameplural', 'assignment');
            $strassignment  = get_string('modulename', 'assignment');
            $strsubmitted  = get_string('submitted', 'assignment');

            foreach ($teachers as $teacher) {
                $info = new object();
                $info->username = fullname($user, true);
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
     * Returns a list of teachers that should be grading given submission
     */
    function get_graders($user) {
        //potential graders
        $potgraders = get_users_by_capability($this->context, 'mod/assignment:grade', '', '', '', '', '', '', false, false);

        $graders = array();
        if (groups_get_activity_groupmode($this->cm) == SEPARATEGROUPS) {   // Separate groups are being used
            if ($groups = groups_get_all_groups($this->course->id, $user->id)) {  // Try to find all groups
                foreach ($groups as $group) {
                    foreach ($potgraders as $t) {
                        if ($t->id == $user->id) {
                            continue; // do not send self
                        }
                        if (groups_is_member($group->id, $t->id)) {
                            $graders[$t->id] = $t;
                        }
                    }
                }
            } else {
                // user not in group, try to find graders without group
                foreach ($potgraders as $t) {
                    if ($t->id == $user->id) {
                        continue; // do not send self
                    }
                    if (!groups_get_all_groups($this->course->id, $t->id)) { //ugly hack
                        $graders[$t->id] = $t;
                    }
                }
            }
        } else {
            foreach ($potgraders as $t) {
                if ($t->id == $user->id) {
                    continue; // do not send self
                }
                $graders[$t->id] = $t;
            }
        }
        return $graders;
    }

    /**
     * Creates the text content for emails to teachers
     *
     * @param $info object The info used by the 'emailteachermail' language string
     * @return string
     */
    function email_teachers_text($info) {
        $posttext  = format_string($this->course->shortname).' -> '.$this->strassignments.' -> '.
                     format_string($this->assignment->name)."\n";
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
                     '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'">'.format_string($this->course->shortname).'</a> ->'.
                     '<a href="'.$CFG->wwwroot.'/mod/assignment/index.php?id='.$this->course->id.'">'.$this->strassignments.'</a> ->'.
                     '<a href="'.$CFG->wwwroot.'/mod/assignment/view.php?id='.$this->cm->id.'">'.format_string($this->assignment->name).'</a></font></p>';
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
                    $ffurl = get_file_url("$filearea/$file", array('forcedownload'=>1));

                    $output .= '<img src="'.$CFG->pixpath.'/f/'.$icon.'" class="icon" alt="'.$icon.'" />'.
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
     * Return true if is set description is hidden till available date
     *
     * This is needed by calendar so that hidden descriptions do not
     * come up in upcoming events.
     *
     * Check that description is hidden till available date
     * By default return false
     * Assignments types should implement this method if needed
     * @return boolen
     */
    function description_is_hidden() {
        return false;
    }

    /**
     * Return an outline of the user's interaction with the assignment
     *
     * The default method prints the grade and timemodified
     * @param $grade object
     * @return object with properties ->info and ->time
     */
    function user_outline($grade) {
        $result = new object();
        $result->info = get_string('grade').': '.$grade->str_long_grade;
        $result->time = $grade->dategraded;
        return $result;
    }

    /**
     * Print complete information about the user's interaction with the assignment
     *
     * @param $user object
     */
    function user_complete($user, $grade=null) {
        if ($grade) {
            echo '<p>'.get_string('grade').': '.$grade->str_long_grade.'</p>';
            if ($grade->str_feedback) {
                echo '<p>'.get_string('feedback').': '.$grade->str_feedback.'</p>';
            }
        }

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

            $this->view_feedback($submission);

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

    /**
     * Add a get_coursemodule_info function in case any assignment type wants to add 'extra' information
     * for the course (see resource).
     *
     * Given a course_module object, this function returns any "extra" information that may be needed
     * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
     *
     * @param $coursemodule object The coursemodule object (record).
     * @return object An object on information that the coures will know about (most noticeably, an icon).
     *
     */
    function get_coursemodule_info($coursemodule) {
        return false;
    }

    /**
     * Plugin cron method - do not use $this here, create new assignment instances if needed.
     * @return void
     */
    function cron() {
        //no plugin cron by default - override if needed
    }

    /**
     * Reset all submissions
     */
    function reset_userdata($data) {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        if (!count_records('assignment', 'course', $data->courseid, 'assignmenttype', $this->type)) {
            return array(); // no assignments of this type present
        }

        $componentstr = get_string('modulenameplural', 'assignment');
        $status = array();

        $typestr = get_string('type'.$this->type, 'assignment');
        // ugly hack to support pluggable assignment type titles...
        if($typestr === '[[type'.$this->type.']]'){
            $typestr = get_string('type'.$this->type, 'assignment_'.$this->type);
        }

        if (!empty($data->reset_assignment_submissions)) {
            $assignmentssql = "SELECT a.id
                                 FROM {$CFG->prefix}assignment a
                                WHERE a.course={$data->courseid} AND a.assignmenttype='{$this->type}'";

            delete_records_select('assignment_submissions', "assignment IN ($assignmentssql)");

            if ($assignments = get_records_sql($assignmentssql)) {
                foreach ($assignments as $assignmentid=>$unused) {
                    fulldelete($CFG->dataroot.'/'.$data->courseid.'/moddata/assignment/'.$assignmentid);
                }
            }

            $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallsubmissions','assignment').': '.$typestr, 'error'=>false);

            if (empty($data->reset_gradebook_grades)) {
                // remove all grades from gradebook
                assignment_reset_gradebook($data->courseid, $this->type);
            }
        }

        /// updating dates - shift may be negative too
        if ($data->timeshift) {
            shift_course_mod_dates('assignment', array('timedue', 'timeavailable'), $data->timeshift, $data->courseid);
            $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged').': '.$typestr, 'error'=>false);
        }

        return $status;
    }

    /**
     * base implementation for backing up subtype specific information
     * for one single module
     *
     * @param filehandle $bf file handle for xml file to write to
     * @param mixed $preferences the complete backup preference object
     *
     * @return boolean
     *
     * @static
     */
    function backup_one_mod($bf, $preferences, $assignment) {
        return true;
    }

    /**
     * base implementation for backing up subtype specific information
     * for one single submission
     *
     * @param filehandle $bf file handle for xml file to write to
     * @param mixed $preferences the complete backup preference object
     * @param object $submission the assignment submission db record
     *
     * @return boolean
     *
     * @static
     */
    function backup_one_submission($bf, $preferences, $assignment, $submission) {
        return true;
    }

    /**
     * base implementation for restoring subtype specific information
     * for one single module
     *
     * @param array  $info the array representing the xml
     * @param object $restore the restore preferences
     *
     * @return boolean
     *
     * @static
     */
    function restore_one_mod($info, $restore, $assignment) {
        return true;
    }

    /**
     * base implementation for restoring subtype specific information
     * for one single submission
     *
     * @param object $submission the newly created submission
     * @param array  $info the array representing the xml
     * @param object $restore the restore preferences
     *
     * @return boolean
     *
     * @static
     */
    function restore_one_submission($info, $restore, $assignment, $submission) {
        return true;
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

    // fall back to base class if plugin missing
    $classfile = "$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php";
    if (file_exists($classfile)) {
        require_once($classfile);
        $assignmentclass = "assignment_$assignment->assignmenttype";

    } else {
        debugging("Missing assignment plug-in: {$assignment->assignmenttype}. Using base class for deleting instead.");
        $assignmentclass = "assignment_base";
    }

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

    require_once("$CFG->libdir/gradelib.php");
    require_once("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $ass = new $assignmentclass($mod->id, $assignment, $mod, $course);
    $grades = grade_get_grades($course->id, 'mod', 'assignment', $assignment->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        return $ass->user_outline(reset($grades->items[0]->grades));
    } else {
        return null;
    }
}

/**
 * Prints the complete info about a user's interaction with an assignment
 *
 * This is done by calling the user_complete() method of the assignment type class
 */
function assignment_user_complete($course, $user, $mod, $assignment) {
    global $CFG;

    require_once("$CFG->libdir/gradelib.php");
    require_once("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $ass = new $assignmentclass($mod->id, $assignment, $mod, $course);
    $grades = grade_get_grades($course->id, 'mod', 'assignment', $assignment->id, $user->id);
    if (empty($grades->items[0]->grades)) {
        $grade = false;
    } else {
        $grade = reset($grades->items[0]->grades);
    }
    return $ass->user_complete($user, $grade);
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * Finds all assignment notifications that have yet to be mailed out, and mails them
 */
function assignment_cron () {

    global $CFG, $USER;

    /// first execute all crons in plugins
    if ($plugins = get_list_of_plugins('mod/assignment/type')) {
        foreach ($plugins as $plugin) {
            require_once("$CFG->dirroot/mod/assignment/type/$plugin/assignment.class.php");
            $assignmentclass = "assignment_$plugin";
            $ass = new $assignmentclass();
            $ass->cron();
        }
    }

    /// Notices older than 1 day will not be mailed.  This is to avoid the problem where
    /// cron has not been running for a long time, and then suddenly people are flooded
    /// with mail from the past few weeks or months

    $timenow   = time();
    $endtime   = $timenow - $CFG->maxeditingtime;
    $starttime = $endtime - 24 * 3600;   /// One day earlier

    if ($submissions = assignment_get_unmailed_submissions($starttime, $endtime)) {

        $realuser = clone($USER);

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

            if (! $course = get_record("course", "id", "$submission->course")) {
                echo "Could not find course $submission->course\n";
                continue;
            }

            /// Override the language and timezone of the "current" user, so that
            /// mail is customised for the receiver.
            $USER = $user;
            course_setup($course);

            if (!has_capability('moodle/course:view', get_context_instance(CONTEXT_COURSE, $submission->course), $user->id)) {
                echo fullname($user)." not an active participant in " . format_string($course->shortname) . "\n";
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

            $assignmentinfo = new object();
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

        $USER = $realuser;
        course_setup(SITEID); // reset cron user language, theme and timezone settings

    }

    return true;
}

/**
 * Return grade for given user or all users.
 *
 * @param int $assignmentid id of assignment
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function assignment_get_user_grades($assignment, $userid=0) {
    global $CFG;

    $user = $userid ? "AND u.id = $userid" : "";

    $sql = "SELECT u.id, u.id AS userid, s.grade AS rawgrade, s.submissioncomment AS feedback, s.format AS feedbackformat,
                   s.teacher AS usermodified, s.timemarked AS dategraded, s.timemodified AS datesubmitted
              FROM {$CFG->prefix}user u, {$CFG->prefix}assignment_submissions s
             WHERE u.id = s.userid AND s.assignment = $assignment->id
                   $user";

    return get_records_sql($sql);
}

/**
 * Update grades by firing grade_updated event
 *
 * @param object $assignment null means all assignments
 * @param int $userid specific user only, 0 mean all
 */
function assignment_update_grades($assignment=null, $userid=0, $nullifnone=true) {
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    if ($assignment != null) {
        if ($grades = assignment_get_user_grades($assignment, $userid)) {
            foreach($grades as $k=>$v) {
                if ($v->rawgrade == -1) {
                    $grades[$k]->rawgrade = null;
                }
            }
            assignment_grade_item_update($assignment, $grades);
        } else {
            assignment_grade_item_update($assignment);
        }

    } else {
        $sql = "SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
                  FROM {$CFG->prefix}assignment a, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                 WHERE m.name='assignment' AND m.id=cm.module AND cm.instance=a.id";
        if ($rs = get_recordset_sql($sql)) {
            while ($assignment = rs_fetch_next_record($rs)) {
                if ($assignment->grade != 0) {
                    assignment_update_grades($assignment);
                } else {
                    assignment_grade_item_update($assignment);
                }
            }
            rs_close($rs);
        }
    }
}

/**
 * Create grade item for given assignment
 *
 * @param object $assignment object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function assignment_grade_item_update($assignment, $grades=NULL) {
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    if (!isset($assignment->courseid)) {
        $assignment->courseid = $assignment->course;
    }

    $params = array('itemname'=>$assignment->name, 'idnumber'=>$assignment->cmidnumber);

    if ($assignment->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $assignment->grade;
        $params['grademin']  = 0;

    } else if ($assignment->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$assignment->grade;

    } else {
        $params['gradetype'] = GRADE_TYPE_TEXT; // allow text comments only
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }

    return grade_update('mod/assignment', $assignment->courseid, 'mod', 'assignment', $assignment->id, 0, $grades, $params);
}

/**
 * Delete grade item for given assignment
 *
 * @param object $assignment object
 * @return object assignment
 */
function assignment_grade_item_delete($assignment) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    if (!isset($assignment->courseid)) {
        $assignment->courseid = $assignment->course;
    }

    return grade_update('mod/assignment', $assignment->courseid, 'mod', 'assignment', $assignment->id, 0, NULL, array('deleted'=>1));
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
function assignment_scale_used($assignmentid, $scaleid) {

    $return = false;

    $rec = get_record('assignment','id',$assignmentid,'grade',-$scaleid);

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Checks if scale is being used by any instance of assignment
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any assignment
 */
function assignment_scale_used_anywhere($scaleid) {
    if ($scaleid and record_exists('assignment', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
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
function assignment_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $USER;

    // do not use log table if possible, it may be huge

    if (!$submissions = get_records_sql("SELECT asb.id, asb.timemodified, cm.id AS cmid, asb.userid,
                                                u.firstname, u.lastname, u.email, u.picture
                                           FROM {$CFG->prefix}assignment_submissions asb
                                                JOIN {$CFG->prefix}assignment a      ON a.id = asb.assignment
                                                JOIN {$CFG->prefix}course_modules cm ON cm.instance = a.id
                                                JOIN {$CFG->prefix}modules md        ON md.id = cm.module
                                                JOIN {$CFG->prefix}user u            ON u.id = asb.userid
                                          WHERE asb.timemodified > $timestart AND
                                                a.course = {$course->id} AND
                                                md.name = 'assignment'
                                       ORDER BY asb.timemodified ASC")) {
         return false;
    }

    $modinfo =& get_fast_modinfo($course); // reference needed because we might load the groups
    $show    = array();
    $grader  = array();

    foreach($submissions as $submission) {
        if (!array_key_exists($submission->cmid, $modinfo->cms)) {
            continue;
        }
        $cm = $modinfo->cms[$submission->cmid];
        if (!$cm->uservisible) {
            continue;
        }
        if ($submission->userid == $USER->id) {
            $show[] = $submission;
            continue;
        }

        // the act of sumbitting of assignment may be considered private - only graders will see it if specified
        if (empty($CFG->assignment_showrecentsubmissions)) {
            if (!array_key_exists($cm->id, $grader)) {
                $grader[$cm->id] = has_capability('moodle/grade:viewall', get_context_instance(CONTEXT_MODULE, $cm->id));
            }
            if (!$grader[$cm->id]) {
                continue;
            }
        }

        $groupmode = groups_get_activity_groupmode($cm, $course);

        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', get_context_instance(CONTEXT_MODULE, $cm->id))) {
            if (isguestuser()) {
                // shortcut - guest user does not belong into any group
                continue;
            }

            if (is_null($modinfo->groups)) {
                $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
            }

            // this will be slow - show only users that share group with me in this cm
            if (empty($modinfo->groups[$cm->id])) {
                continue;
            }
            $usersgroups =  groups_get_all_groups($course->id, $cm->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $interset = array_intersect($usersgroups, $modinfo->groups[$cm->id]);
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $submission;
    }

    if (empty($show)) {
        return false;
    }

    print_headline(get_string('newsubmissions', 'assignment').':');

    foreach ($show as $submission) {
        $cm = $modinfo->cms[$submission->cmid];
        $link = $CFG->wwwroot.'/mod/assignment/view.php?id='.$cm->id;
        print_recent_activity_note($submission->timemodified, $submission, $cm->name, $link, false, $viewfullnames);
    }

    return true;
}


/**
 * Returns all assignments since a given time in specified forum.
 */
function assignment_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0)  {

    global $CFG, $COURSE, $USER;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = get_record('course', 'id', $courseid);
    }

    $modinfo =& get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];

    if ($userid) {
        $userselect = "AND u.id = $userid";
    } else {
        $userselect = "";
    }

    if ($groupid) {
        $groupselect = "AND gm.groupid = $groupid";
        $groupjoin   = "JOIN {$CFG->prefix}groups_members gm ON  gm.userid=u.id";
    } else {
        $groupselect = "";
        $groupjoin   = "";
    }

    if (!$submissions = get_records_sql("SELECT asb.id, asb.timemodified, asb.userid,
                                                u.firstname, u.lastname, u.email, u.picture
                                           FROM {$CFG->prefix}assignment_submissions asb
                                                JOIN {$CFG->prefix}assignment a      ON a.id = asb.assignment
                                                JOIN {$CFG->prefix}user u            ON u.id = asb.userid
                                                $groupjoin
                                          WHERE asb.timemodified > $timestart AND a.id = $cm->instance
                                                $userselect $groupselect
                                       ORDER BY asb.timemodified ASC")) {
         return;
    }

    $groupmode       = groups_get_activity_groupmode($cm, $course);
    $cm_context      = get_context_instance(CONTEXT_MODULE, $cm->id);
    $grader          = has_capability('moodle/grade:viewall', $cm_context);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $cm_context);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $cm_context);

    if (is_null($modinfo->groups)) {
        $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
    }

    $show = array();

    foreach($submissions as $submission) {
        if ($submission->userid == $USER->id) {
            $show[] = $submission;
            continue;
        }
        // the act of submitting of assignment may be considered private - only graders will see it if specified
        if (empty($CFG->assignment_showrecentsubmissions)) {
            if (!$grader) {
                continue;
            }
        }

        if ($groupmode == SEPARATEGROUPS and !$accessallgroups) {
            if (isguestuser()) {
                // shortcut - guest user does not belong into any group
                continue;
            }

            // this will be slow - show only users that share group with me in this cm
            if (empty($modinfo->groups[$cm->id])) {
                continue;
            }
            $usersgroups = groups_get_all_groups($course->id, $cm->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $interset = array_intersect($usersgroups, $modinfo->groups[$cm->id]);
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $submission;
    }

    if (empty($show)) {
        return;
    }

    if ($grader) {
        require_once($CFG->libdir.'/gradelib.php');
        $userids = array();
        foreach ($show as $id=>$submission) {
            $userids[] = $submission->userid;

        }
        $grades = grade_get_grades($courseid, 'mod', 'assignment', $cm->instance, $userids);
    }

    $aname = format_string($cm->name,true);
    foreach ($show as $submission) {
        $tmpactivity = new object();

        $tmpactivity->type         = 'assignment';
        $tmpactivity->cmid         = $cm->id;
        $tmpactivity->name         = $aname;
        $tmpactivity->sectionnum   = $cm->sectionnum;
        $tmpactivity->timestamp    = $submission->timemodified;

        if ($grader) {
            $tmpactivity->grade = $grades->items[0]->grades[$submission->userid]->str_long_grade;
        }

        $tmpactivity->user->userid   = $submission->userid;
        $tmpactivity->user->fullname = fullname($submission, $viewfullnames);
        $tmpactivity->user->picture  = $submission->picture;

        $activities[$index++] = $tmpactivity;
    }

    return;
}

/**
 * Print recent activity from all assignments in a given course
 *
 * This is used by course/recent.php
 */
function assignment_print_recent_mod_activity($activity, $courseid, $detail, $modnames)  {
    global $CFG;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="assignment-recent">';

    echo "<tr><td class=\"userpicture\" valign=\"top\">";
    print_user_picture($activity->user->userid, $courseid, $activity->user->picture);
    echo "</td><td>";

    if ($detail) {
        $modname = $modnames[$activity->type];
        echo '<div class="title">';
        echo "<img src=\"$CFG->modpixpath/assignment/icon.gif\" ".
             "class=\"icon\" alt=\"$modname\">";
        echo "<a href=\"$CFG->wwwroot/mod/assignment/view.php?id={$activity->cmid}\">{$activity->name}</a>";
        echo '</div>';
    }

    if (isset($activity->grade)) {
        echo '<div class="grade">';
        echo get_string('grade').': ';
        echo $activity->grade;
        echo '</div>';
    }

    echo '<div class="user">';
    echo "<a href=\"$CFG->wwwroot/user/view.php?id={$activity->user->userid}&amp;course=$courseid\">"
         ."{$activity->user->fullname}</a>  - ".userdate($activity->timestamp);
    echo '</div>';

    echo "</td></tr></table>";
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
function assignment_count_real_submissions($cm, $groupid=0) {
    global $CFG;

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    // this is all the users with this capability set, in this context or higher
    if ($users = get_users_by_capability($context, 'mod/assignment:submit', 'u.id', '', '', '', $groupid, '', false)) {
        $users = array_keys($users);
    }

    // if groupmembersonly used, remove users who are not in any group
    if ($users and !empty($CFG->enablegroupings) and $cm->groupmembersonly) {
        if ($groupingusers = groups_get_grouping_members($cm->groupingid, 'u.id', 'u.id')) {
            $users = array_intersect($users, array_keys($groupingusers));
        }
    }

    if (empty($users)) {
        return 0;
    }

    $userlists = implode(',', $users);

    return count_records_sql("SELECT COUNT('x')
                                FROM {$CFG->prefix}assignment_submissions
                               WHERE assignment = $cm->instance AND
                                     timemodified > 0 AND
                                     userid IN ($userlists)");
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

/**
 * Add a get_coursemodule_info function in case any assignment type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param $coursemodule object The coursemodule object (record).
 * @return object An object on information that the coures will know about (most noticeably, an icon).
 *
 */
function assignment_get_coursemodule_info($coursemodule) {
    global $CFG;

    if (! $assignment = get_record('assignment', 'id', $coursemodule->instance, '', '', '', '', 'id, assignmenttype, name')) {
        return false;
    }

    $libfile = "$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php";

    if (file_exists($libfile)) {
        require_once($libfile);
        $assignmentclass = "assignment_$assignment->assignmenttype";
        $ass = new $assignmentclass('staticonly');
        if ($result = $ass->get_coursemodule_info($coursemodule)) {
            return $result;
        } else {
            $info = new object();
            $info->name = $assignment->name;
            return $info;
        }

    } else {
        debugging('Incorrect assignment type: '.$assignment->assignmenttype);
        return false;
    }
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

        // ugly hack to support pluggable assignment type titles..
        if ($types[$name] == '[[type'.$name.']]') { 
            $types[$name] = get_string('type'.$name, 'assignment_'.$name);
        } 
    }
    asort($types);
    return $types;
}

/**
 * Executes upgrade scripts for assignment types when necessary
 */
function assignment_upgrade_submodules() {

    global $CFG;

/// Install/upgrade assignment types (it uses, simply, the standard plugin architecture)
    upgrade_plugins('assignment_type', 'mod/assignment/type', "$CFG->wwwroot/$CFG->admin/index.php");

}

function assignment_print_overview($courses, &$htmlarray) {

    global $USER, $CFG;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$assignments = get_all_instances_in_courses('assignment',$courses)) {
        return;
    }

    $assignmentids = array();

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
        }else{
            $assignmentids[] = $assignment->id;
        }
    }

    if(empty($assignmentids)){
        // no assigments to look at - we're done
        return true;
    }

    $strduedate = get_string('duedate', 'assignment');
    $strduedateno = get_string('duedateno', 'assignment');
    $strgraded = get_string('graded', 'assignment');
    $strnotgradedyet = get_string('notgradedyet', 'assignment');
    $strnotsubmittedyet = get_string('notsubmittedyet', 'assignment');
    $strsubmitted = get_string('submitted', 'assignment');
    $strassignment = get_string('modulename', 'assignment');
    $strreviewed = get_string('reviewed','assignment');


    // NOTE: we do all possible database work here *outside* of the loop to ensure this scales 
    
    // build up and array of unmarked submissions indexed by assigment id/ userid
    // for use where the user has grading rights on assigment
    $rs = get_recordset_sql("SELECT id, assignment, userid 
                            FROM {$CFG->prefix}assignment_submissions
                            WHERE teacher = 0 AND timemarked = 0
                            AND assignment IN (". implode(',', $assignmentids).")");

    $unmarkedsubmissions = array();
    while ($ra = rs_fetch_next_record($rs)) {
        $unmarkedsubmissions[$ra->assignment][$ra->userid] = $ra->id;
    }
    rs_close($rs);


    // get all user submissions, indexed by assigment id
    $mysubmissions = get_records_sql("SELECT assignment, timemarked, teacher, grade
                                      FROM {$CFG->prefix}assignment_submissions 
                                      WHERE userid = {$USER->id} AND 
                                      assignment IN (".implode(',', $assignmentids).")");

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
            if ($students = get_users_by_capability($context, 'mod/assignment:submit', 'u.id', '', '', '', 0, '', false)) {
                foreach($students as $student){
                    if(isset($unmarkedsubmissions[$assignment->id][$student->id])){
                        $submissions++;
                    }
                }
            }

            if ($submissions) {
                $str .= get_string('submissionsnotgraded', 'assignment', $submissions);
            }
        } else {
            if(isset($mysubmissions[$assignment->id])){

                $submission = $mysubmissions[$assignment->id];

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

function assignment_get_types() {
    global $CFG;
    $types = array();

    $type = new object();
    $type->modclass = MOD_CLASS_ACTIVITY;
    $type->type = "assignment_group_start";
    $type->typestr = '--'.get_string('modulenameplural', 'assignment');
    $types[] = $type;

    $standardassignments = array('upload','online','uploadsingle','offline');
    foreach ($standardassignments as $assignmenttype) {
        $type = new object();
        $type->modclass = MOD_CLASS_ACTIVITY;
        $type->type = "assignment&amp;type=$assignmenttype";
        $type->typestr = get_string("type$assignmenttype", 'assignment');
        $types[] = $type;
    }

    /// Drop-in extra assignment types
    $assignmenttypes = get_list_of_plugins('mod/assignment/type');
    foreach ($assignmenttypes as $assignmenttype) {
        if (!empty($CFG->{'assignment_hide_'.$assignmenttype})) {  // Not wanted
            continue;
        }
        if (!in_array($assignmenttype, $standardassignments)) {
            $type = new object();
            $type->modclass = MOD_CLASS_ACTIVITY;
            $type->type = "assignment&amp;type=$assignmenttype";
            $type->typestr = get_string("type$assignmenttype", 'assignment_'.$assignmenttype);
            $types[] = $type;
        }
    }

    $type = new object();
    $type->modclass = MOD_CLASS_ACTIVITY;
    $type->type = "assignment_group_end";
    $type->typestr = '--';
    $types[] = $type;

    return $types;
}

/**
 * Removes all grades from gradebook
 * @param int $courseid
 * @param string optional type
 */
function assignment_reset_gradebook($courseid, $type='') {
    global $CFG;

    $type = $type ? "AND a.assignmenttype='$type'" : '';

    $sql = "SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
              FROM {$CFG->prefix}assignment a, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
             WHERE m.name='assignment' AND m.id=cm.module AND cm.instance=a.id AND a.course=$courseid $type";

    if ($assignments = get_records_sql($sql)) {
        foreach ($assignments as $assignment) {
            assignment_grade_item_update($assignment, 'reset');
        }
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all posts from the specified assignment
 * and clean up any related data.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function assignment_reset_userdata($data) {
    global $CFG;

    $status = array();

    foreach (get_list_of_plugins('mod/assignment/type') as $type) {
        require_once("$CFG->dirroot/mod/assignment/type/$type/assignment.class.php");
        $assignmentclass = "assignment_$type";
        $ass = new $assignmentclass();
        $status = array_merge($status, $ass->reset_userdata($data));
    }

    return $status;
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the assignment.
 * @param $mform form passed by reference
 */
function assignment_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'assignmentheader', get_string('modulenameplural', 'assignment'));
    $mform->addElement('advcheckbox', 'reset_assignment_submissions', get_string('deleteallsubmissions','assignment'));
}

/**
 * Course reset form defaults.
 */
function assignment_reset_course_form_defaults($course) {
    return array('reset_assignment_submissions'=>1);
}

/**
 * Returns all other caps used in module
 */
function assignment_get_extra_capabilities() {
    return array('moodle/site:accessallgroups', 'moodle/site:viewfullnames');
}

?>
