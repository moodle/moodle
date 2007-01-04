<?PHP
$string['description']                  = 'Description';
$string['mnet']                         = 'Moodle Networking';
$string['net']                          = 'Networking';
$string['yourhost']                     = 'Your Host';
$string['yourpeers']                    = 'Your Peers';
$string['settings']                     = 'Settings';
$string['hostsettings']                 = 'Host Settings';
$string['mnetpeers']                    = 'Moodle Network Peers';
$string['mnetservices']                 = 'Moodle Network Services';
$string['trustedhosts']                 = 'Trusted Hosts';
$string['trustedhostsexplain']          = 'Please enter a list of IP addresses or networks, one on each line. Some examples:<br>'.
                                          'Your local host:<br>'.
                                          '127.0.0.1<br>'.
                                          'Your local host (with a network block):<br>'.
                                          '127.0.0.1/32<br>'.
                                          'Only the host with IP address 192.168.0.7:<br>'.
                                          '192.168.0.7/32<br>'.
                                          'Any host with an IP address between 192.168.0.1 and 192.168.0.255:<br>'.
                                          '192.168.0.0/24<br>'.
                                          'Any host whatsoever:<br>'.
                                          '192.168.0.0/0<br>'.
                                          'Obviously the last example is not a recommended configuration.';

$string['is_in_range']                  = 'The IP address &nbsp;<code>$a</code>&nbsp; represents a valid trusted host.';
$string['validated_by']                 = 'It is validated by the network: &nbsp;<code>$a</code>';
$string['not_in_range']                 = 'The IP address &nbsp;<code>$a</code>&nbsp; does not represent a valid trusted host.';

$string['testtrustedhosts']             = 'Test an Address';
$string['testtrustedhostsexplain']      = 'Enter an IP address to see if it is a trusted host.';

$string['forbidden-function']           = 'That function has not been enabled for RPC.';
$string['forbidden-transport']          = 'The transport method you are trying to use is not permitted.';

$string['registerallhosts']             = 'Register all hosts';
$string['registerallhostsexplain']      = 'You can choose to register all hosts that try to connect to you. This means that a record will appear in your hosts list for any '.
                                          'Moodle site that connects to you and requests your public key.<br>'.
                                          'You have the option below to configure services for \'All Hosts\' and by enabling some services there, you are able to provide '.
                                          'services to any Moodle server indiscriminately.';

$string['enabled_for_all']              = '(This service has been enabled for all hosts).';
$string['nosuchfile']                   = 'The file/function $a does not exist.';
$string['nosuchfunction']               = 'Unable to locate function, or function prohibited for RPC.';
$string['nosuchmodule']                 = 'The function was incorrectly addressed and could not be located.\nPlease use the mod/modulename/lib/functionname format.';
$string['nosuchpublickey']              = 'Unable to obtain public key for signature verification.';
$string['nosuchservice']                = 'The RPC service is not running on this host.';
$string['nosuchtransport']              = 'No transport with that ID exists.';

$string['phperror']                     = 'An internal PHP error prevented your request being fulfilled.';
$string['wrong-ip']                     = 'Your IP address does not match the address we have on record.';

$string['verifysignature-error']        = 'The signature verification failed. An error has occurred.';
$string['verifysignature-invalid']      = 'The signature verification failed. It appears that this payload was not signed by you.';
$string['mnetsettings']                 = 'Moodle Network Settings';
$string['mnetservices']                 = 'Services';
$string['mnetlog']                      = 'Logs';

$string['issubscribed']                 = 'The $a Moodle is subscribing to this service on your host.';
$string['ispublished']                  = 'The $a Moodle has enabled this service for you.';
$string['version']                      = 'version';
$string['id']                           = 'ID';
$string['hostname']                     = 'Hostname';
$string['last_connect_time']            = 'Last Connect Time';
$string['RPC_HTTPS_VERIFIED']           = 'HTTPS (signed)';
$string['RPC_HTTPS_SELF_SIGNED']        = 'HTTPS (self-signed)';
$string['RPC_HTTP_VERIFIED']            = 'HTTP (signed)';
$string['RPC_HTTP_SELF_SIGNED']         = 'HTTP (self-signed)';
$string['RPC_HTTP_PLAINTEXT']           = 'HTTP unencrypted';
$string['remotehosts']                  = 'Remote Hosts';
$string['permittedtransports']          = 'Permitted Transports';
$string['current_transport']            = 'Current Transport';
$string['system']                       = 'System';
$string['on']                           = 'On';
$string['off']                          = 'Off';
$string['strict']                       = 'Strict';
$string['promiscuous']                  = 'Promiscuous';
$string['aboutyourhost']                = 'About Your Server';
$string['invalidhost']                  = 'You must provide a valid host identifier';

$string['moodleloc']                    = 'Moodle Location';
$string['addnewhost']                   = 'Add a new host';
$string['addhost']                      = 'Add Host';

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
$string['invalidpubkey']                = 'There was a problem retrieving the public key. Maybe the host does not allow Moodle Networking or the key is invalid.';
$string['last_connect_time_help']       = 'The time that you last connected to this host.';
$string['last_transport_help']          = 'The transport that you used for the last connection to this host.';
$string['transport_help']               = 'These options are reciprocal, so you can only force a remote host to use a signed SSL cert if your server also has a signed SSL cert.';
$string['https_verified_help']          = 'Permit connections using a verified SSL Certificate on the remote host.';

$string['http_self_signed_help']        = 'Permit connections using a self-signed DIY SSL Certificate on the remote host.';
$string['http_verified_help']           = 'Permit connections using a verified SSL Certificate in PHP on the remote host, but over http (not https).';
$string['https_self_signed_help']       = 'Permit connections using a self-signed DIY SSL in PHP on the remote host over http.';
$string['hostexists']                   = 'A record already exists for that host and Moodle deployment with ID $a.<br />Click on <em>Continue</em> to edit that record.';
$string['publickey']                    = 'Public key';
$string['publickey_help']               = 'The public key is automatically obtained from the remote server.';
$string['couldnotgetcert']              = 'No certificate found at <br />$a. <br />The host may be down or incorrectly configured.';
$string['ipaddress']                    = 'IP address';
$string['badcert']                      = 'This is not a valid certificate.';
$string['couldnotmatchcert']            = 'This does not match the certificate currently published by the webserver.';
$string['forcesavechanges']             = 'Force Save Changes';

$string['serviceswepublish']            = 'Services we publish to $a.';
$string['serviceswesubscribeto']        = 'Services on $a that we subscribe to.';
$string['nohostid']                     = 'This page requires a Host ID, which should be an integer.';

$string['networksettings']              = 'Network Settings';
$string['helpnetworksettings']          = 'Configure inter-Moodle communication';
$string['mnet_concatenate_strings']     = 'Concatenate (up to) 3 strings and return the result';
$string['notPEM']                       = 'This key is not in PEM format. It will not work.';
$string['notBASE64']                    = 'This string is not in Base64 Encoded format. It cannot be a valid key.';

$string['usercannotchangepassword'] = 'You cannot change your password here since you are a remote user.';
$string['userchangepasswordlink'] = '<br> You may be able to change your password at your <a href=\"$a->wwwroot/login/change_password.php\">$a->description</a> provider.';

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
?>
