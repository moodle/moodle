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
 * This page lets admins manage the list of known remote Opaque engines.
 *
 * @package    qtype
 * @subpackage opaque
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(dirname(__FILE__) . '/locallib.php');

// Check the user is logged in.
require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/question:config', $context);

admin_externalpage_setup('qtypesettingopaque');

// See if any action was requested.
$delete = optional_param('delete', 0, PARAM_INT);
if ($delete) {
    $engine = $DB->get_record('question_opaque_engines', array('id' => $delete), '*', MUST_EXIST);
    if (optional_param('confirm', false, PARAM_BOOL) && confirm_sesskey()) {
        qtype_opaque_delete_engine_def($delete);
        redirect($PAGE->url);
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('deleteconfigareyousure', 'qtype_opaque',
                format_string($engine->name)),
                new moodle_url('/question/type/opaque/engines.php',
                        array('delete' => $delete, 'confirm' => 'yes', 'sesskey' => sesskey())),
                $PAGE->url);
        echo $OUTPUT->footer();
        exit;
    }
}

// Get the list of configured engines.
$engines = $DB->get_records('question_opaque_engines', array(), 'id ASC');

// Header.
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help(get_string('configuredquestionengines', 'qtype_opaque'),
        'configuredquestionengines', 'qtype_opaque');

// List of configured engines.
if ($engines) {
    $strtest = get_string('testconnection', 'qtype_opaque');
    $stredit = get_string('edit');
    $strdelete = get_string('delete');

    foreach ($engines as $engine) {
        echo html_writer::tag('p', format_string($engine->name) .
                $OUTPUT->action_icon(new moodle_url('/question/type/opaque/testengine.php',
                        array('engineid' => $engine->id)),
                        new pix_icon('t/preview', $strtest)) .
                $OUTPUT->action_icon(new moodle_url('/question/type/opaque/editengine.php',
                        array('engineid' => $engine->id)),
                        new pix_icon('t/edit', $stredit)) .
                $OUTPUT->action_icon(new moodle_url('/question/type/opaque/engines.php',
                        array('delete' => $engine->id)),
                        new pix_icon('t/delete', $strdelete)));
    }
} else {
    echo html_writer::tag('p', get_string('noengines', 'qtype_opaque'));
}

// Add new engine link.
echo html_writer::tag('p', html_writer::link(new moodle_url('/question/type/opaque/editengine.php'),
        get_string('addengine', 'qtype_opaque')));

// Footer.
echo $OUTPUT->footer();
