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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $dir = dirname(__FILE__);
    require_once($dir .'/lib.php');

    $settings->add(new admin_setting_configcheckbox('commerce_enable_external',
                                                    get_string('useexternalshop', 'block_iomad_commerce'),
                                                    get_string('useexternalshop_help', 'block_iomad_commerce'),
                                                    1));

    $settings->add(new admin_setting_configtext('commerce_externalshop_url',
                                            get_string('commerce_externalshop_url', 'block_iomad_commerce'),
                                            get_string('commerce_externalshop_url', 'block_iomad_commerce'),
                                            '',
                                            PARAM_TEXT));

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

    $pp = get_payment_providers();
    foreach ($pp as $p) {

        $pname = get_string('pp_' . $p . '_name', 'block_iomad_commerce');

        $settings->add(new admin_setting_configcheckbox($p . '_enabled',
                                        get_string('paymentprovider_enabled', 'block_iomad_commerce', $pname),
                                        get_string('paymentprovider_enabled_help', 'block_iomad_commerce', $pname),
                                        0));

        $phpname = "$dir/checkout/$p/settings.php";

        if (file_exists($phpname)) {
            require_once($phpname);
        }
    }
}


