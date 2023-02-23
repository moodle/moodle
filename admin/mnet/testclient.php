<?php
/**
 * A service browser for remote Moodles
 *
 * This script 'remotely' executes the reflection methods on a remote Moodle,
 * and publishes the details of the available services
 *
 * @package    core
 * @subpackage mnet
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */
require(__DIR__.'/../../config.php');
require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';
require_once($CFG->libdir.'/adminlib.php');
include_once($CFG->dirroot.'/mnet/lib.php');

if ($CFG->mnet_dispatcher_mode === 'off') {
    throw new \moodle_exception('mnetdisabled', 'mnet');
}

admin_externalpage_setup('mnettestclient');

error_reporting(DEBUG_ALL);

echo $OUTPUT->header();
if (!extension_loaded('openssl')) {
    throw new \moodle_exception('requiresopenssl', 'mnet', '', null, true);
}

// optional drilling down parameters
$hostid = optional_param('hostid', 0, PARAM_INT);
$servicename = optional_param('servicename', '', PARAM_SAFEDIR);
$methodid = optional_param('method', 0, PARAM_INT);

$hosts = $DB->get_records('mnet_host');
$moodleapplicationid = $DB->get_field('mnet_application', 'id', array('name' => 'moodle'));

$url = new moodle_url('/admin/mnet/testclient.php');
$PAGE->set_url($url);

echo $OUTPUT->heading(get_string('hostlist', 'mnet'));
foreach ($hosts as $id => $host) {
    if (empty($host->wwwroot) || $host->wwwroot == $CFG->wwwroot) {
        continue;
    }
    $newurl = new moodle_url($url, array('hostid' => $host->id));
    echo '<p>' . html_writer::link($newurl, $host->wwwroot) . '</p>';
}

if (!empty($hostid) && array_key_exists($hostid, $hosts)) {
    $host = $hosts[$hostid];
    if ($host->applicationid != $moodleapplicationid) {
        echo $OUTPUT->notification(get_string('notmoodleapplication', 'mnet'));
    }
    $mnet_peer = new mnet_peer();
    $mnet_peer->set_wwwroot($host->wwwroot);

    $mnet_request = new mnet_xmlrpc_client();

    $mnet_request->set_method('system/listServices');
    $mnet_request->send($mnet_peer);

    $services = $mnet_request->response;
    $yesno = array('No', 'Yes');
    $servicenames = array();

    echo $OUTPUT->heading(get_string('servicesavailableonhost', 'mnet', $host->wwwroot));

    if (!empty($mnet_request->error)) {
        echo $OUTPUT->heading(get_string('error'), 3);
        echo html_writer::alist($mnet_request->error);
        $services = array();
    }

    $table = new html_table();
    $table->head = array(
        get_string('serviceid', 'mnet'),
        get_string('service', 'mnet'),
        get_string('version', 'mnet'),
        get_string('theypublish', 'mnet'),
        get_string('theysubscribe', 'mnet'),
        get_string('options', 'mnet'),
    );
    $table->data = array();

    $yesno = array(get_string('no'), get_string('yes'));

    // this query is horrible and has to be remapped afterwards, because of the non-uniqueness
    // of the remoterep service (it has two plugins so far that use it)
    // it's possible to get a unique list back using a subquery with LIMIT but that would break oracle
    // so it's best to just do this small query and then remap the results afterwards
    $sql = '
        SELECT DISTINCT
            ' . $DB->sql_concat('r.plugintype', "'_'", 'r.pluginname', "'_'", 's.name')  . ' AS uniqueid,
             s.name,
             r.plugintype,
             r.pluginname
        FROM
            {mnet_service} s
       JOIN {mnet_remote_service2rpc} s2r ON s2r.serviceid = s.id
       JOIN {mnet_remote_rpc} r ON r.id = s2r.rpcid';

    $serviceinfo = array();

    foreach ($DB->get_records_sql($sql) as $result) {
        $serviceinfo[$result->name] = $result->plugintype . '_' . $result->pluginname;
    }

    foreach ($services as $id => $servicedata) {
        if (array_key_exists($servicedata['name'], $serviceinfo)) {
            $service = $serviceinfo[$servicedata['name']];
            $servicedata['humanname'] = get_string($servicedata['name'].'_name', $service);
        } else {
            $servicedata['humanname'] = get_string('unknown', 'mnet');
        }
        $newurl = new moodle_url($url, array('hostid' => $host->id, 'servicename' => $servicedata['name']));
        $table->data[] = array(
            $servicedata['name'],
            $servicedata['humanname'],
            $servicedata['apiversion'],
            $yesno[$servicedata['publish']],
            $yesno[$servicedata['subscribe']],
            html_writer::link($newurl, get_string('listservices', 'mnet'))
        );

    }
    echo html_writer::table($table);


    $mnet_request = new mnet_xmlrpc_client();
    $mnet_request->set_method('system/listMethods');
    if (isset($servicename) && array_key_exists($servicename, $serviceinfo)) {
        echo $OUTPUT->heading(get_string('methodsavailableonhostinservice', 'mnet', (object)array('host' => $host->wwwroot, 'service' => $servicename)));
        $service = $serviceinfo[$servicename];
        $mnet_request->add_param($servicename, 'string');
    } else {
        echo $OUTPUT->heading(get_string('methodsavailableonhost', 'mnet', $host->wwwroot));
    }

    $mnet_request->send($mnet_peer);
    $methods = $mnet_request->response;

    if (!empty($mnet_request->error)) {
        echo $OUTPUT->heading(get_string('error'), 3);
        echo html_writer::alist($mnet_request->error);
        $methods = array();
    }

    $table = new html_table();
    $table->head = array(
        get_string('method', 'mnet'),
        get_string('options', 'mnet'),
    );
    $table->data = array();

    foreach ($methods as $id => $method) {
        $params = array('hostid' => $host->id, 'method' => $id+1);
        if (isset($servicename)) {
            $params['servicename'] = $servicename;
        }
        $newurl = new moodle_url($url, $params);
        $table->data[] = array(
            $method,
            html_writer::link($newurl, get_string('inspect', 'mnet'))
        );
    }
    echo html_writer::table($table);

    if (isset($methodid) && array_key_exists($methodid-1, $methods)) {
        $method = $methods[$methodid-1];

        $mnet_request = new mnet_xmlrpc_client();
        $mnet_request->set_method('system/methodSignature');
        $mnet_request->add_param($method, 'string');
        $mnet_request->send($mnet_peer);
        $signature = $mnet_request->response;

        echo $OUTPUT->heading(get_string('methodsignature', 'mnet', $method));

        if (!empty($mnet_request->error)) {
            echo $OUTPUT->heading(get_string('error'), 3);
            echo html_writer::alist($mnet_request->error);
            $signature = array();
        }

        $table = new html_table();
        $table->head = array(
            get_string('position', 'mnet'),
            get_string('name', 'mnet'),
            get_string('type', 'mnet'),
            get_string('description', 'mnet'),
        );
        $table->data = array();

        $params = $signature['parameters'];
        foreach ($params as $pos => $details) {
            $table->data[] = array(
                $pos,
                $details['name'],
                $details['type'],
                $details['description'],
            );
        }
        $table->data[] = array(
            get_string('returnvalue', 'mnet'),
            '',
            $signature['return']['type'],
            $signature['return']['description']
        );

        echo html_writer::table($table);

        $mnet_request->set_method('system/methodHelp');
        $mnet_request->add_param($method, 'string');
        $mnet_request->send($mnet_peer);
        $help = $mnet_request->response;

        echo $OUTPUT->heading(get_string('methodhelp', 'mnet', $method));
        echo(str_replace('\n', '<br />',$help));
    }
}

echo $OUTPUT->footer();
?>
