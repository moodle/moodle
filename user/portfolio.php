<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');

$config = optional_param('config', 0, PARAM_INT);
$hide = optional_param('hide', 0, PARAM_INT);

$course  = optional_param('course', SITEID, PARAM_INT);

if (! $course = $DB->get_record("course", array("id"=>$course))) {
    print_error('invalidcourseid');
}

$user = $USER;
$fullname = fullname($user);
$strportfolios = get_string('portfolios', 'portfolio');
$configstr = get_string('manageyourportfolios', 'portfolio');
$namestr = get_string('name');
$pluginstr = get_string('plugin', 'portfolio');
$baseurl = $CFG->wwwroot . '/user/portfolio.php';

$display = true; // set this to false in the conditions to stop processing

require_login($course, false);

$navlinks[] = array('name' => $fullname, 'link' => $CFG->wwwroot . '/user/view.php?id=' . $user->id, 'type' => 'misc');
$navlinks[] = array('name' => $strportfolios, 'link' => null, 'type' => 'misc');

$navigation = build_navigation($navlinks);

print_header("$course->fullname: $fullname: $strportfolios", $course->fullname,
             $navigation, "", "", true, "&nbsp;", navmenu($course));

$currenttab = 'portfolioconf';
$showroles = 1;
include('tabs.php');

if (!empty($config)) {
    $instance = portfolio_instance($config);
    $mform = new portfolio_user_form('', array('instance' => $instance, 'userid' => $user->id));
    if ($mform->is_cancelled()){
        redirect($baseurl);
        exit;
    } else if ($fromform = $mform->get_data()){
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        //this branch is where you process validated data.
        $success = $instance->set_user_config($fromform, $USER->id);
            //$success = $success && $instance->save();
        if ($success) {
            redirect($baseurl, get_string('instancesaved', 'portfolio'), 3);
        } else {
            print_error('instancenotsaved', 'portfolio', $baseurl);
        }
        exit;
    } else {
        echo $OUTPUT->heading(get_string('configplugin', 'portfolio'));
        echo $OUTPUT->box_start();
        $mform->display();
        echo $OUTPUT->box_end();
        $display = false;
    }

} else if (!empty($hide)) {
    $instance = portfolio_instance($hide);
    $instance->set_user_config(array('visible' => !$instance->get_user_config('visible', $USER->id)), $USER->id);
}

if ($display) {
    echo $OUTPUT->heading($configstr);
    echo $OUTPUT->box_start();

    if (!$instances = portfolio_instances(true, false)) {
        print_error('noinstances', 'portfolio', $CFG->wwwroot . '/user/view.php');
    }

    $table = new html_table();
    $table->head = array($namestr, $pluginstr, '');
    $table->data = array();

    foreach ($instances as $i) {
        $visible = $i->get_user_config('visible', $USER->id);
        $table->data[] = array($i->get('name'), $i->get('plugin'),
            ($i->has_user_config()
                ?  '<a href="' . $baseurl . '?config=' . $i->get('id') . '"><img src="' . $OUTPUT->old_icon_url('t/edit') . '" alt="' . get_string('configure') . '" /></a>' : '') .
                   ' <a href="' . $baseurl . '?hide=' . $i->get('id') . '"><img src="' . $OUTPUT->old_icon_url('t/' . (($visible) ? 'hide' : 'show')) . '" alt="' . get_string($visible ? 'hide' : 'show') . '" /></a><br />'
        );
    }

    echo $OUTPUT->table($table);
}
echo $OUTPUT->footer();

?>
