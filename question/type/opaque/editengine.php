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
 * Page for editing the configuration of a particular Opaque engine.
 *
 * @package    qtype
 * @subpackage opaque
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/edit_engine_form.php');

$engineid = optional_param('engineid', 0, PARAM_INT);

// Check the user is logged in.
require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/question:config', $context);

admin_externalpage_setup('qtypesettingopaque', '', null,
        new moodle_url('/question/type/opaque/editengine.php', array('engineid' => $engineid)));
$PAGE->set_title(get_string('editquestionengine', 'qtype_opaque'));
$PAGE->navbar->add(get_string('editquestionengineshort', 'qtype_opaque'));

// Create form.
$mform = new qtype_opaque_engine_edit_form('editengine.php');

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/question/type/opaque/engines.php'));

} else if ($data = $mform->get_data()) {
    $engine = new stdClass();
    if (!empty($data->engineid)) {
        $engine->id = $data->engineid;
    }
    $engine->name = $data->enginename;
    $engine->passkey = trim($data->passkey);
    $engine->questionengines = $mform->extracturllist($data, 'questionengineurls');
    $engine->questionbanks = $mform->extracturllist($data, 'questionbankurls');
    qtype_opaque_save_engine_def($engine);
    redirect(new moodle_url('/question/type/opaque/engines.php'));
}

// Prepare defaults.
$defaults = new stdClass();
$defaults->engineid = $engineid;
if ($engineid) {
    $engine = qtype_opaque_load_engine_def($engineid);
    $defaults->enginename = $engine->name;
    $defaults->questionengineurls = implode("\n", $engine->questionengines);
    $defaults->questionbankurls = implode("\n", $engine->questionbanks);
    $defaults->passkey = $engine->passkey;
}
$mform->set_data($defaults);

// Display the form.
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help(get_string('editquestionengine', 'qtype_opaque'),
        'editquestionengine', 'qtype_opaque');
$mform->display();
echo $OUTPUT->footer();
