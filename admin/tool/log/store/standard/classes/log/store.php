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

class store implements \tool_log\log\writer, \core\log\sql_reader {
    /** @var string $logguests true if logging guest access */
    protected $logguests;

    public function __construct(\tool_log\log\manager $manager) {
        $this->logguests = get_config('logstore_standard', 'logguests');
        if ($this->logguests === false) {
            // Log everything before setting is saved for the first time.
            $this->logguests = '1';
        }
    }

    public function write(\core\event\base $event) {
        global $DB;

        // Filter events.
        if (!CLI_SCRIPT and !$this->logguests) {
            // Always log inside CLI scripts because we do not login there.
            if (!isloggedin() or isguestuser()) {
                return;
            }
        }

        $data = $event->get_data();
        $data['other'] = serialize($data['other']);
        if (CLI_SCRIPT) {
            $data['origin'] = 'cli';
        } else {
            $data['origin'] = getremoteaddr();
        }
        $data['realuserid'] = \core\session\manager::is_loggedinas() ? $_SESSION['USER']->realuser : null;

        $DB->insert_record('logstore_standard_log', $data);
    }

    public function get_name() {
        return get_string('pluginname', 'logstore_standard');
    }

    public function get_description() {
        return get_string('pluginname_desc', 'logstore_standard');
    }

    public function can_access(\context $context) {
        return has_capability('logstore/standard:read', $context);
    }

    public function get_events($selectwhere, array $params, $sort, $limitfrom, $limitnum) {
        global $DB;

        $events = array();
        $records = $DB->get_records_select('logstore_standard_log', $selectwhere, $params, $sort, '*', $limitfrom, $limitnum);

        foreach ($records as $data) {
            $extra = array('origin'=>$data->origin, 'realuserid'=>$data->realuserid);
            $data = (array)$data;
            $id = $data['id'];
            $data['other'] = unserialize($data['other']);
            if ($data['other'] === false) {
                $data['other'] = array();
            }
            unset($data['origin']);
            unset($data['realuserid']);
            unset($data['id']);

            $events[$id] = \core\event\base::restore($data, $extra);
        }

        return $events;
    }

    public function get_events_count($selectwhere, array $params) {
        global $DB;
        return $DB->count_records_select('logstore_standard_log', $selectwhere, $params);
    }

    public function get_log_table() {
        return 'logstore_standard_log';
    }

    public function cron() {
    }

    public function dispose() {
    }
}
