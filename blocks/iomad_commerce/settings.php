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
 * iomad - admin settings
 *
 * @package    iomad
 * @copyright  2011 onwards E-Learn Design Limited
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$companyid = iomad::get_my_companyid(context_system::instance(), false);

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configcheckbox('commerce_enable_external',
                                                    get_string('useexternalshop', 'block_iomad_commerce'),
                                                    get_string('useexternalshop_help', 'block_iomad_commerce'),
                                                    0));

    $settings->add(new admin_setting_configtext('commerce_externalshop_url',
                                            get_string('commerce_externalshop_url', 'block_iomad_commerce'),
                                            get_string('commerce_externalshop_url', 'block_iomad_commerce'),
                                            '',
                                            PARAM_TEXT));

    if (!empty($companyid)) {
        $settings->add(new admin_setting_configtext('commerce_externalshop_url' . "_$companyid",
                                                get_string('commerce_externalshop_url_company', 'block_iomad_commerce'),
                                                get_string('commerce_externalshop_url_company', 'block_iomad_commerce'),
                                                '',
                                                PARAM_TEXT));
    }

    $settings->add(new admin_setting_configtext('commerce_externalshop_link_timeout',
                                            get_string('commerce_externalshop_link_timeout', 'block_iomad_commerce'),
                                            get_string('commerce_externalshop_link_timeout', 'block_iomad_commerce'),
                                            30,
                                            PARAM_INT));

    $settings->add(new admin_setting_configtext('commerce_admin_firstname',
                                            get_string('commerce_admin_firstname', 'block_iomad_commerce'),
                                            get_string('commerce_admin_firstname_help', 'block_iomad_commerce'),
                                            '',
                                            PARAM_TEXT));

    $settings->add(new admin_setting_configtext('commerce_admin_lastname',
                                            get_string('commerce_admin_lastname', 'block_iomad_commerce'),
                                            get_string('commerce_admin_lastname_help', 'block_iomad_commerce'),
                                            '',
                                            PARAM_TEXT));

    $settings->add(new admin_setting_configtext('commerce_admin_email',
                                            get_string('commerce_admin_email', 'block_iomad_commerce'),
                                            get_string('commerce_admin_email_help', 'block_iomad_commerce'),
                                            '',
                                            PARAM_EMAIL));

    $paypalcurrencies = enrol_get_plugin('paypal')->get_currencies();
    $settings->add(new admin_setting_configselect('commerce_admin_currency', get_string('currency', 'enrol_paypal'), '', 'GBP', $paypalcurrencies));

    $settings->add(new admin_setting_configcheckbox('commerce_admin_enableall',
                                                    get_string('opentoallcompanies', 'block_iomad_commerce'),
                                                    get_string('opentoallcompanies_help', 'block_iomad_commerce'),
                                                    1));

    $settings->add(new admin_setting_configtext('commerce_admin_default_license_access_length',
                                            get_string('commerce_default_license_access_length', 'block_iomad_commerce'),
                                            get_string('commerce_default_license_access_length_help', 'block_iomad_commerce'),
                                            30,
                                            PARAM_INT));

    $settings->add(new admin_setting_configtext('commerce_admin_default_license_shelf_life',
                                            get_string('commerce_admin_default_license_shelf_life', 'block_iomad_commerce'),
                                            get_string('commerce_admin_default_license_shelf_life_help', 'block_iomad_commerce'),
                                            365,
                                            PARAM_INT));

     $accounts = \core_payment\helper::get_payment_accounts_menu(context_system::instance());
     if ($accounts) {
         $accounts = ((count($accounts) > 1) ? ['' => ''] : []) + $accounts;
     } else {
         $accounts = ['' => ''];
     }
     $settings->add(new admin_setting_configselect('commerce_admin_paymentaccount', get_string('paymentaccount', 'payment'), '', '', $accounts));

}


