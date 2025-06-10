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
 * Web service auto configuration page
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_ally\auto_config;
use tool_ally\local;

require(__DIR__.'/../../../config.php');

$PAGE->set_url(new moodle_url('/admin/tool/ally/autoconfigws.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('autoconfigure', 'tool_ally'));

require_login();
require_capability('moodle/site:config', context_system::instance());

$action = optional_param('action', null, PARAM_ALPHA);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('autoconfigure', 'tool_ally'));

if ($action === 'confirm' || $action === 'view') {
    if ($action === 'confirm') {
        $ac = new auto_config();
        $ac->configure();
    }
    $wstoken = local::get_ws_token();
    if (empty($wstoken->token)) {
        throw new moodle_exception('generalexceptionmessage', 'error', '', get_string('error:wstokenmissing', 'tool_ally'));
    }
    $token = $wstoken->token;
    $sampleapicall = $CFG->wwwroot.'/webservice/rest/server.php?wstoken='.$token.
            '&wsfunction=tool_ally_version_info&'.'moodlewsrestformat=json';
    $context = (object) ['token' => $token, 'sampleapicall' => $sampleapicall];
    echo $OUTPUT->render_from_template('tool_ally/auto_conf_result', $context);
    echo $OUTPUT->continue_button(new moodle_url('/admin/settings.php', ['section' => 'tool_ally']));
} else {
    $continueurl = new moodle_url('/admin/tool/ally/autoconfigws.php', ['action' => 'confirm']);
    $cancelurl = new moodle_url('/admin/settings.php', ['section' => 'tool_ally']);
    echo $OUTPUT->confirm(get_string('autoconfigureconfirmation', 'tool_ally'), $continueurl, $cancelurl);
}

echo $OUTPUT->footer();
