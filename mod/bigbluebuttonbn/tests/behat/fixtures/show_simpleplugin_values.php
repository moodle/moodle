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
 * Test page for simple subplugins.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Shamiso Jaravaza  (shamiso [dt] jaravaza [at] blindsidenetworks [dt] com)
 */

require_once(__DIR__ . '/../../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();
global $PAGE, $OUTPUT;
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/mod/bigbluebuttonbn/tests/behat/fixtures/show_simpleplugin_values.php');

echo $OUTPUT->header();

$bnid = $DB->get_field('bigbluebuttonbn', 'id', ['name' => 'BBB Instance name']);
// Check that the subplugin has the correct meeting events data.
$meetingevent = $DB->get_field('bbbext_simple', 'meetingevents', ['bigbluebuttonbnid' => $bnid]);
$meetingevent = json_decode($meetingevent, true);
$chats = $meetingevent['data']['attendees'][0]['engagement']['chats'];

echo "<p>(BBB Instance name): meetingevents: {$chats}</p>";

echo $OUTPUT->footer();
