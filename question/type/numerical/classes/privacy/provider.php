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
 * Privacy Subsystem implementation for qtype_numerical.
 *
 * @package    qtype_numerical
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_numerical\privacy;

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\user_preference_provider;
use \core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for qtype_numerical implementing user_preference_provider.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // This component has data.
        // We need to return default options that have been set a user preferences.
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\user_preference_provider
{

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference('qtype_numerical_defaultmark', 'privacy:preference:defaultmark');
        $collection->add_user_preference('qtype_numerical_penalty', 'privacy:preference:penalty');
        $collection->add_user_preference('qtype_numerical_unitrole', 'privacy:preference:unitrole');
        $collection->add_user_preference('qtype_numerical_unitpenalty', 'privacy:preference:unitpenalty');
        $collection->add_user_preference('qtype_numerical_unitgradingtypes', 'privacy:preference:unitgradingtypes');
        $collection->add_user_preference('qtype_numerical_multichoicedisplay', 'privacy:preference:multichoicedisplay');
        $collection->add_user_preference('qtype_numerical_unitsleft', 'privacy:preference:unitsleft');
        return $collection;
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preference = get_user_preferences('qtype_numerical_defaultmark', null, $userid);
        if (null !== $preference) {
            $desc = get_string('privacy:preference:defaultmark', 'qtype_numerical');
            writer::export_user_preference('qtype_numerical', 'defaultmark', $preference, $desc);
        }

        $preference = get_user_preferences('qtype_numerical_penalty', null, $userid);
        if (null !== $preference) {
            $desc = get_string('privacy:preference:penalty', 'qtype_numerical');
            writer::export_user_preference('qtype_numerical', 'penalty', transform::percentage($preference), $desc);
        }

        $preference = get_user_preferences('qtype_numerical_unitrole', null, $userid);
        if (null !== $preference) {
            if ($preference == \qtype_numerical::UNITNONE) {
                $stringvalue = get_string('onlynumerical', 'qtype_numerical');
            } else if ($preference == \qtype_numerical::UNITOPTIONAL) {
                $stringvalue = get_string('manynumerical', 'qtype_numerical');
            } else if ($preference == \qtype_numerical::UNITGRADED) {
                $stringvalue = get_string('unitgraded', 'qtype_numerical');
            }
            $desc = get_string('privacy:preference:unitrole', 'qtype_numerical');
            writer::export_user_preference('qtype_numerical', 'unitrole', $stringvalue, $desc);
        }

        $preference = get_user_preferences('qtype_numerical_unitpenalty', null, $userid);
        if (null !== $preference) {
            $desc = get_string('privacy:preference:unitpenalty', 'qtype_numerical');
            writer::export_user_preference('qtype_numerical', 'unitpenalty', $preference, $desc);
        }

        $preference = get_user_preferences('qtype_numerical_unitgradingtypes', null, $userid);
        if (null !== $preference) {
            if ($preference == \qtype_numerical::UNITGRADEDOUTOFMARK) {
                $stringvalue = get_string('decfractionofresponsegrade', 'qtype_numerical');
            } else if ($preference == \qtype_numerical::UNITGRADEDOUTOFMAX) {
                $stringvalue = get_string('decfractionofquestiongrade', 'qtype_numerical');
            }
            $desc = get_string('privacy:preference:unitgradingtypes', 'qtype_numerical');
            writer::export_user_preference('qtype_numerical', 'unitgradingtypes', $stringvalue, $desc);
        }

        $preference = get_user_preferences('qtype_numerical_multichoicedisplay', null, $userid);
        if (null !== $preference) {
            if ($preference == \qtype_numerical::UNITINPUT) {
                $stringvalue = get_string('editableunittext', 'qtype_numerical');
            } else if ($preference == \qtype_numerical::UNITRADIO) {
                $stringvalue = get_string('unitchoice', 'qtype_numerical');
            } else if ($preference == \qtype_numerical::UNITSELECT) {
                $stringvalue = get_string('unitselect', 'qtype_numerical');
            }
            $desc = get_string('privacy:preference:multichoicedisplay', 'qtype_numerical');
            writer::export_user_preference('qtype_numerical', 'multichoicedisplay', $stringvalue, $desc);
        }

        $preference = get_user_preferences('qtype_numerical_unitsleft', null, $userid);
        if (null !== $preference) {
            if ($preference) {
                $stringvalue = get_string('leftexample', 'qtype_numerical');
            } else {
                $stringvalue = get_string('rightexample', 'qtype_numerical');
            }
            $desc = get_string('privacy:preference:unitsleft', 'qtype_numerical');
            writer::export_user_preference('qtype_numerical', 'unitsleft', $stringvalue, $desc);
        }
    }
}
