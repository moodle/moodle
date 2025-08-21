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

declare(strict_types=1);

namespace mod_bigbluebuttonbn;

use cm_info;
use core\activity_dates;

/**
 * Class for fetching the important dates in mod_bigbluebuttonbn for a given module instance and a user.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates extends activity_dates {
    /**
     * Returns the activity due date.
     *
     * @var int|null $timeclose the activity due date
     */
    protected ?int $timeclose = null;
    /**
     * @var int|null $timeopen the activity open date
     */
    protected ?int $timeopen = null;

    /**
     * @var instance the instance of the activity
     */
    protected instance $instance;

    /**
     * activity_dates constructor.
     *
     * @param cm_info $cm course module
     * @param int $userid user id
     */
    public function __construct(cm_info $cm, int $userid) {
        parent::__construct($cm, $userid);
        $this->instance = instance::get_from_cmid((int) $cm->id);
    }

    /**
     * Returns a list of important dates in mod_choice
     *
     * @return array
     */
    protected function get_dates(): array {
        $timeopen = $this->instance->get_instance_var('openingtime');
        $timeclose = $this->instance->get_instance_var('closingtime');
        $now = time();
        $dates = [];

        if ($timeopen) {
            $openlabelid = $timeopen > $now ? 'activitydate:opens' : 'activitydate:opened';
            $dates[] = [
                'dataid' => 'timeopen',
                'label' => get_string($openlabelid, 'course'),
                'timestamp' => (int) $timeopen,
            ];
            $this->timeopen = (int) $timeopen;
        }

        if ($timeclose) {
            $closelabelid = $timeclose > $now ? 'activitydate:closes' : 'activitydate:closed';
            $dates[] = [
                'dataid' => 'timeclose',
                'label' => get_string($closelabelid, 'course'),
                'timestamp' => (int) $timeclose,
            ];
            $this->timeclose = (int) $timeclose;
        }

        return $dates;
    }

    /**
     * Returns the activity due date.
     *
     * @return int|null
     */
    public function get_close_date(): ?int {
        if (!isset($this->timeclose)) {
            $this->get_dates();
        }
        return $this->timeclose;
    }

    /**
     * Returns the activity open date.
     *
     * @return int|null
     */
    public function get_open_date(): ?int {
        if (!isset($this->timeopen)) {
            $this->get_dates();
        }
        return $this->timeopen;
    }
}
