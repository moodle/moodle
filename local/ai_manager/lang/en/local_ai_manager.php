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
 * Lang strings for local_ai_manager - EN.
 *
 * @package    local_ai_manager
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addinstance'] = 'Add AI tool';
$string['addnavigationentry'] = 'Add navigation entry';
$string['addnavigationentrydesc'] = 'Enable if the AI manager configuration should be accessible by the primary navigation';
$string['ai_info_table_row_highlighted'] = 'The highlighted AI tools are the ones which are being used by the plugin you were using when clicking the link to this page.';
$string['ai_manager:manage'] = 'Configure AI manager settings for a tenant';
$string['ai_manager:managetenants'] = 'Configure AI manager settings for all tenants';
$string['ai_manager:managevertexcache'] = 'Get and change the configuration of the Google Vertex AI caching status';
$string['ai_manager:use'] = 'Use ai_manager';
$string['ai_manager:viewstatistics'] = 'View statistics';
$string['ai_manager:viewusage'] = 'View usage information';
$string['ai_manager:viewusernames'] = 'View non anonymized usernames in statistics';
$string['ai_manager:viewuserstatistics'] = 'View statistics of single users';
$string['aiadministrationlink'] = 'AI tools administration';
$string['aiinfotitle'] = 'AI tools in your tenant';
$string['aiisbeingused'] = 'You are using an AI tool. The entered data will be sent to an external AI tool.';
$string['aitool'] = 'AI tool';
$string['aitooldeleted'] = 'AI tool deleted';
$string['aitoolsaved'] = 'AI tool data saved';
$string['aiwarning'] = 'AI generated content should always be validated.';
$string['aiwarningurl'] = 'Link for warning about AI generated content';
$string['aiwarningurldesc'] = 'You can specify an URL which contains additional information about the issue of AI generated content.';
$string['allowedtenants'] = 'Allowed tenants';
$string['allowedtenantsdesc'] = 'Specify a list of allowed tenants: One identifier per line.';
$string['anonymized'] = 'Anonymized';
$string['apikey'] = 'API key';
$string['applyfilter'] = 'Apply filter';
$string['assignpurposes'] = 'Assign purposes';
$string['assignrole'] = 'Assign role';
$string['basicsettings'] = 'Basic settings';
$string['basicsettingsdesc'] = 'Configure basic settings for the AI manager plugin';
$string['cachedef_googleauth'] = 'Cache for Google OAuth2 access token';
$string['configure_instance'] = 'Configure AI Tool Instances';
$string['configureaitool'] = 'Configure AI tool';
$string['configurepurposes'] = 'Configure the purposes';
$string['confirm'] = 'Confirm';
$string['confirmaitoolsusage_heading'] = 'Confirm AI usage';
$string['confirmed'] = 'Accepted';
$string['currentlyusedaitools'] = 'Currently configured AI tools';
$string['defaultrole'] = 'default role';
$string['defaulttenantname'] = 'Default tenant';
$string['empty_api_key'] = 'Empty API Key';
$string['enable_ai_integration'] = 'Enable AI integration';
$string['endpoint'] = 'API endpoint';
$string['error_http400'] = 'Error sanitizing passed options';
$string['error_http403blocked'] = 'Your ByCS admin has blocked access to the AI tools for you';
$string['error_http403disabled'] = 'Your ByCS admin has not enabled the AI tools feature';
$string['error_http403notconfirmed'] = 'You have not yet confirmed the terms of use';
$string['error_http403usertype'] = 'Your ByCS admin has disabled this purpose for your user type';
$string['error_http409'] = 'The itemid {$a} is already taken';
$string['error_http429'] = 'You have reached the maximum amount of requests. You are only allowed to send {$a->count} requests in a period of {$a->period}';
$string['error_limitreached'] = 'You have reached the maximum amount of requests for this purpose. Please wait until the counter has been reset.';
$string['error_noaitoolassignedforpurpose'] = 'There is no AI tool assigned for the purpose "{$a}"';
$string['error_pleaseconfirm'] = 'Please accept them before using.';
$string['error_purposenotconfigured'] = 'There is no AI tool configured for this purpose. Please contact your tenant manager.';
$string['error_sendingrequestfailed'] = 'Sending the request to the AI tool failed.';
$string['error_tenantdisabled'] = 'The AI tools are not enabled for your tenant. Please contact your tenant manager.';
$string['error_unavailable_noselection'] = 'This tool is only available if text has been selected.';
$string['error_unavailable_selection'] = 'This tool is only available if no text has been selected.';
$string['error_userlocked'] = 'Your user has been locked by your tenant manager.';
$string['error_usernotconfirmed'] = 'You have not accepted the terms of use yet.';
$string['error_vertexai_serviceaccountjsonempty'] = 'You need to paste the content of the JSON file that you downloaded when creating the service account in your Google Cloud Console.';
$string['error_vertexai_serviceaccountjsoninvalid'] = 'Invalid format. Has to be valid JSON.';
$string['error_vertexai_serviceaccountjsoninvalidmissing'] = 'Invalid format. The entry "{$a}" is missing.';
$string['exception_badmessageformat'] = 'Messages have been submitted in an invalid format';
$string['exception_changestatusnotallowed'] = 'You must not change the status of this user';
$string['exception_curl'] = 'A connection error to the external API endpoint has occurred';
$string['exception_curl28'] = 'The API took too long to process your request or could not be reached in a reasonable time';
$string['exception_default'] = 'A general error occurred while trying to send the request to the AI tool';
$string['exception_editinstancedenied'] = 'You must not edit this AI tool (instance).';
$string['exception_http401'] = 'Access to the API has been denied because of invalid credentials';
$string['exception_http429'] = 'There have been sent too many or too big requests to the AI tool in a certain amount of time. Please try again later.';
$string['exception_http500'] = 'An internal server error of the AI tool occurred';
$string['exception_instanceidmissing'] = 'You must specify the ID of AI tool (instance)';
$string['exception_instancenotexists'] = 'AI tool (instance) with ID {$a} does not exist';
$string['exception_invalidpurpose'] = 'Invalid purpose';
$string['exception_notenantmanagerrights'] = 'You do not have the rights to manage the AI tools';
$string['exception_novalidconnector'] = 'No valid connector specified';
$string['exception_retrievingaccesstoken'] = 'An error occured while trying to retrieve the access token';
$string['exception_retrievingcachestatus'] = 'An error occured while trying to retrieve the cache status';
$string['exception_tenantaccessdenied'] = 'You must not access this tenant ({$a}).';
$string['exception_tenantnotallowed'] = 'The tenant is not allowed by the administrator';
$string['exception_usernotexists'] = 'The user does not exist';
$string['female'] = 'Female';
$string['filterroles'] = 'Filter roles';
$string['formvalidation_editinstance_azureapiversion'] = 'You must provide the api version of your Azure Resource';
$string['formvalidation_editinstance_azuredeploymentid'] = 'You must provide the deployment id of your Azure Resource';
$string['formvalidation_editinstance_azureresourcename'] = 'You must provide the resource name of your Azure Resource';
$string['formvalidation_editinstance_endpointnossl'] = 'For security and data privacy reasons only HTTPS endpoints are allowed';
$string['formvalidation_editinstance_name'] = 'Please insert a name for the AI tool';
$string['formvalidation_editinstance_temperaturerange'] = 'Temperature value must be between 0 und 1';
$string['general_information_heading'] = 'General Information';
$string['general_information_text'] = 'As of now, your moodle instance does not provide any AI tools. However, the moodle instance offers interfaces through which AI tools can be used within the moodle instance. For this to be possible for all the users of your tenant, the tenant must acquire or provide such a tool. The tenant manager can then store the access data via a configuration page and thus enable the AI functions offered in the moodle instance.';
$string['general_user_settings'] = 'General user settings';
$string['get_ai_response_failed'] = 'Retrieving AI response failed';
$string['get_ai_response_failed_desc'] = 'While trying to get a result from the endpoint of an external AI tool an error occurred';
$string['get_ai_response_succeeded'] = 'Successfully received AI response';
$string['get_ai_response_succeeded_desc'] = 'The attempt to retrieve a response from an endpoint of an external AI tool was successful';
$string['heading_home'] = 'AI tools';
$string['heading_purposes'] = 'Purposes';
$string['heading_statistics'] = 'Statistics';
$string['infolink'] = 'Link for further information';
$string['instanceaddmodal_heading'] = 'Which AI tool do you want to add?';
$string['instancedeleteconfirm'] = 'Are you sure that you want to delete this AI tool?';
$string['instancename'] = 'Internal identifier';
$string['landscape'] = 'landscape';
$string['large'] = 'large';
$string['locked'] = 'Locked';
$string['lockuser'] = 'Lock user';
$string['male'] = 'Male';
$string['max_request_time_window'] = 'Time window for maximum number of requests';
$string['max_requests_purpose'] = 'Maximum number of requests per time window ({$a})';
$string['max_requests_purpose_heading'] = 'Purpose {$a}';
$string['medium'] = 'medium';
$string['model'] = 'Model';
$string['nodata'] = 'No data to show';
$string['notconfirmed'] = 'Not confirmed';
$string['notselected'] = 'Disabled';
$string['per'] = 'per';
$string['pluginname'] = 'AI Manager';
$string['portrait'] = 'portrait';
$string['preconfiguredmodel'] = 'Preconfigured model';
$string['privacy:metadata'] = 'The local ai_manager plugin does not store any personal data.';
$string['privacy_table_description'] = 'In the table below, you can see an overview of the AI tools configured by your school. Your ByCS admin may have provided additional notes on the terms of use and privacy notices of the respective AI tools in the "Info link" column.';
$string['privacy_terms_description'] = 'Following are the notes about data privacy and terms of use in the exact same form like you confirmed or still have to confirm to use the AI functionalities.';
$string['privacy_terms_heading'] = 'Privacy and Terms of Use';
$string['privacy_terms_missing'] = 'No terms of use have been specified.';
$string['privacy_terms_missing_enable_anyway'] = 'Please enable the following switch to be able to use the AI functionalities.';
$string['purpose'] = 'Purpose';
$string['purposesdescription'] = 'Which of your configured AI tools should be used for which purpose?';
$string['purposesheading'] = 'Purposes for {$a->role} ({$a->currentcount}/{$a->maxcount} assigned)';
$string['quotaconfig'] = 'Limits configuration';
$string['quotadescription'] = 'Set the time window and the maximum number of requests per student and teacher here. After the time window expires, the number of requests will automatically reset.';
$string['request_count'] = 'Request count';
$string['requesttimeout'] = 'Timeout for request to the AI endpoints';
$string['requesttimeoutdesc'] = 'Maximum amount of time in seconds for requests to the external AI endpoints';
$string['resetfilter'] = 'Reset filter';
$string['resetuserusagetask'] = 'Reset AI manager user usage data';
$string['restricttenants'] = 'Lock access for certain tenants';
$string['restricttenantsdesc'] = 'Enable to limit the AI tools to specific tenants which can be defined by the "allowedtenants" config option.';
$string['revokeconfirmation'] = 'Revoke confirmation';
$string['rightsconfig'] = 'Rights configuration';
$string['role'] = 'Role';
$string['role_basic'] = 'base role';
$string['role_extended'] = 'extended role';
$string['role_unlimited'] = 'unlimited role';
$string['select_tool_for_purpose'] = 'Purpose {$a}';
$string['selecteduserscount'] = '{$a} selected';
$string['serviceaccountjson'] = 'Content of the JSON file of the Google service account';
$string['small'] = 'small';
$string['squared'] = 'squared';
$string['statistics_for'] = 'Statistic for {$a}';
$string['statisticsoverview'] = 'Global overview';
$string['subplugintype_aipurpose'] = 'AI purpose';
$string['subplugintype_aipurpose_plural'] = 'AI purposes';
$string['subplugintype_aitool'] = 'AI tool';
$string['subplugintype_aitool_plural'] = 'AI tools';
$string['table_heading_infolink'] = 'Info link';
$string['table_heading_instance_name'] = 'AI tool name';
$string['table_heading_model'] = 'Model';
$string['table_heading_purpose'] = 'Purpose';
$string['technical_function_heading'] = 'Technical Functionality';
$string['technical_function_step1'] = 'The tenant manager stores a configuration for a specific purpose, for example, configuring the option for image generation, because his tenant has a contract with OpenAI, so the tenant can use the Dall-E tool.';
$string['technical_function_step2'] = 'A user of this tenant then finds the corresponding AI function in the moodle instance, for example, the ability to generate an image via a prompt directly in the editor and insert it into the editor.';
$string['technical_function_step3'] = 'If a user now uses this function, the prompt is sent to the servers of the moodle instance and evaluated by them.';
$string['technical_function_step4'] = 'The servers of the moodle instance use the stored access data for the AI tool of the tenant and send the request on behalf of the user to the servers of the external AI tool.';
$string['technical_function_step4_emphasized'] = 'In this process, the moodle instance acts as the end-user of the external tool, meaning that the external tool cannot trace which individual user made the corresponding request to the AI tool. Only the tenant to which the user belongs is identifiable for the AI tool.';
$string['technical_function_step5'] = 'The response from the AI tool is sent back to the user by the moodle instance or the result, such as a generated image, is directly integrated into the respective activity.';
$string['technical_function_text'] = 'When using the AI functions within this moodle instance, the technical process is as follows:';
$string['temperature_creative_balanced'] = 'Balanced';
$string['temperature_custom_value'] = 'Custom value (between 0 and 1)';
$string['temperature_defaultsetting'] = 'Temperature default';
$string['temperature_desc'] = 'This describes "randomness" or "creativity". Low temperature will generate more coherent but predictable text. High numbers means more creative but not accurate. The range is from 0 to 1.';
$string['temperature_more_creative'] = 'Rather creative';
$string['temperature_more_precise'] = 'Rather precise';
$string['temperature_use_custom_value'] = 'Use custom temperature value';
$string['tenant'] = 'Tenant';
$string['tenantcolumn'] = 'Tenant column';
$string['tenantcolumndesc'] = 'The column of the user table which contains the identifier of the tenant which a user should be associated with';
$string['tenantconfig_heading'] = 'AI at your tenant';
$string['tenantdisabled'] = 'disabled';
$string['tenantenabled'] = 'enabled';
$string['tenantenabledescription'] = 'For your tenant users to gain access to all AI features of the moodle instance you need to enable and configure the features here.';
$string['tenantenablednextsteps'] = 'The AI features of the moodle instance are now enabled for your tenant. Please note that you now have to define the tools and purposes for the features to be actually usable.<br/>All users will have access to the AI features. However, by going to {$a} you can disable users.';
$string['tenantenableheading'] = 'AI tools at your tenant';
$string['tenantnotallowed'] = 'The feature is globally disabled for your tenant and thus not usable.';
$string['termsofusesetting'] = 'Terms of use';
$string['termsofusesettingdesc'] = 'Here you can add your specific terms of use for the AI manager. These will have to be accepted by the user before he/she will be able to use the AI tools.';
$string['unit_count'] = 'request(s)';
$string['unit_token'] = 'token';
$string['unlockuser'] = 'Unlock user';
$string['usage'] = 'Usage';
$string['use_openai_by_azure_apiversion'] = 'API version of the Azure resource';
$string['use_openai_by_azure_deploymentid'] = 'Deployment ID of the Azure resource';
$string['use_openai_by_azure_heading'] = 'Use OpenAI via Azure';
$string['use_openai_by_azure_name'] = 'Name of the Azure resource';
$string['userconfig'] = 'User configuration';
$string['userconfirmation_headline'] = 'Confirmation for usage of AI tools';
$string['userstatistics'] = 'User overview';
$string['userstatusupdated'] = 'The user\'s/users\' status has been updated';
$string['userwithusageonlyshown'] = 'Only users who already have used this purpose are being shown in this table.';
$string['verifyssl'] = 'Verify SSL certificates';
$string['verifyssldesc'] = 'If enabled, connections to the AI tools will only be established if the certificates can properly be verified. Only recommended to disable for development use!';
$string['vertex_cachingdisabled'] = 'Caching disabled';
$string['vertex_cachingenabled'] = 'Caching enabled';
$string['vertex_disablecaching'] = 'Disable Caching';
$string['vertex_enablecaching'] = 'Enable Caching';
$string['vertex_error_cachestatus'] = 'Error while querying/updating the Vertex AI caching configuration';
$string['vertex_nocachestatus'] = 'Click the refresh button to query the current Vertex AI cache status.';
$string['vertexcachestatus'] = 'Query and change Vertex AI cache status';
$string['within'] = 'in';
