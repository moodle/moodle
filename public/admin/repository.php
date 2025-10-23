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

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/adminlib.php');

$repository       = optional_param('repos', '', PARAM_ALPHANUMEXT);
$action           = optional_param('action', '', PARAM_ALPHANUMEXT);
$sure             = optional_param('sure', '', PARAM_ALPHA);
$downloadcontents = optional_param('downloadcontents', false, PARAM_BOOL);

$display = true; // fall through to normal display

$pagename = 'managerepositories';

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

admin_externalpage_setup($pagename);

// The URL used for redirection, and that all edit related URLs will be based off.
$baseurl = new moodle_url('/admin/repository.php');

$return = true;

if (($action == 'edit') || ($action == 'new')) {
    $pluginname = '';
    if ($action == 'edit') {
        $repositorytype = repository::get_type_by_typename($repository);
        $classname = 'repository_' . $repositorytype->get_typename();
        $configs = call_user_func(array($classname, 'get_type_option_names'));
        $plugin = $repositorytype->get_typename();
        // looking for instance to edit plugin name
        $instanceoptions = call_user_func(array($classname, 'get_instance_option_names'));
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
                if (property_exists($fromform, 'enablecourseinstances')) {
                    $settings['enablecourseinstances'] = $fromform->enablecourseinstances;
                }
                else {
                    $settings['enablecourseinstances'] = 0;
                }
                if (property_exists($fromform, 'enableuserinstances')) {
                    $settings['enableuserinstances'] = $fromform->enableuserinstances;
                }
                else {
                    $settings['enableuserinstances'] = 0;
                }
            }
            $success = $repositorytype->update_options($settings);
        } else {
            $type = new repository_type($plugin, (array)$fromform, $visible);
            $success = true;
            if (!$repoid = $type->create()) {
                $success = false;
            } else {
                add_to_config_log('repository_visibility', '', (int)$visible, $plugin);
            }
            $data = data_submitted();
        }
        if ($success) {
            // configs saved
            core_plugin_manager::reset_caches();
            redirect($baseurl);
        } else {
            throw new \moodle_exception('instancenotsaved', 'repository', $baseurl);
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
                repository::display_instances_list(context_system::instance(), $repository);
            }
        }
    }
} else if ($action == 'show') {
    require_sesskey();
    $class = \core_plugin_manager::resolve_plugininfo_class('repository');
    $class::enable_plugin($repository, 1);
    $return = true;
} else if ($action == 'hide') {
    require_sesskey();
    $class = \core_plugin_manager::resolve_plugininfo_class('repository');
    $class::enable_plugin($repository, 0);
    $return = true;
} else if ($action == 'delete') {
    $repositorytype = repository::get_type_by_typename($repository);
    if ($sure) {
        $PAGE->set_pagetype('admin-repository-' . $repository);
        require_sesskey();

        if ($repositorytype->delete($downloadcontents)) {
            // Include this information into config changes table.
            add_to_config_log('repository_visibility', $repositorytype->get_visible(), '', $repository);
            core_plugin_manager::reset_caches();
            redirect($baseurl);
        } else {
            throw new \moodle_exception('instancenotdeleted', 'repository', $baseurl);
        }
        exit;
    } else {
        echo $OUTPUT->header();

        $message = get_string('confirmremove', 'repository', $repositorytype->get_readablename());

        $output = $OUTPUT->box_start('generalbox', 'notice');
        $output .= html_writer::tag('p', $message);

        $removeurl = new moodle_url($baseurl, [
            'action' =>'delete',
            'repos' => $repository,
            'sure' => 'yes',
        ]);

        $removeanddownloadurl = new moodle_url($removeurl, [
            'downloadcontents' => 1,
        ]);

        $output .= $OUTPUT->single_button($removeurl, get_string('continueuninstall', 'repository'));
        $output .= $OUTPUT->single_button($removeanddownloadurl, get_string('continueuninstallanddownload', 'repository'));
        $output .= $OUTPUT->single_button($baseurl, get_string('cancel'));
        $output .= $OUTPUT->box_end();

        echo $output;

        $return = false;
    }
} else if ($action == 'moveup') {
    require_sesskey();
    $repositorytype = repository::get_type_by_typename($repository);
    $repositorytype->move_order('up');
} else if ($action == 'movedown') {
    require_sesskey();
    $repositorytype = repository::get_type_by_typename($repository);
    $repositorytype->move_order('down');
} else {
    // If page is loaded directly
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('manage', 'repository'));

    // Get strings that are used
    $strshow = get_string('on', 'repository');
    $strhide = get_string('off', 'repository');
    $strdelete = get_string('disabled', 'repository');
    $struninstall = get_string('uninstallplugin', 'core_admin');

    $actionchoicesforexisting = array(
        'show' => $strshow,
        'hide' => $strhide,
        'delete' => $strdelete
    );

    $actionchoicesfornew = array(
        'newon' => $strshow,
        'newoff' => $strhide,
        'delete' => $strdelete
    );

    $output = '';
    $output .= $OUTPUT->box_start('generalbox');

    // Set strings that are used multiple times
    $settingsstr = get_string('settings');
    $disablestr = get_string('disable');

    // Table to list plug-ins
    $table = new html_table();
    $table->head = array(get_string('name'), get_string('isactive', 'repository'), get_string('order'), $settingsstr, $struninstall);

    $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
    $table->id = 'repositoriessetting';
    $table->data = array();
    $table->attributes['class'] = 'admintable table generaltable table-hover';

    // Get list of used plug-ins
    $repositorytypes = repository::get_types();
    // Array to store plugins being used
    $alreadyplugins = array();
    if (!empty($repositorytypes)) {
        $totalrepositorytypes = count($repositorytypes);
        $updowncount = 1;
        foreach ($repositorytypes as $i) {
            $settings = '';
            $typename = $i->get_typename();
            // Display edit link only if you can config the type or if it has multiple instances (e.g. has instance config)
            $typeoptionnames = repository::static_function($typename, 'get_type_option_names');
            $instanceoptionnames = repository::static_function($typename, 'get_instance_option_names');

            if (!empty($typeoptionnames) || !empty($instanceoptionnames)) {
                // Calculate number of instances in order to display them for the Moodle administrator
                if (!empty($instanceoptionnames)) {
                    $params = array();
                    $params['context'] = array(context_system::instance());
                    $params['onlyvisible'] = false;
                    $params['type'] = $typename;
                    $admininstancenumber = count(repository::static_function($typename, 'get_instances', $params));
                    // site instances
                    $admininstancenumbertext = get_string('instancesforsite', 'repository', $admininstancenumber);
                    $params['context'] = array();
                    $instances = repository::static_function($typename, 'get_instances', $params);
                    $courseinstances = array();
                    $userinstances = array();

                    foreach ($instances as $instance) {
                        $repocontext = context::instance_by_id($instance->instance->contextid);
                        if ($repocontext->contextlevel == CONTEXT_COURSE) {
                            $courseinstances[] = $instance;
                        } else if ($repocontext->contextlevel == CONTEXT_USER) {
                            $userinstances[] = $instance;
                        }
                    }
                    // course instances
                    $instancenumber = count($courseinstances);
                    $courseinstancenumbertext = get_string('instancesforcourses', 'repository', $instancenumber);

                    // user private instances
                    $instancenumber =  count($userinstances);
                    $userinstancenumbertext = get_string('instancesforusers', 'repository', $instancenumber);
                } else {
                    $admininstancenumbertext = "";
                    $courseinstancenumbertext = "";
                    $userinstancenumbertext = "";
                }

                $settings = html_writer::link(new moodle_url($baseurl, ['action' => 'edit', 'repos' => $typename]), $settingsstr);
                $settings .= $OUTPUT->container_start('mdl-left');
                $settings .= '<br/>';
                $settings .= $admininstancenumbertext;
                $settings .= '<br/>';
                $settings .= $courseinstancenumbertext;
                $settings .= '<br/>';
                $settings .= $userinstancenumbertext;
                $settings .= $OUTPUT->container_end();
            }
            // Get the current visibility
            if ($i->get_visible()) {
                $currentaction = 'show';
            } else {
                $currentaction = 'hide';
            }

            // Active toggle.
            $selectaction = new moodle_url($baseurl, ['sesskey' => sesskey(), 'repos' => $typename]);
            $select = new single_select($selectaction, 'action', $actionchoicesforexisting, $currentaction, null,
                'applyto' . basename($typename));
            $select->set_label(get_string('action'), array('class' => 'accesshide'));

            // Display up/down link
            $updown = '';
            $spacer = $OUTPUT->spacer(array('height'=>15, 'width'=>15)); // should be done with CSS instead

            if ($updowncount > 1) {
                $moveupaction = new moodle_url($baseurl, [
                    'sesskey' => sesskey(),
                    'action' => 'moveup',
                    'repos' => $typename,
                ]);
                $updown .= html_writer::link($moveupaction, $OUTPUT->pix_icon('t/up', get_string('moveup'))) . '&nbsp;';
            }
            else {
                $updown .= $spacer;
            }
            if ($updowncount < $totalrepositorytypes) {
                $movedownaction = new moodle_url($baseurl, [
                    'sesskey' => sesskey(),
                    'action' => 'movedown',
                    'repos' => $typename,
                ]);
                $updown .= html_writer::link($movedownaction, $OUTPUT->pix_icon('t/down', get_string('movedown'))) . '&nbsp;';
            }
            else {
                $updown .= $spacer;
            }

            $updowncount++;

            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('repository_' . $typename, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $struninstall);
            }

            $table->data[] = array($i->get_readablename(), $OUTPUT->render($select), $updown, $settings, $uninstall);
            $table->rowclasses[] = '';

            if (!in_array($typename, $alreadyplugins)) {
                $alreadyplugins[] = $typename;
            }
        }
    }

    // Get all the plugins that exist on disk
    $plugins = core_component::get_plugin_list('repository');
    if (!empty($plugins)) {
        foreach ($plugins as $plugin => $dir) {
            // Check that it has not already been listed
            if (!in_array($plugin, $alreadyplugins)) {
                $selectaction = new moodle_url($baseurl, ['sesskey' => sesskey(), 'repos' => $plugin]);
                $select = new single_select($selectaction, 'action', $actionchoicesfornew, 'delete', null,
                    'applyto' . basename($plugin));
                $select->set_label(get_string('action'), array('class' => 'accesshide'));
                $uninstall = '';
                if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('repository_' . $plugin, 'manage')) {
                    $uninstall = html_writer::link($uninstallurl, $struninstall);
                }
                $table->data[] = array(get_string('pluginname', 'repository_'.$plugin), $OUTPUT->render($select), '', '', $uninstall);
                $table->rowclasses[] = 'dimmed_text';
            }
        }
    }

    $output .= html_writer::table($table);
    $output .= $OUTPUT->box_end();
    print $output;
    $return = false;
}

if ($return) {
    redirect($baseurl);
}
echo $OUTPUT->footer();
