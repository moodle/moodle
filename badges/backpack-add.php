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
 * Optionally award a badge and redirect to the my badges page.
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

require_login();

$userbackpack = badges_get_user_backpack();
if (badges_open_badges_backpack_api($userbackpack->id) != OPEN_BADGES_V2) {
    throw new coding_exception('No backpacks support Open Badges V2.');
}

$id = required_param('hash', PARAM_ALPHANUM);

$PAGE->set_url('/badges/backpack-add.php', array('hash' => $id));
$PAGE->set_context(context_system::instance());
$output = $PAGE->get_renderer('core', 'badges');

$issuedbadge = new \core_badges\output\issued_badge($id);
if (!empty($issuedbadge->recipient->id)) {
    // The flow for issuing a badge is:
    // * Create issuer
    // * Create badge
    // * Create assertion (Award the badge!)

    // With the introduction OBv2.1 and MDL-65959 to allow cross region Badgr imports the above (old) procedure will
    // only be completely performed if both the site and user backpacks conform to the same apiversion.
    // Else we will attempt at pushing the assertion to the user's backpack. In this case, the id set against the assertion
    // has to be a publicly accessible resource.

    // Get the backpack.
    $badgeid = $issuedbadge->badgeid;
    $badge = new badge($badgeid);
    $backpack = $DB->get_record('badge_backpack', array('userid' => $USER->id));
    $userbackpack = badges_get_site_backpack($backpack->externalbackpackid, $USER->id);
    $assertion = new core_badges_assertion($id, OPEN_BADGES_V2);
    $assertiondata = $assertion->get_badge_assertion(false, false);
    $assertionid = $assertion->get_assertion_hash();
    $assertionentityid = $assertiondata['id'];
    $badgeadded = false;
    if (badges_open_badges_backpack_api() == OPEN_BADGES_V2) {
        $sitebackpack = badges_get_site_primary_backpack();
        $api = new \core_badges\backpack_api($sitebackpack);
        $response = $api->authenticate();

        // A numeric response indicates a valid successful authentication. Else an error object will be returned.
        if (is_numeric($response)) {
            // Create issuer.
            $issuer = $assertion->get_issuer();
            if (!($issuerentityid = badges_external_get_mapping($sitebackpack->id, OPEN_BADGES_V2_TYPE_ISSUER, $issuer['email']))) {
                $response = $api->put_issuer($issuer);
                if (!$response) {
                    throw new moodle_exception('invalidrequest', 'error');
                }
                $issuerentityid = $response->id;
                badges_external_create_mapping($sitebackpack->id, OPEN_BADGES_V2_TYPE_ISSUER, $issuer['email'],
                    $issuerentityid);
            }
            // Create badge.
            $badge = $assertion->get_badge_class(false);
            $badgeid = $assertion->get_badge_id();
            if (!($badgeentityid = badges_external_get_mapping($sitebackpack->id, OPEN_BADGES_V2_TYPE_BADGE, $badgeid))) {
                $response = $api->put_badgeclass($issuerentityid, $badge);
                if (!$response) {
                    throw new moodle_exception('invalidrequest', 'error');
                }
                $badgeentityid = $response->id;
                badges_external_create_mapping($sitebackpack->id, OPEN_BADGES_V2_TYPE_BADGE, $badgeid,
                    $badgeentityid);
            }

            // Create assertion (Award the badge!).
            $assertionentityid = badges_external_get_mapping(
                $sitebackpack->id,
                OPEN_BADGES_V2_TYPE_ASSERTION,
                $assertionid
            );

            if (!$assertionentityid && strpos($sitebackpack->backpackapiurl, 'badgr')) {
                $assertionentityid = badges_generate_badgr_open_url(
                    $sitebackpack,
                    OPEN_BADGES_V2_TYPE_ASSERTION,
                    $assertionentityid
                );
            }

            // Create an assertion for the recipient in the issuer's account.
            if (!$assertionentityid) {
                $response = $api->put_badgeclass_assertion($badgeentityid, $assertiondata);
                if (!$response) {
                    throw new moodle_exception('invalidrequest', 'error');
                }
                $assertionentityid = badges_generate_badgr_open_url($sitebackpack, OPEN_BADGES_V2_TYPE_ASSERTION, $response->id);
                $badgeadded = true;
                badges_external_create_mapping($sitebackpack->id, OPEN_BADGES_V2_TYPE_ASSERTION, $assertionid,
                    $response->id);
            } else {
                // An assertion already exists. Make sure it's up to date.
                $internalid = badges_external_get_mapping(
                    $sitebackpack->id,
                    OPEN_BADGES_V2_TYPE_ASSERTION,
                    $assertionid,
                    'externalid'
                );
                $response = $api->update_assertion($internalid, $assertiondata);
                if (!$response) {
                    throw new moodle_exception('invalidrequest', 'error');
                }
            }
        }
    }

    // Now award/upload the badge to the user's account.
    // - If a user and site backpack have the same provider we can skip this as Badgr automatically maps recipients
    // based on email address.
    // - This is only needed when the backpacks are from different regions.
    if ($assertionentityid && !badges_external_get_mapping($userbackpack->id, OPEN_BADGES_V2_TYPE_ASSERTION, $assertionid)) {
        $userapi = new \core_badges\backpack_api($userbackpack, $backpack);
        $userapi->authenticate();
        $response = $userapi->import_badge_assertion($assertionentityid);
        if (!$response) {
            throw new moodle_exception('invalidrequest', 'error');
        }
        $assertionentityid = $response->id;
        $badgeadded = true;
        badges_external_create_mapping($userbackpack->id, OPEN_BADGES_V2_TYPE_ASSERTION, $assertionid,
            $assertionentityid);
    }

    $response = $badgeadded ? ['success' => 'addedtobackpack'] : ['warning' => 'existsinbackpack'];
    redirect(new moodle_url('/badges/mybadges.php', $response));
} else {
    redirect(new moodle_url('/badges/mybadges.php'));
}
