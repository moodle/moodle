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
 * Observer
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\entities\quizquestionrelations;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\datatypes_service;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'slot_deleted' event is triggered.
     *
     * @param \mod_quiz\event\slot_deleted $event
     */
    public static function slot_deleted(\mod_quiz\event\slot_deleted $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            self::export_event($event);
        }
    }

    /**
     * Export data event.
     *
     * @param $event
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($event, $fields = []) {
        global $DB;

        $eventdata = $event->get_data();

        $record = (object)[
            'id' => $eventdata['objectid'],
            'quizid' => $eventdata['other']['quizid'],
            'slot' => $eventdata['other']['slotnumber'],
            'crud' => 'd',
        ];

        $entity = new quizquestionrelation($record, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);

        $migration = datatypes_service::init_migration(datatypes_service::get_datatype('quizquestionrelations'), null, false);
        list($sql, $params) = $migration->get_sql(false, 'quizid=' . $eventdata['other']['quizid']);

        foreach ($DB->get_recordset_sql($sql, $params) as $record) {
            $entity = new quizquestionrelation($record, $fields);
            $data = $entity->export();

            $tracking = new events_service($entity::TYPE);
            $tracking->track($data);
        }
    }

}

