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
  * File in which the grade_report_stats class is defined.
  * @package gradebook
  */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

foreach (glob($CFG->dirroot . '/grade/report/stats/statistics/stat_*.php') as $filename) {
   require_once($filename);
}

/**
 * Class providing the API for the stats report, including harvesters,
 * reports, and adaptor methods for turing grades in to statistics.
 * @uses grade_report
 * @package gradebook
 */
class grade_report_stats extends grade_report {
    /**
     * Capability to view hidden items.
     * @var bool $canviewhidden
     */
    private $canviewhidden;

    /**
     * Grade objects of users in the course
     * @var array $grades
     */
    private $grades = array();

    /**
     * Array of users final grades affter filtered based on
     * settings.
     * @var array $finalgrades
     */
    private $finalgrades = array();

    /**
     * The value returned from each statistic.
     * @var array $reportedstats
     */
    private $reportedstats = array();

    /**
     * The html of the report to output.
     * @var string $html
     */
    public $html;

    /**
     * The table class used to make the html of the report.
     * @var object $table
     */
     private $table;

     /**
      * Array of clases that extend stats witch have the logic to
      * generate the statstics.
      * @var array $stats
      */
     private static $stats = array();

    /**
     * Constructor. Initialises grade_tree, sets up group, baseurl
     * and pbarurl.
     * @param int $courseid the coures id for the report
     * @param object $gpr grade plugin tracking object
     * @context string $context
     */
    public function __construct($courseid, $gpr, $context) {
        global $CFG;
        parent::__construct($courseid, $gpr, $context, null);

        $this->canviewhidden = has_capability('moodle/grade:viewhidden', get_context_instance(CONTEXT_COURSE, $this->course->id));

        /// Set up urls
        $this->baseurl = 'index.php?id=' . $this->courseid;
        $this->pbarurl = 'index.php?id=' . $this->courseid;

        /// Set the position of the aggregation categorie based on pref
        $switch = $this->get_pref('statsaggregationposition');
        if ($switch == '' && isset($CFG->grade_aggregationposition)) {
            $switch = grade_get_setting($this->courseid, 'aggregationposition', $CFG->grade_aggregationposition);
        }

        /// Build grade tree
        $this->gtree = new grade_tree($this->courseid, false, $switch);

        $this->course->groupmode = 2;
        /// Set up Groups
        if ($this->get_pref('statsshowgroups') || is_null($this->get_pref('statsshowgroups'))) {
            $this->setup_groups();
        }

        /// Load stats classes from ./statistics
        $this->load_stats();
    }

    /**
     * Load all the stats classes into $stats.
     * Looks in /statistics and trys to make an instence of any
     * class that is in a file that starts with stats_ and extends
     * stats directly.
     * @param bool $return if true return stats array, else store in $this->stats
     * @returns array array of clases that extend stats
     */
    private function load_stats($return=false) {
        global $CFG;

        $stats = array();

        foreach (glob($CFG->dirroot . '/grade/report/stats/statistics/stat_*.php') as $path) {
            $filename = substr(basename($path, '.php'), 5);

            if(class_exists($filename) && get_parent_class($class = new $filename) == 'stats' ) {
                $stats[$filename] = $class;
            }
        }

        if($return) {
            return $stats;
        } else {
            grade_report_stats::$stats = $stats;
        }
    }

    /**
     * Returns the current stats being used in the report or if no stats are
     * set, it returns the stats as whould be loaded by load_stats.
     * @returns array array of classes that extend stats
     */
    public function get_stats() {
        if(!isset(grade_report_stats::$stats) || is_null(grade_report_stats::$stats) || empty(grade_report_stats::$stats)) {
            return grade_report_stats::load_stats(true);
        } else {
            return grade_report_stats::$stats;
        }
    }

    /// Added to keep grade_report happy
    public function process_data($data){}
    public function process_action($target, $action){}

    /**
     * Based on load user function from grader report.
     * Pulls out the userids of the users to be used in the stats.
     * @return array array of user ids to use in stats
     */
    public function load_users() {
        global $CFG, $DB;

        $params = array();
        list($usql, $gbr_params) = $DB->get_in_or_equal(explode(',', $this->gradebookroles), SQL_PARAMS_NAMED);

        $sql = "SELECT u.id
                FROM {user} u
                    JOIN {role_assignments} ra ON u.id = ra.userid
                    $this->groupsql
                WHERE ra.roleid $usql
                    $this->groupwheresql
                    AND ra.contextid ".get_related_contexts_string($this->context);

        $params = array_merge($gbr_params, $this->groupwheresql_params);

        $this->users = $DB->get_records_sql($sql, $params);

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
     * Encode an array of stat's data in to a stirng so it can
     * be put in the get part of a url.
     * @param arrray $array array of data to encode
     * @param object $item grade_item to use to fromat stats
     * @param object $stat stats object to use to format stats
     * @return string encoded string
     */
    private function encode_array(array $array, $item, $stat) {
        $string = '';

        /// Encode each elment by puting a delimiter and base64 encoding it.
        foreach($array as $id=>$data) {
            $string .= addslashes(grade_format_gradevalue($data, $item, true, $stat->displaytype, $stat->decimals)) . '"';
        }
        return strtr(base64_encode($string), '+/=', '-_,');
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

            $grades = $DB->get_records_sql($sql);
        }

        $userids = array_keys($this->users);

        if ($grades) {
            foreach ($grades as $graderec) {
                if (in_array($graderec->userid, $userids) and array_key_exists($graderec->itemid, $this->gtree->items)) { // some items may not be present!!
                    $this->grades[$graderec->itemid][$graderec->userid] = new grade_grade($graderec, false);
                    $this->grades[$graderec->itemid][$graderec->userid]->grade_item =& $this->gtree->items[$graderec->itemid]; // db caching
                }
            }
        }

        /// prefil grades that do not exist yet
        foreach ($userids as $userid) {
            foreach ($this->gtree->items as $itemid=>$unused) {
                if (!isset($this->grades[$itemid][$userid])) {
                    $this->grades[$itemid][$userid] = new grade_grade();
                    $this->grades[$itemid][$userid]->itemid = $itemid;
                    $this->grades[$itemid][$userid]->userid = $userid;
                    $this->grades[$itemid][$userid]->grade_item =& $this->gtree->items[$itemid]; // db caching
                }
            }
        }

        $this->finalgrades = array();

        /// Build finalgrades array and filliter out unwanted grades.
        foreach ($this->gtree->items as $id=>$item) {
            if(($item->gradetype == GRADE_TYPE_SCALE &&  ($this->get_pref('statsshowscaleitems') || is_null($this->get_pref('statsshowscaleitems'))))
                || ($item->gradetype == GRADE_TYPE_VALUE &&  ($this->get_pref('statsshowvalueitems') || is_null($this->get_pref('statsshowvalueitems'))))) {
                    $this->finalgrades[$id] = array();
                    $i = 0;

                    if(isset($this->grades[$id]) && !is_null($this->grades[$id])) {
                        foreach ($this->grades[$id] as $grade) {
                            if( (($grade->is_hidden() &&  $this->canviewhidden && ($this->get_pref('statsusehidden') || is_null($this->get_pref('statsusehidden')))) || !$grade->is_hidden())
                                && (($grade->is_locked() && ($this->get_pref('statsuselocked') || is_null($this->get_pref('statsuselocked')))) || !$grade->is_locked())) {
                                    if($this->get_pref('statsincompleasmin') && is_null($grade->finalgrade)) {
                                        $this->finalgrades[$id][$i] = $item->grademin;
                                        $i++;
                                    } elseif(!is_null($grade->finalgrade)) {
                                        $this->finalgrades[$id][$i] = $grade->finalgrade;
                                        $i++;
                                    }
                            }
                        }
                    }
                }
        }
    }

    /**
     * Runs grades for each item threw the report functions
     * of each stats class in $stats and stores the values in
     * reportedstats.
     */
    public function report_data() {
        $this->reportedstats = array();

        foreach(grade_report_stats::$stats as $name=>$stat) {
            if(($stat->capability == null || has_capability($stat->capability, $this->context)) && ($this->get_pref('stats'. $name) || is_null($this->get_pref('stats'. $name)))) {
                $this->reportedstats[$name] = array();

                foreach($this->finalgrades as $itemid=>$item) {
                    sort($item);
                    if(count($item) > 0) {
                        $this->reportedstats[$name][$itemid] = $stat->report_data($item, $this->gtree->items[$itemid]);
                    } else {
                        $this->reportedstats[$name][$itemid] = null;
                    }
                }
            }
        }
    }

    /**
     * Take the reported data and adapt it in to HTML to output.
     * HTML is stored in html.
     * TODO: Deal with tables growing to wide.
     * TODO: Make it look nice.
     */
    public function adapt_data($printerversion = false) {
        global $CFG;

        $inverted = $this->get_pref('statsshowinverted');

        /// Set up table arrays
        $tablecolumns = array('statistic');
        $tableheaders = array($this->get_lang_string('statistic', 'gradereport_stats'));

        /// Loop threw items and build arrays
        if ($inverted) {
            if($this->get_pref('statsshowranges')) {
                array_push($tablecolumns, 'range');
                array_push($tableheaders, $this->get_lang_string('range', 'gradereport_stats'));
            }

            foreach($this->reportedstats as $name=>$data) {
                array_push($tablecolumns, $name);
                array_push($tableheaders, grade_report_stats::$stats[$name]->name);
            }

            if($this->get_pref('statsshownumgrades')) {
                array_push($tablecolumns, 'num_grades');
                array_push($tableheaders, $this->get_lang_string('num_grades', 'gradereport_stats'));
            }
        } else {
            /// Set up range column and number of grades column
            $ranges = array(format_text('<strong>' . $this->get_lang_string('range', 'gradereport_stats') . '</strong>', FORMAT_HTML));
            $numgrades = array(format_text('<strong>' . $this->get_lang_string('num_grades', 'gradereport_stats') . '</strong>', FORMAT_HTML));

            foreach($this->finalgrades as $itemid=>$grades) {
                array_push($tablecolumns, $itemid);
                array_push($tableheaders,  format_text($this->gtree->items[$itemid]->get_name(), FORMAT_HTML));
                array_push($ranges, format_text('<strong>' . grade_format_gradevalue($this->gtree->items[$itemid]->grademin, $this->gtree->items[$itemid], true) . '-' . grade_format_gradevalue($this->gtree->items[$itemid]->grademax, $this->gtree->items[$itemid], true) . '</strong>' , FORMAT_HTML));
                array_push($numgrades, format_text(count($grades), FORMAT_HTML));
            }
        }

        /// Set up flexible table
        $this->table = new flexible_table('grade-report-stats-' . $this->courseid);
        $this->table->define_columns($tablecolumns);
        $this->table->define_headers($tableheaders);
       if ($printerversion) {
            $this->table->collapsible(false);
            $this->table->set_attribute('cellspacing', '1');
            $this->table->set_attribute('border', '1');
        } else {
            $this->table->define_baseurl($this->baseurl);
            $this->table->collapsible(true);
            $this->table->set_attribute('cellspacing', '1');
            $this->table->set_attribute('id', 'stats-grade');
            $this->table->set_attribute('class', 'grade-report-stats gradestable flexible');
        }
        $this->table->setup();

        /// If ranges are being shown add them to the table
        if(!$inverted){
            if ($this->get_pref('statsshowranges')){
                $this->table->add_data($ranges);
                $this->table->add_separator();
            }
        }

        /// Loop threw all the reported data and format it in to cells
        /// If stat retured an array of values display the elements or
        /// make a link to a popup with the data in it.
        if($inverted) {
            foreach($this->finalgrades as $itemid=>$grades) {
                $item = $this->gtree->items[$itemid];
                $row = array(format_text('<strong>' . $item->get_name() . '</strong>' , FORMAT_HTML));

                if($this->get_pref('statsshowranges')) {
                    array_push($row, format_text('<strong>' . grade_format_gradevalue($item->grademin, $item, true) . '-' . grade_format_gradevalue($item->grademax, $item, true) . '</strong>' , FORMAT_HTML));
                }

                foreach($this->reportedstats as $name=>$data) {
                    $stat = $data[$itemid];

                    if(!is_array($stat)) {
                        array_push($row, format_text(grade_format_gradevalue($stat, $item, true, grade_report_stats::$stats[$name]->displaytype, grade_report_stats::$stats[$name]->decimals), FORMAT_HTML));
                    } else {
                        $statstring = "";

                        for($i = 0; $i < 2; $i++) {
                            if($i >= count($stat)) {
                                break;
                            }
                            $statstring .= grade_format_gradevalue($stat[$i], $item, true, grade_report_stats::$stats[$name]->displaytype, grade_report_stats::$stats[$name]->decimals) . ', ';
                        }

                        if($i < count($stat)) {
                            if(!$printerversion) {
                                $statstring = "<a href=\"#\" onClick=\"javascript:window.open('{$CFG->wwwroot}/grade/report/stats/arrayview.php?id={$this->courseid}&data={$this->encode_array($stat, $item, grade_report_stats::$stats[$name])}','{$this->get_lang_string('moredata', 'gradereport_stats')}','width=300,height=500,menubar=no,status=no,location=no,directories=no,toolbar=no,scrollbars=yes');\">". format_text($statstring, FORMAT_HTML) . '....</a>';
                            } else {
                                    $statstring .= '...';
                            }
                        } else {
                            $statstring = substr($statstring, 0, strlen($statstring) - 2);
                        }
                        array_push($row, $statstring);
                    }
                }

                if($this->get_pref('statsshownumgrades')) {
                    array_push($row, format_text(count($grades), FORMAT_HTML));
                }

                $this->table->add_data($row);
            }
        } else {
            foreach($this->reportedstats as $name=>$data) {
                $row = array(format_text('<strong>' . grade_report_stats::$stats[$name]->name . '</strong>', FORMAT_HTML));

                foreach($data as $itemid=>$stat) {
                    if(!is_array($stat)) {
                        array_push($row, format_text(grade_format_gradevalue($stat, $this->gtree->items[$itemid], true, grade_report_stats::$stats[$name]->displaytype, grade_report_stats::$stats[$name]->decimals), FORMAT_HTML));
                    } else {
                        $statstring = "";

                        for($i = 0; $i < 2; $i++) {
                            if($i >= count($stat)) {
                                break;
                            }
                            $statstring .= grade_format_gradevalue($stat[$i], $this->gtree->items[$itemid], true, grade_report_stats::$stats[$name]->displaytype, grade_report_stats::$stats[$name]->decimals) . ', ';
                        }

                        if($i < count($stat)) {
                            if(!$printerversion) {
                                $statstring = "<a href=\"#\" onClick=\"javascript:window.open('{$CFG->wwwroot}/grade/report/stats/arrayview.php?id={$this->courseid}&data={$this->encode_array($stat, $this->gtree->items[$itemid], grade_report_stats::$stats[$name])}','{$this->get_lang_string('moredata', 'gradereport_stats')}','width=300,height=500,menubar=no,status=no,location=no,directories=no,toolbar=no,scrollbars=yes');\">". format_text($statstring, FORMAT_HTML) . '....</a>';
                            } else {
                                    $statstring .= '...';
                            }
                        } else {
                            $statstring = substr($statstring, 0, strlen($statstring) - 2);
                        }
                        array_push($row, $statstring);
                    }
                }
                $this->table->add_data($row);
            }
        }

        /// If the number of grades is being shown add it to the table.
        if(!$inverted) {
            if ($this->get_pref('statsshownumgrades')){
                $this->table->add_separator();
                $this->table->add_data($numgrades);
            }
        }

        /// Build html
        ob_start();
            if($this->currentgroup == 0) {
                echo format_text('<strong>Group:</strong> All participants', FORMAT_HTML);
            } else {
                echo format_text('<strong>Group:</strong> ' . groups_get_group_name($this->currentgroup), FORMAT_HTML);
            }
            $this->table->print_html();
        $this->html = ob_get_clean();
    }

    /**
     * Builds HTML for toggles on top of report.
     * Based on grader report  get_toggles_html
     * @return string html code for toggles.
     */
    public function get_toggles_html() {
        global $CFG, $USER;

        $html = '<div id="stats-report-toggles" style="vertical-align: text-top; text-align: center;">';
        $html .= $this->print_toggle('numgrades', true);
        $html .= $this->print_toggle('groups', true);
        $html .= $this->print_toggle('ranges', true);
        $html .= $this->print_toggle('inverted', true);
        $html .= '</div>';

        return $html;
    }

    /**
     * Builds HTML for each individual toggle.
     * Based on grader report print_toggle
     * @param string $type The toggle type.
     * @param bool $return Wheather ro return the HTML or print it.
     */
    private function print_toggle($type, $return=false) {
        global $CFG;

        $icons = array('eyecons' => 't/hide.gif',
                       'numgrades' => 't/grades.gif',
                       'calculations' => 't/calc.gif',
                       'locks' => 't/lock.gif',
                       'averages' => 't/mean.gif',
                       'inverted' => 't/switch_whole.gif',
                       'nooutcomes' => 't/outcomes.gif');

        $pref_name = 'grade_report_statsshow' . $type;

        if (array_key_exists($pref_name, $CFG)) {
            $show_pref = get_user_preferences($pref_name, $CFG->$pref_name);
        } else {
            $show_pref = get_user_preferences($pref_name);
        }

        $strshow = $this->get_lang_string('show' . $type, 'gradereport_stats');
        $strhide = $this->get_lang_string('hide' . $type, 'gradereport_stats');

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

        $retval = $img . '<a href="' . $this->baseurl . "&amp;toggle=$toggle_action&amp;toggle_type=$type\">"
             . format_text($string, FORMAT_HTML) . '</a> ';

        if ($return) {
            return $retval;
        } else {
            echo $retval;
        }
    }
}
?>
