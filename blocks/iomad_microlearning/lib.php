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

require_once(dirname(__FILE__) . '/../../config.php');

class microlearning {

    public function check_valid_thread($companyid, $threadid) {
        global $DB;
        if ($DB->get_record('microlearning_thread', array('id' => $threadid, 'companyid' => $companyid))) {
            return true;
        } else {
            return false;
        }
    }

    public static function delete_thread($threadid) {
        global $DB;

        // start transaction.
        $transaction = $DB->start_delegated_transaction();
        $errors = false;

        // Delete users.
        if (!$DB->delete_records('microlearning_thread_user', array('threadid' => $threadid))) {
            $errors = true;
        }

        // Delete nuggets
        $nuggets = $DB->get_records('microlearning_nugget', array('threadid' => $threadid));
        foreach ($nuggets as $nugget) {

            // Delete nugget schedules
            if (!$DB->delete_records('microlearning_nugget_sched', array('nuggetid' => $nugget->id))) {
                $errors = true;
            }
        }

        // Finally delete the nugget.
        if (!$DB->delete_records('microlearning_nugget', array('threadid' => $threadid))) {
            $errors = true;
        }

        // Delete thread
        if (!$DB->delete_records('microlearning_thread', array('id' => $threadid))) {
            $errors = true;
        }

        // End transaction.
        if (!$errors) {
            $transaction->allow_commit();
        } else {
            $transaction->rollback();
        }

        // Fire an Event for this.
        $eventother = array('companyid' => $companyid);

        $event = \block_iomad_microlearning\event\thread_deleted::create(array('context' => context_system::instance(),
                                                                               'userid' => $USER->id,
                                                                               'objectid' => $threadid,
                                                                               'other' => $eventother));
        $event->trigger();
    }

    public static function delete_nugget($nuggetid) {
        global $DB;

        // Does the nugget exist.
        if (!$nugget = $DB->get_record('microlearning_nugget', array('id' => $nuggetid))) {
            return false;
        }

        // Start a transaction.
        $errors = false;
        $transaction = $DB->start_delegated_transaction();

        // Get any nuggets after this one.
        if ($afters = $DB->get_records_sql("SELECT * FROM {microlearning_nugget}
                                            WHERE threadid = :threadid
                                            AND order > :current",
                                            array('threadid' => $nugget->threadid,
                                                  'current' => $nugget->order))) {
            // Move them up.
            foreach ($afters as $after) {
                $after->order--;
                if ($after->order < 0) {
                    $after->order = 0;
                }
                $DB->update_record('microlearning_nugget', $after);
            }
        }

        // Delete the nugget.
        if (!$DB->delete_records('microlearning_nugget', array('id' => $nugget->id))) {
             $transaction->rollback();
        } else {
            $transaction->allow_commit();
        }

        // Fire an Event for this.
        $eventother = array('threadid' => $nugget->threadid);

        $event = \block_iomad_microlearning\event\nugget_deleted::create(array('context' => context_system::instance(),
                                                                               'userid' => $USER->id,
                                                                               'objectid' => $nuggetid,
                                                                               'other' => $eventother));
        $event->trigger();

    }

    public static function up_nugget($nuggetid) {
        global $DB;

        // Does the nugget exist.
        if (!$nugget = $DB->get_record('microlearning_nugget', array('id' => $nuggetid))) {
            return false;
        }

        // is it already the first one?
        if ($nugget->order == 0) {
            return true;
        }

        // Get any nuggets after this one.
        if ($above = $DB->get_record_sql("SELECT * FROM {microlearning_nugget}
                                            WHERE threadid = :threadid
                                            AND order = :above",
                                            array('threadid' => $nugget->threadid,
                                                  'above' => $nugget->order - 1))) {
            $above->order++;
            $DB->update_record('microlearning_nugget', $above);
            $nugget->order--;
            $DB->update_record('microlearning_nugget', $nugget);
        }

        // Fire an Event for this.
        $eventother = array('threadid' => $nugget->threadid);

        $event = \block_iomad_microlearning\event\nugget_moved::create(array('context' => context_system::instance(),
                                                                             'userid' => $USER->id,
                                                                             'objectid' => $nuggetid,
                                                                             'other' => $eventother));
        $event->trigger();

    }

    public static function down_nugget($nuggetid) {
        global $DB;

        // Does the nugget exist.
        if (!$nugget = $DB->get_record('microlearning_nugget', array('id' => $nuggetid))) {
            return false;
        }

        // is it already the first one?
        if ($nugget->order == 0) {
            return true;
        }

        // Get any nuggets after this one.
        if ($below = $DB->get_record_sql("SELECT * FROM {microlearning_nugget}
                                            WHERE threadid = :threadid
                                            AND order = :above",
                                            array('threadid' => $nugget->threadid,
                                                  'above' => $nugget->order + 1))) {
            $below->order--;
            $DB->update_record('microlearning_nugget', $below);
            $nugget->order++;
            $DB->update_record('microlearning_nugget', $nugget);
        }

        // Fire an Event for this.
        $eventother = array('threadid' => $nugget->threadid);

        $event = \block_iomad_microlearning\event\nugget_moved::create(array('context' => context_system::instance(),
                                                                             'userid' => $USER->id,
                                                                             'objectid' => $nuggetid,
                                                                             'other' => $eventother));
        $event->trigger();

    }

    public static function get_schedules($threadinfo, $nuggets) {
        global $DB, $CFG;

        $returndata = new std_class();
        $returndata->threadid = $threadinfo->id;
        $startdate = $threadinfo->startdate;
        $schedulearray = array();
        $duedatearray = array();
        $reminder1array = array();
        $reminder2array = array();

        foreach ($nuggets as $nugget) {
            // Check if we already have a schedule.
            if ($schedule = $DB->get_record('microlearning_nugget_sched', array('nuggetid' => $nugget->id))) {
                $startdate = $schedule->due_date;
                $schedulearray[$nugget->id] = $schedule->schedule_date;
                $duedatearray[$nugget->id] = $schedule->due_date;
                $reminder1array[$nugget->id] = $schedule->reminder1_date;
                $reminder2array[$nugget->id] = $schedule->reminder2_date;
            } else {
                $schedulearray[$nugget->id] = $startdate;
                $duedatearray[$nugget->id] = strtotime(" + " .$CFG->microlearningdefaultdue . " days", $startdate);
                $reminder1array[$nugget->id] = strtotime(" + " .$CFG->microlearningdefaultreminder1 . " days", $startdate);
                $reminder2array[$nugget->id] = strtotime(" + " .$CFG->microlearningdefaultreminder2 . " days", $startdate);
                $startdate = strtotime(" + " . $CFG->microlearningdefaultpulse . " days", $startdate);
            }
        }
        $returndata->schedulearray = $schedulearray;
        $returndata->duedatearray = $duedatearray;
        $returndata->reminder1array = $reminder1array;
        $returndata->reminder2array = $reminder2array;

        return $returndaata;
    }

    public static function reset_thread_schedule($threadinfo) {
        global $DB;

        // Delete the current schedules for any nuggets.
        if ($nuggets = $DB->get_records('microlearning_nugget', array('threadid' => $threadinfo->id))) {
            foreach ($nuggets as $nugget) {
                $DB->delete_records('microlearning_nugget_sched', array('nuggetid' => $nugget->id));
            }

            // Get the new schedule info.
            $scheduledata = self::get_schedules($threadinfo, $nuggets);
            self::update_thread_schedule($scheduledata);
        }
    }

    public static function update_thread_schedule($scheduledata) {
        global $DB;

        // process the scheduledata.
        foreach (array_keys($scheduledata->schedulearray) as $nuggetid) {
            // Does it exist already?
            if ($DB->record_exists('microlearning_nugget_sched', array('nuggetid' => $nuggetid))) {
                // Update the stored nugget schedules.
                $DB->set_field('microlearning_nugget_sched', 'scheduledate', $scheduledata->schedulearray[$nuggetid], array('nuggetid' => $nuggetid));
                $DB->set_field('microlearning_nugget_sched', 'due_date', $scheduledata->duedatearray[$nuggetid], array('nuggetid' => $nuggetid));
                $DB->set_field('microlearning_nugget_sched', 'reminder1_date', $scheduledata->reminder1datearray[$nuggetid], array('nuggetid' => $nuggetid));
                $DB->set_field('microlearning_nugget_sched', 'reminder2_date', $scheduledata->reminder2datearray[$nuggetid], array('nuggetid' => $nuggetid));
            } else {
                $DB->insert_record('microlearning_nugget_sched', array('scheduledate' => $scheduledata->schedulearray[$nuggetid],
                                                                       'due_date', $scheduledata->duedatearray[$nuggetid],
                                                                       'reminder1_date', $scheduledata->reminder1datearray[$nuggetid],
                                                                       'reminder2_date', $scheduledata->reminder2datearray[$nuggetid]));
            }

            // Update the user nugget schedules.
            $DB->set_field('microlearning_thread_user', 'schedule_date', $scheduledata->schedulearray[$nuggetid], array('threadid' => $scheduledata->threadid, 'nuggetid' => $nuggetid));
            $DB->set_field('microlearning_thread_user', 'due_date', $scheduledata->duedatearray[$nuggetid], array('threadid' => $scheduledata->threadid, 'nuggetid' => $nuggetid));
            $DB->set_field('microlearning_thread_user', 'reminder1_date', $scheduledata->reminder1datearray[$nuggetid], array('threadid' => $scheduledata->threadid, 'nuggetid' => $nuggetid));
            $DB->set_field('microlearning_thread_user', 'reminder2_date', $scheduledata->reminder2datearray[$nuggetid], array('threadid' => $scheduledata->threadid, 'nuggetid' => $nuggetid));
        }

        // Fire an event for this.
        $eventother = array('threadid' => $scheduledata->threadid);

        $event = \block_iomad_microlearning\event\thread_schedule_updated::create(array('context' => context_system::instance(),
                                                                                        'userid' => $USER->id,
                                                                                        'objectid' => $scheduledata->threadid,
                                                                                        'other' => $eventother));
        $event->trigger();

    }

    public static function add_user_to_thread($threadid, $userid) {
        global $DB;

        // check the user is valid.
        if (!$user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0))) {
            return false;
        }

        // Check the thread is valid.
        if (!$threadinfo = $DB->get_record('microlearning_thread', array('id' => $threadid))) {
            return false;
        }

        // start transaction.
        $transaction = $DB->start_delegated_transaction();
        $errors = false;

        // Get the thread nuggets.
        $nuggets = $DB->get_records('microlearning_nugget', array('threadid' => $threadid));
        $scheduleinfo = self::get_schedules($threadinfo, $nuggets);

        // insert the user schedule info.
        foreach ($nuggets as $nugget) {
            $schedulerec = new stdclass();
            $schedulerec->userid = $userid;
            $schedulerec->threadid = $threadid;
            $schedulerec->nuggetid = $nugget->id;
            $schedulerec->schedule_date = $scheduleinfo->schedulearray[$nugget->id];
            $schedulerec->due_date = $scheduleinfo->duedatearray[$nugget->id];
            $schedulerec->message_time = $threadinfo->message_time;
            $schedulerec->message_delivered = false;
            $schedulerec->reminder1_delivered = false;
            $schedulerec->reminder2_delivered = false;
            $schedulerec->timecreated = time();
            if (!empty($nugget->cmid)) {
                if ($modcompletion = $DB->get_record_sql("SELECT * FROM {course_modules_completion}
                                                          WHERE userid = :userid
                                                          AND coursemoduleid = :cmid
                                                          AND completionstate > 0",
                                                          array('userid' => $userid,
                                                                'cmid' => $nugget->cmid))) {
                    $schedulerec->timecompleted = $modcompletion->timemodified;
                }
            } else if (!empty($nugget->sectionid)) {
                // Get all of the course modules in that section which have completion set up.
                $requiredcount = $DB->count_records_sql("SELECT COUNT(id) FROM {course_modules}
                                                         WHERE section = :section
                                                         AND completion > 0",
                                                         array('section' => $nugget->sectionid));

                // Get all of the course modules in that section which have completion set up.
                $actualcount = $DB->get_records_sql("SELECT * FROM {course_modules_completion}
                                                     WHERE userid = :userid
                                                     AND completionstate > 0
                                                     AND coursemoduleid IN
                                                      (SELECT id FROM {course_modules
                                                       WHERE section = :section)
                                                     ORDER BY timemodified DESC",
                                                      array('userid' => $userid,
                                                            'section' => $nugget->sectionid));

                if ($requiredcount >= count($actualcount)) {
                    // Get the maximum time modified.
                    $last = array_shift($actualcount);
                    $schedulerec->timecompleted = $last->timemodified;
                }
            } else {
                $schedulerec->timecompleted = null;
            }
            $schedulerec->accesskey = self::generate_accesskey();
            if (!$DB->insert_recoord('microlearning_thread_user', $schedulerec)) {
                $errors = true;
            }
        }

        if ($errors) {
            $transaction->rollback();
            return false;
        } else {
            $transaction->allow_commit();
            return true;
        }
    }

    public static function remove_user_from_thread($threadid, $userid) {
        global $DB;

        // check the user is valid.
        if (!$user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0))) {
            return false;
        }

        // Check the thread is valid.
        if (!$threadinfo = $DB->get_record('microlearning_thread', array('id' => $threadid))) {
            return false;
        }

        // start transaction.
        $transaction = $DB->start_delegated_transaction();
        $errors = false;

        if (!$DB->delete_records('microlearning_thread_user', array('userid' => $userid, 'threadid' => $threadid))) {
            $transaction->rollback();
            return false;
        } else {
            $transaction->allow_commit();
            return true;
        }
    }

    public static function generate_accesskey() {
        return bin2hex(random_bytes(64));
    }

    public static function get_nugget_url($nugget) {
        global $DB;

        // Get the nugget url.
        if (!empty($nugget->section)) {
            $sectioninfo = $DB->get_record('course_sections', array('id' => $nuugget->sectionid));
            $linkurl = course_get_url($sectioninfo->course, $sectioninfo->section);
        } else if (!empty($nugget->cmid)) {
            $moduleinfo - $DB->get_record('course_modules', array('id' => $nugget->cmid));
            $course = $DB->get_record('course', array('id' => $moduleinfo->course));
            $modinfo = get_fast_modinfo($course);
            $cm = $modinfo->cms[$nugget->cmid];
            $linkurl = $cm->url;
        }

        return $linkurl;
    }
    /* Event handlers */

    public static function event_thread_created(\block_iomad_microlearning\event\thread_created $event) {
        global $DB;
    }

    public static function event_thread_deleted(\block_iomad_microlearning\event\thread_deleted $event) {
        global $DB;
    }

    public static function event_thread_updated(\block_iomad_microlearning\event\thread_updated $event) {
        global $DB;
    }

    public static function event_thread_schedule_updated(\block_iomad_microlearning\event\thread_schedule_updated $event) {
        global $DB;
    }

    public static function event_nugget_created(\block_iomad_microlearning\event\nugget_created $event) {
        global $DB;
    }

    public static function event_nugget_deleted(\block_iomad_microlearning\event\nugget_deleted $event) {
        global $DB;
    }

    public static function event_nugget_updated(\block_iomad_microlearning\event\nugget_updated $event) {
        global $DB;
    }

    public static function event_nugget_moved(\block_iomad_microlearning\event\nugget_moved $event) {
        global $DB;
    }

    public static function user_deleted(\core\event\user_deleted $event) {
        global $DB;

        // Delete all of the schedules for this user.
        $DB->delete_records('microlearning_thread_user', array('userid' => $event->objectid));
    }

    public static function course_module_completion_updated(\core\event\course_module_completion_updated $event) {
        global $DB;
        $cmid = $event->contextinstanceid;
        if ($nuggets = $DB->get_records_sql("SELECT mtu.* FROM {microlearning_thread_user} mtu
                                             JOIN {microlearning_nugget} mn ON (mtu.nuggetid = mn.id)
                                             WHERE mtu.userid = :userid
                                             AND mn.cmid = :cmid", array('userid' => $event->relateduserid, 'cmid' => $cmid))) {
            foreach ($nuggets as $nugget) {
                $DB->set_field('microlearning_thread_user', 'timecompleted', $event->timecreated, array('id' => $nugget->id));
            }
        }

        // check if there is a section set instead.
        $cmidrec = $DB->get_record('course_modules', array('id' => $event->contextinstance));
        if ($nuggets = $DB->get_records_sql("SELECT mtu.* FROM {microlearning_thread_user} mtu
                                         JOIN {microlearning_nugget} mn ON (mtu.nuggetid = mn.id)
                                         WHERE mtu.userid = :userid
                                         AND mn.sectionid = :sectionid", array('userid' => $event->relateduserid, 'sectionid' => $cmidrec->section))) {

            // Get all of the course modules in that section which have completion set up.
            $requiredcount = $DB->count_records_sql("SELECT COUNT(id) FROM {course_modules}
                                                     WHERE section = :section
                                                     AND completion > 0",
                                                     array('section' => $cmid->section));

            // Get all of the course modules in that section which have completion set up.
            $actualcount = $DB->count_records_sql("SELECT COUNT(id) FROM {course_modules_completion}
                                                     WHERE userid = :userid
                                                     AND completionstate > 0
                                                     AND coursemoduleid IN
                                                      (SELECT id FROM {course_modules
                                                       WHERE section = :section)",
                                                     array('userid' => $event->relateduserid,
                                                           'section' => $cmid->section));

            if ($requiredcount == $actualcount) {
                foreach ($nuggets as $nugget) {
                    $DB->set_field('microlearning_thread_user', 'timecompleted', $event->timecreated, array('id' => $nugget->id));
                }
            }
        }
    }

    public static function cron() {
        global $DB;

        // Get the current timestamp.
        $runtime = time();

        mtrace("starting block_microlearning cron at $runtime");

        // Get users who need to be sent a new link email.
        mtrace("getting list of users who have a new nugget");
        if ($scheduleusers = $DB->get_records_sql("SELECT mtu.* FROM {microlearning_thread_user} mtu
                                                   JOIN {microlearning_thread} mt
                                                   ON (mtu.threadid = mt.id AND mt.send_message = 1)
                                                   AND mtu.message_delivered = 0
                                                   WHERE mtu.timecompleted IS NULL
                                                   AND mtu.schedule_date + mtu.message_time < :runtime",
                                                   array('runtime' => $runtime))) {
            foreach ($scheduleusers as $scheduleuser) {
                $scheduleuser->message_delivered = 1;

                if ($user = $DB->get_record('user', array('id' => $scheduleuser->userid, 'suspended' => 0, 'deleted' => 0))) {
                    // Get the email payload.
                    if ($nugget = $DB->get_record('microlearning_nugget', array('id' => $scheduleuser->nuggetid))) {
                        $company = company::by_userid($user->id);
                        // Fire the email.
                        EmailTemplate::send('microlearning_nugget_scheduled', array('user' => $user, 'company' => $company, 'nugget' => $nugget));
                        $DB->set_field('microlearning_thread_user', 'message_delivered', true, array('id' => $scheduleuser->id));
                    }
                }
                $DB->update_record('microlearning_thread_user', $scheduleuser);
            }
        }
        unset($scheduleusers);

        // Get users who need to be sent a reminder email
        mtrace("getting list of users for first reminder");
        if ($reminder1users = $DB->get_records_sql("SSELECT mtu.* FROM {microlearning_thread_user} mtu
                                                   JOIN {microlearning_thread} mt
                                                   ON (mtu.threadid = mt.id AND mt.send_message = 1)
                                                   WHERE mtu.timecompleted IS NULL
                                                   AND mtu.reminder1_delivered = 0
                                                   AND mtu.reminder1_date IS NOT NULL
                                                   AND mtu.reminder1_date + mtu.message_time < :runtime",
                                                   array('runtime' => $runtime))) {
            foreach ($reminder1users as $reminder1user) {
                $reminder1user->reminder1_delivered = 1;

                if ($user = $DB->get_record('user', array('id' => $reminder1user->userid, 'suspended' => 0, 'deleted' => 0))) {
                    // Get the email payload.
                    if ($nugget = $DB->get_record('microlearning_nugget', array('id' => $reminder1user->nuggetid))) {
                        $company = company::by_userid($user->id);
                        // Fix the payload.
                        $nugget->name = format_text($nugget->name);
                        $nugget->url = 
                        // Fire the email.
                        EmailTemplate::send('microlearning_nugget_reminder1', array('user' => $user, 'company' => $company, 'nugget' => $nugget));
                        $DB->set_field('microlearning_thread_user', 'reminder1_delivered', true, array('id' => $scheduleuser->id));
                    }
                }
                $DB->update_record('microlearning_thread_user', $reminder1user);
            }
        }
        unset($reminder1users);

        // Get users who need to be sent a second reminder email.
        mtrace("getting list of users for second reminder");
        if ($reminder2users = $DB->get_records_sql("SELECT mtu.* FROM {microlearning_thread_user} mtu
                                                   JOIN {microlearning_thread} mt
                                                   ON (mtu.threadid = mt.id AND mt.send_message = 1)
                                                   WHERE mtu.timecompleted IS NULL
                                                   AND mtu.reminder2_delivered = 0
                                                   AND mtu.reminder2_date IS NOT NULL
                                                   AND mtu.reminder2_date + mtu.message_time < :runtime",
                                                   array('runtime' => $runtime))) {
            foreach ($reminder2users as $reminder2user) {
                $reminder2user->reminder2_delivered = 1;

                if ($user = $DB->get_record('user', array('id' => $reminder2user->userid, 'suspended' => 0, 'deleted' => 0))) {
                    // Get the email payload.
                    if ($nugget = $DB->get_record('microlearning_nugget', array('id' => $reminder2user->nuggetid))) {
                        $company = company::by_userid($user->id);
                        // Fire the email.
                        EmailTemplate::send('microlearning_nugget_reminder2', array('user' => $user, 'company' => $company, 'nugget' => $nugget));
                        $DB->set_field('microlearning_thread_user', 'reminder1_delivered', true, array('id' => $scheduleuser->id));
                    }
                }
                $DB->update_record('microlearning_thread_user', $reminder2user);
            }
        }
        unset($reminder2users);
        mtrace("microlearning cron finished - " . time());
    }
}