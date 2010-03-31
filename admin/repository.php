<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/adminlib.php');

$edit    = optional_param('edit', 0, PARAM_FORMAT);
$new     = optional_param('new', '', PARAM_FORMAT);
$hide    = optional_param('hide', '', PARAM_FORMAT);
$delete  = optional_param('delete', 0, PARAM_FORMAT);
$sure    = optional_param('sure', '', PARAM_ALPHA);
$move    = optional_param('move', '', PARAM_ALPHANUM);
$type    = optional_param('type', '', PARAM_ALPHANUM);

$display = true; // fall through to normal display

$pagename = 'repositorycontroller';

if ($edit) {
    $pagename = 'repositorysettings' . $edit;
} else if ($delete) {
    $pagename = 'repositorydelete';
} else if ($new) {
    $pagename = 'repositorynew';
}

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));
admin_externalpage_setup($pagename);

$sesskeyurl = $CFG->wwwroot.'/'.$CFG->admin.'/repository.php?sesskey=' . sesskey();
$baseurl    = $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=managerepositories';

$configstr  = get_string('managerepositories', 'repository');

$return = true;

if (!empty($edit) || !empty($new)) {
    if (!empty($edit)) {
        $repositorytype = repository::get_type_by_typename($edit);
        $classname = 'repository_' . $repositorytype->get_typename();
        $configs = call_user_func(array($classname,'get_type_option_names'));
        $plugin = $repositorytype->get_typename();
    } else {
        $repositorytype = null;
        $plugin = $new;
        $typeid = $new;
    }
    $PAGE->set_pagetype('admin-repository-' . $plugin);
    // display the edit form for this instance
    $mform = new repository_type_form('', array('plugin' => $plugin, 'instance' => $repositorytype));
    $fromform = $mform->get_data();

    //detect if we create a new type without config (in this case if don't want to display a setting page during creation)
    $nosettings = false;
    if (!empty($new)) {
        $adminconfignames = repository::static_function($new, 'get_type_option_names');
        $nosettings = empty($adminconfignames);
    }
    // end setup, begin output

    if ($mform->is_cancelled()){
        redirect($baseurl);
    } else if (!empty($fromform) || $nosettings) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($edit) {
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
             $instanceoptionnames = repository::static_function($edit, 'get_instance_option_names');
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
            $type = new repository_type($plugin, (array)$fromform);
            $type->create();
            $success = true;
            $data = data_submitted();
        }
        if ($success) {
            $savedstr = get_string('configsaved', 'repository');
            $has_instance = repository::static_function($plugin, 'get_instance_option_names');

            if (!empty($has_instance)) {
                // no common setting for this type, so go to setup instances
                redirect($sesskeyurl.'&amp;edit='.$plugin, $savedstr, 1);
            } else {
                // configs saved
                redirect($baseurl, $savedstr, 1);
            }
        } else {
            print_error('instancenotsaved', 'repository', $baseurl);
        }
        exit;
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('configplugin', 'repository_'.$plugin));
        $displaysettingform = true;
        if ($edit) {
            $typeoptionnames = repository::static_function($edit, 'get_type_option_names');
            $instanceoptionnames = repository::static_function($edit, 'get_instance_option_names');
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

        //display instances list and creation form
        if ($edit){
             $instanceoptionnames = repository::static_function($edit, 'get_instance_option_names');
             if (!empty($instanceoptionnames)){
                repository::display_instances_list(get_context_instance(CONTEXT_SYSTEM), $edit);
           }
        }

    }
} else if (!empty($hide)) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', '', $baseurl);
    }
    $repositorytype = repository::get_type_by_typename($hide);
    if (empty($repositorytype)) {
        print_error('invalidplugin', 'repository', '', $hide);
    }
    $repositorytype->switch_and_update_visibility();
    $return = true;
} else if (!empty($delete)) {
    $repositorytype = repository::get_type_by_typename($delete);
    if ($sure) {
        $PAGE->set_pagetype('admin-repository-' . $delete);
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        if ($repositorytype->delete()) {
            $deletedstr = get_string('removed', 'repository');
            redirect($baseurl, $deletedstr, 3);
        } else {
            print_error('instancenotdeleted', 'repository', $baseurl);
        }
        exit;
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('confirmremove', 'repository', $repositorytype->get_readablename()), $sesskeyurl . '&delete=' . $delete . '&sure=yes', $baseurl);
        $return = false;
    }
}
else if (!empty($move) && !empty($type)) {
    $repositorytype = repository::get_type_by_typename($type);
    $repositorytype->move_order($move);
}

if (!empty($return)) {
    redirect($baseurl);
}
echo $OUTPUT->footer();
