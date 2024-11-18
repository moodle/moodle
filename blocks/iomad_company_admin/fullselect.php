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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( '../../config.php');

// We always require users to be logged in for this page.
require_login();

// Get parameters.
$search = optional_param('search', '', PARAM_ALPHANUM);
$systemcontext = context_system::instance();
$companycontext = $systemcontext;

$company = $SESSION->currenteditingcompany;

if (!empty($company)) {
    $companycontext =  \core\context\company::instance($company);
}

$url = new moodle_url('/blocks/iomad_company_admin/fullselect.php');

// Page setup stuff.
$PAGE->set_context($companycontext);
$PAGE->set_url($url);
$PAGE->set_pagelayout('base');
$PAGE->set_title(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->requires->js_call_amd('block_iomad_company_admin/admin', 'init');
// Renderer
$renderer = $PAGE->get_renderer('block_iomad_company_admin');

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the page heading.
$PAGE->set_heading(get_string('selectacompany', 'block_iomad_company_admin'));

$full_companies_select = new block_iomad_company_admin\output\full_companies_select(['search' => $search]);
$companysearchform = new iomad_company_search_form($url, []);

echo $output->header();

echo html_writer::start_tag('p');
$companysearchform->display();
echo html_writer::end_tag('p');


echo $output->render($full_companies_select);

echo $output->footer();