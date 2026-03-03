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
 * Strings for component aiprovider_awsbedrock, language 'en'.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:explain_text:model'] = 'AI model';
$string['action:explain_text:model_help'] = 'The model used to explain the provided text.';
$string['action:explain_text:systeminstruction'] = 'System instruction';
$string['action:explain_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:generate_image:model'] = 'AI model';
$string['action:generate_image:model_help'] = 'The model used to generate images from user prompts. <a href="https://docs.aws.amazon.com/bedrock/latest/userguide/models-supported.html" target="_blank">Supported models</a>.';
$string['action:generate_text:model'] = 'AI model';
$string['action:generate_text:model_help'] = 'The model used to generate the text response. <a href="https://docs.aws.amazon.com/bedrock/latest/userguide/models-supported.html" target="_blank">Supported models</a>';
$string['action:generate_text:systeminstruction'] = 'System instruction';
$string['action:generate_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:summarise_text:model'] = 'AI model';
$string['action:summarise_text:model_help'] = 'The model used to summarise the provided text.';
$string['action:summarise_text:systeminstruction'] = 'System instruction';
$string['action:summarise_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['apikey'] = 'Amazon API key credential';
$string['apikey_help'] = 'Generate a key using IAM in the AWS console or using the AWS CLI.';
$string['apisecret'] = 'Amazon API secret credential';
$string['apisecret_help'] = 'Generate a secret using IAM in the AWS console or using the AWS CLI.';
$string['awsregion'] = 'Region';
$string['awsregion:af-south-1'] = 'Africa (Cape Town)';
$string['awsregion:ap-east-2'] = 'Asia Pacific (Taipei)';
$string['awsregion:ap-northeast-1'] = 'Asia Pacific (Tokyo)';
$string['awsregion:ap-northeast-2'] = 'Asia Pacific (Seoul)';
$string['awsregion:ap-northeast-3'] = 'Asia Pacific (Osaka)';
$string['awsregion:ap-south-1'] = 'Asia Pacific (Mumbai)';
$string['awsregion:ap-south-2'] = 'Asia Pacific (Hyderabad)';
$string['awsregion:ap-southeast-1'] = 'Asia Pacific (Singapore)';
$string['awsregion:ap-southeast-2'] = 'Asia Pacific (Sydney)';
$string['awsregion:ap-southeast-3'] = 'Asia Pacific (Jakarta)';
$string['awsregion:ap-southeast-4'] = 'Asia Pacific (Melbourne)';
$string['awsregion:ap-southeast-5'] = 'Asia Pacific (Malaysia)';
$string['awsregion:ap-southeast-6'] = 'Asia Pacific (New Zealand)';
$string['awsregion:ap-southeast-7'] = 'Asia Pacific (Thailand)';
$string['awsregion:ca-central-1'] = 'Canada (Central)';
$string['awsregion:ca-west-1'] = 'Canada West (Calgary)';
$string['awsregion:eu-central-1'] = 'Europe (Frankfurt)';
$string['awsregion:eu-central-2'] = 'Europe (Zurich)';
$string['awsregion:eu-north-1'] = 'Europe (Stockholm)';
$string['awsregion:eu-south-1'] = 'Europe (Milan)';
$string['awsregion:eu-south-2'] = 'Europe (Spain)';
$string['awsregion:eu-west-1'] = 'Europe (Ireland)';
$string['awsregion:eu-west-2'] = 'Europe (London)';
$string['awsregion:eu-west-3'] = 'Europe (Paris)';
$string['awsregion:il-central-1'] = 'Israel (Tel Aviv)';
$string['awsregion:me-central-1'] = 'Middle East (UAE)';
$string['awsregion:me-south-1'] = 'Middle East (Bahrain)';
$string['awsregion:mx-central-1'] = 'Mexico (Central)';
$string['awsregion:sa-east-1'] = 'South America (São Paulo)';
$string['awsregion:us-east-1'] = 'US East (N. Virginia)';
$string['awsregion:us-east-2'] = 'US East (Ohio)';
$string['awsregion:us-gov-east-1'] = 'AWS GovCloud (US-East)';
$string['awsregion:us-gov-west-1'] = 'AWS GovCloud (US-West)';
$string['awsregion:us-west-1'] = 'US West (N. California)';
$string['awsregion:us-west-2'] = 'US West (Oregon)';
$string['awsregion_help'] = 'The AWS region where the AI model is hosted.';
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
$string['model_ai21.jamba-1-5-large-v1:0'] = 'AI21 Labs Jamba 1.5 Large';
$string['model_ai21.jamba-1-5-mini-v1:0'] = 'AI21 Labs Jamba 1.5 Mini';
$string['model_amazon.nova-canvas-v1:0'] = 'Amazon Nova Canvas';
$string['model_amazon.nova-lite-v1:0'] = 'Amazon Nova Lite';
$string['model_amazon.nova-micro-v1:0'] = 'Amazon Nova Micro';
$string['model_amazon.nova-pro-v1:0'] = 'Amazon Nova Pro';
$string['model_amazon.titan-image-generator-v2:0'] = 'Amazon Titan Image Generator V2';
$string['model_anthropic.claude-3-5-sonnet-20240620-v1:0'] = 'Claude 3.5 Sonnet V1';
$string['model_anthropic.claude-3-5-sonnet-20241022-v2:0'] = 'Claude 3.5 Sonnet V2';
$string['model_anthropic.claude-3-7-sonnet-20250219-v1:0'] = 'Claude 3.7 Sonnet V1';
$string['model_anthropic.claude-3-haiku-20240307-v1:0'] = 'Claude 3 Haiku';
$string['model_anthropic.claude-haiku-4-5-20251001-v1:0'] = 'Claude 4.5 Haiku';
$string['model_anthropic.claude-sonnet-4-20250514-v1:0'] = 'Claude 4.0 Sonnet V1';
$string['model_anthropic.claude-sonnet-4-5-20250929-v1:0'] = 'Claude 4.5 Sonnet V1';
$string['model_meta.llama3-1-405b-instruct-v1:0'] = 'Meta Llama 3.1 405B Instruct';
$string['model_meta.llama3-1-70b-instruct-v1:0'] = 'Meta Llama 3.1 70B Instruct';
$string['model_meta.llama3-1-8b-instruct-v1:0'] = 'Meta Llama 3.1 8B Instruct';
$string['model_meta.llama3-2-11b-instruct-v1:0'] = 'Meta Llama 3.2 11B Instruct';
$string['model_meta.llama3-2-1b-instruct-v1:0'] = 'Meta Llama 3.2 1B Instruct';
$string['model_meta.llama3-2-3b-instruct-v1:0'] = 'Meta Llama 3.2 3B Instruct';
$string['model_meta.llama3-2-90b-instruct-v1:0'] = 'Meta Llama 3.2 90B Instruct';
$string['model_meta.llama3-3-70b-instruct-v1:0'] = 'Meta Llama 3.3 70B Instruct';
$string['model_meta.llama3-70b-instruct-v1:0'] = 'Meta Llama 3 70B Instruct';
$string['model_meta.llama3-8b-instruct-v1:0'] = 'Meta Llama 3 8B Instruct';
$string['model_mistral.mistral-7b-instruct-v0:2'] = 'Mistral 7B Instruct';
$string['model_mistral.mistral-large-2402-v1:0'] = 'Mistral Large Instruct';
$string['model_mistral.mistral-small-2402-v1:0'] = 'Mistral Small Instruct';
$string['model_mistral.mixtral-8x7b-instruct-v0:1'] = 'Mixtral 8X7B Instruct';
$string['model_stability.sd3-5-large-v1:0'] = 'Stability AI Stable Diffusion 3.5 Large';
$string['model_stability.stable-image-core-v1:1'] = 'Stability AI Stable Image Core 1.1';
$string['model_stability.stable-image-ultra-v1:1'] = 'Stability AI Stable Image Ultra 1.1';
$string['none'] = 'None';
$string['pluginname'] = 'AWS Bedrock Provider';
$string['privacy:metadata'] = 'The AWS Bedrock provider plugin does not store any personal data.';
$string['privacy:metadata:aiprovider_awsbedrock:externalpurpose'] = 'This information is sent to AWS in order for a response to be generated. Your AWS account settings may change how AWS stores and retains this data. No user data is explicitly sent to AWS or stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_awsbedrock:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_awsbedrock:numberimages'] = 'When generating images the number of images used in the response.';
$string['privacy:metadata:aiprovider_awsbedrock:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['privacy:metadata:aiprovider_awsbedrock:responseformat'] = 'The format of the response. When generating images.';
$string['settings'] = 'Settings';
$string['settings_cfg_scale'] = 'CFG scale';
$string['settings_cfg_scale_help'] = 'Specifies how strongly the generated image should adhere to the prompt. Use a lower value to introduce more randomness in the generation. Min: {$a->min}, Max: {$a->max}, Default: {$a->default}.';
$string['settings_cross_region_inference'] = 'Cross region inference';
$string['settings_cross_region_inference_help'] = 'The inference profile ID for this model. Default: {$a->default}';
$string['settings_frequency_penalty'] = 'Frequency penalty';
$string['settings_frequency_penalty_help'] = 'Penalizes new tokens based on their frequency in the text so far. Resulting in fewer repeated words. Min: {$a->min}, Max: {$a->max}, Default: {$a->default}.';
$string['settings_help'] = 'You can adjust the settings below to customize how requests are sent to AWS. Update the values as needed, ensuring they align with your requirements.';
$string['settings_max_tokens'] = 'Max Tokens';
$string['settings_max_tokens_help'] = 'The maximum number of tokens to generate in the response. Min: {$a->min}, Max: {$a->max}, Default: {$a->default}.';
$string['settings_negative_prompt_img'] = 'Negative prompt';
$string['settings_negative_prompt_img_help'] = 'Specifies keywords describing elements that should be excluded from the generated image. Enter words or short phrases separated by commas. Maximum length: 10,000 characters';
$string['settings_presence_penalty'] = 'Presence penalty';
$string['settings_presence_penalty_help'] = 'Reduce the frequency of repeated words within a single message by increasing this number. Unlike frequency penalty, presence penalty is the same no matter how many times a word appears. Min: {$a->min}, Max: {$a->max}, Default: {$a->default}.';
$string['settings_schema_version'] = 'Schema Version';
$string['settings_schema_version_help'] = 'Schema version to use for the request. Default: {$a->default}.';
$string['settings_seed'] = 'Seed';
$string['settings_seed_help'] = 'If specified, the backend will make a best effort to sample tokens deterministically, such that repeated requests with the same seed and parameters should return the same result. However, determinism cannot be totally guaranteed. Min: {$a->min}, Max: {$a->max}, Default: {$a->default}.';
$string['settings_seed_img'] = 'Seed';
$string['settings_seed_img_help'] = 'Determines the initial noise setting for the generation process. Changing the seed value while leaving all other parameters the same will produce a totally new image that still adheres to your prompt, dimensions, and other settings. It is common to experiment with a variety of seed values to find the perfect image. Min: {$a->min}, Max: {$a->max}, Default: {$a->default}.';
$string['settings_stop_sequences'] = 'Stop Sequence';
$string['settings_stop_sequences_help'] = 'Specify a character sequence to indicate where the model should stop';
$string['settings_temperature'] = 'Temperature';
$string['settings_temperature_help'] = 'Use a lower value to decrease randomness in responses. Min: {$a->min}, Max: {$a->max}, Default: {$a->default}.';
$string['settings_top_k'] = 'Top K';
$string['settings_top_k_help'] = 'Only sample from the top K options for each subsequent token. Min: {$a->min}, Max: {$a->max}, Default: {$a->default}.';
$string['settings_top_p'] = 'Top P';
$string['settings_top_p_help'] = 'Use a lower value to ignore less probable options and decrease the diversity of responses. Min: {$a->min}, Max: {$a->max}, Default: {$a->default}.';
