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

    if (isloggedin($USER) && $USER->auth == 'shibboleth') {
        // Logout user from application.
        require_logout();
    }

    // Finally, send user to the return URL.
    redirect($redirect);

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

/**
 * Handles SOAP Back-channel logout notification
 *
 * @param string $spsessionid SP-provided Shibboleth Session ID
 * @return SoapFault or void if everything was fine
 */
function LogoutNotification($spsessionid) {
    global $CFG;

    // Delete session of user using $spsessionid.
    // The setting $CFG->session_handler_class may not be set. But if it is,
    // it should override $CFG->dbsessions.
    if (!empty($CFG->session_handler_class)) {
        $sessionclass = $CFG->session_handler_class;
        if (preg_match('/database/i', $sessionclass) === 1) {
            return \auth_shibboleth\helper::logout_db_session($spsessionid);
        } else if (preg_match('/file/i', $sessionclass) === 1) {
            return \auth_shibboleth\helper::logout_file_session($spsessionid);
        } else {
            throw new moodle_exception("Shibboleth logout not implemented for '$sessionclass'");
        }
    } else {
        // Session handler class is not specified, check dbsessions instead.
        if (!empty($CFG->dbsessions)) {
            return \auth_shibboleth\helper::logout_db_session($spsessionid);
        }
        // Assume file sessions if dbsessions isn't used.
        return \auth_shibboleth\helper::logout_file_session($spsessionid);
    }
    // If no SoapFault was thrown, the function will return OK as the SP assumes.
}
