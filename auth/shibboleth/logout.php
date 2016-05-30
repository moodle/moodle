<?php

// Implements logout for Shibboleth authenticated users according to:
// - https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPLogoutInitiator
// - https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPNotify

require_once("../../config.php");

require_once($CFG->dirroot."/auth/shibboleth/auth.php");

$action = optional_param('action', '', PARAM_ALPHA);
$redirect = optional_param('return', '', PARAM_URL);

// Find out whether host supports https
$protocol = 'http://';
if (is_https()) {
    $protocol = 'https://';
}

// If the shibboleth plugin is not enable, throw an exception.
if (!is_enabled_auth('shibboleth')) {
    throw new moodle_exception(get_string('pluginnotenabled', 'auth', 'shibboleth'));
}

// Front channel logout.
$inputstream = file_get_contents("php://input");
if ($action == 'logout' && !empty($redirect)) {

    if ($USER->auth == 'shibboleth') {
        // Logout out user from application.
        require_logout();
         // Finally, send user to the return URL.
        redirect($redirect);
    }

} else if (!empty($inputstream)) {

    // Back channel logout.
    // Set SOAP header.
    $server = new SoapServer($protocol.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'/LogoutNotification.wsdl');
    $server->addFunction("LogoutNotification");
    $server->handle();

} else {

    // Return WSDL.
    header('Content-Type: text/xml');

    echo <<<WSDL
<?xml version ="1.0" encoding ="UTF-8" ?>
<definitions name="LogoutNotification"
  targetNamespace="urn:mace:shibboleth:2.0:sp:notify"
  xmlns:notify="urn:mace:shibboleth:2.0:sp:notify"
  xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
  xmlns="http://schemas.xmlsoap.org/wsdl/">

<!--
This page either has to be called with the GET arguments 'action' and 'return' via
a redirect from the Shibboleth Service Provider logout handler (front-channel
logout) or via a SOAP request by a Shibboleth Service Provider (back-channel
logout).
Because neither of these two variants seems to be the case, the WSDL file for
the web service is returned.

For more information see:
- https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPLogoutInitiator
- https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPNotify
-->

    <types>
       <schema targetNamespace="urn:mace:shibboleth:2.0:sp:notify"
           xmlns="http://www.w3.org/2000/10/XMLSchema"
           xmlns:notify="urn:mace:shibboleth:2.0:sp:notify">

            <simpleType name="string">
                <restriction base="string">
                    <minLength value="1"/>
                </restriction>
            </simpleType>

            <element name="OK" type="notify:OKType"/>
            <complexType name="OKType">
                <sequence/>
            </complexType>

        </schema>
    </types>

    <message name="getLogoutNotificationRequest">
        <part name="SessionID" type="notify:string" />
    </message>

    <message name="getLogoutNotificationResponse" >
        <part name="OK"/>
    </message>

    <portType name="LogoutNotificationPortType">
        <operation name="LogoutNotification">
            <input message="getLogoutNotificationRequest"/>
            <output message="getLogoutNotificationResponse"/>
        </operation>
    </portType>

    <binding name="LogoutNotificationBinding" type="notify:LogoutNotificationPortType">
        <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
        <operation name="LogoutNotification">
            <soap:operation soapAction="urn:xmethods-logout-notification#LogoutNotification"/>
        </operation>
    </binding>

    <service name="LogoutNotificationService">
          <port name="LogoutNotificationPort" binding="notify:LogoutNotificationBinding">
            <soap:address location="{$protocol}{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}"/>
          </port>
    </service>
</definitions>
WSDL;
    exit;
}
/******************************************************************************/

function LogoutNotification($SessionID){

    global $CFG, $SESSION, $DB;

    // Delete session of user using $SessionID
    if(empty($CFG->dbsessions)) {

        // File session
        $dir = $CFG->dataroot .'/sessions';
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                // Read all session files
                while (($file = readdir($dh)) !== false) {
                    // Check if it is a file
                    if (is_file($dir.'/'.$file)){
                        $session_key = preg_replace('/sess_/', '', $file);

                        // Read session file data
                        $data = file($dir.'/'.$file);
                        if (isset($data[0])){
                            $user_session = unserializesession($data[0]);

                            // Check if we have found session that shall be deleted
                            if (isset($user_session['SESSION']) && isset($user_session['SESSION']->shibboleth_session_id)){

                                // If there is a match, delete file
                                if ($user_session['SESSION']->shibboleth_session_id == $SessionID){
                                    // Delete session file
                                    if (!unlink($dir.'/'.$file)){
                                        return new SoapFault('LogoutError', 'Could not delete Moodle session file.');
                                    }
                                }
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
    } else {
        // DB Session
        //TODO: this needs to be rewritten to use new session stuff
        if (!empty($CFG->sessiontimeout)) {
            $ADODB_SESS_LIFE   = $CFG->sessiontimeout;
        }

            if ($user_session_data = $DB->get_records_sql('SELECT sesskey, sessdata FROM {sessions2} WHERE expiry > NOW()')) {
            foreach ($user_session_data as $session_data) {

                // Get user session
                $user_session = adodb_unserialize( urldecode($session_data->sessdata) );

                if (isset($user_session['SESSION']) && isset($user_session['SESSION']->shibboleth_session_id)){

                    // If there is a match, delete file
                    if ($user_session['SESSION']->shibboleth_session_id == $SessionID){
                        // Delete this session entry
                        if (ADODB_Session::destroy($session_data->sesskey) !== true){
                            return new SoapFault('LogoutError', 'Could not delete Moodle session entry in database.');
                        }
                    }
                }
            }
        }
    }

    // If now SoapFault was thrown the function will return OK as the SP assumes

}

/*****************************************************************************/

// Same function as in adodb, but cannot be used for file session for some reason...
function unserializesession($serialized_string) {
    $variables = array();
    $a = preg_split("/(\w+)\|/", $serialized_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $counta = count($a);
    for ($i = 0; $i < $counta; $i = $i+2) {
            $variables[$a[$i]] = unserialize($a[$i+1]);
    }
    return $variables;
}
