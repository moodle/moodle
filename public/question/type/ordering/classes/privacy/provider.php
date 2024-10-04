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

namespace qtype_ordering\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem for qtype_numerical implementing null_provider.
 *
 * @package    qtype_ordering
 * @copyright  2013 Gordon Bateson (gordon.bateson@gmail.com)
 * @author     rdebleu@eWallah.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\user_preference_provider {
    /**
     * Returns metadata about this question.
     *
     * @param  collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this question.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference('qtype_ordering_layouttype', 'privacy:preference:layouttype');
        $collection->add_user_preference('qtype_ordering_selecttype', 'privacy:preference:selecttype');
        $collection->add_user_preference('qtype_ordering_selectcount', 'privacy:preference:selectcount');
        $collection->add_user_preference('qtype_ordering_gradingtype', 'privacy:preference:gradingtype');
        $collection->add_user_preference('qtype_ordering_showgrading', 'privacy:preference:showgrading');
        $collection->add_user_preference('qtype_ordering_numberingstyle', 'privacy:preference:numberingstyle');
        return $collection;
    }

    /**
     * Export all user preferences for the question.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     * @return void
     */
    public static function export_user_preferences(int $userid): void {
        $preference = get_user_preferences('qtype_ordering_layouttype', null, $userid);
        if (null !== $preference) {
            writer::export_user_preference(
                'qtype_ordering',
                'layouttype',
                $preference,
                get_string('privacy:preference:layouttype', 'qtype_ordering')
            );
        }

        $preference = get_user_preferences('qtype_ordering_selecttype', null, $userid);
        if (null !== $preference) {
            writer::export_user_preference(
                'qtype_ordering',
                'selecttype',
                $preference,
                get_string('privacy:preference:selecttype', 'qtype_ordering')
            );
        }

        $preference = get_user_preferences('qtype_ordering_selectcount', null, $userid);
        if (null !== $preference) {
            writer::export_user_preference(
                'qtype_ordering',
                'selectcount',
                $preference,
                get_string('privacy:preference:selectcount', 'qtype_ordering')
            );
        }

        $preference = get_user_preferences('qtype_ordering_gradingtype', null, $userid);
        if (null !== $preference) {
            writer::export_user_preference(
                'qtype_ordering',
                'gradingtype',
                $preference,
                get_string('privacy:preference:gradingtype', 'qtype_ordering')
            );
        }

        $preference = get_user_preferences('qtype_ordering_showgrading', null, $userid);
        if (null !== $preference) {
            writer::export_user_preference(
                'qtype_ordering',
                'showgrading',
                $preference,
                get_string('privacy:preference:showgrading', 'qtype_ordering')
            );
        }

        $preference = get_user_preferences('qtype_ordering_numberingstyle', null, $userid);
        if (null !== $preference) {
            writer::export_user_preference(
                'qtype_ordering',
                'numberingstyle',
                $preference,
                get_string('privacy:preference:numberingstyle', 'qtype_ordering')
            );
        }
    }
}
