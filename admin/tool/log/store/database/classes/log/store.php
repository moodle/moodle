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
 * External database store.
 *
 * @package    logstore_database
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_database\log;
defined('MOODLE_INTERNAL') || die();

class store implements \tool_log\log\writer, \core\log\sql_select_reader {
    use \tool_log\helper\store,
        \tool_log\helper\reader,
        \tool_log\helper\buffered_writer {
        dispose as helper_dispose;
    }

    /** @var \moodle_database $extdb */
    protected $extdb;

    /** @var bool $logguests true if logging guest access */
    protected $logguests;

    /** @var array $includelevels An array of education levels to include */
    protected $includelevels = array();

    /** @var array $includeactions An array of actions types to include */
    protected $includeactions = array();

    /**
     * Construct
     *
     * @param \tool_log\log\manager $manager
     */
    public function __construct(\tool_log\log\manager $manager) {
        $this->helper_setup($manager);
        $this->buffersize = $this->get_config('buffersize', 50);
        $this->logguests = $this->get_config('logguests', 1);
        $actions = $this->get_config('includeactions', '');
        $levels = $this->get_config('includelevels', '');
        $this->includeactions = $actions === '' ? array() : explode(',', $actions);
        $this->includelevels = $levels === '' ? array() : explode(',', $levels);
    }

    /**
     * Setup the Database.
     *
     * @return bool
     */
    protected function init() {
        if (isset($this->extdb)) {
            return !empty($this->extdb);
        }

        $dbdriver = $this->get_config('dbdriver');
        if (empty($dbdriver)) {
            $this->extdb = false;
            return false;
        }
        list($dblibrary, $dbtype) = explode('/', $dbdriver);

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
                $this->get_config('dbname'), false, $dboptions);
            $tables = $db->get_tables();
            if (!in_array($this->get_config('dbtable'), $tables)) {
                debugging('Cannot find the specified table', DEBUG_DEVELOPER);
                $this->extdb = false;
                return false;
            }
        } catch (\moodle_exception $e) {
            debugging('Cannot connect to external database: ' . $e->getMessage(), DEBUG_DEVELOPER);
            $this->extdb = false;
            return false;
        }

        $this->extdb = $db;
        return true;
    }

    /**
     * Should the event be ignored (== not logged)?
     * @param \core\event\base $event
     * @return bool
     */
    protected function is_event_ignored(\core\event\base $event) {
        if (!in_array($event->crud, $this->includeactions) &&
            !in_array($event->edulevel, $this->includelevels)
        ) {
            // Ignore event if the store settings do not want to store it.
            return true;
        }
        if ((!CLI_SCRIPT or PHPUNIT_TEST) and !$this->logguests) {
            // Always log inside CLI scripts because we do not login there.
            if (!isloggedin() or isguestuser()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Insert events in bulk to the database.
     *
     * @param array $evententries raw event data
     */
    protected function insert_event_entries($evententries) {
        if (!$this->init()) {
            return;
        }
        if (!$dbtable = $this->get_config('dbtable')) {
            return;
        }
        try {
            $this->extdb->insert_records($dbtable, $evententries);
        } catch (\moodle_exception $e) {
            debugging('Cannot write to external database: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Get an array of events based on the passed on params.
     *
     * @param string $selectwhere select conditions.
     * @param array $params params.
     * @param string $sort sortorder.
     * @param int $limitfrom limit constraints.
     * @param int $limitnum limit constraints.
     *
     * @return array|\core\event\base[] array of events.
     */
    public function get_events_select($selectwhere, array $params, $sort, $limitfrom, $limitnum) {
        if (!$this->init()) {
            return array();
        }

        if (!$dbtable = $this->get_config('dbtable')) {
            return array();
        }

        $sort = self::tweak_sort_by_id($sort);

        $events = array();
        $records = $this->extdb->get_records_select($dbtable, $selectwhere, $params, $sort, '*', $limitfrom, $limitnum);

        foreach ($records as $data) {
            $extra = array('origin' => $data->origin, 'realuserid' => $data->realuserid, 'ip' => $data->ip);
            $data = (array)$data;
            $id = $data['id'];
            $data['other'] = unserialize($data['other']);
            if ($data['other'] === false) {
                $data['other'] = array();
            }
            unset($data['origin']);
            unset($data['ip']);
            unset($data['realuserid']);
            unset($data['id']);

            $event = \core\event\base::restore($data, $extra);
            // Add event to list if it's valid.
            if ($event) {
                $events[$id] = $event;
            }
        }

        return $events;
    }

    /**
     * Get number of events present for the given select clause.
     *
     * @param string $selectwhere select conditions.
     * @param array $params params.
     *
     * @return int Number of events available for the given conditions
     */
    public function get_events_select_count($selectwhere, array $params) {
        if (!$this->init()) {
            return 0;
        }

        if (!$dbtable = $this->get_config('dbtable')) {
            return 0;
        }

        return $this->extdb->count_records_select($dbtable, $selectwhere, $params);
    }

    /**
     * Are the new events appearing in the reader?
     *
     * @return bool true means new log events are being added, false means no new data will be added
     */
    public function is_logging() {
        if (!$this->init()) {
            return false;
        }
        return true;
    }

    /**
     * Dispose off database connection after pushing any buffered events to the database.
     */
    public function dispose() {
        $this->helper_dispose();
        if ($this->extdb) {
            $this->extdb->dispose();
        }
        $this->extdb = null;
    }
}
