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
 * Regenerate the Private Key and Certificate files
 *
 * @package    auth_iomadsaml2
 * @copyright  Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require('setup.php');

require_login();
require_capability('moodle/site:config', context_system::instance());
$heading = get_string('regenerateheading', 'auth_iomadsaml2');

$here = "$CFG->wwwroot/auth/iomadsaml2/regenerate.php";

auth_iomadsaml2_admin_nav($heading, $here);

$mform = new \auth_iomadsaml2\form\regenerate();

if ($mform->is_cancelled()) {
    redirect("$CFG->wwwroot/admin/settings.php?section=authsettingiomadsaml2");
}

$path = $iomadsaml2auth->certcrt;
$error = '';
$success = false;

if ($fromform = $mform->get_data()) {
    try {
        auth_iomadsaml2_process_regenerate_form($fromform);
        redirect(new moodle_url('/auth/iomadsaml2/cert.php'), get_string('success'), null, \core\output\notification::NOTIFY_SUCCESS);
    } catch (iomadsaml2_exception $exception) {
        $error = $exception->getMessage() . $exception->getTraceAsString();
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);
echo "<p>Path: $path</p>";

// Load data from the current certificate.
$data = openssl_x509_parse(file_get_contents($path));

// Calculate date expirey interval.
$date1 = date("Y-m-d\TH:i:s\Z", str_replace ('Z', '', $data['validFrom_time_t']));
$date2 = date("Y-m-d\TH:i:s\Z", str_replace ('Z', '', $data['validTo_time_t']));
$datetime1 = new DateTime($date1);
$datetime2 = new DateTime($date2);
$interval = $datetime1->diff($datetime2);
$expirydays = $interval->format('%a');

$toform = array (
    "email" => $data['subject']['emailAddress'],
    "expirydays" => $expirydays,
    "commonname" => substr($data['subject']['CN'], 0, 64),
    "countryname"       => $data['subject']['C'],
    "localityname"      => $data['subject']['L'],
    "organizationname"  => $data['subject']['O'],
    "stateorprovincename"    => $data['subject']['ST'],
    "organizationalunitname" => $data['subject']['OU'],
);
$mform->set_data($toform); // Load current data into form.

if ($success) {
    echo $OUTPUT->notification(get_string('regeneratesuccess', 'auth_iomadsaml2'), \core\output\notification::NOTIFY_SUCCESS);
} else if ($error) {
    echo $OUTPUT->notification($error, \core\output\notification::NOTIFY_ERROR);
} else {
    echo $OUTPUT->notification(get_string('regeneratewarning', 'auth_iomadsaml2'), \core\output\notification::NOTIFY_WARNING);
}

echo html_writer::tag('h1', get_string('regenerateheader', 'auth_iomadsaml2'));
echo html_writer::tag('p', get_string('regeneratepath', 'auth_iomadsaml2', $path));

$mform->display();

echo $OUTPUT->footer();

