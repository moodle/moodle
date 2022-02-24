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
 * For a given question type, list the number of
 *
 * @package    report
 * @subpackage questioninstances
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/questionlib.php');

// Get URL parameters.
$requestedqtype = optional_param('qtype', '', PARAM_SAFEDIR);

// Print the header & check permissions.
admin_externalpage_setup('reportquestioninstances', '', null, '', array('pagelayout'=>'report'));
$PAGE->set_primary_active_tab('siteadminnode');
echo $OUTPUT->header();

// Log.
\report_questioninstances\event\report_viewed::create(array('other' => array('requestedqtype' => $requestedqtype)))->trigger();

// Prepare the list of capabilities to choose from
$qtypes = question_bank::get_all_qtypes();
$qtypechoices = array();
foreach ($qtypes as $qtype) {
    $qtypechoices[$qtype->name()] = $qtype->local_name();
}

// Print the settings form.
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');
echo '<form method="get" action="." id="settingsform"><div>';
echo $OUTPUT->heading(get_string('reportsettings', 'report_questioninstances'));
echo '<p id="intro">', get_string('intro', 'report_questioninstances') , '</p>';
echo '<p><label for="menuqtype"> ' . get_string('questiontype', 'admin') . '</label> ';
echo html_writer::select($qtypechoices, 'qtype', $requestedqtype, array('_all_'=>get_string('all')));
echo '</p>';
echo '<p><input type="submit" class="btn btn-secondary" id="settingssubmit" value="' .
        get_string('getreport', 'report_questioninstances') . '" /></p>';
echo '</div></form>';
echo $OUTPUT->box_end();

$params[] = \core_question\local\bank\question_version_status::QUESTION_STATUS_HIDDEN;
// If we have a qtype to report on, generate the report.
if ($requestedqtype) {

    // Work out the bits needed for the SQL WHERE clauses.
    if ($requestedqtype == 'missingtype') {
        $title = get_string('reportformissingqtypes', 'report_questioninstances');

        $othertypes = array_keys($qtypes);
        $key = array_search('missingtype', $othertypes);
        unset($othertypes[$key]);
        list($sqlqtypetest, $params) = $DB->get_in_or_equal($othertypes, SQL_PARAMS_QM, '', false);
        $sqlqtypetest = 'WHERE qtype ' . $sqlqtypetest;

    } else if ($requestedqtype == '_all_') {
        $title = get_string('reportforallqtypes', 'report_questioninstances');

        $sqlqtypetest = '';

    } else {
        $title = get_string('reportforqtype', 'report_questioninstances',
                question_bank::get_qtype($requestedqtype)->local_name());

        $sqlqtypetest = 'WHERE qtype = ?';
        $params [] = $requestedqtype;
    }

    // Get the question counts, and all the context information, for each
    // context. That is, rows of these results can be used as $context objects.
    $ctxpreload = context_helper::get_preload_record_columns_sql('con');
    $ctxgroupby = implode(',', array_keys(context_helper::get_preload_record_columns('con')));
    $counts = $DB->get_records_sql("
            SELECT result.contextid, SUM(numquestions) AS numquestions, SUM(numhidden) AS numhidden, $ctxpreload
              FROM (SELECT data.contextid, data.versionid, COUNT(data.numquestions) AS numquestions,
                           (SELECT COUNT(qv.id)
                              FROM {question_versions} qv
                             WHERE qv.id = data.versionid
                                   AND qv.status = ?) AS numhidden
                      FROM (SELECT qv.id as versionid, qc.contextid, 1 AS numquestions
                              FROM {question} q
                              JOIN {question_versions} qv ON qv.questionid = q.id
                              JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                              JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                              JOIN {context} con ON con.id = qc.contextid
                              $sqlqtypetest
                                   AND qv.version = (SELECT MAX(v.version)
                                                       FROM {question_versions} v
                                                       JOIN {question_bank_entries} be
                                                         ON be.id = v.questionbankentryid
                                                      WHERE be.id = qbe.id)
                                   AND (q.parent = 0 OR q.parent = q.id)) data
                  GROUP BY data.contextid, data.versionid) result
              JOIN {context} con ON con.id = result.contextid
          GROUP BY result.contextid, $ctxgroupby
          ORDER BY numquestions DESC, numhidden ASC, con.contextlevel ASC, con.id ASC", $params);

    // Print the report heading.
    echo $OUTPUT->heading($title);

    // Initialise the table.
    $table = new html_table();
    $table->head = array(
            get_string('context', 'role'),
            get_string('totalquestions', 'report_questioninstances'),
            get_string('visiblequestions', 'report_questioninstances'),
            get_string('hiddenquestions', 'report_questioninstances'));
    $table->data = array();
    $table->class = '';
    $table->id = '';

    // Add the data for each row.
    $totalquestions = 0;
    $totalvisible = 0;
    $totalhidden = 0;
    foreach ($counts as $count) {
        // Work out a link for editing questions in this context.
        context_helper::preload_from_record($count);
        $context = context::instance_by_id($count->contextid);
        $contextname = $context->get_context_name();
        $url = question_edit_url($context);
        if ($url) {
            $contextname = '<a href="' . $url . '" title="' .
                    get_string('editquestionshere', 'report_questioninstances') .
                    '">' . $contextname . '</a>';
        }

        // Put the scores in the table.
        $numvisible = $count->numquestions - $count->numhidden;
        $table->data[] = array(
                $contextname,
                $count->numquestions,
                $numvisible,
                $count->numhidden);

        // Update the totals.
        $totalquestions += $count->numquestions;
        $totalvisible += $numvisible;
        $totalhidden += $count->numhidden;
    }

    // Add a totals row.
    $table->data[] = array(
            '<b>' . get_string('total') . '</b>',
            $totalquestions,
            $totalvisible,
            $totalhidden);

    // Print it.
    echo html_writer::table($table);
}

// Footer.
echo $OUTPUT->footer();
