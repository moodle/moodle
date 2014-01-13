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
 * External database writer.
 *
 * @package    logstore_database
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_database\log;

defined('MOODLE_INTERNAL') || die();

class store implements \tool_log\log\writer {
    /** @var \tool_log\log\manager $manager */
    protected $manager;
    /** @var \moodle_database $extdb */
    protected $extdb;

    public function __construct(\tool_log\log\manager $manager) {
        $this->manager = $manager;
    }

    protected function init() {
        if (isset($this->extdb)) {
            return !empty($this->extdb);
        }

        $dbdriver = $this->get_config('dbdriver');
        if (!$dbdriver) {
            $this->extdb = false;
            return false;
        }
        list($dbtype, $dblibrary) = explode('/', $dbdriver);

        if (!$db = \moodle_database::get_driver_instance($dbtype, $dblibrary, true)) {
            debugging("Unknown driver $dblibrary/$dbtype", DEBUG_DEVELOPER);
            $this->extdb = false;
            return false;
        }

        $dboptions = array();
        $dboptions['dbpersist'] = $this->get_config('dbpersist', '0');
        $dboptions['dbsocket'] = $this->get_config('dbsocket', '');
        $dboptions['dbport'] = $this->get_config('dbport', '');
        $dboptions['dbschema'] = $this->get_config('dbschema', '');
        $dboptions['dbcollation'] = $this->get_config('dbcollation', '');

        try {
            $db->connect($this->get_config('dbhost'), $this->get_config('dbuser'), $this->get_config('dbpass'),
                $this->get_config('dbname'), $this->get_config('dbprefix'), $dboptions);
        } catch (\moodle_exception $e) {
            debugging('Cannot connect to external database: '.$e->getMessage(), DEBUG_DEVELOPER);
            $this->extdb = false;
            return false;
        }

        $this->extdb = $db;
        return true;
    }

    protected function get_config($name, $default = null) {
        $value = \get_config('logstore_database', $name);
        if ($value !== false) {
            return $value;
        }
        return $default;
    }

    public function write(\core\event\base $event) {
        if (!$this->init()) {
            return;
        }

        if (!$dbtable = $this->get_config('dbtable')) {
            return;
        }

        $data = $event->get_data();
        if (CLI_SCRIPT) {
            $data['origin'] = 'cli';
        } else {
            $data['origin'] = getremoteaddr();
        }
        $data['realuserid'] = \core\session\manager::is_loggedinas() ? $_SESSION['USER']->realuser : null;

        $this->extdb->insert_record($dbtable, $data);
    }

    public function cron() {
    }

    public function dispose() {
        if ($this->extdb) {
            $this->extdb->dispose();
        }
        $this->extdb = null;
    }
}
