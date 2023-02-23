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
 * Moodle app subscription information for the current site.
 *
 * @package   tool_mobile
 * @copyright 2020 Moodle Pty Ltd
 * @author    <juan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('mobileappsubscription', '', null, '');

// Check Mobile web services enabled. This page should not be linked in that case, but avoid just in case.
if (!$CFG->enablemobilewebservice) {
    throw new \moodle_exception('enablewsdescription', 'webservice');
}
// Check is this feature is globaly disabled.
if (!empty($CFG->disablemobileappsubscription)) {
    throw new \moodle_exception('disabled', 'admin');
}

$subscriptiondata = \tool_mobile\api::get_subscription_information();

echo $OUTPUT->header();

if (empty($subscriptiondata)) {
    echo $OUTPUT->notification(get_string('subscriptionerrorrequest', 'tool_mobile'), \core\output\notification::NOTIFY_ERROR);
} else {
    $templatable = new \tool_mobile\output\subscription($subscriptiondata);
    echo $PAGE->get_renderer('tool_mobile')->render($templatable);
}

echo $OUTPUT->footer();
