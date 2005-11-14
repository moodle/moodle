<?php  // $Id$
       
/*
 *  Jumps to a given URL.  Mostly used for accessibility.
 *
 */

    require('../config.php');

//TODO: fix redirect before enabling - SC#189
/*    $jump = optional_param('jump', '');

    if ($jump) {
        redirect(urldecode($jump));
    }*/

    redirect($_SERVER['HTTP_REFERER']);

?>
