<?php

 # http user space authentication layer
 # пппппппппппппппппппппппппппппппппппп
 # can be used with the tools/, if you don't want to
 # set up the .htaccess and .htpasswd files


 #-- (pw array - I have such one in an external config file)
 $passwords = array(
//   "user" => "password",
//   "u2" => "password",
 );



 #-- fetch user:password
 if ($uu = trim($_SERVER["HTTP_AUTHORIZATION"])) {
    strtok($uu, " ");
    $uu = strtok(" ");
    $uu = base64_decode($uu);
    list($_a_u, $_a_p) = explode(":", $uu, 2);
 }
 elseif (strlen($_a_u = trim($_SERVER["PHP_AUTH_USER"]))) {
    $_a_p = trim($_SERVER["PHP_AUTH_PW"]);
 }

 #-- check password
 $_success = false;
 if (strlen($_a_u) && strlen($_a_p) && ($_a_p == @$passwords[$_a_u])) {
    $_success = $_a_u; 
 }

 #-- request HTTP Basic authentication otherwise
 if (!$_success) {
    header('HTTP/1.1 401 Authentication Required');
    header('Status: 401 Authentication Required');
    header('WWW-Authenticate: Basic realm="restricted access"');
    die();
 }

?>