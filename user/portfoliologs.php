<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');

$course  = optional_param('course', SITEID, PARAM_INT);
if (! $course = $DB->get_record("course", array("id"=>$course))) {
    print_error('invalidcourseid');
}

$user = $USER;
$fullname = fullname($user);
$strportfolios = get_string('portfolios', 'portfolio');

require_login($course, false);

$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);

$PAGE->set_title("$course->fullname: $fullname: $strportfolios");
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$currenttab = 'portfoliologs';
$showroles = 1;
include('tabs.php');

$queued = $DB->get_records('portfolio_tempdata', array('userid' => $USER->id), '', 'id, expirytime');
if (count($queued) > 0) {
    $table = new html_table();
    $table->head = array(
        get_string('displayarea', 'portfolio'),
        get_string('plugin', 'portfolio'),
        get_string('displayinfo', 'portfolio'),
        get_string('displayexpiry', 'portfolio'),
    );
    $table->data = array();
    foreach ($queued as $q){
        $e = portfolio_exporter::rewaken_object($q->id);
        $e->verify_rewaken(true);
        $table->data[] = array(
            $e->get('caller')->display_name(),
            $e->get('instance')->get('name'),
            $e->get('caller')->heading_summary(),
            userdate($q->expirytime),
        );
        unset($e); // this could potentially be quite big, so free it.
    }
    echo $OUTPUT->heading(get_string('queuesummary', 'portfolio'));
    echo $OUTPUT->table($table);
}
$logcount = $DB->count_records('portfolio_log', array('userid' => $USER->id));
if ($logcount > 0) {
    $table = new html_table();
    $table->head = array(
        get_string('plugin', 'portfolio'),
        get_string('displayarea', 'portfolio'),
        get_string('transfertime', 'portfolio'),
    );
    $logs = $DB->get_records('portfolio_log', array('userid' => $USER->id), 'time DESC', '*', ($page * $perpage), $perpage);
    foreach ($logs as $log) {
        require_once($CFG->dirroot . $log->caller_file);
        $class = $log->caller_class;
        $pluginname = '';
        try {
            $plugin = portfolio_instance($log->portfolio);
            $pluginname = $plugin->get('name');
        } catch (portfolio_exception $e) { // may have been deleted
            $pluginname = get_string('unknownplugin', 'portfolio');
        }

        $table->data[] = array(
            $pluginname,
            call_user_func(array($class, 'display_name')),
            userdate($log->time),
        );
    }
    echo $OUTPUT->heading(get_string('logsummary', 'portfolio'));
    $pagingbar = moodle_paging_bar::make($logcount, $page, $perpage, $CFG->wwwroot . '/user/portfoliologs.php?');
    echo $OUTPUT->paging_bar($pagingbar);
    echo $OUTPUT->table($table);
    echo $OUTPUT->paging_bar($pagingbar);

}

echo $OUTPUT->footer();

?>
