<?php  // $Id$
       
/*
 *  Jumps to a given URL.  Mostly used for accessibility.
 *
 */

    require('../config.php');

    $jump = optional_param('jump', '');

    if ($jump) {
        redirect(urldecode($jump));
    }

    redirect($_SERVER['HTTP_REFERER']);

?>
