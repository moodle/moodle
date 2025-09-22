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
 * Strings for component aiprovider_deepseek, language 'en'.
 *
 * @package    aiprovider_deepseek
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:explain_text:endpoint'] = 'API endpoint';
$string['action:explain_text:model'] = 'Text explanation model';
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
$string['apikey'] = 'DeepSeek API key';
$string['apikey_help'] = 'Get a key from your <a href="https://platform.deepseek.com/api_keys" target="_blank">DeepSeek API keys</a>.';
$string['custom_model_name'] = 'Custom model name';
$string['extraparams'] = 'Extra parameters';
$string['extraparams_help'] = 'Extra parameters can be configured here. We support JSON format. For example:
<pre>
{
    "temperature": 0.5,
    "max_tokens": 100
}
</pre>';
$string['invalidjson'] = 'Invalid JSON string';
$string['pluginname'] = 'DeepSeek API provider';
$string['privacy:metadata'] = 'The DeepSeek API provider plugin does not store any personal data.';
$string['privacy:metadata:aiprovider_deepseek:externalpurpose'] = 'This information is sent to the DeepSeek API in order for a response to be generated. Your DeepSeek account settings may change how DeepSeek stores and retains this data. No user data is explicitly sent to DeepSeek or stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_deepseek:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_deepseek:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['settings'] = 'Settings';
$string['settings_frequency_penalty'] = 'frequency_penalty';
$string['settings_frequency_penalty_help'] = 'Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model\'s likelihood to repeat the same line verbatim.';
$string['settings_help'] = 'Adjust the settings below to customise how requests are sent to DeepSeek.';
$string['settings_logprobs'] = 'logprobs';
$string['settings_logprobs_help'] = 'Whether to return log probabilities of the output tokens or not. If true, returns the log probabilities of each output token returned in the content of message.';
$string['settings_logprobs_label'] = 'Enable';
$string['settings_max_tokens'] = 'max_tokens';
$string['settings_max_tokens_help'] = 'Integer between 1 and 8192. The maximum number of tokens that can be generated in the chat completion. The total length of input tokens and generated tokens is limited by the model\'s context length. If max_tokens is not specified, the default value 4096 is used.';
$string['settings_presence_penalty'] = 'presence_penalty';
$string['settings_presence_penalty_help'] = 'Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the model\'s likelihood to talk about new topics.';
$string['settings_temperature'] = 'temperature';
$string['settings_temperature_help'] = 'What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic. We generally recommend altering this or top_p but not both.';
$string['settings_top_logprobs'] = 'top_logprobs';
$string['settings_top_logprobs_help'] = 'An integer between 0 and 20 specifying the number of most likely tokens to return at each token position, each with an associated log probability. logprobs must be set to true if this parameter is used.';
$string['settings_top_p'] = 'top_p';
$string['settings_top_p_help'] = 'An alternative to sampling with temperature, called nucleus sampling, where the model considers the results of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability mass are considered. We generally recommend altering this or temperature but not both.';
