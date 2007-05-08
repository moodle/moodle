<?php
/**********************************************
 * Sets up an automatic grouping 
 **********************************************/
 
require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

require_login($courseid);

groups_seed_random_number_generator();

$courseid = required_param('courseid', PARAM_INT);

$noofstudents   = required_param('noofstudents', PARAM_INT);
$noofgroups     = required_param('noofgroups', PARAM_INT);
$distribevenly  = required_param('distribevenly', PARAM_INT); //TODO: PARAM_BOOL ?
$alphabetical   = required_param('alphabetical', PARAM_INT);
$generationtype = required_param('generationtype', PARAM_ALPHA);

$groupingsettings->name = required_param('name', PARAM_ALPHANUM);
$groupingsettings->description = required_param('description', PARAM_ALPHANUM);
$groupingsettings->prefix = required_param('prefix');
$groupingsettings->defaultgroupdescription = required_param('defaultgroupdescription');


if (confirm_sesskey() and isteacheredit($courseid)) {
    if ($generationtype == 'nogroups') {
        $noofstudents = false;
    }

    $groupingid = groups_create_automatic_grouping($courseid, $noofstudents, $noofgroups, 
        $distribevenly, $groupingsettings, false, $alphabetical); 
    if (!$groupingid) {
        echo '<error>Failed to create grouping</error>';
    } else {
        echo '<groupingid>'.$groupingid.'</groupingid>';
    }
}

echo '</groupsresponse>';
?>
