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
 * @package filter_oembed
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 The POET Group
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);
admin_externalpage_setup('filter_oembed_providers');

$action = optional_param('action', '', PARAM_ALPHA);
$pid = optional_param('pid', 0, PARAM_INT);

if (!empty($action)) {
    require_sesskey();
}

$PAGE->requires->js_call_amd('filter_oembed/manageproviders', 'init');

$oembed = \filter_oembed\service\oembed::get_instance('all');

// Process actions.
switch ($action) {
    case 'edit':
        break;

    case 'disable':
        $oembed->disable_provider($pid);
        break;

    case 'enable':
        $oembed->enable_provider($pid);
        break;

    case 'delete':
        $oembed->delete_provider($pid);
        break;
}

$PAGE->set_context($systemcontext);
$baseurl = new moodle_url('/filter/oembed/manageproviders.php');
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('standard');
$strmanage = get_string('manageproviders', 'filter_oembed');
$PAGE->set_title($strmanage);
$PAGE->set_heading($strmanage);
$PAGE->requires->strings_for_js(
    [
        'deleteprovidertitle',
        'deleteproviderconfirm'
    ],
    'filter_oembed'
);

$output = $PAGE->get_renderer('filter_oembed');
echo $output->header();

$managepage = new \filter_oembed\output\managementpage($oembed->providers);
echo $output->render($managepage);

// Finish the page.
echo $output->footer();
