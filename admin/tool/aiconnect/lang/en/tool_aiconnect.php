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
 * Language strings for this plugin
 *
 * @package    tool_aiconnect
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['apikey'] = 'OpenAI API Key';
$string['apikey_desc'] = 'The API Key for your OpenAI account, from https://platform.openai.com/account/api-keys . Sample key looks like this: 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. This is ignored by Ollama';
$string['emptyapikey'] = 'The API setting is empty, it must be set in plugin settins';

$string['endpoint'] = 'Endpoint';
$string['endpoint_desc'] = 'Endpoint url';
$string['endpointerror'] = 'Endpoint error';
$string['frequency_penalty'] = 'Frequency penalty';
$string['frequency_penalty_desc'] = 'Reduces repetition of words that have already been generated. It counts how many times the word was already used.';
$string['json_format'] = 'Json format response';
$string['json_format_text'] = 'Ask remote system to respond in json format, may not work with all models';
$string['log_requests'] = 'Log requests';
$string['log_requests_text'] = 'Log the messages part of all requests to the remote AI system. Debug must also be set to Developer. It adds a lot of text to  the logging gsystem.';

$string['make_request'] = 'Make request';
$string['misssingmodelerror'] = 'No model has been provided for the call';
$string['model'] = 'Model';
$string['model_desc'] = 'The model used to generate the completion.';

$string['openaisettings'] = 'OpenAI settings';
$string['openaisettings_help'] = 'Settings for Large Language Model services';
$string['pluginname'] = 'AI Connect tool';
$string['presence_penalty'] = 'Presence penalty';
$string['presence_penalty_desc'] = 'Similar to frequency penalty, it reduces probability of using a word that was already used.
The difference is that is does not matter how many times the word was used - just if it was or not.';

// Privacy API.

$string['privacy:metadata:tool_aiconnect'] = 'In order to generate text or image, user needs to pass prompt text and/or image.';
$string['privacy:metadata:tool_aiconnect:image'] = 'Image is an optional argument you can pass to make a base for generated image.';
$string['privacy:metadata:tool_aiconnect:prompttext'] = 'User\'s prompt text is being sent to API services to generate response.';

$string['sourceoftruth'] = 'Source of truth';
$string['sourceoftruth_desc'] = 'Information that is specific for your organization. It will be passed to ChatGPT as facts that should be used when crafting the response.';
$string['temperature'] = 'Temperature';
$string['temperature_desc'] = 'In other words this is "randomness" or "creativity". Low temperature will generate more coherent but predictable text. The range is from 0 to 1.';
$string['testaiconfiguration'] = 'Test your configuration to AI services';
$string['testaiservices'] = 'Test AI connection';
$string['top_p'] = 'Top P';
$string['top_p_desc'] = 'It\'s used for similar purpose as temperature - the lower the setting, the more correct and deterministic the output. The range is also from 0 to 1.';
