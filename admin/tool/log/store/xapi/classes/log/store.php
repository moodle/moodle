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

namespace logstore_xapi\log;
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../src/autoload.php');
require_once($CFG->dirroot . '/admin/tool/log/store/xapi/lib.php');

use core_plugin_manager;
use \tool_log\log\writer as log_writer;
use \tool_log\log\manager as log_manager;
use \tool_log\helper\store as helper_store;
use \tool_log\helper\reader as helper_reader;
use \tool_log\helper\buffered_writer as helper_writer;
use \core\event\base as event_base;
use \stdClass as php_obj;
/**
 * Processes events and enables them to be sent to a logstore.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store extends php_obj implements log_writer {
    use helper_store;
    use helper_reader;
    use helper_writer;

    /**
     * Constructs a new store.
     * @param log_manager $manager
     */
    public function __construct(log_manager $manager) {
        $this->helper_setup($manager);
    }

    /**
     * Should the event be ignored (not logged)? Overrides helper_writer.
     * @param event_base $event
     * @return bool
     *
     */
    protected function is_event_ignored(event_base $event) {
        $allowguestlogging = $this->get_config('logguests', 1);
        if ((!CLI_SCRIPT || PHPUNIT_TEST) && !$allowguestlogging && isguestuser()) {
            // Always log inside CLI scripts because we do not login there.
            return true;
        }

        $enabledevents = explode(',', $this->get_config('routes', ''));
        $isdisabledevent = !in_array($event->eventname, $enabledevents);
        return $isdisabledevent;
    }

    /**
     * Get the eventid from logstore_standard_log.
     * Return the persistent logstore_standard_log id from the database.
     * Return value 0 means not found.
     *
     * @param event_base $event
     * @return int
     *
     */
    protected function get_event_id($event) {
        global $DB;

        $sqlparams = array();
        $where = array('1 = 1');

        if (!empty($event->eventname)) {
            $sqlparams['eventname'] = $event->eventname;
            $where[] = 'eventname = :eventname';
        }

        if (!empty($event->component)) {
            $sqlparams['component'] = $event->component;
            $where[] = 'component = :component';
        }

        if (!empty($event->action)) {
            $sqlparams['action'] = $event->action;
            $where[] = 'action = :action';
        }

        if (!empty($event->target)) {
            $sqlparams['target'] = $event->target;
            $where[] = 'target = :target';
        }

        if (!empty($event->objecttable)) {
            $sqlparams['objecttable'] = $event->objecttable;
            $where[] = 'objecttable = :objecttable';
        } else {
            $where[] = 'objecttable IS NULL';
        }

        if (!empty($event->objectid)) {
            $sqlparams['objectid'] = $event->objectid;
            $where[] = 'objectid = :objectid';
        } else {
            $where[] = 'objectid IS NULL';
        }

        if (!empty($event->timecreated)) {
            $sqlparams['timecreated'] = $event->timecreated;
            $where[] = 'timecreated = :timecreated';
        }

        if (!empty($event->userid)) {
            $sqlparams['userid'] = $event->userid;
            $where[] = 'userid = :userid';
        }

        if (!empty($event->anonymous)) {
            $sqlparams['anonymous'] = $event->anonymous;
            $where[] = 'anonymous = :anonymous';
        }

        // Perhaps we need more rule here.
        if (empty($sqlparams)) {
            return 0;
        }

        $sqlwhere = implode(' AND ', $where);

        $sql = "SELECT MAX(id) AS id
                  FROM {logstore_standard_log}
                 WHERE " . $sqlwhere;

        $row = $DB->get_record_sql($sql, $sqlparams);
        if (empty($row) || empty($row->id)) {
            return 0;
        }
        return $row->id;
    }

    /**
     * Insert events in bulk to the database. Overrides helper_writer.
     * @param array $events raw event data
     */
    protected function insert_event_entries($events) {
        global $DB;

        // If in background mode, just save them in the database.
        if ($this->get_config('backgroundmode', false)) {
            $events = $this->convert_array_to_objects($events);
            $events = $this->get_persistent_eventids($events);
            $DB->insert_records('logstore_xapi_log', $events);
        } else {
            $this->process_events($events);
        }
    }

    /**
     * Retrieve the maximum batch size.
     *
     * @return int
     */
    public function get_max_batch_size() {
        return $this->get_config('maxbatchsize', 100);
    }

    /**
     * Retrieve the maximum batch size for failed events.
     *
     * @return int
     */
    public function get_max_batch_size_for_failed() {
        return $this->get_config('maxbatchsizeforfailed', 100);
    }

    /**
     * Retrieve the maximum batch size for historical events.
     *
     * @return int
     */
    public function get_max_batch_size_for_historical() {
        return $this->get_config('maxbatchsizeforhistorical', 100);
    }

    /**
     * Take rows from logstore_standard_log for the emit_task or failed_task
     * and add in the logstorestandardlogid and set the type.
     *
     * @param array $events raw event data
     * @param int $eventtype event type
     * @return array
     */
    private function get_persistent_eventids(array $events, $eventtype = XAPI_IMPORT_TYPE_LIVE) {
        foreach ($events as $event) {
            $event->logstorestandardlogid = $this->get_event_id($event);
            $event->type = $eventtype;
        }
        return $events;
    }

    /**
     * Take successful events and save each using logstore_xapi_add_event_to_sent_log.
     *
     * @param array $events raw events data
     */
    private function save_sent_events(array $events) {
        $successfulevents = logstore_xapi_get_successful_events($events);
        foreach ($successfulevents as $event) {
            logstore_xapi_add_event_to_sent_log($event);
        }
    }

    /**
     * Process events.
     * Transform events using the correct event handler and save sent events.
     *
     * @param array $events raw event data
     * @param int $eventtype event type
     * @return array
     */
    public function process_events(array $events, $eventtype = XAPI_IMPORT_TYPE_LIVE) {
        $events = $this->convert_array_to_objects($events);
        $events = $this->get_persistent_eventids($events, $eventtype);

        $config = $this->get_handler_config();
        $loadedevents = \src\handler($config, $events);
        $this->save_sent_events($loadedevents);

        return $loadedevents;
    }

    /**
     * Get handler configuration.
     *
     * @return array Handler configuration.
     */
    protected function get_handler_config() {
        global $DB, $CFG;

        $plugin = core_plugin_manager::instance()->get_plugin_info('logstore_xapi');

        $logerror = function ($message = '') {
            debugging($message, DEBUG_NORMAL);
        };
        $loginfo = function ($message = '') {
            debugging($message, DEBUG_DEVELOPER);
        };

        $handlerconfig = [
            'log_error' => $logerror,
            'log_info' => $loginfo,
            'transformer' => [
                'source_lang' => 'en',
                'send_mbox' => $this->get_config('mbox', false),
                'send_response_choices' => $this->get_config('sendresponsechoices', false),
                'send_short_course_id' => $this->get_config('shortcourseid', false),
                'send_course_and_module_idnumber' => $this->get_config('sendidnumber', false),
                'send_username' => $this->get_config('send_username', false),
                'send_jisc_data' => $this->get_config('send_jisc_data', false),
                'session_id' => sesskey(),
                'plugin_url' => 'https://github.com/xAPI-vle/moodle-logstore_xapi',
                'plugin_version' => $plugin->release,
                'repo' => new \src\transformer\repos\MoodleRepository($DB),
                'app_url' => $CFG->wwwroot,
            ],
            'loader' => [
                'loader' => 'moodle_curl_lrs',
                'lrs_endpoint' => $this->get_config('endpoint', ''),
                'lrs_username' => $this->get_config('username', ''),
                'lrs_password' => $this->get_config('password', ''),
                'lrs_max_batch_size' => $this->get_max_batch_size(),
                'lrs_resend_failed_batches' => $this->get_config('resendfailedbatches', false),
            ],
        ];

        if (isset($CFG->totara_release)) {
            $source = [
                'source_url' => 'http://totaralearning.com',
                'source_name' => 'Totara Learn',
                'source_version' => $CFG->totara_version
            ];
        } else {
            $source = [
                'source_url' => 'http://moodle.org',
                'source_name' => 'Moodle',
                'source_version' => $CFG->release
            ];
        }

        $handlerconfig['transformer'] = array_merge($handlerconfig['transformer'], $source);

        return $handlerconfig;
    }

    /**
     * Determines if a connection exists to the store.
     * @return boolean
     */
    public function is_logging() {
        return true;
    }

    /**
     * Reread or convert event to object.
     *
     * @param array $events Array of events
     * @return array of objects of events.
     */
    protected function convert_array_to_objects($events) {
        $return = array();

        if (!empty($events)) {
            foreach ($events as $event) {
                if (is_object($event)) {
                    $return[] = $event;
                } else {
                    $return[] = (object)$event;
                }
            }
        }

        return $return;
    }
}
