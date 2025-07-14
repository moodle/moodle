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
 * Contains the class for fetching the important dates in mod_workshop for a given module instance and a user.
 *
 * @package   mod_workshop
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_workshop;

use core\activity_dates;

/**
 * Class for fetching the important dates in mod_workshop for a given module instance and a user.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates extends activity_dates {

    /**
     * Returns a list of important dates in mod_workshop
     *
     * @return array
     */
    protected function get_dates(): array {
        $submissionstart = $this->cm->customdata['submissionstart'] ?? null;
        $submissionend = $this->cm->customdata['submissionend'] ?? null;
        $assessmentstart = $this->cm->customdata['assessmentstart'] ?? null;
        $assessmentend = $this->cm->customdata['assessmentend'] ?? null;

        $now = time();
        $dates = [];

        if ($submissionstart) {
            $openlabelid = $submissionstart > $now ? 'activitydate:submissionsopen' : 'activitydate:submissionsopened';
            $dates[] = [
                'dataid' => 'submissionstart',
                'label' => get_string($openlabelid, 'mod_workshop'),
                'timestamp' => (int) $submissionstart,
            ];
        }

        if ($submissionend) {
            $closelabelid = $submissionend > $now ? 'activitydate:submissionsclose' : 'activitydate:submissionsclosed';
            $dates[] = [
                'dataid' => 'submissionend',
                'label' => get_string($closelabelid, 'mod_workshop'),
                'timestamp' => (int) $submissionend,
            ];
        }

        if ($assessmentstart) {
            $openlabelid = $assessmentstart > $now ? 'activitydate:assessmentsopen' : 'activitydate:assessmentsopened';
            $dates[] = [
                'dataid' => 'assessmentstart',
                'label' => get_string($openlabelid, 'mod_workshop'),
                'timestamp' => (int) $assessmentstart,
            ];
        }

        if ($assessmentend) {
            $closelabelid = $assessmentend > $now ? 'activitydate:assessmentsclose' : 'activitydate:assessmentsclosed';
            $dates[] = [
                'dataid' => 'assessmentend',
                'label' => get_string($closelabelid, 'mod_workshop'),
                'timestamp' => (int) $assessmentend,
            ];
        }

        return $dates;
    }
}
