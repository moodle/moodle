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
 * A page which can be used to represent a messaging service while testing the custom link communication provider.
 *
 * The current Moodle user's username is listed in the heading to make it easier to confirm the page has been
 * opened by the expected user.
 *
 * @package    communication_customlink
 * @copyright  2023 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $OUTPUT, $PAGE, $USER;

$PAGE->set_url('/communication/provider/customlink/tests/behat/fixtures/custom_link_test_page.php');
require_login();
$PAGE->set_context(core\context\system::instance());

echo $OUTPUT->header();
echo "<h2>Example messaging service - {$USER->username}</h2>";
echo "<p>Imagine this is a wonderful messaging service being accessed directly from a link in Moodle!</p>";
echo $OUTPUT->footer();
