<?php
/* THIS IS A STUB RESTORELIB.PHP TO 
 * DEMONSTRATE THE NEW GRANULAR
 * BACKUP/RESTORE API.
 * IT NEEDS TO BE FILLED OUT!!!
 */

function data_restore_mods($mod,$restore) {

    global $CFG;

    $status = true;

    // this all carries on exactly like normal except where checking for user data do
    if (restore_userdata_selected($restore,'data',$mod->id)) {
        // restore userdata, including files.
    }

    // instead of the old way:
    // if ($restore->mods['data']->userinfo) {

    
    // this should be the ONLY CHANGE IN THIS FILE.

    return $status;
}




?>