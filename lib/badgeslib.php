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
 * Contains classes, functions and constants used in badges.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/* Include required award criteria library. */
require_once($CFG->dirroot . '/badges/criteria/award_criteria.php');

/*
 * Number of records per page.
*/
define('BADGE_PERPAGE', 50);

/*
 * Badge award criteria aggregation method.
 */
define('BADGE_CRITERIA_AGGREGATION_ALL', 1);

/*
 * Badge award criteria aggregation method.
 */
define('BADGE_CRITERIA_AGGREGATION_ANY', 2);

/*
 * Inactive badge means that this badge cannot be earned and has not been awarded
 * yet. Its award criteria can be changed.
 */
define('BADGE_STATUS_INACTIVE', 0);

/*
 * Active badge means that this badge can we earned, but it has not been awarded
 * yet. Can be deactivated for the purpose of changing its criteria.
 */
define('BADGE_STATUS_ACTIVE', 1);

/*
 * Inactive badge can no longer be earned, but it has been awarded in the past and
 * therefore its criteria cannot be changed.
 */
define('BADGE_STATUS_INACTIVE_LOCKED', 2);

/*
 * Active badge means that it can be earned and has already been awarded to users.
 * Its criteria cannot be changed any more.
 */
define('BADGE_STATUS_ACTIVE_LOCKED', 3);

/*
 * Archived badge is considered deleted and can no longer be earned and is not
 * displayed in the list of all badges.
 */
define('BADGE_STATUS_ARCHIVED', 4);

/*
 * Badge type for site badges.
 */
define('BADGE_TYPE_SITE', 1);

/*
 * Badge type for course badges.
 */
define('BADGE_TYPE_COURSE', 2);

/*
 * Badge messaging schedule options.
 */
define('BADGE_MESSAGE_NEVER', 0);
define('BADGE_MESSAGE_ALWAYS', 1);
define('BADGE_MESSAGE_DAILY', 2);
define('BADGE_MESSAGE_WEEKLY', 3);
define('BADGE_MESSAGE_MONTHLY', 4);

/*
 * URL of backpack. Currently only the Open Badges backpack is supported.
 */
define('BADGE_BACKPACKURL', 'backpack.openbadges.org');

/**
 * Class that represents badge.
 *
 */
class badge {
    /** @var int Badge id */
    public $id;

    /** Values from the table 'badge' */
    public $name;
    public $description;
    public $timecreated;
    public $timemodified;
    public $usercreated;
    public $usermodified;
    public $issuername;
    public $issuerurl;
    public $issuercontact;
    public $expiredate;
    public $expireperiod;
    public $type;
    public $courseid;
    public $message;
    public $messagesubject;
    public $attachment;
    public $notification;
    public $status = 0;
    public $nextcron;

    /** @var array Badge criteria */
    public $criteria = array();

    /**
     * Constructs with badge details.
     *
     * @param int $badgeid badge ID.
     */
    public function __construct($badgeid) {
        global $DB;
        $this->id = $badgeid;

        $data = $DB->get_record('badge', array('id' => $badgeid));

        if (empty($data)) {
            print_error('error:nosuchbadge', 'badges', $badgeid);
        }

        foreach ((array)$data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }

        $this->criteria = self::get_criteria();
    }

    /**
     * Use to get context instance of a badge.
     * @return context instance.
     */
    public function get_context() {
        if ($this->type == BADGE_TYPE_SITE) {
            return context_system::instance();
        } else if ($this->type == BADGE_TYPE_COURSE) {
            return context_course::instance($this->courseid);
        } else {
            debugging('Something is wrong...');
        }
    }

    /**
     * Return array of aggregation methods
     * @return array
     */
    public static function get_aggregation_methods() {
        return array(
                BADGE_CRITERIA_AGGREGATION_ALL => get_string('all', 'badges'),
                BADGE_CRITERIA_AGGREGATION_ANY => get_string('any', 'badges'),
        );
    }

    /**
     * Return array of accepted criteria types for this badge
     * @return array
     */
    public function get_accepted_criteria() {
        $criteriatypes = array();

        if ($this->type == BADGE_TYPE_COURSE) {
            $criteriatypes = array(
                    BADGE_CRITERIA_TYPE_OVERALL,
                    BADGE_CRITERIA_TYPE_MANUAL,
                    BADGE_CRITERIA_TYPE_COURSE,
                    BADGE_CRITERIA_TYPE_ACTIVITY
            );
        } else if ($this->type == BADGE_TYPE_SITE) {
            $criteriatypes = array(
                    BADGE_CRITERIA_TYPE_OVERALL,
                    BADGE_CRITERIA_TYPE_MANUAL,
                    BADGE_CRITERIA_TYPE_COURSESET,
                    BADGE_CRITERIA_TYPE_PROFILE,
            );
        }

        return $criteriatypes;
    }

    /**
     * Save/update badge information in 'badge' table only.
     * Cannot be used for updating awards and criteria settings.
     *
     * @return bool Returns true on success.
     */
    public function save() {
        global $DB;

        $fordb = new stdClass();
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
        }
        unset($fordb->criteria);

        $fordb->timemodified = time();
        if ($DB->update_record_raw('badge', $fordb)) {
            return true;
        } else {
            throw new moodle_exception('error:save', 'badges');
            return false;
        }
    }

    /**
     * Creates and saves a clone of badge with all its properties.
     * Clone is not active by default and has 'Copy of' attached to its name.
     *
     * @return int ID of new badge.
     */
    public function make_clone() {
        global $DB, $USER;

        $fordb = new stdClass();
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
        }

        $fordb->name = get_string('copyof', 'badges', $this->name);
        $fordb->status = BADGE_STATUS_INACTIVE;
        $fordb->usercreated = $USER->id;
        $fordb->usermodified = $USER->id;
        $fordb->timecreated = time();
        $fordb->timemodified = time();
        unset($fordb->id);

        if ($fordb->notification > 1) {
            $fordb->nextcron = badges_calculate_message_schedule($fordb->notification);
        }

        $criteria = $fordb->criteria;
        unset($fordb->criteria);

        if ($new = $DB->insert_record('badge', $fordb, true)) {
            $newbadge = new badge($new);

            // Copy badge image.
            $fs = get_file_storage();
            if ($file = $fs->get_file($this->get_context()->id, 'badges', 'badgeimage', $this->id, '/', 'f1.png')) {
                if ($imagefile = $file->copy_content_to_temp()) {
                    badges_process_badge_image($newbadge, $imagefile);
                }
            }

            // Copy badge criteria.
            foreach ($this->criteria as $crit) {
                $crit->make_clone($new);
            }

            return $new;
        } else {
            throw new moodle_exception('error:clone', 'badges');
            return false;
        }
    }

    /**
     * Checks if badges is active.
     * Used in badge award.
     *
     * @return bool A status indicating badge is active
     */
    public function is_active() {
        if (($this->status == BADGE_STATUS_ACTIVE) ||
            ($this->status == BADGE_STATUS_ACTIVE_LOCKED)) {
            return true;
        }
        return false;
    }

    /**
     * Use to get the name of badge status.
     *
     */
    public function get_status_name() {
        return get_string('badgestatus_' . $this->status, 'badges');
    }

    /**
     * Use to set badge status.
     * Only active badges can be earned/awarded/issued.
     *
     * @param int $status Status from BADGE_STATUS constants
     */
    public function set_status($status = 0) {
        $this->status = $status;
        $this->save();
    }

    /**
     * Checks if badges is locked.
     * Used in badge award and editing.
     *
     * @return bool A status indicating badge is locked
     */
    public function is_locked() {
        if (($this->status == BADGE_STATUS_ACTIVE_LOCKED) ||
                ($this->status == BADGE_STATUS_INACTIVE_LOCKED)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if badge has been awarded to users.
     * Used in badge editing.
     *
     * @return bool A status indicating badge has been awarded at least once
     */
    public function has_awards() {
        global $DB;
        $awarded = $DB->record_exists_sql('SELECT b.uniquehash
                    FROM {badge_issued} b INNER JOIN {user} u ON b.userid = u.id
                    WHERE b.badgeid = :badgeid AND u.deleted = 0', array('badgeid' => $this->id));

        return $awarded;
    }

    /**
     * Gets list of users who have earned an instance of this badge.
     *
     * @return array An array of objects with information about badge awards.
     */
    public function get_awards() {
        global $DB;

        $awards = $DB->get_records_sql(
                'SELECT b.userid, b.dateissued, b.uniquehash, u.firstname, u.lastname
                    FROM {badge_issued} b INNER JOIN {user} u
                        ON b.userid = u.id
                    WHERE b.badgeid = :badgeid AND u.deleted = 0', array('badgeid' => $this->id));

        return $awards;
    }

    /**
     * Indicates whether badge has already been issued to a user.
     *
     */
    public function is_issued($userid) {
        global $DB;
        return $DB->record_exists('badge_issued', array('badgeid' => $this->id, 'userid' => $userid));
    }

    /**
     * Issue a badge to user.
     *
     * @param int $userid User who earned the badge
     * @param bool $nobake Not baking actual badges (for testing purposes)
     */
    public function issue($userid, $nobake = false) {
        global $DB, $CFG;

        $now = time();
        $issued = new stdClass();
        $issued->badgeid = $this->id;
        $issued->userid = $userid;
        $issued->uniquehash = sha1(rand() . $userid . $this->id . $now);
        $issued->dateissued = $now;

        if ($this->can_expire()) {
            $issued->dateexpire = $this->calculate_expiry($now);
        } else {
            $issued->dateexpire = null;
        }

        // Take into account user badges privacy settings.
        // If none set, badges default visibility is set to public.
        $issued->visible = get_user_preferences('badgeprivacysetting', 1, $userid);

        $result = $DB->insert_record('badge_issued', $issued, true);

        if ($result) {
            // Lock the badge, so that its criteria could not be changed any more.
            if ($this->status == BADGE_STATUS_ACTIVE) {
                $this->set_status(BADGE_STATUS_ACTIVE_LOCKED);
            }

            // Update details in criteria_met table.
            $compl = $this->get_criteria_completions($userid);
            foreach ($compl as $c) {
                $obj = new stdClass();
                $obj->id = $c->id;
                $obj->issuedid = $result;
                $DB->update_record('badge_criteria_met', $obj, true);
            }

            if (!$nobake) {
                // Bake a badge image.
                $pathhash = badges_bake($issued->uniquehash, $this->id, $userid, true);

                // Notify recipients and badge creators.
                badges_notify_badge_award($this, $userid, $issued->uniquehash, $pathhash);
            }
        }
    }

    /**
     * Reviews all badge criteria and checks if badge can be instantly awarded.
     *
     * @return int Number of awards
     */
    public function review_all_criteria() {
        global $DB, $CFG;
        $awards = 0;

        // Raise timelimit as this could take a while for big web sites.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        foreach ($this->criteria as $crit) {
            // Overall criterion is decided when other criteria are reviewed.
            if ($crit->criteriatype == BADGE_CRITERIA_TYPE_OVERALL) {
                continue;
            }

            list($extrajoin, $extrawhere, $extraparams) = $crit->get_completed_criteria_sql();
            // For site level badges, get all active site users who can earn this badge and haven't got it yet.
            if ($this->type == BADGE_TYPE_SITE) {
                $sql = "SELECT DISTINCT u.id, bi.badgeid
                        FROM {user} u
                        {$extrajoin}
                        LEFT JOIN {badge_issued} bi
                            ON u.id = bi.userid AND bi.badgeid = :badgeid
                        WHERE bi.badgeid IS NULL AND u.id != :guestid AND u.deleted = 0 " . $extrawhere;
                $params = array_merge(array('badgeid' => $this->id, 'guestid' => $CFG->siteguest), $extraparams);
                $toearn = $DB->get_fieldset_sql($sql, $params);
            } else {
                // For course level badges, get all users who already earned the badge in this course.
                // Then find the ones who are enrolled in the course and don't have a badge yet.
                $earned = $DB->get_fieldset_select('badge_issued', 'userid AS id', 'badgeid = :badgeid', array('badgeid' => $this->id));
                $wheresql = '';
                $earnedparams = array();
                if (!empty($earned)) {
                    list($earnedsql, $earnedparams) = $DB->get_in_or_equal($earned, SQL_PARAMS_NAMED, 'u', false);
                    $wheresql = ' WHERE u.id ' . $earnedsql;
                }
                list($enrolledsql, $enrolledparams) = get_enrolled_sql($this->get_context(), 'moodle/badges:earnbadge', 0, true);
                $sql = "SELECT u.id
                        FROM {user} u
                        {$extrajoin}
                        JOIN ({$enrolledsql}) je ON je.id = u.id " . $wheresql . $extrawhere;
                $params = array_merge($enrolledparams, $earnedparams, $extraparams);
                $toearn = $DB->get_fieldset_sql($sql, $params);
            }

            foreach ($toearn as $uid) {
                $reviewoverall = false;
                if ($crit->review($uid, true)) {
                    $crit->mark_complete($uid);
                    if ($this->criteria[BADGE_CRITERIA_TYPE_OVERALL]->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                        $this->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($uid);
                        $this->issue($uid);
                        $awards++;
                    } else {
                        $reviewoverall = true;
                    }
                } else {
                    // Will be reviewed some other time.
                    $reviewoverall = false;
                }
                // Review overall if it is required.
                if ($reviewoverall && $this->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($uid)) {
                    $this->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($uid);
                    $this->issue($uid);
                    $awards++;
                }
            }
        }

        return $awards;
    }

    /**
     * Gets an array of completed criteria from 'badge_criteria_met' table.
     *
     * @param int $userid Completions for a user
     * @return array Records of criteria completions
     */
    public function get_criteria_completions($userid) {
        global $DB;
        $completions = array();
        $sql = "SELECT bcm.id, bcm.critid
                FROM {badge_criteria_met} bcm
                    INNER JOIN {badge_criteria} bc ON bcm.critid = bc.id
                WHERE bc.badgeid = :badgeid AND bcm.userid = :userid ";
        $completions = $DB->get_records_sql($sql, array('badgeid' => $this->id, 'userid' => $userid));

        return $completions;
    }

    /**
     * Checks if badges has award criteria set up.
     *
     * @return bool A status indicating badge has at least one criterion
     */
    public function has_criteria() {
        if (count($this->criteria) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Returns badge award criteria
     *
     * @return array An array of badge criteria
     */
    public function get_criteria() {
        global $DB;
        $criteria = array();

        if ($records = (array)$DB->get_records('badge_criteria', array('badgeid' => $this->id))) {
            foreach ($records as $record) {
                $criteria[$record->criteriatype] = award_criteria::build((array)$record);
            }
        }

        return $criteria;
    }

    /**
     * Get aggregation method for badge criteria
     *
     * @param int $criteriatype If none supplied, get overall aggregation method (optional)
     * @return int One of BADGE_CRITERIA_AGGREGATION_ALL or BADGE_CRITERIA_AGGREGATION_ANY
     */
    public function get_aggregation_method($criteriatype = 0) {
        global $DB;
        $params = array('badgeid' => $this->id, 'criteriatype' => $criteriatype);
        $aggregation = $DB->get_field('badge_criteria', 'method', $params, IGNORE_MULTIPLE);

        if (!$aggregation) {
            return BADGE_CRITERIA_AGGREGATION_ALL;
        }

        return $aggregation;
    }

    /**
     * Checks if badge has expiry period or date set up.
     *
     * @return bool A status indicating badge can expire
     */
    public function can_expire() {
        if ($this->expireperiod || $this->expiredate) {
            return true;
        }
        return false;
    }

    /**
     * Calculates badge expiry date based on either expirydate or expiryperiod.
     *
     * @param int $timestamp Time of badge issue
     * @return int A timestamp
     */
    public function calculate_expiry($timestamp) {
        $expiry = null;

        if (isset($this->expiredate)) {
            $expiry = $this->expiredate;
        } else if (isset($this->expireperiod)) {
            $expiry = $timestamp + $this->expireperiod;
        }

        return $expiry;
    }

    /**
     * Checks if badge has manual award criteria set.
     *
     * @return bool A status indicating badge can be awarded manually
     */
    public function has_manual_award_criteria() {
        foreach ($this->criteria as $criterion) {
            if ($criterion->criteriatype == BADGE_CRITERIA_TYPE_MANUAL) {
                return true;
            }
        }
        return false;
    }

    /**
     * Fully deletes the badge or marks it as archived.
     *
     * @param $archive bool Achive a badge without actual deleting of any data.
     */
    public function delete($archive = true) {
        global $DB;

        if ($archive) {
            $this->status = BADGE_STATUS_ARCHIVED;
            $this->save();
            return;
        }

        $fs = get_file_storage();

        // Remove all issued badge image files and badge awards.
        // Cannot bulk remove area files here because they are issued in user context.
        $awards = $this->get_awards();
        foreach ($awards as $award) {
            $usercontext = context_user::instance($award->userid);
            $fs->delete_area_files($usercontext->id, 'badges', 'userbadge', $this->id);
        }
        $DB->delete_records('badge_issued', array('badgeid' => $this->id));

        // Remove all badge criteria.
        $criteria = $this->get_criteria();
        foreach ($criteria as $criterion) {
            $criterion->delete();
        }

        // Delete badge images.
        $badgecontext = $this->get_context();
        $fs->delete_area_files($badgecontext->id, 'badges', 'badgeimage', $this->id);

        // Finally, remove badge itself.
        $DB->delete_records('badge', array('id' => $this->id));
    }
}

/**
 * Sends notifications to users about awarded badges.
 *
 * @param badge $badge Badge that was issued
 * @param int $userid Recipient ID
 * @param string $issued Unique hash of an issued badge
 * @param string $filepathhash File path hash of an issued badge for attachments
 */
function badges_notify_badge_award(badge $badge, $userid, $issued, $filepathhash) {
    global $CFG, $DB;

    $admin = get_admin();
    $userfrom = new stdClass();
    $userfrom->id = $admin->id;
    $userfrom->email = !empty($CFG->badges_defaultissuercontact) ? $CFG->badges_defaultissuercontact : $admin->email;
    foreach (get_all_user_name_fields() as $addname) {
        $userfrom->$addname = !empty($CFG->badges_defaultissuername) ? '' : $admin->$addname;
    }
    $userfrom->firstname = !empty($CFG->badges_defaultissuername) ? $CFG->badges_defaultissuername : $admin->firstname;
    $userfrom->maildisplay = true;

    $issuedlink = html_writer::link(new moodle_url('/badges/badge.php', array('hash' => $issued)), $badge->name);
    $userto = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

    $params = new stdClass();
    $params->badgename = $badge->name;
    $params->username = fullname($userto);
    $params->badgelink = $issuedlink;
    $message = badge_message_from_template($badge->message, $params);
    $plaintext = html_to_text($message);

    // Notify recipient.
    $eventdata = new stdClass();
    $eventdata->component         = 'moodle';
    $eventdata->name              = 'badgerecipientnotice';
    $eventdata->userfrom          = $userfrom;
    $eventdata->userto            = $userto;
    $eventdata->notification      = 1;
    $eventdata->subject           = $badge->messagesubject;
    $eventdata->fullmessage       = $plaintext;
    $eventdata->fullmessageformat = FORMAT_HTML;
    $eventdata->fullmessagehtml   = $message;
    $eventdata->smallmessage      = '';

    // Attach badge image if possible.
    if (!empty($CFG->allowattachments) && $badge->attachment && is_string($filepathhash)) {
        $fs = get_file_storage();
        $file = $fs->get_file_by_hash($filepathhash);
        $eventdata->attachment = $file;
        $eventdata->attachname = str_replace(' ', '_', $badge->name) . ".png";

        message_send($eventdata);
    } else {
        message_send($eventdata);
    }

    // Notify badge creator about the award if they receive notifications every time.
    if ($badge->notification == 1) {
        $userfrom = core_user::get_noreply_user();
        $userfrom->maildisplay = true;

        $creator = $DB->get_record('user', array('id' => $badge->usercreated), '*', MUST_EXIST);
        $a = new stdClass();
        $a->user = fullname($userto);
        $a->link = $issuedlink;
        $creatormessage = get_string('creatorbody', 'badges', $a);
        $creatorsubject = get_string('creatorsubject', 'badges', $badge->name);

        $eventdata = new stdClass();
        $eventdata->component         = 'moodle';
        $eventdata->name              = 'badgecreatornotice';
        $eventdata->userfrom          = $userfrom;
        $eventdata->userto            = $creator;
        $eventdata->notification      = 1;
        $eventdata->subject           = $creatorsubject;
        $eventdata->fullmessage       = html_to_text($creatormessage);
        $eventdata->fullmessageformat = FORMAT_HTML;
        $eventdata->fullmessagehtml   = $creatormessage;
        $eventdata->smallmessage      = '';

        message_send($eventdata);
        $DB->set_field('badge_issued', 'issuernotified', time(), array('badgeid' => $badge->id, 'userid' => $userid));
    }
}

/**
 * Caclulates date for the next message digest to badge creators.
 *
 * @param in $schedule Type of message schedule BADGE_MESSAGE_DAILY|BADGE_MESSAGE_WEEKLY|BADGE_MESSAGE_MONTHLY.
 * @return int Timestamp for next cron
 */
function badges_calculate_message_schedule($schedule) {
    $nextcron = 0;

    switch ($schedule) {
        case BADGE_MESSAGE_DAILY:
            $nextcron = time() + 60 * 60 * 24;
            break;
        case BADGE_MESSAGE_WEEKLY:
            $nextcron = time() + 60 * 60 * 24 * 7;
            break;
        case BADGE_MESSAGE_MONTHLY:
            $nextcron = time() + 60 * 60 * 24 * 7 * 30;
            break;
    }

    return $nextcron;
}

/**
 * Replaces variables in a message template and returns text ready to be emailed to a user.
 *
 * @param string $message Message body.
 * @return string Message with replaced values
 */
function badge_message_from_template($message, $params) {
    $msg = $message;
    foreach ($params as $key => $value) {
        $msg = str_replace("%$key%", $value, $msg);
    }

    return $msg;
}

/**
 * Get all badges.
 *
 * @param int Type of badges to return
 * @param int Course ID for course badges
 * @param string $sort An SQL field to sort by
 * @param string $dir The sort direction ASC|DESC
 * @param int $page The page or records to return
 * @param int $perpage The number of records to return per page
 * @param int $user User specific search
 * @return array $badge Array of records matching criteria
 */
function badges_get_badges($type, $courseid = 0, $sort = '', $dir = '', $page = 0, $perpage = BADGE_PERPAGE, $user = 0) {
    global $DB;
    $records = array();
    $params = array();
    $where = "b.status != :deleted AND b.type = :type ";
    $params['deleted'] = BADGE_STATUS_ARCHIVED;

    $userfields = array('b.id, b.name, b.status');
    $usersql = "";
    if ($user != 0) {
        $userfields[] = 'bi.dateissued';
        $userfields[] = 'bi.uniquehash';
        $usersql = " LEFT JOIN {badge_issued} bi ON b.id = bi.badgeid AND bi.userid = :userid ";
        $params['userid'] = $user;
        $where .= " AND (b.status = 1 OR b.status = 3) ";
    }
    $fields = implode(', ', $userfields);

    if ($courseid != 0 ) {
        $where .= "AND b.courseid = :courseid ";
        $params['courseid'] = $courseid;
    }

    $sorting = (($sort != '' && $dir != '') ? 'ORDER BY ' . $sort . ' ' . $dir : '');
    $params['type'] = $type;

    $sql = "SELECT $fields FROM {badge} b $usersql WHERE $where $sorting";
    $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

    $badges = array();
    foreach ($records as $r) {
        $badge = new badge($r->id);
        $badges[$r->id] = $badge;
        if ($user != 0) {
            $badges[$r->id]->dateissued = $r->dateissued;
            $badges[$r->id]->uniquehash = $r->uniquehash;
        } else {
            $badges[$r->id]->awards = $DB->count_records_sql('SELECT COUNT(b.userid)
                                        FROM {badge_issued} b INNER JOIN {user} u ON b.userid = u.id
                                        WHERE b.badgeid = :badgeid AND u.deleted = 0', array('badgeid' => $badge->id));
            $badges[$r->id]->statstring = $badge->get_status_name();
        }
    }
    return $badges;
}

/**
 * Get badges for a specific user.
 *
 * @param int $userid User ID
 * @param int $courseid Badges earned by a user in a specific course
 * @param int $page The page or records to return
 * @param int $perpage The number of records to return per page
 * @param string $search A simple string to search for
 * @param bool $onlypublic Return only public badges
 * @return array of badges ordered by decreasing date of issue
 */
function badges_get_user_badges($userid, $courseid = 0, $page = 0, $perpage = 0, $search = '', $onlypublic = false) {
    global $DB;

    $params = array(
        'userid' => $userid
    );
    $sql = 'SELECT
                bi.uniquehash,
                bi.dateissued,
                bi.dateexpire,
                bi.id as issuedid,
                bi.visible,
                u.email,
                b.*
            FROM
                {badge} b,
                {badge_issued} bi,
                {user} u
            WHERE b.id = bi.badgeid
                AND u.id = bi.userid
                AND bi.userid = :userid';

    if (!empty($search)) {
        $sql .= ' AND (' . $DB->sql_like('b.name', ':search', false) . ') ';
        $params['search'] = '%'.$DB->sql_like_escape($search).'%';
    }
    if ($onlypublic) {
        $sql .= ' AND (bi.visible = 1) ';
    }

    if ($courseid != 0) {
        $sql .= ' AND (b.courseid = :courseid) ';
        $params['courseid'] = $courseid;
    }
    $sql .= ' ORDER BY bi.dateissued DESC';
    $badges = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

    return $badges;
}

/**
 * Extends the course administration navigation with the Badges page
 *
 * @param navigation_node $coursenode
 * @param object $course
 */
function badges_add_course_navigation(navigation_node $coursenode, stdClass $course) {
    global $CFG, $SITE;

    $coursecontext = context_course::instance($course->id);
    $isfrontpage = (!$coursecontext || $course->id == $SITE->id);
    $canmanage = has_any_capability(array('moodle/badges:viewawarded',
                                          'moodle/badges:createbadge',
                                          'moodle/badges:awardbadge',
                                          'moodle/badges:configurecriteria',
                                          'moodle/badges:configuremessages',
                                          'moodle/badges:configuredetails',
                                          'moodle/badges:deletebadge'), $coursecontext);

    if (!empty($CFG->enablebadges) && !empty($CFG->badges_allowcoursebadges) && !$isfrontpage && $canmanage) {
        $coursenode->add(get_string('coursebadges', 'badges'), null,
                navigation_node::TYPE_CONTAINER, null, 'coursebadges',
                new pix_icon('i/badge', get_string('coursebadges', 'badges')));

        $url = new moodle_url('/badges/index.php', array('type' => BADGE_TYPE_COURSE, 'id' => $course->id));

        $coursenode->get('coursebadges')->add(get_string('managebadges', 'badges'), $url,
            navigation_node::TYPE_SETTING, null, 'coursebadges');

        if (has_capability('moodle/badges:createbadge', $coursecontext)) {
            $url = new moodle_url('/badges/newbadge.php', array('type' => BADGE_TYPE_COURSE, 'id' => $course->id));

            $coursenode->get('coursebadges')->add(get_string('newbadge', 'badges'), $url,
                    navigation_node::TYPE_SETTING, null, 'newbadge');
        }
    }
}

/**
 * Triggered when badge is manually awarded.
 *
 * @param   object      $data
 * @return  boolean
 */
function badges_award_handle_manual_criteria_review(stdClass $data) {
    $criteria = $data->crit;
    $userid = $data->userid;
    $badge = new badge($criteria->badgeid);

    if (!$badge->is_active() || $badge->is_issued($userid)) {
        return true;
    }

    if ($criteria->review($userid)) {
        $criteria->mark_complete($userid);

        if ($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($userid)) {
            $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($userid);
            $badge->issue($userid);
        }
    }

    return true;
}

/**
 * Process badge image from form data
 *
 * @param badge $badge Badge object
 * @param string $iconfile Original file
 */
function badges_process_badge_image(badge $badge, $iconfile) {
    global $CFG, $USER;
    require_once($CFG->libdir. '/gdlib.php');

    if (!empty($CFG->gdversion)) {
        process_new_icon($badge->get_context(), 'badges', 'badgeimage', $badge->id, $iconfile);
        @unlink($iconfile);

        // Clean up file draft area after badge image has been saved.
        $context = context_user::instance($USER->id, MUST_EXIST);
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'user', 'draft');
    }
}

/**
 * Print badge image.
 *
 * @param badge $badge Badge object
 * @param stdClass $context
 * @param string $size
 */
function print_badge_image(badge $badge, stdClass $context, $size = 'small') {
    $fsize = ($size == 'small') ? 'f2' : 'f1';

    $imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', $fsize, false);
    // Appending a random parameter to image link to forse browser reload the image.
    $imageurl->param('refresh', rand(1, 10000));
    $attributes = array('src' => $imageurl, 'alt' => s($badge->name), 'class' => 'activatebadge');

    return html_writer::empty_tag('img', $attributes);
}

/**
 * Bake issued badge.
 *
 * @param string $hash Unique hash of an issued badge.
 * @param int $badgeid ID of the original badge.
 * @param int $userid ID of badge recipient (optional).
 * @param boolean $pathhash Return file pathhash instead of image url (optional).
 * @return string|url Returns either new file path hash or new file URL
 */
function badges_bake($hash, $badgeid, $userid = 0, $pathhash = false) {
    global $CFG, $USER;
    require_once(dirname(dirname(__FILE__)) . '/badges/lib/bakerlib.php');

    $badge = new badge($badgeid);
    $badge_context = $badge->get_context();
    $userid = ($userid) ? $userid : $USER->id;
    $user_context = context_user::instance($userid);

    $fs = get_file_storage();
    if (!$fs->file_exists($user_context->id, 'badges', 'userbadge', $badge->id, '/', $hash . '.png')) {
        if ($file = $fs->get_file($badge_context->id, 'badges', 'badgeimage', $badge->id, '/', 'f1.png')) {
            $contents = $file->get_content();

            $filehandler = new PNG_MetaDataHandler($contents);
            $assertion = new moodle_url('/badges/assertion.php', array('b' => $hash));
            if ($filehandler->check_chunks("tEXt", "openbadges")) {
                // Add assertion URL tExt chunk.
                $newcontents = $filehandler->add_chunks("tEXt", "openbadges", $assertion->out(false));
                $fileinfo = array(
                        'contextid' => $user_context->id,
                        'component' => 'badges',
                        'filearea' => 'userbadge',
                        'itemid' => $badge->id,
                        'filepath' => '/',
                        'filename' => $hash . '.png',
                );

                // Create a file with added contents.
                $newfile = $fs->create_file_from_string($fileinfo, $newcontents);
                if ($pathhash) {
                    return $newfile->get_pathnamehash();
                }
            }
        } else {
            debugging('Error baking badge image!', DEBUG_DEVELOPER);
            return;
        }
    }

    // If file exists and we just need its path hash, return it.
    if ($pathhash) {
        $file = $fs->get_file($user_context->id, 'badges', 'userbadge', $badge->id, '/', $hash . '.png');
        return $file->get_pathnamehash();
    }

    $fileurl = moodle_url::make_pluginfile_url($user_context->id, 'badges', 'userbadge', $badge->id, '/', $hash, true);
    return $fileurl;
}

/**
 * Returns external backpack settings and badges from this backpack.
 *
 * This function first checks if badges for the user are cached and
 * tries to retrieve them from the cache. Otherwise, badges are obtained
 * through curl request to the backpack.
 *
 * @param int $userid Backpack user ID.
 * @param boolean $refresh Refresh badges collection in cache.
 * @return null|object Returns null is there is no backpack or object with backpack settings.
 */
function get_backpack_settings($userid, $refresh = false) {
    global $DB;
    require_once(dirname(dirname(__FILE__)) . '/badges/lib/backpacklib.php');

    // Try to get badges from cache first.
    $badgescache = cache::make('core', 'externalbadges');
    $out = $badgescache->get($userid);
    if ($out !== false && !$refresh) {
        return $out;
    }
    // Get badges through curl request to the backpack.
    $record = $DB->get_record('badge_backpack', array('userid' => $userid));
    if ($record) {
        $backpack = new OpenBadgesBackpackHandler($record);
        $out = new stdClass();
        $out->backpackurl = $backpack->get_url();

        if ($collections = $DB->get_records('badge_external', array('backpackid' => $record->id))) {
            $out->totalcollections = count($collections);
            $out->totalbadges = 0;
            $out->badges = array();
            foreach ($collections as $collection) {
                $badges = $backpack->get_badges($collection->collectionid);
                if (isset($badges->badges)) {
                    $out->badges = array_merge($out->badges, $badges->badges);
                    $out->totalbadges += count($out->badges);
                } else {
                    $out->badges = array_merge($out->badges, array());
                }
            }
        } else {
            $out->totalbadges = 0;
            $out->totalcollections = 0;
        }

        $badgescache->set($userid, $out);
        return $out;
    }

    return null;
}

/**
 * Download all user badges in zip archive.
 *
 * @param int $userid ID of badge owner.
 */
function badges_download($userid) {
    global $CFG, $DB;
    $context = context_user::instance($userid);
    $records = $DB->get_records('badge_issued', array('userid' => $userid));

    // Get list of files to download.
    $fs = get_file_storage();
    $filelist = array();
    foreach ($records as $issued) {
        $badge = new badge($issued->badgeid);
        // Need to make image name user-readable and unique using filename safe characters.
        $name =  $badge->name . ' ' . userdate($issued->dateissued, '%d %b %Y') . ' ' . hash('crc32', $badge->id);
        $name = str_replace(' ', '_', $name);
        if ($file = $fs->get_file($context->id, 'badges', 'userbadge', $issued->badgeid, '/', $issued->uniquehash . '.png')) {
            $filelist[$name . '.png'] = $file;
        }
    }

    // Zip files and sent them to a user.
    $tempzip = tempnam($CFG->tempdir.'/', 'mybadges');
    $zipper = new zip_packer();
    if ($zipper->archive_to_pathname($filelist, $tempzip)) {
        send_temp_file($tempzip, 'badges.zip');
    } else {
        debugging("Problems with archiving the files.");
    }
}

/**
 * Print badges on user profile page.
 *
 * @param int $userid User ID.
 * @param int $courseid Course if we need to filter badges (optional).
 */
function profile_display_badges($userid, $courseid = 0) {
    global $CFG, $PAGE, $USER, $SITE;
    require_once($CFG->dirroot . '/badges/renderer.php');

    // Determine context.
    if (isloggedin()) {
        $context = context_user::instance($USER->id);
    } else {
        $context = context_system::instance();
    }

    if ($USER->id == $userid || has_capability('moodle/badges:viewotherbadges', $context)) {
        $records = badges_get_user_badges($userid, $courseid, null, null, null, true);
        $renderer = new core_badges_renderer($PAGE, '');

        // Print local badges.
        if ($records) {
            $left = get_string('localbadgesp', 'badges', format_string($SITE->fullname));
            $right = $renderer->print_badges_list($records, $userid, true);
            echo html_writer::tag('dt', $left);
            echo html_writer::tag('dd', $right);
        }

        // Print external badges.
        if ($courseid == 0 && !empty($CFG->badges_allowexternalbackpack)) {
            $backpack = get_backpack_settings($userid);
            if (isset($backpack->totalbadges) && $backpack->totalbadges !== 0) {
                $left = get_string('externalbadgesp', 'badges');
                $right = $renderer->print_badges_list($backpack->badges, $userid, true, true);
                echo html_writer::tag('dt', $left);
                echo html_writer::tag('dd', $right);
            }
        }
    }
}

/**
 * Checks if badges can be pushed to external backpack.
 *
 * @return string Code of backpack accessibility status.
 */
function badges_check_backpack_accessibility() {
    global $CFG;
    include_once $CFG->libdir . '/filelib.php';

    // Using fake assertion url to check whether backpack can access the web site.
    $fakeassertion = new moodle_url('/badges/assertion.php', array('b' => 'abcd1234567890'));

    // Curl request to backpack baker.
    $curl = new curl();
    $options = array(
        'FRESH_CONNECT' => true,
        'RETURNTRANSFER' => true,
        'HEADER' => 0,
        'CONNECTTIMEOUT' => 2,
    );
    $location = 'http://' . BADGE_BACKPACKURL . '/baker';
    $out = $curl->get($location, array('assertion' => $fakeassertion->out(false)), $options);

    $data = json_decode($out);
    if (!empty($curl->error)) {
        return 'curl-request-timeout';
    } else {
        if (isset($data->code) && $data->code == 'http-unreachable') {
            return 'http-unreachable';
        } else {
            return 'available';
        }
    }

    return false;
}

/**
 * Checks if user has external backpack connected.
 *
 * @param int $userid ID of a user.
 * @return bool True|False whether backpack connection exists.
 */
function badges_user_has_backpack($userid) {
    global $DB;
    return $DB->record_exists('badge_backpack', array('userid' => $userid));
}

/**
 * Handles what happens to the course badges when a course is deleted.
 *
 * @param int $courseid course ID.
 * @return void.
 */
function badges_handle_course_deletion($courseid) {
    global $CFG, $DB;
    include_once $CFG->libdir . '/filelib.php';

    $systemcontext = context_system::instance();
    $coursecontext = context_course::instance($courseid);
    $fs = get_file_storage();

    // Move badges images to the system context.
    $fs->move_area_files_to_new_context($coursecontext->id, $systemcontext->id, 'badges', 'badgeimage');

    // Get all course badges.
    $badges = $DB->get_records('badge', array('type' => BADGE_TYPE_COURSE, 'courseid' => $courseid));
    foreach ($badges as $badge) {
        // Archive badges in this course.
        $toupdate = new stdClass();
        $toupdate->id = $badge->id;
        $toupdate->type = BADGE_TYPE_SITE;
        $toupdate->courseid = null;
        $toupdate->status = BADGE_STATUS_ARCHIVED;
        $DB->update_record('badge', $toupdate);
    }
}

/**
 * Loads JS files required for backpack support.
 *
 * @uses   $CFG, $PAGE
 * @return void
 */
function badges_setup_backpack_js() {
    global $CFG, $PAGE;
    if (!empty($CFG->badges_allowexternalbackpack)) {
        $PAGE->requires->string_for_js('error:backpackproblem', 'badges');
        $protocol = (is_https()) ? 'https://' : 'http://';
        $PAGE->requires->js(new moodle_url($protocol . BADGE_BACKPACKURL . '/issuer.js'), true);
        $PAGE->requires->js('/badges/backpack.js', true);
    }
}
