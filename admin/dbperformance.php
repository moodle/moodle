<?PHP
// dbperformance.php - shows latest ADOdb stats for the current server

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

require_once('../config.php');

error('TODO: rewrite db perf code'); // TODO: rewrite


    $topframe    = optional_param('topframe', 0, PARAM_BOOL);
    $bottomframe = optional_param('bottomframe', 0, PARAM_BOOL);
    $do          = optional_param('do', '', PARAM_ALPHA);

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    $strdatabaseperformance = get_string("databaseperformance");
    $stradministration = get_string("administration");
    $site = get_site();

    if (!empty($topframe)) {
        $PAGE->set_url('/admin/dbperformance.php');
        $PAGE->navbar->add($stradministration, new moodle_url('/admin/index.php'));
        $PAGE->navbar->add($strdatabaseperformance);
        $PAGE->set_title("$site->shortname: $strdatabaseperformance");
        $PAGE->set_heading($site->fullname);
        echo $OUTPUT->header();
        exit;
    }
