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
 * Local plugin "QubitsSite"
 *
 * @package   local_qubitssite
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/local/qubitssite/locallib.php');
require_once($CFG->dirroot.'/local/qubitssite/classes/sitesearch_form.php');
global $PAGE, $OUTPUT, $SESSION;
require_login();
$context = context_system::instance();

if(!has_capability('local/qubitssite:viewtenantsite', $context)){
	print_error(get_string('accessdenied','local_qubitssite'));
}

$url = new moodle_url($CFG->wwwroot.'/local/qubitssite/index.php');

$search = optional_param('search', '', PARAM_RAW);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);
$sortcolumn = optional_param('sortcolumn', 0, PARAM_INT);
$sortdir = optional_param('sortdir', 0, PARAM_INT);
$publish = optional_param('publish', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname','local_qubitssite'));
$PAGE->set_heading(get_string('pluginname','local_qubitssite'));
$PAGE->navbar->add(get_string('pluginname','local_qubitssite'), $url, navigation_node::TYPE_SETTING );
$PAGE->requires->jquery();

if($publish > 0){
    $status = local_qubitssite_publish_site($publish);
	if($status == true){
		$msg = "Successfully updated!";
	} else {
		$msg = "Unable to process!";
	}
	$args = array('search' => $search,'sortdir' => $sortdir, 'sortcolumn' => $sortcolumn, 'page' => $page);
    $urlredirect = new moodle_url($CFG->wwwroot.'/local/qubitssite/index.php',$args);
    purge_all_caches();
    redirect($urlredirect, $msg);
}

$args = array('search' => $search);
$mform = new sitesearch_form(null, $args);
if ($mform->is_cancelled()) {
	$urlredirect = new moodle_url($CFG->wwwroot.'/local/qubitssite/index.php');
	redirect($urlredirect);
} else if ($mform->get_data()) {
	$formdata = data_submitted();
	$search = $formdata->search;
}

echo $OUTPUT->header();
echo "<h3>".get_string("siteslist", "local_qubitssite")."</h3>";
echo $mform->display();
echo local_qubits_site_render_list($search, $page, $perpage, $sortcolumn, $sortdir);
echo $OUTPUT->footer();