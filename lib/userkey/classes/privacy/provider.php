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
 * Privacy class for requesting user data.
 *
 * @package    core_userkey
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_userkey\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;

/**
 * Privacy class for requesting user data.
 *
 * @package    core_userkey
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,

    \core_privacy\local\request\subsystem\plugin_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $collection->add_database_table('user_private_key', [
                'script' => 'privacy:metadata:user_private_key:script',
                'value' => 'privacy:metadata:user_private_key:value',
                'userid' => 'privacy:metadata:user_private_key:userid',
                'instance' => 'privacy:metadata:user_private_key:instance',
                'iprestriction' => 'privacy:metadata:user_private_key:iprestriction',
                'validuntil' => 'privacy:metadata:user_private_key:validuntil',
                'timecreated' => 'privacy:metadata:user_private_key:timecreated',
            ], 'privacy:metadata:user_private_key');

        return $collection;
    }

    /**
     * Exports the data relating to user keys for the specified scripts and instance, within the specified
     * context/subcontext.
     *
     * @param  \context         $context Context owner of the data.
     * @param  array            $subcontext Context owner of the data.
     * @param  string           $script The owner of the data (usually a component name).
     * @param  int              $instance The instance owner of the data.
     */
    public static function export_userkeys(\context $context, array $subcontext, $script, $instance = null) {
        global $DB, $USER;

        $searchparams = [
            'script' => $script,
            'userid' => $USER->id,
        ];

        if (null !== $instance) {
            $searchparams['instance'] = $instance;
        }

        $keys = $DB->get_recordset('user_private_key', $searchparams);
        $keydata = [];
        foreach ($keys as $key) {
            $keydata[] = (object) [
                'script' => $key->script,
                'instance' => $key->instance,
                'iprestriction' => $key->iprestriction,
                'validuntil' => transform::datetime($key->validuntil),
                'timecreated' => transform::datetime($key->timecreated),
            ];
        }
        $keys->close();

        if (!empty($keydata)) {
            $data = (object) [
                'keys' => $keydata,
            ];

            writer::with_context($context)->export_related_data($subcontext, 'userkeys', $data);
        }
    }

    /**
     * Deletes all userkeys for a script.
     *
     * @param  string           $script The owner of the data (usually a component name).
     * @param  int              $userid The owner of the data.
     * @param  int              $instance The instance owner of the data.
     */
    public static function delete_userkeys($script, $userid = null, $instance = null) {
        global $DB;

        $searchparams = [
            'script' => $script,
        ];

        if (null !== $userid) {
            $searchparams['userid'] = $userid;
        }

        if (null !== $instance) {
            $searchparams['instance'] = $instance;
        }

        $DB->delete_records('user_private_key', $searchparams);
    }
}
