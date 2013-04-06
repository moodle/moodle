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
 * Cron job for reviewing and aggregating badge award criteria
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/badgeslib.php');

function badge_cron() {
    global $CFG;

    if (!empty($CFG->enablebadges)) {
        badge_review_cron();
        badge_message_cron();
    }
}

/**
 * Reviews criteria and awards badges
 *
 * First find all badges that can be earned, then reviews each badge.
 * (Not sure how efficient this is timewise).
 */
function badge_review_cron() {
    global $DB, $CFG;
    $total = 0;

    $courseparams = array();
    if (empty($CFG->badges_allowcoursebadges)) {
        $coursesql = '';
    } else {
        $coursesql = ' OR EXISTS (SELECT id FROM {course} WHERE visible = :visible AND startdate < :current) ';
        $courseparams = array('visible' => true, 'current' => time());
    }

    $sql = 'SELECT id
                FROM {badge}
                WHERE (status = :active OR status = :activelocked)
                    AND (type = :site ' . $coursesql . ')';
    $badgeparams = array(
                    'active' => BADGE_STATUS_ACTIVE,
                    'activelocked' => BADGE_STATUS_ACTIVE_LOCKED,
                    'site' => BADGE_TYPE_SITE
                    );
    $params = array_merge($badgeparams, $courseparams);
    $badges = $DB->get_fieldset_sql($sql, $params);

    mtrace('Started reviewing available badges.');
    foreach ($badges as $bid) {
        $badge = new badge($bid);

        if ($badge->has_criteria()) {
            if (debugging()) {
                mtrace('Processing badge "' . $badge->name . '"...');
            }

            $issued = $badge->review_all_criteria();

            if (debugging()) {
                mtrace('...badge was issued to ' . $issued . ' users.');
            }
            $total += $issued;
        }
    }

    mtrace('Badges were issued ' . $total . ' time(s).');
}

/**
 * Sends out scheduled messages to badge creators
 *
 */
function badge_message_cron() {
    global $DB;

    mtrace('Sending scheduled badge notifications.');

    $scheduled = $DB->get_records_select('badge', 'notification > ? AND (status != ?) AND nextcron < ?',
                            array(BADGE_MESSAGE_ALWAYS, BADGE_STATUS_ARCHIVED, time()),
                            'notification ASC', 'id, name, notification, usercreated as creator, timecreated');

    foreach ($scheduled as $sch) {
        // Send messages.
        badge_assemble_notification($sch);

        // Update next cron value.
        $nextcron = badges_calculate_message_schedule($sch->notification);
        $DB->set_field('badge', 'nextcron', $nextcron, array('id' => $sch->id));
    }
}

/**
 * Creates single message for all notification and sends it out
 *
 * @param object $badge A badge which is notified about.
 */
function badge_assemble_notification(stdClass $badge) {
    global $CFG, $DB;

    $admin = get_admin();
    $userfrom = new stdClass();
    $userfrom->id = $admin->id;
    $userfrom->email = !empty($CFG->badges_defaultissuercontact) ? $CFG->badges_defaultissuercontact : $admin->email;
    $userfrom->firstname = !empty($CFG->badges_defaultissuername) ? $CFG->badges_defaultissuername : $admin->firstname;
    $userfrom->lastname = !empty($CFG->badges_defaultissuername) ? '' : $admin->lastname;
    $userfrom->maildisplay = true;

    if ($msgs = $DB->get_records_select('badge_issued', 'issuernotified IS NULL AND badgeid = ?', array($badge->id))) {
        // Get badge creator.
        $creator = $DB->get_record('user', array('id' => $badge->creator), '*', MUST_EXIST);
        $creatorsubject = get_string('creatorsubject', 'badges', $badge->name);
        $creatormessage = '';

        // Put all messages in one digest.
        foreach ($msgs as $msg) {
            $issuedlink = html_writer::link(new moodle_url('/badges/badge.php', array('hash' => $msg->uniquehash)), $badge->name);
            $recipient = $DB->get_record('user', array('id' => $msg->userid), '*', MUST_EXIST);

            $a = new stdClass();
            $a->user = fullname($recipient);
            $a->link = $issuedlink;
            $creatormessage .= get_string('creatorbody', 'badges', $a);
            $DB->set_field('badge_issued', 'issuernotified', time(), array('badgeid' => $msg->badgeid, 'userid' => $msg->userid));
        }

        // Create a message object.
        $eventdata = new stdClass();
        $eventdata->component         = 'moodle';
        $eventdata->name              = 'instantmessage';
        $eventdata->userfrom          = $userfrom;
        $eventdata->userto            = $creator;
        $eventdata->notification      = 1;
        $eventdata->subject           = $creatorsubject;
        $eventdata->fullmessage       = $creatormessage;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = format_text($creatormessage, FORMAT_HTML);
        $eventdata->smallmessage      = '';

        message_send($eventdata);
    }
}
