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
use iomad;
use company;
use company_user;
use context_system;

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/local/iomad/lib/company.php');

class processor {
    public static function trigger_oncheckout($invoiceid) {

        self::process_all_items($invoiceid, 'oncheckout');
        $_SESSION['Payment_Amount'] = \block_iomad_commerce\helper::get_basket_total();

        \block_iomad_commerce\helper::create_invoice_reference($invoiceid);
    }

    public static function trigger_onordercomplete($invoice) {
        global $DB;
        
        self::process_all_items($invoice->id, 'onordercomplete', $invoice );
        self::trigger_invoiceitem_onordercomplete($invoice->id, 'onordercomplete', $invoice );
        $invoice->status = \block_iomad_commerce\helper::INVOICESTATUS_PAID;
        $DB->update_record('invoice', $invoice);
    }

    private static function process_all_items($invoiceid, $eventname, $invoice = null) {
        global $DB, $CFG;

        if ($items = $DB->get_records('invoiceitem', array('invoiceid' => $invoiceid, 'processed' => 0), null, '*')) {
            foreach ($items as $item) {
                $processorname = $item->invoiceableitemtype;
                $function = $processorname . "_" . $eventname;
                self::$function($item, $invoice);
            }
        }
    }

    public static function trigger_invoiceitem_onordercomplete($invoiceitemid, $invoice) {
        global $DB;
        if ($item = $DB->get_record('invoiceitem', array('id' => $invoiceitemid, 'processed' => 0), '*')) {
            $processorname = $item->invoiceableitemtype;
            $function = $processorname . "_" . $eventname;
            self::$function($item, $invoice);
        }
    }

    private static function singlepurchase_oncheckout($invoiceitem) {
        global $DB;

        if($ii = $DB->get_record_sql
          ('SELECT ii.*, css.single_purchase_currency, css.single_purchase_price, css.single_purchase_validlength
                                       FROM
                                            {invoiceitem} ii
                                            INNER JOIN {course_shopsettings} css ON css.id = ii.invoiceableitemid
                                       WHERE
                                            ii.id = :invoiceitemid', array('invoiceitemid' => $invoiceitem->id)))
        {
            $ii->currency = $ii->single_purchase_currency;
            $ii->price = $ii->single_purchase_price;
            $ii->license_validlength = $ii->single_purchase_validlength;
            $DB->update_record('invoiceitem', $ii);
        }
    }

    private static function singlepurchase_onordercomplete($invoiceitem, $invoice) {
        global $DB, $CFG;

        $runtime = time();

        // Get the item's single purchase details.
        $iteminfo = $DB->get_record('course_shopsettings', array('id' => $invoiceitem->invoiceableitemid));

        // Get the courses.
        $courses = $DB->get_records('course_shopsettings_courses', ['itemid' => $iteminfo->id]);
        $licensecoursecount = $DB->count_records_sql("SELECT COUNT(csc.id) FROM {course_shopsettings_courses} csc
                                                      JOIN {iomad_courses} ic ON (csc.courseid = ic.courseid)
                                                      WHERE
                                                      ic.licensed = 1
                                                      AND csc.itemid = :itemid",
                                                      ['itemid' => $iteminfo->id]); 

        $transaction = $DB->start_delegated_transaction();

        // Get name for company license.
        $companyid = iomad::get_my_companyid(context_system::instance());
        $company = $DB->get_record('company', ['id' => $companyid]);
        $licensename = $company->shortname . " [" . $iteminfo->name . "] " . date($CFG->iomad_date_format);
        $count = $DB->count_records_sql("SELECT COUNT(*) FROM {companylicense} WHERE " . $DB->sql_like('name', ":licensename"),
                                         ['licensename' => str_replace("'", "\'", $licensename) . "%'"]);

        if ($count) {
            $licensename .= ' (' . ($count + 1) . ')';
        }

        // Create mdl_companylicense record.
        $companylicense = (object) [];
        $companylicense->name = $licensename;
        if (empty($iteminfo->program)) {
            $companylicense->allocation = $licensecoursecount;
            $companylicense->humanallocation = $licensecoursecount;
        } else {
            $companylicense->allocation = $licensecoursecount;
            $companylicense->humanallocation = 1;
        }
        $companylicense->used = 0;
        $companylicense->program = $iteminfo->program;
        $companylicense->clearonexpire = $iteminfo->clearonexpire;
        $companylicense->instant = $iteminfo->instant;
        $validlength = (int) $iteminfo->single_purchase_validlength / 86400;
        if ($validlength == 0 ) {
            // Always get 1 day.
            $validlength = 1;
        } 
        $companylicense->validlength = $validlength;
        if (!empty($iteminfo->single_purchase_shelflife)) {
            $companylicense->expirydate = $iteminfo->single_purchase_shelflife + $runtime;
        } else {
            $companylicense->expirydate = 0;
        }
        if (!empty($iteminfo->cutofftime)) {
            $companylicense->cutoffdate = $iteminfo->cutofftime + $runtime;
        } else {
            $companylicense->cutoffdate = 0;
        }
        $companylicense->companyid = $company->id;
        $companylicenseid = $DB->insert_record('companylicense', $companylicense);

        foreach ($courses as $course) {
            if ($DB->get_record('iomad_courses', ['courseid' => $course->courseid, 'licensed' => 1])) {
                $DB->insert_record('companylicense_courses', ['licenseid' => $companylicenseid, 'courseid' => $course->courseid]);

                // Create an event to assign the license.
                $eventother = array('licenseid' => $companylicenseid,
                                    'issuedate' => $runtime,
                                    'duedate' => $runtime);
                $event = \block_iomad_company_admin\event\user_license_assigned::create(array('context' => \context_course::instance($course->courseid),
                                                                                              'objectid' => $companylicenseid,
                                                                                              'courseid' => $course->courseid,
                                                                                              'userid' => $invoice->userid,
                                                                                              'other' => $eventother));
                $event->trigger();
            } else {

                // Enrol user into course.
                $user = new stdClass;
                $user->id = $invoice->userid;
                company_user::enrol($user, array($course->courseid));
            }
        }

        // Mark the invoice item as processed.
        $invoiceitem->processed = 1;
        $DB->update_record('invoiceitem', $invoiceitem);

        $transaction->allow_commit();
    }

    public static function licenseblock_oncheckout($invoiceitem) {
        global $DB;

        if ($ii = $DB->get_record('invoiceitem', array('id' => $invoiceitem->id), '*')) {
            if ($block = \block_iomad_commerce\helper::get_license_block($ii->invoiceableitemid, $ii->license_allocation)) {
                $ii->currency = $block->currency;
                $ii->price = $block->price;
                $ii->license_validlength = $block->validlength;
                $ii->license_shelflife = $block->shelflife;

                $DB->update_record('invoiceitem', $ii);
            }
        }
    }

    public static function licenseblock_onordercomplete($invoiceitem, $invoice) {
        global $DB, $CFG;

        $runtime = time();
        $transaction = $DB->start_delegated_transaction();

        // Get name for company license.
        $companyid = iomad::get_my_companyid(context_system::instance());
        $company = $DB->get_record('company', ['id' => $companyid]);
        $item = $DB->get_record('course_shopsettings', ['id' => $invoiceitem->invoiceableitemid]);
        $courses = $DB->get_records('course_shopsettings_courses', ['itemid' => $item->id]);
        $licensename = $company->shortname . " [" . $item->name . "] " . date($CFG->iomad_date_format);
        $count = $DB->count_records_sql("SELECT COUNT(*) FROM {companylicense} WHERE name LIKE '" .
                                        (str_replace("'", "\'", $licensename)) . "%'");
        if ($count) {
            $licensename .= ' (' . ($count + 1) . ')';
        }

        // Create mdl_companylicense record.
        $companylicense = (object) [];
        $companylicense->name = $licensename;
        $companylicense->program = $item->program;
        if (empty($companylicense->program)) {
            $companylicense->allocation = $invoiceitem->license_allocation;
        } else {
            $companylicense->allocation = $invoiceitem->license_allocation * count($courses);
        }
        $companylicense->humanallocation = $invoiceitem->license_allocation;
        $companylicense->clearonexpire = $item->clearonexpire;
        $companylicense->instant = $item->instant;

        // Deal with license valid length.
        $validlength = (int) $item->single_purchase_validlength / 86400;
        if ($validlength == 0 ) {
            // Always get 1 day.
            $validlength = 1;
        } 
        $companylicense->validlength = $validlength;

        // Deal with license shelf life.
        if (!empty($item->single_purchase_shelflife)) {
            $companylicense->expirydate = $item->single_purchase_shelflife + $runtime;
        } else {
            $companylicense->expirydate = 0;
        }

        // Deal with cut off time.
        if (!empty($item->cutofftime)) {
            $companylicense->cutoffdate = $item->cutofftime + $runtime;
        } else {
            $companylicense->cutoffdate = $companylicense->expirydate;
        }

        $companylicense->startdate = $runtime;
        $companylicense->companyid = $company->id;
        $companylicenseid = $DB->insert_record('companylicense', $companylicense);

        foreach ($courses as $course) {
            $DB->insert_record('companylicense_courses', ['licenseid' => $companylicenseid, 'courseid' => $course->courseid]);
        }

        // Mark the invoice item as processed.
        $invoiceitem->processed = 1;
        $DB->update_record('invoiceitem', $invoiceitem);
        $transaction->allow_commit();
    }
}
