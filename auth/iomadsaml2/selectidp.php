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
 * IdP selection GUI.
 *
 * @package   auth_iomadsaml2
 * @author    Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use auth_iomadsaml2\admin\iomadsaml2_settings;

// @codingStandardsIgnoreStart
require_once(__DIR__ . '/../../config.php');
// @codingStandardsIgnoreEnd
require('setup.php');

$site = get_site();
$loginsite = get_string("loginsite");

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/auth/iomadsaml2/selectidp.php'));
$PAGE->set_title("$site->fullname: $loginsite");
$PAGE->set_heading("$site->fullname");
$PAGE->navbar->add($loginsite);
$PAGE->requires->css('/auth/iomadsaml2/styles.css');

$wants = optional_param('wants', '', PARAM_RAW);

$idpname = $iomadsaml2auth->config->idpname;

// Retrieve IdP used for login when 'rememberidp' checkbox was set.
$storedchoiceidp = $iomadsaml2auth->get_idp_cookie();
if (empty($idpname)) {
    $idpname = get_string('idpnamedefault', 'auth_iomadsaml2');
}

$data = [
    'metadataentities' => $iomadsaml2auth->metadataentities,
    'storedchoiceidp' => $storedchoiceidp,
    'wants' => $wants,
    'idpname' => $idpname
];

$action = new moodle_url('/auth/iomadsaml2/selectidp.php');

$displaytype = $iomadsaml2auth->config->multiidpdisplay;

if ($displaytype == iomadsaml2_settings::OPTION_MULTI_IDP_DISPLAY_DROPDOWN) {
    $mform = new \auth_iomadsaml2\form\selectidp_dropdown($action, $data);
} else if ($displaytype == iomadsaml2_settings::OPTION_MULTI_IDP_DISPLAY_BUTTONS) {
    $mform = new \auth_iomadsaml2\form\selectidp_buttons($action, $data);
} else {
    throw new SimpleSAML_Error_Exception('An invalid multiple IdP display type has been selected.');
}

if ($fromform = $mform->get_data()) {
    $idp = required_param('idp', PARAM_RAW);
    $wants = optional_param('wants', '', PARAM_RAW);
    $rememberidp = optional_param('rememberidp', '', PARAM_RAW);

    $params = [
        'wants' => $wants,
        'idp' => $idp,
        'rememberidp' => $rememberidp
    ];

    $loginurl = new moodle_url('/auth/iomadsaml2/login.php', $params);
    redirect($loginurl);
} else {
    $rememberidp = $storedchoiceidp !== '' ? 1 : 0;

    $data = array('rememberidp' => $rememberidp);

    if ($displaytype == iomadsaml2_settings::OPTION_MULTI_IDP_DISPLAY_DROPDOWN) {
        $data['idp'] = $storedchoiceidp;
    }

    $mform->set_data($data);

    // Default is if rememberidp is on.
    $passive = (bool)optional_param('passive', $rememberidp, PARAM_BOOL);

    // If rememberidp is set and we are not returning from a passive attempt to login.
    if ($passive) {
        $errorurl = $PAGE->url;
        $errorurl->params(array('passive' => 0));

        $params = [
            'wants' => $wants,
            'idp' => $storedchoiceidp,
            'passive' => 1,
            'errorurl' => $errorurl->out(false)
        ];
        $loginurl = new moodle_url('/auth/iomadsaml2/login.php', $params);
        redirect($loginurl);
    }
}

echo $OUTPUT->header();
echo html_writer::start_div('loginbox');
echo html_writer::tag('h2', get_string('selectloginservice', 'auth_iomadsaml2'));
echo html_writer::start_div('subcontent');
$mform->display();
echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();
