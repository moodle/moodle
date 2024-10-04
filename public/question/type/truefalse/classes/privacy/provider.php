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

namespace qtype_truefalse\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem implementation for qtype_truefalse
 *
 * @package    qtype_truefalse
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns metadata about this system
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference('qtype_truefalse_showstandardinstruction', 'privacy:preference:showstandardinstruction');
        return $collection;
    }

    /**
     * Export plugin user preferences for the given user
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid): void {
        $preference = get_user_preferences('qtype_truefalse_showstandardinstruction', null, $userid);
        if ($preference !== null) {
            writer::export_user_preference('qtype_truefalse', 'showstandardinstruction', transform::yesno($preference),
                get_string('privacy:preference:showstandardinstruction', 'qtype_truefalse'));
        }
    }
}
