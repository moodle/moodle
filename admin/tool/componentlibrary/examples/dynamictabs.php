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
 * Moodle Component Library
 *
 * A sample page with dynamic tabs.
 *
 * @package    tool_componentlibrary
 * @copyright  2021 David Matamoros <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require_once(__DIR__ . '/../../../../config.php');

use core\output\dynamic_tabs;

require_login();
require_capability('moodle/site:configview', context_system::instance());

global $PAGE, $OUTPUT;
$PAGE->set_url(new moodle_url('/admin/tool/componentlibrary/examples/dynamictabs.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('embedded');

$PAGE->set_heading('Moodle dynamic tabs');
$PAGE->set_title('Moodle dynamic tabs');

echo $OUTPUT->header();

// Add dynamic tabs to our page.
$tabs = [
    new \tool_componentlibrary\local\examples\dynamictabs\tab1(['demotab' => 'Tab1']),
    new \tool_componentlibrary\local\examples\dynamictabs\tab2(['demotab' => 'Tab2']),
];
echo $OUTPUT->render_from_template('core/dynamic_tabs',
    (new dynamic_tabs($tabs))->export_for_template($OUTPUT));

echo $OUTPUT->footer();
