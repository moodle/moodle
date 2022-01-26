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

namespace core_reportbuilder\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use core_reportbuilder\local\helpers\user_filter_manager;
use core_reportbuilder\local\models\audience;
use core_reportbuilder\local\models\column;
use core_reportbuilder\local\models\filter;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\models\schedule;

/**
 * Privacy Subsystem for core_reportbuilder
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns metadata about the component
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(report::TABLE, [
            'name' => 'privacy:metadata:report:name',
            'usercreated' => 'privacy:metadata:report:usercreated',
            'usermodified' => 'privacy:metadata:report:usermodified',
        ], 'privacy:metadata:report');

        $collection->add_database_table(column::TABLE, [
            'uniqueidentifier' => 'privacy:metadata:column:uniqueidentifier',
            'usercreated' => 'privacy:metadata:column:usercreated',
            'usermodified' => 'privacy:metadata:column:usermodified',
        ], 'privacy:metadata:column');

        $collection->add_database_table(filter::TABLE, [
            'uniqueidentifier' => 'privacy:metadata:filter:uniqueidentifier',
            'usercreated' => 'privacy:metadata:filter:usercreated',
            'usermodified' => 'privacy:metadata:filter:usermodified',
        ], 'privacy:metadata:filter');

        $collection->add_database_table(audience::TABLE, [
            'classname' => 'privacy:metadata:audience:classname',
            'usercreated' => 'privacy:metadata:audience:usercreated',
            'usermodified' => 'privacy:metadata:audience:usermodified',
        ], 'privacy:metadata:audience');

        $collection->add_database_table(schedule::TABLE, [
            'name' => 'privacy:metadata:schedule:name',
            'userviewas' => 'privacy:metadata:schedule:userviewas',
            'usercreated' => 'privacy:metadata:schedule:usercreated',
            'usermodified' => 'privacy:metadata:schedule:usermodified',
        ], 'privacy:metadata:schedule');

        $collection->add_user_preference('core_reportbuilder', 'privacy:metadata:preference:reportfilter');

        return $collection;
    }

    /**
     * Export all user preferences for the component
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid): void {
        $preferencestring = get_string('privacy:metadata:preference:reportfilter', 'core_reportbuilder');

        $filters = user_filter_manager::get_all_for_user($userid);
        foreach ($filters as $key => $filter) {
            writer::export_user_preference('core_reportbuilder',
                $key,
                json_encode($filter, JSON_PRETTY_PRINT),
                $preferencestring
            );
        }
    }
}
