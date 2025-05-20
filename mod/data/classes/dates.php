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
 * Contains the class for fetching the important dates in mod_data for a given module instance and a user.
 *
 * @package   mod_data
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_data;

use core\activity_dates;

/**
 * Class for fetching the important dates in mod_data for a given module instance and a user.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates extends activity_dates {

    /** @var int|null timeopen the activity opening date */
    private ?int $timeopen;

    /** @var int|null timeclose the activity closing date */
    private ?int $timeclose;

    /**
     * Returns a list of important dates in mod_data
     *
     * @return array
     */
    protected function get_dates(): array {
        $timeopen = $this->cm->customdata['timeavailablefrom'] ?? null;
        $timeclose = $this->cm->customdata['timeavailableto'] ?? null;

        $this->timeopen = $timeopen ? (int) $timeopen : null;
        $this->timeclose = $timeclose ? (int) $timeclose : null;

        $now = time();
        $dates = [];

        if ($this->timeopen) {
            $openlabelid = $this->timeopen > $now ? 'activitydate:opens' : 'activitydate:opened';
            $dates[] = [
                'dataid' => 'timeavailablefrom',
                'label' => get_string($openlabelid, 'course'),
                'timestamp' => (int) $this->timeopen,
            ];
        }

        if ($this->timeclose) {
            $closelabelid = $this->timeclose > $now ? 'activitydate:closes' : 'activitydate:closed';
            $dates[] = [
                'dataid' => 'timeavailableto',
                'label' => get_string($closelabelid, 'course'),
                'timestamp' => (int) $this->timeclose,
            ];
        }

        return $dates;
    }

    /**
     * Returns the dues date data, if any.
     * @return int|null the close timestamp or null if not set.
     */
    public function get_due_date(): ?int {
        if (!isset($this->timeclose)) {
            $this->get_dates();
        }
        return $this->timeclose;
    }
}
