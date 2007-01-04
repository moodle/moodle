<?php 


    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    include_once($CFG->dirroot.'/mnet/lib.php');

    require_login();

    if (!isadmin()) {
        error('Only administrators can use this page!');
    }

    if (!$site = get_site()) {
        error('Site isn\'t defined!');
    }
    $hostid = optional_param('hostid', NULL, PARAM_INT);
    $stradministration   = get_string('administration');
    $strconfiguration    = get_string('configuration');
    $strmnetsettings     = get_string('mnetsettings', 'mnet');
    $strmnetservices     = get_string('mnetservices', 'mnet');
    $strmnetlog          = get_string('mnetlog', 'mnet');
    $strmnetedithost     = get_string('reviewhostdetails', 'mnet');
    $strmneteditservices = get_string('reviewhostservices', 'mnet');

print_header("$site->shortname: $strmnetsettings", "$site->fullname",
             '<a href="'.$CFG->wwwroot.'/admin/index.php">'.$stradministration.'</a> -> '.
             '<a href="'.$CFG->wwwroot.'/admin/mnet/index.php">'.get_string('mnetsettings', 'mnet').'</a> -> '.get_string('hostsettings', 'mnet'));

print_heading(get_string('mnetsettings', 'mnet'));
$tabs[] = new tabobject('mnetdetails', 'index.php?step=update&hostid='.$hostid, $strmnetedithost, $strmnetedithost, false);
$tabs[] = new tabobject('mnetservices', 'mnet_services.php?step=list&hostid='.$hostid, $strmnetservices, $strmnetservices, false);
$tabs[] = new tabobject('mnetlog', 'mnet_log.php?step=list&hostid='.$hostid, $strmnetlog, $strmnetlog, false);
print_tabs(array($tabs), 'mnetlog');
print_simple_box_start("center", ""); 

?>

<table cellpadding="9" cellspacing="0" >
<tr>
    <td align="left" valign="top" colspan="2">
    Activity Logs
    </td>
</tr>

<tr>
    <td align="left" valign="top" colspan="2">
    <div id="formElements"><input type="hidden" name="outer" value="4" /></div>
    </td>
</tr>

<?php

echo '    </table>';
print_simple_box_end();
print_simple_box_end();

?>
</form>

<?php

print_footer();
?>
