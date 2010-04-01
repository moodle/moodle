<?php // $Id$
/**
 * A service browser for remote Moodles
 *
 * This script 'remotely' executes the reflection methods on a remote Moodle,
 * and publishes the details of the available services
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';

// Site admins only, thanks.
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/site:config', $context);

error_reporting(DEBUG_ALL);

// Some HTML sugar
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head><title>Moodle MNET Test Client</title></head><body>
<H1>Hosts</H1>
<?php

$hosts = get_records('mnet_host');

foreach ($hosts as $id => $host) {
    // Skip the 'all hosts' option
    if(empty($host->wwwroot)) continue;
    // Skip localhost
    if($host->wwwroot == $CFG->wwwroot) continue;
    // Skip non-moodle hosts
    if($host->applicationid != 1 && $host->applicationid != 2) continue; //TODO: get rid of magic numbers.
    echo '<p><a href="testclient.php?hostid='.$host->id.'">'.$host->wwwroot."</a></p>\n";
}

if (!empty($_GET['hostid']) && array_key_exists($_GET['hostid'], $hosts)) {
    $host = $hosts[$_GET['hostid']];
    $mnet_peer = new mnet_peer();
    $mnet_peer->set_wwwroot($host->wwwroot);

    $mnet_request = new mnet_xmlrpc_client();

    // Tell it the path to the method that we want to execute
    $mnet_request->set_method('system/listServices');
    $mnet_request->send($mnet_peer);
    $services = $mnet_request->response;
    $yesno = array('No', 'Yes');
    $servicenames = array();

    echo '<hr /><br /><h3>Services available on host: '.$host->wwwroot .'</h3><table><tr valign="top"><th>&nbsp;&nbsp;Service ID&nbsp;&nbsp;</th><th>&nbsp;&nbsp;Service&nbsp;&nbsp;</th><th>&nbsp;&nbsp;Version&nbsp;&nbsp;</th><th>&nbsp;&nbsp;They Publish&nbsp;&nbsp;</th><th>&nbsp;&nbsp;They Subscribe&nbsp;&nbsp;</th><th></th></tr>';
    foreach ($services as $id => $service) {
        $sql = 'select c.id, c.parent_type, c.parent from '.$CFG->prefix.'mnet_service2rpc a,'.$CFG->prefix.'mnet_service b, '.$CFG->prefix.'mnet_rpc c where a.serviceid = b.id and b.name=\''.addslashes($service['name']).'\' and c.id = a.rpcid ';

        echo '<tr valign="top">
                <td>'.$service['name'].'</td>';
        if ($detail = get_record_sql($sql)) {
            $service['humanname'] = get_string($service['name'].'_name', $detail->parent_type.'_'.$detail->parent);
            echo '<td>'.$service['humanname'].'</td>';
        } else {
            $service['humanname'] = $service['name'];
            echo '<td> unknown </td>';
        }
        echo '
                <td>'.$service['apiversion'].'</td>
                <td>'.$yesno[$service['publish']].'</td>
                <td>'.$yesno[$service['subscribe']].'</td>
                <td><a href="testclient.php?hostid='.$host->id.'&service='.$service['name'].'">List methods</a></td>
            </tr>'."\n";
        $servicenames[$service['name']] = $service;
    }
    echo '</table>';



    if (isset($_GET['service']) && array_key_exists($_GET['service'], $servicenames)) {
        $service = $servicenames[$_GET['service']];
        // Tell it the path to the method that we want to execute
        $mnet_request->set_method('system/listMethods');
        $mnet_request->add_param($service['name'], 'string');
        $mnet_request->send($mnet_peer);
        $methods = $mnet_request->response;

        echo '<hr /><br /><h3>Methods in the '.$service['humanname'] .' service</h3><table><th>Method</th><th colspan="2">Options</th>';
        foreach ($methods as $id => $method) {
            echo '<tr><td>'.$method.'</td><td> <a href="testclient.php?hostid='.$host->id.'&service='.$service['name'].'&method='.$id.'&show=sig">Inspect</a></td></tr>'."\n";
        }
        echo '</table>';
    } else {
        // Tell it the path to the method that we want to execute
        $mnet_request->set_method('system/listMethods');
        $mnet_request->send($mnet_peer);
        $methods = $mnet_request->response;
    
        echo '<hr /><br /><h3>Methods '.$host->wwwroot .'</h3><table><th>Method</th><th colspan="2">Options</th>';
        foreach ($methods as $id => $method) {
            echo '<tr><td>'.$method.'</td><td> <a href="testclient.php?hostid='.$host->id.'&method='.$id.'&show=sig">Inspect</a></td></tr>'."\n";
        }
        echo '</table>';
    }

    if (isset($_GET['method']) && array_key_exists($_GET['method'], $methods)) {
        $method = $methods[$_GET['method']];

        $mnet_request = new mnet_xmlrpc_client();

        // Tell it the path to the method that we want to execute
        $mnet_request->set_method('system/methodSignature');
        $mnet_request->add_param($method, 'string');
        $mnet_request->send($mnet_peer);
        $signature = $mnet_request->response;
        echo '<hr /><br /><h3>Method signature for '.$method.':</h3><table border="1"><th>Position</th><th>Type</th><th>Description</th>';
        $params = array_pop($signature);
        foreach ($params as $pos => $details) {
            echo '<tr><td>'.$pos.'</td><td>'.$details['type'].'</td><td>'.$details['description'].'</td></tr>';
        }
        echo '</table>';

        // Tell it the path to the method that we want to execute
        $mnet_request->set_method('system/methodHelp');
        $mnet_request->add_param($method, 'string');
        $mnet_request->send($mnet_peer);
        $help = $mnet_request->response;
        echo '<hr /><br /><h3>Help details from docblock for '.$method.':</h3>';
        echo(str_replace('\n', '<br />',$help));
        echo '</pre>';
    }
}


?>
</body>
</html>
