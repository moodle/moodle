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
require_once($CFG->dirroot . '/blocks/iomad_company_admin/lib.php');

\block_iomad_commerce\helper::require_commerce_enabled();

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.
$context = context_system::instance();

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('course_shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/checkout.php');
$shopurl = new moodle_url('/blocks/iomad_commerce/shop.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('checkout', 'block_iomad_commerce'));

// Build the nav bar.
$PAGE->navbar->add($linktext, $shopurl);
$PAGE->navbar->add(get_string('checkout', 'block_iomad_commerce'));

// JS For payment gateway.
$PAGE->requires->js_call_amd('core_payment/gateways_modal', 'init');

$data = clone $USER;
$companyid = iomad::get_my_companyid(context_system::instance());
$companyrec = $DB->get_record('company', ['id' => $companyid]);

$data->company = $companyrec->name;
$data->address = $companyrec->address;
$data->postcode = $companyrec->postcode;
$data->city = $companyrec->city;
$data->state = $companyrec->region;

$mform = new \block_iomad_commerce\forms\checkout_form($PAGE->url);
$mform->set_data($data);

$error = '';
$displaypage = 1;

$basketid = \block_iomad_commerce\helper::get_basket_id();

if (empty($basketid) || $mform->is_cancelled()) {
redirect(new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/basket.php'));

} else if ($data = $mform->get_data()) {

    $data->id = $basketid;
    $data->companyid = $companyid;

    $DB->update_record('invoice', $data, array('id' => $data->id));

    echo $OUTPUT->header();

    $baskethtml = \block_iomad_commerce\helper::get_basket_html();

    echo $baskethtml;

    $paymentoptions = core_payment\helper::gateways_modal_link_params('block_iomad_commerce', 'invoice', $basketid, trim(html_to_text(\block_iomad_commerce\helper::get_invoice_summary($basketid, 0, 0, 0))));
    $paymentoptions['class'] = 'btn btn-primary';

    echo html_writer::start_tag('p');
    echo html_writer::tag('button', get_string('sendpaymentbutton', 'enrol_fee'), $paymentoptions);
    echo " " . get_string('or', 'block_iomad_commerce') . " ";
    echo html_writer::tag('a', get_string('returntoshop', 'block_iomad_commerce'), ['class' => 'btn btn-secondary',
                                                                                    'href' => new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/shop.php')]);
    echo html_writer::end_tag('p');
    

    echo $OUTPUT->footer();
    die;

}

echo $OUTPUT->header();

echo $error;

$mform->display();

echo \block_iomad_commerce\helper::get_basket_html();

echo $OUTPUT->footer();
