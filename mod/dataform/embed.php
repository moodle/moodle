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
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$urlparams = new stdClass;
$urlparams->d = optional_param('d', 0, PARAM_INT);
$urlparams->id = optional_param('id', 0, PARAM_INT);

// Current view id.
$urlparams->view = optional_param('view', 0, PARAM_INT);
// Current filter ('filtid' is used in the action url of entries form due to conflicts with 'filter' in moodleforms).
$urlparams->filter = optional_param('filter', optional_param('filtid', 0, PARAM_INT), PARAM_INT);
$urlparams->pagelayout = optional_param('pagelayout', '', PARAM_ALPHAEXT);
$urlparams->refresh = optional_param('refresh', 0, PARAM_INT);
$urlparams->renew = optional_param('renew', 0, PARAM_INT);

// Set a dataform object with guest autologin.
$df = mod_dataform_dataform::instance($urlparams->d, $urlparams->id, true);

$pageparams = array(
        'js' => true,
        'css' => true,
        'rss' => true,
        'completion' => true,
        'comments' => true,
        'pagelayout' => 'embedded',
        'urlparams' => $urlparams);
$df->set_page('embed', $pageparams);

// Activate navigation node.
$currentviewid = $urlparams->view ? $urlparams->view : $df->defaultview;
if ($currentviewid) {
    navigation_node::override_active_url(new moodle_url('/mod/dataform/view.php', array('d' => $df->id, 'view' => $currentviewid)));
}

$df->process_data();

$output = $df->get_renderer();
$headerparams = array(
        'groups' => true,
        'urlparams' => $urlparams);
echo $output->header($headerparams);

echo $df->display();

echo $output->footer();
