<?php //$Id$

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');

$config = optional_param('config', 0, PARAM_INT);

$course  = optional_param('course', SITEID, PARAM_INT);

if (! $course = $DB->get_record("course", array("id"=>$course))) {
    print_error('invalidcourseid');
}

$user = $USER;
$baseurl = $CFG->wwwroot . '/user/repository.php';
$namestr = get_string('name');
$fullname = fullname($user);
$strrepos = get_string('repositories', 'repository');
$configstr = get_string('manageuserrepository', 'repository');
$pluginstr = get_string('plugin', 'repository');

require_login($course, false);

$link = new moodle_url($CFG->wwwroot . '/user/view.php', array('id'=>$user->id));
$PAGE->navbar->add($fullname, $link);
$PAGE->navbar->add($strrepos);
$PAGE->set_title("$course->fullname: $fullname: $strrepos");
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$currenttab = 'repositories';
include('tabs.php');

echo $OUTPUT->heading($configstr);
echo $OUTPUT->box_start();

if (!$instances = repository::get_instances($COURSE->context, $USER->id)) {
    print_error('noinstances', 'repository', $CFG->wwwroot . '/user/view.php');
}

$table = new html_table();
$table->head = array($namestr, $pluginstr, '');
$table->data = array();

foreach ($instances as $i) {
    $path = '/repository/'.$i->type.'/settings.php';
    $settings = file_exists($CFG->dirroot.$path);
    $table->data[] = array($i->name, $i->type,
        $settings ? '<a href="'.$CFG->wwwroot.$path.'">'
            .get_string('settings', 'repository').'</a>' : '');
}

echo $OUTPUT->table($table);
echo $OUTPUT->footer();

?>
