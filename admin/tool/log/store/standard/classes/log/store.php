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
 * Standard log reader/writer.
 *
 * @package    logstore_standard
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_standard\log;

defined('MOODLE_INTERNAL') || die();

class store implements \tool_log\log\writer, \core\log\sql_internal_reader {
    use \tool_log\helper\store,
        \tool_log\helper\buffered_writer,
        \tool_log\helper\reader;

    /** @var string $logguests true if logging guest access */
    protected $logguests;

    public function __construct(\tool_log\log\manager $manager) {
        $this->helper_setup($manager);
        // Log everything before setting is saved for the first time.
        $this->logguests = $this->get_config('logguests', 1);
    }

    /**
     * Finally store the events into the database.
     *
     * @param \core\event\base[] $events
     */
    protected function insert_events($events) {
        global $DB;

        $dataobj = array();

        // Filter events.
        foreach ($events as $event) {
            if ((!CLI_SCRIPT or PHPUNIT_TEST) and !$this->logguests) {
                // Always log inside CLI scripts because we do not login there.
                if (!isloggedin() or isguestuser()) {
                    continue;
                }
            }

            $data = $event->get_data();
            $data['other'] = serialize($data['other']);
            if (CLI_SCRIPT) {
                $data['origin'] = 'cli';
                $data['ip'] = null;
            } else {
                $data['origin'] = 'web';
                $data['ip'] = getremoteaddr();
            }
            $data['realuserid'] = \core\session\manager::is_loggedinas() ? $_SESSION['USER']->realuser : null;
            $dataobj[] = $data;
        }

        $DB->insert_records('logstore_standard_log', $dataobj);
    }

    public function get_events_select($selectwhere, array $params, $sort, $limitfrom, $limitnum) {
        global $DB;

        $events = array();
        $records = $DB->get_records_select('logstore_standard_log', $selectwhere, $params, $sort, '*', $limitfrom, $limitnum);

        foreach ($records as $data) {
            $extra = array('origin' => $data->origin, 'ip' => $data->ip, 'realuserid' => $data->realuserid);
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

            $events[$id] = \core\event\base::restore($data, $extra);
        }

        return $events;
    }

    public function get_events_select_count($selectwhere, array $params) {
        global $DB;
        return $DB->count_records_select('logstore_standard_log', $selectwhere, $params);
    }

    public function get_internal_log_table_name() {
        return 'logstore_standard_log';
    }

    public function cron() {
        global $DB;
        $loglifetime = $this->get_config('loglifetime', 0);

        // NOTE: we should do this only once a day, new cron will deal with this.

        if ($loglifetime > 0) {
            $loglifetime = time() - ($loglifetime * 3600 * 24); // Value in days.
            $DB->delete_records_select("logstore_standard_log", "timecreated < ?", array($loglifetime));
            mtrace(" Deleted old log records from standard store.");
        }
    }

    /**
     * Are the new events appearing in the reader?
     *
     * @return bool true means new log events are being added, false means no new data will be added
     */
    public function is_logging() {
        // Only enabled stpres are queried,
        // this means we can return true here unless store has some extra switch.
        return true;
    }
}
