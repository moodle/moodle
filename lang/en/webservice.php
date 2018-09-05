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
 * Strings for component 'webservice', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   core_webservice
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accessexception'] = 'Access control exception';
$string['actwebserviceshhdr'] = 'Active web service protocols';
$string['addaservice'] = 'Add service';
$string['addcapabilitytousers'] = 'Check users capability';
$string['addcapabilitytousersdescription'] = 'Users should have two capabilities - webservice:createtoken and a capability matching the protocols used, for example webservice/rest:use, webservice/soap:use. To achieve this, create a web services role with the appropriate capabilities allowed and assign it to the web services user as a system role.';
$string['addfunction'] = 'Add function';
$string['addfunctionhelp'] = 'Select the function to add to the service.';
$string['addfunctions'] = 'Add functions';
$string['addfunctionsdescription'] = 'Select required functions for the newly created service.';
$string['addrequiredcapability'] = 'Assign/unassign the required capability';
$string['addservice'] = 'Add a new service: {$a->name} (id: {$a->id})';
$string['addservicefunction'] = 'Add functions to the service "{$a}"';
$string['allusers'] = 'All users';
$string['apiexplorer'] = 'API explorer';
$string['apiexplorernotavalaible'] = 'API explorer not available yet.';
$string['arguments'] = 'Arguments';
$string['authmethod'] = 'Authentication method';
$string['callablefromajax'] = 'Callable from AJAX';
$string['cannotcreatetoken'] = 'No permission to create web service token for the service {$a}.';
$string['cannotgetcoursecontents'] = 'Cannot get course contents';
$string['configwebserviceplugins'] = 'For security reasons, only protocols that are in use should be enabled.';
$string['context'] = 'Context';
$string['createservicedescription'] = 'A service is a set of web service functions. You will allow the user to access to a new service. On the <strong>Add service</strong> page check \'Enable\' and \'Authorised users\' options. Select \'No required capability\'.';
$string['createserviceforusersdescription'] = 'A service is a set of web service functions. You will allow users to access to a new service. On the <strong>Add service</strong> page check \'Enable\' and uncheck \'Authorised users\' options. Select \'No required capability\'.';
$string['createtoken'] = 'Create token';
$string['createtokenforuser'] = 'Create a token for a user';
$string['createtokenforuserdescription'] = 'Create a token for the web services user.';
$string['createuser'] = 'Create a specific user';
$string['createuserdescription'] = 'A web services user is required to represent the system controlling Moodle.';
$string['criteriaerror'] = 'Missing permissions to search on a criterion.';
$string['default'] = 'Default to "{$a}"';
$string['deleteaservice'] = 'Delete service';
$string['deleteservice'] = 'Delete the service: {$a->name} (id: {$a->id})';
$string['deleteserviceconfirm'] = 'Deleting a service will also delete the tokens related to this service. Do you really want to delete external service "{$a}"?';
$string['deletetokenconfirm'] = 'Do you really want to delete this web service token for <strong>{$a->user}</strong> on the service <strong>{$a->service}</strong>?';
$string['disabledwarning'] = 'All web service protocols are disabled.  The "Enable web services" setting can be found in Advanced features.';
$string['doc'] = 'Documentation';
$string['docaccessrefused'] = 'You are not allowed to see the documentation for this token';
$string['documentation'] = 'web service documentation';
$string['downloadfiles'] = 'Can download files';
$string['downloadfiles_help'] = 'If enabled, any user can download files with their security keys. Of course they are restricted to the files they are allowed to download in the site.';
$string['editaservice'] = 'Edit service';
$string['editservice'] = 'Edit the service: {$a->name} (id: {$a->id})';
$string['enabled'] = 'Enabled';
$string['enabledocumentation'] = 'Enable developer documentation';
$string['enabledocumentationdescription'] = 'Detailed web services documentation is available for enabled protocols.';
$string['enableprotocols'] = 'Enable protocols';
$string['enableprotocolsdescription'] = 'At least one protocol should be enabled. For security reasons, only protocols that are to be used should be enabled.';
$string['enablews'] = 'Enable web services';
$string['enablewsdescription'] = 'Web services must be enabled in Advanced features.';
$string['entertoken'] = 'Enter a security key/token:';
$string['error'] = 'Error: {$a}';
$string['errorcatcontextnotvalid'] = 'You cannot execute functions in the category context (category id:{$a->catid}). The context error message was: {$a->message}';
$string['errorcodes'] = 'Error message';
$string['errorcoursecontextnotvalid'] = 'You cannot execute functions in the course context (course id:{$a->courseid}). The context error message was: {$a->message}';
$string['errorinvalidparam'] = 'The param "{$a}" is invalid.';
$string['errornotemptydefaultparamarray'] = 'The web service description parameter named \'{$a}\' is an single or multiple structure. The default can only be empty array. Check web service description.';
$string['erroroptionalparamarray'] = 'The web service description parameter named \'{$a}\' is an single or multiple structure. It can not be set as VALUE_OPTIONAL. Check web service description.';
$string['eventwebservicefunctioncalled'] = 'Web service function called';
$string['eventwebserviceloginfailed'] = 'Web service login failed';
$string['eventwebserviceservicecreated'] = 'Web service service created';
$string['eventwebserviceservicedeleted'] = 'Web service service deleted';
$string['eventwebserviceserviceupdated'] = 'Web service service updated';
$string['eventwebserviceserviceuseradded'] = 'Web service service user added';
$string['eventwebserviceserviceuserremoved'] = 'Web service service user removed';
$string['eventwebservicetokencreated'] = 'Web service token created';
$string['eventwebservicetokensent'] = 'Web service token sent';
$string['execute'] = 'Execute';
$string['executewarnign'] = 'WARNING: If you press execute your database will be modified and changes can not be reverted automatically!';
$string['externalservice'] = 'External service';
$string['externalservicefunctions'] = 'External service functions';
$string['externalservices'] = 'External services';
$string['externalserviceusers'] = 'External service users';
$string['failedtolog'] = 'Failed to log';
$string['filenameexist'] = 'File name already exists: {$a}';
$string['forbiddenwsuser'] = 'Can not create token for an unconfirmed, deleted, suspended or guest user.';
$string['function'] = 'Function';
$string['functions'] = 'Functions';
$string['generalstructure'] = 'General structure';
$string['checkusercapability'] = 'Check user capability';
$string['checkusercapabilitydescription'] = 'The user should have appropriate capabilities according to the protocols used, for example webservice/rest:use, webservice/soap:use. To achieve this, create a web services role with protocol capabilities allowed and assign it to the web services user as a system role.';
$string['information'] = 'Information';
$string['installserviceshortnameerror'] = 'Coding error: the service shortname "{$a}" should have contains numbers, letters  and _-.. only.';
$string['installexistingserviceshortnameerror'] = 'A web service with the shortname "{$a}" already exists. Can not install/update a different web service with this shortname.';
$string['invalidextparam'] = 'Invalid external api parameter: {$a}';
$string['invalidextresponse'] = 'Invalid external api response: {$a}';
$string['invalidiptoken'] = 'Invalid token - your IP is not supported';
$string['invalidtimedtoken'] = 'Invalid token - token expired';
$string['invalidtoken'] = 'Invalid token - token not found';
$string['iprestriction'] = 'IP restriction';
$string['iprestriction_help'] = 'The user will need to call the web service from the listed IPs (separated by commas).';
$string['key'] = 'Key';
$string['keyshelp'] = 'The keys are used to access your Moodle account from external applications.';
$string['loginrequired'] = 'Restricted to logged-in users';
$string['manageprotocols'] = 'Manage protocols';
$string['managetokens'] = 'Manage tokens';
$string['missingcaps'] = 'Missing capabilities';
$string['missingcaps_help'] = 'List of required capabilities for the service which the selected user does not have. Missing capabilities must be added to the user\'s role in order to use the service.';
$string['missingpassword'] = 'Missing password';
$string['missingrequiredcapability'] = 'The capability {$a} is required.';
$string['missingusername'] = 'Missing username';
$string['missingversionfile'] = 'Coding error: version.php file is missing for the component {$a}';
$string['nameexists'] = 'This name is already in use by another service';
$string['nocapabilitytouseparameter'] = 'The user does not have the required capability to use the parameter {$a}';
$string['nofunctions'] = 'This service has no functions.';
$string['norequiredcapability'] = 'No required capability';
$string['notoken'] = 'The token list is empty.';
$string['onesystemcontrolling'] = 'Allow an external system to control Moodle';
$string['onesystemcontrollingdescription'] = 'The following steps help you to set up the Moodle web services to allow an external system to interact with Moodle. This includes setting up a token (security key) authentication method.';
$string['onlyseecreatedtokens'] = 'Any token can be deleted, though you can only view tokens that you created.';
$string['operation'] = 'Operation';
$string['optional'] = 'Optional';
$string['passwordisexpired'] = 'Password is expired.';
$string['phpparam'] = 'XML-RPC (PHP structure)';
$string['phpresponse'] = 'XML-RPC (PHP structure)';
$string['postrestparam'] = 'PHP code for REST (POST request)';
$string['potusers'] = 'Not authorised users';
$string['potusersmatching'] = 'Not authorised users matching';
$string['print'] = 'Print all';
$string['privacy:metadata:serviceusers'] = 'A list of users who can use a certain external services';
$string['privacy:metadata:serviceusers:iprestriction'] = 'IP restricted to use the service';
$string['privacy:metadata:serviceusers:timecreated'] = 'The date at which the record was created';
$string['privacy:metadata:serviceusers:userid'] = 'The ID of the user';
$string['privacy:metadata:serviceusers:validuntil'] = 'The date at which the authorisation ends';
$string['privacy:metadata:tokens'] = 'A record of tokens for interacting with Moodle through web services or Mobile applications.';
$string['privacy:metadata:tokens:creatorid'] = 'The ID of the user who created the token';
$string['privacy:metadata:tokens:iprestriction'] = 'IP restricted to use this token';
$string['privacy:metadata:tokens:lastaccess'] = 'The date at which the token was last used';
$string['privacy:metadata:tokens:privatetoken'] = 'A more private token occasionally used to validate certain operations, such as SSO';
$string['privacy:metadata:tokens:timecreated'] = 'The date at which the token was created';
$string['privacy:metadata:tokens:token'] = 'The user\'s token';
$string['privacy:metadata:tokens:tokentype'] = 'The type of token';
$string['privacy:metadata:tokens:userid'] = 'The ID of the user whose token it is';
$string['privacy:metadata:tokens:validuntil'] = 'The date that the token is valid until';
$string['privacy:request:notexportedsecurity'] = 'Not exported for security reasons';
$string['protocol'] = 'Protocol';
$string['removefunction'] = 'Remove';
$string['removefunctionconfirm'] = 'Do you really want to remove function "{$a->function}" from service "{$a->service}"?';
$string['requireauthentication'] = 'This method requires authentication with xxx permission.';
$string['required'] = 'Required';
$string['requiredcapability'] = 'Required capability';
$string['requiredcapability_help'] = 'If set, only users with the required capability can access the service.';
$string['requiredcaps'] = 'Required capabilities';
$string['resettokenconfirm'] = 'Do you really want to reset this web service key for <strong>{$a->user}</strong> on the service <strong>{$a->service}</strong>?';
$string['resettokenconfirmsimple'] = 'Do you really want to reset this key? Any saved links containing the old key will not work anymore.';
$string['response'] = 'Response';
$string['restcode'] = 'REST';
$string['restexception'] = 'REST';
$string['restparam'] = 'REST (POST parameters)';
$string['restrictedusers'] = 'Authorised users only';
$string['restrictedusers_help'] = 'This setting determines whether all users with the permission to create a web services token can generate a token for this service via their security keys page or whether only authorised users can do so.';
$string['restoredaccountresetpassword'] = 'Restored account need to reset password before getting a token.';
$string['securitykey'] = 'Security key (token)';
$string['securitykeys'] = 'Security keys';
$string['selectauthorisedusers'] = 'Select authorised users';
$string['selectedcapability'] = 'Selected';
$string['selectedcapabilitydoesntexit'] = 'The currently set required capability ({$a}) doesn\'t exist any more. Please change it and save the changes.';
$string['selectservice'] = 'Select a service';
$string['selectspecificuser'] = 'Select a specific user';
$string['selectspecificuserdescription'] = 'Add the web services user as an authorised user.';
$string['service'] = 'Service';
$string['servicehelpexplanation'] = 'A service is a set of functions. A service can be accessed by all users or just specified users.';
$string['servicename'] = 'Service name';
$string['servicenotavailable'] = 'Web service is not available (it doesn\'t exist or might be disabled)';
$string['servicesbuiltin'] = 'Built-in services';
$string['servicescustom'] = 'Custom services';
$string['serviceusers'] = 'Authorised users';
$string['serviceusersettings'] = 'User settings';
$string['serviceusersmatching'] = 'Authorised users matching';
$string['serviceuserssettings'] = 'Change settings for the authorised users';
$string['shortnametaken'] = 'Short name is already used for another service ({$a})';
$string['simpleauthlog'] = 'Simple authentication login';
$string['step'] = 'Step';
$string['supplyinfo'] = 'More details';
$string['testauserwithtestclientdescription'] = 'Simulate external access to the service using the web service test client. Before doing so, log in as a user with the moodle/webservice:createtoken capability and obtain the security key (token) via the user\'s preferences page. You will use this token in the test client. In the test client, also choose an enabled protocol with the token authentication. <strong>WARNING: The functions that you test WILL BE EXECUTED for this user, so be careful what you choose to test!</strong>';
$string['testclient'] = 'Web service test client';
$string['testclientdescription'] = '* The web service test client <strong>executes</strong> the functions for <strong>REAL</strong>. Do not test functions that you don\'t know. <br/>* All existing web service functions are not yet implemented into the test client. <br/>* In order to check that a user cannot access some functions, you can test some functions that you didn\'t allow.<br/>* To see clearer error messages set the debugging to <strong>{$a->mode}</strong> into {$a->atag}.';
$string['testwithtestclient'] = 'Test the service';
$string['testwithtestclientdescription'] = 'Simulate external access to the service using the web service test client. Use an enabled protocol with token authentication. <strong>WARNING: The functions that you test WILL BE EXECUTED, so be careful what you choose to test!</strong>';
$string['token'] = 'Token';
$string['tokenauthlog'] = 'Token authentication';
$string['tokencreatedbyadmin'] = 'Can only be reset by administrator (*)';
$string['tokencreator'] = 'Creator';
$string['unknownoptionkey'] = 'Unknown option key ({$a})';
$string['unnamedstringparam'] = 'A string parameter is unnamed.';
$string['updateusersettings'] = 'Update';
$string['uploadfiles'] = 'Can upload files';
$string['uploadfiles_help'] = 'If enabled, any user can upload files with their security keys to their own private files area or a draft file area. Any user file quotas apply.';
$string['userasclients'] = 'Users as clients with token';
$string['userasclientsdescription'] = 'The following steps help you to set up the Moodle web service for users as clients. These steps also help to set up the recommended token (security keys) authentication method. In this use case, the user will generate their token from the security keys page via their preferences page.';
$string['usermissingcaps'] = 'Missing capabilities: {$a}';
$string['usernameorid'] = 'Username / User id';
$string['usernameorid_help'] = 'Enter a username or a user id.';
$string['usernameoridnousererror'] = 'No users were found with this username/user id.';
$string['usernameoridoccurenceerror'] = 'More than one user was found with this username. Please enter the user id.';
$string['usernotallowed'] = 'The user is not allowed for this service. First you need to allow this user on the {$a}\'s allowed users administration page.';
$string['userservices'] = 'User services: {$a}';
$string['usersettingssaved'] = 'User settings saved';
$string['validuntil'] = 'Valid until';
$string['validuntil_help'] = 'If set, the service will be inactivated after this date for this user.';
$string['webservice'] = 'Web service';
$string['webservices'] = 'Web services';
$string['webservicesoverview'] = 'Overview';
$string['webservicetokens'] = 'Web service tokens';
$string['wrongusernamepassword'] = 'Wrong username or password';
$string['wsaccessuserdeleted'] = 'Refused web service access for deleted username: {$a}';
$string['wsaccessuserexpired'] = 'Refused web service access for password expired username: {$a}';
$string['wsaccessusernologin'] = 'Refused web service access for nologin authentication username: {$a}';
$string['wsaccessusersuspended'] = 'Refused web service access for suspended username: {$a}';
$string['wsaccessuserunconfirmed'] = 'Refused web service access for unconfirmed username: {$a}';
$string['wsclientdoc'] = 'Moodle web service client documentation';
$string['wsdocapi'] = 'API Documentation';
$string['wsdocumentation'] = 'Web service documentation';
$string['wsdocumentationdisable'] = 'Web service documentation is disabled.';
$string['wsdocumentationintro'] = 'To create a client we advise you to read the {$a->doclink}';
$string['wsdocumentationlogin'] = 'or enter your web service username and password:';
$string['wspassword'] = 'Web service password';
$string['wsusername'] = 'Web service username';
