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
 * Strings for component aiprovider_gemini, language 'en'.
 *
 * @package    aiprovider_gemini
 * @copyright  2025 University of Ferrara, Italy
 * @author     Andrea Bertelli <andrea.bertelli@unife.it>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:explain_text:endpoint'] = 'API endpoint';
$string['action:explain_text:model'] = 'AI model';
$string['action:explain_text:model_help'] = 'The model used to explain the provided text.';
$string['action:explain_text:systeminstruction'] = 'System instruction';
$string['action:explain_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:generate_image:endpoint'] = 'API endpoint';
$string['action:generate_image:model'] = 'AI model';
$string['action:generate_image:model_help'] = 'The model used to generate images from user prompts.';
$string['action:generate_image:systeminstruction'] = 'System instruction';
$string['action:generate_image:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
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
$string['apikey'] = 'Gemini API key';
$string['apikey_help'] = 'Get a key from <a href="https://aistudio.google.com/apikey" target="_blank">Google AI Studio website API keys</a>.';
$string['custom_model_name'] = 'Custom model name';
$string['extraparams'] = 'Extra parameters';
$string['extraparams_help'] = 'Extra parameters can be configured here. We support JSON format. For example:
<pre>
{
    "temperature": 0.5,
    "max_output_tokens": 100
}
</pre>';
$string['getallmodels_error'] = 'You need to insert API key before.';
$string['invalidjson'] = 'Invalid JSON string';
$string['pluginname'] = 'Gemini API provider';
$string['privacy:metadata'] = 'The Gemini API provider plugin does not store any personal data.';
$string['privacy:metadata:aiprovider_gemini:externalpurpose'] = 'This information is sent to the Gemini API in order for a response to be generated. Your Gemini account settings may change how Gemini stores and retains this data. No user data is explicitly sent to Gemini or stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_gemini:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_gemini:numberimages'] = 'When generating images the number of images used in the response.';
$string['privacy:metadata:aiprovider_gemini:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['privacy:metadata:aiprovider_gemini:responseformat'] = 'When generating images the format of the response.';
$string['settings'] = 'Settings';
$string['settings_help'] = 'Adjust the settings below to customise how requests are sent to Gemini.';
$string['settings_max_output_tokens'] = 'Max tokens';
$string['settings_max_output_tokens_help'] = 'The maximum number of tokens used in the generated text.';
$string['settings_stop_sequences'] = 'Stop sequences';
$string['settings_stop_sequences_help'] = 'Specify one or more character sequences (comma-separated) where the model should stop generating text.';
$string['settings_temperature'] = 'Temperature';
$string['settings_temperature_help'] = 'Temperature influences whether the output is more random and creative or more predictable. Increasing the temperature will make the model answer more creatively. This setting works alongside other options like topP and topK to generate the output.';
$string['settings_top_k'] = 'Top k';
$string['settings_top_k_help'] = 'Reduces the probability of generating nonsense. A higher value (e.g. 100) will give more diverse answers, while a lower value (e.g. 10) will be more conservative.';
$string['settings_top_p'] = 'Top p';
$string['settings_top_p_help'] = 'Top p (nucleus sampling) determines how many possible words to consider. A high value (e.g. 0.9) means the model looks at more words, which makes the generated text more diverse.';
