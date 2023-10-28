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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Rabea de Groot, Anna Heynkes, Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

$string['actiondropdown'] = "Options";
$string['activities'] = 'Activities';
$string['addAComment'] = 'Add a comment';
$string['add_protected_comment'] = 'Add a comment to private question';
$string['add_private_comment'] = 'Add a comment to personal note';
$string['ago'] = '{$a} ago';
$string['all'] = 'all';
$string['allanswers'] = 'all';
$string['all_answers'] = 'All answers';
$string['allquestions'] = 'all';
$string['all_questions'] = 'All questions';
$string['allquestionsimgtitle'] = "Show all questions in this document";
$string['allquestionstitle'] = 'All questions in';
$string['allreports'] = 'all reports';
$string['annotationDeleted'] = 'Annotation has been deleted';
$string['anonymous'] = 'Anonymous';
$string['answer'] = 'Answer';
$string['answerButton'] = 'Answer';
$string['answercounthelpicon'] = 'Number of answers';
$string['answercounthelpicon_help'] = 'This column tells you how many answers a question has received.';
$string['answers'] = 'answers';
$string['answerSolved'] = 'This answer was marked as correct by the manager.';
$string['answerstab'] = 'Answers';
$string['answerstabicon'] = 'Answers';
$string['answerstabicon_help'] = "This page can show you all answers or only answers to questions you subscribed* to. The list covers all annotators in this course.<br>*When you post a question yourself, you are automatically subscribed to it as long as you don't unsubscribe.";
$string['author'] = 'Author';
$string['average'] = 'average';
$string['average_answers'] = 'Average answers';
$string['average_help'] = 'Only users who wrote at least one comment are included in the calculation of the average (arithmetic mean)';
$string['average_questions'] = 'Average questions';

$string['by'] = 'by';
$string['by_other_users'] = 'by other users';
$string['bynameondate'] = 'by {$a->name} - {$a->date}';

$string['cancelButton'] = 'Cancel';
$string['chart_title'] = 'Questions and answers in the annotators in this course';
$string['clicktoopen2'] = 'Click {$a} link to view the file.';
$string['closedquestions'] = 'solved';
$string['colorPicker'] = 'Pick a color';
$string['comment'] = 'Comment';
$string['commentDeleted'] = 'Comment has been deleted';
$string['comments'] = 'Comments';
$string['configmaxbytes'] = 'Maximum file size';
$string['correct'] = 'correct';
$string['count'] = 'count';
$string['createAnnotation'] = 'Create Annotation';
$string['currentPage'] = 'current page number';

$string['day'] = 'day';
$string['days'] = 'days';
$string['decision:overlappingAnnotation'] = 'You clicked an area, in which is more than one annotation. Decide which one you wanted to click.';
$string['decision'] = 'Make a decision';
$string['delete'] = 'Delete';
$string['deleteComment'] = 'Delete comment';
$string['deletedComment'] = 'deleted comment';
$string['deletedQuestion'] = 'deleted question';
$string['deletingAnnotation_manager'] = 'The annotation and all corresponding comments will be deleted.';
$string['deletingAnnotation_student'] = "The annotation and all corresponding comments will be deleted.<br>You may delete your own annotations as long as they haven't been commented by other users.";
$string['deletingComment'] = 'The comment will be deleted. It will be displayed as deleted unless it is the last comment in its thread.';
$string['deletingCommentTitle'] = 'Are you sure?';
$string['deletingQuestion_manager'] = 'The comment will be deleted.<br>Hint: If you want to delete all answers as well, delete the annotation in the document.';
$string['deletingQuestion_student'] = 'The question will be deleted.<br>If it is not answered, the annotation will be deleted too, otherwise the question will be displayed as deleted';
$string['deletionForbidden'] = 'Deletion not allowed';
$string['didyouknow'] = 'Did you know?';
$string['dnduploadpdfannotator'] = 'Create file for PDF Annotation';
$string['document'] = 'Document';
$string['drawing'] = 'Draw in the document with the pen.';

$string['edit'] = 'Edit';
$string['editAnnotation'] = 'The annotation will be moved. <br>This might change the context of the question.';
$string['editAnnotationTitle'] = 'Are you sure?';
$string['editButton'] = 'Save';
$string['editedComment'] = 'last edited';
$string['editNotAllowed'] = 'Panning not allowed!';
$string['emptypdf'] = 'There are no comments in this annotator at present.';
$string['enterText'] = 'Enter text';
$string['entity_helptitle'] = 'Help for';
$string['error:addAnnotation'] = 'An error has occurred while adding an annotation.';
$string['error:addComment'] = 'An error has occurred while adding the comment.';
$string['error:closequestion'] = 'An error has occurred while closing/opening the question.';
$string['error:deleteAnnotation'] = 'An error has occurred while deleting an annotation.';
$string['error:editAnnotation'] = 'An error has occurred while editing an annotation.';
$string['error:editcomment'] = 'An error has occurred while trying to edit a comment.';
$string['error:findimage'] = 'An error occurred while trying to find image {$a}.';
$string['error:forwardquestion'] = 'An error has occurred while forwarding the question.';
$string['error:forwardquestionnorecipient'] = 'An error has occurerd while forwarding the question.: No person in this course has the capability to receive forwarded questions.';
$string['error:getAllQuestions'] = 'An error has occurred while getting the questions of this document.';
$string['error:getAnnotation'] = 'An error has occurred while getting the annotation.';
$string['error:getAnnotations'] = 'An error has occurred while getting all annotations.';
$string['error:getComments'] = 'An error has occurred while getting the comments.';
$string['error:getimageheight'] = 'An error has occurred while getting image height of {$a}.';
$string['error:getimagewidth'] = 'An error has occurred while getting image width of {$a}.';
$string['error:getQuestions'] = 'An error has occurred while getting the questions for this page.';
$string['error:hideComment'] = "An error has occurred while trying to hide the comment from participants' view.";
$string['error:markasread'] = 'The item could not be marked as read.';
$string['error:markasunread'] = 'The item could not be marked as unread.';
$string['error:markcorrectanswer'] = 'An error has occurred while marking the answer as correct.';
$string['error:maximalsizeoffile'] = 'Your file {$a->filename}, because it exceeds {$a->filesize} as the maximum size of files. You can attach file(s) with at most {$a->maxfilesize} to a single comment.';
$string['error:missingAnnotationtype'] = 'Annotationtype does not exists. Possibly the entry in table pdfannotator_annotationtypes is missing.';
$string['error:openingPDF'] = 'An error occurred while opening the PDF file.';
$string['error:openprintview'] = 'An error has occurred while trying to open the pdf in Acrobat Reader.';
$string['error:printcomments'] = 'An error has occurred while trying to open the comments in a pdf.';
$string['error:printcommentsdata'] = 'Error with data from server.';
$string['error:printlatex'] = 'An error has occurred while trying to add a LaTeX formula to the pdf.';
$string['error:redisplayComment'] = 'An error has occurred while redisplaying the comment.';
$string['error:renderPage'] = 'An error has occurred while rendering the page.';
$string['error:reportComment'] = 'An error has occurred while saving the report.';
$string['error:subscribe'] = 'An error has occurred while subscribing to the question.';
$string['error:unsubscribe'] = 'An error has occurred while unsubscribing to the question.';
$string['error:unsupportedextension'] = 'The extension of submitted data is not supported. Please select other extension.';
$string['error:redihideCommentsplayComment'] = 'An error occurred while re-inserting the comment for attendees.';
$string['error:voteComment'] = 'An error has occurred while saving the vote.';
$string['error'] = 'Error!';
$string['eventreport_added'] = 'A comment was reported';

$string['filenotfound'] = 'File not found, sorry.';
$string['forward'] = 'Forward';
$string['forwardedquestionhtml'] = '{$a->sender} forwarded the following question to you: <br /> <br />
        "{$a->questioncontent}" <br /> <br />
        with the message: <br /> <br />
        "{$a->message}" <br /> <br />
        The question is available <a href="{$a->urltoquestion}">here</a>.';
$string['forwardedquestiontext'] = '{$a->sender} forwarded the following question to you:

        "{$a->questioncontent}"

        with the message:

        "{$a->message}"

        The question is available at: {$a->urltoquestion}';
$string['fullscreen'] = 'Fullscreen';
$string['fullscreenBack'] = 'Exit Fullscreen';

$string['global_setting_anonymous'] = 'Allow anonymous posting?';
$string['global_setting_anonymous_desc'] = 'With this option you allow your user to post comments anonymously. This option activates anonymous posting globally';
$string['global_setting_attobuttons'] = 'Atto editor toolbar config';
$string['global_setting_attobuttons_desc'] = 'The list of plugins and the order they are displayed can be configured here. The configuration consists of groups (one per line) followed by the ordered list of plugins for that group. The group is separated from the plugins with an equals sign and the plugins are separated with commas. The group names must be unique and should indicate what the buttons have in common. Button and group names should not be repeated and may only contain alphanumeric characters.';
$string['global_setting_latexapisetting'] = 'LaTeX to PNG API';
$string['global_setting_latexapisetting_desc'] = 'API for converting Latex to PNG for PDF Downloads.<br>
        Note: If you use the Google Chart API, Google will get all formulas in the document if someone chooses to use LaTeX<br>
        If you use the Moodle API, you need a latex, dvips and convert binary installed on your server.
        (See  <a href="https://docs.moodle.org/38/en/TeX_notation_filter">Moodle Documentation</a>)';
$string['global_setting_latexusemoodle'] = 'Internal Moodle API';
$string['global_setting_latexusegoogle'] = 'Google Chart API';
$string['global_setting_use_studentdrawing'] = 'Allow drawings for participants?';
$string['global_setting_use_studentdrawing_desc'] = 'Please note that drawings are anonymous and can neither be commented nor reported.';
$string['global_setting_use_studenttextbox'] = 'Allow textboxes for participants?';
$string['global_setting_use_studenttextbox_desc'] = "Please note that textbox annotations are anonymous and can neither be commented nor reported.";
$string['global_setting_useprint'] = 'Allow save and print?';
$string['global_setting_useprint_comments'] = 'Allow saving/printing comments?';
$string['global_setting_useprint_comments_desc'] = 'Allow participants to save and print the annotations and comments';
$string['global_setting_use_private_comments'] = 'Allow personal notes?';
$string['global_setting_use_private_comments_desc'] = 'Allow participants to write personal annotations and personal notes';
$string['global_setting_use_protected_comments'] = 'Allow private comments?';
$string['global_setting_use_protected_comments_desc'] = 'Allow participants to write private annotations and private comments. Only author and manager can see this comment.';
$string['global_setting_useprint_desc'] = 'Allow participants to save and print the pdf document and its comments';
$string['global_setting_useprint_document'] = 'Allow saving/printing document?';
$string['global_setting_useprint_document_desc'] = 'Allow participants to save and print the pdf document';
$string['global_setting_usevotes'] = 'Allow liking of comments?';
$string['global_setting_usevotes_desc'] = 'With this option users can like / vote for posts other than their own.';

$string['hiddenComment'] = 'hidden comment';
$string['hiddenforparticipants'] = 'Hidden from students';
$string['hideAnnotations'] = 'Hide Annotations';
$string['highlight'] = 'Highlight text and add a comment.';
$string['hour'] = 'hour';
$string['hours'] = 'hours';

$string['in_course'] = 'in this course';
$string['in_document'] = 'in this document';
$string['infonocomments'] = "This document contains no comments at present.";
$string['iscorrecthelpicon'] = 'Correct';
$string['iscorrecthelpicon_help'] = 'When a teacher or manager has marked an answer as correct, a green check mark appears next to it.';
$string['itemsperpage'] = 'Items per page';

$string['justnow'] = 'just now';

$string['lastanswered'] = 'Last Answer';
$string['lastedited'] = 'last edited';
$string['legacyfiles'] = 'Migration of old course file';
$string['legacyfilesactive'] = 'Active';
$string['legacyfilesdone'] = 'Finished';
$string['like'] = 'like';
$string['likeAnswer'] = 'helpful';
$string['likeAnswerForbidden'] = 'already marked as helpful';
$string['likeCountAnswer'] = 'persons think this answer is helpful';
$string['likeCountQuestion'] = 'persons are also interested in this question';
$string['likeForbidden'] = 'You are not allowed to like this comment';
$string['likeOwnComment'] = 'own comment';
$string['likeQuestion'] = 'interesting question';
$string['likeQuestionForbidden'] = 'already marked as helpful';
$string['loading'] = 'Loading!';

$string['markasread'] = 'Mark as read';
$string['markasunread'] = 'Mark as unread';
$string['markCorrect'] = 'Mark as correct';
$string['markhidden'] = 'Hide';
$string['markSolved'] = 'Close question';
$string['markUnsolved'] = 'Reopen question';
$string['maximumfilesize'] = 'Maximum file size';
$string['maximumfilesize_help'] = 'Files uploaded by users may be up to this size.';
$string['me'] = 'me';
$string['messageforwardform'] = 'Your message to the recipient/s';
$string['messageprovider:forwardedquestion'] = 'When a question was forwarded to you';
$string['messageprovider:newanswer'] = 'When a question you subscribed to was answered';
$string['messageprovider:newquestion'] = 'When a new question was asked';
$string['messageprovider:newreport'] = 'When a comment was reported';
$string['min0Chars'] = 'An empty question or comment is not allowed.';
$string['minute'] = 'minute';
$string['minutes'] = 'minutes';
$string['missingAnnotation'] = 'The corresponding annotation could not be found!';
$string['modifiedby'] = 'by';
$string['modulename'] = 'PDF Annotation';
$string['modulename_help'] = 'This Tool enables collaborative markup on PDF Documents. The users are able to annotate specific parts of an PDF and discuss them with other users.';
$string['modulename_link'] = 'mod/pdfannotator/view';
$string['modulenameplural'] = 'PDF Annotations';
$string['month'] = 'month';
$string['months'] = 'months';
$string['myanswers'] = 'My answers';
$string['mypost'] = 'My post';
$string['myprivate'] = 'My personal notes';
$string['myprotectedanswers'] = 'My private answers';
$string['myprotectedquestions'] = 'My private questions';
$string['mypublicanswers'] = 'My public answers';
$string['mypublicquestions'] = 'My public questions';
$string['myquestion'] = 'Question';
$string['myquestions'] = 'My questions';

$string['newanswerhtml'] = 'Your subscribed question "{$a->question}" was answered by {$a->answeruser} with the comment: <br /> <br /> "{$a->content}"<br /><br />
The answer is <a href="{$a->urltoanswer}">here</a> available.';
$string['newanswertext'] = 'Your subscribed question "{$a->question}" was answered by {$a->answeruser} with the comment:

    "{$a->content}"

The answer is available under: {$a->urltoanswer}';
$string['newquestionhtml'] = 'A new Questions was added by {$a->answeruser} with the content: <br /> <br /> "{$a->content}"<br /><br />
The question is <a href="{$a->urltoanswer}">hier</a> available.';
$string['newquestions'] = 'Recently asked';
$string['newquestiontext'] = 'A new Questions was added by {$a->answeruser} with the content:

    "{$a->content}"

The question is available under: {$a->urltoanswer}';
$string['nextPage'] = 'Next page';
$string['noanswers'] = 'There are no answers in this course at present.';
$string['noanswerssubscribed'] = 'There are no answers to subscribed questions in this course at present.';
$string['noCommentsupported'] = 'This kind of annotation does not support comments.';
$string['nomyposts'] = 'You have posted no question or answer in this course yet.';
$string['noquestions'] = 'No questions on this page!';
$string['noquestions_overview'] = 'There are no questions in this course at present.';
$string['noquestions_view'] = 'There are no questions in this document at present.';
$string['noquestionsclosed_overview'] = 'There are no closed questions in this course at present.';
$string['noquestionsopen_overview'] = 'There are no open questions in this course at present.';
$string['noreadreports'] = 'There are no read reports in this course at present.';
$string['noreports'] = 'There are no reports in this course at present.';
$string['nosearchresults'] = 'No search results found.';
$string['notificationsubject:forwardedquestion'] = 'Forwarded question in {$a}';
$string['notificationsubject:newanswer'] = 'New answer to subscribed question in {$a}';
$string['notificationsubject:newquestion'] = 'New question in {$a}';
$string['notificationsubject:newreport'] = 'A comment was reported in {$a}';
$string['nounreadreports'] = 'There are no unread reports in this course at present.';

$string['on'] = 'on';
$string['onlyDeleteOwnAnnotations'] = ", because it belongs to another user.";
$string['onlyDeleteUncommentedPosts'] = ", because the other users comments would be deleted as well.";
$string['openquestions'] = 'unsolved';
$string['overview'] = 'Overview';
$string['overviewactioncolumn'] = 'Manage';
$string['ownpoststab'] = 'My posts';
$string['ownpoststabicon'] = 'My posts';
$string['ownpoststabicon_help'] = 'This page displays all comments that you posted in this course.';

$string['page'] = 'page';
$string['pdfannotator:addinstance'] = 'add instance';
$string['pdfannotator:administrateuserinput'] = 'Administrate comments';
$string['pdfannotator:closeanyquestion'] = 'Close any question';
$string['pdfannotator:closequestion'] = 'Close own questions';
$string['pdfannotator:create'] = 'Create annotations and comments';
$string['pdfannotator:deleteany'] = 'Delete any annotation and comment';
$string['pdfannotator:deleteown'] = 'Delete your own annotations and comments';
$string['pdfannotator:edit'] = 'Edit your own annotations and comments';
$string['pdfannotator:editanypost'] = 'Edit any annotation and comment';
$string['pdfannotator:forwardquestions'] = 'Forward questions';
$string['pdfannotator:getforwardedquestions'] = 'Receive forwarded questions';
$string['pdfannotator:hidecomments'] = 'Hide comments for participants';
$string['pdfannotator:markcorrectanswer'] = 'Mark answers as correct';
$string['pdfannotator:printcomments'] = 'Download the comments (even if the option is disabled for a PDF-Annotator)';
$string['pdfannotator:printdocument'] = 'Download the document (even if the option is disabled for a PDF-Annotator)';
$string['pdfannotator:recievenewquestionnotifications'] = 'Recieve notifications about new questions';
$string['pdfannotator:report'] = 'Report inappropriate comments to the course manager';
$string['pdfannotator:seehiddencomments'] = 'See hidden comments';
$string['pdfannotator:subscribe'] = 'Subscribe to a question';
$string['pdfannotator:usedrawing'] = 'Use drawing (even if the option is disabled for a PDF-Annotator)';
$string['pdfannotator:usetextbox'] = 'Use textbox (even if the option is disabled for a PDF-Annotator)';
$string['pdfannotator:view'] = 'View PDF Annotation';
$string['pdfannotator:viewanswers'] = 'View answers to subscribed questions (overview page)';
$string['pdfannotator:viewposts'] = 'View own comments (overview page)';
$string['pdfannotator:viewprotectedcomments'] = 'See private comments';
$string['pdfannotator:viewquestions'] = 'View open questions (overview page)';
$string['pdfannotator:viewreports'] = 'View reported comments (overview page)';
$string['pdfannotator:viewstatistics'] = 'View statistics page';
$string['pdfannotator:viewteacherstatistics'] = 'See additional information on statistics page';
$string['pdfannotator:vote'] = "Vote for an interesting question or helpful answer";
$string['pdfannotator:writeprivatecomments'] = 'Make personal notes';
$string['pdfannotator:writeprotectedcomments'] = 'Write private comments';
$string['pdfannotator'] = 'Document';
$string['pdfannotatorcolumn'] = 'Document';
$string['pdfannotatorcontent'] = 'Files and subfolders';
$string['pdfannotatorname'] = 'PDF Annotation Tool';
$string['pdfannotatorpost'] = 'Comments and questions';
$string['pdfButton'] = 'Document';
$string['pluginadministration'] = 'PDF Annotation administration';
$string['pluginname'] = 'PDF Annotation';
$string['point'] = 'Add a pin in the document and write a comment.';
$string['prevPage'] = 'Previous page';
$string['print'] = 'download document';
$string['printButton'] = 'Download';
$string['printviewtitle'] = 'Comments';
$string['printwithannotations'] = 'download comments';
$string['privacy:metadata:core_files'] = 'The Pdfannotator stores files which have been uploaded by the user as a basis for annotation and discussion.';
$string['privacy:metadata:pdfannotator_annotations:annotationid'] = 'The ID of the annotation that was made. It refers to the data listed above.';
$string['privacy:metadata:pdfannotator_annotations:userid'] = 'The ID of the user who made this annotation.';
$string['privacy:metadata:pdfannotator_annotations'] = "Information about the annotations a user made. This includes the type of annotation (e.g. highlight or drawing), its position within a specific file, as well as the time of creation.";
$string['privacy:metadata:pdfannotator_comments:annotationid'] = 'The ID of the underlying annotation.';
$string['privacy:metadata:pdfannotator_comments:content'] = 'The literal comment.';
$string['privacy:metadata:pdfannotator_comments:userid'] = "The ID of the comment's author.";
$string['privacy:metadata:pdfannotator_comments'] = "Information about a user's comments. This includes the content and time of creation of the comment, as well as the underlying annotation.";
$string['privacy:metadata:pdfannotator_reports:commentid'] = 'The ID of the reported comment.';
$string['privacy:metadata:pdfannotator_reports:message'] = 'The text content of the report.';
$string['privacy:metadata:pdfannotator_reports:userid'] = 'The author of the report.';
$string['privacy:metadata:pdfannotator_reports'] = "Users can report other users' comments as inappropriate. These reports stored. This includes the ID of the reported comment as well as the author, content and time of the report.";
$string['privacy:metadata:pdfannotator_subscriptions:annotationid'] = 'The ID of the question/discussion that was subscribed to.';
$string['privacy:metadata:pdfannotator_subscriptions:userid'] = 'The ID of the user with this subscription.';
$string['privacy:metadata:pdfannotator_subscriptions'] = "Information about the subscriptions to individual questions/discussions.";
$string['privacy:metadata:pdfannotator_votes:commentid'] = "The ID of the comment.";
$string['privacy:metadata:pdfannotator_votes:userid'] = "The ID of the user who marked the comment as interesting or helpful. It is saved in order to prevent users from voting for the same comment repeatedly.";
$string['privacy:metadata:pdfannotator_votes'] = "Information about questions and comments that were marked as interesting or helpful.";
$string['private_comments'] = "Personal notes";
$string['private_comments_help'] = 'Visible only for you.';
$string['protected_answers'] = 'Private answers';
$string['protected_comments'] = "Private comments";
$string['protected_comments_help'] = 'Visible only for you and teachers.';
$string['protected_questions'] = 'Private questions';
$string['publicanswers'] = 'Public answers';
$string['public_comments'] = 'Public comments';
$string['publicquestions'] = 'Public questions';

$string['question'] = 'Question';
$string['questionsimgtitle'] = "Show all questions on this page";
$string['questionSolved'] = 'Questions is closed. However, you can still create new comments.';
$string['questionstab'] = 'Questions';
$string['questionstabicon'] = 'Questions';
$string['questionstabicon_help'] = 'This page displays all unsolved questions that were asked in this course. You can also choose to see all or all solved questions in this course.';
$string['questionstitle'] = 'Questions on page';

$string['read'] = 'Read';
$string['reason'] = 'Explanation';
$string['recievenewquestionnotifications'] = 'Notify about new questions';
$string['recipient'] = 'Recipient/s';
$string['recipient_help'] = 'To select several persons, hold down "Ctrl"';
$string['recipientforwardform'] = 'Forward to';
$string['recipientrequired'] = 'Please select recipient/s';
$string['rectangle'] = 'Add a Rectangle in the document and write a comment.';
$string['removeCorrect'] = 'Remove marking as correct';
$string['removehidden'] = 'Show';
$string['report'] = 'Report';
$string['reportaddedhtml'] = '{$a->reportinguser} has reported a comment with the message: <br /><br /> "{$a->introduction}"<br /><br />
It is <a href="{$a->urltoreport}">available on the web site</a>.';
$string['reportaddedtext'] = '{$a->reportinguser} has reported a comment with the message:

    "{$a->introduction}"

It is available under: {$a->urltoreport}';
$string['reportedby'] = 'by / on';
$string['reportedcomment'] = 'Reported comment';
$string['reports'] = 'Reported comments';
$string['reportsendbutton'] = 'Send';
$string['reportstab'] = 'Reported comments';
$string['reportstabicon'] = 'Reported comments';
$string['reportstabicon_help'] = 'This page displays comments that were reported as inappropriate in this course. You can choose to see only unread/read* reports or all reports.<br>* Any manager of this course can mark a report as read.';
$string['reportwassentoff'] = 'The comment has been reported.';

$string['search'] = 'Search';
$string['searchresults'] = 'Search results';
$string['second'] = 'second';
$string['seconds'] = 'seconds';
$string['seeabove'] = '';
$string['seenreports'] = 'read only';
$string['send'] = 'Send';
$string['sendAnonymous'] = 'post anonymous';
$string['sendPrivate'] = 'post personal note';
$string['sendProtected'] = 'post private comment';
$string['setting_alternative_name'] = 'Name';
$string['setting_alternative_name_desc'] = 'Provide an alternative name for the PDF. If empty, the name of the pdf will be taken as representative name';
$string['setting_alternative_name_help'] = "If the name is more than 20 characters long, the remaining characters will be replaced with '...' in the annotator's internal tab navigation.";
$string['setting_anonymous'] = 'Allow anonymous posting?';
$string['setting_fileupload'] = 'Select a pdf-file';
$string['setting_fileupload_help'] = "You can only change the selected file until the annotator has been created by a click on 'Save'.";
$string['setting_use_studentdrawing'] = "Drawing";
$string['setting_use_studentdrawing_help'] = "Allow participants to save and print the pdf document without annotations or comments";
$string['setting_use_studenttextbox'] = "Textbox";
$string['setting_use_studenttextbox_help'] = "Please note that textbox annotations are not anonymous and can neither be commented nor reported.";
$string['setting_useprint'] = "save and print";
$string['setting_useprint_comments'] = 'Save and print comments';
$string['setting_useprint_comments_help'] = 'Allow participants to save and print the annotations and comments';
$string['setting_useprint_document'] = 'Save and print pdf document';
$string['setting_useprint_document_help'] = 'Allow participants to save and print the pdf document';
$string['setting_useprint_help'] = "Please note that drawings are not anonymous and can neither be commented nor reported.";
$string['setting_use_private_comments'] = "Allow personal notes";
$string['setting_use_private_comments_help'] = "Allow participants to write personal notes. Other person cannot see this comment.";
$string['setting_use_protected_comments'] = "Allow private comments";
$string['setting_use_protected_comments_help'] = "Allow participants to write private comments. Only the author and teachers can see this comment.";
$string['setting_usevotes'] = "Votes/Likes";
$string['setting_usevotes_help'] = "With this option enabled, users can like / vote for posts other than their own.";
$string['show'] = 'Show';
$string['showAnnotations'] = 'Show Annotations';
$string['showless'] = 'less';
$string['showmore'] = 'more';
$string['slotdatetimelabel'] = 'Date and time';
$string['startDiscussion'] = 'Start a discussion';
$string['statistic'] = 'Statistics';
$string['strftimedatetime'] = '%d %b %Y, %I:%M %p';
$string['strikeout'] = 'Strikeout text and add a comment.';
$string['studentdrawingforbidden'] = 'This annotator does not support drawings for your user role.';
$string['studenttextboxforbidden'] = 'This annotator does not support textboxes for your user role.';
$string['subscribe'] = 'Subscribe to this Annotations';
$string['subscribed'] = 'Subscribed';
$string['subscribedanswers'] = 'to my subscribed questions';
$string['subscribeQuestion'] = 'Subscribe';
$string['subtitleforreportcommentform'] = 'Your message for the course manager';
$string['successfullyEdited'] = 'Changes saved';
$string['successfullyHidden'] = 'Participants now see this comment as hidden.';
$string['successfullymarkedasread'] = 'The report was marked as read.';
$string['successfullymarkedasreadandnolongerdisplayed'] = 'The report was marked as read and removed from the table.';
$string['successfullymarkedasunread'] = 'The report was marked as unread.';
$string['successfullymarkedasunreadandnolongerdisplayed'] = 'The report was marked as unread and removed from the table.';
$string['successfullyRedisplayed'] = 'The comment is visible to participants once more';
$string['successfullySubscribed'] = 'Subscribed to question.';
$string['successfullySubscribed'] = 'Your subscription to the question was registered.';
$string['successfullyUnsubscribed'] = 'Your subscribtion was cancelled.';
$string['successfullyUnsubscribedPlural'] = 'Your subscribtion was cancelled. All {$a} answers to the question were removed from this table.';
$string['successfullyUnsubscribedSingular'] = 'Your subscribtion to the question was cancelled and the only answer removed from this table.';
$string['successfullyUnsubscribedTwo'] = 'Your subscribtion was cancelled. Both answers to the question were removed from this table.';
$string['sumPages'] = 'Number of pages';

$string['text'] = 'Add a text in the document.';
$string['titleforreportcommentform'] = 'Report comment';
$string['titleforwardform'] = 'Forward question';
$string['toreport'] = 'Report';

$string['unseenreports'] = 'unread only';
$string['unsolvedquestionstitle'] = 'Unsolved Questions';
$string['unsolvedquestionstitle_help'] = 'All unsolved questions in this course are listed.';
$string['unsubscribe'] = 'Unsubscribe from this Annotations';
$string['unsubscribe_notification'] = 'To unsubscribe from notification, please click <a href="{$a}">here</a>.';
$string['unsubscribeQuestion'] = 'Unsubscribe';
$string['unsubscribingDidNotWork'] = 'The subscription could not be cancelled.';
$string['use_studentdrawing'] = "Enable drawing for participants?";
$string['use_studenttextbox'] = "Enable textbox tool for participants?";
$string['useprint'] = "Give participants access to the PDF?";
$string['useprint_comments'] = "Give participants access to the PDF and its comments?";
$string['use_private_comments'] = "Allow participants to write personal notes?";
$string['use_protected_comments'] = "Allow participants to write private comments?";
$string['useprint_document'] = "Give participants access to the PDF?";
$string['usevotes'] = "Allow users to like comments.";

$string['view'] = 'Document';
$string['votes'] = 'Likes';
$string['voteshelpicon'] = 'Likes';
$string['voteshelpicon_help'] = 'This column tells you how many other people take an interest in the question.';
$string['voteshelpicontwo'] = 'Likes';
$string['voteshelpicontwo_help'] = 'This column tells you how often your posts were <em>liked</em>.';

$string['week'] = 'week';
$string['weeks'] = 'weeks';

$string['year'] = 'year';
$string['years'] = 'years';
$string['yesButton'] = 'Yes';

$string['zoom'] = 'zoom';
$string['zoomin'] = 'zoom in';
$string['zoomout'] = 'zoom out';
