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

require_once('processor.php');
require_once(dirname(__FILE__) . '/../../../local/iomad/lib/user.php');

class singlepurchase extends processor {
    // Update the invoice item with the latest single purchase price and license valid_length.
    function oncheckout($invoiceitem) {
        global $DB;
        if($ii = $DB->get_record_sql
          ('SELECT ii.*, css.single_purchase_currency, css.single_purchase_price, css.single_purchase_validlength
                                       FROM
                                            {invoiceitem} ii
                                            INNER JOIN {course_shopsettings} css ON css.courseid = ii.invoiceableitemid
                                       WHERE
                                            ii.id = :invoiceitemid', array('invoiceitemid' => $invoiceitem->id)))
        {
            $ii->currency = $ii->single_purchase_currency;
            $ii->price = $ii->single_purchase_price;
            $ii->license_validlength = $ii->single_purchase_validlength;
            $DB->update_record('invoiceitem', $ii);
        }
    }
    function onordercomplete($invoiceitem, $invoice) {
        global $DB, $CFG;
        $transaction = $DB->start_delegated_transaction();
        if ($DB->get_record('iomad_courses', array('courseid' => $invoiceitem->invoiceableitemid, 'licensed' => 1))) {
            // Get the item's single purchase details.
            $courseinfo = $DB->get_record('course_shopsettings', array('courseid' => $invoiceitem->invoiceableitemid));
                    // Get name for company license.
            $company = company::get_company_byuserid($invoice->userid);
            $course = $DB->get_record('course', array('id' => $invoiceitem->invoiceableitemid), 'id, shortname', MUST_EXIST);
            $licensename = $company->shortname . " [" . $course->shortname . "] " . date($CFG->iomad_date_format);
            $count = $DB->count_records_sql("SELECT COUNT(*) FROM {companylicense} WHERE name LIKE '".
            (str_replace("'", "\'", $licensename)) . "%'");
            if ($count) {
                $licensename .= ' (' . ($count + 1) . ')';
            }
            // Create mdl_companylicense record.
            $companylicense = new stdClass;
            $companylicense->name = $licensename;
            $companylicense->allocation = 1;
            $companylicense->used = 1;
            $companylicense->validlength = $courseinfo->single_purchase_validlength;
            if (!empty($courseinfo->single_purchase_shelflife)) {
                $companylicense->expirydate = ($courseinfo->single_purchase_shelflife * 86400) + time();
                // 86400 = 24*60*60 = number of seconds in a day.
            } else {
                $companylicense->expirydate = 0;
            }
            $companylicense->companyid = $company->id;
            $companylicenseid = $DB->insert_record('companylicense', $companylicense);
            // Create mdl_companylicense_courses record for the course.
            $clc = new stdClass;
            $clc->licenseid = $companylicenseid;
            $clc->courseid = $course->id;
            $DB->insert_record('companylicense_courses', $clc);
            //  Assign the license to the user.
            $DB->insert_record('companylicense_users', array('userid' => $invoice->userid,
                                                             'licenseid' => $companylicenseid,
                                                             'licensecourseid' => $course->id,
                                                             'issuedate' => time()));
        } else {
            // Enrol user into course.
            $user = new stdClass;
            $user->id = $invoice->userid;
            company_user::enrol($user, array($invoiceitem->invoiceableitemid));
        }
        // Mark the invoice item as processed.
        $invoiceitem->processed = 1;
        $DB->update_record('invoiceitem', $invoiceitem);

        $transaction->allow_commit();
    }
}
