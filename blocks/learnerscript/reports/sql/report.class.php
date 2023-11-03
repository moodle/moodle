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

/** Learnerscript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
define('REPORT_CUSTOMSQL_MAX_RECORDS', 5000);
use block_learnerscript\local\ls;
use block_learnerscript\local\reportbase;
use stdclass;
use html_table;

class report_sql extends reportbase {
    public $tablehead;
    private $tablealign;
    private $tablesize;
    private $tablewrap;
    private $calcdata;
    public $columns;

    public function __construct($report, $reportproperties=array()) {
        parent::__construct($report, $reportproperties);
        $this->parent = true;
        $this->components = array('customsql', 'filters', 'permissions', 'calcs', 'plot');
    }
    function init() {

    }
    function prepare_sql($sql) {
        global $DB, $USER, $CFG, $COURSE;
        $sql = str_replace('%%LS_STARTDATE%%', $this->ls_startdate, $sql);
        $sql = str_replace('%%LS_ENDDATE%%', $this->ls_enddate, $sql);
        // $sql = str_replace('%%SEARCH%%', $this->search, $sql);

            if (!empty($this->search) && preg_match("/%%SEARCH:([^%]+)%%/i", $sql, $output)) {
                list($field, $operator) = preg_split('/:/', $output[1]);
                if ($operator != '' && !in_array($operator, $operators)) {
                    print_error('nosuchoperator');
                }
                if ($operator== '' || $operator == '~') {
                    $replace = " AND " . $field . " LIKE '%" . $this->search . "%'";
                } else if ($operator == 'in') {
                    $processeditems = array();
                    // Accept comma-separated values, allowing for '\,' as a literal comma.
                    foreach (preg_split("/(?<!\\\\),/", $this->search) as $searchitem) {
                        // Strip leading/trailing whitespace and quotes (we'll add our own quotes later).
                        $searchitem = trim($searchitem);
                        $searchitem = trim($searchitem, '"\'');

                        // We can also safely remove escaped commas now.
                        $searchitem = str_replace('\\,', ',', $searchitem);

                        // Escape and quote strings...
                        if (!is_numeric($searchitem)) {
                            $searchitem = "'" . addslashes($searchitem) . "'";
                        }
                        $processeditems[] = "$field like $searchitem";
                    }
                    // Despite the name, by not actually using in() we can support wildcards, and maybe be more portable as well.
                    $replace = " AND (" . implode(" OR ", $processeditems) . ")";
                } else {
                    $replace = ' AND ' . $field . ' ' . $operator . ' ' . $filtersearchtext;
                }
                // return str_replace('%%FILTER_SEARCHTEXT:' . $output[1] . '%%', $replace, $finalelements);
                $sql = str_replace('%%SEARCH:' . $output[1] . '%%', $replace, $sql);
            }
        // Enable debug mode from SQL query.
        $this->config->debug = (strpos($sql, '%%DEBUG%%') !== false) ? true : false;

        // Pass special custom undefined variable as filter.
        // Security warning !!! can be used for sql injection.
        // Use %%FILTER_VAR%% in your sql code with caution.
        $filter_var = optional_param('filter_var', '', PARAM_RAW);
        if (!empty($filter_var)) {
            $sql = str_replace('%%FILTER_VAR%%', $filter_var, $sql);
        }

        $sql = str_replace('%%USERID%%', $this->userid, $sql);
        $sql = str_replace('%%COURSEID%%', $COURSE->id, $sql);
        $sql = str_replace('%%CATEGORYID%%', $COURSE->category, $sql);

        // Current timestamp
        $date = new \DateTime();
        $timestamp = $date->getTimestamp(); 
        $sql = str_replace('%%UNIXTIME%%', $timestamp, $sql);

        //TOP & LIMIT
        if ($CFG->dbtype == 'sqlsrv') {
            $sql = str_replace('%%TOP%%', 'TOP 10', $sql);
        } else {
            $sql = str_replace('%%LIMIT%%', 'LIMIT 10', $sql);
        }

        if (preg_match("/%%FILTER_USER:([^%]+)%%/i", $sql, $output) && $this->params['filter_users'] > 1) {
            $replace = ' AND ' . $output[1] . ' = ' . $this->params['filter_users'];
            $sql = str_replace('%%FILTER_USER:' . $output[1] . '%%', $replace, $sql);
        }
        if (preg_match("/%%FILTER_CATEGORIES:([^%]+)%%/i", $sql, $output) && $this->params['filter_categories']>=0) {
            $replace = ' AND ' . $output[1] . ' = ' . $this->params['filter_categories'];
            $sql = str_replace('%%FILTER_CATEGORIES:' . $output[1] . '%%', $replace, $sql);
        }
        if (preg_match("/%%FILTER_COURSES:([^%]+)%%/i", $sql, $output) && $this->params['filter_courses']>0) {
            $replace = ' AND ' . $output[1] . ' = ' . $this->params['filter_courses'];
            $sql = str_replace('%%FILTER_COURSES:' . $output[1] . '%%', $replace, $sql);
        }
        if (preg_match("/%%FILTER_ACTIVITIES:([^%]+)%%/i", $sql, $output) && $this->params['filter_activities']>0) {
            $replace = ' AND ' . $output[1] . ' = ' . $this->params['filter_activities'];
            $sql = str_replace('%%FILTER_ACTIVITIES:' . $output[1] . '%%', $replace, $sql);
        }
        if (preg_match("/%%FILTER_MODULE:([^%]+)%%/i", $sql, $output) && $this->params['filter_modules']>0) {
            $replace = ' AND ' . $output[1] . ' = ' . $this->params['filter_modules'];
            $sql = str_replace('%%FILTER_MODULE:' . $output[1] . '%%', $replace, $sql);
        }
        if (preg_match("/%%FILTER_COHORT:([^%]+)%%/i", $sql, $output) && $this->params['filter_cohort']>0) {
            $replace = ' AND ' . $output[1] . ' = ' . $this->params['filter_cohort'];
            $sql = str_replace('%%FILTER_COHORT:' . $output[1] . '%%', $replace, $sql);
        }        
        
        $userfullname = $DB->sql_concat('u.firstname', "' '", 'u.lastname') ; 
        $sql = str_replace('%%USERFULLNAME%%', $userfullname, $sql);

        // See http://en.wikipedia.org/wiki/Year_2038_problem
        $sql = str_replace(array('%%STARTTIME%%', '%%ENDTIME%%'), array('0', '2145938400'), $sql);
        $sql = str_replace('%%WWWROOT%%', $CFG->wwwroot, $sql);
        $sql = preg_replace('/%{2}[^%]+%{2}/i', '', $sql);

        $sql = str_replace('?', '[[QUESTIONMARK]]', $sql);
       return $sql;
    }

    function get_all_elements() {
        global $remoteDB, $DB, $CFG;

        $this->sql = preg_replace('/\bprefix_(?=\w+)/i', $CFG->prefix, $this->sql);

        // Use a custom $DB (and not current system's $DB)
        // todo: major security issue
        // $remoteDBhost = get_config('block_learnerscript', 'dbhost');
        // if (empty($remoteDBhost)) {
        //     $remoteDBhost = $CFG->dbhost;
        // }
        // $remoteDBname = get_config('block_learnerscript', 'dbname');
        // if (empty($remoteDBname)) {
        //     $remoteDBname = $CFG->dbname;
        // }
        // $remoteDBuser = get_config('block_learnerscript', 'dbuser');
        // if (empty($remoteDBuser)) {
        //     $remoteDBuser = $CFG->dbuser;
        // }
        // $remoteDBpass = get_config('block_learnerscript', 'dbpass');
        // if (empty($remoteDBpass)) {
        //     $remoteDBpass = $CFG->dbpass;
        // }

        $reportlimit = get_config('block_learnerscript', 'reportlimit');
        if (empty($reportlimit) or $reportlimit == '0') {
            $reportlimit = REPORT_CUSTOMSQL_MAX_RECORDS;
        }

        // $db_class = get_class($DB);
        // $remoteDB = new $db_class();
        // $remoteDB->connect($remoteDBhost, $remoteDBuser, $remoteDBpass, $remoteDBname, $CFG->prefix);
        preg_match("/(GROUP BY|group by)[\r\t|\r\n|\r\s]+(.*)/", $this->sql, $groupmatch, PREG_OFFSET_CAPTURE, 0);
        preg_match("/^(SELECT|select)[\r\t|\r\n|\r\s]+(.*)/", $this->sql, $matches, PREG_OFFSET_CAPTURE, 0);
        $selectcount = explode(',', $matches[2][0]);
        // if (!stripos('count', $selectcount[0]) && empty($groupmatch) &&
        //  !(sizeof($selectcount) == 1 && !ctype_alnum($selectcount[0]))) {
        //  str_replace($selectcount[0], '', $matches[2][0]);
        //  if (!stripos('AS', $selectcount[0])) {
        //      $selectcount = explode(' ', $selectcount[0]);
        //  }
        //  $replacesql = 'SELECT COUNT(' . $selectcount[0] . '), ' . $matches[2][0] . ' ';
        //  $countsql = str_replace($matches[0][0], $replacesql, $sql);
        //  $totalrecords = $remoteDB->count_records_sql($countsql);
        // } else {
        $nolimitrecords = $DB->get_recordset_sql($this->sql);
        $totalrecords = [];
        foreach ($nolimitrecords as $row) {
            $totalrecords[] = $row;
        }
        $totalrecords = count($totalrecords);
        // }
        $starttime = microtime(true);

        if (preg_match('/\b(INSERT|INTO|CREATE)\b/i', $this->sql)) {
            // Run special (dangerous) queries directly.
            $results = $DB->execute($this->sql);
        } else {
            $results = $DB->get_recordset_sql($this->sql, null, $this->start, $this->length);
        }

        // Update the execution time in the DB.
        // $updaterecord = $DB->get_record('block_learnerscript', array('id' => $this->config->id));
        $lastexecutiontime = round((microtime(true) - $starttime) * 1000);
        $this->config->lastexecutiontime = $lastexecutiontime;

        // $DB->update_record('block_learnerscript', $updaterecord);
        $DB->set_field('block_learnerscript', 'lastexecutiontime', $lastexecutiontime,  array('id' => $this->config->id));
        return compact('results', 'totalrecords');
    }

    function get_rows() {
        global $CFG, $DB;

        $components = (new ls)->cr_unserialize($this->config->components);

        $config = (isset($components['customsql']['config'])) ? $components['customsql']['config'] : new stdclass;
        $reportfilters = (isset($components['filters']['elements'])) ? $components['filters']['elements'] : array();

        $sql = '';

        if (isset($config->querysql)) {
            // FILTERS
            $sql = $config->querysql;
            if (!empty($reportfilters)) {
                foreach ($reportfilters as $f) {
                    if ($f['formdata']->value) {
                        require_once $CFG->dirroot . '/blocks/learnerscript/components/filters/' . $f['pluginname'] . '/plugin.class.php';
                        $classname = 'block_learnerscript\lsreports\plugin_' . $f['pluginname'];
                        $class = new $classname($this->config);

                        $sql = $class->execute($sql, $f['formdata'], $this->params);
                    }
                }
            }

            $this->sql = $this->prepare_sql($sql);
            $columns = (isset($components['columns']['elements'])) ? $components['columns']['elements'] : array();
            $selectedcolumns = array();
            $tablehead = array();
            $tablesize = array();
            $tablewrap = array();
            $tablealign = array();
            $finaltable = array();
            foreach ($columns as $c) {
                $selectedcolumns[$c['formdata']->column] = $c['formdata']->column;
                $tablehead[$c['formdata']->column] = $c['formdata']->columname;

                $tablealign[] = $c['formdata']->align;
                $tablesize[] = $c['formdata']->size;
                $tablewrap[] = $c['formdata']->wrap;
            }

            if ($rs = $this->get_all_elements()) {

                foreach ($rs['results'] as $row) {
                    // if (empty($finaltable)) {
                    //     foreach ($row as $colname => $value) {
                    //         $tablehead[] = str_replace('_', ' ', ucwords($colname));
                    //     }
                    // }
                    $i = 0;
                    $r = array();
                    foreach ($selectedcolumns as $k => $v) {
                        if (!empty($v)) {
                            if (in_array($v, $selectedcolumns)) { 
                                if ($this->config->name == 'Avg time spent by learners and teachers on course' && $this->reporttype == 'table') {
                                    if ($v == 'teachertimespent' && $row->$v != 0) {
                                        $row->$v = (new ls)->strTime(ROUND($row->$v, 2));
                                    }
                                    if ($v == 'studenttimespent' && $row->$v != 0) {
                                        $row->$v = (new ls)->strTime(ROUND($row->$v, 2));
                                    }
                                }
                                if($this->config->name == 'Need grading' && $this->reporttype == 'table'){
                                    if ($v == 'datesubmitted' ) {
                                        $row->$v = userdate($row->$v, '%Y-%m-%d %H:%M:%S');
                                    }
                                    if ($v == 'delay' && $row->$v != 0) {
                                        $row->$v = (new ls)->strTime(ROUND($row->$v, 2));
                                    }
                                }
                                $row->$v = format_text($row->$v, FORMAT_HTML, array('trusted' => true, 'noclean' => true, 'para' => false));
                                $r[$k] = str_replace('[[QUESTIONMARK]]', '?', $row->$v);
                                $i++;
                            }
                        }
                    }

                    // foreach ($row as $ii => $cell) {
                    //     if(in_array($ii,$components->selectedcolumns))
                    //     $array_row[$ii] = str_replace('[[QUESTIONMARK]]', '?', $cell);
                    // }

                    // $totalrecords++;
                    $rows[] = $row;
                    $finaltable[] = $r;
                }
                $totalrecords = $rs['totalrecords'];
            }
        }
        return compact('finaltable', 'totalrecords', 'tablehead', 'rows', 'tablealign', 'tablewrap', 'tablesize');
    }

    function create_report($blockinstanceid = null) {
        global $DB, $CFG;
        $this->check_filters_request();
        $components = (new ls)->cr_unserialize($this->config->components);
        $reportfilters = (isset($components['filters']['elements'])) ? $components['filters']['elements'] : array();
        $calcs = (isset($components['calculations']['elements'])) ? $components['calculations']['elements'] : array();

        $tablehead = array();
        $finalcalcs = array();
        $finaltable = array();
        $tablehead = array();
        $config = (isset($components['customsql']['config'])) ? $components['customsql']['config'] : new stdclass;

        $finaldata = $this->get_rows();

        $this->totalrecords = $finaldata['totalrecords'];
        if ($blockinstanceid == null) {
            $blockinstanceid = $this->config->id;
        }

        $finalheadcalcs = $this->get_calcs($finaldata['finaltable'], $tablehead);
        $finalcalcs = $finalheadcalcs->data;
        $table = new stdclass;
        $table->id = 'reporttable_' . $blockinstanceid . '';
        $table->data = $finaldata['finaltable'];
        $table->head = $finaldata['tablehead'];
        $table->align = $finaldata['tablealign'];
        $table->wrap = $finaldata['tablewrap'];
        $table->size = $finaldata['tablesize'];
        $this->tablehead = $finaldata['tablehead'];
        $calcs = new html_table();
        $calcshead = array();
        $filterheads = array();
        $calcshead[] = 'Column Name';

        foreach ($finalheadcalcs->calcdata as $key => $head) {
            $calcshead[$head] = ucfirst($head);
            $calcshead1[$head] = $head;
            $filterheads[$key] = $finalheadcalcs->calcdata[$key];
        }
        $calcsdata = array();
        foreach ($finalheadcalcs->head as $key => $head) {
            $row = array();
            $row[] = ucfirst($key);
            foreach ($calcshead1 as $key1 => $value) {
                if (array_key_exists($key . '-' . $value, $finalcalcs)) {
                    $row[] = $finalcalcs[$key . '-' . $value]/*$finalcalcs[$key1]*/;
                } else {
                    $row[] = 'N/A';
                }
            }
            $calcsdata[] = $row;
        }

        $calcs->data = $calcsdata;
        $calcs->head = $calcshead;
        // $calcs->data = array($finalcalcs);
        // $calcs->head = $this->tablehead;

        if (!$this->finalreport) {
            $this->finalreport = new StdClass;
        }
        $this->finalreport->table = $table;
        $this->finalreport->calcs = $calcs;

        return true;
    }
}
