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
 * Lib functions
 *
 * @package    report
 * @subpackage security
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


define('REPORT_SECURITY_OK', 'ok');
define('REPORT_SECURITY_INFO', 'info');
define('REPORT_SECURITY_WARNING', 'warning');
define('REPORT_SECURITY_SERIOUS', 'serious');
define('REPORT_SECURITY_CRITICAL', 'critical');

function report_security_hide_timearning() {
     global $PAGE;
     $PAGE->requires->js_init_code("Y.one('#timewarning').addClass('timewarninghidden')");
}

function report_security_get_issue_list() {
    return array(
        'report_security_check_unsecuredataroot',
        'report_security_check_displayerrors',
        'report_security_check_vendordir',
        'report_security_check_nodemodules',
        'report_security_check_noauth',
        'report_security_check_embed',
        'report_security_check_mediafilterswf',
        'report_security_check_openprofiles',
        'report_security_check_crawlers',
        'report_security_check_passwordpolicy',
        'report_security_check_emailchangeconfirmation',
        'report_security_check_cookiesecure',
        'report_security_check_configrw',
        'report_security_check_riskxss',
        'report_security_check_riskadmin',
        'report_security_check_riskbackup',
        'report_security_check_defaultuserrole',
        'report_security_check_guestrole',
        'report_security_check_frontpagerole',
        'report_security_check_webcron',
        'report_security_check_preventexecpath',

    );
}

function report_security_doc_link($issue, $name) {
    global $CFG, $OUTPUT;

    if (empty($CFG->docroot)) {
        return $name;
    }

    return $OUTPUT->doc_link('report/security/'.$issue, $name);
}

///=============================================
///               Issue checks
///=============================================


/**
 * Verifies unsupported noauth setting
 * @param bool $detailed
 * @return object result
 */
function report_security_check_noauth($detailed=false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_noauth';
    $result->name    = get_string('check_noauth_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=manageauths\">".get_string('authsettings', 'admin').'</a>';

    if (is_enabled_auth('none')) {
        $result->status = REPORT_SECURITY_CRITICAL;
        $result->info   = get_string('check_noauth_error', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_noauth_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_noauth_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies if password policy set
 * @param bool $detailed
 * @return object result
 */
function report_security_check_passwordpolicy($detailed=false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_passwordpolicy';
    $result->name    = get_string('check_passwordpolicy_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=sitepolicies\">".get_string('sitepolicies', 'admin').'</a>';

    if (empty($CFG->passwordpolicy)) {
        $result->status = REPORT_SECURITY_WARNING;
        $result->info   = get_string('check_passwordpolicy_error', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_passwordpolicy_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_passwordpolicy_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies sloppy embedding - this should have been removed long ago!!
 * @param bool $detailed
 * @return object result
 */
function report_security_check_embed($detailed=false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_embed';
    $result->name    = get_string('check_embed_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=sitepolicies\">".get_string('sitepolicies', 'admin').'</a>';

    if (!empty($CFG->allowobjectembed)) {
        $result->status = REPORT_SECURITY_CRITICAL;
        $result->info   = get_string('check_embed_error', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_embed_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_embed_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies sloppy swf embedding - this should have been removed long ago!!
 * @param bool $detailed
 * @return object result
 */
function report_security_check_mediafilterswf($detailed=false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_mediafilterswf';
    $result->name    = get_string('check_mediafilterswf_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=managemediaplayers\">" .
        get_string('managemediaplayers', 'media') . '</a>';

    $activefilters = filter_get_globally_enabled();

    $enabledmediaplayers = \core\plugininfo\media::get_enabled_plugins();
    if (array_search('mediaplugin', $activefilters) !== false and array_key_exists('swf', $enabledmediaplayers)) {
        $result->status = REPORT_SECURITY_CRITICAL;
        $result->info   = get_string('check_mediafilterswf_error', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_mediafilterswf_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_mediafilterswf_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies fatal misconfiguration of dataroot
 * @param bool $detailed
 * @return object result
 */
function report_security_check_unsecuredataroot($detailed=false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_unsecuredataroot';
    $result->name    = get_string('check_unsecuredataroot_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = null;

    $insecuredataroot = is_dataroot_insecure(true);

    if ($insecuredataroot == INSECURE_DATAROOT_WARNING) {
        $result->status = REPORT_SECURITY_SERIOUS;
        $result->info   = get_string('check_unsecuredataroot_warning', 'report_security', $CFG->dataroot);

    } else if ($insecuredataroot == INSECURE_DATAROOT_ERROR) {
        $result->status = REPORT_SECURITY_CRITICAL;
        $result->info   = get_string('check_unsecuredataroot_error', 'report_security', $CFG->dataroot);

    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_unsecuredataroot_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_unsecuredataroot_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies displaying of errors - problem for lib files and 3rd party code
 * because we can not disable debugging in these scripts (they do not include config.php)
 * @param bool $detailed
 * @return object result
 */
function report_security_check_displayerrors($detailed=false) {
    $result = new stdClass();
    $result->issue   = 'report_security_check_displayerrors';
    $result->name    = get_string('check_displayerrors_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = null;

    if (defined('WARN_DISPLAY_ERRORS_ENABLED')) {
        $result->status = REPORT_SECURITY_WARNING;
        $result->info   = get_string('check_displayerrors_error', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_displayerrors_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_displayerrors_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies open profiles - originally open by default, not anymore because spammer abused it a lot
 * @param bool $detailed
 * @return object result
 */
function report_security_check_openprofiles($detailed=false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_openprofiles';
    $result->name    = get_string('check_openprofiles_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=sitepolicies\">".get_string('sitepolicies', 'admin').'</a>';

    if (empty($CFG->forcelogin) and empty($CFG->forceloginforprofiles)) {
        $result->status = REPORT_SECURITY_WARNING;
        $result->info   = get_string('check_openprofiles_error', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_openprofiles_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_openprofiles_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies web crawler (search engine) access not combined with disabled guest access
 * because attackers might gain guest access by modifying browser signature.
 * @param bool $detailed
 * @return object result
 */
function report_security_check_crawlers($detailed=false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_crawlers';
    $result->name    = get_string('check_crawlers_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=sitepolicies\">".get_string('sitepolicies', 'admin').'</a>';

    if (empty($CFG->opentowebcrawlers)) {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_crawlers_ok', 'report_security');
    } else if (!empty($CFG->guestloginbutton)) {
        $result->status = REPORT_SECURITY_INFO;
        $result->info   = get_string('check_crawlers_info', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_SERIOUS;
        $result->info   = get_string('check_crawlers_error', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_crawlers_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies email confirmation - spammers were changing mails very often
 * @param bool $detailed
 * @return object result
 */
function report_security_check_emailchangeconfirmation($detailed=false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_emailchangeconfirmation';
    $result->name    = get_string('check_emailchangeconfirmation_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=sitepolicies\">".get_string('sitepolicies', 'admin').'</a>';

    if (empty($CFG->emailchangeconfirmation)) {
        if (empty($CFG->allowemailaddresses)) {
            $result->status = REPORT_SECURITY_WARNING;
            $result->info   = get_string('check_emailchangeconfirmation_error', 'report_security');
        } else {
            $result->status = REPORT_SECURITY_INFO;
            $result->info   = get_string('check_emailchangeconfirmation_info', 'report_security');
        }
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_emailchangeconfirmation_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_emailchangeconfirmation_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies if https enabled only secure cookies allowed,
 * this prevents redirections and sending of cookies to unsecure port.
 * @param bool $detailed
 * @return object result
 */
function report_security_check_cookiesecure($detailed=false) {
    global $CFG;

    if (!is_https()) {
        return null;
    }

    $result = new stdClass();
    $result->issue   = 'report_security_check_cookiesecure';
    $result->name    = get_string('check_cookiesecure_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=httpsecurity\">".get_string('httpsecurity', 'admin').'</a>';

    if (!is_moodle_cookie_secure()) {
        $result->status = REPORT_SECURITY_SERIOUS;
        $result->info   = get_string('check_cookiesecure_error', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_cookiesecure_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_cookiesecure_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies config.php is not writable anymore after installation,
 * config files were changed on several outdated server.
 * @param bool $detailed
 * @return object result
 */
function report_security_check_configrw($detailed=false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_configrw';
    $result->name    = get_string('check_configrw_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = null;

    if (is_writable($CFG->dirroot.'/config.php')) {
        $result->status = REPORT_SECURITY_WARNING;
        $result->info   = get_string('check_configrw_warning', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_configrw_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_configrw_details', 'report_security');
    }

    return $result;
}


/**
 * Lists all users with XSS risk, it would be great to combine this with risk trusts in user table,
 * unfortunately nobody implemented user trust UI yet :-(
 * @param bool $detailed
 * @return object result
 */
function report_security_check_riskxss($detailed=false) {
    global $DB;

    $result = new stdClass();
    $result->issue   = 'report_security_check_riskxss';
    $result->name    = get_string('check_riskxss_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = REPORT_SECURITY_WARNING;
    $result->link    = null;

    $params = array('capallow'=>CAP_ALLOW);

    $sqlfrom = "FROM (SELECT DISTINCT rcx.contextid, rcx.roleid
                       FROM {role_capabilities} rcx
                       JOIN {capabilities} cap ON (cap.name = rcx.capability AND ".$DB->sql_bitand('cap.riskbitmask', RISK_XSS)." <> 0)
                       WHERE rcx.permission = :capallow) rc,
                     {context} c,
                     {context} sc,
                     {role_assignments} ra,
                     {user} u
               WHERE c.id = rc.contextid
                     AND (sc.path = c.path OR sc.path LIKE ".$DB->sql_concat('c.path', "'/%'")." OR c.path LIKE ".$DB->sql_concat('sc.path', "'/%'").")
                     AND u.id = ra.userid AND u.deleted = 0
                     AND ra.contextid = sc.id AND ra.roleid = rc.roleid";

    $count = $DB->count_records_sql("SELECT COUNT(DISTINCT u.id) $sqlfrom", $params);

    $result->info = get_string('check_riskxss_warning', 'report_security', $count);

    if ($detailed) {
        $userfields = user_picture::fields('u');
        $users = $DB->get_records_sql("SELECT DISTINCT $userfields $sqlfrom", $params);
        foreach ($users as $uid=>$user) {
            $users[$uid] = fullname($user);
        }
        $users = implode(', ', $users);
        $result->details = get_string('check_riskxss_details', 'report_security', $users);
    }

    return $result;
}

/**
 * Verifies sanity of default user role.
 * @param bool $detailed
 * @return object result
 */
function report_security_check_defaultuserrole($detailed=false) {
    global $DB, $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_defaultuserrole';
    $result->name    = get_string('check_defaultuserrole_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=userpolicies\">".get_string('userpolicies', 'admin').'</a>';

    if (!$default_role = $DB->get_record('role', array('id'=>$CFG->defaultuserroleid))) {
        $result->status  = REPORT_SECURITY_WARNING;
        $result->info    = get_string('check_defaultuserrole_notset', 'report_security');
        $result->details = $result->info;

        return $result;
    }

    // risky caps - usually very dangerous
    $params = array('capallow'=>CAP_ALLOW, 'roleid'=>$default_role->id);
    $sql = "SELECT COUNT(DISTINCT rc.contextid)
              FROM {role_capabilities} rc
              JOIN {capabilities} cap ON cap.name = rc.capability
             WHERE ".$DB->sql_bitand('cap.riskbitmask', (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))." <> 0
                   AND rc.permission = :capallow
                   AND rc.roleid = :roleid";

    $riskycount = $DB->count_records_sql($sql, $params);

    // it may have either none or 'user' archetype - nothing else, or else it would break during upgrades badly
    if ($default_role->archetype === '' or $default_role->archetype === 'user') {
        $legacyok = true;
    } else {
        $legacyok = false;
    }

    if ($riskycount or !$legacyok) {
        $result->status  = REPORT_SECURITY_CRITICAL;
        $result->info    = get_string('check_defaultuserrole_error', 'report_security', role_get_name($default_role));

    } else {
        $result->status  = REPORT_SECURITY_OK;
        $result->info    = get_string('check_defaultuserrole_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_defaultuserrole_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies sanity of guest role
 * @param bool $detailed
 * @return object result
 */
function report_security_check_guestrole($detailed=false) {
    global $DB, $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_guestrole';
    $result->name    = get_string('check_guestrole_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=userpolicies\">".get_string('userpolicies', 'admin').'</a>';

    if (!$guest_role = $DB->get_record('role', array('id'=>$CFG->guestroleid))) {
        $result->status  = REPORT_SECURITY_WARNING;
        $result->info    = get_string('check_guestrole_notset', 'report_security');
        $result->details = $result->info;

        return $result;
    }

    // risky caps - usually very dangerous
    $params = array('capallow'=>CAP_ALLOW, 'roleid'=>$guest_role->id);
    $sql = "SELECT COUNT(DISTINCT rc.contextid)
              FROM {role_capabilities} rc
              JOIN {capabilities} cap ON cap.name = rc.capability
             WHERE ".$DB->sql_bitand('cap.riskbitmask', (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))." <> 0
                   AND rc.permission = :capallow
                   AND rc.roleid = :roleid";

    $riskycount = $DB->count_records_sql($sql, $params);

    // it may have either no or 'guest' archetype - nothing else, or else it would break during upgrades badly
    if ($guest_role->archetype === '' or $guest_role->archetype === 'guest') {
        $legacyok = true;
    } else {
        $legacyok = false;
    }

    if ($riskycount or !$legacyok) {
        $result->status  = REPORT_SECURITY_CRITICAL;
        $result->info    = get_string('check_guestrole_error', 'report_security', format_string($guest_role->name));

    } else {
        $result->status  = REPORT_SECURITY_OK;
        $result->info    = get_string('check_guestrole_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_guestrole_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies sanity of frontpage role
 * @param bool $detailed
 * @return object result
 */
function report_security_check_frontpagerole($detailed=false) {
    global $DB, $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_frontpagerole';
    $result->name    = get_string('check_frontpagerole_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=frontpagesettings\">".get_string('frontpagesettings','admin').'</a>';

    if (!$frontpage_role = $DB->get_record('role', array('id'=>$CFG->defaultfrontpageroleid))) {
        $result->status  = REPORT_SECURITY_INFO;
        $result->info    = get_string('check_frontpagerole_notset', 'report_security');
        $result->details = get_string('check_frontpagerole_details', 'report_security');

        return $result;
    }

    // risky caps - usually very dangerous
    $params = array('capallow'=>CAP_ALLOW, 'roleid'=>$frontpage_role->id);
    $sql = "SELECT COUNT(DISTINCT rc.contextid)
              FROM {role_capabilities} rc
              JOIN {capabilities} cap ON cap.name = rc.capability
             WHERE ".$DB->sql_bitand('cap.riskbitmask', (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))." <> 0
                   AND rc.permission = :capallow
                   AND rc.roleid = :roleid";

    $riskycount = $DB->count_records_sql($sql, $params);

    // there is no legacy role type for frontpage yet - anyway we can not allow teachers or admins there!
    if ($frontpage_role->archetype === 'teacher' or $frontpage_role->archetype === 'editingteacher'
      or $frontpage_role->archetype === 'coursecreator' or $frontpage_role->archetype === 'manager') {
        $legacyok = false;
    } else {
        $legacyok = true;
    }

    if ($riskycount or !$legacyok) {
        $result->status  = REPORT_SECURITY_CRITICAL;
        $result->info    = get_string('check_frontpagerole_error', 'report_security', role_get_name($frontpage_role));

    } else {
        $result->status  = REPORT_SECURITY_OK;
        $result->info    = get_string('check_frontpagerole_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_frontpagerole_details', 'report_security');
    }

    return $result;
}

/**
 * Lists all admins.
 * @param bool $detailed
 * @return object result
 */
function report_security_check_riskadmin($detailed=false) {
    global $DB, $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_riskadmin';
    $result->name    = get_string('check_riskadmin_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = null;

    $userfields = user_picture::fields('u');
    $sql = "SELECT $userfields
              FROM {user} u
             WHERE u.id IN ($CFG->siteadmins)";

    $admins = $DB->get_records_sql($sql);
    $admincount = count($admins);

    if ($detailed) {
        foreach ($admins as $uid=>$user) {
            $url = "$CFG->wwwroot/user/view.php?id=$user->id";
            $admins[$uid] = '<li><a href="'.$url.'">'.fullname($user).' ('.$user->email.')</a></li>';
        }
        $admins = '<ul>'.implode('', $admins).'</ul>';
    }

    $result->status  = REPORT_SECURITY_OK;
    $result->info = get_string('check_riskadmin_ok', 'report_security', $admincount);

    if ($detailed) {
        $result->details = get_string('check_riskadmin_detailsok', 'report_security', $admins);
    }

    return $result;
}

/**
 * Lists all roles that have the ability to backup user data, as well as users
 * @param bool $detailed
 * @return object result
 */
function report_security_check_riskbackup($detailed=false) {
    global $CFG, $DB;

    $result = new stdClass();
    $result->issue   = 'report_security_check_riskbackup';
    $result->name    = get_string('check_riskbackup_name', 'report_security');
    $result->info    = null;
    $result->details = null;
    $result->status  = null;
    $result->link    = null;

    $syscontext = context_system::instance();

    $params = array('capability'=>'moodle/backup:userinfo', 'permission'=>CAP_ALLOW, 'contextid'=>$syscontext->id);
    $sql = "SELECT DISTINCT r.id, r.name, r.shortname, r.sortorder, r.archetype
              FROM {role} r
              JOIN {role_capabilities} rc ON rc.roleid = r.id
             WHERE rc.capability = :capability
               AND rc.contextid  = :contextid
               AND rc.permission = :permission";
    $systemroles = $DB->get_records_sql($sql, $params);

    $params = array('capability'=>'moodle/backup:userinfo', 'permission'=>CAP_ALLOW, 'contextid'=>$syscontext->id);
    $sql = "SELECT DISTINCT r.id, r.name, r.shortname, r.sortorder, r.archetype, rc.contextid
              FROM {role} r
              JOIN {role_capabilities} rc ON rc.roleid = r.id
             WHERE rc.capability = :capability
               AND rc.contextid <> :contextid
               AND rc.permission = :permission";
    $overriddenroles = $DB->get_records_sql($sql, $params);

    // list of users that are able to backup personal info
    // note: "sc" is context where is role assigned,
    //       "c" is context where is role overridden or system context if in role definition
    $params = array('capability'=>'moodle/backup:userinfo', 'permission'=>CAP_ALLOW, 'context1'=>CONTEXT_COURSE, 'context2'=>CONTEXT_COURSE);

    $sqluserinfo = "
        FROM (SELECT DISTINCT rcx.contextid, rcx.roleid
                FROM {role_capabilities} rcx
               WHERE rcx.permission = :permission AND rcx.capability = :capability) rc,
             {context} c,
             {context} sc,
             {role_assignments} ra,
             {user} u
       WHERE c.id = rc.contextid
             AND (sc.path = c.path OR sc.path LIKE ".$DB->sql_concat('c.path', "'/%'")." OR c.path LIKE ".$DB->sql_concat('sc.path', "'/%'").")
             AND u.id = ra.userid AND u.deleted = 0
             AND ra.contextid = sc.id AND ra.roleid = rc.roleid
             AND sc.contextlevel <= :context1 AND c.contextlevel <= :context2";

    $usercount = $DB->count_records_sql("SELECT COUNT('x') FROM (SELECT DISTINCT u.id $sqluserinfo) userinfo", $params);
    $systemrolecount = empty($systemroles) ? 0 : count($systemroles);
    $overriddenrolecount = empty($overriddenroles) ? 0 : count($overriddenroles);

    if (max($usercount, $systemrolecount, $overriddenrolecount) > 0) {
        $result->status = REPORT_SECURITY_WARNING;
    } else {
        $result->status = REPORT_SECURITY_OK;
    }

    $a = (object)array('rolecount'=>$systemrolecount,'overridecount'=>$overriddenrolecount,'usercount'=>$usercount);
    $result->info = get_string('check_riskbackup_warning', 'report_security', $a);

    if ($detailed) {

        $result->details = '';  // Will be added to later

        // Make a list of roles
        if ($systemroles) {
            $links = array();
            foreach ($systemroles as $role) {
                $role->name = role_get_name($role);
                $role->url = "$CFG->wwwroot/$CFG->admin/roles/manage.php?action=edit&amp;roleid=$role->id";
                $links[] = '<li>'.get_string('check_riskbackup_editrole', 'report_security', $role).'</li>';
            }
            $links = '<ul>'.implode($links).'</ul>';
            $result->details .= get_string('check_riskbackup_details_systemroles', 'report_security', $links);
        }

        // Make a list of overrides to roles
        $rolelinks2 = array();
        if ($overriddenroles) {
            $links = array();
            foreach ($overriddenroles as $role) {
                $role->name = $role->localname;
                $context = context::instance_by_id($role->contextid);
                $role->name = role_get_name($role, $context, ROLENAME_BOTH);
                $role->contextname = $context->get_context_name();
                $role->url = "$CFG->wwwroot/$CFG->admin/roles/override.php?contextid=$role->contextid&amp;roleid=$role->id";
                $links[] = '<li>'.get_string('check_riskbackup_editoverride', 'report_security', $role).'</li>';
            }
            $links = '<ul>'.implode('', $links).'</ul>';
            $result->details .= get_string('check_riskbackup_details_overriddenroles', 'report_security', $links);
        }

        // Get a list of affected users as well
        $users = array();

        list($sort, $sortparams) = users_order_by_sql('u');
        $userfields = user_picture::fields('u');
        $rs = $DB->get_recordset_sql("SELECT DISTINCT $userfields, ra.contextid, ra.roleid
            $sqluserinfo ORDER BY $sort", array_merge($params, $sortparams));

        foreach ($rs as $user) {
            $context = context::instance_by_id($user->contextid);
            $url = "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=$user->contextid&amp;roleid=$user->roleid";
            $a = (object)array('fullname'=>fullname($user), 'url'=>$url, 'email'=>$user->email,
                               'contextname'=>$context->get_context_name());
            $users[] = '<li>'.get_string('check_riskbackup_unassign', 'report_security', $a).'</li>';
        }
        $rs->close();
        if (!empty($users)) {
            $users = '<ul>'.implode('', $users).'</ul>';
            $result->details .= get_string('check_riskbackup_details_users', 'report_security', $users);
        }
    }

    return $result;
}

/**
 * Verifies the status of web cron
 *
 * @param bool $detailed
 * @return object result
 */
function report_security_check_webcron($detailed = false) {
    global $CFG;

    $croncli = $CFG->cronclionly;
    $cronremotepassword = $CFG->cronremotepassword;

    $result = new stdClass();
    $result->issue   = 'report_security_check_webcron';
    $result->name    = get_string('check_webcron_name', 'report_security');
    $result->details = null;
    $result->link    = "<a href=\"$CFG->wwwroot/$CFG->admin/settings.php?section=sitepolicies\">"
            .get_string('sitepolicies', 'admin').'</a>';

    if (empty($croncli) && empty($cronremotepassword)) {
        $result->status = REPORT_SECURITY_WARNING;
        $result->info   = get_string('check_webcron_warning', 'report_security');
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_webcron_ok', 'report_security');
    }

    if ($detailed) {
        $result->details = get_string('check_webcron_details', 'report_security');
    }

    return $result;
}

/**
 * Verifies the status of preventexecpath
 *
 * @param bool $detailed
 * @return object result
 */
function report_security_check_preventexecpath($detailed = false) {
    global $CFG;

    $result = new stdClass();
    $result->issue   = 'report_security_check_preventexecpath';
    $result->name    = get_string('check_preventexecpath_name', 'report_security');
    $result->details = null;
    $result->link    = null;

    if (empty($CFG->preventexecpath)) {
        $result->status = REPORT_SECURITY_WARNING;
        $result->info   = get_string('check_preventexecpath_warning', 'report_security');
        if ($detailed) {
            $result->details = get_string('check_preventexecpath_details', 'report_security');
        }
    } else {
        $result->status = REPORT_SECURITY_OK;
        $result->info   = get_string('check_preventexecpath_ok', 'report_security');
    }

    return $result;
}

/**
 * Check the presence of the vendor directory.
 *
 * @param bool $detailed Return detailed info.
 * @return object Result data.
 */
function report_security_check_vendordir($detailed = false) {
    global $CFG;

    $result = (object)[
        'issue' => 'report_security_check_vendordir',
        'name' => get_string('check_vendordir_name', 'report_security'),
        'info' => get_string('check_vendordir_info', 'report_security'),
        'details' => null,
        'status' => null,
        'link' => null,
    ];

    if (is_dir($CFG->dirroot.'/vendor')) {
        $result->status = REPORT_SECURITY_WARNING;
    } else {
        $result->status = REPORT_SECURITY_OK;
    }

    if ($detailed) {
        $result->details = get_string('check_vendordir_details', 'report_security', ['path' => $CFG->dirroot.'/vendor']);
    }

    return $result;
}

/**
 * Check the presence of the node_modules directory.
 *
 * @param bool $detailed Return detailed info.
 * @return object Result data.
 */
function report_security_check_nodemodules($detailed = false) {
    global $CFG;

    $result = (object)[
        'issue' => 'report_security_check_nodemodules',
        'name' => get_string('check_nodemodules_name', 'report_security'),
        'info' => get_string('check_nodemodules_info', 'report_security'),
        'details' => null,
        'status' => null,
        'link' => null,
    ];

    if (is_dir($CFG->dirroot.'/node_modules')) {
        $result->status = REPORT_SECURITY_WARNING;
    } else {
        $result->status = REPORT_SECURITY_OK;
    }

    if ($detailed) {
        $result->details = get_string('check_nodemodules_details', 'report_security', ['path' => $CFG->dirroot.'/node_modules']);
    }

    return $result;
}
