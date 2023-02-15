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
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');

\block_iomad_commerce\helper::require_commerce_enabled();

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$invoiceid = required_param('id', PARAM_INTEGER);

$context = context_system::instance();
require_login();

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/blocks/iomad_commerce/orderlist.php', $urlparams);

$invoice = \block_iomad_commerce\helper::get_invoice($invoiceid);

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

$mform = new \block_iomad_commerce\forms\order_edit_form($PAGE->url, $invoiceid);
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
         block_iomad_commerce\processor::trigger_invoiceitem_onordercomplete($itemid, $invoice);

        $count++;
    }

    $transaction->allow_commit();

    redirect($companylist);

} else {

    echo $OUTPUT->header();

    $mform->display();

    echo $OUTPUT->footer();
}

