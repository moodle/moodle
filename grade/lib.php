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
 * Functions used by gradebook plugins and reports.
 *
 * @package   moodlecore
 * @copyright 2009 Petr Skoda and Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once $CFG->libdir.'/gradelib.php';

/**
 * This class iterates over all users that are graded in a course.
 * Returns detailed info about users and their grades.
 *
 * @author Petr Skoda <skodak@moodle.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class graded_users_iterator {
    public $course;
    public $grade_items;
    public $groupid;
    public $users_rs;
    public $grades_rs;
    public $gradestack;
    public $sortfield1;
    public $sortorder1;
    public $sortfield2;
    public $sortorder2;

    /**
     * Constructor
     *
     * @param object $course A course object
     * @param array  $grade_items array of grade items, if not specified only user info returned
     * @param int    $groupid iterate only group users if present
     * @param string $sortfield1 The first field of the users table by which the array of users will be sorted
     * @param string $sortorder1 The order in which the first sorting field will be sorted (ASC or DESC)
     * @param string $sortfield2 The second field of the users table by which the array of users will be sorted
     * @param string $sortorder2 The order in which the second sorting field will be sorted (ASC or DESC)
     */
    public function graded_users_iterator($course, $grade_items=null, $groupid=0,
                                          $sortfield1='lastname', $sortorder1='ASC',
                                          $sortfield2='firstname', $sortorder2='ASC') {
        $this->course      = $course;
        $this->grade_items = $grade_items;
        $this->groupid     = $groupid;
        $this->sortfield1  = $sortfield1;
        $this->sortorder1  = $sortorder1;
        $this->sortfield2  = $sortfield2;
        $this->sortorder2  = $sortorder2;

        $this->gradestack  = array();
    }

    /**
     * Initialise the iterator
     * @return boolean success
     */
    public function init() {
        global $CFG, $DB;

        $this->close();

        grade_regrade_final_grades($this->course->id);
        $course_item = grade_item::fetch_course_item($this->course->id);
        if ($course_item->needsupdate) {
            // can not calculate all final grades - sorry
            return false;
        }

        $coursecontext = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $relatedcontexts = get_related_contexts_string($coursecontext);

        list($gradebookroles_sql, $params) =
            $DB->get_in_or_equal(explode(',', $CFG->gradebookroles), SQL_PARAMS_NAMED, 'grbr');

        //limit to users with an active enrolment
        list($enrolledsql, $enrolledparams) = get_enrolled_sql($coursecontext);

        $params = array_merge($params, $enrolledparams);

        if ($this->groupid) {
            $groupsql = "INNER JOIN {groups_members} gm ON gm.userid = u.id";
            $groupwheresql = "AND gm.groupid = :groupid";
            // $params contents: gradebookroles
            $params['groupid'] = $this->groupid;
        } else {
            $groupsql = "";
            $groupwheresql = "";
        }

        if (empty($this->sortfield1)) {
            // we must do some sorting even if not specified
            $ofields = ", u.id AS usrt";
            $order   = "usrt ASC";

        } else {
            $ofields = ", u.$this->sortfield1 AS usrt1";
            $order   = "usrt1 $this->sortorder1";
            if (!empty($this->sortfield2)) {
                $ofields .= ", u.$this->sortfield2 AS usrt2";
                $order   .= ", usrt2 $this->sortorder2";
            }
            if ($this->sortfield1 != 'id' and $this->sortfield2 != 'id') {
                // user order MUST be the same in both queries,
                // must include the only unique user->id if not already present
                $ofields .= ", u.id AS usrt";
                $order   .= ", usrt ASC";
            }
        }

        // $params contents: gradebookroles and groupid (for $groupwheresql)
        $users_sql = "SELECT u.* $ofields
                        FROM {user} u
                        JOIN ($enrolledsql) je ON je.id = u.id
                             $groupsql
                        JOIN (
                                  SELECT DISTINCT ra.userid
                                    FROM {role_assignments} ra
                                   WHERE ra.roleid $gradebookroles_sql
                                     AND ra.contextid $relatedcontexts
                             ) rainner ON rainner.userid = u.id
                         WHERE u.deleted = 0
                             $groupwheresql
                    ORDER BY $order";
        $this->users_rs = $DB->get_recordset_sql($users_sql, $params);

        if (!empty($this->grade_items)) {
            $itemids = array_keys($this->grade_items);
            list($itemidsql, $grades_params) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED, 'items');
            $params = array_merge($params, $grades_params);
            // $params contents: gradebookroles, enrolledparams, groupid (for $groupwheresql) and itemids

            $grades_sql = "SELECT g.* $ofields
                             FROM {grade_grades} g
                             JOIN {user} u ON g.userid = u.id
                             JOIN ($enrolledsql) je ON je.id = u.id
                                  $groupsql
                             JOIN (
                                      SELECT DISTINCT ra.userid
                                        FROM {role_assignments} ra
                                       WHERE ra.roleid $gradebookroles_sql
                                         AND ra.contextid $relatedcontexts
                                  ) rainner ON rainner.userid = u.id
                              WHERE u.deleted = 0
                              AND g.itemid $itemidsql
                              $groupwheresql
                         ORDER BY $order, g.itemid ASC";
            $this->grades_rs = $DB->get_recordset_sql($grades_sql, $params);
        } else {
            $this->grades_rs = false;
        }

        return true;
    }

    /**
     * Returns information about the next user
     * @return mixed array of user info, all grades and feedback or null when no more users found
     */
    function next_user() {
        if (!$this->users_rs) {
            return false; // no users present
        }

        if (!$this->users_rs->valid()) {
            if ($current = $this->_pop()) {
                // this is not good - user or grades updated between the two reads above :-(
            }

            return false; // no more users
        } else {
            $user = $this->users_rs->current();
            $this->users_rs->next();
        }

        // find grades of this user
        $grade_records = array();
        while (true) {
            if (!$current = $this->_pop()) {
                break; // no more grades
            }

            if (empty($current->userid)) {
                break;
            }

            if ($current->userid != $user->id) {
                // grade of the next user, we have all for this user
                $this->_push($current);
                break;
            }

            $grade_records[$current->itemid] = $current;
        }

        $grades = array();
        $feedbacks = array();

        if (!empty($this->grade_items)) {
            foreach ($this->grade_items as $grade_item) {
                if (array_key_exists($grade_item->id, $grade_records)) {
                    $feedbacks[$grade_item->id]->feedback       = $grade_records[$grade_item->id]->feedback;
                    $feedbacks[$grade_item->id]->feedbackformat = $grade_records[$grade_item->id]->feedbackformat;
                    unset($grade_records[$grade_item->id]->feedback);
                    unset($grade_records[$grade_item->id]->feedbackformat);
                    $grades[$grade_item->id] = new grade_grade($grade_records[$grade_item->id], false);
                } else {
                    $feedbacks[$grade_item->id]->feedback       = '';
                    $feedbacks[$grade_item->id]->feedbackformat = FORMAT_MOODLE;
                    $grades[$grade_item->id] =
                        new grade_grade(array('userid'=>$user->id, 'itemid'=>$grade_item->id), false);
                }
            }
        }

        $result = new stdClass();
        $result->user      = $user;
        $result->grades    = $grades;
        $result->feedbacks = $feedbacks;

        return $result;
    }

    /**
     * Close the iterator, do not forget to call this function.
     * @return void
     */
    function close() {
        if ($this->users_rs) {
            $this->users_rs->close();
            $this->users_rs = null;
        }
        if ($this->grades_rs) {
            $this->grades_rs->close();
            $this->grades_rs = null;
        }
        $this->gradestack = array();
    }


    /**
     * _push
     *
     * @param grade_grade $grade Grade object
     *
     * @return void
     */
    function _push($grade) {
        array_push($this->gradestack, $grade);
    }


    /**
     * _pop
     *
     * @return void
     */
    function _pop() {
        global $DB;
        if (empty($this->gradestack)) {
            if (!$this->grades_rs) {
                return null; // no grades present
            }

            if ($this->grades_rs->next()) {
                return null; // no more grades
            }

            return $this->grades_rs->current();
        } else {
            return array_pop($this->gradestack);
        }
    }
}

/**
 * Print a selection popup form of the graded users in a course.
 *
 * @deprecated since 2.0
 *
 * @param int    $course id of the course
 * @param string $actionpage The page receiving the data from the popoup form
 * @param int    $userid   id of the currently selected user (or 'all' if they are all selected)
 * @param int    $groupid id of requested group, 0 means all
 * @param int    $includeall bool include all option
 * @param bool   $return If true, will return the HTML, otherwise, will print directly
 * @return null
 */
function print_graded_users_selector($course, $actionpage, $userid=0, $groupid=0, $includeall=true, $return=false) {
    global $CFG, $USER, $OUTPUT;
    return $OUTPUT->render(grade_get_graded_users_select(substr($actionpage, 0, strpos($actionpage, '/')), $course, $userid, $groupid, $includeall));
}

function grade_get_graded_users_select($report, $course, $userid, $groupid, $includeall) {
    global $USER;

    if (is_null($userid)) {
        $userid = $USER->id;
    }

    $menu = array(); // Will be a list of userid => user name
    $gui = new graded_users_iterator($course, null, $groupid);
    $gui->init();
    $label = get_string('selectauser', 'grades');
    if ($includeall) {
        $menu[0] = get_string('allusers', 'grades');
        $label = get_string('selectalloroneuser', 'grades');
    }
    while ($userdata = $gui->next_user()) {
        $user = $userdata->user;
        $menu[$user->id] = fullname($user);
    }
    $gui->close();

    if ($includeall) {
        $menu[0] .= " (" . (count($menu) - 1) . ")";
    }
    $select = new single_select(new moodle_url('/grade/report/'.$report.'/index.php', array('id'=>$course->id)), 'userid', $menu, $userid);
    $select->label = $label;
    $select->formid = 'choosegradeuser';
    return $select;
}

/**
 * Print grading plugin selection popup form.
 *
 * @param array   $plugin_info An array of plugins containing information for the selector
 * @param boolean $return return as string
 *
 * @return nothing or string if $return true
 */
function print_grade_plugin_selector($plugin_info, $active_type, $active_plugin, $return=false) {
    global $CFG, $OUTPUT, $PAGE;

    $menu = array();
    $count = 0;
    $active = '';

    foreach ($plugin_info as $plugin_type => $plugins) {
        if ($plugin_type == 'strings') {
            continue;
        }

        $first_plugin = reset($plugins);

        $sectionname = $plugin_info['strings'][$plugin_type];
        $section = array();

        foreach ($plugins as $plugin) {
            $link = $plugin->link->out(false);
            $section[$link] = $plugin->string;
            $count++;
            if ($plugin_type === $active_type and $plugin->id === $active_plugin) {
                $active = $link;
            }
        }

        if ($section) {
            $menu[] = array($sectionname=>$section);
        }
    }

    // finally print/return the popup form
    if ($count > 1) {
        $select = new url_select($menu, $active, null, 'choosepluginreport');

        if ($return) {
            return $OUTPUT->render($select);
        } else {
            echo $OUTPUT->render($select);
        }
    } else {
        // only one option - no plugin selector needed
        return '';
    }
}

/**
 * Print grading plugin selection tab-based navigation.
 *
 * @param string  $active_type type of plugin on current page - import, export, report or edit
 * @param string  $active_plugin active plugin type - grader, user, cvs, ...
 * @param array   $plugin_info Array of plugins
 * @param boolean $return return as string
 *
 * @return nothing or string if $return true
 */
function grade_print_tabs($active_type, $active_plugin, $plugin_info, $return=false) {
    global $CFG, $COURSE;

    if (!isset($currenttab)) { //TODO: this is weird
        $currenttab = '';
    }

    $tabs = array();
    $top_row  = array();
    $bottom_row = array();
    $inactive = array($active_plugin);
    $activated = array();

    $count = 0;
    $active = '';

    foreach ($plugin_info as $plugin_type => $plugins) {
        if ($plugin_type == 'strings') {
            continue;
        }

        // If $plugins is actually the definition of a child-less parent link:
        if (!empty($plugins->id)) {
            $string = $plugins->string;
            if (!empty($plugin_info[$active_type]->parent)) {
                $string = $plugin_info[$active_type]->parent->string;
            }

            $top_row[] = new tabobject($plugin_type, $plugins->link, $string);
            continue;
        }

        $first_plugin = reset($plugins);
        $url = $first_plugin->link;

        if ($plugin_type == 'report') {
            $url = $CFG->wwwroot.'/grade/report/index.php?id='.$COURSE->id;
        }

        $top_row[] = new tabobject($plugin_type, $url, $plugin_info['strings'][$plugin_type]);

        if ($active_type == $plugin_type) {
            foreach ($plugins as $plugin) {
                $bottom_row[] = new tabobject($plugin->id, $plugin->link, $plugin->string);
                if ($plugin->id == $active_plugin) {
                    $inactive = array($plugin->id);
                }
            }
        }
    }

    $tabs[] = $top_row;
    $tabs[] = $bottom_row;

    if ($return) {
        return print_tabs($tabs, $active_type, $inactive, $activated, true);
    } else {
        print_tabs($tabs, $active_type, $inactive, $activated);
    }
}

/**
 * grade_get_plugin_info
 *
 * @param int    $courseid The course id
 * @param string $active_type type of plugin on current page - import, export, report or edit
 * @param string $active_plugin active plugin type - grader, user, cvs, ...
 *
 * @return array
 */
function grade_get_plugin_info($courseid, $active_type, $active_plugin) {
    global $CFG, $SITE;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    $plugin_info = array();
    $count = 0;
    $active = '';
    $url_prefix = $CFG->wwwroot . '/grade/';

    // Language strings
    $plugin_info['strings'] = grade_helper::get_plugin_strings();

    if ($reports = grade_helper::get_plugins_reports($courseid)) {
        $plugin_info['report'] = $reports;
    }

    //showing grade categories and items make no sense if we're not within a course
    if ($courseid!=$SITE->id) {
        if ($edittree = grade_helper::get_info_edit_structure($courseid)) {
            $plugin_info['edittree'] = $edittree;
        }
    }

    if ($scale = grade_helper::get_info_scales($courseid)) {
        $plugin_info['scale'] = array('view'=>$scale);
    }

    if ($outcomes = grade_helper::get_info_outcomes($courseid)) {
        $plugin_info['outcome'] = $outcomes;
    }

    if ($letters = grade_helper::get_info_letters($courseid)) {
        $plugin_info['letter'] = $letters;
    }

    if ($imports = grade_helper::get_plugins_import($courseid)) {
        $plugin_info['import'] = $imports;
    }

    if ($exports = grade_helper::get_plugins_export($courseid)) {
        $plugin_info['export'] = $exports;
    }

    foreach ($plugin_info as $plugin_type => $plugins) {
        if (!empty($plugins->id) && $active_plugin == $plugins->id) {
            $plugin_info['strings']['active_plugin_str'] = $plugins->string;
            break;
        }
        foreach ($plugins as $plugin) {
            if (is_a($plugin, 'grade_plugin_info')) {
                if ($active_plugin == $plugin->id) {
                    $plugin_info['strings']['active_plugin_str'] = $plugin->string;
                }
            }
        }
    }

    //hide course settings if we're not in a course
    if ($courseid!=$SITE->id) {
        if ($setting = grade_helper::get_info_manage_settings($courseid)) {
            $plugin_info['settings'] = array('course'=>$setting);
        }
    }

    // Put preferences last
    if ($preferences = grade_helper::get_plugins_report_preferences($courseid)) {
        $plugin_info['preferences'] = $preferences;
    }

    foreach ($plugin_info as $plugin_type => $plugins) {
        if (!empty($plugins->id) && $active_plugin == $plugins->id) {
            $plugin_info['strings']['active_plugin_str'] = $plugins->string;
            break;
        }
        foreach ($plugins as $plugin) {
            if (is_a($plugin, 'grade_plugin_info')) {
                if ($active_plugin == $plugin->id) {
                    $plugin_info['strings']['active_plugin_str'] = $plugin->string;
                }
            }
        }
    }

    return $plugin_info;
}

/**
 * A simple class containing info about grade plugins.
 * Can be subclassed for special rules
 *
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_plugin_info {
    /**
     * A unique id for this plugin
     *
     * @var mixed
     */
    public $id;
    /**
     * A URL to access this plugin
     *
     * @var mixed
     */
    public $link;
    /**
     * The name of this plugin
     *
     * @var mixed
     */
    public $string;
    /**
     * Another grade_plugin_info object, parent of the current one
     *
     * @var mixed
     */
    public $parent;

    /**
     * Constructor
     *
     * @param int $id A unique id for this plugin
     * @param string $link A URL to access this plugin
     * @param string $string The name of this plugin
     * @param object $parent Another grade_plugin_info object, parent of the current one
     *
     * @return void
     */
    public function __construct($id, $link, $string, $parent=null) {
        $this->id = $id;
        $this->link = $link;
        $this->string = $string;
        $this->parent = $parent;
    }
}

/**
 * Prints the page headers, breadcrumb trail, page heading, (optional) dropdown navigation menu and
 * (optional) navigation tabs for any gradebook page. All gradebook pages MUST use these functions
 * in favour of the usual print_header(), print_header_simple(), print_heading() etc.
 * !IMPORTANT! Use of tabs.php file in gradebook pages is forbidden unless tabs are switched off at
 * the site level for the gradebook ($CFG->grade_navmethod = GRADE_NAVMETHOD_DROPDOWN).
 *
 * @param int     $courseid Course id
 * @param string  $active_type The type of the current page (report, settings,
 *                             import, export, scales, outcomes, letters)
 * @param string  $active_plugin The plugin of the current page (grader, fullview etc...)
 * @param string  $heading The heading of the page. Tries to guess if none is given
 * @param boolean $return Whether to return (true) or echo (false) the HTML generated by this function
 * @param string  $bodytags Additional attributes that will be added to the <body> tag
 * @param string  $buttons Additional buttons to display on the page
 *
 * @return string HTML code or nothing if $return == false
 */
function print_grade_page_head($courseid, $active_type, $active_plugin=null,
                               $heading = false, $return=false,
                               $buttons=false, $shownavigation=true) {
    global $CFG, $OUTPUT, $PAGE;

    $plugin_info = grade_get_plugin_info($courseid, $active_type, $active_plugin);

    // Determine the string of the active plugin
    $stractive_plugin = ($active_plugin) ? $plugin_info['strings']['active_plugin_str'] : $heading;
    $stractive_type = $plugin_info['strings'][$active_type];

    if (empty($plugin_info[$active_type]->id) || !empty($plugin_info[$active_type]->parent)) {
        $title = $PAGE->course->fullname.': ' . $stractive_type . ': ' . $stractive_plugin;
    } else {
        $title = $PAGE->course->fullname.': ' . $stractive_plugin;
    }

    if ($active_type == 'report') {
        $PAGE->set_pagelayout('report');
    } else {
        $PAGE->set_pagelayout('admin');
    }
    $PAGE->set_title(get_string('grades') . ': ' . $stractive_type);
    $PAGE->set_heading($title);
    if ($buttons instanceof single_button) {
        $buttons = $OUTPUT->render($buttons);
    }
    $PAGE->set_button($buttons);
    grade_extend_settings($plugin_info, $courseid);

    $returnval = $OUTPUT->header();
    if (!$return) {
        echo $returnval;
    }

    // Guess heading if not given explicitly
    if (!$heading) {
        $heading = $stractive_plugin;
    }

    if ($shownavigation) {
        if ($CFG->grade_navmethod == GRADE_NAVMETHOD_COMBO || $CFG->grade_navmethod == GRADE_NAVMETHOD_DROPDOWN) {
            $returnval .= print_grade_plugin_selector($plugin_info, $active_type, $active_plugin, $return);
        }
        $returnval .= $OUTPUT->heading($heading);
        if ($CFG->grade_navmethod == GRADE_NAVMETHOD_COMBO || $CFG->grade_navmethod == GRADE_NAVMETHOD_TABS) {
            $returnval .= grade_print_tabs($active_type, $active_plugin, $plugin_info, $return);
        }
    }

    if ($return) {
        return $returnval;
    }
}

/**
 * Utility class used for return tracking when using edit and other forms in grade plugins
 *
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_plugin_return {
    public $type;
    public $plugin;
    public $courseid;
    public $userid;
    public $page;

    /**
     * Constructor
     *
     * @param array $params - associative array with return parameters, if null parameter are taken from _GET or _POST
     */
    public function grade_plugin_return($params = null) {
        if (empty($params)) {
            $this->type     = optional_param('gpr_type', null, PARAM_SAFEDIR);
            $this->plugin   = optional_param('gpr_plugin', null, PARAM_SAFEDIR);
            $this->courseid = optional_param('gpr_courseid', null, PARAM_INT);
            $this->userid   = optional_param('gpr_userid', null, PARAM_INT);
            $this->page     = optional_param('gpr_page', null, PARAM_INT);

        } else {
            foreach ($params as $key=>$value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Returns return parameters as options array suitable for buttons.
     * @return array options
     */
    public function get_options() {
        if (empty($this->type)) {
            return array();
        }

        $params = array();

        if (!empty($this->plugin)) {
            $params['plugin'] = $this->plugin;
        }

        if (!empty($this->courseid)) {
            $params['id'] = $this->courseid;
        }

        if (!empty($this->userid)) {
            $params['userid'] = $this->userid;
        }

        if (!empty($this->page)) {
            $params['page'] = $this->page;
        }

        return $params;
    }

    /**
     * Returns return url
     *
     * @param string $default default url when params not set
     * @param array  $extras Extra URL parameters
     *
     * @return string url
     */
    public function get_return_url($default, $extras=null) {
        global $CFG;

        if (empty($this->type) or empty($this->plugin)) {
            return $default;
        }

        $url = $CFG->wwwroot.'/grade/'.$this->type.'/'.$this->plugin.'/index.php';
        $glue = '?';

        if (!empty($this->courseid)) {
            $url .= $glue.'id='.$this->courseid;
            $glue = '&amp;';
        }

        if (!empty($this->userid)) {
            $url .= $glue.'userid='.$this->userid;
            $glue = '&amp;';
        }

        if (!empty($this->page)) {
            $url .= $glue.'page='.$this->page;
            $glue = '&amp;';
        }

        if (!empty($extras)) {
            foreach ($extras as $key=>$value) {
                $url .= $glue.$key.'='.$value;
                $glue = '&amp;';
            }
        }

        return $url;
    }

    /**
     * Returns string with hidden return tracking form elements.
     * @return string
     */
    public function get_form_fields() {
        if (empty($this->type)) {
            return '';
        }

        $result  = '<input type="hidden" name="gpr_type" value="'.$this->type.'" />';

        if (!empty($this->plugin)) {
            $result .= '<input type="hidden" name="gpr_plugin" value="'.$this->plugin.'" />';
        }

        if (!empty($this->courseid)) {
            $result .= '<input type="hidden" name="gpr_courseid" value="'.$this->courseid.'" />';
        }

        if (!empty($this->userid)) {
            $result .= '<input type="hidden" name="gpr_userid" value="'.$this->userid.'" />';
        }

        if (!empty($this->page)) {
            $result .= '<input type="hidden" name="gpr_page" value="'.$this->page.'" />';
        }
    }

    /**
     * Add hidden elements into mform
     *
     * @param object &$mform moodle form object
     *
     * @return void
     */
    public function add_mform_elements(&$mform) {
        if (empty($this->type)) {
            return;
        }

        $mform->addElement('hidden', 'gpr_type', $this->type);
        $mform->setType('gpr_type', PARAM_SAFEDIR);

        if (!empty($this->plugin)) {
            $mform->addElement('hidden', 'gpr_plugin', $this->plugin);
            $mform->setType('gpr_plugin', PARAM_SAFEDIR);
        }

        if (!empty($this->courseid)) {
            $mform->addElement('hidden', 'gpr_courseid', $this->courseid);
            $mform->setType('gpr_courseid', PARAM_INT);
        }

        if (!empty($this->userid)) {
            $mform->addElement('hidden', 'gpr_userid', $this->userid);
            $mform->setType('gpr_userid', PARAM_INT);
        }

        if (!empty($this->page)) {
            $mform->addElement('hidden', 'gpr_page', $this->page);
            $mform->setType('gpr_page', PARAM_INT);
        }
    }

    /**
     * Add return tracking params into url
     *
     * @param moodle_url $url A URL
     *
     * @return string $url with return tracking params
     */
    public function add_url_params(moodle_url $url) {
        if (empty($this->type)) {
            return $url;
        }

        $url->param('gpr_type', $this->type);

        if (!empty($this->plugin)) {
            $url->param('gpr_plugin', $this->plugin);
        }

        if (!empty($this->courseid)) {
            $url->param('gpr_courseid' ,$this->courseid);
        }

        if (!empty($this->userid)) {
            $url->param('gpr_userid', $this->userid);
        }

        if (!empty($this->page)) {
            $url->param('gpr_page', $this->page);
        }

        return $url;
    }
}

/**
 * Function central to gradebook for building and printing the navigation (breadcrumb trail).
 *
 * @param string $path The path of the calling script (using __FILE__?)
 * @param string $pagename The language string to use as the last part of the navigation (non-link)
 * @param mixed  $id Either a plain integer (assuming the key is 'id') or
 *                   an array of keys and values (e.g courseid => $courseid, itemid...)
 *
 * @return string
 */
function grade_build_nav($path, $pagename=null, $id=null) {
    global $CFG, $COURSE, $PAGE;

    $strgrades = get_string('grades', 'grades');

    // Parse the path and build navlinks from its elements
    $dirroot_length = strlen($CFG->dirroot) + 1; // Add 1 for the first slash
    $path = substr($path, $dirroot_length);
    $path = str_replace('\\', '/', $path);

    $path_elements = explode('/', $path);

    $path_elements_count = count($path_elements);

    // First link is always 'grade'
    $PAGE->navbar->add($strgrades, new moodle_url('/grade/index.php', array('id'=>$COURSE->id)));

    $link = null;
    $numberofelements = 3;

    // Prepare URL params string
    $linkparams = array();
    if (!is_null($id)) {
        if (is_array($id)) {
            foreach ($id as $idkey => $idvalue) {
                $linkparams[$idkey] = $idvalue;
            }
        } else {
            $linkparams['id'] = $id;
        }
    }

    $navlink4 = null;

    // Remove file extensions from filenames
    foreach ($path_elements as $key => $filename) {
        $path_elements[$key] = str_replace('.php', '', $filename);
    }

    // Second level links
    switch ($path_elements[1]) {
        case 'edit': // No link
            if ($path_elements[3] != 'index.php') {
                $numberofelements = 4;
            }
            break;
        case 'import': // No link
            break;
        case 'export': // No link
            break;
        case 'report':
            // $id is required for this link. Do not print it if $id isn't given
            if (!is_null($id)) {
                $link = new moodle_url('/grade/report/index.php', $linkparams);
            }

            if ($path_elements[2] == 'grader') {
                $numberofelements = 4;
            }
            break;

        default:
            // If this element isn't among the ones already listed above, it isn't supported, throw an error.
            debugging("grade_build_nav() doesn't support ". $path_elements[1] .
                    " as the second path element after 'grade'.");
            return false;
    }
    $PAGE->navbar->add(get_string($path_elements[1], 'grades'), $link);

    // Third level links
    if (empty($pagename)) {
        $pagename = get_string($path_elements[2], 'grades');
    }

    switch ($numberofelements) {
        case 3:
            $PAGE->navbar->add($pagename, $link);
            break;
        case 4:
            if ($path_elements[2] == 'grader' AND $path_elements[3] != 'index.php') {
                $PAGE->navbar->add(get_string('pluginname', 'gradereport_grader'), new moodle_url('/grade/report/grader/index.php', $linkparams));
            }
            $PAGE->navbar->add($pagename);
            break;
    }

    return '';
}

/**
 * General structure representing grade items in course
 *
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_structure {
    public $context;

    public $courseid;

    /**
     * 1D array of grade items only
     */
    public $items;

    /**
     * Returns icon of element
     *
     * @param array &$element An array representing an element in the grade_tree
     * @param bool  $spacerifnone return spacer if no icon found
     *
     * @return string icon or spacer
     */
    public function get_element_icon(&$element, $spacerifnone=false) {
        global $CFG, $OUTPUT;

        switch ($element['type']) {
            case 'item':
            case 'courseitem':
            case 'categoryitem':
                $is_course   = $element['object']->is_course_item();
                $is_category = $element['object']->is_category_item();
                $is_scale    = $element['object']->gradetype == GRADE_TYPE_SCALE;
                $is_value    = $element['object']->gradetype == GRADE_TYPE_VALUE;
                $is_outcome  = !empty($element['object']->outcomeid);

                if ($element['object']->is_calculated()) {
                    $strcalc = get_string('calculatedgrade', 'grades');
                    return '<img src="'.$OUTPUT->pix_url('i/calc') . '" class="icon itemicon" title="'.
                            s($strcalc).'" alt="'.s($strcalc).'"/>';

                } else if (($is_course or $is_category) and ($is_scale or $is_value)) {
                    if ($category = $element['object']->get_item_category()) {
                        switch ($category->aggregation) {
                            case GRADE_AGGREGATE_MEAN:
                            case GRADE_AGGREGATE_MEDIAN:
                            case GRADE_AGGREGATE_WEIGHTED_MEAN:
                            case GRADE_AGGREGATE_WEIGHTED_MEAN2:
                            case GRADE_AGGREGATE_EXTRACREDIT_MEAN:
                                $stragg = get_string('aggregation', 'grades');
                                return '<img src="'.$OUTPUT->pix_url('i/agg_mean') . '" ' .
                                        'class="icon itemicon" title="'.s($stragg).'" alt="'.s($stragg).'"/>';
                            case GRADE_AGGREGATE_SUM:
                                $stragg = get_string('aggregation', 'grades');
                                return '<img src="'.$OUTPUT->pix_url('i/agg_sum') . '" ' .
                                        'class="icon itemicon" title="'.s($stragg).'" alt="'.s($stragg).'"/>';
                        }
                    }

                } else if ($element['object']->itemtype == 'mod') {
                    //prevent outcomes being displaying the same icon as the activity they are attached to
                    if ($is_outcome) {
                        $stroutcome = s(get_string('outcome', 'grades'));
                        return '<img src="'.$OUTPUT->pix_url('i/outcomes') . '" ' .
                            'class="icon itemicon" title="'.$stroutcome.
                            '" alt="'.$stroutcome.'"/>';
                    } else {
                        $strmodname = get_string('modulename', $element['object']->itemmodule);
                        return '<img src="'.$OUTPUT->pix_url('icon',
                            $element['object']->itemmodule) . '" ' .
                            'class="icon itemicon" title="' .s($strmodname).
                            '" alt="' .s($strmodname).'"/>';
                    }
                } else if ($element['object']->itemtype == 'manual') {
                    if ($element['object']->is_outcome_item()) {
                        $stroutcome = get_string('outcome', 'grades');
                        return '<img src="'.$OUTPUT->pix_url('i/outcomes') . '" ' .
                                'class="icon itemicon" title="'.s($stroutcome).
                                '" alt="'.s($stroutcome).'"/>';
                    } else {
                        $strmanual = get_string('manualitem', 'grades');
                        return '<img src="'.$OUTPUT->pix_url('t/manual_item') . '" '.
                                'class="icon itemicon" title="'.s($strmanual).
                                '" alt="'.s($strmanual).'"/>';
                    }
                }
                break;

            case 'category':
                $strcat = get_string('category', 'grades');
                return '<img src="'.$OUTPUT->pix_url('f/folder') . '" class="icon itemicon" ' .
                        'title="'.s($strcat).'" alt="'.s($strcat).'" />';
        }

        if ($spacerifnone) {
            return $OUTPUT->spacer().' ';
        } else {
            return '';
        }
    }

    /**
     * Returns name of element optionally with icon and link
     *
     * @param array &$element An array representing an element in the grade_tree
     * @param bool  $withlink Whether or not this header has a link
     * @param bool  $icon Whether or not to display an icon with this header
     * @param bool  $spacerifnone return spacer if no icon found
     *
     * @return string header
     */
    public function get_element_header(&$element, $withlink=false, $icon=true, $spacerifnone=false) {
        global $CFG;

        $header = '';

        if ($icon) {
            $header .= $this->get_element_icon($element, $spacerifnone);
        }

        $header .= $element['object']->get_name();

        if ($element['type'] != 'item' and $element['type'] != 'categoryitem' and
            $element['type'] != 'courseitem') {
            return $header;
        }

        $itemtype     = $element['object']->itemtype;
        $itemmodule   = $element['object']->itemmodule;
        $iteminstance = $element['object']->iteminstance;

        if ($withlink and $itemtype=='mod' and $iteminstance and $itemmodule) {
            if ($cm = get_coursemodule_from_instance($itemmodule, $iteminstance, $this->courseid)) {

                $a = new stdClass();
                $a->name = get_string('modulename', $element['object']->itemmodule);
                $title = get_string('linktoactivity', 'grades', $a);
                $dir = $CFG->dirroot.'/mod/'.$itemmodule;

                if (file_exists($dir.'/grade.php')) {
                    $url = $CFG->wwwroot.'/mod/'.$itemmodule.'/grade.php?id='.$cm->id;
                } else {
                    $url = $CFG->wwwroot.'/mod/'.$itemmodule.'/view.php?id='.$cm->id;
                }

                $header = '<a href="'.$url.'" title="'.s($title).'">'.$header.'</a>';
            }
        }

        return $header;
    }

    /**
     * Returns the grade eid - the grade may not exist yet.
     *
     * @param grade_grade $grade_grade A grade_grade object
     *
     * @return string eid
     */
    public function get_grade_eid($grade_grade) {
        if (empty($grade_grade->id)) {
            return 'n'.$grade_grade->itemid.'u'.$grade_grade->userid;
        } else {
            return 'g'.$grade_grade->id;
        }
    }

    /**
     * Returns the grade_item eid
     * @param grade_item $grade_item A grade_item object
     * @return string eid
     */
    public function get_item_eid($grade_item) {
        return 'i'.$grade_item->id;
    }

    /**
     * Given a grade_tree element, returns an array of parameters
     * used to build an icon for that element.
     *
     * @param array $element An array representing an element in the grade_tree
     *
     * @return array
     */
    public function get_params_for_iconstr($element) {
        $strparams = new stdClass();
        $strparams->category = '';
        $strparams->itemname = '';
        $strparams->itemmodule = '';

        if (!method_exists($element['object'], 'get_name')) {
            return $strparams;
        }

        $strparams->itemname = html_to_text($element['object']->get_name());

        // If element name is categorytotal, get the name of the parent category
        if ($strparams->itemname == get_string('categorytotal', 'grades')) {
            $parent = $element['object']->get_parent_category();
            $strparams->category = $parent->get_name() . ' ';
        } else {
            $strparams->category = '';
        }

        $strparams->itemmodule = null;
        if (isset($element['object']->itemmodule)) {
            $strparams->itemmodule = $element['object']->itemmodule;
        }
        return $strparams;
    }

    /**
     * Return edit icon for give element
     *
     * @param array  $element An array representing an element in the grade_tree
     * @param object $gpr A grade_plugin_return object
     *
     * @return string
     */
    public function get_edit_icon($element, $gpr) {
        global $CFG, $OUTPUT;

        if (!has_capability('moodle/grade:manage', $this->context)) {
            if ($element['type'] == 'grade' and has_capability('moodle/grade:edit', $this->context)) {
                // oki - let them override grade
            } else {
                return '';
            }
        }

        static $strfeedback   = null;
        static $streditgrade = null;
        if (is_null($streditgrade)) {
            $streditgrade = get_string('editgrade', 'grades');
            $strfeedback  = get_string('feedback');
        }

        $strparams = $this->get_params_for_iconstr($element);

        $object = $element['object'];

        switch ($element['type']) {
            case 'item':
            case 'categoryitem':
            case 'courseitem':
                $stredit = get_string('editverbose', 'grades', $strparams);
                if (empty($object->outcomeid) || empty($CFG->enableoutcomes)) {
                    $url = new moodle_url('/grade/edit/tree/item.php',
                            array('courseid' => $this->courseid, 'id' => $object->id));
                } else {
                    $url = new moodle_url('/grade/edit/tree/outcomeitem.php',
                            array('courseid' => $this->courseid, 'id' => $object->id));
                }
                break;

            case 'category':
                $stredit = get_string('editverbose', 'grades', $strparams);
                $url = new moodle_url('/grade/edit/tree/category.php',
                        array('courseid' => $this->courseid, 'id' => $object->id));
                break;

            case 'grade':
                $stredit = $streditgrade;
                if (empty($object->id)) {
                    $url = new moodle_url('/grade/edit/tree/grade.php',
                            array('courseid' => $this->courseid, 'itemid' => $object->itemid, 'userid' => $object->userid));
                } else {
                    $url = new moodle_url('/grade/edit/tree/grade.php',
                            array('courseid' => $this->courseid, 'id' => $object->id));
                }
                if (!empty($object->feedback)) {
                    $feedback = addslashes_js(trim(format_string($object->feedback, $object->feedbackformat)));
                }
                break;

            default:
                $url = null;
        }

        if ($url) {
            return $OUTPUT->action_icon($gpr->add_url_params($url), new pix_icon('t/edit', $stredit));

        } else {
            return '';
        }
    }

    /**
     * Return hiding icon for give element
     *
     * @param array  $element An array representing an element in the grade_tree
     * @param object $gpr A grade_plugin_return object
     *
     * @return string
     */
    public function get_hiding_icon($element, $gpr) {
        global $CFG, $OUTPUT;

        if (!has_capability('moodle/grade:manage', $this->context) and
            !has_capability('moodle/grade:hide', $this->context)) {
            return '';
        }

        $strparams = $this->get_params_for_iconstr($element);
        $strshow = get_string('showverbose', 'grades', $strparams);
        $strhide = get_string('hideverbose', 'grades', $strparams);

        $url = new moodle_url('/grade/edit/tree/action.php', array('id' => $this->courseid, 'sesskey' => sesskey(), 'eid' => $element['eid']));
        $url = $gpr->add_url_params($url);

        if ($element['object']->is_hidden()) {
            $type = 'show';
            $tooltip = $strshow;

            // Change the icon and add a tooltip showing the date
            if ($element['type'] != 'category' and $element['object']->get_hidden() > 1) {
                $type = 'hiddenuntil';
                $tooltip = get_string('hiddenuntildate', 'grades',
                        userdate($element['object']->get_hidden()));
            }

            $url->param('action', 'show');

            $hideicon = $OUTPUT->action_icon($url, new pix_icon('t/'.$type, $tooltip, 'moodle', array('alt'=>$strshow, 'class'=>'iconsmall')));

        } else {
            $url->param('action', 'hide');
            $hideicon = $OUTPUT->action_icon($url, new pix_icon('t/hide', $strhide));
        }

        return $hideicon;
    }

    /**
     * Return locking icon for given element
     *
     * @param array  $element An array representing an element in the grade_tree
     * @param object $gpr A grade_plugin_return object
     *
     * @return string
     */
    public function get_locking_icon($element, $gpr) {
        global $CFG, $OUTPUT;

        $strparams = $this->get_params_for_iconstr($element);
        $strunlock = get_string('unlockverbose', 'grades', $strparams);
        $strlock = get_string('lockverbose', 'grades', $strparams);

        $url = new moodle_url('/grade/edit/tree/action.php', array('id' => $this->courseid, 'sesskey' => sesskey(), 'eid' => $element['eid']));
        $url = $gpr->add_url_params($url);

        // Don't allow an unlocking action for a grade whose grade item is locked: just print a state icon
        if ($element['type'] == 'grade' && $element['object']->grade_item->is_locked()) {
            $strparamobj = new stdClass();
            $strparamobj->itemname = $element['object']->grade_item->itemname;
            $strnonunlockable = get_string('nonunlockableverbose', 'grades', $strparamobj);

            $action = $OUTPUT->pix_icon('t/unlock_gray', $strnonunlockable);

        } else if ($element['object']->is_locked()) {
            $type = 'unlock';
            $tooltip = $strunlock;

            // Change the icon and add a tooltip showing the date
            if ($element['type'] != 'category' and $element['object']->get_locktime() > 1) {
                $type = 'locktime';
                $tooltip = get_string('locktimedate', 'grades',
                        userdate($element['object']->get_locktime()));
            }

            if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:unlock', $this->context)) {
                $action = '';
            } else {
                $url->param('action', 'unlock');
                $action = $OUTPUT->action_icon($url, new pix_icon('t/'.$type, $tooltip, 'moodle', array('alt'=>$strunlock, 'class'=>'smallicon')));
            }

        } else {
            if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:lock', $this->context)) {
                $action = '';
            } else {
                $url->param('action', 'lock');
                $action = $OUTPUT->action_icon($url, new pix_icon('t/lock', $strlock));
            }
        }

        return $action;
    }

    /**
     * Return calculation icon for given element
     *
     * @param array  $element An array representing an element in the grade_tree
     * @param object $gpr A grade_plugin_return object
     *
     * @return string
     */
    public function get_calculation_icon($element, $gpr) {
        global $CFG, $OUTPUT;
        if (!has_capability('moodle/grade:manage', $this->context)) {
            return '';
        }

        $type   = $element['type'];
        $object = $element['object'];

        if ($type == 'item' or $type == 'courseitem' or $type == 'categoryitem') {
            $strparams = $this->get_params_for_iconstr($element);
            $streditcalculation = get_string('editcalculationverbose', 'grades', $strparams);

            $is_scale = $object->gradetype == GRADE_TYPE_SCALE;
            $is_value = $object->gradetype == GRADE_TYPE_VALUE;

            // show calculation icon only when calculation possible
            if (!$object->is_external_item() and ($is_scale or $is_value)) {
                if ($object->is_calculated()) {
                    $icon = 't/calc';
                } else {
                    $icon = 't/calc_off';
                }

                $url = new moodle_url('/grade/edit/tree/calculation.php', array('courseid' => $this->courseid, 'id' => $object->id));
                $url = $gpr->add_url_params($url);
                return $OUTPUT->action_icon($url, new pix_icon($icon, $streditcalculation)) . "\n";
            }
        }

        return '';
    }
}

/**
 * Flat structure similar to grade tree.
 *
 * @uses grade_structure
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_seq extends grade_structure {

    /**
     * 1D array of elements
     */
    public $elements;

    /**
     * Constructor, retrieves and stores array of all grade_category and grade_item
     * objects for the given courseid. Full objects are instantiated. Ordering sequence is fixed if needed.
     *
     * @param int  $courseid The course id
     * @param bool $category_grade_last category grade item is the last child
     * @param bool $nooutcomes Whether or not outcomes should be included
     */
    public function grade_seq($courseid, $category_grade_last=false, $nooutcomes=false) {
        global $USER, $CFG;

        $this->courseid   = $courseid;
        $this->context    = get_context_instance(CONTEXT_COURSE, $courseid);

        // get course grade tree
        $top_element = grade_category::fetch_course_tree($courseid, true);

        $this->elements = grade_seq::flatten($top_element, $category_grade_last, $nooutcomes);

        foreach ($this->elements as $key=>$unused) {
            $this->items[$this->elements[$key]['object']->id] =& $this->elements[$key]['object'];
        }
    }

    /**
     * Static recursive helper - makes the grade_item for category the last children
     *
     * @param array &$element The seed of the recursion
     * @param bool $category_grade_last category grade item is the last child
     * @param bool $nooutcomes Whether or not outcomes should be included
     *
     * @return array
     */
    public function flatten(&$element, $category_grade_last, $nooutcomes) {
        if (empty($element['children'])) {
            return array();
        }
        $children = array();

        foreach ($element['children'] as $sortorder=>$unused) {
            if ($nooutcomes and $element['type'] != 'category' and
                $element['children'][$sortorder]['object']->is_outcome_item()) {
                continue;
            }
            $children[] = $element['children'][$sortorder];
        }
        unset($element['children']);

        if ($category_grade_last and count($children) > 1) {
            $cat_item = array_shift($children);
            array_push($children, $cat_item);
        }

        $result = array();
        foreach ($children as $child) {
            if ($child['type'] == 'category') {
                $result = $result + grade_seq::flatten($child, $category_grade_last, $nooutcomes);
            } else {
                $child['eid'] = 'i'.$child['object']->id;
                $result[$child['object']->id] = $child;
            }
        }

        return $result;
    }

    /**
     * Parses the array in search of a given eid and returns a element object with
     * information about the element it has found.
     *
     * @param int $eid Gradetree Element ID
     *
     * @return object element
     */
    public function locate_element($eid) {
        // it is a grade - construct a new object
        if (strpos($eid, 'n') === 0) {
            if (!preg_match('/n(\d+)u(\d+)/', $eid, $matches)) {
                return null;
            }

            $itemid = $matches[1];
            $userid = $matches[2];

            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$itemid)) {
                return null;
            }

            // $gradea->id may be null - means does not exist yet
            $grade = new grade_grade(array('itemid'=>$itemid, 'userid'=>$userid));

            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'n'.$itemid.'u'.$userid,'object'=>$grade, 'type'=>'grade');

        } else if (strpos($eid, 'g') === 0) {
            $id = (int) substr($eid, 1);
            if (!$grade = grade_grade::fetch(array('id'=>$id))) {
                return null;
            }
            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$grade->itemid)) {
                return null;
            }
            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'g'.$id,'object'=>$grade, 'type'=>'grade');
        }

        // it is a category or item
        foreach ($this->elements as $element) {
            if ($element['eid'] == $eid) {
                return $element;
            }
        }

        return null;
    }
}

/**
 * This class represents a complete tree of categories, grade_items and final grades,
 * organises as an array primarily, but which can also be converted to other formats.
 * It has simple method calls with complex implementations, allowing for easy insertion,
 * deletion and moving of items and categories within the tree.
 *
 * @uses grade_structure
 * @package moodlecore
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_tree extends grade_structure {

    /**
     * The basic representation of the tree as a hierarchical, 3-tiered array.
     * @var object $top_element
     */
    public $top_element;

    /**
     * 2D array of grade items and categories
     * @var array $levels
     */
    public $levels;

    /**
     * Grade items
     * @var array $items
     */
    public $items;

    /**
     * Constructor, retrieves and stores a hierarchical array of all grade_category and grade_item
     * objects for the given courseid. Full objects are instantiated. Ordering sequence is fixed if needed.
     *
     * @param int   $courseid The Course ID
     * @param bool  $fillers include fillers and colspans, make the levels var "rectangular"
     * @param bool  $category_grade_last category grade item is the last child
     * @param array $collapsed array of collapsed categories
     * @param bool  $nooutcomes Whether or not outcomes should be included
     */
    public function grade_tree($courseid, $fillers=true, $category_grade_last=false,
                               $collapsed=null, $nooutcomes=false) {
        global $USER, $CFG;

        $this->courseid   = $courseid;
        $this->levels     = array();
        $this->context    = get_context_instance(CONTEXT_COURSE, $courseid);

        // get course grade tree
        $this->top_element = grade_category::fetch_course_tree($courseid, true);

        // collapse the categories if requested
        if (!empty($collapsed)) {
            grade_tree::category_collapse($this->top_element, $collapsed);
        }

        // no otucomes if requested
        if (!empty($nooutcomes)) {
            grade_tree::no_outcomes($this->top_element);
        }

        // move category item to last position in category
        if ($category_grade_last) {
            grade_tree::category_grade_last($this->top_element);
        }

        if ($fillers) {
            // inject fake categories == fillers
            grade_tree::inject_fillers($this->top_element, 0);
            // add colspans to categories and fillers
            grade_tree::inject_colspans($this->top_element);
        }

        grade_tree::fill_levels($this->levels, $this->top_element, 0);

    }

    /**
     * Static recursive helper - removes items from collapsed categories
     *
     * @param array &$element The seed of the recursion
     * @param array $collapsed array of collapsed categories
     *
     * @return void
     */
    public function category_collapse(&$element, $collapsed) {
        if ($element['type'] != 'category') {
            return;
        }
        if (empty($element['children']) or count($element['children']) < 2) {
            return;
        }

        if (in_array($element['object']->id, $collapsed['aggregatesonly'])) {
            $category_item = reset($element['children']); //keep only category item
            $element['children'] = array(key($element['children'])=>$category_item);

        } else {
            if (in_array($element['object']->id, $collapsed['gradesonly'])) { // Remove category item
                reset($element['children']);
                $first_key = key($element['children']);
                unset($element['children'][$first_key]);
            }
            foreach ($element['children'] as $sortorder=>$child) { // Recurse through the element's children
                grade_tree::category_collapse($element['children'][$sortorder], $collapsed);
            }
        }
    }

    /**
     * Static recursive helper - removes all outcomes
     *
     * @param array &$element The seed of the recursion
     *
     * @return void
     */
    public function no_outcomes(&$element) {
        if ($element['type'] != 'category') {
            return;
        }
        foreach ($element['children'] as $sortorder=>$child) {
            if ($element['children'][$sortorder]['type'] == 'item'
              and $element['children'][$sortorder]['object']->is_outcome_item()) {
                unset($element['children'][$sortorder]);

            } else if ($element['children'][$sortorder]['type'] == 'category') {
                grade_tree::no_outcomes($element['children'][$sortorder]);
            }
        }
    }

    /**
     * Static recursive helper - makes the grade_item for category the last children
     *
     * @param array &$element The seed of the recursion
     *
     * @return void
     */
    public function category_grade_last(&$element) {
        if (empty($element['children'])) {
            return;
        }
        if (count($element['children']) < 2) {
            return;
        }
        $first_item = reset($element['children']);
        if ($first_item['type'] == 'categoryitem' or $first_item['type'] == 'courseitem') {
            // the category item might have been already removed
            $order = key($element['children']);
            unset($element['children'][$order]);
            $element['children'][$order] =& $first_item;
        }
        foreach ($element['children'] as $sortorder => $child) {
            grade_tree::category_grade_last($element['children'][$sortorder]);
        }
    }

    /**
     * Static recursive helper - fills the levels array, useful when accessing tree elements of one level
     *
     * @param array &$levels The levels of the grade tree through which to recurse
     * @param array &$element The seed of the recursion
     * @param int   $depth How deep are we?
     * @return void
     */
    public function fill_levels(&$levels, &$element, $depth) {
        if (!array_key_exists($depth, $levels)) {
            $levels[$depth] = array();
        }

        // prepare unique identifier
        if ($element['type'] == 'category') {
            $element['eid'] = 'c'.$element['object']->id;
        } else if (in_array($element['type'], array('item', 'courseitem', 'categoryitem'))) {
            $element['eid'] = 'i'.$element['object']->id;
            $this->items[$element['object']->id] =& $element['object'];
        }

        $levels[$depth][] =& $element;
        $depth++;
        if (empty($element['children'])) {
            return;
        }
        $prev = 0;
        foreach ($element['children'] as $sortorder=>$child) {
            grade_tree::fill_levels($levels, $element['children'][$sortorder], $depth);
            $element['children'][$sortorder]['prev'] = $prev;
            $element['children'][$sortorder]['next'] = 0;
            if ($prev) {
                $element['children'][$prev]['next'] = $sortorder;
            }
            $prev = $sortorder;
        }
    }

    /**
     * Static recursive helper - makes full tree (all leafes are at the same level)
     *
     * @param array &$element The seed of the recursion
     * @param int   $depth How deep are we?
     *
     * @return int
     */
    public function inject_fillers(&$element, $depth) {
        $depth++;

        if (empty($element['children'])) {
            return $depth;
        }
        $chdepths = array();
        $chids = array_keys($element['children']);
        $last_child  = end($chids);
        $first_child = reset($chids);

        foreach ($chids as $chid) {
            $chdepths[$chid] = grade_tree::inject_fillers($element['children'][$chid], $depth);
        }
        arsort($chdepths);

        $maxdepth = reset($chdepths);
        foreach ($chdepths as $chid=>$chd) {
            if ($chd == $maxdepth) {
                continue;
            }
            for ($i=0; $i < $maxdepth-$chd; $i++) {
                if ($chid == $first_child) {
                    $type = 'fillerfirst';
                } else if ($chid == $last_child) {
                    $type = 'fillerlast';
                } else {
                    $type = 'filler';
                }
                $oldchild =& $element['children'][$chid];
                $element['children'][$chid] = array('object'=>'filler', 'type'=>$type,
                                                    'eid'=>'', 'depth'=>$element['object']->depth,
                                                    'children'=>array($oldchild));
            }
        }

        return $maxdepth;
    }

    /**
     * Static recursive helper - add colspan information into categories
     *
     * @param array &$element The seed of the recursion
     *
     * @return int
     */
    public function inject_colspans(&$element) {
        if (empty($element['children'])) {
            return 1;
        }
        $count = 0;
        foreach ($element['children'] as $key=>$child) {
            $count += grade_tree::inject_colspans($element['children'][$key]);
        }
        $element['colspan'] = $count;
        return $count;
    }

    /**
     * Parses the array in search of a given eid and returns a element object with
     * information about the element it has found.
     * @param int $eid Gradetree Element ID
     * @return object element
     */
    public function locate_element($eid) {
        // it is a grade - construct a new object
        if (strpos($eid, 'n') === 0) {
            if (!preg_match('/n(\d+)u(\d+)/', $eid, $matches)) {
                return null;
            }

            $itemid = $matches[1];
            $userid = $matches[2];

            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$itemid)) {
                return null;
            }

            // $gradea->id may be null - means does not exist yet
            $grade = new grade_grade(array('itemid'=>$itemid, 'userid'=>$userid));

            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'n'.$itemid.'u'.$userid,'object'=>$grade, 'type'=>'grade');

        } else if (strpos($eid, 'g') === 0) {
            $id = (int) substr($eid, 1);
            if (!$grade = grade_grade::fetch(array('id'=>$id))) {
                return null;
            }
            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$grade->itemid)) {
                return null;
            }
            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'g'.$id,'object'=>$grade, 'type'=>'grade');
        }

        // it is a category or item
        foreach ($this->levels as $row) {
            foreach ($row as $element) {
                if ($element['type'] == 'filler') {
                    continue;
                }
                if ($element['eid'] == $eid) {
                    return $element;
                }
            }
        }

        return null;
    }

    /**
     * Returns a well-formed XML representation of the grade-tree using recursion.
     *
     * @param array  $root The current element in the recursion. If null, starts at the top of the tree.
     * @param string $tabs The control character to use for tabs
     *
     * @return string $xml
     */
    public function exporttoxml($root=null, $tabs="\t") {
        $xml = null;
        $first = false;
        if (is_null($root)) {
            $root = $this->top_element;
            $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
            $xml .= "<gradetree>\n";
            $first = true;
        }

        $type = 'undefined';
        if (strpos($root['object']->table, 'grade_categories') !== false) {
            $type = 'category';
        } else if (strpos($root['object']->table, 'grade_items') !== false) {
            $type = 'item';
        } else if (strpos($root['object']->table, 'grade_outcomes') !== false) {
            $type = 'outcome';
        }

        $xml .= "$tabs<element type=\"$type\">\n";
        foreach ($root['object'] as $var => $value) {
            if (!is_object($value) && !is_array($value) && !empty($value)) {
                $xml .= "$tabs\t<$var>$value</$var>\n";
            }
        }

        if (!empty($root['children'])) {
            $xml .= "$tabs\t<children>\n";
            foreach ($root['children'] as $sortorder => $child) {
                $xml .= $this->exportToXML($child, $tabs."\t\t");
            }
            $xml .= "$tabs\t</children>\n";
        }

        $xml .= "$tabs</element>\n";

        if ($first) {
            $xml .= "</gradetree>";
        }

        return $xml;
    }

    /**
     * Returns a JSON representation of the grade-tree using recursion.
     *
     * @param array $root The current element in the recursion. If null, starts at the top of the tree.
     * @param string $tabs Tab characters used to indent the string nicely for humans to enjoy
     *
     * @return string
     */
    public function exporttojson($root=null, $tabs="\t") {
        $json = null;
        $first = false;
        if (is_null($root)) {
            $root = $this->top_element;
            $first = true;
        }

        $name = '';


        if (strpos($root['object']->table, 'grade_categories') !== false) {
            $name = $root['object']->fullname;
            if ($name == '?') {
                $name = $root['object']->get_name();
            }
        } else if (strpos($root['object']->table, 'grade_items') !== false) {
            $name = $root['object']->itemname;
        } else if (strpos($root['object']->table, 'grade_outcomes') !== false) {
            $name = $root['object']->itemname;
        }

        $json .= "$tabs {\n";
        $json .= "$tabs\t \"type\": \"{$root['type']}\",\n";
        $json .= "$tabs\t \"name\": \"$name\",\n";

        foreach ($root['object'] as $var => $value) {
            if (!is_object($value) && !is_array($value) && !empty($value)) {
                $json .= "$tabs\t \"$var\": \"$value\",\n";
            }
        }

        $json = substr($json, 0, strrpos($json, ','));

        if (!empty($root['children'])) {
            $json .= ",\n$tabs\t\"children\": [\n";
            foreach ($root['children'] as $sortorder => $child) {
                $json .= $this->exportToJSON($child, $tabs."\t\t");
            }
            $json = substr($json, 0, strrpos($json, ','));
            $json .= "\n$tabs\t]\n";
        }

        if ($first) {
            $json .= "\n}";
        } else {
            $json .= "\n$tabs},\n";
        }

        return $json;
    }

    /**
     * Returns the array of levels
     *
     * @return array
     */
    public function get_levels() {
        return $this->levels;
    }

    /**
     * Returns the array of grade items
     *
     * @return array
     */
    public function get_items() {
        return $this->items;
    }

    /**
     * Returns a specific Grade Item
     *
     * @param int $itemid The ID of the grade_item object
     *
     * @return grade_item
     */
    public function get_item($itemid) {
        if (array_key_exists($itemid, $this->items)) {
            return $this->items[$itemid];
        } else {
            return false;
        }
    }
}

/**
 * Local shortcut function for creating an edit/delete button for a grade_* object.
 * @param string $type 'edit' or 'delete'
 * @param int $courseid The Course ID
 * @param grade_* $object The grade_* object
 * @return string html
 */
function grade_button($type, $courseid, $object) {
    global $CFG, $OUTPUT;
    if (preg_match('/grade_(.*)/', get_class($object), $matches)) {
        $objectidstring = $matches[1] . 'id';
    } else {
        throw new coding_exception('grade_button() only accepts grade_* objects as third parameter!');
    }

    $strdelete = get_string('delete');
    $stredit   = get_string('edit');

    if ($type == 'delete') {
        $url = new moodle_url('index.php', array('id' => $courseid, $objectidstring => $object->id, 'action' => 'delete', 'sesskey' => sesskey()));
    } else if ($type == 'edit') {
        $url = new moodle_url('edit.php', array('courseid' => $courseid, 'id' => $object->id));
    }

    return $OUTPUT->action_icon($url, new pix_icon('t/'.$type, ${'str'.$type}));

}

/**
 * This method adds settings to the settings block for the grade system and its
 * plugins
 *
 * @global moodle_page $PAGE
 */
function grade_extend_settings($plugininfo, $courseid) {
    global $PAGE;

    $gradenode = $PAGE->settingsnav->prepend(get_string('gradeadministration', 'grades'), null, navigation_node::TYPE_CONTAINER);

    $strings = array_shift($plugininfo);

    if ($reports = grade_helper::get_plugins_reports($courseid)) {
        foreach ($reports as $report) {
            $gradenode->add($report->string, $report->link, navigation_node::TYPE_SETTING, null, $report->id, new pix_icon('i/report', ''));
        }
    }

    if ($imports = grade_helper::get_plugins_import($courseid)) {
        $importnode = $gradenode->add($strings['import'], null, navigation_node::TYPE_CONTAINER);
        foreach ($imports as $import) {
            $importnode->add($import->string, $import->link, navigation_node::TYPE_SETTING, null, $import->id, new pix_icon('i/restore', ''));
        }
    }

    if ($exports = grade_helper::get_plugins_export($courseid)) {
        $exportnode = $gradenode->add($strings['export'], null, navigation_node::TYPE_CONTAINER);
        foreach ($exports as $export) {
            $exportnode->add($export->string, $export->link, navigation_node::TYPE_SETTING, null, $export->id, new pix_icon('i/backup', ''));
        }
    }

    if ($setting = grade_helper::get_info_manage_settings($courseid)) {
        $gradenode->add(get_string('coursegradesettings', 'grades'), $setting->link, navigation_node::TYPE_SETTING, null, $setting->id, new pix_icon('i/settings', ''));
    }

    if ($preferences = grade_helper::get_plugins_report_preferences($courseid)) {
        $preferencesnode = $gradenode->add(get_string('myreportpreferences', 'grades'), null, navigation_node::TYPE_CONTAINER);
        foreach ($preferences as $preference) {
            $preferencesnode->add($preference->string, $preference->link, navigation_node::TYPE_SETTING, null, $preference->id, new pix_icon('i/settings', ''));
        }
    }

    if ($letters = grade_helper::get_info_letters($courseid)) {
        $letters = array_shift($letters);
        $gradenode->add($strings['letter'], $letters->link, navigation_node::TYPE_SETTING, null, $letters->id, new pix_icon('i/settings', ''));
    }

    if ($outcomes = grade_helper::get_info_outcomes($courseid)) {
        $outcomes = array_shift($outcomes);
        $gradenode->add($strings['outcome'], $outcomes->link, navigation_node::TYPE_SETTING, null, $outcomes->id, new pix_icon('i/outcomes', ''));
    }

    if ($scales = grade_helper::get_info_scales($courseid)) {
        $gradenode->add($strings['scale'], $scales->link, navigation_node::TYPE_SETTING, null, $scales->id, new pix_icon('i/scales', ''));
    }

    if ($categories = grade_helper::get_info_edit_structure($courseid)) {
        $categoriesnode = $gradenode->add(get_string('categoriesanditems','grades'), null, navigation_node::TYPE_CONTAINER);
        foreach ($categories as $category) {
            $categoriesnode->add($category->string, $category->link, navigation_node::TYPE_SETTING, null, $category->id, new pix_icon('i/report', ''));
        }
    }

    if ($gradenode->contains_active_node()) {
        // If the gradenode is active include the settings base node (gradeadministration) in
        // the navbar, typcially this is ignored.
        $PAGE->navbar->includesettingsbase = true;

        // If we can get the course admin node make sure it is closed by default
        // as in this case the gradenode will be opened
        if ($coursenode = $PAGE->settingsnav->get('courseadmin', navigation_node::TYPE_COURSE)){
            $coursenode->make_inactive();
            $coursenode->forceopen = false;
        }
    }
}

/**
 * Grade helper class
 *
 * This class provides several helpful functions that work irrespective of any
 * current state.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class grade_helper {
    /**
     * Cached manage settings info {@see get_info_settings}
     * @var grade_plugin_info|false
     */
    protected static $managesetting = null;
    /**
     * Cached grade report plugins {@see get_plugins_reports}
     * @var array|false
     */
    protected static $gradereports = null;
    /**
     * Cached grade report plugins preferences {@see get_info_scales}
     * @var array|false
     */
    protected static $gradereportpreferences = null;
    /**
     * Cached scale info {@see get_info_scales}
     * @var grade_plugin_info|false
     */
    protected static $scaleinfo = null;
    /**
     * Cached outcome info {@see get_info_outcomes}
     * @var grade_plugin_info|false
     */
    protected static $outcomeinfo = null;
    /**
     * Cached info on edit structure {@see get_info_edit_structure}
     * @var array|false
     */
    protected static $edittree = null;
    /**
     * Cached leftter info {@see get_info_letters}
     * @var grade_plugin_info|false
     */
    protected static $letterinfo = null;
    /**
     * Cached grade import plugins {@see get_plugins_import}
     * @var array|false
     */
    protected static $importplugins = null;
    /**
     * Cached grade export plugins {@see get_plugins_export}
     * @var array|false
     */
    protected static $exportplugins = null;
    /**
     * Cached grade plugin strings
     * @var array
     */
    protected static $pluginstrings = null;

    /**
     * Gets strings commonly used by the describe plugins
     *
     * report => get_string('view'),
     * edittree => get_string('edittree', 'grades'),
     * scale => get_string('scales'),
     * outcome => get_string('outcomes', 'grades'),
     * letter => get_string('letters', 'grades'),
     * export => get_string('export', 'grades'),
     * import => get_string('import'),
     * preferences => get_string('mypreferences', 'grades'),
     * settings => get_string('settings')
     *
     * @return array
     */
    public static function get_plugin_strings() {
        if (self::$pluginstrings === null) {
            self::$pluginstrings = array(
                'report' => get_string('view'),
                'edittree' => get_string('edittree', 'grades'),
                'scale' => get_string('scales'),
                'outcome' => get_string('outcomes', 'grades'),
                'letter' => get_string('letters', 'grades'),
                'export' => get_string('export', 'grades'),
                'import' => get_string('import'),
                'preferences' => get_string('mypreferences', 'grades'),
                'settings' => get_string('settings')
            );
        }
        return self::$pluginstrings;
    }
    /**
     * Get grade_plugin_info object for managing settings if the user can
     *
     * @param int $courseid
     * @return grade_plugin_info
     */
    public static function get_info_manage_settings($courseid) {
        if (self::$managesetting !== null) {
            return self::$managesetting;
        }
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        if (has_capability('moodle/course:update', $context)) {
            self::$managesetting = new grade_plugin_info('coursesettings', new moodle_url('/grade/edit/settings/index.php', array('id'=>$courseid)), get_string('course'));
        } else {
            self::$managesetting = false;
        }
        return self::$managesetting;
    }
    /**
     * Returns an array of plugin reports as grade_plugin_info objects
     *
     * @param int $courseid
     * @return array
     */
    public static function get_plugins_reports($courseid) {
        global $SITE;

        if (self::$gradereports !== null) {
            return self::$gradereports;
        }
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $gradereports = array();
        $gradepreferences = array();
        foreach (get_plugin_list('gradereport') as $plugin => $plugindir) {
            //some reports make no sense if we're not within a course
            if ($courseid==$SITE->id && ($plugin=='grader' || $plugin=='user')) {
                continue;
            }

            // Remove ones we can't see
            if (!has_capability('gradereport/'.$plugin.':view', $context)) {
                continue;
            }

            $pluginstr = get_string('pluginname', 'gradereport_'.$plugin);
            $url = new moodle_url('/grade/report/'.$plugin.'/index.php', array('id'=>$courseid));
            $gradereports[$plugin] = new grade_plugin_info($plugin, $url, $pluginstr);

            // Add link to preferences tab if such a page exists
            if (file_exists($plugindir.'/preferences.php')) {
                $url = new moodle_url('/grade/report/'.$plugin.'/preferences.php', array('id'=>$courseid));
                $gradepreferences[$plugin] = new grade_plugin_info($plugin, $url, $pluginstr);
            }
        }
        if (count($gradereports) == 0) {
            $gradereports = false;
            $gradepreferences = false;
        } else if (count($gradepreferences) == 0) {
            $gradepreferences = false;
            asort($gradereports);
        } else {
            asort($gradereports);
            asort($gradepreferences);
        }
        self::$gradereports = $gradereports;
        self::$gradereportpreferences = $gradepreferences;
        return self::$gradereports;
    }
    /**
     * Returns an array of grade plugin report preferences for plugin reports that
     * support preferences
     * @param int $courseid
     * @return array
     */
    public static function get_plugins_report_preferences($courseid) {
        if (self::$gradereportpreferences !== null) {
            return self::$gradereportpreferences;
        }
        self::get_plugins_reports($courseid);
        return self::$gradereportpreferences;
    }
    /**
     * Get information on scales
     * @param int $courseid
     * @return grade_plugin_info
     */
    public static function get_info_scales($courseid) {
        if (self::$scaleinfo !== null) {
            return self::$scaleinfo;
        }
        if (has_capability('moodle/course:managescales', get_context_instance(CONTEXT_COURSE, $courseid))) {
            $url = new moodle_url('/grade/edit/scale/index.php', array('id'=>$courseid));
            self::$scaleinfo = new grade_plugin_info('scale', $url, get_string('view'));
        } else {
            self::$scaleinfo = false;
        }
        return self::$scaleinfo;
    }
    /**
     * Get information on outcomes
     * @param int $courseid
     * @return grade_plugin_info
     */
    public static function get_info_outcomes($courseid) {
        global $CFG, $SITE;

        if (self::$outcomeinfo !== null) {
            return self::$outcomeinfo;
        }
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $canmanage = has_capability('moodle/grade:manage', $context);
        $canupdate = has_capability('moodle/course:update', $context);
        if (!empty($CFG->enableoutcomes) && ($canmanage || $canupdate)) {
            $outcomes = array();
            if ($canupdate) {
                if ($courseid!=$SITE->id) {
                    $url = new moodle_url('/grade/edit/outcome/course.php', array('id'=>$courseid));
                    $outcomes['course'] = new grade_plugin_info('course', $url, get_string('outcomescourse', 'grades'));
                }
                $url = new moodle_url('/grade/edit/outcome/index.php', array('id'=>$courseid));
                $outcomes['edit'] = new grade_plugin_info('edit', $url, get_string('editoutcomes', 'grades'));
                $url = new moodle_url('/grade/edit/outcome/import.php', array('courseid'=>$courseid));
                $outcomes['import'] = new grade_plugin_info('import', $url, get_string('importoutcomes', 'grades'));
            } else {
                if ($courseid!=$SITE->id) {
                    $url = new moodle_url('/grade/edit/outcome/course.php', array('id'=>$courseid));
                    $outcomes['edit'] = new grade_plugin_info('edit', $url, get_string('outcomescourse', 'grades'));
                }
            }
            self::$outcomeinfo = $outcomes;
        } else {
            self::$outcomeinfo = false;
        }
        return self::$outcomeinfo;
    }
    /**
     * Get information on editing structures
     * @param int $courseid
     * @return array
     */
    public static function get_info_edit_structure($courseid) {
        if (self::$edittree !== null) {
            return self::$edittree;
        }
        if (has_capability('moodle/grade:manage', get_context_instance(CONTEXT_COURSE, $courseid))) {
            $url = new moodle_url('/grade/edit/tree/index.php', array('sesskey'=>sesskey(), 'showadvanced'=>'0', 'id'=>$courseid));
            self::$edittree = array(
                'simpleview' => new grade_plugin_info('simpleview', $url, get_string('simpleview', 'grades')),
                'fullview' => new grade_plugin_info('fullview', new moodle_url($url, array('showadvanced'=>'1')), get_string('fullview', 'grades'))
            );
        } else {
            self::$edittree = false;
        }
        return self::$edittree;
    }
    /**
     * Get information on letters
     * @param int $courseid
     * @return array
     */
    public static function get_info_letters($courseid) {
        if (self::$letterinfo !== null) {
            return self::$letterinfo;
        }
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $canmanage = has_capability('moodle/grade:manage', $context);
        $canmanageletters = has_capability('moodle/grade:manageletters', $context);
        if ($canmanage || $canmanageletters) {
            self::$letterinfo = array(
                'view' => new grade_plugin_info('view', new moodle_url('/grade/edit/letter/index.php', array('id'=>$context->id)), get_string('view')),
                'edit' => new grade_plugin_info('edit', new moodle_url('/grade/edit/letter/index.php', array('edit'=>1,'id'=>$context->id)), get_string('edit'))
            );
        } else {
            self::$letterinfo = false;
        }
        return self::$letterinfo;
    }
    /**
     * Get information import plugins
     * @param int $courseid
     * @return array
     */
    public static function get_plugins_import($courseid) {
        global $CFG;

        if (self::$importplugins !== null) {
            return self::$importplugins;
        }
        $importplugins = array();
        $context = get_context_instance(CONTEXT_COURSE, $courseid);

        if (has_capability('moodle/grade:import', $context)) {
            foreach (get_plugin_list('gradeimport') as $plugin => $plugindir) {
                if (!has_capability('gradeimport/'.$plugin.':view', $context)) {
                    continue;
                }
                $pluginstr = get_string('pluginname', 'gradeimport_'.$plugin);
                $url = new moodle_url('/grade/import/'.$plugin.'/index.php', array('id'=>$courseid));
                $importplugins[$plugin] = new grade_plugin_info($plugin, $url, $pluginstr);
            }


            if ($CFG->gradepublishing) {
                $url = new moodle_url('/grade/import/keymanager.php', array('id'=>$courseid));
                $importplugins['keymanager'] = new grade_plugin_info('keymanager', $url, get_string('keymanager', 'grades'));
            }
        }

        if (count($importplugins) > 0) {
            asort($importplugins);
            self::$importplugins = $importplugins;
        } else {
            self::$importplugins = false;
        }
        return self::$importplugins;
    }
    /**
     * Get information export plugins
     * @param int $courseid
     * @return array
     */
    public static function get_plugins_export($courseid) {
        global $CFG;

        if (self::$exportplugins !== null) {
            return self::$exportplugins;
        }
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $exportplugins = array();
        if (has_capability('moodle/grade:export', $context)) {
            foreach (get_plugin_list('gradeexport') as $plugin => $plugindir) {
                if (!has_capability('gradeexport/'.$plugin.':view', $context)) {
                    continue;
                }
                $pluginstr = get_string('pluginname', 'gradeexport_'.$plugin);
                $url = new moodle_url('/grade/export/'.$plugin.'/index.php', array('id'=>$courseid));
                $exportplugins[$plugin] = new grade_plugin_info($plugin, $url, $pluginstr);
            }

            if ($CFG->gradepublishing) {
                $url = new moodle_url('/grade/export/keymanager.php', array('id'=>$courseid));
                $exportplugins['keymanager'] = new grade_plugin_info('keymanager', $url, get_string('keymanager', 'grades'));
            }
        }
        if (count($exportplugins) > 0) {
            asort($exportplugins);
            self::$exportplugins = $exportplugins;
        } else {
            self::$exportplugins = false;
        }
        return self::$exportplugins;
    }
}
