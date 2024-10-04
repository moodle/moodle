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

namespace qbank_columnsortorder\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

/**
 * Privacy provider for columnsortorder.
 *
 * @package    qbank_columnsortorder
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // The forms subsystem does not store any data itself, it has no database tables.
    \core_privacy\local\metadata\provider,

    // The forms subsystem has user preferences.
    \core_privacy\local\request\user_preference_provider {

    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference('enabledcol', 'privacy:metadata:preference:enabledcol');
        $collection->add_user_preference('hiddencols', 'privacy:metadata:preference:hiddencols');
        $collection->add_user_preference('colsize', 'privacy:metadata:preference:colsize');

        return $collection;
    }

    public static function export_user_preferences(int $userid) {
        $components = ['core_question', "qbank_history"];
        foreach ($components as $component) {
            $prefnames = ['enabledcol', 'hiddencols', 'colsize'];
            foreach ($prefnames as $name) {
                $preference = get_user_preferences("{$component}_{$name}", null, $userid);
                if ($preference !== null) {
                    $desc = get_string('enabledcol', 'privacy:metadata:preference:enabledcol');
                    writer::export_user_preference('qbank_columnsortorder', "{$component}_{$name}", $preference, $desc);
                }
            }
        }
    }
}
