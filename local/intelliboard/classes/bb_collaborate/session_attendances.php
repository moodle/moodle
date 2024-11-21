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

namespace local_intelliboard\bb_collaborate;

class session_attendances {
    /** @var string  */
    const STATUS_ABSENCE = 'absence';

    /** @var string  */
    const STATUS_LATE = 'late';

    /** @var string  */
    const STATUS_PRESENT = 'present';

    /** @var array  */
    private $data;

    private $session;

    /**
     * Array (response data from BB collaborate server)
     * /sessions/%s/instances/%s/attendees
     *
     * session_attendances constructor.
     * @param array $data
     */
    public function __construct($session, $data) {
        $this->data = $data;
        $this->session = $session;
    }

    /**
     * Get attendances
     *
     * @return array
     */
    public function get_attendances() {
        return $this->data;
    }

    /**
     * Get user status in session
     *
     * @param $userid
     * @return bool
     */
    public function get_status($userid) {
        $status = self::STATUS_ABSENCE;

        foreach($this->data as $item) {
            if($item['externalUserId'] == $userid) {
                $joins = array_map(function($join) {
                    return strtotime($join['joined']);
                }, $item['attendance']);
                $firstjoin = min($joins);

                if($firstjoin <= $this->session->timestart) {
                    $status = self::STATUS_PRESENT;
                } else {
                    $status = self::STATUS_LATE;
                }
                break;
            }
        }

        return $status;
    }
}