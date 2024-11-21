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
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\tools;

use local_intelliboard\bb_collaborate\bb_collaborate_adapter;
use local_intelliboard\services\bb_collaborate_service;
use local_intelliboard\bb_collaborate\bb_collaborate_repository;

class bb_collaborate_tool {
    /**
     * Get BB collaborate service
     *
     * @return bb_collaborate_service
     */
    public static function service() {
        return new bb_collaborate_service();
    }

    /**
     * Get BB collaborate adapter
     *
     * @return bb_collaborate_adapter
     * @throws \dml_exception
     */
    public static function adapter() {
        return new bb_collaborate_adapter(
            new bb_collaborate_service(), new bb_collaborate_repository()
        );
    }

    /**
     * Get BB collaborate repository
     *
     * @return bb_collaborate_repository
     */
    public static function repository() {
        return new bb_collaborate_repository();
    }

    /**
     * Convert server time to UTC
     *
     * @param $timestamp
     * @return false|int
     */
    public static function server_time_to_utc($timestamp) {
        $utctimezone = 'UTC';
        $serverTimezoneName = (new \DateTime())->getTimezone()->getName();

        $strTimestamp = date('Y-m-d H:i', $timestamp);
        $serverDatetime = new \DateTime(
            $strTimestamp, new \DateTimeZone($serverTimezoneName)
        );
        $serverDatetime->setTimezone(new \DateTimeZone($utctimezone));
        return strtotime($serverDatetime->format('Y-m-d H:i'));
    }
}