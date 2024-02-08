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

namespace block_iomad_commerce;

use moodle_url;
use html_writer;
use html_table;
use DirectoryIterator;
use company;
use iomad;
use company_user;
use context_system;

require_once(dirname(__FILE__) . '/../../../config.php');

class helper {
    const INVOICESTATUS_BASKET = 'b';
    const INVOICESTATUS_UNPAID = 'u';
    const INVOICESTATUS_PAID = 'p';

    public static function require_commerce_enabled() {
        return;
    }

    public static function get_lowest_price_text($course_shopsetting_with_lowest_block_price) {
        global $CFG, $DB;

        if (empty($course_shopsetting_with_lowest_block_price->single_purchase_currency)) {    
            if (!empty($CFG->commerce_admin_currency)) {
                $currency = $CFG->commerce_admin_currency;
            } else {
                $currency = 'GBP';
            }
        } else {
            $currency = $course_shopsetting_with_lowest_block_price->single_purchase_currency;
        }
        $prices = array();
        if ($course_shopsetting_with_lowest_block_price->allow_single_purchase) {
            if ($course_shopsetting_with_lowest_block_price->single_purchase_price) {
                $prices[] = $course_shopsetting_with_lowest_block_price->single_purchase_price;
            }
        }
        if ($course_shopsetting_with_lowest_block_price->allow_license_blocks) {
            if ($blockprices = $DB->get_records_sql("SELECT * FROM {course_shopblockprice}
                                                    WHERE itemid = :itemid
                                                    AND price_bracket_start < 2",
                                                    ['itemid' => $course_shopsetting_with_lowest_block_price->id])) {
                foreach ($blockprices as $blockprice) {
                    $prices[] = $blockprice->price;
                }
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

    public static function get_license_block($itemid, $nlicenses) {
        global $DB;

        $record =  $DB->get_records_sql("SELECT *
                                         FROM {course_shopblockprice}
                                         WHERE itemid = :itemid
                                         AND price_bracket_start <= :nlicenses
                                         ORDER BY price_bracket_start DESC",
                                         ['nlicenses' => $nlicenses, 'itemid' => $itemid],
                                         0, 1);
        return array_shift($record);

    }

    public static function get_basket_id() {
        if ($basket = self::get_basket('id')) {
            return $basket->id;
        }
        return 0;
    }

    public static function get_basket_total($basketid = 0) {
        global $DB, $SESSION;

        if (empty($basketid)) {
            $basketid = $SESSION->basketid;
        }

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
                                        ', array('basketid' => $basketid, 'status' => self::INVOICESTATUS_BASKET))) {
            return $basket->total;
        }
        return 0;
    }

    public static function get_basket_by_id($basketid = 0) {
        global $DB, $SESSION;

        if (empty($basketid)) {
            $basketid = $SESSION->basketid;
        }

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
                                        ', array('basketid' => $basketid, 'status' => self::INVOICESTATUS_BASKET))) {

            $currency = $DB->get_record_sql("SELECT DISTINCT ii.currency
                                             FROM {invoice} i
                                             INNER JOIN {invoiceitem} ii ON ii.invoiceid = i.id
                                             WHERE
                                             i.status = :status
                                             AND
                                             i.id = :basketid
                                           ", ['basketid' => $basketid, 'status' => self::INVOICESTATUS_BASKET]);
            $basket->currency = $currency->currency;
            return $basket;
        }

        return false;
    }

    public static function get_basket($fields = '*') {
        global $SESSION, $DB;

        if (!empty($SESSION->basketid)) {
            return $DB->get_record('invoice', array('id' => $SESSION->basketid), $fields);
        }

        return false;
    }

    public static function enrich_invoice($invoice) {
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

    public static function get_invoice($invoiceid, $fields = '*') {
        global $DB;
        return $DB->get_record('invoice', array('id' => $invoiceid), $fields);
    }

    public static function get_invoice_by_reference($invoicereference, $fields = '*') {
        global $DB;
        return $DB->get_record('invoice', array('reference' => $invoicereference), $fields);
    }

    public static function get_basket_info() {
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
                                           ', array('basketid' => $SESSION->basketid, 'status' => self::INVOICESTATUS_BASKET));
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

    public static function show_basket_info() {
        echo self::get_basket_info();
    }

    public static function get_basket_menu_link() {
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
                                           ', array('basketid' => $SESSION->basketid, 'status' => self::INVOICESTATUS_BASKET));
        } else {
            return '-' . get_string('emptybasket', 'block_iomad_commerce') . "|#\n\r";
        }

        if ($nitems) {
            $strkey = ($nitems == 1) ? 'basket_1item' : 'basket_nitems';
            $url = new moodle_url('/blocks/iomad_commerce/basket.php');
            return '-' . get_string($strkey, 'block_iomad_commerce', $nitems) . '|' .$url->out() . "\n\r";
        } else {
            return '-' . get_string('emptybasket', 'block_iomad_commerce') . "|#\n\r";
        }
    }

    public static function get_shop_menu_link($companyrec) {
        global $DB, $CFG, $USER;

        $shoplink = "";
        $companycontext = \core\context\company::instance($companyrec->id);
        if (iomad::has_capability('block/iomad_commerce:buyitnow', $companycontext) || iomad::has_capability('block/iomad_commerce:buyinbulk', $companycontext)) {
            if (!empty($CFG->commerce_enable_external)) {
                // Get and store a one time token.
                $token = company_user::generate_token();
                $configname = "commerce_externalshop_url_" . $companyrec->id;
                if (empty($CFG->$configname)) {
                    $configname = "commerce_externalshop_url";
                }
                $link = new moodle_url($CFG->$configname . '/wp-content/plugins/wooiomad/land.php', array('username' => $USER->username, 'token' => $token));
                $shoplink = "" . get_string('gotoshop', 'block_iomad_commerce') . '|' . $link->out() . "\n\r";
            } else {
                if ($DB->get_records('course_shopsettings', ['companyid' => $companyrec->id, 'enabled' => 1])) {
                    $shoplink = "" . get_string('buycourses', 'block_iomad_commerce') . "|#\n\r";
                    $shoplink .= "-" . get_string('gotoshop', 'block_iomad_commerce') . "|/blocks/iomad_commerce/shop.php\n\r";
                    $shoplink .= self::get_basket_menu_link();
                }
            }
        }

        return $shoplink;
    }

    public static function get_payment_providers() {
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

    public static function payment_provider_enabled($providername) {
        global $CFG;

        $penabled = $providername . "_enabled";
        if (!empty($CFG->$penabled)) {
            return ($CFG->$penabled);
        } else {
            return false;
        }
    }

    public static function get_enabled_payment_providers() {
        $result = array();
        foreach (self::get_payment_providers() as $p) {
            if (self::payment_provider_enabled($p)) {
                $result[] = $p;
            }
        }
        return $result;
    }

    public static function get_enabled_payment_providers_instances() {
        $ppnames = self::get_enabled_payment_providers();
        $result = array();
        foreach ($ppnames as $ppname) {
            $result[] = self::get_payment_provider_instance($ppname);
        }
        return $result;
    }

    public static function get_payment_provider_instance($providername) {
        $path = dirname(__FILE__) . '/checkout/' . $providername . '/' . $providername . '.php';
        require_once($path);
        return new $providername;
    }

    public static function check_multiple_currencies($invoiceid) {
        global $DB;

        $currencycount = $DB->count_records_sql('SELECT count(DISTINCT currency)
                                                 FROM {invoiceitem}
                                                 WHERE invoiceid = :invoiceid', 
                                                ['invoiceid' => $invoiceid]);

        if ($currencycount > 1) {
            return true;
        }

        return false;
    }
    public static function get_payment_provider_displayname($providername) {
        return get_string('pp_' . $providername . '_name', 'block_iomad_commerce');
    }

    public static function get_basket_html($includeremove = 0) {
        if ($basketid = self::get_basket_id()) {
            return self::get_invoice_html($basketid, $includeremove);
        }
    }

    public static function get_invoice_html($invoiceid, $includeremove = 0, $links = 1, $showprocessed = 0) {
        global $DB, $USER, $CFG;

        $result = '';
        $multiplecurrency = false;
        $currentcurrency = '';

        if ($basketitems = $DB->get_records_sql('SELECT ii.*, css.name
                                                FROM {invoiceitem} ii
                                                    INNER JOIN {course_shopsettings} css ON ii.invoiceableitemid = css.id
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

                if (!empty($currentcurrency) && $item->currency != $currentcurrency) {
                    $multiplecurrency = true;
                } else {
                    $currentcurrency = $item->currency;
                }

                $row = array(
                    ($links ? "<a href='" . new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/item.php', ['itemid' => $item->invoiceableitemid]) ."'>" .$item->name ."</a>" : $item->name),
                    get_string('type_quantity_' . ($item->license_allocation > 1 ? 'n' : '1') .
                    '_' . $item->invoiceableitemtype, 'block_iomad_commerce', $item->license_allocation),
                    $unitprice,
                    $item->currency . ' ' .number_format($rowtotal, 2)
                );
                if ($includeremove) {
                    $row[] = "<a href='basket.php?remove=$item->id'><i class='icon fa fa-trash fa-fw ' title='" . get_string('remove') ."' role='img' aria-label='". get_string('remove') ."'></i></a>";
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

            if (!$multiplecurrency) {
                $totalrow = array(
                    '<b>' . get_string('total', 'block_iomad_commerce') . '</b>',
                    '',
                    '',
                    '<b>' . $currency . ' ' . number_format($total, 2) . '</b>'
                );
            } else {
                $totalrow = ['','','',''];
            }
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
        if ($multiplecurrency) {
            \core\notification::error(get_string('multiplecurrencies', 'block_iomad_commerce'));
        }

        return $result;
    }

    public static function get_invoice_summary($invoiceid, $includeremove = 0, $links = 1, $showprocessed = 0) {
        global $DB, $USER, $CFG;

        $result = '';
        $multiplecurrency = false;
        $currentcurrency = '';

        if ($basketitems = $DB->get_records_sql('SELECT ii.*, css.name
                                                FROM {invoiceitem} ii
                                                    INNER JOIN {course_shopsettings} css ON ii.invoiceableitemid = css.id
                                                WHERE ii.invoiceid = :invoiceid
                                                ORDER BY ii.id
                                               ', array('invoiceid' => $invoiceid))) {

            foreach ($basketitems as $item) {
                $rowtotal = $item->price * $item->license_allocation;

                if ($item->invoiceableitemtype == 'singlepurchase') {
                    $unitprice = '';
                } else {
                    $unitprice = $item->currency . number_format($item->price, 2);
                }

                if (!empty($currentcurrency) && $item->currency != $currentcurrency) {
                    $multiplecurrency = true;
                } else {
                    $currentcurrency = $item->currency;
                }

                $row = $item->name . ": " .
                    get_string('type_quantity_' . ($item->license_allocation > 1 ? 'n' : '1') .
                    '_' . $item->invoiceableitemtype, 'block_iomad_commerce', $item->license_allocation) . " @ " .
                    $unitprice . ' = ' .
                    $item->currency .number_format($rowtotal, 2);

                $result .= $row;
            }
        }

        return $result;
    }

    public static function get_error_table($msg, $data) {
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

    public static function get_shop_tags() {
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

    public static function get_course_tags($itemid) {
        global $DB;

        if ($shoptags = $DB->get_records_sql('SELECT
                                                st.tag
                                              FROM
                                                {course_shoptag} cst INNER JOIN
                                                {shoptag} st ON cst.shoptagid = st.id
                                              WHERE
                                                cst.itemid = :itemid
                                              ORDER BY
                                                st.tag', array('itemid' => $itemid))) {
            $tags = array();
            foreach ($shoptags as $st) {
                $tags[] = $st->tag;
            }
            return implode(', ', $tags);
        }
        return '';
    }

    public static function random_invoice_reference() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $refstr = '';
        for ($i = 0; $i < 6; $i++) {
            $refstr .= $chars[rand(0, strlen($chars) -1 )];
        }
        return $refstr;
    }

    public static function set_new_invoice_reference($invoiceid) {
        global $DB;
        try {
            return $DB->set_field('invoice', 'reference', self::random_invoice_reference(), array('id' => $invoiceid));
        } catch (Exception $e) {
            // Assume the issue we have is a unique index issue.
            return false;
        }
    }

    public static function create_invoice_reference($invoiceid) {

        $invariant = 1000;
        if (!self::get_invoice($invoiceid, 'reference')->reference) {
            while ($invariant-- && !self::set_new_invoice_reference($invoiceid));
        }
    }

    public static function is_commerce_configured() {
        global $CFG;

        // Confirm commerce admin has been defined
        if (!$CFG->commerce_enable_external &&
             (!$CFG->commerce_admin_firstname ||
              !$CFG->commerce_admin_lastname ||
              !$CFG->commerce_admin_email ||
              !$CFG->commerce_admin_paymentaccount)) {
            return false;
        }
        // If we are using the external shop then also need the URL.
        if ($CFG->commerce_enable_external &&
            !$CFG->commerce_externalshop_url) {
            return false;
        }

        return true;
    }

    public static function import_item_to_company($itemid, $companyid) {
        global $DB;

        if (!empty($companyid)) {
            $checkcourses = true;
            $company = new company($companyid);
            $companycourses = $company->get_menu_courses(true, false);
        } else {
            $checkcourses = false;
        }

        if ($shopitem = $DB->get_record('course_shopsettings', ['id' => $itemid])) {
            unset($shopitem->id);
            $shopitem->companyid = $companyid;
            if ($newitemid = $DB->insert_record('course_shopsettings', $shopitem)) {
                if ($courses = $DB->get_records('course_shopsettings_courses', ['itemid' => $itemid])) {
                    foreach ($courses as $course) {

                        // Only bring in courses which the company can see.
                        if (!$checkcourses || !empty($companycourses[$course->courseid])) {
                            unset($course->id);
                            $course->itemid = $newitemid;
                            $DB->insert_record('course_shopsettings_courses', $course);
                        }
                    }
                }
                if ($blockprices = $DB->get_records('course_shopblockprice', ['itemid' => $itemid])) {
                    foreach ($blockprices as $blockid => $blockprice) {
                        unset($blockprice->id);
                        $blockprice->itemid = $newitemid;
                        $DB->insert_record('course_shopblockprice', $blockprice);
                    }
                }
                if ($shoptags = $DB->get_records('course_shoptag', ['itemid' => $itemid])) {
                    foreach ($shoptags as $shoptagid => $shoptag) {
                        unset($shoptag->id);
                        $shoptag->itemid = $newitemid;
                        $DB->insert_record('course_shoptag', $shoptag);
                    }
                }
                return true;
            } else {
                return false;
            }
        }

    }
}
