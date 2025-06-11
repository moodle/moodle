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
 * Strings for component 'qtype_essayautograde', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage essayautograde
 * @copyright  2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @copyright  based on work by 1999 Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Essay (auto-grade)';
$string['pluginname_help'] = 'In response to a question that may include an image, the respondent writes an answer of one or more paragraphs. Initially, a grade is awarded automatically based on the number of chars, words, sentences or paragraphs, and the presence of certain target phrases. The automatic grade may be overridden later by the teacher.';
$string['pluginname_link'] = 'question/type/essayautograde';
$string['pluginnameadding'] = 'Adding an Essay (auto-grade) question';
$string['pluginnameediting'] = 'Editing an Essay (auto-grade) question';
$string['pluginnamesummary'] = 'Allows an essay of several sentences or paragraphs to be submitted as a question response. The essay is graded automatically. The grade may be overridden later.';

$string['privacy:metadata'] = 'The Essay (auto-grade) question type plugin does not store any personal data.';

$string['addmultiplebands'] = 'Add {$a} more grade bands';
$string['addmultiplephrases'] = 'Add {$a} more target phrases';
$string['addpartialgrades_help'] = 'If this option is enabled, grades will be added for partially completed grade bands.';
$string['addpartialgrades'] = 'Award partial grades?';
$string['addsingleband'] = 'Add 1 more grade band';
$string['addsinglephrase'] = 'Add 1 more target phrase';
$string['aiassistant_help'] = 'Select the AI grading assistant, if any, that you wish to use to generate feedback and preliminary grades for student submissions.';
$string['aiassistant'] = 'AI assistant';
$string['aiassistantnotenabled'] = 'There are currently no text-generating AI assistants enabled on this Moodle site. Please ask your Moodle administrator to add settings for an AI provider (Site administration > AI)';
$string['aipercent_help'] = 'Select the percentage contribution of the AI grade to the total grade for this question.';
$string['aipercent'] = 'AI grade';
$string['aiprompt_help'] = 'Add a prompt suitable for sending to an AI assistant in order to get feedback and/or a grade for work submitted by students.';
$string['aiprompt'] = 'AI prompt';
$string['aisettings'] = 'AI Settings';
$string['allowsimilarity_help'] = 'The maximum level of similarity that is allowed between a student\'s response and the response template or sample response. The higher this value, the more similar the student\'s response can be to the template or sample. Conversely, the lower the value, the more different the student\'s response must be from the template or sample. Adjusting this value can affect the level of originality and detail required from students in their responses.';
$string['allowsimilarity'] = 'Allow similarity?';
$string['allowsimilaritypercent'] = 'Yes - allow up to {$a}% similarity';
$string['auto'] = 'Auto';
$string['autograding'] = 'Auto-grading';
$string['bandtext'] = 'For {$a->count} or more items, award {$a->percent} of the question grade.';
$string['bandtext1'] = 'For ';
$string['bandtext2'] = 'or more items, award';
$string['bandtext3'] = 'of the question grade.';
$string['chars'] = 'Characters';
$string['charspersentence'] = 'Characters per sentence';
$string['commonerror'] = 'Common error';
$string['commonerrors_help'] = 'The common errors are defined in the "Glossary of errors" associated with this question.';
$string['commonerrors'] = 'Common errors';
$string['correctresponse'] = 'To get full marks for this question, you must satisfy the following criteria:';
$string['crop'] = 'Crop';
$string['enableautograde_help'] = 'Enable, or disable, automatic grading';
$string['enableautograde'] = 'Enable automatic grading';
$string['errorbehavior_help'] = 'These settings refine the matching behavior for entries in the Glossary of common errors.';
$string['errorbehavior'] = 'Error matching behavior';
$string['errorcmid_help'] = 'Choose the Glossary that contains a list of common errors. 

Each time one of the errors is found in the essay response, the specified penalty will be deducted from the student\'s grade for this question.';
$string['errorcmid'] = 'Glossary of errors';
$string['errorpercent_help'] = 'Select the percentage of total grade that should be deducted for each error that is found in the response.';
$string['errorpercent'] = 'Penalty per error';
$string['excludecommonerrors'] = 'Do not make any of the common errors in <a href="{$a->href}" target="_blank">{$a->name}</a>';
$string['explanationautopercent'] = 'This is outside the normal percentage range, so it was adjusted to {$a->autopercent}%.';
$string['explanationcommonerror'] = '{$a->percent}% for including "{$a->error}", which is a common error';
$string['explanationcompleteband'] = '{$a->percent}% for completing Grade band [{$a->gradeband}]';
$string['explanationdatetime'] = 'on %Y %b %d (%a) at %H:%M';
$string['explanationfiles'] = '{$a->percent}% for submitting {$a->filecount} / {$a->itemcount} files';
$string['explanationfirstitems'] = '{$a->percent}% for the first {$a->count} {$a->itemtype}';
$string['explanationgrade'] = 'Therefore, the computer-generated grade for this essay was set to {$a->finalgrade} = ({$a->finalpercent}% of {$a->maxgrade}).';
$string['explanationitems'] = '{$a->percent}% for {$a->count} {$a->itemtype}';
$string['explanationmaxgrade'] = 'The maximum grade for this question is {$a->maxgrade}.';
$string['explanationnotenough'] = '{$a->count} {$a->itemtype} is less than the minimum amount required to be given a grade.';
$string['explanationoverride'] = 'Later, {$a->datetime}, the grade for this essay was manually set to {$a->manualgrade}.';
$string['explanationpartialband'] = '{$a->percent}% for partially completing Grade band [{$a->gradeband}]';
$string['explanationpenalty'] = 'However, {$a->penaltytext} was deducted for checking the response before submission.';
$string['explanationrawpercent'] = 'The raw percentage grade for this essay is {$a->rawpercent}% <br /> = {$a->details}.';
$string['explanationremainingitems'] = '{$a->percent}% for the remaining {$a->count} {$a->itemtype}';
$string['explanationseecomment'] = '(see comment below)';
$string['explanationtargetphrase'] = '{$a->percent} for including the phrase "{$a->phrase}"';
$string['feedback'] = 'Feedback';
$string['feedbackhintbreaks'] = 'Did you use too many line breaks?';
$string['feedbackhintchars'] = 'Did you write the required number of characters?';
$string['feedbackhinterrors'] = 'Did you make any common errors?';
$string['feedbackhintfiles'] = 'Did you attach the required number of files?';
$string['feedbackhintparagraphs'] = 'Did you write the required number of paragraphs?';
$string['feedbackhintphrases'] = 'Did you include all the target phrases?';
$string['feedbackhints'] = 'Hints to improve your grade';
$string['feedbackhintsentences'] = 'Did you write the required number of sentences?';
$string['feedbackhintwords'] = 'Did you reach the word-count goal?';
$string['files'] = 'Files';
$string['fogindex_help'] = 'The Gunning fog index is a measure of readability. It is calculated using the following formula.

* ((words per sentence) + (long words per sentence)) x 0.4

For more information see: <https://en.wikipedia.org/wiki/Gunning_fog_index>';
$string['fogindex'] = 'Fog index';
$string['forceupgrade'] = 'Force upgrade';
$string['gradeband_help'] = 'Specify the minimum number of countable items for this band to be applied, and the grade that is to be awarded if this band is applied.';
$string['gradeband'] = 'Grade band [{no}]';
$string['gradebands'] = 'Grade bands';
$string['gradecalculation'] = 'Grade calculation';
$string['gradeforthisquestion'] = 'Grade for this question';
$string['hidden'] = 'Hidden';
$string['hidesample'] = 'Hide sample';
$string['itemcount_help'] = 'The minimum number of countable items that must be in the essay text in order to achieve the maximum grade for this question.

Note, that this value may be rendered ineffective by the grade bands, if any, defined below.';
$string['itemcount'] = 'Expected number of items';
$string['itemtype_help'] = 'Select the type of items in the essay text that will contribute to the auto-grade.';
$string['itemtype'] = 'Type of countable items';
$string['lexicaldensity_help'] = 'The lexical density is a percentage calculated using the following formula.

* 100 x (number of unique words) / (total number of words)

Thus, an essay in which many words are repeated has a low lexical density, while a essay with many unique words has a high lexical density.';
$string['lexicaldensity'] = 'Lexical density';
$string['longwords_help'] = '"Long words" are words that have three or more syllables. Note that the algorithm for determining the number of syllables yields only approximate results.';
$string['longwords'] = 'Long words';
$string['longwordspersentence'] = 'Long words per sentence';
$string['maximumfilecount'] = 'Maximum number of files: {$a}';
$string['maximumfilesize'] = 'Maximum file size: {$a}';
$string['minimumfilecount'] = 'Minimum number of files: {$a}';
$string['missing'] = 'Missing';
$string['overflow'] = 'Overflow';
$string['paragraphs'] = 'Paragraphs';
$string['phrasebehavior_help'] = 'These settings refine the matching behavior for this target phrase.';
$string['phrasebehavior'] = 'Target phrase [{no}] behavior';
$string['phrasecasesensitiveno'] = 'Match is case-insensitive.';
$string['phrasecasesensitiveyes'] = 'Match is case-sensitive.';
$string['phrasefullmatchno'] = 'Match full or partial words.';
$string['phrasefullmatchyes'] = 'Match full words only.';
$string['phraseignorebreaksno'] = 'Recognize line breaks.';
$string['phraseignorebreaksyes'] = 'Ignore line breaks.';

$string['phrasematch'] = 'Phrase match';
$string['phrasepercent'] = 'Phrase percent';
$string['phrasedivisor'] = 'Phrase divisor';

$string['phrasetext'] = 'If {$a->phrase} is used, award {$a->percent} of the question grade.';
$string['phrasetext1'] = 'If';
$string['phrasetext2'] = 'is used, award';
$string['phrasetext3'] = '';
$string['phrasetext4'] = 'of the question grade.';

$string['phrasepercentexactly'] = 'exactly';
$string['phrasepercentdividedby'] = 'divided by {$a}';

$string['pleaseattachfiles'] = 'Please attach the required number of files.';
$string['pleaseinputtext'] = 'Please input your response in the text box.';
$string['present'] = 'Present';
$string['requiredfilecount'] = 'Required number of files: {$a}';
$string['responseisnotoriginal'] = 'Please make your text more original.';
$string['responsesample_help'] = 'Any text here will be displayed as a sample response, if the student clicks the "Show sample" link in the question text.';
$string['responsesample'] = 'Sample response';
$string['responsesampleformat_help'] = 'Select the format of the sample response text.';
$string['responsesampleformat'] = 'Sample essay format';
$string['rewriteresubmit'] = ' and submit again.';
$string['rewriteresubmitbreaks'] = 'remove any line breaks';
$string['rewriteresubmitchars'] = 'add more characters';
$string['rewriteresubmiterrors'] = 'fix the common errors, ';
$string['rewriteresubmitfiles'] = 'attach the required number of files';
$string['rewriteresubmitjoin'] = ', ';
$string['rewriteresubmitparagraphs'] = 'add more paragraphs';
$string['rewriteresubmitphrases'] = 'add the missing phrases';
$string['rewriteresubmitsentences'] = 'add more sentences';
$string['rewriteresubmitwords'] = 'add more words';
$string['rotate'] = 'Rotate';
$string['scale'] = 'Scale';
$string['sentences'] = 'Sentences';
$string['sentencesperparagraph'] = 'Sentences per paragraph';
$string['showcalculation_help'] = 'If this option is enabled, an explanation of the calculation of the automatically generated grade will be shown on the grading and review pages.';
$string['showcalculation'] = 'Show grade calculation?';
$string['showfeedback_help'] = 'If this option is enabled, a table of actionable feedback will be shown on the grading and review pages. Actionable feedback is feedback that tells students what they need to do to improve.';
$string['showfeedback'] = 'Show student feedback?';
$string['showgradebands_help'] = 'If this option is enabled, details of the grade bands will be shown on the grading and review pages.';
$string['showgradebands'] = 'Show grade bands?';
$string['showsample'] = 'Show sample';
$string['showtargetphrases_help'] = 'If this option is enabled, details of the target phrases will be shown on the grading and review pages.';
$string['showtargetphrases'] = 'Show target phrases?';
$string['showtextstats_help'] = 'If this option is enabled, statistics about the text will be shown.';
$string['showtextstats'] = 'Show text statistics?';
$string['showtostudentsonly'] = 'Yes, show to students only';
$string['showtoteachersandstudents'] = 'Yes, show to teachers and students';
$string['showtoteachersonly'] = 'Yes, show to teachers only';
$string['targetphrase_help'] = 'Specify the grade that will be added if this target phrase appears in the essay.

> **e.g.** If [Finally] is used, award [10% of the question grade.]

The target phrase can be a single phrase or a list phrases separated by either a comma "," or the word "OR" (upper case).

> **e.g.** If [Finally OR Lastly] is used, award [10% of the question grade.]

A question mark "?" in a phrase matches any single character, while an asterisk "*" matches an arbitrary number of chars (including zero chars).

> **e.g.** If [First\*Then\*Finally] is used, award [50% of the question grade.]';
$string['targetphrase'] = 'Target phrase [{no}]';
$string['targetphrases'] = 'Target phrases';
$string['textstatistics'] = 'Text statistics';
$string['textstatitems_help'] = 'Select any items here that you wish to appear in the text statistics that are shown on grading and review pages.';
$string['textstatitems'] = 'Statistical items';
$string['uniquewords'] = 'Unique words';
$string['uploadfiles'] = 'Upload files';
$string['visible'] = 'Visible';
$string['words'] = 'Words';
$string['wordspersentence'] = 'Words per sentence';

$string['countcharslabel'] = 'Current character count';
$string['countfileslabel'] = 'Current file count';
$string['countparagraphslabel'] = 'Current paragraph count';
$string['countsentenceslabel'] = 'Current sentence count';
$string['countwordslabel'] = 'Current word count';

$string['maxwordserror'] = 'Oops, you wrote too many words!';
$string['maxwordslabel'] = 'Maximum word count';
$string['maxwordswarning'] = 'Oops, you\'ve written too many words!';
$string['minwordserror'] = 'Oops! you didn\'t write enough words.';
$string['minwordslabel'] = 'Minimum word count';
$string['minwordswarning'] = 'Keep going! You haven\'t written enough words yet.';
