<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_aiquestions
 * @category    string
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'AI Text to questions generator';
$string['pluginname_desc'] = 'This plugin allows you to generate questions from a text';
$string['pluginname_help'] = 'Use this plugin from the course administration menu';
$string['privacy:metadata'] = 'AI Text to questions generator does not store any personal data';
$string['openaikey'] = 'OpenAI API Key';
$string['openaikeydesc'] = 'Please Type Here Your OpenAI API Key<br>
You can get your API key from <a href="https://platform.openai.com/account/api-keys">https://platform.openai.com/account/api-keys</a><br>
Select the "+ Create New Secret Key" button and copy the key to this field.<br>
Note that you need to have an OpenAI account that include billing settings to get an API key.';
$string['story'] = 'Story';
$string['storydesc'] = 'Please Type Here Your Story';
$string['numofquestions'] = 'Number of Questions';
$string['numofquestionsdesc'] = 'Please Select Here The Number of Questions You Want to Generate';
$string['generate'] = 'Generate Questions';
$string['aiquestions'] = 'AI Questions';
$string['backtocourse'] = 'Back to course';
$string['gotoquestionbank'] = 'Go to question bank';
$string['generatemore'] = 'Generate More Questions';
$string['createdquestionwithid'] = 'Created question with id ';
$string['language'] = 'Language';
$string['languagedesc'] = 'Please Select Here The Language You Want to Use For The Questions Generation<br>
Note that some languages are less supported than others on ChatGPT.';
$string['usepersonalprompt'] = 'Use Personal Prompt';
$string['usepersonalpromptdesc'] = 'Please Select Here If You Want to Use Personal Prompt';
$string['personalprompt'] = 'Personal Prompt';
$string['personalpromptdesc'] = 'Please type here your personal prompt,
The prompt is the explanation to ChatGPT how to generate the questions<br>
You should include these two place holders: {{numofquestions}} and {{language}}';
$string['tasksuccess'] = 'The question generation task was successfully created';
$string['generating'] = 'Generating your questions... (You can safely leave this page, and check later on the question bank)';
$string['generationfailed'] = 'The question generation failed after {$a} tries';
$string['generationtries'] = 'Number of tries sent to OpenAI: <b>{$a}</b>';
$string['outof'] = 'out of';
$string['numoftries'] = '<b>{$a}</b> tries';
$string['numoftriesset'] = 'Number of Tries';
$string['numoftriesdesc'] = 'Please Write Here The Number of Tries You Want to Send to OpenAI';
$string['preview'] = 'Preview question in new tab';
$string['cronoverdue'] = 'The cron task seems not to run,
questions generation rely on AdHoc Tasks that are created by the cron task, please check your cron settings.
See <a href="https://docs.moodle.org/en/Cron#Setting_up_cron_on_your_system">
https://docs.moodle.org/en/Cron#Setting_up_cron_on_your_system
</a> for more information.';
$string['createdquestionsuccess'] = 'Created question successfully';
$string['createdquestionssuccess'] = 'Created questions successfully';
$string['errornotcreated'] = 'Error: questions were not created';
