<?php
// This file must be Shibboleth protected
// Consult the README for further instructions

require_once("../../config.php");
header("Location: ".$CFG->wwwroot."/auth/shibboleth/");
?>
