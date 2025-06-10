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

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/mhaairs/block_mhaairs_util.php');

/*
 * We can not use PARAM_INT for filtering since it filters values up to PHP integer maximum,
 * which can be less than database coulmn maximum
 * for example on Linux complied PHP 32-bit signed maxint is 2147483647
 * on Linux x64 compiled PHP 64-bit signed maxint is 9223372036854775807
 * on Windows both offical PHP 32-bit and unofficial 64-bit have maxint of 2147483647
 * in case of mysql Moodle uses by default signed BIGINT for all id columns which has maxint of 9223372036854775807
 * 64bit version
 *
 */

$courseid = required_param('courseid', PARAM_ALPHANUM);
$serviceurl = required_param('serviceurl', PARAM_ALPHANUM);
$serviceid = required_param('serviceid' , PARAM_ALPHANUM);

if (!is_numeric($courseid)) {
    print_error('invalidaccessparameter');
}
require_login($courseid);

// Set the url and redirect.
$url = MHUtil::get_sso_url($serviceurl, $serviceid);
redirect($url);
