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
 * The code used to build the Moodle category structure on Panopto
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2017
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
global $CFG;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once(dirname(__FILE__) . '/../../config.php');
}
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/classes/panopto_build_category_structure_form.php');
require_once(dirname(__FILE__) . '/lib/block_panopto_lib.php');
require_once(dirname(__FILE__) . '/lib/panopto_data.php');
require_once(dirname(__FILE__) . '/lib/panopto_category_data.php');

// Populate list of servernames to select from.
$aserverarray = [];
$appkeyarray = [];

$numservers = get_config('block_panopto', 'server_number');
$numservers = isset($numservers) ? $numservers : 0;

// Increment numservers by 1 to take into account starting at 0.
++$numservers;

for ($serverwalker = 1; $serverwalker <= $numservers; ++$serverwalker) {

    // Generate strings corresponding to potential servernames in the config.
    $thisservername = get_config('block_panopto', 'server_name' . $serverwalker);
    $thisappkey = get_config('block_panopto', 'application_key' . $serverwalker);

    $hasservername = !panopto_is_string_empty($thisservername);
    if ($hasservername && !panopto_is_string_empty($thisappkey)) {
        $aserverarray[$serverwalker - 1] = $thisservername;
        $appkeyarray[$serverwalker - 1] = $thisappkey;
    }
}

// If only one server, simply provision with that server. Setting these values will circumvent loading the selection form
// prior to provisioning.
if (count($aserverarray) == 1) {
    // Get first element from associative array. aServerArray and appKeyArray will have same key values.
    $key = array_keys($aserverarray);
    $selectedserver = trim($aserverarray[$key[0]]);
    $selectedkey = trim($appkeyarray[$key[0]]);
}

require_login();

/**
 * The category structure process workhorse function
 *
 * @param string $selectedserver server name
 * @param string $selectedkey selected key
 */
function build_category_structure($selectedserver, $selectedkey) {
    global $DB;

    $defaultmaxtime = ini_get('max_execution_time');

    $twohoursinseconds = 7200;

    set_time_limit($twohoursinseconds);

    panopto_category_data::build_category_structure(true, $selectedserver, $selectedkey);

    set_time_limit($defaultmaxtime);
}

$context = context_system::instance();

$PAGE->set_context($context);

$returnurl = optional_param('return_url', $CFG->wwwroot . '/admin/settings.php?section=blocksettingpanopto', PARAM_LOCALURL);

$urlparams['return_url'] = $returnurl;

$PAGE->set_url('/blocks/panopto/build_category_structure.php', $urlparams);
$PAGE->set_pagelayout('base');

// Check System context capability before allowing to build the category structure.
require_capability('block/panopto:provision_multiple', $context);

$mform = new panopto_build_category_structure_form($PAGE->url);

if ($mform->is_cancelled()) {
    redirect(new moodle_url($returnurl));
} else {
    $buildcategorytitle = get_string('block_global_build_category_structure', 'block_panopto');
    $PAGE->set_pagelayout('base');
    $PAGE->set_title($buildcategorytitle);
    $PAGE->set_heading($buildcategorytitle);

    $manageblocks = new moodle_url('/admin/blocks.php');
    $panoptosettings = new moodle_url('/admin/settings.php?section=blocksettingpanopto');
    $PAGE->navbar->add(get_string('blocks'), $manageblocks);
    $PAGE->navbar->add(get_string('pluginname', 'block_panopto'), $panoptosettings);

    $PAGE->navbar->add($buildcategorytitle, new moodle_url($PAGE->url));

    echo $OUTPUT->header();

    if ($data = $mform->get_data()) {
        $selectedserver = trim($aserverarray[$data->servers]);
        $selectedkey = trim($appkeyarray[$data->servers]);

        build_category_structure($selectedserver, $selectedkey);

        echo "<a href='$returnurl'>" . get_string('back_to_config', 'block_panopto') . '</a>';
    } else {
        $mform->display();
    }

    echo $OUTPUT->footer();
}

/* End of file build_category_structure.php */
