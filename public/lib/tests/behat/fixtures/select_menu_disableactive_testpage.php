<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Test page for select_menu output component disableactive behaviour.
 *
 * @copyright 2026 Monash University
 * @author    Cameron Ball <cameronball@catalyst-au.net>
 * @package   core
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require_once(__DIR__ . '/../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $PAGE, $OUTPUT;

require_login();

$PAGE->set_url('/lib/tests/behat/fixtures/select_menu_disableactive_testpage.php');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_context(core\context\system::instance());
$PAGE->set_title('Select menu disableactive fixture');

$options = [
    'opt1' => 'Option 1',
    'opt2' => 'Option 2',
    'opt3' => 'Option 3',
];

$selectmenu = new core\output\select_menu('fixtureselect', $options, 'opt1', true);
$selectmenu->set_label('Test combobox');

echo $OUTPUT->header();

echo '<h2>Select menu disableactive fixture</h2>';
echo $OUTPUT->render($selectmenu);
echo $OUTPUT->footer();
