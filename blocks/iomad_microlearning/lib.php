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

    public static function check_valid_thread($companyid, $threadid) {
        global $DB, $USER;
        if ($DB->get_record('microlearning_thread', array('id' => $threadid, 'companyid' => $companyid))) {
            return true;
        } else {
            return false;
        }
    }

    public static function delete_thread($threadid) {
        global $DB, $USER;

        if (!$threadrec = $DB->get_record('microlearning_thread', array('id' => $threadid))) {
            return false;
        }

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

            // Fire an Event for this.
            $eventother = array('companyid' => $threadrec->companyid);

            $event = \block_iomad_microlearning\event\thread_deleted::create(array('context' => context_system::instance(),
                                                                                   'userid' => $USER->id,
                                                                                   'objectid' => $threadid,
                                                                                   'other' => $eventother));
            $event->trigger();

            return true;
        } else {
            try {
                throw new Exception('Could not delete thread');
            } catch (\Exception $e) {
                $transaction->rollback($e);
            }

            return false;
        }
    }

    public static function clone_thread($threadid) {
        global $DB, $USER;

        if (!$threadrec = $DB->get_record('microlearning_thread', array('id' => $threadid))) {
            return false;
        }

        // start transaction.
        $transaction = $DB->start_delegated_transaction();
        $errors = false;

        // create thread copy.
        $originalthreadid = $threadrec->id;
        unset($threadrec->id);
        $threadrec->name = $threadrec->name . get_string('copy', 'block_iomad_microlearning');
        if (!$threadrec->id = $DB->insert_record('microlearning_thread', $threadrec)) {
            $errors = true;
        }

        // Clone nuggets
        $nuggets = $DB->get_records('microlearning_nugget', array('threadid' => $originalthreadid));
        foreach ($nuggets as $nugget) {

            // Copy the nugget.
            $originalnuggetid = $nugget->id;
            unset($nugget->id);
            $nugget->threadid = $threadrec->id;
            if (!$nugget->id = $DB->insert_record('microlearning_nugget', $nugget)) {
                $errors = true;
            }

            // Deal with the schedules
            if ($nuggetschedule = $DB->get_record('microlearning_nugget_sched', array('nuggetid' => $originalnuggetid))) {
                // Copy nugget schedules
                $nuggetschedule->nuggetid = $nugget->id;
                if (!$DB->insert_record('microlearning_nugget_sched', $nuggetschedule)) {
                    $errors = true;
                }
            }
        }

        // End transaction.
        if (!$errors) {
            $transaction->allow_commit();

            // Fire an Event for this.
            $eventother = array('companyid' => $threadrec->companyid);

            $event = \block_iomad_microlearning\event\thread_created::create(array('context' => context_system::instance(),
                                                                                   'userid' => $USER->id,
                                                                                   'objectid' => $threadrec->id,
                                                                                   'other' => $eventother));
            $event->trigger();
            return true;
        } else {
            try {
                throw new Exception('Could not clone thread');
            } catch (\Exception $e) {
                $transaction->rollback($e);
            }
        }
    }

    public static function import_thread($threadid, $companyid) {
        global $DB, $USER;

        if (!$threadrec = $DB->get_record('microlearning_thread', array('id' => $threadid))) {
            return false;
        }

        // start transaction.
        $transaction = $DB->start_delegated_transaction();
        $errors = false;

        // create thread copy.
        $originalthreadid = $threadrec->id;
        unset($threadrec->id);
        $threadrec->companyid = $companyid;
        $threadrec->name = $threadrec->name . get_string('copy', 'block_iomad_microlearning');
        if (!$threadrec->id = $DB->insert_record('microlearning_thread', $threadrec)) {
            $errors = true;
        }

        // Clone nuggets
        $nuggets = $DB->get_records('microlearning_nugget', array('threadid' => $originalthreadid));
        foreach ($nuggets as $nugget) {

            // Copy the nugget.
            $originalnuggetid = $nugget->id;
            unset($nugget->id);
            $nugget->threadid = $threadrec->id;
            if (!$nugget->id = $DB->insert_record('microlearning_nugget', $nugget)) {
                $errors = true;
            }

            // Deal with the schedules
            if ($nuggetschedule = $DB->get_record('microlearning_nugget_sched', array('nuggetid' => $originalnuggetid))) {
                // Copy nugget schedules
                $nuggetschedule->nuggetid = $nugget->id;
                if (!$DB->insert_record('microlearning_nugget_sched', $nuggetschedule)) {
                    $errors = true;
                }
            }
        }

        // End transaction.
        if (!$errors) {
            $transaction->allow_commit();

            // Fire an Event for this.
            $eventother = array('companyid' => $threadrec->companyid);

            $event = \block_iomad_microlearning\event\thread_created::create(array('context' => context_system::instance(),
                                                                                   'userid' => $USER->id,
                                                                                   'objectid' => $threadrec->id,
                                                                                   'other' => $eventother));
            $event->trigger();
            return true;
        } else {
            try {
                throw new Exception('Could not clone thread');
            } catch (\Exception $e) {
                $transaction->rollback($e);
            }
        }
    }

    public static function delete_nugget($nuggetid) {
        global $DB, $USER;

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
                                            AND nuggetorder > :current",
                                            array('threadid' => $nugget->threadid,
                                                  'current' => $nugget->nuggetorder))) {
            // Move them up.
            foreach ($afters as $after) {
                $after->nuggetorder--;
                if ($after->nuggetorder < 0) {
                    $after->nuggetorder = 0;
                }
                $DB->update_record('microlearning_nugget', $after);
            }
        }

        // Delete the nugget.
        if (!$DB->delete_records('microlearning_nugget', array('id' => $nugget->id))) {
            try {
                throw new Exception('Could not delete nugget');
            } catch (\Exception $e) {
                $transaction->rollback($e);
            }

            return false;
        } else {
            $transaction->allow_commit();

            // Fire an Event for this.
            $eventother = array('threadid' => $nugget->threadid);

            $event = \block_iomad_microlearning\event\nugget_deleted::create(array('context' => context_system::instance(),
                                                                                   'userid' => $USER->id,
                                                                                   'objectid' => $nuggetid,
                                                                                   'other' => $eventother));
            $event->trigger();

            return true;
        }

    }

    public static function up_nugget($nuggetid) {
        global $DB, $USER;

        // Does the nugget exist.
        if (!$nugget = $DB->get_record('microlearning_nugget', array('id' => $nuggetid))) {
            return false;
        }

        // is it already the first one?
        if ($nugget->nuggetorder == 0) {
            return true;
        }

        // Get any nuggets after this one.
        if ($above = $DB->get_record_sql("SELECT * FROM {microlearning_nugget}
                                            WHERE threadid = :threadid
                                            AND nuggetorder = :above",
                                            array('threadid' => $nugget->threadid,
                                                  'above' => $nugget->nuggetorder - 1))) {
            $above->nuggetorder++;
            $DB->update_record('microlearning_nugget', $above);
            $nugget->nuggetorder--;
            $DB->update_record('microlearning_nugget', $nugget);
            if ($nugget->nuggetorder < 0) {
                // we need to re-order all of the nuggets as something went wrong....
                $threadnuggets = $DB->get_records('microlearning_nugget', array('threadid' => $nugget->threadid), 'nuggetorder', 'id');
                $newcount = 0;
                foreach ($threadnuggets as $threadnugget) {
                    $DB->set_field('microlearning_nugget', 'nuggetorder', $newcount, array('id' => $threadnugget->id));
                    $newcount++;
                }
            }
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
        global $DB, $USER;

        // Does the nugget exist.
        if (!$nugget = $DB->get_record('microlearning_nugget', array('id' => $nuggetid))) {
            return false;
        }

        // Get any nuggets after this one.
        if ($below = $DB->get_record_sql("SELECT * FROM {microlearning_nugget}
                                            WHERE threadid = :threadid
                                            AND nuggetorder = :below",
                                            array('threadid' => $nugget->threadid,
                                                  'below' => $nugget->nuggetorder + 1))) {
            $below->nuggetorder--;
            $DB->update_record('microlearning_nugget', $below);
            $nugget->nuggetorder++;
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

    public static function get_schedules($threadinfo, $nuggets, $startdate = null, $fromnuggetid = 0) {
        global $DB, $CFG;

        $returndata = new stdclass();
        $returndata->threadid = $threadinfo->id;
        if (empty($startdate)) {
            $passedtime = false;
            $startdate = $threadinfo->startdate;
        } else {
            $passedtime = true;
        }
        $schedulearray = array();
        $duedatearray = array();
        $reminder1array = array();
        $reminder2array = array();
        $found = false;

        foreach ($nuggets as $nugget) {
            // if we are passed a nugget ID we need to go from that one only.
            if (!empty($fromnuggetid) && $nugget->id == $fromnuggetid) {
                $found = true;
            }
            if (!empty($fromnuggetid) && $nugget->id != $fromnuggetid && !$found) {
                continue;
            }
            // Check if we already have a schedule.
            if ($schedule = $DB->get_record('microlearning_nugget_sched', array('nuggetid' => $nugget->id))) {
                if (!$passedtime || $startdate < $schedule->scheduledate) {
                    $startdate = $schedule->scheduledate;
                    $schedulearray[$nugget->id] = $schedule->scheduledate;
                    $duedatearray[$nugget->id] = $schedule->due_date;
                    $reminder1array[$nugget->id] = $schedule->reminder1_date;
                    $reminder2array[$nugget->id] = $schedule->reminder2_date;
                } else {
                    $schedulearray[$nugget->id] = $startdate;
                    $duedatearray[$nugget->id] = $startdate + $schedule->due_date - $schedule->scheduledate;
                    $reminder1array[$nugget->id] = $startdate + $schedule->reminder1_date - $schedule->scheduledate;
                    $reminder2array[$nugget->id] = $startdate + $schedule->reminder2_date - $schedule->scheduledate;
                    $startdate = $startdate + $schedule->due_date - $schedule->scheduledate;
                }
            } else {
                $schedulearray[$nugget->id] = $startdate + $threadinfo->message_preset + $threadinfo->message_time;
                $duedatearray[$nugget->id] =  $startdate + $threadinfo->message_preset + $threadinfo->defaultdue + $threadinfo->message_time;
                if (!empty($threadinfo->reminder1)) {
                    $reminder1array[$nugget->id] = $startdate + $threadinfo->message_preset + $threadinfo->reminder1 + $threadinfo->message_time;
                } else {
                    $reminder1array[$nugget->id] = null;
                }
                if (!empty($threadinfo->reminder2)) {
                    $reminder2array[$nugget->id] = $startdate + $threadinfo->message_preset + $threadinfo->reminder2 + $threadinfo->message_time;
                } else {
                    $reminder2array[$nugget->id] = null;
                }
                $startdate = $startdate + $threadinfo->releaseinterval;
            }
        }
        $returndata->threadinfo = $threadinfo;
        $returndata->schedulearray = $schedulearray;
        $returndata->duedatearray = $duedatearray;
        $returndata->reminder1array = $reminder1array;
        $returndata->reminder2array = $reminder2array;

        return $returndata;
    }

    public static function reset_thread_schedule($threadinfo) {
        global $DB, $USER;

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
        global $DB, $USER;

        // process the scheduledata.
        foreach (array_keys($scheduledata->schedulearray) as $nuggetid) {
            // Does it exist already?
            if ($DB->record_exists('microlearning_nugget_sched', array('nuggetid' => $nuggetid))) {
                // Update the stored nugget schedules.
                $DB->set_field('microlearning_nugget_sched', 'scheduledate', $scheduledata->schedulearray[$nuggetid], array('nuggetid' => $nuggetid));
                $DB->set_field('microlearning_nugget_sched', 'due_date', $scheduledata->duedatearray[$nuggetid], array('nuggetid' => $nuggetid));
                $DB->set_field('microlearning_nugget_sched', 'reminder1_date', $scheduledata->reminder1array[$nuggetid], array('nuggetid' => $nuggetid));
                $DB->set_field('microlearning_nugget_sched', 'reminder2_date', $scheduledata->reminder2array[$nuggetid], array('nuggetid' => $nuggetid));
            } else {
                // Make sure we have all the defaults.
                if (empty($scheduledata->duedatearray[$nuggetid])) {
                    $scheduledata->duedatearray[$nuggetid] = 0;
                }
                if (empty($scheduledata->reminder1array[$nuggetid])) {
                    $scheduledata->reminder1array[$nuggetid] = 0;
                }
                if (empty($scheduledata->reminder2array[$nuggetid])) {
                    $scheduledata->reminder2array[$nuggetid] = 0;
                }
                $DB->insert_record('microlearning_nugget_sched', array('scheduledate' => $scheduledata->schedulearray[$nuggetid],
                                                                       'nuggetid' => $nuggetid,
                                                                       'timecreated' => time(),
                                                                       'send_message' => $scheduledata->threadinfo->send_message,
                                                                       'send_reminder' => $scheduledata->threadinfo->send_reminder,
                                                                       'reminder1_date' => $scheduledata->reminder1array[$nuggetid],
                                                                       'reminder2_date' => $scheduledata->reminder2array[$nuggetid],
                                                                       'due_date'=> $scheduledata->duedatearray[$nuggetid]));
            }

            // Update the user nugget schedules.
            $DB->set_field('microlearning_thread_user', 'schedule_date', $scheduledata->schedulearray[$nuggetid], array('threadid' => $scheduledata->threadid, 'nuggetid' => $nuggetid));
            $DB->set_field('microlearning_thread_user', 'due_date', $scheduledata->duedatearray[$nuggetid], array('threadid' => $scheduledata->threadid, 'nuggetid' => $nuggetid));
            $DB->set_field('microlearning_thread_user', 'reminder1_date', $scheduledata->reminder1array[$nuggetid], array('threadid' => $scheduledata->threadid, 'nuggetid' => $nuggetid));
            $DB->set_field('microlearning_thread_user', 'reminder2_date', $scheduledata->reminder2array[$nuggetid], array('threadid' => $scheduledata->threadid, 'nuggetid' => $nuggetid));
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
        global $DB, $USER;

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
        if (empty($threadinfo->halt_until_fulfilled)) {
            $scheduleinfo = self::get_schedules($threadinfo, $nuggets);
        } else {
            // We want midnight last night.
            $starttime = time() - (time() % 86400);
            $scheduleinfo = self::get_schedules($threadinfo, $nuggets, $starttime);
        }

        // insert the user schedule info.
        $stop = false;
        $completed = false;
        foreach ($nuggets as $nugget) {
            $schedulerec = new stdclass();
            $schedulerec->userid = $userid;
            $schedulerec->threadid = $threadid;
            $schedulerec->nuggetid = $nugget->id;
            $schedulerec->schedule_date = $scheduleinfo->schedulearray[$nugget->id];
            $schedulerec->due_date = $scheduleinfo->duedatearray[$nugget->id];
            $schedulerec->message_time = $threadinfo->message_time;
            if (!empty($scheduleinfo->reminder2array[$nugget->id])) {
                $schedulerec->reminder1_date = $scheduleinfo->reminder1array[$nugget->id];
            } else {
                $schedulerec->reminder1_date = 0;
            }
            if (!empty($scheduleinfo->reminder2array[$nugget->id])) {
                $schedulerec->reminder2_date = $scheduleinfo->reminder2array[$nugget->id];
            } else {
                $schedulerec->reminder2_date = 0;
            }
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
                    $completed = true;
                } else {
                    $completed = false;
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
                                                      (SELECT id FROM {course_modules}
                                                       WHERE section = :section)
                                                     ORDER BY timemodified DESC",
                                                      array('userid' => $userid,
                                                            'section' => $nugget->sectionid));

                if (!empty($actualcount) && $requiredcount >= count($actualcount)) {
                    // Get the maximum time modified.
                    $last = array_shift($actualcount);
                    $schedulerec->timecompleted = $last->timemodified;
                    $completed = true;
                } else {
                    $completed = false;
                }
            } else {
                $schedulerec->timecompleted = null;
            }
            $schedulerec->accesskey = self::generate_accesskey();
            if (!$DB->insert_record('microlearning_thread_user', $schedulerec)) {
                $errors = true;
            }

            // Is this a halt until completed?
            if (!empty($threadinfo->halt_until_fulfilled) && !$completed) {
                break;
            }
        }

        if ($errors) {
            try {
                throw new Exception('Could not add user to thread');
            } catch (\Exception $e) {
                $transaction->rollback($e);
            }

            return false;
        } else {
            $transaction->allow_commit();
            return true;
        }
    }

    public static function remove_user_from_thread($threadid, $userid) {
        global $DB, $USER;

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
            try {
                throw new Exception('Could not remove user from thread');
            } catch (\Exception $e) {
                $transaction->rollback($e);
            }

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
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . '/course/lib.php');

        // Get the nugget url.
        if (!empty($nugget->url)) {
            $linkurl = $nugget->url;
        } else if (!empty($nugget->sectionid)) {
            $sectioninfo = $DB->get_record('course_sections', array('id' => $nugget->sectionid));
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

    public static function get_menu_threads($companyid) {
        global $DB, $USER;

        $threads = $DB->get_records_menu('microlearning_thread', array('companyid' => $companyid), 'name', 'id,name');
        $menuthreads = array(0 => get_string('selectthread', 'block_iomad_microlearning'));
        foreach ($threads as $id => $name) {
            $menuthreads[$id] = format_text($name);
        }

        return $menuthreads;
    }

    public static function assign_thread_to_user($user, $threadid, $companyid) {
        global $DB, $USER;

        // Is the user valid.
        if (!$userrec = $DB->get_record('user', array('id' => $user->id, 'deleted' => 0, 'suspended' => 0))) {
            return false;
        }

        // The thread?
        if (!$threadrec = $DB->get_record('microlearning_thread', array('id' => $threadid, 'companyid' => $companyid))) {
            return false;
        }

        // The company?
        if (!$companyrec = $DB->get_record('company', array('id' => $companyid, 'suspended' => 0))) {
            return false;
        }

        // All OK so do the work.
        self::add_user_to_thread($threadid, $user->id);

        // Fire an event for this.
        $eventother = array('companyid' => $companyid);

        $event = \block_iomad_microlearning\event\nugget_created::create(array('context' => context_system::instance(),
                                                                               'userid' => $USER->id,
                                                                               'relateduserid' => $user->id,
                                                                               'objectid' => $threadid,
                                                                               'other' => $eventother));
        $event->trigger();

    }

    public static function remove_thread_from_user($user, $threadid, $companyid) {
        global $DB, $USER;

        // Is the user valid.
        if (!$userrec = $DB->get_record('user', array('id' => $user->id, 'deleted' => 0, 'suspended' => 0))) {
            return false;
        }

        // The thread?
        if (!$threadrec = $DB->get_record('microlearning_thread', array('id' => $threadid, 'companyid' => $companyid))) {
            return false;
        }

        // The company?
        if (!$companyrec = $DB->get_record('company', array('id' => $companyid, 'suspended' => 0))) {
            return false;
        }

        // All OK so do the work.
        self::remove_user_from_thread($threadid, $user->id);

        // Fire an event for this.
        $eventother = array('companyid' => $companyid);

        $event = \block_iomad_microlearning\event\nugget_created::create(array('context' => context_system::instance(),
                                                                               'userid' => $USER->id,
                                                                               'relateduserid' => $user->id,
                                                                               'objectid' => $threadid,
                                                                               'other' => $eventother));
        $event->trigger();

    }

    /* Event handlers */

    public static function event_thread_created(\block_iomad_microlearning\event\thread_created $event) {
        global $DB, $USER;
    }

    public static function event_thread_deleted(\block_iomad_microlearning\event\thread_deleted $event) {
        global $DB, $USER;
    }

    public static function event_thread_updated(\block_iomad_microlearning\event\thread_updated $event) {
        global $DB, $USER;
    }

    public static function event_thread_schedule_updated(\block_iomad_microlearning\event\thread_schedule_updated $event) {
        global $DB, $USER;
    }

    public static function event_nugget_created(\block_iomad_microlearning\event\nugget_created $event) {
        global $DB, $USER;
    }

    public static function event_nugget_deleted(\block_iomad_microlearning\event\nugget_deleted $event) {
        global $DB, $USER;
    }

    public static function event_nugget_updated(\block_iomad_microlearning\event\nugget_updated $event) {
        global $DB, $USER;
    }

    public static function event_nugget_moved(\block_iomad_microlearning\event\nugget_moved $event) {
        global $DB, $USER;
    }

    public static function event_user_deleted(\core\event\user_deleted $event) {
        global $DB, $USER;

        // Delete all of the schedules for this user.
        $DB->delete_records('microlearning_thread_user', array('userid' => $event->objectid));
    }

    public static function event_course_module_completion_updated(\core\event\course_module_completion_updated $event) {
        global $DB, $USER;
        $cmid = $event->contextinstanceid;
        $userid = $event->relateduserid;
        $found = false;
        $threads = array();
        if ($nuggets = $DB->get_records_sql("SELECT mtu.* FROM {microlearning_thread_user} mtu
                                             JOIN {microlearning_nugget} mn ON (mtu.nuggetid = mn.id)
                                             WHERE mtu.userid = :userid
                                             AND mn.cmid = :cmid", array('userid' => $userid, 'cmid' => $cmid))) {
            foreach ($nuggets as $nugget) {
                if ($nugget->cmid == $cmid) {
                    $found = true;
                    if (empty($threads[$nugget->threadid])) {
                        $threads[$nugget->threadid] = array();
                    }
                    $threads[$nugget->threadid][$nugget->nuggetid] = $nugget->nuggetid;
                }
                $DB->set_field('microlearning_thread_user', 'timecompleted', $event->timecreated, array('id' => $nugget->id, 'userid' => $userid));
            }
        }

        // check if there is a section set instead.
        $cmidrec = $DB->get_record('course_modules', array('id' => $cmid));
        if ($nuggets = $DB->get_records_sql("SELECT mtu.* FROM {microlearning_thread_user} mtu
                                         JOIN {microlearning_nugget} mn ON (mtu.nuggetid = mn.id)
                                         WHERE mtu.userid = :userid
                                         AND mn.sectionid = :sectionid", array('userid' => $userid, 'sectionid' => $cmidrec->section))) {

            // Get all of the course modules in that section which have completion set up.
            $requiredcount = $DB->count_records_sql("SELECT COUNT(id) FROM {course_modules}
                                                     WHERE section = :section
                                                     AND completion > 0",
                                                     array('section' => $cmidrec->section));

            // Get all of the course modules in that section which have completion set up.
            $actualcount = $DB->count_records_sql("SELECT COUNT(id) FROM {course_modules_completion}
                                                     WHERE userid = :userid
                                                     AND completionstate > 0
                                                     AND coursemoduleid IN
                                                      (SELECT id FROM {course_modules}
                                                       WHERE section = :section)",
                                                     array('userid' => $userid,
                                                           'section' => $cmidrec->section));
            // If we have everything we need, mark it as completed.
            if ($requiredcount == $actualcount) {
                foreach ($nuggets as $nugget) {
                    $found = true;
                    if (empty($threads[$nugget->threadid])) {
                        $threads[$nugget->threadid] = array();
                    }
                    $threads[$nugget->threadid][$nugget->nuggetid] = $nugget->nuggetid;
                    $DB->set_field('microlearning_thread_user', 'timecompleted', $event->timecreated, array('id' => $nugget->id));
                }
            }
        }

        // Did we find anything?  Check if we need to anything else if it's a halted thread.
        if ($found) {
            foreach ($threads as $threadid => $threadnuggets) {
                if (!$threadrec = $DB->get_record('microlearning_thread', array('id' => $threadid, 'halt_until_fulfilled' => 1))) {
                    continue;
                }
                // Get the nuggets from the thread.
                $mynuggets = $DB->get_records('microlearning_nugget', array('threadid' => $threadid), 'nuggetorder', '*');
                $found = false;
                foreach ($mynuggets as $nugget) {
                    if (!empty($threadnuggets[$nugget->id])) {
                        $found = true;
                        break;
                    } else {
                        unset($mynuggets[$nugget->id]);
                    }
                }
                if ($found && count($mynuggets) > 1) {
                    unset($mynuggets[$nugget->id]);
                    $wantednuggets = $mynuggets;
                    $nextnugget = array_shift($wantednuggets);
                    $threadscheds = self::get_schedules($threadrec, $mynuggets, $event->timecreated, $nextnugget->id);
                    $completed = false;
                    $stop = false;
                    foreach ($mynuggets as $mynugget) {
                        $schedulerec = new stdclass();
                        $schedulerec->userid = $userid;
                        $schedulerec->threadid = $threadid;
                        $schedulerec->nuggetid = $mynugget->id;
                        $schedulerec->schedule_date = $threadscheds->schedulearray[$mynugget->id];
                        $schedulerec->due_date = $threadscheds->duedatearray[$mynugget->id];
                        $schedulerec->message_time = $threadrec->message_time;
                        $schedulerec->reminder1_date = $schedulerec->schedule_date + $threadrec->reminder1;
                        $schedulerec->reminder2_date = $schedulerec->schedule_date + $threadrec->reminder2;
                        $schedulerec->message_delivered = false;
                        $schedulerec->reminder1_delivered = false;
                        $schedulerec->reminder2_delivered = false;
                        $schedulerec->timecreated = time();
                        if (!empty($mynugget->cmid)) {
                            if ($modcompletion = $DB->get_record_sql("SELECT * FROM {course_modules_completion}
                                                                      WHERE userid = :userid
                                                                      AND coursemoduleid = :cmid
                                                                      AND completionstate > 0",
                                                                      array('userid' => $userid,
                                                                            'cmid' => $mynugget->cmid))) {
                                $schedulerec->timecompleted = $modcompletion->timemodified;
                                $completed = true;
                            } else {
                                $completed = false;
                            }
                        } else if (!empty($mynugget->sectionid)) {
                            // Get all of the course modules in that section which have completion set up.
                            $requiredcount = $DB->count_records_sql("SELECT COUNT(id) FROM {course_modules}
                                                                     WHERE section = :section
                                                                     AND completion > 0",
                                                                     array('section' => $mynugget->sectionid));

                            // Get all of the course modules in that section which have completion set up.
                            $actualcount = $DB->get_records_sql("SELECT * FROM {course_modules_completion}
                                                                 WHERE userid = :userid
                                                                 AND completionstate > 0
                                                                 AND coursemoduleid IN
                                                                  (SELECT id FROM {course_modules}
                                                                   WHERE section = :section)
                                                                 ORDER BY timemodified DESC",
                                                                  array('userid' => $userid,
                                                                        'section' => $mynugget->sectionid));

                            if (!empty($actualcount) && $requiredcount >= count($actualcount)) {
                            // Get the maximum time modified.
                                $last = array_shift($actualcount);
                                $schedulerec->timecompleted = $last->timemodified;
                                $completed = true;
                            } else {
                                $completed = false;
                            }
                        } else {
                            $schedulerec->timecompleted = null;
                        }
                        $schedulerec->accesskey = self::generate_accesskey();
                        if (!$DB->insert_record('microlearning_thread_user', $schedulerec)) {
                            $errors = true;
                        }

                        // Is this a halt until completed?
                        if (!empty($threadrec->halt_until_fulfilled) && !$completed) {
                            break;
                        }
                    }
                }
            }
        }
    }

    public static function cron() {
        global $DB, $USER;

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
                                                   AND mtu.schedule_date < :runtime",
                                                   array('runtime' => $runtime))) {
            foreach ($scheduleusers as $scheduleuser) {
                $scheduleuser->message_delivered = 1;

                if ($user = $DB->get_record('user', array('id' => $scheduleuser->userid, 'suspended' => 0, 'deleted' => 0))) {
                    // Get the email payload.
                    if ($nugget = $DB->get_record('microlearning_nugget', array('id' => $scheduleuser->nuggetid))) {
                        $company = company::by_userid($user->id);
                        // Get the nugget link.
                        $nugget->url = new moodle_url('/blocks/iomad_microlearning/land.php', array('nuggetid' => $nugget->id, 'userid' => $user->id, 'accesskey' =>$scheduleuser->accesskey));
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
        if ($reminder1users = $DB->get_records_sql("SELECT mtu.* FROM {microlearning_thread_user} mtu
                                                   JOIN {microlearning_thread} mt
                                                   ON (mtu.threadid = mt.id)
                                                   WHERE mt.send_reminder = 1
                                                   AND mtu.timecompleted IS NULL
                                                   AND mtu.reminder1_delivered = 0
                                                   AND mtu.reminder1_date IS NOT NULL
                                                   AND mtu.reminder1_date < :runtime",
                                                   array('runtime' => $runtime))) {
            foreach ($reminder1users as $reminder1user) {
                $reminder1user->reminder1_delivered = 1;

                if ($user = $DB->get_record('user', array('id' => $reminder1user->userid, 'suspended' => 0, 'deleted' => 0))) {
                    // Get the email payload.
                    if ($nugget = $DB->get_record('microlearning_nugget', array('id' => $reminder1user->nuggetid))) {
                        $company = company::by_userid($user->id);
                        // Fix the payload.
                        $nugget->name = format_text($nugget->name);
                        $nugget->url = new moodle_url('/blocks/iomad_microlearning/land.php', array('nuggetid' => $nugget->id, 'userid' => $user->id, 'accesskey' =>$reminder1user->accesskey));
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
                                                   ON (mtu.threadid = mt.id)
                                                   WHERE mt.send_reminder = 1
                                                   AND mtu.timecompleted IS NULL
                                                   AND mtu.reminder2_delivered = 0
                                                   AND mtu.reminder2_date IS NOT NULL
                                                   AND mtu.reminder2_date < :runtime",
                                                   array('runtime' => $runtime))) {
            foreach ($reminder2users as $reminder2user) {
                $reminder2user->reminder2_delivered = 1;

                if ($user = $DB->get_record('user', array('id' => $reminder2user->userid, 'suspended' => 0, 'deleted' => 0))) {
                    // Get the email payload.
                    if ($nugget = $DB->get_record('microlearning_nugget', array('id' => $reminder2user->nuggetid))) {
                        $company = company::by_userid($user->id);
                        // Fix the payload.
                        $nugget->name = format_text($nugget->name);
                        $nugget->url = new moodle_url('/blocks/iomad_microlearning/land.php', array('nuggetid' => $nugget->id, 'userid' => $user->id, 'accesskey' =>$reminder2user->accesskey));
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
