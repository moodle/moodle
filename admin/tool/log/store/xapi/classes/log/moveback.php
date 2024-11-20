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

require_once($CFG->dirroot . '/admin/tool/log/store/xapi/lib.php');

/**
 * Class for moving back the given failed events to standard log.
 *
 * @package     logstore_xapi
 * @subpackage  log
 * @author      Záborski László <laszlo.zaborski@learningpool.com>
 * @copyright   2020 Learning Pool Ltd (http://learningpool.com)
 */
class moveback {

    /**
     * logstore database names.
     */
    const LOGSTORE_NEW = "logstore_xapi_log";

    /**
     * List of event IDs.
     *
     * @var array event ids[]
     */
    protected $eventids = array();

    /**
     * The table where the process works
     *
     * @var bool $historical Data come from logstore table
     */
    protected $historical = false;

    /**
     * The table where the process works
     *
     * @var string
     */
    protected $table = '';

    /**
     * A list containing the constructed sql fragment.
     *
     * @var string
     */
    protected $select = '1=1';

    /**
     * An array of parameters.
     *
     * @var string
     */
    protected $params = array();

    /**
     * Standard constructor for class.
     *
     * @param array $eventids event ids
     * @param bool $historical
     */
    public function __construct(array $eventids, $historical = false) {
        global $DB;

        $this->eventids = $eventids;
        $this->historical = $historical;
        $this->table = XAPI_REPORT_SOURCE_FAILED;
        $this->type = XAPI_IMPORT_TYPE_FAILED;

        if ($this->historical) {
            $this->table = XAPI_REPORT_SOURCE_HISTORICAL;
            $this->type = XAPI_IMPORT_TYPE_HISTORIC;
        }

        if (!empty($eventids)) {
            list($insql, $this->params) = $DB->get_in_or_equal($this->eventids);
            $this->select = 'id ' . $insql;
        }
    }

    /**
     * Return events.
     *
     * @return array
     */
    protected function extract_events() {
        global $DB;

        $events = $DB->get_records_select($this->table, $this->select, $this->params);

        return $events;
    }

    /**
     * Move event.
     *
     * @param object $event The event to move.
     * @return array
     */
    protected function move_event($event) {
        global $DB;

        $skipinsert = false;

        // We set the event type so the scheduled tasks can differentiate the events for resending.
        $event->type = $this->type;

        if ($this->historical) {
            $params = array(
                'logstorestandardlogid' => $event->id
            );

            $event->logstorestandardlogid = $event->id;

            if (!empty($DB->count_records(self::LOGSTORE_NEW, $params))) {
                $skipinsert = true;
            }

        } else {
            unset($event->errortype, $event->response);

            $params = array(
                'id' => $event->id
            );
        }

        if (!$skipinsert) {
            $DB->insert_record(self::LOGSTORE_NEW, $event);
        }

        if (!empty($DB->count_records(XAPI_REPORT_SOURCE_FAILED, $params))) {
            $DB->delete_records(XAPI_REPORT_SOURCE_FAILED, $params);
        }
    }

    /**
     * Do the job.
     *
     * @return bool
     */
    public function execute() {
        $events = $this->extract_events();

        if (empty($events)) {
            return false;
        }

        foreach ($events as $event) {
            $this->move_event($event);
        }

        return true;
    }
}
