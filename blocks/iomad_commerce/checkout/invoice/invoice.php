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

require_once(dirname(__FILE__) . '/../paymentprovider.php');
require_once(dirname(__FILE__) . '/../../../../local/email/lib.php');

class invoice extends payment_provider {
    public function init() {
        parent::init();

        $url = new moodle_url('/blocks/iomad_commerce/review.php');
        redirect($url);
    }

    public function get_order_review_html() {
        $html = '';

        $html .= '<p>' . get_string('pp_invoice_review_instructions', 'block_iomad_commerce') . '</p>';

        return $html;
    }

    public function confirm() {
        global $DB, $CFG;
        if ($basketid = get_basket_id()) {
            // Send invoice email to the user.
            $basket = get_basket();
            $basket->itemized = get_invoice_html($basketid, 0, 0);
            // Notify shop admin.
            if (isset($CFG->commerce_admin_email)) {
                if (!$shopadmin = $DB->get_record('user', array('email' => $CFG->commerce_admin_email))) {
                    $shopadmin = new stdClass;
                    $shopadmin->email = $CFG->commerce_admin_email;
                    if (empty($CFG->commerce_admin_firstname)) {
                        $shopadmin->firstname = "Shop";
                    } else {
                        $shopadmin->firstname = $CFG->commerce_admin_firstname;
                    }
                    if (empty($CFG->commerce_admin_lastname)) {
                        $shopadmin->lastname = "Admin";
                    } else {
                        $shopadmin->lastname = $CFG->commerce_admin_lastname;
                    }
                    $shopadmin->id = -999;
                }
            } else {
                $shopadmin = new stdClass;
                $shopadmin->email = $CFG->support_email;
                if (empty($CFG->commerce_admin_firstname)) {
                    $shopadmin->firstname = "Shop";
                } else {
                    $shopadmin->firstname = $CFG->commerce_admin_firstname;
                }
                if (empty($CFG->commerce_admin_lastname)) {
                    $shopadmin->lastname = "Admin";
                } else {
                    $shopadmin->lastname = $CFG->commerce_admin_lastname;
                }
                $shopadmin->id = -999;
            }

            if ($user = $DB->get_record('user',  array('id' => $basket->userid))) {
                EmailTemplate::send('invoice_ordercomplete', array('user' => $user, 'invoice' => $basket, 'sender' => $shopadmin));

                // Notify shop admin.
                if (isset($CFG->commerce_admin_email)) {
                    EmailTemplate::send('invoice_ordercomplete_admin', array('user' => $shopadmin,
                                                                             'invoice' => $basket,
                                                                             'sender' => $shopadmin));
                }

                // Set status of invoice to unpaid.
                $DB->set_field('invoice', 'status', INVOICESTATUS_UNPAID, array('id' => $basketid));

                return '';
            }
        }

        return '<p class="error">' . get_string('pp_invoice_basketnolongeravailable', 'block_iomad_commerce') . '</p>';
    }

    public function get_confirmation_html($invoice) {
        return '<p>' . get_string('pp_invoice_confirmation', 'block_iomad_commerce', $invoice) . '</p>';
    }

    public function pre_order_review_processing() {
    }
}
