<?php
// $Id$
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');
//require_once($CFG->libdir . '/adminlib.php');

// id of repository
$edit    = optional_param('edit', 0, PARAM_INT);
$new     = optional_param('new', '', PARAM_FORMAT);
$delete  = optional_param('delete', 0, PARAM_INT);
$sure    = optional_param('sure', '', PARAM_ALPHA);
$contextid = optional_param('contextid', 0, PARAM_INT);

$display = true; // fall through to normal display

if ($edit){
    $pagename = 'repositoryinstanceedit';
}else if ($delete) {
    $pagename = 'repositorydelete';
} else if ($new) {
    $pagename = 'repositoryinstancenew';
}
else {
    $pagename = 'repositorylist';
}

require_login(SITEID, false);

$context = get_context_instance_by_id($contextid);

//security: detect if we are in a course context
if ($context->contextlevel == CONTEXT_COURSE) {
    $pagename = get_string("repositorycourse",'repository');

    //is the user is allowed to edit this course, he's allowed to edit list of repository instances
    require_capability('moodle/course:update',  $context);
    //retrieve course
    //Retrieve the course object
    if ( !$course = $DB->get_record('course', array('id'=>$context->instanceid))) {
        print_error('invalidcourseid');
    }
}

$baseurl    = $CFG->wwwroot . '/repository/manage_instances.php?contextid=' . $contextid . '&amp;sesskey='. sesskey();

//security: we cannot perform any action if the type is not visible
if (!empty($new)){
    $type = repository_get_type_by_typename($new);
} else if (!empty($edit)){
    $instance = repository_get_instance($edit);
    $type = repository_get_type_by_id($instance->typeid);
} else if (!empty($delete)){
    $instance = repository_get_instance($delete);
    $type = repository_get_type_by_id($instance->typeid);
}
if (isset($type) && !$type->get_visible()) {
    print_error('typenotvisible', 'repository', $baseurl);
}



//Create header crumbtrail
//$streditrepositoryaccount = get_string("editrepositoryinstance",'repository');
$navlinks = array();
if (!empty($course)) {
    $navlinks[] = array('name' => $course->shortname,
                        'link' => "$CFG->wwwroot/course/view.php?id=$course->id",
                        'type' => 'misc');
    $navlinks[] = array('name' => $pagename,
                        'link' => null,
                        'type' => 'misc');
    $title = $pagename;
    $fullname = $course->fullname;
}
$navigation = build_navigation($navlinks);

//display page header
print_header($title, $fullname, $navigation);
print_heading($pagename);

$return = true;

if (!empty($edit) || !empty($new)) {
    if (!empty($edit)) {
        $instance = repository_get_instance($edit);
        $instancetype = repository_get_type_by_id($instance->typeid);
        $classname = 'repository_' . $instancetype->get_typename();
        $configs  = $instance->get_instance_option_names();
        $plugin = $instancetype->get_typename();
        $typeid = $instance->typeid;
    } else {
        $plugin = $new;
        $typeid = $new;
        $instance = null;
    }

    // create edit form for this instance
    $mform = new repository_instance_form('', array('plugin' => $plugin, 'typeid' => $typeid,'instance' => $instance, 'contextid' => $contextid));
    // end setup, begin output

    if ($mform->is_cancelled()){
        redirect($baseurl);
        exit;
    } else if ($fromform = $mform->get_data()){
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($edit) {
            $settings = array();
            $settings['name'] = $fromform->name;
            foreach($configs as $config) {
                $settings[$config] = $fromform->$config;
            }
            $success = $instance->set_option($settings);
        } else {
            $success = repository_static_function($plugin, 'create', $plugin, 0, get_context_instance_by_id($contextid), $fromform);
            $data = data_submitted();
        }
        if ($success) {
            $savedstr = get_string('configsaved', 'repository');
            //admin_externalpage_print_header();
            print_heading($savedstr);
            redirect($baseurl, $savedstr, 3);
        } else {
            print_error('instancenotsaved', 'repository', $baseurl);
        }
        exit;
    } else {
       // admin_externalpage_print_header();
        print_heading(get_string('configplugin', 'repository_'.$plugin));
        print_simple_box_start();
        $mform->display();
        print_simple_box_end();
        $return = false;
    }
} else if (!empty($delete)) {
   // admin_externalpage_print_header();
    $instance = repository_get_instance($delete);
    if ($sure) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($instance->delete()) {
            $deletedstr = get_string('instancedeleted', 'repository');
            print_heading($deletedstr);
            redirect($baseurl, $deletedstr, 3);
        } else {
            print_error('instancenotdeleted', 'repository', $baseurl);
        }
        exit;
    }
    notice_yesno(get_string('confirmdelete', 'repository', $instance->name), $baseurl . '&amp;delete=' . $delete . '&amp;sure=yes', $baseurl);
    $return = false;
} else {
    repository_display_instances_list($context);
    $return = false;
}

if (!empty($return)) {
    redirect($baseurl);
}

//display page footer
print_footer($course);