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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once('lib.php');
require_once(dirname(__FILE__) . '/processor/processor.php');

require_commerce_enabled();

$invoicereference = required_param('u', PARAM_CLEAN);

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.

$context = context_system::instance();

// Correct the navbar .
// Set the name for the page.
$linktext = get_string('course_shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/shop.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('confirmation', 'block_iomad_commerce'));

// Build the nav bar.
$PAGE->navbar->add($linktext, $linkurl);
$PAGE->navbar->add(get_string('confirmation', 'block_iomad_commerce'));


echo $OUTPUT->header();

$invoice = get_invoice_by_reference($invoicereference);
$pp = get_payment_provider_instance($invoice->checkout_method);
echo $pp->get_confirmation_html($invoice);
echo get_invoice_html($invoice->id);

// Check if the user has a company.
if (empty($USER->profile['company'])) {
    if (!$company = $DB->get_record('company', array('name' => $invoice->company))) {
        $company = new stdclass();
        $company->name = $invoice->company;
        $company->shortname = preg_replace('~\b(\w)|.~', '$1', $company->name);
        // Does this shortname already exist?
        if ($count = $DB->get_record_sql("SELECT count(id) AS count
                                          FROM {company}
                                          WHERE shortname LIKE '".$company->shortname."%'")) {
            $count++;
            $company->shortname = $company->shortname.$count->count;
        }
        $company->country = $invoice->country;
        $company->city = $invoice->city;
        $companyid = $DB->insert_record('company', $company);

        // Set up default department.
        company::initialise_departments($companyid);
        $company->id = $companyid;

        //  Set up a profiles field category for this company.
        $catdata = new stdclass();
        $catdata->sortorder = $DB->count_records('user_info_category') + 1;
        $catdata->name = $company->shortname;
        $DB->insert_record('user_info_category', $catdata, false);

        // Set up course category for company.
        $coursecat = new stdclass();
        $coursecat->name = $company->name;
        $coursecat->sortorder = 999;
        $coursecat->id = $DB->insert_record('course_categories', $coursecat);
        $coursecat->context = context_coursecat::instance($coursecat->id);
        $categorycontext = $coursecat->context;
        $coursecat->context->mark_dirty();
        $DB->update_record('course_categories', $coursecat);
        fix_course_sortorder();
        $companydetails = $DB->get_record('company', array('id' => $company->id));
        $companydetails->category = $coursecat->id;
        $DB->update_record('company', $companydetails);
    }
    // Add user to default company department.
    $USER->profile_field_company = $company->shortname;
    profile_save_data($USER);
    $companydepartment = company::get_company_parentnode($company->id);
    company::assign_user_to_department($companydepartment->id, $USER->id);
}

if ($invoice->status == INVOICESTATUS_PAID) {
    processor::trigger_onordercomplete($invoice);
}

echo $OUTPUT->footer();
