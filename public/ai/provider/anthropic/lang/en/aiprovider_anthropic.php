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
 * Strings for component aiprovider_anthropic, language 'en'.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:explain_text:endpoint'] = 'API endpoint';
$string['action:explain_text:model'] = 'AI model';
$string['action:explain_text:model_help'] = 'The model used to explain the provided text.';
$string['action:explain_text:systeminstruction'] = 'System instruction';
$string['action:explain_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:generate_text:endpoint'] = 'API endpoint';
$string['action:generate_text:model'] = 'AI model';
$string['action:generate_text:model_help'] = 'The model used to generate the text response.';
$string['action:generate_text:systeminstruction'] = 'System instruction';
$string['action:generate_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:summarise_text:endpoint'] = 'API endpoint';
$string['action:summarise_text:model'] = 'AI model';
$string['action:summarise_text:model_help'] = 'The model used to summarise the provided text.';
$string['action:summarise_text:systeminstruction'] = 'System instruction';
$string['action:summarise_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['apikey'] = 'Anthropic API key';
$string['apikey_help'] = 'Get a key from <a href="https://console.anthropic.com/settings/keys" target="_blank">Anthropic console API keys</a>.';
$string['custom_model_name'] = 'Custom model name';
$string['custom_model_name_help'] = 'The name of the Claude model to use, exactly as it appears in the Anthropic API, for example claude-sonnet-4-5-20250929. Use this to select a model that is not in the list, such as one released after this version of Moodle LMS. Newer Claude models no longer accept the temperature setting, so leave Temperature empty unless you know the model supports it.';
$string['error:nocontent'] = 'The Anthropic API returned no usable text content (stop reason: {$a}).';
$string['pluginname'] = 'Anthropic Claude API provider';
$string['privacy:metadata:aiprovider_anthropic:externalpurpose'] = 'This information is sent to the Anthropic Claude API in order for a response to be generated. Your Anthropic account settings may change how Anthropic stores and retains this data. No data is stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_anthropic:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_anthropic:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['privacy:metadata:aiprovider_anthropic:userid'] = 'A one-way hash of the site identifier and the user id, sent so that Anthropic can detect and mitigate abuse. It does not identify the user.';
$string['settings'] = 'Model settings';
$string['settings_max_tokens'] = 'Max tokens';
$string['settings_max_tokens_help'] = 'The maximum number of tokens to generate in the response. The Anthropic API requires this value to be set.';
$string['settings_temperature'] = 'Temperature';
$string['settings_temperature_help'] = 'Temperature controls how random the model\'s output is. Lower values (closer to 0) make the output more predictable and focused. Higher values (closer to 1) make the output more creative and varied.';
