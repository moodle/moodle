<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
$RWSEDBG = false;
$RWSDBGL = "respondusws_err.log";
$RWSIHLOG = false;
$RWSRWROOT = false;
$RWSECAS  = false;
$RWSESL3  = false;
$RWSECMUL = false;
$RWSELDB = false;
$RWSSRURL = "";
$RWSCRURL = "";
$RWSPFNAME = "partiallycorrectfeedbackformat";
$RWSUID = null;
define("NO_DEBUG_DISPLAY", true);
$r_rmf = dirname(dirname(dirname(__FILE__))) . "/config.php";
if (is_readable($r_rmf)) {
    require_once($r_rmf);
} else {
    RWSSErr("2002");
}
defined("MOODLE_INTERNAL") || die();
$r_rsf = true;
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->dirroot/version.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/moodlelib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/datalib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/filelib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/completionlib.php");
}
if ($r_rsf && $CFG->version >= 2015051100) {
} else if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/conditionlib.php");
}
if ($r_rsf && $CFG->version >= 2018120300) {
} else if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/eventslib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/weblib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/accesslib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/dmllib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/ddllib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/questionlib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/grouplib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->libdir/gradelib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->dirroot/mod/quiz/lib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->dirroot/course/lib.php");
}
if ($r_rsf && $CFG->version >= 2014111000) {
    $r_rsf = is_readable("$CFG->dirroot/mod/quiz/locallib.php");
} else if ($r_rsf) {
    $r_rsf = is_readable("$CFG->dirroot/mod/quiz/editlib.php");
}
if ($r_rsf) {
    $r_rsf = is_readable("$CFG->dirroot/question/editlib.php");
}
if ($r_rsf && $RWSECAS) {
    $r_rsf = is_readable("$CFG->dirroot/auth/cas/CAS/CAS.php");
}
if (!$r_rsf) {
    RWSSErr("2003");
}
require_once("$CFG->dirroot/version.php");
require_once("$CFG->libdir/moodlelib.php");
require_once("$CFG->libdir/datalib.php");
require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/completionlib.php");
if ($CFG->version >= 2015051100) {
} else {
    require_once("$CFG->libdir/conditionlib.php");
}
if ($r_rsf && $CFG->version >= 2018120300) {
} else if ($r_rsf) {
    require_once("$CFG->libdir/eventslib.php");
}
require_once("$CFG->libdir/weblib.php");
require_once("$CFG->libdir/accesslib.php");
require_once("$CFG->libdir/dmllib.php");
require_once("$CFG->libdir/ddllib.php");
require_once("$CFG->libdir/questionlib.php");
require_once("$CFG->libdir/grouplib.php");
require_once("$CFG->libdir/gradelib.php");
require_once("$CFG->dirroot/mod/quiz/lib.php");
require_once("$CFG->dirroot/course/lib.php");
if ($CFG->version >= 2014111000) {
    require_once("$CFG->dirroot/mod/quiz/locallib.php");
} else {
    require_once("$CFG->dirroot/mod/quiz/editlib.php");
}
require_once("$CFG->dirroot/question/editlib.php");
if ($RWSECAS) {
    require_once("$CFG->dirroot/auth/cas/CAS/CAS.php");
}
$r_rlb = dirname(__FILE__) . "/lib.php";
if (is_readable($r_rlb)) {
    require_once($r_rlb);
} else {
    RWSSErr("2000");
}
$RWSLB                   = new stdClass();
$RWSLB->atts         = 0;
$RWSLB->revs          = 0;
$RWSLB->pw         = "";
$RWSLB->mex    = false;
$RWSLB->mok        = false;
$RWSLB->bex     = false;
$RWSLB->bok         = false;
$RWSLB->gerr = false;
$RWSLB->perr = false;
if ($RWSELDB) {
    $RWSLB->mex =
        is_readable("$CFG->dirroot/mod/lockdown/locklib.php");
    $RWSLB->bex  =
        is_readable("$CFG->dirroot/blocks/lockdownbrowser/locklib.php");
    if ($RWSLB->mex) {
        include_once("$CFG->dirroot/mod/lockdown/locklib.php");
        $RWSLB->mok = lockdown_module_status();
    } else if ($RWSLB->bex) {
        include_once("$CFG->dirroot/blocks/lockdownbrowser/locklib.php");
        $RWSLB->bok = (!empty($CFG->customscripts)
            && is_readable("$CFG->customscripts/mod/quiz/attempt.php")
            && (($DB->get_manager()->table_exists("block_lockdownbrowser_tokens")
                    && $DB->count_records("block_lockdownbrowser_tokens") > 0)
                || ($DB->get_manager()->table_exists("block_lockdownbrowser_toke")
                    && $DB->count_records("block_lockdownbrowser_toke") > 0)));
    }
}
define("RWSQAD", 1);
define("RWSRRE", 1 * 0x1041);
define("RWSRSC", 2 * 0x1041);
define("RWSRFE", 4 * 0x1041);
define("RWSRAN", 8 * 0x1041);
define("RWSRSO", 16 * 0x1041);
define("RWSRGE", 32 * 0x1041);
define("RWSROV", 1 * 0x4440000);
define("RWSRIM", 0x3c003f);
define("RWSROP", 0x3c00fc0);
define("RWSRCL", 0x3c03f000);
define("RWSRDU", 0x10000);
define("RWSRIA", 0x01000);
define("RWSRLA", 0x00100);
define("RWSRAF", 0x00010);
define("RWSUIN", 0);
define("RWSUNO", 3);
define("RWSOPT", 0);
define("RWSGRD", 1);
define("RWSATT", "rwsatt");
define("RWSRSV", "rwsrsv");
define("RWSUNK", "rwsunk");
define("RWSSHA", "shortanswer");
define("RWSTRF", "truefalse");
define("RWSMAN", "multianswer");
define("RWSNUM", "numerical");
define("RWSMCH", "multichoice");
define("RWSCAL", "calculated");
define("RWSMAT", "match");
define("RWSDES", "description");
define("RWSESS", "essay");
define("RWSRND", "random");
define("RWSRSM", "randomsamatch");
define("RWSCSI", "calculatedsimple");
define("RWSCMU", "calculatedmulti");
define("RWSCAS", "cas");
define("RWSAUM", 60);
define("RWSRXP", "regexp");
define("RWSMXC", 1200);
define("RWSPRF", "rawfile");
function respondusws_utf8encode($r_inp, $r_encoding = "") {
    if (strlen($r_inp) == 0) {
        return $r_inp;
    } else if ($r_encoding == 'UTF-8') {
        return $r_inp;
    } else if (RWSIVUtf8($r_inp)) {
        return $r_inp;
    } else if (strlen($r_encoding) == 0) {
        if (function_exists('mb_detect_encoding')) {
            $r_detected = mb_detect_encoding($r_inp, mb_detect_order(), true);
            if ($r_detected === false) {
                if (function_exists('mb_check_encoding')) {
                    if (mb_check_encoding($r_inp, 'ISO-8859-1')) {
                        return utf8_encode($r_inp);
                    } else {
                        return $r_inp;
                    }
                } else {
                    return utf8_encode($r_inp);
                }
            } else {
                return mb_convert_encoding($r_inp, 'UTF-8', $r_detected);
            }
        } else {
            return utf8_encode($r_inp);
        }
    } else if ($r_encoding == 'ISO-8859-1') {
        return utf8_encode($r_inp);
    } else if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($r_inp, 'UTF-8', $r_encoding);
    } else {
        return $r_inp;
    }
}
function RWSIVUtf8($r_str) {
    $r_l = strlen($r_str);
     if($r_l == 0) {
        return true;
    } else if (function_exists('mb_check_encoding')) {
        return  mb_check_encoding($r_str, 'UTF-8');
    } else {
    }
    $r_i = 0;
    while ($r_i < $r_l) {
        $r_c0 = ord($r_str[$r_i]);
        if ($r_i + 1 < $r_l) {
            $r_c1 = ord($r_str[$r_i + 1]);
        }
        if ($r_i + 2 < $r_l) {
            $r_c2 = ord($r_str[$r_i + 2]);
        }
        if ($r_i + 3 < $r_l) {
            $r_c3 = ord($r_str[$r_i + 3]);
        }
        if ($r_c0 >= 0x00 && $r_c0 <= 0x7e) {
            $r_i++;
        } else if ($r_i + 1 < $r_l
            && $r_c0 >= 0xc2 && $r_c0 <= 0xdf
            && $r_c1 >= 0x80 && $r_c1 <= 0xbf
        ) {
            $r_i += 2;
        } else if ($r_i + 2 < $r_l
            && $r_c0 == 0xe0
            && $r_c1 >= 0xa0 && $r_c1 <= 0xbf
            && $r_c2 >= 0x80 && $r_c2 <= 0xbf
        ) {
            $r_i += 3;
        } else if ($r_i + 2 < $r_l
            && (($r_c0 >= 0xe1 && $r_c0 <= 0xec) || $r_c0 == 0xee || $r_c0 == 0xef)
            && $r_c1 >= 0x80 && $r_c1 <= 0xbf
            && $r_c2 >= 0x80 && $r_c2 <= 0xbf
        ) {
            $r_i += 3;
        } else if ($r_i + 2 < $r_l
            && $r_c0 == 0xed
            && $r_c1 >= 0x80 && $r_c1 <= 0x9f
            && $r_c2 >= 0x80 && $r_c2 <= 0xbf
        ) {
            $r_i += 3;
        } else if ($r_i + 3 < $r_l
            && $r_c0 == 0xf0
            && $r_c1 >= 0x90 && $r_c1 <= 0xbf
            && $r_c2 >= 0x80 && $r_c2 <= 0xbf
            && $r_c3 >= 0x80 && $r_c3 <= 0xbf
        ) {
            $r_i += 4;
        } else if ($r_i + 3 < $r_l
            && $r_c0 >= 0xf1 && $r_c0 <= 0xf3
            && $r_c1 >= 0x80 && $r_c1 <= 0xbf
            && $r_c2 >= 0x80 && $r_c2 <= 0xbf
            && $r_c3 >= 0x80 && $r_c3 <= 0xbf
        ) {
            $r_i += 4;
        } else if ($r_i + 3 < $r_l
            && $r_c0 == 0xf4
            && $r_c1 >= 0x80 && $r_c1 <= 0x8f
            && $r_c2 >= 0x80 && $r_c2 <= 0xbf
            && $r_c3 >= 0x80 && $r_c3 <= 0xbf
        ) {
            $r_i += 4;
        } else {
            return false;
        }
    }
    return true;
}
function RWSRHCom() {
    header("Cache-Control: private, must-revalidate");
    header("Expires: -1");
    header("Pragma: no-cache");
}
function RWSRHXml() {
    RWSRHCom();
    header("Content-Type: text/xml");
}
function RWSRHHtml() {
    RWSRHCom();
    header("Content-Type: text/html");
}
function RWSRHBin($r_fn, $r_clen) {
    RWSRHCom();
    header("Content-Type: application/octet-stream");
    header("Content-Length: " . $r_clen);
    header(
        "Content-Disposition: attachment; filename=\""
        . htmlspecialchars(trim($r_fn)) . "\""
    );
    header("Content-Transfer-Encoding: binary");
}
function RWSSWarn($r_wm = "") {
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    echo "<service_warning>";
    if (!empty($r_wm)) {
        RWSELog("warning=$r_wm");
        echo respondusws_utf8encode(htmlspecialchars($r_wm));
    } else {
        RWSELog("warning=3004");
        echo "3004";
    }
    echo "</service_warning>\r\n";
    exit;
}
function RWSSStat($r_sm = "") {
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    echo "<service_status>";
    if (!empty($r_sm)) {
        RWSELog("status=$r_sm");
        echo respondusws_utf8encode(htmlspecialchars($r_sm));
    } else {
        RWSELog("status=1007");
        echo "1007";
    }
    echo "</service_status>\r\n";
    exit;
}
function RWSSErr($r_errm = "") {
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    echo "<service_error>";
    if (!empty($r_errm)) {
        RWSELog("error=$r_errm");
        echo respondusws_utf8encode(htmlspecialchars($r_errm));
    } else {
        RWSELog("error=2004");
        echo "2004";
    }
    echo "</service_error>\r\n";
    exit;
}
function RWSBErr($r_errm = "") {
    RWSRHHtml();
    if (empty($r_errm)) {
        $r_errm = "No message provided.";
    }
    RWSELog("error=$r_errm");
    echo "<html>\r\n";
    echo "<head><title>Service Error</title></head>\r\n";
    echo "<body>\r\n";
    echo "<b>Service Error:</b><br>\r\n";
    echo htmlspecialchars($r_errm) . "\r\n";
    echo "</body>\r\n";
    echo "</html>\r\n";
    exit;
}
function RWSLOMUser() {
    global $USER;
    global $CFG;
    global $DB;
    global $RWSECAS;
    if (!$RWSECAS) {
        require_logout();
        RWSSStat("1001");
    }
    if (respondusws_floatcompare($CFG->version, 2010122500, 2) >= 0) {
        if (isloggedin()) {
            $r_aus = get_enabled_auth_plugins();
            foreach ($r_aus as $r_aun) {
                $r_aup = get_auth_plugin($r_aun);
                if (strcasecmp($r_aup->authtype, RWSCAS) == 0) {
                    $r_csp = $r_aup;
                    RWSPLOCas($r_csp);
                } else {
                    $r_aup->prelogout_hook();
                }
            }
        }
        if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
            $r_ssi = session_id();
            $r_evt = \core\event\user_loggedout::create(
                array(
                    'userid' => $USER->id,
                    'objectid' => $USER->id,
                    'other' => array('sessionid' => $r_ssi)
                )
            );
            if ($r_ses = $DB->get_record('sessions', array('sid'=>$r_ssi))) {
                $r_evt->add_record_snapshot('sessions', $r_ses);
            }
            \core\session\manager::terminate_current();
            $r_evt->trigger();
        } else {
            $r_prms = $USER;
            events_trigger('user_logout', $r_prms);
            if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
                \core\session\manager::terminate_current();
            } else {
                session_get_instance()->terminate_current();
            }
            unset($r_prms);
        }
    } else {
        RWSSErr("2006,$CFG->version,2010122500");
    }
    RWSSStat("1001");
}
function RWSCMBVer() {
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        return;
    }
    $r_bv = intval($r_rv);
    if ($r_bv == 2009093000
        || $r_bv == 2010042801
        || $r_bv == 2010063001
        || $r_bv == 2010063002
        || $r_bv == 2010063003
        || $r_bv == 2010063004
        || $r_bv == 2010063005
        || $r_bv == 2011020100
        || $r_bv == 2011040400
        || $r_bv == 2011071500
        || $r_bv == 2011080100
        || $r_bv == 2011102500
        || $r_bv == 2011121500
        || $r_bv == 2012081300
        || $r_bv == 2013030700
        || $r_bv == 2013031900
        || $r_bv == 2013042500
        || $r_bv == 2013053000
        || $r_bv == 2013061700
        || $r_bv == 2013073000
        || $r_bv == 2013081900
        || $r_bv == 2013120900
        || $r_bv == 2014072400
        || $r_bv == 2014091800
        || $r_bv == 2014091801
        || $r_bv == 2014102100
        || $r_bv == 2014102101
        || $r_bv == 2014102102
        || $r_bv == 2014112500
        || $r_bv == 2014112501
        || $r_bv == 2015010700
        || $r_bv == 2015061600
        || $r_bv == 2015122100
        || $r_bv == 2016051300
        || $r_bv == 2017042800
        || $r_bv == 2018062700
        || $r_bv == 2019011400
        || $r_bv == 2020021400
        || $r_bv == 2022050900
        || $r_bv == 2023060800
        || $r_bv == 2023091500
        || $r_bv == 2023092500
        || $r_bv == 2023121500
    ) {
        return;
    }
    RWSSErr("2106");
}
function RWSCMVer() {
    global $CFG;
    $r_req     = "";
    $r_vf = RWSGMPath() . "/version.php";
    if (is_readable($r_vf)) {
        include($r_vf);
    }
    if (isset($respondusws_info)) {
        if (!empty($respondusws_info->requires)) {
            $r_req = $respondusws_info->requires;
        }
    }
    if (empty($r_req)) {
        RWSSErr("2005");
    }
    $r_res = respondusws_floatcompare($CFG->version, $r_req, 2);
    if ($r_res == -1) {
        RWSSErr("2006,$CFG->version,$r_req");
    }
}
function RWSCMInst() {
    global $DB;
    $r_dbm = $DB->get_manager();
    if ($r_dbm->table_exists("respondusws")) {
        $r_ins = $DB->get_records("respondusws", array("course" => SITEID));
    } else {
        $r_ins = array();
    }
    $r_ok = (count($r_ins) == 1);
    if (!$r_ok) {
        RWSSErr("2007");
    }
}
function RWSATLog($r_cid, $r_ac, $r_inf = "") {
    add_to_log($r_cid, "respondusws", $r_ac,
        "index.php?id=$r_cid", $r_inf);
}
function RWSGMPath() {
    $r_mp = dirname(__FILE__);
    if (DIRECTORY_SEPARATOR != '/') {
        $r_mp = str_replace('\\', '/', $r_mp);
    }
    return $r_mp;
}
function RWSGTPath() {
    global $CFG;
    if (respondusws_floatcompare($CFG->version, 2011120500.00, 2) >= 0) {
        if (isset($CFG->tempdir)) {
            $r_tp = "$CFG->tempdir";
        } else {
            $r_tp = "$CFG->dataroot/temp";
        }
    } else {
        $r_tp = "$CFG->dataroot/temp";
    }
    return $r_tp;
}
function RWSGSUrl($r_fhts, $r_inq) {
    global $CFG;
    $r_hs = ($r_fhts || !empty($CFG->sslproxy));
    if (!$r_hs) {
        $r_hs = (isset($_SERVER['HTTPS'])
            && !empty($_SERVER['HTTPS'])
            && strcasecmp($_SERVER['HTTPS'], "off") != 0);
    }
    if ($r_hs) {
        $r_su = 'https://';
    } else {
        $r_su = 'http://';
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
        $r_su .= $_SERVER['HTTP_X_FORWARDED_SERVER'];
    } else if (!empty($_SERVER['HTTP_X_HOST'])) {
        $r_su .= $_SERVER['HTTP_X_HOST'];
    } else if (!empty($_SERVER['SERVER_NAME'])) {
        $r_su .= $_SERVER['SERVER_NAME'];
    } else {
        $r_su .= $_SERVER['HTTP_HOST'];
    }
    if (strpos($r_su, ":", 6) === false) {
        if (($r_hs && $_SERVER['SERVER_PORT'] != 443)
            || (!$r_hs && $_SERVER['SERVER_PORT'] != 80)
        ) {
            $r_su .= ':';
            $r_su .= $_SERVER['SERVER_PORT'];
        }
    }
    if (!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
        if (isset($_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?';
            $_SERVER['REQUEST_URI'] .= $_SERVER['QUERY_STRING'];
        }
    }
    $r_bu = explode("?", $_SERVER['REQUEST_URI'], 2);
    $r_su .= $r_bu[0];
    if ($r_inq) {
        $r_qry = "";
        if ($_GET) {
            $r_pms = array();
            foreach ($_GET as $r_k => $r_val) {
                $r_pms[] = urlencode($r_k) . "=" . urlencode($r_val);
            }
            $r_qry = join("&", $r_pms);
        }
        if (strlen($r_qry) > 0) {
            $r_su .= "?" . $r_qry;
        }
    }
    return $r_su;
}
function RWSLIMUser($r_usrn, $r_pw, $r_csf) {
    global $RWSECAS;
    if ($RWSECAS) {
        RWSPLICas($r_usrn, $r_pw, $r_csf);
    }
    $r_usr = authenticate_user_login($r_usrn, $r_pw);
    if ($r_usr) {
        complete_user_login($r_usr);
    }
    if (isloggedin()) {
        RWSSStat("1000");
    } else {
        if ($RWSECAS) {
            if (isset($_SESSION['rwscas']['cookiejar'])) {
                $r_ckf = $_SESSION['rwscas']['cookiejar'];
                if (file_exists($r_ckf)) {
                    unlink($r_ckf);
                }
                unset($_SESSION['rwscas']['cookiejar']);
            }
            unset($_SESSION['rwscas']);
        }
        RWSSErr("2008");
    }
}
function RWSPLICas($r_usrn, $r_pw, $r_csf) {
    global $RWSESL3;
    global $RWSSRURL;
    global $RWSCRURL;
    global $RWSECMUL;
    if ($r_csf) {
        return;
    }
    $r_aus = get_enabled_auth_plugins();
    foreach ($r_aus as $r_aun) {
        $r_aup = get_auth_plugin($r_aun);
        if (strcasecmp($r_aup->authtype, RWSCAS) == 0) {
            $r_csp = $r_aup;
            break;
        }
    }
    if (!isset($r_csp)) {
        return;
    }
    if (empty($r_csp->config->hostname)) {
        return;
    }
    if ($r_csp->config->multiauth) {
        $r_auc = RWSGSOpt("authCAS", PARAM_ALPHANUMEXT);
        if ($r_auc === false || strlen($r_auc) == 0) {
            $r_auc = "CAS";
        }
        if (strcasecmp($r_auc, "CAS") != 0) {
            return;
        }
    }
    list($r_v1, $r_v2, $r_v3) = explode(".", phpCAS::getVersion());
    $r_csp->connectCAS();
    if (phpCAS::isSessionAuthenticated()) {
        return;
    }
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        unset($r_bv);
    } else {
        $r_bv = intval($r_rv);
    }
    if (strlen($RWSCRURL) > 0) {
        $r_svu = $RWSCRURL;
    } else {
        $r_svu = RWSGSUrl(false, false);
    }
    $r_svu .= "?rwscas=1";
    if (isset($r_bv)) {
        $r_svu .= "&version=";
        $r_svu .= urlencode($r_bv);
    }
    if ($RWSECMUL || $r_csp->config->multiauth) {
        if (isset($r_usrn)) {
            $r_svu .= "&rwsuser=";
            $r_svu .= urlencode($r_usrn);
        }
        if (isset($r_pw)) {
            $r_svu .= "&rwspass=";
            $r_svu .= urlencode($r_pw);
        }
    }
    phpCAS::setFixedServiceURL($r_svu);
    if ($r_csp->config->proxycas) {
        if (strlen($RWSCRURL) > 0) {
            $r_cbu = $RWSCRURL;
        } else {
            $r_cbu = RWSGSUrl(true, false);
        }
        $r_cbu .= "?rwscas=2";
        if (isset($r_bv)) {
            $r_cbu .= "&version=";
            $r_cbu .= urlencode($r_bv);
        }
        if ($RWSECMUL || $r_csp->config->multiauth) {
            if (isset($r_usrn)) {
                $r_cbu .= "&rwsuser=";
                $r_cbu .= urlencode($r_usrn);
            }
            if (isset($r_pw)) {
                $r_cbu .= "&rwspass=";
                $r_cbu .= urlencode($r_pw);
            }
        }
            phpCAS::setFixedCallbackURL($r_cbu);
    }
    $r_tpp = RWSGTPath();
    if ($r_tpp !== false) {
        $r_ckf = tempnam($r_tpp, "rws");
        if ($r_ckf !== false) {
            $_SESSION['rwscas']['cookiejar'] = $r_ckf;
        }
    }
    $r_liu = phpCAS::getServerLoginURL();
    $r_ch = curl_init();
    curl_setopt($r_ch, CURLOPT_URL, $r_liu);
    curl_setopt($r_ch, CURLOPT_HTTPGET, true);
    curl_setopt($r_ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($r_ch, CURLOPT_HEADER, true);
    curl_setopt($r_ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($r_ch, CURLOPT_FAILONERROR, true);
    curl_setopt($r_ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($r_ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($r_ch, CURLOPT_SSL_VERIFYPEER, false);
    if ($RWSESL3) {
        curl_setopt($r_ch, CURLOPT_SSLVERSION, 3);
    }
    curl_setopt($r_ch, CURLOPT_USERAGENT, "PHP");
    if (isset($r_ckf)) {
        curl_setopt($r_ch, CURLOPT_COOKIEFILE, $r_ckf);
        curl_setopt($r_ch, CURLOPT_COOKIEJAR, $r_ckf);
    }
    $r_rsp = curl_exec($r_ch);
    if ($r_rsp === false) {
        curl_close($r_ch);
        return;
    }
    $r_p = 0;
    while (stripos($r_rsp, "HTTP/", $r_p) === 0) {
        $r_p = stripos($r_rsp, "\r\n\r\n", $r_p);
        if ($r_p === false) {
            break;
        }
        $r_p += 4;
    }
    if ($r_p === 0) {
        $r_hdrs     = "";
        $r_hset = "";
        $r_bdy        = $r_rsp;
    } else if ($r_p === false) {
        $r_hdrs     = $r_rsp;
        $r_hset = explode("\r\n\r\n", $r_hdrs);
        $r_bdy        = "";
    } else {
        $r_hdrs     = substr($r_rsp, 0, $r_p - 4);
        $r_hset = explode("\r\n\r\n", $r_hdrs);
        $r_bdy        = substr($r_rsp, $r_p);
    }
    $r_ac    = "";
    $r_lt        = "";
    $r_evt_id  = "";
    $r_sub    = "";
    $r_wrn      = "";
    $r_exc = "";
    $r_rst     = "";
    $r_p       = 0;
    $r_l       = strlen($r_bdy);
    $r_st = stripos($r_bdy, "<form ");
    if ($r_st !== false) {
        $r_end = stripos($r_bdy, ">", $r_st);
        if ($r_end === false) {
            $r_end = $r_l;
        }
        $r_p = stripos($r_bdy, "action=\"", $r_st);
        if ($r_p === false || $r_p > $r_end) {
            $r_p = stripos($r_bdy, "action = \"", $r_st);
        }
        if ($r_p === false || $r_p > $r_end) {
            $r_p = stripos($r_bdy, "action=\'", $r_st);
        }
        if ($r_p === false || $r_p > $r_end) {
            $r_p = stripos($r_bdy, "action = \'", $r_st);
        }
        if ($r_p !== false && $r_p < $r_end) {
            while ($r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                $r_p++;
            }
            $r_p++;
            $r_st = $r_p;
            while ($r_p < $r_end && $r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                $r_p++;
            }
            $r_end    = $r_p;
            $r_ac = substr($r_bdy, $r_st, $r_end - $r_st);
        }
    }
    while (strlen($r_lt) == 0
        || strlen($r_evt_id) == 0
        || strlen($r_sub) == 0
        || strlen($r_wrn) == 0
        || strlen($r_exc) == 0
        || strlen($r_rst) == 0
    ) {
        $r_nx = stripos($r_bdy, "<input ", $r_p);
        if ($r_nx === false) {
            break;
        }
        $r_st = $r_nx;
        $r_end   = stripos($r_bdy, ">", $r_st);
        if ($r_end === false) {
            $r_end = $r_l;
        }
        if (strlen($r_lt) == 0) {
            $r_st = stripos($r_bdy, "name=\"lt\"", $r_nx);
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \"lt\"", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name=\'lt\'", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \'lt\'", $r_nx);
            }
            if ($r_st !== false && $r_st < $r_end) {
                $r_p = stripos($r_bdy, "value=\"", $r_st);
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \"", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value=\'", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \'", $r_st);
                }
                if ($r_p !== false && $r_p < $r_end) {
                    while ($r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_p++;
                    $r_st = $r_p;
                    while ($r_p < $r_end && $r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_end = $r_p;
                    $r_lt  = substr($r_bdy, $r_st, $r_end - $r_st);
                }
            }
        }
        if (strlen($r_evt_id) == 0) {
            $r_st = stripos($r_bdy, "name=\"_eventId\"", $r_nx);
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \"_eventId\"", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name=\'_eventId\'", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \'_eventId\'", $r_nx);
            }
            if ($r_st !== false && $r_st < $r_end) {
                $r_p = stripos($r_bdy, "value=\"", $r_st);
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \"", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value=\'", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \'", $r_st);
                }
                if ($r_p !== false && $r_p < $r_end) {
                    while ($r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_p++;
                    $r_st = $r_p;
                    while ($r_p < $r_end && $r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_end      = $r_p;
                    $r_evt_id = substr($r_bdy, $r_st, $r_end - $r_st);
                }
            }
        }
        if (strlen($r_sub) == 0) {
            $r_st = stripos($r_bdy, "name=\"submit\"", $r_nx);
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \"submit\"", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name=\'submit\'", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \'submit\'", $r_nx);
            }
            if ($r_st !== false && $r_st < $r_end) {
                $r_p = stripos($r_bdy, "value=\"", $r_st);
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \"", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value=\'", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \'", $r_st);
                }
                if ($r_p !== false && $r_p < $r_end) {
                    while ($r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_p++;
                    $r_st = $r_p;
                    while ($r_p < $r_end && $r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_end    = $r_p;
                    $r_sub = substr($r_bdy, $r_st, $r_end - $r_st);
                }
            }
        }
        if (strlen($r_wrn) == 0) {
            $r_st = stripos($r_bdy, "name=\"warn\"", $r_nx);
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \"warn\"", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name=\'warn\'", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \'warn\'", $r_nx);
            }
            if ($r_st !== false && $r_st < $r_end) {
                $r_p = stripos($r_bdy, "value=\"", $r_st);
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \"", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value=\'", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \'", $r_st);
                }
                if ($r_p !== false && $r_p < $r_end) {
                    while ($r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_p++;
                    $r_st = $r_p;
                    while ($r_p < $r_end && $r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_end  = $r_p;
                    $r_wrn = substr($r_bdy, $r_st, $r_end - $r_st);
                }
            }
        }
        if (strlen($r_exc) == 0) {
            $r_st = stripos($r_bdy, "name=\"execution\"", $r_nx);
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \"execution\"", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name=\'execution\'", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \'execution\'", $r_nx);
            }
            if ($r_st !== false && $r_st < $r_end) {
                $r_p = stripos($r_bdy, "value=\"", $r_st);
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \"", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value=\'", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \'", $r_st);
                }
                if ($r_p !== false && $r_p < $r_end) {
                    while ($r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_p++;
                    $r_st = $r_p;
                    while ($r_p < $r_end && $r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_end       = $r_p;
                    $r_exc = substr($r_bdy, $r_st, $r_end - $r_st);
                }
            }
        }
        if (strlen($r_rst) == 0) {
            $r_st = stripos($r_bdy, "name=\"reset\"", $r_nx);
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \"reset\"", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name=\'reset\'", $r_nx);
            }
            if ($r_st === false || $r_st > $r_end) {
                $r_st = stripos($r_bdy, "name = \'reset\'", $r_nx);
            }
            if ($r_st !== false && $r_st < $r_end) {
                $r_p = stripos($r_bdy, "value=\"", $r_st);
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \"", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value=\'", $r_st);
                }
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = stripos($r_bdy, "value = \'", $r_st);
                }
                if ($r_p !== false && $r_p < $r_end) {
                    while ($r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_p++;
                    $r_st = $r_p;
                    while ($r_p < $r_end && $r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
                        $r_p++;
                    }
                    $r_end   = $r_p;
                    $r_rst = substr($r_bdy, $r_st, $r_end - $r_st);
                }
            }
        }
        $r_p = $r_nx + 1;
    }
    if (strlen($r_ac) == 0 || strlen($r_lt) == 0) {
        curl_close($r_ch);
        return;
    }
    if (strlen($r_evt_id) == 0) {
        unset($r_evt_id);
    }
    if (isset($r_evt_id) && strlen($r_sub) == 0) {
        $r_sub = "LOGIN";
    }
    if (strlen($r_wrn) == 0) {
        unset($r_wrn);
    }
    if (strlen($r_exc) == 0) {
        unset($r_exc);
    }
    if (strlen($r_rst) == 0) {
        unset($r_rst);
    }
    if (stripos($r_ac, "http://") !== 0
        && stripos($r_ac, "https://") !== 0
    ) {
        if ($r_ac[0] == "/") {
            $r_p = stripos($r_liu, "://");
            if ($r_p !== false) {
                $r_p += 3;
                $r_p = stripos($r_liu, "/", $r_p);
                if ($r_p !== false) {
                    $r_acu = substr($r_liu, 0, $r_p);
                    $r_acu .= $r_ac;
                }
            }
        } else {
            $r_p = stripos($r_liu, "/login?");
            if ($r_p !== false) {
                $r_acu = substr($r_liu, 0, $r_p);
                $r_acu .= "/$r_ac";
            }
        }
    } else {
        $r_acu = $r_ac;
    }
    if (!isset($r_acu)) {
        $r_acu = $r_liu;
    }
    $r_psf = "username=";
    $r_psf .= urlencode($r_usrn);
    $r_psf .= "&password=";
    $r_psf .= urlencode($r_pw);
    $r_psf .= "&lt=";
    $r_psf .= urlencode($r_lt);
    $r_psf .= "&service=";
    $r_psf .= urlencode($r_svu);
    if (isset($r_evt_id)) {
        $r_psf .= "&_eventId=";
        $r_psf .= urlencode($r_evt_id);
        $r_psf .= "&submit=";
        $r_psf .= urlencode($r_sub);
    }
    if (isset($r_wrn)) {
        $r_psf .= "&warn=";
        $r_psf .= urlencode($r_wrn);
    }
    if (isset($r_exc)) {
        $r_psf .= "&execution=";
        $r_psf .= urlencode($r_exc);
    }
    if (isset($r_rst)) {
        $r_psf .= "&reset=";
        $r_psf .= urlencode($r_rst);
    }
    curl_setopt($r_ch, CURLOPT_URL, $r_acu);
    curl_setopt($r_ch, CURLOPT_HTTPGET, false);
    curl_setopt($r_ch, CURLOPT_POST, true);
    curl_setopt($r_ch, CURLOPT_POSTFIELDS, $r_psf);
    $r_rsp = curl_exec($r_ch);
    if ($r_rsp === false) {
        curl_close($r_ch);
        return;
    }
    $r_p = 0;
    while (stripos($r_rsp, "HTTP/", $r_p) === 0) {
        $r_p = stripos($r_rsp, "\r\n\r\n", $r_p);
        if ($r_p === false) {
            break;
        }
        $r_p += 4;
    }
    if ($r_p === 0) {
        $r_hdrs     = "";
        $r_hset = "";
        $r_bdy        = $r_rsp;
    } else if ($r_p === false) {
        $r_hdrs     = $r_rsp;
        $r_hset = explode("\r\n\r\n", $r_hdrs);
        $r_bdy        = "";
    } else {
        $r_hdrs     = substr($r_rsp, 0, $r_p - 4);
        $r_hset = explode("\r\n\r\n", $r_hdrs);
        $r_bdy        = substr($r_rsp, $r_p);
    }
    foreach ($r_hset as $r_set) {
        $r_hdrl = explode("\r\n", $r_set);
        foreach ($r_hdrl as $r_hdr) {
            if (stripos($r_hdr, "Location:") !== false) {
                $r_st = stripos($r_hdr, "?ticket=");
                if ($r_st === false) {
                    $r_st = stripos($r_hdr, "&ticket=");
                }
                if ($r_st !== false) {
                    $r_end = stripos($r_hdr, "&", $r_st + 1);
                    if ($r_end === false) {
                        $r_end = strlen($r_hdr);
                    }
                    $r_pm = substr($r_hdr, $r_st + 8, $r_end - $r_st);
                    if ($r_pm !== false && strlen($r_pm) > 0) {
                        $r_tkt = trim(urldecode($r_pm));
                        break;
                    }
                }
            }
        }
        if (isset($r_tkt)) {
            break;
        }
    }
    $r_rurl = "";
    $r_p       = 0;
    $r_l       = strlen($r_bdy);
    while (strlen($r_rurl) == 0) {
        $r_nx = stripos($r_bdy, "window.location.href", $r_p);
        if ($r_nx === false) {
            $r_nx = stripos($r_bdy, "window.location.replace", $r_p);
        }
        if ($r_nx === false) {
            $r_nx = stripos($r_bdy, "window.location", $r_p);
        }
        if ($r_nx === false) {
            $r_nx = stripos($r_bdy, "window.navigate", $r_p);
        }
        if ($r_nx === false) {
            $r_nx = stripos($r_bdy, "document.location.href", $r_p);
        }
        if ($r_nx === false) {
            $r_nx = stripos($r_bdy, "document.location.URL", $r_p);
        }
        if ($r_nx === false) {
            $r_nx = stripos($r_bdy, "document.location", $r_p);
        }
        if ($r_nx === false) {
            break;
        }
        $r_p = $r_nx;
        while ($r_p < $r_l && $r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
            $r_p++;
        }
        if ($r_p < $r_l) {
            $r_p++;
        }
        $r_st = $r_p;
        while ($r_p < $r_end && $r_bdy[$r_p] != "\"" && $r_bdy[$r_p] != "\'") {
            $r_p++;
        }
        $r_end       = $r_p;
        $r_rurl = substr($r_bdy, $r_st, $r_end - $r_st);
        $r_st = stripos($r_rurl, "?ticket=");
        if ($r_st === false) {
            $r_st = stripos($r_rurl, "&ticket=");
        }
        if ($r_st !== false) {
            $r_end = stripos($r_rurl, "&", $r_st + 1);
            if ($r_end === false) {
                $r_end = strlen($r_rurl);
            }
            $r_pm = substr($r_rurl, $r_st + 8, $r_end - $r_st);
            if ($r_pm !== false && strlen($r_pm) > 0) {
                $r_tkt = trim(urldecode($r_pm));
            }
        }
        if (!isset($r_tkt)) {
            $r_rurl = "";
        }
        $r_p = $r_nx + 1;
    }
    if (strlen($r_rurl) != 0) {
        curl_setopt($r_ch, CURLOPT_URL, $r_rurl);
        curl_setopt($r_ch, CURLOPT_HTTPGET, true);
        curl_setopt($r_ch, CURLOPT_POST, false);
        curl_setopt($r_ch, CURLOPT_POSTFIELDS, "");
        $redir_res = curl_exec($r_ch);
        if ($redir_res !== false) {
            $r_rsp = $redir_res;
            $r_p      = 0;
            while (stripos($r_rsp, "HTTP/", $r_p) === 0) {
                $r_p = stripos($r_rsp, "\r\n\r\n", $r_p);
                if ($r_p === false) {
                    break;
                }
                $r_p += 4;
            }
            if ($r_p === 0) {
                $r_hdrs     = "";
                $r_hset = "";
                $r_bdy        = $r_rsp;
            } else if ($r_p === false) {
                $r_hdrs     = $r_rsp;
                $r_hset = explode("\r\n\r\n", $r_hdrs);
                $r_bdy        = "";
            } else {
                $r_hdrs     = substr($r_rsp, 0, $r_p - 4);
                $r_hset = explode("\r\n\r\n", $r_hdrs);
                $r_bdy        = substr($r_rsp, $r_p);
            }
        }
    }
    $r_asu = "";
    $r_psf = "";
    if (strlen($r_asu) != 0) {
        curl_setopt($r_ch, CURLOPT_URL, $r_asu);
        curl_setopt($r_ch, CURLOPT_HTTPGET, false);
        curl_setopt($r_ch, CURLOPT_POST, true);
        curl_setopt($r_ch, CURLOPT_POSTFIELDS, $r_psf);
        $r_ares = curl_exec($r_ch);
        if ($r_ares !== false) {
            $r_rsp = $r_ares;
            $r_p      = 0;
            while (stripos($r_rsp, "HTTP/", $r_p) === 0) {
                $r_p = stripos($r_rsp, "\r\n\r\n", $r_p);
                if ($r_p === false) {
                    break;
                }
                $r_p += 4;
            }
            if ($r_p === 0) {
                $r_hdrs     = "";
                $r_hset = "";
                $r_bdy        = $r_rsp;
            } else if ($r_p === false) {
                $r_hdrs     = $r_rsp;
                $r_hset = explode("\r\n\r\n", $r_hdrs);
                $r_bdy        = "";
            } else {
                $r_hdrs     = substr($r_rsp, 0, $r_p - 4);
                $r_hset = explode("\r\n\r\n", $r_hdrs);
                $r_bdy        = substr($r_rsp, $r_p);
            }
        }
    }
    if (!isset($r_tkt)) {
        $r_st = stripos($r_bdy, "<rwscas>");
        if ($r_st !== false) {
            $r_end = stripos($r_bdy, "</rwscas>", $r_st);
            if ($r_end === false) {
                $r_end = strlen($r_hdr);
            }
            $r_p = stripos($r_bdy, "<st>", $r_st);
            if ($r_p !== false && $r_p < $r_end) {
                $r_p += 4;
                $r_st = $r_p;
                $r_p   = stripos($r_bdy, "</st>", $r_st);
                if ($r_p === false || $r_p > $r_end) {
                    $r_p = $r_end;
                }
                $r_end   = $r_p;
                $r_pm = trim(substr($r_bdy, $r_st, $r_end));
                if (strlen($r_pm)) {
                    $r_tkt = $r_pm;
                }
            }
        }
    }
    curl_close($r_ch);
    if (!isset($r_tkt)) {
        return;
    }
    if (strlen($RWSSRURL) > 0) {
        $r_rurl = $RWSSRURL;
    } else {
        $r_rurl = RWSGSUrl(false, false);
    }
    $r_rurl .= "?rwscas=3";
    if (isset($r_bv)) {
        $r_rurl .= "&version=";
        $r_rurl .= urlencode($r_bv);
    }
    if ($RWSECMUL || $r_csp->config->multiauth) {
        if (isset($r_usrn)) {
            $r_rurl .= "&rwsuser=";
            $r_rurl .= urlencode($r_usrn);
        }
        if (isset($r_pw)) {
            $r_rurl .= "&rwspass=";
            $r_rurl .= urlencode($r_pw);
        }
    }
    if (isset($r_tkt)) {
        $r_rurl .= "&ticket=";
        $r_rurl .= urlencode($r_tkt);
    }
    header("Location: $r_rurl");
    exit;
}
function RWSPCReqs() {
    global $RWSESL3;
    global $RWSCRURL;
    $r_rwc = RWSGSOpt("rwscas", PARAM_ALPHANUM);
    if ($r_rwc === false || strlen($r_rwc) == 0) {
        return;
    }
    if ($r_rwc != "1" && $r_rwc != "2" && $r_rwc != "3") {
        return;
    }
    $r_ver = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_ver === false || strlen($r_ver) == 0) {
        return;
    }
    $r_rwu = RWSGSOpt("rwsuser", PARAM_RAW);
    if ($r_rwu === false || strlen($r_rwu) == 0) {
        unset($r_rwu);
    }
    $r_rwp = RWSGSOpt("rwspass", PARAM_RAW);
    if ($r_rwp === false || strlen($r_rwp) == 0) {
        unset($r_rwp);
    }
    $r_tkt = RWSGSOpt("ticket", PARAM_RAW);
    if ($r_tkt === false || strlen($r_tkt) == 0) {
        unset($r_tkt);
    }
    $r_pid = RWSGSOpt("pgtId", PARAM_RAW);
    if ($r_pid === false || strlen($r_pid) == 0) {
        unset($r_pid);
    }
    $r_piou = RWSGSOpt("pgtIou", PARAM_RAW);
    if ($r_piou === false || strlen($r_piou) == 0) {
        unset($r_piou);
    }
    $r_aus = get_enabled_auth_plugins();
    foreach ($r_aus as $r_aun) {
        $r_aup = get_auth_plugin($r_aun);
        if (strcasecmp($r_aup->authtype, RWSCAS) == 0) {
            $r_csp = $r_aup;
            break;
        }
    }
    if (!isset($r_csp)) {
        return;
    }
    if (empty($r_csp->config->hostname)) {
        return;
    }
    list($r_v1, $r_v2, $r_v3) = explode(".", phpCAS::getVersion());
    $r_csp->connectCAS();
    if ($r_rwc == "1") {
        if (isset($r_tkt)) {
            RWSRHXml();
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
            echo "<rwscas>\r\n";
            echo "\t<st>";
            echo respondusws_utf8encode(htmlspecialchars(trim($r_tkt)));
            echo "\t</st>\r\n";
            echo "</rwscas>\r\n";
            exit;
        } else if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $r_ok = phpCAS::checkAuthentication();
            if (!isset($r_rwu)) {
                $r_rwu = phpCAS::getUser();
            }
            if (!isset($r_rwp)) {
                $r_rwp = "passwdCas";
            }
            RWSLIMUser($r_rwu, $r_rwp, $r_ok);
        } else if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $r_psd = urldecode(file_get_contents("php://input"));
            if (stripos($r_psd, "<samlp:LogoutRequest ") !== false) {
                RWSAOLog();
            }
        }
    } else if ($r_rwc == "2") {
        if (isset($r_pid) && isset($r_piou)) {
            if ($r_csp->config->proxycas) {
                phpCAS::checkAuthentication();
            }
        } else if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $r_psd = urldecode(file_get_contents("php://input"));
            if (stripos($r_psd, "<samlp:LogoutRequest ") !== false) {
                RWSAOLog();
            }
        }
    } else if ($r_rwc == "3") {
        if (isset($r_tkt)) {
            if (strlen($RWSCRURL) > 0) {
                $r_svu = $RWSCRURL;
            } else {
                $r_svu = RWSGSUrl(false, false);
            }
            $r_svu .= "?rwscas=1";
            if (isset($r_ver)) {
                $r_svu .= "&version=";
                $r_svu .= urlencode($r_ver);
            }
            if (isset($r_rwu)) {
                $r_svu .= "&rwsuser=";
                $r_svu .= urlencode($r_rwu);
            }
            if (isset($r_rwp)) {
                $r_svu .= "&rwspass=";
                $r_svu .= urlencode($r_rwp);
            }
            phpCAS::setFixedServiceURL($r_svu);
            if ($r_csp->config->proxycas) {
                if (strlen($RWSCRURL) > 0) {
                    $r_cbu = $RWSCRURL;
                } else {
                    $r_cbu = RWSGSUrl(true, false);
                }
                $r_cbu .= "?rwscas=2";
                if (isset($r_ver)) {
                    $r_cbu .= "&version=";
                    $r_cbu .= urlencode($r_ver);
                }
                if (isset($r_rwu)) {
                    $r_cbu .= "&rwsuser=";
                    $r_cbu .= urlencode($r_rwu);
                }
                if (isset($r_rwp)) {
                    $r_cbu .= "&rwspass=";
                    $r_cbu .= urlencode($r_rwp);
                }
                    phpCAS::setFixedCallbackURL($r_cbu);
            }
            if (phpCAS::checkAuthentication()) {
                exit;
            }
            if (isset($r_rwu) && isset($r_rwp)) {
                RWSLIMUser($r_rwu, $r_rwp, true);
            }
        }
    }
    RWSSErr("2008");
}
function RWSCMMaint($r_bo = false) {
    global $CFG;
    global $RWSUID;
    if (is_siteadmin($RWSUID)) {
        return;
    }
    if (!empty($CFG->maintenance_enabled)
        || file_exists($CFG->dataroot . "/" . SITEID . "/maintenance.html")
    ) {
        if ($r_bo) {
            RWSBErr("The Moodle site is currently undergoing maintenance.");
        } else {
            RWSSErr("2009");
        }
    }
}
function RWSCMAuth($r_bo = false) {
    if (!isloggedin()) {
        if ($r_bo) {
            RWSBErr("Must be logged in to perform the requested action.");
        } else {
            RWSSErr("2010");
        }
    }
}
function RWSCRAuth() {
    global $CFG;
    global $DB;
    global $SESSION;
    global $USER;
    global $RWSUID;
    $RWSUID = $USER->id;
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    if ($r_bv < 2013061700) {
        return;
    }
    if (isset($SESSION->respondusws_module_auth)) {
        if ($SESSION->respondusws_module_auth != true) {
            return;
        }
    } else {
        $SESSION->respondusws_module_auth = false;
        $r_cfg_user = get_config("respondusws", "username");
        if ($r_cfg_user === false || strlen($r_cfg_user) == 0) {
            return;
        }
        if (strcmp($USER->username, $r_cfg_user) != 0) {
            return;
        }
        $SESSION->respondusws_module_auth = true;
    }
    $r_usrn = RWSGSOpt("username", PARAM_RAW);
    if ($r_usrn === false || strlen($r_usrn) == 0) {
        RWSSErr("2054");
    }
    $r_tok = RWSGSOpt("token", PARAM_ALPHANUMEXT);
    if ($r_tok === false || strlen($r_tok) == 0) {
        RWSSErr("2118");
    }
    $r_rws = $DB->get_record("respondusws", array("course" => SITEID));
    if ($r_rws === false) {
        RWSSErr("2007");
    }
    $r_usr = $DB->get_record("user", array("username" => $r_usrn,
      "mnethostid" => $CFG->mnet_localhost_id));
    if ($r_usr === false) {
        RWSSErr("2120");
    }
    $r_auu = $DB->get_record("respondusws_auth_users",
      array("responduswsid" => $r_rws->id, "userid" => $r_usr->id));
    if ($r_auu === false) {
        RWSSErr("2121");
    }
    $r_h = sha1($r_tok);
    if (strcmp($r_h, $r_auu->authtoken) != 0) {
        RWSSErr("2119");
    }
    $r_ctm = time();
    $r_mxt = $r_auu->timeissued
      + (60 * 60 * 24 * RWSAUM);
    if ($r_ctm < $r_auu->timeissued || $r_ctm > $r_mxt) {
        RWSSErr("2115");
    }
    $RWSUID = $r_usr->id;
}
function RWSCMUCourse($r_cid, $r_cqa = false) {
    global $DB;
    global $CFG;
    global $RWSUID;
    $r_rcd = $DB->get_record("course", array("id" => $r_cid));
    if ($r_rcd === false) {
        RWSSErr("2011");
    }
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context_course::instance($r_cid);
    } else {
        $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
    }
    if ($r_cqa) {
        if (respondusws_floatcompare($CFG->version, 2012062501.07, 2) >= 0) {
            if (!has_capability("mod/quiz:addinstance", $r_ctx, $RWSUID)) {
                RWSSErr("2012");
            }
        } else {
            if (!course_allowed_module($r_rcd, "quiz")) {
                RWSSErr("2012");
            }
        }
    }
    if (!RWSIUMCourse($r_cid)) {
        RWSSErr("2013");
    }
    return $r_rcd;
}
function RWSCMUQuiz($r_qzmi) {
    global $DB;
    $r_rcd = $DB->get_record("course_modules", array("id" => $r_qzmi));
    if ($r_rcd === false) {
        RWSSErr("2014");
    }
    if (!RWSIUMQuiz($r_qzmi)) {
        RWSSErr("2015");
    }
    return $r_rcd;
}
function RWSGUQCats($r_cid) {
    global $CFG;
    global $RWSUID;
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_ctxs = array();
    if ($r_bv >= 2010063001) {
        if (is_siteadmin($RWSUID)) {
            if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
                $r_sys = context_system::instance();
            } else {
                $r_sys = get_context_instance(CONTEXT_SYSTEM);
            }
            $r_ctxs[] = $r_sys->id;
        }
    }
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context_course::instance($r_cid);
    } else {
        $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
    }
    $r_ctxs[] = $r_ctx->id;
    $r_qzms = RWSGUVQList($r_cid);
    if (count($r_qzms) > 0) {
        foreach ($r_qzms as $r_qzm) {
            if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
                $r_ctx = context_module::instance($r_qzm->id);
            } else {
                $r_ctx = get_context_instance(CONTEXT_MODULE, $r_qzm->id);
            }
            if ($r_ctx !== false) {
                if (!in_array($r_ctx->id, $r_ctxs)) {
                    $r_ctxs[] = $r_ctx->id;
                }
            }
        }
    }
    $r_qcs = false;
    if (count($r_ctxs) == 0) {
        return array();
    } else if (count($r_ctxs) == 1) {
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
            $r_qcs = \qbank_managecategories\helper::get_categories_for_contexts($r_ctxs[0]);
        } else {
            $r_qcs = get_categories_for_contexts($r_ctxs[0]);
        }
    } else {
        $r_ctxl = implode(", ", $r_ctxs);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
            $r_qcs = \qbank_managecategories\helper::get_categories_for_contexts($r_ctxl);
        } else {
            $r_qcs = get_categories_for_contexts($r_ctxl);
        }
    }
    if ($r_qcs === false || count($r_qcs) == 0) {
        return array();
    }
    return $r_qcs;
}
function RWSGUVSList($r_cid) {
    global $CFG;
    global $RWSUID;
    $r_vs = array();
    if (respondusws_floatcompare($CFG->version, 2012120300, 2) >= 0) {
        $modinfo = get_fast_modinfo($r_cid);
        $r_secs = $modinfo->get_section_info_all();
    } else {
        $r_secs = get_all_sections($r_cid);
    }
    if ($r_secs === false || count($r_secs) == 0) {
        return $r_vs;
    }
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context_course::instance($r_cid);
    } else {
        $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
    }
    $r_vh = has_capability("moodle/course:viewhiddensections", $r_ctx, $RWSUID);
    if (!$r_vh) {
        $r_vh = is_siteadmin($RWSUID);
    }
    foreach ($r_secs as $r_s) {
        if ($r_s->visible || $r_vh) {
            $r_vs[] = $r_s;
        }
    }
    return $r_secs;
}
function RWSGUVQList($r_cid) {
    global $CFG;
    global $RWSUID;
    $r_vqms = array();
    $r_qzms = get_coursemodules_in_course("quiz", $r_cid);
    if ($r_qzms === false || count($r_qzms) == 0) {
        return $r_vqms;
    }
    foreach ($r_qzms as $r_qzm) {
        if ($CFG->version >= 2015051100) {
            $r_iv = \core_availability\info_module::is_user_visible($r_qzm, $RWSUID, false);
        } else {
            $r_iv = coursemodule_visible_for_user($r_qzm, $RWSUID);
        }
        if ($r_iv) {
            $r_vqms[] = $r_qzm;
        }
    }
    return $r_vqms;
}
function RWSGUMQList($r_qzms) {
    $r_mqms = array();
    if (!$r_qzms || count($r_qzms) == 0) {
        return $r_mqms;
    }
    foreach ($r_qzms as $r_qzm) {
        if (RWSIUMQuiz($r_qzm->id)) {
            $r_mqms[] = $r_qzm;
        }
    }
    return $r_mqms;
}
function RWSIUMQuiz($r_qzmi) {
    global $CFG;
    global $RWSUID;
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context_module::instance($r_qzmi);
    } else {
        $r_ctx = get_context_instance(CONTEXT_MODULE, $r_qzmi);
    }
    $r_ok = ($r_ctx !== false);
    if ($r_ok) {
        $r_ok = has_capability("mod/quiz:view", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("mod/quiz:preview", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("mod/quiz:manage", $r_ctx, $RWSUID);
    }
    if (!$r_ok) {
        $r_ok = is_siteadmin($RWSUID);
    }
    return $r_ok;
}
function RWSGUMCList() {
    global $RWSUID;
    $r_mc = array();
    $r_crss = enrol_get_users_courses($RWSUID);
    if ($r_crss === false || count($r_crss) == 0) {
        return $r_mc;
    }
    $r_crss = array_slice($r_crss, 0, RWSMXC);
    foreach ($r_crss as $r_c) {
        if ($r_c->id != SITEID && RWSIUMCourse($r_c->id)) {
            $r_mc[] = $r_c;
        }
    }
    return $r_mc;
}
function RWSCMUSvc($r_bo = false) {
}
function RWSIUMCourse($r_cid) {
    global $CFG;
    global $RWSUID;
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context_course::instance($r_cid);
    } else {
        $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
    }
    $r_ok = ($r_ctx !== false);
    if ($r_ok) {
        $r_ok = has_capability("moodle/site:viewfullnames", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/course:activityvisibility", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/course:viewhiddencourses", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/course:viewhiddenactivities", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/course:viewhiddensections", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/course:update", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/course:manageactivities", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/course:managefiles", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:managecategory", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:add", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:editmine", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:editall", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:viewmine", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:viewall", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:usemine", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:useall", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:movemine", $r_ctx, $RWSUID);
    }
    if ($r_ok) {
        $r_ok = has_capability("moodle/question:moveall", $r_ctx, $RWSUID);
    }
    if (respondusws_floatcompare($CFG->version, 2012062501.07, 2) >= 0) {
        if ($r_ok) {
            $r_ok = has_capability("mod/quiz:addinstance", $r_ctx, $RWSUID);
        }
    }
    if (!$r_ok) {
        $r_ok = is_siteadmin($RWSUID);
    }
    return $r_ok;
}
function RWSGSOpt($r_nm, $r_typ) {
    global $RWSECAS;
    if ($r_typ == RWSPRF) {
        if (isloggedin()) {
            if (isset($_FILES[$r_nm])) {
                if ($_FILES[$r_nm]['error'] === UPLOAD_ERR_OK) {
                    $r_fl           = new stdClass();
                    $r_fl->filename = $_FILES[$r_nm]['name'];
                    $r_fl->filedata = file_get_contents($_FILES[$r_nm]['tmp_name']);
                    return $r_fl;
                }
            }
        }
        return false;
    }
    if (isset($_POST[$r_nm])) {
        if (strlen($_POST[$r_nm]) == 0) {
            return "";
        } else {
            return strval(optional_param($r_nm, false, $r_typ));
        }
    } else if (isset($_GET[$r_nm])) {
        if (isset($_GET["action"])
          && ($_GET["action"] == "authstart"
          || $_GET["action"] == "authfinish")
          ) {
            if (strlen($_GET[$r_nm]) == 0) {
                return "";
            } else {
                return strval(optional_param($r_nm, false, $r_typ));
            }
        }
    }
    if ($RWSECAS) {
        if (!isloggedin()) {
            if (isset($_GET[$r_nm])) {
                if (strlen($_GET[$r_nm]) == 0) {
                    return "";
                } else {
                    return strval(optional_param($r_nm, false, $r_typ));
                }
            }
        }
    }
    return false;
}
function RWSSQDLocal(&$r_qiz) {
    global $DB;
    global $CFG;
    if (!empty($r_qiz->coursemodule)) {
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context_module::instance($r_qiz->coursemodule);
        } else {
            $r_ctx = get_context_instance(CONTEXT_MODULE, $r_qiz->coursemodule);
        }
        $r_ctxi = $r_ctx->id;
    } else if (!empty($r_qiz->course)) {
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context_course::instance($r_qiz->course);
        } else {
            $r_ctx = get_context_instance(CONTEXT_COURSE, $r_qiz->course);
        }
        $r_ctxi = $r_ctx->id;
    } else {
        $r_ctxi = null;
    }
    $r_qiz->intro       = "";
    $r_qiz->introformat = FORMAT_HTML;
    $r_qiz->timeopen = 0;
    $r_qiz->timeclose = 0;
    $r_qiz->timelimitenable = 0;
    $r_qiz->timelimit       = 0;
    $r_qiz->attempts        = 0;
    $r_qiz->grademethod     = 1;
    if (respondusws_floatcompare($CFG->version, 2012040205, 2) >= 0) {
        $r_qiz->overduehandling = "autoabandon";
    }
    if (respondusws_floatcompare($CFG->version, 2012040206, 2) >= 0) {
        $r_qiz->graceperiod = 86400;
    }
    $r_qiz->questionsperpage = 0;
    $r_qiz->shufflequestions = 0;
    if (respondusws_floatcompare($CFG->version, 2012030901, 2) >= 0) {
        $r_qiz->navmethod = "free";
    }
    $r_qiz->shuffleanswers = 1;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->preferredbehaviour = "adaptive";
    } else {
        $r_qiz->adaptive      = 1;
        $r_qiz->penaltyscheme = 1;
    }
    $r_qiz->attemptonlast = 0;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->attemptduring          = 1;
        $r_qiz->correctnessduring      = 1;
        $r_qiz->marksduring            = 1;
        $r_qiz->specificfeedbackduring = 1;
        $r_qiz->generalfeedbackduring  = 1;
        $r_qiz->rightanswerduring      = 1;
        $r_qiz->overallfeedbackduring  = 1;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->attemptimmediately          = 1;
        $r_qiz->correctnessimmediately      = 1;
        $r_qiz->marksimmediately            = 1;
        $r_qiz->specificfeedbackimmediately = 1;
        $r_qiz->generalfeedbackimmediately  = 1;
        $r_qiz->rightanswerimmediately      = 1;
        $r_qiz->overallfeedbackimmediately  = 1;
    } else {
        $r_qiz->responsesimmediately       = 1;
        $r_qiz->answersimmediately         = 1;
        $r_qiz->feedbackimmediately        = 1;
        $r_qiz->generalfeedbackimmediately = 1;
        $r_qiz->scoreimmediately           = 1;
        $r_qiz->overallfeedbackimmediately = 1;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->attemptopen          = 1;
        $r_qiz->correctnessopen      = 1;
        $r_qiz->marksopen            = 1;
        $r_qiz->specificfeedbackopen = 1;
        $r_qiz->generalfeedbackopen  = 1;
        $r_qiz->rightansweropen      = 1;
        $r_qiz->overallfeedbackopen  = 1;
    } else {
        $r_qiz->responsesopen       = 1;
        $r_qiz->answersopen         = 1;
        $r_qiz->feedbackopen        = 1;
        $r_qiz->generalfeedbackopen = 1;
        $r_qiz->scoreopen           = 1;
        $r_qiz->overallfeedbackopen = 1;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->attemptclosed          = 1;
        $r_qiz->correctnessclosed      = 1;
        $r_qiz->marksclosed            = 1;
        $r_qiz->specificfeedbackclosed = 1;
        $r_qiz->generalfeedbackclosed  = 1;
        $r_qiz->rightanswerclosed      = 1;
        $r_qiz->overallfeedbackclosed  = 1;
    } else {
        $r_qiz->responsesclosed       = 1;
        $r_qiz->answersclosed         = 1;
        $r_qiz->feedbackclosed        = 1;
        $r_qiz->generalfeedbackclosed = 1;
        $r_qiz->scoreclosed           = 1;
        $r_qiz->overallfeedbackclosed = 1;
    }
    $r_qiz->showuserpicture       = 0;
    $r_qiz->decimalpoints         = 2;
    $r_qiz->questiondecimalpoints = -1;
    $r_qiz->showblocks            = 0;
    $r_qiz->quizpassword = "";
    $r_qiz->subnet = "";
    $r_qiz->delay1          = 0;
    $r_qiz->delay2          = 0;
    $r_qiz->popup           = 0;
    $r_qiz->browsersecurity = "-";
    $r_nf = 5;
    for ($r_i = 0; $r_i < $r_nf; $r_i++) {
        $r_drf                          = 0;
        $r_cmp                        = "mod_quiz";
        $r_far                         = "feedback";
        $r_iti                           = null;
        $r_op                          = null;
        $r_txt                             = "";
        $r_qiz->feedbacktext[$r_i]["text"]   = file_prepare_draft_area(
            $r_drf, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_op, $r_txt
        );
        $r_qiz->feedbacktext[$r_i]["format"] = FORMAT_HTML;
        $r_qiz->feedbacktext[$r_i]["itemid"] = $r_drf;
        if ($r_i < $r_nf - 1) {
            $r_qiz->feedbackboundaries[$r_i] = "";
        }
    }
    $r_qiz->groupmode  = NOGROUPS;
    $r_qiz->groupingid = 0;
    $r_qiz->visible    = 1;
    $r_qiz->cmidnumber = "";
    if (!empty($r_qiz->course)) {
        $r_crs = $DB->get_record("course", array("id" => $r_qiz->course));
        if ($r_crs !== false && $r_crs->groupmodeforce) {
            $r_qiz->groupmode  = $r_crs->groupmode;
            $r_qiz->groupingid = $r_crs->defaultgroupingid;
        }
    }
    $r_qiz->grade = 10;
}
function RWSSQDMoodle(&$r_qiz) {
    global $DB;
    global $CFG;
    if (!empty($r_qiz->coursemodule)) {
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context_module::instance($r_qiz->coursemodule);
        } else {
            $r_ctx = get_context_instance(CONTEXT_MODULE, $r_qiz->coursemodule);
        }
        $r_ctxi = $r_ctx->id;
    } else if (!empty($r_qiz->course)) {
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context_course::instance($r_qiz->course);
        } else {
            $r_ctx = get_context_instance(CONTEXT_COURSE, $r_qiz->course);
        }
        $r_ctxi = $r_ctx->id;
    } else {
        $r_ctxi = null;
    }
    $r_dfs = get_config("quiz");
    $r_qiz->intro       = "";
    $r_qiz->introformat = FORMAT_HTML;
    $r_qiz->timeopen  = 0;
    $r_qiz->timeclose = 0;
    if ($r_dfs->timelimit > 0) {
        $r_qiz->timelimitenable = 1;
    } else {
        $r_qiz->timelimitenable = 0;
    }
    $r_qiz->timelimit = $r_dfs->timelimit;
    $r_qiz->attempts    = $r_dfs->attempts;
    $r_qiz->grademethod = $r_dfs->grademethod;
    if (respondusws_floatcompare($CFG->version, 2012040205, 2) >= 0) {
        $r_qiz->overduehandling = $r_dfs->overduehandling;
    }
    if (respondusws_floatcompare($CFG->version, 2012040206, 2) >= 0) {
        $r_qiz->graceperiod = $r_dfs->graceperiod;
    }
    $r_qiz->questionsperpage = $r_dfs->questionsperpage;
    $r_qiz->shufflequestions = $r_dfs->shufflequestions;
    if (respondusws_floatcompare($CFG->version, 2012030901, 2) >= 0) {
        $r_qiz->navmethod = $r_dfs->navmethod;
    }
    $r_qiz->shuffleanswers = $r_dfs->shuffleanswers;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->preferredbehaviour = $r_dfs->preferredbehaviour;
    } else {
        $r_qiz->adaptive      = $r_dfs->optionflags & RWSQAD;
        $r_qiz->penaltyscheme = $r_dfs->penaltyscheme;
    }
    $r_qiz->attemptonlast = $r_dfs->attemptonlast;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->attemptduring = $r_dfs->reviewattempt & RWSRDU;
        if (!$r_qiz->attemptduring) {
            unset($r_qiz->attemptduring);
        }
        $r_qiz->correctnessduring = $r_dfs->reviewcorrectness & RWSRDU;
        if (!$r_qiz->correctnessduring) {
            unset($r_qiz->correctnessduring);
        }
        $r_qiz->marksduring = $r_dfs->reviewmarks & RWSRDU;
        if (!$r_qiz->marksduring) {
            unset($r_qiz->marksduring);
        }
        $r_qiz->specificfeedbackduring = $r_dfs->reviewspecificfeedback & RWSRDU;
        if (!$r_qiz->specificfeedbackduring) {
            unset($r_qiz->specificfeedbackduring);
        }
        $r_qiz->generalfeedbackduring = $r_dfs->reviewgeneralfeedback & RWSRDU;
        if (!$r_qiz->generalfeedbackduring) {
            unset($r_qiz->generalfeedbackduring);
        }
        $r_qiz->rightanswerduring = $r_dfs->reviewrightanswer & RWSRDU;
        if (!$r_qiz->rightanswerduring) {
            unset($r_qiz->rightanswerduring);
        }
        $r_qiz->overallfeedbackduring = $r_dfs->reviewoverallfeedback & RWSRDU;
        if (!$r_qiz->overallfeedbackduring) {
            unset($r_qiz->overallfeedbackduring);
        }
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->attemptimmediately = $r_dfs->reviewattempt & RWSRIA;
        if (!$r_qiz->attemptimmediately) {
            unset($r_qiz->attemptimmediately);
        }
        $r_qiz->correctnessimmediately = $r_dfs->reviewcorrectness & RWSRIA;
        if (!$r_qiz->correctnessimmediately) {
            unset($r_qiz->correctnessimmediately);
        }
        $r_qiz->marksimmediately = $r_dfs->reviewmarks & RWSRIA;
        if (!$r_qiz->marksimmediately) {
            unset($r_qiz->marksimmediately);
        }
        $r_qiz->specificfeedbackimmediately = $r_dfs->reviewspecificfeedback & RWSRIA;
        if (!$r_qiz->specificfeedbackimmediately) {
            unset($r_qiz->specificfeedbackimmediately);
        }
        $r_qiz->generalfeedbackimmediately = $r_dfs->reviewgeneralfeedback & RWSRIA;
        if (!$r_qiz->generalfeedbackimmediately) {
            unset($r_qiz->generalfeedbackimmediately);
        }
        $r_qiz->rightanswerimmediately = $r_dfs->reviewrightanswer & RWSRIA;
        if (!$r_qiz->rightanswerimmediately) {
            unset($r_qiz->rightanswerimmediately);
        }
        $r_qiz->overallfeedbackimmediately = $r_dfs->reviewoverallfeedback & RWSRIA;
        if (!$r_qiz->overallfeedbackimmediately) {
            unset($r_qiz->overallfeedbackimmediately);
        }
    } else {
        $r_qiz->responsesimmediately = $r_dfs->review & RWSRRE & RWSRIM;
        if (!$r_qiz->responsesimmediately) {
            unset($r_qiz->responsesimmediately);
        }
        $r_qiz->answersimmediately = $r_dfs->review & RWSRAN & RWSRIM;
        if (!$r_qiz->answersimmediately) {
            unset($r_qiz->answersimmediately);
        }
        $r_qiz->feedbackimmediately = $r_dfs->review & RWSRFE & RWSRIM;
        if (!$r_qiz->feedbackimmediately) {
            unset($r_qiz->feedbackimmediately);
        }
        $r_qiz->generalfeedbackimmediately = $r_dfs->review & RWSRGE
            & RWSRIM;
        if (!$r_qiz->generalfeedbackimmediately) {
            unset($r_qiz->generalfeedbackimmediately);
        }
        $r_qiz->scoreimmediately = $r_dfs->review & RWSRSC & RWSRIM;
        if (!$r_qiz->scoreimmediately) {
            unset($r_qiz->scoreimmediately);
        }
        $r_qiz->overallfeedbackimmediately = $r_dfs->review & RWSROV
            & RWSRIM;
        if (!$r_qiz->overallfeedbackimmediately) {
            unset($r_qiz->overallfeedbackimmediately);
        }
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->attemptopen = $r_dfs->reviewattempt & RWSRLA;
        if (!$r_qiz->attemptopen) {
            unset($r_qiz->attemptopen);
        }
        $r_qiz->correctnessopen = $r_dfs->reviewcorrectness & RWSRLA;
        if (!$r_qiz->correctnessopen) {
            unset($r_qiz->correctnessopen);
        }
        $r_qiz->marksopen = $r_dfs->reviewmarks & RWSRLA;
        if (!$r_qiz->marksopen) {
            unset($r_qiz->marksopen);
        }
        $r_qiz->specificfeedbackopen = $r_dfs->reviewspecificfeedback & RWSRLA;
        if (!$r_qiz->specificfeedbackopen) {
            unset($r_qiz->specificfeedbackopen);
        }
        $r_qiz->generalfeedbackopen = $r_dfs->reviewgeneralfeedback & RWSRLA;
        if (!$r_qiz->generalfeedbackopen) {
            unset($r_qiz->generalfeedbackopen);
        }
        $r_qiz->rightansweropen = $r_dfs->reviewrightanswer & RWSRLA;
        if (!$r_qiz->rightansweropen) {
            unset($r_qiz->rightansweropen);
        }
        $r_qiz->overallfeedbackopen = $r_dfs->reviewoverallfeedback & RWSRLA;
        if (!$r_qiz->overallfeedbackopen) {
            unset($r_qiz->overallfeedbackopen);
        }
    } else {
        $r_qiz->responsesopen = $r_dfs->review & RWSRRE & RWSROP;
        if (!$r_qiz->responsesopen) {
            unset($r_qiz->responsesopen);
        }
        $r_qiz->answersopen = $r_dfs->review & RWSRAN & RWSROP;
        if (!$r_qiz->answersopen) {
            unset($r_qiz->answersopen);
        }
        $r_qiz->feedbackopen = $r_dfs->review & RWSRFE & RWSROP;
        if (!$r_qiz->feedbackopen) {
            unset($r_qiz->feedbackopen);
        }
        $r_qiz->generalfeedbackopen = $r_dfs->review & RWSRGE & RWSROP;
        if (!$r_qiz->generalfeedbackopen) {
            unset($r_qiz->generalfeedbackopen);
        }
        $r_qiz->scoreopen = $r_dfs->review & RWSRSC & RWSROP;
        if (!$r_qiz->scoreopen) {
            unset($r_qiz->scoreopen);
        }
        $r_qiz->overallfeedbackopen = $r_dfs->review & RWSROV & RWSROP;
        if (!$r_qiz->overallfeedbackopen) {
            unset($r_qiz->overallfeedbackopen);
        }
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->attemptclosed = $r_dfs->reviewattempt & RWSRAF;
        if (!$r_qiz->attemptclosed) {
            unset($r_qiz->attemptclosed);
        }
        $r_qiz->correctnessclosed = $r_dfs->reviewcorrectness & RWSRAF;
        if (!$r_qiz->correctnessclosed) {
            unset($r_qiz->correctnessclosed);
        }
        $r_qiz->marksclosed = $r_dfs->reviewmarks & RWSRAF;
        if (!$r_qiz->marksclosed) {
            unset($r_qiz->marksclosed);
        }
        $r_qiz->specificfeedbackclosed = $r_dfs->reviewspecificfeedback & RWSRAF;
        if (!$r_qiz->specificfeedbackclosed) {
            unset($r_qiz->specificfeedbackclosed);
        }
        $r_qiz->generalfeedbackclosed = $r_dfs->reviewgeneralfeedback & RWSRAF;
        if (!$r_qiz->generalfeedbackclosed) {
            unset($r_qiz->generalfeedbackclosed);
        }
        $r_qiz->rightanswerclosed = $r_dfs->reviewrightanswer & RWSRAF;
        if (!$r_qiz->rightanswerclosed) {
            unset($r_qiz->rightanswerclosed);
        }
        $r_qiz->overallfeedbackclosed = $r_dfs->reviewoverallfeedback & RWSRAF;
        if (!$r_qiz->overallfeedbackclosed) {
            unset($r_qiz->overallfeedbackclosed);
        }
    } else {
        $r_qiz->responsesclosed = $r_dfs->review & RWSRRE & RWSRCL;
        if (!$r_qiz->responsesclosed) {
            unset($r_qiz->responsesclosed);
        }
        $r_qiz->answersclosed = $r_dfs->review & RWSRAN & RWSRCL;
        if (!$r_qiz->answersclosed) {
            unset($r_qiz->answersclosed);
        }
        $r_qiz->feedbackclosed = $r_dfs->review & RWSRFE & RWSRCL;
        if (!$r_qiz->feedbackclosed) {
            unset($r_qiz->feedbackclosed);
        }
        $r_qiz->generalfeedbackclosed = $r_dfs->review & RWSRGE & RWSRCL;
        if (!$r_qiz->generalfeedbackclosed) {
            unset($r_qiz->generalfeedbackclosed);
        }
        $r_qiz->scoreclosed = $r_dfs->review & RWSRSC & RWSRCL;
        if (!$r_qiz->scoreclosed) {
            unset($r_qiz->scoreclosed);
        }
        $r_qiz->overallfeedbackclosed = $r_dfs->review & RWSROV & RWSRCL;
        if (!$r_qiz->overallfeedbackclosed) {
            unset($r_qiz->overallfeedbackclosed);
        }
    }
    $r_qiz->showuserpicture       = $r_dfs->showuserpicture;
    $r_qiz->decimalpoints         = $r_dfs->decimalpoints;
    $r_qiz->questiondecimalpoints = $r_dfs->questiondecimalpoints;
    $r_qiz->showblocks            = $r_dfs->showblocks;
    $r_qiz->quizpassword = $r_dfs->password;
    $r_qiz->subnet       = $r_dfs->subnet;
    $r_qiz->delay1       = $r_dfs->delay1;
    $r_qiz->delay2       = $r_dfs->delay2;
    if (isset($r_dfs->browsersecurity)) {
        $r_qiz->browsersecurity = $r_dfs->browsersecurity;
    } else {
        $r_qiz->popup = $r_dfs->popup;
    }
    $r_nf = 5;
    for ($r_i = 0; $r_i < $r_nf; $r_i++) {
        $r_drf                          = 0;
        $r_cmp                        = "mod_quiz";
        $r_far                         = "feedback";
        $r_iti                           = null;
        $r_op                          = null;
        $r_txt                             = "";
        $r_qiz->feedbacktext[$r_i]["text"]   = file_prepare_draft_area(
            $r_drf, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_op, $r_txt
        );
        $r_qiz->feedbacktext[$r_i]["format"] = FORMAT_HTML;
        $r_qiz->feedbacktext[$r_i]["itemid"] = $r_drf;
        if ($r_i < $r_nf - 1) {
            $r_qiz->feedbackboundaries[$r_i] = "";
        }
    }
    $r_qiz->groupmode  = NOGROUPS;
    $r_qiz->groupingid = 0;
    $r_qiz->visible    = 1;
    $r_qiz->cmidnumber = "";
    if (!empty($r_qiz->course)) {
        $r_crs = $DB->get_record("course", array("id" => $r_qiz->course));
        if ($r_crs !== false) {
            $r_qiz->groupmode  = $r_crs->groupmode;
            $r_qiz->groupingid = $r_crs->defaultgroupingid;
            if (!empty($r_qiz->section)) {
                if (respondusws_floatcompare($CFG->version, 2012120300, 2) >= 0) {
                    $modinfo = get_fast_modinfo($r_qiz->course);
                    $r_sec = $modinfo->get_section_info($r_qiz->section);
                } else {
                    $r_sec = get_course_section($r_qiz->section, $r_qiz->course);
                }
                $r_qiz->visible = $r_sec->visible;
            }
        }
    }
    $r_qiz->grade = $r_dfs->maximumgrade;
}
function RWSSQDefs(&$r_qiz, $r_pop = false) {
    global $RWSLB;
        RWSSQDMoodle($r_qiz);
    $RWSLB->atts = 0;
    $RWSLB->revs  = 0;
    $RWSLB->pw = "";
    if ($r_pop) {
        if (is_null($r_qiz->quizpassword) && !is_null($r_qiz->password)) {
            $r_qiz->quizpassword = $r_qiz->password;
        }
        quiz_process_options($r_qiz);
    }
}
function RWSIQSet(&$r_qiz, $r_sfl, $r_sd, $r_ecd, $r_pop = false) {
    $r_clnid  = false;
    $r_clnif = false;
    $r_cloif = false;
    if ($r_ecd) {
        $r_dcd = base64_decode($r_sd);
        if ($r_dcd === false) {
            RWSSErr("2017");
        }
    } else {
        $r_dcd = $r_sd;
    }
    $r_imd       = RWSMTFldr();
    $r_ok               = ($r_imd !== false);
    $r_clnid = $r_ok;
    if (!$r_ok) {
        $r_err = "2018";
    }
    if ($r_ok) {
        $r_ok = RWSDIData($r_dcd, $r_imd);
        if (!$r_ok) {
            $r_err = "2019";
        }
    }
    if ($r_ok) {
        $r_p = strrpos($r_sfl, ".");
        $r_ok  = ($r_p !== false && $r_p !== 0);
        if (!$r_ok) {
            $r_err = "2020";
        }
    }
    if ($r_ok) {
        $r_imf = "$r_imd/";
        if ($r_p === false) {
            $r_imf .= $r_sfl;
        } else {
            $r_imf .= substr($r_sfl, 0, $r_p);
        }
        $r_imf .= ".dat";
        $r_ok                = file_exists($r_imf);
        $r_clnif = $r_ok;
        if (!$r_ok) {
            $r_err = "2020";
        }
    }
    if ($r_ok) {
        $r_hdl            = fopen($r_imf, "rb");
        $r_ok                = ($r_hdl !== false);
        $r_cloif = $r_ok;
        if (!$r_ok) {
            $r_err = "2021";
        }
    }
    if ($r_ok) {
        $r_ok = RWSCSFSig($r_hdl);
        if (!$r_ok) {
            $r_err = "2022";
        }
    }
    if ($r_ok) {
        $r_ok = RWSCSFVer($r_hdl);
        if (!$r_ok) {
            $r_err = "2023";
        }
    }
    if ($r_ok) {
        $r_rcd = RWSRSRec($r_hdl);
        $r_ok     = ($r_rcd !== false);
        if (!$r_ok) {
            $r_err = "2024";
        }
    }
    if ($r_ok) {
        $r_ok = RWSISRec($r_qiz, $r_rcd, $r_pop);
        if (!$r_ok) {
            $r_err = "2025";
        }
    }
    if ($r_cloif) {
        fclose($r_hdl);
    }
    if ($r_clnif && file_exists($r_imf)) {
        unlink($r_imf);
    }
    if ($r_clnid && file_exists($r_imd)) {
        rmdir($r_imd);
    }
    if (!$r_ok) {
        RWSSErr($r_err);
    }
}
function RWSEQSet($r_qiz, &$r_sfl, $r_w64) {
        $r_fv = 0;
    $r_fnc   = "rwsexportsdata.zip";
    $r_fnu = "rwsexportsdata.dat";
    $r_sfl                 = "";
    $r_clned      = false;
    $r_clnef     = false;
    $r_clncf = false;
    $r_cloef     = false;
    $r_ok                    = true;
    if ($r_ok) {
        $r_exd       = RWSMTFldr();
        $r_ok               = ($r_exd !== false);
        $r_clned = $r_ok;
        if (!$r_ok) {
            $r_err = "2026";
        }
    }
    if ($r_ok) {
        $r_exf       = "$r_exd/$r_fnu";
        $r_hdl            = fopen($r_exf, "wb");
        $r_ok                = ($r_hdl !== false);
        $r_clnef = $r_ok;
        $r_cloef = $r_ok;
        if (!$r_ok) {
            $r_err = "2027";
        }
    }
    if ($r_ok) {
            $r_dat = pack("C*", 0x21, 0xfd, 0x65, 0x0d, 0x6e, 0xae, 0x4d, 0x01,
                0x86, 0x78, 0xf5, 0x13, 0x00, 0x86, 0x99, 0x2a);
        $r_dat .= pack("n", $r_fv);
        $r_by = fwrite($r_hdl, $r_dat);
        $r_ok    = ($r_by !== false);
        if (!$r_ok) {
            $r_err = "2028";
        }
    }
    if ($r_ok) {
        $r_rcd = RWSESRec($r_qiz);
        $r_ok     = ($r_rcd !== false);
        if (!$r_ok) {
            $r_err = "2029";
        }
    }
    if ($r_ok) {
        $r_ok = RWSWSRec($r_hdl, $r_rcd);
        if (!$r_ok) {
            $r_err = "2028";
        }
    }
    if ($r_cloef) {
        fclose($r_hdl);
    }
    if ($r_ok) {
        $r_cf       = "$r_exd/$r_fnc";
        $r_ok                    = RWSCEData($r_exf, $r_cf);
        $r_clncf = $r_ok;
        if (!$r_ok) {
            $r_err = "2031";
        }
    }
    if ($r_ok) {
        $r_cpr = file_get_contents($r_cf);
        $r_ok         = ($r_cpr !== false);
        if (!$r_ok) {
            $r_err = "2032";
        }
    }
    if ($r_ok && $r_w64) {
        $r_ecd = base64_encode($r_cpr);
    }
    if ($r_clnef && file_exists($r_exf)) {
        unlink($r_exf);
    }
    if ($r_clncf && file_exists($r_cf)) {
        unlink($r_cf);
    }
    if ($r_clned && file_exists($r_exd)) {
        rmdir($r_exd);
    }
    if (!$r_ok) {
        RWSSErr($r_err);
    }
    $r_sfl = $r_fnc;
    if ($r_w64) {
        return $r_ecd;
    } else {
        return $r_cpr;
    }
}
function RWSIQues($r_cid, $r_qci, $r_qfl, $r_qd, $r_ecd, &$r_drp, &$r_ba) {
    $r_impd          = 0;
    $r_drp           = 0;
    $r_ba           = 0;
    $r_br           = 0;
    $r_clnid  = false;
    $r_clnif = false;
    $r_cloif = false;
    if ($r_ecd) {
        $r_dcd = base64_decode($r_qd);
        if ($r_dcd === false) {
            RWSSErr("2033");
        }
    } else {
        $r_dcd = $r_qd;
    }
    $r_imd       = RWSMTFldr();
    $r_ok               = ($r_imd !== false);
    $r_clnid = $r_ok;
    if (!$r_ok) {
        $r_err = "2034";
    }
    if ($r_ok) {
        $r_ok = RWSDIData($r_dcd, $r_imd);
        if (!$r_ok) {
            $r_err = "2035";
        }
    }
    if ($r_ok) {
        $r_p = strrpos($r_qfl, ".");
        $r_ok  = ($r_p !== false && $r_p !== 0);
        if (!$r_ok) {
            $r_err = "2036";
        }
    }
    if ($r_ok) {
        $r_imf = "$r_imd/";
        if ($r_p === false) {
            $r_imf .= $r_qfl;
        } else {
            $r_imf .= substr($r_qfl, 0, $r_p);
        }
        $r_imf .= ".dat";
        $r_ok                = file_exists($r_imf);
        $r_clnif = $r_ok;
        if (!$r_ok) {
            $r_err = "2036";
        }
    }
    if ($r_ok) {
        $r_hdl            = fopen($r_imf, "rb");
        $r_ok                = ($r_hdl !== false);
        $r_cloif = $r_ok;
        if (!$r_ok) {
            $r_err = "2037";
        }
    }
    if ($r_ok) {
        $r_ok = RWSCQFSig($r_hdl);
        if (!$r_ok) {
            $r_err = "2038";
        }
    }
    if ($r_ok) {
        $r_ok = RWSCQFVer($r_hdl);
        if (!$r_ok) {
            $r_err = "2039";
        }
    }
    if ($r_ok) {
        $r_qsti = array();
        $r_rcd       = RWSRNQRec($r_hdl);
        while ($r_rcd !== false) {
            $r_typ = RWSGQRType($r_rcd);
            switch ($r_typ) {
                case RWSATT:
                    $r_sbp = RWSIARec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSSHA:
                    $r_qi = RWSISARec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSTRF:
                    $r_qi = RWSITFRec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSMCH:
                    $r_qi = RWSIMCRec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSMAT:
                    $r_qi = RWSIMRec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSDES:
                    $r_qi = RWSIDRec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSESS:
                    $r_qi = RWSIERec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSCAL:
                    $r_qi = RWSICRec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSMAN:
                    $r_qi = RWSIMARec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSRSV:
                    $r_res = RWSIRRec($r_cid, $r_qci, $r_rcd);
                    break;
                case RWSCSI:
                case RWSCMU:
                case RWSRND:
                case RWSNUM:
                case RWSRSM:
                case RWSUNK:
                default:
                    $r_qi = false;
                    break;
            }
            if ($r_typ == RWSATT) {
                if ($r_sbp === false) {
                    $r_ba++;
                }
            } else if ($r_typ == RWSRSV) {
                if ($r_res === false) {
                    $r_br++;
                }
            } else {
                if ($r_qi === false) {
                    $r_drp++;
                } else {
                    $r_impd++;
                    $r_qsti[] = $r_qi;
                }
            }
            $r_rcd = RWSRNQRec($r_hdl);
        }
    }
    if ($r_cloif) {
        fclose($r_hdl);
    }
    if ($r_clnif && file_exists($r_imf)) {
        unlink($r_imf);
    }
    if ($r_clnid && file_exists($r_imd)) {
        rmdir($r_imd);
    }
    if (!$r_ok) {
        RWSSErr($r_err);
    }
    if ($r_impd == 0) {
        if ($r_drp == 0) {
            RWSSErr("2040");
        } else {
            RWSSErr("2041");
        }
    }
    return $r_qsti;
}
function RWSCQFSig($r_hdl) {
    $r_es = array(
        0xe1,
        0x8a,
        0x3b,
        0xaf,
        0xd0,
        0x30,
        0x4d,
        0xce,
        0xb4,
        0x75,
        0x8a,
        0xdf,
        0x1e,
        0xa9,
        0x08,
        0x36
    );
    if (feof($r_hdl)) {
        return false;
    }
    $r_bf = fread($r_hdl, 16);
    if ($r_bf === false) {
        return false;
    }
    if (feof($r_hdl)) {
        return false;
    }
    $r_as = array_values(unpack("C*", $r_bf));
    $r_ct = count($r_es);
    if ($r_ct != count($r_as)) {
        return false;
    }
    for ($r_i = 0; $r_i < $r_ct; $r_i++) {
        if ($r_as[$r_i] != $r_es[$r_i]) {
            return false;
        }
    }
    return true;
}
function RWSCSFSig($r_hdl) {
    $r_es = array(
        0x07,
        0x0b,
        0x28,
        0x3a,
        0x98,
        0xfa,
        0x4c,
        0xcd,
        0x8a,
        0x62,
        0x14,
        0xa7,
        0x97,
        0x33,
        0x84,
        0x37
    );
    if (feof($r_hdl)) {
        return false;
    }
    $r_bf = fread($r_hdl, 16);
    if ($r_bf === false) {
        return false;
    }
    if (feof($r_hdl)) {
        return false;
    }
    $r_as = array_values(unpack("C*", $r_bf));
    $r_ct = count($r_es);
    if ($r_ct != count($r_as)) {
        return false;
    }
    for ($r_i = 0; $r_i < $r_ct; $r_i++) {
        if ($r_as[$r_i] != $r_es[$r_i]) {
            return false;
        }
    }
    return true;
}
function RWSCQFVer($r_hdl) {
    $r_ev = 0;
    if (feof($r_hdl)) {
        return false;
    }
    $r_bf = fread($r_hdl, 2);
    if ($r_bf === false) {
        return false;
    }
    if (feof($r_hdl)) {
        return false;
    }
    $r_dat           = unpack("n", $r_bf);
    $r_av = $r_dat[1];
    if ($r_av == $r_ev) {
        return true;
    } else {
        return false;
    }
}
function RWSCSFVer($r_hdl) {
    $r_ev = 0;
    if (feof($r_hdl)) {
        return false;
    }
    $r_bf = fread($r_hdl, 2);
    if ($r_bf === false) {
        return false;
    }
    if (feof($r_hdl)) {
        return false;
    }
    $r_dat           = unpack("n", $r_bf);
    $r_av = $r_dat[1];
    if ($r_av == $r_ev) {
        return true;
    } else {
        return false;
    }
}
function RWSRSRec($r_hdl) {
    if (feof($r_hdl)) {
        return false;
    }
    $r_cpos = ftell($r_hdl);
    if (fseek($r_hdl, 0, SEEK_END) != 0) {
        return false;
    }
    $r_ep = ftell($r_hdl);
    $r_sz    = $r_ep - $r_cpos;
    if (fseek($r_hdl, $r_cpos, SEEK_SET) != 0) {
        return false;
    }
    $r_rcd = fread($r_hdl, $r_sz);
    if ($r_rcd === false) {
        return false;
    }
    if (feof($r_hdl)) {
        return false;
    }
    for ($r_i = 0; $r_i < $r_sz; $r_i++) {
        $r_dat = unpack("C", $r_rcd[$r_i]);
        $r_n    = (intval($r_dat[1]) ^ 0x55) - 1;
        if ($r_n < 0) {
            $r_n = 255;
        }
        $r_rcd[$r_i] = pack("C", $r_n);
    }
    return $r_rcd;
}
function RWSWSRec($r_hdl, $r_rcd) {
    $r_ok = true;
    $r_l = strlen($r_rcd);
    for ($r_i = 0; $r_i < $r_l; $r_i++) {
        $r_dat = unpack("C", $r_rcd[$r_i]);
        $r_n = intval($r_dat[1]) - 1;
        if ($r_n < 0) {
            $r_n = 255;
        }
        $r_n ^= 0xaa;
        $r_rcd[$r_i] = pack("C", $r_n);
    }
    if ($r_l > 0) {
        $r_by = fwrite($r_hdl, $r_rcd);
        $r_ok    = ($r_by !== false);
    }
    return $r_ok;
}
function RWSRNQRec($r_hdl) {
    $r_rcd = "";
    if (feof($r_hdl)) {
        return false;
    }
    $r_bf = fread($r_hdl, 1);
    if ($r_bf === false) {
        return false;
    }
    if (feof($r_hdl)) {
        return false;
    }
    $r_rcd .= $r_bf;
    $r_bf = fread($r_hdl, 4);
    if ($r_bf === false) {
        return false;
    }
    if (feof($r_hdl)) {
        return false;
    }
    $r_rcd .= $r_bf;
    $r_sz = strlen($r_bf);
    for ($r_i = 0; $r_i < $r_sz; $r_i++) {
        $r_dat = unpack("C", $r_bf[$r_i]);
        $r_n    = (intval($r_dat[1]) ^ 0x55) - 1;
        if ($r_n < 0) {
            $r_n = 255;
        }
        $r_bf[$r_i] = pack("C", $r_n);
    }
    $r_dat = unpack("N", $r_bf);
    $r_sz = $r_dat[1];
    if ($r_sz < 1) {
        return false;
    }
    $r_bf = fread($r_hdl, $r_sz);
    if ($r_bf === false) {
        return false;
    }
    if (feof($r_hdl)) {
        return false;
    }
    $r_rcd .= $r_bf;
    $r_sz = strlen($r_rcd);
    for ($r_i = 0; $r_i < $r_sz; $r_i++) {
        $r_dat = unpack("C", $r_rcd[$r_i]);
        $r_n    = (intval($r_dat[1]) ^ 0x55) - 1;
        if ($r_n < 0) {
            $r_n = 255;
        }
        $r_rcd[$r_i] = pack("C", $r_n);
    }
    return $r_rcd;
}
function RWSWNQRec($r_hdl, $r_rcd) {
    $r_ok = true;
    $r_l = strlen($r_rcd);
    for ($r_i = 0; $r_i < $r_l; $r_i++) {
        $r_dat = unpack("C", $r_rcd[$r_i]);
        $r_n = intval($r_dat[1]) - 1;
        if ($r_n < 0) {
            $r_n = 255;
        }
        $r_n ^= 0xaa;
        $r_rcd[$r_i] = pack("C", $r_n);
    }
    if ($r_l > 0) {
        $r_by = fwrite($r_hdl, $r_rcd);
        $r_ok    = ($r_by !== false);
    }
    return $r_ok;
}
function RWSGQRType($r_rcd) {
    $r_dat = unpack("C", $r_rcd[0]);
    $r_typ = intval($r_dat[1]);
    switch ($r_typ) {
        case 0:
            return RWSATT;
        case 1:
            return RWSMCH;
        case 2:
            return RWSTRF;
        case 3:
            return RWSSHA;
        case 4:
            return RWSESS;
        case 5:
            return RWSMAT;
        case 6:
            return RWSDES;
        case 7:
            return RWSCAL;
        case 8:
            return RWSNUM;
        case 9:
            return RWSMAN;
        case 10:
            return RWSRND;
        case 11:
            return RWSRSM;
        case 12:
            return RWSRSV;
        case 13:
            return RWSCSI;
        case 14:
            return RWSCMU;
        default:
            return RWSUNK;
    }
}
function RWSGDIMon($r_mo, $r_y) {
    switch ($r_mo) {
        case 1:
        case 3:
        case 5:
        case 7:
        case 8:
        case 10:
        case 12:
            return 31;
        case 4:
        case 6:
        case 9:
        case 11:
            return 30;
        case 2:
            if ($r_y % 400 == 0) {
                return 29;
            } else if ($r_y % 100 == 0) {
                return 28;
            } else if ($r_y % 4 == 0) {
                return 29;
            } else {
                return 28;
            }
        default:
            return false;
    }
}
function RWSISRec(&$r_qiz, $r_rcd, $r_pop = false) {
    global $RWSLB;
    global $CFG;
    $r_p  = 0;
    $r_sz = strlen($r_rcd);
    if (!empty($r_qiz->coursemodule)) {
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context_module::instance($r_qiz->coursemodule);
        } else {
            $r_ctx = get_context_instance(CONTEXT_MODULE, $r_qiz->coursemodule);
        }
        $r_ctxi = $r_ctx->id;
    } else if (!empty($r_qiz->course)) {
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context_course::instance($r_qiz->course);
        } else {
            $r_ctx = get_context_instance(CONTEXT_COURSE, $r_qiz->course);
        }
        $r_ctxi = $r_ctx->id;
    } else {
        $r_ctxi = null;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qiz->intro       = trim($r_fld);
    $r_qiz->introformat = FORMAT_HTML;
    $r_ct = 2;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("n", $r_fld);
    $r_y = $r_dat[1];
    if ($r_y != 0 && ($r_y < 1970 || $r_y > 2037)) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("C", $r_fld);
    $r_mo = intval($r_dat[1]);
    if ($r_y != 0 && ($r_mo < 1 || $r_mo > 12)) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("C", $r_fld);
    $r_da  = intval($r_dat[1]);
    if ($r_y != 0 && ($r_da < 1 || $r_da > RWSGDIMon($r_mo, $r_y))) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("C", $r_fld);
    $r_hr = intval($r_dat[1]);
    if ($r_y != 0 && ($r_hr < 0 || $r_hr > 23)) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat   = unpack("C", $r_fld);
    $r_mt = intval($r_dat[1]);
    if ($r_y != 0 && ($r_mt < 0 || $r_mt > 55 || $r_mt % 5 != 0)) {
        return false;
    }
    if ($r_y == 0) {
        $r_qiz->timeopen = 0;
    } else {
        $r_qiz->timeopen = make_timestamp($r_y, $r_mo, $r_da, $r_hr, $r_mt);
    }
    $r_ct = 2;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("n", $r_fld);
    $r_y = $r_dat[1];
    if ($r_y != 0 && ($r_y < 1970 || $r_y > 2037)) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("C", $r_fld);
    $r_mo = intval($r_dat[1]);
    if ($r_y != 0 && ($r_mo < 1 || $r_mo > 12)) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("C", $r_fld);
    $r_da  = intval($r_dat[1]);
    if ($r_y != 0 && ($r_da < 1 || $r_da > RWSGDIMon($r_mo, $r_y))) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("C", $r_fld);
    $r_hr = intval($r_dat[1]);
    if ($r_y != 0 && ($r_hr < 0 || $r_hr > 23)) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat   = unpack("C", $r_fld);
    $r_mt = intval($r_dat[1]);
    if ($r_y != 0 && ($r_mt < 0 || $r_mt > 55 || $r_mt % 5 != 0)) {
        return false;
    }
    if ($r_y == 0) {
        $r_qiz->timeclose = 0;
    } else {
        $r_qiz->timeclose = make_timestamp($r_y, $r_mo, $r_da, $r_hr, $r_mt);
    }
    if ($r_qiz->timeopen != 0 && $r_qiz->timeclose != 0
        && $r_qiz->timeopen > $r_qiz->timeclose
    ) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat                  = unpack("C", $r_fld);
    $r_qiz->timelimitenable = intval($r_dat[1]);
    if ($r_qiz->timelimitenable != 0 && $r_qiz->timelimitenable != 1) {
        return false;
    }
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat            = unpack("N", $r_fld);
    $r_qiz->timelimit = $r_dat[1] * 60;
    if ($r_qiz->timelimitenable == 0) {
        $r_qiz->timelimit = 0;
    }
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat         = unpack("N", $r_fld);
    $r_qiz->delay1 = $r_dat[1];
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat         = unpack("N", $r_fld);
    $r_qiz->delay2 = $r_dat[1];
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat                   = unpack("C", $r_fld);
    $r_qiz->questionsperpage = intval($r_dat[1]);
    if ($r_qiz->questionsperpage < 0 || $r_qiz->questionsperpage > 50) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat                   = unpack("C", $r_fld);
    $r_qiz->shufflequestions = intval($r_dat[1]);
    if ($r_qiz->shufflequestions != 0 && $r_qiz->shufflequestions != 1) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat                 = unpack("C", $r_fld);
    $r_qiz->shuffleanswers = intval($r_dat[1]);
    if ($r_qiz->shuffleanswers != 0 && $r_qiz->shuffleanswers != 1) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat           = unpack("C", $r_fld);
    $r_qiz->attempts = intval($r_dat[1]);
    if ($r_qiz->attempts < 0 || $r_qiz->attempts > 10) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat                = unpack("C", $r_fld);
    $r_qiz->attemptonlast = intval($r_dat[1]);
    if ($r_qiz->attemptonlast != 0 && $r_qiz->attemptonlast != 1) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat     = unpack("C", $r_fld);
    $r_adap = intval($r_dat[1]);
    if ($r_adap != 0 && $r_adap != 1) {
        return false;
    }
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat        = unpack("N", $r_fld);
    $r_qiz->grade = $r_dat[1];
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat              = unpack("C", $r_fld);
    $r_qiz->grademethod = intval($r_dat[1]);
    switch ($r_qiz->grademethod) {
        case 1:
        case 2:
        case 3:
        case 4:
            break;
        default:
            return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat          = unpack("C", $r_fld);
    $r_pen = intval($r_dat[1]);
    if ($r_pen != 0 && $r_pen != 1) {
        return false;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        if ($r_adap == 0 && $r_pen == 0) {
            $r_qiz->preferredbehaviour = "deferredfeedback";
        } else if ($r_adap == 0 && $r_pen == 1) {
            $r_qiz->preferredbehaviour = "deferredfeedback";
        } else if ($r_adap == 1 && $r_pen == 0) {
            $r_qiz->preferredbehaviour = "adaptivenopenalty";
        } else if ($r_adap == 1 && $r_pen == 1) {
            $r_qiz->preferredbehaviour = "adaptive";
        } else {
            return false;
        }
    } else {
        $r_qiz->adaptive      = $r_adap;
        $r_qiz->penaltyscheme = $r_pen;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat                = unpack("C", $r_fld);
    $r_qiz->decimalpoints = intval($r_dat[1]);
    switch ($r_qiz->decimalpoints) {
        case 0:
        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
            break;
        default:
            return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_rim = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_aim = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_fim = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_gim = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_sim = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_oim = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_rop = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_aop = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_fop = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_gop = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_sop = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_oop = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_rcl = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_acl = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_fcl = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_gcl = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_scl = $r_stg;
    } else {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_stg = intval($r_dat[1]);
    if ($r_stg == 0 || $r_stg == 1) {
        $r_ocl = $r_stg;
    } else {
        return false;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qiz->attemptduring = 1;
        if (!$r_qiz->attemptduring) {
            unset($r_qiz->attemptduring);
        }
        $r_qiz->correctnessduring = 1;
        if (!$r_qiz->correctnessduring) {
            unset($r_qiz->correctnessduring);
        }
        $r_qiz->marksduring = 1;
        if (!$r_qiz->marksduring) {
            unset($r_qiz->marksduring);
        }
        $r_qiz->specificfeedbackduring = $r_fim;
        if (!$r_qiz->specificfeedbackduring) {
            unset($r_qiz->specificfeedbackduring);
        }
        $r_qiz->generalfeedbackduring = $r_gim;
        if (!$r_qiz->generalfeedbackduring) {
            unset($r_qiz->generalfeedbackduring);
        }
        $r_qiz->rightanswerduring = $r_aim;
        if (!$r_qiz->rightanswerduring) {
            unset($r_qiz->rightanswerduring);
        }
        $r_qiz->overallfeedbackduring = 0;
        if (!$r_qiz->overallfeedbackduring) {
            unset($r_qiz->overallfeedbackduring);
        }
        $r_qiz->attemptimmediately = $r_rim;
        if (!$r_qiz->attemptimmediately) {
            unset($r_qiz->attemptimmediately);
        }
        $r_qiz->correctnessimmediately = $r_sim;
        if (!$r_qiz->correctnessimmediately) {
            unset($r_qiz->correctnessimmediately);
        }
        $r_qiz->marksimmediately = $r_sim;
        if (!$r_qiz->marksimmediately) {
            unset($r_qiz->marksimmediately);
        }
        $r_qiz->specificfeedbackimmediately = $r_fim;
        if (!$r_qiz->specificfeedbackimmediately) {
            unset($r_qiz->specificfeedbackimmediately);
        }
        $r_qiz->generalfeedbackimmediately = $r_gim;
        if (!$r_qiz->generalfeedbackimmediately) {
            unset($r_qiz->generalfeedbackimmediately);
        }
        $r_qiz->rightanswerimmediately = $r_aim;
        if (!$r_qiz->rightanswerimmediately) {
            unset($r_qiz->rightanswerimmediately);
        }
        $r_qiz->overallfeedbackimmediately = $r_oim;
        if (!$r_qiz->overallfeedbackimmediately) {
            unset($r_qiz->overallfeedbackimmediately);
        }
        $r_qiz->attemptopen = $r_rop;
        if (!$r_qiz->attemptopen) {
            unset($r_qiz->attemptopen);
        }
        $r_qiz->correctnessopen = $r_sop;
        if (!$r_qiz->correctnessopen) {
            unset($r_qiz->correctnessopen);
        }
        $r_qiz->marksopen = $r_sop;
        if (!$r_qiz->marksopen) {
            unset($r_qiz->marksopen);
        }
        $r_qiz->specificfeedbackopen = $r_fop;
        if (!$r_qiz->specificfeedbackopen) {
            unset($r_qiz->specificfeedbackopen);
        }
        $r_qiz->generalfeedbackopen = $r_gop;
        if (!$r_qiz->generalfeedbackopen) {
            unset($r_qiz->generalfeedbackopen);
        }
        $r_qiz->rightansweropen = $r_aop;
        if (!$r_qiz->rightansweropen) {
            unset($r_qiz->rightansweropen);
        }
        $r_qiz->overallfeedbackopen = $r_oop;
        if (!$r_qiz->overallfeedbackopen) {
            unset($r_qiz->overallfeedbackopen);
        }
        $r_qiz->attemptclosed = $r_rcl;
        if (!$r_qiz->attemptclosed) {
            unset($r_qiz->attemptclosed);
        }
        $r_qiz->correctnessclosed = $r_scl;
        if (!$r_qiz->correctnessclosed) {
            unset($r_qiz->correctnessclosed);
        }
        $r_qiz->marksclosed = $r_scl;
        if (!$r_qiz->marksclosed) {
            unset($r_qiz->marksclosed);
        }
        $r_qiz->specificfeedbackclosed = $r_fcl;
        if (!$r_qiz->specificfeedbackclosed) {
            unset($r_qiz->specificfeedbackclosed);
        }
        $r_qiz->generalfeedbackclosed = $r_gcl;
        if (!$r_qiz->generalfeedbackclosed) {
            unset($r_qiz->generalfeedbackclosed);
        }
        $r_qiz->rightanswerclosed = $r_acl;
        if (!$r_qiz->rightanswerclosed) {
            unset($r_qiz->rightanswerclosed);
        }
        $r_qiz->overallfeedbackclosed = $r_ocl;
        if (!$r_qiz->overallfeedbackclosed) {
            unset($r_qiz->overallfeedbackclosed);
        }
    } else {
        $r_qiz->responsesimmediately = $r_rim;
        if (!$r_qiz->responsesimmediately) {
            unset($r_qiz->responsesimmediately);
        }
        $r_qiz->answersimmediately = $r_aim;
        if (!$r_qiz->answersimmediately) {
            unset($r_qiz->answersimmediately);
        }
        $r_qiz->feedbackimmediately = $r_fim;
        if (!$r_qiz->feedbackimmediately) {
            unset($r_qiz->feedbackimmediately);
        }
        $r_qiz->generalfeedbackimmediately = $r_gim;
        if (!$r_qiz->generalfeedbackimmediately) {
            unset($r_qiz->generalfeedbackimmediately);
        }
        $r_qiz->scoreimmediately = $r_sim;
        if (!$r_qiz->scoreimmediately) {
            unset($r_qiz->scoreimmediately);
        }
        $r_qiz->overallfeedbackimmediately = $r_oim;
        if (!$r_qiz->overallfeedbackimmediately) {
            unset($r_qiz->overallfeedbackimmediately);
        }
        $r_qiz->responsesopen = $r_rop;
        if (!$r_qiz->responsesopen) {
            unset($r_qiz->responsesopen);
        }
        $r_qiz->answersopen = $r_aop;
        if (!$r_qiz->answersopen) {
            unset($r_qiz->answersopen);
        }
        $r_qiz->feedbackopen = $r_fop;
        if (!$r_qiz->feedbackopen) {
            unset($r_qiz->feedbackopen);
        }
        $r_qiz->generalfeedbackopen = $r_gop;
        if (!$r_qiz->generalfeedbackopen) {
            unset($r_qiz->generalfeedbackopen);
        }
        $r_qiz->scoreopen = $r_sop;
        if (!$r_qiz->scoreopen) {
            unset($r_qiz->scoreopen);
        }
        $r_qiz->overallfeedbackopen = $r_oop;
        if (!$r_qiz->overallfeedbackopen) {
            unset($r_qiz->overallfeedbackopen);
        }
        $r_qiz->responsesclosed = $r_rcl;
        if (!$r_qiz->responsesclosed) {
            unset($r_qiz->responsesclosed);
        }
        $r_qiz->answersclosed = $r_acl;
        if (!$r_qiz->answersclosed) {
            unset($r_qiz->answersclosed);
        }
        $r_qiz->feedbackclosed = $r_fcl;
        if (!$r_qiz->feedbackclosed) {
            unset($r_qiz->feedbackclosed);
        }
        $r_qiz->generalfeedbackclosed = $r_gcl;
        if (!$r_qiz->generalfeedbackclosed) {
            unset($r_qiz->generalfeedbackclosed);
        }
        $r_qiz->scoreclosed = $r_scl;
        if (!$r_qiz->scoreclosed) {
            unset($r_qiz->scoreclosed);
        }
        $r_qiz->overallfeedbackclosed = $r_ocl;
        if (!$r_qiz->overallfeedbackclosed) {
            unset($r_qiz->overallfeedbackclosed);
        }
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("C", $r_fld);
    $popup = intval($r_dat[1]);
    if ($popup != 0 && $popup != 1) {
        return false;
    }
    $r_qiz->popup = $popup;
    if ($popup == 0) {
        $r_qiz->browsersecurity = "-";
    } else {
        $r_qiz->browsersecurity = "securewindow";
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qiz->quizpassword = trim($r_fld);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qiz->subnet = trim($r_fld);
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat            = unpack("C", $r_fld);
    $r_qiz->groupmode = intval($r_dat[1]);
    switch ($r_qiz->groupmode) {
        case 0:
        case 1:
        case 2:
            break;
        default:
            return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat          = unpack("C", $r_fld);
    $r_qiz->visible = intval($r_dat[1]);
    if ($r_qiz->visible != 0 && $r_qiz->visible != 1) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qiz->cmidnumber = trim($r_fld);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat      = unpack("C", $r_fld);
    $r_nf = intval($r_dat[1]);
    $r_fds = array();
    for ($r_i = 0; $r_i < $r_nf; $r_i++) {
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_fds[] = trim($r_fld);
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat       = unpack("C", $r_fld);
    $r_nb = intval($r_dat[1]);
    $r_bds = array();
    for ($r_i = 0; $r_i < $r_nb; $r_i++) {
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_bd = trim($r_fld);
        $r_l   = strlen($r_bd);
        if ($r_l == 0) {
            return false;
        }
        if (is_numeric($r_bd)) {
            if ($r_bd <= 0 || $r_bd >= $r_qiz->grade) {
                return false;
            }
            if ($r_i > 0 && $r_bd >= $r_lb) {
                return false;
            }
            $r_lb = $r_bd;
        } else {
            if ($r_bd[$r_l - 1] != '%') {
                return false;
            }
            $r_pct = trim(substr($r_bd, 0, -1));
            if (!is_numeric($r_pct)) {
                return false;
            }
            if ($r_pct <= 0 || $r_pct >= 100) {
                return false;
            }
            if ($r_i > 0 && $r_bd >= $r_lb) {
                return false;
            }
            $r_lb = $r_bd * $r_qiz->grade / 100.0;
        }
        $r_bds[] = $r_bd;
    }
    $r_nf  = count($r_fds);
    $r_nb = count($r_bds);
    if ($r_nf > 0) {
        if ($r_nf != $r_nb + 1) {
            return false;
        }
        for ($r_i = 0; $r_i < $r_nf; $r_i++) {
            if (isset($r_qiz->feedbacktext[$r_i]["itemid"])) {
                $r_drf = $r_qiz->feedbacktext[$r_i]["itemid"];
            } else {
                $r_drf = 0;
            }
            $r_cmp                        = "mod_quiz";
            $r_far                         = "feedback";
            $r_iti                           = null;
            $r_op                          = null;
            $r_txt                             = $r_fds[$r_i];
            $r_qiz->feedbacktext[$r_i]["text"]   = file_prepare_draft_area(
                $r_drf, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_op, $r_txt
            );
            $r_qiz->feedbacktext[$r_i]["format"] = FORMAT_HTML;
            $r_qiz->feedbacktext[$r_i]["itemid"] = $r_drf;
            if ($r_i < $r_nf - 1) {
                $r_qiz->feedbackboundaries[$r_i] = $r_bds[$r_i];
            }
        }
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat     = unpack("C", $r_fld);
    $r_lbq = intval($r_dat[1]);
    if ($r_lbq != 0 && $r_lbq != 1) {
        return false;
    }
    $RWSLB->atts = $r_lbq;
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat       = unpack("C", $r_fld);
    $r_lbr = intval($r_dat[1]);
    if ($r_lbr != 0 && $r_lbr != 1) {
        return false;
    }
    $RWSLB->revs = $r_lbr;
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $RWSLB->pw = trim($r_fld);
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    if ($r_pop) {
        if (is_null($r_qiz->quizpassword) && !is_null($r_qiz->password)) {
            $r_qiz->quizpassword = $r_qiz->password;
        }
        quiz_process_options($r_qiz);
    }
    return true;
}
function RWSSLBSet(&$r_qiz) {
    global $RWSLB;
    $RWSLB->perr = false;
    if ($RWSLB->mok) {
        $r_ok = lockdown_set_settings($r_qiz->instance, $RWSLB->atts,
            $RWSLB->revs, $RWSLB->pw);
        if (!$r_ok) {
            $RWSLB->perr = true;
        }
    } else if ($RWSLB->bok) {
        $r_upq = false;
        if ($RWSLB->atts == 1) {
            $r_ok = lockdown_set_quiz_options($r_qiz->instance, $RWSLB);
            if (!$r_ok) {
                $RWSLB->perr = true;
            }
            if ($r_ok) {
                $r_suf     = get_string("requires_ldb", "block_lockdownbrowser");
                $r_qiz->name = str_replace($r_suf, "", $r_qzn);
                $r_qiz->name .= $r_suf;
                $r_upq = true;
            }
        } else {
            $r_rcd = lockdown_get_quiz_options($r_qiz->instance);
            if ($r_rcd !== false) {
                lockdown_delete_options($r_qiz->instance);
                $r_suf      = get_string("requires_ldb", "block_lockdownbrowser");
                $r_qiz->name  = str_replace($r_suf, "", $r_qzn);
                $r_upq = true;
            }
        }
        if ($r_upq) {
            if (is_null($r_qiz->quizpassword) && !is_null($r_qiz->password)) {
                $r_qiz->quizpassword = $r_qiz->password;
            }
            $r_res = quiz_update_instance($r_qiz);
            if (!$r_res || is_string($r_res)) {
                $RWSLB->perr = true;
            }
        }
    }
}
function RWSIARec($r_cid, $r_qci, $r_rcd) {
    global $CFG;
    global $RWSUID;
    if (RWSGQRType($r_rcd) != RWSATT) {
        return false;
    }
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_ff = $r_fld;
    $r_ff = clean_filename($r_ff);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_fn = $r_fld;
    $r_fn = clean_filename($r_fn);
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_fdat = $r_fld;
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context_course::instance($r_cid);
    } else {
        $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
    }
    $r_ctxi = $r_ctx->id;
    $r_cmp = "mod_respondusws";
    $r_far  = "upload";
    $r_iti    = $RWSUID;
    $r_fpt  = "/$r_ff/";
    $r_fna  = $r_fn;
    $r_finf  = array(
        "contextid" => $r_ctxi,
        "component" => $r_cmp,
        "filearea"  => $r_far,
        "itemid"    => $r_iti,
        "filepath"  => $r_fpt,
        "filename"  => $r_fna
    );
    $r_crpth = "$r_ff/$r_fn";
    try {
        $r_fs          = get_file_storage();
        $r_fex = $r_fs->file_exists(
            $r_ctxi, $r_cmp, $r_far, $r_iti, $r_fpt, $r_fna
        );
        if ($r_fex) {
            return false;
        }
        if (!$r_fs->create_file_from_string($r_finf, $r_fdat)) {
            return false;
        }
    } catch (Exception $r_e) {
        return false;
    }
    return $r_crpth;
}
function RWSIRRec($r_cid, $r_qci, $r_rcd) {
    if (RWSGQRType($r_rcd) != RWSRSV) {
        return false;
    }
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    return true;
}
function RWSISARec($r_cid, $r_qci, $r_rcd) {
    global $CFG;
    global $DB;
    global $RWSUID;
    if (RWSGQRType($r_rcd) != RWSSHA) {
        return false;
    }
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_ctxi = $DB->get_field("question_categories", "contextid",
        array("id" => $r_qci));
    $r_qst             = new stdClass();
    $r_qst->qtype      = RWSSHA;
    $r_qst->parent     = 0;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_qst->hidden   = 0;
        $r_qst->category = $r_qci;
    }
    $r_qst->length     = 1;
    $r_qst->stamp      = make_unique_id_code();
    $r_qst->createdby  = $RWSUID;
    $r_qst->modifiedby = $RWSUID;
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->name = trim($r_fld);
    if (strlen($r_qst->name) == 0) {
        return false;
    }
    $r_qst->name = clean_param($r_qst->name, PARAM_TEXT);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->questiontext       = trim($r_fld);
    $r_qst->questiontextformat = FORMAT_HTML;
    $r_qst->questiontext = clean_param($r_qst->questiontext, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("N", $r_fld);
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qst->defaultmark = $r_dat[1];
    } else {
        $r_qst->defaultgrade = $r_dat[1];
    }
    $r_ct = 8;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->penalty = RWSDblIn($r_fld);
    if ($r_qst->penalty < 0 || $r_qst->penalty > 1) {
        return false;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        switch (strval($r_qst->penalty)) {
            case "1":
            case "0.5":
            case "0.3333333":
            case "0.25":
            case "0.2":
            case "0.1":
            case "0":
                break;
            default:
                $r_qst->penalty = "0.3333333";
                break;
        }
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->generalfeedback       = trim($r_fld);
    $r_qst->generalfeedbackformat = FORMAT_HTML;
    $r_qst->generalfeedback = clean_param($r_qst->generalfeedback, PARAM_RAW);
    $r_op = new stdClass();
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat             = unpack("C", $r_fld);
    $r_op->usecase = intval($r_dat[1]);
    if ($r_op->usecase != 0 && $r_op->usecase != 1) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat        = unpack("C", $r_fld);
    $r_na = intval($r_dat[1]);
    if ($r_na < 1) {
        return false;
    }
    $r_asrs      = array();
    $r_mf = -1;
    for ($r_i = 0; $r_i < $r_na; $r_i++) {
        $r_asr = new stdClass();
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_asr->answer       = trim($r_fld);
        $r_asr->answerformat = FORMAT_PLAIN;
        $r_asr->answer = clean_param($r_asr->answer, PARAM_RAW);
        $r_ct = 8;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_asr->fraction = strval(RWSDblIn($r_fld));
        switch ($r_asr->fraction) {
            case "1":
            case "0.9":
            case "0.8333333":
            case "0.8":
            case "0.75":
            case "0.7":
            case "0.6666667":
            case "0.6":
            case "0.5":
            case "0.4":
            case "0.3333333":
            case "0.3":
            case "0.25":
            case "0.2":
            case "0.1666667":
            case "0.1428571":
            case "0.125":
            case "0.1111111":
            case "0.1":
            case "0.05":
            case "0":
                break;
            default:
                if (respondusws_floatcompare($r_bv, 2011020100, 2) >= 0) {
                    $r_asr->fraction = "0";
                }
                break;
        }
        if (respondusws_floatcompare($r_bv, 2011020100, 2) == -1) {
            switch ($r_asr->fraction) {
                case "0.83333":
                    $r_asr->fraction = "0.8333333";
                    break;
                case "0.66666":
                    $r_asr->fraction = "0.6666667";
                    break;
                case "0.33333":
                    $r_asr->fraction = "0.3333333";
                    break;
                case "0.16666":
                    $r_asr->fraction = "0.1666667";
                    break;
                case "0.142857":
                    $r_asr->fraction = "0.1428571";
                    break;
                case "0.11111":
                    $r_asr->fraction = "0.1111111";
                    break;
                default:
                    $r_asr->fraction = "0";
                    break;
            }
        }
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_asr->feedback       = trim($r_fld);
        $r_asr->feedbackformat = FORMAT_HTML;
        $r_asr->feedback = clean_param($r_asr->feedback, PARAM_RAW);
        if (strlen($r_asr->answer) == 0) {
            continue;
        }
        $r_asrs[] = $r_asr;
        if ($r_asr->fraction > $r_mf) {
            $r_mf = $r_asr->fraction;
        }
    }
    if (count($r_asrs) < 1) {
        return false;
    }
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->timecreated  = time();
    $r_qst->timemodified = time();
    $r_qst->id           = $DB->insert_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qb = new \stdClass();
        $r_qb->questioncategoryid = $r_qci;
        $r_qb->idnumber = null;
        $r_qb->ownerid = $r_qst->createdby;
        $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
        $r_qv = new \stdClass();
        $r_qv->questionbankentryid = $r_qb->id;
        $r_qv->questionid = $r_qst->id;
        $r_qv->version = get_next_version($r_qb->id);
        $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $r_qv->status = $r_sts;
        $r_qv->id = $DB->insert_record('question_versions', $r_qv);
    } else {
    }
    $r_cmp              = "question";
    $r_far               = "questiontext";
    $r_iti                 = $r_qst->id;
    $r_txt                   = $r_qst->questiontext;
    $r_qst->questiontext = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                 = "question";
    $r_far                  = "generalfeedback";
    $r_iti                    = $r_qst->id;
    $r_txt                      = $r_qst->generalfeedback;
    $r_qst->generalfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_h = question_hash($r_qst);
        $DB->set_field("question", "version", $r_h, array("id" => $r_qst->id));
    }
    $r_aid = array();
    foreach ($r_asrs as $r_an) {
        $r_an->question = $r_qst->id;
        $r_an->id       = $DB->insert_record("question_answers", $r_an);
        $r_cmp     = "question";
        $r_far      = "answerfeedback";
        $r_iti        = $r_an->id;
        $r_txt          = $r_an->feedback;
        $r_an->feedback = RWSPAtt($r_qst->qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $DB->update_record("question_answers", $r_an);
        $r_aid[] = $r_an->id;
    }
    if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
        $r_op->questionid = $r_qst->id;
        $r_op->id         = $DB->insert_record("qtype_shortanswer_options", $r_op);
    } else {
        $r_op->question = $r_qst->id;
        $r_op->answers  = implode(",", $r_aid);
        $r_op->id       = $DB->insert_record("question_shortanswer", $r_op);
    }
    return $r_qst->id;
}
function RWSITFRec($r_cid, $r_qci, $r_rcd) {
    global $CFG;
    global $DB;
    global $RWSUID;
    if (RWSGQRType($r_rcd) != RWSTRF) {
        return false;
    }
    $r_ctxi = $DB->get_field("question_categories", "contextid",
        array("id" => $r_qci));
    $r_qst             = new stdClass();
    $r_qst->qtype      = RWSTRF;
    $r_qst->parent     = 0;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_qst->hidden   = 0;
        $r_qst->category = $r_qci;
    }
    $r_qst->length     = 1;
    $r_qst->stamp      = make_unique_id_code();
    $r_qst->createdby  = $RWSUID;
    $r_qst->modifiedby = $RWSUID;
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->name = trim($r_fld);
    if (strlen($r_qst->name) == 0) {
        return false;
    }
    $r_qst->name = clean_param($r_qst->name, PARAM_TEXT);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->questiontext       = trim($r_fld);
    $r_qst->questiontextformat = FORMAT_HTML;
    $r_qst->questiontext = clean_param($r_qst->questiontext, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("N", $r_fld);
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qst->defaultmark = $r_dat[1];
    } else {
        $r_qst->defaultgrade = $r_dat[1];
    }
    $r_ct = 8;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->penalty = RWSDblIn($r_fld);
    if ($r_qst->penalty != 1) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->generalfeedback       = trim($r_fld);
    $r_qst->generalfeedbackformat = FORMAT_HTML;
    $r_qst->generalfeedback = clean_param($r_qst->generalfeedback, PARAM_RAW);
    $r_tru         = new stdClass();
    $r_tru->answer = get_string("true", "quiz");
    $r_fal         = new stdClass();
    $r_fal->answer = get_string("false", "quiz");
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat    = unpack("C", $r_fld);
    $r_cor = intval($r_dat[1]);
    if ($r_cor != 0 && $r_cor != 1) {
        return false;
    }
    $r_tru->fraction  = $r_cor;
    $r_fal->fraction = 1 - $r_cor;
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_tru->feedback       = trim($r_fld);
    $r_tru->feedbackformat = FORMAT_HTML;
    $r_tru->feedback = clean_param($r_tru->feedback, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_fal->feedback       = trim($r_fld);
    $r_fal->feedbackformat = FORMAT_HTML;
    $r_fal->feedback = clean_param($r_fal->feedback, PARAM_RAW);
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->timecreated  = time();
    $r_qst->timemodified = time();
    $r_qst->id           = $DB->insert_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qb = new \stdClass();
        $r_qb->questioncategoryid = $r_qci;
        $r_qb->idnumber = null;
        $r_qb->ownerid = $r_qst->createdby;
        $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
        $r_qv = new \stdClass();
        $r_qv->questionbankentryid = $r_qb->id;
        $r_qv->questionid = $r_qst->id;
        $r_qv->version = get_next_version($r_qb->id);
        $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $r_qv->status = $r_sts;
        $r_qv->id = $DB->insert_record('question_versions', $r_qv);
    } else {
    }
    $r_cmp              = "question";
    $r_far               = "questiontext";
    $r_iti                 = $r_qst->id;
    $r_txt                   = $r_qst->questiontext;
    $r_qst->questiontext = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                 = "question";
    $r_far                  = "generalfeedback";
    $r_iti                    = $r_qst->id;
    $r_txt                      = $r_qst->generalfeedback;
    $r_qst->generalfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_h = question_hash($r_qst);
        $DB->set_field("question", "version", $r_h, array("id" => $r_qst->id));
    }
    $r_tru->question = $r_qst->id;
    $r_tru->id       = $DB->insert_record("question_answers", $r_tru);
    $r_cmp      = "question";
    $r_far       = "answerfeedback";
    $r_iti         = $r_tru->id;
    $r_txt           = $r_tru->feedback;
    $r_tru->feedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question_answers", $r_tru);
    $r_fal->question = $r_qst->id;
    $r_fal->id       = $DB->insert_record("question_answers", $r_fal);
    $r_cmp       = "question";
    $r_far        = "answerfeedback";
    $r_iti          = $r_fal->id;
    $r_txt            = $r_fal->feedback;
    $r_fal->feedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question_answers", $r_fal);
    $r_op              = new stdClass();
    $r_op->question    = $r_qst->id;
    $r_op->trueanswer  = $r_tru->id;
    $r_op->falseanswer = $r_fal->id;
    $r_op->id          = $DB->insert_record("question_truefalse", $r_op);
    return $r_qst->id;
}
function RWSIMARec($r_cid, $r_qci, $r_rcd) {
    global $CFG;
    global $DB;
    global $RWSUID;
    if (RWSGQRType($r_rcd) != RWSMAN) {
        return false;
    }
    $r_ctxi = $DB->get_field("question_categories", "contextid",
        array("id" => $r_qci));
    $r_qst             = new stdClass();
    $r_qst->qtype      = RWSMAN;
    $r_qst->parent     = 0;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_qst->hidden   = 0;
        $r_qst->category = $r_qci;
    }
    $r_qst->length     = 1;
    $r_qst->stamp      = make_unique_id_code();
    $r_qst->createdby  = $RWSUID;
    $r_qst->modifiedby = $RWSUID;
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->name = trim($r_fld);
    if (strlen($r_qst->name) == 0) {
        return false;
    }
    $r_qst->name = clean_param($r_qst->name, PARAM_TEXT);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->questiontext       = trim($r_fld);
    $r_qst->questiontextformat = FORMAT_HTML;
    $r_qst->questiontext = clean_param($r_qst->questiontext, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_ct = 8;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->penalty = RWSDblIn($r_fld);
    if ($r_qst->penalty < 0 || $r_qst->penalty > 1) {
        return false;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        switch (strval($r_qst->penalty)) {
            case "1":
            case "0.5":
            case "0.3333333":
            case "0.25":
            case "0.2":
            case "0.1":
            case "0":
                break;
            default:
                $r_qst->penalty = "0.3333333";
                break;
        }
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->generalfeedback       = trim($r_fld);
    $r_qst->generalfeedbackformat = FORMAT_HTML;
    $r_qst->generalfeedback = clean_param($r_qst->generalfeedback, PARAM_RAW);
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_chn = array();
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qst->defaultmark = 0;
    } else {
        $r_qst->defaultgrade = 0;
    }
    $r_clzf = RWSGCFields($r_qst->questiontext);
    if ($r_clzf === false) {
        return false;
    }
    $r_chc = count($r_clzf);
    for ($r_i = 0; $r_i < $r_chc; $r_i++) {
        $r_chd = RWSCCChild($r_qst, $r_clzf[$r_i]);
        if ($r_chd === false) {
            return false;
        }
        $r_chn[] = $r_chd;
        if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
            $r_qst->defaultmark += $r_chd->defaultmark;
        } else {
            $r_qst->defaultgrade += $r_chd->defaultgrade;
        }
        $r_pk            = $r_i + 1;
        $r_qst->questiontext = implode("{#$r_pk}",
            explode($r_clzf[$r_i], $r_qst->questiontext, 2));
    }
    $r_qst->timecreated  = time();
    $r_qst->timemodified = time();
    $r_qst->id           = $DB->insert_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qb = new \stdClass();
        $r_qb->questioncategoryid = $r_qci;
        $r_qb->idnumber = null;
        $r_qb->ownerid = $r_qst->createdby;
        $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
        $r_qv = new \stdClass();
        $r_qv->questionbankentryid = $r_qb->id;
        $r_qv->questionid = $r_qst->id;
        $r_qv->version = get_next_version($r_qb->id);
        $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $r_qv->status = $r_sts;
        $r_qv->id = $DB->insert_record('question_versions', $r_qv);
    } else {
    }
    $r_cmp              = "question";
    $r_far               = "questiontext";
    $r_iti                 = $r_qst->id;
    $r_txt                   = $r_qst->questiontext;
    $r_qst->questiontext = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                 = "question";
    $r_far                  = "generalfeedback";
    $r_iti                    = $r_qst->id;
    $r_txt                      = $r_qst->generalfeedback;
    $r_qst->generalfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_h = question_hash($r_qst);
        $DB->set_field("question", "version", $r_h, array("id" => $r_qst->id));
    }
    $r_chid = array();
    foreach ($r_chn as $r_chd) {
        $r_chd->parent       = $r_qst->id;
        $r_chd->parent_qtype = $r_qst->qtype;
        $r_chd->id           = RWSCChild($r_chd, $r_cid, $r_qci, $r_ctxi);
        if ($r_chd->id === false) {
            if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
                question_delete_question($r_qst->id);
            } else {
                delete_question($r_qst->id);
            }
            return false;
        }
        $r_chid[] = $r_chd->id;
    }
    if (count($r_chid) > 0) {
        $r_op           = new stdClass();
        $r_op->question = $r_qst->id;
        $r_op->sequence = implode(",", $r_chid);
        $r_op->id       = $DB->insert_record("question_multianswer", $r_op);
    }
    return $r_qst->id;
}
function RWSCCChild($r_qst, $r_fld) {
    global $CFG;
    global $RWSPFNAME;
    $r_rxpt = false;
    $r_qtn = get_list_of_plugins("question/type");
    if (count($r_qtn) > 0) {
        foreach ($r_qtn as $r_qn) {
            if (strcasecmp($r_qn, RWSRXP) == 0) {
                $r_rxpt = true;
                break;
            }
        }
    }
    $r_rxpc = false;
    $r_pth         = "$CFG->dirroot/question/type/multianswer/questiontype.php";
    $r_dat         = file_get_contents($r_pth);
    if ($r_dat !== false
        && strpos($r_dat, "ANSWER_REGEX_ANSWER_TYPE_REGEXP") !== false
    ) {
        $r_rxpc = true;
    }
    $r_rxps = ($r_rxpt && $r_rxpc);
    $r_chd                     = new stdClass();
    $r_chd->name               = $r_qst->name;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_chd->category = $r_qst->category;
    }
    $r_chd->questiontext       = $r_fld;
    $r_chd->questiontextformat = $r_qst->questiontextformat;
    $r_chd->questiontext = clean_param($r_chd->questiontext, PARAM_RAW);
    $r_chd->answer         = array();
    $r_chd->answerformat   = array();
    $r_chd->fraction       = array();
    $r_chd->feedback       = array();
    $r_chd->feedbackformat = array();
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_chd->defaultmark = 1;
    } else {
        $r_chd->defaultgrade = 1;
    }
    $r_st  = 1;
    $r_ofs = strpos(substr($r_fld, $r_st), ":");
    if ($r_ofs === false) {
        return false;
    }
    if ($r_ofs > 0) {
        $r_sbf = trim(substr($r_fld, $r_st, $r_ofs));
        if (strlen($r_sbf) > 0 && is_numeric($r_sbf)) {
            if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
                $r_chd->defaultmark = floatval($r_sbf);
            } else {
                $r_chd->defaultgrade = floatval($r_sbf);
            }
        }
    }
    $r_st += $r_ofs;
    $r_sbf = substr($r_fld, $r_st);
    if (strncmp($r_sbf, ":NUMERICAL:", 11) == 0
        || strncmp($r_sbf, ":NM:", 4) == 0
    ) {
        $r_chd->qtype              = RWSNUM;
        $r_chd->tolerance          = array();
        $r_chd->multiplier         = array();
        $r_chd->units              = array();
        $r_chd->instructions       = "";
        $r_chd->instructionsformat = FORMAT_HTML;
    } else if (strncmp($r_sbf, ":SHORTANSWER:", 13) == 0
        || strncmp($r_sbf, ":SA:", 4) == 0
        || strncmp($r_sbf, ":MW:", 4) == 0
    ) {
        $r_chd->qtype   = RWSSHA;
        $r_chd->usecase = 0;
    } else if (strncmp($r_sbf, ":SHORTANSWER_C:", 15) == 0
        || strncmp($r_sbf, ":SAC:", 5) == 0
        || strncmp($r_sbf, ":MWC:", 5) == 0
    ) {
        $r_chd->qtype   = RWSSHA;
        $r_chd->usecase = 1;
    } else if (strncmp($r_sbf, ":MULTICHOICE:", 13) == 0
        || strncmp($r_sbf, ":MC:", 4) == 0
    ) {
        $r_chd->qtype                    = RWSMCH;
        $r_chd->single                   = 1;
        $r_chd->answernumbering          = 0;
        $r_chd->shuffleanswers           = 1;
        $r_chd->correctfeedback          = "";
        $r_chd->correctfeedbackformat    = FORMAT_HTML;
        $r_chd->partiallycorrectfeedback = "";
        if (strlen($RWSPFNAME) > 0) {
            $r_chd->$RWSPFNAME = FORMAT_HTML;
        }
        $r_chd->incorrectfeedback       = "";
        $r_chd->incorrectfeedbackformat = FORMAT_HTML;
        if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
            $r_chd->shownumcorrect = 0;
        }
        $r_chd->layout = 0;
    } else if (strncmp($r_sbf, ":MULTICHOICE_V:", 15) == 0
        || strncmp($r_sbf, ":MCV:", 5) == 0
    ) {
        $r_chd->qtype                    = RWSMCH;
        $r_chd->single                   = 1;
        $r_chd->answernumbering          = 0;
        $r_chd->shuffleanswers           = 1;
        $r_chd->correctfeedback          = "";
        $r_chd->correctfeedbackformat    = FORMAT_HTML;
        $r_chd->partiallycorrectfeedback = "";
        if (strlen($RWSPFNAME) > 0) {
            $r_chd->$RWSPFNAME = FORMAT_HTML;
        }
        $r_chd->incorrectfeedback       = "";
        $r_chd->incorrectfeedbackformat = FORMAT_HTML;
        if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
            $r_chd->shownumcorrect = 0;
        }
        $r_chd->layout = 1;
    } else if (strncmp($r_sbf, ":MULTICHOICE_H:", 15) == 0
        || strncmp($r_sbf, ":MCH:", 5) == 0
    ) {
        $r_chd->qtype                    = RWSMCH;
        $r_chd->single                   = 1;
        $r_chd->answernumbering          = 0;
        $r_chd->shuffleanswers           = 1;
        $r_chd->correctfeedback          = "";
        $r_chd->correctfeedbackformat    = FORMAT_HTML;
        $r_chd->partiallycorrectfeedback = "";
        if (strlen($RWSPFNAME) > 0) {
            $r_chd->$RWSPFNAME = FORMAT_HTML;
        }
        $r_chd->incorrectfeedback       = "";
        $r_chd->incorrectfeedbackformat = FORMAT_HTML;
        if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
            $r_chd->shownumcorrect = 0;
        }
        $r_chd->layout = 2;
    } else if ($r_rxps
        && strncmp($r_sbf, ":REGEXP:", 8) == 0
    ) {
        $r_chd->qtype   = RWSRXP;
        $r_chd->usehint = 0;
    } else {
        return false;
    }
    $r_st++;
    $r_ofs = strpos(substr($r_fld, $r_st), ":");
    $r_st += $r_ofs;
    $r_st++;
    $r_fln = strlen($r_fld);
    while ($r_st < $r_fln) {
        if ($r_fld[$r_st] == '}') {
            break;
        }
        if ($r_fld[$r_st] == '~') {
            $r_st++;
        }
        $r_fra = "0";
        if ($r_fld[$r_st] == '=') {
            $r_fra = "1";
            $r_st++;
        }
        if ($r_fld[$r_st] == '%') {
            $r_st++;
            $r_pct = "";
            while ($r_st < $r_fln) {
                if ($r_fld[$r_st] == '%') {
                    break;
                }
                $r_pct .= $r_fld[$r_st];
                $r_st++;
            }
            $r_pct = trim($r_pct);
            if (strlen($r_pct) == 0 || !ctype_digit($r_pct)) {
                return false;
            }
            $r_fra = .01 * $r_pct;
            $r_st++;
        }
        $r_asr = "";
        if ($r_chd->qtype == RWSNUM) {
            $r_tol = "";
            $r_fnd     = false;
            while ($r_st < $r_fln) {
                if ($r_fld[$r_st] == '#'
                    || $r_fld[$r_st] == '~'
                    || $r_fld[$r_st] == '}'
                ) {
                    break;
                } else if ($r_fld[$r_st] == ':') {
                    $r_fnd = true;
                    $r_st++;
                    continue;
                }
                if ($r_fnd) {
                    $r_tol .= $r_fld[$r_st];
                } else {
                    $r_asr .= $r_fld[$r_st];
                }
                $r_st++;
            }
            $r_asr = trim($r_asr);
            if (strlen($r_asr) == 0) {
                return false;
            }
            if (($r_asr != strval(floatval($r_asr))) && $r_asr != "*") {
                return false;
            }
            $r_asr = clean_param($r_asr, PARAM_RAW);
            $r_tol = trim($r_tol);
            if (strlen($r_tol) == 0
                || ($r_tol != strval(floatval($r_tol)))
                || $r_asr == "*"
            ) {
                $r_tol = 0;
            }
        } else {
            $r_itg = false;
            while ($r_st < $r_fln) {
                if ($r_fld[$r_st] == '<') {
                    $r_itg = true;
                } else if ($r_fld[$r_st] == '>') {
                    $r_itg = false;
                } else if (!$r_itg &&
                    ($r_fld[$r_st] == '#'
                        || $r_fld[$r_st] == '~'
                        || $r_fld[$r_st] == '}')
                ) {
                    $r_st--;
                    $r_esc = ($r_fld[$r_st] == '\\');
                    $r_st++;
                    if (!$r_esc) {
                        break;
                    }
                }
                $r_asr .= $r_fld[$r_st];
                $r_st++;
            }
            $r_asr = trim($r_asr);
            if (strlen($r_asr) == 0) {
                return false;
            }
            $r_asr = str_replace("\#", "#", $r_asr);
            $r_asr = str_replace("\}", "}", $r_asr);
            $r_asr = str_replace("\~", "~", $r_asr);
            $r_asr = clean_param($r_asr, PARAM_RAW);
        }
        $r_fb = "";
        if ($r_fld[$r_st] == '#') {
            $r_st++;
            $r_fb = "";
            $r_itg   = false;
            while ($r_st < $r_fln) {
                if ($r_fld[$r_st] == '<') {
                    $r_itg = true;
                } else if ($r_fld[$r_st] == '>') {
                    $r_itg = false;
                } else if (!$r_itg &&
                    ($r_fld[$r_st] == '~'
                        || $r_fld[$r_st] == '}')
                ) {
                    $r_st--;
                    $r_esc = ($r_fld[$r_st] == '\\');
                    $r_st++;
                    if (!$r_esc) {
                        break;
                    }
                }
                $r_fb .= $r_fld[$r_st];
                $r_st++;
            }
            $r_fb = trim($r_fb);
            $r_fb = str_replace("\#", "#", $r_fb);
            $r_fb = str_replace("\}", "}", $r_fb);
            $r_fb = str_replace("\~", "~", $r_fb);
            $r_fb = clean_param($r_fb, PARAM_RAW);
        }
        $r_chd->answer[] = $r_asr;
        if ($r_chd->qtype == RWSNUM
            || $r_chd->qtype == RWSSHA
            || $r_chd->qtype == RWSRXP
        ) {
            $r_chd->answerformat[] = FORMAT_PLAIN;
        } else {
            $r_chd->answerformat[] = FORMAT_HTML;
        }
        $r_chd->fraction[]       = $r_fra;
        $r_chd->feedback[]       = $r_fb;
        $r_chd->feedbackformat[] = FORMAT_HTML;
        if ($r_chd->qtype == RWSNUM) {
            $r_chd->tolerance[] = $r_tol;
        }
    }
    $r_na = count($r_chd->answer);
    if ($r_na == 0) {
        return false;
    }
    if (count($r_chd->fraction) != $r_na) {
        return false;
    }
    if (count($r_chd->feedback) != $r_na) {
        return false;
    }
    if ($r_chd->qtype == RWSNUM && count($r_chd->tolerance) != $r_na) {
        return false;
    }
    return $r_chd;
}
function RWSGCFields($r_qstx) {
    $r_p      = 0;
    $r_l      = strlen($r_qstx);
    $r_itg   = false;
    $r_ifd = false;
    $r_flds   = array();
    while ($r_p < $r_l) {
        if ($r_qstx[$r_p] == '<') {
            $r_itg = true;
        } else if ($r_qstx[$r_p] == '>') {
            $r_itg = false;
        } else if (!$r_ifd && !$r_itg && $r_qstx[$r_p] == '{') {
            $r_esc = false;
            if ($r_p > 0) {
                $r_p--;
                $r_esc = ($r_qstx[$r_p] == '\\');
                $r_p++;
            }
            if (!$r_esc) {
                $r_fld    = "";
                $r_ifd = true;
            }
        } else if ($r_ifd && !$r_itg && $r_qstx[$r_p] == '}') {
            $r_p--;
            $r_esc = ($r_qstx[$r_p] == '\\');
            $r_p++;
            if (!$r_esc) {
                $r_fld .= $r_qstx[$r_p];
                $r_flds[] = $r_fld;
                $r_ifd = false;
            }
        }
        if ($r_ifd) {
            $r_fld .= $r_qstx[$r_p];
        }
        $r_p++;
    }
    return $r_flds;
}
function RWSCChild($r_chd, $r_cid, $r_qci, $r_ctxi) {
    global $CFG;
    global $DB;
    global $RWSUID;
    global $RWSPFNAME;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_chd->hidden = 0;
    }
    $r_chd->length                = 1;
    $r_chd->stamp                 = make_unique_id_code();
    $r_chd->createdby             = $RWSUID;
    $r_chd->modifiedby            = $RWSUID;
    $r_chd->penalty               = 0;
    $r_chd->generalfeedback       = "";
    $r_chd->generalfeedbackformat = FORMAT_HTML;
    $r_chd->timecreated           = time();
    $r_chd->timemodified          = time();
    if ($r_chd->qtype == RWSNUM) {
        $r_chd->id = $DB->insert_record("question", $r_chd);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
            $r_qb = new \stdClass();
            $r_qb->questioncategoryid = $r_qci;
            $r_qb->idnumber = null;
            $r_qb->ownerid = $r_chd->createdby;
            $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
            $r_qv = new \stdClass();
            $r_qv->questionbankentryid = $r_qb->id;
            $r_qv->questionid = $r_chd->id;
            $r_qv->version = get_next_version($r_qb->id);
            $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
            $r_qv->status = $r_sts;
            $r_qv->id = $DB->insert_record('question_versions', $r_qv);
        } else {
        }
        $r_cmp           = "question";
        $r_far            = "questiontext";
        $r_iti              = $r_chd->id;
        $r_txt                = $r_chd->questiontext;
        $r_chd->questiontext = RWSPAtt($r_chd->parent_qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $DB->update_record("question", $r_chd);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        } else {
            $r_h = question_hash($r_chd);
            $DB->set_field("question", "version", $r_h, array("id" => $r_chd->id));
        }
        $r_na = count($r_chd->answer);
        for ($r_i = 0; $r_i < $r_na; $r_i++) {
            $r_an                 = new stdClass();
            $r_an->answer         = $r_chd->answer[$r_i];
            $r_an->answerformat   = $r_chd->answerformat[$r_i];
            $r_an->fraction       = $r_chd->fraction[$r_i];
            $r_an->feedback       = $r_chd->feedback[$r_i];
            $r_an->feedbackformat = $r_chd->feedbackformat[$r_i];
            $r_an->question       = $r_chd->id;
            $r_an->id             = $DB->insert_record("question_answers", $r_an);
            $r_cmp     = "question";
            $r_far      = "answerfeedback";
            $r_iti        = $r_an->id;
            $r_txt          = $r_an->feedback;
            $r_an->feedback = RWSPAtt($r_chd->parent_qtype,
                $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
            );
            $DB->update_record("question_answers", $r_an);
            $r_op            = new stdClass();
            $r_op->question  = $r_chd->id;
            $r_op->answer    = $r_an->id;
            $r_op->tolerance = $r_chd->tolerance[$r_i];
            $r_op->id        = $DB->insert_record("question_numerical", $r_op);
        }
    } else if ($r_chd->qtype == RWSSHA) {
        $r_chd->id = $DB->insert_record("question", $r_chd);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
            $r_qb = new \stdClass();
            $r_qb->questioncategoryid = $r_qci;
            $r_qb->idnumber = null;
            $r_qb->ownerid = $r_chd->createdby;
            $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
            $r_qv = new \stdClass();
            $r_qv->questionbankentryid = $r_qb->id;
            $r_qv->questionid = $r_chd->id;
            $r_qv->version = get_next_version($r_qb->id);
            $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
            $r_qv->status = $r_sts;
            $r_qv->id = $DB->insert_record('question_versions', $r_qv);
        } else {
        }
        $r_cmp           = "question";
        $r_far            = "questiontext";
        $r_iti              = $r_chd->id;
        $r_txt                = $r_chd->questiontext;
        $r_chd->questiontext = RWSPAtt($r_chd->parent_qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $DB->update_record("question", $r_chd);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        } else {
            $r_h = question_hash($r_chd);
            $DB->set_field("question", "version", $r_h, array("id" => $r_chd->id));
        }
        $r_aid  = array();
        $r_na = count($r_chd->answer);
        for ($r_i = 0; $r_i < $r_na; $r_i++) {
            $r_an                 = new stdClass();
            $r_an->answer         = $r_chd->answer[$r_i];
            $r_an->answerformat   = $r_chd->answerformat[$r_i];
            $r_an->fraction       = $r_chd->fraction[$r_i];
            $r_an->feedback       = $r_chd->feedback[$r_i];
            $r_an->feedbackformat = $r_chd->feedbackformat[$r_i];
            $r_an->question       = $r_chd->id;
            $r_an->id             = $DB->insert_record("question_answers", $r_an);
            $r_cmp     = "question";
            $r_far      = "answerfeedback";
            $r_iti        = $r_an->id;
            $r_txt          = $r_an->feedback;
            $r_an->feedback = RWSPAtt($r_chd->parent_qtype,
                $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
            );
            $DB->update_record("question_answers", $r_an);
            $r_aid[] = $r_an->id;
        }
        $r_op          = new stdClass();
        $r_op->usecase = $r_chd->usecase;
        if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
            $r_op->questionid = $r_chd->id;
            $r_op->id         = $DB->insert_record("qtype_shortanswer_options", $r_op);
        } else {
            $r_op->question = $r_chd->id;
            $r_op->answers  = implode(",", $r_aid);
            $r_op->id       = $DB->insert_record("question_shortanswer", $r_op);
        }
    } else if ($r_chd->qtype == RWSMCH) {
        $r_chd->id = $DB->insert_record("question", $r_chd);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
            $r_qb = new \stdClass();
            $r_qb->questioncategoryid = $r_qci;
            $r_qb->idnumber = null;
            $r_qb->ownerid = $r_chd->createdby;
            $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
            $r_qv = new \stdClass();
            $r_qv->questionbankentryid = $r_qb->id;
            $r_qv->questionid = $r_chd->id;
            $r_qv->version = get_next_version($r_qb->id);
            $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
            $r_qv->status = $r_sts;
            $r_qv->id = $DB->insert_record('question_versions', $r_qv);
        } else {
        }
        $r_cmp           = "question";
        $r_far            = "questiontext";
        $r_iti              = $r_chd->id;
        $r_txt                = $r_chd->questiontext;
        $r_chd->questiontext = RWSPAtt($r_chd->parent_qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $DB->update_record("question", $r_chd);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        } else {
            $r_h = question_hash($r_chd);
            $DB->set_field("question", "version", $r_h, array("id" => $r_chd->id));
        }
        $r_aid  = array();
        $r_na = count($r_chd->answer);
        for ($r_i = 0; $r_i < $r_na; $r_i++) {
            $r_an                 = new stdClass();
            $r_an->answer         = $r_chd->answer[$r_i];
            $r_an->answerformat   = $r_chd->answerformat[$r_i];
            $r_an->fraction       = $r_chd->fraction[$r_i];
            $r_an->feedback       = $r_chd->feedback[$r_i];
            $r_an->feedbackformat = $r_chd->feedbackformat[$r_i];
            $r_an->question       = $r_chd->id;
            $r_an->id             = $DB->insert_record("question_answers", $r_an);
            $r_cmp   = "question";
            $r_far    = "answer";
            $r_iti      = $r_an->id;
            $r_txt        = $r_an->answer;
            $r_an->answer = RWSPAtt($r_chd->parent_qtype,
                $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
            );
            $r_cmp     = "question";
            $r_far      = "answerfeedback";
            $r_iti        = $r_an->id;
            $r_txt          = $r_an->feedback;
            $r_an->feedback = RWSPAtt($r_chd->parent_qtype,
                $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
            );
            $DB->update_record("question_answers", $r_an);
            $r_aid[] = $r_an->id;
        }
        $r_op                           = new stdClass();
        $r_op->single                   = $r_chd->single;
        $r_op->answernumbering          = $r_chd->answernumbering;
        $r_op->shuffleanswers           = $r_chd->shuffleanswers;
        $r_op->correctfeedback          = $r_chd->correctfeedback;
        $r_op->partiallycorrectfeedback = $r_chd->partiallycorrectfeedback;
        $r_op->incorrectfeedback        = $r_chd->incorrectfeedback;
        if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
            $r_op->shownumcorrect = $r_chd->shownumcorrect;
        }
        $r_op->layout = $r_chd->layout;
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_op->questionid = $r_chd->id;
            $r_op->id         = $DB->insert_record("qtype_multichoice_options", $r_op);
        } else {
            $r_op->question = $r_chd->id;
            $r_op->answers  = implode(",", $r_aid);
            $r_op->id       = $DB->insert_record("question_multichoice", $r_op);
        }
    } else if ($r_chd->qtype == RWSRXP) {
        $r_chd->id = $DB->insert_record("question", $r_chd);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
            $r_qb = new \stdClass();
            $r_qb->questioncategoryid = $r_qci;
            $r_qb->idnumber = null;
            $r_qb->ownerid = $r_chd->createdby;
            $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
            $r_qv = new \stdClass();
            $r_qv->questionbankentryid = $r_qb->id;
            $r_qv->questionid = $r_chd->id;
            $r_qv->version = get_next_version($r_qb->id);
            $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
            $r_qv->status = $r_sts;
            $r_qv->id = $DB->insert_record('question_versions', $r_qv);
        } else {
            $r_h = question_hash($r_chd);
        }
        $r_cmp           = "question";
        $r_far            = "questiontext";
        $r_iti              = $r_chd->id;
        $r_txt                = $r_chd->questiontext;
        $r_chd->questiontext = RWSPAtt($r_chd->parent_qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $DB->update_record("question", $r_chd);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        } else {
            $DB->set_field("question", "version", $r_h, array("id" => $r_chd->id));
        }
        $r_aid  = array();
        $r_na = count($r_chd->answer);
        for ($r_i = 0; $r_i < $r_na; $r_i++) {
            $r_an                 = new stdClass();
            $r_an->answer         = $r_chd->answer[$r_i];
            $r_an->answerformat   = $r_chd->answerformat[$r_i];
            $r_an->fraction       = $r_chd->fraction[$r_i];
            $r_an->feedback       = $r_chd->feedback[$r_i];
            $r_an->feedbackformat = $r_chd->feedbackformat[$r_i];
            $r_an->question       = $r_chd->id;
            $r_an->id             = $DB->insert_record("question_answers", $r_an);
            $r_cmp     = "question";
            $r_far      = "answerfeedback";
            $r_iti        = $r_an->id;
            $r_txt          = $r_an->feedback;
            $r_an->feedback = RWSPAtt($r_chd->parent_qtype,
                $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
            );
            $DB->update_record("question_answers", $r_an);
            $r_aid[] = $r_an->id;
        }
        $r_op           = new stdClass();
        $r_op->question = $r_chd->id;
        $r_op->answers  = implode(",", $r_aid);
        $r_op->id       = $DB->insert_record("question_regexp", $r_op);
    } else {
        return false;
    }
    return $r_chd->id;
}
function RWSICRec($r_cid, $r_qci, $r_rcd) {
    global $CFG;
    global $DB;
    global $RWSUID;
    global $RWSPFNAME;
    if (RWSGQRType($r_rcd) != RWSCAL) {
        return false;
    }
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_ctxi = $DB->get_field("question_categories", "contextid",
        array("id" => $r_qci));
    $r_qst             = new stdClass();
    $r_qst->qtype      = RWSCAL;
    $r_qst->parent     = 0;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_qst->hidden   = 0;
        $r_qst->category = $r_qci;
    }
    $r_qst->length     = 1;
    $r_qst->stamp      = make_unique_id_code();
    $r_qst->createdby  = $RWSUID;
    $r_qst->modifiedby = $RWSUID;
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->name = trim($r_fld);
    if (strlen($r_qst->name) == 0) {
        return false;
    }
    $r_qst->name = clean_param($r_qst->name, PARAM_TEXT);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->questiontext       = trim($r_fld);
    $r_qst->questiontextformat = FORMAT_HTML;
    $r_qst->questiontext = clean_param($r_qst->questiontext, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("N", $r_fld);
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qst->defaultmark = $r_dat[1];
    } else {
        $r_qst->defaultgrade = $r_dat[1];
    }
    $r_ct = 8;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->penalty = RWSDblIn($r_fld);
    if ($r_qst->penalty < 0 || $r_qst->penalty > 1) {
        return false;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        switch (strval($r_qst->penalty)) {
            case "1":
            case "0.5":
            case "0.3333333":
            case "0.25":
            case "0.2":
            case "0.1":
            case "0":
                break;
            default:
                $r_qst->penalty = "0.3333333";
                break;
        }
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->generalfeedback       = trim($r_fld);
    $r_qst->generalfeedbackformat = FORMAT_HTML;
    $r_qst->generalfeedback = clean_param($r_qst->generalfeedback, PARAM_RAW);
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat        = unpack("C", $r_fld);
    $r_na = intval($r_dat[1]);
    if ($r_na != 1) {
        return false;
    }
    $r_asrs        = array();
    $r_tf = 0;
    for ($r_i = 0; $r_i < $r_na; $r_i++) {
        $r_an = new stdClass();
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_an->formula = trim($r_fld);
        if (strlen($r_an->formula) == 0) {
            return false;
        }
        if (!RWSCFSyn($r_an->formula)) {
            return false;
        }
        $r_ct = 8;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_an->fraction = strval(RWSDblIn($r_fld));
        switch ($r_an->fraction) {
            case "1":
            case "0.9":
            case "0.8333333":
            case "0.8":
            case "0.75":
            case "0.7":
            case "0.6666667":
            case "0.6":
            case "0.5":
            case "0.4":
            case "0.3333333":
            case "0.3":
            case "0.25":
            case "0.2":
            case "0.1666667":
            case "0.1428571":
            case "0.125":
            case "0.1111111":
            case "0.1":
            case "0.05":
            case "0":
                break;
            default:
                if (respondusws_floatcompare($r_bv, 2011020100, 2) >= 0) {
                    $r_asr->fraction = "0";
                }
                break;
        }
        if (respondusws_floatcompare($r_bv, 2011020100, 2) == -1) {
            switch ($r_asr->fraction) {
                case "0.83333":
                    $r_asr->fraction = "0.8333333";
                    break;
                case "0.66666":
                    $r_asr->fraction = "0.6666667";
                    break;
                case "0.33333":
                    $r_asr->fraction = "0.3333333";
                    break;
                case "0.16666":
                    $r_asr->fraction = "0.1666667";
                    break;
                case "0.142857":
                    $r_asr->fraction = "0.1428571";
                    break;
                case "0.11111":
                    $r_asr->fraction = "0.1111111";
                    break;
                default:
                    $r_asr->fraction = "0";
                    break;
            }
        }
        if ($r_an->fraction != "1") {
            return false;
        }
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_an->feedback       = trim($r_fld);
        $r_an->feedbackformat = FORMAT_HTML;
        $r_an->feedback = clean_param($r_an->feedback, PARAM_RAW);
        $r_ct = 8;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_an->tolerance = RWSDblIn($r_fld);
        $r_ct = 1;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_dat               = unpack("C", $r_fld);
        $r_an->tolerancetype = intval($r_dat[1]);
        switch ($r_an->tolerancetype) {
            case 1:
            case 2:
            case 3:
                break;
            default:
                return false;
        }
        $r_ct = 1;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_dat                     = unpack("C", $r_fld);
        $r_an->correctanswerlength = intval($r_dat[1]);
        if ($r_an->correctanswerlength < 0 || $r_an->correctanswerlength > 9) {
            return false;
        }
        $r_ct = 1;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_dat                     = unpack("C", $r_fld);
        $r_an->correctanswerformat = intval($r_dat[1]);
        switch ($r_an->correctanswerformat) {
            case 1:
            case 2:
                break;
            default:
                return false;
        }
        $r_asrs[] = $r_an;
        $r_tf += $r_an->fraction;
    }
    if (count($r_asrs) != 1) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat      = unpack("C", $r_fld);
    $r_nu = intval($r_dat[1]);
    if ($r_nu < 0 || $r_nu > 1) {
        return false;
    }
    $r_uts           = array();
    $r_fbu = false;
    for ($r_i = 0; $r_i < $r_nu; $r_i++) {
        $r_ut = new stdClass();
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_ut->name = trim($r_fld);
        if (strlen($r_ut->name) == 0) {
            return false;
        }
        $r_ut->name = clean_param($r_ut->name, PARAM_NOTAGS);
        $r_ct = 8;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_ut->multiplier = RWSDblIn($r_fld);
        if (respondusws_floatcompare($r_ut->multiplier, 1, 1) == 0) {
            $r_fbu = true;
        } else {
            return false;
        }
        $r_uts[] = $r_ut;
    }
    if (count($r_uts) > 1) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat      = unpack("C", $r_fld);
    $r_nd = intval($r_dat[1]);
    if ($r_nd < 1) {
        return false;
    }
    $r_dset = array();
    for ($r_i = 0; $r_i < $r_nd; $r_i++) {
        $r_ds = new stdClass();
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_ds->name = trim($r_fld);
        if (strlen($r_ds->name) == 0) {
            return false;
        }
        $r_ds->name = clean_param($r_ds->name, PARAM_NOTAGS);
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_ds->distribution = trim($r_fld);
        switch ($r_ds->distribution) {
            case "uniform":
            case "loguniform":
                break;
            default:
                return false;
        }
        if ($r_ds->distribution != "uniform") {
            return false;
        }
        $r_ct = 8;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_ds->min = RWSDblIn($r_fld);
        $r_ct = 8;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_ds->max = RWSDblIn($r_fld);
        $r_ct = 1;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_dat            = unpack("C", $r_fld);
        $r_ds->precision = intval($r_dat[1]);
        if ($r_ds->precision < 0 || $r_ds->precision > 10) {
            return false;
        }
        if (respondusws_floatcompare($r_ds->max, $r_ds->min, $r_ds->precision) < 0) {
            return false;
        }
        $r_ct = 1;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_dat       = unpack("C", $r_fld);
        $r_ds->type = intval($r_dat[1]);
        if ($r_ds->type != 1) {
            return false;
        }
        $r_ct = 1;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_dat         = unpack("C", $r_fld);
        $r_ds->status = intval($r_dat[1]);
        if ($r_ds->status != 0 && $r_ds->status != 1) {
            return false;
        }
        $r_ct = 1;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_dat            = unpack("C", $r_fld);
        $r_ds->itemcount = intval($r_dat[1]);
        if ($r_ds->itemcount < 1) {
            return false;
        }
        $r_ds->items = array();
        $r_map         = array_fill(1, $r_ds->itemcount, 0);
        for ($r_j = 0; $r_j < $r_ds->itemcount; $r_j++) {
            $r_it = new stdClass();
            $r_ct = 1;
            if ($r_sz < $r_ct) {
                return false;
            }
            $r_fld = substr($r_rcd, $r_p, $r_ct);
            $r_p += $r_ct;
            $r_sz -= $r_ct;
            $r_dat             = unpack("C", $r_fld);
            $r_it->itemnumber = intval($r_dat[1]);
            if ($r_it->itemnumber < 1 || $r_it->itemnumber > $r_ds->itemcount) {
                return false;
            }
            if ($r_map[$r_it->itemnumber] == 1) {
                return false;
            }
            $r_map[$r_it->itemnumber] = 1;
            $r_ct = 8;
            if ($r_sz < $r_ct) {
                return false;
            }
            $r_fld = substr($r_rcd, $r_p, $r_ct);
            $r_p += $r_ct;
            $r_sz -= $r_ct;
            $r_it->value = RWSDblIn($r_fld);
            $r_ds->items[] = $r_it;
        }
        if (array_sum($r_map) != $r_ds->itemcount) {
            return false;
        }
        $r_dset[] = $r_ds;
    }
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->timecreated  = time();
    $r_qst->timemodified = time();
    $r_qst->id           = $DB->insert_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qb = new \stdClass();
        $r_qb->questioncategoryid = $r_qci;
        $r_qb->idnumber = null;
        $r_qb->ownerid = $r_qst->createdby;
        $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
        $r_qv = new \stdClass();
        $r_qv->questionbankentryid = $r_qb->id;
        $r_qv->questionid = $r_qst->id;
        $r_qv->version = get_next_version($r_qb->id);
        $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $r_qv->status = $r_sts;
        $r_qv->id = $DB->insert_record('question_versions', $r_qv);
    } else {
    }
    $r_cmp              = "question";
    $r_far               = "questiontext";
    $r_iti                 = $r_qst->id;
    $r_txt                   = $r_qst->questiontext;
    $r_qst->questiontext = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                 = "question";
    $r_far                  = "generalfeedback";
    $r_iti                    = $r_qst->id;
    $r_txt                      = $r_qst->generalfeedback;
    $r_qst->generalfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_h = question_hash($r_qst);
        $DB->set_field("question", "version", $r_h, array("id" => $r_qst->id));
    }
    $r_op                           = new stdClass();
    $r_op->question                 = $r_qst->id;
    $r_op->synchronize              = 0;
    $r_op->single                   = 0;
    $r_op->answernumbering          = "abc";
    $r_op->shuffleanswers           = 0;
    $r_op->correctfeedback          = "";
    $r_op->correctfeedbackformat    = FORMAT_HTML;
    $r_op->partiallycorrectfeedback = "";
    if (strlen($RWSPFNAME) > 0) {
        $r_op->$RWSPFNAME = FORMAT_HTML;
    }
    $r_op->incorrectfeedback       = "";
    $r_op->incorrectfeedbackformat = FORMAT_HTML;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_op->shownumcorrect = 0;
    }
    $r_op->id = $DB->insert_record("question_calculated_options", $r_op);
    foreach ($r_asrs as $a) {
        $r_an                 = new stdClass();
        $r_an->answer         = $a->formula;
        $r_an->fraction       = $a->fraction;
        $r_an->feedback       = $a->feedback;
        $r_an->feedbackformat = $a->feedbackformat;
        $r_an->question       = $r_qst->id;
        $r_an->id             = $DB->insert_record("question_answers", $r_an);
        $r_cmp     = "question";
        $r_far      = "answerfeedback";
        $r_iti        = $r_an->id;
        $r_txt          = $r_an->feedback;
        $r_an->feedback = RWSPAtt($r_qst->qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $DB->update_record("question_answers", $r_an);
        $r_o                      = new stdClass();
        $r_o->tolerance           = $a->tolerance;
        $r_o->tolerancetype       = $a->tolerancetype;
        $r_o->correctanswerlength = $a->correctanswerlength;
        $r_o->correctanswerformat = $a->correctanswerformat;
        $r_o->question            = $r_qst->id;
        $r_o->answer              = $r_an->id;
        $r_o->id                  = $DB->insert_record("question_calculated", $r_o);
    }
    foreach ($r_uts as $r_u) {
        $r_ut             = new stdClass();
        $r_ut->unit       = $r_u->name;
        $r_ut->multiplier = $r_u->multiplier;
        $r_ut->question   = $r_qst->id;
        $r_ut->id         = $DB->insert_record("question_numerical_units", $r_ut);
    }
    $r_o              = new stdClass();
    $r_o->question    = $r_qst->id;
    $r_o->unitpenalty = 0.1;
    if (count($r_uts) > 0) {
        $r_o->unitgradingtype = RWSGRD;
        $r_o->showunits       = RWSUIN;
    } else {
        $r_o->unitgradingtype = RWSOPT;
        $r_o->showunits       = RWSUNO;
    }
    $r_o->unitsleft = 0;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) < 0) {
        $r_o->instructions       = "";
        $r_o->instructionsformat = FORMAT_HTML;
    }
    $r_o->id = $DB->insert_record("question_numerical_options", $r_o);
    foreach ($r_dset as $r_ds) {
        $r_df            = new stdClass();
        $r_df->name      = $r_ds->name;
        $r_df->options   =
            "$r_ds->distribution:$r_ds->min:$r_ds->max:$r_ds->precision";
        $r_df->itemcount = $r_ds->itemcount;
        $r_df->type      = $r_ds->type;
        if ($r_ds->status == 0) {
            $r_df->category = 0;
        } else {
            if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
                $r_df->category = $r_qci;
            } else {
                $r_df->category = $r_qst->category;
            }
        }
        $r_df->id = $DB->insert_record("question_dataset_definitions", $r_df);
        $r_qds                    = new stdClass();
        $r_qds->question          = $r_qst->id;
        $r_qds->datasetdefinition = $r_df->id;
        $r_qds->id                = $DB->insert_record("question_datasets", $r_qds);
        foreach ($r_ds->items as $r_di) {
            $r_it             = new stdClass();
            $r_it->itemnumber = $r_di->itemnumber;
            $r_it->value      = $r_di->value;
            $r_it->definition = $r_df->id;
            $r_it->id         = $DB->insert_record("question_dataset_items", $r_it);
        }
    }
    return $r_qst->id;
}
function RWSCFSyn($r_for) {
    while (preg_match('~\\{[[:alpha:]][^>} <{"\']*\\}~', $r_for, $r_rgs)) {
        $r_for = str_replace($r_rgs[0], '1', $r_for);
    }
    $r_for = strtolower(str_replace(' ', '', $r_for));
    $r_soc = '-+/*%>:^~<?=&|!';
    $r_oon = "[$r_soc.0-9eE]";
    while (preg_match("~(^|[$r_soc,(])([a-z0-9_]*)" .
        "\\(($r_oon+(,$r_oon+((,$r_oon+)+)?)?)?\\)~",
        $r_for, $r_rgs)) {
        switch ($r_rgs[2]) {
            case '':
                if ((isset($r_rgs[4]) && $r_rgs[4]) || strlen($r_rgs[3]) == 0) {
                    return false;
                }
                break;
            case 'pi':
                if ($r_rgs[3]) {
                    return false;
                }
                break;
            case 'abs':
            case 'acos':
            case 'acosh':
            case 'asin':
            case 'asinh':
            case 'atan':
            case 'atanh':
            case 'bindec':
            case 'ceil':
            case 'cos':
            case 'cosh':
            case 'decbin':
            case 'decoct':
            case 'deg2rad':
            case 'exp':
            case 'expm1':
            case 'floor':
            case 'is_finite':
            case 'is_infinite':
            case 'is_nan':
            case 'log10':
            case 'log1p':
            case 'octdec':
            case 'rad2deg':
            case 'sin':
            case 'sinh':
            case 'sqrt':
            case 'tan':
            case 'tanh':
                if (!empty($r_rgs[4]) || empty($r_rgs[3])) {
                    return false;
                }
                break;
            case 'log':
            case 'round':
                if (!empty($r_rgs[5]) || empty($r_rgs[3])) {
                    return false;
                }
                break;
            case 'atan2':
            case 'fmod':
            case 'pow':
                if (!empty($r_rgs[5]) || empty($r_rgs[4])) {
                    return false;
                }
                break;
            case 'min':
            case 'max':
                if (empty($r_rgs[4])) {
                    return false;
                }
                break;
            default:
                return false;
        }
        if ($r_rgs[1]) {
            $r_for = str_replace($r_rgs[0], $r_rgs[1] . '1', $r_for);
        } else {
            $r_for = preg_replace("~^$r_rgs[2]\\([^)]*\\)~", '1', $r_for);
        }
    }
    if (preg_match("~[^$r_soc.0-9eE]+~", $r_for, $r_rgs)) {
        return false;
    } else {
        return true;
    }
}
function RWSIMCRec($r_cid, $r_qci, $r_rcd) {
    global $CFG;
    global $DB;
    global $RWSUID;
    global $RWSPFNAME;
    if (RWSGQRType($r_rcd) != RWSMCH) {
        return false;
    }
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_ctxi = $DB->get_field("question_categories", "contextid",
        array("id" => $r_qci));
    $r_qst             = new stdClass();
    $r_qst->qtype      = RWSMCH;
    $r_qst->parent     = 0;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_qst->hidden   = 0;
        $r_qst->category = $r_qci;
    }
    $r_qst->length     = 1;
    $r_qst->stamp      = make_unique_id_code();
    $r_qst->createdby  = $RWSUID;
    $r_qst->modifiedby = $RWSUID;
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->name = trim($r_fld);
    if (strlen($r_qst->name) == 0) {
        return false;
    }
    $r_qst->name = clean_param($r_qst->name, PARAM_TEXT);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->questiontext       = trim($r_fld);
    $r_qst->questiontextformat = FORMAT_HTML;
    $r_qst->questiontext = clean_param($r_qst->questiontext, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("N", $r_fld);
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qst->defaultmark = $r_dat[1];
    } else {
        $r_qst->defaultgrade = $r_dat[1];
    }
    $r_ct = 8;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->penalty = RWSDblIn($r_fld);
    if ($r_qst->penalty < 0 || $r_qst->penalty > 1) {
        return false;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        switch (strval($r_qst->penalty)) {
            case "1":
            case "0.5":
            case "0.3333333":
            case "0.25":
            case "0.2":
            case "0.1":
            case "0":
                break;
            default:
                $r_qst->penalty = "0.3333333";
                break;
        }
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->generalfeedback       = trim($r_fld);
    $r_qst->generalfeedbackformat = FORMAT_HTML;
    $r_qst->generalfeedback = clean_param($r_qst->generalfeedback, PARAM_RAW);
    $r_op = new stdClass();
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat            = unpack("C", $r_fld);
    $r_op->single = intval($r_dat[1]);
    if ($r_op->single != 0 && $r_op->single != 1) {
        return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("C", $r_fld);
    $r_flg = intval($r_dat[1]);
    if ($r_flg != 0 && $r_flg != 1) {
        return false;
    }
    $r_op->shuffleanswers = (bool)$r_flg;
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_op->answernumbering = trim($r_fld);
    switch ($r_op->answernumbering) {
        case "abc":
        case "ABCD":
        case "123":
        case "none":
            break;
        default:
            return false;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat        = unpack("C", $r_fld);
    $r_na = intval($r_dat[1]);
    if ($r_na < 2) {
        return false;
    }
    $r_asrs        = array();
    $r_tf = 0;
    $r_mf   = -1;
    for ($r_i = 0; $r_i < $r_na; $r_i++) {
        $r_asr = new stdClass();
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_asr->answer       = trim($r_fld);
        $r_asr->answerformat = FORMAT_HTML;
        $r_asr->answer = clean_param($r_asr->answer, PARAM_RAW);
        $r_ct = 8;
        if ($r_sz < $r_ct) {
            return false;
        }
        $r_fld = substr($r_rcd, $r_p, $r_ct);
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_asr->fraction = strval(RWSDblIn($r_fld));
        switch ($r_asr->fraction) {
            case "1":
            case "0.9":
            case "0.8333333":
            case "0.8":
            case "0.75":
            case "0.7":
            case "0.6666667":
            case "0.6":
            case "0.5":
            case "0.4":
            case "0.3333333":
            case "0.3":
            case "0.25":
            case "0.2":
            case "0.1666667":
            case "0.1428571":
            case "0.125":
            case "0.1111111":
            case "0.1":
            case "0.05":
            case "0":
            case "-0.05":
            case "-0.1":
            case "-0.1111111":
            case "-0.125":
            case "-0.1428571":
            case "-0.1666667":
            case "-0.2":
            case "-0.25":
            case "-0.3":
            case "-0.3333333":
            case "-0.4":
            case "-0.5":
            case "-0.6":
            case "-0.6666667":
            case "-0.7":
            case "-0.75":
            case "-0.8":
            case "-0.8333333":
            case "-0.9":
            case "-1":
                break;
            default:
                if (respondusws_floatcompare($r_bv, 2011020100, 2) >= 0) {
                    $r_asr->fraction = "0";
                }
                break;
        }
        if (respondusws_floatcompare($r_bv, 2011020100, 2) == -1) {
            switch ($r_asr->fraction) {
                case "0.83333":
                    $r_asr->fraction = "0.8333333";
                    break;
                case "0.66666":
                    $r_asr->fraction = "0.6666667";
                    break;
                case "0.33333":
                    $r_asr->fraction = "0.3333333";
                    break;
                case "0.16666":
                    $r_asr->fraction = "0.1666667";
                    break;
                case "0.142857":
                    $r_asr->fraction = "0.1428571";
                    break;
                case "0.11111":
                    $r_asr->fraction = "0.1111111";
                    break;
                case "-0.11111":
                    $r_asr->fraction = "-0.1111111";
                    break;
                case "-0.142857":
                    $r_asr->fraction = "-0.1428571";
                    break;
                case "-0.16666":
                    $r_asr->fraction = "-0.1666667";
                    break;
                case "-0.33333":
                    $r_asr->fraction = "-0.3333333";
                    break;
                case "-0.66666":
                    $r_asr->fraction = "-0.6666667";
                    break;
                case "-0.83333":
                    $r_asr->fraction = "-0.8333333";
                    break;
                default:
                    $r_asr->fraction = "0";
                    break;
            }
        }
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_asr->feedback       = trim($r_fld);
        $r_asr->feedbackformat = FORMAT_HTML;
        $r_asr->feedback = clean_param($r_asr->feedback, PARAM_RAW);
        if (strlen($r_asr->answer) == 0) {
            continue;
        }
        $r_asrs[] = $r_asr;
        if ($r_asr->fraction > 0) {
            $r_tf += $r_asr->fraction;
        }
        if ($r_asr->fraction > $r_mf) {
            $r_mf = $r_asr->fraction;
        }
    }
    if (count($r_asrs) < 2) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_op->correctfeedback       = trim($r_fld);
    $r_op->correctfeedbackformat = FORMAT_HTML;
    $r_op->correctfeedback = clean_param($r_op->correctfeedback, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_op->partiallycorrectfeedback = trim($r_fld);
    if (strlen($RWSPFNAME) > 0) {
        $r_op->$RWSPFNAME = FORMAT_HTML;
    }
    $r_op->partiallycorrectfeedback = clean_param($r_op->partiallycorrectfeedback, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_op->incorrectfeedback       = trim($r_fld);
    $r_op->incorrectfeedbackformat = FORMAT_HTML;
    $r_op->incorrectfeedback = clean_param($r_op->incorrectfeedback, PARAM_RAW);
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_op->shownumcorrect = 0;
    }
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->timecreated  = time();
    $r_qst->timemodified = time();
    $r_qst->id           = $DB->insert_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qb = new \stdClass();
        $r_qb->questioncategoryid = $r_qci;
        $r_qb->idnumber = null;
        $r_qb->ownerid = $r_qst->createdby;
        $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
        $r_qv = new \stdClass();
        $r_qv->questionbankentryid = $r_qb->id;
        $r_qv->questionid = $r_qst->id;
        $r_qv->version = get_next_version($r_qb->id);
        $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $r_qv->status = $r_sts;
        $r_qv->id = $DB->insert_record('question_versions', $r_qv);
    } else {
    }
    $r_cmp              = "question";
    $r_far               = "questiontext";
    $r_iti                 = $r_qst->id;
    $r_txt                   = $r_qst->questiontext;
    $r_qst->questiontext = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                 = "question";
    $r_far                  = "generalfeedback";
    $r_iti                    = $r_qst->id;
    $r_txt                      = $r_qst->generalfeedback;
    $r_qst->generalfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_h = question_hash($r_qst);
        $DB->set_field("question", "version", $r_h, array("id" => $r_qst->id));
    }
    $r_aid = array();
    foreach ($r_asrs as $r_an) {
        $r_an->question = $r_qst->id;
        $r_an->id       = $DB->insert_record("question_answers", $r_an);
        $r_cmp   = "question";
        $r_far    = "answer";
        $r_iti      = $r_an->id;
        $r_txt        = $r_an->answer;
        $r_an->answer = RWSPAtt($r_qst->qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $r_cmp     = "question";
        $r_far      = "answerfeedback";
        $r_iti        = $r_an->id;
        $r_txt          = $r_an->feedback;
        $r_an->feedback = RWSPAtt($r_qst->qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $DB->update_record("question_answers", $r_an);
        $r_aid[] = $r_an->id;
    }
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_op->questionid = $r_qst->id;
        $r_op->id       = $DB->insert_record("qtype_multichoice_options", $r_op);
    } else {
        $r_op->question = $r_qst->id;
        $r_op->answers  = implode(",", $r_aid);
        $r_op->id       = $DB->insert_record("question_multichoice", $r_op);
    }
    $r_cmp                = "qtype_multichoice";
    $r_far                 = "correctfeedback";
    $r_iti                   = $r_qst->id;
    $r_txt                     = $r_op->correctfeedback;
    $r_op->correctfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                         = "qtype_multichoice";
    $r_far                          = "partiallycorrectfeedback";
    $r_iti                            = $r_qst->id;
    $r_txt                              = $r_op->partiallycorrectfeedback;
    $r_op->partiallycorrectfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                  = "qtype_multichoice";
    $r_far                   = "incorrectfeedback";
    $r_iti                     = $r_qst->id;
    $r_txt                       = $r_op->incorrectfeedback;
    $r_op->incorrectfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $DB->update_record("qtype_multichoice_options", $r_op);
    } else {
        $DB->update_record("question_multichoice", $r_op);
    }
    return $r_qst->id;
}
function RWSIMRec($r_cid, $r_qci, $r_rcd) {
    global $CFG;
    global $DB;
    global $RWSUID;
    global $RWSPFNAME;
    if (RWSGQRType($r_rcd) != RWSMAT) {
        return false;
    }
    $r_ctxi = $DB->get_field("question_categories", "contextid",
        array("id" => $r_qci));
    $r_qst             = new stdClass();
    $r_qst->qtype      = RWSMAT;
    $r_qst->parent     = 0;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_qst->hidden   = 0;
        $r_qst->category = $r_qci;
    }
    $r_qst->length     = 1;
    $r_qst->stamp      = make_unique_id_code();
    $r_qst->createdby  = $RWSUID;
    $r_qst->modifiedby = $RWSUID;
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->name = trim($r_fld);
    if (strlen($r_qst->name) == 0) {
        return false;
    }
    $r_qst->name = clean_param($r_qst->name, PARAM_TEXT);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->questiontext       = trim($r_fld);
    $r_qst->questiontextformat = FORMAT_HTML;
    $r_qst->questiontext = clean_param($r_qst->questiontext, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("N", $r_fld);
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qst->defaultmark = $r_dat[1];
    } else {
        $r_qst->defaultgrade = $r_dat[1];
    }
    $r_ct = 8;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->penalty = RWSDblIn($r_fld);
    if ($r_qst->penalty < 0 || $r_qst->penalty > 1) {
        return false;
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        switch (strval($r_qst->penalty)) {
            case "1":
            case "0.5":
            case "0.3333333":
            case "0.25":
            case "0.2":
            case "0.1":
            case "0":
                break;
            default:
                $r_qst->penalty = "0.3333333";
                break;
        }
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->generalfeedback       = trim($r_fld);
    $r_qst->generalfeedbackformat = FORMAT_HTML;
    $r_qst->generalfeedback = clean_param($r_qst->generalfeedback, PARAM_RAW);
    $r_op = new stdClass();
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("C", $r_fld);
    $r_flg = intval($r_dat[1]);
    if ($r_flg != 0 && $r_flg != 1) {
        return false;
    }
    $r_op->shuffleanswers = (bool)$r_flg;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_op->correctfeedback          = "";
        $r_op->correctfeedbackformat    = FORMAT_HTML;
        $r_op->partiallycorrectfeedback = "";
        if (strlen($RWSPFNAME) > 0) {
            $r_op->$RWSPFNAME = FORMAT_HTML;
        }
        $r_op->incorrectfeedback       = "";
        $r_op->incorrectfeedbackformat = FORMAT_HTML;
        $r_op->shownumcorrect          = 0;
    }
    $r_ct = 1;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat      = unpack("C", $r_fld);
    $r_np = intval($r_dat[1]);
    if ($r_np < 3) {
        return false;
    }
    $r_prs      = array();
    $r_sbqct = 0;
    for ($r_i = 0; $r_i < $r_np; $r_i++) {
        $r_sbq = new stdClass();
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_sbq->questiontext       = trim($r_fld);
        $r_sbq->questiontextformat = FORMAT_HTML;
        $r_sbq->questiontext = clean_param($r_sbq->questiontext, PARAM_RAW);
        if ($r_sz < 1) {
            return false;
        }
        $r_ct = strpos(substr($r_rcd, $r_p), "\0");
        if ($r_ct === false) {
            return false;
        }
        if ($r_ct > 0) {
            $r_fld = substr($r_rcd, $r_p, $r_ct);
        } else {
            $r_fld = "";
        }
        $r_ct++;
        $r_p += $r_ct;
        $r_sz -= $r_ct;
        $r_sbq->answertext = trim($r_fld);
        $r_sbq->answertext = clean_param($r_sbq->answertext, PARAM_TEXT);
        if (strlen($r_sbq->answertext) == 0) {
            continue;
        }
        if (strlen($r_sbq->questiontext) != 0) {
            $r_sbqct++;
        }
        $r_prs[] = $r_sbq;
    }
    if ($r_sbqct < 2 || count($r_prs) < 3) {
        return false;
    }
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->timecreated  = time();
    $r_qst->timemodified = time();
    $r_qst->id           = $DB->insert_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qb = new \stdClass();
        $r_qb->questioncategoryid = $r_qci;
        $r_qb->idnumber = null;
        $r_qb->ownerid = $r_qst->createdby;
        $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
        $r_qv = new \stdClass();
        $r_qv->questionbankentryid = $r_qb->id;
        $r_qv->questionid = $r_qst->id;
        $r_qv->version = get_next_version($r_qb->id);
        $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $r_qv->status = $r_sts;
        $r_qv->id = $DB->insert_record('question_versions', $r_qv);
    } else {
    }
    $r_cmp              = "question";
    $r_far               = "questiontext";
    $r_iti                 = $r_qst->id;
    $r_txt                   = $r_qst->questiontext;
    $r_qst->questiontext = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                 = "question";
    $r_far                  = "generalfeedback";
    $r_iti                    = $r_qst->id;
    $r_txt                      = $r_qst->generalfeedback;
    $r_qst->generalfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_h = question_hash($r_qst);
        $DB->set_field("question", "version", $r_h, array("id" => $r_qst->id));
    }
    $r_pis = array();
    foreach ($r_prs as $r_pr) {
        if (respondusws_floatcompare($CFG->version, 2013051400, 2) < 0) {
            $r_pr->code = rand(1, 999999999);
            while ($DB->record_exists("question_match_sub", array(
                 "code"     => $r_pr->code,
                 "question" => $r_qst->id
            )) === true) {
                $r_pr->code = rand(1, 999999999);
            }
        }
        if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
            $r_pr->questionid = $r_qst->id;
            $r_pr->id         = $DB->insert_record("qtype_match_subquestions", $r_pr);
        } else {
            $r_pr->question = $r_qst->id;
            $r_pr->id       = $DB->insert_record("question_match_sub", $r_pr);
        }
        $r_cmp          = "qtype_match";
        $r_far           = "subquestion";
        $r_iti             = $r_pr->id;
        $r_txt               = $r_pr->questiontext;
        $r_pr->questiontext = RWSPAtt($r_qst->qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
            $DB->update_record("qtype_match_subquestions", $r_pr);
        } else {
            $DB->update_record("question_match_sub", $r_pr);
        }
        $r_pis[] = $r_pr->id;
    }
    if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
        $r_op->questionid = $r_qst->id;
        $r_op->id         = $DB->insert_record("qtype_match_options", $r_op);
    } else {
        $r_op->question     = $r_qst->id;
        $r_op->subquestions = implode(",", $r_pis);
        $r_op->id           = $DB->insert_record("question_match", $r_op);
    }
    return $r_qst->id;
}
function RWSIDRec($r_cid, $r_qci, $r_rcd) {
    global $CFG;
    global $DB;
    global $RWSUID;
    if (RWSGQRType($r_rcd) != RWSDES) {
        return false;
    }
    $r_ctxi = $DB->get_field("question_categories", "contextid",
        array("id" => $r_qci));
    $r_qst             = new stdClass();
    $r_qst->qtype      = RWSDES;
    $r_qst->parent     = 0;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_qst->hidden   = 0;
        $r_qst->category = $r_qci;
    }
    $r_qst->length     = 0;
    $r_qst->stamp      = make_unique_id_code();
    $r_qst->createdby  = $RWSUID;
    $r_qst->modifiedby = $RWSUID;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qst->defaultmark = 0;
    } else {
        $r_qst->defaultgrade = 0;
    }
    $r_qst->penalty = 0;
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->name = trim($r_fld);
    if (strlen($r_qst->name) == 0) {
        return false;
    }
    $r_qst->name = clean_param($r_qst->name, PARAM_TEXT);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->questiontext       = trim($r_fld);
    $r_qst->questiontextformat = FORMAT_HTML;
    $r_qst->questiontext = clean_param($r_qst->questiontext, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->generalfeedback       = trim($r_fld);
    $r_qst->generalfeedbackformat = FORMAT_HTML;
    $r_qst->generalfeedback = clean_param($r_qst->generalfeedback, PARAM_RAW);
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->timecreated  = time();
    $r_qst->timemodified = time();
    $r_qst->id           = $DB->insert_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qb = new \stdClass();
        $r_qb->questioncategoryid = $r_qci;
        $r_qb->idnumber = null;
        $r_qb->ownerid = $r_qst->createdby;
        $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
        $r_qv = new \stdClass();
        $r_qv->questionbankentryid = $r_qb->id;
        $r_qv->questionid = $r_qst->id;
        $r_qv->version = get_next_version($r_qb->id);
        $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $r_qv->status = $r_sts;
        $r_qv->id = $DB->insert_record('question_versions', $r_qv);
    } else {
    }
    $r_cmp              = "question";
    $r_far               = "questiontext";
    $r_iti                 = $r_qst->id;
    $r_txt                   = $r_qst->questiontext;
    $r_qst->questiontext = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                 = "question";
    $r_far                  = "generalfeedback";
    $r_iti                    = $r_qst->id;
    $r_txt                      = $r_qst->generalfeedback;
    $r_qst->generalfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_h = question_hash($r_qst);
        $DB->set_field("question", "version", $r_h, array("id" => $r_qst->id));
    }
    return $r_qst->id;
}
function RWSIERec($r_cid, $r_qci, $r_rcd) {
    global $CFG;
    global $DB;
    global $RWSUID;
    if (RWSGQRType($r_rcd) != RWSESS) {
        return false;
    }
    $r_ctxi = $DB->get_field("question_categories", "contextid",
        array("id" => $r_qci));
    $r_qst             = new stdClass();
    $r_qst->qtype      = RWSESS;
    $r_qst->parent     = 0;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_qst->hidden   = 0;
        $r_qst->category = $r_qci;
    }
    $r_qst->length     = 1;
    $r_qst->stamp      = make_unique_id_code();
    $r_qst->createdby  = $RWSUID;
    $r_qst->modifiedby = $RWSUID;
    $r_qst->penalty    = 0;
    $r_p   = 1;
    $r_ct = 4;
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_dat = unpack("N", $r_fld);
    $r_sz = $r_dat[1];
    if (strlen($r_rcd) != $r_p + $r_sz) {
        return false;
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false || $r_ct < 1) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->name = trim($r_fld);
    if (strlen($r_qst->name) == 0) {
        return false;
    }
    $r_qst->name = clean_param($r_qst->name, PARAM_TEXT);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->questiontext       = trim($r_fld);
    $r_qst->questiontextformat = FORMAT_HTML;
    $r_qst->questiontext = clean_param($r_qst->questiontext, PARAM_RAW);
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat = unpack("N", $r_fld);
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_qst->defaultmark = $r_dat[1];
    } else {
        $r_qst->defaultgrade = $r_dat[1];
    }
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->generalfeedback       = trim($r_fld);
    $r_qst->generalfeedbackformat = FORMAT_HTML;
    $r_qst->generalfeedback = clean_param($r_qst->generalfeedback, PARAM_RAW);
    $r_asr           = new stdClass();
    $r_asr->fraction = 0;
    if ($r_sz < 1) {
        return false;
    }
    $r_ct = strpos(substr($r_rcd, $r_p), "\0");
    if ($r_ct === false) {
        return false;
    }
    if ($r_ct > 0) {
        $r_fld = substr($r_rcd, $r_p, $r_ct);
    } else {
        $r_fld = "";
    }
    $r_ct++;
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_asr->feedback       = trim($r_fld);
    $r_asr->feedbackformat = FORMAT_HTML;
    $r_asr->feedback = clean_param($r_asr->feedback, PARAM_RAW);
    $r_asr->answer       = $r_asr->feedback;
    $r_asr->answerformat = $r_asr->feedbackformat;
    $r_ct = 4;
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_fld = substr($r_rcd, $r_p, $r_ct);
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_dat  = unpack("N", $r_fld);
    $r_ct = $r_dat[1];
    if ($r_sz < $r_ct) {
        return false;
    }
    $r_p += $r_ct;
    $r_sz -= $r_ct;
    $r_qst->timecreated  = time();
    $r_qst->timemodified = time();
    $r_qst->id           = $DB->insert_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qb = new \stdClass();
        $r_qb->questioncategoryid = $r_qci;
        $r_qb->idnumber = null;
        $r_qb->ownerid = $r_qst->createdby;
        $r_qb->id = $DB->insert_record("question_bank_entries", $r_qb);
        $r_qv = new \stdClass();
        $r_qv->questionbankentryid = $r_qb->id;
        $r_qv->questionid = $r_qst->id;
        $r_qv->version = get_next_version($r_qb->id);
        $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $r_qv->status = $r_sts;
        $r_qv->id = $DB->insert_record('question_versions', $r_qv);
    } else {
    }
    $r_cmp              = "question";
    $r_far               = "questiontext";
    $r_iti                 = $r_qst->id;
    $r_txt                   = $r_qst->questiontext;
    $r_qst->questiontext = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $r_cmp                 = "question";
    $r_far                  = "generalfeedback";
    $r_iti                    = $r_qst->id;
    $r_txt                      = $r_qst->generalfeedback;
    $r_qst->generalfeedback = RWSPAtt($r_qst->qtype,
        $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
    );
    $DB->update_record("question", $r_qst);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
    } else {
        $r_h = question_hash($r_qst);
        $DB->set_field("question", "version", $r_h, array("id" => $r_qst->id));
    }
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_op                     = new stdClass();
        $r_op->questionid         = $r_qst->id;
        $r_op->responseformat     = "editor";
        $r_op->responsefieldlines = 15;
        $r_op->attachments        = 0;
        $r_op->graderinfo         = $r_asr->answer;
        $r_op->graderinfoformat   = $r_asr->answerformat;
        if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
            $r_op->responsetemplate       = "";
            $r_op->responsetemplateformat = FORMAT_HTML;
        }
        if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
            $r_op->responserequired = 1;
            $r_op->attachmentsrequired = 0;
        }
        $r_op->id = $DB->insert_record("qtype_essay_options", $r_op);
        $r_cmp           = "qtype_essay";
        $r_far            = "graderinfo";
        $r_iti              = $r_qst->id;
        $r_txt                = $r_op->graderinfo;
        $r_op->graderinfo = RWSPAtt($r_qst->qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $DB->update_record("qtype_essay_options", $r_op);
    } else {
        $r_asr->question = $r_qst->id;
        $r_asr->id       = $DB->insert_record("question_answers", $r_asr);
        $r_cmp        = "question";
        $r_far         = "answerfeedback";
        $r_iti           = $r_asr->id;
        $r_txt             = $r_asr->feedback;
        $r_asr->feedback = RWSPAtt($r_qst->qtype,
            $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt
        );
        $r_asr->answer = $r_asr->feedback;
        $DB->update_record("question_answers", $r_asr);
    }
    return $r_qst->id;
}
function RWSESRec($r_qiz) {
    global $DB;
    global $RWSLB;
    global $CFG;
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context_course::instance($r_qiz->coursemodule);
    } else {
        $r_ctx = get_context_instance(CONTEXT_MODULE, $r_qiz->coursemodule);
    }
    $r_ctxi = $r_ctx->id;
    $r_txt      = $r_qiz->intro;
    $r_scr    = "pluginfile.php";
    $r_cmp = "mod_quiz";
    $r_far  = "intro";
    $r_iti    = 0;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld  = pack("a*x", $r_fld);
    $r_rcd = $r_fld;
    if ($r_qiz->timeopen == 0) {
        $r_y   = 0;
        $r_mo  = 0;
        $r_da    = 0;
        $r_hr   = 0;
        $r_mt = 0;
    } else {
        $r_std = usergetdate($r_qiz->timeopen);
        $r_y       = $r_std['year'];
        $r_mo      = $r_std['mon'];
        $r_da        = $r_std['mday'];
        $r_hr       = $r_std['hours'];
        $r_mt     = $r_std['minutes'];
    }
    $r_fld = pack("nC*", $r_y, $r_mo, $r_da, $r_hr, $r_mt);
    $r_rcd .= $r_fld;
    if ($r_qiz->timeclose == 0) {
        $r_y   = 0;
        $r_mo  = 0;
        $r_da    = 0;
        $r_hr   = 0;
        $r_mt = 0;
    } else {
        $r_edt = usergetdate($r_qiz->timeclose);
        $r_y     = $r_edt['year'];
        $r_mo    = $r_edt['mon'];
        $r_da      = $r_edt['mday'];
        $r_hr     = $r_edt['hours'];
        $r_mt   = $r_edt['minutes'];
    }
    $r_fld = pack("nC*", $r_y, $r_mo, $r_da, $r_hr, $r_mt);
    $r_rcd .= $r_fld;
    $r_en  = ($r_qiz->timelimit == 0) ? 0 : 1;
    $r_mts = $r_qiz->timelimit / 60;
    if ($r_mts * 60 < $r_qiz->timelimit) {
        $r_mts += 1;
    }
    $r_fld = pack("CN", $r_en, $r_mts);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->delay1;
    if ($r_fld < 900) {
        $r_fld = 0;
    } else if ($r_fld < 2700) {
        $r_fld = 1800;
    } else if ($r_fld < 5400) {
        $r_fld = 3600;
    } else if ($r_fld < 9000) {
        $r_fld = 7200;
    } else if ($r_fld < 12600) {
        $r_fld = 10800;
    } else if ($r_fld < 16200) {
        $r_fld = 14400;
    } else if ($r_fld < 19800) {
        $r_fld = 18000;
    } else if ($r_fld < 23400) {
        $r_fld = 21600;
    } else if ($r_fld < 27000) {
        $r_fld = 25200;
    } else if ($r_fld < 30600) {
        $r_fld = 28800;
    } else if ($r_fld < 34200) {
        $r_fld = 32400;
    } else if ($r_fld < 37800) {
        $r_fld = 36000;
    } else if ($r_fld < 41400) {
        $r_fld = 39600;
    } else if ($r_fld < 45000) {
        $r_fld = 43200;
    } else if ($r_fld < 48600) {
        $r_fld = 46800;
    } else if ($r_fld < 52200) {
        $r_fld = 50400;
    } else if ($r_fld < 55800) {
        $r_fld = 54000;
    } else if ($r_fld < 59400) {
        $r_fld = 57600;
    } else if ($r_fld < 63000) {
        $r_fld = 61200;
    } else if ($r_fld < 66600) {
        $r_fld = 64800;
    } else if ($r_fld < 70200) {
        $r_fld = 68400;
    } else if ($r_fld < 73800) {
        $r_fld = 72000;
    } else if ($r_fld < 77400) {
        $r_fld = 75600;
    } else if ($r_fld < 81000) {
        $r_fld = 79200;
    } else if ($r_fld < 84600) {
        $r_fld = 82800;
    } else if ($r_fld < 126000) {
        $r_fld = 86400;
    } else if ($r_fld < 216000) {
        $r_fld = 172800;
    } else if ($r_fld < 302400) {
        $r_fld = 259200;
    } else if ($r_fld < 388800) {
        $r_fld = 345600;
    } else if ($r_fld < 475200) {
        $r_fld = 432000;
    } else if ($r_fld < 561600) {
        $r_fld = 518400;
    } else {
        $r_fld = 604800;
    }
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->delay2;
    if ($r_fld < 900) {
        $r_fld = 0;
    } else if ($r_fld < 2700) {
        $r_fld = 1800;
    } else if ($r_fld < 5400) {
        $r_fld = 3600;
    } else if ($r_fld < 9000) {
        $r_fld = 7200;
    } else if ($r_fld < 12600) {
        $r_fld = 10800;
    } else if ($r_fld < 16200) {
        $r_fld = 14400;
    } else if ($r_fld < 19800) {
        $r_fld = 18000;
    } else if ($r_fld < 23400) {
        $r_fld = 21600;
    } else if ($r_fld < 27000) {
        $r_fld = 25200;
    } else if ($r_fld < 30600) {
        $r_fld = 28800;
    } else if ($r_fld < 34200) {
        $r_fld = 32400;
    } else if ($r_fld < 37800) {
        $r_fld = 36000;
    } else if ($r_fld < 41400) {
        $r_fld = 39600;
    } else if ($r_fld < 45000) {
        $r_fld = 43200;
    } else if ($r_fld < 48600) {
        $r_fld = 46800;
    } else if ($r_fld < 52200) {
        $r_fld = 50400;
    } else if ($r_fld < 55800) {
        $r_fld = 54000;
    } else if ($r_fld < 59400) {
        $r_fld = 57600;
    } else if ($r_fld < 63000) {
        $r_fld = 61200;
    } else if ($r_fld < 66600) {
        $r_fld = 64800;
    } else if ($r_fld < 70200) {
        $r_fld = 68400;
    } else if ($r_fld < 73800) {
        $r_fld = 72000;
    } else if ($r_fld < 77400) {
        $r_fld = 75600;
    } else if ($r_fld < 81000) {
        $r_fld = 79200;
    } else if ($r_fld < 84600) {
        $r_fld = 82800;
    } else if ($r_fld < 126000) {
        $r_fld = 86400;
    } else if ($r_fld < 216000) {
        $r_fld = 172800;
    } else if ($r_fld < 302400) {
        $r_fld = 259200;
    } else if ($r_fld < 388800) {
        $r_fld = 345600;
    } else if ($r_fld < 475200) {
        $r_fld = 432000;
    } else if ($r_fld < 561600) {
        $r_fld = 518400;
    } else {
        $r_fld = 604800;
    }
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->questionsperpage;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->shufflequestions;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->shuffleanswers;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->attempts;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->attemptonlast;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        if ($r_qiz->preferredbehaviour == "adaptive"
            || $r_qiz->preferredbehaviour == "adaptivenopenalty"
        ) {
            $r_fld = 1;
        } else {
            $r_fld = 0;
        }
    } else {
        $r_fld = $r_qiz->optionflags & RWSQAD;
    }
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->grade;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->grademethod;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        if ($r_qiz->preferredbehaviour == "adaptive") {
            $r_fld = 1;
        } else {
            $r_fld = 0;
        }
    } else {
        $r_fld = $r_qiz->penaltyscheme;
    }
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->decimalpoints;
    if ($r_fld > 3) {
        $r_fld = 3;
    }
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_rsps = (($r_qiz->reviewattempt & RWSRDU)
            || ($r_qiz->reviewattempt & RWSRIA)) ? 1 : 0;
        $r_asrs   = (($r_qiz->reviewrightanswer & RWSRDU)
            || ($r_qiz->reviewrightanswer & RWSRIA)) ? 1 : 0;
        $r_fb  = (($r_qiz->reviewspecificfeedback & RWSRDU)
            || ($r_qiz->reviewspecificfeedback & RWSRIA)) ? 1 : 0;
        $r_gen   = (($r_qiz->reviewgeneralfeedback & RWSRDU)
            || ($r_qiz->reviewgeneralfeedback & RWSRIA)) ? 1 : 0;
        $r_sc    = (($r_qiz->reviewmarks & RWSRDU)
            || ($r_qiz->reviewmarks & RWSRIA)
            || ($r_qiz->reviewcorrectness & RWSRDU)
            || ($r_qiz->reviewcorrectness & RWSRIA)) ? 1 : 0;
        $r_ov   = (($r_qiz->reviewoverallfeedback & RWSRDU)
            || ($r_qiz->reviewoverallfeedback & RWSRIA)) ? 1 : 0;
    } else {
        $r_rsps = ($r_qiz->review & RWSRRE & RWSRIM) ? 1 : 0;
        $r_asrs   = ($r_qiz->review & RWSRAN & RWSRIM) ? 1 : 0;
        $r_fb  = ($r_qiz->review & RWSRFE & RWSRIM) ? 1 : 0;
        $r_gen   = ($r_qiz->review & RWSRGE & RWSRIM) ? 1 : 0;
        $r_sc    = ($r_qiz->review & RWSRSC & RWSRIM) ? 1 : 0;
        $r_ov   = ($r_qiz->review & RWSROV & RWSRIM) ? 1 : 0;
    }
    $r_fld = pack("C*", $r_rsps, $r_asrs, $r_fb, $r_gen, $r_sc, $r_ov);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_rsps = ($r_qiz->reviewattempt & RWSRLA) ? 1 : 0;
        $r_asrs   = ($r_qiz->reviewrightanswer & RWSRLA) ? 1 : 0;
        $r_fb  = ($r_qiz->reviewspecificfeedback & RWSRLA) ? 1 : 0;
        $r_gen   = ($r_qiz->reviewgeneralfeedback & RWSRLA) ? 1 : 0;
        $r_sc    = (($r_qiz->reviewcorrectness & RWSRLA)
            || ($r_qiz->reviewmarks & RWSRLA)) ? 1 : 0;
        $r_ov   = ($r_qiz->reviewoverallfeedback & RWSRLA) ? 1 : 0;
    } else {
        $r_rsps = ($r_qiz->review & RWSRRE & RWSROP) ? 1 : 0;
        $r_asrs   = ($r_qiz->review & RWSRAN & RWSROP) ? 1 : 0;
        $r_fb  = ($r_qiz->review & RWSRFE & RWSROP) ? 1 : 0;
        $r_gen   = ($r_qiz->review & RWSRGE & RWSROP) ? 1 : 0;
        $r_sc    = ($r_qiz->review & RWSRSC & RWSROP) ? 1 : 0;
        $r_ov   = ($r_qiz->review & RWSROV & RWSROP) ? 1 : 0;
    }
    $r_fld = pack("C*", $r_rsps, $r_asrs, $r_fb, $r_gen, $r_sc, $r_ov);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_rsps = ($r_qiz->reviewattempt & RWSRAF) ? 1 : 0;
        $r_asrs   = ($r_qiz->reviewrightanswer & RWSRAF) ? 1 : 0;
        $r_fb  = ($r_qiz->reviewspecificfeedback & RWSRAF) ? 1 : 0;
        $r_gen   = ($r_qiz->reviewgeneralfeedback & RWSRAF) ? 1 : 0;
        $r_sc    = (($r_qiz->reviewcorrectness & RWSRAF)
            || ($r_qiz->reviewmarks & RWSRAF)) ? 1 : 0;
        $r_ov   = ($r_qiz->reviewoverallfeedback & RWSRAF) ? 1 : 0;
    } else {
        $r_rsps = ($r_qiz->review & RWSRRE & RWSRCL) ? 1 : 0;
        $r_asrs   = ($r_qiz->review & RWSRAN & RWSRCL) ? 1 : 0;
        $r_fb  = ($r_qiz->review & RWSRFE & RWSRCL) ? 1 : 0;
        $r_gen   = ($r_qiz->review & RWSRGE & RWSRCL) ? 1 : 0;
        $r_sc    = ($r_qiz->review & RWSRSC & RWSRCL) ? 1 : 0;
        $r_ov   = ($r_qiz->review & RWSROV & RWSRCL) ? 1 : 0;
    }
    $r_fld = pack("C*", $r_rsps, $r_asrs, $r_fb, $r_gen, $r_sc, $r_ov);
    $r_rcd .= $r_fld;
    if (isset($r_qiz->browsersecurity)) {
        if ($r_qiz->browsersecurity == "securewindow"
            || $r_qiz->browsersecurity == "safebrowser"
        ) {
            $popup = 1;
        } else {
            $popup = 0;
        }
    } else {
        $popup = $r_qiz->popup;
    }
    $r_fld = $popup;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->password;
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->subnet;
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->groupmode;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->visible;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qiz->cmidnumber;
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = "1";
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fbt       = array();
    $r_fbb = array();
    $r_qzf      = $DB->get_records("quiz_feedback",
        array("quizid" => $r_qiz->id), "mingrade DESC");
    if (count($r_qzf) > 0) {
        foreach ($r_qzf as $r_qf) {
            $r_txt           = $r_qf->feedbacktext;
            $r_scr         = "pluginfile.php";
            $r_cmp      = "mod_quiz";
            $r_far       = "feedback";
            $r_iti         = $r_qf->id;
            $r_fbt[] = file_rewrite_pluginfile_urls(
                $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
            );
            if ($r_qf->mingrade > 0) {
                $r_bd                = (100.0 * $r_qf->mingrade / $r_qiz->grade) . "%";
                $r_fbb[] = $r_bd;
            }
        }
    }
    $r_fld = count($r_fbt);
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    if (count($r_fbt) > 0) {
        foreach ($r_fbt as $r_fd) {
            $r_fld = $r_fd;
            $r_fld = respondusws_utf8encode($r_fld);
            $r_fld = pack("a*x", $r_fld);
            $r_rcd .= $r_fld;
        }
    }
    $r_fld = count($r_fbb);
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    foreach ($r_fbb as $r_bd) {
        $r_fld = $r_bd;
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
    }
    RWSLLBSet($r_qiz);
    $r_fld = $RWSLB->atts;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $RWSLB->revs;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $RWSLB->pw;
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = 8;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = time();
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = crc32($r_rcd);
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    return $r_rcd;
}
function RWSLLBSet($r_qiz) {
    global $RWSLB;
    $RWSLB->atts = 0;
    $RWSLB->revs  = 0;
    $RWSLB->pw = "";
    $RWSLB->gerr = false;
    if ($RWSLB->mok) {
        $r_op = lockdown_get_quiz_options($r_qiz->instance);
        if (!$r_op) {
            $RWSLB->gerr = true;
        } else {
            $RWSLB->atts = $r_op->attempts;
            $RWSLB->revs  = $r_op->reviews;
            $RWSLB->pw = $r_op->password;
        }
    } else if ($RWSLB->bok) {
        $r_op = lockdown_get_quiz_options($r_qiz->instance);
        if ($r_op !== false) {
            $RWSLB->atts = $r_op->attempts;
            if (!is_null($r_op->reviews)) {
                $RWSLB->revs = $r_op->reviews;
            }
            if (!is_null($r_op->password)) {
                $RWSLB->pw = $r_op->password;
            }
        }
    }
}
function RWSERRec($r_dat) {
    $r_rcd = "";
    $r_l = strlen($r_dat);
    if ($r_l > 0) {
        $r_rcd .= $r_dat;
    }
    if ($r_l > 0) {
        $r_fld = crc32($r_dat);
        $r_fld = pack("N", $r_fld);
        $r_rcd .= $r_fld;
    }
    $r_rd = pack("C", 12);
    $r_rd .= pack("N", strlen($r_rcd));
    $r_rd .= $r_rcd;
    return $r_rd;
}
function RWSESARec($r_qst) {
    global $DB;
    global $CFG;
    if ($r_qst->qtype != RWSSHA) {
        return false;
    }
    if ($r_qst->parent != 0) {
        return false;
    }
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_ctxi = false;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbi = $DB->get_field("question_versions", "questionbankentryid",
          array("questionid" => $r_qst->id));
        if ($r_qbi === false) {
            return false;
        }
        $r_qcd = $DB->get_field("question_bank_entries", "questioncategoryid",
          array("id" => $r_qbi));
        if ($r_qcd === false) {
            return false;
        }
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qcd));
    } else {
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qst->category));
    }
    if ($r_ctxi === false) {
        return false;
    }
    $r_fld = $r_qst->name;
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld  = pack("a*x", $r_fld);
    $r_rcd = $r_fld;
    $r_txt      = $r_qst->questiontext;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "questiontext";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = "";
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_fld = $r_qst->defaultmark;
    } else {
        $r_fld = $r_qst->defaultgrade;
    }
    if ($r_fld < 0) {
        $r_fld = 0;
    }
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qst->penalty;
    $r_fld = RWSDblOut($r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_qst->generalfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "generalfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
        $r_op = $DB->get_record("qtype_shortanswer_options",
            array("questionid" => $r_qst->id));
    } else {
        $r_op = $DB->get_record("question_shortanswer",
            array("question" => $r_qst->id));
    }
    if ($r_op === false) {
        return false;
    }
    $r_fld = $r_op->usecase;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
        $r_asrs = $DB->get_records("question_answers",
            array("question" => $r_qst->id), "id ASC");
    } else {
        $r_asrs    = array();
        $r_aid = explode(",", $r_op->answers);
        foreach ($r_aid as $r_id) {
            $r_asr = $DB->get_record("question_answers", array("id" => $r_id));
            if ($r_asr === false) {
                return false;
            }
            $r_asrs[] = $r_asr;
        }
    }
    if (count($r_asrs) == 0) {
        return false;
    }
    $r_fld = count($r_asrs);
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    foreach ($r_asrs as $r_asr) {
        $r_fld = $r_asr->answer;
        $r_fld = respondusws_utf8encode($r_fld);
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_asr->fraction;
        if (respondusws_floatcompare($r_bv, 2011020100, 2) == -1) {
            switch (strval($r_fld)) {
                case "0.8333333":
                    $r_fld = "0.83333";
                    break;
                case "0.6666667":
                    $r_fld = "0.66666";
                    break;
                case "0.3333333":
                    $r_fld = "0.33333";
                    break;
                case "0.1666667":
                    $r_fld = "0.16666";
                    break;
                case "0.1428571":
                    $r_fld = "0.142857";
                    break;
                case "0.1111111":
                    $r_fld = "0.11111";
                    break;
                default:
                    break;
            }
        }
        $r_fld = RWSDblOut($r_fld);
        $r_rcd .= $r_fld;
        $r_txt      = $r_asr->feedback;
        $r_scr    = "pluginfile.php";
        $r_cmp = "question";
        $r_far  = "answerfeedback";
        $r_iti    = $r_asr->id;
        $r_fld     = file_rewrite_pluginfile_urls(
            $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
        );
        $r_fld = respondusws_utf8encode($r_fld);
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
    }
    $r_fld = 8;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = time();
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = crc32($r_rcd);
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_qd = pack("C", 3);
    $r_qd .= pack("N", strlen($r_rcd));
    $r_qd .= $r_rcd;
    return $r_qd;
}
function RWSETFRec($r_qst) {
    global $DB;
    global $CFG;
    if ($r_qst->qtype != RWSTRF) {
        return false;
    }
    if ($r_qst->parent != 0) {
        return false;
    }
    $r_ctxi = false;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbi = $DB->get_field("question_versions", "questionbankentryid",
          array("questionid" => $r_qst->id));
        if ($r_qbi === false) {
            return false;
        }
        $r_qcd = $DB->get_field("question_bank_entries", "questioncategoryid",
          array("id" => $r_qbi));
        if ($r_qcd === false) {
            return false;
        }
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qcd));
    } else {
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qst->category));
    }
    if ($r_ctxi === false) {
        return false;
    }
    $r_fld = $r_qst->name;
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld  = pack("a*x", $r_fld);
    $r_rcd = $r_fld;
    $r_txt      = $r_qst->questiontext;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "questiontext";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = "";
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_fld = $r_qst->defaultmark;
    } else {
        $r_fld = $r_qst->defaultgrade;
    }
    if ($r_fld < 0) {
        $r_fld = 0;
    }
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qst->penalty;
    $r_fld = RWSDblOut($r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_qst->generalfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "generalfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_op = $DB->get_record("question_truefalse",
        array("question" => $r_qst->id));
    if ($r_op === false) {
        return false;
    }
    $r_tru = $DB->get_record("question_answers",
        array("id" => $r_op->trueanswer));
    if ($r_tru === false) {
        return false;
    }
    $r_fal = $DB->get_record("question_answers",
        array("id" => $r_op->falseanswer));
    if ($r_fal === false) {
        return false;
    }
    $r_fld = $r_tru->fraction;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_tru->feedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "answerfeedback";
    $r_iti    = $r_tru->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_fal->feedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "answerfeedback";
    $r_iti    = $r_fal->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = 8;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = time();
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = crc32($r_rcd);
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_qd = pack("C", 2);
    $r_qd .= pack("N", strlen($r_rcd));
    $r_qd .= $r_rcd;
    return $r_qd;
}
function RWSEMARec($r_qst) {
    global $DB;
    global $CFG;
    if ($r_qst->qtype != RWSMAN) {
        return false;
    }
    if ($r_qst->parent != 0) {
        return false;
    }
    $r_ctxi = false;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbi = $DB->get_field("question_versions", "questionbankentryid",
          array("questionid" => $r_qst->id));
        if ($r_qbi === false) {
            return false;
        }
        $r_qcd = $DB->get_field("question_bank_entries", "questioncategoryid",
          array("id" => $r_qbi));
        if ($r_qcd === false) {
            return false;
        }
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qcd));
    } else {
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qst->category));
    }
    if ($r_ctxi === false) {
        return false;
    }
    $r_fld = $r_qst->name;
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld  = pack("a*x", $r_fld);
    $r_rcd = $r_fld;
    $r_txt                   = $r_qst->questiontext;
    $r_scr                 = "pluginfile.php";
    $r_cmp              = "question";
    $r_far               = "questiontext";
    $r_iti                 = $r_qst->id;
    $r_qst->questiontext = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_clzf = RWSGCFields($r_qst->questiontext);
    if ($r_clzf === false) {
        return false;
    }
    $r_op = $DB->get_record("question_multianswer",
        array("question" => $r_qst->id));
    if ($r_op === false) {
        return false;
    }
    $r_chid   = explode(",", $r_op->sequence);
    $r_chc = count($r_chid);
    if ($r_chc != count($r_clzf)) {
        return false;
    }
    for ($r_i = 0; $r_i < $r_chc; $r_i++) {
        $r_chd = $DB->get_record("question", array("id" => $r_chid[$r_i]));
        if ($r_chd === false) {
            return false;
        }
        $r_txt                = $r_chd->questiontext;
        $r_scr              = "pluginfile.php";
        $r_cmp           = "question";
        $r_far            = "questiontext";
        $r_iti              = $r_chd->id;
        $r_chd->questiontext = file_rewrite_pluginfile_urls(
            $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
        );
        $r_qst->questiontext = implode($r_chd->questiontext,
            explode($r_clzf[$r_i], $r_qst->questiontext, 2));
    }
    $r_fld = $r_qst->questiontext;
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = "";
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qst->penalty;
    $r_fld = RWSDblOut($r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_qst->generalfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "generalfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = 8;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = time();
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = crc32($r_rcd);
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_qd = pack("C", 9);
    $r_qd .= pack("N", strlen($r_rcd));
    $r_qd .= $r_rcd;
    return $r_qd;
}
function RWSCRFISort($r_rc1, $r_rc2) {
    if ($r_rc1->id == $r_rc2->id) {
        return 0;
    }
    return ($r_rc1->id < $r_rc2->id) ? -1 : 1;
}
function RWSECRec($r_qst) {
    global $DB;
    global $CFG;
    if ($r_qst->qtype != RWSCAL
        && $r_qst->qtype != RWSCSI
    ) {
        return false;
    }
    if ($r_qst->parent != 0) {
        return false;
    }
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_ctxi = false;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbi = $DB->get_field("question_versions", "questionbankentryid",
          array("questionid" => $r_qst->id));
        if ($r_qbi === false) {
            return false;
        }
        $r_qcd = $DB->get_field("question_bank_entries", "questioncategoryid",
          array("id" => $r_qbi));
        if ($r_qcd === false) {
            return false;
        }
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qcd));
    } else {
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qst->category));
    }
    if ($r_ctxi === false) {
        return false;
    }
    $r_fld = $r_qst->name;
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld  = pack("a*x", $r_fld);
    $r_rcd = $r_fld;
    $r_txt      = $r_qst->questiontext;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "questiontext";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = "";
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_fld = $r_qst->defaultmark;
    } else {
        $r_fld = $r_qst->defaultgrade;
    }
    if ($r_fld < 0) {
        $r_fld = 0;
    }
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qst->penalty;
    $r_fld = RWSDblOut($r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_qst->generalfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "generalfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_asrs = $DB->get_records("question_answers",
        array("question" => $r_qst->id), "id ASC");
    if (count($r_asrs) == 0) {
        return false;
    }
    if (count($r_asrs) > 1) {
        usort($r_asrs, "RWSCRFISort");
    }
    $r_fld = count($r_asrs);
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    foreach ($r_asrs as $r_an) {
        $r_fld = $r_an->answer;
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_an->fraction;
        if (respondusws_floatcompare($r_bv, 2011020100, 2) == -1) {
            switch (strval($r_fld)) {
                case "0.8333333":
                    $r_fld = "0.83333";
                    break;
                case "0.6666667":
                    $r_fld = "0.66666";
                    break;
                case "0.3333333":
                    $r_fld = "0.33333";
                    break;
                case "0.1666667":
                    $r_fld = "0.16666";
                    break;
                case "0.1428571":
                    $r_fld = "0.142857";
                    break;
                case "0.1111111":
                    $r_fld = "0.11111";
                    break;
                default:
                    break;
            }
        }
        $r_fld = RWSDblOut($r_fld);
        $r_rcd .= $r_fld;
        $r_txt      = $r_an->feedback;
        $r_scr    = "pluginfile.php";
        $r_cmp = "question";
        $r_far  = "answerfeedback";
        $r_iti    = $r_an->id;
        $r_fld     = file_rewrite_pluginfile_urls(
            $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
        );
        $r_fld = respondusws_utf8encode($r_fld);
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
        $r_o = $DB->get_record("question_calculated",
            array("answer" => $r_an->id));
        if ($r_o === false) {
            return false;
        }
        $r_fld = $r_o->tolerance;
        $r_fld = RWSDblOut($r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_o->tolerancetype;
        $r_fld = pack("C", $r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_o->correctanswerlength;
        $r_fld = pack("C", $r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_o->correctanswerformat;
        $r_fld = pack("C", $r_fld);
        $r_rcd .= $r_fld;
    }
    $r_uts = $DB->get_records("question_numerical_units",
        array("question" => $r_qst->id), "id ASC");
    if (count($r_uts) > 1) {
        usort($r_uts, "RWSCRFISort");
    }
    $r_fld = count($r_uts);
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    if (count($r_uts) > 0) {
        foreach ($r_uts as $r_ut) {
            $r_fld = $r_ut->unit;
            $r_fld = pack("a*x", $r_fld);
            $r_rcd .= $r_fld;
            $r_fld = $r_ut->multiplier;
            $r_fld = RWSDblOut($r_fld);
            $r_rcd .= $r_fld;
        }
    }
    $r_dset = $DB->get_records("question_datasets",
        array("question" => $r_qst->id));
    if (count($r_dset) == 0) {
        return false;
    }
    $r_fld = count($r_dset);
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    foreach ($r_dset as $r_qds) {
        $r_df = $DB->get_record("question_dataset_definitions",
            array("id" => $r_qds->datasetdefinition));
        if ($r_df === false) {
            return false;
        }
        $r_fld = $r_df->name;
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
        list($r_dstr, $r_mi, $r_mx, $r_pre) =
            explode(":", $r_df->options, 4);
        $r_fld = $r_dstr;
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_mi;
        $r_fld = RWSDblOut($r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_mx;
        $r_fld = RWSDblOut($r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_pre;
        $r_fld = pack("C", $r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_df->type;
        $r_fld = pack("C", $r_fld);
        $r_rcd .= $r_fld;
        if ($r_df->category == 0) {
            $r_fld = 0;
        } else {
            $r_fld = 1;
        }
        $r_fld = pack("C", $r_fld);
        $r_rcd .= $r_fld;
        $r_its = $DB->get_records("question_dataset_items",
            array("definition" => $r_df->id));
        if (count($r_its) == 0) {
            return false;
        }
        $r_fld = count($r_its);
        $r_fld = pack("C", $r_fld);
        $r_rcd .= $r_fld;
        foreach ($r_its as $r_it) {
            $r_fld = $r_it->itemnumber;
            $r_fld = pack("C", $r_fld);
            $r_rcd .= $r_fld;
            $r_fld = $r_it->value;
            $r_fld = RWSDblOut($r_fld);
            $r_rcd .= $r_fld;
        }
    }
    $r_fld = 8;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = time();
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = crc32($r_rcd);
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_qd = pack("C", 7);
    $r_qd .= pack("N", strlen($r_rcd));
    $r_qd .= $r_rcd;
    return $r_qd;
}
function RWSEMCRec($r_qst) {
    global $DB;
    global $CFG;
    if ($r_qst->qtype != RWSMCH) {
        return false;
    }
    if ($r_qst->parent != 0) {
        return false;
    }
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_ctxi = false;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbi = $DB->get_field("question_versions", "questionbankentryid",
          array("questionid" => $r_qst->id));
        if ($r_qbi === false) {
            return false;
        }
        $r_qcd = $DB->get_field("question_bank_entries", "questioncategoryid",
          array("id" => $r_qbi));
        if ($r_qcd === false) {
            return false;
        }
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qcd));
    } else {
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qst->category));
    }
    if ($r_ctxi === false) {
        return false;
    }
    $r_fld = $r_qst->name;
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld  = pack("a*x", $r_fld);
    $r_rcd = $r_fld;
    $r_txt      = $r_qst->questiontext;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "questiontext";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = "";
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_fld = $r_qst->defaultmark;
    } else {
        $r_fld = $r_qst->defaultgrade;
    }
    if ($r_fld < 0) {
        $r_fld = 0;
    }
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qst->penalty;
    $r_fld = RWSDblOut($r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_qst->generalfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "generalfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_op = $DB->get_record("qtype_multichoice_options",
            array("questionid" => $r_qst->id));
    } else {
        $r_op = $DB->get_record("question_multichoice",
            array("question" => $r_qst->id));
    }
    if ($r_op === false) {
        return false;
    }
    $r_fld = $r_op->single;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_op->shuffleanswers;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_op->answernumbering;
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_asrs = $DB->get_records("question_answers",
            array("question" => $r_qst->id), "id ASC");
    } else {
        $r_asrs    = array();
        $r_aid = explode(",", $r_op->answers);
        foreach ($r_aid as $r_id) {
            $r_asr = $DB->get_record("question_answers", array("id" => $r_id));
            if ($r_asr === false) {
                return false;
            }
            $r_asrs[] = $r_asr;
        }
    }
    if (count($r_asrs) == 0) {
        return false;
    }
    $r_fld = count($r_asrs);
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    foreach ($r_asrs as $r_asr) {
        $r_txt      = $r_asr->answer;
        $r_scr    = "pluginfile.php";
        $r_cmp = "question";
        $r_far  = "answer";
        $r_iti    = $r_asr->id;
        $r_fld     = file_rewrite_pluginfile_urls(
            $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
        );
        $r_fld = respondusws_utf8encode($r_fld);
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_asr->fraction;
        if (respondusws_floatcompare($r_bv, 2011020100, 2) == -1) {
            switch (strval($r_fld)) {
                case "0.8333333":
                    $r_fld = "0.83333";
                    break;
                case "0.6666667":
                    $r_fld = "0.66666";
                    break;
                case "0.3333333":
                    $r_fld = "0.33333";
                    break;
                case "0.1666667":
                    $r_fld = "0.16666";
                    break;
                case "0.1428571":
                    $r_fld = "0.142857";
                    break;
                case "0.1111111":
                    $r_fld = "0.11111";
                    break;
                case "-0.1111111":
                    $r_fld = "-0.11111";
                    break;
                case "-0.1428571":
                    $r_fld = "-0.142857";
                    break;
                case "-0.1666667":
                    $r_fld = "-0.16666";
                    break;
                case "-0.3333333":
                    $r_fld = "-0.33333";
                    break;
                case "-0.6666667":
                    $r_fld = "-0.66666";
                    break;
                case "-0.8333333":
                    $r_fld = "-0.83333";
                    break;
                default:
                    break;
            }
        }
        $r_fld = RWSDblOut($r_fld);
        $r_rcd .= $r_fld;
        $r_txt      = $r_asr->feedback;
        $r_scr    = "pluginfile.php";
        $r_cmp = "question";
        $r_far  = "answerfeedback";
        $r_iti    = $r_asr->id;
        $r_fld     = file_rewrite_pluginfile_urls(
            $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
        );
        $r_fld = respondusws_utf8encode($r_fld);
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
    }
    $r_txt      = $r_op->correctfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "qtype_multichoice";
    $r_far  = "correctfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_op->partiallycorrectfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "qtype_multichoice";
    $r_far  = "partiallycorrectfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_op->incorrectfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "qtype_multichoice";
    $r_far  = "incorrectfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = 8;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = time();
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = crc32($r_rcd);
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_qd = pack("C", 1);
    $r_qd .= pack("N", strlen($r_rcd));
    $r_qd .= $r_rcd;
    return $r_qd;
}
function RWSEMRec($r_qst) {
    global $DB;
    global $CFG;
    if ($r_qst->qtype != RWSMAT) {
        return false;
    }
    if ($r_qst->parent != 0) {
        return false;
    }
    $r_ctxi = false;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbi = $DB->get_field("question_versions", "questionbankentryid",
          array("questionid" => $r_qst->id));
        if ($r_qbi === false) {
            return false;
        }
        $r_qcd = $DB->get_field("question_bank_entries", "questioncategoryid",
          array("id" => $r_qbi));
        if ($r_qcd === false) {
            return false;
        }
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qcd));
    } else {
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qst->category));
    }
    if ($r_ctxi === false) {
        return false;
    }
    $r_fld = $r_qst->name;
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld  = pack("a*x", $r_fld);
    $r_rcd = $r_fld;
    $r_txt      = $r_qst->questiontext;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "questiontext";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = "";
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_fld = $r_qst->defaultmark;
    } else {
        $r_fld = $r_qst->defaultgrade;
    }
    if ($r_fld < 0) {
        $r_fld = 0;
    }
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = $r_qst->penalty;
    $r_fld = RWSDblOut($r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_qst->generalfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "generalfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
        $r_op = $DB->get_record("qtype_match_options",
            array("questionid" => $r_qst->id));
    } else {
        $r_op = $DB->get_record("question_match",
            array("question" => $r_qst->id));
    }
    if ($r_op === false) {
        return false;
    }
    $r_fld = $r_op->shuffleanswers;
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
        $r_prs = $DB->get_records("qtype_match_subquestions",
            array("questionid" => $r_qst->id), "id ASC");
    } else {
        $r_prs    = array();
        $r_pis = explode(",", $r_op->subquestions);
        foreach ($r_pis as $r_id) {
            $r_pr = $DB->get_record("question_match_sub",
                array("id" => $r_id));
            if ($r_pr === false) {
                return false;
            }
            $r_prs[] = $r_pr;
        }
    }
    if (count($r_prs) == 0) {
        return false;
    }
    $r_fld = count($r_prs);
    $r_fld = pack("C", $r_fld);
    $r_rcd .= $r_fld;
    foreach ($r_prs as $r_pr) {
        $r_txt      = $r_pr->questiontext;
        $r_scr    = "pluginfile.php";
        $r_cmp = "qtype_match";
        $r_far  = "subquestion";
        $r_iti    = $r_pr->id;
        $r_fld     = file_rewrite_pluginfile_urls(
            $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
        );
        $r_fld = respondusws_utf8encode($r_fld);
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
        $r_fld = $r_pr->answertext;
        $r_fld = respondusws_utf8encode($r_fld);
        $r_fld = pack("a*x", $r_fld);
        $r_rcd .= $r_fld;
    }
    $r_fld = 8;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = time();
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = crc32($r_rcd);
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_qd = pack("C", 5);
    $r_qd .= pack("N", strlen($r_rcd));
    $r_qd .= $r_rcd;
    return $r_qd;
}
function RWSEDRec($r_qst) {
    global $DB;
    global $CFG;
    if ($r_qst->qtype != RWSDES) {
        return false;
    }
    if ($r_qst->parent != 0) {
        return false;
    }
    $r_ctxi = false;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbi = $DB->get_field("question_versions", "questionbankentryid",
          array("questionid" => $r_qst->id));
        if ($r_qbi === false) {
            return false;
        }
        $r_qcd = $DB->get_field("question_bank_entries", "questioncategoryid",
          array("id" => $r_qbi));
        if ($r_qcd === false) {
            return false;
        }
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qcd));
    } else {
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qst->category));
    }
    if ($r_ctxi === false) {
        return false;
    }
    $r_fld = $r_qst->name;
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld  = pack("a*x", $r_fld);
    $r_rcd = $r_fld;
    $r_txt      = $r_qst->questiontext;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "questiontext";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = "";
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_qst->generalfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "generalfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = 8;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = time();
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = crc32($r_rcd);
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_qd = pack("C", 6);
    $r_qd .= pack("N", strlen($r_rcd));
    $r_qd .= $r_rcd;
    return $r_qd;
}
function RWSEERec($r_qst) {
    global $DB;
    global $CFG;
    if ($r_qst->qtype != RWSESS) {
        return false;
    }
    if ($r_qst->parent != 0) {
        return false;
    }
    $r_ctxi = false;
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbi = $DB->get_field("question_versions", "questionbankentryid",
          array("questionid" => $r_qst->id));
        if ($r_qbi === false) {
            return false;
        }
        $r_qcd = $DB->get_field("question_bank_entries", "questioncategoryid",
          array("id" => $r_qbi));
        if ($r_qcd === false) {
            return false;
        }
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qcd));
    } else {
        $r_ctxi = $DB->get_field("question_categories", "contextid",
          array("id" => $r_qst->category));
    }
    if ($r_ctxi === false) {
        return false;
    }
    $r_fld = $r_qst->name;
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld  = pack("a*x", $r_fld);
    $r_rcd = $r_fld;
    $r_txt      = $r_qst->questiontext;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "questiontext";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = "";
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_fld = $r_qst->defaultmark;
    } else {
        $r_fld = $r_qst->defaultgrade;
    }
    if ($r_fld < 0) {
        $r_fld = 0;
    }
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_txt      = $r_qst->generalfeedback;
    $r_scr    = "pluginfile.php";
    $r_cmp = "question";
    $r_far  = "generalfeedback";
    $r_iti    = $r_qst->id;
    $r_fld     = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
        $r_op   = $DB->get_record("qtype_essay_options",
            array("questionid" => $r_qst->id));
        $r_txt      = $r_op->graderinfo;
        $r_cmp = "qtype_essay";
        $r_far  = "graderinfo";
        $r_iti    = $r_qst->id;
    } else {
        $r_asr = $DB->get_record("question_answers",
            array("question" => $r_qst->id));
        if ($r_asr === false) {
            return false;
        }
        $r_txt      = $r_asr->feedback;
        $r_scr    = "pluginfile.php";
        $r_cmp = "question";
        $r_far  = "answerfeedback";
        $r_iti    = $r_asr->id;
    }
    $r_scr = "pluginfile.php";
    $r_fld  = file_rewrite_pluginfile_urls(
        $r_txt, $r_scr, $r_ctxi, $r_cmp, $r_far, $r_iti, null
    );
    $r_fld = respondusws_utf8encode($r_fld);
    $r_fld = pack("a*x", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = 8;
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = time();
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_fld = crc32($r_rcd);
    $r_fld = pack("N", $r_fld);
    $r_rcd .= $r_fld;
    $r_qd = pack("C", 4);
    $r_qd .= pack("N", strlen($r_rcd));
    $r_qd .= $r_rcd;
    return $r_qd;
}
function RWSCEData($r_uf, $r_cf) {
    $r_sps = array(
        basename($r_uf) => $r_uf
    );
    $r_pkr    = get_file_packer("application/zip");
    $r_ok        = $r_pkr->archive_to_pathname($r_sps, $r_cf);
    return $r_ok;
}
function RWSDIData($r_fdat, $r_imd) {
    $r_clntf = false;
    $r_tpp = RWSGTPath();
    $r_tpf = tempnam($r_tpp, "rws");
    $r_ok      = ($r_tpf !== false);
    if ($r_ok) {
        $r_ext = pathinfo($r_tpf, PATHINFO_EXTENSION);
        if (empty($r_ext)) {
            $r_onm = $r_tpf;
            $r_tpf .= ".tmp";
            if (file_exists($r_tpf)) {
                unlink($r_tpf);
            }
            $r_ok = rename($r_onm, $r_tpf);
        }
    }
    if ($r_ok) {
        $r_tmp           = fopen($r_tpf, "wb");
        $r_ok            = ($r_tmp !== false);
        $r_clntf = $r_ok;
    }
    if ($r_ok) {
        $r_by = fwrite($r_tmp, $r_fdat);
        $r_ok    = ($r_by !== false);
    }
    if ($r_clntf) {
        fclose($r_tmp);
    }
    if ($r_ok) {
        $r_pkr  = get_file_packer("application/zip");
        $r_ress = $r_pkr->extract_to_pathname($r_tpf, $r_imd);
        if ($r_ress === false) {
            $r_ok = false;
        }
        if ($r_ok) {
            foreach ($r_ress as $r_nm => $r_sts) {
                if ($r_sts !== true) {
                    $r_ok = false;
                    break;
                }
            }
        }
    }
    if ($r_clntf && file_exists($r_tpf)) {
        unlink($r_tpf);
    }
    return $r_ok;
}
function RWSMTFldr() {
    global $CFG;
    if (respondusws_floatcompare($CFG->version, 2011120500.00, 2) >= 0) {
        $r_tpp = make_temp_directory("rws" . time());
        return $r_tpp;
    } else {
        $r_tpp = RWSGTPath();
        $r_ok      = ($r_tpp !== false);
        if ($r_ok) {
            $r_tpf = tempnam($r_tpp, "rws");
            $r_ok      = ($r_tpf !== false);
        }
        if ($r_ok && file_exists($r_tpf)) {
            $r_ok = unlink($r_tpf);
        }
        if ($r_ok) {
            $r_ok = mkdir($r_tpf);
        }
        if ($r_ok) {
            return $r_tpf;
        } else {
            return false;
        }
    }
}
function RWSEQCQues($r_qci, &$r_qfl, &$r_drp, $r_w64) {
    global $CFG;
    global $DB;
    $r_drp   = 0;
    $r_mss   = 0;
    $r_qd     = "";
    $r_qtps     = array();
    $r_qsts = array();
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbl = $DB->get_records("question_bank_entries", array("questioncategoryid" => $r_qci));
        foreach ($r_qbl as $r_qb) {
            $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
            $r_qvl = $DB->get_records("question_versions",
              array(
                "questionbankentryid" => $r_qb->id,
                "status" => "$r_sts"
              ));
            $r_qv = false;
            foreach ($r_qvl as $r_qvi) {
                if ($r_qv === false || $r_qvi->version > $r_qv->version) {
                    $r_qv = $r_qvi;
                }
            }
            if ($r_qv === false) {
                $r_mss++;
                continue;
            }
            $r_q = $DB->get_record("question", array("id" => $r_qv->questionid));
            if ($r_q === false) {
                $r_mss++;
                continue;
            }
            $r_qsts[] = $r_q;
        }
    } else {
        $r_qsts = $DB->get_records("question", array("category" => $r_qci));
    }
    if (count($r_qsts) > 0) {
        foreach ($r_qsts as $r_q) {
            if ($r_q->parent == 0) {
                $r_qtps[] = $r_q;
            }
        }
    }
    if (count($r_qtps) < 1) {
        RWSSErr("2102");
    }
    $r_ran = 0;
    $r_qd  = RWSEQues(
        $r_qtps, $r_qfl, $r_drp, $r_ran, $r_w64);
    $r_drp += $r_mss;
    return $r_qd;
}
function RWSEQQues($r_qzmi, &$r_qfl, &$r_drp, &$r_ran, $r_w64) {
    global $CFG;
    global $DB;
    $r_drp = 0;
    $r_ran  = 0;
    $r_mss = 0;
    $r_qd = "";
    $r_cmod = $DB->get_record("course_modules",
        array("id" => $r_qzmi));
    if ($r_cmod === false) {
        RWSSErr("2042");
    }
    $r_mr = $DB->get_record("modules",
        array("id" => $r_cmod->module));
    if ($r_mr === false) {
        RWSSErr("2043");
    }
    $r_qiz = $DB->get_record($r_mr->name,
        array("id" => $r_cmod->instance));
    if ($r_qiz === false) {
        RWSSErr("2044");
    }
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qis = array();
        $r_slts = $DB->get_records("quiz_slots", array("quizid" => $r_qiz->id), "slot");
        foreach ($r_slts as $r_slt) {
            $r_qr = $DB->get_record("question_references",
              array(
                "component" => "mod_quiz",
                "questionarea" => "slot",
                "itemid" => $r_slt->id
              ));
            if ($r_qr === false) {
                $r_qsr = $DB->get_records("question_set_references",
                  array(
                    "component" => "mod_quiz",
                    "questionarea" => "slot",
                    "itemid" => $r_slt->id
                  ));
                if ($r_qsr === false) {
                    $r_mss++;
                    continue;
                }
                $r_ran++;
                continue;
            }
            if ($r_qr->version) {
                $r_qv = $DB->get_record("question_versions",
                  array(
                    "questionbankentryid" => $r_qr->questionbankentryid,
                    "version" => $r_qr->version
                  ));
            } else {
                $r_sts = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
                $r_qvl = $DB->get_records("question_versions",
                  array(
                    "questionbankentryid" => $r_qr->questionbankentryid,
                    "status" => "$r_sts"
                  ));
                $r_qv = false;
                foreach ($r_qvl as $r_qvi) {
                    if ($r_qv === false || $r_qvi->version > $r_qv->version) {
                        $r_qv = $r_qvi;
                    }
                }
            }
            if ($r_qv === false) {
                $r_mss++;
                continue;
            }
            $r_qis[] = $r_qv->questionid;
        }
    } else if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
        $r_qis = array();
        $r_slts = $DB->get_records("quiz_slots", array("quizid" => $r_qiz->id), "slot");
        foreach ($r_slts as $r_slt) {
            $r_qis[] = $r_slt->questionid;
        }
    } else {
        $r_qis  = explode(",", $r_qiz->questions);
    }
    $r_qsts = array();
    if ($r_qis !== false) {
        foreach ($r_qis as $r_id) {
            if ($r_id == "0") {
                continue;
            }
            $r_q = $DB->get_record("question", array("id" => $r_id));
            if ($r_q !== false) {
                $r_qsts[] = $r_q;
            } else {
                $r_mss++;
            }
        }
    }
    if (count($r_qsts) < 1) {
        RWSSErr("2103");
    }
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_drp += $r_ran;
    } else {
    }
    $r_qd = RWSEQues(
        $r_qsts, $r_qfl, $r_drp, $r_ran, $r_w64);
    $r_drp += $r_mss;
    return $r_qd;
}
function RWSEQues($r_qsts, &$r_qfl, &$r_drp, &$r_ran, $r_w64) {
        $r_fv = 0;
    $r_fnc   = "rwsexportqdata.zip";
    $r_fnu = "rwsexportqdata.dat";
    $r_qfl                 = "";
    $r_exp              = 0;
    $r_drp               = 0;
    $r_ran                = 0;
    $r_clned      = false;
    $r_clnef     = false;
    $r_clncf = false;
    $r_cloef     = false;
    $r_ok = (count($r_qsts) > 0);
    if (!$r_ok) {
        return "";
    }
    if ($r_ok) {
        $r_exd       = RWSMTFldr();
        $r_ok               = ($r_exd !== false);
        $r_clned = $r_ok;
        if (!$r_ok) {
            $r_err = "2045";
        }
    }
    if ($r_ok) {
        $r_exf       = "$r_exd/$r_fnu";
        $r_hdl            = fopen($r_exf, "wb");
        $r_ok                = ($r_hdl !== false);
        $r_clnef = $r_ok;
        $r_cloef = $r_ok;
        if (!$r_ok) {
            $r_err = "2046";
        }
    }
    if ($r_ok) {
            $r_dat = pack("C*", 0xc7, 0x89, 0xf0, 0x4c, 0xa4, 0x03, 0x47, 0x9b,
                0xa3, 0x7b, 0x29, 0xc6, 0xad, 0xd5, 0x30, 0x81);
        $r_dat .= pack("n", $r_fv);
        $r_by = fwrite($r_hdl, $r_dat);
        $r_ok    = ($r_by !== false);
        if (!$r_ok) {
            $r_err = "2047";
        }
    }
    if ($r_ok) {
        $r_i = 0;
        foreach ($r_qsts as $r_q) {
            $r_i++;
            if ($r_i % 10 == 0) {
                $r_rcd = RWSERRec(time());
                $r_ok2    = ($r_rcd !== false);
                if ($r_ok2) {
                    RWSWNQRec($r_hdl, $r_rcd);
                }
            }
            switch ($r_q->qtype) {
                case RWSSHA:
                    $r_rcd = RWSESARec($r_q);
                    break;
                case RWSTRF:
                    $r_rcd = RWSETFRec($r_q);
                    break;
                case RWSMCH:
                    $r_rcd = RWSEMCRec($r_q);
                    break;
                case RWSMAT:
                    $r_rcd = RWSEMRec($r_q);
                    break;
                case RWSDES:
                    $r_rcd = RWSEDRec($r_q);
                    break;
                case RWSESS:
                    $r_rcd = RWSEERec($r_q);
                    break;
                case RWSCSI:
                case RWSCAL:
                    $r_rcd = RWSECRec($r_q);
                    break;
                case RWSMAN:
                    $r_rcd = RWSEMARec($r_q);
                    break;
                case RWSRND:
                    $r_ran++;
                    $r_rcd = false;
                    break;
                case RWSCMU:
                case RWSNUM:
                case RWSRSM:
                default:
                    $r_rcd = false;
                    break;
            }
            $r_ok2 = ($r_rcd !== false);
            if ($r_ok2) {
                $r_ok2 = RWSWNQRec($r_hdl, $r_rcd);
            }
            if ($r_ok2) {
                $r_exp++;
            } else {
                $r_drp++;
            }
        }
    }
    if ($r_cloef) {
        fclose($r_hdl);
    }
    if ($r_ok && $r_exp > 0) {
        $r_cf       = "$r_exd/$r_fnc";
        $r_ok                    = RWSCEData($r_exf, $r_cf);
        $r_clncf = $r_ok;
        if (!$r_ok) {
            $r_err = "2048";
        }
    }
    if ($r_ok && $r_exp > 0) {
        $r_cpr = file_get_contents($r_cf);
        $r_ok         = ($r_cpr !== false);
        if (!$r_ok) {
            $r_err = "2049";
        }
    }
    if ($r_ok && $r_exp > 0 && $r_w64) {
        $r_ecd = base64_encode($r_cpr);
    }
    if ($r_clnef && file_exists($r_exf)) {
        unlink($r_exf);
    }
    if ($r_clncf && file_exists($r_cf)) {
        unlink($r_cf);
    }
    if ($r_clned && file_exists($r_exd)) {
        rmdir($r_exd);
    }
    if (!$r_ok) {
        RWSSErr($r_err);
    }
    if ($r_exp == 0) {
        if ($r_drp == $r_ran) {
            RWSSErr("2122");
        } else {
            RWSSErr("2104");
        }
    }
    $r_qfl = $r_fnc;
    if ($r_w64) {
        return $r_ecd;
    } else {
        return $r_cpr;
    }
}
function RWSUQGrades($r_qiz) {
    $r_gi = grade_item::fetch(array(
                                         'itemtype'     => 'mod',
                                         'itemmodule'   => $r_qiz->modulename,
                                         'iteminstance' => $r_qiz->instance,
                                         'itemnumber'   => 0,
                                         'courseid'     => $r_qiz->course
                                    ));
    if ($r_gi && $r_gi->idnumber != $r_qiz->cmidnumber) {
        $r_gi->idnumber = $r_qiz->cmidnumber;
        $r_gi->update();
    }
    $r_its = grade_item::fetch_all(array(
                                        'itemtype'     => 'mod',
                                        'itemmodule'   => $r_qiz->modulename,
                                        'iteminstance' => $r_qiz->instance,
                                        'courseid'     => $r_qiz->course
                                   ));
    if ($r_its && isset($r_qiz->gradecat)) {
        if ($r_qiz->gradecat == -1) {
            $r_gcat           = new grade_category();
            $r_gcat->courseid = $r_qiz->course;
            $r_gcat->fullname = $r_qiz->name;
            $r_gcat->insert();
            if ($r_gi) {
                $r_par = $r_gi->get_parent_category();
                $r_gcat->set_parent($r_par->id);
            }
            $r_qiz->gradecat = $r_gcat->id;
        }
        foreach ($r_its as $r_iti => $r_un) {
            $r_its[$r_iti]->set_parent($r_qiz->gradecat);
            if ($r_iti == $r_gi->id) {
                $r_gi = $r_its[$r_iti];
            }
        }
    }
    if ($r_ocs = grade_outcome::fetch_all_available($r_qiz->course)) {
        $r_gis = array();
        $r_mit = 999;
        if ($r_its) {
            foreach ($r_its as $r_it) {
                if ($r_it->itemnumber > $r_mit) {
                    $r_mit = $r_it->itemnumber;
                }
            }
        }
        foreach ($r_ocs as $r_oc) {
            $r_eln = 'outcome_' . $r_oc->id;
            if (property_exists($r_qiz, $r_eln) and $r_qiz->$r_eln) {
                if ($r_its) {
                    foreach ($r_its as $r_it) {
                        if ($r_it->outcomeid == $r_oc->id) {
                            continue 2;
                        }
                    }
                }
                $r_mit++;
                $r_oi               = new grade_item();
                $r_oi->courseid     = $r_qiz->course;
                $r_oi->itemtype     = 'mod';
                $r_oi->itemmodule   = $r_qiz->modulename;
                $r_oi->iteminstance = $r_qiz->instance;
                $r_oi->itemnumber   = $r_mit;
                $r_oi->itemname     = $r_oc->fullname;
                $r_oi->outcomeid    = $r_oc->id;
                $r_oi->gradetype    = GRADE_TYPE_SCALE;
                $r_oi->scaleid      = $r_oc->scaleid;
                $r_oi->insert();
                if ($r_gi) {
                    $r_oi->set_parent($r_gi->categoryid);
                    $r_oi->move_after_sortorder($r_gi->sortorder);
                } else if (isset($r_qiz->gradecat)) {
                    $r_oi->set_parent($r_qiz->gradecat);
                }
            }
        }
    }
}
function RWSDQCat($r_qci) {
    global $DB;
    global $CFG;
    $r_chn = $DB->get_records("question_categories",
        array("parent" => $r_qci));
    if (count($r_chn) > 0) {
        foreach ($r_chn as $r_chd) {
            RWSDQCat($r_chd->id);
        }
    }
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbl = $DB->get_records("question_bank_entries", array("questioncategoryid" => $r_qci));
        foreach ($r_qbl as $r_qb) {
            $r_qvl = $DB->get_records("question_versions", array("questionbankentryid" => $r_qb->id));
            foreach ($r_qvl as $r_qv) {
                $r_q = $DB->get_record("question", array("id" => $r_qv->questionid));
                if ($r_q === false) {
                    continue;
                }
                $r_qsts[] = $r_q;
            }
        }
    } else {
        $r_qsts = $DB->get_records("question", array("category" => $r_qci));
    }
    if (count($r_qsts) > 0) {
        foreach ($r_qsts as $r_q) {
            if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
                question_delete_question($r_q->id);
            } else {
                delete_question($r_q->id);
            }
        }
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        } else {
            $DB->delete_records("question", array("category" => $r_qci));
        }
    }
    $DB->delete_records("question_categories", array("id" => $r_qci));
}
function RWSIQCUsed($r_qci) {
    global $DB;
    global $CFG;
    $r_chn = $DB->get_records("question_categories",
        array("parent" => $r_qci));
    if (count($r_chn) > 0) {
        foreach ($r_chn as $r_chd) {
            if (RWSIQCUsed($r_chd->id)) {
                return true;
            }
        }
    }
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        $r_qbl = $DB->get_records("question_bank_entries", array("questioncategoryid" => $r_qci));
        foreach ($r_qbl as $r_qb) {
            $r_qvl = $DB->get_records("question_versions", array("questionbankentryid" => $r_qb->id));
            foreach ($r_qvl as $r_qv) {
                $r_q = $DB->get_record("question", array("id" => $r_qv->questionid));
                if ($r_q === false) {
                    continue;
                }
                $r_qsts[] = $r_q;
            }
        }
    } else {
        $r_qsts = $DB->get_records("question", array("category" => $r_qci));
    }
    if (count($r_qsts) > 0) {
        if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
            $r_qis = array();
            foreach ($r_qsts as $r_q) {
                $r_qis[] = $r_q->id;
            }
            if (questions_in_use($r_qis)) {
                return true;
            }
        } else {
            foreach ($r_qsts as $r_q) {
                if (count(question_list_instances($r_q->id)) > 0) {
                    return true;
                }
            }
        }
    }
    return false;
}
function RWSPAtt($r_qty, $r_cid, $r_ctxi, $r_cmp, $r_far, $r_iti, $r_txt) {
    global $CFG;
    global $RWSUID;
    $r_l = strlen($r_txt);
    $r_out = "";
    $r_p = 0;
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_sctx = context_course::instance($r_cid);
    } else {
        $r_sctx = get_context_instance(CONTEXT_COURSE, $r_cid);
    }
    $r_sup = "%%COURSEPATH%%";
    $r_dup = "@@PLUGINFILE@@";
    while ($r_p < $r_l) {
        $r_nx = strpos($r_txt, "$r_sup/", $r_p);
        if ($r_nx === false) {
            break;
        }
        $r_st = $r_p;
        $r_end   = $r_nx;
        $r_out .= substr($r_txt, $r_st, $r_end - $r_st);
        $r_st = $r_nx + strlen("$r_sup/");
        $r_end   = strpos($r_txt, "/", $r_st);
        if ($r_end === false) {
            $r_end   = $r_st;
            $r_st = $r_nx;
            $r_out .= substr($r_txt, $r_st, $r_end - $r_st);
            $r_p = $r_end;
            continue;
        }
        $r_ff = substr($r_txt, $r_st, $r_end - $r_st);
        $r_st = $r_end + 1;
        $r_end   = strpos($r_txt, "\"", $r_st);
        if ($r_end === false) {
            $r_end   = $r_st;
            $r_st = $r_nx;
            $r_out .= substr($r_txt, $r_st, $r_end - $r_st);
            $r_p = $r_end;
            continue;
        }
        $r_fn = substr($r_txt, $r_st, $r_end - $r_st);
        $r_p       = $r_end;
        $r_sctxid = $r_sctx->id;
        $r_scmp = "mod_respondusws";
        $r_sfar  = "upload";
        $r_sitm    = $RWSUID;
        $r_sfip  = "/$r_ff/";
        $r_sfnm  = $r_fn;
        $r_dcxi = $r_ctxi;
        $r_dcmp = $r_cmp;
        $r_dfar  = $r_far;
        $r_ditm    = $r_iti;
        $r_dfip  = "/";
        $r_dfnm  = $r_fn;
        try {
            $r_fs   = get_file_storage();
            $r_fl = $r_fs->get_file($r_sctxid, $r_scmp,
                $r_sfar, $r_sitm, $r_sfip, $r_sfnm);
        } catch (Exception $r_e) {
            $r_fl = false;
        }
        if ($r_fl === false) {
            $r_st = $r_nx;
            $r_end   = $r_p;
            $r_out .= substr($r_txt, $r_st, $r_end - $r_st);
            continue;
        }
        try {
            $r_fex = $r_fs->file_exists($r_dcxi, $r_dcmp,
                $r_dfar, $r_ditm, $r_dfip, $r_dfnm);
            if ($r_fex == false) {
                $r_finf = array(
                    "contextid" => $r_dcxi,
                    "component" => $r_dcmp,
                    "filearea"  => $r_dfar,
                    "itemid"    => $r_ditm,
                    "filepath"  => $r_dfip,
                    "filename"  => $r_dfnm
                );
                if ($r_fs->create_file_from_storedfile($r_finf, $r_fl)) {
                    $r_fex = true;
                }
            }
        } catch (Exception $r_e) {
            $r_fex = false;
        }
        if ($r_fex == false) {
            $r_st = $r_nx;
            $r_end   = $r_p;
            $r_out .= substr($r_txt, $r_st, $r_end - $r_st);
            continue;
        }
        $r_url = $r_dup . $r_dfip . $r_dfnm;
        $r_out .= $r_url;
    }
    if ($r_p < $r_l) {
        $r_st = $r_p;
        $r_end   = $r_l;
        $r_out .= substr($r_txt, $r_st, $r_end - $r_st);
    }
    return $r_out;
}
function RWSDSAct($r_ac) {
    RWSELog("action=$r_ac");
    if ($r_ac == "phpinfo") {
        RWSAPInfo();
    } else if ($r_ac == "serviceinfo") {
        RWSASInfo();
    } else if ($r_ac == "login") {
        RWSAILog();
    } else if ($r_ac == "logout") {
        RWSAOLog();
    } else if ($r_ac == "courselist") {
        RWSACList();
    } else if ($r_ac == "sectionlist") {
        RWSASList();
    } else if ($r_ac == "quizlist") {
        RWSAQList();
    } else if ($r_ac == "qcatlist") {
        RWSAQCList();
    } else if ($r_ac == "addqcat") {
        RWSAAQCat();
    } else if ($r_ac == "deleteqcat") {
        RWSADQCat();
    } else if ($r_ac == "deletequiz") {
        RWSADQuiz();
    } else if ($r_ac == "addquiz") {
        RWSAAQuiz();
    } else if ($r_ac == "updatequiz") {
        RWSAUQuiz();
    } else if ($r_ac == "addqlist") {
        RWSAAQList();
    } else if ($r_ac == "addqrand") {
        RWSAAQRand();
    } else if ($r_ac == "importqdata") {
        RWSAIQData();
    } else if ($r_ac == "getquiz") {
        RWSAGQuiz();
    } else if ($r_ac == "exportqdata") {
        RWSAEQData();
    } else if ($r_ac == "uploadfile") {
        RWSAUFile();
    } else if ($r_ac == "dnloadfile") {
        RWSADFile();
    } else if ($r_ac == "authstart") {
        RWSAAStart();
    } else if ($r_ac == "authfinish") {
        RWSAAFinish();
    } else {
        RWSSErr("2050");
    }
}
function RWSAPInfo() {
    global $RWSUID;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    if (!is_siteadmin($RWSUID)) {
        RWSSErr("2107");
    }
    phpinfo();
    exit;
}
function RWSASInfo() {
    global $CFG;
    global $RWSLB;
    global $RWSUID;
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_ilg = isloggedin();
    $r_ia = false;
    if ($r_ilg) {
        RWSCRAuth();
        $r_ia = is_siteadmin($RWSUID);
    }
    $r_su = RWSGSUrl(false, true);
    $r_ver      = "";
    $r_rel      = "";
    $r_req     = "";
    $r_vf = RWSGMPath() . "/version.php";
    if (is_readable($r_vf)) {
        include($r_vf);
    }
    if (isset($respondusws_info)) {
        if (!empty($respondusws_info->version)) {
            $r_ver = $respondusws_info->version;
        }
        if (!empty($respondusws_info->release)) {
            $r_rel = $respondusws_info->release;
        }
        if (!empty($respondusws_info->requires)) {
            $r_req = $respondusws_info->requires;
        }
        if (!empty($respondusws_info->requires)) {
            $r_req = $respondusws_info->requires;
        }
    }
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    echo "<service_info>\r\n";
    if ($r_ia) {
        echo "\t<description>Respondus 4.0 Web Service Extension For Moodle</description>\r\n";
    } else {
        echo "\t<description>(authentication required)</description>\r\n";
    }
    if (!empty($r_ver)) {
        echo "\t<module_version>";
        if ($r_bv >= 2010042801) {
            echo respondusws_utf8encode(htmlspecialchars($r_ver));
        } else {
            echo "2009093000";
        }
        echo "</module_version>\r\n";
    } else {
        echo "\t<module_version />\r\n";
    }
    if (!empty($r_rel)) {
        echo "\t<module_release>";
        if ($r_bv >= 2010042801) {
            echo respondusws_utf8encode(htmlspecialchars($r_rel));
        } else {
            echo "1.0.2";
        }
        echo "</module_release>\r\n";
    } else {
        echo "\t<module_release />\r\n";
    }
    if ($r_bv >= 2010042801) {
        echo "\t<module_behavior>";
        echo respondusws_utf8encode(htmlspecialchars($r_bv));
        echo "</module_behavior>\r\n";
    }
    if ($r_ia) {
        if (!empty($r_req)) {
            echo "\t<module_requires>";
            echo respondusws_utf8encode(htmlspecialchars($r_req));
            echo "</module_requires>\r\n";
        } else {
            echo "\t<module_requires />\r\n";
        }
    } else {
        echo "\t<module_requires>(authentication required)</module_requires>\r\n";
    }
    if ($r_bv <= 2014102100) {
        echo "\t<module_latest />\r\n";
    }
    if ($r_ia) {
        echo "\t<endpoint>";
        echo respondusws_utf8encode(htmlspecialchars($r_su));
        echo "</endpoint>\r\n";
    } else {
        echo "\t<endpoint>(authentication required)</endpoint>\r\n";
    }
    if ($r_ia) {
        echo "\t<whoami>";
        $r_who = trim(exec("whoami"));
        echo respondusws_utf8encode(htmlspecialchars($r_who));
        echo "</whoami>\r\n";
    } else {
        echo "\t<whoami>(authentication required)</whoami>\r\n";
    }
    if ($r_ilg) {
        echo "\t<moodle_version>";
        echo respondusws_utf8encode(htmlspecialchars($CFG->version));
        echo "</moodle_version>\r\n";
    } else {
        echo "\t<moodle_version>(authentication required)</moodle_version>\r\n";
    }
    if ($r_ilg) {
        echo "\t<moodle_release>";
        echo respondusws_utf8encode(htmlspecialchars($CFG->release));
        echo "</moodle_release>\r\n";
    } else {
        echo "\t<moodle_release>(authentication required)</moodle_release>\r\n";
    }
    if ($r_ia) {
        echo "\t<moodle_site_id>";
        echo respondusws_utf8encode(htmlspecialchars(SITEID));
        echo "</moodle_site_id>\r\n";
    } else {
        echo "\t<moodle_site_id>(authentication required)</moodle_site_id>\r\n";
    }
    if ($r_ia) {
        echo "\t<moodle_maintenance>";
        if (!empty($CFG->maintenance_enabled)
            || file_exists($CFG->dataroot . "/" . SITEID . "/maintenance.html")
        ) {
            echo "yes";
        } else {
            echo "no";
        }
        echo "</moodle_maintenance>\r\n";
    } else if ($r_bv >= 2010063001) {
        echo "\t<moodle_maintenance>(authentication required)</moodle_maintenance>\r\n";
    } else {
        echo "\t<moodle_maintenance>no</moodle_maintenance>\r\n";
    }
    if ($r_ia) {
        $r_mn = get_list_of_plugins("mod");
        if ($r_mn && count($r_mn) > 0) {
            $r_ml = implode(",", $r_mn);
            echo "\t<moodle_module_types>";
            echo respondusws_utf8encode(htmlspecialchars(trim($r_ml)));
            echo "</moodle_module_types>\r\n";
        } else {
            echo "\t<moodle_module_types />\r\n";
        }
    } else {
        echo "\t<moodle_module_types>(authentication required)</moodle_module_types>\r\n";
    }
    $r_qtn = get_list_of_plugins("question/type");
    if (!$r_qtn) {
        $r_qtn = array();
    }
    $r_irx = false;
    if (count($r_qtn) > 0) {
        foreach ($r_qtn as $r_qn) {
            if (strcasecmp($r_qn, RWSRXP) == 0) {
                $r_irx = true;
                break;
            }
        }
    }
    if ($r_ia) {
        if (count($r_qtn) > 0) {
            $r_qtl = implode(",", $r_qtn);
            echo "\t<moodle_question_types>";
            echo respondusws_utf8encode(htmlspecialchars(trim($r_qtl)));
            echo "</moodle_question_types>\r\n";
        } else {
            echo "\t<moodle_question_types />\r\n";
        }
    } else {
        echo "\t<moodle_question_types>(authentication required)</moodle_question_types>\r\n";
    }
    if ($r_ilg) {
        echo "\t<cloze_regexp_support>";
        $r_pth = "$CFG->dirroot/question/type/multianswer/questiontype.php";
        $r_dat = file_get_contents($r_pth);
        if ($r_dat !== false
            && strpos($r_dat, "ANSWER_REGEX_ANSWER_TYPE_REGEXP") !== false
        ) {
            if ($r_bv >= 2010063001) {
                if ($r_irx) {
                    echo "yes";
                } else {
                    echo "no";
                }
            } else {
                echo "yes";
            }
        } else {
            echo "no";
        }
        echo "</cloze_regexp_support>\r\n";
    } else if ($r_bv >= 2010063001) {
        echo "\t<cloze_regexp_support>(authentication required)</cloze_regexp_support>\r\n";
    } else {
        echo "\t<cloze_regexp_support>no</cloze_regexp_support>\r\n";
    }
    if ($r_ilg) {
        echo "\t<ldb_module_detected>";
        if ($RWSLB->mex || $RWSLB->bex) {
            echo "yes";
        } else {
            echo "no";
        }
        echo "</ldb_module_detected>\r\n";
    } else if ($r_bv >= 2010063001) {
        echo "\t<ldb_module_detected>(authentication required)</ldb_module_detected>\r\n";
    } else {
        echo "\t<ldb_module_detected>no</ldb_module_detected>\r\n";
    }
    if ($r_ilg) {
        echo "\t<ldb_module_ok>";
        if ($RWSLB->mok || $RWSLB->bok) {
            echo "yes";
        } else {
            echo "no";
        }
        echo "</ldb_module_ok>\r\n";
    } else if ($r_bv >= 2010063001) {
        echo "\t<ldb_module_ok>(authentication required)</ldb_module_ok>\r\n";
    } else {
        echo "\t<ldb_module_ok>no</ldb_module_ok>\r\n";
    }
    echo "</service_info>\r\n";
    exit;
}
function RWSDblOut($r_val) {
    $r_t  = unpack("C*", pack("S", 256));
    $r_chrs = array_values(unpack("C*", pack("d", $r_val)));
    if ($r_t[1] == 1) {
        $r_by = $r_chrs;
    } else {
        $r_by = array_reverse($r_chrs);
    }
    $r_bn = "";
    foreach ($r_by as $r_b) {
        $r_bn .= pack("C", $r_b);
    }
    return $r_bn;
}
function RWSDblIn($r_val) {
    $r_t  = unpack("C*", pack("S", 256));
    $r_by = array_values(unpack("C*", $r_val));
    if ($r_t[1] == 1) {
        $r_chrs = $r_by;
    } else {
        $r_chrs = array_reverse($r_by);
    }
    $r_bn = "";
    foreach ($r_chrs as $r_c) {
        $r_bn .= pack("C", $r_c);
    }
    $r_d = unpack("d", $r_bn);
    return $r_d[1];
}
function RWSGCFCat($r_ctx) {
    global $DB;
    switch ($r_ctx->contextlevel) {
        case CONTEXT_COURSE:
            $r_cid = $r_ctx->instanceid;
            break;
        case CONTEXT_MODULE:
            $r_cid = $DB->get_field("course_modules", "course",
                array("id" => $r_ctx->instanceid));
            if ($r_cid === false) {
                RWSSErr("2111");
            }
            break;
        case CONTEXT_COURSECAT:
        case CONTEXT_SYSTEM:
            $r_cid = SITEID;
            break;
        default:
            RWSSErr("2053");
    }
    return $r_cid;
}
function RWSAILog() {
    global $CFG;
    global $DB;
    global $SESSION;
    global $RWSIHLOG;
    global $RWSECAS;
    $SESSION->respondusws_module_auth = false;
    if (!$RWSIHLOG) {
        if ($CFG->loginhttps && !$CFG->sslproxy) {
            if (!isset($_SERVER["HTTPS"])
                || empty($_SERVER["HTTPS"])
                || strcasecmp($_SERVER["HTTPS"], "off") == 0
            ) {
                RWSSErr("4001");
            }
        }
    }
    $r_usrn = RWSGSOpt("username", PARAM_RAW);
    if ($r_usrn === false || strlen($r_usrn) == 0) {
        RWSSErr("2054");
    }
    $r_pw = RWSGSOpt("password", PARAM_RAW);
    if ($r_pw === false || strlen($r_pw) == 0) {
        RWSSErr("2055");
    }
    if (isloggedin()) {
        RWSSErr("2056");
    }
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    if ($r_bv >= 2013061700) {
        $r_rws = $DB->get_record("respondusws", array("course" => SITEID));
        if ($r_rws === false) {
            RWSSErr("2007");
        }
        $r_usr = $DB->get_record("user", array("username" => $r_usrn,
          "mnethostid" => $CFG->mnet_localhost_id));
        $r_ok = ($r_usr !== false);
        if ($r_ok) {
            $r_auu = $DB->get_record("respondusws_auth_users",
              array("responduswsid" => $r_rws->id, "userid" => $r_usr->id));
            $r_ok = ($r_auu !== false);
        }
        if ($r_ok) {
            $r_h = sha1($r_pw);
            $r_ok = (strcmp($r_h, $r_auu->authtoken) == 0);
        }
        if ($r_ok) {
            $r_ctm = time();
            $r_mxt = $r_auu->timeissued
              + (60 * 60 * 24 * RWSAUM);
            if ($r_ctm < $r_auu->timeissued || $r_ctm > $r_mxt) {
                RWSSErr("2115");
            }
        }
        if ($r_ok) {
            $r_usrn = get_config("respondusws", "username");
            if ($r_usrn === false || strlen($r_usrn) == 0) {
                RWSSErr("2116");
            }
            $r_pw = get_config("respondusws", "password");
            if ($r_pw === false || strlen($r_pw) == 0) {
                RWSSErr("2117");
            }
            $RWSECAS = false;
            $SESSION->respondusws_module_auth = true;
        }
    }
    RWSLIMUser($r_usrn, $r_pw, false);
}
function RWSAOLog() {
    global $SESSION;
    RWSCMAuth();
    RWSLOMUser();
    unset($SESSION->respondusws_module_auth);
}
function RWSPLOCas($r_csp) {
    global $RWSESL3;
    if (isset($_SESSION['rwscas']['cookiejar'])) {
        $r_ckf = $_SESSION['rwscas']['cookiejar'];
    }
    if (empty($r_csp->config->hostname)
        || !$r_csp->config->logoutcas
    ) {
        if (isset($r_ckf)) {
            if (file_exists($r_ckf)) {
                unlink($r_ckf);
            }
            unset($_SESSION['rwscas']['cookiejar']);
        }
        unset($_SESSION['rwscas']);
        return;
    }
    list($r_v1, $r_v2, $r_v3) = explode(".", phpCAS::getVersion());
    $r_csp->connectCAS();
    $r_lou = phpCAS::getServerLogoutURL();
    $r_ch = curl_init();
    curl_setopt($r_ch, CURLOPT_URL, $r_lou);
    curl_setopt($r_ch, CURLOPT_HTTPGET, true);
    curl_setopt($r_ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($r_ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($r_ch, CURLOPT_FAILONERROR, true);
    curl_setopt($r_ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($r_ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($r_ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($r_ch, CURLOPT_USERAGENT, "PHP");
    if (isset($r_ckf)) {
        curl_setopt($r_ch, CURLOPT_COOKIEFILE, $r_ckf);
        curl_setopt($r_ch, CURLOPT_COOKIEJAR, $r_ckf);
    }
    curl_exec($r_ch);
    curl_close($r_ch);
    if (isset($r_ckf)) {
        if (file_exists($r_ckf)) {
            unlink($r_ckf);
        }
        unset($_SESSION['rwscas']['cookiejar']);
    }
    unset($_SESSION['rwscas']);
    session_unset();
    session_destroy();
}
function RWSACList() {
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_crss = RWSGUMCList();
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    if (count($r_crss) == 0) {
        echo "<courselist />\r\n";
        exit;
    }
    echo "<courselist>\r\n";
    foreach ($r_crss as $r_c) {
        echo "\t<course>\r\n";
        echo "\t\t<name>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_c->fullname)));
        echo "</name>\r\n";
        echo "\t\t<id>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_c->id)));
        echo "</id>\r\n";
        echo "\t</course>\r\n";
    }
    echo "</courselist>\r\n";
    exit;
}
function RWSASList() {
    global $CFG;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_pm = RWSGSOpt("courseid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2057");
    }
    $r_cid = intval($r_pm);
    $r_crs    = RWSCMUCourse($r_cid);
    $r_secs = RWSGUVSList($r_cid);
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    if (count($r_secs) == 0) {
        echo "<sectionlist />\r\n";
        exit;
    }
    echo "<sectionlist>\r\n";
    if ($r_bv < 2011020100) {
        if (respondusws_floatcompare($CFG->version, 2012120300, 2) >= 0) {
            $r_fnm = get_section_name($r_crs, $r_secs[0]);
        } else {
            $r_fnm = get_generic_section_name($r_crs->format, $r_secs[0]);
        }
        $r_p = strrpos($r_fnm, " ");
        if ($r_p !== false) {
            $r_fnm = substr($r_fnm, 0, $r_p);
        }
        echo "\t<format_name>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_fnm)));
        echo "</format_name>\r\n";
    }
    foreach ($r_secs as $r_s) {
        echo "\t<section>\r\n";
        if ($r_bv >= 2011020100) {
            $r_nm = get_section_name($r_crs, $r_s);
            echo "\t\t<name>";
            echo respondusws_utf8encode(htmlspecialchars($r_nm));
            echo "</name>\r\n";
        }
        $r_sum = trim($r_s->summary);
        if (strlen($r_sum) > 0) {
            echo "\t\t<summary>";
            echo respondusws_utf8encode(htmlspecialchars($r_sum));
            echo "</summary>\r\n";
        } else {
            echo "\t\t<summary />\r\n";
        }
        echo "\t\t<id>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_s->id)));
        echo "</id>\r\n";
        echo "\t\t<relative_index>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_s->section)));
        echo "</relative_index>\r\n";
        echo "\t</section>\r\n";
    }
    echo "</sectionlist>\r\n";
    exit;
}
function RWSAQList() {
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("courseid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2057");
    }
    $r_cid = intval($r_pm);
    RWSCMUCourse($r_cid);
    $r_vqzs = RWSGUVQList($r_cid);
    if (count($r_vqzs) > 0) {
        $r_mqzs = RWSGUMQList($r_vqzs);
    } else {
        $r_mqzs = array();
    }
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    if (count($r_vqzs) == 0) {
        echo "<quizlist />\r\n";
        exit;
    }
    echo "<quizlist>\r\n";
    foreach ($r_vqzs as $r_q) {
        echo "\t<quiz>\r\n";
        echo "\t\t<name>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_q->name)));
        echo "</name>\r\n";
        echo "\t\t<id>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_q->id)));
        echo "</id>\r\n";
        echo "\t\t<section_id>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_q->section)));
        echo "</section_id>\r\n";
        echo "\t\t<writable>";
        if (in_array($r_q, $r_mqzs)) {
            echo "yes";
        } else {
            echo "no";
        }
        echo "</writable>\r\n";
        echo "\t</quiz>\r\n";
    }
    echo "</quizlist>\r\n";
    exit;
}
function RWSAQCList() {
    global $CFG;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_pm = RWSGSOpt("courseid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2057");
    }
    $r_cid = intval($r_pm);
    RWSCMUCourse($r_cid);
    $r_qcs = RWSGUQCats($r_cid);
    if ($r_bv >= 2010063001) {
        foreach ($r_qcs as $r_qc) {
            if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
                $r_ctx = context::instance_by_id($r_qc->contextid);
            } else {
                $r_ctx = get_context_instance_by_id($r_qc->contextid);
            }
            $r_qc->ci = RWSGCFCat($r_ctx);
        }
    }
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    if (count($r_qcs) == 0) {
        echo "<qcatlist />\r\n";
        exit;
    }
    echo "<qcatlist>\r\n";
    foreach ($r_qcs as $r_qc) {
        echo "\t<category>\r\n";
        echo "\t\t<name>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_qc->name)));
        echo "</name>\r\n";
        echo "\t\t<id>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_qc->id)));
        echo "</id>\r\n";
        if (!empty($r_qc->parent) && array_key_exists($r_qc->parent, $r_qcs)) {
            echo "\t\t<parent_id>";
            echo respondusws_utf8encode(htmlspecialchars(trim($r_qc->parent)));
            echo "</parent_id>\r\n";
        }
        if ($r_bv >= 2010063001) {
            if ($r_qc->ci == SITEID) {
                echo "\t\t<system>yes</system>\r\n";
            } else {
                echo "\t\t<system>no</system>\r\n";
            }
        }
        echo "\t</category>\r\n";
    }
    echo "</qcatlist>\r\n";
    exit;
}
function RWSAAQCat() {
    global $CFG;
    global $DB;
    global $RWSUID;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("name", PARAM_TEXT);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2058");
    }
    $r_qcn = trim(clean_text(strip_tags($r_pm, "<lang><span>")));
    if (strlen($r_qcn) > 254) {
        RWSSErr("2059");
    }
    $r_cid = false;
    $r_pm     = RWSGSOpt("courseid", PARAM_ALPHANUM);
    if ($r_pm !== false && strlen($r_pm) > 0) {
        $r_cid = intval($r_pm);
    }
    $r_pi = false;
    $r_pm     = RWSGSOpt("parentid", PARAM_ALPHANUM);
    if ($r_pm !== false && strlen($r_pm) > 0) {
        $r_pi = intval($r_pm);
    }
    if ($r_cid === false && $r_pi === false) {
        RWSSErr("2060");
    } else if ($r_cid !== false && $r_pi === false) {
        RWSCMUCourse($r_cid);
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context_course::instance($r_cid);
        } else {
            $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
        }
        $r_pi = 0;
        if (respondusws_floatcompare($CFG->version, 2018051700, 2) >= 0) {
            $r_top = question_get_top_category($r_ctx->id, true);
            $r_pi = $r_top->id;
        }
    } else if ($r_cid === false && $r_pi !== false) {
        $r_rcd = $DB->get_record("question_categories",
            array("id" => $r_pi));
        if ($r_rcd === false) {
            RWSSErr("2061");
        }
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context::instance_by_id($r_rcd->contextid);
        } else {
            $r_ctx = get_context_instance_by_id($r_rcd->contextid);
        }
        $r_cid = RWSGCFCat($r_ctx);
        RWSCMUCourse($r_cid);
        if ($r_cid == SITEID) {
            if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
                $r_ctx = context_system::instance();
            } else {
                $r_ctx = get_context_instance(CONTEXT_SYSTEM);
            }
        } else {
            if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
                $r_ctx = context_course::instance($r_cid);
            } else {
                $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
            }
        }
    } else {
        RWSCMUCourse($r_cid);
        $r_rcd = $DB->get_record("question_categories",
            array("id" => $r_pi));
        if ($r_rcd === false) {
            RWSSErr("2061");
        }
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context::instance_by_id($r_rcd->contextid);
        } else {
            $r_ctx = get_context_instance_by_id($r_rcd->contextid);
        }
        $r_qcci = RWSGCFCat($r_ctx);
        if ($r_qcci != $r_cid) {
            if (is_siteadmin($RWSUID)) {
                if ($r_qcci != SITEID) {
                    RWSSErr("2110");
                } else {
                    $r_ctx = $r_sys;
                }
            } else {
                RWSSErr("2062");
            }
        } else {
            if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
                $r_ctx = context_course::instance($r_cid);
            } else {
                $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
            }
        }
    }
    $r_qca             = new stdClass();
    $r_qca->parent     = $r_pi;
    $r_qca->contextid  = $r_ctx->id;
    $r_qca->name       = $r_qcn;
    $r_qca->info       = "Created by Respondus";
    $r_qca->infoformat = FORMAT_HTML;
    $r_qca->sortorder  = 999;
    $r_qca->stamp      = make_unique_id_code();
    $r_qci          = $DB->insert_record("question_categories", $r_qca);
    rebuild_course_cache($r_cid);
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    echo "<addqcat>\r\n";
    echo "\t<name>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_qcn)));
    echo "</name>\r\n";
    echo "\t<id>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_qci)));
    echo "</id>\r\n";
    if ($r_pi != 0) {
        echo "\t<parent_id>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_pi)));
        echo "</parent_id>\r\n";
    }
    echo "</addqcat>\r\n";
    exit;
}
function RWSADQCat() {
    global $CFG;
    global $DB;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("qcatid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2064");
    }
    $r_qci = intval($r_pm);
    $r_qca = $DB->get_record("question_categories", array("id" => $r_qci));
    if ($r_qca === false) {
        RWSSErr("2065");
    }
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context::instance_by_id($r_qca->contextid);
    } else {
        $r_ctx = get_context_instance_by_id($r_qca->contextid);
    }
    $r_cid = RWSGCFCat($r_ctx);
    RWSCMUCourse($r_cid);
    if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
        \qbank_managecategories\helper::question_can_delete_cat($r_qci);
    } else {
        question_can_delete_cat($r_qci);
    }
    if (RWSIQCUsed($r_qci)) {
        RWSSErr("2066");
    }
    RWSDQCat($r_qci);
    rebuild_course_cache($r_cid);
    RWSSStat("1002");
}
function RWSADQuiz() {
    global $CFG;
    global $RWSLB;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("quizid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2067");
    }
    $r_qzmi = intval($r_pm);
    $r_rcd = RWSCMUQuiz($r_qzmi);
    $r_cid = $r_rcd->course;
    RWSCMUCourse($r_cid, true);
    if (respondusws_floatcompare($CFG->version, 2013051400, 2) >= 0) {
        course_delete_module($r_qzmi);
    } else {
        if (!quiz_delete_instance($r_rcd->instance)) {
            RWSSErr("2068");
        }
        if (!delete_course_module($r_qzmi)) {
            RWSSErr("2069");
        }
        if (!delete_mod_from_section($r_qzmi, $r_rcd->section)) {
            RWSSErr("2070");
        }
    }
    if ($RWSLB->mok) {
        lockdown_delete_options($r_rcd->instance);
    } else if ($RWSLB->bok) {
        lockdown_delete_options($r_rcd->instance);
    }
    rebuild_course_cache($r_cid);
    RWSSStat("1003");
}
function RWSAAQuiz() {
    global $CFG;
    global $DB;
    global $RWSLB;
    global $RWSUID;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("courseid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2057");
    }
    $r_cid = intval($r_pm);
    $r_crs    = RWSCMUCourse($r_cid, true);
    $r_si = false;
    $r_pm      = RWSGSOpt("sectionid", PARAM_ALPHANUM);
    if ($r_pm !== false && strlen($r_pm) > 0) {
        $r_si = intval($r_pm);
    }
    if ($r_si === false) {
        $r_sr = 0;
    } else {
        $r_sec = $DB->get_record("course_sections",
            array("id" => $r_si));
        if ($r_sec === false) {
            RWSSErr("2071");
        }
        if ($r_sec->course != $r_cid) {
            RWSSErr("2072");
        }
        $r_sr = $r_sec->section;
    }
    $r_pm = RWSGSOpt("name", PARAM_TEXT);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2073");
    }
    $r_qzn = trim(clean_text(strip_tags($r_pm, "<lang><span>")));
    $r_sfl = RWSGSOpt("sfile", RWSPRF);
    if ($r_sfl === false) {
        $r_sn   = RWSGSOpt("sname", PARAM_FILE);
        $r_sd   = RWSGSOpt("sdata", PARAM_NOTAGS);
        $r_ecd = true;
    } else {
        $r_sn   = $r_sfl->filename;
        $r_sd   = $r_sfl->filedata;
        $r_ecd = false;
    }
    $r_imp = false;
    if ($r_sd !== false && strlen($r_sd) > 0) {
        if ($r_sn === false || strlen($r_sn) == 0) {
            RWSSErr("2075");
        }
        $r_sn  = clean_filename($r_sn);
        $r_imp = true;
    }
    $r_mr = $DB->get_record("modules", array("name" => "quiz"));
    if ($r_mr === false) {
        RWSSErr("2074");
    }
    $r_qiz                   = new stdClass();
    $r_qiz->name             = $r_qzn;
    $r_qiz->section          = $r_sr;
    $r_qiz->course           = $r_cid;
    $r_qiz->coursemodule     = 0;
    $r_qiz->instance         = 0;
    $r_qiz->id               = 0;
    $r_qiz->modulename       = $r_mr->name;
    $r_qiz->module           = $r_mr->id;
    $r_qiz->groupmembersonly = 0;
    if (respondusws_floatcompare($CFG->version, 2011120500.00, 2) >= 0) {
        $r_qiz->showdescription = 0;
    }
    $r_cpl = new completion_info($r_crs);
    if ($r_cpl->is_enabled()) {
        $r_qiz->completion                = COMPLETION_TRACKING_NONE;
        $r_qiz->completionview            = COMPLETION_VIEW_NOT_REQUIRED;
        $r_qiz->completiongradeitemnumber = null;
        $r_qiz->completionexpected        = 0;
    }
    if ($CFG->enableavailability) {
        $r_qiz->availablefrom  = 0;
        $r_qiz->availableuntil = 0;
        if ($r_qiz->availableuntil) {
            $r_qiz->availableuntil = strtotime("23:59:59",
                $r_qiz->availableuntil);
        }
        $r_qiz->showavailability = 0;
    }
    RWSSQDefs($r_qiz);
    if ($r_imp) {
        RWSIQSet($r_qiz, $r_sn, $r_sd, $r_ecd);
    }
    if (is_null($r_qiz->quizpassword) && !is_null($r_qiz->password)) {
        $r_qiz->quizpassword = $r_qiz->password;
    }
    $r_qzmi = add_course_module($r_qiz);
    if (!$r_qzmi) {
        RWSSErr("2077");
    }
    $r_qiz->coursemodule = $r_qzmi;
    $r_insi = quiz_add_instance($r_qiz);
    if (!$r_insi || is_string($r_insi)) {
        RWSSErr("2076");
    }
    $r_qiz->instance = $r_insi;
    if (respondusws_floatcompare($CFG->version, 2012120300, 2) >= 0) {
        $r_siu = course_add_cm_to_section($r_qiz->course,
            $r_qiz->coursemodule, $r_qiz->section);
    } else {
        $r_siu = add_mod_to_section($r_qiz);
    }
    if (!$r_siu) {
        RWSSErr("2078");
    }
    if (respondusws_floatcompare($CFG->version, 2012120300, 2) < 0) {
        $DB->set_field("course_modules", "section", $r_siu,
            array("id" => $r_qzmi));
    }
    if ($r_si !== false && $r_siu != $r_si) {
        RWSSErr("2078");
    }
    RWSSLBSet($r_qiz);
    set_coursemodule_visible($r_qzmi, $r_qiz->visible);
    if (isset($r_qiz->cmidnumber)) {
        set_coursemodule_idnumber($r_qzmi, $r_qiz->cmidnumber);
    }
    RWSUQGrades($r_qiz);
    if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
        $r_qiz->modname = $r_qiz->modulename;
        $r_qiz->id = $r_qiz->coursemodule;
        $r_evt = \core\event\course_module_created::create_from_cm($r_qiz);
        $r_evt->trigger();
    } else {
        $r_evt             = new stdClass();
        $r_evt->modulename = $r_qiz->modulename;
        $r_evt->name       = $r_qiz->name;
        $r_evt->cmid       = $r_qiz->coursemodule;
        $r_evt->courseid   = $r_qiz->course;
        $r_evt->userid     = $RWSUID;
        events_trigger("mod_created", $r_evt);
    }
    rebuild_course_cache($r_cid);
    grade_regrade_final_grades($r_cid);
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    echo "<addquiz>\r\n";
    echo "\t<name>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_qiz->name)));
    echo "</name>\r\n";
    echo "\t<id>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_qzmi)));
    echo "</id>\r\n";
    echo "\t<section_id>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_siu)));
    echo "</section_id>\r\n";
    echo "\t<writable>yes</writable>\r\n";
    if ($RWSLB->mex || $RWSLB->bex) {
        if ($RWSLB->mok) {
            if ($RWSLB->perr) {
                echo "\t<service_warning>3003</service_warning>\r\n";
            }
        } else if ($RWSLB->bok) {
            if ($RWSLB->perr) {
                echo "\t<service_warning>3003</service_warning>\r\n";
            }
        } else {
            echo "\t<service_warning>3001</service_warning>\r\n";
        }
    } else {
        echo "\t<service_warning>3000</service_warning>\r\n";
    }
    echo "</addquiz>\r\n";
    exit;
}
function RWSAUQuiz() {
    global $CFG;
    global $DB;
    global $RWSLB;
    global $RWSUID;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("quizid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2067");
    }
    $r_qzmi = intval($r_pm);
    $r_cmod = RWSCMUQuiz($r_qzmi);
    $r_sfl = RWSGSOpt("sfile", RWSPRF);
    if ($r_sfl === false) {
        $r_sn   = RWSGSOpt("sname", PARAM_FILE);
        $r_sd   = RWSGSOpt("sdata", PARAM_NOTAGS);
        $r_ecd = true;
    } else {
        $r_sn   = $r_sfl->filename;
        $r_sd   = $r_sfl->filedata;
        $r_ecd = false;
    }
    $r_imp = false;
    if ($r_sd !== false && strlen($r_sd) > 0) {
        if ($r_sn === false || strlen($r_sn) == 0) {
            RWSSErr("2075");
        }
        $r_sn  = clean_filename($r_sn);
        $r_imp = true;
    }
    $r_cid = $r_cmod->course;
    $r_crs    = RWSCMUCourse($r_cid, true);
    $r_mr = $DB->get_record("modules",
        array("id" => $r_cmod->module));
    if ($r_mr === false) {
        RWSSErr("2043");
    }
    $r_qiz = $DB->get_record($r_mr->name,
        array("id" => $r_cmod->instance));
    if ($r_qiz === false) {
        RWSSErr("2044");
    }
    $r_ren = false;
    $r_pm  = RWSGSOpt("rename", PARAM_TEXT);
    if ($r_pm !== false && strlen($r_pm) > 0) {
        $r_ren     = trim(clean_text(strip_tags($r_pm, "<lang><span>")));
        $r_qiz->name = $r_ren;
    }
    if ($r_ren === false) {
        if ($r_sd === false || strlen($r_sd) == 0) {
            RWSSErr("2080");
        }
    }
    $r_sec = $DB->get_record("course_sections",
        array("id" => $r_cmod->section));
    if ($r_sec === false) {
        RWSSErr("2079");
    }
    $r_qiz->coursemodule     = $r_cmod->id;
    $r_qiz->section          = $r_sec->section;
    $r_qiz->visible          = $r_cmod->visible;
    $r_qiz->cmidnumber       = $r_cmod->idnumber;
    $r_qiz->groupmode        = groups_get_activity_groupmode($r_cmod);
    $r_qiz->groupingid       = $r_cmod->groupingid;
    $r_qiz->groupmembersonly = $r_cmod->groupmembersonly;
    $r_qiz->course           = $r_cid;
    $r_qiz->module           = $r_mr->id;
    $r_qiz->modulename       = $r_mr->name;
    $r_qiz->instance         = $r_cmod->instance;
    if (respondusws_floatcompare($CFG->version, 2011120500.00, 2) >= 0) {
        $r_qiz->showdescription = 0;
    }
    $r_cpl = new completion_info($r_crs);
    if ($r_cpl->is_enabled()) {
        $r_qiz->completion         = $r_cmod->completion;
        $r_qiz->completionview     = $r_cmod->completionview;
        $r_qiz->completionexpected = $r_cmod->completionexpected;
        $r_qiz->completionusegrade =
            is_null($r_cmod->completiongradeitemnumber) ? 0 : 1;
    }
    if ($CFG->enableavailability) {
        $r_qiz->availablefrom  = $r_cmod->availablefrom;
        $r_qiz->availableuntil = $r_cmod->availableuntil;
        if ($r_qiz->availableuntil) {
            $r_qiz->availableuntil = strtotime("23:59:59",
                $r_qiz->availableuntil);
        }
        $r_qiz->showavailability = $r_cmod->showavailability;
    }
    $r_its = grade_item::fetch_all(array(
        'itemtype'     => 'mod',
        'itemmodule'   => $r_qiz->modulename,
        'iteminstance' => $r_qiz->instance,
        'courseid'     => $r_cid
    ));
    if ($r_its) {
        foreach ($r_its as $r_it) {
            if (!empty($r_it->outcomeid)) {
                $r_qiz->{'outcome_' . $r_it->outcomeid} = 1;
            }
        }
        $r_gc = false;
        foreach ($r_its as $r_it) {
            if ($r_gc === false) {
                $r_gc = $r_it->categoryid;
                continue;
            }
            if ($r_gc != $r_it->categoryid) {
                $r_gc = false;
                break;
            }
        }
        if ($r_gc !== false) {
            $r_qiz->gradecat = $r_gc;
        }
    }
    if ($r_imp) {
        RWSIQSet($r_qiz, $r_sn, $r_sd, $r_ecd);
    }
    $DB->update_record("course_modules", $r_qiz);
    if (is_null($r_qiz->quizpassword) && !is_null($r_qiz->password)) {
        $r_qiz->quizpassword = $r_qiz->password;
    }
    $r_res = quiz_update_instance($r_qiz);
    if (!$r_res || is_string($r_res)) {
        RWSSErr("2081");
    }
    RWSSLBSet($r_qiz);
    set_coursemodule_visible($r_qzmi, $r_qiz->visible);
    if (isset($r_qiz->cmidnumber)) {
        set_coursemodule_idnumber($r_qzmi, $r_qiz->cmidnumber);
    }
    RWSUQGrades($r_qiz);
    if ($r_cpl->is_enabled() && !empty($r_qiz->completionunlocked)) {
        $r_cpl->reset_all_state($r_qiz);
    }
    if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
        $r_qiz->modname = $r_qiz->modulename;
        $r_qiz->id = $r_qiz->coursemodule;
        \core\event\course_module_updated::create_from_cm($r_qiz)->trigger();
    } else {
        $r_evt             = new stdClass();
        $r_evt->modulename = $r_qiz->modulename;
        $r_evt->name       = $r_qiz->name;
        $r_evt->cmid       = $r_qiz->coursemodule;
        $r_evt->courseid   = $r_qiz->course;
        $r_evt->userid     = $RWSUID;
        events_trigger("mod_updated", $r_evt);
    }
    rebuild_course_cache($r_cid);
    grade_regrade_final_grades($r_cid);
    if ($RWSLB->mex || $RWSLB->bex) {
        if ($RWSLB->mok) {
            if ($RWSLB->perr) {
                RWSSWarn("3003");
            }
        } else if ($RWSLB->bok) {
            if ($RWSLB->perr) {
                RWSSWarn("3003");
            }
        } else {
            RWSSWarn("3001");
        }
    } else {
        RWSSWarn("3000");
    }
    RWSSStat("1004");
}
function RWSAAQList() {
    global $DB;
    global $CFG;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("quizid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2067");
    }
    $r_qzmi = intval($r_pm);
    $r_cmod = RWSCMUQuiz($r_qzmi);
    $r_cid = $r_cmod->course;
    RWSCMUCourse($r_cid, true);
    $r_ql = RWSGSOpt("qlist", PARAM_SEQUENCE);
    if ($r_ql === false || strlen($r_ql) == 0) {
        RWSSErr("2082");
    }
    $r_qis = explode(",", $r_ql);
    if (count($r_qis) == 0 || strlen($r_qis[0]) == 0) {
        RWSSErr("2082");
    }
    foreach ($r_qis as $r_k => $r_val) {
        if ($r_val === false || strlen($r_val) == 0) {
            RWSSErr("2108");
        }
        $r_qis[$r_k] = intval($r_val);
    }
    $r_mr = $DB->get_record("modules",
        array("id" => $r_cmod->module));
    if ($r_mr === false) {
        RWSSErr("2043");
    }
    $r_qiz = $DB->get_record($r_mr->name,
        array("id" => $r_cmod->instance));
    if ($r_qiz === false) {
        RWSSErr("2044");
    }
    if (!isset($r_qiz->instance)) {
        $r_qiz->instance = $r_qiz->id;
    }
    $r_erri = array();
    foreach ($r_qis as $r_id) {
        $r_rc = $DB->get_record("question", array("id" => $r_id));
        $r_ok  = ($r_rc !== false);
        if ($r_ok) {
            if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
                quiz_add_quiz_question($r_id, $r_qiz);
            } else {
                $r_ok = quiz_add_quiz_question($r_id, $r_qiz);
            }
        }
        if (!$r_ok) {
            $r_erri[] = $r_id;
        }
    }
    if (count($r_erri) > 0) {
        $r_errl = implode(",", $r_erri);
        RWSSErr("2083,$r_errl");
    }
    if (count($r_erri) < count($r_qis)) {
        quiz_delete_previews($r_qiz);
    }
    if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
        quiz_update_sumgrades($r_qiz);
    } else {
        $r_qiz->grades = quiz_get_all_question_grades($r_qiz);
        $r_sumg   = array_sum($r_qiz->grades);
        $DB->set_field("quiz", "sumgrades", $r_sumg, array("id" => $r_qiz->id));
    }
    RWSSStat("1005");
}
function RWSAAQRand() {
    global $CFG;
    global $DB;
    global $RWSUID;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("quizid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2067");
    }
    $r_qzmi = intval($r_pm);
    $r_cmod = RWSCMUQuiz($r_qzmi);
    $r_cid = $r_cmod->course;
    RWSCMUCourse($r_cid, true);
    $r_mr = $DB->get_record("modules",
        array("id" => $r_cmod->module));
    if ($r_mr === false) {
        RWSSErr("2043");
    }
    $r_qiz = $DB->get_record($r_mr->name,
        array("id" => $r_cmod->instance));
    if ($r_qiz === false) {
        RWSSErr("2044");
    }
    $r_pm = RWSGSOpt("qcatid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2064");
    }
    $r_qci = intval($r_pm);
    $r_qca = $DB->get_record("question_categories", array("id" => $r_qci));
    if ($r_qca === false) {
        RWSSErr("2065");
    }
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context::instance_by_id($r_qca->contextid);
    } else {
        $r_ctx = get_context_instance_by_id($r_qca->contextid);
    }
    $r_qcci = RWSGCFCat($r_ctx);
    if ($r_qcci != $r_cid) {
        if (is_siteadmin($RWSUID)) {
            if ($r_qcci != SITEID) {
                RWSSErr("2109");
            }
        } else {
            RWSSErr("2084");
        }
    }
    $r_pm = RWSGSOpt("qcount", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2085");
    }
    $r_qct = intval($r_pm);
    if ($r_qct <= 0) {
        RWSSErr("2085");
    }
    $r_pm = RWSGSOpt("qgrade", PARAM_NOTAGS);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2086");
    }
    $r_qg = round(floatval($r_pm));
    if ($r_qg <= 0) {
        RWSSErr("2086");
    }
    $r_mr = $DB->get_record("modules", array("id" => $r_cmod->module));
    if ($r_mr === false) {
        RWSSErr("2043");
    }
    $r_qiz = $DB->get_record($r_mr->name, array("id" => $r_cmod->instance));
    if ($r_qiz === false) {
        RWSSErr("2044");
    }
    if (!isset($r_qiz->instance)) {
        $r_qiz->instance = $r_qiz->id;
    }
    $r_aerr = 0;
    $r_isc = true;
    $r_cqm = true;
    if (respondusws_floatcompare($CFG->version, 2023100900, 2) >= 0) {
        $r_cqm = false;
        $r_stgs = \mod_quiz\quiz_settings::create($r_qiz->id);
        $r_stu = \mod_quiz\structure::create_for_quiz($r_stgs);
        $r_ft = [
            'category' => [
                'jointype' => \qbank_managecategories\category_condition::JOINTYPE_DEFAULT,
                'values' => [$r_qca->id],
                'filteroptions' => ['includesubcategories' => $r_isc],
            ],
        ];
        $r_ftc['filter'] = $r_ft;
        $r_stu->add_random_questions(0, $r_qct, $r_ftc);
        $r_uctx = context_module::instance($r_qzmi);
        if ($r_uctx !== false) {
            $r_qsrl = $DB->get_records("question_set_references",
              array(
                "component" => "mod_quiz",
                "questionarea" => "slot",
                "usingcontextid" => $r_uctx->id,
                "questionscontextid" => $r_qca->contextid
              ));
            foreach ($r_qsrl as $r_qsr) {
                $r_ft = json_decode($r_qsr->filtercondition);
                if ($r_ft->questioncategoryid != $r_qca->id) {
                    continue;
                }
                $DB->set_field("quiz_slots", "maxmark", $r_qg, array("id" => $r_qsr->itemid));
            }
        }
    }
    else if (respondusws_floatcompare($CFG->version, 2018051700, 2) >= 0) {
        $r_cqm = false;
        quiz_add_random_questions($r_qiz, 0, $r_qca->id, $r_qct, $r_isc);
        if (respondusws_floatcompare($CFG->version, 2022041900, 2) >= 0) {
            $r_uctx = context_module::instance($r_qzmi);
            if ($r_uctx !== false) {
                $r_qsrl = $DB->get_records("question_set_references",
                  array(
                    "component" => "mod_quiz",
                    "questionarea" => "slot",
                    "usingcontextid" => $r_uctx->id,
                    "questionscontextid" => $r_qca->contextid
                  ));
                foreach ($r_qsrl as $r_qsr) {
                    $r_ft = json_decode($r_qsr->filtercondition);
                    if ($r_ft->questioncategoryid != $r_qca->id) {
                        continue;
                    }
                    $DB->set_field("quiz_slots", "maxmark", $r_qg, array("id" => $r_qsr->itemid));
                }
            }
        } else {
            $DB->set_field("question", "defaultmark", $r_qg, array("qtype" => RWSRND, "category" => $r_qca->id));
            $DB->set_field("quiz_slots", "maxmark", $r_qg, array("questioncategoryid" => $r_qca->id));
        }
    }
    for ($r_i = 0; $r_cqm && $r_i < $r_qct; $r_i++) {
        $r_qst               = new stdClass();
        $r_qst->qtype        = RWSRND;
        $r_qst->parent       = 0;
        $r_qst->hidden       = 0;
        $r_qst->length       = 1;
        $r_qst->questiontext = 1;
        if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
            $r_rqt    = question_bank::get_qtype($r_qst->qtype);
            $r_qst->name = $r_rqt->question_name($r_qca, $r_isc);
        } else {
            $r_qst->name = random_qtype::question_name($r_qca, $r_isc);
        }
        $r_qst->questiontextformat = FORMAT_HTML;
        $r_qst->penalty            = 0;
        if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
            $r_qst->defaultmark = $r_qg;
        } else {
            $r_qst->defaultgrade = $r_qg;
        }
        $r_qst->generalfeedback       = "";
        $r_qst->generalfeedbackformat = FORMAT_HTML;
        $r_qst->category              = $r_qca->id;
        $r_qst->stamp                 = make_unique_id_code();
        $r_qst->createdby             = $RWSUID;
        $r_qst->modifiedby            = $RWSUID;
        $r_qst->timecreated           = time();
        $r_qst->timemodified          = time();
        $r_qst->id                    = $DB->insert_record("question", $r_qst);
        $DB->set_field("question", "parent", $r_qst->id,
            array("id" => $r_qst->id));
        $r_h = question_hash($r_qst);
        $DB->set_field("question", "version", $r_h,
            array("id" => $r_qst->id));
        if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
            quiz_add_quiz_question($r_qst->id, $r_qiz);
        } else {
            $r_ok = quiz_add_quiz_question($r_qst->id, $r_qiz);
            if (!$r_ok) {
                $DB->delete_records("question", array("id" => $r_qst->id));
                $r_aerr++;
            }
        }
    }
    if ($r_aerr > 0) {
        RWSSErr("2087,$r_aerr");
    }
    if ($r_aerr < $r_qct) {
        quiz_delete_previews($r_qiz);
    }
    if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
        quiz_update_sumgrades($r_qiz);
    } else {
        $r_qiz->grades = quiz_get_all_question_grades($r_qiz);
        $r_sumg   = array_sum($r_qiz->grades);
        $DB->set_field("quiz", "sumgrades", $r_sumg, array("id" => $r_qiz->id));
    }
    RWSSStat("1006");
}
function RWSAIQData() {
    global $CFG;
    global $DB;
    global $RWSUID;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("qcatid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2064");
    }
    $r_qci = intval($r_pm);
    $r_qca = $DB->get_record("question_categories", array("id" => $r_qci));
    if ($r_qca === false) {
        RWSSErr("2065");
    }
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context::instance_by_id($r_qca->contextid);
    } else {
        $r_ctx = get_context_instance_by_id($r_qca->contextid);
    }
    $r_cid = RWSGCFCat($r_ctx);
    RWSCMUCourse($r_cid);
    $r_qfl = RWSGSOpt("qfile", RWSPRF);
    if ($r_qfl === false) {
        $r_qn   = RWSGSOpt("qname", PARAM_FILE);
        $r_qd   = RWSGSOpt("qdata", PARAM_NOTAGS);
        $r_ecd = true;
    } else {
        $r_qn   = $r_qfl->filename;
        $r_qd   = $r_qfl->filedata;
        $r_ecd = false;
    }
    if ($r_qn === false || strlen($r_qn) == 0) {
        RWSSErr("2088");
    }
    $r_qn = clean_filename($r_qn);
    if ($r_qd === false || strlen($r_qd) == 0) {
        RWSSErr("2089");
    }
    if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0
        ) {
        $r_evt = \mod_respondusws\event\questions_published::create(
            array(
                'userid' => $RWSUID,
                'courseid' => $r_cid,
                'context' => $r_ctx,
                'other' => array('qcatid' => $r_qci)
            )
        );
        $r_evt->add_record_snapshot("question_categories", $r_qca);
        $r_evt->trigger();
    } else {
        RWSATLog($r_cid, "publish", "qcatid=$r_qci");
    }
    $r_drp = 0;
    $r_ba = 0;
    $r_qis    = RWSIQues(
        $r_cid, $r_qci, $r_qn, $r_qd, $r_ecd, $r_drp, $r_ba);
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context_course::instance($r_cid);
    } else {
        $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
    }
    $r_ctxi = $r_ctx->id;
    $r_cmp = "mod_respondusws";
    $r_far  = "upload";
    $r_iti    = $RWSUID;
    try {
        $r_fs = get_file_storage();
        if (!$r_fs->is_area_empty($r_ctxi, $r_cmp, $r_far, $r_iti, false)) {
            $r_fls = $r_fs->get_area_files($r_ctxi, $r_cmp, $r_far, $r_iti);
            foreach ($r_fls as $r_fl) {
                $r_old = time() - 60 * 60 * 24 * 1;
                if ($r_fl->get_timecreated() < $r_old) {
                    $r_fl->delete();
                }
            }
        }
    } catch (Exception $r_e) {
        RWSSErr("2114");
    }
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    echo "<importqdata>\r\n";
    echo "\t<category_id>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_qci)));
    echo "</category_id>\r\n";
    echo "\t<dropped>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_drp)));
    echo "</dropped>\r\n";
    echo "\t<badatts>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_ba)));
    echo "</badatts>\r\n";
    $r_ql = implode(",", $r_qis);
    echo "\t<qlist>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_ql)));
    echo "</qlist>\r\n";
    echo "</importqdata>\r\n";
    exit;
}
function RWSAGQuiz() {
    global $CFG;
    global $DB;
    global $RWSLB;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_fmt = RWSGSOpt("format", PARAM_ALPHANUMEXT);
    if (strcasecmp($r_fmt, "base64") == 0) {
        $r_w64 = true;
    } else if (strcasecmp($r_fmt, "binary") == 0) {
        $r_w64 = false;
    } else {
        RWSSErr("2051");
    }
    $r_pm = RWSGSOpt("quizid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2067");
    }
    $r_qzmi = intval($r_pm);
    $r_cmod = RWSCMUQuiz($r_qzmi);
    $r_cid = $r_cmod->course;
    $r_crs    = RWSCMUCourse($r_cid, true);
    $r_mr = $DB->get_record("modules",
        array("id" => $r_cmod->module));
    if ($r_mr === false) {
        RWSSErr("2043");
    }
    $r_qiz = $DB->get_record($r_mr->name,
        array("id" => $r_cmod->instance));
    if ($r_qiz === false) {
        RWSSErr("2044");
    }
    $r_sec = $DB->get_record("course_sections",
        array("id" => $r_cmod->section));
    if ($r_sec === false) {
        RWSSErr("2079");
    }
    $r_qiz->coursemodule     = $r_cmod->id;
    $r_qiz->section          = $r_sec->section;
    $r_qiz->visible          = $r_cmod->visible;
    $r_qiz->cmidnumber       = $r_cmod->idnumber;
    $r_qiz->groupmode        = groups_get_activity_groupmode($r_cmod);
    $r_qiz->groupingid       = $r_cmod->groupingid;
    $r_qiz->groupmembersonly = $r_cmod->groupmembersonly;
    $r_qiz->course           = $r_cid;
    $r_qiz->module           = $r_mr->id;
    $r_qiz->modulename       = $r_mr->name;
    $r_qiz->instance         = $r_cmod->instance;
    if (respondusws_floatcompare($CFG->version, 2011120500.00, 2) >= 0) {
        $r_qiz->showdescription = $r_cmod->showdescription;
    }
    $r_cpl = new completion_info($r_crs);
    if ($r_cpl->is_enabled()) {
        $r_qiz->completion         = $r_cmod->completion;
        $r_qiz->completionview     = $r_cmod->completionview;
        $r_qiz->completionexpected = $r_cmod->completionexpected;
        $r_qiz->completionusegrade =
            is_null($r_cmod->completiongradeitemnumber) ? 0 : 1;
    }
    if ($CFG->enableavailability) {
        $r_qiz->availablefrom  = $r_cmod->availablefrom;
        $r_qiz->availableuntil = $r_cmod->availableuntil;
        if ($r_qiz->availableuntil) {
            $r_qiz->availableuntil = strtotime("23:59:59",
                $r_qiz->availableuntil);
        }
        $r_qiz->showavailability = $r_cmod->showavailability;
    }
    $r_its = grade_item::fetch_all(array(
        'itemtype'     => 'mod',
        'itemmodule'   => $r_qiz->modulename,
        'iteminstance' => $r_qiz->instance,
        'courseid'     => $r_cid
    ));
    if ($r_its) {
        foreach ($r_its as $r_it) {
            if (!empty($r_it->outcomeid)) {
                $r_qiz->{'outcome_' . $r_it->outcomeid} = 1;
            }
        }
        $r_gc = false;
        foreach ($r_its as $r_it) {
            if ($r_gc === false) {
                $r_gc = $r_it->categoryid;
                continue;
            }
            if ($r_gc != $r_it->categoryid) {
                $r_gc = false;
                break;
            }
        }
        if ($r_gc !== false) {
            $r_qiz->gradecat = $r_gc;
        }
    }
    $r_sfl = "";
    $r_sd = RWSEQSet($r_qiz, $r_sfl, $r_w64);
    if ($r_w64) {
        RWSRHXml();
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
        echo "<getquiz>\r\n";
        echo "\t<name>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_qiz->name)));
        echo "</name>\r\n";
        echo "\t<id>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_qzmi)));
        echo "</id>\r\n";
        echo "\t<section_id>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_qiz->section)));
        echo "</section_id>\r\n";
        echo "\t<writable>yes</writable>\r\n";
        echo "\t<sfile>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_sfl)));
        echo "</sfile>\r\n";
        echo "\t<sdata>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_sd)));
        echo "</sdata>\r\n";
        if ($RWSLB->mex || $RWSLB->bex) {
            if ($RWSLB->mok) {
                if ($RWSLB->gerr) {
                    echo "\t<service_warning>3002</service_warning>\r\n";
                }
            } else if ($RWSLB->bok) {
                if ($RWSLB->gerr) {
                    echo "\t<service_warning>3002</service_warning>\r\n";
                }
            } else {
                echo "\t<service_warning>3001</service_warning>\r\n";
            }
        } else {
            echo "\t<service_warning>3000</service_warning>\r\n";
        }
        echo "</getquiz>\r\n";
    } else {
        $r_fld         = "name=\"" . htmlspecialchars(trim($r_qiz->name)) . "\"; ";
        $r_chdr = $r_fld;
        $r_fld = "id=" . htmlspecialchars(trim($r_qzmi)) . "; ";
        $r_chdr .= $r_fld;
        $r_fld = "section_id=" . htmlspecialchars(trim($r_qiz->section)) . "; ";
        $r_chdr .= $r_fld;
        $r_fld = "writable=yes";
        $r_chdr .= $r_fld;
        if ($RWSLB->mex || $RWSLB->bex) {
            if ($RWSLB->mok) {
                if ($RWSLB->gerr) {
                    $r_fld = "; service_warning=3002";
                    $r_chdr .= $r_fld;
                }
            } else if ($RWSLB->bok) {
                if ($RWSLB->gerr) {
                    $r_fld = "; service_warning=3002";
                    $r_chdr .= $r_fld;
                }
            } else {
                $r_fld = "; service_warning=3001";
                $r_chdr .= $r_fld;
            }
        } else {
            $r_fld = "; service_warning=3000";
            $r_chdr .= $r_fld;
        }
        header("X-GetQuiz: " . $r_chdr);
        RWSRHBin($r_sfl, strlen($r_sd));
        echo $r_sd;
    }
    exit;
}
function RWSAEQData() {
    global $CFG;
    global $DB;
    global $RWSUID;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_fmt = RWSGSOpt("format", PARAM_ALPHANUMEXT);
    if (strcasecmp($r_fmt, "base64") == 0) {
        $r_w64 = true;
    } else if (strcasecmp($r_fmt, "binary") == 0) {
        $r_w64 = false;
    } else {
        RWSSErr("2051");
    }
    $r_qzmi = false;
    $r_pm     = RWSGSOpt("quizid", PARAM_ALPHANUM);
    if ($r_pm !== false && strlen($r_pm) > 0) {
        $r_qzmi = intval($r_pm);
    }
    $r_qci = false;
    $r_pm   = RWSGSOpt("qcatid", PARAM_ALPHANUM);
    if ($r_pm !== false && strlen($r_pm) > 0) {
        $r_qci = intval($r_pm);
    }
    if ($r_qzmi === false && $r_qci === false) {
        RWSSErr("2090");
    } else if ($r_qzmi !== false && $r_qci === false) {
        $r_cmod = RWSCMUQuiz($r_qzmi);
        $r_cid     = $r_cmod->course;
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context_module::instance($r_cmod->id);
        } else {
            $r_ctx = get_context_instance(CONTEXT_MODULE, $r_cmod->id);
        }
    } else if ($r_qzmi === false && $r_qci !== false) {
        $r_qca = $DB->get_record("question_categories",
            array("id" => $r_qci));
        if ($r_qca === false) {
            RWSSErr("2065");
        }
        if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
            $r_ctx = context::instance_by_id($r_qca->contextid);
        } else {
            $r_ctx = get_context_instance_by_id($r_qca->contextid);
        }
        $r_cid = RWSGCFCat($r_ctx);
    } else {
        RWSSErr("2091");
    }
    RWSCMUCourse($r_cid);
    if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0
        ) {
        $r_prms = array(
            'userid' => $RWSUID,
            'courseid' => $r_cid,
            'context' => $r_ctx
            );
        if ($r_qzmi !== false) {
            $r_prms['other'] = array('quizcmid' => $r_qzmi);
        } else {
            $r_prms['other'] = array('qcatid' => $r_qci);
        }
        $r_evt = \mod_respondusws\event\questions_retrieved::create($r_prms);
        if ($r_qzmi !== false) {
            $r_evt->add_record_snapshot("course_modules", $r_cmod);
        } else {
            $r_evt->add_record_snapshot("question_categories", $r_qca);
        }
        $r_evt->trigger();
    } else {
        if ($r_qzmi !== false) {
            RWSATLog($r_cid, "retrieve", "quizid=$r_qzmi");
        } else {
            RWSATLog($r_cid, "retrieve", "qcatid=$r_qci");
        }
    }
    $r_qfl   = "";
    $r_drp = 0;
    $r_ran  = 0;
    if ($r_qzmi !== false) {
        $r_qd = RWSEQQues(
            $r_qzmi, $r_qfl, $r_drp, $r_ran, $r_w64);
    } else {
        $r_qd = RWSEQCQues(
            $r_qci, $r_qfl, $r_drp, $r_w64);
    }
    if ($r_w64) {
        RWSRHXml();
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
        echo "<exportqdata>\r\n";
        if ($r_qzmi !== false) {
            echo "\t<quiz_id>";
            echo respondusws_utf8encode(htmlspecialchars(trim($r_qzmi)));
            echo "</quiz_id>\r\n";
        } else {
            echo "\t<category_id>";
            echo respondusws_utf8encode(htmlspecialchars(trim($r_qci)));
            echo "</category_id>\r\n";
        }
        echo "\t<dropped>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_drp)));
        echo "</dropped>\r\n";
        if ($r_qzmi !== false) {
            echo "\t<random>";
            echo respondusws_utf8encode(htmlspecialchars(trim($r_ran)));
            echo "</random>\r\n";
        }
        echo "\t<qfile>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_qfl)));
        echo "</qfile>\r\n";
        echo "\t<qdata>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_qd)));
        echo "</qdata>\r\n";
        echo "</exportqdata>\r\n";
    } else {
        if ($r_qzmi !== false) {
            $r_fld = "quiz_id=" . htmlspecialchars(trim($r_qzmi)) . "; ";
        } else {
            $r_fld = "category_id=" . htmlspecialchars(trim($r_qci)) . "; ";
        }
        $r_chdr = $r_fld;
        $r_fld = "dropped=" . htmlspecialchars(trim($r_drp));
        $r_chdr .= $r_fld;
        if ($r_qzmi !== false) {
            $r_fld = "; random=" . htmlspecialchars(trim($r_ran));
            $r_chdr .= $r_fld;
        }
        header("X-ExportQData: " . $r_chdr);
        RWSRHBin($r_qfl, strlen($r_qd));
        echo $r_qd;
    }
    exit;
}
function RWSAUFile() {
    global $CFG;
    global $RWSUID;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("courseid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2057");
    }
    if (strcasecmp($r_pm, "site") == 0) {
        $r_cid = SITEID;
    } else {
        $r_cid = intval($r_pm);
    }
    RWSCMUCourse($r_cid);
    $r_ff = RWSGSOpt("folder", PARAM_FILE);
    if ($r_ff === false || strlen($r_ff) == 0) {
        RWSSErr("2092");
    }
    $r_ff = clean_filename($r_ff);
    $r_fbn = RWSGSOpt("filebinary", RWSPRF);
    if ($r_fbn === false) {
        $r_fn = RWSGSOpt("filename", PARAM_FILE);
        $r_fdat = RWSGSOpt("filedata", PARAM_NOTAGS);
        $r_ecd   = true;
    } else {
        $r_fn = $r_fbn->filename;
        $r_fdat = $r_fbn->filedata;
        $r_ecd   = false;
    }
    if ($r_fn === false || strlen($r_fn) == 0) {
        RWSSErr("2093");
    }
    $r_fn = clean_filename($r_fn);
    if ($r_fdat === false || strlen($r_fdat) == 0) {
        RWSSErr("2094");
    }
    if ($r_ecd) {
        $r_dcd_data = base64_decode($r_fdat);
        if ($r_dcd_data === false) {
            RWSSErr("2097");
        }
    } else {
        $r_dcd_data = $r_fdat;
    }
    if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
        $r_ctx = context_course::instance($r_cid);
    } else {
        $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
    }
    $r_ctxi = $r_ctx->id;
    $r_cmp = "mod_respondusws";
    $r_far  = "upload";
    $r_iti    = $RWSUID;
    $r_fpt  = "/$r_ff/";
    $r_fna  = $r_fn;
    $r_finf  = array(
        "contextid" => $r_ctxi,
        "component" => $r_cmp,
        "filearea"  => $r_far,
        "itemid"    => $r_iti,
        "filepath"  => $r_fpt,
        "filename"  => $r_fna
    );
    $r_crpth = "$r_ff/$r_fn";
    try {
        $r_fs          = get_file_storage();
        $r_fex = $r_fs->file_exists(
            $r_ctxi, $r_cmp, $r_far, $r_iti, $r_fpt, $r_fna
        );
        if ($r_fex) {
            RWSSErr("2096,$r_crpth");
        }
        if (!$r_fs->create_file_from_string($r_finf, $r_dcd_data)) {
            RWSSErr("2098");
        }
    } catch (Exception $r_e) {
        RWSSErr("2098");
    }
    RWSRHXml();
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    echo "<uploadfile>\r\n";
    echo "\t<course_subpath>";
    echo respondusws_utf8encode(htmlspecialchars(trim($r_crpth)));
    echo "</course_subpath>\r\n";
    echo "</uploadfile>\r\n";
    exit;
}
function RWSADFile() {
    global $CFG;
    global $RWSUID;
    global $RWSEDBG;
    global $RWSDBGL;
    RWSCMAuth();
    RWSCRAuth();
    RWSCMUSvc();
    RWSCMMaint();
    $r_pm = RWSGSOpt("courseid", PARAM_ALPHANUM);
    if ($r_pm === false || strlen($r_pm) == 0) {
        RWSSErr("2057");
    }
    if (strcasecmp($r_pm, "site") == 0) {
        $r_cid = SITEID;
    } else {
        $r_cid = intval($r_pm);
    }
    if (!$RWSEDBG) {
        $r_crs = RWSCMUCourse($r_cid);
    }
    $r_fmt = RWSGSOpt("format", PARAM_ALPHANUMEXT);
    if (strcasecmp($r_fmt, "base64") == 0) {
        $r_w64 = true;
    } else if (strcasecmp($r_fmt, "binary") == 0) {
        $r_w64 = false;
    } else {
        RWSSErr("2051");
    }
    $r_fr = RWSGSOpt("fileref", PARAM_RAW);
    if ($r_fr === false || strlen($r_fr) == 0) {
        RWSSErr("2099");
    }
    $r_st = stripos($r_fr, "/pluginfile.php");
    if ($r_st !== false) {
        $r_st = strpos($r_fr, "/", $r_st + 1);
        if ($r_st === false) {
            RWSSErr("2100");
        }
        $r_pth  = substr($r_fr, $r_st);
        $r_pts = explode("/", ltrim($r_pth, '/'));
        if (count($r_pts) < 5) {
            RWSSErr("2100");
        }
        $r_ctxi = intval(array_shift($r_pts));
        if (respondusws_floatcompare($CFG->version, 2011120500.00, 2) >= 0) {
            $r_cmp = clean_param(array_shift($r_pts), PARAM_COMPONENT);
            $r_far  = clean_param(array_shift($r_pts), PARAM_AREA);
        } else {
            $r_cmp = clean_param(array_shift($r_pts), PARAM_SAFEDIR);
            $r_far  = clean_param(array_shift($r_pts), PARAM_SAFEDIR);
        }
        $r_iti   = intval(array_shift($r_pts));
        $r_fna = clean_filename(array_pop($r_pts));
        $r_fpt = "/";
        if (count($r_pts) > 0) {
            $r_fpt = "/" . implode("/", $r_pts) . "/";
        }
        try {
            $r_fs          = get_file_storage();
            $r_fex = $r_fs->file_exists(
                $r_ctxi, $r_cmp, $r_far, $r_iti, $r_fpt, $r_fna
            );
            if (!$r_fex) {
                RWSSErr("2100");
            }
            $r_fl = $r_fs->get_file(
                $r_ctxi, $r_cmp, $r_far, $r_iti, $r_fpt, $r_fna
            );
            if ($r_fl === false) {
                RWSSErr("2101");
            }
            $r_fdat = $r_fl->get_content();
            $r_fn = $r_fna;
        } catch (Exception $r_e) {
            RWSSErr("2101");
        }
    } else if ($RWSEDBG && $r_cid == SITEID
        && strcmp($r_fr, $RWSDBGL) == 0
    ) {
        $r_pth      = RWSGTPath();
        $r_fn = $r_fr;
        $r_fdat = file_get_contents("$r_pth/$r_fn");
        if ($r_fdat === false) {
            RWSSErr("2101");
        }
    } else {
        $r_st = stripos($r_fr, "/draftfile.php");
        if ($r_st !== false) {
            $r_st = strpos($r_fr, "/", $r_st + 1);
            if ($r_st === false) {
                RWSSErr("2100");
            }
            $r_pth  = substr($r_fr, $r_st);
            $r_pts = explode("/", ltrim($r_pth, '/'));
            if (count($r_pts) < 5) {
                RWSSErr("2100");
            }
            $r_ctxi = intval(array_shift($r_pts));
            if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
                $r_ctx = context::instance_by_id($r_ctxi);
            } else {
                $r_ctx = get_context_instance_by_id($r_ctxi);
            }
            if ($r_ctx->contextlevel != CONTEXT_USER) {
                RWSSErr("2100");
            }
            $r_cmp = array_shift($r_pts);
            if ($r_cmp !== "user") {
                RWSSErr("2100");
            }
            $r_far = array_shift($r_pts);
            if ($r_far !== "draft") {
                RWSSErr("2100");
            }
            $r_drf  = intval(array_shift($r_pts));
            $r_rlp  = implode("/", $r_pts);
            $r_fna = array_pop($r_pts);
            $r_fph = "/$r_ctxi/user/draft/$r_drf/$r_rlp";
            try {
                $r_fs   = get_file_storage();
                $r_fl = $r_fs->get_file_by_hash(sha1($r_fph));
                if ($r_fl === false) {
                    RWSSErr("2101");
                }
                if ($r_fl->get_filename() == ".") {
                    RWSSErr("2101");
                }
                $r_fdat = $r_fl->get_content();
                $r_fn = $r_fna;
            } catch (Exception $r_e) {
                RWSSErr("2101");
            }
        } else {
            $r_st = stripos($r_fr, "/file.php");
            if ($r_st !== false) {
                $r_st = strpos($r_fr, "/", $r_st + 1);
                if ($r_st === false) {
                    RWSSErr("2100");
                }
                $r_pth  = substr($r_fr, $r_st);
                $r_pts = explode("/", ltrim($r_pth, '/'));
                if (count($r_pts) < 1) {
                    RWSSErr("2100");
                }
                if ($r_crs->legacyfiles != 2) {
                    RWSSErr("2113");
                }
                $r_ci = intval(array_shift($r_pts));
                if ($r_ci != $r_cid) {
                    RWSSErr("2100");
                }
                if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
                    $r_ctx = context_course::instance($r_cid);
                } else {
                    $r_ctx = get_context_instance(CONTEXT_COURSE, $r_cid);
                }
                $r_ctxi = $r_ctx->id;
                $r_rlp   = implode("/", $r_pts);
                $r_fna  = array_pop($r_pts);
                $r_fph  = "/$r_ctxi/course/legacy/0/$r_rlp";
                try {
                    $r_fs   = get_file_storage();
                    $r_fl = $r_fs->get_file_by_hash(sha1($r_fph));
                    if ($r_fl === false) {
                        RWSSErr("2101");
                    }
                    $r_fdat = $r_fl->get_content();
                    $r_fn = $r_fna;
                } catch (Exception $r_e) {
                    RWSSErr("2101");
                }
            } else {
                RWSSErr("2100");
            }
        }
    }
    if ($r_w64) {
        RWSRHXml();
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
        echo "<dnloadfile>\r\n";
        echo "\t<filename>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_fn)));
        echo "</filename>\r\n";
        $r_ed = base64_encode($r_fdat);
        echo "\t<filedata>";
        echo respondusws_utf8encode(htmlspecialchars(trim($r_ed)));
        echo "</filedata>\r\n";
        echo "</dnloadfile>\r\n";
    } else {
        RWSRHBin($r_fn, strlen($r_fdat));
        echo $r_fdat;
    }
    exit;
}
function RWSAAStart() {
    global $CFG;
    global $SESSION;
    global $RWSSRURL;
    global $RWSRWROOT;
    $r_bo = true;
    RWSCMMaint($r_bo);
    if (isloggedin()) {
        RWSBErr("The session is already logged in.");
    }
    $r_cfg = get_config("respondusws");
    if ($r_cfg === false) {
        RWSBErr("Module authentication settings are empty.");
    }
    if (!isset($r_cfg->username) || strlen($r_cfg->username) == 0) {
        RWSBErr("User name not found in module authentication settings.");
    }
    if (!isset($r_cfg->password) || strlen($r_cfg->password) == 0) {
        RWSBErr("Password not found in module authentication settings.");
    }
    if (!isset($r_cfg->secret) || strlen($r_cfg->secret) == 0) {
        RWSBErr("Secret not found in module authentication settings.");
    }
    if (strlen($RWSSRURL) > 0) {
        $r_su = $RWSSRURL;
    } else {
        $r_su = RWSGSUrl(false, false);
    }
    $r_ac = "authfinish";
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    if ($r_bv < 2013061700) {
        RWSBErr(
          "The authentication framework is not supported by the requested behavior version."
          );
    }
    $r_usrn = RWSGSOpt("username", PARAM_RAW);
    if ($r_usrn === false || strlen($r_usrn) == 0) {
        RWSBErr("No username specified.");
    }
    $r_rtm = time();
    $r_rmc = RWSGAMac(
      $r_ac . $r_bv . $r_usrn . $r_rtm
      );
    $SESSION->wantsurl = $r_su
      . "?action=" . urlencode($r_ac)
      . "&version=" . urlencode($r_bv)
      . "&username=" . urlencode($r_usrn)
      . "&time=" . urlencode($r_rtm)
      . "&mac=" . urlencode($r_rmc);
    if ($RWSRWROOT) {
        if (stripos($CFG->wwwroot, "http:") === 0) {
            $SESSION->wantsurl = str_replace("https:", "http:", $SESSION->wantsurl);
        } else if (stripos($CFG->wwwroot, "https:") === 0) {
            $SESSION->wantsurl = str_replace("http:", "https:", $SESSION->wantsurl);
        }
    }
    $r_rurl = get_login_url();
    header("Location: $r_rurl");
    exit;
}
function RWSAAFinish() {
    global $DB;
    global $SESSION;
    global $USER;
    unset($SESSION->wantsurl);
    $r_bo = true;
    RWSCMAuth($r_bo);
    RWSCMUSvc($r_bo);
    RWSCMMaint($r_bo);
    $r_ac = RWSGSOpt("action", PARAM_ALPHANUMEXT);
    if ($r_ac === false || strlen($r_ac) == 0) {
        RWSBErr("No service action was specified.");
    }
    $r_rv = RWSGSOpt("version", PARAM_ALPHANUMEXT);
    if ($r_rv === false || strlen($r_rv) == 0) {
        $r_bv = 2009093000;
    } else {
        $r_bv = intval($r_rv);
    }
    $r_usrn = RWSGSOpt("username", PARAM_RAW);
    if ($r_usrn === false || strlen($r_usrn) == 0) {
        RWSBErr("No username specified.");
    }
    $r_rtm = RWSGSOpt("time", PARAM_ALPHANUM);
    if ($r_rtm === false || strlen($r_rtm) == 0) {
        RWSBErr("No request time specified.");
    }
    $r_rmc = RWSGSOpt("mac", PARAM_ALPHANUMEXT);
    if ($r_rmc === false || strlen($r_rmc) == 0) {
        RWSBErr("No message authentication code specified.");
    }
    if ($r_bv < 2013061700) {
        RWSBErr(
          "The authentication framework is not supported by the requested behavior version."
          );
    }
    if (strcmp($r_usrn, $USER->username) != 0) {
        RWSBErr("Invalid username specified.");
    }
    $r_ctm = time();
    $r_mxt = $r_rtm + (60 * 10);
    if ($r_ctm < $r_rtm || $r_ctm > $r_mxt) {
        RWSBErr("Invalid request time specified.");
    }
    $r_chm = RWSGAMac(
      $r_ac . $r_bv . $r_usrn . $r_rtm
      );
    if (strcmp($r_chm, $r_rmc) != 0) {
        RWSBErr("Invalid message authentication code.");
    }
    $r_rws = $DB->get_record("respondusws", array("course" => SITEID));
    if ($r_rws === false) {
        RWSBErr(
          "The respondusws module has not yet been installed. Please contact the system administrator."
          );
    }
    $r_chrs = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $r_cmx = strlen($r_chrs) - 1;
    $r_tln = 40;
    $r_tok = "";
    for ($r_i = 0; $r_i < $r_tln; $r_i++) {
        $r_tok .= substr($r_chrs, mt_rand(0, $r_cmx), 1);
    }
    $r_h = sha1($r_tok);
    $r_auu = $DB->get_record("respondusws_auth_users",
      array("responduswsid" => $r_rws->id, "userid" => $USER->id));
    if ($r_auu === false) {
        $r_auu = new stdClass();
        $r_auu->responduswsid = $r_rws->id;
        $r_auu->userid = $USER->id;
        $r_auu->authtoken = $r_h;
        $r_auu->timeissued = $r_ctm;
        try {
            $r_auu->id = $DB->insert_record("respondusws_auth_users", $r_auu);
        } catch (Exception $r_e) {
            RWSBErr("Unable to issue authentication token.");
        }
    } else {
        $r_auu->authtoken = $r_h;
        $r_auu->timeissued = $r_ctm;
        try {
            $DB->update_record("respondusws_auth_users", $r_auu);
        } catch (Exception $r_e) {
            RWSBErr("Unable to issue authentication token.");
        }
    }
    RWSRHHtml();
    echo "{\"RWSAuthToken\":\"$r_tok\"}";
    exit;
}
function RWSGAMac($r_inp) {
    $r_srt = get_config("respondusws", "secret");
    if ($r_srt === false || strlen($r_srt) == 0) {
        RWSBErr("Secret not found in module authentication settings.");
    }
    return sha1($r_inp . $r_srt);
}
function RWSELog($r_msg) {
    global $RWSEDBG;
    global $RWSDBGL;
    if ($RWSEDBG) {
        $r_ent  = date("m-d-Y H:i:s") . " - " . $r_msg . "\r\n";
        $r_pth   = RWSGTPath();
        $r_hdl = fopen("$r_pth/$RWSDBGL", "ab");
        if ($r_hdl !== false) {
            fwrite($r_hdl, $r_ent, strlen($r_ent));
            fclose($r_hdl);
        }
    }
}
function RWSEHdlr($r_ex) {
    abort_all_db_transactions();
    $r_inf = get_exception_info($r_ex);
    $r_msg = "\r\n-- Exception occurred --";
    $r_msg .= "\r\nmessage: $r_inf->message";
    $r_msg .= "\r\nerrorcode: $r_inf->errorcode";
    $r_msg .= "\r\nfile: " . $r_ex->getFile();
    $r_msg .= "\r\nline: " . $r_ex->getLine();
    $r_msg .= "\r\nlink: $r_inf->link";
    $r_msg .= "\r\nmoreinfourl: $r_inf->moreinfourl";
    $r_msg .= "\r\na: $r_inf->a";
    $r_msg .= "\r\ndebuginfo: $r_inf->debuginfo\r\n";
    RWSELog($r_msg);
    RWSELog("\r\nstacktrace: " . $r_ex->getTraceAsString());
    RWSSErr("2112,$r_inf->errorcode");
}
