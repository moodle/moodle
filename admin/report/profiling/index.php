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
 * @package    core
 * @subpackage profiling
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// TODO: Move all the DB stuff to profiling_db_xxxx() function in xhprof_moodle.php

require_once(dirname(__FILE__).'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir . '/xhprof/xhprof_moodle.php');

define('PROFILING_RUNSPERPAGE', 50);

// page parameters
$script   = optional_param('script', null, PARAM_PATH);
$runid    = optional_param('runid', null, PARAM_ALPHANUM);
$runid2   = optional_param('runid2', null, PARAM_ALPHANUM);
$listurl  = optional_param('listurl', null, PARAM_PATH);
$runreference= optional_param('runreference', 0, PARAM_INT);
$runcomment  = optional_param('runcomment', null, PARAM_TEXT);

$dbfields = 'runid, url, totalexecutiontime, totalcputime, ' .
            'totalcalls, totalmemory, runreference, runcomment, timecreated';

admin_externalpage_setup('reportprofiling');

// Always add listurl if available
if ($listurl) {
    $listurlnav = new moodle_url('/admin/report/profiling/index.php', array('listurl' => $listurl));
    $PAGE->navbar->add($listurl, $listurlnav);
}

// Header
echo $OUTPUT->header();

// We have requested the last available run for one script
if (isset($script)) {
    // Get the last available run for the given script
    $run = $DB->get_record_sql("SELECT $dbfields
                                 FROM {profiling}
                                WHERE url = ?
                                  AND id = (SELECT MAX(id)
                                              FROM {profiling}
                                             WHERE url = ?)",
                              array($script, $script), IGNORE_MISSING);

    // No run found for script, warn and exit
    if (!$run) {
        notice(get_string('cannotfindanyrunforurl', 'report_profiling', $script), 'index.php');
    }

    // Check if there is any previous run marked as reference one
    $prevreferences = $DB->get_records_select('profiling',
                                              'url = ? AND runreference = 1 AND timecreated < ?',
                                              array($run->url, $run->timecreated),
                                              'timecreated DESC', 'runid', 0, 1);
    $prevrunid = $prevreferences ? reset($prevreferences)->runid : false;
    echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
    $header = get_string('lastrunof', 'report_profiling', $script);
    echo $OUTPUT->heading($header);
    $table = profiling_print_run($run, $prevrunid);
    echo $table;
    echo $OUTPUT->box_end();


// We have requested the diff between 2 runs
} else if (isset($runid) && isset($runid2)) {
    $run1 = $DB->get_record('profiling', array('runid'=>$runid), $dbfields, MUST_EXIST);
    $run2 = $DB->get_record('profiling', array('runid'=>$runid2), $dbfields, MUST_EXIST);
    if ($run1->url == $run2->url && $run1->runid != $run2->runid) {
        if ($run2->timecreated < $run1->timecreated) {
            $runtemp = $run1;
            $run1 = $run2;
            $run2 = $runtemp;
        }
        echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
        $header = get_string('differencesbetween2runsof', 'report_profiling', $run1->url);
        echo $OUTPUT->heading($header);
        $table = profiling_print_rundiff($run1, $run2);
        echo $table;
        echo $OUTPUT->box_end();
    }


// We have requested one run, invoke it
} else if (isset($runid)) {
    // Check if we are trying to update the runreference/runcomment for the run
    if (isset($runcomment) && confirm_sesskey()) {
        $id = $DB->get_field('profiling', 'id', array('runid' => $runid), MUST_EXIST);
        $rec = new stdClass();
        $rec->id = $id;
        $rec->runreference = (bool)$runreference;
        $rec->runcomment   = $runcomment;
        $DB->update_record('profiling', $rec);
    }
    // Get the requested runid
    $run = $DB->get_record('profiling', array('runid'=>$runid), $dbfields, IGNORE_MISSING);

    // No run found for runid, warn and exit
    if (!$run) {
        notice(get_string('cannotfindanyrunforrunid', 'report_profiling', $runid), 'index.php');
    }

    // Check if there is any previous run marked as reference one
    $prevreferences = $DB->get_records_select('profiling',
                                              'url = ? AND runreference = 1 AND timecreated < ?',
                                              array($run->url, $run->timecreated),
                                              'timecreated DESC', 'runid', 0, 1);
    $prevrunid = $prevreferences ? reset($prevreferences)->runid : false;
    echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
    $header = get_string('summaryof', 'report_profiling', $run->url);
    echo $OUTPUT->heading($header);
    $table = profiling_print_run($run, $prevrunid);
    echo $table;
    echo $OUTPUT->box_end();


// Default: List one page of runs
} else {

    // The flexitable that will root listings
    $table = new xhprof_table_sql('profiling-list-table');
    $baseurl = $CFG->wwwroot . '/admin/report/profiling/index.php';

    // Check if we are listing all or some URL ones
    $sqlconditions = '';
    $sqlparams = array();
    if (!isset($listurl)) {
        $header = get_string('pluginname', 'report_profiling');
        $sqlconditions = '1 = 1';
        $table->set_listurlmode(false);
    } else {
        $header =  get_string('profilingrunsfor', 'report_profiling', $listurl);
        $sqlconditions = 'url = :url';
        $sqlparams['url'] = $listurl;
        $table->set_listurlmode(true);
        $baseurl .= '?listurl=' . urlencode($listurl);
    }

    echo $OUTPUT->heading($header);

    // TODO: Fix flexitable to validate tsort/thide/tshow/tifirs/tilast/page
    // TODO: Fix table_sql to allow it to work without WHERE clause
    // add silly condition (1 = 1) because of table_sql bug
    $table->set_sql($dbfields, '{profiling}', $sqlconditions, $sqlparams);
    $table->set_count_sql("SELECT COUNT(*) FROM {profiling} WHERE $sqlconditions", $sqlparams);
    $columns = array(
        'url', 'timecreated', 'totalexecutiontime', 'totalcputime',
        'totalcalls', 'totalmemory', 'runcomment');
    $headers = array(
        get_string('url'), get_string('date'), get_string('executiontime', 'report_profiling'),
        get_string('cputime', 'report_profiling'), get_string('calls', 'report_profiling'),
        get_string('memory', 'report_profiling'), get_string('comment', 'report_profiling'));
    $table->define_columns($columns);
    $table->define_headers($headers);
    $table->sortable(true, 'timecreated', SORT_DESC);
    $table->define_baseurl($baseurl);
    $table->column_suppress('url');
    $table->out(PROFILING_RUNSPERPAGE, true);

    // Print the controller block with different options
    echo profiling_list_controls($listurl);
}

// Footer.
echo $OUTPUT->footer();

