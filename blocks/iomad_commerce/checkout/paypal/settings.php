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

$settings->add(new admin_setting_configcheckbox('paypal_usesandbox',
                                get_string('pp_paypal_usesandbox', 'block_iomad_commerce', $pname),
                                get_string('pp_paypal_usesandbox_help', 'block_iomad_commerce', $pname),
                                0));

$settings->add(new admin_setting_configtext('paypal_api_username',
                                            get_string('pp_paypal_api_username', 'block_iomad_commerce'),
                                            '',
                                            '',
                                            PARAM_NOTAGS));

$settings->add(new admin_setting_configtext('paypal_api_password',
                                            get_string('pp_paypal_api_password', 'block_iomad_commerce'),
                                            '',
                                            '',
                                            PARAM_NOTAGS));

$settings->add(new admin_setting_configtext('paypal_api_signature',
                                            get_string('pp_paypal_api_signature', 'block_iomad_commerce'),
                                            '',
                                            '',
                                            PARAM_NOTAGS));
