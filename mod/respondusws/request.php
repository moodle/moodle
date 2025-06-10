<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
$r_rsv = dirname(__FILE__) . "/servicelib.php";
if (is_readable($r_rsv)) {
    include_once($r_rsv);
    defined("MOODLE_INTERNAL") || die();
} else {
    header("Content-Type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
    echo "<service_error>2000</service_error>\r\n";
    exit;
}
raise_memory_limit(MEMORY_EXTRA);
set_exception_handler("RWSEHdlr");
RWSCMBVer();
RWSCMVer();
RWSCMInst();
if ($RWSECAS) {
    RWSPCReqs();
}
$r_raction = RWSGSOpt("action", PARAM_ALPHANUMEXT);
if ($r_raction === false || strlen($r_raction) == 0) {
    RWSSErr("2001");
} else {
    RWSDSAct($r_raction);
}
