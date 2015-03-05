<?php
 /*
     session.php - Sandbox sesson manager

     Version     : 1.1.0
     Made by     : Jean-Damien POGOLOTTI
     Last Update : 18/01/11

     This file can be distributed under the license you can find at :

                       http://www.pchart.net/license

     You can find the whole class documentation on the pChart web site.
 */

 session_start();

 foreach($_GET as $Key => $Value)
  { $_SESSION[$Key] = $Value; }

 print_r($_SESSION);

 usleep(200000);
?>