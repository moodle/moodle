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

use core_badges\local\backpack\helper;

require_login();

$userbackpack = badges_get_user_backpack();
if (badges_open_badges_backpack_api($userbackpack->id) != OPEN_BADGES_V2) {
    throw new coding_exception('No backpacks support Open Badges V2.');
}

$assertionhash = required_param('hash', PARAM_ALPHANUM);

$PAGE->set_url('/badges/backpack-add.php', ['hash' => $assertionhash]);
$PAGE->set_context(context_system::instance());
$output = $PAGE->get_renderer('core', 'badges');

$issuedbadge = new \core_badges\output\issued_badge($assertionhash);
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
    $assertiondata = helper::export_achievement_credential(OPEN_BADGES_V2, $assertionhash, false, false);
    $assertionentityid = $assertiondata['id'];
    $badgeadded = false;
    $issuerexists = false;
    if (badges_open_badges_backpack_api() == OPEN_BADGES_V2) {
        $sitebackpack = badges_get_site_primary_backpack();
        $api = new \core_badges\backpack_api($sitebackpack);
        $response = $api->authenticate();

        // A numeric response indicates a valid successful authentication. Else an error object will be returned.
        if (is_numeric($response)) {
            // Create issuer.
            $issuer = helper::export_issuer(OPEN_BADGES_V2, $badgeid);
            if (!($issuerentityid = badges_external_get_mapping($sitebackpack->id, OPEN_BADGES_V2_TYPE_ISSUER, $issuer['email']))) {
                $response = $api->put_issuer($issuer);
                if ($response) {
                    $issuerexists = true;
                    $issuerentityid = $response->id;
                    badges_external_create_mapping(
                        $sitebackpack->id,
                        OPEN_BADGES_V2_TYPE_ISSUER,
                        $issuer['email'],
                        $issuerentityid,
                    );
                }
            }
            if ($issuerexists) {
                // Create badge.
                $badge = helper::export_credential(
                    OPEN_BADGES_V2,
                    $badgeid,
                    false,
                );
                if (!($badgeentityid = badges_external_get_mapping($sitebackpack->id, OPEN_BADGES_V2_TYPE_BADGE, $badgeid))) {
                    $response = $api->put_badgeclass($issuerentityid, $badge);
                    if ($response) {
                        $badgeentityid = $response->id;
                        badges_external_create_mapping(
                            $sitebackpack->id,
                            OPEN_BADGES_V2_TYPE_BADGE,
                            $badgeid,
                            $badgeentityid,
                        );
                    }
                }

                // Create assertion (Award the badge!).
                $assertionentityid = badges_external_get_mapping(
                    $sitebackpack->id,
                    OPEN_BADGES_V2_TYPE_ASSERTION,
                    $assertionhash
                );

                if ($assertionentityid && strpos($sitebackpack->backpackapiurl, 'badgr')) {
                    $assertionentityid = badges_generate_badgr_open_url(
                        $sitebackpack,
                        OPEN_BADGES_V2_TYPE_ASSERTION,
                        $assertionentityid
                    );
                }

                // Create an assertion for the recipient in the issuer's account.
                if (!$assertionentityid) {
                    $response = $api->put_badgeclass_assertion($badgeentityid, $assertiondata);
                    if ($response) {
                        $assertionentityid = badges_generate_badgr_open_url(
                            $sitebackpack,
                            OPEN_BADGES_V2_TYPE_ASSERTION,
                            $response->id,
                        );
                        $badgeadded = true;
                        badges_external_create_mapping(
                            $sitebackpack->id,
                            OPEN_BADGES_V2_TYPE_ASSERTION,
                            $assertionhash,
                            $response->id,
                        );
                    }
                } else {
                    // An assertion already exists. Make sure it's up to date.
                    $internalid = badges_external_get_mapping(
                        $sitebackpack->id,
                        OPEN_BADGES_V2_TYPE_ASSERTION,
                        $assertionhash,
                        'externalid'
                    );
                    $response = $api->update_assertion($internalid, $assertiondata);
                }
            }
        }
    }

    // Now award/upload the badge to the user's account.
    // - If a user and site backpack have the same provider we can skip this as Badgr automatically maps recipients
    // based on email address.
    // - This is only needed when the backpacks are from different regions.
    if (
        $assertionentityid
        && (!$issuerexists || !badges_external_get_mapping($userbackpack->id, OPEN_BADGES_V2_TYPE_ASSERTION, $assertionhash))
    ) {
        $userapi = new \core_badges\backpack_api($userbackpack, $backpack);
        $userapi->authenticate();
        $response = $userapi->import_badge_assertion($assertionentityid);
        if ($response) {
            $assertionentityid = $response->id;
            $badgeadded = true;
            badges_external_create_mapping(
                $userbackpack->id,
                OPEN_BADGES_V2_TYPE_ASSERTION,
                $assertionhash,
                $assertionentityid,
            );
        }
    }

    if ($badgeadded) {
        $message = get_string('addedtobackpack', 'badges');
        $messagetype = \core\output\notification::NOTIFY_SUCCESS;
    } else {
        if (isset($userapi) && !empty($userapi->get_errors())) {
            // If the api used to import the badge to the backpack has errors, show them to inform the user.
            if (array_filter($userapi->get_errors(), fn($element) => str_contains($element, "DUPLICATE_BADGE"))) {
                // Duplicated badges are displayed as a warning.
                $message = get_string('existsinbackpack', 'badges');
                $messagetype = \core\output\notification::NOTIFY_WARNING;
            } else {
                // If the userapi has any other errors, we will use those to inform the user.
                $message = get_string(
                    'error:cannotsendtobackpack',
                    'badges',
                    implode($userapi->get_errors()),
                );
                $messagetype = \core\output\notification::NOTIFY_ERROR;
            }
        } else if (isset($api) && !empty($api->get_errors())) {
            // If the api used to create/update the issuer has errors, show them to inform the user.
            $errors = $api->get_errors() ?? [get_string('invalidrequest', 'error')];
            $message = get_string(
                'error:cannotsendtobackpack',
                'badges',
                implode($errors),
            );
            $messagetype = \core\output\notification::NOTIFY_ERROR;
        }
    }

    redirect(
        url: new \core\url('/badges/mybadges.php'),
        message: $message,
        messagetype: $messagetype,
    );
} else {
    redirect(new \core\url('/badges/mybadges.php'));
}
