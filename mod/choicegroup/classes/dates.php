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
 * Version information
 *
 * @package    mod_choicegroup
 * @copyright  2021 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_choicegroup;

use core\activity_dates;

class dates extends activity_dates {

    /**
     * Returns a list of important dates in mod_choicegroup
     * (code copied from /mod/assign/classes/dates.php)
     *
     * @return array
     */
    protected function get_dates(): array {

        $course = get_course($this->cm->course);
        $context = \context_module::instance($this->cm->id);

        list($course, $module) = get_course_and_cm_from_cmid($this->cm->id);

        $choicegroup = choicegroup_get_choicegroup($module->instance);

        $timeopen = $choicegroup->timeopen ?? null;
        $timeclose = $choicegroup->timeclose ?? null;

        $now = time();
        $dates = [];

        if ($timeopen) {
            $openlabelid = $timeopen > $now ? 'activitydate:willopen' : 'activitydate:hasopened';
            $date = [
                    'label'     => get_string($openlabelid, 'mod_choicegroup'),
                    'timestamp' => (int)$timeopen,
            ];
            $dates[] = $date;
        }

        if ($timeclose) {
            $date = [
                    'label'     => get_string('activitydate:willclose', 'mod_choicegroup'),
                    'timestamp' => (int)$timeclose,
            ];
            $dates[] = $date;
        }

        return $dates;
    }
}
