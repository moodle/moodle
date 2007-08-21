<?php // $Id$
// question.php - created with Moodle 1.8 dev

$string['adminreport'] = 'Report on possible problems in your question database.';
$string['broken'] = 'This is a \"broken link\", it points to a nonexistent file.';
$string['byandon'] = 'by <em>$a->user</em> on <em>$a->time</em>';
$string['categorydoesnotexist'] = 'This category does not exist';
$string['categorycurrent'] = 'Current Category';
$string['categorycurrentuse'] = 'Use This Category';
$string['categorymoveto'] = 'Save in Category';
$string['changepublishstatuscat'] = '<a href=\"$a->caturl\">Category \"$a->name\"</a> in course \"$a->coursename\" will have it\'s sharing status changed from <strong>$a->changefrom to $a->changeto</strong>.';
$string['cwrqpfs'] = 'Random questions selecting questions from sub categories.';
$string['cwrqpfsinfo'] = '<p>During the upgrade to Moodle 1.9 we will seperate question categories into
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
$string['copy']= 'Copy from $a and change links.';
$string['created'] = 'Created';
$string['createdmodifiedheader'] = 'Created / Last Saved';
$string['defaultfor'] = 'Default for $a';
$string['defaultinfofor'] = 'The default category for questions shared in context \'$a\'.';
$string['donothing']= 'Don\'t copy or move files or change links.';
$string['editingcategory'] = 'Editing a category';
$string['editingquestion'] = 'Editing a question';
$string['erroraccessingcontext'] = 'Cannot access context';
$string['errorfilecannotbecopied'] = 'Error cannot copy file $a.';
$string['errorfilecannotbemoved'] = 'Error cannot move file $a.';
$string['errorfileschanged'] = 'Error files linked to from questions have changed since form was displayed.';
$string['exportcategory'] = 'Export category';
$string['filesareasite']= 'the site files area';
$string['filesareacourse']= 'the course files area';
$string['filestomove']= 'Move / copy files to $a?';
$string['fractionsnomax'] = 'One of the answers should have a score of 100%% so it is possible to get full marks for this question.';
$string['getcategoryfromfile'] = 'Get category from file';
$string['getcontextfromfile'] = 'Get context from file';
$string['ignorebroken'] = 'Ignore broken links';
$string['linkedfiledoesntexist'] = 'Linked file $a doesn\'t exist';
$string['makechildof'] = "Make Child of '\$a'";
$string['maketoplevelitem'] = 'Move to top level';
$string['missingimportantcode'] = 'This question type is missing important code: $a.';
$string['modified'] = 'Last saved';
$string['move']= 'Move from $a and change links.';
$string['movecategory']= 'Move Category';
$string['movelinksonly']= 'Just change where links point to, do not move or copy files.';
$string['moveqtoanothercontext']= 'Move question to another context.';
$string['moveq']= 'Move question(s)';
$string['movingcategory']= 'Moving Category';
$string['movingcategoryandfiles']= 'Are you sure you want to move category {$a->name} and all child categories to context for \"{$a->contextto}\"?<br /> We have detected {$a->urlcount} files linked from questions in {$a->fromareaname}, would you like to copy or move these to {$a->toareaname}?';
$string['movingcategorynofiles']= 'Are you sure you want to move category \"{$a->name}\" and all child categories to context for \"{$a->contextto}\"?';
$string['movingquestions'] = 'Moving Questions and Any Files';
$string['movingquestionsandfiles']= 'Are you sure you want to move question(s) {$a->questions} to context for <strong>\"{$a->tocontext}\"</strong>?<br /> We have detected <strong>{$a->urlcount} files</strong> linked from these question(s) in {$a->fromareaname}, would you like to copy or move these to {$a->toareaname}?';
$string['movingquestionsnofiles']=  'Are you sure you want to move question(s) {$a->questions} to context for <strong>\"{$a->tocontext}\"</strong>?<br /> There are <strong>no files</strong> linked from these question(s) in {$a->fromareaname}.';
$string['needtochoosecat'] = 'You need to choose a category to move this question to or press \'cancel\'.';
$string['nopermissionadd'] = 'You don\'t have permission to add questions here.';
$string['noprobs'] = 'No problems found in your question database.';
$string['notenoughdatatoeditaquestion'] = 'Neither a question id, nor a category id and question type, was specified.';
$string['notenoughdatatomovequestions'] = 'You need to provide the question ids of questions you want to move.';
$string['permissionedit'] = 'Edit this question';
$string['permissionmove'] = 'Move this question';
$string['permissionsaveasnew'] = 'Save this as a new question';
$string['permissionto'] = 'You have permission to :';
$string['published'] = 'shared';
$string['questionaffected'] = '<a href=\"$a->qurl\">Question \"$a->name\" ($a->qtype)</a> is in this question category but is also being used in <a href=\"$a->qurl\">quiz \"$a->quizname\"</a> in another course \"$a->coursename\".';
$string['questionbank'] = 'Question bank';
$string['questioncatsfor'] = 'Question Categories for \'$a\'';
$string['questiondoesnotexist'] = 'This question does not exist';
$string['questionuse'] = 'Use question in this activity';
$string['shareincontext'] = 'Share in context for $a';
$string['tofilecategory'] = 'Write category to file';
$string['tofilecontext'] = 'Write context to file';
$string['unknown'] = 'Unknown';
$string['unknownquestiontype'] = 'Unknown question type: $a.';
$string['unpublished'] = 'unshared';
?>
