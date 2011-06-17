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
 * @package   moodlecore
 * @subpackage backup-imscc
 * @copyright 2009 Mauro Rondinelli (mauro.rondinelli [AT] uvcms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

// GENERAL PARAMETERS ************************************************************************************************* //
define('ROOT_DEEP', 2);

// PACKAGES FORMATS *************************************************************************************************** //
define('FORMAT_UNKNOWN', 'NA');
define('FORMAT_COMMON_CARTRIDGE', 'CC');
define('FORMAT_BLACK_BOARD', 'BB');

// FORMATS NAMESPACES ************************************************************************************************* //
define('NS_COMMON_CARTRIDGE', 'http://www.imsglobal.org/xsd/imscc/imscp_v1p1');
define('NS_BLACK_BOARD', 'http://www.blackboard.com/content-packaging');

// SHEET FILES ******************************************************************************************************** //
define('SHEET_BASE', 'cc/sheets/base.xml');
define('SHEET_INFO_DETAILS_MOD', 'cc/sheets/info_details_mod.xml');
define('SHEET_INFO_DETAILS_MOD_INSTANCE', 'cc/sheets/info_details_mod_instance.xml');
define('SHEET_COURSE_BLOCKS_BLOCK', 'cc/sheets/course_blocks_block.xml');
define('SHEET_COURSE_HEADER', 'cc/sheets/course_header.xml');
define('SHEET_COURSE_SECTIONS_SECTION', 'cc/sheets/course_sections_section.xml');
define('SHEET_COURSE_SECTIONS_SECTION_MODS_MOD', 'cc/sheets/course_sections_section_mods_mod.xml');
define('SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_FORUM', 'cc/sheets/course_modules_mod_forum.xml');
define('SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_LABEL', 'cc/sheets/course_modules_mod_label.xml');
define('SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_RESOURCE', 'cc/sheets/course_modules_mod_resource.xml');
define('SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_QUIZ', 'cc/sheets/course_modules_mod_quiz.xml');
define('SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_QUIZ_QUESTION_INSTANCE', 'cc/sheets/course_modules_mod_quiz_question_instance.xml');
define('SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_QUIZ_FEEDBACK', 'cc/sheets/course_modules_mod_quiz_feedback.xml');
define('SHEET_COURSE_QUESTION_CATEGORIES', 'cc/sheets/course_question_categories.xml');
define('SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY', 'cc/sheets/course_question_categories_question_category.xml');
define('SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION', 'cc/sheets/course_question_categories_question_category_question.xml');
define('SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_MULTIPLE_CHOICE', 'cc/sheets/course_question_categories_question_category_question_multiple_choice.xml');
define('SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_TRUE_FALSE', 'cc/sheets/course_question_categories_question_category_question_true_false.xml');
define('SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_EESAY', 'cc/sheets/course_question_categories_question_category_question_eesay.xml');
define('SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_SHORTANSWER', 'cc/sheets/course_question_categories_question_category_question_shortanswer.xml');
define('SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_ANSWER', 'cc/sheets/course_question_categories_question_category_question_answer.xml');

// CC RESOURCES TYPE ************************************************************************************************** //
define('CC_TYPE_FORUM', 'imsdt_xmlv1p0');
define('CC_TYPE_QUIZ', 'imsqti_xmlv1p2/imscc_xmlv1p0/assessment');
define('CC_TYPE_QUESTION_BANK', 'imsqti_xmlv1p2/imscc_xmlv1p0/question-bank');
define('CC_TYPE_WEBLINK', 'imswl_xmlv1p0');
define('CC_TYPE_WEBCONTENT', 'webcontent');
define('CC_TYPE_ASSOCIATED_CONTENT', 'associatedcontent/imscc_xmlv1p0/learning-application-resource');
define('CC_TYPE_EMPTY', '');

// MOODLE RESOURCES TYPE ********************************************************************************************** //
define('MOODLE_TYPE_FORUM', 'forum');
define('MOODLE_TYPE_QUIZ', 'quiz');
define('MOODLE_TYPE_QUESTION_BANK', 'question_bank');
define('MOODLE_TYPE_RESOURCE', 'resource');
define('MOODLE_TYPE_LABEL', 'label');

// UNKNOWN TYPE ******************************************************************************************************* //
define('TYPE_UNKNOWN', '[UNKNOWN]');

// CC QUESTIONS TYPES ************************************************************************************************* //
define('CC_QUIZ_MULTIPLE_CHOICE', 'cc.multiple_choice.v0p1');
define('CC_QUIZ_TRUE_FALSE', 'cc.true_false.v0p1');
define('CC_QUIZ_FIB', 'cc.fib.v0p1');
define('CC_QUIZ_MULTIPLE_RESPONSE', 'cc.multiple_response.v0p1');
define('CC_QUIZ_PATTERN_MACHT', 'cc.pattern_match.v0p1');
define('CC_QUIZ_ESSAY', 'cc.essay.v0p1');

//MOODLE QUESTIONS TYPES ********************************************************************************************** //
define('MOODLE_QUIZ_MULTIPLE_CHOICE', 'multichoice');
define('MOODLE_QUIZ_TRUE_FALSE', 'truefalse');
define('MOODLE_QUIZ_MULTIANSWER', 'multianswer');
define('MOODLE_QUIZ_MULTIPLE_RESPONSE', 'multichoice');
define('MOODLE_QUIZ_MACHT', 'match');
define('MOODLE_QUIZ_ESSAY', 'essay');
define('MOODLE_QUIZ_SHORTANSWER', 'shortanswer');
