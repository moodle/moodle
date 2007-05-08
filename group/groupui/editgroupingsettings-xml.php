<?php
/**********************************************
 * Adds a new grouping
 **********************************************/

require_once('../../config.php');
require_once('../lib/lib.php');

@header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<groupsresponse>';

$groupingid = required_param('groupingid', PARAM_INT);
$courseid   = required_param('courseid', PARAM_INT);

$groupingsettings->name       = required_param('groupingname', PARAM_ALPHANUM);
$groupingsettings->description= required_param('description', PARAM_ALPHANUM);

require_login($courseid);

if (confirm_sesskey() and isteacheredit($courseid)) {
    $groupingid = groups_set_grouping_settings($groupingid, $groupingsettings);
}

echo '</groupsresponse>';
?>
