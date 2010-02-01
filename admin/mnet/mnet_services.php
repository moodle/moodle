<?php
    // Allows the admin to configure services for remote hosts

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');
    include_once($CFG->dirroot.'/mnet/lib.php');

    require_login();
    admin_externalpage_setup('mnetpeers');

    $context = get_context_instance(CONTEXT_SYSTEM);

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

    if (!$site = get_site()) {
        print_error('nosite', '', '', NULL, true);
    }

/// Initialize variables.

    // Step must be one of:
    // input   Parse the details of a new host and fetch its public key
    // commit  Save our changes (to a new OR existing host)
    // force   Go ahead with something we've been warned is strange
    $step   = optional_param('step', NULL, PARAM_ALPHA);
    $hostid = optional_param('hostid', NULL, PARAM_INT);
    $publishes  = optional_param('publish', null, PARAM_BOOL); // optional_param cleans arrays too
    $subscribes = optional_param('subscribe', null, PARAM_BOOL); // optional_param cleans arrays too
    $exists     = optional_param('exists', null, PARAM_BOOL); // optional_param cleans arrays too

    $nocertstring = '';
    $nocertmatch  = '';
    $badcert = '';
    $certerror = '';
    $noipmatch = '';
    $stradministration   = get_string('administration');
    $strconfiguration    = get_string('configuration');
    $strmnetsettings     = get_string('mnetsettings', 'mnet');
    $strmnetservices     = get_string('mnetservices', 'mnet');
    $strmnetthemes       = get_string('mnetthemes', 'mnet');
    $strmnetlog          = get_string('mnetlog', 'mnet');
    $strmnetedithost     = get_string('reviewhostdetails', 'mnet');
    $strmneteditservices = get_string('reviewhostservices', 'mnet');

    $mnet_peer = new mnet_peer();

    if (($form = data_submitted()) && confirm_sesskey()) {
        $mnet_peer->set_id($hostid);
        $treevals = array();
        foreach($exists as $key => $value) {
            $host2service   = get_record('mnet_host2service', 'hostid', $hostid, 'serviceid', $key);
            $publish        = array_key_exists($key, $publishes) ? $publishes[$key] : 0;
            $subscribe      = array_key_exists($key, $subscribes) ? $subscribes[$key] : 0;

            if ($publish != 1 && $subscribe != 1) {
                if (false == $host2service) {
                    // We don't have or need a record - do nothing!
                } else {
                    // We don't need the record - delete it
                    delete_records('mnet_host2service', 'hostid', $hostid, 'serviceid', $key);
                }
            } elseif (false == $host2service && ($publish == 1 || $subscribe == 1)) {
                $host2service = new stdClass();
                $host2service->hostid = $hostid;
                $host2service->serviceid = $key;
                
                $host2service->publish = $publish;
                $host2service->subscribe = $subscribe;

                $host2service->id = insert_record('mnet_host2service', $host2service);
            } elseif ($host2service->publish != $publish || $host2service->subscribe != $subscribe) {
                $host2service->publish   = $publish;
                $host2service->subscribe = $subscribe;
                $tf = update_record('mnet_host2service', $host2service);
            }
        }
    }

    if (is_int($hostid)) {
        if (0 == $mnet_peer->id) $mnet_peer->set_id($hostid);
        $mnet_peer->nextstep = 'verify';

        $id_list = $mnet_peer->id;
        if (!empty($CFG->mnet_all_hosts_id)) {
            $id_list .= ', '.$CFG->mnet_all_hosts_id;
        }

        $concat = sql_concat('COALESCE(h2s.id,0) ', ' \'-\' ', ' svc.id', '\'-\'', 'r.parent_type', '\'-\'', 'r.parent');

        $query = "
            SELECT DISTINCT
                $concat as id,
                svc.id as serviceid,
                svc.name,
                svc.offer,
                svc.apiversion,
                r.parent_type,
                r.parent,
                h2s.hostid,
                h2s.publish,
                h2s.subscribe
            FROM
                {$CFG->prefix}mnet_service2rpc s2r,
                {$CFG->prefix}mnet_rpc r,
                {$CFG->prefix}mnet_service svc
            LEFT JOIN
                {$CFG->prefix}mnet_host2service h2s
            ON
                h2s.hostid in ($id_list) AND
                h2s.serviceid = svc.id
            WHERE
                svc.offer = '1' AND
                s2r.serviceid = svc.id AND
                s2r.rpcid = r.id
            ORDER BY
                svc.name ASC";

        $resultset = get_records_sql($query);

        if (is_array($resultset)) {
            $resultset = array_values($resultset);
        } else {
            $resultset = array();
        }

        require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';

        $remoteservices = array();
        if ($hostid != $CFG->mnet_all_hosts_id) {
            // Create a new request object
            $mnet_request = new mnet_xmlrpc_client();

            // Tell it the path to the method that we want to execute
            $mnet_request->set_method('system/listServices');
            $mnet_request->send($mnet_peer);
            if (is_array($mnet_request->response)) {
                foreach($mnet_request->response as $service) {
                    $remoteservices[$service['name']][$service['apiversion']] = $service;
                }
            }
        }

        $myservices = array();
        foreach($resultset as $result) {
            $result->hostpublishes  = false;
            $result->hostsubscribes = false;
            if (isset($remoteservices[$result->name][$result->apiversion])) {
                if ($remoteservices[$result->name][$result->apiversion]['publish'] == 1) {
                    $result->hostpublishes  = true;
                }
                if ($remoteservices[$result->name][$result->apiversion]['subscribe'] == 1) {
                    $result->hostsubscribes  = true;
                }
            }

            if (empty($myservices[$result->name][$result->apiversion])) {
                $myservices[$result->name][$result->apiversion] = array('serviceid' => $result->serviceid,
                                                                        'name' => $result->name,
                                                                        'offer' => $result->offer,
                                                                        'apiversion' => $result->apiversion,
                                                                        'parent_type' => $result->parent_type,
                                                                        'parent' => $result->parent,
                                                                        'hostsubscribes' => $result->hostsubscribes,
                                                                        'hostpublishes' => $result->hostpublishes
                                                                        );
            }

            // allhosts_publish allows us to tell the admin that even though he
            // is disabling a service, it's still available to the host because
            // he's also publishing it to 'all hosts'
            if ($result->hostid == $CFG->mnet_all_hosts_id && $CFG->mnet_all_hosts_id != $mnet_peer->id) {
                $myservices[$result->name][$result->apiversion]['allhosts_publish'] = $result->publish;
                $myservices[$result->name][$result->apiversion]['allhosts_subscribe'] = $result->subscribe;
            } elseif (!empty($result->hostid)) {
                $myservices[$result->name][$result->apiversion]['I_publish'] = $result->publish;
                $myservices[$result->name][$result->apiversion]['I_subscribe'] = $result->subscribe;
            }

        }

    } else {
        redirect('peers.php', get_string('nohostid','mnet'), '5');
        exit;
    }

    include('./mnet_services.html');
?>
