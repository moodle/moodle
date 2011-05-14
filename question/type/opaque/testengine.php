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
 * Page for testing that Moodle can connect to a particular Opaque engine.
 *
 * @package    qtype
 * @subpackage opaque
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(dirname(__FILE__) . '/locallib.php');

$engineid = required_param('engineid', PARAM_INT);

// Check the user is logged in.
require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/question:config', $context);

admin_externalpage_setup('qtypesettingopaque', '', null,
        new moodle_url('/question/type/opaque/testengine.php', array('engineid' => $engineid)));
$PAGE->set_title(get_string('testingengine', 'qtype_opaque'));
$PAGE->navbar->add(get_string('testingengine', 'qtype_opaque'));

// Load the engine definition.
$engine = qtype_opaque_load_engine_def($engineid);

// Do the test.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('testingengine', 'qtype_opaque'));

$ok = true;
foreach ($engine->questionengines as $engineurl) {
    echo $OUTPUT->heading(get_string('testconnectionto', 'qtype_opaque', $engineurl), 3);

    try {
        $info = qtype_opaque_get_engine_info($engineurl);
        if (is_array($info) && isset($info['engineinfo']['#'])) {
            echo xml_to_dl($info['engineinfo']['#']);
        } else {
            echo $OUTPUT->notification(get_string('testconnectionunknownreturn', 'qtype_opaque'));
            echo html_writer::tag('<pre>', s($info));
            $ok = false;
        }
    } catch (SoapFault $sf) {
        echo $OUTPUT->notification(get_string('testconnectionfailed', 'qtype_opaque'));
        echo html_writer::tag('<pre>', s($sf));
        $ok = false;
    }
}

if ($ok) {
    echo $OUTPUT->notification(get_string('testconnectionpassed', 'qtype_opaque'), 'notifysuccess');
} else {
    echo $OUTPUT->notification(get_string('testconnectionfailed', 'qtype_opaque'));
}

echo $OUTPUT->continue_button(new moodle_url('/question/type/opaque/engines.php'));
echo $OUTPUT->footer();

/**
 * @param output some XML as a <dl>.
 */
function xml_to_dl($xml) {
    $output = html_writer::start_tag('dl');
    foreach ($xml as $element => $content) {
        $output .= html_writer::tag('dt', $element) .
                html_writer::tag('dd', s($content['0']['#'])) . "\n";
    }
    $output .= html_writer::end_tag('dl');
    return $output;
}
