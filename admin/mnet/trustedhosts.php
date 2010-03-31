<?php
    // Allows the admin to configure services for remote hosts

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');
    include_once($CFG->dirroot.'/mnet/lib.php');

    require_login();
    admin_externalpage_setup('trustedhosts');

    $context = get_context_instance(CONTEXT_SYSTEM);

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

    if (!extension_loaded('openssl')) {
        echo $OUTPUT->header();
        print_error('requiresopenssl', 'mnet', '', NULL, true);
    }

    $site = get_site();

    $trusted_hosts = '';//array();
    $old_trusted_hosts = get_config('mnet', 'mnet_trusted_hosts');
    if (!empty($old_trusted_hosts)) {
        $old_trusted_hosts =  explode(',', $old_trusted_hosts);
    } else {
        $old_trusted_hosts = array();
    }

    $test_ip_address = optional_param('testipaddress', NULL, PARAM_HOST);
    $in_range = false;
    if (!empty($test_ip_address)) {
        foreach($old_trusted_hosts as $host) {
            if (address_in_subnet($test_ip_address, $host)) {
                $in_range = true;
                $validated_by = $host;
                break;
            }
        }
    }

    /// If data submitted, process and store
    if (($form = data_submitted()) && confirm_sesskey()) {
        $hostlist = preg_split("/[\s,]+/", $form->hostlist);
        foreach($hostlist as $host) {
            list($address, $mask) = explode('/', $host.'/');
            if (empty($address)) continue;
            if (strlen($mask) == 0) $mask = 32;
            $trusted_hosts .= trim($address).'/'.trim($mask)."\n";
            unset($address, $mask);
        }
        set_config('mnet_trusted_hosts', str_replace("\n", ',', $trusted_hosts), 'mnet');
    } elseif (!empty($old_trusted_hosts)) {
        foreach($old_trusted_hosts as $host) {
            list($address, $mask) = explode('/', $host.'/');
            if (empty($address)) continue;
            if (strlen($mask) == 0) $mask = 32;
            $trusted_hosts .= trim($address).'/'.trim($mask)."\n";
            unset($address, $mask);
        }
    }

    include('./trustedhosts.html');
