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
 * Strings for component 'ai', language 'en'
 *
 * @package    core
 * @category   string
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['acceptai'] = 'Accept and continue';
$string['action'] = 'Action';
$string['action_explain_text'] = 'Explain text';
$string['action_explain_text_desc'] = 'Explains the text content on a course page.';
$string['action_explain_text_instruction'] = 'You will receive a text input from the user. Your task is to explain the provided text. Follow these guidelines:
    1. Elaborate: Expand on key ideas and concepts, ensuring the explanation adds meaningful depth and avoids restating the text verbatim.
    2. Simplify: Break down complex terms or ideas into simpler components, making them easy to understand for a wide audience, including learners.
    3. Provide Context: Explain why something happens, how it works, or what its purpose is. Include relevant examples or analogies to enhance understanding where appropriate.
    4. Organise Logically: Structure your explanation to flow naturally, beginning with fundamental ideas before moving to finer details.

Important Instructions:
    1. Return the summary in plain text only.
    2. Do not include any markdown formatting, greetings, or platitudes.
    3. Focus on clarity, conciseness, and accessibility.

Ensure the explanation is easy to read and effectively conveys the main points of the original text.';
$string['action_generate_image'] = 'Generate image';
$string['action_generate_image_desc'] = 'Generates an image based on a text prompt.';
$string['action_generate_text'] = 'Generate text';
$string['action_generate_text_desc'] = 'Generates text based on a text prompt.';
$string['action_generate_text_instruction'] = 'You will receive a text input from the user. Your task is to generate text based on their request. Follow these important instructions:
    1. Return the summary in plain text only.
    2. Do not include any markdown formatting, greetings, or platitudes.';
$string['action_summarise_text'] = 'Summarise text';
$string['action_summarise_text_desc'] = 'Summarises the text content on a course page.';
$string['action_summarise_text_instruction'] = 'You will receive a text input from the user. Your task is to summarize the provided text. Follow these guidelines:
    1. Condense: Shorten long passages into key points.
    2. Simplify: Make complex information easier to understand, especially for learners.

Important Instructions:
    1. Return the summary in plain text only.
    2. Do not include any markdown formatting, greetings, or platitudes.
    3. Focus on clarity, conciseness, and accessibility.

Ensure the summary is easy to read and effectively conveys the main points of the original text.';
$string['action_translate_text'] = 'Translate text';
$string['action_translate_text_desc'] = 'Translate provided text from one language to another.';
$string['actionsettingprovider'] = '{$a} action settings';
$string['actionsettingprovider_desc'] = 'These settings control how the {$a->providername} performs the action {$a->actionname}.';
$string['actionsettings'] = 'Action settings';
$string['actionsettings_desc'] = 'These settings control the AI actions for this provider instance.';
$string['ai'] = 'AI';
$string['aiactionregister'] = 'AI action register';
$string['aiplacements'] = 'AI placements';
$string['aipolicyacceptance'] = 'AI policy acceptance';
$string['aipolicyregister'] = 'AI policy register';
$string['aiproviders'] = 'AI providers';
$string['aireports'] = 'AI reports';
$string['aiusage'] = 'AI usage';
$string['aiusagepolicy'] = 'AI usage policy';
$string['availableplacements'] = 'Choose where AI actions are available';
$string['availableplacements_desc'] = 'Placements define how and where AI actions can be used in your site. You can choose which actions are available in each placement through the settings.';
$string['availableproviders'] = 'Manage the AI providers connected to your LMS';
$string['availableproviders_desc'] = 'AI providers add AI functionality to your site through \'actions\' like text summarisation or image generation.<br/>
You can manage the actions for each provider in their settings.';
$string['btninstancecreate'] = 'Create instance';
$string['btninstanceupdate'] = 'Update instance';
$string['completiontokens'] = 'Completion tokens';
$string['completiontokens_help'] = 'Completion tokens are text units generated by the AI model as a response to your input. Longer responses use more tokens, which is likely to cost more.';
$string['configureprovider'] = 'Configure provider instance';
$string['contentwatermark'] = 'Generated by AI';
$string['createnewprovider'] = 'Create a new provider instance';
$string['dateaccepted'] = 'Date accepted';
$string['declineaipolicy'] = 'Decline';
$string['enableglobalratelimit'] = 'Set site-wide rate limit';
$string['enableglobalratelimit_help'] = 'Limit the number of requests that the AI provider can receive across the entire site every hour.';
$string['enableuserratelimit'] = 'Set user rate limit';
$string['enableuserratelimit_help'] = 'Limit the number of requests each user can make to the AI provider every hour.';
$string['error:actionnotfound'] = 'Action \'{$a}\' is not supported.';
$string['error:providernotfound'] = 'The AI provider instance is not found.';
$string['globalratelimit'] = 'Maximum number of site-wide requests';
$string['globalratelimit_help'] = 'The number of site-wide requests allowed per hour.';
$string['manageaiplacements'] = 'Manage AI placements';
$string['manageaiproviders'] = 'Manage AI providers';
$string['noproviders'] = 'This action is unavailable. No <a href="{$a}">AI providers</a> are configured for this action.';
$string['placement'] = 'Placement';
$string['placementactionsettings'] = 'Actions';
$string['placementactionsettings_desc'] = 'The AI actions available for this placement.';
$string['placementsettings'] = 'Placement-specific settings';
$string['placementsettings_desc'] = 'These settings control how this AI placement connects to the AI service, and related operations.';
$string['privacy:metadata:ai_action_explain_text'] = 'A table storing the explain text requests made by users.';
$string['privacy:metadata:ai_action_explain_text:completiontoken'] = 'The completion tokens used to explain the text.';
$string['privacy:metadata:ai_action_explain_text:fingerprint'] = 'The unique hash representing the state/version of the model and content.';
$string['privacy:metadata:ai_action_explain_text:generatedcontent'] = 'The actual text generated by the AI model based on the input prompt.';
$string['privacy:metadata:ai_action_explain_text:prompt'] = 'The prompt for the explain text request.';
$string['privacy:metadata:ai_action_explain_text:prompttokens'] = 'The prompt tokens used to explain the text.';
$string['privacy:metadata:ai_action_explain_text:responseid'] = 'The ID of the response.';
$string['privacy:metadata:ai_action_generate_image'] = 'A table storing the image generation requests made by users.';
$string['privacy:metadata:ai_action_generate_image:aspectratio'] = 'The aspect ratio of the generated images.';
$string['privacy:metadata:ai_action_generate_image:numberimages'] = 'The number of images generated.';
$string['privacy:metadata:ai_action_generate_image:prompt'] = 'The prompt for the image generation request.';
$string['privacy:metadata:ai_action_generate_image:quality'] = 'The quality of the generated images.';
$string['privacy:metadata:ai_action_generate_image:revisedprompt'] = 'The revised prompt of the generated images.';
$string['privacy:metadata:ai_action_generate_image:sourceurl'] = 'The source URL of the generated images.';
$string['privacy:metadata:ai_action_generate_image:style'] = 'The style of the generated images.';
$string['privacy:metadata:ai_action_generate_text'] = 'A table storing the text generation requests made by users.';
$string['privacy:metadata:ai_action_generate_text:completiontoken'] = 'The completion tokens used to generate the text.';
$string['privacy:metadata:ai_action_generate_text:fingerprint'] = 'The unique hash representing the state/version of the model and content.';
$string['privacy:metadata:ai_action_generate_text:generatedcontent'] = 'The actual text generated by the AI model based on the input prompt.';
$string['privacy:metadata:ai_action_generate_text:prompt'] = 'The prompt for the text generation request.';
$string['privacy:metadata:ai_action_generate_text:prompttokens'] = 'The prompt tokens used to generate the text.';
$string['privacy:metadata:ai_action_generate_text:responseid'] = 'The ID of the response.';
$string['privacy:metadata:ai_action_register'] = 'A table storing the action requests made by users.';
$string['privacy:metadata:ai_action_register:actionid'] = 'The ID of the action request.';
$string['privacy:metadata:ai_action_register:actionname'] = 'The action name of the request.';
$string['privacy:metadata:ai_action_register:model'] = 'The model used to generate the response.';
$string['privacy:metadata:ai_action_register:provider'] = 'The name of the provider that handled the request.';
$string['privacy:metadata:ai_action_register:success'] = 'The state of the action request.';
$string['privacy:metadata:ai_action_register:timecompleted'] = 'The completed time of the request.';
$string['privacy:metadata:ai_action_register:timecreated'] = 'The created time of the request.';
$string['privacy:metadata:ai_action_register:userid'] = 'The ID of the user who made the request.';
$string['privacy:metadata:ai_action_summarise_text'] = 'A table storing the summarise text requests made by users.';
$string['privacy:metadata:ai_action_summarise_text:completiontoken'] = 'The completion tokens used to summarise the text.';
$string['privacy:metadata:ai_action_summarise_text:fingerprint'] = 'The unique hash representing the state/version of the model and content.';
$string['privacy:metadata:ai_action_summarise_text:generatedcontent'] = 'The actual text generated by the AI model based on the input prompt.';
$string['privacy:metadata:ai_action_summarise_text:prompt'] = 'The prompt for the summarise text request.';
$string['privacy:metadata:ai_action_summarise_text:prompttokens'] = 'The prompt tokens used to summarise the text.';
$string['privacy:metadata:ai_action_summarise_text:responseid'] = 'The ID of the response.';
$string['privacy:metadata:ai_policy_register'] = 'A table storing the status of the AI policy acceptance for each user.';
$string['privacy:metadata:ai_policy_register:contextid'] = 'The ID of the context whose data was saved.';
$string['privacy:metadata:ai_policy_register:timeaccepted'] = 'The time the user accepted the AI policy.';
$string['privacy:metadata:ai_policy_register:userid'] = 'The ID of the user whose data was saved.';
$string['prompttokens'] = 'Prompt tokens';
$string['prompttokens_help'] = 'Prompt tokens are text units that make up the input you send to the AI model. Longer inputs use more tokens, which is likely to cost more.';
$string['provider'] = 'Provider';
$string['provideractionsettings'] = 'Actions';
$string['provideractionsettings_desc'] = 'Choose and configure the actions that the {$a} can perform on your site.';
$string['providerinstanceactionupdated'] = '{$a} action settings updated';
$string['providerinstancecreated'] = '{$a} AI provider instance created.';
$string['providerinstancedelete'] = 'Delete AI provider instance';
$string['providerinstancedeleteconfirm'] = 'You are about to delete the AI provider instance {$a->name} ({$a->provider}). Are you sure?';
$string['providerinstancedeleted'] = '{$a} AI provider instance deleted.';
$string['providerinstancedeletefailed'] = 'Cannot delete the {$a} AI provider instance. The provider is either in use or there is a database issue. Check if the provider is active or contact your database administrator for help.';
$string['providerinstancedisablefailed'] = 'Cannot disable the AI provider instance. The provider is either in use or there is a database issue. Check if the provider is active or contact your database administrator for help.';
$string['providerinstanceupdated'] = '{$a} AI provider instance updated.';
$string['providermoveddown'] = '{$a} moved down.';
$string['providermovedup'] = '{$a} moved up.';
$string['providername'] = 'Name for instance';
$string['providers'] = 'Providers';
$string['providersettings'] = 'Settings';
$string['providertype'] = 'Choose AI provider plugin';
$string['timegenerated'] = 'Time generated';
$string['unknownvalue'] = '—';
$string['userpolicy'] = '<h4><strong>Welcome to the new AI feature!</strong></h4>
<p>This Artificial Intelligence (AI) feature is based solely on external Large Language Models (LLM) to improve your learning and teaching experience. Before you start using these AI services, please read this usage policy.</p>
<h4><strong>Accuracy of AI-generated content</strong></h4>
<p>AI can give useful suggestions and information, but its accuracy may vary. You should always double-check the information provided to make sure it\'s accurate, complete, and suitable for your specific situation.</p>
<h4><strong>How your data is processed</strong></h4>
<p>This AI feature uses external Large Language Models (LLM). If you use this feature, any information or personal data you share will be handled according to the privacy policy of those LLMs. We recommend that you read their privacy policy to understand how they will handle your data. Additionally, a record of your interactions with the AI features may be saved in this site.</p>
<p>If you have questions about how your data is processed, please check with your teachers or learning organisation.</p>
<p>By continuing, you acknowledge that you understand and agree to this policy.</p>';
$string['userratelimit'] = 'Maximum number of requests per user';
$string['userratelimit_help'] = 'The number of requests allowed per hour, per user.';
