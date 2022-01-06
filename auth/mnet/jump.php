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
 * Authentication Plugin: Moodle Network Authentication
 * Multiple host authentication support for Moodle Network.
 *
 * @package auth_mnet
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once __DIR__ . '/../../config.php';

// grab the GET params - wantsurl could be anything - take it
// with PARAM_RAW
$hostid = optional_param('hostid', '0', PARAM_INT);
$hostwwwroot = optional_param('hostwwwroot', '', PARAM_URL);
$wantsurl = optional_param('wantsurl', '', PARAM_RAW);

$url = new moodle_url('/auth/mnet/jump.php');
if ($hostid !== '0') $url->param('hostid', $hostid);
if ($hostwwwroot !== '') $url->param('hostwwwroot', $hostwwwroot);
if ($wantsurl !== '') $url->param('wantsurl', $wantsurl);
$PAGE->set_url($url);

if (!isloggedin() or isguestuser()) {
    $SESSION->wantsurl = $PAGE->url->out(false);
    redirect(get_login_url());
}

if (!is_enabled_auth('mnet')) {
    print_error('mnetdisable');
}

// If hostid hasn't been specified, try getting it using wwwroot
if (!$hostid) {
    $hostwwwroot = trim($hostwwwroot);
    $hostwwwroot = rtrim($hostwwwroot, '/');

    // ensure the wwwroot starts with a http or https prefix
    if (strtolower(substr($hostwwwroot, 0, 4)) != 'http') {
        $hostwwwroot = 'http://'.$hostwwwroot;
    }
    $hostid = $DB->get_field('mnet_host', 'id', array('wwwroot' => $hostwwwroot));
}

// start the mnet session and redirect browser to remote URL
$mnetauth = get_auth_plugin('mnet');
$url      = $mnetauth->start_jump_session($hostid, $wantsurl);

if (empty($url)) {
    print_error('DEBUG: Jump session was not started correctly or blank URL returned.'); // TODO: errors
}
redirect($url);


