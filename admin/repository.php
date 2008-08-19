<?php // $Id$
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/adminlib.php');

$CFG->pagepath = 'admin/managerepositories';

// id of repository
$edit    = optional_param('edit', 0, PARAM_INT);
$new     = optional_param('new', '', PARAM_FORMAT);
$hide    = optional_param('hide', 0, PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);
$sure    = optional_param('sure', '', PARAM_ALPHA);

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
        $instance = repository_instance($edit);
        $configs  = $instance->get_option_names();
        $plugin = $instance->type;
    } else {
        $plugin = $new;
        $instance = null;
    }
    $CFG->pagepath = 'admin/managerepository/' . $plugin;
    // display the edit form for this instance
    $mform = new repository_admin_form('', array('plugin' => $plugin, 'instance' => $instance));
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
            $success = repository_static_function($plugin, 'create', $plugin, 0, get_system_context(), $fromform);
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
    }
} else if (!empty($hide)) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', '', $baseurl);
    }
    $instance = repository_instance($hide);
    $instance->hide();
    $return = true;
} else if (!empty($delete)) {
    admin_externalpage_print_header();
    $instance = repository_instance($delete);
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
    notice_yesno(get_string('confirmdelete', 'repository', $instance->name), $sesskeyurl . '&delete=' . $delete . '&sure=yes', $baseurl);
    $return = false;
}


if (!empty($return)) {
    redirect($baseurl);
}
admin_externalpage_print_footer();
