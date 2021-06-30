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
 * A token refreshing helper for Generico
 *
 *
 * @package    filter_generico
 * @copyright  Justin Hunt (justin@poodll.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

use \filter_generico\constants;

require_login(0, false);
$systemcontext = context_system::instance();

if (has_capability('moodle/site:config', $systemcontext)) {
    $apiuser = get_config(constants::MOD_FRANKY, 'cpapiuser');
    $apisecret = get_config(constants::MOD_FRANKY, 'cpapisecret');
    $force = true;
    if ($apiuser && $apisecret) {
        $gu = new \filter_generico\generico_utils();
        $gu->fetch_token($apiuser, $apisecret, $force);
    }
}
redirect($CFG->wwwroot . '/admin/settings.php?section=filtersettinggenerico');