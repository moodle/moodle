<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * This file contains the admin related mnet functions
 *
 * @since 2.0
 * @package moodlecore
 * @copyright 2010 Penny Leach <penny@liip.ch>
 * @copyright 2006 Donal McMullan <donal@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * upgrades the mnet rpc definitions for the given component.
 * this method doesn't return status, an exception will be thrown in the case of an error
 *
 * @param string $component the plugin to upgrade, eg auth_mnet
 */
function upgrade_plugin_mnet_functions($component) {
    global $DB, $CFG;

    list($type, $plugin) = explode('_', $component);
    $path = get_plugin_directory($type, $plugin);

    if (file_exists($path . '/db/mnet.php')) {
        require_once($path . '/db/mnet.php'); // $publishes comes from this file
    }
    if (empty($publishes)) {
        $publishes = array(); // still need this to be able to disable stuff later
    }
    if (empty($subscribes)) {
        $subscribes = array(); // still need this to be able to disable stuff later
    }

    static $servicecache = array();

    // rekey an array based on the rpc method for easy lookups later
    $publishmethodservices = array();
    $subscribemethodservices = array();
    foreach($publishes as $servicename => $service) {
        if (is_array($service['methods'])) {
            foreach($service['methods'] as $methodname) {
                $service['servicename'] = $servicename;
                $publishmethodservices[$methodname][] = $service;
            }
        }
    }

    // Disable functions that don't exist (any more) in the source
    // Should these be deleted? What about their permissions records?
    foreach ($DB->get_records('mnet_rpc', array('pluginname'=>$plugin, 'plugintype'=>$type), 'functionname ASC ') as $rpc) {
        if (!array_key_exists($rpc->functionname, $publishmethodservices) && $rpc->enabled) {
            $DB->set_field('mnet_rpc', 'enabled', 0, array('id' => $rpc->id));
        } else if (array_key_exists($rpc->functionname, $publishmethodservices) && !$rpc->enabled) {
            $DB->set_field('mnet_rpc', 'enabled', 1, array('id' => $rpc->id));
        }
    }

    // reflect all the services we're publishing and save them
    require_once($CFG->dirroot . '/lib/zend/Zend/Server/Reflection.php');
    static $cachedclasses = array(); // to store reflection information in
    foreach ($publishes as $service => $data) {
        $f = $data['filename'];
        $c = $data['classname'];
        foreach ($data['methods'] as $method) {
            $dataobject = new stdclass;
            $dataobject->plugintype  = $type;
            $dataobject->pluginname  = $plugin;
            $dataobject->enabled     = 1;
            $dataobject->classname   = $c;
            $dataobject->filename    = $f;

            if (is_string($method)) {
                $dataobject->functionname = $method;

            } else if (is_array($method)) { // wants to override file or class
                $dataobject->functionname = $method['method'];
                $dataobject->classname     = $method['classname'];
                $dataobject->filename      = $method['filename'];
            }
            $dataobject->xmlrpcpath = $type.'/'.$plugin.'/'.$dataobject->filename.'/'.$method;
            $dataobject->static = false;

            require_once($path . '/' . $dataobject->filename);
            $functionreflect = null; // slightly different ways to get this depending on whether it's a class method or a function
            if (!empty($dataobject->classname)) {
                if (!class_exists($dataobject->classname)) {
                    throw new moodle_exception('installnosuchmethod', 'mnet', '', (object)array('method' => $dataobject->functionname, 'class' => $dataobject->classname));
                }
                $key = $dataobject->filename . '|' . $dataobject->classname;
                if (!array_key_exists($key, $cachedclasses)) { // look to see if we've already got a reflection object
                    try {
                        $cachedclasses[$key] = Zend_Server_Reflection::reflectClass($dataobject->classname);
                    } catch (Zend_Server_Reflection_Exception $e) { // catch these and rethrow them to something more helpful
                        throw new moodle_exception('installreflectionclasserror', 'mnet', '', (object)array('method' => $dataobject->functionname, 'class' => $dataobject->classname, 'error' => $e->getMessage()));
                    }
                }
                $r =& $cachedclasses[$key];
                if (!$r->hasMethod($dataobject->functionname)) {
                    throw new moodle_exception('installnosuchmethod', 'mnet', '', (object)array('method' => $dataobject->functionname, 'class' => $dataobject->classname));
                }
                // stupid workaround for zend not having a getMethod($name) function
                $ms = $r->getMethods();
                foreach ($ms as $m) {
                    if ($m->getName() == $dataobject->functionname) {
                        $functionreflect = $m;
                        break;
                    }
                }
                $dataobject->static = (int)$functionreflect->isStatic();
            } else {
                if (!function_exists($dataobject->functionname)) {
                    throw new moodle_exception('installnosuchfunction', 'mnet', '', (object)array('method' => $dataobject->functionname, 'file' => $dataobject->filename));
                }
                try {
                    $functionreflect = Zend_Server_Reflection::reflectFunction($dataobject->functionname);
                } catch (Zend_Server_Reflection_Exception $e) { // catch these and rethrow them to something more helpful
                    throw new moodle_exception('installreflectionfunctionerror', 'mnet', '', (object)array('method' => $dataobject->functionname, '' => $dataobject->filename, 'error' => $e->getMessage()));
                }
            }
            $dataobject->profile =  serialize(admin_mnet_method_profile($functionreflect));
            $dataobject->help = $functionreflect->getDescription();

            if ($record_exists = $DB->get_record('mnet_rpc', array('xmlrpcpath'=>$dataobject->xmlrpcpath))) {
                $dataobject->id      = $record_exists->id;
                $dataobject->enabled = $record_exists->enabled;
                $DB->update_record('mnet_rpc', $dataobject);
            } else {
                $dataobject->id = $DB->insert_record('mnet_rpc', $dataobject, true);
            }
        }

        foreach ($publishmethodservices[$dataobject->functionname] as $service) {
            if ($serviceobj = $DB->get_record('mnet_service', array('name'=>$service['servicename']))) {
                $serviceobj->apiversion = $service['apiversion'];
                $DB->update_record('mnet_service', $serviceobj);
            } else {
                $serviceobj = new stdClass();
                $serviceobj->name        = $service['servicename'];
                $serviceobj->apiversion  = $service['apiversion'];
                $serviceobj->offer       = 1;
                $serviceobj->id          = $DB->insert_record('mnet_service', $serviceobj);
            }
            $servicecache[$service['servicename']] = $serviceobj;
            if (!$DB->record_exists('mnet_service2rpc', array('rpcid'=>$dataobject->id, 'serviceid'=>$serviceobj->id))) {
                $obj = new stdClass();
                $obj->rpcid = $dataobject->id;
                $obj->serviceid = $serviceobj->id;
                $DB->insert_record('mnet_service2rpc', $obj, true);
            }
        }
    }

    // finished with methods we publish, now do subscribable methods
    foreach($subscribes as $service => $methods) {
        if (!array_key_exists($service, $servicecache)) {
            if (!$serviceobj = $DB->get_record('mnet_service', array('name' =>  $service))) {
                debugging("skipping unknown service $service");
                continue;
            }
            $servicecache[$service] = $serviceobj;
        } else {
            $serviceobj = $servicecache[$service];
        }
        foreach ($methods as $method => $xmlrpcpath) {
            if (!$rpcid = $DB->get_field('mnet_remote_rpc', 'id', array('xmlrpcpath'=>$xmlrpcpath))) {
                $remoterpc = (object)array(
                    'functionname' => $method,
                    'xmlrpcpath' => $xmlrpcpath,
                    'plugintype' => $type,
                    'pluginname' => $plugin,
                    'enabled'    => 1,
                );
                $rpcid = $remoterpc->id = $DB->insert_record('mnet_remote_rpc', $remoterpc, true);
            }
            if (!$DB->record_exists('mnet_remote_service2rpc', array('rpcid'=>$rpcid, 'serviceid'=>$serviceobj->id))) {
                $obj = new stdClass();
                $obj->rpcid = $rpcid;
                $obj->serviceid = $serviceobj->id;
                $DB->insert_record('mnet_remote_service2rpc', $obj, true);
            }
            $subscribemethodservices[$method][] = $servicename;
        }
    }

    foreach ($DB->get_records('mnet_remote_rpc', array('pluginname'=>$plugin, 'plugintype'=>$type), 'functionname ASC ') as $rpc) {
        if (!array_key_exists($rpc->functionname, $subscribemethodservices) && $rpc->enabled) {
            $DB->set_field('mnet_remote_rpc', 'enabled', 0, array('id' => $rpc->id));
        } else if (array_key_exists($rpc->functionname, $subscribemethodservices) && !$rpc->enabled) {
            $DB->set_field('mnet_remote_rpc', 'enabled', 1, array('id' => $rpc->id));
        }
    }

    return true;
}

/**
 * Given some sort of Zend Reflection function/method object, return a profile array, ready to be serialized and stored
 *
 * @param Zend_Server_Reflection_Function_Abstract $function can be any subclass of this object type
 *
 * @return array
 */
function admin_mnet_method_profile(Zend_Server_Reflection_Function_Abstract $function) {
    $proto = array_pop($function->getPrototypes());
    $ret = $proto->getReturnValue();
    $profile = array(
        'parameters' =>  array(),
        'return'     =>  array(
            'type'        => $ret->getType(),
            'description' => $ret->getDescription(),
        ),
    );
    foreach ($proto->getParameters() as $p) {
        $profile['parameters'][] = array(
            'name' => $p->getName(),
            'type' => $p->getType(),
            'description' => $p->getDescription(),
        );
    }
    return $profile;
}
