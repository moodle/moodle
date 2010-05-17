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
 * Strings for component 'question', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   question
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['adminreport'] = 'Report on possible problems in your question database.';
$string['availableq'] = 'Available?';
$string['badbase'] = 'Bad base before **: {$a}**';
$string['broken'] = 'This is a "broken link", it points to a nonexistent file.';
$string['byandon'] = 'by <em>{$a->user}</em> on <em>{$a->time}</em>';
$string['cannotcopybackup'] = 'Could not copy backup file';
$string['cannotcreate'] = 'Could not create new entry in question_attempts table';
$string['cannotcreatepath'] = 'Cannot create path: {$a}';
$string['cannotdeletecate'] = 'You can\'t delete that category it is the default category for this context.';
$string['cannotenable'] = 'Question type {$a} cannot be created directly.';
$string['cannotfindcate'] = 'Could not find category record';
$string['cannotfindquestionfile'] = 'Could not find question data file in zip';
$string['cannotgetdsfordependent'] = 'Cannot get the specified dataset for a dataset dependent question! (question: {$a[0]}, datasetitem: {a[1]})';
$string['cannotgetdsforquestion'] = 'Cannot get the specified dataset for a calculated question! (question: {$a})';
$string['cannothidequestion'] = 'Was not able to hide question';
$string['cannotimportformat'] = 'Sorry, importing this format is not yet implemented!';
$string['cannotinsertquestion'] = 'Could not insert new question!';
$string['cannotinsertquestioncatecontext'] = 'Could not insert the new question category {$a->cat} illegal contextid {$a->ctx}';
$string['cannotloadquestion'] = 'Could not load question';
$string['cannotmovequestion'] = 'You can\'t use this script to move questions that have files associated with them from different areas.';
$string['cannotopenforwriting'] = 'Cannot open for writing: {$a}';
$string['cannotpreview'] = 'You can\'t preview these questions!';
$string['cannotretrieveqcat'] = 'Could retrieve question category';
$string['cannotunhidequestion'] = 'Failed to unhide the question.';
$string['cannotunzip'] = 'Could not unzip file.';
$string['cannotwriteto'] = 'Cannot write exported questions to {$a}';
$string['categorycurrent'] = 'Current Category';
$string['categorycurrentuse'] = 'Use This Category';
$string['categorydoesnotexist'] = 'This category does not exist';
$string['categorymoveto'] = 'Save in Category';
$string['clicktoflag'] = 'Click to flag this question';
$string['clicktounflag'] = 'Click to un-flag this question';
$string['contexterror'] = 'You shouldn\'t have got here if you\'re not moving a category to another context.';
$string['copy'] = 'Copy from {$a} and change links.';
$string['created'] = 'Created';
$string['createdby'] = 'Created by';
$string['createdmodifiedheader'] = 'Created / Last Saved';
$string['createnewquestion'] = 'Create a new question ...';
$string['cwrqpfs'] = 'Random questions selecting questions from sub categories.';
$string['cwrqpfsinfo'] = '<p>During the upgrade to Moodle 1.9 we will separate question categories into
different contexts. Some question categories and questions on your site will have to have their sharing
status changed. This is necessary in the rare case that one or more \'random\' questions in a quiz are set up to select from a mixture of
shared and unshared categories (as is the case on this site). This happens when a \'random\' question is set to select
from subcategories and one or more subcategories have a different sharing status to the parent category in which
the random question is created.</p>
<p>The following question categories, from which \'random\' questions in parent categories select questions from,
will have their sharing status changed to the same sharing status as the category with the \'random\' question in
on upgrading to Moodle 1.9. The following categories will have their sharing status changed. Questions which are
affected will continue to work in all existing quizzes until you remove them from these quizzes.</p>';
$string['cwrqpfsnoprob'] = 'No question categories in your site are affected by the \'Random questions selecting questions from sub categories\' issue.';
$string['defaultfor'] = 'Default for {$a}';
$string['defaultinfofor'] = 'The default category for questions shared in context \'{$a}\'.';
$string['deletecoursecategorywithquestions'] = 'There are questions in the question bank associated with this course category. If you proceed, they will be deleted. You may wish to move them first, using the question bank interface.';
$string['disabled'] = 'Disabled';
$string['disterror'] = 'The distribution {$a} caused problems';
$string['donothing'] = 'Don\'t copy or move files or change links.';
$string['editingcategory'] = 'Editing a category';
$string['editingquestion'] = 'Editing a question';
$string['editthiscategory'] = 'Edit this category';
$string['emptyxml'] = 'Unkown error - empty imsmanifest.xml';
$string['enabled'] = 'Enabled';
$string['erroraccessingcontext'] = 'Cannot access context';
$string['errordeletingquestionsfromcategory'] = 'Error deleting questions from category {$a}.';
$string['errorduringpost'] = 'Error occurred during post-processing!';
$string['errorduringpre'] = 'Error occurred during pre-processing!';
$string['errorduringproc'] = 'Error occurred during processing!';
$string['errorduringregrade'] = 'Could not regrade question {$a->qid}, going to state {$a->stateid}.';
$string['errorfilecannotbecopied'] = 'Error: cannot copy file {$a}.';
$string['errorfilecannotbemoved'] = 'Error: cannot move file {$a}.';
$string['errorfileschanged'] = 'Error: files linked to from questions have changed since form was displayed.';
$string['errormanualgradeoutofrange'] = 'The grade {$a->grade} is not between 0 and {$a->maxgrade} for question {$a->name}. The score and comment have not been saved.';
$string['errormovingquestions'] = 'Error while moving questions with ids {$a}.';
$string['errorpostprocess'] = 'Error occurred during post-processing!';
$string['errorpreprocess'] = 'Error occurred during pre-processing!';
$string['errorprocess'] = 'Error occurred during processing!';
$string['errorprocessingresponses'] = 'An error occurred while processing your responses.';
$string['errorsavingcomment'] = 'Error saving the comment for question {$a->name} in the database.';
$string['errorupdatingattempt'] = 'Error updating attempt {$a->id} in the database.';
$string['exportcategory'] = 'Export category';
$string['exporterror'] = 'Errors occur during exporting!';
$string['filesareacourse'] = 'the course files area';
$string['filesareasite'] = 'the site files area';
$string['filestomove'] = 'Move / copy files to {$a}?';
$string['flagged'] = 'Flagged';
$string['flagthisquestion'] = 'Flag this question';
$string['formquestionnotinids'] = 'Form contained question that is not in questionids';
$string['fractionsnomax'] = 'One of the answers should have a score of 100% so it is possible to get full marks for this question.';
$string['getcategoryfromfile'] = 'Get category from file';
$string['getcontextfromfile'] = 'Get context from file';
$string['changepublishstatuscat'] = '<a href="{$a->caturl}">Category "{$a->name}"</a> in course "{$a->coursename}" will have it\'s sharing status changed from <strong>{$a->changefrom} to {$a->changeto}</strong>.';
$string['chooseqtypetoadd'] = 'Choose a question type to add';
$string['ignorebroken'] = 'Ignore broken links';
$string['impossiblechar'] = 'Impossible character {$a} detected as parenthesis character';
$string['invalidarg'] = 'No valid arguments supplied or incorrect server configuration';
$string['invalidcategoryidforparent'] = 'Invalid category id for parent!';
$string['invalidcategoryidtomove'] = 'Invalid category id to move!';
$string['invalidconfirm'] = 'Confirmation string was incorrect';
$string['invalidcontextinhasanyquestions'] = 'Invalid context passed to question_context_has_any_questions.';
$string['invalidwizardpage'] = 'Incorrect or no wizard page specified!';
$string['lastmodifiedby'] = 'Last modified by';
$string['linkedfiledoesntexist'] = 'Linked file {$a} doesn\'t exist';
$string['makechildof'] = 'Make Child of \'{$a}\'';
$string['maketoplevelitem'] = 'Move to top level';
$string['missingcourseorcmid'] = 'Need to provide courseid or cmid to print_question.';
$string['missingcourseorcmidtolink'] = 'Need to provide courseid or cmid to get_question_edit_link.';
$string['missingimportantcode'] = 'This question type is missing important code: {$a}.';
$string['missingoption'] = 'The cloze question {$a} is missing its options';
$string['modified'] = 'Last saved';
$string['move'] = 'Move from {$a} and change links.';
$string['movecategory'] = 'Move Category';
$string['movedquestionsandcategories'] = 'Moved questions and question categories from {$a->oldplace} to {$a->newplace}.';
$string['movelinksonly'] = 'Just change where links point to, do not move or copy files.';
$string['moveq'] = 'Move question(s)';
$string['moveqtoanothercontext'] = 'Move question to another context.';
$string['movingcategory'] = 'Moving Category';
$string['movingcategoryandfiles'] = 'Are you sure you want to move category {$a->name} and all child categories to context for "{$a->contextto}"?<br /> We have detected {$a->urlcount} files linked from questions in {$a->fromareaname}, would you like to copy or move these to {$a->toareaname}?';
$string['movingcategorynofiles'] = 'Are you sure you want to move category "{$a->name}" and all child categories to context for "{$a->contextto}"?';
$string['movingquestions'] = 'Moving Questions and Any Files';
$string['movingquestionsandfiles'] = 'Are you sure you want to move question(s) {$a->questions} to context for <strong>"{$a->tocontext}"</strong>?<br /> We have detected <strong>{$a->urlcount} files</strong> linked from these question(s) in {$a->fromareaname}, would you like to copy or move these to {$a->toareaname}?';
$string['movingquestionsnofiles'] = 'Are you sure you want to move question(s) {$a->questions} to context for <strong>"{$a->tocontext}"</strong>?<br /> There are <strong>no files</strong> linked from these question(s) in {$a->fromareaname}.';
$string['needtochoosecat'] = 'You need to choose a category to move this question to or press \'cancel\'.';
$string['nocate'] = 'No such category {$a}!';
$string['nopermissionadd'] = 'You don\'t have permission to add questions here.';
$string['nopermissionmove'] = 'You don\'t have permission to move questions from here. You must save the question in this category or save it as a new question.';
$string['noprobs'] = 'No problems found in your question database.';
$string['notenoughdatatoeditaquestion'] = 'Neither a question id, nor a category id and question type, was specified.';
$string['notenoughdatatomovequestions'] = 'You need to provide the question ids of questions you want to move.';
$string['notflagged'] = 'Not flagged';
$string['novirtualquestiontype'] = 'No virtual question type for question type {$a}';
$string['parenthesisinproperclose'] = 'Parenthesis before ** is not properly closed in {$a}**';
$string['parenthesisinproperstart'] = 'Parenthesis before ** is not properly started in {$a}**';
$string['permissionedit'] = 'Edit this question';
$string['permissionmove'] = 'Move this question';
$string['permissionsaveasnew'] = 'Save this as a new question';
$string['permissionto'] = 'You have permission to :';
$string['published'] = 'shared';
$string['qtypeveryshort'] = 'T';
$string['questionaffected'] = '<a href="{$a->qurl}">Question "{$a->name}" ({$a->qtype})</a> is in this question category but is also being used in <a href="{$a->qurl}">quiz "{$a->quizname}"</a> in another course "{$a->coursename}".';
$string['questionbank'] = 'Question bank';
$string['questioncategory'] = 'Question category';
$string['questioncatsfor'] = 'Question Categories for \'{$a}\'';
$string['questiondoesnotexist'] = 'This question does not exist';
$string['questionname'] = 'Question name';
$string['questionsaveerror'] = 'Errors occur during saving question - ({$a})';
$string['questionsmovedto'] = 'Questions still in use moved to "{$a}" in the parent course category.';
$string['questionsrescuedfrom'] = 'Questions saved from context {$a}.';
$string['questionsrescuedfrominfo'] = 'These questions (some of which may be hidden) were saved when context {$a} was deleted because they are still used by some quizzes or other activities.';
$string['questiontype'] = 'Question type';
$string['questionuse'] = 'Use question in this activity';
$string['saveflags'] = 'Save the state of the flags';
$string['selectacategory'] = 'Select a category:';
$string['selectaqtypefordescription'] = 'Select a question type to see its description.';
$string['selectquestionsforbulk'] = 'Select questions for bulk actions';
$string['shareincontext'] = 'Share in context for {$a}';
$string['tofilecategory'] = 'Write category to file';
$string['tofilecontext'] = 'Write context to file';
$string['unknown'] = 'Unknown';
$string['unknownquestiontype'] = 'Unknown question type: {$a}.';
$string['unknowntolerance'] = 'Unknown tolerance type {$a}';
$string['unpublished'] = 'unshared';
$string['upgradeproblemcategoryloop'] = 'Problem detected when upgrading question categories. There is a loop in the category tree. The affected category ids are {$a}.';
$string['upgradeproblemcouldnotupdatecategory'] = 'Could not update question category {$a->name} ({$a->id}).';
$string['upgradeproblemunknowncategory'] = 'Problem detected when upgrading question categories. Category {$a->id} refers to parent {$a->parent}, which does not exist. Parent changed to fix problem.';
$string['wrongprefix'] = 'Wrongly formatted nameprefix {$a}';
$string['youmustselectaqtype'] = 'You must select a question type.';
$string['yourfileshoulddownload'] = 'Your export file should start to download shortly. If not, please <a href="{$a}">click here</a>.';
