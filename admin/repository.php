<?php
// $Id$
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/adminlib.php');

$CFG->pagepath = 'admin/managerepositories';

$edit    = optional_param('edit', 0, PARAM_ALPHA);
$new     = optional_param('new', '', PARAM_FORMAT);
$hide    = optional_param('hide', 0, PARAM_ALPHA);
$delete  = optional_param('delete', 0, PARAM_ALPHA);
$sure    = optional_param('sure', '', PARAM_ALPHA);
$move    = optional_param('move', '', PARAM_ALPHA);
$type    = optional_param('type', '', PARAM_ALPHA);

$display = true; // fall through to normal display

$pagename = 'repositorycontroller';

if ($edit) {
    $pagename = 'repositorysettings' . $edit;
} else if ($delete) {
    $pagename = 'repositorydelete';
} else if ($new) {
    $pagename = 'repositorynew';
}

admin_externalpage_setup($pagename);
require_login(SITEID, false);
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$sesskeyurl = $CFG->wwwroot . '/' . $CFG->admin . '/repository.php?sesskey=' . sesskey();
$baseurl    = $CFG->wwwroot . '/admin/settings.php?section=managerepositories';

$configstr  = get_string('managerepositories', 'repository');

$return = true;

if (!empty($edit) || !empty($new)) {
    if (!empty($edit)) {
        $repositorytype = repository_get_type_by_typename($edit);
        $classname = 'repository_' . $repositorytype->get_typename();
        $configs = call_user_func(array($classname,'get_admin_option_names'));
        $plugin = $repositorytype->get_typename();
    } else {
        $plugin = $new;
        $typeid = $new;
        $repositorytype = null;
    }
    $CFG->pagepath = 'admin/managerepository/' . $plugin;
    // display the edit form for this instance
    $mform = new repository_admin_form('', array('plugin' => $plugin, 'instance' => $repositorytype));
    $fromform = $mform->get_data();
    // end setup, begin output
    if ($mform->is_cancelled()){
        redirect($baseurl);
        exit;
    } else if (!empty($fromform) || (!empty($new) && !repository_static_function($new,"has_admin_config"))){
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($edit) {
            $settings = array();
            foreach($configs as $config) {
                $settings[$config] = $fromform->$config;
            }
            $success = $repositorytype->update_options($settings);
        } else {
            $type = new repository_type($plugin,(array)$fromform);
            $type->create();
            $success = true;
            $data = data_submitted();
        }
        if ($success) {
            $savedstr = get_string('configsaved', 'repository');
            admin_externalpage_print_header();
            print_heading($savedstr);
            redirect($baseurl, $savedstr, 3);
        } else {
            print_error('instancenotsaved', 'repository', $baseurl);
        }
        exit;
    } else {
        admin_externalpage_print_header();
        print_heading(get_string('configplugin', 'repository_'.$plugin));
        print_simple_box_start();
        $mform->display();
        print_simple_box_end();
        $return = false;

        //display instances list and creation form
        if ($edit){
             if (repository_static_function($edit,"has_instance_config")
                 || repository_static_function($edit,"has_multiple_instances")){
                repository_display_instances_list(get_context_instance(CONTEXT_SYSTEM), $edit);
           }
        }

    }
} else if (!empty($hide)) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', '', $baseurl);
    }
    $repositorytype = repository_get_type_by_typename($hide);
    $repositorytype->switch_and_update_visibility();
    $return = true;
} else if (!empty($delete)) {
    admin_externalpage_print_header();
    $repositorytype = repository_get_type_by_typename($delete);
    if ($sure) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($repositorytype->delete()) {
            $deletedstr = get_string('removed', 'repository');
            print_heading($deletedstr);
            redirect($baseurl, $deletedstr, 3);
        } else {
            print_error('instancenotdeleted', 'repository', $baseurl);
        }
        exit;
    }
    notice_yesno(get_string('confirmremove', 'repository', $repositorytype->get_readablename()), $sesskeyurl . '&amp;delete=' . $delete . '&amp;sure=yes', $baseurl);
    $return = false;
}
else if (!empty($move) && !empty($type)) {
    $repositorytype = repository_get_type_by_typename($type);
    $repositorytype->move_order($move);
}

if (!empty($return)) {
    redirect($baseurl);
}
admin_externalpage_print_footer();
