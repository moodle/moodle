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
 * @package dataformtool
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');

$urlparams = new stdClass;

$urlparams->d = optional_param('d', 0, PARAM_INT);
$urlparams->id = optional_param('id', 0, PARAM_INT);

// Views list actions.
$urlparams->run = optional_param('run', '', PARAM_PLUGIN);  // tool plugin to run

$urlparams->confirmed = optional_param('confirmed', 0, PARAM_INT);

// Set a dataform object.
$df = mod_dataform_dataform::instance($urlparams->d, $urlparams->id);
$df->require_manage_permission('tools');

$df->set_page('tool/index', array('urlparams' => $urlparams));
$PAGE->set_context($df->context);

// Activate navigation node.
navigation_node::override_active_url(new moodle_url('/mod/dataform/tool/index.php', array('id' => $df->cm->id)));

// DATA PROCESSING.
if ($urlparams->run and confirm_sesskey()) {
    // Run selected tool.
    $toolclass = "dataformtool_{$urlparams->run}_tool";

    $toolclass::execute($df);
}

// Get the list of tools.
$tools = array();
foreach (array_keys(core_component::get_plugin_list('dataformtool')) as $subpluginname) {
    $tools[$subpluginname] = (object) array(
        'name' => get_string('pluginname', "dataformtool_$subpluginname"),
        'description' => get_string('pluginname_help', "dataformtool_$subpluginname")
    );
}
ksort($tools);    // sort in alphabetical order

// Any notifications?
if (!$tools) {
    $df->notifications = array('problem' => array('toolnoneindataform' => get_string('toolnoneindataform', 'dataform')));
}

$output = $df->get_renderer();
echo $output->header(array('tab' => 'tools', 'heading' => $df->name, 'urlparams' => $urlparams));

// If there are tools print admin style list of them.
if ($tools) {
    $actionbaseurl = '/mod/dataform/tool/index.php';
    $linkparams = array('d' => $df->id, 'sesskey' => sesskey());

    // Table headings.
    $strname = get_string('name');
    $strdesc = get_string('description');
    $strrun = get_string('toolrun', 'dataform');

    $table = new html_table();
    $table->head = array($strname, $strdesc, $strrun);
    $table->align = array('left', 'left', 'center');
    $table->wrap = array(false, false, false);
    $table->attributes['align'] = 'center';

    foreach ($tools as $dir => $tool) {

        $runlink = html_writer::link(new moodle_url($actionbaseurl, $linkparams + array('run' => $dir)),
                        $OUTPUT->pix_icon('t/addgreen', $strrun));

        $table->data[] = array(
            $tool->name,
            $tool->description,
            $runlink,
        );
    }
    echo html_writer::table($table);
}

echo $output->footer();
