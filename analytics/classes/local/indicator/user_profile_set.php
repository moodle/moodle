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
 * User profile set indicator.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * User profile set indicator.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_profile_set extends linear {


    public static function get_name() {
        return get_string('indicator:completeduserprofile', 'analytics');
    }

    public static function required_sample_data() {
        return array('user');
    }

    protected function calculate_sample($sampleid, $sampleorigin, $starttime = false, $endtime = false) {
        global $CFG;

        $user = $this->retrieve('user', $sampleid);

        // Nothing set results in -1.
        $calculatedvalue = self::MIN_VALUE;

        if (!empty($CFG->sitepolicy) && !$user->policyagreed) {
            return self::MIN_VALUE;
        }

        if (!$user->confirmed) {
            return self::MIN_VALUE;
        }

        if ($user->description != '') {
            $calculatedvalue += 1;
        }

        if ($user->picture != '') {
            $calculatedvalue += 1;
        }

        // 0.2 for any of the following fields being set (some of them may even be compulsory or have a default).
        $fields = array('institution', 'department', 'address', 'city', 'country', 'url');
        foreach ($fields as $fieldname) {
            if ($user->{$fieldname} != '') {
                $calculatedvalue += 0.2;
            }
        }

        return $this->limit_value($calculatedvalue);
    }
}
