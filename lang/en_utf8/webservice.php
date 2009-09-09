<?php // $Id$
$string['activated'] = 'Activated';
$string['activatedfunctions'] = 'Activated functions';
$string['amfdebug'] = 'AMF server debug mode';
$string['clicktoactivate'] = 'Click to activate';
$string['clicktodeactivate'] = 'Click to deactivate';
$string['component'] = 'Component';
$string['createservicelabel'] = 'Create a custom service';
$string['custom'] = 'Custom';
$string['debugdisplayon'] = '\"Display debug messages\" is set On. The XMLRPC server will not work. The other web service servers could also return some problems. <br/>Alert the Moodle administrator to set it Off.';
$string['enabled'] = 'Enabled';
$string['fail'] = 'FAIL';
$string['functionlist'] = 'list of web service functions';
$string['functionname'] = 'Function name';
$string['moodlepath'] = 'Moodle path';
$string['ok'] = 'OK';
$string['protocolenable'] = '$a[0] protocol enable';
$string['protocols'] = 'Protocols';
$string['save'] = 'Save';
$string['servicelist'] = 'Services';
$string['servicename'] = 'Service name';
$string['soapdocumentation'] = '<H2>SOAP Manual</H2>
        <b>1.</b> Call the method <b>get_token</b> on \"<i>http://remotemoodle/webservice/soap/server.php?wsdl</i>\"<br>
        The function parameter is an array: in PHP it would be array(\"username\" => \"wsuser\", \"password\" => \"wspassword\")<br>
        Return value is a token (integer)<br>
        <br>
        <b>2.</b> Then call a moodle web service method on \"<i>http://remotemoodle/webservice/soap/server.php?token=the_received_token&classpath=the_moodle_path&wsdl</i>\"<br>
        Every method has only one parameter which is an array.<br>
        <br>
        For example in PHP for this specific function:<br>
        Moodle path: <b>user</b><br>
        <b>tmp_delete_user</b>( string username , integer mnethostid )<br>
        You will call something like:<br>
        your_client->tmp_delete_user(array(\"username\" => \"username_to_delete\",\"mnethostid\" => 1))<br><br>
';
$string['systemsettings'] = 'Common settings';
$string['usersettings'] = 'Users with web service permissions';
$string['webservicesenable'] = 'Web services enable';
$string['wsdeletefunction'] = '<b>$a->functionname</b> function has been deleted from the <b>$a->servicename</b> service.';
$string['wsinsertfunction'] = '<b>$a->functionname</b> function has been inserted into the <b>$a->servicename</b> service.';
$string['wspagetitle'] = 'Web services documentation';
$string['wsuserreminder'] = 'Reminder: the Moodle administrator of this site needs to give you moodle/site:usewebservices capability.';
$string['xmlrpcdocumentation'] = '<H2>XMLRPC Manual</H2>
        <b>1.</b> Call the method <b>authentication.get_token</b> on \"<i>http://remotemoodle/webservice/xmlrpc/server.php</i>\"<br>
        The function parameter is an array: in PHP it would be array(\"username\" => \"wsuser\", \"password\" => \"wspassword\")<br>
        Return value is a token (integer)<br>
        <br>
        <b>2.</b> Then call a moodle web service method on \"<i>http://remotemoodle/webservice/xmlrpc/server.php?classpath=the_moodle_path&token=the_received_token</i>\"<br>
        Every method has only one parameter which is an array.<br>
        <br>
        For example in PHP for this specific function:<br>
        Moodle path: <b>user</b><br>
        <b>tmp_delete_user</b>( string username , integer mnethostid )<br>
        You will call something like:<br>
        your_client->call(\"user.tmp_delete_user\", array(array(\"username\" => \"username_to_delete\",\"mnethostid\" => 1)))<br>

';

//service names
$string['user'] = "User";

