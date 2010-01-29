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
     * @var array $grades
     */
    var $grades;

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
     * Capability check caching
     * */
    var $canviewhidden;

    var $preferences_page=false;

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

        $this->canviewhidden = has_capability('moodle/grade:viewhidden', get_context_instance(CONTEXT_COURSE, $this->course->id));

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

        // if user report preference set or site report setting set use it, otherwise use course or site setting
        $switch = $this->get_pref('aggregationposition');
        if ($switch == '') {
            $switch = grade_get_setting($this->courseid, 'aggregationposition', $CFG->grade_aggregationposition);
        }

        // Grab the grade_tree for this course
        $this->gtree = new grade_tree($this->courseid, true, $switch, $this->collapsed, $nooutcomes);

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

        $this->setup_groups();

        $this->setup_sortitemid();
    }

    /**
     * Processes the data sent by the form (grades and feedbacks).
     * Caller is reposible for all access control checks
     * @param array $data form submission (with magic quotes)
     * @return array empty array if success, array of warnings if something fails.
     */
    function process_data($data) {
        $warnings = array();

        $separategroups = false;
        $mygroups       = array();
        if ($this->groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $this->context)) {
            $separategroups = true;
            $mygroups = groups_get_user_groups($this->course->id);
            $mygroups = $mygroups[0]; // ignore groupings
            // reorder the groups fro better perf bellow
            $current = array_search($this->currentgroup, $mygroups);
            if ($current !== false) {
                unset($mygroups[$current]);
                array_unshift($mygroups, $this->currentgroup);
            }
        }

        // always initialize all arrays
        $queue = array();
        foreach ($data as $varname => $postedvalue) {

            $needsupdate = false;

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
            if ($oldvalue == $postedvalue) { // string comparison
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

                $errorstr = '';
                // Warn if the grade is out of bounds.
                if (is_null($finalgrade)) {
                    // ok
                } else {
                    $bounded = $grade_item->bounded_grade($finalgrade);
                    if ($bounded > $finalgrade) {
                        $errorstr = 'lessthanmin';
                    } else if ($bounded < $finalgrade) {
                        $errorstr = 'morethanmax';
                    }
                }
                if ($errorstr) {
                    $user = get_record('user', 'id', $userid, '', '', '', '', 'id, firstname, lastname');
                    $gradestr = new object();
                    $gradestr->username = fullname($user);
                    $gradestr->itemname = $grade_item->get_name();
                    $warnings[] = get_string($errorstr, 'grades', $gradestr);
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

            // group access control
            if ($separategroups) {
                // note: we can not use $this->currentgroup because it would fail badly
                //       when having two browser windows each with different group
                $sharinggroup = false;
                foreach($mygroups as $groupid) {
                    if (groups_is_member($groupid, $userid)) {
                        $sharinggroup = true;
                        break;
                    }
                }
                if (!$sharinggroup) {
                    // either group membership changed or somebedy is hacking grades of other group
                    $warnings[] = get_string('errorsavegrade', 'grades');
                    continue;
                }
            }

            $grade_item->update_final_grade($userid, $finalgrade, 'gradebook', $feedback, FORMAT_MOODLE);
        }

        return $warnings;
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
                if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                } else {
                    $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                }
            } else {
                // this is the first sort, i.e. by last name
                if (!isset($SESSION->gradeuserreport->sortitemid)) {
                    if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                    } else {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                    }
                } else if ($SESSION->gradeuserreport->sortitemid == $this->sortitemid) {
                    // same as last sort
                    if ($SESSION->gradeuserreport->sort == 'ASC') {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                    } else {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                    }
                } else {
                    if ($this->sortitemid == 'firstname' || $this->sortitemid == 'lastname') {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'ASC';
                    } else {
                        $this->sortorder = $SESSION->gradeuserreport->sort = 'DESC';
                    }
                }
            }
            $SESSION->gradeuserreport->sortitemid = $this->sortitemid;
        } else {
            // not requesting sort, use last setting (for paging)

            if (isset($SESSION->gradeuserreport->sortitemid)) {
                $this->sortitemid = $SESSION->gradeuserreport->sortitemid;
            }else{
                $this->sortitemid = 'lastname';
            }

            if (isset($SESSION->gradeuserreport->sort)) {
                $this->sortorder = $SESSION->gradeuserreport->sort;
            } else {
                $this->sortorder = 'ASC';
            }
        }
    }

    /**
     * pulls out the userids of the users to be display, and sorts them
     */
    function load_users() {
        global $CFG;

        if (is_numeric($this->sortitemid)) {
            // the MAX() magic is required in order to please PG
            $sort = "MAX(g.finalgrade) $this->sortorder";

            $sql = "SELECT u.id, u.firstname, u.lastname, u.imagealt, u.picture, u.idnumber
                      FROM {$CFG->prefix}user u
                           JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                           $this->groupsql
                           LEFT JOIN {$CFG->prefix}grade_grades g ON (g.userid = u.id AND g.itemid = $this->sortitemid)
                     WHERE ra.roleid in ($this->gradebookroles) AND u.deleted = 0
                           $this->groupwheresql
                           AND ra.contextid ".get_related_contexts_string($this->context)."
                  GROUP BY u.id, u.firstname, u.lastname, u.imagealt, u.picture, u.idnumber
                  ORDER BY $sort";

        } else {
            switch($this->sortitemid) {
                case 'lastname':
                    $sort = "u.lastname $this->sortorder, u.firstname $this->sortorder"; break;
                case 'firstname':
                    $sort = "u.firstname $this->sortorder, u.lastname $this->sortorder"; break;
                case 'idnumber':
                default:
                    $sort = "u.idnumber $this->sortorder"; break;
            }

            $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.imagealt, u.picture, u.idnumber
                      FROM {$CFG->prefix}user u
                           JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                           $this->groupsql
                     WHERE ra.roleid in ($this->gradebookroles)
                           $this->groupwheresql
                           AND ra.contextid ".get_related_contexts_string($this->context)."
                  ORDER BY $sort";
        }


        $this->users = get_records_sql($sql, $this->get_pref('studentsperpage') * $this->page,
                            $this->get_pref('studentsperpage'));

        if (empty($this->users)) {
            $this->userselect = '';
            $this->users = array();
        } else {
            $this->userselect = 'AND g.userid in ('.implode(',', array_keys($this->users)).')';
        }

        return $this->users;
    }

    /**
     * we supply the userids in this query, and get all the grades
     * pulls out all the grades, this does not need to worry about paging
     */
    function load_final_grades() {
        global $CFG;

        // please note that we must fetch all grade_grades fields if we want to contruct grade_grade object from it!
        $sql = "SELECT g.*
                  FROM {$CFG->prefix}grade_items gi,
                       {$CFG->prefix}grade_grades g
                 WHERE g.itemid = gi.id AND gi.courseid = {$this->courseid} {$this->userselect}";

        $userids = array_keys($this->users);


        if ($grades = get_records_sql($sql)) {
            foreach ($grades as $graderec) {
                if (in_array($graderec->userid, $userids) and array_key_exists($graderec->itemid, $this->gtree->items)) { // some items may not be present!!
                    $this->grades[$graderec->userid][$graderec->itemid] = new grade_grade($graderec, false);
                    $this->grades[$graderec->userid][$graderec->itemid]->grade_item =& $this->gtree->items[$graderec->itemid]; // db caching
                }
            }
        }

        // prefil grades that do not exist yet
        foreach ($userids as $userid) {
            foreach ($this->gtree->items as $itemid=>$unused) {
                if (!isset($this->grades[$userid][$itemid])) {
                    $this->grades[$userid][$itemid] = new grade_grade();
                    $this->grades[$userid][$itemid]->itemid = $itemid;
                    $this->grades[$userid][$itemid]->userid = $userid;
                    $this->grades[$userid][$itemid]->grade_item =& $this->gtree->items[$itemid]; // db caching
                }
            }
        }
    }

    /**
     * Builds and returns a div with on/off toggles.
     * @return string HTML code
     */
    function get_toggles_html() {
        global $CFG, $USER, $COURSE;

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
                $html .= $this->print_toggle('quickfeedback', true);
            }

            if (has_capability('moodle/grade:manage', $this->context)) {
                $html .= $this->print_toggle('calculations', true);
            }
        }

        if ($this->canviewhidden) {
            $html .= $this->print_toggle('averages', true);
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
                       'averages' => 't/mean.gif',
                       'quickfeedback' => 't/feedback.gif',
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
        
        $this->rowcount = 0;
        $fixedstudents = $this->is_fixed_students();

        if (!$fixedstudents) {
            $strsortasc   = $this->get_lang_string('sortasc', 'grades');
            $strsortdesc  = $this->get_lang_string('sortdesc', 'grades');
            $strfirstname = $this->get_lang_string('firstname');
            $strlastname  = $this->get_lang_string('lastname');
            $showuseridnumber = $this->get_pref('showuseridnumber');

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

            if ($fixedstudents) {
                $headerhtml .= '<tr class="heading_name_row">';
            } else {
                $headerhtml .= '<tr class="heading r'.$this->rowcount++.'">';
                if ($key == $numrows - 1) {
                    $headerhtml .= '<th class=" header c'.$columncount++.'" scope="col" colspan="2"><a href="'.$this->baseurl.'&amp;sortitemid=firstname">'
                                . $strfirstname . '</a> '
                                . $firstarrow. '/ <a href="'.$this->baseurl.'&amp;sortitemid=lastname">' . $strlastname . '</a>'. $lastarrow .'</th>';
                    if ($showuseridnumber) {
                        if ('idnumber' == $this->sortitemid) {
                            if ($this->sortorder == 'ASC') {
                                $idnumberarrow = print_arrow('up', $strsortasc, true);
                            } else {
                                $idnumberarrow = print_arrow('down', $strsortdesc, true);
                            }
                        } else {
                            $idnumberarrow = '';
                        }
                        $headerhtml .= '<th class="header  c'.$columncount++.' useridnumber" scope="col"><a href="'.$this->baseurl.'&amp;sortitemid=idnumber">'
                                . get_string('idnumber') . '</a> ' . $idnumberarrow . '</th>';
                    }
                 } else {
                    $colspan = 'colspan="2" ';
                    if ($showuseridnumber) {
                        $colspan = 'colspan="3" ';
                    }

                    $headerhtml .= '<td '.$colspan.'class="cell c'.$columncount++.' topleft">&nbsp;</td>';

                    if ($showuseridnumber) {
                        $columncount++;
                    }
                }
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
                    //MDL-21088 - IE 7 ignores nowraps on td or th so we put this in a span with a nowrap on it.
                    $headerhtml .= '<th class=" '. $columnclass.' category'.$catlevel.'" '.$colspan.' scope="col"><span>'
                                . shorten_text($element['object']->get_name());
                    $headerhtml .= $this->get_collapsing_icon($element);

                    // Print icons
                    if ($USER->gradeediting[$this->courseid]) {
                        $headerhtml .= $this->get_icons($element);
                    }

                    $headerhtml .= '</span></th>';
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

                    $hidden = '';
                    if ($element['object']->is_hidden()) {
                        $hidden = ' hidden ';
                    }

                    $headerlink = $this->gtree->get_element_header($element, true, $this->get_pref('showactivityicons'), false);
                    //MDL-21088 - IE 7 ignores nowraps on tds or ths so we this in a span with a nowrap on it.
                    $headerhtml .= '<th class=" '.$columnclass.' '.$type.$catlevel.$hidden.'" scope="col" onclick="set_col(this.cellIndex)"><span>'
                                .shorten_text($headerlink) . $arrow;
                    $headerhtml .= '</span></th>';
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
        $strfeedback  = $this->get_lang_string("feedback");
        $strgrade     = $this->get_lang_string('grade');
        $gradetabindex = 1;
        $numusers      = count($this->users);
        $showuserimage = $this->get_pref('showuserimage');
        $showuseridnumber = $this->get_pref('showuseridnumber');
        $fixedstudents = $this->is_fixed_students();

        // Preload scale objects for items with a scaleid
        $scales_list = '';
        $tabindices = array();

        foreach ($this->gtree->items as $item) {
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

        $row_classes = array(' even ', ' odd ');

        foreach ($this->users as $userid => $user) {

            if ($this->canviewhidden) {
                $altered = array();
                $unknown = array();
            } else {
                $hiding_affected = grade_grade::get_hiding_affected($this->grades[$userid], $this->gtree->items);
                $altered = $hiding_affected['altered'];
                $unknown = $hiding_affected['unknown'];
                unset($hiding_affected);
            }

            $columncount = 0;
            if ($fixedstudents) {
                $studentshtml .= '<tr class="r'.$this->rowcount++ . $row_classes[$this->rowcount % 2] . '">';
            } else {
                // Student name and link
                $user_pic = null;
                if ($showuserimage) {
                    $user_pic = '<div class="userpic">' . print_user_picture($user, $this->courseid, null, 0, true) . '</div>';
                }

                //we're either going to add a th or a colspan to keep things aligned
                $userreportcell = '';
                $userreportcellcolspan = '';
                if (has_capability('gradereport/'.$CFG->grade_profilereport.':view', $this->context)) {
                    $a->user = fullname($user);
                    $strgradesforuser = get_string('gradesforuser', 'grades', $a);
                    $userreportcell = '<th class="header userreport"><a href="'.$CFG->wwwroot.'/grade/report/'.$CFG->grade_profilereport.'/index.php?id='.$this->courseid.'&amp;userid='.$user->id.'">'
                                    .'<img src="'.$CFG->pixpath.'/t/grades.gif" alt="'.$strgradesforuser.'" title="'.$strgradesforuser.'" /></a></th>';
                }
                else {
                    $userreportcellcolspan = 'colspan=2';
                }

                $studentshtml .= '<tr class="r'.$this->rowcount++ . $row_classes[$this->rowcount % 2] . '">'
                              .'<th class="c'.$columncount++.' user" scope="row" onclick="set_row(this.parentNode.rowIndex);" '.$userreportcellcolspan.' >'.$user_pic
                              .'<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$this->course->id.'">'
                              .fullname($user)."</a></th>$userreportcell\n";

                if ($showuseridnumber) {
                    $studentshtml .= '<th class="c'.$columncount++.' useridnumber" onclick="set_row(this.parentNode.rowIndex);">'.
                            $user->idnumber.'</th>';
                }

            }

            foreach ($this->gtree->items as $itemid=>$unused) {
                $item =& $this->gtree->items[$itemid];
                $grade = $this->grades[$userid][$item->id];

                // Get the decimal points preference for this item
                $decimalpoints = $item->get_decimals();

                if (in_array($itemid, $unknown)) {
                    $gradeval = null;
                } else if (array_key_exists($itemid, $altered)) {
                    $gradeval = $altered[$itemid];
                } else {
                    $gradeval = $grade->finalgrade;
                }

                // MDL-11274
                // Hide grades in the grader report if the current grader doesn't have 'moodle/grade:viewhidden'
                if (!$this->canviewhidden and $grade->is_hidden()) {
                    if (!empty($CFG->grade_hiddenasdate) and $grade->get_datesubmitted() and !$item->is_category_item() and !$item->is_course_item()) {
                        // the problem here is that we do not have the time when grade value was modified, 'timemodified' is general modification date for grade_grades records
                        $studentshtml .= '<td class="cell c'.$columncount++.'"><span class="datesubmitted">'.userdate($grade->get_datesubmitted(),get_string('strftimedatetimeshort')).'</span></td>';
                    } else {
                        $studentshtml .= '<td class="cell c'.$columncount++.'">-</td>';
                    }
                    continue;
                }

                // emulate grade element
                $eid = $this->gtree->get_grade_eid($grade);
                $element = array('eid'=>$eid, 'object'=>$grade, 'type'=>'grade');

                $cellclasses = 'grade cell c'.$columncount++;
                if ($item->is_category_item()) {
                    $cellclasses .= ' cat';
                }
                if ($item->is_course_item()) {
                    $cellclasses .= ' course';
                }
                if ($grade->is_overridden()) {
                    $cellclasses .= ' overridden';
                }

                if ($grade->is_excluded()) {
                    // $cellclasses .= ' excluded';
                }

                $grade_title = '<div class="fullname">'.fullname($user).'</div>';
                $grade_title .= '<div class="itemname">'.$item->get_name(true).'</div>';

                if (!empty($grade->feedback) && !$USER->gradeediting[$this->courseid]) {
                    $grade_title .= '<div class="feedback">'
                                 .wordwrap(trim(format_string($grade->feedback, $grade->feedbackformat)), 34, '<br/ >') . '</div>';
                } else {

                }

                $studentshtml .= '<td class="'.$cellclasses.'" title="'.s($grade_title).'">';

                if ($grade->is_excluded()) {
                    $studentshtml .= '<span class="excludedfloater">'.get_string('excluded', 'grades') . '</span> ';
                }

                // Do not show any icons if no grade (no record in DB to match)
                if (!$item->needsupdate and $USER->gradeediting[$this->courseid]) {
                    $studentshtml .= $this->get_icons($element);
                }

                $hidden = '';
                if ($grade->is_hidden()) {
                    $hidden = ' hidden ';
                }

                $gradepass = ' gradefail ';
                if ($grade->is_passed($item)) {
                    $gradepass = ' gradepass ';
                } elseif (is_null($grade->is_passed($item))) {
                    $gradepass = '';
                }

                // if in editting mode, we need to print either a text box
                // or a drop down (for scales)
                // grades in item of type grade category or course are not directly editable
                if ($item->needsupdate) {
                    $studentshtml .= '<span class="gradingerror'.$hidden.'">'.get_string('error').'</span>';

                } else if ($USER->gradeediting[$this->courseid]) {

                    if ($item->scaleid && !empty($scales_array[$item->scaleid])) {
                        $scale = $scales_array[$item->scaleid];
                        $gradeval = (int)$gradeval; // scales use only integers
                        $scales = explode(",", $scale->scale);
                        // reindex because scale is off 1

                        // MDL-12104 some previous scales might have taken up part of the array
                        // so this needs to be reset
                        $scaleopt = array();
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
                            if ($gradeval < 1) {
                                $studentshtml .= '<span class="gradevalue'.$hidden.$gradepass.'">-</span>';
                            } else {
                                $gradeval = $grade->grade_item->bounded_grade($gradeval); //just in case somebody changes scale
                                $studentshtml .= '<span class="gradevalue'.$hidden.$gradepass.'">'.$scales[$gradeval-1].'</span>';
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
                            $studentshtml .= '<span class="gradevalue'.$hidden.$gradepass.'">'.format_float($gradeval, $decimalpoints).'</span>';
                        }
                    }


                    // If quickfeedback is on, print an input element
                    if ($this->get_pref('showquickfeedback') and $grade->is_editable()) {

                        $studentshtml .= '<input type="hidden" name="oldfeedback_'
                                      .$userid.'_'.$item->id.'" value="' . s($grade->feedback) . '" />';
                        $studentshtml .= '<input class="quickfeedback" tabindex="' . $tabindices[$item->id]['feedback']
                                      . '" size="6" title="' . $strfeedback . '" type="text" name="feedback_'
                                      .$userid.'_'.$item->id.'" value="' . s($grade->feedback) . '" />';
                    }

                } else { // Not editing
                    $gradedisplaytype = $item->get_displaytype();

                    // If feedback present, surround grade with feedback tooltip: Open span here

                    if ($item->needsupdate) {
                        $studentshtml .= '<span class="gradingerror'.$hidden.$gradepass.'">'.get_string('error').'</span>';

                    } else {
                        $studentshtml .= '<span class="gradevalue'.$hidden.$gradepass.'">'.grade_format_gradevalue($gradeval, $item, true, $gradedisplaytype, null).'</span>';
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

    function get_studentnameshtml() {
        global $CFG, $USER;
        $studentshtml = '';

        $showuserimage = $this->get_pref('showuserimage');
        $showuseridnumber = $this->get_pref('showuseridnumber');
        $fixedstudents = $this->is_fixed_students();

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

        if ($fixedstudents) {
            $studentshtml .= '<div class="left_scroller">
                <table id="fixed_column" class="fixed_grades_column">
                    <tbody class="leftbody">';

            $colspan = 'colspan="2"';
            if ($showuseridnumber) {
                $colspan = 'colspan="3"';
            }

            $levels = count($this->gtree->levels) - 1;


            for ($i = 0; $i < $levels; $i++) {
                $studentshtml .= '
                        <tr class="heading name_row">
                            <td '.$colspan.' class="fixedcolumn cell c0 topleft">&nbsp;</td>
                        </tr>
                        ';
            }

            $studentshtml .= '<tr class="heading"><th id="studentheader" colspan="2" class="header c0" scope="col"><a href="'.$this->baseurl.'&amp;sortitemid=firstname">'
                        . $strfirstname . '</a> '
                        . $firstarrow. '/ <a href="'.$this->baseurl.'&amp;sortitemid=lastname">' . $strlastname . '</a>'. $lastarrow .'</th>';

            if ($showuseridnumber) {
                if ('idnumber' == $this->sortitemid) {
                    if ($this->sortorder == 'ASC') {
                        $idnumberarrow = print_arrow('up', $strsortasc, true);
                    } else {
                        $idnumberarrow = print_arrow('down', $strsortdesc, true);
                    }
                } else {
                    $idnumberarrow = '';
                }
                $studentshtml .= '<th class="header c0 useridnumber" scope="col"><a href="'.$this->baseurl.'&amp;sortitemid=idnumber">'
                        . get_string('idnumber') . '</a> ' . $idnumberarrow . '</th>';
            }

            $studentshtml .= '</tr>';

            if ($USER->gradeediting[$this->courseid]) {
                $studentshtml .= '<tr class="controls"><th class="header c0 controls" scope="row" '.$colspan.'>'.$this->get_lang_string('controls','grades').'</th></tr>';
            }

            $row_classes = array(' even ', ' odd ');

            foreach ($this->users as $userid => $user) {

                $user_pic = null;
                if ($showuserimage) {
                    $user_pic = '<div class="userpic">' . print_user_picture($user, $this->courseid, NULL, 0, true) . "</div>\n";
                }

                //either add a th or a colspan to keep things aligned
                $userreportcell = '';
                $userreportcellcolspan = '';
                if (has_capability('gradereport/'.$CFG->grade_profilereport.':view', $this->context)) {
                    $a->user = fullname($user);
                    $strgradesforuser = get_string('gradesforuser', 'grades', $a);
                    $userreportcell = '<th class="userreport"><a href="'.$CFG->wwwroot.'/grade/report/'.$CFG->grade_profilereport.'/index.php?id='.$this->courseid.'&amp;userid='.$user->id.'">'
                                    .'<img src="'.$CFG->pixpath.'/t/grades.gif" alt="'.$strgradesforuser.'" title="'.$strgradesforuser.'" /></a></th>';
                }
                else {
                    $userreportcellcolspan = 'colspan=2';
                }

                $studentshtml .= '<tr class="r'.$this->rowcount++ . $row_classes[$this->rowcount % 2] . '">'
                              .'<th class="c0 user" scope="row" onclick="set_row(this.parentNode.rowIndex);" '.$userreportcellcolspan.' >'.$user_pic
                              .'<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$this->course->id.'">'
                              .fullname($user)."</a></th>$userreportcell\n";

                if ($showuseridnumber) {
                    $studentshtml .= '<th class="c0 useridnumber" onclick="set_row(this.parentNode.rowIndex);">'. $user->idnumber."</th>\n";
                }
                $studentshtml .= "</tr>\n";
            }

            if ($this->get_pref('showranges')) {
                $studentshtml .= '<tr class="range r'.$this->rowcount++.'">' . '<th class="header c0 range " '.$colspan.' scope="row">'.$this->get_lang_string('range','grades').'</th></tr>';
            }

            // Averages heading

            $straverage_group = get_string('groupavg', 'grades');
            $straverage = get_string('overallaverage', 'grades');
            $showaverages = $this->get_pref('showaverages');
            $showaverages_group = $this->currentgroup && $showaverages;

            if ($showaverages_group) {
                $studentshtml .= '<tr class="groupavg r'.$this->rowcount++.'"><th class="header c0" '.$colspan.'scope="row">'.$straverage_group.'</th></tr>';
            }

            if ($showaverages) {
                $studentshtml .= '<tr class="avg r'.$this->rowcount++.'"><th class="header c0" '.$colspan.'scope="row">'.$straverage.'</th></tr>';
            }

            $studentshtml .= '</tbody>
                </table>
            </div>
            <div class="right_scroller">
                <table id="user-grades" class="">
                    <tbody class="righttest">';

        } else {
            $studentshtml .= '<table id="user-grades" class="gradestable flexible boxaligncenter generaltable">
                                <tbody>';
        }

        return $studentshtml;
    }

    /**
     * Closes all open elements
     */
    function get_endhtml() {
        global $CFG, $USER;

        $fixedstudents = $this->is_fixed_students();

        if ($fixedstudents) {
            return "</tbody></table></div>";
        } else {
            return "</tbody></table>";
        }
    }

    /**
     * Builds and return the HTML row of column totals.
     * @param  bool $grouponly Whether to return only group averages or all averages.
     * @return string HTML
     */
    function get_avghtml($grouponly=false) {
        global $CFG, $USER;

        if (!$this->canviewhidden) {
            // totals might be affected by hiding, if user can not see hidden grades the aggregations might be altered
            // better not show them at all if user can not see all hideen grades
            return;
        }

        $averagesdisplaytype   = $this->get_pref('averagesdisplaytype');
        $averagesdecimalpoints = $this->get_pref('averagesdecimalpoints');
        $meanselection         = $this->get_pref('meanselection');
        $shownumberofgrades    = $this->get_pref('shownumberofgrades');

        $avghtml = '';
        $avgcssclass = 'avg';

        if ($grouponly) {
            $straverage = get_string('groupavg', 'grades');
            $showaverages = $this->currentgroup && $this->get_pref('showaverages');
            $groupsql = $this->groupsql;
            $groupwheresql = $this->groupwheresql;
            $avgcssclass = 'groupavg';
        } else {
            $straverage = get_string('overallaverage', 'grades');
            $showaverages = $this->get_pref('showaverages');
            $groupsql = "";
            $groupwheresql = "";
        }

        if ($shownumberofgrades) {
            $straverage .= ' (' . get_string('submissions', 'grades') . ') ';
        }

        $totalcount = $this->get_numusers($grouponly);

        if ($showaverages) {

            // find sums of all grade items in course
            $SQL = "SELECT g.itemid, SUM(g.finalgrade) AS sum
                      FROM {$CFG->prefix}grade_items gi
                           JOIN {$CFG->prefix}grade_grades g      ON g.itemid = gi.id
                           JOIN {$CFG->prefix}user u              ON u.id = g.userid
                           JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                           $groupsql
                     WHERE gi.courseid = $this->courseid
                           AND ra.roleid in ($this->gradebookroles)
                           AND ra.contextid ".get_related_contexts_string($this->context)."
                           AND g.finalgrade IS NOT NULL
                           $groupwheresql
                  GROUP BY g.itemid";
            $sum_array = array();
            if ($sums = get_records_sql($SQL)) {
                foreach ($sums as $itemid => $csum) {
                    $sum_array[$itemid] = $csum->sum;
                }
            }

            $columncount=0;

            $avghtml = '<tr class="' . $avgcssclass . ' r'.$this->rowcount++.'">';

            // MDL-10875 Empty grades must be evaluated as grademin, NOT always 0
            // This query returns a count of ungraded grades (NULL finalgrade OR no matching record in grade_grades table)
            $SQL = "SELECT gi.id, COUNT(u.id) AS count
                      FROM {$CFG->prefix}grade_items gi
                           CROSS JOIN {$CFG->prefix}user u
                           JOIN {$CFG->prefix}role_assignments ra        ON ra.userid = u.id
                           LEFT OUTER JOIN  {$CFG->prefix}grade_grades g ON (g.itemid = gi.id AND g.userid = u.id AND g.finalgrade IS NOT NULL)
                           $groupsql
                     WHERE gi.courseid = $this->courseid
                           AND ra.roleid in ($this->gradebookroles)
                           AND ra.contextid ".get_related_contexts_string($this->context)."
                           AND g.id IS NULL
                           $groupwheresql
                  GROUP BY gi.id";

            $ungraded_counts = get_records_sql($SQL);

            $fixedstudents = $this->is_fixed_students();
            if (!$fixedstudents) {
                $colspan='colspan="2" ';
                if ($this->get_pref('showuseridnumber')) {
                    $colspan = 'colspan="3" ';
                }
                $avghtml .= '<th class="header c0 range" '.$colspan.' scope="row">'.$straverage.'</th>';
            }

            foreach ($this->gtree->items as $itemid=>$unused) {
                $item =& $this->gtree->items[$itemid];

                if ($item->needsupdate) {
                    $avghtml .= '<td class="cell c' . $columncount++.'"><span class="gradingerror">'.get_string('error').'</span></td>';
                    continue;
                }

                if (!isset($sum_array[$item->id])) {
                    $sum_array[$item->id] = 0;
                }

                if (empty($ungraded_counts[$itemid])) {
                    $ungraded_count = 0;
                } else {
                    $ungraded_count = $ungraded_counts[$itemid]->count;
                }

                if ($meanselection == GRADE_REPORT_MEAN_GRADED) {
                    $mean_count = $totalcount - $ungraded_count;
                } else { // Bump up the sum by the number of ungraded items * grademin
                    $sum_array[$item->id] += $ungraded_count * $item->grademin;
                    $mean_count = $totalcount;
                }

                $decimalpoints = $item->get_decimals();

                // Determine which display type to use for this average
                if ($USER->gradeediting[$this->courseid]) {
                    $displaytype = GRADE_DISPLAY_TYPE_REAL;

                } else if ($averagesdisplaytype == GRADE_REPORT_PREFERENCE_INHERIT) { // no ==0 here, please resave the report and user preferences
                    $displaytype = $item->get_displaytype();

                } else {
                    $displaytype = $averagesdisplaytype;
                }

                // Override grade_item setting if a display preference (not inherit) was set for the averages
                if ($averagesdecimalpoints == GRADE_REPORT_PREFERENCE_INHERIT) {
                    $decimalpoints = $item->get_decimals();

                } else {
                    $decimalpoints = $averagesdecimalpoints;
                }

                if (!isset($sum_array[$item->id]) || $mean_count == 0) {
                    $avghtml .= '<td class="cell c' . $columncount++.'">-</td>';
                } else {
                    $sum = $sum_array[$item->id];
                    $avgradeval = $sum/$mean_count;
                    $gradehtml = grade_format_gradevalue($avgradeval, $item, true, $displaytype, $decimalpoints);

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
        global $USER, $CFG;

        $rangehtml = '';
        if ($this->get_pref('showranges')) {
            $rangesdisplaytype   = $this->get_pref('rangesdisplaytype');
            $rangesdecimalpoints = $this->get_pref('rangesdecimalpoints');

            $columncount=0;
            $rangehtml = '<tr class="range r'.$this->rowcount++.' heading">';

            $fixedstudents = $this->is_fixed_students();
            if (!$fixedstudents) {
                $colspan='colspan="2" ';
	                 if ($this->get_pref('showuseridnumber')) {
                    $colspan = 'colspan="3" ';
                }
                $rangehtml .= '<th class="header c0 range" '.$colspan.' scope="row">'.$this->get_lang_string('range','grades').'</th>';
            }

            foreach ($this->gtree->items as $itemid=>$unused) {
                $item =& $this->gtree->items[$itemid];


                $hidden = '';
                if ($item->is_hidden()) {
                    $hidden = ' hidden ';
                }

                $formatted_range = $item->get_formatted_range($rangesdisplaytype, $rangesdecimalpoints);

                $rangehtml .= '<th class="header c'.$columncount++.' range"><span class="rangevalues'.$hidden.'">'. $formatted_range .'</span></th>';

            }
            $rangehtml .= '</tr>';
        }
        return $rangehtml;
    }

    /**
     * Builds and return the HTML row of ranges for each column (i.e. range).
     * @return string HTML
     */
    function get_iconshtml() {
        global $USER, $CFG;

        $iconshtml = '';
        if ($USER->gradeediting[$this->courseid]) {

            $iconshtml = '<tr class="controls">';

            $fixedstudents = $this->is_fixed_students();
            $showuseridnumber = $this->get_pref('showuseridnumber');

            $colspan = 'colspan="2"';
            if ($showuseridnumber) {
                $colspan = 'colspan="3"';
            }

            if (!$fixedstudents) {
                $iconshtml .= '<th class="header c0 controls" scope="row" '.$colspan.'>'.$this->get_lang_string('controls','grades').'</th>';
            }

            $columncount = 0;
            foreach ($this->gtree->items as $itemid=>$unused) {
                // emulate grade element
                $item =& $this->gtree->items[$itemid];

                $eid = $this->gtree->get_item_eid($item);
                $element = $this->gtree->locate_element($eid);

                $iconshtml .= '<td class="controls cell c'.$columncount++.' icons">' . $this->get_icons($element) . '</td>';
            }
            $iconshtml .= '</tr>';
        }
        return $iconshtml;
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
        $edit_icon = '';

        if ($element['type'] != 'categoryitem' && $element['type'] != 'courseitem') {
            $edit_icon             = $this->gtree->get_edit_icon($element, $this->gpr);
        }

        $edit_calculation_icon = '';
        $show_hide_icon        = '';
        $lock_unlock_icon      = '';

        if (has_capability('moodle/grade:manage', $this->context)) {

            if ($this->get_pref('showcalculations')) {
                $edit_calculation_icon = $this->gtree->get_calculation_icon($element, $this->gpr);
            }

            if ($this->get_pref('showeyecons')) {
               $show_hide_icon = $this->gtree->get_hiding_icon($element, $this->gpr);
            }

            if ($this->get_pref('showlocks')) {
                $lock_unlock_icon = $this->gtree->get_locking_icon($element, $this->gpr);
            }
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

            if (in_array($element['object']->id, $this->collapsed['aggregatesonly'])) {
                $expand_contract = 'switch_plus';
            } elseif (!empty($this->collapsed['gradesonly']) && in_array($element['object']->id, $this->collapsed['gradesonly'])) {
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
    
    /**
     * Returns whether or not to display fixed students column.
     * Includes a browser check, because IE6 doesn't support the scrollbar.
     *
     * @return bool
     */
    function is_fixed_students() {
        global $USER, $CFG;
        return empty($USER->screenreader) && $CFG->grade_report_fixedstudents && 
            (check_browser_version('MSIE', '7.0') || 
             check_browser_version('Firefox', '2.0') ||
             check_browser_version('Gecko', '2006010100') ||
             check_browser_version('Camino', '1.0') ||
             check_browser_version('Opera', '6.0') ||
             check_browser_version('Safari', '2.0')); 
    }
}
?>
