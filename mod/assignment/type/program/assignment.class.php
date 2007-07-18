<?php
// $Id$

define('ASSIGNMENT_STATUS_SUBMITTED', 'submitted');
define('NUMTESTS', 4); // Default number of tests

/**
 * Extends the base assignment class
 *
 * @author Arkaitz Garro
 * @version 0.1
 */
class assignment_program extends assignment_base {

    function assignment_program($cmid = 0) {
        parent::    assignment_base($cmid);
    }

    function setup_elements(& $mform) {
        global $CFG, $COURSE;

        $add           = optional_param('add', '', PARAM_ALPHA);
        $update        = optional_param('update', 0, PARAM_INT);

        $ynoptions = array(0 => get_string('no'), 1 => get_string('yes'));

        // Programming languages
        $choices = assignment_program_languages();
        $mform->addElement('select', 'lang', get_string("assignmentlangs", "assignment"), $choices);
        $mform->setHelpButton('lang', array('lang',get_string('assignmentlangs','assignment'),'assignment'));
        $mform->setDefault('lang', 'java');

        // Cron date
        $mform->addElement('date_time_selector', 'var1', get_string('crondate', 'assignment'), array('optional' => true));
        $mform->setHelpButton('var1', array('timecron',get_string('crondate','assignment'), 'assignment'));
        $mform->disabledIf('var1', 'var1', 'eq', 0);
        $mform->setDefault('var1', time() + 7 * 24 * 3600);

        // Max. CPU time
        unset($choices);
        $choices = $this->get_max_cpu_times($CFG->assignment_maxcpu);
        $mform->addElement('select', 'var2', get_string('maximumcpu', 'assignment'), $choices);
        $mform->setHelpButton('var2', array('maximumcpu',get_string('maximumcpu','assignment'), 'assignment'));
        $mform->setDefault('var2', $CFG->assignment_maxcpu);

        // Max. memory usage
        unset($choices);
        $choices = $this->get_max_memory_usages($CFG->assignment_maxmem);
        $mform->addElement('select', 'var3', get_string('maximummem', 'assignment'), $choices);
        $mform->setHelpButton('var3', array('maximummem',get_string('maximummem','assignment'), 'assignment'));
        $mform->setDefault('var3', $CFG->assignment_maxmem);

        // Allow resubmit
        $mform->addElement('select', 'resubmit', get_string("allowresubmit", "assignment"), $ynoptions);
        $mform->setHelpButton('resubmit', array('resubmit',get_string('allowresubmit','assignment'), 'assignment'));
        $mform->setDefault('resubmit', 0);

        // Email teachers
        $mform->addElement('select', 'emailteachers', get_string("emailteachers", "assignment"), $ynoptions);
        $mform->setHelpButton('emailteachers', array('emailteachers',get_string('emailteachers','assignment'), 'assignment'));
        $mform->setDefault('emailteachers', 0);

        // Submission max bytes
        $choices = get_max_upload_sizes($CFG->maxbytes, $COURSE->maxbytes);
        $choices[1] = get_string('uploadnotallowed');
        $choices[0] = get_string('courseuploadlimit') . ' (' . display_size($COURSE->maxbytes) . ')';
        $mform->addElement('select', 'maxbytes', get_string('maximumfilesize', 'assignment'), $choices);
        $mform->setDefault('maxbytes', $CFG->assignment_maxbytes);

        // Tests form
        $mform->addElement('header', 'tests', get_string('tests', 'assignment'));

        // Output filter
        unset ($choices);
        $choices[1] = get_string('ignorespace', 'assignment');
        $choices[2] = get_string('ignorecase', 'assignment');
        $choices[3] = get_string('exactouput', 'assignment');
        $mform->addElement('select', 'var4', get_string('outputfilter', 'assignment'), $choices);
        $mform->setHelpButton('var4', array('outputfilter',get_string('outputfilter','assignment'), 'assignment'));

        // Get course module instance
        $cm = new Object();
        if (!empty($update)) {
            $cm = get_record("course_modules", "id", $update);
        }

        // Get tests data
        $tests = array ();
        $numtests = $this->get_tests($cm, $tests);
        if ($tests) {
            // Tests allready defined (update assignment)
            $i = 1;
            foreach ($tests as $tstObj => $tstValue) {
                $mform->addElement('text', "input[$i]", get_string('input', 'assignment') . $i);
                $mform->setDefault("input[$i]",$tstValue->input);
                $mform->addElement('text', "output[$i]", get_string('output', 'assignment') . $i);
                $mform->setDefault("output[$i]",$tstValue->output);

                $i++;
            }
        } else {
            // New assignment
            for ($i = 1; $i <= $numtests; $i++) {
                $mform->addElement('text', "input[$i]", get_string('input', 'assignment') . $i);
                $mform->addElement('text', "output[$i]", get_string('output', 'assignment') . $i);
            }
        }
    }

    /**
     * Create a new program type assignment activity
     *
     * Given an object containing all the necessary data,
     * (defined by the form in mod.html) this function
     * will create a new instance and return the id number
     * of the new instance.
     * The due data is added to the calendar
     * Tests are added to assignment_epaile_tests table
     *
     * @param $assignment object The data from the form on mod.html
     * @return int The id of the assignment
     */
    function add_instance($assignment) {
        // Add assignment instance
        $assignment->id = parent::add_instance($assignment);
        if ($assignment->id) {
            $this->after_add_update($assignment);
        }

        return $assignment->id;
    }

    /**
     * Updates a program assignment activity
     *
     * Given an object containing all the necessary data,
     * (defined by the form in mod.html) this function
     * will update the assignment instance and return the id number
     * The due date is updated in the calendar
     *
     * @param $assignment object The data from the form on mod.html
     * @return int The assignment id
     */

    function update_instance($assignment) {
        // Add assignment instance
        $returnid = parent::update_instance($assignment);
        if ($returnid) {
            $this->after_add_update($assignment);
        }

        return $returnid;
    }

    /**
     * Deletes a program assignment activity
     *
     * Deletes all database records, files and calendar events for this assignment.
     * @param $assignment object The assignment to be deleted
     * @return boolean False indicates error
     */
    function delete_instance($assignment) {
        global $CFG;

        // DELETE submissions results
        $sql = 'test IN (SELECT id FROM '.$CFG->prefix.'assignment_epaile_tests WHERE assignment='.$assignment->id.')';
        if (!delete_records_select('assignment_epaile_results', $sql)) {
            return false;
        }

        // DELETE submissions
        $sql = 'submission IN (SELECT id FROM '.$CFG->prefix.'assignment_submissions WHERE assignment='.$assignment->id.')';
        if (!delete_records_select('assignment_epaile_submissions', $sql)) {
            return false;
        }

        // DELETE tests
        if (!delete_records('assignment_epaile_tests', 'assignment', $assignment->id)) {
            return false;
        }

        $result = parent::delete_instance($assignment);

        return $result;
    }

    /**
    * This function is called at the end of add_instance
    * and update_instance, to add or update tests
    *
    * @param object $assignment the epaile object.
    */
    function after_add_update($assignment) {
        // Count real input/output (not empty tests)
        $assignment->numtests = count($assignment->input);

        // Delete actual tests
        delete_records('assignment_epaile_tests', 'assignment', $assignment->id);

        // Insert new tests
        for ($i = 0; $i < $assignment->numtests; $i++) {
            // Check if tests is not empty
            if(!empty($assignment->input[$i+1]) && !empty($assignment->output[$i+1])) {
                $test = new Object();
                $test->assignment = $assignment->id;
                $test->input = $assignment->input[$i+1];
                $test->output = $assignment->output[$i+1];

                if (!insert_record('assignment_epaile_tests', $test)) {
                    return get_string('notestinsert', 'assignment');
                }

                unset ($test);
            }
        }
    }

    /**
     * Get tests data for current assignment
     *
     * @param $instanceid int Instance ID
     * @param $tests object The object used to fill the tests
     *
     * @return $numtests Number of tests
     */
    function get_tests($cm, & $tests) {
        if (isset ($cm->instance))
            $tests = get_records('assignment_epaile_tests', 'assignment', $cm->instance, 'id ASC');

        if (empty ($tests)) {
            $numtests = NUMTESTS;
        } else {
            $numtests = count($tests);
        }
        return $numtests;
    }

    /**
     * Get compile time / errors
     *
     * @param $submissionid int Submission id
     *
     * @return $comp Array
     */
    function get_compile($submissionid) {
        $comp = array ();
        if ($submissionid)
            $comp = get_record('assignment_epaile_submissions', 'submission', $submissionid);

        return $comp;
    }

    /**
     * Get tests results for current submission
     *
     * @param $submissionid int Submission id
     *
     * @return $res Array
     */
    function get_results($submissionid) {
        $res = array ();
        if ($submissionid)
            $res = get_records('assignment_epaile_results', 'submission', $submissionid);

        return $res;
    }

    /**
     * Get number of errors in tests
     *
     * @param $submissionid int Submission id
     *
     * @return $num int
     */
    function get_errors_number($submissionid) {
        global $CFG;

        $num = 0;
        if($submissionid) {
            $sql  = "SELECT COUNT(id) AS num FROM ".$CFG->prefix."assignment_epaile_results";
            $sql .= " WHERE submission=$submissionid AND error<>''";

            if (($res = get_record_sql($sql)) !== false) {
                $num = $res->num;
            }
        }
        return $num;
    }

    /**
     * View assignment details.
     */
    function view() {
        global $USER;

        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);

        require_capability('mod/assignment:view', $context);
        add_to_log($this->course->id, "assignment", "view", "view.php?id={$this->cm->id}", $this->assignment->id, $this->cm->id);

        $this->view_header();
        $this->view_intro();
        $this->view_lang();
        $this->view_dates();
        $filecount = $this->count_user_files($USER->id);

        if ($submission = $this->get_submission()) {
            if ($submission->timemarked) {
                $this->view_feedback();
            }
            if ($filecount) {
                print_simple_box($this->print_user_files($USER->id, true), 'center');
            }
        }
        if (has_capability('mod/assignment:submit', $context) && $this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            $this->view_upload_form();
        }
        $this->view_footer();
    }

    /**
     * Upload file
     */
    function upload() {
        global $CFG, $USER;
        require_capability('mod/assignment:submit', get_context_instance(CONTEXT_MODULE, $this->cm->id));
        $this->view_header(get_string('upload'));
        $filecount = $this->count_user_files($USER->id);
        $submission = $this->get_submission($USER->id);
        if ($this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            if ($submission = $this->get_submission($USER->id)) {
                if (($submission->grade >= 0) and !$this->assignment->resubmit) {
                    notify(get_string('alreadygraded', 'assignment'));
                }
            }
            $dir = $this->file_area_name($USER->id);
            require_once ($CFG->dirroot . '/lib/uploadlib.php');
            $um = new upload_manager('newfile', true, false, $this->course, false, $this->assignment->maxbytes);
            if ($um->process_file_uploads($dir)) {
                $newfile_name = $um->get_new_filename();
                if ($submission) {
                    $submission->timemodified = time();
                    $submission->numfiles = 1;
                    $submission->submissioncomment = addslashes($submission->submissioncomment);
                    unset ($submission->data1); // Don't need to update this.
                    unset ($submission->data2); // Don't need to update this.
                    if (update_record("assignment_submissions", $submission)) {
                        add_to_log($this->course->id, 'assignment', 'upload', 'view.php?a='
                            . $this->assignment->id, $this->assignment->id, $this->cm->id);
                        $this->email_teachers($submission);
                        print_heading(get_string('uploadedfile'));
                    } else {
                        notify(get_string("uploadfailnoupdate", "assignment"));
                    }
                    print_continue('view.php?id=' . $this->cm->id);
                } else {
                    $newsubmission = $this->prepare_new_submission($USER->id);
                    $newsubmission->timemodified = time();
                    $newsubmission->numfiles = 1;
                    if (insert_record('assignment_submissions', $newsubmission)) {
                        add_to_log($this->course->id, 'assignment', 'upload', 'view.php?a='
                            . $this->assignment->id, $this->assignment->id, $this->cm->id);
                        $this->email_teachers($newsubmission);
                        print_heading(get_string('uploadedfile'));
                        /*******************************************************
                        **  Automated grade test                              **
                        ********************************************************/
                        $this->print_loading();
                        $submission = $this->get_submission($USER->id);
                        chdir($CFG->dataroot . '/' . $dir);
                        $cmd = "javac " . $um->get_new_filename() . " > cerrors 2>&1";
                        exec($cmd, $exit, $retval);
                        // Compile errors
                        if ($retval) {
                            // Read compile errors
                            $gestor = fopen($CFG->dataroot . '/' . $dir . '/cerrors', 'r');
                            $errors = fread($gestor, filesize($CFG->dataroot . '/' . $dir . '/cerrors'));
                            fclose($gestor);
                            // Delete compile errors file
                            unlink($CFG->dataroot . '/' . $dir . '/cerrors');
                            // Update submission
                            $submission = $this->get_submission($USER->id);
                            $submission->compileerrors = addslashes($errors);
                        }
                        $submission->submission = $submission->id;
                        $submission->runtime = 10;
                        if (!insert_record("assignment_epaile_submissions", $submission))
                            error(get_string('gradeerror', 'assignment'));
                        #print_continue('view.php?id='.$this->cm->id);
                    } else {
                        notify(get_string("uploadnotregistered", "assignment", $newfile_name));
                        print_continue('view.php?id=' . $this->cm->id);
                    }
                }
            }
        } else {
            notify(get_string("uploaderror", "assignment")); //submitting not allowed!
            print_continue('view.php?id=' . $this->cm->id);
        }
        $this->view_footer();
    }

    function view_upload_form() {
        global $CFG;
        $struploadafile = get_string("uploadafile");
        $strmaxsize = get_string("maxsize", "", display_size($this->assignment->maxbytes));
        echo '<center>';
        echo '<form enctype="multipart/form-data" method="post" ' .        "action=\"$CFG->wwwroot/mod/assignment/upload.php\">";
        echo "<p>$struploadafile ($strmaxsize)</p>";
        echo '<input type="hidden" name="id" value="' . $this->cm->id . '" />';
        require_once ($CFG->libdir . '/uploadlib.php');
        upload_print_form_fragment(1, array (
            'newfile'
        ), false, null, 0, $this->assignment->maxbytes, false);
        echo '<input type="submit" name="save" value="' . get_string('uploadthisfile') . '" />';
        echo '</form>';
        echo '</center>';
    }

    function print_student_answer($userid, $return = false) {
        global $CFG, $USER;
        $filearea = $this->file_area_name($userid);
        $output = '';
        if ($basedir = $this->file_area($userid)) {
            if ($files = get_directory_list($basedir)) {
                foreach ($files as $key => $file) {
                    require_once ($CFG->libdir . '/filelib.php');
                    $icon = mimeinfo('icon', $file);
                    if ($CFG->slasharguments) {
                        $ffurl = "$CFG->wwwroot/file.php/$filearea/$file";
                    } else {
                        $ffurl = "$CFG->wwwroot/file.php?file=/$filearea/$file";
                    }
                    //died right here
                    //require_once($ffurl);
                    #$output = '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                    #        '<a href="'.$ffurl.'" >'.$file.'</a><br />';
                    $output = link_to_popup_window('/mod/assignment/type/program/source.php?id=' . $this->cm->id . '&amp;userid=' . $userid . '&amp;file=' . $file, $file . ' source code', $file, 710, 780, $file, 'none', true, 'button' . $userid);
                }
            }
        }
        $output = '<div class="files">' . $output . '</div>';
        return $output;
    }

    /**
     * Produces a list of links to the files uploaded by a user
     *
     * @param $userid int optional id of the user. If 0 then $USER->id is used.
     * @param $return boolean optional defaults to false. If true the list is returned rather than printed
     * @return string optional
     */
    function print_user_files($userid = 0, $return = false) {
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
                require_once ($CFG->libdir . '/filelib.php');
                foreach ($files as $key => $file) {
                    $icon = mimeinfo('icon', $file);
                    if ($CFG->slasharguments) {
                        $ffurl = "$CFG->wwwroot/file.php/$filearea/$file";
                    } else {
                        $ffurl = "$CFG->wwwroot/file.php?file=/$filearea/$file";
                    }
                    #$output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                    #        '<a href="'.$ffurl.'" >'.$file.'</a><br />';
                    // Syntax Highlighert source code
                    $output = link_to_popup_window('/mod/assignment/type/program/source.php?id=' . $this->cm->id . '&amp;userid=' . $userid . '&amp;file=' . $file, $file . ' source code', $file, 710, 780, $file, 'none', true, 'button' . $userid);
                }
            }
        }
        $output = '<div class="files">' . $output . '</div>';
        if ($return) {
            return $output;
        }
        echo $output;
    }

    /**
     * Return the programming language of this instance
     *
     * @return string Programming language
     */
    function get_language() {
        $lang = '';
        if ($ass = get_record('assignment', 'id', $this->cm->instance))
            $lang = $ass->lang;
        return $lang;
    }

    /**
     * Display programming language for this assignment
     */
    function view_lang() {
        $lang = $this->get_language();
        if (!empty ($lang)) {
            print_simple_box_start('center', '', '', 0, 'generalbox', 'dates');
            $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            echo format_text('<strong>Programing language:</strong> ' . get_string('lang' . $lang, 'assignment'), $this->assignment->format, $formatoptions);
            print_simple_box_end();
        }
    }

    /**
     * Print grade progress
     */
    function print_loading() {
        global $CFG;
        print_simple_box_start('center', '30%', '', '', 'generalbox', 'intro');
        echo '<p align="center">' . get_string('loading', 'assignment') . '</p>';
        echo '<p align="center"><img src="' . $CFG->pixpath . '/t/loading.gif" alt="Loading" width="20" /></p>';
        print_simple_box_end();
    }

    /**
     *  Display all the submissions ready for grading
     */
    function display_submissions() {
        global $CFG, $db, $USER;

        /* first we check to see if the form has just been submitted
         * to request user_preference updates
         */
        if (isset ($_POST['updatepref'])) {
            $perpage = optional_param('perpage', 10, PARAM_INT);
            $perpage = ($perpage <= 0) ? 10 : $perpage;
            set_user_preference('assignment_perpage', $perpage);
            set_user_preference('assignment_quickgrade', optional_param('quickgrade', 0, PARAM_BOOL));
        }

        /* next we get perpage and quickgrade (allow quick grade) params
         * from database
         */
        $perpage = get_user_preferences('assignment_perpage', 10);
        $quickgrade = get_user_preferences('assignment_quickgrade', 0);
        $teacherattempts = true; /// Temporary measure
        $page = optional_param('page', 0, PARAM_INT);
        $strsaveallfeedback = get_string('saveallfeedback', 'assignment');

        // Some shortcuts to make the code read better
        $course = $this->course;
        $assignment = $this->assignment;
        $cm = $this->cm;
        $tabindex = 1; //tabindex for quick grading tabbing; Not working for dropdowns yet

        add_to_log($course->id, 'assignment', 'view submission', 'submissions.php?id=' . $this->assignment->id, $this->assignment->id, $this->cm->id);

        print_header_simple(format_string($this->assignment->name, true), "", '<a href="index.php?id=' . $course->id . '">' . $this->strassignments . '</a> -> <a href="view.php?a=' . $this->assignment->id . '">' . format_string($this->assignment->name, true) . '</a> -> ' . $this->strsubmissions, '', '', true, update_module_button($cm->id, $course->id, $this->strassignment), navmenu($course, $cm));

        // Position swapped
        if ($groupmode = groupmode($course, $cm)) { // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, 'submissions.php?id=' . $this->cm->id);
        } else {
            $currentgroup = false;
        }

        // Get all teachers and students
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        } else {
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            $users = get_users_by_capability($context, 'mod/assignment:submit'); // everyone with this capability set to non-prohibit
        }

        $tablecolumns = array (
            'picture',
            'fullname',
            'grade',
            'compileerrors',
            'runtimeerrors',
            'submissioncomment',
            'timemodified',
            'timemarked',
            'status'
        );

        $tableheaders = array (
            '',
            get_string('fullname'
        ), get_string('grade'), get_string('compileerrors', 'assignment'), get_string('runtimeerrors', 'assignment'), get_string('comment', 'assignment'), get_string('lastmodified') . ' (' . $course->student . ')', get_string('lastmodified') . ' (' . $course->teacher . ')', get_string('status'));

        require_once ($CFG->libdir . '/tablelib.php');
        $table = new flexible_table('mod-assignment-submissions');
        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot . '/mod/assignment/submissions.php?id=' . $this->cm->id . '&amp;currentgroup=' . $currentgroup);
        $table->sortable(true, 'lastname'); //sorted by lastname by default
        $table->collapsible(true);
        $table->initialbars(true);
        $table->column_suppress('picture');
        $table->column_suppress('fullname');
        $table->column_class('picture', 'picture');
        $table->column_class('fullname', 'fullname');
        $table->column_class('grade', 'grade');
        $table->column_class('compileerrors', 'comment');
        $table->column_class('runtimeerrors', 'comment');
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
            if (!empty ($teachers)) {
                $keys = array_keys($teachers);
            }
            foreach ($keys as $key) {
                unset ($users[$key]);
            }
        }

        if (empty ($users)) {
            print_heading(get_string('noattempts', 'assignment'));
            return true;
        }

        /// Construct the SQL
        if ($where = $table->get_sql_where()) {
            $where .= ' AND ';
        }

        if ($sort = $table->get_sql_sort()) {
            $sort = ' ORDER BY ' . $sort;
        }

        $select = 'SELECT u.id, u.firstname, u.lastname, u.picture,
                                  s.id AS submissionid, s.grade, s.submissioncomment, se.compileerrors,
                                  s.timemodified, s.timemarked ';
        $sql = 'FROM ' . $CFG->prefix . 'user u ' .        'LEFT JOIN ' . $CFG->prefix . 'assignment_submissions s ON u.id = s.userid
                                                                          AND s.assignment = ' . $this->assignment->id . ' ' .        'LEFT JOIN ' . $CFG->prefix . 'assignment_epaile_submissions se ON s.id = se.submission ' .        'WHERE ' . $where . 'u.id IN (' . implode(',', array_keys($users)) . ') ';
        $table->pagesize($perpage, count($users));

        ///offset used to calculate index of student in that particular query, needed for the pop up to know who's next
        $offset = $page * $perpage;
        $strupdate = get_string('update');
        $strgrade = get_string('grade');
        $grademenu = make_grades_menu($this->assignment->grade);

        if (($ausers = get_records_sql($select . $sql . $sort, $table->get_page_start(), $table->get_page_size())) !== false) {
            foreach ($ausers as $auser) {
                /// Calculate user status
                $auser->status = ($auser->timemarked > 0) && ($auser->timemarked >= $auser->timemodified);
                $picture = print_user_picture($auser->id, $course->id, $auser->picture, false, true);

                if (empty ($auser->submissionid)) {
                    $auser->grade = -1; //no submission yet
                }

                if (!empty ($auser->submissionid)) {
                    ///Prints student answer and student modified date
                    ///attach file or print link to student answer, depending on the type of the assignment.
                    ///Refer to print_student_answer in inherited classes.
                    if ($auser->timemodified > 0) {
                        $studentmodified = '<div id="ts' . $auser->id . '">' . $this->print_student_answer($auser->id) . userdate($auser->timemodified) . '</div>';
                    } else {
                        $studentmodified = '<div id="ts' . $auser->id . '">&nbsp;</div>';
                    }

                    ///Print grade, dropdown or text
                    if ($auser->timemarked > 0) {
                        $teachermodified = '<div id="tt' . $auser->id . '">' . userdate($auser->timemarked) . '</div>';
                        if ($quickgrade) {
                            $grade = '<div id="g' . $auser->id . '">' . choose_from_menu(make_grades_menu($this->assignment->grade), 'menu[' .                            $auser->id . ']', $auser->grade, get_string('nograde'), '', -1, true, false, $tabindex++) . '</div>';
                        } else {
                            $grade = '<div id="g' . $auser->id . '">' . $this->display_grade($auser->grade) . '</div>';
                        }
                    } else {
                        $teachermodified = '<div id="tt' . $auser->id . '">&nbsp;</div>';
                        if ($quickgrade) {
                            $grade = '<div id="g' . $auser->id . '">' . choose_from_menu(make_grades_menu($this->assignment->grade), 'menu[' .                            $auser->id . ']', $auser->grade, get_string('nograde'), '', -1, true, false, $tabindex++) . '</div>';
                        } else {
                            $grade = '<div id="g' . $auser->id . '">' . $this->display_grade($auser->grade) . '</div>';
                        }
                    }

                    ///Print Comment
                    if ($quickgrade) {
                        $comment = '<div id="com' . $auser->id . '"><textarea tabindex="' . $tabindex++ . '" name="submissioncomment[' . $auser->id . ']" id="submissioncomment[' . $auser->id . ']">' . ($auser->submissioncomment) . '</textarea></div>';
                    } else {
                        $comment = '<div id="com' . $auser->id . '">' . shorten_text(strip_tags($auser->submissioncomment), 15) . '</div>';
                    }
                } else {
                    $studentmodified = '<div id="ts' . $auser->id . '">&nbsp;</div>';
                    $teachermodified = '<div id="tt' . $auser->id . '">&nbsp;</div>';
                    $status = '<div id="st' . $auser->id . '">&nbsp;</div>';

                    if ($quickgrade) { // allow editing
                        $grade = '<div id="g' . $auser->id . '">' . choose_from_menu(make_grades_menu($this->assignment->grade), 'menu[' .                        $auser->id . ']', $auser->grade, get_string('nograde'), '', -1, true, false, $tabindex++) . '</div>';
                    } else {
                        $grade = '<div id="g' . $auser->id . '">-</div>';
                    }

                    if ($quickgrade) {
                        $comment = '<div id="com' . $auser->id . '"><textarea tabindex="' . $tabindex++ . '" name="submissioncomment[' . $auser->id . ']" id="submissioncomment[' . $auser->id . ']">' . ($auser->submissioncomment) . '</textarea></div>';
                    } else {
                        $comment = '<div id="com' . $auser->id . '">&nbsp;</div>';
                    }
                }

                ///Print Compile errors
                $compileerrors = ($auser->compileerrors) ? 'Yes' : 'No';
                $compileerrors = '<div id="ce' . $auser->id . '">' . $compileerrors . '</div>';

                ///Print Runtime errors
                $runtimeerrors = ($this->get_errors_number($auser->submissionid)>0) ? 'Yes' : 'No';
                $runtimeerrors = '<div id="re' . $auser->id . '">' . $runtimeerrors . '</div>';

                if (empty ($auser->status)) { /// Confirm we have exclusively 0 or 1
                    $auser->status = 0;
                } else {
                    $auser->status = 1;
                }

                $buttontext = ($auser->status == 1) ? $strupdate : $strgrade;

                ///No more buttons, we use popups ;-).
                $button = link_to_popup_window('/mod/assignment/submissions.php?id=' . $this->cm->id . '&amp;userid=' . $auser->id . '&amp;mode=single' . '&amp;offset=' . $offset++, 'grade' .                $auser->id, $buttontext, 500, 780, $buttontext, 'none', true, 'button' . $auser->id);
                $status = '<div id="up' . $auser->id . '" class="s' . $auser->status . '">' . $button . '</div>';
                $row = array (
                    $picture,
                    fullname($auser
                ), $grade, $compileerrors, $runtimeerrors, $comment, $studentmodified, $teachermodified, $status);
                $table->add_data($row);
            }
        }

        /// Print quickgrade form around the table
        if ($quickgrade) {
            echo '<form action="submissions.php" name="fastg" method="post">';
            echo '<input type="hidden" name="id" value="' . $this->cm->id . '">';
            echo '<input type="hidden" name="mode" value="fastgrade">';
            echo '<input type="hidden" name="page" value="' . $page . '">';
            echo '<p align="center"><input type="submit" name="fastg" value="' . get_string('saveallfeedback', 'assignment') . '" /></p>';
        }

        $table->print_html(); /// Print the whole table

        if ($quickgrade) {
            echo '<p align="center"><input type="submit" name="fastg" value="' . get_string('saveallfeedback', 'assignment') . '" /></p>';
            echo '</form>';
        }

        /// End of fast grading form
        /// Mini form for setting user preference
        echo '<br />';
        echo '<form name="options" action="submissions.php?id=' . $this->cm->id . '" method="post">';
        echo '<input type="hidden" id="updatepref" name="updatepref" value="1" />';
        echo '<table id="optiontable" align="center">';
        echo '<tr align="right"><td>';
        echo '<label for="perpage">' . get_string('pagesize', 'assignment') . '</label>';
        echo ':</td>';
        echo '<td align="left">';
        echo '<input type="text" id="perpage" name="perpage" size="1" value="' . $perpage . '" />';
        helpbutton('pagesize', get_string('pagesize', 'assignment'), 'assignment');
        echo '</td></tr>';
        echo '<tr align="right">';
        echo '<td>';
        print_string('quickgrade', 'assignment');
        echo ':</td>';
        echo '<td align="left">';

        if ($quickgrade) {
            echo '<input type="checkbox" name="quickgrade" value="1" checked="checked" />';
        } else {
            echo '<input type="checkbox" name="quickgrade" value="1" />';

        }

        helpbutton('quickgrade', get_string('quickgrade', 'assignment'), 'assignment') . '</p></div>';
        echo '</td></tr>';
        echo '<tr>';
        echo '<td colspan="2" align="right">';
        echo '<input type="submit" value="' . get_string('savepreferences') . '" />';
        echo '</td></tr></table>';
        echo '</form>';

        ///End of mini form
        print_footer($this->course);
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
        $offset = required_param('offset', PARAM_INT); //offset for where to start looking for student.

        if (!$user = get_record('user', 'id', $userid)) {
            error('No such user!');
        }

        if (!$submission = $this->get_submission($user->id)) {
            $submission = $this->prepare_new_submission($userid);
        }

        if (isset($submission->id)) {
            $comp = $this->get_compile($submission->id);
        }

        if ($submission->timemodified > $submission->timemarked) {
            $subtype = 'assignmentnew';
        } else {
            $subtype = 'assignmentold';
        }

        /// construct SQL, using current offset to find the data of the next student
        $course = $this->course;
        $assignment = $this->assignment;
        $cm = $this->cm;

        /// Get all teachers and students
        $currentgroup = get_current_group($course->id);
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        } else {
            $users = get_course_users($course->id);
        }

        $select = 'SELECT u.id, u.firstname, u.lastname, u.picture,
                                  s.id AS submissionid, s.grade, s.submissioncomment, se.compileerrors,
                                  s.timemodified, s.timemarked ';
        $sql = 'FROM ' . $CFG->prefix . 'user u ' .        'LEFT JOIN ' . $CFG->prefix . 'assignment_submissions s ON u.id = s.userid
                                                                          AND s.assignment = ' . $this->assignment->id . ' ' .        'LEFT JOIN ' . $CFG->prefix . 'assignment_epaile_submissions se ON s.id = se.submission ' .        'WHERE u.id IN (' . implode(',', array_keys($users)) . ') ';

        require_once ($CFG->libdir . '/tablelib.php');
        if ($sort = flexible_table :: get_sql_sort('mod-assignment-submissions')) {
            $sort = 'ORDER BY ' . $sort . ' ';
        }

        $nextid = 0;
        if (($auser = get_records_sql($select . $sql . $sort, $offset +1, 1)) !== false) {
            $nextuser = array_shift($auser);
            /// Calculate user status
            $nextuser->status = ($nextuser->timemarked > 0) && ($nextuser->timemarked >= $nextuser->timemodified);
            $nextid = $nextuser->id;
        }

        print_header(get_string('feedback', 'assignment') . ':' . fullname($user, true) . ':' . format_string($this->assignment->name));

        /// Print any extra javascript needed for saveandnext
        echo $extra_javascript;

        ///Some javascript to help with setting up >.>
        echo '<script type="text/javascript">' . "\n";
        echo 'function setNext(){' . "\n";
        echo 'document.submitform.mode.value=\'next\';' . "\n";
        echo 'document.submitform.userid.value="' . $nextid . '";' . "\n";
        echo '}' . "\n";
        echo 'function saveNext(){' . "\n";
        echo 'document.submitform.mode.value=\'saveandnext\';' . "\n";
        echo 'document.submitform.userid.value="' . $nextid . '";' . "\n";
        echo 'document.submitform.saveuserid.value="' . $userid . '";' . "\n";
        echo 'document.submitform.menuindex.value = document.submitform.grade.selectedIndex;' . "\n";
        echo '}' . "\n";
        echo '</script>' . "\n";
        echo '<table cellspacing="0" class="feedback ' . $subtype . '" >';

        ///Start of student info row
        echo '<tr>';
        echo '<td width="35" valign="top" class="picture teacher">';
        print_user_picture($user->id, $this->course->id, $user->picture);
        echo '</td>';
        echo '<td class="topic">';
        echo '<div class="from">';
        echo '<div class="fullname">' . fullname($user, true) . '</div>';

        if ($submission->timemodified) {
            echo '<div class="time">' . userdate($submission->timemodified) .            $this->display_lateness($submission->timemodified) . '</div>';
        }
        echo '</div>';

        $this->print_user_files($user->id);
        if(isset($comp))
            $this->print_user_errors($user->id,$comp->compileerrors, '');
        echo '</td>';
        echo '</tr>';
        ///End of student info row

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
        echo '<td class="content">';
        echo '<form name="submitform" action="submissions.php" method="post">';
        echo '<input type="hidden" name="offset" value="' . ++ $offset . '">';
        echo '<input type="hidden" name="userid" value="' . $userid . '" />';
        echo '<input type="hidden" name="id" value="' . $this->cm->id . '" />';
        echo '<input type="hidden" name="mode" value="grade" />';
        echo '<input type="hidden" name="menuindex" value="0" />'; //selected menu index
        //new hidden field, initialized to -1.
        echo '<input type="hidden" name="saveuserid" value="-1" />';
        if ($submission->timemarked) {
            echo '<div class="from">';
            echo '<div class="fullname">' . fullname($teacher, true) . '</div>';
            echo '<div class="time">' . userdate($submission->timemarked) . '</div>';
            echo '</div>';
        }
        echo '<div class="grade">' . get_string('grade') . ':';
        choose_from_menu(make_grades_menu($this->assignment->grade), 'grade', $submission->grade, get_string('nograde'), '', -1);
        echo '</div>';
        echo '<div class="clearer"></div>';
        $this->preprocess_submission($submission);
        echo '<br />';
        print_textarea($this->usehtmleditor, 14, 58, 0, 0, 'submissioncomment', $submission->submissioncomment, $this->course->id);
        if ($this->usehtmleditor) {
            echo '<input type="hidden" name="format" value="' . FORMAT_HTML . '" />';
        } else {
            echo '<div align="right" class="format">';
            choose_from_menu(format_text_menu(), "format", $submission->format, "");
            helpbutton("textformat", get_string("helpformatting"));
            echo '</div>';
        }
        ///End of teacher info row
        ///Print Buttons in Single View
        echo '<div class="buttons" align="center">';
        echo '<input type="submit" name="submit" value="' . get_string('savechanges') . '" onclick = "document.submitform.menuindex.value = document.submitform.grade.selectedIndex" />';
        echo '<input type="submit" name="cancel" value="' . get_string('cancel') . '" />';
        //if there are more to be graded.
        if ($nextid) {
            echo '<input type="submit" name="saveandnext" value="' . get_string('saveandnext') . '" onclick="saveNext()" />';
            echo '<input type="submit" name="next" value="' . get_string('next') . '" onclick="setNext();" />';
        }
        echo '</div>';
        echo '</form>';
        $customfeedback = $this->custom_feedbackform($submission, true);
        if (!empty ($customfeedback)) {
            echo $customfeedback;
        }
        echo '</td></tr>';
        echo '</table>';
        if ($this->usehtmleditor) {
            use_html_editor();
        }
        print_footer('none');
    }

    /**
     * Show compile or runtime errors
     *
     * @param $userid int optional id of the user. If 0 then $USER->id is used.
     * @param $return boolean optional defaults to false. If true the list is returned rather than printed
     * @return string optional
     */
    function print_user_errors($userid = 0, $compileerrors = '', $runtimeerrors = '', $return = false) {
        global $CFG, $USER;

        if (!$userid) {
            if (!isloggedin()) {
                return '';
            }
            $userid = $USER->id;
        }

        $output = '';
        $clear = '<div class="clearer"></div>';
        if ($compileerrors) {
            $output .= '<br />Compile errors:<br />';
            $output .= '<div class="generaltable epaileerror"><pre>' . $compileerrors . '</pre></div>';
        }

        if ($runtimeerrors) {
            $output .= '<br />';
            $output .= 'Runtime output:<br />';
            $output .= '<div class="generaltable"><pre>' . $runtimeerrors . '<pre></div>';
        }

        $output = '<div class="errors">' . $output . '</div>';

        if ($return) {
            return $clear . $output;
        }

        echo $clear . $output;
    }

    /**
     * This function returns an
     * array of possible memory sizes in an array, translated to the
     * local language.
     *
     * @uses SORT_NUMERIC
     * @param int $sizebytes Moodle site $CGF->assignment_maxmem
     * @return array
     */
    function get_max_memory_usages($sitebytes=0) {
        global $CFG;

        // Get max size
        $maxsize = $sitebytes;

        $memusage[$maxsize] = display_size($maxsize);

        $sizelist = array(10240, 51200, 102400, 512000, 1048576, 2097152,
                          5242880, 10485760);

        // Allow maxbytes to be selected if it falls outside the above boundaries
        if( isset($CFG->maxmem) && !in_array($CFG->maxmem, $sizelist) ){
                $sizelist[] = $CFG->maxbytes;
        }

        foreach ($sizelist as $sizebytes) {
           if ($sizebytes < $maxsize) {
               $memusage[$sizebytes] = display_size($sizebytes);
           }
        }

        krsort($memusage, SORT_NUMERIC);

        return $memusage;
    }

    /**
     * This function returns an
     * array of possible CPU time (in seconds) in an array
     *
     * @uses SORT_NUMERIC
     * @param int $time Moodle site $CGF->assignment_maxcpu
     * @return array
     */
    function get_max_cpu_times($time=0) {
        global $CFG;

        // Get max size
        $maxtime = $time;

        $cputime[$maxtime] = $maxtime.' secs';

        $timelist = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 20, 25, 30, 40, 50, 60);

        // Allow maxtime to be selected if it falls outside the above boundaries
        if( isset($CFG->maxcpu) && !in_array($CFG->maxcpu, $timelist) ){
                $cputime[] = $CFG->maxbytes;
        }

        foreach ($timelist as $timesecs) {
           if ($timesecs < $maxtime) {
               $cputime[$timesecs] = $timesecs.' secs';
           }
        }

        krsort($cputime, SORT_NUMERIC);

        return $cputime;
    }
}

/**
 * OTHER GENERAL FUNCTIONS FOR PROGRAM ASSIGNMENTS
 */

/**
 * Returns an array of installed programming languages indexed and sorted by name
 *
 * @return array The index is the name of the assignment type, the value its full name from the language strings
 */
function assignment_program_languages() {
    global $CFG;
    $lang = array ();
    $dir = $CFG->dirroot . '/mod/assignment/type/program/languages/';
    $files = get_directory_list($dir);
    $names = preg_replace('/\.(\w+)/', '', $files); // Replace file extension with nothing
    foreach ($names as $name) {
        $lang[$name] = get_string('lang' . $name, 'assignment');
    }
    asort($lang);
    return $lang;
}

/**
 * This function returns an
 * array of possible CPU time (in seconds) in an array
 *
 * This is done by calling the get_max_cpu_times() method of the assignment type class
 */
function assignment_program_get_max_cpu_times($time=0) {
    $ass = new assignment_program();

    return $ass->get_max_cpu_times($time);
}

/**
 * This function returns an
 * array of possible memory sizes in an array, translated to the
 * local language.
 *
 * This is done by calling the get_max_memory_usages() method of the assignment type class
 */
function assignment_program_get_max_memory_usages($sitebytes=0) {
    $ass = new assignment_program();

    return $ass->get_max_memory_usages($sitebytes);
}
?>
