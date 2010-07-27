<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/adminlib.php');

$repository    = optional_param('repos', '', PARAM_FORMAT);
$action        = optional_param('action', '', PARAM_ALPHA);
$sure          = optional_param('sure', '', PARAM_ALPHA);

$display = true; // fall through to normal display

$pagename = 'repositorycontroller';

if ($action == 'edit') {
    $pagename = 'repositorysettings' . $repository;
} else if ($action == 'delete') {
    $pagename = 'repositorydelete';
} else if (($action == 'newon') || ($action == 'newoff')) {
    $pagename = 'repositorynew';
}

// Need to remember this for form
$formaction = $action;

// Check what visibility to show the new repository
if ($action == 'newon') {
    $action = 'new';
    $visible = true;
} else if ($action == 'newoff') {
    $action = 'new';
    $visible = false;
}

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));
admin_externalpage_setup($pagename);

$sesskeyurl = $CFG->wwwroot.'/'.$CFG->admin.'/repository.php?sesskey=' . sesskey();
$baseurl    = $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=managerepositories';

$configstr  = get_string('manage', 'repository');

$return = true;

if (($action == 'edit') || ($action == 'new')) {
    $pluginname = '';
    if ($action == 'edit') {
        $repositorytype = repository::get_type_by_typename($repository);
        $classname = 'repository_' . $repositorytype->get_typename();
        $configs = call_user_func(array($classname, 'get_type_option_names'));
        $plugin = $repositorytype->get_typename();
        // looking for instance to edit plugin name
        $instanceoptions = $classname::get_instance_option_names();
        if (empty($instanceoptions)) {
            $params = array();
            $params['type'] = $plugin;
            $instances = repository::get_instances($params);
            if ($instance = array_pop($instances)) {
                // use the one form db record
                $pluginname = $instance->instance->name;
            }
        }

    } else {
        $repositorytype = null;
        $plugin = $repository;
        $typeid = $repository;
    }
    $PAGE->set_pagetype('admin-repository-' . $plugin);
    // display the edit form for this instance
    $mform = new repository_type_form('', array('pluginname'=>$pluginname, 'plugin' => $plugin, 'instance' => $repositorytype, 'action' => $formaction));
    $fromform = $mform->get_data();

    //detect if we create a new type without config (in this case if don't want to display a setting page during creation)
    $nosettings = false;
    if ($action == 'new') {
        $adminconfignames = repository::static_function($repository, 'get_type_option_names');
        $nosettings = empty($adminconfignames);
    }
    // end setup, begin output

    if ($mform->is_cancelled()){
        redirect($baseurl);
    } else if (!empty($fromform) || $nosettings) {
        require_sesskey();
        if ($action == 'edit') {
            $settings = array();
            foreach($configs as $config) {
                if (!empty($fromform->$config)) {
                    $settings[$config] = $fromform->$config;
                } else {
                    // if the config name is not appear in $fromform
                    // empty this config value
                    $settings[$config] = '';
                }
            }
            $instanceoptionnames = repository::static_function($repository, 'get_instance_option_names');
            if (!empty($instanceoptionnames)) {
                if (array_key_exists('enablecourseinstances', $fromform)) {
                    $settings['enablecourseinstances'] = $fromform->enablecourseinstances;
                }
                else {
                    $settings['enablecourseinstances'] = 0;
                }
                if (array_key_exists('enableuserinstances', $fromform)) {
                    $settings['enableuserinstances'] = $fromform->enableuserinstances;
                }
                else {
                    $settings['enableuserinstances'] = 0;
                }
            }
            $success = $repositorytype->update_options($settings);
        } else {
            $type = new repository_type($plugin, (array)$fromform, $visible);
            $type->create();
            $success = true;
            $data = data_submitted();
        }
        if ($success) {
            // configs saved
            redirect($baseurl);
        } else {
            print_error('instancenotsaved', 'repository', $baseurl);
        }
        exit;
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('configplugin', 'repository_'.$plugin));
        $displaysettingform = true;
        if ($action == 'edit') {
            $typeoptionnames = repository::static_function($repository, 'get_type_option_names');
            $instanceoptionnames = repository::static_function($repository, 'get_instance_option_names');
            if (empty($typeoptionnames) && empty($instanceoptionnames)) {
                $displaysettingform = false;
            }
        }
        if ($displaysettingform){
            $OUTPUT->box_start();
            $mform->display();
            $OUTPUT->box_end();
        }
        $return = false;

        // Display instances list and creation form
        if ($action == 'edit') {
           $instanceoptionnames = repository::static_function($repository, 'get_instance_option_names');
           if (!empty($instanceoptionnames)) {
               repository::display_instances_list(get_context_instance(CONTEXT_SYSTEM), $repository);
           }
        }
    }
} else if ($action == 'show') {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', '', $baseurl);
    }
    $repositorytype = repository::get_type_by_typename($repository);
    if (empty($repositorytype)) {
        print_error('invalidplugin', 'repository', '', $repository);
    }
    $repositorytype->update_visibility(true);
    $return = true;
} else if ($action == 'hide') {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', '', $baseurl);
    }
    $repositorytype = repository::get_type_by_typename($repository);
    if (empty($repositorytype)) {
        print_error('invalidplugin', 'repository', '', $repository);
    }
    $repositorytype->update_visibility(false);
    $return = true;
} else if ($action == 'delete') {
    $repositorytype = repository::get_type_by_typename($repository);
    if ($sure) {
        $PAGE->set_pagetype('admin-repository-' . $repository);
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($repositorytype->delete()) {
            redirect($baseurl);
        } else {
            print_error('instancenotdeleted', 'repository', $baseurl);
        }
        exit;
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('confirmremove', 'repository', $repositorytype->get_readablename()), $sesskeyurl . '&action=delete&repos=' . $repository . '&sure=yes', $baseurl);
        $return = false;
    }
} else if ($action == 'moveup') {
    $repositorytype = repository::get_type_by_typename($repository);
    $repositorytype->move_order('up');
} else if ($action == 'movedown') {
    $repositorytype = repository::get_type_by_typename($repository);
    $repositorytype->move_order('down');
}

if (!empty($return)) {
    redirect($baseurl);
}
echo $OUTPUT->footer();
