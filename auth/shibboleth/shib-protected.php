<?php
/* 
This file must be Shibboleth protected with something like:

--
<Location ~  "/auth/shibboleth/shib-protected.php">
        AuthType shibboleth
        ShibRequireSession On
        require valid-user
</Location>
--

in your web server configuration.

Consult moodle/auth/shibboleth/README.txt for further instructions.
*/

require_once("../../config.php");
header("Location: ".$CFG->wwwroot."/auth/shibboleth/");
?>
