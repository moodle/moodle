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
 * @package mod_dataform
 * @category filter
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

$urlparams = new stdClass;
$urlparams->d = required_param('d', PARAM_INT);
$urlparams->view = required_param('view', PARAM_INT);       // view id
$urlparams->pagefile = required_param('pagefile', PARAM_TEXT);       // df page file
$urlparams->fid = optional_param('fid', mod_dataform_filter_manager::USER_FILTER_ADVANCED, PARAM_INT);       // view id

// Set a dataform object.
$df = mod_dataform_dataform::instance($urlparams->d);
$df->set_page($urlparams->pagefile, array('urlparams' => $urlparams));
// Require_capability('mod/dataform:advancedfilter', $df->context);.

// Activate navigation node
// navigation_node::override_active_url(new moodle_url('/mod/dataform/filter/editadvanced.php', array('d' => $df->id)));.

$fm = mod_dataform_filter_manager::instance($df->id);

$pagefile = $urlparams->pagefile;
$view = $df->view_manager->get_view_by_id($urlparams->view);
$filter = $fm->get_filter_by_id($urlparams->fid, array('view' => $view));

$mform = $fm->get_advanced_filter_form($filter, $view, $pagefile);

if ($mform->is_cancelled()) {
    redirect(new moodle_url("/mod/dataform/$pagefile.php", array('d' => $df->id, 'view' => $view->id)));
}

if ($data = $mform->get_submitted_data()) {
    $filter = $fm->get_filter_from_form($filter, $data, true);
    $mform = $fm->get_advanced_filter_form($filter, $view, $pagefile);

    $save = !empty($data->submitbutton);
    $newfilter = !empty($data->submitbutton_new);

    if (($save or $newfilter) and $data = $mform->get_data()) {
        $filter = $fm->get_filter_from_form($filter, $data, true);
        if ($filter = $fm->set_advanced_filter($filter, $view, $newfilter)) {
            $redirecturl = $view->baseurl;
            $redirecturl->param('filter', $filter->id);
            redirect($redirecturl);
        }
    }
}

$output = $df->get_renderer();
$headerparams = array(
        'heading' => $df->name,
        'tab' => 'browse',
        'groups' => true,
        'urlparams' => $urlparams);
echo $output->header($headerparams);

$streditinga = $filter->id ? get_string('filteredit', 'dataform', $filter->name) : get_string('filternew', 'dataform');
echo html_writer::tag('h2', format_string($streditinga), array('class' => 'mdl-align'));

// Display form.
$mform->display();

echo $output->footer();
