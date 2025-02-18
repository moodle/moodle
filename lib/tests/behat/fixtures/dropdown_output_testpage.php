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
 * Test page for dropdown dialog output component.
 *
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @package   core
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $CFG, $PAGE, $OUTPUT;
$PAGE->set_url('/lib/tests/behat/fixtures/dropdown_dialog_testpage.php');
$PAGE->add_body_class('limitedwidth');
require_login();
$PAGE->set_context(core\context\system::instance());

echo $OUTPUT->header();

echo "<h2>Dropdown dialog test page</h2>";

echo '<div id="regularscenario" class="mb-4">';
echo "<h3>Basic example</h3>";
$dialog = new core\output\local\dropdown\dialog('Open dialog', 'Dialog content');
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="richcontent" class="mb-4">';
echo "<h3>Rich content example</h3>";
$content = '
    <p>Some rich content <b>element</b>.</p>
    <ul>
        <li>Item 1 <a href="#">Link 1</a></li>
        <li>Item 2 <a href="#">Link 2</a></li>
    </ul>
';
$dialog = new core\output\local\dropdown\dialog('Open dialog', $content);
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="richbutton" class="mb-4">';
echo "<h3>Rich button example</h3>";
$button = $OUTPUT->pix_icon('t/hide', 'Eye icon') . ' Click to <b>open</b></a>';
$dialog = new core\output\local\dropdown\dialog($button, 'Dialog content');
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="cssoverride" class="mb-4">';
echo "<h3>CSS override example</h3>";
$dialog = new core\output\local\dropdown\dialog(
    'Open dialog',
    'Dialog content',
    [
        'buttonclasses' => 'btn btn-primary extraclass',
    ]
);
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="extraattributes" class="mb-4">';
echo "<h3>Extra data attributes example</h3>";
$dialog = new core\output\local\dropdown\dialog('Open dialog', 'Dialog content', [
    'extras' => ['data-foo' => 'bar'],
]);
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="customid" class="mb-4">';
echo "<h3>Custom element id</h3>";
$dialog = new core\output\local\dropdown\dialog('Open dialog', 'Dialog content', [
    'extras' => ['id' => 'CustomDropdownButtonId'],
]);
echo $OUTPUT->render($dialog);
echo '</div>';

$inlinejs = "document.querySelector('#CustomDropdownButtonId button').innerHTML = 'Custom ID button found';";
$PAGE->requires->js_amd_inline($inlinejs);

echo '<div id="position" class="mb-4">';
echo "<h3>Dropdown position example</h3>";
$dialog = new core\output\local\dropdown\dialog('Open dialog', 'Dialog content');
$dialog->set_position(core\output\local\dropdown\dialog::POSITION['end']);
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="widths" class="mb-4">';
echo "<h3>Dropdown max width values example</h3>";
$content = '
    Some long long content. Some long long content. Some long long content. Some long long content.
    Some long long content. Some long long content. Some long long content. Some long long content.
    Some long long content. Some long long content. Some long long content. Some long long content.
';

$dialog = new core\output\local\dropdown\dialog('Default dialog (adaptative)', $content);
$dialog->set_classes('mb-3');
echo $OUTPUT->render($dialog);

$dialog = new core\output\local\dropdown\dialog('Big dialog', $content);
$dialog->set_dialog_width(core\output\local\dropdown\dialog::WIDTH['big']);
$dialog->set_classes('mb-3');
echo $OUTPUT->render($dialog);

$dialog = new core\output\local\dropdown\dialog('Small dialog', $content);
$dialog->set_dialog_width(core\output\local\dropdown\dialog::WIDTH['small']);
$dialog->set_classes('mb-3');
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="dialogjscontrolssection" class="mb-4">';
echo "<h3>Dropdown JS module controls</h3>";
echo '<div class="mb-2">
    <button class="btn btn-secondary" id="buttontext">Change button text</button>
    <button class="btn btn-secondary" id="opendropdown">Open</button>
    <button class="btn btn-secondary" id="closedropdown">Close</button>
    <span id="dialogvisibility"></span>
</div>';
$dialog = new core\output\local\dropdown\dialog('Open dialog', 'Dialog content', [
    'extras' => ['id' => 'dialogjscontrols'],
]);
echo $OUTPUT->render($dialog);
echo '</div>';

$inlinejs = <<<EOF
require(
    ['core/local/dropdown/dialog'],
    (Module) => {
        const dialog = Module.getDropdownDialog('#dialogjscontrols');

        document.querySelector('#buttontext').addEventListener('click', () => {
            dialog.setButtonContent('New button text');
        });

        document.querySelector('#opendropdown').addEventListener('click', (e) => {
            e.stopPropagation();
            dialog.setVisible(true);
        });

        document.querySelector('#closedropdown').addEventListener('click', (e) => {
            e.stopPropagation();
            dialog.setVisible(false);
        });

        const visibility = () => {
            const text = 'The dropdown is ' + (dialog.isVisible() ? 'visible' : 'hidden') + '.';
            document.querySelector('#dialogvisibility').innerHTML = text;
        }
        visibility();

        dialog.getElement().addEventListener('shown.bs.dropdown', (e) => {
            visibility();
        });
        dialog.getElement().addEventListener('hidden.bs.dropdown', (e) => {
            visibility();
        });
    }
);
EOF;
$PAGE->requires->js_amd_inline($inlinejs);

echo "<h2>Dropdown status test page</h2>";

echo '<div id="statusregularscenario" class="mb-4">';
echo "<h3>Basic example</h3>";
$choice = new core\output\choicelist('Dialog content');
$choice->add_option('option1', 'Option 1', [
    'description' => 'Option 1 description'
]);
$choice->add_option('option2', 'Option 2', [
    'description' => 'Option 2 description',
    'icon' => new pix_icon('t/hide', 'Eye icon 1')
]);
$choice->add_option('option3', 'Option 3', [
    'icon' => new pix_icon('t/show', 'Eye icon 2')
]);
$dialog = new core\output\local\dropdown\status('Open dialog', $choice);
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="statusselectedscenario" class="mb-4">';
echo "<h3>Selected element example</h3>";
$choice = new core\output\choicelist('Dialog content');
$choice->add_option('option1', 'Option 1', [
    'description' => 'Option 1 description',
    'icon' => new pix_icon('t/show', 'Eye icon 1')
]);
$choice->add_option('option2', 'Option 2', [
    'description' => 'Option 2 description',
    'icon' => new pix_icon('t/hide', 'Eye icon 2')
]);
$choice->add_option('option3', 'Option 3', [
    'description' => 'Option 3 description',
    'icon' => new pix_icon('t/stealth', 'Eye icon 3')
]);
$choice->set_selected_value('option2');
$dialog = new core\output\local\dropdown\status('Open dialog', $choice);
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="statusdisablescenario" class="mb-4">';
echo "<h3>Disable option example</h3>";
$choice = new core\output\choicelist('Dialog content');
$choice->add_option('option1', 'Option 1');
$choice->add_option('option2', 'Option 2', [
    'description' => 'Option 2 description',
    'icon' => new pix_icon('t/hide', 'Eye icon')
]);
$choice->add_option('option3', 'Option 3');
$choice->set_option_disabled('option2', true);
$dialog = new core\output\local\dropdown\status('Open dialog', $choice);
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="statusoptionextrasscenario" class="mb-4">';
echo "<h3>Set option extra attributes example</h3>";
$choice = new core\output\choicelist('Dialog content');
$choice->add_option('option1', 'Option 1');
$choice->add_option('option2', 'Option 2', [
    'description' => 'Option 2 description',
    'icon' => new pix_icon('t/hide', 'Eye icon')
]);
$choice->add_option('option3', 'Option 3');
$choice->set_option_extras('option2', ['data-foo' => 'bar']);
$dialog = new core\output\local\dropdown\status('Open dialog', $choice);
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="statusoptionurl" class="mb-4">';
echo "<h3>Set option url example</h3>";
$choice = new core\output\choicelist('Dialog content');
$choice->add_option('option1', 'Option 1');
$choice->add_option('option2', 'Option 2', [
    'url' => new moodle_url('/lib/tests/behat/fixtures/dropdown_output_testpage.php', ['foo' => 'bar']),
]);
$choice->add_option('option3', 'Option 3');
$choice->set_option_extras('option2', ['data-foo' => 'bar']);
$dialog = new core\output\local\dropdown\status('Open dialog', $choice);
echo $OUTPUT->render($dialog);
$foo = optional_param('foo', 'none', PARAM_TEXT);
echo "<p>Foo param value: $foo</p>";
echo '</div>';

echo '<div id="statussyncbutton" class="mb-4">';
echo "<h3>Sync button text</h3>";
$choice = new core\output\choicelist('Dialog content');
$choice->add_option('option1', 'Option 1', [
    'description' => 'Option 1 description',
    'icon' => new pix_icon('t/show', 'Eye icon 1')
]);
$choice->add_option('option2', 'Option 2', [
    'description' => 'Option 2 description',
    'icon' => new pix_icon('t/hide', 'Eye icon 2')
]);
$choice->add_option('option3', 'Option 3', [
    'description' => 'Option 3 description',
    'icon' => new pix_icon('t/stealth', 'Eye icon 3')
]);
$choice->set_selected_value('option2');
$dialog = new core\output\local\dropdown\status(
    'Open dialog',
    $choice,
    ['buttonsync' => true, 'updatestatus' => true]
);
echo '<button class="btn">Focus helper</button>';
echo $OUTPUT->render($dialog);
echo '</div>';

echo '<div id="statusjscontrolsection" class="mb-4">';
echo "<h3>Status JS controls</h3>";
echo '<div class="mb-2 d-flex gap-2">
    <button class="btn btn-secondary" id="setselected">Change selected value</button>
    <button class="btn btn-secondary" id="syncbutton">Enable sync</button>
    <button class="btn btn-secondary" id="updatestatus">Disable update</button>
    <span id="statusvalue"></span>';
$choice = new core\output\choicelist('Dialog content');
$choice->add_option('option1', 'Option 1', [
    'description' => 'Option 1 description',
    'icon' => new pix_icon('t/show', 'Eye icon 1')
]);
$choice->add_option('option2', 'Option 2', [
    'description' => 'Option 2 description',
    'icon' => new pix_icon('t/hide', 'Eye icon 2')
]);
$choice->add_option('option3', 'Option 3', [
    'description' => 'Option 3 description',
    'icon' => new pix_icon('t/stealth', 'Eye icon 3')
]);
$choice->set_selected_value('option2');
$dialog = new core\output\local\dropdown\status(
    'Open dialog',
    $choice,
    [
        'extras' => ['id' => 'statusjscontrols'],
        'updatestatus' => true
    ],
);
echo $OUTPUT->render($dialog);
echo '</div></div>';

$inlinejs = <<<EOF
require(
    ['core/local/dropdown/status', 'jquery'],
    (Module, jQuery) => {
        const status = Module.getDropdownStatus('#statusjscontrols');

        const printValue = () => {
            const text = 'The status value is ' + status.getSelectedValue() + '.';
            document.querySelector('#statusvalue').innerHTML = text;
        }
        printValue();

        document.querySelector('#setselected').addEventListener('click', () => {
            if (status.getSelectedValue() == 'option2') {
                status.setSelectedValue('option3');
            } else {
                status.setSelectedValue('option2');
            }
        });

        document.querySelector('#syncbutton').addEventListener('click', (e) => {
            if (status.isButtonSyncEnabled()) {
                status.setButtonSyncEnabled(false);
            } else {
                status.setButtonSyncEnabled(true);
            }
            e.target.innerHTML = (status.isButtonSyncEnabled()) ? 'Disable sync': 'Enable sync';
        });

        document.querySelector('#updatestatus').addEventListener('click', (e) => {
            if (status.isUpdateStatusEnabled()) {
                status.setUpdateStatusEnabled(false);
            } else {
                status.setUpdateStatusEnabled(true);
            }
            e.target.innerHTML = (status.isUpdateStatusEnabled()) ? 'Disable update': 'Enable update';
        });

        status.getElement().addEventListener('change', () => {
            printValue();
        });

    }
);
EOF;
$PAGE->requires->js_amd_inline($inlinejs);

echo $OUTPUT->footer();
