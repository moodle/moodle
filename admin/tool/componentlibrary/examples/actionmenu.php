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
 * A sample of a default action menu.
 *
 * @package    tool_componentlibrary
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require_once(__DIR__ . '/../../../../config.php');

global $PAGE;

$PAGE->set_url(new moodle_url('/admin/tool/componentlibrary/examples/actionmenu.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('embedded');

$PAGE->set_heading('Moodle action menus');
$PAGE->set_title('Moodle action menus');

/** @var core_renderer $output*/
$output = $PAGE->get_renderer('core');

// Some menu items require as renderable element. This is just an
// example of a choice list but it can be any other renderable.
$choice = new core\output\choicelist('Choice example');
$choice->add_option("statusa", "Status A", [
    'url' => $PAGE->url,
    'description' => 'Status A description',
    'icon' => new pix_icon('t/user', '', ''),
]);
$choice->add_option("statusb", "Status B", [
    'url' => $PAGE->url,
    'description' => 'Status B description',
    'icon' => new pix_icon('t/groupv', '', ''),
]);
$choice->set_selected_value('statusb');

// Those are some examples of action items.

// Action menu links is the most used action item.
$basicactionlink = new action_menu_link(
    new moodle_url($PAGE->url),
    new pix_icon('t/emptystar', ''),
    'Action link example',
    false
);

// Subpanels display lateral panels on hovered or clicked.
$subpanel = new core\output\local\action_menu\subpanel(
    'Subpanel example',
    $choice
);

echo $output->header();

echo '<p><strong>Important note:</strong> actions menus are not prepared
    to be displayed inside iframes. You may need to scroll to see the
    action menu options.</p>';

echo $output->heading("Action menu default example", 4);

$menu = new action_menu();

$menu->add($basicactionlink);
$menu->add($basicactionlink);
$menu->add($subpanel);
$menu->add($basicactionlink);

echo '<div class="border m-3 p-3 d-flex flex-row">';
echo '<div class="flex-fill">Example of default an action menu</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo $output->heading("Kebab menu example", 4);

$menu = new action_menu();
$menu->set_kebab_trigger(get_string('edit'), $output);
$menu->set_additional_classes('fields-actions');

$menu->add($basicactionlink);
$menu->add($basicactionlink);
$menu->add(new core\output\local\action_menu\subpanel(
    'Subpanel example',
    $choice
));
$menu->add($basicactionlink);

echo '<div class="border m-3 p-3 d-flex flex-row">';
echo '<div class="flex-fill">Example of kebab menu</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo $output->heading("Custom trigger menu example", 4);

$menu = new action_menu();
$menu->set_menu_trigger(get_string('edit'));

$menu->add($basicactionlink);
$menu->add($basicactionlink);
$menu->add(new core\output\local\action_menu\subpanel(
    'Subpanel example',
    $choice
));
$menu->add($basicactionlink);

echo '<div class="border m-3 p-3 d-flex flex-row">';
echo '<div class="flex-fill">Example of kebab menu</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo $output->heading("Primary actions menu example", 4);

$menu = new action_menu();
$menu->set_menu_trigger(get_string('edit'));

$menu->add($basicactionlink);
$menu->add($basicactionlink);
$menu->add(new core\output\local\action_menu\subpanel(
    'Subpanel example',
    $choice
));
$menu->add($basicactionlink);
$menu->add(new action_menu_link_primary(
    $PAGE->url,
    new pix_icon('t/emptystar', ''),
    'Action link example',
));
$menu->add(new action_menu_link_primary(
    $PAGE->url,
    new pix_icon('t/user', ''),
    'Action link example',
));

echo '<div class="border m-3 p-3 d-flex flex-row">';
echo '<div class="flex-fill">Example of a menu with primary actions</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo $output->footer();
