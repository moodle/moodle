<?php
// version $Id$
// Page for forbidden access from CAS
    require_once("../../config.php");
    $errormsg = get_string("auth_cas_invalidcaslogin", "auth");
    print_header("$site->fullname: $loginsite", "$site->fullname", $loginsite,
    $focus, "", true, "<div align=right>$langmenu</div>");
    include("forbidden.html");
    print_footer();
    exit;
?>

