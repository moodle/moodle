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
 * Badge assertion library.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

namespace core_badges;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/badgeslib.php');

use context_system;
use context_course;
use context_user;
use moodle_exception;
use moodle_url;
use core_text;
use award_criteria;
use core_php_time_limit;
use html_writer;
use stdClass;

/**
 * Class that represents badge.
 *
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badge {
    /** @var int Badge id */
    public $id;

    /** @var string Badge name */
    public $name;

    /** @var string Badge description */
    public $description;

    /** @var integer Timestamp this badge was created */
    public $timecreated;

    /** @var integer Timestamp this badge was modified */
    public $timemodified;

    /** @var int The user who created this badge */
    public $usercreated;

    /** @var int The user who modified this badge */
    public $usermodified;

    /** @var string The name of the issuer of this badge */
    public $issuername;

    /** @var string The url of the issuer of this badge */
    public $issuerurl;

    /** @var string The email of the issuer of this badge */
    public $issuercontact;

    /** @var integer Timestamp this badge will expire */
    public $expiredate;

    /** @var integer Duration this badge is valid for */
    public $expireperiod;

    /** @var integer Site or course badge */
    public $type;

    /** @var integer The course this badge belongs to */
    public $courseid;

    /** @var string The message this badge includes. */
    public $message;

    /** @var string The subject of the message for this badge */
    public $messagesubject;

    /** @var int Is this badge image baked. */
    public $attachment;

    /** @var int Send a message when this badge is awarded. */
    public $notification;

    /** @var int Lifecycle status for this badge. */
    public $status = 0;

    /** @var int Timestamp to next run cron for this badge. */
    public $nextcron;

    /** @var int What backpack api version to use for this badge. */
    public $version;

    /** @var string What language is this badge written in. */
    public $language;

    /** @var string The author of the image for this badge. */
    public $imageauthorname;

    /** @var string The email of the author of the image for this badge. */
    public $imageauthoremail;

    /** @var string The url of the author of the image for this badge. */
    public $imageauthorurl;

    /** @var string The caption of the image for this badge. */
    public $imagecaption;

    /** @var array Badge criteria */
    public $criteria = array();

    /** @var int|null Total users which have the award. Called from badges_get_badges() */
    public $awards;

    /** @var string|null The name of badge status. Called from badges_get_badges() */
    public $statstring;

    /** @var int|null The date the badges were issued. Called from badges_get_badges() */
    public $dateissued;

    /** @var string|null Unique hash. Called from badges_get_badges() */
    public $uniquehash;

    /** @var string|null Message format. Called from file_prepare_standard_editor() */
    public $messageformat;

    /** @var array Message editor. Called from file_prepare_standard_editor() */
    public $message_editor = [];

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
            throw new moodle_exception('error:nosuchbadge', 'badges', '', $badgeid);
        }

        foreach ((array)$data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }

        if (badges_open_badges_backpack_api() != OPEN_BADGES_V1) {
            // For Open Badges 2 we need to use a single site issuer with no exceptions.
            $issuer = badges_get_default_issuer();
            $this->issuername = $issuer['name'];
            $this->issuercontact = $issuer['email'];
            $this->issuerurl = $issuer['url'];
        }

        $this->criteria = self::get_criteria();
    }

    /**
     * Use to get context instance of a badge.
     *
     * @return \context|void instance.
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
     *
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
     *
     * @return array
     */
    public function get_accepted_criteria() {
        global $CFG;
        $criteriatypes = array();

        if ($this->type == BADGE_TYPE_COURSE) {
            $criteriatypes = array(
                    BADGE_CRITERIA_TYPE_OVERALL,
                    BADGE_CRITERIA_TYPE_MANUAL,
                    BADGE_CRITERIA_TYPE_COURSE,
                    BADGE_CRITERIA_TYPE_BADGE,
                    BADGE_CRITERIA_TYPE_ACTIVITY,
                    BADGE_CRITERIA_TYPE_COMPETENCY
            );
        } else if ($this->type == BADGE_TYPE_SITE) {
            $criteriatypes = array(
                    BADGE_CRITERIA_TYPE_OVERALL,
                    BADGE_CRITERIA_TYPE_MANUAL,
                    BADGE_CRITERIA_TYPE_COURSESET,
                    BADGE_CRITERIA_TYPE_BADGE,
                    BADGE_CRITERIA_TYPE_PROFILE,
                    BADGE_CRITERIA_TYPE_COHORT,
                    BADGE_CRITERIA_TYPE_COMPETENCY
            );
        }
        $alltypes = badges_list_criteria();
        foreach ($criteriatypes as $index => $type) {
            if (!isset($alltypes[$type])) {
                unset($criteriatypes[$index]);
            }
        }

        return $criteriatypes;
    }

    /**
     * Save/update badge information in 'badge' table only.
     * Cannot be used for updating awards and criteria settings.
     *
     * @return boolean Returns true on success.
     */
    public function save() {
        global $DB;

        $fordb = new stdClass();
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
        }
        // TODO: We need to making it more simple.
        // Since the variables are not exist in the badge table,
        // unsetting them is a must to avoid errors.
        unset($fordb->criteria);
        unset($fordb->awards);
        unset($fordb->statstring);
        unset($fordb->dateissued);
        unset($fordb->uniquehash);
        unset($fordb->messageformat);
        unset($fordb->message_editor);

        $fordb->timemodified = time();
        if ($DB->update_record_raw('badge', $fordb)) {
            // Trigger event, badge updated.
            $eventparams = array('objectid' => $this->id, 'context' => $this->get_context());
            $event = \core\event\badge_updated::create($eventparams);
            $event->trigger();
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
        global $DB, $USER, $PAGE;

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
        $tags = $this->get_badge_tags();
        unset($fordb->id);

        if ($fordb->notification > 1) {
            $fordb->nextcron = badges_calculate_message_schedule($fordb->notification);
        }

        $criteria = $fordb->criteria;
        unset($fordb->criteria);

        if ($new = $DB->insert_record('badge', $fordb, true)) {
            $newbadge = new badge($new);
            // Copy badge tags.
            \core_tag_tag::set_item_tags('core_badges', 'badge', $newbadge->id, $this->get_context(), $tags);

            // Copy badge image.
            $fs = get_file_storage();
            if ($file = $fs->get_file($this->get_context()->id, 'badges', 'badgeimage', $this->id, '/', 'f3.png')) {
                if ($imagefile = $file->copy_content_to_temp()) {
                    badges_process_badge_image($newbadge, $imagefile);
                }
            }

            // Copy badge criteria.
            foreach ($this->criteria as $crit) {
                $crit->make_clone($new);
            }

            // Trigger event, badge duplicated.
            $eventparams = array('objectid' => $new, 'context' => $PAGE->context);
            $event = \core\event\badge_duplicated::create($eventparams);
            $event->trigger();

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
     * @return boolean A status indicating badge is active
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
     * @return string
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
        if ($status == BADGE_STATUS_ACTIVE) {
            // Trigger event, badge enabled.
            $eventparams = array('objectid' => $this->id, 'context' => $this->get_context());
            $event = \core\event\badge_enabled::create($eventparams);
            $event->trigger();
        } else if ($status == BADGE_STATUS_INACTIVE) {
            // Trigger event, badge disabled.
            $eventparams = array('objectid' => $this->id, 'context' => $this->get_context());
            $event = \core\event\badge_disabled::create($eventparams);
            $event->trigger();
        }
    }

    /**
     * Checks if badges is locked.
     * Used in badge award and editing.
     *
     * @return boolean A status indicating badge is locked
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
     * @return boolean A status indicating badge has been awarded at least once
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
     * @param int $userid User to check
     * @return boolean
     */
    public function is_issued($userid) {
        global $DB;
        return $DB->record_exists('badge_issued', array('badgeid' => $this->id, 'userid' => $userid));
    }

    /**
     * Issue a badge to user.
     *
     * @param int $userid User who earned the badge
     * @param boolean $nobake Not baking actual badges (for testing purposes)
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
            // Trigger badge awarded event.
            $eventdata = array (
                'context' => $this->get_context(),
                'objectid' => $this->id,
                'relateduserid' => $userid,
                'other' => array('dateexpire' => $issued->dateexpire, 'badgeissuedid' => $result)
            );
            \core\event\badge_awarded::create($eventdata)->trigger();

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
                $earned = $DB->get_fieldset_select(
                    'badge_issued',
                    'userid AS id',
                    'badgeid = :badgeid',
                    array('badgeid' => $this->id)
                );

                $wheresql = '';
                $earnedparams = array();
                if (!empty($earned)) {
                    list($earnedsql, $earnedparams) = $DB->get_in_or_equal($earned, SQL_PARAMS_NAMED, 'u', false);
                    $wheresql = ' WHERE u.id ' . $earnedsql;
                }
                list($enrolledsql, $enrolledparams) = get_enrolled_sql($this->get_context(), 'moodle/badges:earnbadge', 0, true);
                $sql = "SELECT DISTINCT u.id
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
     * @return boolean A status indicating badge has at least one criterion
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
     * @return boolean A status indicating badge can expire
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
     * @return boolean A status indicating badge can be awarded manually
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
     * @param boolean $archive Achive a badge without actual deleting of any data.
     */
    public function delete($archive = true) {
        global $DB;

        if ($archive) {
            $this->status = BADGE_STATUS_ARCHIVED;
            $this->save();

            // Trigger event, badge archived.
            $eventparams = array('objectid' => $this->id, 'context' => $this->get_context());
            $event = \core\event\badge_archived::create($eventparams);
            $event->trigger();
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

        // Delete endorsements, competencies and related badges.
        $DB->delete_records('badge_endorsement', array('badgeid' => $this->id));
        $relatedsql = 'badgeid = :badgeid OR relatedbadgeid = :relatedbadgeid';
        $relatedparams = array(
            'badgeid' => $this->id,
            'relatedbadgeid' => $this->id
        );
        $DB->delete_records_select('badge_related', $relatedsql, $relatedparams);
        $DB->delete_records('badge_alignment', array('badgeid' => $this->id));

        // Delete all tags.
        \core_tag_tag::remove_all_item_tags('core_badges', 'badge', $this->id);

        // Finally, remove badge itself.
        $DB->delete_records('badge', array('id' => $this->id));

        // Trigger event, badge deleted.
        $eventparams = array('objectid' => $this->id,
            'context' => $this->get_context(),
            'other' => array('badgetype' => $this->type, 'courseid' => $this->courseid)
            );
        $event = \core\event\badge_deleted::create($eventparams);
        $event->trigger();
    }

    /**
     * Add multiple related badges.
     *
     * @param array $relatedids Id of badges.
     */
    public function add_related_badges($relatedids) {
        global $DB;
        $relatedbadges = array();
        foreach ($relatedids as $relatedid) {
            $relatedbadge = new stdClass();
            $relatedbadge->badgeid = $this->id;
            $relatedbadge->relatedbadgeid = $relatedid;
            $relatedbadges[] = $relatedbadge;
        }
        $DB->insert_records('badge_related', $relatedbadges);
    }

    /**
     * Delete an related badge.
     *
     * @param int $relatedid Id related badge.
     * @return boolean A status for delete an related badge.
     */
    public function delete_related_badge($relatedid) {
        global $DB;
        $sql = "(badgeid = :badgeid AND relatedbadgeid = :relatedid) OR " .
               "(badgeid = :relatedid2 AND relatedbadgeid = :badgeid2)";
        $params = ['badgeid' => $this->id, 'badgeid2' => $this->id, 'relatedid' => $relatedid, 'relatedid2' => $relatedid];
        return $DB->delete_records_select('badge_related', $sql, $params);
    }

    /**
     * Checks if badge has related badges.
     *
     * @return boolean A status related badge.
     */
    public function has_related() {
        global $DB;
        $sql = "SELECT DISTINCT b.id
                    FROM {badge_related} br
                    JOIN {badge} b ON (br.relatedbadgeid = b.id OR br.badgeid = b.id)
                   WHERE (br.badgeid = :badgeid OR br.relatedbadgeid = :badgeid2) AND b.id != :badgeid3";
        return $DB->record_exists_sql($sql, ['badgeid' => $this->id, 'badgeid2' => $this->id, 'badgeid3' => $this->id]);
    }

    /**
     * Get related badges of badge.
     *
     * @param boolean $activeonly Do not get the inactive badges when is true.
     * @return array Related badges information.
     */
    public function get_related_badges($activeonly = false) {
        global $DB;

        $params = array('badgeid' => $this->id, 'badgeid2' => $this->id, 'badgeid3' => $this->id);
        $query = "SELECT DISTINCT b.id, b.name, b.version, b.language, b.type
                    FROM {badge_related} br
                    JOIN {badge} b ON (br.relatedbadgeid = b.id OR br.badgeid = b.id)
                   WHERE (br.badgeid = :badgeid OR br.relatedbadgeid = :badgeid2) AND b.id != :badgeid3";
        if ($activeonly) {
            $query .= " AND b.status <> :status";
            $params['status'] = BADGE_STATUS_INACTIVE;
        }
        $relatedbadges = $DB->get_records_sql($query, $params);
        return $relatedbadges;
    }

    /**
     * Insert/update alignment information of badge.
     *
     * @param stdClass $alignment Data of a alignment.
     * @param int $alignmentid ID alignment.
     * @return bool|int A status/ID when insert or update data.
     */
    public function save_alignment($alignment, $alignmentid = 0) {
        global $DB;

        $record = $DB->record_exists('badge_alignment', array('id' => $alignmentid));
        if ($record) {
            $alignment->id = $alignmentid;
            return $DB->update_record('badge_alignment', $alignment);
        } else {
            return $DB->insert_record('badge_alignment', $alignment, true);
        }
    }

    /**
     * Delete a alignment of badge.
     *
     * @param int $alignmentid ID alignment.
     * @return boolean A status for delete a alignment.
     */
    public function delete_alignment($alignmentid) {
        global $DB;
        return $DB->delete_records('badge_alignment', array('badgeid' => $this->id, 'id' => $alignmentid));
    }

    /**
     * Get alignments of badge.
     *
     * @return array List content alignments.
     */
    public function get_alignments() {
        global $DB;
        return $DB->get_records('badge_alignment', array('badgeid' => $this->id));
    }

    /**
     * Insert/update Endorsement information of badge.
     *
     * @param stdClass $endorsement Data of an endorsement.
     * @return bool|int A status/ID when insert or update data.
     */
    public function save_endorsement($endorsement) {
        global $DB;
        $record = $DB->get_record('badge_endorsement', array('badgeid' => $this->id));
        if ($record) {
            $endorsement->id = $record->id;
            return $DB->update_record('badge_endorsement', $endorsement);
        } else {
            return $DB->insert_record('badge_endorsement', $endorsement, true);
        }
    }

    /**
     * Get endorsement of badge.
     *
     * @return array|stdClass Endorsement information.
     */
    public function get_endorsement() {
        global $DB;
        return $DB->get_record('badge_endorsement', array('badgeid' => $this->id));
    }

    /**
     * Markdown language support for criteria.
     *
     * @return string $output Markdown content to output.
     */
    public function markdown_badge_criteria() {
        $agg = $this->get_aggregation_methods();
        if (empty($this->criteria)) {
            return get_string('nocriteria', 'badges');
        }
        $overalldescr = '';
        $overall = $this->criteria[BADGE_CRITERIA_TYPE_OVERALL];
        if (!empty($overall->description)) {
                $overalldescr = format_text($overall->description, $overall->descriptionformat,
                    array('context' => $this->get_context())) . '\n';
        }
        // Get the condition string.
        if (count($this->criteria) == 2) {
            $condition = get_string('criteria_descr', 'badges');
        } else {
            $condition = get_string('criteria_descr_' . BADGE_CRITERIA_TYPE_OVERALL, 'badges',
                core_text::strtoupper($agg[$this->get_aggregation_method()]));
        }
        unset($this->criteria[BADGE_CRITERIA_TYPE_OVERALL]);
        $items = array();
        // If only one criterion left, make sure its description goe to the top.
        if (count($this->criteria) == 1) {
            $c = reset($this->criteria);
            if (!empty($c->description)) {
                $overalldescr = $c->description . '\n';
            }
            if (count($c->params) == 1) {
                $items[] = ' * ' . get_string('criteria_descr_single_' . $c->criteriatype, 'badges') .
                    $c->get_details();
            } else {
                $items[] = '* ' . get_string('criteria_descr_' . $c->criteriatype, 'badges',
                        core_text::strtoupper($agg[$this->get_aggregation_method($c->criteriatype)])) .
                    $c->get_details();
            }
        } else {
            foreach ($this->criteria as $type => $c) {
                $criteriadescr = '';
                if (!empty($c->description)) {
                    $criteriadescr = $c->description;
                }
                if (count($c->params) == 1) {
                    $items[] = ' * ' . get_string('criteria_descr_single_' . $type, 'badges') .
                        $c->get_details() . $criteriadescr;
                } else {
                    $items[] = '* ' . get_string('criteria_descr_' . $type, 'badges',
                            core_text::strtoupper($agg[$this->get_aggregation_method($type)])) .
                        $c->get_details() . $criteriadescr;
                }
            }
        }
        return strip_tags($overalldescr . $condition . html_writer::alist($items, array(), 'ul'));
    }

    /**
     * Define issuer information by format Open Badges specification version 2.
     *
     * @param int $obversion OB version to use.
     * @return array Issuer informations of the badge.
     */
    public function get_badge_issuer(?int $obversion = null) {
        global $DB;

        $issuer = [];
        if ($obversion == OPEN_BADGES_V1) {
            $data = $DB->get_record('badge', ['id' => $this->id]);
            $issuer['name'] = $data->issuername;
            $issuer['url'] = $data->issuerurl;
            $issuer['email'] = $data->issuercontact;
        } else {
            $issuer['name'] = $this->issuername;
            $issuer['url'] = $this->issuerurl;
            $issuer['email'] = $this->issuercontact;
            $issuer['@context'] = OPEN_BADGES_V2_CONTEXT;
            $issueridurl = new moodle_url('/badges/issuer_json.php', array('id' => $this->id));
            $issuer['id'] = $issueridurl->out(false);
            $issuer['type'] = OPEN_BADGES_V2_TYPE_ISSUER;
        }

        return $issuer;
    }

    /**
     * Get tags of badge.
     *
     * @return array Badge tags.
     */
    public function get_badge_tags(): array {
        return array_values(\core_tag_tag::get_item_tags_array('core_badges', 'badge', $this->id));
    }
}
