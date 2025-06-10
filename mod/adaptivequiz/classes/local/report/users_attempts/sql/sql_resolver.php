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
 * A class to keep the logic of resolving what sql with its parameters should be used for the report
 * depending on filtering requested, etc.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\users_attempts\sql;

use context;
use mod_adaptivequiz\local\report\users_attempts\filter\filter;
use mod_adaptivequiz\local\report\users_attempts\filter\filter_options;

final class sql_resolver {

    public static function sql_and_params(filter $filter, context $context): sql_and_params {
        if (!$filter->users || $filter->users == filter_options::BOTH_ENROLLED_AND_NOT_ENROLLED_USERS_WITH_ATTEMPTS) {
            $sqlandparams = sql_and_params::default($filter->adaptivequizid);
            if ($filter->groupid) {
                $sqlandparams = $sqlandparams->with_group_filtering($filter->groupid);
            }

            return $sqlandparams;
        }

        $enrolledjoin = get_enrolled_with_capabilities_join($context, '', 'mod/adaptivequiz:attempt',
            $filter->groupid, self::resolve_active_enrolment_flag($filter->users, $filter->includeinactiveenrolments));

        if ($filter->users == filter_options::ENROLLED_USERS_WITH_NO_ATTEMPTS) {
            $sqlandparams = sql_and_params::for_enrolled_with_no_attempts($filter->adaptivequizid, $enrolledjoin);
        }
        if ($filter->users == filter_options::ENROLLED_USERS_WITH_ATTEMPTS) {
            $sqlandparams = sql_and_params::for_enrolled_with_attempts($filter->adaptivequizid, $enrolledjoin);
        }
        if ($filter->users == filter_options::NOT_ENROLLED_USERS_WITH_ATTEMPTS) {
            $sqlandparams = sql_and_params::for_not_enrolled_with_attempts($filter->adaptivequizid, $enrolledjoin);
        }

        if ($filter->groupid) {
            $sqlandparams = $sqlandparams->with_group_filtering($filter->groupid);
        }

        return $sqlandparams;
    }

    private static function resolve_active_enrolment_flag(
        int $usersoption,
        int $includeinactiveenrolmentsoption
    ): bool {
        if ($usersoption == filter_options::ENROLLED_USERS_WITH_NO_ATTEMPTS
            || $usersoption == filter_options::ENROLLED_USERS_WITH_ATTEMPTS) {
            return !$includeinactiveenrolmentsoption;
        }

        return $includeinactiveenrolmentsoption;
    }
}
