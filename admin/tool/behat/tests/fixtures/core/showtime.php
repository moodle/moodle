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
 * Fixture to show the current server time using \core\clock.
 *
 * @package tool_behat
 * @copyright 2024 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.RequireLogin.Missing
require(__DIR__ . '/../../../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die('Behat fixture');

$PAGE->set_context(\context_system::instance());
$PAGE->set_url(new \moodle_url('/admin/tool/behat/tests/fixtures/core/showtime.php'));

echo $OUTPUT->header();

$clock = \core\di::get(\core\clock::class);
$dt = $clock->now();
$realbefore = time();
$time = $clock->time();
$realafter = time();

echo html_writer::div('Unix time ' . $time);
echo html_writer::div('Date-time ' . $dt->format('Y-m-d H:i:s'));

echo html_writer::div('TZ ' . $dt->getTimezone()->getName());

if ($time >= $realbefore && $time <= $realafter) {
    echo html_writer::div('Behat time is the same as real time');
} else {
    echo html_writer::div('Behat time is not the same as real time');
}

echo $OUTPUT->footer();
