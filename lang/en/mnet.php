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
 * Strings for component 'mnet', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   core_mnet
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['aboutyourhost'] = 'About your server';
$string['accesslevel'] = 'Access level';
$string['addhost'] = 'Add host';
$string['addnewhost'] = 'Add a new host';
$string['addtoacl'] = 'Add to access control';
$string['allhosts'] = 'All hosts';
$string['allhosts_no_options'] = 'No options are available when viewing multiple hosts';
$string['allow'] = 'Allow';
$string['applicationtype'] = 'Application type';
$string['authfail_nosessionexists'] = 'Authorisation failed: the mnet session does not exist.';
$string['authfail_sessiontimedout'] = 'Authorisation failed: the mnet session has timed out.';
$string['authfail_usermismatch'] = 'Authorisation failed: the user does not match.';
$string['authmnetdisabled'] = 'MNet authentication plugin is <strong>disabled</strong>.';
$string['badcert'] = 'This is not a valid certificate.';
$string['certdetails'] = 'Cert details';
$string['configmnet'] = 'MNet allows communication of this server with other servers or services.';
$string['couldnotgetcert'] = 'No certificate found at <br />{$a}. <br />The host may be down or incorrectly configured.';
$string['couldnotmatchcert'] = 'This does not match the certificate currently published by the webserver.';
$string['courses'] = 'courses';
$string['courseson'] = 'courses on';
$string['currentkey'] = 'Current public key';
$string['current_transport'] = 'Current transport';
$string['databaseerror'] = 'Could not write details to the database.';
$string['deleteaserver'] = 'Deleting a server';
$string['deletehost'] = 'Delete host';
$string['deletedhostinfo'] = 'This host has been deleted. If you want to undelete it, switch the deleted status back to \'No\'.';
$string['deletedhosts'] = 'Deleted hosts: {$a}';
$string['deletekeycheck'] = 'Are you absolutely sure you want to delete this key?';
$string['deleteoutoftime'] = 'Your 60-second window for deleting this key has expired. Please start again.';
$string['deleteuserrecord'] = 'SSO ACL: delete record for user \'{$a->user}\' from {$a->host}.';
$string['deletewrongkeyvalue'] = 'An error has occurred. If you were not trying to delete your server\'s SSL key, it is possible you have been the subject of a malicious attack. No action has been taken.';
$string['deny'] = 'Deny';
$string['description'] = 'Description';
$string['duplicate_usernames'] = 'We failed to create an index on the columns "mnethostid" and "username" in your user table.<br />This can occur when you have <a href="{$a}" target="_blank">duplicate usernames in your user table</a>.<br />Your upgrade should still complete successfully. Click on the link above, and instructions on fixing this problem will appear in a new window. You can attend to that at the end of the upgrade.<br />';
$string['enabled_for_all'] = '(This service has been enabled for all hosts).';
$string['enterausername'] = 'Please enter a username, or a list of usernames separated by commas.';
$string['error7020'] = 'This error normally occurs if the remote site has created a record for you with the wrong wwwroot, for example, http://yoursite.com instead of http://www.yoursite.com. You should contact the administrator of the remote site with your wwwroot (as specified in config.php) asking her to update her record for your host.';
$string['error7022'] = 'The message you sent to the remote site was encrypted properly, but not signed. This is very unexpected; you should probably file a bug if this occurs (giving as much information as possible about the application versions in question etc).';
$string['error7023'] = 'The remote site has tried to decrypt your message with all the keys it has on record for your site. They have all failed. You might be able to fix this problem by manually re-keying with the remote site. This is unlikely to occur unless you\'ve been out of communication with the remote site for a few months.';
$string['error7024'] = 'You send an unencrypted message to the remote site, but the remote site doesn\'t accept unencrypted communication from your site. This is very unexpected; you should probably file a bug if this occurs (giving as much information as possible about the application versions in question, etc.';
$string['error7026'] = 'The key that your message was signed with differs from the key that the remote host has on file for your server. Further, the remote host attempted to fetch your current key and failed to do so. Please manually re-key with the remote host and try again.';
$string['error709'] = 'The remote site failed to obtain a SSL key from you.';
$string['eventaccesscontrolcreated'] = 'Access control created';
$string['eventaccesscontrolupdated'] = 'Access control updated';
$string['expired'] = 'This key expired on';
$string['expires'] = 'Valid until';
$string['expireyourkey'] = 'Delete this key';
$string['expireyourkeyexplain'] = 'Moodle automatically rotates your keys every 28 days (by default) but you have the option to <em>manually</em> expire this key at any time. This will only be useful if you believe this key has been compromised. A replacement will be immediately automatically generated.<br />Deleting this key will make it impossible for other applications to communicate with you, until you manually contact each administrator and provide them with your new key.';
$string['exportfields'] = 'Fields to export';
$string['failedaclwrite'] = 'Failed to write to the MNet access control list for user \'{$a}\'.';
$string['findlogin'] = 'Find login';
$string['forbidden-function'] = 'That function has not been enabled for RPC.';
$string['forbidden-transport'] = 'The transport method you are trying to use is not permitted.';
$string['forcesavechanges'] = 'Force save changes';
$string['helpnetworksettings'] = 'Configure MNet communication';
$string['hidelocal'] = 'Hide local users';
$string['hideremote'] = 'Hide remote users';
$string['host'] = 'host';
$string['hostcoursenotfound'] = 'Host or course not found';
$string['hostdeleted'] = 'Host deleted';
$string['hostexists'] = 'A record already exists for a host with that hostname (it may be deleted). <a href="{$a}">click here</a> to edit that record.';
$string['hostlist'] = 'List of networked hosts';
$string['hostname'] = 'Hostname';
$string['hostnamehelp'] = 'The fully-qualified domain name of the remote host, e.g. www.example.com';
$string['hostnotconfiguredforsso'] = 'This server is not configured for remote login.';
$string['hostsettings'] = 'Host settings';
$string['http_self_signed_help'] = 'Permit connections using a self-signed DIY SSL certificate on the remote host.';
$string['https_self_signed_help'] = 'Permit connections using a self-signed DIY SSL in PHP on the remote host over http.';
$string['https_verified_help'] = 'Permit connections using a verified SSL certificate on the remote host.';
$string['http_verified_help'] = 'Permit connections using a verified SSL certificate in PHP on the remote host, but over http (not https).';
$string['id'] = 'ID';
$string['idhelp'] = 'This value is automatically assigned and cannot be changed';
$string['importfields'] = 'Fields to import';
$string['inspect'] = 'Inspect';
$string['installnosuchfunction'] = 'Coding error! Something is trying to install a mnet xmlrpc function ({$a->method}) from a file ({$a->file}) and it can\'t be found!';
$string['installnosuchmethod'] = 'Coding error! Something is trying to install a mnet xmlrpc method ({$a->method}) on a class ({$a->class}) and it can\'t be found!';
$string['installreflectionclasserror'] = 'Coding error! MNet introspection failed for method \'{$a->method}\' in class \'{$a->class}\'.  The original error message, in case it helps, is: \'{$a->error}\'';
$string['installreflectionfunctionerror'] = 'Coding error! MNet introspection failed for function \'{$a->method}\' in file \'{$a->file}\'.  The original error message, in case it helps, is: \'{$a->error}\'';
$string['invalidaccessparam'] = 'Invalid access parameter.';
$string['invalidactionparam'] = 'Invalid action parameter.';
$string['invalidhost'] = 'You must provide a valid host identifier';
$string['invalidpubkey'] = 'The key is not a valid SSL key. ({$a})';
$string['invalidurl'] = 'Invalid URL parameter.';
$string['ipaddress'] = 'IP address';
$string['is_in_range'] = 'The IP address <code>{$a}</code> represents a valid trusted host.';
$string['ispublished'] = '{$a} has enabled this service for you.';
$string['issubscribed'] = '{$a} is subscribing to this service on your host.';
$string['keydeleted'] = 'Your key has been successfully deleted and replaced.';
$string['keymismatch'] = 'The public key you are holding for this host is different from the public key it is currently publishing. The currently published key is:';
$string['last_connect_time'] = 'Last connect time';
$string['last_connect_time_help'] = 'The time that you last connected to this host.';
$string['last_transport_help'] = 'The transport that you used for the last connection to this host.';
$string['leavedefault'] = 'Use the default settings instead';
$string['listservices'] = 'List services';
$string['loginlinkmnetuser'] = '<br />If you are MNet remote user and can <a href="{$a}">confirm your email address here</a>, you can be redirected to your login page.<br />';
$string['logs'] = 'logs';
$string['managemnetpeers'] = 'Manage peers';
$string['method'] = 'Method';
$string['methodhelp'] = 'Method help for {$a}';
$string['methodsavailableonhost'] = 'Methods available on {$a}';
$string['methodsavailableonhostinservice'] = 'Methods available for {$a->service} on {$a->host}';
$string['methodsignature'] = 'Method signature for {$a}';
$string['mnet'] = 'MNet';
$string['mnet_concatenate_strings'] = 'Concatenate (up to) 3 strings and return the result';
$string['mnetdisabled'] = 'MNet is <strong>disabled</strong>.';
$string['mnetidprovider'] = 'MNet ID provider';
$string['mnetidproviderdesc'] = 'You can use this facility to retrieve a link that you can log in at, if you can provide the correct email address to match the username you previously tried to log in with.';
$string['mnetidprovidermsg'] = 'You should be able to login at your {$a} provider.';
$string['mnetidprovidernotfound'] = 'Sorry, but no further information could be found.';
$string['mnetpeers'] = 'Peers';
$string['mnetservices'] = 'Services';
$string['mnet_session_prohibited'] = 'Users from your home server are not currently permitted to roam to {$a}.';
$string['mnetsettings'] = 'MNet settings';
$string['moodle_home_help'] = 'The path to the homepage of MNet application on the remote host, e.g. /moodle/.';
$string['name'] = 'Name';
$string['net'] = 'Networking';
$string['networksettings'] = 'Network settings';
$string['never'] = 'Never';
$string['noaclentries'] = 'No entries in the SSO access control list';
$string['noaddressforhost'] = 'Sorry, but that hostname ({$a}) could not be resolved!';
$string['nocurl'] = 'PHP cURL library is not installed';
$string['nolocaluser'] = 'No local record exists for remote user, and it could not be created, as this host will not auto create users.  Please contact your administrator!';
$string['nomodifyacl'] = 'You are not permitted to modify the MNet access control list.';
$string['nonmatchingcert'] = 'The subject of the certificate: <br /><em>{$a->subject}</em><br />does not match the host it came from:<br /><em>{$a->host}</em>.';
$string['nopubkey'] = 'There was a problem retrieving the public key.<br />Maybe the host does not allow MNet or the key is invalid.';
$string['nosite'] = 'Could not find site-level course';
$string['nosuchfile'] = 'The file/function {$a} does not exist.';
$string['nosuchfunction'] = 'Unable to locate function, or function prohibited for RPC.';
$string['nosuchmodule'] = 'The function was incorrectly addressed and could not be located. Please use the
mod/modulename/lib/functionname format.';
$string['nosuchpublickey'] = 'Unable to obtain public key for signature verification.';
$string['nosuchservice'] = 'The RPC service is not running on this host.';
$string['nosuchtransport'] = 'No transport with that ID exists.';
$string['notBASE64'] = 'This string is not in base64 encoded format. It cannot be a valid key.';
$string['notenoughidpinfo'] = 'Your identity provider is not giving us enough information to create or update your account locally. Sorry!';
$string['not_in_range'] = 'The IP address <code>{$a}</code> does not represent a valid trusted host.';
$string['notinxmlrpcserver'] = 'Attempt to access the MNet remote client, not during XMLRPC server execution';
$string['notmoodleapplication'] = 'WARNING: This is not a Moodle application, so some of the inspection methods may not work properly.';
$string['notPEM'] = 'This key is not in PEM format. It will not work.';
$string['notpermittedtojump'] = 'You do not have permission to begin a remote session from this Moodle server.';
$string['notpermittedtojumpas'] = 'You can\'t begin a remote session while you are logged in as another user.';
$string['notpermittedtoland'] = 'You do not have permission to begin a remote session.';
$string['off'] = 'Off';
$string['on'] = 'On';
$string['options'] = 'Options';
$string['peerprofilefielddesc'] = 'Here you can override the global settings for which profile fields to send and import when new users are created';
$string['permittedtransports'] = 'Permitted transports';
$string['phperror'] = 'An internal PHP error prevented your request being fulfilled.';
$string['position'] = 'Position';
$string['postrequired'] = 'The delete function requires a POST request.';
$string['profileexportfields'] = 'Fields to send';
$string['profilefielddesc'] = 'Here you can configure the list of profile fields that are sent and received over MNet when user accounts are created, or updated.  You can also override this for each MNet peer individually. Note that the following fields are always sent and are not optional: {$a}';
$string['profilefields'] = 'Profile fields';
$string['profileimportfields'] = 'Fields to import';
$string['promiscuous'] = 'Promiscuous';
$string['publickey'] = 'Public key';
$string['publickey_help'] = 'The public key is automatically obtained from the remote server.';
$string['publickeyrequired'] = 'You must provide a public key.';
$string['publish'] = 'Publish';
$string['reallydeleteserver'] = 'Are you sure you want to delete the server';
$string['receivedwarnings'] = 'The following warnings were received';
$string['recordnoexists'] = 'Record does not exist.';
$string['reenableserver'] = 'No - select this option to re-enable this server.';
$string['registerallhosts'] = 'Register all hosts (promiscuous mode)';
$string['registerallhostsexplain'] = 'You can choose to register all hosts that try to connect to you automatically. This means that a record will appear in your hosts list for any MNet site that connects to you and requests your public key.<br />You have the option below to configure services for \'All hosts\' and by enabling some services there, you are able to provide services to any remote server indiscriminately.';
$string['registerhostsoff'] = 'Register all hosts is currently <b>off</b>';
$string['registerhostson'] = 'Register all hosts is currently <b>on</b>';
$string['remotecourses'] = 'Remote courses';
$string['remotehost'] = 'Remote host';
$string['remotehosts'] = 'Remote hosts';
$string['remoteuser'] = 'Remote {$a->remotetype} user';
$string['remoteuserinfo'] = 'Profile fetched from <a href="{$a->remoteurl}">{$a->remotename}</a>';
$string['requiresopenssl'] = 'Networking requires the OpenSSL extension';
$string['restore'] = 'Restore';
$string['returnvalue'] = 'Return value';
$string['reviewhostdetails'] = 'Review host details';
$string['reviewhostservices'] = 'Review host services';
$string['RPC_HTTP_PLAINTEXT'] = 'HTTP unencrypted';
$string['RPC_HTTP_SELF_SIGNED'] = 'HTTP (self-signed)';
$string['RPC_HTTPS_SELF_SIGNED'] = 'HTTPS (self-signed)';
$string['RPC_HTTPS_VERIFIED'] = 'HTTPS (signed)';
$string['RPC_HTTP_VERIFIED'] = 'HTTP (signed)';
$string['selectaccesslevel'] = 'Please select an access level from the list.';
$string['selectahost'] = 'Please select a remote host.';
$string['service'] = 'Service name';
$string['serviceid'] = 'Service ID';
$string['servicesavailableonhost'] = 'Services available on {$a}';
$string['serviceswepublish'] = 'Services we publish to {$a}.';
$string['serviceswesubscribeto'] = 'Services on {$a} that we subscribe to.';
$string['settings'] = 'Settings';
$string['showlocal'] = 'Show local users';
$string['showremote'] = 'Show remote users';
$string['ssl_acl_allow'] = 'SSO ACL: Allow user \'{$a->user}\' from \'{$a->host}\'';
$string['ssl_acl_deny'] = 'SSO ACL: Deny user \'{$a->user}\' from \'{$a->host}\'';
$string['sslverification'] = 'SSL verification';
$string['sslverification_help'] = 'This option allows you to configure the level of security when connecting to a peer using HTTPS.

* None: no level of security
* Verify host only: validates the domain of the SSL certificate
* Verify host and peer (recommended): validates the domain and issuer of the SSL certificate';
$string['ssoaccesscontrol'] = 'SSO access control';
$string['ssoacldescr'] = 'Use this page to grant/deny access to specific users from remote MNet hosts. This is functional when you are offering SSO services to remote users. To control your <em>local</em> users\' ability to roam to other MNet hosts, use the roles system to grant them the <em>mnetlogintoremote</em> capability.';
$string['ssoaclneeds'] = 'For this functionality to work, you must have Networking on, plus the MNet authentication plugin enabled.';
$string['strict'] = 'Strict';
$string['subscribe'] = 'Subscribe';
$string['system'] = 'System';
$string['testclient'] = 'MNet test client';
$string['testtrustedhosts'] = 'Test an address';
$string['testtrustedhostsexplain'] = 'Enter an IP address to see if it is a trusted host.';
$string['theypublish'] = 'They publish';
$string['theysubscribe'] = 'They subscribe';
$string['transport_help'] = 'These options are reciprocal, so you can only force a remote host to use a signed SSL cert if your server also has a signed SSL cert.';
$string['trustedhosts'] = 'XML-RPC hosts';
$string['trustedhostsexplain'] = '<p>The trusted hosts mechanism allows specific machines to execute calls via XML-RPC to any part of the Moodle API. This is available for scripts to control Moodle behaviour and can be a very dangerous option to enable. If in doubt, keep it off.</p>
<p><strong>This is not needed for any standard MNet feature!</strong> Turn it on only if you know what you are doing.</p>
<p>To enable it, enter a list of IP addresses or networks,
one on each line. Some examples:</p>
Your local host:<br />127.0.0.1<br />Your local host (with a network block):<br />127.0.0.1/32<br />Only the host with IP address 192.168.0.7:<br />192.168.0.7/32<br />Any host with an IP address between 192.168.0.1 and 192.168.0.255:<br />192.168.0.0/24<br />Any host whatsoever:<br />192.168.0.0/0<br />Obviously the last example is <strong>not</strong> a recommended configuration.';
$string['turnitoff'] = 'Turn it off';
$string['turniton'] = 'Turn it on';
$string['type'] = 'Type';
$string['unknown'] = 'Unknown';
$string['unknownerror'] = 'Unknown error occurred during negotiation.';
$string['usercannotchangepassword'] = 'You cannot change your password here since you are a remote user.';
$string['userchangepasswordlink'] = '<br /> You may be able to change your password at your <a href="{$a->wwwroot}/login/change_password.php">{$a->description}</a> provider.';
$string['usernotfullysetup'] = 'Your user account is incomplete.  You need to go <a href="{$a}">back to your provider</a> and ensure your profile is completed there.  You may need to log out and in again for this to take effect.';
$string['usersareonline'] = 'Warning: {$a} users from that server are currently logged on to your site.';
$string['validated_by'] = 'It is validated by the network: <code>{$a}</code>';
$string['verifyhostandpeer'] = 'Verify host and peer';
$string['verifyhostonly'] = 'Verify host only';
$string['verifysignature-error'] = 'The signature verification failed. An error has occurred.';
$string['verifysignature-invalid'] = 'The signature verification failed. It appears that this payload was not signed by you.';
$string['version'] = 'Version';
$string['warning'] = 'Warning';
$string['wrong-ip'] = 'Your IP address does not match the address we have on record.';
$string['xmlrpc-missing'] = 'You must have XML-RPC installed in your PHP build to be able to use this feature.';
$string['yourhost'] = 'Your host';
$string['yourpeers'] = 'Your peers';
