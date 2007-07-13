<?php // $Id$
/**
 * File in which the grader_report class is defined.
 * @package gradebook
 */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

/**
 * Class providing an API for the grader report building and displaying.
 * @uses grade_report
 * @package gradebook
 */
class grade_report_grader extends grade_report {
    /**
     * The final grades.
     * @var array $finalgrades
     */
    var $finalgrades;

    /**
     * The grade items.
     * @var array $items
     */
    var $items;

    /**
     * Array of errors for bulk grades updating.
     * @var array $gradeserror
     */
    var $gradeserror = array();

//// SQL-RELATED

    /**
     * The id of the grade_item by which this report will be sorted.
     * @var int $sortitemid
     */
    var $sortitemid;

    /**
     * Sortorder used in the SQL selections.
     * @var int $sortorder
     */
    var $sortorder;

    /**
     * An SQL fragment affecting the search for users.
     * @var string $userselect
     */
    var $userselect;

//// GROUP VARIABLES (including SQL)

    /**
     * The current group being displayed.
     * @var int $currentgroup
     */
    var $currentgroup;

    /**
     * A HTML select element used to select the current group.
     * @var string $group_selector
     */
    var $group_selector;

    /**
     * An SQL fragment used to add linking information to the group tables.
     * @var string $groupsql
     */
    var $groupsql;

    /**
     * An SQL constraint to append to the queries used by this object to build the report.
     * @var string $groupwheresql
     */
    var $groupwheresql;


    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param string $context
     * @param int $page The current page being viewed (when report is paged)
     * @param int $sortitemid The id of the grade_item by which to sort the table
     */
    function grade_report_grader($courseid, $context, $page=null, $sortitemid=null) {
        global $CFG;
        parent::grade_report($courseid, $context, $page);

        $this->sortitemid = $sortitemid;

        // base url for sorting by first/last name
        $this->baseurl = 'report.php?id='.$this->courseid.'&amp;perpage='.$this->get_pref('studentsperpage')
                        .'&amp;report=grader&amp;page='.$this->page;
        //
        $this->pbarurl = 'report.php?id='.$this->courseid.'&amp;perpage='.$this->get_pref('studentsperpage')
                        .'&amp;report=grader&amp;';

        if ($this->get_pref('showgroups')) {
            $this->setup_groups();
        }

        $this->setup_sortitemid();
    }

    /**
     * Processes the data sent by the form (grades and feedbacks).
     * @var array $data
     * @return bool Success or Failure (array of errors).
     */
    function process_data($data) {
        // always initialize all arrays
        $queue = array();

        foreach ($data as $varname => $postedvalue) {
            // this is a bit tricky - we have to first load all grades into memory,
            // check if changed and only then start updating the final grades because
            // columns might depend one on another - the result would be overriden calculated and category grades

            $needsupdate = false;
            $note = false; // TODO implement note??

            // skip, not a grade nor feedback
            if (strpos($varname, 'grade') === 0) {
                $data_type = 'grade';
            } else if (strpos($varname, 'feedback') === 0) {
                $data_type = 'feedback';
            } else {
                continue;
            }

            $gradeinfo = explode("_", $varname);

            $userid = clean_param($gradeinfo[1], PARAM_INT);
            $itemid = clean_param($gradeinfo[2], PARAM_INT);

            if (!$grade_item = grade_item::fetch(array('id'=>$itemid, 'courseid'=>$this->courseid))) { // we must verify course id here!
                error('Incorrect grade item id');
            }

            // Pre-process grade
            if ($data_type == 'grade') {
                $feedback = false;
                $feedbackformat = false;
                if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
                    if ($postedvalue == -1) { // -1 means no grade
                        $finalgrade = null;
                    } else {
                        $finalgrade = (float)$postedvalue;
                    }
                } else {
                    if ($postedvalue == '') { // empty string means no grade
                        $finalgrade = null;
                    } else {
                        $finalgrade = $this->format_grade($postedvalue);
                    }
                }
                if (!is_null($finalgrade) and ($finalgrade < $grade_item->grademin or $finalgrade > $grade_item->grademax)) {
                    $this->gradeserror[$grade_item->id][$userid] = 'outofrange'; //TODO: localize
                    // another possiblity is to use bounded number instead
                    continue;
                }

            } else if ($data_type == 'feedback') {
                $finalgrade = false;
                $trimmed = trim($postedvalue);
                if (empty($trimmed)) {
                    $postedvalue = NULL;
                }
            }

            // Get the grade object to compare old value with new value
            if ($grade = grade_grades::fetch(array('userid'=>$userid, 'itemid'=>$grade_item->id))) {
                if ($data_type == 'feedback') {
                    if ($text = $grade->load_text()) {
                        if ($text->feedback !== $postedvalue) {
                            $feedback       = $postedvalue;
                            $feedbackformat = $text->feedbackformat; // keep original format or else we would have to do proper conversion (but it is not always possible)
                            $needsupdate    = true;
                        }
                    } else {
                        $feedback       = $postedvalue;
                        $feedbackformat = FORMAT_MOODLE; // this is the default format option everywhere else
                        $needsupdate    = true;
                    }

                } else if ($data_type == 'grade') {
                    if (!is_null($grade->finalgrade)) {
                        $grade->finalgrade = (float)$grade->finalgrade;
                    }
                    if ($grade->finalgrade !== $finalgrade) {
                        $needsupdate = true;
                    }
                }
            } else { // The grade_grades record doesn't yet exist
                $needsupdate = true;
            }

            // we must not update all grades, only changed ones - we do not want to mark everything as overriden
            if ($needsupdate) {
                $gradedata = new object();
                $gradedata->grade_item     = $grade_item;
                $gradedata->userid         = $userid;
                $gradedata->note           = $note;
                $gradedata->finalgrade     = $finalgrade;
                $gradedata->feedback       = $feedback;
                $gradedata->feedbackformat = $feedbackformat;

                $queue[] = $gradedata;
            }
        }

        // now we update the new final grade for each changed grade
        foreach ($queue as $gradedata) {
            $gradedata->grade_item->update_final_grade($gradedata->userid, $gradedata->finalgrade, 'gradebook',
                                                       $gradedata->note, $gradedata->feedback, $gradedata->feedbackformat);
        }

        return true;
    }

    /**
     * Sets up this object's group variables, mainly to restrict the selection of users to display.
     */
    function setup_groups() {
        global $CFG;

        /// find out current groups mode
        $course = get_record('course', 'id', $this->courseid);
        $groupmode = $course->groupmode;
        ob_start();
        $this->currentgroup = setup_and_print_groups($course, $groupmode, $this->baseurl);
        $this->group_selector = ob_get_clean();

        // update paging after group
        $this->baseurl .= 'group='.$this->currentgroup.'&amp;';
        $this->pbarurl .= 'group='.$this->currentgroup.'&amp;';

        if ($this->currentgroup) {
            $this->groupsql = " LEFT JOIN {$CFG->prefix}groups_members gm ON gm.userid = u.id ";
            $this->groupwheresql = " AND gm.groupid = $this->currentgroup ";
        }
    }

    /**
     * Setting the sort order, this depends on last state
     * all this should be in the new table class that we might need to use
     * for displaying grades.
     */
    function setup_sortitemid() {
        if ($this->sortitemid) {
            if (!isset($SESSION->gradeuserreport->sort)) {
                $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
            } else {
                // this is the first sort, i.e. by last name
                if (!isset($SESSION->gradeuserreport->sortitemid)) {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                } else if ($SESSION->gradeuserreport->sortitemid == $this->sortitemid) {
                    // same as last sort
                    if ($SESSION->gradeuserreport->sort == 'ASC') {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                    } else {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                    }
                } else {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                }
            }
            $SESSION->gradeuserreport->sortitemid = $this->sortitemid;
        } else {
            // not requesting sort, use last setting (for paging)

            if (isset($SESSION->gradeuserreport->sortitemid)) {
                $this->sortitemid = $SESSION->gradeuserreport->sortitemid;
            }
            if (isset($SESSION->gradeuserreport->sort)) {
                $this->sortorder = $SESSION->gradeuserreport->sort;
            } else {
                $this->sortorder = 'ASC';
            }
        }
    }

    /**
     * pulls out the userids of the users to be display, and sort them
     * the right outer join is needed because potentially, it is possible not
     * to have the corresponding entry in grade_grades table for some users
     * this is check for user roles because there could be some users with grades
     * but not supposed to be displayed
     */
    function load_users() {
        global $CFG;

        if (is_numeric($this->sortitemid)) {
            $sql = "SELECT u.id, u.firstname, u.lastname
                    FROM {$CFG->prefix}grade_grades g RIGHT OUTER JOIN
                         {$CFG->prefix}user u ON (u.id = g.userid AND g.itemid = $this->sortitemid)
                         LEFT JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                         $this->groupsql
                    WHERE ra.roleid in ($this->gradebookroles)
                         $this->groupwheresql
                    AND ra.contextid ".get_related_contexts_string($this->context)."
                    ORDER BY g.finalgrade $this->sortorder";
            $this->users = get_records_sql($sql, $this->get_pref('studentsperpage') * $this->page,
                                $this->get_pref('studentsperpage'));
        } else {
            // default sort
            // get users sorted by lastname
            $this->users = get_role_users(@implode(',', $CFG->gradebookroles), $this->context, false,
                                'u.id, u.firstname, u.lastname', 'u.'.$this->sortitemid .' '. $this->sortorder,
                                false, $this->page * $this->get_pref('studentsperpage'), $this->get_pref('studentsperpage'),
                                $this->currentgroup);
            // need to cut users down by groups

        }

        if (empty($this->users)) {
            $this->userselect = '';
            $this->users = array();
        } else {
            $this->userselect = 'AND g.userid in ('.implode(',', array_keys($this->users)).')';
        }

        return $this->users;
    }

    /**
     * Fetches and returns a count of all the users that will be shows on this page.
     * @return int Count of users
     */
    function get_numusers() {
        global $CFG;
        $countsql = "SELECT COUNT(DISTINCT u.id)
                    FROM {$CFG->prefix}grade_grades g RIGHT OUTER JOIN
                         {$CFG->prefix}user u ON (u.id = g.userid AND g.itemid = $this->sortitemid)
                         LEFT JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                         $this->groupsql
                    WHERE ra.roleid in ($this->gradebookroles)
                         $this->groupwheresql
                    AND ra.contextid ".get_related_contexts_string($this->context);
        return count_records_sql($countsql);
    }

    /**
     * we supply the userids in this query, and get all the grades
     * pulls out all the grades, this does not need to worry about paging
     */
    function load_final_grades() {
        global $CFG;

        $sql = "SELECT g.id, g.itemid, g.userid, g.finalgrade, g.hidden, g.locked, g.locktime, g.overridden, gt.feedback, gt.feedbackformat
                FROM  {$CFG->prefix}grade_items gi,
                      {$CFG->prefix}grade_grades g
                LEFT JOIN {$CFG->prefix}grade_grades_text gt ON g.id = gt.gradeid
                WHERE g.itemid = gi.id
                AND gi.courseid = $this->courseid $this->userselect";

        if ($grades = get_records_sql($sql)) {
            foreach ($grades as $grade) {
                $this->finalgrades[$grade->userid][$grade->itemid] = $grade;
            }
        }
    }

    /**
     * Builds and returns a div with on/off toggles.
     * @return string HTML code
     */
    function get_toggles_html() {
        global $USER;
        $html = '<div id="grade-report-toggles">';
        if ($USER->gradeediting) {
            $html .= $this->print_toggle('eyecons', true);
            $html .= $this->print_toggle('locks', true);
            $html .= $this->print_toggle('calculations', true);
        }

        $html .= $this->print_toggle('grandtotals', true);
        $html .= $this->print_toggle('groups', true);
        $html .= $this->print_toggle('scales', true);
        $html .= '</div>';
        return $html;
    }

    /**
    * Shortcut function for printing the grader report toggles.
    * @param string $type The type of toggle
    * @param bool $return Whether to return the HTML string rather than printing it
    * @return void
    */
    function print_toggle($type, $return=false) {
        global $CFG;

        $icons = array('eyecons' => 'hide',
                       'calculations' => 'calc',
                       'locks' => 'lock',
                       'grandtotals' => 'sigma');

        $pref_name = 'grade_report_show' . $type;
        $show_pref = get_user_preferences($pref_name, $CFG->$pref_name);

        $strshow = get_string('show' . $type, 'grades');
        $strhide = get_string('hide' . $type, 'grades');

        $show_hide = 'show';
        $toggle_action = 1;

        if ($show_pref) {
            $show_hide = 'hide';
            $toggle_action = 0;
        }

        if (array_key_exists($type, $icons)) {
            $image_name = $icons[$type];
        } else {
            $image_name = $type;
        }

        $string = ${'str' . $show_hide};

        $img = '<img src="'.$CFG->pixpath.'/t/'.$image_name.'.gif" class="iconsmall" alt="'
                      .$string.'" title="'.$string.'" />'. "\n";

        $retval = '<div class="gradertoggle">' . $img . '<a href="' . $this->baseurl . "&amp;toggle=$toggle_action&amp;toggle_type=$type\">"
             . $string . '</a></div>';

        if ($return) {
            return $retval;
        } else {
            echo $retval;
        }
    }

    /**
     * Builds and returns the HTML code for the headers.
     * @return string $headerhtml
     */
    function get_headerhtml() {
        global $CFG, $USER;

        $strsortasc  = get_string('sortasc', 'grades');
        $strsortdesc = get_string('sortdesc', 'grades');
        if ($this->sortitemid === 'lastname') {
            if ($this->sortorder == 'ASC') {
                $lastarrow = print_arrow('up', $strsortasc, true);
            } else {
                $lastarrow = print_arrow('down', $strsortdesc, true);
            }
        } else {
            $lastarrow = '';
        }

        if ($this->sortitemid === 'firstname') {
            if ($this->sortorder == 'ASC') {
                $firstarrow = print_arrow('up', $strsortasc, true);
            } else {
                $firstarrow = print_arrow('down', $strsortdesc, true);
            }
        } else {
            $firstarrow = '';
        }
        // Prepare Table Headers
        $headerhtml = '';

        $numrows = count($this->gtree->levels);

        foreach ($this->gtree->levels as $key=>$row) {
            if ($key == 0) {
                // do not diplay course grade category
                // continue;
            }

            $headerhtml .= '<tr class="heading">';

            if ($key == $numrows - 1) {
                $headerhtml .= '<th class="user"><a href="'.$this->baseurl.'&amp;sortitemid=firstname">Firstname</a> ' //TODO: localize
                            . $firstarrow. '/ <a href="'.$this->baseurl.'&amp;sortitemid=lastname">Lastname </a>'. $lastarrow .'</th>';
            } else {
                $headerhtml .= '<td class="topleft">&nbsp;</td>';
            }

            foreach ($row as $element) {
                $eid    = $element['eid'];
                $object = $element['object'];
                $type   = $element['type'];

                if (!empty($element['colspan'])) {
                    $colspan = 'colspan="'.$element['colspan'].'"';
                } else {
                    $colspan = '';
                }

                if (!empty($element['depth'])) {
                    $catlevel = ' catlevel'.$element['depth'];
                } else {
                    $catlevel = '';
                }


                if ($type == 'filler' or $type == 'fillerfirst' or $type == 'fillerlast') {
                    $headerhtml .= '<td class="'.$type.$catlevel.'" '.$colspan.'>&nbsp;</td>';
                } else if ($type == 'category') {
                    $headerhtml .= '<td class="category'.$catlevel.'" '.$colspan.'>'.$element['object']->get_name();

                    // Print icons
                    if ($USER->gradeediting) {
                        $headerhtml .= $this->get_icons($element);
                    }

                    $headerhtml .= '</td>';
                } else {
                    if ($element['object']->id == $this->sortitemid) {
                        if ($this->sortorder == 'ASC') {
                            $arrow = print_arrow('up', $strsortasc, true);
                        } else {
                            $arrow = print_arrow('down', $strsortdesc, true);
                        }
                    } else {
                        $arrow = '';
                    }

                    $dimmed = '';
                    if ($element['object']->is_hidden()) {
                        $dimmed = ' dimmed_text ';
                    }

                    if ($object->itemtype == 'mod') {
                        $icon = '<img src="'.$CFG->modpixpath.'/'.$object->itemmodule.'/icon.gif" class="icon" alt="'
                              .get_string('modulename', $object->itemmodule).'"/>';
                    } else if ($object->itemtype == 'manual') {
                        //TODO: add manual grading icon
                        $icon = '<img src="'.$CFG->pixpath.'/t/edit.gif" class="icon" alt="'.get_string('manualgrade', 'grades')
                              .'"/>';
                    }


                    $headerhtml .= '<th class="'.$type.$catlevel.$dimmed.'"><a href="'.$this->baseurl.'&amp;sortitemid='
                              . $element['object']->id .'">'. $element['object']->get_name()
                              . '</a>' . $arrow;

                    $headerhtml .= $this->get_icons($element) . '</th>';

                    $this->items[$element['object']->sortorder] =& $element['object'];
                }

            }

            $headerhtml .= '</tr>';
        }
        return $headerhtml;
    }

    /**
     * Builds and return the HTML rows of the table (grades headed by student).
     * @return string HTML
     */
    function get_studentshtml() {
        global $CFG, $USER;
        $studentshtml = '';
        $strfeedback = get_string("feedback");

        foreach ($this->users as $userid => $user) {
            // Student name and link
            $studentshtml .= '<tr><th class="user"><a href="' . $CFG->wwwroot . '/user/view.php?id='
                          . $user->id . '">' . fullname($user) . '</a></th>';
            foreach ($this->items as $item) {

                if (isset($this->finalgrades[$userid][$item->id])) {
                    $gradeval = $this->finalgrades[$userid][$item->id]->finalgrade;
                    $grade = new grade_grades($this->finalgrades[$userid][$item->id], false);
                    $grade->feedback = $this->finalgrades[$userid][$item->id]->feedback;
                    $grade->feedbackformat = $this->finalgrades[$userid][$item->id]->feedbackformat;

                } else {
                    $gradeval = null;
                    $grade = new grade_grades(array('userid' => $userid, 'itemid' => $item->id), false);
                    $grade->feedback = '';
                }

                if ($grade->is_overridden()) {
                    $studentshtml .= '<td class="overridden">';
                } else {
                    $studentshtml .= '<td>';
                }

                // emulate grade element
                $grade->courseid = $this->courseid;
                $grade->grade_item = $item; // this may speedup is_hidden() and other grade_grades methods
                $element = array ('eid'=>'g'.$grade->id, 'object'=>$grade, 'type'=>'grade');

                // Do not show any icons if no grade (no record in DB to match)
                // TODO: change edit/hide/etc. links to use itemid and userid to allow creating of new grade objects
                if (!empty($grade->id)) {
                    $studentshtml .= $this->get_icons($element);
                }

                // if in editting mode, we need to print either a text box
                // or a drop down (for scales)

                // grades in item of type grade category or course are not directly editable
                if ($USER->gradeediting) {
                    // We need to retrieve each grade_grade object from DB in order to
                    // know if they are hidden/locked

                    if ($item->scaleid) {
                        if ($scale = get_record('scale', 'id', $item->scaleid)) {
                            $scales = explode(",", $scale->scale);
                            // reindex because scale is off 1
                            $i = 0;
                            foreach ($scales as $scaleoption) {
                                $i++;
                                $scaleopt[$i] = $scaleoption;
                            }

                            if ($this->get_pref('quickgrading') and $grade->is_editable()) {
                                $studentshtml .= choose_from_menu($scaleopt, 'grade_'.$userid.'_'.$item->id,
                                                              $gradeval, get_string('nograde'), '', -1, true);
                            } elseif ($scale = get_record('scale', 'id', $item->scaleid)) {
                                $scales = explode(",", $scale->scale);

                                // invalid grade if gradeval < 1
                                if ((int) $gradeval < 1) {
                                    $studentshtml .= '-';
                                } else {
                                    $studentshtml .= $scales[$gradeval-1];
                                }
                            } else {
                                // no such scale, throw error?
                            }
                        }

                    } else if ($item->gradetype != GRADE_TYPE_TEXT) {
                        if ($this->get_pref('quickgrading') and $grade->is_editable()) {
                            $studentshtml .= '<input size="6" type="text" name="grade_'.$userid.'_'
                                          .$item->id.'" value="'.$this->get_grade_clean($gradeval).'"/>';
                        } else {
                            $studentshtml .= $this->get_grade_clean($gradeval);
                        }
                    }


                    // If quickfeedback is on, print an input element
                    if ($this->get_pref('quickfeedback') and $grade->is_editable()) {
                        if ($this->get_pref('quickgrading')) {
                            $studentshtml .= '<br />';
                        }
                        $studentshtml .= '<input size="6" type="text" name="feedback_'.$userid.'_'.$item->id.'" value="'
                                      . s($grade->feedback) . '"/>';
                    }

                } else {
                    // If feedback present, surround grade with feedback tooltip
                    if (!empty($grade->feedback)) {
                        if ($grade->feedbackformat == 1) {
                            $overlib = "return overlib('$grade->feedback', CAPTION, '$strfeedback');";
                        } else {
                            $overlib = "return overlib('" . s(ltrim($grade->feedback)) . "', FULLHTML);";
                        }

                        $studentshtml .= '<span onmouseover="' . $overlib . '" onmouseout="return nd();">';
                    }

                    // finalgrades[$userid][$itemid] could be null because of the outer join
                    // in this case it's different than a 0
                    if ($item->scaleid) {
                        if ($scale = get_record('scale', 'id', $item->scaleid)) {
                            $scales = explode(",", $scale->scale);

                            // invalid grade if gradeval < 1
                            if ((int) $gradeval < 1) {
                                $studentshtml .= '-';
                            } else {
                                $studentshtml .= $scales[$gradeval-1];
                            }
                        } else {
                            // no such scale, throw error?
                        }
                    } else {
                        if (is_null($gradeval)) {
                            $studentshtml .= '-';
                        } else {
                            $studentshtml .=  $this->get_grade_clean($gradeval);
                        }
                    }
                    if (!empty($grade->feedback)) {
                        $studentshtml .= '</span>';
                    }
                }

                if (!empty($this->gradeserror[$item->id][$userid])) {
                    $studentshtml .= $this->gradeserror[$item->id][$userid];
                }

                $studentshtml .=  '</td>' . "\n";
            }
            $studentshtml .= '</tr>';
        }
        return $studentshtml;
    }

    /**
     * Builds and return the HTML rows of the table (grades headed by student).
     * @return string HTML
     */
    function get_groupsumhtml() {
        global $CFG;

        $groupsumhtml = '';

        if ($this->currentgroup && $this->get_pref('showgroups')) {

        /** SQL for finding group sum */
            $SQL = "SELECT g.itemid, SUM(g.finalgrade) as sum
                FROM {$CFG->prefix}grade_items gi LEFT JOIN
                     {$CFG->prefix}grade_grades g ON gi.id = g.itemid RIGHT OUTER JOIN
                     {$CFG->prefix}user u ON u.id = g.userid LEFT JOIN
                     {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                     $this->groupsql
                WHERE gi.courseid = $this->courseid
                     $this->groupwheresql
                AND ra.roleid in ($this->gradebookroles)
                AND ra.contextid ".get_related_contexts_string($this->context)."
                GROUP BY g.itemid";

            $groupsum = array();
            $sums = get_records_sql($SQL);
            foreach ($sums as $itemid => $csum) {
                $groupsum[$itemid] = $csum;
            }

            $groupsumhtml = '<tr><th>Group total</th>';
            foreach ($this->items as $item) {
                if (!isset($groupsum[$item->id])) {
                    $groupsumhtml .= '<td>-</td>';
                } else {
                    $sum = $groupsum[$item->id];
                    $groupsumhtml .= '<td>'.$this->get_grade_clean($sum->sum).'</td>';
                }
            }
            $groupsumhtml .= '</tr>';
        }
        return $groupsumhtml;
    }

    /**
     * Builds and return the HTML row of column totals.
     * @return string HTML
     */
    function get_gradesumhtml() {
        global $CFG;

        $gradesumhtml = '';
        if ($this->get_pref('showgrandtotals')) {

        /** SQL for finding the SUM grades of all visible users ($CFG->gradebookroles) */

            $SQL = "SELECT g.itemid, SUM(g.finalgrade) as sum
                FROM {$CFG->prefix}grade_items gi LEFT JOIN
                     {$CFG->prefix}grade_grades g ON gi.id = g.itemid RIGHT OUTER JOIN
                     {$CFG->prefix}user u ON u.id = g.userid LEFT JOIN
                     {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                WHERE gi.courseid = $this->courseid
                AND ra.roleid in ($this->gradebookroles)
                AND ra.contextid ".get_related_contexts_string($this->context)."
                GROUP BY g.itemid";

            $classsum = array();
            $sums = get_records_sql($SQL);
            foreach ($sums as $itemid => $csum) {
                $classsum[$itemid] = $csum;
            }

            $gradesumhtml = '<tr><th>Total</th>';
            foreach ($this->items as $item) {
                if (!isset($classsum[$item->id])) {
                    $gradesumhtml .= '<td>-</td>';
                } else {
                    $sum = $classsum[$item->id];
                    $gradesumhtml .= '<td>'.$this->get_grade_clean($sum->sum).'</td>';
                }
            }
            $gradesumhtml .= '</tr>';
        }
        return $gradesumhtml;
    }

    /**
     * Builds and return the HTML row of scales for each column (i.e. range).
     * @return string HTML
     */
    function get_scalehtml() {
        $scalehtml = '';
        if ($this->get_pref('showscales')) {
            $scalehtml = '<tr><td>'.get_string('range','grades').'</td>';
            foreach ($this->items as $item) {
                $scalehtml .= '<td>'. $this->get_grade_clean($item->grademin).'-'. $this->get_grade_clean($item->grademax).'</td>';
            }
            $scalehtml .= '</tr>';
        }
        return $scalehtml;
    }

    /**
     * Given a grade_category, grade_item or grade_grade, this function
     * figures out the state of the object and builds then returns a div
     * with the icons needed for the grader report.
     *
     * @param object $object
     * @param array $icons An array of icon names that this function is explicitly requested to print, regardless of settings
     * @param bool $limit If true, use the $icons array as the only icons that will be printed. If false, use it to exclude these icons.
     * @return string HTML
     */
    function get_icons($element, $icons=null, $limit=true) {
        global $CFG;
        global $USER;

        // Load language strings
        $stredit           = get_string("edit");
        $streditcalculation= get_string("editcalculation", 'grades');
        $strfeedback       = get_string("feedback");
        $strmove           = get_string("move");
        $strmoveup         = get_string("moveup");
        $strmovedown       = get_string("movedown");
        $strmovehere       = get_string("movehere");
        $strcancel         = get_string("cancel");
        $stredit           = get_string("edit");
        $strdelete         = get_string("delete");
        $strhide           = get_string("hide");
        $strshow           = get_string("show");
        $strlock           = get_string("lock", 'grades');
        $strswitch_minus   = get_string("contract", 'grades');
        $strswitch_plus    = get_string("expand", 'grades');
        $strunlock         = get_string("unlock", 'grades');

        // Prepare container div
        $html = '<div class="grade_icons">';

        // Prepare reference variables
        $eid    = $element['eid'];
        $object = $element['object'];
        $type   = $element['type'];

        // Add mock attributes in case the object is not of the right type
        if ($type != 'grade') {
            $object->feedback = '';
        }

        $overlib = '';
        if (!empty($object->feedback)) {
            if (empty($object->feedbackformat) || $object->feedbackformat != 1) {
                $function = "return overlib('$object->feedback', CAPTION, '$strfeedback');";
            } else {
                $function = "return overlib('" . s(ltrim($object->feedback)) . "', FULLHTML);";
            }
            $overlib = 'onmouseover="' . $function . '" onmouseout="return nd();"';
        }

        // Prepare image strings
        $edit_icon = '';
        if ($object->is_editable()) {
            if ($type == 'category') {
                $edit_icon = '<a href="'. GRADE_EDIT_URL . '/category.php?courseid='.$object->courseid.'&amp;id='.$object->id.'">'
                           . '<img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'
                           . $stredit.'" title="'.$stredit.'" /></a>'. "\n";
            } else if ($type == 'item' or $type == 'categoryitem' or $type == 'courseitem'){
                $edit_icon = '<a href="'. GRADE_EDIT_URL . '/item.php?courseid='.$object->courseid.'&amp;id='.$object->id.'">'
                           . '<img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'
                           . $stredit.'" title="'.$stredit.'" /></a>'. "\n";
            } else if ($type == 'grade' and ($object->is_editable() or empty($object->id))) {
            // TODO: change link to use itemid and userid to allow creating of new grade objects
                $edit_icon = '<a href="'. GRADE_EDIT_URL . '/grade.php?courseid='.$object->courseid.'&amp;id='.$object->id.'">'
                                 . '<img ' . $overlib . ' src="'.$CFG->pixpath.'/t/edit.gif"'
                                 . 'class="iconsmall" alt="' . $stredit.'" title="'.$stredit.'" /></a>'. "\n";
            }
        }

        $edit_calculation_icon = '';
        if ($type == 'item' or $type == 'courseitem' or $type == 'categoryitem') {
            // show calculation icon only when calculation possible
            if (!$object->is_normal_item() and ($object->gradetype == GRADE_TYPE_SCALE or $object->gradetype == GRADE_TYPE_VALUE)) {
                $edit_calculation_icon = '<a href="'. GRADE_EDIT_URL . '/calculation.php?courseid='.$object->courseid.'&amp;id='.$object->id.'">'
                                       . '<img src="'.$CFG->pixpath.'/t/calc.gif" class="iconsmall" alt="'
                                       . $streditcalculation.'" title="'.$streditcalculation.'" /></a>'. "\n";
            }
        }

        // Prepare Hide/Show icon state
        $hide_show = 'hide';
        if ($object->is_hidden()) {
            $hide_show = 'show';
        }

        $show_hide_icon = '<a href="report.php?report=grader&amp;target='.$eid
                        . "&amp;action=$hide_show" . $this->gtree->commonvars . "\">\n"
                        . '<img src="'.$CFG->pixpath.'/t/'.$hide_show.'.gif" class="iconsmall" alt="'
                        . ${'str' . $hide_show}.'" title="'.${'str' . $hide_show}.'" /></a>'. "\n";

        // Prepare lock/unlock string
        $lock_unlock = 'lock';
        if ($object->is_locked()) {
            $lock_unlock = 'unlock';
        }

        // Print lock/unlock icon

        $lock_unlock_icon = '<a href="report.php?report=grader&amp;target='.$eid
                          . "&amp;action=$lock_unlock" . $this->gtree->commonvars . "\">\n"
                          . '<img src="'.$CFG->pixpath.'/t/'.$lock_unlock.'.gif" class="iconsmall" alt="'
                          . ${'str' . $lock_unlock}.'" title="'.${'str' . $lock_unlock}.'" /></a>'. "\n";

        // Prepare expand/contract string
        $expand_contract = 'switch_minus'; // Default: expanded
        $state = get_user_preferences('grade_category_' . $object->id, GRADE_CATEGORY_EXPANDED);
        if ($state == GRADE_CATEGORY_CONTRACTED) {
            $expand_contract = 'switch_plus';
        }

        $contract_expand_icon = '<a href="report.php?report=grader&amp;target=' . $eid
                              . "&amp;action=$expand_contract" . $this->gtree->commonvars . "\">\n"
                              . '<img src="'.$CFG->pixpath.'/t/'.$expand_contract.'.gif" class="iconsmall" alt="'
                              . ${'str' . $expand_contract}.'" title="'.${'str' . $expand_contract}.'" /></a>'. "\n";

        // If an array of icon names is given, return only these in the order they are given
        if (!empty($icons) && is_array($icons)) {
            $new_html = '';

            foreach ($icons as $icon_name) {
                if ($limit) {
                    $new_html .= ${$icon_name . '_icon'};
                } else {
                    ${'show_' . $icon_name} = false;
                }
            }
            if ($limit) {
                return $new_html;
            } else {
                $html .= $new_html;
            }
        }

        // Icons shown when edit mode is on
        if ($USER->gradeediting) {
            // Edit icon (except for grade_grades)
            if ($edit_icon) {
                $html .= $edit_icon;
            }

            // Calculation icon for items and categories
            if ($this->get_pref('showcalculations')) {
                $html .= $edit_calculation_icon;
            }

            if ($this->get_pref('showeyecons')) {
                $html .= $show_hide_icon;
            }

            if ($this->get_pref('showlocks')) {
                $html .= $lock_unlock_icon;
            }

            // If object is a category, display expand/contract icon
            if (get_class($object) == 'grade_category' && $this->get_pref('aggregationview') == GRADER_REPORT_AGGREGATION_VIEW_COMPACT) {
                $html .= $contract_expand_icon;
            }
        } else { // Editing mode is off
        }

        return $html . '</div>';
    }
}
?>
