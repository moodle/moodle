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

defined('MOODLE_INTERNAL') || die();

function xmldb_block_iomad_commerce_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2012012800) {

        // Changing type of field invoiceableitemtype on table invoiceitem to char.
        $table = new xmldb_table('invoiceitem');
        $field = new xmldb_field('invoiceableitemtype',
                                  XMLDB_TYPE_CHAR,
                                  '20',
                                  null,
                                  XMLDB_NOTNULL,
                                  null,
                                  null,
                                  'invoiceableitemid');

        // Launch change of type for field invoiceableitemtype.
        $dbman->change_field_type($table, $field);

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2012012800, 'iomad_commerce');
    }

    if ($oldversion < 2012012801) {

        // Define field date to be added to invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('date', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'pp_reason');

        // Conditionally launch add field date.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2012012801, 'iomad_commerce');
    }

    if ($oldversion < 2012012802) {

        // Define field single_purchase_shelflife to be added to course_shopsettings.
        $table = new xmldb_table('course_shopsettings');
        $field = new xmldb_field('single_purchase_shelflife',
                                 XMLDB_TYPE_INTEGER,
                                 '20',
                                 XMLDB_UNSIGNED,
                                 XMLDB_NOTNULL,
                                 null,
                                 '0',
                                 'single_purchase_validlength');

        // Conditionally launch add field single_purchase_shelflife.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2012012802, 'iomad_commerce');
    }

    if ($oldversion < 2017011000) {

        // Define field state to be added to invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('state', XMLDB_TYPE_CHAR, '120', null, null, null, null, 'city');

        // Conditionally launch add field state.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2017011000, 'iomad_commerce');
    }

    if ($oldversion < 2017030700) {

        // Changing type of field company on table invoice to char.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('company', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'pp_payerstatus');

        // Launch change of type for field company.
        $dbman->change_field_type($table, $field);

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2017030700, 'iomad_commerce');
    }

    if ($oldversion < 2023021000) {

        // Define field name to be added to course_shopsettings.
        $table = new xmldb_table('course_shopsettings');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, 'id');

        // Conditionally launch add field name.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field companyid to be added to course_shopsettings.
        $table = new xmldb_table('course_shopsettings');
        $field = new xmldb_field('companyid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'id');

        // Conditionally launch add field companyid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field program to be added to course_shopsettings.
        $table = new xmldb_table('course_shopsettings');
        $field = new xmldb_field('program', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'single_purchase_shelflife');

        // Conditionally launch add field program.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field instant to be added to course_shopsettings.
        $table = new xmldb_table('course_shopsettings');
        $field = new xmldb_field('instant', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'program');

        // Conditionally launch add field instant.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field cutofftime to be added to course_shopsettings.
        $table = new xmldb_table('course_shopsettings');
        $field = new xmldb_field('cutofftime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'instant');

        // Conditionally launch add field cutofftime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field clearonexpire to be added to course_shopsettings.
        $table = new xmldb_table('course_shopsettings');
        $field = new xmldb_field('clearonexpire', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'cutofftime');

        // Conditionally launch add field clearonexpire.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table course_shopsettings_courses to be created.
        $table = new xmldb_table('course_shopsettings_courses');

        // Adding fields to table course_shopsettings_courses.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table course_shopsettings_courses.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for course_shopsettings_courses.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Get all of the potential companies that have access to the shop.
        if (!empty($CFG->commerce_admin_enableall)) {
            $potentialcompanies = $DB->get_records('company');
        } else {
            $potentialcompanies = $DB->get_records('company', ['ecommerce' => 1]);
        }

        // Move all of the current courses over to the new tables.
        if ($shopitems = $DB->get_records('course_shopsettings')) {
            foreach ($shopitems as $shopitem) {
                if (!empty($shopitem->courseid)) {
                    $DB->insert_record('course_shopsettings_courses', ['itemid' => $shopitem->id, 'courseid' => $shopitem->courseid]);
                    if ($course = $DB->get_record('course', ['id' => $shopitem->courseid])) {
                        $shopitem->name = $course->fullname;
                        $ignore = false;
                    } else {
                        $shopitem->name = "MISSING COURSE";
                        $ignore = true;
                    }
                    $shopitem->single_purchase_validlength = $shopitem->single_purchase_validlength * 60 * 60 * 24;
                    $shopitem->single_purchase_shelflife = $shopitem->single_purchase_shelflife * 60 * 60 * 24;
                    $DB->update_record('course_shopsettings', $shopitem);
                    $blockprices = $DB->get_records('course_shopblockprice', ['courseid' => $shopitem->courseid]);
                    foreach ($blockprices as $blockid => $blockprice) {
                        $blockprice->courseid = $shopitem->id;
                        $DB->update_record('course_shopblockprice', $blockprice);
                        $blockprices[$blockid]->courseid = $shopitem->id;
                    }
                    $shoptags = $DB->get_records('course_shoptag', ['courseid' => $shopitem->courseid]);
                    foreach ($shoptags as $shoptagid => $shoptag) {
                        $shoptag->courseid = $shopitem->id;
                        $shoptags[$shoptagid]->courseid = $shopitem->id;
                    }
                    $DB->set_field('invoiceitem', 'invoiceableitemid', $shopitem->id, ['invoiceableitemid' => $shopitem->courseid]);

                    // Deal with all of the companies which could see this product.
                    if (!$ignore) {
                        foreach ($potentialcompanies as $company) {
                            if ($DB->get_record('company_course', ['companyid' => $company->id, 'courseid' => $course->id]) ||
                                $DB->get_record('iomad_courses', ['courseid' => $course->id, 'shared' => 1])) {
                                $companyitem = clone($shopitem);
                                unset($companyitem->id);
                                $companyitem->companyid = $company->id;
                                $companyitemid = $DB->insert_record('course_shopsettings', $companyitem);
                                $DB->insert_record('course_shopsettings_courses', ['itemid' => $companyitemid, 'courseid' => $course->id]);
                                $companyblockprices = $blockprices;
                                foreach ($companyblockprices as $companyblockprice) {
                                    unset($companyblockprice->id);
                                    $companyblockprice->courseid = $companyitemid;
                                    $DB->insert_record('course_shopblockprice', $companyblockprice);
                                }
                                $companyshoptags = $shoptags;
                                foreach ($companyshoptags as $companyshoptag) {
                                    unset($companyshoptag->id);
                                    $companyshoptag->courseid = $companyitemid;
                                    $DB->insert_record('course_shoptag', $companyshoptag);
                                }
                            }
                        }
                    }
                }
            }
        }
            
        // Drop the courseid field from the  course_shopsettings table.
        // Define field courseid to be dropped from course_shopsettings.
        $table = new xmldb_table('course_shopsettings');
        $field = new xmldb_field('courseid');

        // Conditionally launch drop field courseid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Rename field itemid on table course_shopblockprice to itemid.
        $table = new xmldb_table('course_shopblockprice');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'currency');

        // Launch rename field itemid.
        $dbman->rename_field($table, $field, 'itemid');

        // Rename field courseid on table course_shoptag to itemid.
        $table = new xmldb_table('course_shoptag');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch rename field courseid.
        $dbman->rename_field($table, $field, 'itemid');


        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2023021000, 'iomad_commerce');
    }

    if ($oldversion < 2023021900) {

        // Rename field pp_payerid on table invoice to paymentid.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_payerid');

        // Conditionally launch drop field pp_payerid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Rename field pp_payerstatus on table invoice to pp_accountid.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_payerstatus');

        // Conditionally launch drop field pp_payerstatus.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_ack to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_ack');

        // Conditionally launch drop field pp_ack.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_transactionid to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_transactionid');

        // Conditionally launch drop field pp_transactionid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_transactiontype to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_transactiontype');

        // Conditionally launch drop field pp_transactiontype.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_paymenttype to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_paymenttype');

        // Conditionally launch drop field pp_paymenttype.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_ordertime to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_ordertime');

        // Conditionally launch drop field pp_ordertime.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_currencycode to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_currencycode');

        // Conditionally launch drop field pp_currencycode.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_amount to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_amount');

        // Conditionally launch drop field pp_amount.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_feeamt to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_feeamt');

        // Conditionally launch drop field pp_feeamt.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_settleamt to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_settleamt');

        // Conditionally launch drop field pp_settleamt.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_taxamt to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_taxamt');

        // Conditionally launch drop field pp_taxamt.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_exchangerate to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_exchangerate');

        // Conditionally launch drop field pp_exchangerate.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_paymentstatus to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_paymentstatus');

        // Conditionally launch drop field pp_paymentstatus.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_pendingreason to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_pendingreason');

        // Conditionally launch drop field pp_pendingreason.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field pp_reason to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('pp_reason');

        // Conditionally launch drop field pp_reason.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field paymentid to be added to invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('paymentid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'phone1');

        // Conditionally launch add field paymentid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2023021900, 'iomad_commerce');
    }

    if ($oldversion < 2023021901) {

        // Define field companyid to be added to invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('companyid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'paymentid');

        // Conditionally launch add field companyid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // get all of the companies.
        $companies = $DB->get_records('company');
        foreach ($companies as $company) {
            $DB->set_field('invoice', 'companyid', $company->id, ['company' => $company->name]);
        }

        // Define field company to be dropped from invoice.
        $table = new xmldb_table('invoice');
        $field = new xmldb_field('company');

        // Conditionally launch drop field company.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Iomad_commerce savepoint reached.
        upgrade_block_savepoint(true, 2023021901, 'iomad_commerce');
    }

    return $result;
}
