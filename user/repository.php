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

$navlinks[] = array('name' => $fullname, 'link' => $CFG->wwwroot . '/user/view.php?id=' . $user->id, 'type' => 'misc');
$navlinks[] = array('name' => $strrepos, 'link' => null, 'type' => 'misc');

$navigation = build_navigation($navlinks);

print_header("$course->fullname: $fullname: $strrepos", $course->fullname,
             $navigation, "", "", true, "&nbsp;", navmenu($course));

$currenttab = 'repositories';
include('tabs.php');

echo $OUTPUT->heading($configstr);
echo $OUTPUT->box_start();

if (!$instances = repository::get_instances($COURSE->context, $USER->id)) {
    print_error('noinstances', 'repository', $CFG->wwwroot . '/user/view.php');
}

$table = new StdClass;
$table->head = array($namestr, $pluginstr, '');
$table->data = array();

foreach ($instances as $i) {
    $path = '/repository/'.$i->type.'/settings.php';
    $settings = file_exists($CFG->dirroot.$path);
    $table->data[] = array($i->name, $i->type,
        $settings ? '<a href="'.$CFG->wwwroot.$path.'">'
            .get_string('settings', 'repository').'</a>' : '');
}

print_table($table);
echo $OUTPUT->footer();

?>
