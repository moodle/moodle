<?php
 ///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

 /**
  * File in whiich the grade_report_visual class is defined.
  * @package gradebook
  */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

/// Require all visualization classes
foreach (glob($CFG->dirroot . '/grade/report/visual/visualizations/visual_*.php') as $filename) {
   require_once($filename);
}

/**
 * Class providing the API for the visual report, including harvesters,
 * reports, and adaptor methods for turing grades in to visualizations.
 * @uses grade_report
 * @package gradebook
 */
class grade_report_visual extends grade_report {
    /**
     * Capability to view hidden items.
     * @var bool $canviewhidden
     */
    public $canviewhidden;

    /**
     * Array of visualizations available.
     * @var array $visualizations
     */
    private static $visualizations = array();

    /**
     * Grade objects of users in the course
     * @var array $grades
     */
    public $grades = array();

    /**
     * Array of data to be sent to the flash front end.
     * Data is generated from grades as needed using
     * report_data function and $visid.
     * @var array $visdata
     */
    private $visdata = array();

    /**
     * The id of the visualization that has been selected.
     * @var string $visid
     */
    public $visid;

    /**
     * Array of flashvars to be sent to flash front end.
     * @var array $flashvars
     */
    private $flashvars = array();


    private $flashvarshtml;

    /**
     * The html of the report to output.
     * @var string $html
     */
    public $html;


    /**
     * Constructor. Initialises grade_tree, sets up group, baseurl
     * and pbarurl.
     * @param int $courseid the coures id for the report
     * @param object $gpr grade plugin tracking object
     * @context string $context
     */
    public function __construct($courseid, $gpr, $context, $visid=null) {
        global $CFG;
        parent::__construct($courseid, $gpr, $context, null);

        $this->canviewhidden = has_capability('moodle/grade:viewhidden', get_context_instance(CONTEXT_COURSE, $this->course->id));

        /// Set up urls
        $this->baseurl = 'index.php?id=' . $this->courseid;
        $this->pbarurl = 'index.php?id=' . $this->courseid;

        /// Set the position of the aggregation categorie based on pref
        $switch = $this->get_pref('visual', 'aggregationposition');
        if ($switch == '' && isset($CFG->grade_aggregationposition)) {
            $switch = grade_get_setting($this->courseid, 'aggregationposition', $CFG->grade_aggregationposition);
        }

        /// Build grade tree
        $this->gtree = new grade_tree($this->courseid, false, $switch);

        $this->load_visualizations();

        if(!is_null($visid) && !empty($visid) && array_key_exists($visid,  grade_report_visual::$visualizations)) {
            $this->visid = $visid;
        } else {
            $keys = array_keys(grade_report_visual::$visualizations);
            $this->visid = $keys[0];
        }

        $this->set_up_flashvars();

        /// Set up Groups
        if($this->visid && grade_report_visual::get_visualization($this->visid)->usegroups) {
            $this->setup_groups();
        }
    }

    /// Added to keep grade_report happy
    public function process_data($data){}
    public function process_action($target, $action){}

    /**
     * Load all the visualization classes into $visualizations.
     * Looks in /visualizations and trys to make an instence of any
     * class that is in a file that starts with visual_ and extends
     * visualization directly.
     * @param bool $return if true return visualizations array, else store in $this->visualizations
     * @param object $context the context to use for checking if a user has access to view a visualization.
     * @returns array array of clases that extend visualization
     */
    private function load_visualizations($return=false, $context = null) {
        global $CFG;

        if($context == null) {
            $context = $this->context;
        }

        if(!isset(grade_report_visual::$visualizations) || is_null(grade_report_visual::$visualizations) || empty(grade_report_visual::$visualizations)) {
            $visualizations = array();

            foreach (glob($CFG->dirroot . '/grade/report/visual/visualizations/visual_*.php') as $path) {
                $filename = substr(basename($path, '.php'), 7);

                if(class_exists($filename) && get_parent_class($class = new $filename) == 'visualization' ) {
                    if($class->capability == null || has_capability($class->capability, $context)) {
                        $visualizations[$filename] = $class;
                    }
                }
            }

            if($return) {
                return $visualizations;
            } else {
                grade_report_visual::$visualizations = $visualizations;
            }
        } else if($return) {
            return grade_report_visual::$visualizations;
        }
    }

    /**
     * Returns the current visualizations being used in the report
     * or if none are set, it returns the them as whould be loaded
     *  by load_visualizations.
     * @param object $context the context to use for checking if a user has access to view a visualization.
     * @returns array array of classes that extend visualization
     */
    public function get_visualizations($context=null) {
        if(!isset(grade_report_visual::$visualizations) || is_null(grade_report_visual::$visualizations) || empty(grade_report_visual::$visualizations)) {
            return grade_report_visual::load_visualizations(true, $context);
        } else {
            return grade_report_visual::$visualizations;
        }
    }

    /**
     * Returns a specified visulization object.
     * @param object $context the context to use for checking if a user has access to view the visualization.
     * @returns object visulization object or null if the visulization does not exist or the user does not have access to view it in the given context.
     */
    public function get_visualization($filename, $context=null) {
        if($context == null) {
            $context = $this->context;
        }

        if(!isset(grade_report_visual::$visualizations) || is_null(grade_report_visual::$visualizations) || empty(grade_report_visual::$visualizations) || is_null(grade_report_visual::$visualizations[$filename])) {
            if(class_exists($filename) && get_parent_class($class = new $filename) == 'visualization' ) {
                if($class->capability == null || has_capability($class->capability, $context)) {
                    return $class;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return grade_report_visual::$visualizations[$filename];
        }
    }

    /**
     * Based on load user function from grader report.
     * Pulls out the userids of the users to be used in the stats.
     * @return array array of user ids to use in stats
     */
    public function load_users() {
        global $CFG, $DB;

        if(isset($DB) && !is_null($DB)) {
            $params = array();
            list($usql, $gbr_params) = $DB->get_in_or_equal(explode(',', $this->gradebookroles));

            $sql = "SELECT u.id, u.firstname, u.lastname, u.imagealt, u.picture, u.idnumber, u.username
                    FROM {user} u
                        JOIN {role_assignments} ra ON u.id = ra.userid
                        $this->groupsql
                    WHERE ra.roleid $usql
                        $this->groupwheresql
                        AND ra.contextid ".get_related_contexts_string($this->context);

            $params = array_merge($gbr_params, $this->groupwheresql_params);
            $this->users = $DB->get_records_sql($sql, $params);
        } else {
            $sql = "SELECT u.id, u.firstname, u.lastname, u.imagealt, u.picture, u.idnumber
                      FROM {user) u
                           JOIN {role_assignments} ra ON u.id = ra.userid
                           $this->groupsql
                     WHERE ra.roleid in ($this->gradebookroles)
                           $this->groupwheresql
                           AND ra.contextid ".get_related_contexts_string($this->context);

            $this->users = $DB->get_records_sql($sql);
        }

        if (empty($this->users)) {
            $this->userselect = '';
            $this->users = array();
            $this->userselect_params = array();
        } else {
            if(isset($DB) && !is_null($DB)) {
                list($usql, $params) = $DB->get_in_or_equal(array_keys($this->users));
                $this->userselect = "AND g.userid $usql";
                $this->userselect_params = $params;
            }else{
                $this->userselect = 'AND g.userid in ('.implode(',', array_keys($this->users)).')';
            }
        }

        return $this->users;
    }

    /**
     * Harvest the grades from the data base and build the finalgrades array.
     * Filters out hidden, locked and null grades based on users settings.
     * Partly based on grader reports load_final_grades function.
     */
    public function harvest_data() {
        global $CFG, $DB;

        $params = array();

        if(isset($DB) && !is_null($DB)) {
            $params = array_merge(array($this->courseid), $this->userselect_params);

            /// please note that we must fetch all grade_grades fields if we want to contruct grade_grade object from it!
            $sql = "SELECT g.*
                  FROM {grade_items} gi,
                       {grade_grades} g
                 WHERE g.itemid = gi.id AND gi.courseid = ? {$this->userselect}";

            $grades = $DB->get_records_sql($sql, $params);
        } else {
            /// please note that we must fetch all grade_grades fields if we want to contruct grade_grade object from it!
            $sql = "SELECT g.*
                  FROM {grade_items} gi,
                       {grade_grades} g
                 WHERE g.itemid = gi.id AND gi.courseid = {$this->courseid} {$this->userselect}";

            $grades = get_records_sql($sql);
        }

        $userids = array_keys($this->users);

        if ($grades) {
            foreach ($grades as $graderec) {
                if (in_array($graderec->userid, $userids) && array_key_exists($graderec->itemid, $this->gtree->items)) { // some items may not be present!!
                    $grade = new grade_grade($graderec, false);
                    $grade->grade_item =& $this->gtree->items[$graderec->itemid];
                    if((($grade->is_hidden() && $this->canviewhidden && ($this->get_pref('visual', 'usehidden') || is_null($this->get_pref('visual', 'usehidden')))) || !$grade->is_hidden())
                        && (($grade->is_locked() && ($this->get_pref('visual', 'uselocked') || is_null($this->get_pref('visual', 'uselocked')))) || !$grade->is_locked())) {
                            $this->grades[$graderec->itemid][$graderec->userid] = $grade;
                    }
                }
            }
        }

        if($this->get_pref('visual', 'incompleasmin')) {
        /// prefil grades that do not exist yet
            foreach ($userids as $userid) {
                foreach ($this->gtree->items as $itemid=>$unused) {
                    if (!isset($this->grades[$itemid][$userid])) {
                        $grade = new grade_grade();
                        $grade->itemid = $itemid;
                        $grade->userid = $userid;
                        $grade->grade_item =& $this->gtree->items[$itemid]; // db caching
                        $grade->finalgrade = $this->gtree->items[$itemid]->grademin;

                        if((($grade->is_hidden() && $this->canviewhidden && ($this->get_pref('visual', 'usehidden') || is_null($this->get_pref('visual', 'usehidden')))) || !$grade->is_hidden())
                            && (($grade->is_locked() && ($this->get_pref('visual', 'uselocked') || is_null($this->get_pref('visual', 'uselocked')))) || !$grade->is_locked())) {
                                $this->grades[$itemid][$userid] = $grade;
                        }
                    }
                }
            }
        }
    }

    /**
    * Generates the data for a visualization or all visualizations to be sent to
    * the front end.
    * @param bool $all if true loads the data for all visualizations into visdata, else loads only the visualization selected by visid.
    */
    public function report_data($all = false) {
        if($all) {
            $visuals = $this->get_visualizations();

            foreach($visuals as $key=>$visual) {
                $this->visdata[$key] = $visual->report_data($this);
            }
        } else {
            $visual = $this->get_visualization($this->visid);
            $this->visdata[$this->visid] = $visual->report_data($this);
        }
    }

    /**
     * Generates the values of the flashvars that will be sent to the
     * flash front end and encodes them as html in flashvarshtml so
     * they can be droped in to the flash tag.
     */
    private function set_up_flashvars() {
        global $USER, $SESSION, $CFG;

        $flashvars = array();
        $flashvars['username'] = $USER->username;
        $flashvars['userid'] = $USER->id;
        $flashvars['courseid'] = $this->courseid;
        $flashvars['coursefullname'] = $this->course->fullname;
        $flashvars['courseshortname'] = $this->course->shortname;
        $flashvars['sessionid'] = session_id();
        $flashvars['sessioncookie'] = $CFG->sessioncookie;
        // $flashvars['sessiontest'] = $SESSION->session_test; // This session variable no longer exists in 2.0
        $flashvars['dirroot'] = $CFG->dirroot;
        $flashvars['wwwroot'] = $CFG->wwwroot;
        $flashvars['visid'] = $this->visid;

        foreach($flashvars as $key=>$val) {
            $this->flashvarshtml .= $key. '=' . addslashes(urlencode(strip_tags($val))) . '&';
        }
        $this->flashvarshtml = substr($this->flashvarshtml, 0, strlen($this->flashvarshtml) - 1);
    }

    /**
     * Returns or prints html for the report.
     * HTML produced is based on flex.php and the values in this calss.
     * @param bool $printerversion if true the HTML will be for the printerversion of the report.
     * @param bool $return if true the HTML will be returned as a string rather then echoed.
     * @returns string if $return is true a string of the HTML will be retruned.
     */
    public function adapt_html($printerversion = false, $return = false) {
        global $CFG;

        $flashvarshtml = $this->flashvarshtml;
        $visual = $this->get_visualization($this->visid);

        if($printerversion) {
            $flashvarshtml .= '&printerversion=true';
        } else {
            $flashvarshtml .= '&printerversion=false';
        }

        ob_start();
        require($CFG->dirroot.'/grade/report/visual/flex.php');
        $this->html = ob_get_clean();

        if($return) {
            return $this->html;
        } else {
            echo $this->html;
        }
    }

    /**
     * Adapts $visdata to a fromat that can be read by the flash front end.
     * TODO: Have options for diffrent fromats rather then just tab format.
     * @param bool $return if true the data is returned in a string, else it is echoed.
     * @returns string if $return is true a string of the adapted data will be returned.
     */
    public function adapt_data($return = false) {
        if($return) {
            return $this->get_tab(true);
        } else {
            echo $this->get_tab(true);
        }
    }

    /**
     * Generates the HTML for a selector feild witch lists the visualizations avaible
     * to the current user.
     * @param bool $return if true the HTML is retruned as a string, otherwise it is echoed.
     * @returns string if $return is true, the HTML is returned as a string.
     */
    public function visualization_selector($return = false) {
        $visuals = $this->get_visualizations();
        $visualmenu = array();
        $vislabel = $this->get_lang_string('visualselector', 'gradereport_visual');

        foreach($visuals as $key=>$visual) {
            $visualmenu[$key] = format_string($visual->name);
        }

        if(count($visualmenu) > 1) {
            $selectorhtml =  popup_form($this->pbarurl . '&amp;visid=', $visualmenu, 'selectvisual', $this->visid, '', '', '', true, 'self', $vislabel);
        } else {
            $selectorhtml = '';
        }

        if($return) {
            return $selectorhtml;
        } else {
            echo $selectorhtml;
        }
    }

    /**
     * Truncates a string to $max chars long and adds $end to the string
     * if it is more then $max chars. The resulting string will allways be at
     * most $max chars long. $end must be shorter then $max long.
     * @param string $string the string to truncate.
     * @param int $max the maxium length of the string.
     * @param string $end a string that will be appened to the end of the truncated string if it is over $max in length.
     * @returns string returns the truncated string if $string is over $max in length, other wise returns $string.
     */
    public static function truncate($string, $max = 25, $end = '...') {
        if(strlen($string) <= $max) {
            return $string;
        }

        return substr_replace($string, $end, $max - strlen($end));
    }

    /**
     * Returns the data for a visulasation in tab format.
     * @param boolean $return if true return a string, other wise echo the data.
     * @return string If return is set to true, returns a string of the data in tab format.
     */
    private function get_tab($return = true) {
        if ($return) {
            return $this->tab_encode($this->visdata[$this->visid]);
        } else {
            echo $this->tab_encode($this->visdata[$this->visid]);
        }
    }

    /**
     * Encodes an array to a string using the tab format.
     *@param array $data the array to encode.
     *@return string a string encoded in tab format based on the given array.
     */
    public static function tab_encode(array $data, $useindexes = false) {
        $outstring = '';

        foreach($data as $key=>$row) {
            if(is_array($row)) {
                if($useindexes) {
                    $outstring .= $key . "\t";
                }

                foreach($row as $col) {
                    $outstring .= $col . "\t";
                }

                $outstring[strlen($outstring) - 1] = "\n";
            } else {
                if($useindexes) {
                    $outstring .= $key . "\t" . $row . "\n";
                } else {
                    $outstring .= $row . "\n";
                }
            }
        }

        return $outstring;
    }
}
?>
