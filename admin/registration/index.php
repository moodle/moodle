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
 * @package    moodle
 * @subpackage registration
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This page displays the site registration form for Moodle.net.
 * It handles redirection to the hub to continue the registration workflow process.
 * It also handles update operation by web service.
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('registrationmoodleorg');

$unregistration = optional_param('unregistration', 0, PARAM_INT);

if ($unregistration && \core\hub\registration::is_registered()) {
    $siteunregistrationform = new \core\hub\site_unregistration_form();

    if ($siteunregistrationform->is_cancelled()) {
        redirect(new moodle_url('/admin/registration/index.php'));
    } else if ($data = $siteunregistrationform->get_data()) {
        if (\core\hub\registration::unregister($data->unpublishalladvertisedcourses,
            $data->unpublishalluploadedcourses)) {
            redirect(new moodle_url('/admin/registration/index.php'));
        }
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('unregisterfrom', 'hub', 'Moodle.net'), 3, 'main');
    $siteunregistrationform->display();
    echo $OUTPUT->footer();
    exit;
}

$isinitialregistration = \core\hub\registration::show_after_install(true);
if (!$returnurl = optional_param('returnurl', null, PARAM_LOCALURL)) {
    $returnurl = $isinitialregistration ? '/admin/index.php' : '/admin/registration/index.php';
}

$siteregistrationform = new \core\hub\site_registration_form();
$siteregistrationform->set_data(['returnurl' => $returnurl]);
if ($fromform = $siteregistrationform->get_data()) {

    // Save the settings.
    \core\hub\registration::save_site_info($fromform);

    if (\core\hub\registration::is_registered()) {
        if (\core\hub\registration::update_manual()) {
            redirect(new moodle_url($returnurl));
        }
        redirect(new moodle_url('/admin/registration/index.php', ['returnurl' => $returnurl]));
    } else {
        \core\hub\registration::register($returnurl);
        // This method will redirect away.
    }

}

// OUTPUT SECTION.

echo $OUTPUT->header();

// Current status of registration on Moodle.net.

$notificationtype = \core\output\notification::NOTIFY_ERROR;
if (\core\hub\registration::is_registered()) {
    $lastupdated = \core\hub\registration::get_last_updated();
    if ($lastupdated == 0) {
        $registrationmessage = get_string('pleaserefreshregistrationunknown', 'admin');
    } else if (\core\hub\registration::get_new_registration_fields()) {
        $registrationmessage = get_string('pleaserefreshregistrationnewdata', 'admin');
    } else {
        $lastupdated = userdate($lastupdated, get_string('strftimedate', 'langconfig'));
        $registrationmessage = get_string('pleaserefreshregistration', 'admin', $lastupdated);
        $notificationtype = \core\output\notification::NOTIFY_INFO;
    }
    echo $OUTPUT->notification($registrationmessage, $notificationtype);
} else if (!$isinitialregistration) {
    $registrationmessage = get_string('registrationwarning', 'admin');
    echo $OUTPUT->notification($registrationmessage, $notificationtype);
}

// Heading.
if (\core\hub\registration::is_registered()) {
    echo $OUTPUT->heading(get_string('updatesite', 'hub', 'Moodle.net'));
} else if ($isinitialregistration) {
    echo $OUTPUT->heading(get_string('completeregistration', 'hub'));
} else {
    echo $OUTPUT->heading(get_string('registerwithmoodleorg', 'admin'));
}

$renderer = $PAGE->get_renderer('core', 'register');
echo $renderer->moodleorg_registration_message();

$siteregistrationform->display();

if (\core\hub\registration::is_registered()) {
    // Unregister link.
    $unregisterhuburl = new moodle_url("/admin/registration/index.php", ['unregistration' => 1]);
    echo html_writer::div(html_writer::link($unregisterhuburl, get_string('unregister', 'hub')), 'unregister');
} else if ($isinitialregistration) {
    echo html_writer::div(html_writer::link(new moodle_url($returnurl), get_string('skipregistration', 'hub')), 'skipregistration');
}
echo $OUTPUT->footer();
