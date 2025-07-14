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
 * Fixture for testing secure layout pages have no nav link.
 *
 * @package core
 * @copyright 2019 Luca Bösch <luca.boesch@bfh.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
// Behat test fixture only.
defined('BEHAT_SITE_RUNNING') || die('Only available on Behat test server');

$PAGE->set_pagelayout('secure');
$PAGE->set_url('/lib/tests/fixtures/securetestpage.php');
$PAGE->set_context(context_system::instance());
$title = 'Secure test page';
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

echo $OUTPUT->heading('Hello world');

echo $OUTPUT->footer();
