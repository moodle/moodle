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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../local_intellidata_base_generator.php');

use local_intellidata\persistent\tracking;
use local_intellidata\helpers\PageParamsHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class tracking_generator implements local_intellidata_base_generator {

    /**
     * Create record.
     *
     * @param $record
     * @return tracking
     */
    public function create($record = null) {

        $record = (object)$record;

        if (!isset($record->page)) {
            $record->page = PageParamsHelper::PAGETYPE_SITE;
        }
        if (!isset($record->param)) {
            $record->param = PageParamsHelper::PAGEPARAM_SYSTEM;
        }
        if (!isset($record->visits)) {
            $record->visits = 1;
        }
        if (!isset($record->timespend)) {
            $record->timespend = 1;
        }
        if (!isset($record->firstaccess)) {
            $record->firstaccess = time();
        }
        if (!isset($record->lastaccess)) {
            $record->lastaccess = time();
        }
        if (!isset($record->timemodified)) {
            $record->timemodified = time();
        }
        if (!isset($record->useragent)) {
            $record->useragent = '';
        }
        if (!isset($record->ip)) {
            $record->ip = '';
        }

        $tracking = new tracking(0, $record);
        $tracking->save();

        return $tracking;
    }
}
