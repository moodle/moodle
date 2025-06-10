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
 * The Generation 1 to Generation 2 logic for Panopto
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
require_once(dirname(__FILE__) . '/classes/panopto_upgrade_all_folders_form.php');
require_once(dirname(__FILE__) . '/lib/panopto_data.php');
require_once(dirname(__FILE__) . '/lib/block_panopto_bulk_lib.php');

require_login();

$context = context_system::instance();

$PAGE->set_context($context);

$returnurl = optional_param('return_url', $CFG->wwwroot . '/admin/settings.php?section=blocksettingpanopto', PARAM_LOCALURL);

$urlparams['return_url'] = $returnurl;

$PAGE->set_url('/blocks/panopto/upgrade_all_folders.php', $urlparams);
$PAGE->set_pagelayout('base');

// Check System context capability before allowing to upgrade the folders.
require_capability('block/panopto:provision_multiple', $context);

$mform = new panopto_upgrade_all_folders_form($PAGE->url);

$upgradetitle = get_string('block_global_upgrade_all_folders', 'block_panopto');
$PAGE->set_pagelayout('base');
$PAGE->set_title($upgradetitle);
$PAGE->set_heading($upgradetitle);

$manageblocks = new moodle_url('/admin/blocks.php');
$panoptosettings = new moodle_url('/admin/settings.php?section=blocksettingpanopto');
$PAGE->navbar->add(get_string('blocks'), $manageblocks);
$PAGE->navbar->add(get_string('pluginname', 'block_panopto'), $panoptosettings);

$PAGE->navbar->add($upgradetitle, new moodle_url($PAGE->url));

echo $OUTPUT->header();

if ($mform->is_cancelled()) {
    redirect(new moodle_url($returnurl));
} else if ($mform->get_data()) {
    // Calling this with null parameter should upgrade everything.
    panopto_upgrade_all_folders(null);

    echo "<a href='$returnurl'>" . get_string('back_to_config', 'block_panopto') . '</a>';
} else {
    include('views/bulk_task_reprovision_warning.html.php');

    $mform->display();
}

echo $OUTPUT->footer();
/* End of file upgrade_all_folders.php */
