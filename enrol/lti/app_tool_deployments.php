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
 * LTI 1.3 page to create or edit a tool deployment for a registration.
 *
 * This page is only used by LTI 1.3. Older versions do not require deployments be registered with the tool during
 * registration.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use enrol_lti\local\ltiadvantage\repository\deployment_repository;

require_once(__DIR__ . '/../../config.php');
global $CFG, $OUTPUT, $PAGE;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/enrol/lti/lib.php');

$registrationid = required_param('registrationid', PARAM_INT);

navigation_node::override_active_url(
    new moodle_url('/admin/settings.php', ['section' => 'enrolsettingslti_registrations'])
);
admin_externalpage_setup('enrolsettingslti_deployments_list', '', null, '', ['pagelayout' => 'admin']);
$PAGE->navbar->add(get_string('deployments', 'enrol_lti'));

$deploymentsrepo = new deployment_repository();
$renderer = $PAGE->get_renderer('enrol_lti');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managedeployments', 'enrol_lti'));
echo $renderer->render_registered_tool_deployments($registrationid,
    $deploymentsrepo->find_all_by_registration($registrationid));
echo $OUTPUT->footer();
