<?PHP
$string['xmlrpc-missing']               = 'You must have XML-RPC installed in your PHP build to be able to use this feature.';
$string['description']                  = 'Description';
$string['mnet']                         = 'Moodle Networking';
$string['net']                          = 'Networking';
$string['requiresopenssl']              = 'Networking requires the OpenSSL extension';
$string['yourhost']                     = 'Your Host';
$string['yourpeers']                    = 'Your Peers';
$string['settings']                     = 'Settings';
$string['hostsettings']                 = 'Host Settings';
$string['mnetpeers']                    = 'Peers';
$string['mnetservices']                 = 'Moodle network services';
$string['trustedhosts']                 = 'XML-RPC hosts';
$string['trustedhostsexplain']          = '<p>The trusted hosts mechanism allows specific machines to
                                           execute calls via XML-RPC to any part of the Moodle API. This
                                           is available for scripts to control Moodle behaviour and can be
                                           a very dangerous option to enable. If in doubt, keep it off.</p>
                                           <p>This is <strong>not</strong> needed for Moodle Networking.</p>
                                           <p>To enable it, enter a list of IP addresses or networks, 
                                           one on each line. 
                                           Some examples:</p>'.
                                          'Your local host:<br />'.
                                          '127.0.0.1<br />'.
                                          'Your local host (with a network block):<br />'.
                                          '127.0.0.1/32<br />'.
                                          'Only the host with IP address 192.168.0.7:<br />'.
                                          '192.168.0.7/32<br />'.
                                          'Any host with an IP address between 192.168.0.1 and 192.168.0.255:<br />'.
                                          '192.168.0.0/24<br />'.
                                          'Any host whatsoever:<br />'.
                                          '192.168.0.0/0<br />'.
                                          'Obviously the last example is <strong>not</strong> a recommended configuration.';
$string['otherenrolledusers']           = 'Other enrolled users';
$string['hideremote']                   = 'Hide remote users';
$string['showremote']                   = 'Show remote users';
$string['hidelocal']                    = 'Hide local users';
$string['showlocal']                    = 'Show local users';
$string['hostcoursenotfound']           = 'Host or course not found';
$string['enrollingincourse']            = 'Enrolling in course $a[0] on host $a[1]<br />';

$string['duplicate_usernames']          = 'We failed to create an index on the columns \"mnethostid\" and \"username\" in your user table.<br />'.
                                          'This can occur when you have <a href=\"$a\" target=\"_blank\">duplicate usernames in your user table</a>.<br />'.
                                          'Your upgrade should still complete successfully. Click on the link above, and instructions on fixing'.
                                          ' this problem will appear in a new window. You can attend to that at the end of the upgrade.<br />';

$string['nomodifyacl']                  = 'You are not permitted to modify the MNET access control list.';
$string['recordnoexists']               = 'Record does not exist.';
$string['enterausername']               = 'Please enter a username, or a list of usernames separated by commas.';
$string['selectahost']                  = 'Please select a remote Moodle host.';
$string['selectaccesslevel']            = 'Please select an access level from the list.';
$string['noaclentries']                 = 'No entries in the SSO access control list';
$string['deleteaserver']                = 'Deleting a Server';
$string['nosite']                       = 'Could not find site-level course';
$string['postrequired']                 = 'The delete function requires a POST request.';
$string['hostdeleted']                  = 'Ok - host deleted';
$string['reenableserver']               = 'No - select this option to re-enable this server.';
$string['nocurl']                       = 'PHP Curl library is not installed';
$string['publish']                      = 'Publish';
$string['subscribe']                    = 'Subscribe';
$string['failedaclwrite']               = 'Failed to write to the MNET access control list for user \'$a\'.';

$string['receivedwarnings']             = 'The following warnings were received';
$string['reallydeleteserver']           = 'Are you sure you want to delete the server';

$string['deleteuserrecord']             = 'SSO ACL: delete record for user \'$a[0]\' from $a[1].';
$string['invalidaccessparam']           = 'Invalid access parameter.';
$string['invalidactionparam']           = 'Invalid action parameter.';
$string['currentkey']                   = 'Current Public Key';
$string['keymismatch']                  = 'The public key you are holding for this host is different from the public key it is currently publishing.';
$string['invalidurl']                   = 'Invalid URL parameter.';
$string['expireyourkey']                = 'Delete This Key';
$string['deletekeycheck']               = 'Are you absolutely sure you want to delete this key?';
$string['expireyourkeyexplain']         = 'Moodle automatically rotates your keys every 28 days (by default) but you have the option to '.
                                          '<em>manually</em> expire this key at any time. This will only be useful if you believe this '.
                                          'key has been compromised. A replacement will be immediately automatically generated.<br />'.
                                          'Deleting this key will make it impossible for other Moodles to communicate with you, until you '.
                                          'manually contact each administrator and provide them with your new key.';
$string['deleteoutoftime']              = 'Your 60-second window for deleting this key has expired. Please start again.'; 
$string['deletewrongkeyvalue']          = 'An error has occurred. If you were not trying to delete your server\'s SSL key, it is possible '.
                                          'you have been the subject of a malicious attack. No action has been taken.';

$string['keydeleted']                   = 'Your key has been successfully deleted and replaced.';

$string['is_in_range']                  = 'The IP address &nbsp;<code>$a</code>&nbsp; represents a valid trusted host.';
$string['validated_by']                 = 'It is validated by the network: &nbsp;<code>$a</code>';
$string['not_in_range']                 = 'The IP address &nbsp;<code>$a</code>&nbsp; does not represent a valid trusted host.';

$string['testtrustedhosts']             = 'Test an address';
$string['testtrustedhostsexplain']      = 'Enter an IP address to see if it is a trusted host.';

$string['forbidden-function']           = 'That function has not been enabled for RPC.';
$string['forbidden-transport']          = 'The transport method you are trying to use is not permitted.';

$string['registerallhosts']             = 'Register all hosts (<em>Hub mode</em>)';
$string['registerallhostsexplain']      = 'You can choose to register all hosts that try to connect to you automatically. 
                                           This means that a record will appear in your hosts list for any '.
                                          'Moodle site that connects to you and requests your public key.<br />'.
                                          'You have the option below to configure services for \'All Hosts\' and by enabling some services there, you are able to provide '.
                                          'services to any Moodle server indiscriminately.';

$string['mnet_session_prohibited']      = 'Users from your home server are not currently permitted to roam to $a.';
$string['ssl_acl_allow']                = 'SSO ACL: Allow user $a[0] from $a[1]';
$string['ssl_acl_deny']                = 'SSO ACL: Deny user $a[0] from $a[1]';
$string['enabled_for_all']              = '(This service has been enabled for all hosts).';
$string['nosuchfile']                   = 'The file/function $a does not exist.';
$string['nosuchfunction']               = 'Unable to locate function, or function prohibited for RPC.';
$string['nosuchmodule']                 = 'The function was incorrectly addressed and could not be located. Please use the 
mod/modulename/lib/functionname format.';
$string['nosuchpublickey']              = 'Unable to obtain public key for signature verification.';
$string['nosuchservice']                = 'The RPC service is not running on this host.';
$string['nosuchtransport']              = 'No transport with that ID exists.';

$string['phperror']                     = 'An internal PHP error prevented your request being fulfilled.';
$string['wrong-ip']                     = 'Your IP address does not match the address we have on record.';

$string['verifysignature-error']        = 'The signature verification failed. An error has occurred.';
$string['verifysignature-invalid']      = 'The signature verification failed. It appears that this payload was not signed by you.';
$string['mnetsettings']                 = 'Moodle network settings';
$string['mnetservices']                 = 'Services';
$string['mnetlog']                      = 'Logs';

$string['issubscribed']                 = 'The $a Moodle is subscribing to this service on your host.';
$string['ispublished']                  = 'The $a Moodle has enabled this service for you.';
$string['version']                      = 'version';
$string['id']                           = 'ID';
$string['hostname']                     = 'Hostname';
$string['last_connect_time']            = 'Last connect time';
$string['RPC_HTTPS_VERIFIED']           = 'HTTPS (signed)';
$string['RPC_HTTPS_SELF_SIGNED']        = 'HTTPS (self-signed)';
$string['RPC_HTTP_VERIFIED']            = 'HTTP (signed)';
$string['RPC_HTTP_SELF_SIGNED']         = 'HTTP (self-signed)';
$string['RPC_HTTP_PLAINTEXT']           = 'HTTP unencrypted';
$string['remotehosts']                  = 'Remote hosts';
$string['remotemoodles']                = 'Remote Moodles';
$string['remotecourses']                = 'Remote Courses';
$string['courseson']                    = ' courses on ';
$string['permittedtransports']          = 'Permitted transports';
$string['current_transport']            = 'Current transport';
$string['system']                       = 'System';
$string['on']                           = 'On';
$string['off']                          = 'Off';
$string['strict']                       = 'Strict';
$string['promiscuous']                  = 'Promiscuous';
$string['aboutyourhost']                = 'About your server';
$string['invalidhost']                  = 'You must provide a valid host identifier';

$string['moodleloc']                    = 'Moodle location';
$string['addnewhost']                   = 'Add a new host';
$string['addhost']                      = 'Add host';

$string['never']                        = 'Never';
$string['restore']                      = 'Restore';
$string['warning']                      = 'Warning';
$string['illegalchar-host']             = 'Your hostname contains the illegal character: $a';
$string['usersareonline']               = 'Warning: $a users from that server are currently logged on to your site.';
$string['illegalchar-moodlehome']       = 'Your Moodle location contains illegal characters';

$string['nonmatchingcert']              = 'The subject of the certificate: <br /><em>$a[0]</em><br />does not match the host it came from:<br /><em>$a[1]</em>.';
$string['noipmatch']                    = 'The remote machine\'s address: <br /><em>$a[0]</em><br />does not match the one on record:<br /><em>$a[1]</em>.';
$string['reviewhostdetails']            = 'Review Host Details';
$string['reviewhostservices']           = 'Review Host Services';
$string['moodle_home_help']             = 'The path to the homepage of Moodle on the remote host, e.g. /moodle/.';
$string['hostnamehelp']                 = 'The fully-qualified domain name of the remote host, e.g. www.example.com';
$string['idhelp']                       = 'This value is automatically assigned and cannot be changed';
$string['invalidpubkey']                = 'The key is not a valid SSL key.';
$string['nopubkey']                     = 'There was a problem retrieving the public key.<br />Maybe the host does not allow Moodle Networking or the key is invalid.';
$string['last_connect_time_help']       = 'The time that you last connected to this host.';
$string['last_transport_help']          = 'The transport that you used for the last connection to this host.';
$string['transport_help']               = 'These options are reciprocal, so you can only force a remote host to use a signed SSL cert if your server also has a signed SSL cert.';
$string['https_verified_help']          = 'Permit connections using a verified SSL Certificate on the remote host.';

$string['http_self_signed_help']        = 'Permit connections using a self-signed DIY SSL Certificate on the remote host.';
$string['http_verified_help']           = 'Permit connections using a verified SSL Certificate in PHP on the remote host, but over http (not https).';
$string['https_self_signed_help']       = 'Permit connections using a self-signed DIY SSL in PHP on the remote host over http.';
$string['hostexists']                   = 'A record already exists for that host and Moodle deployment with ID $a.<br />Click on <em>Continue</em> to edit that record.';
$string['publickey']                    = 'Public key';
$string['expires']                      = 'Valid until';
$string['expired']                      = 'This key expired on';
$string['publickey_help']               = 'The public key is automatically obtained from the remote server.';
$string['couldnotgetcert']              = 'No certificate found at <br />$a. <br />The host may be down or incorrectly configured.';
$string['ipaddress']                    = 'IP address';
$string['badcert']                      = 'This is not a valid certificate.';
$string['couldnotmatchcert']            = 'This does not match the certificate currently published by the webserver.';
$string['forcesavechanges']             = 'Force Save Changes';

$string['serviceswepublish']            = 'Services we publish to $a.';
$string['serviceswesubscribeto']        = 'Services on $a that we subscribe to.';
$string['nohostid']                     = 'This page requires a Host ID, which should be an integer.';

$string['networksettings']              = 'Network settings';
$string['helpnetworksettings']          = 'Configure inter-Moodle communication';
$string['mnet_concatenate_strings']     = 'Concatenate (up to) 3 strings and return the result';
$string['notPEM']                       = 'This key is not in PEM format. It will not work.';
$string['notBASE64']                    = 'This string is not in Base64 Encoded format. It cannot be a valid key.';

$string['usercannotchangepassword'] = 'You cannot change your password here since you are a remote user.';
$string['userchangepasswordlink'] = '<br /> You may be able to change your password at your <a href=\"$a->wwwroot/login/change_password.php\">$a->description</a> provider.';

$string['remotehost'] = 'Remote Hub';
$string['allow'] = 'Allow';
$string['deny'] = 'Deny';
$string['addtoacl'] = 'Add to Access Control';
$string['accesslevel'] = 'Access Level';
$string['ssoaccesscontrol'] = 'SSO Access Control';
$string['notpermittedtojump'] = 'You do not have permission to begin a remote session from this Moodle hub.';
$string['notpermittedtoland'] = 'You do not have permission to begin a remote session.';
$string['authfail_nosessionexists'] = 'Authorisation failed: the mnet session does not exist.';
$string['authfail_sessiontimedout'] = 'Authorisation failed: the mnet session has timed out.';
$string['authfail_usermismatch'] = 'Authorisation failed: the user does not match.';
$string['hostnotconfiguredforsso'] = 'This remote Moodle Hub is not configured for remote login.';
$string['authmnetdisabled'] = 'Moodle Networking authentication is disabled.';
$string['unknownerror'] = 'Unknown error occurred during negotiation.';
$string['nolocaluser'] = 'No local record exists for remote user.';
$string['databaseerror'] = 'Could not write details to the database.';
$string['ssoacldescr'] = 'Use this page to grant/deny access to specific users from remote Moodle Network hosts. This is functional when you are offering SSO services to remote users. To control your <em>local</em> users\' ability to roam to other Moodle Network hosts, use the roles system to grant them the <em>mnetcanroam</em> capability.';
$string['ssoaclneeds'] = 'For this functionality to work, you must have Moodle Networking On, plus the Moodle Network authentication plugin enabled with auto-add users enabled .';
$string['mnetdisabled'] = 'Moodle Network is <strong>disabled</strong>.';
$string['authmnetdisabled'] = 'Moodle Networking <em>Authentication plugin</em>is <strong>disabled</strong>.';
$string['authmnetautoadddisabled'] = '<em>Auto-add users</em> in Moodle Networking Authentication plugin is <strong>disabled</strong>.';
$string['mnetenrol'] = 'Enrolments';
$string['remoteenrolhosts_desc'] = 'Enrol and unenrol users from your Moodle installation
                                    on Moodle Hosts that allow you to do so via the Moodle
                                    Network enrolment plugin.';
$string['host'] = 'host';
$string['enrolments'] = 'enrolments';
$string['editenrolments'] = 'enrol';
$string['logs'] = 'logs';
$string['courses'] = 'courses';

$string['enrolcourses_desc'] = 'Courses offered for remote enrolment by this host.';
$string['enrolcourseenrol_desc'] = 'Enrol/unenrol users from this course using Moodle Network enrolments. 
                                    Note that users may have been enrolled in this course via other enrolment
                                    methods if the remote hosts allows them. Such enrolments are listed under
                                    <em>Other enrolled users</em>';
$string['host'] = 'host';
$string['loginlinkmnetuser'] = '<br/>If you are a Moodle Network remote user and can <a href=\"$a\">confirm your email address here</a>, you can be redirected to your login page.<br />';

?>
