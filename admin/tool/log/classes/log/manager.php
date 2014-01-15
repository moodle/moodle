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
 * Log store manager.
 *
 * @package    tool_log
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\log;

defined('MOODLE_INTERNAL') || die();

class manager implements \core\log\manager {
    /** @var \core\log\reader[] $readers list of initialised log readers */
    protected $readers;

    /** @var \tool_log\log\writer[] $writers list of initialised log writers */
    protected $writers;

    /** @var \tool_log\log\store[] $stores */
    protected $stores;

    /**
     * Delayed initialisation of singleton.
     */
    protected function init() {
        if (isset($this->stores)) {
            // Do not bother checking readers and writers here
            // because everything is init here.
            return;
        }
        $this->stores = array();
        $this->readers = array();
        $this->writers = array();

        // Register shutdown handler - this may be useful for buffering, file handle closing, etc.
        \core_shutdown_manager::register_function(array($this, 'dispose'));

        $plugins = get_config('tool_log', 'enabled_stores');
        if (empty($plugins)) {
            return;
        }

        $plugins = explode(',', $plugins);
        foreach ($plugins as $plugin) {
            $classname = "\\$plugin\\log\\store";
            if (class_exists($classname)) {
                $store = new $classname($this);
                $this->stores[$plugin] = $store;
                if ($store instanceof \tool_log\log\writer) {
                    $this->writers[$plugin] = $store;
                }
                if ($store instanceof \core\log\reader) {
                    $this->readers[$plugin] = $store;
                }
            }
        }
    }

    /**
     * Called from the observer only.
     *
     * @param \core\event\base $event
     */
    public function process(\core\event\base $event) {
        $this->init();
        foreach ($this->writers as $plugin => $writer) {
            try {
                $writer->write($event, $this);
            } catch (\Exception $e) {
                debugging('Exception detected when logging event '.$event->eventname.' in '.$plugin.': '.$e->getMessage(), DEBUG_NORMAL, $e->getTrace());
            }
        }
    }

    /**
     * Returns list of available log readers.
     *
     * This way the reports find out available sources of data.
     *
     * @return \core\log\reader[] list of available log data readers
     */
    public function get_readers() {
        $this->init();
        $return = array();
        foreach ($this->readers as $plugin => $reader) {
            $return[$plugin] = $reader;
        }
        return $return;
    }

    /**
     * Intended for store management, do not use from reports.
     *
     * @return store[] Returns list of available store plugins.
     */
    public static function get_store_plugins() {
        return \core_component::get_plugin_list_with_class('logstore', 'log\store');
    }

    /**
     * Usually called automatically from shutdown manager,
     * this allows us to implement buffering of write operations.
     */
    public function dispose() {
        if ($this->stores) {
            foreach ($this->stores as $store) {
                $store->dispose();
            }
        }
        $this->stores = null;
        $this->readers = null;
        $this->writers = null;
    }

    /**
     * Execute cron actions.
     */
    public function cron() {
        $this->init();
        foreach ($this->stores['legacy'] as $store) {
            $store->cron();
        }
    }

    /**
     * Legacy add_to_log() redirection.
     *
     * To be used only from deprecated add_to_log() function and event trigger() method.
     *
     * NOTE: this is hardcoded to legacy log store plugin, hopefully we can get rid of it soon.
     *
     * @param    int     $courseid  The course id
     * @param    string  $module  The module name  e.g. forum, journal, resource, course, user etc
     * @param    string  $action  'view', 'update', 'add' or 'delete', possibly followed by another word to clarify.
     * @param    string  $url     The file and parameters used to see the results of the action
     * @param    string  $info    Additional description information
     * @param    int     $cm      The course_module->id if there is one
     * @param    int|\stdClass $user If log regards $user other than $USER
     */
    public function legacy_add_to_log($courseid, $module, $action, $url='', $info='', $cm=0, $user=0) {
        $this->init();
        if (isset($this->stores['logstore_legacy'])) {
            $this->stores['logstore_legacy']->legacy_add_to_log($courseid, $module, $action, $url, $info, $cm, $user);
        }
    }
}
