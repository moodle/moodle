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

const INVOICESTATUS_BASKET = 'b';
const INVOICESTATUS_UNPAID = 'u';
const INVOICESTATUS_PAID = 'p';

function require_commerce_enabled() {
    return;
}

function get_lowest_price_text($course_shopsetting_with_lowest_block_price) {
    global $CFG;

    if (!empty($CFG->commerce_admin_currency)) {
        $currency = $CFG->commerce_admin_currency;
    } else {
        $currency = 'GBP';
    }
    $prices = array();
    if ($course_shopsetting_with_lowest_block_price->allow_single_purchase) {
        if ($course_shopsetting_with_lowest_block_price->single_purchase_price) {
            $prices[] = $course_shopsetting_with_lowest_block_price->single_purchase_price;
        }
    }
    if ($course_shopsetting_with_lowest_block_price->allow_license_blocks) {
        if ($course_shopsetting_with_lowest_block_price->price) {
            $prices[] = $course_shopsetting_with_lowest_block_price->price;
        }
    }

    $lowestprice = number_format(min($prices), 2);

    if ($lowestprice) {
        $price = get_string('pricefrom', 'block_iomad_commerce', "<b>" . $currency . ' ' . $lowestprice. "</b>");
    } else {
        $price = '';
    }
    return $price;
}

function get_license_block($courseid, $nlicenses) {
    global $DB;
    return $DB->get_record_sql('SELECT *
                                FROM {course_shopblockprice}
                                WHERE courseid = :courseid
                                AND price_bracket_start <= :nlicenses
                                ORDER BY price_bracket_start DESC
                                LIMIT 0, 1
                               ', array('nlicenses' => $nlicenses, 'courseid' => $courseid));

}

function get_basket_id() {
    if ($basket = get_basket('id')) {
        return $basket->id;
    }
    return 0;
}

function get_basket_total() {
    global $DB, $SESSION;
    if ($basket = $DB->get_record_sql('SELECT
                                        i.id,
                                        sum(quantity*license_allocation*price) AS total
                                       FROM
                                        {invoice} i
                                        INNER JOIN {invoiceitem} ii ON ii.invoiceid = i.id
                                       WHERE
                                        i.status = :status
                                        AND
                                        i.id = :basketid
                                       GROUP BY
                                        i.id
                                    ', array('basketid' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET))) {
        return $basket->total;
    }
    return 0;
}

function get_basket($fields = '*') {
    global $SESSION, $DB;
    return $DB->get_record('invoice', array('id' => $SESSION->basketid), $fields);
}

function enrich_invoice($invoice) {
    global $USER, $DB;
    foreach (array('id', 'firstname', 'lastname', 'department', 'address', 'city', 'state', 'country') as $key) {
        if ($key != 'id') {
            $invoice->$key = $USER->$key;
        } else {
            $invoice->userid = $USER->id;
        }
    }
    $DB->update_record('invoice', $invoice);
}

function get_invoice($invoiceid, $fields = '*') {
    global $DB;
    return $DB->get_record('invoice', array('id' => $invoiceid), $fields);
}

function get_invoice_by_reference($invoicereference, $fields = '*') {
    global $DB;
    return $DB->get_record('invoice', array('reference' => $invoicereference), $fields);
}

function get_basket_info() {
    global $SESSION, $DB;

    if (!empty($SESSION->basketid)) {
        $nitems = $DB->count_records_sql('SELECT COUNT(*)
                                      FROM {invoiceitem} ii
                                          INNER JOIN {course} c ON ii.invoiceableitemid = c.id
                                      WHERE EXISTS (SELECT id
                                                   FROM {invoice} i
                                                   WHERE i.id = :basketid
                                                     AND i.status = :status
                                                     AND i.id = ii.invoiceid
                                                )
                                       ', array('basketid' => $SESSION->basketid, 'status' => INVOICESTATUS_BASKET));
    } else {
        return '<p>' . get_string('emptybasket', 'block_iomad_commerce') . '</p>';
    }

    if ($nitems) {
        $strkey = ($nitems == 1) ? 'basket_1item' : 'basket_nitems';
        $url = new moodle_url('/blocks/iomad_commerce/basket.php');
        return '<p><a href="' . $url . '">' . get_string($strkey, 'block_iomad_commerce', $nitems) . '</a></p>';
    } else {
        return '<p>' . get_string('emptybasket', 'block_iomad_commerce') . '</p>';
    }
}

function show_basket_info() {
    echo get_basket_info();
}

function get_payment_providers() {
    $result = array();
    $path = dirname(__FILE__) . '/checkout/';
    foreach (new DirectoryIterator($path) as $file) {
        if ($file->isDot()) {
            continue;
        }

        $filename = $file->getFilename();
        if (is_dir($path . '/' . $filename)) {
            $phpname = $path . '/' . $filename . '/' . $filename . '.php';
            if (file_exists($phpname)) {
                $result[] = $filename;
            }
        }
    }
    return $result;
}

function payment_provider_enabled($providername) {
    global $CFG;

    $penabled = $providername . "_enabled";
    if (!empty($CFG->$penabled)) {
        return ($CFG->$penabled);
    } else {
        return false;
    }
}

function get_enabled_payment_providers() {
    $result = array();
    foreach (get_payment_providers() as $p) {
        if (payment_provider_enabled($p)) {
            $result[] = $p;
        }
    }
    return $result;
}

function get_enabled_payment_providers_instances() {
    $ppnames = get_enabled_payment_providers();
    $result = array();
    foreach ($ppnames as $ppname) {
        $result[] = get_payment_provider_instance($ppname);
    }
    return $result;
}

function get_payment_provider_instance($providername) {
    $path = dirname(__FILE__) . '/checkout/' . $providername . '/' . $providername . '.php';
    require_once($path);
    return new $providername;
}

function get_payment_provider_displayname($providername) {
    return get_string('pp_' . $providername . '_name', 'block_iomad_commerce');
}

function get_basket_html($includeremove = 0) {
    if ($basketid = get_basket_id()) {
        return get_invoice_html($basketid, $includeremove);
    }
}

function get_invoice_html($invoiceid, $includeremove = 0, $links = 1, $showprocessed = 0) {
    global $DB, $USER, $CFG;

    $result = '';

    if ($basketitems = $DB->get_records_sql('SELECT ii.*, c.fullname
                                            FROM {invoiceitem} ii
                                                INNER JOIN {course} c ON ii.invoiceableitemid = c.id
                                            WHERE ii.invoiceid = :invoiceid
                                            ORDER BY ii.id
                                           ', array('invoiceid' => $invoiceid))) {

        $table = new html_table();
        $table->head = array (get_string('course'),
                              "",
                              get_string('unitprice', 'block_iomad_commerce'),
                              get_string('amount', 'block_iomad_commerce')
                             );
        if ($includeremove) {
            $table->head[] = "";
        }
        if ($showprocessed) {
            $table->head[] = get_string('process', 'block_iomad_commerce');
        }
        $table->align = array ("left", "center", "right", "right", "right");
        $table->width = "600px";

        $total = 0;
        $count = 0;
        if (!empty($CFG->commerce_admin_currency)) {
            $currency = get_string($CFG->commerce_admin_currency, 'core_currencies');
        } else {
            $currency = get_string('GBP', 'core_currencies');
        }
        foreach ($basketitems as $item) {
            $rowtotal = $item->price * $item->license_allocation;

            if ($item->invoiceableitemtype == 'singlepurchase') {
                $unitprice = '';
            } else {
                $unitprice = $item->currency . number_format($item->price, 2);
            }

            $row = array(
                ($links ? "<a href='course.php?id=$item->invoiceableitemid'>$item->fullname</a>" : $item->fullname),
                get_string('type_quantity_' . ($item->license_allocation > 1 ? 'n' : '1') .
                '_' . $item->invoiceableitemtype, 'block_iomad_commerce', $item->license_allocation),
                $unitprice,
                $item->currency . ' ' .number_format($rowtotal, 2)
            );
            if ($includeremove) {
                $row[] = "<a href='basket.php?remove=$item->id'>" . strtolower(get_string('remove')) . "</a>";
            }
            if ($showprocessed) {
                if ($item->processed) {
                    $row[] = get_string('processed', 'block_iomad_commerce');
                } else {
                    $row[] = "<input type='checkbox' name='process_" . ($count++) . "' value='" . $item->id . "' />";
                }
            }

            $table->data[] = $row;

            $currency = $item->currency;
            $total += $rowtotal;
        }

        $totalrow = array(
            '<b>' . get_string('total', 'block_iomad_commerce') . '</b>',
            '',
            '',
            '<b>' . $currency . ' ' . number_format($total, 2) . '</b>'
        );
        if ($includeremove) {
            $totalrow[] = '';
        }
        if ($showprocessed) {
            $totalrow[] = '';
        }
        $table->data[] = $totalrow;

        if (!empty($table)) {
            $result .= html_writer::table($table);
        }
    }

    return $result;
}

function get_error_table($msg, $data) {
    $html = "<p class='error'>$msg</p>";

    if ($data) {
        $table = new html_table();
        $table->head = array (get_string('error'),
                              "",
                             );
        $table->align = array ("left", "left");

        $table->data = $data;

        $html .= html_writer::table($table);
    }
    return $html;
}

function get_shop_tags() {
    global $DB;

    if ($shoptags = $DB->get_records_sql('SELECT
                                            st.tag
                                          FROM
                                            {shoptag} st
                                          WHERE
                                            EXISTS (SELECT id FROM {course_shoptag} WHERE shoptagid = st.id)
                                          ORDER BY
                                            st.tag')) {
        $tags = array();
        foreach ($shoptags as $st) {
            $tags[] = $st->tag;
        }
        return $tags;
    }
    return array();
}

function get_course_tags($courseid) {
    global $DB;

    if ($shoptags = $DB->get_records_sql('SELECT
                                            st.tag
                                          FROM
                                            {course_shoptag} cst INNER JOIN
                                            {shoptag} st ON cst.shoptagid = st.id
                                          WHERE
                                            cst.courseid = :courseid
                                          ORDER BY
                                            st.tag', array('courseid' => $courseid))) {
        $tags = array();
        foreach ($shoptags as $st) {
            $tags[] = $st->tag;
        }
        return implode(', ', $tags);
    }
    return '';
}

function random_invoice_reference() {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $refstr = '';
    for ($i = 0; $i < 6; $i++) {
        $refstr .= $chars[rand(0, strlen($chars) -1 )];
    }
    return $refstr;
}

function set_new_invoice_reference($invoiceid) {
    global $DB;
    try {
        return $DB->set_field('invoice', 'reference', random_invoice_reference(), array('id' => $invoiceid));
    } catch (Exception $e) {
        // Assume the issue we have is a unique index issue.
        return false;
    }
}

function create_invoice_reference($invoiceid) {
    $invariant = 1000;
    if (!get_invoice($invoiceid, 'reference')->reference) {
        while ($invariant-- && !set_new_invoice_reference($invoiceid));
    }
}

function is_commerce_configured() {
    global $CFG;

    // Confirm commerce admin has been defined
    if (!$CFG->commerce_admin_firstname || !$CFG->commerce_admin_lastname || !$CFG->commerce_admin_email) {
        return false;
    }

    // Confirm that there are some payment providers configured
    $pp = get_payment_providers();
    if (!$pp) {
        return false;
    }

    // Looks ok
    return true;
}
