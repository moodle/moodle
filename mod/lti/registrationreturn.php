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
 * Handle the return from the Tool Provider after registering a tool proxy.
 *
 * @package mod_lti
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

$top = optional_param('top', 0, PARAM_INT);
$msg = optional_param('lti_msg', '', PARAM_TEXT);
$err = optional_param('lti_errormsg', '', PARAM_TEXT);
$id = optional_param('id', 0, PARAM_INT);

// No guest autologin.
require_sesskey();
require_login(0, false);

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

if (empty($top)) {

    $params = array();
    $params['sesskey'] = sesskey();
    $params['top'] = '1';
    if (!empty($msg)) {
        $params['lti_msg'] = $msg;
    }
    if (!empty($err)) {
        $params['lti_errormsg'] = $err;
    }
    if (!empty($id)) {
        $params['id'] = $id;
    }
    $redirect = new moodle_url('/mod/lti/registrationreturn.php', $params);
    $redirect = $redirect->out(false);

    $clickhere = get_string('click_to_continue', 'lti', (object)array('link' => $redirect));
    $html = <<< EOD
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">
//<![CDATA[
top.location.href = '{$redirect}';
//]]
</script>
</head>
<body>
<noscript>
{$clickhere}
</noscript>
</body>
</html>
EOD;

    // We always send the headers because they set the encoding.
    send_headers('text/html; charset=utf-8', false);
    echo $html;

} else if (!empty($msg) && !empty($err)) {

    $params = array();
    $params['sesskey'] = sesskey();
    $params['top'] = '1';
    if (!empty($err)) {
        $params['lti_errormsg'] = $err;
    }
    if (!empty($id)) {
        $params['id'] = $id;
    }
    $redirect = new moodle_url('/mod/lti/registrationreturn.php', $params);
    $redirect = $redirect->out(false);
    redirect($redirect, $err);

} else {

    $redirect = new moodle_url('/mod/lti/toolproxies.php');
    if (!empty($id)) {
        $toolproxy = $DB->get_record('lti_tool_proxies', array('id' => $id));
        switch($toolproxy->state) {
            case LTI_TOOL_PROXY_STATE_ACCEPTED:
                $redirect->param('tab', 'tp_accepted');
                break;
            case LTI_TOOL_PROXY_STATE_REJECTED:
                $redirect->param('tab', 'tp_rejected');
                break;
            case LTI_TOOL_PROXY_STATE_PENDING:
                // Change the status to configured.
                $toolproxy->state = LTI_TOOL_PROXY_STATE_CONFIGURED;
                lti_update_tool_proxy($toolproxy);
        }
    }

    $redirect = $redirect->out();

    if (empty($msg)) {
        $msg = $err;
    }
    redirect($redirect, $msg);

}
