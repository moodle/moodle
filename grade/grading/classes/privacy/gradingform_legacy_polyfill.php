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
 * This file contains the polyfill to allow a plugin to operate with Moodle 3.3 up.
 *
 * @package    core_grading
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_grading\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * The trait used to provide backwards compatability for third-party plugins.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait gradingform_legacy_polyfill {

    /**
     * Export user data relating to an instance ID.
     *
     * @param  \context $context Context to use with the export writer.
     * @param  int $instanceid The instance ID to export data for.
     * @param  array $subcontext The directory to export this data to.
     */
    public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext) {
        static::_export_gradingform_instance_data($context, $instanceid, $subcontext);
    }

    /**
     * Deletes all user data related to the provided instance IDs.
     *
     * @param  array  $instanceids The instance IDs to delete information from.
     */
    public static function delete_gradingform_for_instances(array $instanceids) {
        static::_delete_gradingform_for_instances($instanceids);
    }

    /**
     * This method is used to export any user data this sub-plugin has using the object to get the context and userid.
     *
     * @deprecated Since Moodle 3.6 MDL-62535 Please use the methods in the gradingform_provider_v2 interface.
     * @todo MDL-63167 remove this method.
     *
     * @param context $context Context owner of the data.
     * @param stdClass $definition Grading definition entry to export.
     * @param int $userid The user whose information is to be exported.
     *
     * @return stdClass The data to export.
     */
    public static function get_gradingform_export_data(\context $context, $definition, int $userid) {
        debugging('This method is deprecated. Please use the gradingform_provider_v2 interface', DEBUG_DEVELOPER);
        return static::_get_gradingform_export_data($context, $definition, $userid);
    }

    /**
     * Any call to this method should delete all user data for the context defined.
     *
     * @deprecated Since Moodle 3.6 MDL-62535 Please use the methods in the gradingform_provider_v2 interface.
     * @todo MDL-63167 remove this method.
     *
     * @param context $context Context owner of the data.
     */
    public static function delete_gradingform_for_context(\context $context) {
        debugging('This method is deprecated. Please use the gradingform_provider_v2 interface', DEBUG_DEVELOPER);
        static::_delete_gradingform_for_context($context);
    }

    /**
     * A call to this method should delete user data (where practicle) from the userid and context.
     *
     * @deprecated Since Moodle 3.6 MDL-62535 Please use the methods in the gradingform_provider_v2 interface.
     * @todo MDL-63167 remove this method.
     *
     * @param int $userid The user whose information is to be deleted.
     * @param context $context Context owner of the data.
     */
    public static function delete_gradingform_for_userid(int $userid, \context $context) {
        debugging('This method is deprecated. Please use the gradingform_provider_v2 interface', DEBUG_DEVELOPER);
        static::_delete_gradingform_for_userid($userid, $context);
    }
}
