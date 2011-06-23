<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/adminlib.php');

// id of repository
$edit    = optional_param('edit', 0, PARAM_INT);
$new     = optional_param('new', '', PARAM_FORMAT);
$hide    = optional_param('hide', 0, PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);
$sure    = optional_param('sure', '', PARAM_ALPHA);
$type    = optional_param('type', '', PARAM_ALPHAEXT);

$context = get_context_instance(CONTEXT_SYSTEM);

$pagename = 'repositorycontroller';

if ($edit){
    $pagename = 'repositoryinstanceedit';
} else if ($delete) {
    $pagename = 'repositorydelete';
} else if ($new) {
    $pagename = 'repositoryinstancenew';
}

admin_externalpage_setup($pagename);
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$sesskeyurl = "$CFG->wwwroot/$CFG->admin/repositoryinstance.php?sesskey=" . sesskey();
$baseurl    = "$CFG->wwwroot/$CFG->admin/repository.php?session=". sesskey() .'&action=edit&repos=';
if ($new) {
    $baseurl .= $new;
}
else {
    $baseurl .= $type;
}

$return = true;

if (!empty($edit) || !empty($new)) {
    if (!empty($edit)) {
        $instance = repository::get_instance($edit);
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

    // display the edit form for this instance
    $mform = new repository_instance_form('', array('plugin' => $plugin, 'typeid' => $typeid, 'instance' => $instance, 'contextid' => $context->id));
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
            if (!$instance->readonly) {
                foreach($configs as $config) {
                    if (isset($fromform->$config)) {
                        $settings[$config] = $fromform->$config;
                    } else {
                        $settings[$config] = null;
                    }
                }
            }
            $success = $instance->set_option($settings);
        } else {
            $success = repository::static_function($plugin, 'create', $plugin, 0, get_system_context(), $fromform);
            $data = data_submitted();
        }
        if ($success) {
            redirect($baseurl);
        } else {
            print_error('instancenotsaved', 'repository', $baseurl);
        }
        exit;
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('configplugin', 'repository_'.$plugin));
        echo $OUTPUT->box_start();
        $mform->display();
        echo $OUTPUT->box_end();
        $return = false;
    }
} else if (!empty($hide)) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', '', $baseurl);
    }
    $instance = repository::get_type_by_typename($hide);
    $instance->hide();
    $return = true;
} else if (!empty($delete)) {
    $instance = repository::get_instance($delete);
    //if you try to delete an instance set as readonly, display an error message
    if ($instance->readonly) {
            throw new repository_exception('readonlyinstance', 'repository');
     }
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
    echo $OUTPUT->confirm(get_string('confirmdelete', 'repository', $instance->name), "$sesskeyurl&type=$type'&delete=$delete'&sure=yes", "$CFG->wwwroot/$CFG->admin/repositoryinstance.php?session=". sesskey());
    $return = false;
}

if (!empty($return)) {

    redirect($baseurl);
}
echo $OUTPUT->footer();
