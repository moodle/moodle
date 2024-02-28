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
 * Cache display administration helper.
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains the components that are requried in order to use caching.
 *
 * @package    core
 * @category   cache
 * @author     Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_cache\local;

use cache_store, cache_factory, cache_config_writer, cache_helper;
use core\output\notification;

/**
 * A cache helper for administration tasks
 *
 * @package    core
 * @category   cache
 * @copyright  2020 Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class administration_display_helper extends \core_cache\administration_helper {

    /**
     * Please do not call constructor directly. Use cache_factory::get_administration_display_helper() instead.
     */
    public function __construct() {
        // Nothing to do here.
    }

    /**
     * Returns all of the actions that can be performed on a definition.
     *
     * @param context $context the system context.
     * @param array $definitionsummary information about this cache, from the array returned by
     *      core_cache\administration_helper::get_definition_summaries(). Currently only 'sharingoptions'
     *      element is used.
     * @return array of actions. Each action is an action_url.
     */
    public function get_definition_actions(\context $context, array $definitionsummary): array {
        global $OUTPUT;
        if (has_capability('moodle/site:config', $context)) {
            $actions = array();
            // Edit mappings.
            $actions[] = $OUTPUT->action_link(
                new \moodle_url('/cache/admin.php', array('action' => 'editdefinitionmapping',
                    'definition' => $definitionsummary['id'])),
                get_string('editmappings', 'cache')
            );
            // Edit sharing.
            if (count($definitionsummary['sharingoptions']) > 1) {
                $actions[] = $OUTPUT->action_link(
                    new \moodle_url('/cache/admin.php', array('action' => 'editdefinitionsharing',
                        'definition' => $definitionsummary['id'])),
                    get_string('editsharing', 'cache')
                );
            }
            // Purge.
            $actions[] = $OUTPUT->action_link(
                new \moodle_url('/cache/admin.php', array('action' => 'purgedefinition',
                    'definition' => $definitionsummary['id'], 'sesskey' => sesskey())),
                get_string('purge', 'cache')
            );
            return $actions;
        }
        return array();
    }

    /**
     * Returns all of the actions that can be performed on a store.
     *
     * @param string $name The name of the store
     * @param array $storedetails information about this store, from the array returned by
     *      core_cache\administration_helper::get_store_instance_summaries().
     * @return array of actions. Each action is an action_url.
     */
    public function get_store_instance_actions(string $name, array $storedetails): array {
        global $OUTPUT;
        $actions = array();
        if (has_capability('moodle/site:config', \context_system::instance())) {
            $baseurl = new \moodle_url('/cache/admin.php', array('store' => $name));
            if (empty($storedetails['default'])) {
                // Edit store.
                $actions[] = $OUTPUT->action_link(
                    new \moodle_url($baseurl, array('action' => 'editstore', 'plugin' => $storedetails['plugin'])),
                    get_string('editstore', 'cache')
                );
                // Delete store.
                $actions[] = $OUTPUT->action_link(
                    new \moodle_url($baseurl, array('action' => 'deletestore')),
                    get_string('deletestore', 'cache')
                );
            }
            // Purge store.
            $actions[] = $OUTPUT->action_link(
                new \moodle_url($baseurl, array('action' => 'purgestore', 'sesskey' => sesskey())),
                get_string('purge', 'cache')
            );
        }
        return $actions;
    }

    /**
     * Returns all of the actions that can be performed on a plugin.
     *
     * @param string $name The name of the plugin
     * @param array $plugindetails information about this store, from the array returned by
     *      core_cache\administration_helper::get_store_plugin_summaries().
     * @return array of actions. Each action is an action_url.
     */
    public function get_store_plugin_actions(string $name, array $plugindetails): array {
        global $OUTPUT;
        $actions = array();
        if (has_capability('moodle/site:config', \context_system::instance())) {
            if (!empty($plugindetails['canaddinstance'])) {
                $url = new \moodle_url('/cache/admin.php',
                    array('action' => 'addstore', 'plugin' => $name));
                $actions[] = $OUTPUT->action_link(
                    $url,
                    get_string('addinstance', 'cache')
                );
            }
        }
        return $actions;
    }

    /**
     * Returns a form that can be used to add a store instance.
     *
     * @param string $plugin The plugin to add an instance of
     * @return cachestore_addinstance_form
     * @throws coding_exception
     */
    public function get_add_store_form(string $plugin): \cachestore_addinstance_form {
        global $CFG; // Needed for includes.
        $plugins = \core_component::get_plugin_list('cachestore');
        if (!array_key_exists($plugin, $plugins)) {
            throw new \coding_exception('Invalid cache plugin used when trying to create an edit form.');
        }
        $plugindir = $plugins[$plugin];
        $class = 'cachestore_addinstance_form';
        if (file_exists($plugindir.'/addinstanceform.php')) {
            require_once($plugindir.'/addinstanceform.php');
            if (class_exists('cachestore_'.$plugin.'_addinstance_form')) {
                $class = 'cachestore_'.$plugin.'_addinstance_form';
                if (!array_key_exists('cachestore_addinstance_form', class_parents($class))) {
                    throw new \coding_exception('Cache plugin add instance forms must extend cachestore_addinstance_form');
                }
            }
        }

        $locks = $this->get_possible_locks_for_stores($plugindir, $plugin);

        $url = new \moodle_url('/cache/admin.php', array('action' => 'addstore'));
        return new $class($url, array('plugin' => $plugin, 'store' => null, 'locks' => $locks));
    }

    /**
     * Returns a form that can be used to edit a store instance.
     *
     * @param string $plugin
     * @param string $store
     * @return cachestore_addinstance_form
     * @throws coding_exception
     */
    public function get_edit_store_form(string $plugin, string $store): \cachestore_addinstance_form {
        global $CFG; // Needed for includes.
        $plugins = \core_component::get_plugin_list('cachestore');
        if (!array_key_exists($plugin, $plugins)) {
            throw new \coding_exception('Invalid cache plugin used when trying to create an edit form.');
        }
        $factory = \cache_factory::instance();
        $config = $factory->create_config_instance();
        $stores = $config->get_all_stores();
        if (!array_key_exists($store, $stores)) {
            throw new \coding_exception('Invalid store name given when trying to create an edit form.');
        }
        $plugindir = $plugins[$plugin];
        $class = 'cachestore_addinstance_form';
        if (file_exists($plugindir.'/addinstanceform.php')) {
            require_once($plugindir.'/addinstanceform.php');
            if (class_exists('cachestore_'.$plugin.'_addinstance_form')) {
                $class = 'cachestore_'.$plugin.'_addinstance_form';
                if (!array_key_exists('cachestore_addinstance_form', class_parents($class))) {
                    throw new \coding_exception('Cache plugin add instance forms must extend cachestore_addinstance_form');
                }
            }
        }

        $locks = $this->get_possible_locks_for_stores($plugindir, $plugin);

        $url = new \moodle_url('/cache/admin.php', array('action' => 'editstore', 'plugin' => $plugin, 'store' => $store));
        $editform = new $class($url, array('plugin' => $plugin, 'store' => $store, 'locks' => $locks));
        if (isset($stores[$store]['lock'])) {
            $editform->set_data(array('lock' => $stores[$store]['lock']));
        }
        // See if the cachestore is going to want to load data for the form.
        // If it has a customised add instance form then it is going to want to.
        $storeclass = 'cachestore_'.$plugin;
        $storedata = $stores[$store];
        if (array_key_exists('configuration', $storedata) &&
            array_key_exists('cache_is_configurable', class_implements($storeclass))) {
            $storeclass::config_set_edit_form_data($editform, $storedata['configuration']);
        }
        return $editform;
    }

    /**
     * Returns an array of suitable lock instances for use with this plugin, or false if the plugin handles locking itself.
     *
     * @param string $plugindir
     * @param string $plugin
     * @return array|false
     */
    protected function get_possible_locks_for_stores(string $plugindir, string $plugin) {
        global $CFG; // Needed for includes.
        $supportsnativelocking = false;
        if (file_exists($plugindir.'/lib.php')) {
            require_once($plugindir.'/lib.php');
            $pluginclass = 'cachestore_'.$plugin;
            if (class_exists($pluginclass)) {
                $supportsnativelocking = array_key_exists('cache_is_lockable', class_implements($pluginclass));
            }
        }

        if (!$supportsnativelocking) {
            $config = \cache_config::instance();
            $locks = array();
            foreach ($config->get_locks() as $lock => $conf) {
                if (!empty($conf['default'])) {
                    $name = get_string($lock, 'cache');
                } else {
                    $name = $lock;
                }
                $locks[$lock] = $name;
            }
        } else {
            $locks = false;
        }

        return $locks;
    }

    /**
     * Processes the results of the add/edit instance form data for a plugin returning an array of config information suitable to
     * store in configuration.
     *
     * @param stdClass $data The mform data.
     * @return array
     * @throws coding_exception
     */
    public function get_store_configuration_from_data(\stdClass $data): array {
        global $CFG;
        $file = $CFG->dirroot.'/cache/stores/'.$data->plugin.'/lib.php';
        if (!file_exists($file)) {
            throw new \coding_exception('Invalid cache plugin provided. '.$file);
        }
        require_once($file);
        $class = 'cachestore_'.$data->plugin;
        if (!class_exists($class)) {
            throw new \coding_exception('Invalid cache plugin provided.');
        }
        if (array_key_exists('cache_is_configurable', class_implements($class))) {
            return $class::config_get_configuration_array($data);
        }
        return array();
    }

    /**
     * Returns an array of lock plugins for which we can add an instance.
     *
     * Suitable for use within an mform select element.
     *
     * @return array
     */
    public function get_addable_lock_options(): array {
        $plugins = \core_component::get_plugin_list_with_class('cachelock', '', 'lib.php');
        $options = array();
        $len = strlen('cachelock_');
        foreach ($plugins as $plugin => $class) {
            $method = "$class::can_add_instance";
            if (is_callable($method) && !call_user_func($method)) {
                // Can't add an instance of this plugin.
                continue;
            }
            $options[substr($plugin, $len)] = get_string('pluginname', $plugin);
        }
        return $options;
    }

    /**
     * Gets the form to use when adding a lock instance.
     *
     * @param string $plugin
     * @param array $lockplugin
     * @return cache_lock_form
     * @throws coding_exception
     */
    public function get_add_lock_form(string $plugin, array $lockplugin = null): \cache_lock_form {
        global $CFG; // Needed for includes.
        $plugins = \core_component::get_plugin_list('cachelock');
        if (!array_key_exists($plugin, $plugins)) {
            throw new \coding_exception('Invalid cache lock plugin requested when trying to create a form.');
        }
        $plugindir = $plugins[$plugin];
        $class = 'cache_lock_form';
        if (file_exists($plugindir.'/addinstanceform.php') && in_array('cache_is_configurable', class_implements($class))) {
            require_once($plugindir.'/addinstanceform.php');
            if (class_exists('cachelock_'.$plugin.'_addinstance_form')) {
                $class = 'cachelock_'.$plugin.'_addinstance_form';
                if (!array_key_exists('cache_lock_form', class_parents($class))) {
                    throw new \coding_exception('Cache lock plugin add instance forms must extend cache_lock_form');
                }
            }
        }
        return new $class(null, array('lock' => $plugin));
    }

    /**
     * Gets configuration data from a new lock instance form.
     *
     * @param string $plugin
     * @param stdClass $data
     * @return array
     * @throws coding_exception
     */
    public function get_lock_configuration_from_data(string $plugin, \stdClass $data): array {
        global $CFG;
        $file = $CFG->dirroot.'/cache/locks/'.$plugin.'/lib.php';
        if (!file_exists($file)) {
            throw new \coding_exception('Invalid cache plugin provided. '.$file);
        }
        require_once($file);
        $class = 'cachelock_'.$plugin;
        if (!class_exists($class)) {
            throw new \coding_exception('Invalid cache plugin provided.');
        }
        if (array_key_exists('cache_is_configurable', class_implements($class))) {
            return $class::config_get_configuration_array($data);
        }
        return array();
    }

    /**
     * Handles the page actions, based on the parameter.
     *
     * @param string $action the action to handle.
     * @param array $forminfo an empty array to be overridden and set.
     * @return array the empty or overridden forminfo array.
     */
    public function perform_cache_actions(string $action, array $forminfo): array {
        switch ($action) {
            case 'rescandefinitions' : // Rescan definitions.
                $this->action_rescan_definition();
                break;

            case 'addstore' : // Add the requested store.
                $forminfo = $this->action_addstore();
                break;

            case 'editstore' : // Edit the requested store.
                $forminfo = $this->action_editstore();
                break;

            case 'deletestore' : // Delete a given store.
                $this->action_deletestore($action);
                break;

            case 'editdefinitionmapping' : // Edit definition mappings.
                $forminfo = $this->action_editdefinitionmapping();
                break;

            case 'editdefinitionsharing' : // Edit definition sharing.
                $forminfo = $this->action_editdefinitionsharing();
                break;

            case 'editmodemappings': // Edit default mode mappings.
                $forminfo = $this->action_editmodemappings();
                break;

            case 'purgedefinition': // Purge a specific definition.
                $this->action_purgedefinition();
                break;

            case 'purgestore':
            case 'purge': // Purge a store cache.
                $this->action_purge();
                break;

            case 'newlockinstance':
                $forminfo = $this->action_newlockinstance();
                break;

            case 'deletelock':
                // Deletes a lock instance.
                $this->action_deletelock($action);
                break;
        }

        return $forminfo;
    }

    /**
     * Performs the rescan definition action.
     *
     * @return void
     */
    public function action_rescan_definition() {
        global $PAGE;

        require_sesskey();
        \cache_config_writer::update_definitions();
        redirect($PAGE->url);
    }

    /**
     * Performs the add store action.
     *
     * @return array an array of the form to display to the user, and the page title.
     */
    public function action_addstore(): array {
        global $PAGE;
        $storepluginsummaries = $this->get_store_plugin_summaries();

        $plugin = required_param('plugin', PARAM_PLUGIN);
        if (!$storepluginsummaries[$plugin]['canaddinstance']) {
            throw new \moodle_exception('ex_unmetstorerequirements', 'cache');
        }
        $mform = $this->get_add_store_form($plugin);
        $title = get_string('addstore', 'cache', $storepluginsummaries[$plugin]['name']);
        if ($mform->is_cancelled()) {
            redirect($PAGE->url);
        } else if ($data = $mform->get_data()) {
            $config = $this->get_store_configuration_from_data($data);
            $writer = \cache_config_writer::instance();
            unset($config['lock']);
            foreach ($writer->get_locks() as $lock => $lockconfig) {
                if ($lock == $data->lock) {
                    $config['lock'] = $data->lock;
                }
            }
            $writer->add_store_instance($data->name, $data->plugin, $config);
            redirect($PAGE->url, get_string('addstoresuccess', 'cache', $storepluginsummaries[$plugin]['name']), 5);
        }

        $PAGE->navbar->add(get_string('addstore', 'cache', 'cache'), $PAGE->url);
        return array('form' => $mform, 'title' => $title);
    }

    /**
     * Performs the edit store action.
     *
     * @return array an array of the form to display, and the page title.
     */
    public function action_editstore(): array {
        global $PAGE;
        $storepluginsummaries = $this->get_store_plugin_summaries();

        $plugin = required_param('plugin', PARAM_PLUGIN);
        $store = required_param('store', PARAM_TEXT);
        $mform = $this->get_edit_store_form($plugin, $store);
        $title = get_string('addstore', 'cache', $storepluginsummaries[$plugin]['name']);
        if ($mform->is_cancelled()) {
            redirect($PAGE->url);
        } else if ($data = $mform->get_data()) {
            $config = $this->get_store_configuration_from_data($data);
            $writer = \cache_config_writer::instance();

            unset($config['lock']);
            foreach ($writer->get_locks() as $lock => $lockconfig) {
                if ($lock == $data->lock) {
                    $config['lock'] = $data->lock;
                }
            }
            $writer->edit_store_instance($data->name, $data->plugin, $config);
            redirect($PAGE->url, get_string('editstoresuccess', 'cache', $storepluginsummaries[$plugin]['name']), 5);
        }

        return array('form' => $mform, 'title' => $title);
    }

    /**
     * Performs the deletestore action.
     *
     * @param string $action the action calling to this function.
     */
    public function action_deletestore(string $action): void {
        global $OUTPUT, $PAGE, $SITE;
        $notifysuccess = true;
        $storeinstancesummaries = $this->get_store_instance_summaries();

        $store = required_param('store', PARAM_TEXT);
        $confirm = optional_param('confirm', false, PARAM_BOOL);

        if (!array_key_exists($store, $storeinstancesummaries)) {
            $notifysuccess = false;
            $notification = get_string('invalidstore', 'cache');
        } else if ($storeinstancesummaries[$store]['mappings'] > 0) {
            $notifysuccess = false;
            $notification = get_string('deletestorehasmappings', 'cache');
        }

        if ($notifysuccess) {
            if (!$confirm) {
                $title = get_string('confirmstoredeletion', 'cache');
                $params = array('store' => $store, 'confirm' => 1, 'action' => $action, 'sesskey' => sesskey());
                $url = new \moodle_url($PAGE->url, $params);
                $button = new \single_button($url, get_string('deletestore', 'cache'));

                $PAGE->set_title($title);
                $PAGE->set_heading($SITE->fullname);
                echo $OUTPUT->header();
                echo $OUTPUT->heading($title);
                $confirmation = get_string('deletestoreconfirmation', 'cache', $storeinstancesummaries[$store]['name']);
                echo $OUTPUT->confirm($confirmation, $button, $PAGE->url);
                echo $OUTPUT->footer();
                exit;
            } else {
                require_sesskey();
                $writer = \cache_config_writer::instance();
                $writer->delete_store_instance($store);
                redirect($PAGE->url, get_string('deletestoresuccess', 'cache'), 5);
            }
        } else {
            redirect($PAGE->url, $notification, null, notification::NOTIFY_ERROR);
        }
    }

    /**
     * Performs the edit definition mapping action.
     *
     * @return array an array of the form to display, and the page title.
     * @throws cache_exception
     */
    public function action_editdefinitionmapping(): array {
        global $PAGE;
        $definitionsummaries = $this->get_definition_summaries();

        $definition = required_param('definition', PARAM_SAFEPATH);
        if (!array_key_exists($definition, $definitionsummaries)) {
            throw new \cache_exception('Invalid cache definition requested');
        }
        $title = get_string('editdefinitionmappings', 'cache', $definition);
        $mform = new \cache_definition_mappings_form($PAGE->url, array('definition' => $definition));
        if ($mform->is_cancelled()) {
            redirect($PAGE->url);
        } else if ($data = $mform->get_data()) {
            $writer = \cache_config_writer::instance();
            $mappings = array();
            foreach ($data->mappings as $mapping) {
                if (!empty($mapping)) {
                    $mappings[] = $mapping;
                }
            }
            $writer->set_definition_mappings($definition, $mappings);
            redirect($PAGE->url);
        }

        $PAGE->navbar->add(get_string('updatedefinitionmapping', 'cache'), $PAGE->url);
        return array('form' => $mform, 'title' => $title);
    }

    /**
     * Performs the edit definition sharing action.
     *
     * @return array an array of the edit definition sharing form, and the page title.
     */
    public function action_editdefinitionsharing(): array {
        global $PAGE;
        $definitionsummaries = $this->get_definition_summaries();

        $definition = required_param('definition', PARAM_SAFEPATH);
        if (!array_key_exists($definition, $definitionsummaries)) {
            throw new \cache_exception('Invalid cache definition requested');
        }
        $title = get_string('editdefinitionsharing', 'cache', $definition);
        $sharingoptions = $definitionsummaries[$definition]['sharingoptions'];
        $customdata = array('definition' => $definition, 'sharingoptions' => $sharingoptions);
        $mform = new \cache_definition_sharing_form($PAGE->url, $customdata);
        $mform->set_data(array(
            'sharing' => $definitionsummaries[$definition]['selectedsharingoption'],
            'userinputsharingkey' => $definitionsummaries[$definition]['userinputsharingkey']
        ));
        if ($mform->is_cancelled()) {
            redirect($PAGE->url);
        } else if ($data = $mform->get_data()) {
            $component = $definitionsummaries[$definition]['component'];
            $area = $definitionsummaries[$definition]['area'];
            // Purge the stores removing stale data before we alter the sharing option.
            \cache_helper::purge_stores_used_by_definition($component, $area);
            $writer = \cache_config_writer::instance();
            $sharing = array_sum(array_keys($data->sharing));
            $userinputsharingkey = $data->userinputsharingkey;
            $writer->set_definition_sharing($definition, $sharing, $userinputsharingkey);
            redirect($PAGE->url);
        }

        $PAGE->navbar->add(get_string('updatedefinitionsharing', 'cache'), $PAGE->url);
        return array('form' => $mform, 'title' => $title);
    }

    /**
     * Performs the edit mode mappings action.
     *
     * @return array an array of the edit mode mappings form.
     */
    public function action_editmodemappings(): array {
        global $PAGE;
        $storeinstancesummaries = $this->get_store_instance_summaries();
        $defaultmodestores = $this->get_default_mode_stores();

        $mform = new \cache_mode_mappings_form(null, $storeinstancesummaries);
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

        return array('form' => $mform);
    }

    /**
     * Performs the purge definition action.
     *
     * @return void
     */
    public function action_purgedefinition() {
        global $PAGE;

        require_sesskey();
        $id = required_param('definition', PARAM_SAFEPATH);
        list($component, $area) = explode('/', $id, 2);
        $factory = cache_factory::instance();
        $definition = $factory->create_definition($component, $area);
        if ($definition->has_required_identifiers()) {
            // We will have to purge the stores used by this definition.
            cache_helper::purge_stores_used_by_definition($component, $area);
        } else {
            // Alrighty we can purge just the data belonging to this definition.
            cache_helper::purge_by_definition($component, $area);
        }

        $message = get_string('purgexdefinitionsuccess', 'cache', [
                    'name' => $definition->get_name(),
                    'component' => $component,
                    'area' => $area,
                ]);
        $purgeagainlink = \html_writer::link(new \moodle_url('/cache/admin.php', [
                'action' => 'purgedefinition', 'sesskey' => sesskey(), 'definition' => $id]),
                get_string('purgeagain', 'cache'));
        redirect($PAGE->url, $message . ' ' . $purgeagainlink, 5);
    }

    /**
     * Performs the purge action.
     *
     * @return void
     */
    public function action_purge() {
        global $PAGE;

        require_sesskey();
        $store = required_param('store', PARAM_TEXT);
        cache_helper::purge_store($store);
        $message = get_string('purgexstoresuccess', 'cache', ['store' => $store]);
        $purgeagainlink = \html_writer::link(new \moodle_url('/cache/admin.php', [
                'action' => 'purgestore', 'sesskey' => sesskey(), 'store' => $store]),
                get_string('purgeagain', 'cache'));
        redirect($PAGE->url, $message . ' ' . $purgeagainlink, 5);
    }

    /**
     * Performs the new lock instance action.
     *
     * @return array An array containing the new lock instance form.
     */
    public function action_newlockinstance(): array {
        global $PAGE;

        // Adds a new lock instance.
        $lock = required_param('lock', PARAM_ALPHANUMEXT);
        $mform = $this->get_add_lock_form($lock);
        if ($mform->is_cancelled()) {
            redirect($PAGE->url);
        } else if ($data = $mform->get_data()) {
            $factory = cache_factory::instance();
            $config = $factory->create_config_instance(true);
            $name = $data->name;
            $data = $this->get_lock_configuration_from_data($lock, $data);
            $config->add_lock_instance($name, $lock, $data);
            redirect($PAGE->url, get_string('addlocksuccess', 'cache', $name), 5);
        }

        return array('form' => $mform);
    }

    /**
     * Performs the delete lock action.
     *
     * @param string $action the action calling this function.
     */
    public function action_deletelock(string $action): void {
        global $OUTPUT, $PAGE, $SITE;
        $notifysuccess = true;
        $locks = $this->get_lock_summaries();

        $lock = required_param('lock', PARAM_ALPHANUMEXT);
        $confirm = optional_param('confirm', false, PARAM_BOOL);
        if (!array_key_exists($lock, $locks)) {
            $notifysuccess = false;
            $notification = get_string('invalidlock', 'cache');
        } else if ($locks[$lock]['uses'] > 0) {
            $notifysuccess = false;
            $notification = get_string('deletelockhasuses', 'cache');
        }
        if ($notifysuccess) {
            if (!$confirm) {
                $title = get_string('confirmlockdeletion', 'cache');
                $params = array('lock' => $lock, 'confirm' => 1, 'action' => $action, 'sesskey' => sesskey());
                $url = new \moodle_url($PAGE->url, $params);
                $button = new \single_button($url, get_string('deletelock', 'cache'));

                $PAGE->set_title($title);
                $PAGE->set_heading($SITE->fullname);
                echo $OUTPUT->header();
                echo $OUTPUT->heading($title);
                $confirmation = get_string('deletelockconfirmation', 'cache', $lock);
                echo $OUTPUT->confirm($confirmation, $button, $PAGE->url);
                echo $OUTPUT->footer();
                exit;
            } else {
                require_sesskey();
                $writer = cache_config_writer::instance();
                $writer->delete_lock_instance($lock);
                redirect($PAGE->url, get_string('deletelocksuccess', 'cache'), 5);
            }
        } else {
            redirect($PAGE->url, $notification, null, notification::NOTIFY_ERROR);
        }
    }

    /**
     * Outputs the main admin page by generating it through the renderer.
     *
     * @param \core_cache\output\renderer $renderer the renderer to use to generate the page.
     * @return string the HTML for the admin page.
     */
    public function generate_admin_page(\core_cache\output\renderer $renderer): string {
        $context = \context_system::instance();
        $html = '';

        $storepluginsummaries = $this->get_store_plugin_summaries();
        $storeinstancesummaries = $this->get_store_instance_summaries();
        $definitionsummaries = $this->get_definition_summaries();
        $defaultmodestores = $this->get_default_mode_stores();
        $locks = $this->get_lock_summaries();

        $html .= $renderer->store_plugin_summaries($storepluginsummaries);
        $html .= $renderer->store_instance_summariers($storeinstancesummaries, $storepluginsummaries);
        $html .= $renderer->definition_summaries($definitionsummaries, $context);
        $html .= $renderer->lock_summaries($locks);
        $html .= $renderer->additional_lock_actions();

        $applicationstore = join(', ', $defaultmodestores[cache_store::MODE_APPLICATION]);
        $sessionstore = join(', ', $defaultmodestores[cache_store::MODE_SESSION]);
        $requeststore = join(', ', $defaultmodestores[cache_store::MODE_REQUEST]);
        $editurl = new \moodle_url('/cache/admin.php', array('action' => 'editmodemappings'));
        $html .= $renderer->mode_mappings($applicationstore, $sessionstore, $requeststore, $editurl);

        return $html;
    }

    /**
     * Gets usage information about the whole cache system.
     *
     * This is a slow function and should only be used on an admin information page.
     *
     * The returned array lists all cache definitions with fields 'cacheid' and 'stores'. For
     * each store, the following fields are available:
     *
     * - name (store name)
     * - class (e.g. cachestore_redis)
     * - supported (true if we have any information)
     * - items (number of items stored)
     * - mean (mean size of item)
     * - sd (standard deviation for item sizes)
     * - margin (margin of error for mean at 95% confidence)
     * - storetotal (total usage for store if known, otherwise null)
     *
     * The storetotal field will be the same for every cache that uses the same store.
     *
     * @param int $samplekeys Number of keys to sample when checking size of large caches
     * @return array Details of cache usage
     */
    public function get_usage(int $samplekeys): array {
        $results = [];

        $factory = cache_factory::instance();

        // Check the caches we already have an instance of, so we don't make another one...
        $got = $factory->get_caches_in_use();
        $gotid = [];
        foreach ($got as $longid => $unused) {
            // The IDs here can be of the form cacheid/morestuff if there are parameters in the
            // cache. Any entry for a cacheid is good enough to consider that we don't need to make
            // another entry ourselves, so we remove the extra bits and track the basic cache id.
            $gotid[preg_replace('~^([^/]+/[^/]+)/.*$~', '$1', $longid)] = true;
        }

        $storetotals = [];

        $config = $factory->create_config_instance();
        foreach ($config->get_definitions() as $configdetails) {
            if (!array_key_exists($configdetails['component'] . '/' .  $configdetails['area'], $gotid)) {
                // Where possible (if it doesn't need identifiers), make an instance of the cache, otherwise
                // we can't get the store instances for it (and it won't show up in the list).
                if (empty($configdetails['requireidentifiers'])) {
                    \cache::make($configdetails['component'], $configdetails['area']);
                }
            }
            $definition = $factory->create_definition($configdetails['component'], $configdetails['area']);
            $stores = $factory->get_store_instances_in_use($definition);

            // Create object for results about this cache definition.
            $currentresult = (object)['cacheid' => $definition->get_id(), 'stores' => []];
            $results[$currentresult->cacheid] = $currentresult;

            /** @var cache_store $store */
            foreach ($stores as $store) {
                // Skip static cache.
                if ($store instanceof \cachestore_static) {
                    continue;
                }

                // Get cache size details from store.
                $currentstore = $store->cache_size_details($samplekeys);

                // Add in basic information about store.
                $currentstore->name = $store->my_name();
                $currentstore->class = get_class($store);

                // Add in store total.
                if (!array_key_exists($currentstore->name, $storetotals)) {
                    $storetotals[$currentstore->name] = $store->store_total_size();
                }
                $currentstore->storetotal = $storetotals[$currentstore->name];

                $currentresult->stores[] = $currentstore;
            }
        }

        ksort($results);
        return $results;
    }
}
