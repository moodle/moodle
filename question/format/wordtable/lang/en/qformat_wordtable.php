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
 * Strings for component 'qformat_wordtable', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qformat_wordtable
 * @copyright  2010-2015 Eoin Campbell
 * @author     Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

// Strings used in format.php.
$string['pluginname'] = 'Microsoft Word 2010 table format (wordtable)';
$string['pluginname_help'] = 'This is a front-end for converting Microsoft Word 2010 files into Moodle Question XML format for import, and converting Moodle Question XML format into a format suitable for editing in Microsoft Word.';
$string['pluginname_link'] = 'qformat/wordtable';
$string['privacy:metadata'] = 'The WordTable question format plugin does not store any personal data.';
$string['wordtable'] = 'Microsoft Word 2010 table format (wordtable)';
$string['wordtable_help'] = 'This is a front-end for converting Microsoft Word 2010 files into Moodle Question XML format for import, and converting Moodle Question XML format into an enhanced XHTML format for exporting into a format suitable for editing in Microsoft Word.';
$string['noquestions'] = 'No questions to export';
$string['xsltunavailable'] = 'You need the XSLT library installed in PHP to save this Word file';
$string['transformationfailed'] = 'XSLT transformation failed (<b>{$a}</b>)';
$string['stylesheetunavailable'] = 'XSLT Stylesheet <b>{$a}</b> is not available';
$string['cannotopentempfile'] = 'Cannot open temporary file <b>{$a}</b>';
$string['cannotreadzippedfile'] = 'Cannot read Zipped file <b>{$a}</b>';
$string['cannotwritetotempfile'] = 'Cannot write to temporary file <b>{$a}</b>';
$string['xmlnotsupported'] = 'Files in XML format not supported: <b>{$a}</b>';
$string['docnotsupported'] = 'Files in Word 2003 format not supported: <b>{$a}</b>, use Moodle2Word 3.x instead';
$string['htmlnotsupported'] = 'Files in HTML format not supported: <b>{$a}</b>';
$string['htmldocnotsupported'] = 'Incorrect Word format: please use <i>File>Save As...</i> to save <b>{$a}</b> in native Word 2010 (.docx) format and import again';
$string['preview_question_not_found'] = 'Preview question not found, name / course ID: {$a}';

// Strings used in XSLT when converting questions into Word format.
$string['cloze_instructions'] = 'Use <strong>bold</strong> for Multichoice, <em>italic</em> for Short Answer, and <u>Underline</u> for Numerical questions.';
$string['cloze_distractor_column_label'] = 'Distractors';
$string['cloze_feedback_column_label'] = 'Distractor Feedback';
$string['cloze_mcformat_label'] = 'Orientation (D = dropdown; V = vertical, H = horizontal radio buttons)';
$string['unsupported_instructions'] = 'Importing this question type is not supported.';
$string['description_instructions'] = 'This is not actually a question. Instead it is a way to add some instructions, rubric or other content to the activity. This is similar to the way that labels can be used to add content to the course page.';
$string['essay_instructions'] = 'Allows a response of a few sentences or paragraphs. This must then be graded manually.';
$string['multichoice_instructions'] = 'Allows the selection of a single or multiple responses from a pre-defined list.';
$string['truefalse_instructions'] = 'Set grade \'100\' to the correct answer.';
$string['interface_language_mismatch'] = 'No questions imported because the language of the labels in the Word file does not match your current Moodle interface language.';

// Unused strings.
$string['export_type_set'] = 'The question export type has been set to ';
$string['tempfile'] = 'Temporary XML file: <b>{$a}</b>';
$string['templateunavailable'] = 'Word-compatible XHTML template <b>{$a}</b> is not available';

// These strings are part of the Word Startup template user interface, not the Moodle interface.
// These templates are available at http://www.moodle2word.net/.
$string['word_about_moodle2word'] = 'About Moodle2Word';
$string['word_about_moodle2word_screentip'] = 'About the Moodle2Word Word templates and Moodle plug-in';
$string['word_conversionfailed'] = 'Question import failed';
$string['word_conversionsucceeded2'] = 'Question import <b>succeeded</b>, <br>click the \'Continue\' button to continue.';
$string['word_conversionsucceeded'] = 'Question import <b>succeeded</b>, <br>click the <b>\'Close\'</b> button to continue.';
$string['word_convert_images'] = 'Convert Images';
$string['word_convert_images_screentip'] = 'Import image data in exported questions';
$string['word_convert_images_supertip'] = 'Embed images into Word file';
$string['word_currentquestion'] = ' (Current Question)';
$string['word_gapselect_screentip'] = 'Warning: customised Select missing words Moodle plugin required for this question type.';
$string['word_import'] = 'Import';
$string['word_import_screentip'] = 'Warning: Works with Moodle 2.x servers only';
$string['word_import_supertip'] = 'Upload and import all the questions to your Moodle server';
$string['word_import_mathtype'] = 'Import MathType Images';
$string['word_import_mathtype_screentip'] = 'Import MathType Equation images';
$string['word_import_mathtype_supertip'] = 'Run afer MathType \'Export Equations\' command';
$string['word_login_supertip'] = 'Choose your own Moodle server';
$string['word_multiple_answer'] = 'Multiple answer';
$string['word_new_question_file'] = 'New Question File';
$string['word_new_question_file_screentip'] = 'Questions must be saved in Word 2010 (.docx) format';
$string['word_new_question_file_supertip'] = 'Each Word file may contain multiple categories';
$string['word_addcategory_supertip'] = 'Category names use the Heading 1 style';
$string['word_preview'] = 'Preview Current Question'; // Obsolete.
$string['word_preview_supertip'] = 'Upload the current question to Moodle for preview';
$string['word_setunset_assessment_view'] = 'Set/Unset Assessment View';
$string['word_setunset_assessment_screentip'] = 'Reformat questions for a printed assessment';
$string['word_setunset_assessment_supertip'] = 'Set question metadata (name, correct answer indicators, feedback, etc.) to hidden';
$string['word_showhide_assessment_view'] = 'Show/Hide Assessment View';
$string['word_showhide_assessment_screentip'] = 'Show question metadata to edit, hide to preview printed assessment';
$string['word_showhide_assessment_supertip'] = 'Shows or hides the hidden text';
$string['word_shuffle_screentip'] = 'Shuffle the answers to MCQ/TF/MA questions';
$string['word_shuffle_supertip'] = 'A few shuffles is better than 1';
$string['word_view'] = 'View';
