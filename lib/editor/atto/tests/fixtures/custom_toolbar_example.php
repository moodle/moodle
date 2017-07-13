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
 * Demonstrates use of Atto editor with overridden toolbar setting.
 *
 * This fixture is only used by the Behat test.
 *
 * @package editor_atto
 * @copyright 2016 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../../config.php');
require_once($CFG->dirroot . '/lib/editor/atto/lib.php');

// Behat test fixture only.
defined('BEHAT_SITE_RUNNING') || die('Only available on Behat test server');

$PAGE->set_url('/lib/editor/atto/tests/fixtures/override_plugins_example.php');
$PAGE->set_context(context_system::instance());

echo $OUTPUT->header();

// If this was sending some input, display it.
$normal = optional_param('normaleditor', '', PARAM_RAW);
$special = optional_param('specialeditor', '', PARAM_RAW);
if ($normal !== '' || $special !== '') {
    echo html_writer::start_div('normalresult');
    echo s($normal);
    echo html_writer::end_div();
    echo html_writer::start_div('specialresult');
    echo s($special);
    echo html_writer::end_div();
} else {
    // Create a form.
    echo html_writer::start_tag('form', array('method' => 'post', 'action' => 'custom_toolbar_example.php'));
    echo html_writer::start_div();

    // Basic editor options.
    $options = array();
    $atto = new atto_texteditor();

    // Normal Atto.
    echo html_writer::start_div('normaldiv');
    echo $OUTPUT->heading('Normal Atto');
    echo html_writer::div(html_writer::tag('textarea', '',
            array('id' => 'normaleditor', 'name' => 'normaleditor', 'rows' => 10)));
    $atto->use_editor('normaleditor', $options);
    echo html_writer::end_div();

    // Second Atto with custom options.
    echo html_writer::start_div('specialdiv');
    $options['atto:toolbar'] = <<<EOT
style1 = bold, italic
list = unorderedlist, orderedlist
EOT;
    echo $OUTPUT->heading('Special Atto');
    echo html_writer::div(html_writer::tag('textarea', '',
            array('id' => 'specialeditor', 'name' => 'specialeditor', 'rows' => 10)));
    $atto->use_editor('specialeditor', $options);
    echo html_writer::end_div();

    // Button to submit form.
    echo html_writer::start_div('', array('style' => 'margin-top: 20px'));
    echo html_writer::tag('button', 'Submit and see the HTML');
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_tag('form');
}

echo $OUTPUT->footer();
