<?php
/**
 * Library functions for mnet
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

/**
 * Parse a file to find out what functions/methods exist in it, and add entries
 * for the remote-call-enabled functions to the database.
 *
 * The path to a file, e.g. auth/mnet/auth.php can be thought of as
 * type/parentname/docname
 *
 * @param  string   $type           mod, auth or enrol
 * @param  string   $parentname     Implementation of type, e.g. 'mnet' in the
 *                                  case of auth/mnet/auth.php
 * @return bool                     True on success, else false
 */
function mnet_get_functions($type, $parentname) {
    global $CFG, $DB;

    $dataobject = new stdClass();
    $docname = $type.'.php';
    $publishes = array();
    if ('mod' == $type) {
        $docname = 'rpclib.php';
        $relname  = '/mod/'.$parentname.'/'.$docname;
        $filename = $CFG->dirroot.$relname;
        if (file_exists($filename)) include_once $filename;
        $mnet_publishes = $parentname.'_mnet_publishes';
        if (function_exists($mnet_publishes)) {
            (array)$publishes = $mnet_publishes();
        }
    } else if ('portfolio' == $type) {
        $docname = 'lib.php';
        $relname = '/portfolio/' . $parentname . '/'. $docname;
        $filename = $CFG->dirroot . $relname;
        require_once($CFG->libdir . '/portfoliolib.php');
        $publishes = (array)portfolio_static_function($parentname, 'mnet_publishes');
    } else if ('repository' == $type) {
        $docname = 'repository.class.php';
        $relname = '/repository/' . $parentname . '/'. $docname;
        $filename = $CFG->dirroot . $relname;
        require_once($CFG->dirroot . '/repository/lib.php');
        $publishes = (array)repository::static_function($parentname, 'mnet_publishes');
    } else {
        // auth or enrol
        $relname  = '/'.$type.'/'.$parentname.'/'.$docname;
        $filename = $CFG->dirroot.$relname;
        if (file_exists($filename)) include_once $filename;
        $class = $type.($type=='enrol'? 'ment':'').'_plugin_'.$parentname;
        if (class_exists($class)) {
            $object = new $class();
            if (method_exists($object, 'mnet_publishes')) {
                (array)$publishes = $object->mnet_publishes();
            }
        }
    }

    $methodServiceArray = array();
    foreach($publishes as $service) {
        if (is_array($service['methods'])) {
            foreach($service['methods'] as $methodname) {
                $methodServiceArray[$methodname][] = $service;
            }
        }
    }

    // Disable functions that don't exist (any more) in the source
    // Should these be deleted? What about their permissions records?
    $rpcrecords = $DB->get_records('mnet_rpc', array('parent'=>$parentname, 'parent_type'=>$type), 'function_name ASC ');
    if (!empty($rpcrecords)) {
        foreach($rpcrecords as $rpc) {
            if (!array_key_exists($rpc->function_name, $methodServiceArray)) {
                $rpc->enabled = 0;
                $DB->update_record('mnet_rpc', $rpc);
            }
        }
    }

    if (!file_exists($filename)) return false;

    if (extension_loaded('tokenizer')) {
        include_once "$CFG->dirroot/$CFG->admin/mnet/MethodTable.php";
        $functions = (array)MethodTable::create($filename,false);
    }

    foreach($methodServiceArray as $method => $servicearray) {
        if (!empty($functions[$method])) {
            $details = $functions[$method];
            $profile = $details['arguments'];
            if (!isset($details['returns'])) {
                array_unshift($profile, array('type' => 'void', 'description' => 'No return value'));
            } else {
                array_unshift($profile, $details['returns']);
            }
            $dataobject->profile       = serialize($profile);
            $dataobject->help          = $details['description'];
        } else {
            $dataobject->profile       = serialize(array(array('type' => 'void', 'description' => 'No return value')));
            $dataobject->help          = '';
        }

        $dataobject->function_name = $method;
        $dataobject->xmlrpc_path   = $type.'/'.$parentname.'/'.$docname.'/'.$method;
        $dataobject->parent_type   = $type;
        $dataobject->parent        = $parentname;
        $dataobject->enabled       = '0';

        if ($record_exists = $DB->get_record('mnet_rpc', array('xmlrpc_path'=>$dataobject->xmlrpc_path))) {
            $dataobject->id      = $record_exists->id;
            $dataobject->enabled = $record_exists->enabled;
            $DB->update_record('mnet_rpc', $dataobject);
        } else {
            $dataobject->id = $DB->insert_record('mnet_rpc', $dataobject, true);
        }

        foreach($servicearray as $service) {
            $serviceobj = $DB->get_record('mnet_service', array('name'=>$service['name']));
            if (false == $serviceobj) {
                $serviceobj = new stdClass();
                $serviceobj->name        = $service['name'];
                $serviceobj->apiversion  = $service['apiversion'];
                $serviceobj->offer       = 1;
                $serviceobj->id          = $DB->insert_record('mnet_service', $serviceobj, true);
            }

            if (false == $DB->get_record('mnet_service2rpc', array('rpcid'=>$dataobject->id, 'serviceid'=>$serviceobj->id))) {
                $obj = new stdClass();
                $obj->rpcid = $dataobject->id;
                $obj->serviceid = $serviceobj->id;
                $DB->insert_record('mnet_service2rpc', $obj, true);
            }
        }
    }
    return true;
}

function upgrade_RPC_functions() {
    global $CFG;

    // TODO: rewrite this thing so that it:
    //         1/ does not include half the world
    //         2/ returns status if something upgraded - needed for proper conitnue button
    //         3/ upgrade functions in general should not use normal function calls to moodle core and modules

    $basedir = $CFG->dirroot.'/mod';
    if (file_exists($basedir) && filetype($basedir) == 'dir') {
        $dirhandle = opendir($basedir);
        while (false !== ($dir = readdir($dirhandle))) {
            $firstchar = substr($dir, 0, 1);
            if ($firstchar == '.' or $dir == 'CVS' or $dir == '_vti_cnf') {
                continue;
            }
            if (filetype($basedir .'/'. $dir) != 'dir') {
                continue;
            }

            mnet_get_functions('mod', $dir);

        }
    }

    $basedir = $CFG->dirroot.'/auth';
    if (file_exists($basedir) && filetype($basedir) == 'dir') {
        $dirhandle = opendir($basedir);
        while (false !== ($dir = readdir($dirhandle))) {
            $firstchar = substr($dir, 0, 1);
            if ($firstchar == '.' or $dir == 'CVS' or $dir == '_vti_cnf') {
                continue;
            }
            if (filetype($basedir .'/'. $dir) != 'dir') {
                continue;
            }

            mnet_get_functions('auth', $dir);
        }
    }

    $basedir = $CFG->dirroot.'/enrol';
    if (file_exists($basedir) && filetype($basedir) == 'dir') {
        $dirhandle = opendir($basedir);
        while (false !== ($dir = readdir($dirhandle))) {
            $firstchar = substr($dir, 0, 1);
            if ($firstchar == '.' or $dir == 'CVS' or $dir == '_vti_cnf') {
                continue;
            }
            if (filetype($basedir .'/'. $dir) != 'dir') {
                continue;
            }

            mnet_get_functions('enrol', $dir);
        }
    }

    if ($plugins = get_list_of_plugins('portfolio')) {
        foreach ($plugins as $p) {
            mnet_get_functions('portfolio', $p);
        }
    }

    if ($plugins = get_list_of_plugins('repository')) {
        foreach ($plugins as $p) {
            mnet_get_functions('repository', $p);
        }
    }
}

