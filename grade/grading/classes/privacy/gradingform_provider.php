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
 * This file contains the grading method interface.
 *
 * Grading method plugins should implement this if they store personal information.
 *
 * @package    core_grading
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_grading\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\approved_contextlist;

interface gradingform_provider extends
    \core_privacy\local\request\plugin\subsystem_provider {

    /**
     * This method is used to export any user data this sub-plugin has using the object to get the context and userid.
     *
     * @param \context $context Context owner of the data.
     * @param \stdClass $definition Grading definition entry to export.
     * @param  int $userid The user whose information is to be exported.
     *
     * @return \stdClass The data to export.
     */
    public static function get_gradingform_export_data(\context $context, $definition, int $userid);

    /**
     * Any call to this method should delete all user data for the context defined.
     *
     * @param \context $context Context owner of the data.
     */
    public static function delete_gradingform_for_context(\context $context);

    /**
     * A call to this method should delete user data (where practicle) from the userid and context.
     *
     * @param int $userid The user to delete.
     * @param \context $context the context to refine the deletion.
     */
    public static function delete_gradingform_for_userid(int $userid, \context $context);
}
