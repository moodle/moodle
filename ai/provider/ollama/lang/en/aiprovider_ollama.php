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
 * Strings for component aiprovider_ollama, language 'en'.
 *
 * @package    aiprovider_ollama
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:generate_text:endpoint'] = 'API endpoint';
$string['action:generate_text:endpoint_desc'] = 'The API endpoint for the provider uses for this action.';
$string['action:generate_text:model'] = 'Text generation model';
$string['action:generate_text:model_desc'] = 'The model used to generate the text response.';
$string['action:generate_text:systeminstruction'] = 'System instruction';
$string['action:generate_text:systeminstruction_desc'] = 'This instruction is provided together with the user prompt for this action. It provides information to the AI model on how to generate the response.';
$string['action:summarise_text:model'] = 'Text summarisation model';
$string['action:summarise_text:model_desc'] = 'The model used to summarise the provided text.';
$string['action:summarise_text:systeminstruction'] = 'System instruction';
$string['action:summarise_text:systeminstruction_desc'] = 'This instruction is provided together with the user prompt for this action. It provides information to the AI model on how to generate the response.';
$string['enablebasicauth'] = 'Enable basic authentication';
$string['enablebasicauth_desc'] = 'Enable basic authentication for the Ollama API provider.';
$string['enableglobalratelimit'] = 'Enable global rate limiting';
$string['enableglobalratelimit_desc'] = 'Enable global rate limiting for the Ollama API provider.';
$string['enableuserratelimit'] = 'Enable user rate limiting';
$string['enableuserratelimit_desc'] = 'Enable user rate limiting for the Ollama API provider.';
$string['endpoint'] = 'API endpoint';
$string['endpoint_desc'] = 'The API endpoint for the Ollama API server.';
$string['globalratelimit'] = 'Global rate limit';
$string['globalratelimit_desc'] = 'Set the number of requests per hour allowed for the global rate limit.';
$string['password'] = 'Password';
$string['password_desc'] = 'The password used for basic authentication.';
$string['pluginname'] = 'Ollama API Provider';
$string['privacy:metadata'] = 'The Ollama API provider plugin does not store any personal data.';
$string['privacy:metadata:aiprovider_ollama:externalpurpose'] = 'This information is sent to the Ollama API in order for a response to be generated. Your Ollama account settings may change how Ollama stores and retains this data. No user data is explicitly sent to Ollama or stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_ollama:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_ollama:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['username'] = 'Username';
$string['username_desc'] = 'The username used for basic authentication.';
$string['userratelimit'] = 'User rate limit';
$string['userratelimit_desc'] = 'Set the number of requests per hour allowed for the user rate limit.';
