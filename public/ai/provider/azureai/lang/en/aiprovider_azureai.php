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
 * Strings for component aiprovider_azureai, language 'en'.
 *
 * @package    aiprovider_azureai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:explain_text:apiversion'] = 'API version';
$string['action:explain_text:deployment'] = 'Deployment ID';
$string['action:explain_text:deployment_help'] = 'The deployment ID that relates to the API endpoint the provider uses for this action.';
$string['action:explain_text:systeminstruction'] = 'System instruction';
$string['action:explain_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:generate_image:apiversion'] = 'API version';
$string['action:generate_image:deployment'] = 'Deployment ID';
$string['action:generate_image:deployment_help'] = 'The deployment ID that relates to the API endpoint the provider uses for this action.';
$string['action:generate_text:apiversion'] = 'API version';
$string['action:generate_text:deployment'] = 'Deployment ID';
$string['action:generate_text:deployment_help'] = 'The deployment ID that relates to the API endpoint the provider uses for this action.';
$string['action:generate_text:systeminstruction'] = 'System instruction';
$string['action:generate_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:summarise_text:apiversion'] = 'API version';
$string['action:summarise_text:deployment'] = 'Deployment ID';
$string['action:summarise_text:deployment_help'] = 'The deployment ID that relates to the API endpoint the provider uses for this action.';
$string['action:summarise_text:systeminstruction'] = 'System instruction';
$string['action:summarise_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['apikey'] = 'Azure AI API key';
$string['apikey_help'] = 'Enter your Azure AI API key.';
$string['endpoint'] = 'Azure AI API endpoint';
$string['endpoint_help'] = 'Enter the endpoint URL for your Azure AI API in the following format: https://YOUR_RESOURCE_NAME.openai.azure.com';
$string['pluginname'] = 'Azure AI API provider';
$string['privacy:metadata'] = 'The Azure AI API provider plugin does not store any personal data.';
$string['privacy:metadata:aiprovider_azureai:externalpurpose'] = 'This information is sent to the Azure API in order for a response to be generated. Your Azure AI account settings may change how Microsoft stores and retains this data. No user data is explicitly sent to Microsoft or stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_azureai:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_azureai:numberimages'] = 'When generating images the number of images used in the response.';
$string['privacy:metadata:aiprovider_azureai:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['privacy:metadata:aiprovider_azureai:responseformat'] = 'The format of the response when generating images.';

// Deprecated since Moodle 5.0.
$string['action_apiversion'] = 'API version';
$string['action_deployment'] = 'Deployment ID';
$string['action_deployment_desc'] = 'The deployment ID that relates to the API endpoint the provider uses for this action.';
$string['action_systeminstruction'] = 'System instruction';
$string['action_systeminstruction_desc'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['apikey_desc'] = 'Enter your Azure AI API key.';
$string['deployment'] = 'Azure AI API deployment name';
$string['deployment_desc'] = 'Enter the deployment name for your Azure AI API.';
$string['enableglobalratelimit'] = 'Set site-wide rate limit';
$string['enableglobalratelimit_desc'] = 'Limit the number of requests that the Azure AI API provider can receive across the entire site every hour.';
$string['enableuserratelimit'] = 'Set user rate limit';
$string['enableuserratelimit_desc'] = 'Limit the number of requests each user can make to the Azure AI API provider every hour.';
$string['endpoint_desc'] = 'Enter the endpoint URL for your Azure AI API in the following format: https://YOUR_RESOURCE_NAME.openai.azure.com';
$string['globalratelimit'] = 'Maximum number of site-wide requests';
$string['globalratelimit_desc'] = 'The number of site-wide requests allowed per hour.';
$string['userratelimit'] = 'Maximum number of requests per user';
$string['userratelimit_desc'] = 'The number of requests allowed per hour, per user.';
