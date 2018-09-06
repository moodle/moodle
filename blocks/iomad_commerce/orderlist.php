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

require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once('lib.php');

require_commerce_enabled();

$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page.

$context = context_system::instance();
require_login();

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('orders', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/orderlist.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
$returnurl = $baseurl;

echo $OUTPUT->header();

//  Check we can actually do anything on this page.
iomad::require_capability('block/iomad_commerce:admin_view', $context);

// Get the number of orders.
$objectcount = $DB->count_records_sql("SELECT COUNT(*) FROM {invoice} WHERE Status != '" . INVOICESTATUS_BASKET . "'");
echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);

flush();

if ($orders = $DB->get_recordset_sql("SELECT
                                        i.*,
                                        (SELECT COUNT(*) FROM {invoiceitem} ii WHERE ii.invoiceid = i.id AND processed = 0)
                                         AS unprocesseditems
                                      FROM {invoice} i
                                      WHERE i.Status != '" . INVOICESTATUS_BASKET . "'
                                      ORDER BY i.Status DESC, i.id DESC", null, $page, $perpage)) {
    if (count($orders)) {
        $stredit   = get_string('edit');
        $strhide = get_string('hide', 'block_iomad_commerce');
        $strshow = get_string('show', 'block_iomad_commerce');

        $table = new html_table();
        $table->head = array (get_string('reference', 'block_iomad_commerce'),
                              get_string('paymentprovider', 'block_iomad_commerce'),
                              get_string('status', 'block_iomad_commerce'),
                              get_string('company', 'block_iomad_company_admin'),
                              get_string('unprocesseditems', 'block_iomad_commerce'),
                              '');
        $table->align = array ("left", "center", "center", "center");
        $table->width = "95%";

        foreach ($orders as $order) {
            if (iomad::has_capability('block/iomad_commerce:admin_view', $context)) {
                    $editbutton = "<a href='" . new moodle_url('edit_order_form.php', array("id" => $order->id)) . "'>$stredit</a>";
            } else {
                    $editbutton = "";
            }
                $table->data[] = array ($order->reference,
                                    get_string('pp_' . $order->checkout_method . '_name', 'block_iomad_commerce'),
                                    get_string('status_' . $order->status, 'block_iomad_commerce'),
                                    $order->company,
                                    ($order->unprocesseditems > 0 ? $order->unprocesseditems : ""),
                                    $editbutton);
        }

        if (!empty($table)) {
            echo html_writer::table($table);
            echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);
        }
    } else {
        echo "<p>" . get_string('noinvoices', 'block_iomad_commerce') . "</p>";
    }
    $orders->close();
}

echo $OUTPUT->footer();
