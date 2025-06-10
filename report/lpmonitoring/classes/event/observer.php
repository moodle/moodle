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
 * An event observer.
 *
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\event;

use report_lpmonitoring\api;
use report_lpmonitoring\report_competency_config;
use core_competency\competency_framework;
use core_competency\competency;

/**
 * An event observer.
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * Listen to events and queue the submission for processing.
     * @param \core\event\competency_framework_deleted $event
     */
    public static function framework_deleted(\core\event\competency_framework_deleted $event) {

        $eventdata = $event->get_data();

        // Get data of framework.
        $record = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);

        api::delete_report_competency_config($record->id);
    }

}
