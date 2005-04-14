<?PHP  // $Id$
/**
 * assignment_base is the base class for assignment types
 *
 * This class provides all the functionality for an assignment
 */


if (!isset($CFG->assignment_maxbytes)) {
    set_config("assignment_maxbytes", 1024000);  // Default maximum size for all assignments
}


/*
 * Standard base class for all assignment submodules (assignment types).
 *
 *
 */
class assignment_base {

    var $cm;
    var $course;
    var $assignment;

    /**
     * Constructor for the base assignment class
     *
     * Constructor for the base assignment class.
     * If cmid is set create the cm, course, assignment objects.
     *
     * @param cmid   integer, the current course module id - not set for new assignments
     */
    function assignment_base($cmid=0) {

        global $CFG;

        if ($cmid) {
            if (! $this->cm = get_record("course_modules", "id", $cmid)) {
                error("Course Module ID was incorrect");
            }

            if (! $this->course = get_record("course", "id", $this->cm->course)) {
                error("Course is misconfigured");
            }

            if (! $this->assignment = get_record("assignment", "id", $this->cm->instance)) {
                error("assignment ID was incorrect");
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

            $this->pagetitle = strip_tags($this->course->shortname.': '.$this->strassignment.': '.$this->assignment->name);

            if (!$this->cm->visible and !isteacher($this->course->id)) {
                $pagetitle = strip_tags($this->course->shortname.': '.$this->strassignment);
                print_header($pagetitle, $this->course->fullname, "$this->navigation $this->strassignment", 
                             "", "", true, '', navmenu($this->course, $this->cm));
                notice(get_string("activityiscurrentlyhidden"), "$CFG->wwwroot/course/view.php?id={$this->course->id}");
            }

            $this->currentgroup = get_current_group($this->course->id);

        }
    }

    /*
     * Display the assignment to students (sub-modules will most likely override this)
     */

    function view() {
        global $CFG;

        add_to_log($this->course->id, "assignment", "view", "view.php?id={$this->cm->id}", 
                   $this->assignment->id, $this->cm->id);

        print_header($this->pagetitle, $this->course->fullname, $this->navigation.' '.$this->assignment->name, '', '', 
                     true, update_module_button($this->cm->id, $this->course->id, $this->strassignment), 
                     navmenu($this->course, $this->cm));

        echo '<div class="reportlink">'.$this->submittedlink().'</div>';

        print_simple_box_start('center');
        echo format_text($this->assignment->description, $this->assignment->format);
        print_simple_box_end();

        print_simple_box_start('center', '', '', '', 'time');
        echo '<table>';
        echo '<tr><td class="c0">'.get_string('availabledate','assignment').':</td>';
        echo '    <td class="c1">'.userdate($this->assignment->timeavailable).'</td></tr>';
        echo '<tr><td class="c0">'.get_string('duedate','assignment').':</td>';
        echo '    <td class="c1">'.userdate($this->assignment->timedue).'</td></tr>';
        echo '</table>';
        
        print_simple_box_end();

        $this->view_feedback();

        print_footer($this->course);
    }


    function view_feedback() {
        global $USER;

    /// Get submission for this assignment
    /// If no submission then just return quietly
        $submission = $this->get_submission($USER->id);
        if (empty($submission->timemarked)) {
            return;
        }

    /// We need the teacher info
        if (! $teacher = get_record('user', 'id', $submission->teacher)) {
            print_object($submission);
            error('Could not find the teacher');
        }

    /// Print the feedback
        echo '<table cellspacing="0" class="feedback">';

        echo '<tr>';
        echo '<td class="left picture">';
        print_user_picture($teacher->id, $this->course->id, $teacher->picture);
        echo '</td>';
        echo '<td class="feedbackheader">';
        echo '<span class="author">'.fullname($teacher).'</span>';
        echo '<span class="time">'.userdate($submission->timemarked).'</span>';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td class="left side">&nbsp;</td>';
        echo '<td class="feedback">';
        if ($this->assignment->grade) {
            echo '<span class="grade">';
            if ($submission->grade or $submission->timemarked) {
                echo get_string("grade").": $submission->grade";
            } else {
                echo get_string("nograde");
            }
            echo '</span>';
        }

        echo text_to_html($submission->comment);
        echo '</tr>';

        echo '</table>';
    }



    /*
     * Print the start of the setup form for the current assignment type
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

        if (empty($form->name)) {
            $form->name = "";
        }
        if (empty($form->assignmenttype)) {
            $form->assignmenttype = "";
        }
        if (empty($form->description)) {
            $form->description = "";
        }

        $strname    = get_string('name');
        $strassignments = get_string('modulenameplural', 'assignment');
        $strheading = empty($form->name) ? get_string("type$form->assignmenttype",'assignment') : $form->name;

        print_header($this->course->shortname.': '.$strheading, "$strheading",
                "<a href=\"$CFG->wwwroot/course/view.php?id={$this->course->id}\">{$this->course->shortname} </a> -> ".
                "<a href=\"$CFG->wwwroot/mod/assignment/index.php?id={$this->course->id}\">$strassignments</a> -> $strheading");

        print_simple_box_start("center");
        print_heading(get_string("type$form->assignmenttype",'assignment'));
        include("$CFG->dirroot/mod/assignment/type/common.html");
    }

    /*
     * Print the end of the setup form for the current assignment type
     */
    function setup_end() {
        global $CFG, $usehtmleditor;

        include($CFG->dirroot.'/mod/assignment/type/common_end.html');

        print_simple_box_end();

        if ($usehtmleditor) {
            use_html_editor();
        }

        print_footer($this->course);
    }


    function add_instance($assignment) {
        // Given an object containing all the necessary data,
        // (defined by the form in mod.html) this function
        // will create a new instance and return the id number
        // of the new instance.

        $assignment->timemodified = time();
        $assignment->timedue = make_timestamp($assignment->dueyear, $assignment->duemonth, 
                                              $assignment->dueday, $assignment->duehour, 
                                              $assignment->dueminute);
        $assignment->timeavailable = make_timestamp($assignment->availableyear, $assignment->availablemonth, 
                                                    $assignment->availableday, $assignment->availablehour, 
                                                    $assignment->availableminute);

        return insert_record("assignment", $assignment);
    }

    function delete_instance($assignment) {
        if (! delete_records("assignment", "id", "$assignment->id")) {
            $result = false;
        }
        return $result;
    }

    function update_instance($assignment) {
        // Given an object containing all the necessary data,
        // (defined by the form in mod.html) this function
        // will create a new instance and return the id number
        // of the new instance.

        $assignment->timemodified = time();
        $assignment->timedue = make_timestamp($assignment->dueyear, $assignment->duemonth, 
                                              $assignment->dueday, $assignment->duehour, 
                                              $assignment->dueminute);
        $assignment->timeavailable = make_timestamp($assignment->availableyear, $assignment->availablemonth, 
                                                    $assignment->availableday, $assignment->availablehour, 
                                                    $assignment->availableminute);
        $assignment->id = $assignment->instance;
        return update_record("assignment", $assignment);
    }



    /*
     * Top-level function for handling of submissions called by submissions.php
     *  
     */
    function submissions($mode) {
        switch ($mode) {
            case 'grade':                         // We are in a popup window grading
                if ($submission = $this->process_feedback()) {
                    print_heading(get_string('changessaved'));
                    /// Run some Javascript to try and update the parent page
                    echo '<script type="text/javascript">'."\n<!--\n";
                    echo 'opener.document.getElementById("ts'.$submission->userid.'").innerHTML="'.userdate($submission->timemodified)."\";\n";
                    echo 'opener.document.getElementById("tt'.$submission->userid.'").innerHTML="'.userdate($submission->timemarked)."\";\n";
                    echo 'opener.document.getElementById("g'.$submission->userid.'").innerHTML="'.$this->display_grade($submission->grade)."\";\n";
                    echo "\n-->\n</script>";
                    fflush();
                }
                close_window();
                break;

            case 'single':                        // We are in a popup window displaying submission
                $this->display_submission();
                break;

            case 'all':                           // Main window, display everything
                $this->display_submissions();
                break;
        }
    }


    /*
     *  Display a grade in user-friendly form, whether it's a scale or not
     *  
     */
    function display_grade($grade) {

        static $scalegrades;   // Cached because we only have one per assignment

        if ($this->assignment->grade >= 0) {    // Normal number
            return $grade;

        } else {                                // Scale
            if (empty($scalegrades)) {
                if ($scale = get_record('scale', 'id', -($this->assignment->grade))) {
                    $scalegrades = make_menu_from_list($scale->scale);
                } else {
                    return '-';
                }
            }
            return $scalegrades[$grade];
        }
    }

    /*
     *  Display a single submission, ready for grading on a popup window
     *  
     */
    function display_submission() {
        
        $userid = required_param('userid');

        if (!$user = get_record('user', 'id', $userid)) {
            error('No such user!');
        }

        if (!$submission = $this->get_submission($user->id)) {  // Get or make one
            error('Could not find submission!');
        }

        if ($submission->timemodified > $submission->timemarked) {
            $subtype = 'assignmentnew';
        } else {
            $subtype = 'assignmentold';
        }

        print_header($this->assignment->name.' '.fullname($user, true));

        echo '<form action="submissions.php" method="post">';
        echo '<input type="hidden" name="userid" value="'.$userid.'">';
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'">';
        echo '<input type="hidden" name="mode" value="grade">';

        echo '<table cellspacing="0" class="submission '.$subtype.'" >';

        echo '<tr>';
        echo '<td width="35" valign="top" class="picture user">';
        print_user_picture($user->id, $this->course->id, $user->picture);
        echo '</td>';
        echo '<td class="heading">'.fullname($user, true).'</td>';
        echo '</tr>';

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

        if (!$submission->grade and !$submission->timemarked) {
            $submission->grade = -1;   /// Hack to stop zero being selected on the menu below (so it shows 'no grade')
        }
        echo get_string('feedback', 'assignment').':';
        choose_from_menu(make_grades_menu($this->assignment->grade), 'grade', 
                         $submission->grade, get_string('nograde'));

        if ($submission->timemarked) {
            echo '&nbsp;&nbsp;'.userdate($submission->timemarked);
        }
        echo '<br />';
        print_textarea(false, 6, 60, 500, 400, 'comment', $submission->comment, $this->course->id);
        echo '</td></tr>';
        echo '</table>';

        echo '<input type="submit" name="submit" value="'.get_string('savechanges').'" />';
        echo '<input type="submit" name="cancel" value="'.get_string('cancel').'" />';
        echo '</form>';
        print_footer('none');
    }


    /*
     *  Display all the submissions ready for grading
     */
    function display_submissions() {

        global $CFG, $db;

        $teacherattempts = true; /// Temporary measure


        $sort    = optional_param('sort', 'timemodified');
        $dir     = optional_param('dir', 'DESC');
        $timenow = optional_param('timenow', 0);
        $page    = optional_param('page', 0);
        $perpage = optional_param('perpage', 10);

        $strsaveallfeedback = get_string('saveallfeedback', 'assignment');

    /// Some shortcuts to make the code read better
        
        $course     = $this->course;
        $assignment = $this->assignment;
        $cm         = $this->cm;


        add_to_log($course->id, 'assignment', 'view submission', 'submissions.php?id='.$this->assignment->id, $this->assignment->id, $this->cm->id);
        
        print_header_simple($this->assignment->name, "", '<a href="index.php?id='.$course->id.'">'.$this->strassignments.'</a> -> <a href="view.php?a='.$this->assignment->id.'">'.$this->assignment->name.'</a> -> '. $this->strsubmissions, '', '', true, update_module_button($cm->id, $course->id, $this->strassignment), navmenu($course, $cm));


        $tablecolumns = array('picture', 'fullname', 'grade', 'timemodified', 'timemarked', 'status');
        $tableheaders = array('', get_string('fullname'), get_string('grade'), get_string('lastmodified').' ('.$course->student.')', get_string('lastmodified').' ('.$course->teacher.')', get_string('status'));


        require_once($CFG->libdir.'/tablelib.php');
        $table = new flexible_table('mod-assignment-submissions');
                        
        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot.'/mod/assignment/submissions.php?id='.$this->cm->id);
                
        $table->sortable(true);
        $table->collapsible(true);
        $table->initialbars(true);
        
        $table->column_suppress('picture');
        $table->column_suppress('fullname');
        
        $table->column_class('picture', 'picture');
        
        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'attempts');
        $table->set_attribute('class', 'generaltable generalbox');
        $table->set_attribute('width', '90%');
        $table->set_attribute('align', 'center');
            
        // Start working -- this is necessary as soon as the niceties are over
        $table->setup();



    /// Check to see if groups are being used in this assignment
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, 'submissions.php?id='.$this->cm->id);
        } else {
            $currentgroup = false;
        }


    /// Get all teachers and students
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        } else {
            $users = get_course_users($course->id);
        }
            
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
            print_heading($strnoattempts);
            return true;
        }


    /// Construct the SQL

        if ($where = $table->get_sql_where()) {
            $where = str_replace('firstname', 'u.firstname', $where);
            $where = str_replace('lastname', 'u.lastname', $where);
            $where .= ' AND ';
        }

        if ($sort = $table->get_sql_sort()) {
            $sortparts = explode(',', $sort);
            $newsort   = array();
            foreach ($sortparts as $sortpart) {
                $sortpart = trim($sortpart);
                $newsort[] = $sortpart;
            }
            $sort = ' ORDER BY '.implode(', ', $newsort);
        }


        $select = 'SELECT '.$db->Concat('u.id', '\'#\'', $db->IfNull('s.userid', '0')).' AS uvs, u.id, u.firstname, u.lastname, u.picture, s.id AS submissionid, s.grade, s.timemodified, s.timemarked ';
        $group  = 'GROUP BY uvs ';
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'assignment_submissions s ON u.id = s.userid AND s.assignment = '.$this->assignment->id.' '.
               'WHERE '.$where.'u.id IN ('.implode(',', array_keys($users)).') ';


        $total = count_records_sql('SELECT COUNT(DISTINCT('.$db->Concat('u.id', '\'#\'', $db->IfNull('s.userid', '0')).')) '.$sql);

        $table->pagesize($perpage, $total);
        
        if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
            $limit = ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size());     
        }
        else {
            $limit = '';
        }

        $strupdate = get_string('update');
        $grademenu = make_grades_menu($this->assignment->grade);

        if (($ausers = get_records_sql($select.$sql.$group.$sort.$limit)) === false) {
        
            $table->add_data(array('No users found')); /// No users to match query
            
        } else {

            foreach ($ausers as $auser) {
                $picture = print_user_picture($auser->id, $course->id, $auser->picture, false, true);
                if (!empty($auser->submissionid)) {
                    if ($auser->timemodified > 0) {
                        $studentmodified = '<div id="ts'.$auser->id.'">'.userdate($auser->timemodified).'</div>';
                        if ($auser->timemarked > $auser->timemodified) {
                            $status = '<div id="st'.$auser->id.'">YES</div>';
                        } else {
                            $status = '<div id="st'.$auser->id.'">NO</div>';
                        }
                    } else {
                        $studentmodified = '<div id="ts'.$auser->id.'">-</div>';
                        $status          = '<div id="st'.$auser->id.'"></div>';
                    }
                    if ($auser->timemarked > 0) {
                        $teachermodified = '<div id="tt'.$auser->id.'">'.userdate($auser->timemarked).'</div>';
                    } else {
                        $teachermodified = '<div id="tt'.$auser->id.'">-</div>';
                    }

                    //$grade = choose_from_menu($grademenu, 'g'.$auser->id, $auser->grade, get_string('nograde'), '', 0, true);
                    $grade = '<div id="g'.$auser->id.'">'.$this->display_grade($auser->grade).'</div>';

                } else {
                    $studentmodified = '<div id="ts'.$auser->id.'">-</div>';
                    $teachermodified = '<div id="tt'.$auser->id.'">-</div>';
                    $status          = '<div id="st'.$auser->id.'"></div>';
                    $grade           = '<div id="g'.$auser->id.'">-</div>';
                }

                $button = button_to_popup_window ('/mod/assignment/submissions.php?id='.$this->cm->id.'&amp;userid='.$auser->id.'&amp;mode=single', 
                        'grade'.$auser->id, $strupdate, 450, 600, $strupdate, 'none', true);


                $row = array($picture, fullname($auser), $grade, $studentmodified, $teachermodified, $status.'&nbsp;'.$button);
                $table->add_data($row);
            }
        }


        $table->print_html();

        print_footer($this->course);

    }



    /*
     *  Display and process the submissions 
     */
    function process_feedback() {

        global $USER;

        if (!$feedback = data_submitted()) {      // No incoming data?
            return false;
        }

        if (!empty($feedback->cancel)) {          // User hit cancel button
            return false;
        }

        $newsubmission = $this->get_submission($feedback->userid);

        $newsubmission->grade      = $feedback->grade;
        $newsubmission->comment    = $feedback->comment;
        $newsubmission->teacher    = $USER->id;
        $newsubmission->mailed     = 0;       // Make sure mail goes out (again, even)
        $newsubmission->timemarked = time();

        if (empty($submission->timemodified)) {   // eg for offline assignments
            $newsubmission->timemodified = time();
        }

        if (! update_record('assignment_submissions', $newsubmission)) {
            return false;
        }

        add_to_log($this->course->id, 'assignment', 'update grades', 
                   'submissions.php?id='.$this->assignment->id.'&user='.$feedback->userid, $feedback->userid, $this->cm->id);
        
        return $newsubmission;

    }


    function get_submission($userid, $createnew=true) {
        $submission = get_record('assignment_submissions', 'assignment', $this->assignment->id, 'userid', $userid);

        if ($submission || !$createnew) {
            return $submission;
        }

        $newsubmission = new Object;
        $newsubmission->assignment = $this->assignment->id;
        $newsubmission->userid = $userid;
        $newsubmission->timecreated = time();
        if (!insert_record("assignment_submissions", $newsubmission)) {
            error("Could not insert a new empty submission");
        }

        return get_record('assignment_submissions', 'assignment', $this->assignment->id, 'userid', $userid);
    }


    function get_submissions($sort='', $dir='DESC') {
        /// Return all assignment submissions by ENROLLED students (even empty)
        global $CFG;

        if ($sort == "lastname" or $sort == "firstname") {
            $sort = "u.$sort $dir";
        } else if (empty($sort)) {
            $sort = "a.timemodified DESC";
        } else {
            $sort = "a.$sort $dir";
        }

        $select = "s.course = '$this->assignment->course' AND";
        $site = get_site();
        if ($this->assignment->course == $site->id) {
            $select = '';
        }   
        return get_records_sql("SELECT a.* 
                FROM {$CFG->prefix}assignment_submissions a, 
                {$CFG->prefix}user_students s,
                {$CFG->prefix}user u
                WHERE a.userid = s.userid
                AND u.id = a.userid
                AND $select a.assignment = '$this->assignment->id' 
                ORDER BY $sort");
    }


    function count_real_submissions($groupid=0) {
        /// Return all real assignment submissions by ENROLLED students (not empty ones)
        global $CFG;

        if ($groupid) {     /// How many in a particular group?
            return count_records_sql("SELECT COUNT(DISTINCT g.userid, g.groupid)
                    FROM {$CFG->prefix}assignment_submissions a,
                    {$CFG->prefix}groups_members g
                    WHERE a.assignment = {$this->assignment->id} 
                    AND a.timemodified > 0
                    AND g.groupid = '$groupid' 
                    AND a.userid = g.userid ");
        } else {
            $select = "s.course = '{$this->assignment->course}' AND";
            if ($this->assignment->course == SITEID) {
                $select = '';
            }
            return count_records_sql("SELECT COUNT(*)
                    FROM {$CFG->prefix}assignment_submissions a, 
                    {$CFG->prefix}user_students s
                    WHERE a.assignment = '{$this->assignment->id}' 
                    AND a.timemodified > 0
                    AND $select a.userid = s.userid ");
        } 
    }

} ////// End of the assignment_base class



/// OTHER STANDARD FUNCTIONS ////////////////////////////////////////////////////////


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


function assignment_update_instance($assignment){
    global $CFG;

    require_once("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $ass = new $assignmentclass();
    return $ass->update_instance($assignment);
}    


function assignment_add_instance($assignment) {
    global $CFG;

    require_once("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
    $assignmentclass = "assignment_$assignment->assignmenttype";
    $ass = new $assignmentclass();
    return $ass->add_instance($assignment);
}

function assignment_types() {
    $types = array();
    $names = get_list_of_plugins('mod/assignment/type');
    foreach ($names as $name) {
        $types[$name] = get_string('type'.$name, 'assignment');
    }
    asort($types);
    return $types;
}

function assignment_upgrade_submodules() {
    global $CFG;

    $types = assignment_types();

    include_once($CFG->dirroot.'/mod/assignment/version.php');  // defines $module with version etc

    foreach ($types as $type) {

        $fullpath = $CFG->dirroot.'/mod/assignment/type/'.$type;

    /// Check for an external version file (defines $submodule)

        if (!is_readable($fullpath .'/version.php')) {
            continue;
        }
        unset($module);
        include_once($fullpath .'/version.php');

    /// Check whether we need to upgrade

        if (!isset($submodule->version)) {
            continue;
        }

    /// Make sure this submodule will work with this assignment version

        if (isset($submodule->requires) and ($submodules->requires > $module->version)) {
            notify("Assignment submodule '$type' is too new for your assignment");
            continue;
        }

    /// If we use versions, make sure an internal record exists

        $currentversion = 'assignment_'.$type.'_version';

        if (!isset($CFG->$currentversion)) {
            set_config($currentversion, 0);
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
            $db->debug=true;
            if ($upgrade_function($CFG->$currentversion)) {
                $db->debug=false;
                set_config($currentversion, $submodule->version);
            }
            $db->debug=false;
        }
    }
}

?>
