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
 * Test that the OneRoster endpoint is configured correctly
 *
 * @package   enrol_oneroster
 * @copyright 2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("{$CFG->libdir}/adminlib.php");

admin_externalpage_setup('enrol_oneroster/testconnection');
$returnurl = new moodle_url('/admin/settings.php', [
    'section' => 'enrolsettingsoneroster',
]);

$onerosterconfig = get_config('enrol_oneroster');

$requiredfields = [
    'oneroster_version',
    'oauth_version',
    'token_url',
    'root_url',
    'clientid',
    'secret',
];

foreach ($requiredfields as $field) {

    if (empty($onerosterconfig->{$field})) {
        redirect(
            $returnurl,
            get_string('missingrequiredconfig', 'enrol_oneroster', $field),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

$client = enrol_oneroster\client_helper::get_client(
    $onerosterconfig->oauth_version,
    $onerosterconfig->oneroster_version,
    $onerosterconfig->token_url,
    $onerosterconfig->root_url,
    $onerosterconfig->clientid,
    $onerosterconfig->secret
);

$client->authenticate();

$foundorgs = [];
$organisations = $client->fetch_organisation_list();
foreach ($organisations as $org) {
    $foundorgs[$org->get('sourcedId')] = $org->get('name');
}

set_config('availableschools', json_encode($foundorgs), 'enrol_oneroster');

redirect(
    $returnurl,
    get_string('configurationcorrect', 'enrol_oneroster'),
    null,
    \core\output\notification::NOTIFY_SUCCESS
);
