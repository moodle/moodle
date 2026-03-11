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
 * Test page for action menu subpanel output component.
 *
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @package   core
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

$foo = optional_param('foo', 'none', PARAM_TEXT);

global $PAGE, $OUTPUT;
$PAGE->set_url('/lib/tests/behat/fixtures/action_menu_subpanel_output_testpage.php');
$PAGE->add_body_class('limitedwidth');
require_login();
$PAGE->set_context(core\context\system::instance());
$PAGE->set_title('Action menu subpanel test page');

echo $OUTPUT->header();

$choice1 = new core\output\choicelist('Choice example');
$choice1->add_option("statusa", "Status A", [
    'url' => new moodle_url($PAGE->url, ['foo' => 'Aardvark']),
    'description' => 'Status A description',
    'icon' => new pix_icon('t/user', '', ''),
]);
$choice1->add_option("statusb", "Status B", [
    'url' => new moodle_url($PAGE->url, ['foo' => 'Beetle']),
    'description' => 'Status B description',
    'icon' => new pix_icon('t/groupv', '', ''),
]);
$choice1->set_selected_value('statusb');

$choice2 = new core\output\choicelist('Choice example');
$choice2->add_option("statusc", "Status C", [
    'url' => new moodle_url($PAGE->url, ['foo' => 'Caterpillar']),
    'description' => 'Status C description',
    'icon' => new pix_icon('t/groups', '', ''),
]);
$choice2->add_option("statusd", "Status D", [
    'url' => new moodle_url($PAGE->url, ['foo' => 'Donkey']),
    'description' => 'Status D description',
    'icon' => new pix_icon('t/hide', '', ''),
]);
$choice2->set_selected_value('statusc');

$normalactionlink = new action_menu_link(
    new moodle_url($PAGE->url, ['foo' => 'bar']),
    new pix_icon('t/emptystar', ''),
    'Action link example',
    false
);

echo "<h2>Action menu subpanel test page</h2>";

echo '<div id="paramcheck" class="mb-4">';
echo "<p>Foo param value: $foo</p>";
echo '</div>';

// Basic example.

echo '<div id="regularscenario" class="mb-4">';
echo "<h3>Basic example</h3>";
$menu = new action_menu();
$menu->add($normalactionlink);
$menu->add($normalactionlink);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Subpanel example',
        $choice1
    )
);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Another subpanel',
        $choice2
    )
);
echo '<div class="border p-2 d-flex flex-row">';
echo '<div class="flex-fill">Menu right example</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo '</div>';

// Menu left example.

echo '<div id="menuleft" class="mb-4">';
echo "<h3>Menu left</h3>";

$menu = new action_menu();
$menu->set_menu_left();
$menu->add($normalactionlink);
$menu->add($normalactionlink);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Subpanel example',
        $choice1,
        null,
        null
    )
);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Another subpanel',
        $choice2,
        null,
        null
    )
);
echo '<div class="border p-2 d-flex flex-row"><div>';
echo $OUTPUT->render($menu);
echo '</div><div class="flex-fill ms-2">Menu left example</div></div>';

echo '</div>';

// Menu item with icon example.

echo '<div id="itemicon" class="mb-4">';
echo "<h3>Menu item with icon</h3>";

$menu = new action_menu();
$menu->add($normalactionlink);
$menu->add($normalactionlink);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Subpanel example',
        $choice1,
        null,
        new pix_icon('t/locked', 'Locked icon')
    )
);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Another subpanel',
        $choice2,
        null,
        new pix_icon('t/message', 'Message icon')
    )
);
echo '<div class="border p-2 d-flex flex-row">';
echo '<div class="flex-fill">Menu right example</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo '</div>';


echo '<div id="itemiconleft" class="mb-4">';
echo "<h3>Left menu with item icon</h3>";

$menu = new action_menu();
$menu->set_menu_left();
$menu->add($normalactionlink);
$menu->add($normalactionlink);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Subpanel example',
        $choice1,
        null,
        new pix_icon('t/locked', 'Locked icon')
    )
);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Another subpanel',
        $choice2,
        null,
        new pix_icon('t/message', 'Message icon')
    )
);
echo '<div class="border p-2 d-flex flex-row"><div>';
echo $OUTPUT->render($menu);
echo '</div><div class="flex-fill ms-2">Menu left example</div></div>';

echo '</div>';

echo '<div id="dataattributes" class="mb-4">';
echo "<h3>Adding data attributes to menu item</h3>";
$menu = new action_menu();
$menu->add($normalactionlink);
$menu->add($normalactionlink);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Subpanel example',
        $choice1,
        ['data-extra' => 'some extra value']
    )
);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Another subpanel',
        $choice2,
        ['data-extra' => 'some other value']
    )
);
echo '<div class="border p-2 d-flex flex-row">';
echo '<div class="flex-fill">Menu right example</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';
echo '<div class="mt-1 p-2 border" id="datachecks">Nothing here.</div>';
echo '</div>';

$inlinejs = <<<EOF
    const datachecks = document.getElementById('datachecks');
    const dataitems = document.querySelectorAll('[data-extra]');
    let dataitemshtml = '';
    for (let i = 0; i < dataitems.length; i++) {
        dataitemshtml += '<p>Extra data attribute detected: ' + dataitems[i].getAttribute('data-extra') + '</p>';
    }
    datachecks.innerHTML = dataitemshtml;
EOF;

$PAGE->requires->js_amd_inline($inlinejs);

echo '<div id="drawersimulation" class="mb-4">';
echo "<h3>Drawer like example</h3>";
$menu = new action_menu();
$menu->add($normalactionlink);
$menu->add($normalactionlink);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Subpanel example',
        $choice1
    )
);
$menu->add(
    new core\output\local\action_menu\subpanel(
        'Another subpanel',
        $choice2
    )
);
echo '<div class="border p-2 d-flex flex-row" data-region="fixed-drawer" data-behat-fake-drawer="true" style="width: 350px;">';
echo '<div class="flex-fill">Drawer example</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo '</div>';

$link1 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Alpha');
$link2 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Beta');
$link3 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Gamma');

// Submenu basic example.
echo '<div id="basicsubmenuexample" class="mb-4">';
echo '<h3>Submenu basic example</h3>';

$submenu = new core\output\submenu([$link1, $link2, $link3]);

$item1 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Item 1');
$item2 = new core\output\action_menu\subpanel('Item 2 with submenu', $submenu);
$item3 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Item 3');
$menu = new action_menu([$item1, $item2, $item3]);

echo '<div class="border p-2 d-flex flex-row">';
echo '<div class="flex-fill">Basic submenu</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo '</div>';

// Submenu left aligned using add_item() example.

echo '<div id="leftsubmenuexample" class="mb-4">';
echo '<h3>Submenu left</h3>';

$submenu = (new core\output\submenu())
    ->add_item($link1)
    ->add_item($link2)
    ->add_item($link3);

$item1 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Item 1');
$item2 = new core\output\action_menu\subpanel('Item 2 with submenu', $submenu);
$item3 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Item 3');
$menu = new action_menu([$item1, $item2, $item3]);
$menu->set_menu_left();

echo '<div class="border p-2 d-flex flex-row">';
echo $OUTPUT->render($menu);
echo '<div class="ms-2">Left submenu</div><div>';
echo '</div></div>';

echo '</div>';

// Nested submenu + choicelist example.

echo '<div id="nestedsubmenuexample" class="mb-4">';
echo '<h3>Submenu with nested choicelist example</h3>';

$nestedchoices = new core\output\choicelist('Nested choices');
$nestedchoices->add_option('opt1', 'Option 1', ['url' => new moodle_url($PAGE->url, ['item' => 'opt1'])]);
$nestedchoices->add_option('opt2', 'Option 2', ['url' => new moodle_url($PAGE->url, ['item' => 'opt2'])]);
$nestedchoices->set_selected_value('opt1');

$submenu = new core\output\submenu([
    $link1,
    $link2,
    new core\output\action_menu\subpanel('More options', $nestedchoices),
]);

$item1 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Item 1');
$item2 = new core\output\action_menu\subpanel('Item 2 with submenu', $submenu);
$item3 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Item 3');
$menu = new action_menu([$item1, $item2, $item3]);

echo '<div class="border p-2 d-flex flex-row">';
echo '<div class="flex-fill">Subpanel with nested submenu and choicelist</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo '</div>';

// Submenu with icons example.

echo '<div id="submenuicons" class="mb-4">';
echo '<h3>Submenu with icons</h3>';

$subitem1 = new core\output\action_menu\link_secondary(
    new moodle_url($PAGE->url, ['item' => 'edit']),
    new pix_icon('t/edit', 'Edit icon'),
    'Edit',
);
$subitem2 = new core\output\action_menu\link_secondary(
    new moodle_url($PAGE->url, ['item' => 'hide']),
    new pix_icon('t/hide', 'Hide icon'),
    'Hide',
);
$subitem3 = new core\output\action_menu\link_secondary(
    new moodle_url($PAGE->url, ['item' => 'delete']),
    new pix_icon('t/delete', 'Delete icon'),
    'Delete',
);
$submenu = new core\output\submenu([$subitem1, $subitem2, $subitem3]);

$item1 = new core\output\action_menu\link_secondary(
    new moodle_url('#'),
    new pix_icon('i/log', ''),
    'Item 1',
);
$item2 = new core\output\action_menu\subpanel(
    'Item 2 with submenu',
    $submenu,
    ['data-foo' => 'bar'],
    new pix_icon('i/search', ''),
);
$item3 = new core\output\action_menu\link_secondary(
    new moodle_url('#'),
    new pix_icon('t/download', ''),
    'Item 3',
);
$menu = new action_menu([$item1, $item2, $item3]);

echo '<div class="border p-2 d-flex flex-row">';
echo '<div class="flex-fill">Submenu links with icons</div><div>';
echo $OUTPUT->render($menu);
echo '</div></div>';

echo '</div>';

// Nested submenu with links.

echo '<div id="nestedsubmenuwithlinkexample" class="mb-4">';
echo '<h3>Submenu left nested with link example</h3>';

$link4 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Yota');
$link5 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Zeta');
$link6 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Omega');
$submenu2 = new core\output\submenu([
    $link4,
    $link5,
    $link6,
]);

$submenu1 = new core\output\submenu([
    $link1,
    $link2,
    new core\output\action_menu\subpanel(text: 'Gamma', subpanel: $submenu2, url: new moodle_url('/my')),
]);

$item1 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Item 1');
$item2 = new core\output\action_menu\subpanel('Item 2 with submenu', $submenu1);
$item3 = new core\output\action_menu\link_secondary(new moodle_url('#'), null, 'Item 3');
$menu = new action_menu([$item1, $item2, $item3]);
$menu->set_menu_left();

echo '<div class="border p-2 d-flex flex-row">';
echo $OUTPUT->render($menu);
echo '<div class="ms-2">Left subpanel with nested submenu and second level submenu with links</div><div>';
echo '</div></div>';

echo '</div>';

echo $OUTPUT->footer();
