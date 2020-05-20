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
 * Connect to backpack site.
 *
 * @package    core_badges
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$scope = optional_param('scope', '', PARAM_RAW);
$action = optional_param('action', null, PARAM_RAW);

if (badges_open_badges_backpack_api() != OPEN_BADGES_V2P1) {
    throw new coding_exception('backpacks only support Open Badges V2.1');
}

require_login();

$externalbackpack = badges_get_site_backpack($CFG->badges_site_backpack);
$persistedissuer = \core\oauth2\issuer::get_record(['id' => $externalbackpack->oauth2_issuerid]);
if ($persistedissuer) {
    $issuer = new \core\oauth2\issuer($externalbackpack->oauth2_issuerid);
    $returnurl = new moodle_url('/badges/backpack-connect.php',
        ['action' => 'authorization', 'sesskey' => sesskey()]);

    $client = new core_badges\oauth2\client($issuer, $returnurl, $scope, $externalbackpack);
    if ($client) {
        if (!$client->is_logged_in()) {
            redirect($client->get_login_url());
        }
        $wantsurl = new moodle_url('/badges/mybadges.php');
        $auth = new \core_badges\oauth2\auth();
        $auth->complete_data($client, $wantsurl);
    } else {
        throw new moodle_exception('Could not get an OAuth client.');
    }
} else {
    throw new moodle_exception('Unknown OAuth client.');
}
