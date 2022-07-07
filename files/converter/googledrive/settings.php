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
 * Link to the OAuth 2 service we will use.
 *
 * @package   fileconverter_googledrive
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $options = [];
    $issuers = \core\oauth2\api::get_all_issuers();

    $options[''] = get_string('disabled', 'fileconverter_googledrive');
    foreach ($issuers as $issuer) {
        $options[$issuer->get('id')] = s($issuer->get('name'));
    }

    $settings->add(new admin_setting_configselect('fileconverter_googledrive/issuerid',
                                                  get_string('issuer', 'fileconverter_googledrive'),
                                                  get_string('issuer_help', 'fileconverter_googledrive'),
                                                  '',
                                                  $options));

    $url = new moodle_url('/files/converter/googledrive/test.php');
    $link = html_writer::link($url, get_string('test_converter', 'fileconverter_googledrive'));
    $settings->add(new admin_setting_heading('test_converter', '', $link));
}
