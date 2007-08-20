<?php // $Id$
// question.php - created with Moodle 1.8 dev

$string['adminreport'] = 'Report on possible problems in your question database.';
$string['categorydoesnotexist'] = 'This category does not exist';
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
$string['editingquestion'] = 'Editing a question';
$string['fractionsnomax'] = 'One of the answers should have a score of 100%% so it is possible to get full marks for this question.';
$string['missingimportantcode'] = 'This question type is missing important code: $a.';
$string['noprobs'] = 'No problems found in your question database.';
$string['notenoughdatatoeditaquestion'] = 'Neither a question id, nor a category id and question type, was specified.';
$string['published'] = 'shared';
$string['questionbank'] = 'Question bank';
$string['questiondoesnotexist'] = 'This question does not exist';
$string['unknownquestiontype'] = 'Unknown question type: $a.';
$string['unpublished'] = 'unshared';

?>
