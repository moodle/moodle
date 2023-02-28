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

require_once('../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/local/qubitssite/locallib.php');
require_once($CFG->dirroot.'/local/qubitssite/classes/qubitssite_edit_form.php');
global $DB, $PAGE, $OUTPUT;
require_login();
$context = context_system::instance();
$id = optional_param('id', 0, PARAM_INT);    // Site id.
$returnto = optional_param('returnto', 0, PARAM_ALPHANUM); // Generic navigation return page switch.
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL); // A return URL. returnto must also be set to 'url'.
$straddnewsite = get_string("addnewsite", "local_qubitssite");
$streditsitesettings = get_string("editsitesettings", "local_qubitssite");

if(empty($id) && !(has_capability('local/qubitssite:createtenantsite', $context))){
    print_error(get_string('accessdenied','local_qubitssite'));
}

if(!empty($id) && !(has_capability('local/qubitssite:edittenantsite', $context))){
    print_error(get_string('accessdenied','local_qubitssite'));
}


if ($returnto === 'url' && confirm_sesskey() && $returnurl) {
    // If returnto is 'url' then $returnurl may be used as the destination to return to after saving or cancelling.
    // Sesskey must be specified, and would be set by the form anyway.
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = new moodle_url($CFG->wwwroot . '/local/qubitssite/index.php');
    if ($returnto !== 0) {
        switch ($returnto) {
            case 'sitelisting':
                $returnurl = new moodle_url($CFG->wwwroot . '/local/qubitssite/index.php');
                break;
        }
    }
}

$PAGE->set_pagelayout('admin');
$pageparams = array();
$qubitssite = null;
if ($id) {
    $qubitssite = $DB->get_record("local_qubits_sites", array('id' => $id));
    $pageparams = array('id' => $id);
    if($qubitssite) {
        $title = get_string('title','local_qubitssite', ' - '.$qubitssite->name);
        $heading = get_string('title','local_qubitssite', ' - '.$qubitssite->name);
        $pagedesc = $streditsitesettings;
    } else {
        throw new \moodle_exception('invalidqubitssiteid');
    }
}else{
    $title = get_string('title','local_qubitssite', $straddnewsite);
    $heading = get_string('title','local_qubitssite', ' - '.$straddnewsite);
    $pagedesc = $straddnewsite; 
}

if ($returnto !== 0) {
    $pageparams['returnto'] = $returnto;
    if ($returnto === 'url' && $returnurl) {
        $pageparams['returnurl'] = $returnurl;
    }
}

$PAGE->set_url('/local/qubitssite/edit.php', $pageparams);
$PAGE->set_context($context);
$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);

// First create the form.
$args = array(
    'qubitssite' => $qubitssite,
    'editoroptions' => $editoroptions,
    'returnto' => $returnto,
    'returnurl' => $returnurl,
    'context' => $context
);

$editform = new qubitssite_edit_form(null, $args);
if ($editform->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
    redirect($returnurl);
} else if ($data = $editform->get_data()) {
    // Process data if submitted.
    if (empty($qubitssite->id)) {
        $qubitssite = local_qubitssite_create_site($data, $editoroptions);
        $qubitssiteurl = new moodle_url('/local/qubitssite/view.php', array('id' => $qubitssite->id));
    }else {
        $qubitssite = local_qubitssite_update_site($data, $editoroptions);
        $qubitssiteurl = new moodle_url('/local/qubitssite/view.php', array('id' => $qubitssite->id));
    }

    if (isset($data->saveanddisplay)) {
        // Redirect user to newly created/updated Qubits site.
        redirect($qubitssiteurl);
    } else {
        // Save and return. Take them back to wherever.
        redirect($returnurl);
    }
}
$PAGE->set_title($title);
$PAGE->add_body_class('limitedwidth');
$PAGE->set_heading($heading);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagedesc);

$editform->display();

echo $OUTPUT->footer();