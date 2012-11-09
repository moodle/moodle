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
 * The administration and management interface for the cache setup and configuration.
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/lib/adminlib.php');
require_once($CFG->dirroot.'/cache/locallib.php');
require_once($CFG->dirroot.'/cache/forms.php');

$action = optional_param('action', null, PARAM_ALPHA);

admin_externalpage_setup('cacheconfig');
$context = context_system::instance();

$stores = cache_administration_helper::get_store_instance_summaries();
$plugins = cache_administration_helper::get_store_plugin_summaries();
$definitions = cache_administration_helper::get_definition_summaries();
$defaultmodestores = cache_administration_helper::get_default_mode_stores();
$locks = cache_administration_helper::get_lock_summaries();

$title = new lang_string('cacheadmin', 'cache');
$mform = null;
$notification = null;
$notifysuccess = true;

if (!empty($action) && confirm_sesskey()) {
    switch ($action) {
        case 'rescandefinitions' : // Rescan definitions.
            cache_config_writer::update_definitions();
            redirect($PAGE->url);
            break;
        case 'addstore' : // Add the requested store.
            $plugin = required_param('plugin', PARAM_PLUGIN);
            if (!$plugins[$plugin]['canaddinstance']) {
                print_error('ex_unmetstorerequirements', 'cache');
            }
            $mform = cache_administration_helper::get_add_store_form($plugin);
            $title = get_string('addstore', 'cache', $plugins[$plugin]['name']);
            if ($mform->is_cancelled()) {
                redirect($PAGE->url);
            } else if ($data = $mform->get_data()) {
                $config = cache_administration_helper::get_store_configuration_from_data($data);
                $writer = cache_config_writer::instance();
                unset($config['lock']);
                foreach ($writer->get_locks() as $lock => $lockconfig) {
                    if ($lock == $data->lock) {
                        $config['lock'] = $data->lock;
                    }
                }
                $writer->add_store_instance($data->name, $data->plugin, $config);
                redirect($PAGE->url, get_string('addstoresuccess', 'cache', $plugins[$plugin]['name']), 5);
            }
            break;
        case 'editstore' : // Edit the requested store.
            $plugin = required_param('plugin', PARAM_PLUGIN);
            $store = required_param('store', PARAM_TEXT);
            $mform = cache_administration_helper::get_edit_store_form($plugin, $store);
            $title = get_string('addstore', 'cache', $plugins[$plugin]['name']);
            if ($mform->is_cancelled()) {
                redirect($PAGE->url);
            } else if ($data = $mform->get_data()) {
                $config = cache_administration_helper::get_store_configuration_from_data($data);
                $writer = cache_config_writer::instance();
                unset($config['lock']);
                foreach ($writer->get_locks() as $lock => $lockconfig) {
                    if ($lock == $data->lock) {
                        $config['lock'] = $data->lock;
                    }
                }
                $writer->edit_store_instance($data->name, $data->plugin, $config);
                redirect($PAGE->url, get_string('editstoresuccess', 'cache', $plugins[$plugin]['name']), 5);
            }
            break;
        case 'deletestore' : // Delete a given store.
            $store = required_param('store', PARAM_TEXT);
            $confirm = optional_param('confirm', false, PARAM_BOOL);

            if (!array_key_exists($store, $stores)) {
                $notifysuccess = false;
                $notification = get_string('invalidstore');
            } else if ($stores[$store]['mappings'] > 0) {
                $notifysuccess = false;
                $notification = get_string('deletestorehasmappings', 'cache');
            }

            if ($notifysuccess) {
                if (!$confirm) {
                    $title = get_string('confirmstoredeletion', 'cache');
                    $params = array('store' => $store, 'confirm' => 1, 'action' => $action, 'sesskey' => sesskey());
                    $url = new moodle_url($PAGE->url, $params);
                    $button = new single_button($url, get_string('deletestore', 'cache'));

                    $PAGE->set_title($title);
                    $PAGE->set_heading($SITE->fullname);
                    echo $OUTPUT->header();
                    echo $OUTPUT->heading($title);
                    $confirmation = get_string('deletestoreconfirmation', 'cache', $stores[$store]['name']);
                    echo $OUTPUT->confirm($confirmation, $button, $PAGE->url);
                    echo $OUTPUT->footer();
                    exit;
                } else {
                    $writer = cache_config_writer::instance();
                    $writer->delete_store_instance($store);
                    redirect($PAGE->url, get_string('deletestoresuccess', 'cache'), 5);
                }
            }
            break;
        case 'editdefinitionmapping' : // Edit definition mappings.
            $definition = required_param('definition', PARAM_TEXT);
            $title = get_string('editdefinitionmappings', 'cache', $definition);
            $mform = new cache_definition_mappings_form($PAGE->url, array('definition' => $definition));
            if ($mform->is_cancelled()) {
                redirect($PAGE->url);
            } else if ($data = $mform->get_data()) {
                $writer = cache_config_writer::instance();
                $mappings = array();
                foreach ($data->mappings as $mapping) {
                    if (!empty($mapping)) {
                        $mappings[] = $mapping;
                    }
                }
                $writer->set_definition_mappings($definition, $mappings);
                redirect($PAGE->url);
            }
            break;
        case 'editmodemappings': // Edit default mode mappings.
            $mform = new cache_mode_mappings_form(null, $stores);
            $mform->set_data(array(
                'mode_'.cache_store::MODE_APPLICATION => key($defaultmodestores[cache_store::MODE_APPLICATION]),
                'mode_'.cache_store::MODE_SESSION => key($defaultmodestores[cache_store::MODE_SESSION]),
                'mode_'.cache_store::MODE_REQUEST => key($defaultmodestores[cache_store::MODE_REQUEST]),
            ));
            if ($mform->is_cancelled()) {
                redirect($PAGE->url);
            } else if ($data = $mform->get_data()) {
                $mappings = array(
                    cache_store::MODE_APPLICATION => array($data->{'mode_'.cache_store::MODE_APPLICATION}),
                    cache_store::MODE_SESSION => array($data->{'mode_'.cache_store::MODE_SESSION}),
                    cache_store::MODE_REQUEST => array($data->{'mode_'.cache_store::MODE_REQUEST}),
                );
                $writer = cache_config_writer::instance();
                $writer->set_mode_mappings($mappings);
                redirect($PAGE->url);
            }
            break;

        case 'purge': // Purge a store cache.
            $store = required_param('store', PARAM_TEXT);
            cache_helper::purge_store($store);
            redirect($PAGE->url, get_string('purgestoresuccess', 'cache'), 5);
            break;
    }
}

$PAGE->set_title($title);
$PAGE->set_heading($SITE->fullname);
$renderer = $PAGE->get_renderer('core_cache');

echo $renderer->header();
echo $renderer->heading($title);

if (!is_null($notification)) {
    echo $renderer->notification($notification, ($notifysuccess)?'notifysuccess' : 'notifyproblem');
}

if ($mform instanceof moodleform) {
    $mform->display();
} else {
    echo $renderer->store_plugin_summaries($plugins);
    echo $renderer->store_instance_summariers($stores, $plugins);
    echo $renderer->definition_summaries($definitions, cache_administration_helper::get_definition_actions($context));
    echo $renderer->lock_summaries($locks);

    $applicationstore = join(', ', $defaultmodestores[cache_store::MODE_APPLICATION]);
    $sessionstore = join(', ', $defaultmodestores[cache_store::MODE_SESSION]);
    $requeststore = join(', ', $defaultmodestores[cache_store::MODE_REQUEST]);
    $editurl = new moodle_url('/cache/admin.php', array('action' => 'editmodemappings', 'sesskey' => sesskey()));
    echo $renderer->mode_mappings($applicationstore, $sessionstore, $requeststore, $editurl);
}

echo $renderer->footer();
