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
 * A scheduled task for journal cron.
 *
 * @package    mod_journal
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_journal\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/journal/lib.php');

/**
 * The cron_task class.
 *
 * @package    mod_journal
 * @copyright  2022 Elearning Software SRL http://elearningsoftware.ro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron_task extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'mod_journal');
    }

    /**
     * Execute the scheduled task.
     */
    public function execute() {
        global $CFG, $USER, $DB;

        $cutofftime = time() - $CFG->maxeditingtime;

        if ($entries = journal_get_unmailed_graded($cutofftime)) {
            $timenow = time();

            if (class_exists('\core_user\fields')) {
                $usernamefields = \core_user\fields::get_name_fields();
            } else {
                $usernamefields = get_all_user_name_fields();
            }
            $requireduserfields = 'id, auth, mnethostid, email, mailformat, maildisplay, lang, deleted, suspended, '
                    .implode(', ', $usernamefields);

            // To save some db queries.
            $users = array();
            $courses = array();

            foreach ($entries as $entry) {

                echo "Processing journal entry $entry->id\n";

                if (!empty($users[$entry->userid])) {
                    $user = $users[$entry->userid];
                } else {
                    if (!$user = $DB->get_record("user", array("id" => $entry->userid), $requireduserfields)) {
                        echo "Could not find user $entry->userid\n";
                        continue;
                    }
                    $users[$entry->userid] = $user;
                }

                $USER->lang = $user->lang;

                if (!empty($courses[$entry->course])) {
                    $course = $courses[$entry->course];
                } else {
                    if (!$course = $DB->get_record('course', array('id' => $entry->course), 'id, shortname')) {
                        echo "Could not find course $entry->course\n";
                        continue;
                    }
                    $courses[$entry->course] = $course;
                }

                if (!empty($users[$entry->teacher])) {
                    $teacher = $users[$entry->teacher];
                } else {
                    if (!$teacher = $DB->get_record("user", array("id" => $entry->teacher), $requireduserfields)) {
                        echo "Could not find teacher $entry->teacher\n";
                        continue;
                    }
                    $users[$entry->teacher] = $teacher;
                }

                // All cached.
                $coursejournals = get_fast_modinfo($course)->get_instances_of('journal');
                if (empty($coursejournals) || empty($coursejournals[$entry->journal])) {
                    echo "Could not find course module for journal id $entry->journal\n";
                    continue;
                }
                $mod = $coursejournals[$entry->journal];

                // This is already cached internally.
                $context = \context_module::instance($mod->id);
                $canadd = has_capability('mod/journal:addentries', $context, $user);
                $entriesmanager = has_capability('mod/journal:manageentries', $context, $user);

                if (!$canadd && $entriesmanager) {
                    continue;  // Not an active participant.
                }

                $journalinfo = new \stdClass();
                $journalinfo->teacher = fullname($teacher);
                $journalinfo->journal = format_string($entry->name, true);
                $journalinfo->url = "$CFG->wwwroot/mod/journal/view.php?id=$mod->id";
                $modnamepl = get_string( 'modulenameplural', 'journal' );
                $msubject = get_string( 'mailsubject', 'journal' );

                $postsubject = "$course->shortname: $msubject: ".format_string($entry->name, true);
                $posttext  = "$course->shortname -> $modnamepl -> ".format_string($entry->name, true)."\n";
                $posttext .= "---------------------------------------------------------------------\n";
                $posttext .= get_string("journalmail", "journal", $journalinfo)."\n";
                $posttext .= "---------------------------------------------------------------------\n";
                if ($user->mailformat == 1) {  // HTML.
                    $posthtml = "<p><font face=\"sans-serif\">".
                    "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                    "<a href=\"$CFG->wwwroot/mod/journal/index.php?id=$course->id\">journals</a> ->".
                    "<a href=\"$CFG->wwwroot/mod/journal/view.php?id=$mod->id\">"
                        .format_string($entry->name, true)
                    ."</a></font></p>";
                    $posthtml .= "<hr /><font face=\"sans-serif\">";
                    $posthtml .= "<p>".get_string("journalmailhtml", "journal", $journalinfo)."</p>";
                    $posthtml .= "</font><hr />";
                } else {
                    $posthtml = "";
                }

                if (! email_to_user($user, $teacher, $postsubject, $posttext, $posthtml)) {
                    echo "Error: Journal cron: Could not send out mail for "
                        . "id $entry->id to user $user->id ($user->email)\n";
                }
                if (!$DB->set_field("journal_entries", "mailed", "1", array("id" => $entry->id))) {
                    echo "Could not update the mailed field for id $entry->id\n";
                }
            }
        }

        return true;
    }
}
