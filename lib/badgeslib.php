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
 * URL of backpack. Custom ones can be added.
 */
define('BADGRIO_BACKPACKAPIURL', 'https://api.badgr.io/v2');
define('BADGRIO_BACKPACKWEBURL', 'https://badgr.io');

/*
 * @deprecated since 3.9 (MDL-66357).
 */
define('BADGE_BACKPACKAPIURL', 'https://backpack.openbadges.org');
define('BADGE_BACKPACKWEBURL', 'https://backpack.openbadges.org');

/*
 * Open Badges specifications.
 */
define('OPEN_BADGES_V1', 1);
define('OPEN_BADGES_V2', 2);
define('OPEN_BADGES_V2P1', 2.1);

/*
 * Only use for Open Badges 2.0 specification
 */
define('OPEN_BADGES_V2_CONTEXT', 'https://w3id.org/openbadges/v2');
define('OPEN_BADGES_V2_TYPE_ASSERTION', 'Assertion');
define('OPEN_BADGES_V2_TYPE_BADGE', 'BadgeClass');
define('OPEN_BADGES_V2_TYPE_ISSUER', 'Issuer');
define('OPEN_BADGES_V2_TYPE_ENDORSEMENT', 'Endorsement');
define('OPEN_BADGES_V2_TYPE_AUTHOR', 'Author');

define('BACKPACK_MOVE_UP', -1);
define('BACKPACK_MOVE_DOWN', 1);

// Global badge class has been moved to the component namespace.
class_alias('\core_badges\badge', 'badge');

/**
 * Sends notifications to users about awarded badges.
 *
 * @param \core_badges\badge $badge Badge that was issued
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
    foreach (\core_user\fields::get_name_fields() as $addname) {
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
    $eventdata = new \core\message\message();
    $eventdata->courseid          = is_null($badge->courseid) ? SITEID : $badge->courseid; // Profile/site come with no courseid.
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
    $eventdata->customdata        = [
        'notificationiconurl' => moodle_url::make_pluginfile_url(
            $badge->get_context()->id, 'badges', 'badgeimage', $badge->id, '/', 'f1')->out(),
        'hash' => $issued,
    ];

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

        $eventdata = new \core\message\message();
        $eventdata->courseid          = $badge->courseid;
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
        $eventdata->customdata        = [
            'notificationiconurl' => moodle_url::make_pluginfile_url(
                $badge->get_context()->id, 'badges', 'badgeimage', $badge->id, '/', 'f1')->out(),
            'hash' => $issued,
        ];

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
            $tomorrow = new DateTime("1 day", core_date::get_server_timezone_object());
            $nextcron = $tomorrow->getTimestamp();
            break;
        case BADGE_MESSAGE_WEEKLY:
            $nextweek = new DateTime("1 week", core_date::get_server_timezone_object());
            $nextcron = $nextweek->getTimestamp();
            break;
        case BADGE_MESSAGE_MONTHLY:
            $nextmonth = new DateTime("1 month", core_date::get_server_timezone_object());
            $nextcron = $nextmonth->getTimestamp();
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
    global $CFG, $DB;

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

    if (empty($CFG->badges_allowcoursebadges)) {
        $sql .= ' AND b.courseid IS NULL';
    } else if ($courseid != 0) {
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
        process_new_icon($badge->get_context(), 'badges', 'badgeimage', $badge->id, $iconfile, true);
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
    require_once(__DIR__ . '/../badges/lib/bakerlib.php');

    $badge = new badge($badgeid);
    $badge_context = $badge->get_context();
    $userid = ($userid) ? $userid : $USER->id;
    $user_context = context_user::instance($userid);

    $fs = get_file_storage();
    if (!$fs->file_exists($user_context->id, 'badges', 'userbadge', $badge->id, '/', $hash . '.png')) {
        if ($file = $fs->get_file($badge_context->id, 'badges', 'badgeimage', $badge->id, '/', 'f3.png')) {
            $contents = $file->get_content();

            $filehandler = new PNG_MetaDataHandler($contents);
            // For now, the site backpack OB version will be used as default.
            $obversion = badges_open_badges_backpack_api();
            $assertion = new core_badges_assertion($hash, $obversion);
            $assertionjson = json_encode($assertion->get_badge_assertion());
            if ($filehandler->check_chunks("iTXt", "openbadges")) {
                // Add assertion URL iTXt chunk.
                $newcontents = $filehandler->add_chunks("iTXt", "openbadges", $assertionjson);
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

    // Try to get badges from cache first.
    $badgescache = cache::make('core', 'externalbadges');
    $out = $badgescache->get($userid);
    if ($out !== false && !$refresh) {
        return $out;
    }
    // Get badges through curl request to the backpack.
    $record = $DB->get_record('badge_backpack', array('userid' => $userid));
    if ($record) {
        $sitebackpack = badges_get_site_backpack($record->externalbackpackid);
        $backpack = new \core_badges\backpack_api($sitebackpack, $record);
        $out = new stdClass();
        $out->backpackid = $sitebackpack->id;

        if ($collections = $DB->get_records('badge_external', array('backpackid' => $record->id))) {
            $out->totalcollections = count($collections);
            $out->totalbadges = 0;
            $out->badges = array();
            foreach ($collections as $collection) {
                $badges = $backpack->get_badges($collection, true);
                if (!empty($badges)) {
                    $out->badges = array_merge($out->badges, $badges);
                    $out->totalbadges += count($badges);
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
        $name = clean_param($name, PARAM_FILE);
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
        debugging("Problems with archiving the files.", DEBUG_DEVELOPER);
        die;
    }
}

/**
 * Checks if badges can be pushed to external backpack.
 *
 * @deprecated Since Moodle 3.11.
 * @return string Code of backpack accessibility status.
 */
function badges_check_backpack_accessibility() {
    // This method was used for OBv1.0. It can be deprecated because OBv1.0 support will be removed.
    // When this method will be removed, badges/ajax.php can be removed too (if it keeps containing only a call to it).
    debugging('badges_check_backpack_accessibility() can not be used any more, it was only used for OBv1.0', DEBUG_DEVELOPER);

    return 'curl-request-timeout';
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
 * @deprecated Since Moodle 3.11.
 * @return void
 */
function badges_setup_backpack_js() {
    // This method was used for OBv1.0. It can be deprecated because OBv1.0 support will be removed.
    debugging('badges_setup_backpack_js() can not be used any more, it was only used for OBv1.0.', DEBUG_DEVELOPER);
}

/**
 * No js files are required for backpack support.
 * This only exists to directly support the custom V1 backpack api.
 *
 * @deprecated Since Moodle 3.11.
 * @param boolean $checksite Call check site function.
 * @return void
 */
function badges_local_backpack_js($checksite = false) {
    // This method was used for OBv1.0. It can be deprecated because OBv1.0 support will be removed.
    debugging('badges_local_backpack_js() can not be used any more, it was only used for OBv1.0.', DEBUG_DEVELOPER);
}

/**
 * Create the site backpack with this data.
 *
 * @param stdClass $data The new backpack data.
 * @return boolean
 */
function badges_create_site_backpack($data) {
    global $DB;
    $context = context_system::instance();
    require_capability('moodle/badges:manageglobalsettings', $context);

    $max = $DB->get_field_sql('SELECT MAX(sortorder) FROM {badge_external_backpack}');
    $data->sortorder = $max + 1;

    return badges_save_external_backpack($data);
}

/**
 * Update the backpack with this id.
 *
 * @param integer $id The backpack to edit
 * @param stdClass $data The new backpack data.
 * @return boolean
 */
function badges_update_site_backpack($id, $data) {
    global $DB;
    $context = context_system::instance();
    require_capability('moodle/badges:manageglobalsettings', $context);

    if ($backpack = badges_get_site_backpack($id)) {
        $data->id = $id;
        return badges_save_external_backpack($data);
    }
    return false;
}


/**
 * Delete the backpack with this id.
 *
 * @param integer $id The backpack to delete.
 * @return boolean
 */
function badges_delete_site_backpack($id) {
    global $DB;

    $context = context_system::instance();
    require_capability('moodle/badges:manageglobalsettings', $context);

    // Only remove site backpack if it's not the default one.
    $defaultbackpack = badges_get_site_primary_backpack();
    if ($defaultbackpack->id != $id && $DB->record_exists('badge_external_backpack', ['id' => $id])) {
        $transaction = $DB->start_delegated_transaction();

        // Remove connections for users to this backpack.
        $sql = "SELECT DISTINCT bb.id
                  FROM {badge_backpack} bb
                 WHERE bb.externalbackpackid = :backpackid";
        $params = ['backpackid' => $id];
        $userbackpacks = $DB->get_fieldset_sql($sql, $params);
        if ($userbackpacks) {
            // Delete user external collections references to this backpack.
            list($insql, $params) = $DB->get_in_or_equal($userbackpacks);
            $DB->delete_records_select('badge_external', "backpackid $insql", $params);
        }
        $DB->delete_records('badge_backpack', ['externalbackpackid' => $id]);

        // Delete backpack entry.
        $result = $DB->delete_records('badge_external_backpack', ['id' => $id]);

        $transaction->allow_commit();

        return $result;
    }

    return false;
}

/**
 * Perform the actual create/update of external bakpacks. Any checks on the validity of the id will need to be
 * performed before it reaches this function.
 *
 * @param stdClass $data The backpack data we are updating/inserting
 * @return int Returns the id of the new/updated record
 */
function badges_save_external_backpack(stdClass $data) {
    global $DB;
    $backpack = new stdClass();

    $backpack->apiversion = $data->apiversion;
    $backpack->backpackweburl = $data->backpackweburl;
    $backpack->backpackapiurl = $data->backpackapiurl;
    $backpack->oauth2_issuerid = $data->oauth2_issuerid ?? '';
    if (isset($data->sortorder)) {
        $backpack->sortorder = $data->sortorder;
    }

    if (empty($data->id)) {
        $backpack->id = $DB->insert_record('badge_external_backpack', $backpack);
    } else {
        $backpack->id = $data->id;
        $DB->update_record('badge_external_backpack', $backpack);
    }
    $data->externalbackpackid = $backpack->id;

    unset($data->id);
    badges_save_backpack_credentials($data);
    return $data->externalbackpackid;
}

/**
 * Create a backpack with the provided details. Stores the auth details of the backpack
 *
 * @param stdClass $data Backpack specific data.
 * @return int The id of the external backpack that the credentials correspond to
 */
function badges_save_backpack_credentials(stdClass $data) {
    global $DB;

    if (isset($data->backpackemail) && isset($data->password)) {
        $backpack = new stdClass();

        $backpack->email = $data->backpackemail;
        $backpack->password = !empty($data->password) ? $data->password : '';
        $backpack->externalbackpackid = $data->externalbackpackid;
        $backpack->userid = $data->userid ?? 0;
        $backpack->backpackuid = $data->backpackuid ?? 0;
        $backpack->autosync = $data->autosync ?? 0;

        if (!empty($data->badgebackpack)) {
            $backpack->id = $data->badgebackpack;
        } else if (!empty($data->id)) {
            $backpack->id = $data->id;
        }

        if (empty($backpack->id)) {
            $backpack->id = $DB->insert_record('badge_backpack', $backpack);
        } else {
            $DB->update_record('badge_backpack', $backpack);
        }

        return $backpack->externalbackpackid;
    }

    return $data->externalbackpackid ?? 0;
}

/**
 * Is any backpack enabled that supports open badges V1?
 * @param int|null $backpackid Check the version of the given id OR if null the sitewide backpack
 * @return boolean
 */
function badges_open_badges_backpack_api(?int $backpackid = null) {
    if (!$backpackid) {
        $backpack = badges_get_site_primary_backpack();
    } else {
        $backpack = badges_get_site_backpack($backpackid);
    }

    if (empty($backpack->apiversion)) {
        return OPEN_BADGES_V2;
    }
    return $backpack->apiversion;
}

/**
 * Get a site backpacks by id for a particular user or site (if userid is 0)
 *
 * @param int $id The backpack id.
 * @param int $userid The owner of the backpack, 0 if it's a sitewide backpack else a user's site backpack
 * @return stdClass
 */
function badges_get_site_backpack($id, int $userid = 0) {
    global $DB;

    $sql = "SELECT beb.*, bb.id AS badgebackpack, bb.password, bb.email AS backpackemail
              FROM {badge_external_backpack} beb
         LEFT JOIN {badge_backpack} bb ON bb.externalbackpackid = beb.id AND bb.userid=:userid
             WHERE beb.id=:id";

    return $DB->get_record_sql($sql, ['id' => $id, 'userid' => $userid]);
}

/**
 * Get the user backpack for the currently logged in user OR the provided user
 *
 * @param int|null $userid The user whose backpack you're requesting for. If null, get the logged in user's backpack
 * @return mixed The user's backpack or none.
 * @throws dml_exception
 */
function badges_get_user_backpack(?int $userid = 0) {
    global $DB;

    if (!$userid) {
        global $USER;
        $userid = $USER->id;
    }

    $sql = "SELECT beb.*, bb.id AS badgebackpack, bb.password, bb.email AS backpackemail
              FROM {badge_external_backpack} beb
              JOIN {badge_backpack} bb ON bb.externalbackpackid = beb.id AND bb.userid=:userid";

    return $DB->get_record_sql($sql, ['userid' => $userid]);
}

/**
 * Get the primary backpack for the site
 *
 * @return stdClass
 */
function badges_get_site_primary_backpack() {
    global $DB;

    $sql = 'SELECT *
              FROM {badge_external_backpack}
             WHERE sortorder = (SELECT MIN(sortorder)
                                  FROM {badge_external_backpack} b2)';
    $firstbackpack = $DB->get_record_sql($sql, null, MUST_EXIST);

    return badges_get_site_backpack($firstbackpack->id);
}

/**
 * List the backpacks at site level.
 *
 * @return array(stdClass)
 */
function badges_get_site_backpacks() {
    global $DB;

    $defaultbackpack = badges_get_site_primary_backpack();
    $all = $DB->get_records('badge_external_backpack', null, 'sortorder ASC');
    foreach ($all as $key => $bp) {
        if ($bp->id == $defaultbackpack->id) {
            $all[$key]->sitebackpack = true;
        } else {
            $all[$key]->sitebackpack = false;
        }
    }

    return $all;
}

/**
 * Moves the backpack in the list one position up or down.
 *
 * @param int $backpackid The backpack identifier to be moved.
 * @param int $direction The direction (BACKPACK_MOVE_UP/BACKPACK_MOVE_DOWN) where to move the backpack.
 *
 * @throws \moodle_exception if attempting to use invalid direction value.
 */
function badges_change_sortorder_backpacks(int $backpackid, int $direction): void {
    global $DB;

    if ($direction != BACKPACK_MOVE_UP && $direction != BACKPACK_MOVE_DOWN) {
        throw new \coding_exception(
            'Must use a valid backpack API move direction constant (BACKPACK_MOVE_UP or BACKPACK_MOVE_DOWN)');
    }

    $backpacks = badges_get_site_backpacks();
    $backpacktoupdate = $backpacks[$backpackid];

    $currentsortorder = $backpacktoupdate->sortorder;
    $targetsortorder = $currentsortorder + $direction;
    if ($targetsortorder > 0 && $targetsortorder <= count($backpacks) ) {
        foreach ($backpacks as $backpack) {
            if ($backpack->sortorder == $targetsortorder) {
                $backpack->sortorder = $backpack->sortorder - $direction;
                $DB->update_record('badge_external_backpack', $backpack);
                break;
            }
        }
        $backpacktoupdate->sortorder = $targetsortorder;
        $DB->update_record('badge_external_backpack', $backpacktoupdate);
    }
}

/**
 * List the supported badges api versions.
 *
 * @return array(version)
 */
function badges_get_badge_api_versions() {
    return [
        (string)OPEN_BADGES_V1 => get_string('openbadgesv1', 'badges'),
        (string)OPEN_BADGES_V2 => get_string('openbadgesv2', 'badges'),
        (string)OPEN_BADGES_V2P1 => get_string('openbadgesv2p1', 'badges')
    ];
}

/**
 * Get the default issuer for a badge from this site.
 *
 * @return array
 */
function badges_get_default_issuer() {
    global $CFG, $SITE;

    $sitebackpack = badges_get_site_primary_backpack();
    $issuer = array();
    $issuerurl = new moodle_url('/');
    $issuer['name'] = $CFG->badges_defaultissuername;
    if (empty($issuer['name'])) {
        $issuer['name'] = $SITE->fullname ? $SITE->fullname : $SITE->shortname;
    }
    $issuer['url'] = $issuerurl->out(false);
    $issuer['email'] = $sitebackpack->backpackemail ?: $CFG->badges_defaultissuercontact;
    $issuer['@context'] = OPEN_BADGES_V2_CONTEXT;
    $issuerid = new moodle_url('/badges/issuer_json.php');
    $issuer['id'] = $issuerid->out(false);
    $issuer['type'] = OPEN_BADGES_V2_TYPE_ISSUER;
    return $issuer;
}

/**
 * Disconnect from the user backpack by deleting the user preferences.
 *
 * @param integer $userid The user to diconnect.
 * @return boolean
 */
function badges_disconnect_user_backpack($userid) {
    global $USER;

    // We can only change backpack settings for our own real backpack.
    if ($USER->id != $userid ||
            \core\session\manager::is_loggedinas()) {

        return false;
    }

    unset_user_preference('badges_email_verify_secret');
    unset_user_preference('badges_email_verify_address');
    unset_user_preference('badges_email_verify_backpackid');
    unset_user_preference('badges_email_verify_password');

    return true;
}

/**
 * Used to remember which objects we connected with a backpack before.
 *
 * @param integer $sitebackpackid The site backpack to connect to.
 * @param string $type The type of this remote object.
 * @param string $internalid The id for this object on the Moodle site.
 * @param string $param The param we need to return. Defaults to the externalid.
 * @return mixed The id or false if it doesn't exist.
 */
function badges_external_get_mapping($sitebackpackid, $type, $internalid, $param = 'externalid') {
    global $DB;
    // Return externalid if it exists.
    $params = [
        'sitebackpackid' => $sitebackpackid,
        'type' => $type,
        'internalid' => $internalid
    ];

    $record = $DB->get_record('badge_external_identifier', $params, $param, IGNORE_MISSING);
    if ($record) {
        return $record->$param;
    }
    return false;
}

/**
 * Save the info about which objects we connected with a backpack before.
 *
 * @param integer $sitebackpackid The site backpack to connect to.
 * @param string $type The type of this remote object.
 * @param string $internalid The id for this object on the Moodle site.
 * @param string $externalid The id of this object on the remote site.
 * @return boolean
 */
function badges_external_create_mapping($sitebackpackid, $type, $internalid, $externalid) {
    global $DB;

    $params = [
        'sitebackpackid' => $sitebackpackid,
        'type' => $type,
        'internalid' => $internalid,
        'externalid' => $externalid
    ];

    return $DB->insert_record('badge_external_identifier', $params);
}

/**
 * Delete all external mapping information for a backpack.
 *
 * @param integer $sitebackpackid The site backpack to connect to.
 * @return boolean
 */
function badges_external_delete_mappings($sitebackpackid) {
    global $DB;

    $params = ['sitebackpackid' => $sitebackpackid];

    return $DB->delete_records('badge_external_identifier', $params);
}

/**
 * Delete a specific external mapping information for a backpack.
 *
 * @param integer $sitebackpackid The site backpack to connect to.
 * @param string $type The type of this remote object.
 * @param string $internalid The id for this object on the Moodle site.
 * @return boolean
 */
function badges_external_delete_mapping($sitebackpackid, $type, $internalid) {
    global $DB;

    $params = [
        'sitebackpackid' => $sitebackpackid,
        'type' => $type,
        'internalid' => $internalid
    ];

    $DB->delete_record('badge_external_identifier', $params);
}

/**
 * Create and send a verification email to the email address supplied.
 *
 * Since we're not sending this email to a user, email_to_user can't be used
 * but this function borrows largely the code from that process.
 *
 * @param string $email the email address to send the verification email to.
 * @param int $backpackid the id of the backpack to connect to
 * @param string $backpackpassword the user entered password to connect to this backpack
 * @return true if the email was sent successfully, false otherwise.
 */
function badges_send_verification_email($email, $backpackid, $backpackpassword) {
    global $DB, $USER;

    // Store a user secret (badges_email_verify_secret) and the address (badges_email_verify_address) as users prefs.
    // The address will be used by edit_backpack_form for display during verification and to facilitate the resending
    // of verification emails to said address.
    $secret = random_string(15);
    set_user_preference('badges_email_verify_secret', $secret);
    set_user_preference('badges_email_verify_address', $email);
    set_user_preference('badges_email_verify_backpackid', $backpackid);
    set_user_preference('badges_email_verify_password', $backpackpassword);

    // To, from.
    $tempuser = $DB->get_record('user', array('id' => $USER->id), '*', MUST_EXIST);
    $tempuser->email = $email;
    $noreplyuser = core_user::get_noreply_user();

    // Generate the verification email body.
    $verificationurl = '/badges/backpackemailverify.php';
    $verificationurl = new moodle_url($verificationurl);
    $verificationpath = $verificationurl->out(false);

    $site = get_site();
    $args = new stdClass();
    $args->link = $verificationpath . '?data='. $secret;
    $args->sitename = $site->fullname;
    $args->admin = generate_email_signoff();

    $messagesubject = get_string('backpackemailverifyemailsubject', 'badges', $site->fullname);
    $messagetext = get_string('backpackemailverifyemailbody', 'badges', $args);
    $messagehtml = text_to_html($messagetext, false, false, true);

    return email_to_user($tempuser, $noreplyuser, $messagesubject, $messagetext, $messagehtml);
}

/**
 * Return all the enabled criteria types for this site.
 *
 * @param boolean $enabled
 * @return array
 */
function badges_list_criteria($enabled = true) {
    global $CFG;

    $types = array(
        BADGE_CRITERIA_TYPE_OVERALL    => 'overall',
        BADGE_CRITERIA_TYPE_ACTIVITY   => 'activity',
        BADGE_CRITERIA_TYPE_MANUAL     => 'manual',
        BADGE_CRITERIA_TYPE_SOCIAL     => 'social',
        BADGE_CRITERIA_TYPE_COURSE     => 'course',
        BADGE_CRITERIA_TYPE_COURSESET  => 'courseset',
        BADGE_CRITERIA_TYPE_PROFILE    => 'profile',
        BADGE_CRITERIA_TYPE_BADGE      => 'badge',
        BADGE_CRITERIA_TYPE_COHORT     => 'cohort',
        BADGE_CRITERIA_TYPE_COMPETENCY => 'competency',
    );
    if ($enabled) {
        foreach ($types as $key => $type) {
            $class = 'award_criteria_' . $type;
            $file = $CFG->dirroot . '/badges/criteria/' . $class . '.php';
            if (file_exists($file)) {
                require_once($file);

                if (!$class::is_enabled()) {
                    unset($types[$key]);
                }
            }
        }
    }
    return $types;
}

/**
 * Check if any badge has records for competencies.
 *
 * @param array $competencyids Array of competencies ids.
 * @return boolean Return true if competencies were found in any badge.
 */
function badge_award_criteria_competency_has_records_for_competencies($competencyids) {
    global $DB;

    list($insql, $params) = $DB->get_in_or_equal($competencyids, SQL_PARAMS_NAMED);

    $sql = "SELECT DISTINCT bc.badgeid
                FROM {badge_criteria} bc
                JOIN {badge_criteria_param} bcp ON bc.id = bcp.critid
                WHERE bc.criteriatype = :criteriatype AND bcp.value $insql";
    $params['criteriatype'] = BADGE_CRITERIA_TYPE_COMPETENCY;

    return $DB->record_exists_sql($sql, $params);
}

/**
 * Creates single message for all notification and sends it out
 *
 * @param object $badge A badge which is notified about.
 */
function badge_assemble_notification(stdClass $badge) {
    global $DB;

    $userfrom = core_user::get_noreply_user();
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
        $eventdata = new \core\message\message();
        $eventdata->courseid          = SITEID;
        $eventdata->component         = 'moodle';
        $eventdata->name              = 'badgecreatornotice';
        $eventdata->userfrom          = $userfrom;
        $eventdata->userto            = $creator;
        $eventdata->notification      = 1;
        $eventdata->subject           = $creatorsubject;
        $eventdata->fullmessage       = format_text_email($creatormessage, FORMAT_HTML);
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = $creatormessage;
        $eventdata->smallmessage      = $creatorsubject;

        message_send($eventdata);
    }
}

/**
 * Attempt to authenticate with the site backpack credentials and return an error
 * if the authentication fails. If external backpacks are not enabled, this will
 * not perform any test.
 *
 * @return string
 */
function badges_verify_site_backpack() {
    $defaultbackpack = badges_get_site_primary_backpack();
    return badges_verify_backpack($defaultbackpack->id);
}

/**
 * Attempt to authenticate with a backpack credentials and return an error
 * if the authentication fails.
 * If external backpacks are not enabled or the backpack version is different
 * from OBv2, this will not perform any test.
 *
 * @param int $backpackid Backpack identifier to verify.
 * @return string The result of the verification process.
 */
function badges_verify_backpack(int $backpackid) {
    global $OUTPUT, $CFG;

    if (empty($CFG->badges_allowexternalbackpack)) {
        return '';
    }

    $backpack = badges_get_site_backpack($backpackid);
    if (empty($backpack->apiversion) || ($backpack->apiversion == OPEN_BADGES_V2)) {
        $backpackapi = new \core_badges\backpack_api($backpack);

        // Clear any cached access tokens in the session.
        $backpackapi->clear_system_user_session();

        // Now attempt a login with these credentials.
        $result = $backpackapi->authenticate();
        if (empty($result) || !empty($result->error)) {
            $warning = $backpackapi->get_authentication_error();

            $params = ['id' => $backpack->id, 'action' => 'edit'];
            $backpackurl = (new moodle_url('/badges/backpacks.php', $params))->out(false);

            $message = get_string('sitebackpackwarning', 'badges', ['url' => $backpackurl, 'warning' => $warning]);
            $icon = $OUTPUT->pix_icon('i/warning', get_string('warning', 'moodle'));
            return $OUTPUT->container($icon . $message, 'text-danger');
        }
    }

    return '';
}

/**
 * Get OAuth2 services for the external backpack.
 *
 * @return array
 * @throws coding_exception
 */
function badges_get_oauth2_service_options() {
    global $DB;

    $issuers = core\oauth2\api::get_all_issuers();
    $options = ['' => 'None'];
    foreach ($issuers as $issuer) {
        $options[$issuer->get('id')] = $issuer->get('name');
    }

    return $options;
}

/**
 * Generate a public badgr URL that conforms to OBv2. This is done because badgr responses do not currently conform to
 * the spec.
 *
 * WARNING: This is an extremely hacky way of implementing this and should be removed once the standards are conformed to.
 *
 * @param stdClass $backpack The Badgr backpack we are pushing to
 * @param string $type The type of object we are dealing with either Issuer, Assertion OR Badge.
 * @param string $externalid The externalid as provided by the backpack
 * @return string The public URL to access Badgr objects
 */
function badges_generate_badgr_open_url($backpack, $type, $externalid) {
    if (badges_open_badges_backpack_api($backpack->id) == OPEN_BADGES_V2) {
        $entity = strtolower($type);
        if ($type == OPEN_BADGES_V2_TYPE_BADGE) {
            $entity = "badge";
        }
        $url = new moodle_url($backpack->backpackapiurl);
        return "{$url->get_scheme()}://{$url->get_host()}/public/{$entity}s/$externalid";

    }
}
