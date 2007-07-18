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
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param int $page The current page being viewed (when report is paged)
     * @param int $sortitemid The id of the grade_item by which to sort the table
     */
    function grade_report_grader($courseid, $gpr, $context, $page=null, $sortitemid=null) {
        global $CFG;
        parent::grade_report($courseid, $gpr, $context, $page);

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

            // Get the grade object to compare old value with new value, the grade might not exist yet
            $grade = new grade_grades(array('userid'=>$userid, 'itemid'=>$grade_item->id));

            if ($data_type == 'feedback') {
                if ($text = $grade->load_text()) {
                    if ($text->feedback !== $postedvalue) {
                        $feedback       = $postedvalue;
                        $feedbackformat = $text->feedbackformat; // keep original format or else we would have to do proper conversion (but it is not always possible)
                        $needsupdate    = true;
                    }
                } else if (is_null($postedvalue)) {
                    //nothing to do - grade does not have text or does not exist yet
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
        $this->currentgroup = setup_and_print_groups($course, $groupmode, $this->pbarurl);
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

        global $SESSION;

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

        $sql = "SELECT g.id, g.itemid, g.userid, g.finalgrade, g.hidden, g.locked, g.locktime, g.overridden,
                       gt.feedback, gt.feedbackformat,
                       gi.grademin, gi.grademax
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

        $html .= $this->print_toggle('averages', true);
        $html .= $this->print_toggle('groups', true);
        $html .= $this->print_toggle('ranges', true);
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
                       'averages' => 'sigma');

        $pref_name = 'grade_report_show' . $type;
        $show_pref = get_user_preferences($pref_name, $CFG->$pref_name);

        $strshow = $this->get_lang_string('show' . $type, 'grades');
        $strhide = $this->get_lang_string('hide' . $type, 'grades');

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

        $strsortasc  = $this->get_lang_string('sortasc', 'grades');
        $strsortdesc = $this->get_lang_string('sortdesc', 'grades');
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
                // Load user preferences for categories
                if ($element['type'] == 'category') {
                    $categoryid = $element['object']->id;
                    $aggregationview = $this->get_pref('aggregationview', $categoryid);

                    if ($aggregationview == GRADE_REPORT_AGGREGATION_VIEW_COMPACT) {
                        $categorystate = get_user_preferences('grade_report_categorystate' . $categoryid, GRADE_CATEGORY_EXPANDED);

                        // Expand/Contract icon must be set appropriately
                        if ($categorystate == GRADE_CATEGORY_CONTRACTED) {
                            // The category is contracted: this means we only show 1 item for this category: the
                            // category's aggregation item. The others must be removed from the grade_tree
                        } elseif ($categorystate == GRADE_CATEGORY_EXPANDED) {
                            // The category is expanded: we only show the non-aggregated items directly descending
                            // from this category. The category's grade_item must be removed from the grade_tree
                        }
                    }
                }

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
                    $headerhtml .= '<th class="'.$type.$catlevel.'" '.$colspan.'>&nbsp;</th>';
                } else if ($type == 'category') {
                    $headerhtml .= '<th class="category'.$catlevel.'" '.$colspan.'>'.$element['object']->get_name();

                    // Print icons
                    if ($USER->gradeediting) {
                        $headerhtml .= $this->get_icons($element);
                    }

                    $headerhtml .= '</th>';
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
                              .$this->get_lang_string('modulename', $object->itemmodule).'"/>';
                    } else if ($object->itemtype == 'manual') {
                        //TODO: add manual grading icon
                        $icon = '<img src="'.$CFG->pixpath.'/t/edit.gif" class="icon" alt="'.$this->get_lang_string('manualgrade', 'grades')
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
        $strfeedback = $this->get_lang_string("feedback");
        $gradetabindex = 1;
        $feedbacktabindex = 16380; // The maximum number of tabindices on 1 page is 32767
        $showuserimage = $this->get_pref('showuserimage');

        // Preload scale objects for items with a scaleid
        $scales_list = '';
        foreach ($this->items as $item) {
            if (!empty($item->scaleid)) {
                $scales_list .= "$item->scaleid,";
            }
        }
        $scales_array = array();

        if (!empty($scales_list)) {
            $scales_list = substr($scales_list, 0, -1);
            $scales_array = get_records_list('scale', 'id', $scales_list);
        }

        foreach ($this->users as $userid => $user) {
            // Student name and link
            $user_pic = null;
            if ($showuserimage) {
                $user_pic = '<div class="userpic">' . print_user_picture($user->id, $this->courseid, true, 0, true) . '</div>';
            }

            $studentshtml .= '<tr><th class="user">' . $user_pic . '<a href="' . $CFG->wwwroot . '/user/view.php?id='
                          . $user->id . '">' . fullname($user) . '</a></th>';

            foreach ($this->items as $item) {
                // Get the decimal points preference for this item
                $decimalpoints = $this->get_pref('decimalpoints', $item->id);

                if (isset($this->finalgrades[$userid][$item->id])) {
                    $gradeval = $this->finalgrades[$userid][$item->id]->finalgrade;

                    $grade = new grade_grades($this->finalgrades[$userid][$item->id], false);
                    $grade->feedback = stripslashes_safe($this->finalgrades[$userid][$item->id]->feedback);
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
                if (!empty($grade->id) && $USER->gradeediting) {
                    $states = array('is_hidden' => $item->hidden,
                                    'is_locked' => $item->locked,
                                    'is_editable' => $item->gradetype != GRADE_TYPE_NONE && !$grade->locked && !$item->locked);
                    $studentshtml .= $this->get_icons($element, null, true, $states);
                }

                // if in editting mode, we need to print either a text box
                // or a drop down (for scales)
                // grades in item of type grade category or course are not directly editable
                if ($USER->gradeediting) {
                    // We need to retrieve each grade_grade object from DB in order to
                    // know if they are hidden/locked

                    if ($item->scaleid && !empty($scales_array[$item->scaleid])) {
                        $scale = $scales_array[$item->scaleid];

                        $scales = explode(",", $scale->scale);
                        // reindex because scale is off 1
                        $i = 0;
                        foreach ($scales as $scaleoption) {
                            $i++;
                            $scaleopt[$i] = $scaleoption;
                        }

                        if ($this->get_pref('quickgrading') and $grade->is_editable()) {
                            $studentshtml .= choose_from_menu($scaleopt, 'grade_'.$userid.'_'.$item->id,
                                                          $gradeval, $this->get_lang_string('nograde'), '', '-1', true, false, $gradetabindex++);
                        } elseif(!empty($scale)) {
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

                    } else if ($item->gradetype != GRADE_TYPE_TEXT) { // Value type
                        if ($this->get_pref('quickgrading') and $grade->is_editable()) {
                            $studentshtml .= '<input size="6" tabindex="' . $gradetabindex++ . '" type="text" name="grade_'.$userid.'_'
                                          .$item->id.'" value="'.$this->get_grade_clean($gradeval, $decimalpoints).'"/>';
                        } else {
                            $studentshtml .= $this->get_grade_clean($gradeval, $decimalpoints);
                        }
                    }


                    // If quickfeedback is on, print an input element
                    if ($this->get_pref('quickfeedback') and $grade->is_editable()) {
                        if ($this->get_pref('quickgrading')) {
                            $studentshtml .= '<br />';
                        }
                        $studentshtml .= '<input tabindex="' . $feedbacktabindex++ . '" size="6" type="text" name="feedback_'
                                      .$userid.'_'.$item->id.'" value="' . s($grade->feedback) . '"/>';
                    }

                } else {
                    // Percentage format if specified by user (check each item for a set preference)
                    $gradedisplaytype = $this->get_pref('gradedisplaytype', $item->id);

                    $percentsign = '';
                    $grademin = $item->grademin;
                    $grademax = $item->grademax;

                    if ($gradedisplaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE) {
                        if (!is_null($gradeval)) {
                            $gradeval = grade_grades::standardise_score($gradeval, $grademin, $grademax, 0, 100);
                        }
                        $percentsign = '%';
                    }

                    // If feedback present, surround grade with feedback tooltip
                    if (!empty($grade->feedback)) {
                        if ($grade->feedbackformat == 1) {
                            $overlib = "return overlib('" . s(ltrim($grade->feedback)) . "', FULLHTML);";
                        } else {
                            $overlib = "return overlib('" . ($grade->feedback) . "', CAPTION, '$strfeedback');";
                        }

                        $studentshtml .= '<span onmouseover="' . $overlib . '" onmouseout="return nd();">';
                    }

                    if ($gradedisplaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER) {
                        $letters = grade_report::get_grade_letters();
                        if (!is_null($gradeval)) {
                            $studentshtml .= grade_grades::get_letter($letters, $gradeval, $grademin, $grademax);
                        }
                    } else if ($item->scaleid && !empty($scales_array[$item->scaleid])
                                && $gradedisplaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL) {
                        $scale = $scales_array[$item->scaleid];
                        $scales = explode(",", $scale->scale);

                        // invalid grade if gradeval < 1
                        if ((int) $gradeval < 1) {
                            $studentshtml .= '-';
                        } else {
                            $studentshtml .= $scales[$gradeval-1];
                        }
                    } else {
                        if (is_null($gradeval)) {
                            $studentshtml .= '-';
                        } else {
                            $studentshtml .=  $this->get_grade_clean($gradeval, $decimalpoints). $percentsign;
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
    function get_groupavghtml() {
        global $CFG, $USER;

        $averagesdisplaytype = $this->get_pref('averagesdisplaytype');
        $mean_pref = $this->get_pref('meanselection');
        $groupavghtml = '';

        if ($mean_pref == GRADE_AGGREGATE_MEAN_GRADED) {
            // non empty grades
            $meanstr = "AND NOT g.finalgrade IS NULL";
        } else {
            $meanstr = "";
        }

        if ($this->currentgroup && $this->get_pref('showgroups')) {

        /** SQL for finding group sum */
        // do not sum -1 (no grade), treat as 0 for now
            $SQL = "SELECT g.itemid, SUM(g.finalgrade) as sum, COUNT(DISTINCT(u.id)) as count
                FROM {$CFG->prefix}grade_items gi LEFT JOIN
                     {$CFG->prefix}grade_grades g ON gi.id = g.itemid RIGHT OUTER JOIN
                     {$CFG->prefix}user u ON u.id = g.userid LEFT JOIN
                     {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                     $this->groupsql
                WHERE gi.courseid = $this->courseid
                     $this->groupwheresql
                AND ra.roleid in ($this->gradebookroles)
                AND ra.contextid ".get_related_contexts_string($this->context)."
                $meanstr
                GROUP BY g.itemid";

            $groupsum = array();
            $groupscount = array();
            $sums = get_records_sql($SQL);
            foreach ($sums as $itemid => $csum) {
                $groupsum[$itemid] = $csum->sum;
                $groupscount[$itemid] = $csum->count;
            }

            $groupavghtml = '<tr><th>'.get_string('groupavg', 'grades').'</th>';
            foreach ($this->items as $item) {
                $decimalpoints = $this->get_pref('decimalpoints', $item->id);
                // Determine which display type to use for this average
                $gradedisplaytype = $this->get_pref('gradedisplaytype', $item->id);
                if ($USER->gradeediting) {
                    $displaytype = GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL;
                } elseif ($averagesdisplaytype == GRADE_REPORT_PREFERENCE_INHERIT) { // Inherit specific column or general preference
                    $displaytype = $gradedisplaytype;
                } else { // General preference overrides specific column display type
                    $displaytype = $averagesdisplaytype;
                }

                if (empty($groupscount[$item->id]) || !isset($groupsum[$item->id])) {
                    $groupavghtml .= '<td>-</td>';
                } else {
                    $sum = $groupsum[$item->id];

                    if ($item->scaleid) {
                        $gradeitemsum = $groupsum[$item->id];
                        $gradeitemavg = $gradeitemsum/$groupscount[$item->id];

                        $scaleval = round($this->get_grade_clean($gradeitemavg, $decimalpoints));

                        $scales_array = get_records_list('scale', 'id', $item->scaleid);
                        $scale = $scales_array[$item->scaleid];
                        $scales = explode(",", $scale->scale);

                        // this could be a 0 when summed and rounded, e.g, 1, no grade, no grade, no grade
                        if ($scaleval < 1) {
                            $scaleval = 1;
                        }

                        $gradehtml = $scales[$scaleval-1];
                        $rawvalue = $scaleval;
                    } else {
                        $gradeval = $this->get_grade_clean($sum/$groupscount[$item->id], $decimalpoints);
                        $gradehtml = round($gradeval, $decimalpoints);
                        $rawvalue = $gradeval;
                    }

                    if ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE) {
                        $gradeval = grade_grades::standardise_score($rawvalue, $item->grademin, $item->grademax, 0, 100);
                        $gradehtml = round($gradeval, $decimalpoints) . '%';
                    } elseif ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER) {
                        $letters = grade_report::get_grade_letters();
                        $gradehtml = grade_grades::get_letter($letters, $gradeval, $item->grademin, $item->grademax);
                    }

                    $groupavghtml .= '<td>'.$gradehtml.'</td>';
                }
            }
            $groupavghtml .= '</tr>';
        }
        return $groupavghtml;
    }

    /**
     * Builds and return the HTML row of column totals.
     * @return string HTML
     */
    function get_gradeavghtml() {
        global $CFG, $USER;

        $averagesdisplaytype = $this->get_pref('averagesdisplaytype');
        $meanselection = $this->get_pref('meanselection');
        $mean_pref = get_user_preferences('grade_report_meanselection', $CFG->grade_report_meanselection);
        $gradeavghtml = '';

        if ($mean_pref == 2) {
            // non empty grades
            $meanstr = "AND NOT g.finalgrade IS NULL";
        } else {
            $meanstr = "";
        }
        if ($this->get_pref('showaverages')) {

            /** SQL for finding the SUM grades of all visible users ($CFG->gradebookroles) */
            // do not sum -1 (no grade), treat as 0 for now
            $SQL = "SELECT g.itemid, SUM(g.finalgrade) as sum, COUNT(DISTINCT(u.id)) as count
                FROM {$CFG->prefix}grade_items gi LEFT JOIN
                     {$CFG->prefix}grade_grades g ON gi.id = g.itemid RIGHT OUTER JOIN
                     {$CFG->prefix}user u ON u.id = g.userid LEFT JOIN
                     {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                WHERE gi.courseid = $this->courseid
                AND ra.roleid in ($this->gradebookroles)
                AND ra.contextid ".get_related_contexts_string($this->context)."
                $meanstr
                GROUP BY g.itemid";

            $classsum = array();

            $sums = get_records_sql($SQL);

            foreach ($sums as $itemid => $csum) {
                $classsum[$itemid] = $csum->sum;
                $classcount[$itemid] = $csum->count;
            }

            $gradeavghtml = '<tr><th>'.get_string('average', 'grades').'</th>';
            foreach ($this->items as $item) {
                $decimalpoints = $this->get_pref('decimalpoints', $item->id);
                // Determine which display type to use for this average
                $gradedisplaytype = $this->get_pref('gradedisplaytype', $item->id);
                if ($USER->gradeediting) {
                    $displaytype = GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL;
                } elseif ($averagesdisplaytype == GRADE_REPORT_PREFERENCE_INHERIT) { // Inherit specific column or general preference
                    $displaytype = $gradedisplaytype;
                } else { // General preference overrides specific column display type
                    $displaytype = $averagesdisplaytype;
                }

                if (empty($classcount[$item->id]) || !isset($classsum[$item->id])) {
                    $gradeavghtml .= '<td>-</td>';
                } else {
                    $sum = $classsum[$item->id];

                    if ($item->scaleid) {
                        $scaleval = round($this->get_grade_clean($sum/$classcount[$item->id], $decimalpoints));
                        $scales_array = get_records_list('scale', 'id', $item->scaleid);
                        $scale = $scales_array[$item->scaleid];
                        $scales = explode(",", $scale->scale);

                        // this could be a 0 when summed and rounded, e.g, 1, no grade, no grade, no grade
                        if ($scaleval < 1) {
                            $scaleval = 1;
                        }

                        $gradehtml = $scales[$scaleval-1];
                        $rawvalue = $scaleval;
                    } else {
                        $gradeval = $this->get_grade_clean($sum/$classcount[$item->id], $decimalpoints);

                        $gradehtml = round($gradeval, $decimalpoints);
                        $rawvalue = $gradeval;
                    }

                    if ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE) {
                        $gradeval = grade_grades::standardise_score($rawvalue, $item->grademin, $item->grademax, 0, 100);
                        $gradehtml = round($gradeval, $decimalpoints) . '%';
                    } elseif ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER) {
                        $letters = grade_report::get_grade_letters();
                        $gradehtml = grade_grades::get_letter($letters, $gradeval, $item->grademin, $item->grademax);
                    }

                    $gradeavghtml .= '<td>'.$gradehtml.'</td>';
                }
            }
            $gradeavghtml .= '</tr>';
        }
        return $gradeavghtml;
    }

    /**
     * Builds and return the HTML row of ranges for each column (i.e. range).
     * @return string HTML
     */
    function get_rangehtml() {
        global $USER;

        $scalehtml = '';
        if ($this->get_pref('showranges')) {
            $rangesdisplaytype = $this->get_pref('rangesdisplaytype');
            $scalehtml = '<tr><th class="range">'.$this->get_lang_string('range','grades').'</th>';
            foreach ($this->items as $item) {
                $decimalpoints = $this->get_pref('decimalpoints', $item->id);
                // Determine which display type to use for this range
                $gradedisplaytype = $this->get_pref('gradedisplaytype', $item->id);

                if ($USER->gradeediting) {
                    $displaytype = GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL;
                } elseif ($rangesdisplaytype == GRADE_REPORT_PREFERENCE_INHERIT) { // Inherit specific column or general preference
                    $displaytype = $gradedisplaytype;
                } else { // General preference overrides specific column display type
                    $displaytype = $rangesdisplaytype;
                }

                if ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL) {
                    $grademin = $this->get_grade_clean($item->grademin, $decimalpoints);
                    $grademax = $this->get_grade_clean($item->grademax, $decimalpoints);
                } elseif ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE) {
                    $grademin = 0;
                    $grademax = 100;
                } elseif ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER) {
                    $letters = grade_report::get_grade_letters();
                    $grademin = end($letters);
                    $grademax = reset($letters);
                }

                $scalehtml .= '<th class="range">'. $grademin.'-'. $grademax.'</th>';
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
     * @param object $states An optional array of states (hidden, locked, editable), shortcuts to increase performance.
     * @return string HTML
     */
    function get_icons($element, $icons=null, $limit=true, $states=array()) {
        global $CFG;
        global $USER;

        // Load language strings
        $stredit           = $this->get_lang_string("edit");
        $streditcalculation= $this->get_lang_string("editcalculation", 'grades');
        $strfeedback       = $this->get_lang_string("feedback");
        $strmove           = $this->get_lang_string("move");
        $strmoveup         = $this->get_lang_string("moveup");
        $strmovedown       = $this->get_lang_string("movedown");
        $strmovehere       = $this->get_lang_string("movehere");
        $strcancel         = $this->get_lang_string("cancel");
        $stredit           = $this->get_lang_string("edit");
        $strdelete         = $this->get_lang_string("delete");
        $strhide           = $this->get_lang_string("hide");
        $strshow           = $this->get_lang_string("show");
        $strlock           = $this->get_lang_string("lock", 'grades');
        $strswitch_minus   = $this->get_lang_string("contract", 'grades');
        $strswitch_plus    = $this->get_lang_string("expand", 'grades');
        $strunlock         = $this->get_lang_string("unlock", 'grades');

        // Prepare container div
        $html = '<div class="grade_icons">';

        // Prepare reference variables
        $eid    = $element['eid'];
        $object = $element['object'];
        $type   = $element['type'];

        if (empty($states)) {
            $states['is_hidden'] = $object->is_hidden();
            $states['is_locked'] = $object->is_locked();
            $states['is_editable'] = $object->is_editable();
        }

        // Add mock attributes in case the object is not of the right type
        if ($type != 'grade') {
            $object->feedback = '';
        }

        $overlib = '';
        if (!empty($object->feedback)) {
            if (empty($object->feedbackformat) || $object->feedbackformat != 1) {
                $function = "return overlib('" . strip_tags($object->feedback) . "', CAPTION, '$strfeedback');";
            } else {
                $function = "return overlib('" . s(ltrim($object->feedback) . "', FULLHTML);");
            }
            $overlib = 'onmouseover="' . $function . '" onmouseout="return nd();"';
        }

        // Prepare image strings
        $edit_icon = '';
        if ($states['is_editable']) {
            if ($type == 'category') {
                $url = GRADE_EDIT_URL . '/category.php?courseid='.$object->courseid.'&amp;id='.$object->id;
                $url = $this->gpr->add_url_params($url);
                $edit_icon = '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'
                           . $stredit.'" title="'.$stredit.'" /></a>'. "\n";
            } else if ($type == 'item' or $type == 'categoryitem' or $type == 'courseitem'){
                $url = GRADE_EDIT_URL . '/item.php?courseid='.$object->courseid.'&amp;id='.$object->id;
                $url = $this->gpr->add_url_params($url);
                $edit_icon = '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'
                           . $stredit.'" title="'.$stredit.'" /></a>'. "\n";
            } else if ($type == 'grade' and ($states['is_editable'] or empty($object->id))) {
            // TODO: change link to use itemid and userid to allow creating of new grade objects
                $url = GRADE_EDIT_URL . '/grade.php?courseid='.$object->courseid.'&amp;id='.$object->id;
                $url = $this->gpr->add_url_params($url);
                $edit_icon = '<a href="'.$url.'"><img ' . $overlib . ' src="'.$CFG->pixpath.'/t/edit.gif"'
                           . 'class="iconsmall" alt="' . $stredit.'" title="'.$stredit.'" /></a>'. "\n";
            }
        }

        $edit_calculation_icon = '';
        if ($type == 'item' or $type == 'courseitem' or $type == 'categoryitem') {
            // show calculation icon only when calculation possible
            if (!$object->is_normal_item() and ($object->gradetype == GRADE_TYPE_SCALE or $object->gradetype == GRADE_TYPE_VALUE)) {
                $url = GRADE_EDIT_URL . '/calculation.php?courseid='.$object->courseid.'&amp;id='.$object->id;
                $url = $this->gpr->add_url_params($url);
                $edit_calculation_icon = '<a href="'. $url.'"><img src="'.$CFG->pixpath.'/t/calc.gif" class="iconsmall" alt="'
                                       . $streditcalculation.'" title="'.$streditcalculation.'" /></a>'. "\n";
            }
        }

        // Prepare Hide/Show icon state
        $hide_show = 'hide';
        if ($states['is_hidden']) {
            $hide_show = 'show';
        }

        $show_hide_icon = '<a href="report.php?report=grader&amp;target='.$eid
                        . "&amp;action=$hide_show" . $this->gtree->commonvars . "\">\n"
                        . '<img src="'.$CFG->pixpath.'/t/'.$hide_show.'.gif" class="iconsmall" alt="'
                        . ${'str' . $hide_show}.'" title="'.${'str' . $hide_show}.'" /></a>'. "\n";

        // Prepare lock/unlock string
        $lock_unlock = 'lock';
        if ($states['is_locked']) {
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
            if (get_class($object) == 'grade_category' && $this->get_pref('aggregationview') == GRADE_REPORT_AGGREGATION_VIEW_COMPACT) {
                $html .= $contract_expand_icon;
            }
        } else { // Editing mode is off
        }

        return $html . '</div>';
    }
}
?>
