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

/**
 * Script to let a user create a course for a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib/course_selectors.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');
require_once('lib.php');
require_once('processor/processor.php');

require_commerce_enabled();

class order_edit_form extends moodleform {
    protected $invoiceid = 0;
    protected $context = null;

    public function __construct($actionurl, $invoiceid) {
        global $CFG;

        $this->invoiceid = $invoiceid;
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $strrequired = get_string('required');

        $mform->addElement('hidden', 'id', $this->invoiceid);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('header', 'header', get_string('order', 'block_iomad_commerce'));

        $mform->addElement('static', 'reference', get_string('reference', 'block_iomad_commerce'));

        $choices = array();
        foreach (array(INVOICESTATUS_UNPAID, INVOICESTATUS_PAID) as $status) {
            $choices[$status] = get_string('status_' . $status, 'block_iomad_commerce');
        }
        $mform->addElement('select', 'status', get_string('status'), $choices);
        $mform->addRule('status', $strrequired, 'required', null, 'client');

        $mform->addElement('header', 'header', get_string('purchaser_details', 'block_iomad_commerce'));

        $mform->addElement('static', 'firstname', get_string('firstname'));

        $mform->addElement('static', 'lastname', get_string('lastname'));
        $mform->addElement('static', 'company', get_string('company', 'block_iomad_company_admin'));
        $mform->addElement('static', 'address', get_string('address'));
        $mform->addElement('static', 'city', get_string('city'));
        $mform->addElement('static', 'postcode', get_string('postcode', 'block_iomad_commerce'));
        $mform->addElement('static', 'state', get_string('state', 'block_iomad_commerce'));
        $mform->addElement('static', 'country', get_string('selectacountry'));
        $mform->addElement('static', 'email', get_string('email'));
        $mform->addElement('static', 'phone1', get_string('phone'));

        $mform->addElement('header', 'header', get_string('basket', 'block_iomad_commerce'));

        $mform->addElement('html', '<p>' . get_string('process_help', 'block_iomad_commerce') . '</p>');
        $mform->addElement('html', get_invoice_html($this->invoiceid, 0, 0, 1));

        $mform->addElement('header', 'header', get_string('paymentprocessing', 'block_iomad_commerce'));

        $mform->addElement('static', 'checkout_method', get_string('paymentprovider', 'block_iomad_commerce'));

        foreach (array('pp_payerid', 'pp_ordertime', 'pp_payerstatus', 'pp_transactionid', 'pp_ack',
                      'pp_transactiontype', 'pp_paymenttype', 'pp_currencycode', 'pp_amount',
                      'pp_feeamt', 'pp_settleamt', 'pp_taxamt', 'pp_exchangerrate',
                      'pp_paymentstatus', 'pp_pendingreason', 'pp_reason') as $ppfield) {
            $mform->addElement('static', $ppfield, get_string($ppfield, 'block_iomad_commerce'));
        }

        $this->add_action_buttons();
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$invoiceid = required_param('id', PARAM_INTEGER);

$context = context_system::instance();
require_login();

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/blocks/iomad_commerce/orderlist.php', $urlparams);

$invoice = get_invoice($invoiceid);

iomad::require_capability('block/iomad_commerce:admin_view', $context);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('orders', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/orderlist.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('edit_invoice', 'block_iomad_commerce'));
$PAGE->navbar->add($linktext, $linkurl);
$PAGE->navbar->add(get_string('edit_invoice', 'block_iomad_commerce'));

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.

$mform = new order_edit_form($PAGE->url, $invoiceid);
$mform->set_data($invoice);

if ($mform->is_cancelled()) {
    redirect($companylist);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;

    $transaction = $DB->start_delegated_transaction();

    $data->id = $invoiceid;
    $DB->update_record('invoice', $data);

    $count = 0;

    while (array_key_exists('process_' . $count, $_POST)) {
        $itemid = $_POST['process_' . $count];
        processor::trigger_invoiceitem_onordercomplete($itemid, $invoice);

        $count++;
    }

    $transaction->allow_commit();

    redirect($companylist);

} else {

    echo $OUTPUT->header();

    $mform->display();

    echo $OUTPUT->footer();
}

