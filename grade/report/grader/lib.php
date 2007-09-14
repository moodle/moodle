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

    /**
     * List of collapsed categories from user preference
     * @var array $collapsed
     */
    var $collapsed;

    /**
     * A count of the rows, used for css classes.
     * @var int $rowcount
     */
    var $rowcount = 0;

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

        // load collapsed settings for this report
        if ($collapsed = get_user_preferences('grade_report_grader_collapsed_categories')) {
            $this->collapsed = unserialize($collapsed);
        } else {
            $this->collapsed = array('aggregatesonly' => array(), 'gradesonly' => array());
        }

        if (empty($CFG->enableoutcomes)) {
            $nooutcomes = false;
        } else {
            $nooutcomes = get_user_preferences('grade_report_shownooutcomes');
        }

        // Grab the grade_tree for this course
        $this->gtree = new grade_tree($this->courseid, true, $this->get_pref('aggregationposition'), $this->collapsed, $nooutcomes);

        $this->sortitemid = $sortitemid;

        // base url for sorting by first/last name
        $studentsperpage = $this->get_pref('studentsperpage');
        $perpage = '';
        $curpage = '';

        if (!empty($studentsperpage)) {
            $perpage = '&amp;perpage='.$studentsperpage;
            $curpage = '&amp;page='.$this->page;
        }
        $this->baseurl = 'index.php?id='.$this->courseid. $perpage.$curpage.'&amp;';

        $this->pbarurl = 'index.php?id='.$this->courseid.$perpage.'&amp;';

        // Setup groups if requested
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

        if (!has_capability('moodle/grade:override', $this->context)) {
            return false;
        }

        // always initialize all arrays
        $queue = array();
        foreach ($data as $varname => $postedvalue) {

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

            $oldvalue = $data->{'old'.$varname};

            // was change requested?
            if ($oldvalue == $postedvalue) {
                continue;
            }

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
                        $finalgrade = $postedvalue;
                    }
                } else {
                    $finalgrade = unformat_float($postedvalue);
                }

            } else if ($data_type == 'feedback') {
                $finalgrade = false;
                $trimmed = trim($postedvalue);
                if (empty($trimmed)) {
                     $feedback = NULL;
                } else {
                     $feedback = stripslashes($postedvalue);
                }
            }

            $grade_item->update_final_grade($userid, $finalgrade, 'gradebook', $note, $feedback);
        }

        return true;
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
                $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
            } else {
                // this is the first sort, i.e. by last name
                if (!isset($SESSION->gradeuserreport->sortitemid)) {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                } else if ($SESSION->gradeuserreport->sortitemid == $this->sortitemid) {
                    // same as last sort
                    if ($SESSION->gradeuserreport->sort == 'ASC') {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                    } else {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                    }
                } else {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
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

            // If lastname or firstname is given as sortitemid, add the other name (firstname or lastname respectively) as second sort param
            $sort2 = '';
            if ($this->sortitemid == 'lastname') {
                $sort2 = ', u.firstname ' . $this->sortorder;
            } elseif ($this->sortitemid == 'firstname') {
                $sort2 = ', u.lastname ' . $this->sortorder;
            }

            $this->users = get_role_users($this->gradebookroles, $this->context, false,
                                'u.id, u.firstname, u.lastname', 'u.'.$this->sortitemid .' '. $this->sortorder . $sort2,
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
     * Fetches and returns a count of all the users that will be shown on this page.
     * @param bool $groups Whether to apply groupsql
     * @return int Count of users
     */
    function get_numusers($groups=true) {
        global $CFG;

        $countsql = "SELECT COUNT(DISTINCT u.id)
                    FROM {$CFG->prefix}grade_grades g RIGHT OUTER JOIN
                         {$CFG->prefix}user u ON (u.id = g.userid AND g.itemid = $this->sortitemid)
                         LEFT JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid ";
        if ($groups) {
            $countsql .= $this->groupsql;
        }
        $countsql .= " WHERE ra.roleid in ($this->gradebookroles) ";
        if ($groups) {
            $countsql .= $this->groupwheresql;
        }
        $countsql .= " AND ra.contextid ".get_related_contexts_string($this->context);
        return count_records_sql($countsql);
    }

    /**
     * we supply the userids in this query, and get all the grades
     * pulls out all the grades, this does not need to worry about paging
     */
    function load_final_grades() {
        global $CFG;

        // please note that we must fetch all grade_grades fields if we want to contruct grade_grade object from it!
        $sql = "SELECT g.*, gt.feedback, gt.feedbackformat, gi.grademin, gi.grademax
                  FROM {$CFG->prefix}grade_items gi,
                       {$CFG->prefix}grade_grades g
                       LEFT JOIN {$CFG->prefix}grade_grades_text gt ON g.id = gt.gradeid
                 WHERE g.itemid = gi.id AND gi.courseid = $this->courseid $this->userselect";

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
        global $CFG, $USER;

        $html = '<div id="grade-report-toggles">';
        if ($USER->gradeediting[$this->courseid]) {
            if (has_capability('moodle/grade:manage', $this->context) or has_capability('moodle/grade:hide', $this->context)) {
                $html .= $this->print_toggle('eyecons', true);
            }
            if (has_capability('moodle/grade:manage', $this->context)
             or has_capability('moodle/grade:lock', $this->context)
             or has_capability('moodle/grade:unlock', $this->context)) {
                $html .= $this->print_toggle('locks', true);
            }
            if (has_capability('moodle/grade:manage', $this->context)) {
                $html .= $this->print_toggle('calculations', true);
            }
        }

        $html .= $this->print_toggle('averages', true);

        if (has_capability('moodle/grade:viewall', $this->context)
         and has_capability('moodle/site:accessallgroups', $this->context)
         and $course_has_groups = true) { // TODO replace that last condition with proper check
            $html .= $this->print_toggle('groups', true);
        }

        $html .= $this->print_toggle('ranges', true);
        if (!empty($CFG->enableoutcomes)) {
            $html .= $this->print_toggle('nooutcomes', true);
        }
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

        $icons = array('eyecons' => 't/hide.gif',
                       'calculations' => 't/calc.gif',
                       'locks' => 't/lock.gif',
                       'averages' => 't/sigma.gif',
                       'nooutcomes' => 't/outcomes.gif');

        $pref_name = 'grade_report_show' . $type;

        if (array_key_exists($pref_name, $CFG)) {
            $show_pref = get_user_preferences($pref_name, $CFG->$pref_name);
        } else {
            $show_pref = get_user_preferences($pref_name);
        }

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
            $image_name = "t/$type.gif";
        }

        $string = ${'str' . $show_hide};

        $img = '<img src="'.$CFG->pixpath.'/'.$image_name.'" class="iconsmall" alt="'
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

        $strsortasc   = $this->get_lang_string('sortasc', 'grades');
        $strsortdesc  = $this->get_lang_string('sortdesc', 'grades');
        $strfirstname = $this->get_lang_string('firstname');
        $strlastname  = $this->get_lang_string('lastname');

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

        $columns_to_unset = array();


        foreach ($this->gtree->levels as $key=>$row) {
            $columncount = 0;
            if ($key == 0) {
                // do not display course grade category
                // continue;
            }

            $headerhtml .= '<tr class="heading r'.$this->rowcount++.'">';

            if ($key == $numrows - 1) {
                $headerhtml .= '<th class="header c'.$columncount++.' user" scope="col"><a href="'.$this->baseurl.'&amp;sortitemid=firstname">'
                            . $strfirstname . '</a> ' //TODO: localize
                            . $firstarrow. '/ <a href="'.$this->baseurl.'&amp;sortitemid=lastname">' . $strlastname . '</a>'. $lastarrow .'</th>';
            } else {
                $headerhtml .= '<td class="cell c'.$columncount++.' topleft">&nbsp;</td>';
            }

            foreach ($row as $columnkey => $element) {
                $sort_link = '';
                if (isset($element['object']->id)) {
                    $sort_link = $this->baseurl.'&amp;sortitemid=' . $element['object']->id;
                }

                $eid    = $element['eid'];
                $object = $element['object'];
                $type   = $element['type'];
                $categorystate = @$element['categorystate'];
                $itemmodule = null;
                $iteminstance = null;

                $columnclass = 'c' . $columncount++;
                if (!empty($element['colspan'])) {
                    $colspan = 'colspan="'.$element['colspan'].'"';
                    $columnclass = '';
                } else {
                    $colspan = '';
                }

                if (!empty($element['depth'])) {
                    $catlevel = ' catlevel'.$element['depth'];
                } else {
                    $catlevel = '';
                }

// Element is a filler
                if ($type == 'filler' or $type == 'fillerfirst' or $type == 'fillerlast') {
                    $headerhtml .= '<th class="'.$columnclass.' '.$type.$catlevel.'" '.$colspan.' scope="col">&nbsp;</th>';
                }
// Element is a category
                else if ($type == 'category') {
                    $headerhtml .= '<th class="header '. $columnclass.' category'.$catlevel.'" '.$colspan.' scope="col">'
                                . $element['object']->get_name();
                    $headerhtml .= $this->get_collapsing_icon($element);

                    // Print icons
                    if ($USER->gradeediting[$this->courseid]) {
                        $headerhtml .= $this->get_icons($element);
                    }

                    $headerhtml .= '</th>';
                }
// Element is a grade_item
                else {
                    $itemmodule = $element['object']->itemmodule;
                    $iteminstance = $element['object']->iteminstance;

                    if ($element['object']->id == $this->sortitemid) {
                        if ($this->sortorder == 'ASC') {
                            $arrow = $this->get_sort_arrow('up', $sort_link);
                        } else {
                            $arrow = $this->get_sort_arrow('down', $sort_link);
                        }
                    } else {
                        $arrow = $this->get_sort_arrow('move', $sort_link);
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
                        $icon = '<img src="'.$CFG->pixpath.'/t/edit.gif" class="icon" alt="'
                                .$this->get_lang_string('manualgrade', 'grades') .'"/>';
                    }

                    $headerlink = $this->get_module_link($element['object']->get_name(), $itemmodule, $iteminstance);
                    $headerhtml .= '<th class="header '.$columnclass.' '.$type.$catlevel.$dimmed.'" scope="col">'. $headerlink . $arrow;
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
        $strgrade = $this->get_lang_string('grade');
        $gradetabindex = 1;
        $showuserimage = $this->get_pref('showuserimage');
        $numusers = count($this->users);

        // Preload scale objects for items with a scaleid
        $scales_list = '';
        $tabindices = array();
        foreach ($this->items as $item) {
            if (!empty($item->scaleid)) {
                $scales_list .= "$item->scaleid,";
            }
            $tabindices[$item->id]['grade'] = $gradetabindex;
            $tabindices[$item->id]['feedback'] = $gradetabindex + $numusers;
            $gradetabindex += $numusers * 2;
        }
        $scales_array = array();

        if (!empty($scales_list)) {
            $scales_list = substr($scales_list, 0, -1);
            $scales_array = get_records_list('scale', 'id', $scales_list);
        }
        
        $canviewhidden = has_capability('moodle/grade:viewhidden', get_context_instance(CONTEXT_COURSE, $this->course->id));

        foreach ($this->users as $userid => $user) {
            $columncount = 0;
            // Student name and link
            $user_pic = null;
            if ($showuserimage) {
                $user_pic = '<div class="userpic">' . print_user_picture($user->id, $this->courseid, true, 0, true) . '</div>';
            }

            $studentshtml .= '<tr class="r'.$this->rowcount++.'"><th class="header c'.$columncount++.' user" scope="row">' . $user_pic
                          . '<a href="' . $CFG->wwwroot . '/user/view.php?id='
                          . $user->id . '">' . fullname($user) . '</a></th>';

            foreach ($this->items as $itemid=>$item) {
                // Get the decimal points preference for this item
                $decimalpoints = $this->get_pref('decimalpoints', $item->id);

                if (isset($this->finalgrades[$userid][$item->id])) {
                    $gradeval = $this->finalgrades[$userid][$item->id]->finalgrade;
                    $grade = new grade_grade($this->finalgrades[$userid][$item->id], false);
                    $grade->feedback = stripslashes_safe($this->finalgrades[$userid][$item->id]->feedback);
                    $grade->feedbackformat = $this->finalgrades[$userid][$item->id]->feedbackformat;

                } else {
                    $gradeval = null;
                    $grade = new grade_grade(array('userid'=>$userid, 'itemid'=>$item->id), false);
                    $grade->feedback = '';
                }

                // MDL-11274
                // Hide grades in the grader report if the current grader doesn't have 'moodle/grade:viewhidden'
                if ($grade->is_hidden() && !$canviewhidden) {
                    if (isset($grade->finalgrade)) {
                        $studentshtml .= '<td class="cell c'.$columncount++.'">'.userdate($grade->timecreated,get_string('strftimedatetimeshort')).'</td>';                  } else {
                        $studentshtml .= '<td class="cell c'.$columncount++.'">-</td>';
                    }
                    continue; 
                }

                $grade->courseid = $this->courseid;
                $grade->grade_item =& $this->items[$itemid]; // this speedsup is_hidden() and other grade_grade methods

                // emulate grade element
                $eid = $this->gtree->get_grade_eid($grade);
                $element = array('eid'=>$eid, 'object'=>$grade, 'type'=>'grade');

                if ($grade->is_overridden()) {
                    $studentshtml .= '<td class="overridden cell c'.$columncount++.'">';
                } else {
                    $studentshtml .= '<td class="cell c'.$columncount++.'">';
                }

                if ($grade->is_excluded()) {
                    $studentshtml .= get_string('excluded', 'grades'); // TODO: improve visual representation of excluded grades
                }

                // Do not show any icons if no grade (no record in DB to match)
                if (!$item->needsupdate and $USER->gradeediting[$this->courseid]) {
                    $studentshtml .= $this->get_icons($element);
                }

                // if in editting mode, we need to print either a text box
                // or a drop down (for scales)
                // grades in item of type grade category or course are not directly editable
                if ($item->needsupdate) {
                    $studentshtml .= '<span class="gradingerror">'.get_string('error').'</span>';

                } else if ($USER->gradeediting[$this->courseid]) {
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
                            $oldval = empty($gradeval) ? -1 : $gradeval;
                            if (empty($item->outcomeid)) {
                                $nogradestr = $this->get_lang_string('nograde');
                            } else {
                                $nogradestr = $this->get_lang_string('nooutcome', 'grades');
                            }
                            $studentshtml .= '<input type="hidden" name="oldgrade_'.$userid.'_'
                                          .$item->id.'" value="'.$oldval.'"/>';
                            $studentshtml .= choose_from_menu($scaleopt, 'grade_'.$userid.'_'.$item->id,
                                                              $gradeval, $nogradestr, '', '-1',
                                                              true, false, $tabindices[$item->id]['grade']);
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
                            $value = format_float($gradeval, $decimalpoints);
                            $studentshtml .= '<input type="hidden" name="oldgrade_'.$userid.'_'.$item->id.'" value="'.$value.'" />';
                            $studentshtml .= '<input size="6" tabindex="' . $tabindices[$item->id]['grade']
                                          . '" type="text" title="'. $strgrade .'" name="grade_'
                                          .$userid.'_' .$item->id.'" value="'.$value.'" />';
                        } else {
                            $studentshtml .= format_float($gradeval, $decimalpoints);
                        }
                    }


                    // If quickfeedback is on, print an input element
                    if ($this->get_pref('quickfeedback') and $grade->is_editable()) {
                        if ($this->get_pref('quickgrading')) {
                            $studentshtml .= '<br />';
                        }
                        $studentshtml .= '<input type="hidden" name="oldfeedback_'
                                      .$userid.'_'.$item->id.'" value="' . s($grade->feedback) . '" />';
                        $studentshtml .= '<input class="quickfeedback" tabindex="' . $tabindices[$item->id]['feedback']
                                      . '" size="6" title="' . $strfeedback . '" type="text" name="feedback_'
                                      .$userid.'_'.$item->id.'" value="' . s($grade->feedback) . '" />';
                    }

                } else {
                    // Percentage format if specified by user (check each item for a set preference)
                    $gradedisplaytype = $this->get_pref('gradedisplaytype', $item->id);

                    $percentsign = '';
                    $grademin = $item->grademin;
                    $grademax = $item->grademax;

                    if ($gradedisplaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE) {
                        if (!is_null($gradeval)) {
                            $gradeval = grade_to_percentage($gradeval, $grademin, $grademax);
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

                    if ($item->needsupdate) {
                        $studentshtml .= '<span class="gradingerror">'.get_string('error').'</span>';

                    } else if ($gradedisplaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER) {
                        $letters = grade_report::get_grade_letters();
                        if (!is_null($gradeval)) {
                            $studentshtml .= grade_grade::get_letter($letters, $gradeval, $grademin, $grademax);
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
                            $studentshtml .=  format_float($gradeval, $decimalpoints). $percentsign;
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
     * Builds and return the HTML row of column totals.
     * @param  bool $grouponly Whether to return only group averages or all averages.
     * @return string HTML
     */
    function get_avghtml($grouponly=false) {
        global $CFG, $USER;

        $averagesdisplaytype   = $this->get_pref('averagesdisplaytype');
        $averagesdecimalpoints = $this->get_pref('averagesdecimalpoints');
        $meanselection         = $this->get_pref('meanselection');
        $shownumberofgrades    = $this->get_pref('shownumberofgrades');

        $avghtml = '';
        $avgcssclass = 'avg';

        if ($grouponly) {
            $straverage = get_string('groupavg', 'grades');
            $showaverages = $this->currentgroup && $this->get_pref('showgroups');
            $groupsql = $this->groupsql;
            $groupwheresql = $this->groupwheresql;
            $avgcssclass = 'groupavg';
        } else {
            $straverage = get_string('overallaverage', 'grades');
            $showaverages = $this->get_pref('showaverages');
            $groupsql = null;
            $groupwheresql = null;
        }

        $totalcount = $this->get_numusers($grouponly);

        if ($showaverages) {

            // the first join on user is needed for groupsql
            $SQL = "SELECT g.itemid, SUM(g.finalgrade) as sum
                FROM {$CFG->prefix}grade_items gi LEFT JOIN
                     {$CFG->prefix}grade_grades g ON gi.id = g.itemid LEFT JOIN
                     {$CFG->prefix}user u ON g.userid = u.id
                     $groupsql
                WHERE gi.courseid = $this->courseid
                     $groupwheresql
                     AND g.userid IN (
                        SELECT DISTINCT(u.id)
                        FROM {$CFG->prefix}user u LEFT JOIN
                             {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                        WHERE ra.roleid in ($this->gradebookroles)
                             AND ra.contextid ".get_related_contexts_string($this->context)."
                     )
                GROUP BY g.itemid";
            $sum_array = array();
            if ($sums = get_records_sql($SQL)) {
                foreach ($sums as $itemid => $csum) {
                    $sum_array[$itemid] = $csum->sum;
                }
            }

            $avghtml = '<tr class="' . $avgcssclass . ' r'.$this->rowcount++.'"><th class="header c0" scope="row">'.$straverage.'</th>';

            $columncount=1;
            foreach ($this->items as $item) {
                if (empty($sum_array[$item->id])) {
                    $sum_array[$item->id] = 0;
                }
                if ($grouponly) {
                    $groupsql = $this->groupsql;
                    $groupwheresql = $this->groupwheresql;
                } else {
                    $groupsql = '';
                    $groupwheresql = '';
                }
                // MDL-10875 Empty grades must be evaluated as grademin, NOT always 0
                // This query returns a count of ungraded grades (NULL finalgrade OR no matching record in grade_grades table)
                $SQL = "SELECT COUNT(*) AS count FROM {$CFG->prefix}user u
                         WHERE u.id NOT IN
                           (SELECT userid FROM {$CFG->prefix}grade_grades
                             WHERE itemid = $item->id
                               AND finalgrade IS NOT NULL
                           )
                           AND u.id IN (
                             SELECT DISTINCT(u.id)
                              FROM {$CFG->prefix}user u LEFT JOIN
                                   {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                                   $groupsql
                             WHERE ra.roleid in ($this->gradebookroles)
                               AND ra.contextid ".get_related_contexts_string($this->context)."
                               $groupwheresql
                           )";

                $ungraded_count = get_field_sql($SQL);

                if ($meanselection == GRADE_REPORT_MEAN_GRADED) {
                    $mean_count = $totalcount - $ungraded_count;
                } else { // Bump up the sum by the number of ungraded items * grademin
                    if (isset($sum_array[$item->id])) {
                        $sum_array[$item->id] += $ungraded_count * $item->grademin;
                    }
                    $mean_count = $totalcount;
                }

                $decimalpoints = $this->get_pref('decimalpoints', $item->id);
                // Determine which display type to use for this average
                $gradedisplaytype = $this->get_pref('gradedisplaytype', $item->id);
                if ($USER->gradeediting[$this->courseid]) {
                    $displaytype = GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL;
                } elseif ($averagesdisplaytype == GRADE_REPORT_PREFERENCE_INHERIT) { // Inherit specific column or general preference
                    $displaytype = $gradedisplaytype;
                } else { // General preference overrides specific column display type
                    $displaytype = $averagesdisplaytype;
                }

                if ($averagesdecimalpoints != GRADE_REPORT_PREFERENCE_INHERIT) {
                    $decimalpoints = $averagesdecimalpoints;
                }

                if (!isset($sum_array[$item->id]) || $mean_count == 0) {
                    $avghtml .= '<td class="cell c' . $columncount++.'">-</td>';
                } else {
                    $sum = $sum_array[$item->id];

                    if ($item->scaleid) {
                        if ($grouponly) {
                            $finalsum = $sum_array[$item->id];
                            $finalavg = $finalsum/$mean_count;
                        } else {
                            $finalavg = $sum/$mean_count;
                        }
                        $scaleval = round($finalavg);
                        $scale_object = new grade_scale(array('id' => $item->scaleid), false);
                        $gradehtml = $scale_object->get_nearest_item($scaleval);
                        $rawvalue = $scaleval;
                    } else {
                        $gradeval = format_float($sum/$mean_count, $decimalpoints);
                        $gradehtml = $gradeval;
                        $rawvalue = $gradeval;
                    }

                    if ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE) {
                        $gradeval = grade_to_percentage($rawvalue, $item->grademin, $item->grademax);
                        $gradehtml = number_format(format_float($gradeval, $decimalpoints), $decimalpoints) . '%';
                    } elseif ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER) {
                        $letters = grade_report::get_grade_letters();
                        $gradehtml = grade_grade::get_letter($letters, $gradeval, $item->grademin, $item->grademax);
                    }

                    $numberofgrades = '';

                    if ($shownumberofgrades) {
                        $numberofgrades = " ($mean_count)";
                    }

                    $avghtml .= '<td class="cell c' . $columncount++.'">'.$gradehtml.$numberofgrades.'</td>';
                }
            }
            $avghtml .= '</tr>';
        }
        return $avghtml;
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
            $rangesdecimalpoints = $this->get_pref('rangesdecimalpoints');
            $scalehtml = '<tr class="r'.$this->rowcount++.'">'
                       . '<th class="header c0 range" scope="row">'.$this->get_lang_string('range','grades').'</th>';

            $columncount = 1;
            foreach ($this->items as $item) {

                $decimalpoints = $this->get_pref('decimalpoints', $item->id);
                // Determine which display type to use for this range
                $gradedisplaytype = $this->get_pref('gradedisplaytype', $item->id);

                if ($USER->gradeediting[$this->courseid]) {
                    $displaytype = GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL;
                } elseif ($rangesdisplaytype == GRADE_REPORT_PREFERENCE_INHERIT) { // Inherit specific column or general preference
                    $displaytype = $gradedisplaytype;
                } else { // General preference overrides specific column display type
                    $displaytype = $rangesdisplaytype;
                }

                if ($rangesdecimalpoints != GRADE_REPORT_PREFERENCE_INHERIT) {
                    $decimalpoints = $rangesdecimalpoints;
                }

                if ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_REAL) {
                    $grademin = format_float($item->grademin, $decimalpoints);
                    $grademax = format_float($item->grademax, $decimalpoints);
                } elseif ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE) {
                    $grademin = 0;
                    $grademax = 100;
                } elseif ($displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER) {
                    $letters = grade_report::get_grade_letters();
                    $grademin = end($letters);
                    $grademax = reset($letters);
                }

                $scalehtml .= '<th class="header c'.$columncount++.' range">'. $grademin.'-'. $grademax.'</th>';
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
     * @return string HTML
     */
    function get_icons($element) {
        global $CFG, $USER;

        if (!$USER->gradeediting[$this->courseid]) {
            return '<div class="grade_icons" />';
        }

        // Init all icons
        $edit_icon             = $this->gtree->get_edit_icon($element, $this->gpr);
        $edit_calculation_icon = '';
        $show_hide_icon        = '';
        $lock_unlock_icon      = '';

        if ($this->get_pref('showcalculations')) {
            $edit_calculation_icon = $this->gtree->get_calculation_icon($element, $this->gpr);
        }

        if ($this->get_pref('showeyecons')) {
           $show_hide_icon = $this->gtree->get_hiding_icon($element, $this->gpr);
        }

        if ($this->get_pref('showlocks')) {
            $lock_unlock_icon = $this->gtree->get_locking_icon($element, $this->gpr);
        }

        return '<div class="grade_icons">'.$edit_icon.$edit_calculation_icon.$show_hide_icon.$lock_unlock_icon.'</div>';
    }

    /**
     * Given a category element returns collapsing +/- icon if available
     * @param object $object
     * @return string HTML
     */
    function get_collapsing_icon($element) {
        global $CFG;

        $contract_expand_icon = '';
        // If object is a category, display expand/contract icon
        if ($element['type'] == 'category') {
            // Load language strings
            $strswitch_minus = $this->get_lang_string('aggregatesonly', 'grades');
            $strswitch_plus  = $this->get_lang_string('gradesonly', 'grades');
            $strswitch_whole = $this->get_lang_string('fullmode', 'grades');

            $expand_contract = 'switch_minus'; // Default: expanded
            // $this->get_pref('aggregationview', $element['object']->id) == GRADE_REPORT_AGGREGATION_VIEW_COMPACT

            if (in_array($element['object']->id, $this->collapsed['aggregatesonly'])) {
                $expand_contract = 'switch_plus';
            } elseif (in_array($element['object']->id, $this->collapsed['gradesonly'])) {
                $expand_contract = 'switch_whole';
            }
            $url = $this->gpr->get_return_url(null, array('target'=>$element['eid'], 'action'=>$expand_contract, 'sesskey'=>sesskey()));
            $contract_expand_icon = '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/'.$expand_contract.'.gif" class="iconsmall" alt="'
                                    .${'str'.$expand_contract}.'" title="'.${'str'.$expand_contract}.'" /></a>';
        }
        return $contract_expand_icon;
    }

    /**
     * Processes a single action against a category, grade_item or grade.
     * @param string $target eid ({type}{id}, e.g. c4 for category4)
     * @param string $action Which action to take (edit, delete etc...)
     * @return
     */
    function process_action($target, $action) {
        // TODO: this code should be in some grade_tree static method
        $targettype = substr($target, 0, 1);
        $targetid = substr($target, 1);
        // TODO: end

        if ($collapsed = get_user_preferences('grade_report_grader_collapsed_categories')) {
            $collapsed = unserialize($collapsed);
        } else {
            $collapsed = array('aggregatesonly' => array(), 'gradesonly' => array());
        }

        switch ($action) {
            case 'switch_minus': // Add category to array of aggregatesonly
                if (!in_array($targetid, $collapsed['aggregatesonly'])) {
                    $collapsed['aggregatesonly'][] = $targetid;
                    set_user_preference('grade_report_grader_collapsed_categories', serialize($collapsed));
                }
                break;

            case 'switch_plus': // Remove category from array of aggregatesonly, and add it to array of gradesonly
                $key = array_search($targetid, $collapsed['aggregatesonly']);
                if ($key !== false) {
                    unset($collapsed['aggregatesonly'][$key]);
                }
                if (!in_array($targetid, $collapsed['gradesonly'])) {
                    $collapsed['gradesonly'][] = $targetid;
                }
                set_user_preference('grade_report_grader_collapsed_categories', serialize($collapsed));
                break;
            case 'switch_whole': // Remove the category from the array of collapsed cats
                $key = array_search($targetid, $collapsed['gradesonly']);
                if ($key !== false) {
                    unset($collapsed['gradesonly'][$key]);
                    set_user_preference('grade_report_grader_collapsed_categories', serialize($collapsed));
                }

                break;
            default:
                break;
        }

        return true;
    }
}
?>
