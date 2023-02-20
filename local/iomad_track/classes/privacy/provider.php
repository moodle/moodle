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
 * @copyright  2021 Derick Turner
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad_track\privacy;

use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\request\helper;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\writer;
use \context_system;
use \context_user;

defined('MOODLE_INTERNAL') || die();

class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'local_iomad_track',
            [
                'id' => 'privacy:metadata:local_iomad_track:id',
                'courseid' => 'privacy:metadata:local_iomad_track:courseid',
                'coursename' => 'privacy:metadata:local_iomad_track:coursename',
                'userid' => 'privacy:metadata:local_iomad_track:userid',
                'timecompleted' => 'privacy:metadata:local_iomad_track:timecompleted',
                'timeenrolled' => 'privacy:metadata:local_iomad_track:timeenrolled',
                'timestarted' => 'privacy:metadata:local_iomad_track:timestarted',
                'finalscore' => 'privacy:metadata:local_iomad_track:finalscore',
                'companyid' => 'privacy:metadata:local_iomad_track:companyid',
                'licenseid' => 'privacy:metadata:local_iomad_track:licenseid',
                'licensename' => 'privacy:metadata:local_iomad_track:licensename',
                'licenseallocated' => 'privacy:metadata:local_iomad_track:licenseallocated',
            ],
            'privacy:metadata:local_iomad_track'
        );

        $collection->add_database_table(
            'local_iomad_track_certs',
            [
                'id' => 'privacy:metadata:local_iomad_track_certs:id',
                'trackid' => 'privacy:metadata:local_iomad_track_certs:trackid',
                'filename' => 'privacy:metadata:local_iomad_track_certs:filename',
            ],
            'privacy:metadata:local_iomad_track_certs'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
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

        $user = $contextlist->get_user();

        $context = context_system::instance();

        if ($tracks = $DB->get_records('local_iomad_track', array('userid' => $user->id))) {
            $trackout = (object) [];
            $trackout->tracks = [];
            $trackout->certs = [];
            foreach ($tracks as $track) {
                if (!empty($track->timeenrolled)) {
                    $track->timeenrolled = transform::datetime($track->timeenrolled);
                }
                if (!empty($track->timestarted)) {
                    $track->timestarted = transform::datetime($track->timestarted);
                }
                if (!empty($track->timecompleted)) {
                    $track->timecompleted = transform::datetime($track->timecompleted);
                }
                if (!empty($track->timeexpires)) {
                    $track->timeexpires = transform::datetime($track->timeexpires);
                }
                if (!empty($track->licenseallocated)) {
                    $track->licenseallocated = transform::datetime($track->licenseallocated);
                }
                if (!empty($track->modifiedtime)) {
                    $track->modifiedtime = transform::datetime($track->modifiedtime);
                }
                $trackout->tracks[$track->id] = $track;
                if ($certinfos = $DB->get_records('local_iomad_track_certs', array('trackid' => $track->id))) {
                    foreach ($certinfos as $certinfo) {
                        // Export the track info
                        $trackout->certs[$cert->id] = $certinfo;
                        //writer::with_context($context)->export_data([], $certinfo);
                    }
                }
            }
            writer::with_context($context)->export_data([get_string('pluginname', 'local_iomad_track')], $trackout);
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
        $DB->delete_records('local_iomad_track', array('userid' => $userid));

        // Get the certs.
        if ($certs = $DB->get_records_sql("SELECT litc* FROM {local_iomad_track_certs}
                                           JOIN {local_iomad_track} lit ON (litc.trackid = lit.id)
                                           WHERE lit.userid = :userid",
                                           array('userid' => $userid))) {
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

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof context_user) {
            return;
        }

        $params = [
            'userid' => $context->id,
            'contextuser' => CONTEXT_USER,
        ];

        $sql = "SELECT lit.userid as userid
                  FROM {local_iomad_track} lit
                  JOIN {context} ctx
                       ON ctx.instanceid = lit.userid
                       AND ctx.contextlevel = :contextuser
                 WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context instanceof context_user) {
            $DB->delete_records('local_iomad_track', array('userid' => $context->id));

            // Get the certs.
            if ($certs = $DB->get_records('local_iomad_track_certs', array('userid' => $context->id))) {
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
                $DB->delete_records('files', array('component' => 'local_iomad_track', 'userid' => $context->id));
            }
        }
    }
}
