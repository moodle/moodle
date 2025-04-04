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

$string['action:explain_text:model'] = 'Text explanation model';
$string['action:explain_text:model_help'] = 'The model used to explain the provided text.';
$string['action:explain_text:systeminstruction'] = 'System instruction';
$string['action:explain_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:generate_text:model'] = 'Text generation model';
$string['action:generate_text:model_help'] = 'The model used to generate the text response.';
$string['action:generate_text:systeminstruction'] = 'System instruction';
$string['action:generate_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:summarise_text:model'] = 'Text summarisation model';
$string['action:summarise_text:model_help'] = 'The model used to summarise the provided text.';
$string['action:summarise_text:systeminstruction'] = 'System instruction';
$string['action:summarise_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['custom_model_name'] = 'Custom model name';
$string['enablebasicauth'] = 'Enable basic authentication';
$string['enablebasicauth_help'] = 'Enable basic authentication for the Ollama API provider.';
$string['endpoint'] = 'API endpoint';
$string['endpoint_help'] = 'The API endpoint for the Ollama API server.';
$string['extraparams'] = 'Extra parameters';
$string['extraparams_help'] = 'Extra parameters can be configured here. We support JSON format. For example:
<pre>
{
    "temperature": 0.5,
    "max_tokens": 100
}
</pre>';
$string['invalidjson'] = 'Invalid JSON string';
$string['password'] = 'Password';
$string['password_help'] = 'The password used for basic authentication.';
$string['pluginname'] = 'Ollama API provider';
$string['privacy:metadata'] = 'The Ollama API provider plugin does not store any personal data.';
$string['privacy:metadata:aiprovider_ollama:externalpurpose'] = 'This information is sent to the Ollama API in order for a response to be generated. Your Ollama account settings may change how Ollama stores and retains this data. No user data is explicitly sent to Ollama or stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_ollama:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_ollama:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['settings'] = 'Settings';
$string['settings_help'] = 'Adjust the settings below to customise how requests are sent to Ollama.';
$string['settings_mirostat'] = 'Mirostat';
$string['settings_mirostat_help'] = 'Mirostat is a neural text decoding algorithm for controlling perplexity. 0 = disabled, 1 = Mirostat, 2 = Mirostat 2.0. (Default: 0)';
$string['settings_seed'] = 'seed';
$string['settings_seed_help'] = 'Sets the random number seed to use for generation. Setting this to a specific number will make the model generate the same text for the same prompt. (Default: 0)';
$string['settings_temperature'] = 'temperature';
$string['settings_temperature_help'] = 'Temperature influences whether the output is more random and creative or more predictable. Increasing the temperature will make the model answer more creatively. (Default: 0.8)';
$string['settings_top_k'] = 'top_k';
$string['settings_top_k_help'] = 'Reduces the probability of generating nonsense. A higher value (e.g. 100) will give more diverse answers, while a lower value (e.g. 10) will be more conservative. (Default: 40)';
$string['settings_top_p'] = 'top_p';
$string['settings_top_p_help'] = 'Works together with top-k. A higher value (e.g. 0.95) will lead to more diverse text, while a lower value (e.g. 0.5) will generate more focused and conservative text. (Default: 0.9)';
$string['username'] = 'Username';
$string['username_help'] = 'The username used for basic authentication.';
