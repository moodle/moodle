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
 * This file is used to manage repositories
 *
 * @since Moodle 2.0
 * @package    core
 * @subpackage repository
 * @copyright  2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');

$edit    = optional_param('edit', 0, PARAM_INT);
$new     = optional_param('new', '', PARAM_ALPHANUMEXT);
$delete  = optional_param('delete', 0, PARAM_INT);
$sure    = optional_param('sure', '', PARAM_ALPHA);
$contextid = optional_param('contextid', 0, PARAM_INT);
$usercourseid = optional_param('usercourseid', SITEID, PARAM_INT);  // Extra: used for user context only

$url = new moodle_url('/repository/manage_instances.php');

$baseurl = new moodle_url('/repository/manage_instances.php');
$baseurl->param('sesskey', sesskey());

if ($edit){
    $url->param('edit', $edit);
    $pagename = 'repositoryinstanceedit';
} else if ($delete) {
    $url->param('delete', $delete);
    $pagename = 'repositorydelete';
} else if ($new) {
    $url->param('new', $new);
    $pagename = 'repositoryinstancenew';
} else {
    $pagename = 'repositorylist';
}

if ($sure !== '') {
    $url->param('sure', $sure);
}
if ($contextid !== 0) {
    $url->param('contextid', $contextid);
    $baseurl->param('contextid', $contextid);
}
if ($usercourseid != SITEID) {
    $url->param('usercourseid', $usercourseid);
}

$context = context::instance_by_id($contextid);

$PAGE->set_url($url);
$PAGE->set_context($context);

/// Security: make sure we're allowed to do this operation
if ($context->contextlevel == CONTEXT_COURSE) {
    $pagename = get_string("repositorycourse",'repository');

    if ( !$course = $DB->get_record('course', array('id'=>$context->instanceid))) {
        print_error('invalidcourseid');
    }
    require_login($course, false);
    // If the user is allowed to edit this course, he's allowed to edit list of repository instances
    require_capability('moodle/course:update',  $context);


} else if ($context->contextlevel == CONTEXT_USER) {
    require_login();
    $pagename = get_string("personalrepositories",'repository');
    //is the user looking at its own repository instances
    if ($USER->id != $context->instanceid){
        print_error('notyourinstances', 'repository');
    }
    $user = $USER;
    $PAGE->set_pagelayout('mydashboard');
} else {
    print_error('invalidcontext');
}

/// Security: we cannot perform any action if the type is not visible or if the context has been disabled
if (!empty($new) && empty($edit)){
    $type = repository::get_type_by_typename($new);
} else if (!empty($edit)){
    $instance = repository::get_instance($edit);
    $type = repository::get_type_by_id($instance->options['typeid']);
} else if (!empty($delete)){
    $instance = repository::get_instance($delete);
    $type = repository::get_type_by_id($instance->options['typeid']);
}

if (isset($type)) {
    if (!$type->get_visible()) {
        print_error('typenotvisible', 'repository', $baseurl);
    }
    // Prevents the user from creating/editing an instance if the repository is not visible in
    // this context OR if the user does not have the capability to view this repository in this context.
    $canviewrepository = has_capability('repository/'.$type->get_typename().':view', $context);
    if (!$type->get_contextvisibility($context) || !$canviewrepository) {
        print_error('usercontextrepositorydisabled', 'repository', $baseurl);
    }
}

// We have an instance when we are going to edit, or delete. Several checks need to be done!
if (!empty($instance)) {
    // The context passed MUST match the context of the repository. And as both have to be
    // similar, this also ensures that the context is either a user one, or a course one.
    if ($instance->instance->contextid != $context->id) {
        print_error('invalidcontext');
    }
    if ($instance->readonly) {
        // Cannot edit, or delete a readonly instance.
        throw new repository_exception('readonlyinstance', 'repository');
    } else if (!$instance->can_be_edited_by_user()) {
        // The user has to have the right to edit the instance.
        throw new repository_exception('nopermissiontoaccess', 'repository');
    }
}

/// Create navigation links
if (!empty($course)) {
    $PAGE->navbar->add($pagename);
    $fullname = $course->fullname;
} else {
    $fullname = fullname($user);
    $strrepos = get_string('repositories', 'repository');
    $PAGE->navbar->add($fullname, new moodle_url('/user/view.php', array('id'=>$user->id)));
    $PAGE->navbar->add($strrepos);
}

// Display page header.
$PAGE->set_title($pagename);
$PAGE->set_heading($fullname);

$return = true;
if (!empty($edit) || !empty($new)) {
    if (!empty($edit)) {
        $instancetype = repository::get_type_by_id($instance->options['typeid']);
        $classname = 'repository_' . $instancetype->get_typename();
        $configs  = $instance->get_instance_option_names();
        $plugin = $instancetype->get_typename();
        $typeid = $instance->options['typeid'];
    } else {
        $plugin = $new;
        $typeid = $new;
        $instance = null;
    }

/// Create edit form for this instance
    $mform = new repository_instance_form('', array('plugin' => $plugin, 'typeid' => $typeid,'instance' => $instance, 'contextid' => $contextid));

/// Process the form data if any, or display
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
                $settings[$config] = isset($fromform->$config) ? $fromform->$config : null;
            }
            $success = $instance->set_option($settings);
        } else {
            $success = repository::static_function($plugin, 'create', $plugin, 0, context::instance_by_id($contextid), $fromform);
            $data = data_submitted();
        }
        if ($success) {
            $savedstr = get_string('configsaved', 'repository');
            redirect($baseurl);
        } else {
            print_error('instancenotsaved', 'repository', $baseurl);
        }
        exit;
    } else {     // Display the form
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('configplugin', 'repository_'.$plugin));
        $OUTPUT->box_start();
        $mform->display();
        $OUTPUT->box_end();
        $return = false;
    }
} else if (!empty($delete)) {
    if ($sure) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($instance->delete()) {
            $deletedstr = get_string('instancedeleted', 'repository');
            redirect($baseurl, $deletedstr, 3);
        } else {
            print_error('instancenotdeleted', 'repository', $baseurl);
        }
        exit;
    }
    echo $OUTPUT->header();
    $formcontinue = new single_button(new moodle_url($baseurl, array('delete' => $delete, 'sure' => 'yes')), get_string('yes'));
    $formcancel = new single_button($baseurl, get_string('no'));
    echo $OUTPUT->confirm(get_string('confirmdelete', 'repository', $instance->name), $formcontinue, $formcancel);
    $return = false;
} else {
    echo $OUTPUT->header();
    repository::display_instances_list($context);
    $return = false;
}

if (!empty($return)) {
    redirect($baseurl);
}

echo $OUTPUT->footer();
