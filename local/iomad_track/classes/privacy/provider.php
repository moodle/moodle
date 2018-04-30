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
 * Privacy Subsystem implementation for local_iomad_track.
 *
 * @package    local_iomad_track
 * @copyright  2018 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad_track\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for local_iomad_track implementing null_provider.
 *
 * @copyright  2018 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // This plugin stores personal data.
        \core_privacy\local\metadata\provider,

        // This plugin is a core_user_data_provider.
        \core_privacy\local\request\plugin\provider {
    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) {
        $items->add_database_table(
            'local_iomad_track',
            [
                'id' => 'privacy:metadata:choice_answers:id',
                'courseid' => 'privacy:metadata:choice_answers:courseid',
                'userid' => 'privacy:metadata:choice_answers:userid',
                'timecompleted' => 'privacy:metadata:choice_answers:timecompleted',
                'timeenrolled' => 'privacy:metadata:choice_answers:timeenrolled',
                'timestarted' => 'privacy:metadata:choice_answers:timestarted',
                'finalscore' => 'privacy:metadata:choice_answers:finalscore',
            ],
            'privacy:metadata:local_iomad_track'
        );

        $items->add_database_table(
            'local_iomad_track_certs',
            [
                'id' => 'privacy:metadata:choice_answers:id',
                'trackid' => 'privacy:metadata:choice_answers:trackid',
                'filename' => 'privacy:metadata:choice_answers:filename',
            ],
            'privacy:metadata:local_iomad_track_certs'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid($userid) {
        $sql = "SELECT c.id
                  FROM {context} c
                WHERE contextlevel = :contextlevel";

        $params = [
            'userid'  => $userid,
            'contextlevel'  => CONTEXT_SYSTEM,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        $context = context_system::instance();

        if ($tracks = $DB->get_records('local_iomad_track', array('userid' => $user->id))) {
            foreach ($tracks as $ctrack) {
                writer::with_context($context)->export_data($context, $track);
                if ($certinfo = $DB->get_record('local_iomad_track_certs', array('trackid' => $track->id))) {
                    // Export the track info
                    writer::with_context($context)->export_data($context, $certinfo);
                    
                }
            }
        }

    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB, $CFG;

        if (empty($context)) {
            return;
        }

        $DB->delete_records('local_iomad_track');

        // Get the certs.
        if ($certs = $DB->get_records('local_iomad_track_certs')) {
            // Delete the files.
            require_once($CFG->libdir . '/filelib.php');
            foreach ($certs as $cert) {
                if ($file = $DB->get_record('files', array('component' => 'local_iomad_track', 'itemid' => $cert->trackid, 'filename' => $cert->filename))) {
                    $filedir1 = substr($file->contenthash,0,2);
                    $filedir2 = substr($file->contenthash,2,2);
                    $filepath = $CFG->dataroot . '/filedir/' . $filedir1 . '/' . $filedir2 . '/' . $file->contenthash;
                    fulldelete($filepath);
                }
            }

            $DB->delete_records('local_iomad_track_certs');
            $DB->delete_records('files', array('component' => 'local_iomad_track'));
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        $DB->delete_records('local_iomad_track');

        // Get the certs.
        if ($certs = $DB->get_records('local_iomad_track_certs', array('userid' => $userid))) {
            // Delete the files.
            require_once($CFG->libdir . '/filelib.php');
            foreach ($certs as $cert) {
                if ($file = $DB->get_record('files', array('component' => 'local_iomad_track', 'itemid' => $cert->trackid, 'filename' => $cert->filename))) {
                    $filedir1 = substr($file->contenthash,0,2);
                    $filedir2 = substr($file->contenthash,2,2);
                    $filepath = $CFG->dataroot . '/filedir/' . $filedir1 . '/' . $filedir2 . '/' . $file->contenthash;
                    fulldelete($filepath);
                }

                $DB->delete_records('local_iomad_track_certs', array('id' => $cert->id));
            }
            $DB->delete_records('files', array('component' => 'local_iomad_track', 'userid' => $userid));
        }
    }
}
