<?php
/**
 * Main script - REST server
 *
 * @author Jerome Mouneyrac <jerome@moodle.com>
 * @version 1.0
 * @package webservices
 */

/*
 * Zend Rest server
 */
require_once(dirname(__FILE__) . '/../../config.php');
include "Zend/Loader.php";
Zend_Loader::registerAutoload();
if (empty($CFG->enablewebservices)) {
    die;
}

// retrieve the token from the url
// if the token doesn't exist, set a class containing only get_token()
$token = optional_param('token',null,PARAM_ALPHANUM);
if (empty($token)) {
    $server = new Zend_Rest_Server();
    $server->setClass("soap_authentication");
    $server->handle();
} else { // if token exist, do the authentication here
    /// TODO: following function will need to be modified
    $user = mock_check_token($token);
    if (empty($user)) {
        throw new moodle_exception('wrongidentification');
    } else {
        /// TODO: probably change this
        global $USER;
        $USER = $user;
    }

    //retrieve the api name
    $classpath = optional_param(classpath,null,PARAM_ALPHA);
    require_once(dirname(__FILE__) . '/../../'.$classpath.'/external.php');

    /// run the server
    $server = new Zend_Rest_Server(); //TODO: need to call the wsdl generation on the fly
    $server->setClass($classpath."_external"); //TODO: pass $user as parameter
    $server->handle();
}


function mock_check_token($token) {
    //fake test
    if ($token == 465465465468468464) {
        ///retrieve the user
        global $DB;
        $user = $DB->get_record('user', array('username'=>'wsuser', 'mnethostid'=>1));

        if (empty($user)) {
            return false;
        }

        return $user;
    } else {
        return false;
    }
}

class soap_authentication {
    /**
     *
     * @param array $params
     * @return integer
     */
    function tmp_get_token($params) {
        if ($params['username'] == 'wsuser' && $params['password'] == 'wspassword') {
            return '465465465468468464';
        } else {
            throw new moodle_exception('wrongusernamepassword');
        }
    }
}
?>