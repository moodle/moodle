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

require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');

\block_iomad_commerce\helper::require_commerce_enabled();

$itemid    = required_param('itemid', PARAM_INT);
$nlicenses   = optional_param('nlicenses', 0, PARAM_INT);
$licenses    = optional_param('licenses', 0, PARAM_INT);

if ($licenses && !$nlicenses) {
    redirect(new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/item.php', ['licenseformempty' => 1, 'id' => $itemid]));
}

global $DB;

$PAGE->set_url(new moodle_url($CFG->wwwroot . "/blocks/iomad_commerce/buynow.php", ['itemid' => $itemid, 'nlicenses' => $nlicenses]));
$PAGE->set_context(context_system::instance());
$context = $PAGE->context;


// Get or create basket.
if (!empty($SESSION->basketid)) {
    if (!$basket = $DB->get_record('invoice', array('id' => $SESSION->basketid, 'status' => \block_iomad_commerce\helper::INVOICESTATUS_BASKET), '*')) {
        $basket = new stdClass;
        $basket->userid = $USER->id;
        $basket->status = \block_iomad_commerce\helper::INVOICESTATUS_BASKET;
        $basket->date = time();
        $basket->id = $DB->insert_record('invoice', $basket, true);
        $SESSION->basketid = $basket->id;
    }
} else {
    $basket = new stdClass;
    $basket->userid = $USER->id;
    $basket->status = \block_iomad_commerce\helper::INVOICESTATUS_BASKET;
    $basket->date = time();
    $basket->id = $DB->insert_record('invoice', $basket, true);
    $SESSION->basketid = $basket->id;
}


$invoiceitem = new stdClass;
$invoiceitem->invoiceid = $basket->id;
$invoiceitem->invoiceableitemid = $itemid;

if ($nlicenses) {

    if ($block = \block_iomad_commerce\helper::get_license_block($itemid, $nlicenses)) {
        $invoiceitem->currency = $block->currency;
        $invoiceitem->price = $block->price;
        $invoiceitem->invoiceableitemtype = 'licenseblock';

        $invoiceitem->license_allocation = $nlicenses;
        $invoiceitem->license_validlength = $block->validlength;
        $invoiceitem->license_shelflife = $block->shelflife;
    }

} else {
    // Single purchase.

    if ($course = $DB->get_record('course_shopsettings', array('id' => $itemid), '*', MUST_EXIST)) {
        $invoiceitem->currency = $course->single_purchase_currency;
        $invoiceitem->price = $course->single_purchase_price;
        $invoiceitem->invoiceableitemtype = 'singlepurchase';

        $invoiceitem->license_allocation = 1;
        $invoiceitem->license_validlength = $course->single_purchase_validlength;
        $invoiceitem->license_shelflife = 0;
    }
}

$DB->insert_record('invoiceitem', $invoiceitem);

redirect(new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/basket.php'));
