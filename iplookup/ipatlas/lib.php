<?php  // $Id$
       // Lookup a user using ipatlas and NetGeo

       // The database for this is REALLY old now and this service is 
       // next to useless.

function iplookup_display($ip, $user=0) {
    global $CFG;

    redirect($CFG->wwwroot.'/iplookup/ipatlas/plot.php?address='.$ip.'&amp;user='.$user);
}

?>
