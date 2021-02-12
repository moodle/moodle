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

require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/../lib.php');

class processor {
    static function trigger_oncheckout($invoiceid) {
        self::process_all_items($invoiceid, 'oncheckout');
        $_SESSION['Payment_Amount'] = get_basket_total();
        create_invoice_reference($invoiceid);
    }
    static function trigger_onordercomplete($invoice) {
        self::process_all_items($invoice->id, 'onordercomplete', $invoice );
    }
    private static function process_all_items($invoiceid, $eventname, $invoice = null) {
        global $DB;
        if ($items = $DB->get_records('invoiceitem', array('invoiceid' => $invoiceid, 'processed' => 0), null, '*')) {
            $curdir = dirname(__FILE__) . '/';
            foreach ($items as $item) {
                $processorname = $item->invoiceableitemtype;
                $path = $curdir . $processorname . '.php';
                if (file_exists($path)) {
                    require_once($path);
                    $p = new $processorname;
                    $p->$eventname($item, $invoice);
                }
            }
        }
    }
    static function trigger_invoiceitem_onordercomplete($invoiceitemid, $invoice) {
        global $DB;
        if ($item = $DB->get_record('invoiceitem', array('id' => $invoiceitemid, 'processed' => 0), '*')) {
            $curdir = dirname(__FILE__) . '/';
            $processorname = $item->invoiceableitemtype;
            $path = $curdir . $processorname . '.php';
            if (file_exists($path)) {
                require_once($path);
                $p = new $processorname;
                $p->onordercomplete($item, $invoice);
            }
        }
    }

    // Methods to be overridden in subclasses.
    function oncheckout($invoiceitem) {
    }
    function pre_order_review_processing() {
    }
    function onordercomplete($invoiceitem, $invoice) {
    }
}
