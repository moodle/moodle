<?php  // $Id$
/**
 * For a given question type, list the number of 
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 */

/** */
require_once(dirname(__FILE__).'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/questionlib.php');

// Check permissions.
require_login();
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/site:viewreports', $systemcontext);

// Get URL parameters.
$requestedqtype = optional_param('qtype', '', PARAM_SAFEDIR);

// Log.
add_to_log(SITEID, "admin", "report questioninstances", "report/questioninstances/index.php?qtype=$requestedqtype", $requestedqtype);

// Print the header.
admin_externalpage_setup('reportquestioninstances');
admin_externalpage_print_header();

// Prepare the list of capabilites to choose from
$qtypechoices = array();
foreach ($QTYPES as $qtype) {
    $qtypechoices[$qtype->name()] = $qtype->local_name();
}

// Print the settings form.
print_box_start('generalbox boxwidthwide boxaligncenter centerpara');
echo '<form method="get" action="." id="settingsform"><div>';
print_heading(get_string('reportsettings', 'report_questioninstances'));
echo '<p id="intro">', get_string('intro', 'report_questioninstances') , '</p>';
echo '<p><label for="menuqtype"> ' . get_string('questiontype', 'admin') . '</label> ';
choose_from_menu($qtypechoices, 'qtype', $requestedqtype);
echo '</p>';
echo '<p><input type="submit" id="settingssubmit" value="' .
        get_string('getreport', 'report_questioninstances') . '" /></p>';
echo '</div></form>';
print_box_end();

// If we have a qtype to report on, generate the report.
if ($requestedqtype) {

    // Work out the bits needed for the SQL WHERE clauses.
    if ($requestedqtype == 'missingtype') {
        $othertypes = array_keys($QTYPES);
        $key = array_search('missingtype', $othertypes);
        unset($othertypes[$key]);
        list($sqlqtypetest, $params) = $DB->get_in_or_equals($othertypes, SQL_PARAMS_QM, '', false);
    } else {
        $sqlqtypetest = '= ?';
        $params = array($requestedqtype);
    }

    // Get all the role_capabilities rows for this capability - that is, all
    // role definitions, and all role overrides.
    $counts = $DB->get_records_sql("
            SELECT qc.contextid, count(1) as numquestions, sum(hidden) as numhidden, con.id, con.contextlevel, con.instanceid, con.path, con.depth 
            FROM {question} q
            JOIN {question_categories} qc ON q.category = qc.id
            JOIN {context} con ON con.id = qc.contextid
            WHERE qtype $sqlqtypetest
            GROUP BY contextid, con.id, con.contextlevel, con.instanceid, con.path, con.depth
            ORDER BY numquestions DESC, numhidden ASC", $params);

    // Print the report heading.
    print_heading(get_string('reportforqtype', 'report_questioninstances', $QTYPES[$requestedqtype]->local_name()));

    // Now, print the table of results.
    // TODO
    print_object($counts);
}

// Footer.
admin_externalpage_print_footer();
?>
