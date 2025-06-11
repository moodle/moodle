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
 * A sample page with dropdowns.
 *
 * @package    tool_componentlibrary
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require_once(__DIR__ . '/../../../../config.php');

require_login();
require_capability('moodle/site:configview', context_system::instance());

global $PAGE, $OUTPUT;
$PAGE->set_url(new moodle_url('/admin/tool/componentlibrary/examples/dropdowns.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('embedded');

$PAGE->set_heading('Moodle dropdowns');
$PAGE->set_title('Moodle dropdowns');

/** @var core_renderer $output*/
$output = $PAGE->get_renderer('core');

echo $output->header();

echo $output->paragraph(
    '<strong>Important note:</strong> dropdowns are not prepared
    to be displayed inside iframes. You may need to scroll to see the
    the dropdown content.'
);

echo $output->heading("Dropdown dialog example", 3);
echo '<div class="p-3">';

$dialog = new core\output\local\dropdown\dialog(
    'Open dialog',
    '<p>Some rich content <b>element</b>.</p>
    <ul>
        <li>Item 1 <a href="#">Link 1</a></li>
        <li>Item 2 <a href="#">Link 2</a></li>
    </ul>'
);
echo $OUTPUT->render($dialog);
echo "</div>";


echo $output->heading("Dropdown status example", 3);
echo '<div class="p-3">';

$choice = new core\output\choicelist('Choice description text');

// Option one is a link.
$choice->add_option('option1', 'Option 1', [
    'icon' => new pix_icon('t/show', 'Eye icon 1'),
    'url' => new moodle_url('/admin/tool/componentlibrary/examples/dropdowns.php'),
]);
// Option two has an icon and description.
$choice->add_option('option2', 'Option 2', [
    'description' => 'Option 2 description',
    'icon' => new pix_icon('t/hide', 'Eye icon 2'),
]);
// Option three is disabled.
$choice->add_option('option3', 'Option 3', [
    'description' => 'Option 3 description',
    'icon' => new pix_icon('t/stealth', 'Eye icon 3'),
    'disabled' => true,
]);

$choice->set_selected_value('option2');

$dialog = new core\output\local\dropdown\status('Open dialog button', $choice);
echo $OUTPUT->render($dialog);
echo "</div>";

echo $output->heading("Dropdown status in update mode example", 3);
echo '<div class="p-3">';

$choice = new core\output\choicelist('Choice description text');

$choice->add_option('option1', 'Option 1', [
    'description' => 'Option 1 description',
    'icon' => new pix_icon('t/show', 'Eye icon 1'),
]);
$choice->add_option('option2', 'Option 2', [
    'description' => 'Option 2 description',
    'icon' => new pix_icon('t/hide', 'Eye icon 2'),
]);

$choice->set_selected_value('option2');

$dialog = new core\output\local\dropdown\status(
    'Open dialog button',
    $choice,
    [
        'buttonsync' => true,
        'updatestatus' => true,
        'dialogwidth' => core\output\local\dropdown\status::WIDTH['big']
    ]
);
echo $OUTPUT->render($dialog);
echo "</div>";

echo $output->footer();
