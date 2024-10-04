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
 * Test page for the collapsable section output component.
 *
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @package    core
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');

use core\output\local\collapsable_section;

defined('BEHAT_SITE_RUNNING') || die();

/**
 * Generate the title content.
 *
 * @param string $content The content to be displayed inside the button.
 * @return string
 */
function title_content(string $content): string {
    return ucfirst($content) . ' title';
}

/**
 * Generate the section content.
 *
 * @param string $content The content to be displayed inside the dialog.
 * @return string
 */
function section_content(string $content): string {
    global $OUTPUT;
    $icon = $OUTPUT->pix_icon('t/hide', 'Eye icon');
    return '
        <p>This is the ' . $content . ' content.</p>
        <p>Some rich content <a href="">Link</a> ' . $icon . '.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
        tempor <b>incididunt ut labore et dolore magna aliqua</b>. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
        consequat.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
        consequat.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
        consequat.</p>
    ';
}

global $CFG, $PAGE, $OUTPUT;
$PAGE->set_url('/lib/tests/behat/fixtures/collapsable_section_output_testpage.php');
$PAGE->add_body_class('limitedwidth');
require_login();
$PAGE->set_context(core\context\system::instance());
$PAGE->set_title('Collapsable section test page');

echo $OUTPUT->header();

echo "<h2>Collapsable section test page</h2>";
echo $OUTPUT->paragraph('This page is used to test the collapsable section output component.');

$sample = 'closed section';
echo '<div id="closedsection" class="mb-4 border border-1 rounded-3 p-3">';
echo $OUTPUT->paragraph($sample . ' example');
$collapsable = new collapsable_section(
    titlecontent: title_content($sample),
    sectioncontent: section_content($sample),
);
echo $OUTPUT->render($collapsable);
echo '</div>';

$sample = 'open section';
echo '<div id="opensection" class="mb-4 border border-1 rounded-3 p-3">';
echo $OUTPUT->paragraph($sample . ' example');
$collapsable = new collapsable_section(
    titlecontent: title_content($sample),
    sectioncontent: section_content($sample),
    open: true,
);
echo $OUTPUT->render($collapsable);
echo '</div>';

$sample = 'extra classes';
echo '<div id="extraclasses" class="mb-4 border border-1 rounded-3 p-3">';
echo $OUTPUT->paragraph($sample . ' example');
$collapsable = new collapsable_section(
    titlecontent: title_content($sample),
    sectioncontent: section_content($sample),
    classes: 'bg-dark text-white p-3 rounded-3 extraclass',
);
echo $OUTPUT->render($collapsable);
echo '</div>';

$sample = 'extra attributes';
echo '<div id="extraattributes" class="mb-4 border border-1 rounded-3 p-3">';
echo $OUTPUT->paragraph($sample . ' example');
$collapsable = new collapsable_section(
    titlecontent: title_content($sample),
    sectioncontent: section_content($sample),
    extras: ['data-foo' => 'bar', 'id' => 'myid'],
);
echo $OUTPUT->render($collapsable);
echo '</div>';

$sample = 'custom labels';
echo '<div id="customlabels" class="mb-4 border border-1 rounded-3 p-3">';
echo $OUTPUT->paragraph($sample . ' example');
$collapsable = new collapsable_section(
    titlecontent: title_content($sample),
    sectioncontent: section_content($sample),
    expandlabel: 'Custom expand',
    collapselabel: 'Custom collapse',
);
echo $OUTPUT->render($collapsable);
echo '</div>';

$sample = 'javascript controls';
echo '<div id="jscontrols" class="mb-4 border border-1 rounded-3 p-3">';
echo $OUTPUT->paragraph($sample . ' example');
echo '
<div class="d-flex justify-content-center">
    <button class="btn btn-secondary mx-2" id="toggleBtn">Toggle</button>
    <button class="btn btn-secondary mx-2" id="showBtn">Show</button>
    <button class="btn btn-secondary mx-2" id="hideBtn">Hide</button>
    <button class="btn btn-secondary mx-2" id="testBtn">Test state</button>
    <div class="d-flex align-content-center mx-2 rounded p-2 border">
        Current state: <div class="d-inline-block" id="state">?</div>
    </div>
</div>';
echo '<div class="rounded my-2 p-2 border">
        Last event: <div class="d-inline-block" id="lastevent">?</div>
    </div>';
$collapsable = new collapsable_section(
    titlecontent: title_content($sample),
    sectioncontent: section_content($sample),
    extras: ['id' => 'jsCollapsable'],
);
echo $OUTPUT->render($collapsable);
echo '</div>';

$inlinejs = <<<EOF
    require(
        [
            'core/local/collapsable_section/controls',
            'core/local/collapsable_section/events'
        ],
        function(
            CollapsableSection,
            events
        ) {

            const section = CollapsableSection.instanceFromSelector('#jsCollapsable');

            document.getElementById('toggleBtn').addEventListener('click', function() {
                section.toggle();
            });

            document.getElementById('showBtn').addEventListener('click', function() {
                section.show();
            });

            document.getElementById('hideBtn').addEventListener('click', function() {
                section.hide();
            });

            document.getElementById('testBtn').addEventListener('click', function() {
                document.getElementById('state').textContent = section.isVisible() ? 'visible' : 'hidden';
            });

            const jscontrolregion = document.getElementById('jscontrols');

            jscontrolregion.addEventListener(events.eventTypes.shown, function() {
                document.getElementById('lastevent').textContent = 'Section shown';
            });

            jscontrolregion.addEventListener(events.eventTypes.hidden, function() {
                document.getElementById('lastevent').textContent = 'Section hidden';
            });
        }
    );
EOF;

$PAGE->requires->js_amd_inline($inlinejs);

echo '</div>';
echo $OUTPUT->footer();
