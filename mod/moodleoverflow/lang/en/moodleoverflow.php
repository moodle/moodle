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
 * English strings for moodleoverflow
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Default strings.
$string['modulename']             = 'Moodleoverflow';
$string['modulenameplural']       = 'Moodleoverflows';
$string['modulename_help']        = 'The Moodleoverflow module enables participants to use a question-answer forum structure. The forum display is non-chronological as the ordering depends on collaborative voting instead of on time.';
$string['moodleoverflowfieldset'] = 'Custom example fieldset';
$string['moodleoverflowname']     = 'Moodleoverflow name';
$string['moodleoverflow']         = 'Moodleoverflow';
$string['pluginadministration']   = 'Moodleoverflow administration';
$string['pluginname']             = 'Moodleoverflow';

// Capabilities.
$string['moodleoverflow:addinstance']         = 'Add a new Moodleoverflow instance';
$string['moodleoverflow:allowforcesubscribe'] = 'Allow forced subscription';
$string['moodleoverflow:createattachment']    = 'Create attachments';
$string['moodleoverflow:managesubscriptions'] = 'Manage subscriptions';
$string['moodleoverflow:ratehelpful']         = 'Mark a post as helpful';
$string['moodleoverflow:ratepost']            = 'Rate a post';
$string['moodleoverflow:viewanyrating']       = 'View ratings';
$string['moodleoverflow:deleteanypost']       = 'Delete posts';
$string['moodleoverflow:deleteownpost']       = 'Delete own posts';
$string['moodleoverflow:editanypost']         = 'Edit posts';
$string['moodleoverflow:startdiscussion']     = 'Start a discussion';
$string['moodleoverflow:replypost']           = 'Reply in discussion';
$string['moodleoverflow:viewdiscussion']      = 'View discussion';
$string['moodleoverflow:view']                = 'View discussionlist';
$string['moodleoverflow:marksolved']          = 'Mark a post as solution';
$string['nowallsubscribed']                   = 'All forums in {$a} are subscribed.';
$string['nowallunsubscribed']                 = 'All forums in {$a} are unsubscribed.';

// Strings for the view.php.
$string['noviewdiscussionspermission'] = 'You do not have the permission to view discussions in this forum';

// Strings for the locallib.php.
$string['addanewdiscussion']    = 'Add a new discussion topic';
$string['nodiscussions']        = 'There are no discussion topics yet in this forum.';
$string['headerdiscussion']     = 'Discussion';
$string['headerstartedby']      = 'Started by';
$string['headerreplies']        = 'Replies';
$string['headerlastpost']       = 'Last post';
$string['headerunread']         = 'Unread';
$string['headervotes']          = 'Votes';
$string['headerstatus']         = 'Status';
$string['markallread']          = 'Mark read';
$string['markallread']          = 'Mark all posts in this discussion read.';
$string['delete']               = 'Delete';
$string['parent']               = 'Show parent';
$string['markread']             = 'Mark read';
$string['markunread']           = 'Mark unread';
$string['permalink']            = 'Permalink';
$string['postbyuser']           = '{$a->post} by {$a->user}';
$string['bynameondate']         = 'by {$a->name} ({$a->rating}) - {$a->date}';
$string['bynameondatenorating'] = 'by {$a->name} - {$a->date}';
$string['deletesure']           = 'Are you sure you want to delete this post?';
$string['deletesureplural']     = 'Are you sure you want to delete this post and all replies? ({$a} posts)';

// Strings for the settings.php.
$string['configmanydiscussions']     = 'Maximum number of discussions shown in a Moodleoverflow instance per page';
$string['manydiscussions']           = 'Discussions per page';
$string['maxattachmentsize']         = 'Maximum attachment size';
$string['maxattachmentsize_help']    = 'This setting specifies the largest size of file that can be attached to a forum post.';
$string['configmaxbytes']            = 'Default maximum size for all forum attachments on the site (subject to course limits and other local settings)';
$string['maxattachments']            = 'Maximum number of attachments';
$string['maxattachments_help']       = 'This setting specifies the maximum number of files that can be attached to a forum post.';
$string['configmaxattachments']      = 'Default maximum number of attachments allowed per post.';
$string['maxeditingtime']            = 'Maximum amount of time during which a post can be edited by its owner (sec)';
$string['configmaxeditingtime']      = 'Default maximum seconds are 3600 (= one hour).';
$string['configoldpostdays']         = 'Number of days old any post is considered read.';
$string['oldpostdays']               = 'Read after days';
$string['trackingoff']               = 'Off';
$string['trackingon']                = 'Forced';
$string['trackingoptional']          = 'Optional';
$string['trackingtype']              = 'Read tracking';
$string['configtrackingtype']        = 'Default setting for read tracking.';
$string['trackmoodleoverflow']       = 'Track unread posts';
$string['configtrackmoodleoverflow'] = 'Set to \'yes\' if you want to track read/unread for each user.';
$string['forcedreadtracking']        = 'Allow forced read tracking';
$string['configforcedreadtracking']  = 'Allows Moodleoverflows to be set to forced read tracking. Will result in decreased performance for some users, particularly on courses with many moodleoverflows and posts. When off, any moodleoverflows previously set to Forced are treated as optional.';
$string['cleanreadtime']             = 'Mark old posts as read hour';
$string['configcleanreadtime']       = 'The hour of the day to clean old posts from the \'read\' table.';
$string['allowdisablerating']        = 'Allow teachers to disable rating and reputation';
$string['configallowdisablerating']  = 'Set to \'yes\' if you want to give teachers the ability to disable rating and reputation.';

$string['votescalevote']               = 'Reputation: Vote.';
$string['configvotescalevote']         = 'The amount of reputation voting gives.';
$string['votescaledownvote']           = 'Reputation: Downvote';
$string['configvotescaledownvote']     = 'The amount of reputation a downvote for your post gives.';
$string['votescaleupvote']             = 'Reputation: Upvote';
$string['configvotescaleupvote']       = 'The amount of reputation an upvote for your post gives.';
$string['votescalesolved']             = 'Reputation: Solution';
$string['configvotescalesolved']       = 'The amount of reputation a mark as solution on your post gives.';
$string['votescalehelpful']            = 'Reputation: Helpful';
$string['configvotescalehelpful']      = 'The amount of reputation a mark as helpful on your post gives.';
$string['reputationnotnegative']       = 'Reputation just positive?';
$string['configreputationnotnegative'] = 'Prohibits the users reputation being negative.';
$string['allowcoursereputation']       = 'Sum reputation within a course.';
$string['configallowcoursereputation'] = 'Allow to sum the reputation of all instances of the current course?';
$string['maxmailingtime']              = 'Maximal mailing time';
$string['configmaxmailingtime'] = 'Posts older than this number of hours will not be mailed to the users. This will help to avoid problems where the cron has not been running for a long time.';

// Strings for the post.php.
$string['invalidmoodleoverflowid'] = 'Forum ID was incorrect';
$string['invalidparentpostid']     = 'Parent post ID was incorrect';
$string['notpartofdiscussion']     = 'This post is not part of a discussion!';
$string['noguestpost']             = 'Sorry, guests are not allowed to post.';
$string['nopostmoodleoverflow']    = 'Sorry, you are not allowed to post to this forum.';
$string['yourreply']               = 'Your reply';
$string['re']                      = 'Re:';
$string['invalidpostid']           = 'Invalid post ID - {$a}';
$string['cannotfindparentpost']    = 'Could not find top parent of post {$a}';
$string['edit']                    = 'Edit';
$string['cannotreply']             = 'You cannot reply to this post';
$string['cannotcreatediscussion']  = 'Could not create new discussion';
$string['couldnotadd']             = 'Could not add your post due to an unknown error';
$string['postaddedsuccess']        = 'Your post was successfully added.';
$string['postaddedtimeleft']       = 'You have {$a} to edit it if you want to make any changes.';
$string['cannotupdatepost']        = 'You can not update this post';
$string['couldnotupdate']          = 'Could not update your post due to an unknown error';
$string['editedpostupdated']       = '{$a}\'s post was updated';
$string['postupdated']             = 'Your post was updated';
$string['editedby']                = 'Edited by {$a->name} - original submission {$a->date}';
$string['cannotdeletepost']        = 'You can\'t delete this post!';
$string['couldnotdeletereplies']   = 'Sorry, that cannot be deleted as people have already responded to it';
$string['errorwhiledelete']        = 'An error occurred while deleting record.';
$string['couldnotdeletereplies']   = 'Sorry, that cannot be deleted as people have already responded to it';

// Strings for the classes/mod_form.php.
$string['subject']                     = 'Subject';
$string['reply']                       = 'Comment';
$string['replyfirst']                  = 'Answer';
$string['message']                     = 'Message';
$string['discussionsubscription']      = 'Discussion subscription';
$string['discussionsubscription_help'] = 'Subscribing to a discussion means you will receive notifications of new posts to that discussion.';
$string['posttomoodleoverflow']        = 'Post to forum';
$string['posts'] = 'Posts';
$string['erroremptysubject']           = 'Post subject cannot be empty.';
$string['erroremptymessage']           = 'Post message cannot be empty';
$string['yournewtopic']                = 'Your new discussion topic';

// Strings for the classes/ratings.php.
$string['postnotexist']             = 'Requested post does not exist';
$string['noratemoodleoverflow']     = 'Sorry, you are not allowed to vote in this forum.';
$string['configallowratingchange']  = 'Can a user change its ratings?';
$string['allowratingchange']        = 'Allow rating changes';
$string['configpreferteachersmark'] = 'The answer marked as solution by a course owner are prioritized over the answer marked as helpful by the starter of the discussion.';
$string['preferteachersmark']       = 'Prefer course owners\' marks?';
$string['noratingchangeallowed']    = 'You are not allowed to change your ratings.';
$string['invalidratingid']          = 'The submitted rating is neither an upvote nor a downvote.';
$string['notstartuser']             = 'Only the user who started the discussion can mark an answer as helpful.';
$string['notteacher']               = 'Only course owners can do this.';
$string['ratingtoold']              = 'Ratings can only be changed within 30 minutes after the first vote. ';

// Strings for the discussion.php.
$string['invaliddiscussionid']         = 'Discussion ID was incorrect';
$string['notexists']                   = 'Discussion no longer exists';
$string['discussionname']              = 'Discussion name';
$string['discussionlocked']            = 'This discussion has been locked so you can no longer reply to it.';
$string['hiddenmoodleoverflowpost']    = 'Hidden forum post';
$string['moodleoverflowsubjecthidden'] = 'Subject (hidden)';
$string['moodleoverflowauthorhidden']  = 'Author (hidden)';
$string['moodleoverflowbodyhidden']    = 'This post cannot be viewed by you, probably because you have not posted in the discussion, the maximum editing time hasn\'t passed yet, the discussion has not started or the discussion has expired.';
$string['addanewreply']                = 'Add a new answer';
$string['ratingfailed']                = 'Rating failed. Try again.';
$string['rateownpost']                 = 'You cannot rate your own post.';
$string['marksolved']                  = 'Mark as solution';
$string['marknotsolved']               = 'Remove solution mark';
$string['markhelpful']                 = 'Mark as Helpful';
$string['marknothelpful']              = 'Not Helpful';
$string['answer']                      = '{$a} Answer';
$string['answers']                     = '{$a} Answers';

// Strings for the readtracking.php.
$string['markreadfailed']                   = 'A post of the discussion could not be marked as read.';
$string['markdiscussionreadsuccessful']     = 'The discussion has been marked as read.';
$string['markmoodleoverflowreadsuccessful'] = 'All posts have been marked as read.';
$string['noguesttracking']                  = 'Sorry, guests are not allowed to set tracking options.';

// OTHER.
$string['messageprovider:posts']        = 'Notification of new posts';
$string['unknownerror']                 = 'This is not expected to happen.';
$string['crontask']                     = 'Moodleoverflow maintenance jobs';
$string['taskcleanreadrecords']         = 'Moodleoverflow maintenance job to clean old read records';
$string['tasksendmails']                = 'Moodleoverflow maintenance job to send mails';
$string['nopermissiontosubscribe']      = 'You do not have the permission to view subscribers';
$string['subscribeenrolledonly']        = 'Sorry, only enrolled users are allowed to subscribe to post notifications.';
$string['everyonecannowchoose']         = 'Everyone can now choose to be subscribed';
$string['noonecansubscribenow']         = 'Subscriptions are now disallowed';
$string['invalidforcesubscribe']        = 'Invalid force subscription mode';
$string['nownotsubscribed']             = '{$a->name} will NOT be notified of new posts in \'{$a->moodleoverflow}\'';
$string['cannotunsubscribe']            = 'Could not unsubscribe you from that forum';
$string['discussionnownotsubscribed']   = '{$a->name} will NOT be notified of new posts in \'{$a->discussion}\' of \'{$a->moodleoverflow}\'';
$string['disallowsubscribe']            = 'Subscriptions not allowed';
$string['noviewdiscussionspermission']  = 'You do not have the permission to view discussions in this forum';
$string['nowsubscribed']                = '{$a->name} will be notified of new posts in \'{$a->moodleoverflow}\'';
$string['discussionnowsubscribed']      = '{$a->name} will be notified of new posts in \'{$a->discussion}\' of \'{$a->moodleoverflow}\'';
$string['unsubscribe']                  = 'Unsubscribe from this forum';
$string['subscribe']                    = 'Subscribe to this forum';
$string['confirmunsubscribediscussion'] = 'Do you really want to unsubscribe from discussion \'{$a->discussion}\' in moodleoverflow \'{$a->moodleoverflow}\'?';
$string['confirmunsubscribe']           = 'Do you really want to unsubscribe from moodleoverflow \'{$a}\'?';
$string['confirmsubscribediscussion']   = 'Do you really want to subscribe to discussion \'{$a->discussion}\' in forum \'{$a->moodleoverflow}\'?';
$string['confirmsubscribe']             = 'Do you really want to subscribe to forum \'{$a}\'?';
$string['postmailsubject']              = '{$a->courseshortname}: {$a->subject}';
$string['smallmessage']                 = '{$a->user} posted in {$a->moodleoverflowname}';
$string['moodleoverflows']              = 'Moodleoverflows';
$string['postmailinfolink']             = 'This is a copy of a message posted in {$a->coursename}.

To reply click on this link: {$a->replylink}';
$string['unsubscribelink']              = 'Unsubscribe from this forum: {$a}';
$string['unsubscribediscussionlink']    = 'Unsubscribe from this discussion: {$a}';
$string['postincontext']                = 'See this post in context';
$string['unsubscribediscussion']        = 'Unsubscribe from this discussion';
$string['nownottracking']               = '{$a->name} is no longer tracking \'{$a->moodleoverflow}\'.';
$string['nowtracking']                  = '{$a->name} is now tracking \'{$a->moodleoverflow}\'.';
$string['cannottrack']                  = 'Could not stop tracking that forum';
$string['notrackmoodleoverflow']        = 'Don\'t track unread posts';
$string['trackmoodleoverflow']          = 'Track unread posts';
$string['discussions']                  = 'Discussions';
$string['subscribed']                   = 'Subscribed';
$string['unreadposts']                  = 'Unread posts';
$string['unreadpostsnumber']            = '{$a} unread posts';
$string['unreadpostsone']               = '1 unread post';
$string['tracking']                     = 'Track';
$string['allsubscribe']                 = 'Subscribe to all forums';
$string['allunsubscribe']               = 'Unsubscribe from all forums';
$string['generalmoodleoverflows']       = 'Forums in this course';
$string['subscribestart']               = 'Send me notifications of new posts in this forum';
$string['subscribestop']                = 'I don\'t want to be notified of new posts in this forum';
$string['everyoneisnowsubscribed']      = 'Everyone is now subscribed to this forum';
$string['everyoneissubscribed']         = 'Everyone is subscribed to this forum';
$string['mailindexlink']                = 'Change your forum preferences: {$a}';
$string['gotoindex']                    = 'Manage preferences';
$string['areaattachment']               = 'Attachments';
$string['areapost']                     = 'Messages';


// EVENTS.
$string['eventdiscussioncreated']             = 'Discussion created';
$string['eventdiscussiondeleted']             = 'Discussion deleted';
$string['eventdiscussionviewed']              = 'Discussion viewed';
$string['eventratingcreated']                 = 'Rating created';
$string['eventratingupdated']                 = 'Rating updated';
$string['eventratingdeleted']                 = 'Rating deleted';
$string['eventpostcreated']                   = 'Post created';
$string['eventpostupdated']                   = 'Post updated';
$string['eventpostdeleted']                   = 'Post deleted';
$string['eventdiscussionsubscriptioncreated'] = 'Discussion subscription created';
$string['eventdiscussionsubscriptiondeleted'] = 'Discussion subscription deleted';
$string['eventsubscriptioncreated']           = 'Subscription created';
$string['eventsubscriptiondeleted']           = 'Subscription deleted';
$string['eventreadtrackingdisabled']          = 'Read tracking disabled';
$string['eventreadtrackingenabled']           = 'Read tracking enabled';


$string['subscriptiontrackingheader']   = 'Subscription and tracking';
$string['subscriptionmode']             = 'Subscription mode';
$string['subscriptionmode_help']        = 'When a participant is subscribed to a forum it means they will receive forum post notifications. There are 4 subscription mode options:

* Optional subscription - Participants can choose whether to be subscribed
* Forced subscription - Everyone is subscribed and cannot unsubscribe
* Auto subscription - Everyone is subscribed initially but can choose to unsubscribe at any time
* Subscription disabled - Subscriptions are not allowed

Note: Any subscription mode changes will only affect users who enrol in the course in the future, and not existing users.';
$string['subscriptionoptional']         = 'Optional subscription';
$string['subscriptionforced']           = 'Forced subscription';
$string['subscriptionauto']             = 'Auto subscription';
$string['subscriptiondisabled']         = 'Subscription disabled';
$string['trackingoff']                  = 'Off';
$string['trackingon']                   = 'Forced';
$string['trackingoptional']             = 'Optional';
$string['trackingtype']                 = 'Read tracking';
$string['trackingtype_help']            = 'Read tracking enables participants to easily check which posts they have not yet seen by highlighting any new posts.

If set to optional, tracking is turned on by default but participants can turn tracking off.

If \'Allow forced read tracking\' is enabled in the site administration, then a further option is available - forced. This means that tracking is always on.';
$string['ratingheading']                = 'Rating and reputation';
$string['starterrating']                = 'Helpful';
$string['teacherrating']                = 'Solution';
$string['ratingpreference']             = 'Display first';
$string['ratingpreference_help']        = 'Answers can be marked as solution and helpful. This option decides which of these will be pinned as the first answer of the discussion. There are 2 options:

* Heplful - A topic starter\'s helpful mark will be pinned at the top of the discussion
* Solved - A teacher\'s solution mark will be pinned at the top of the discussion';
$string['allowrating']                  = 'Allow post ratings?';
$string['allowrating_help']             = 'If set to yes, users can up or downvote a post to give the reader an idea of how helpful the post was to other people. If set to no, ratings will be disabled.';
$string['allowreputation']              = 'Allow user reputation?';
$string['allowreputation_help']         = 'If set to yes, the users can gain or lose reputation depending on other users voting on their posts. If set to no, user reputation will be disabled.';
$string['allownegativereputation']      = 'Allow negative reputation?';
$string['allownegativereputation_help'] = 'If set to yes, the users reputation within a course or within a module can be negative. If set to no, the reputation will stop to decrease at zero.';
$string['coursewidereputation']         = 'Cross module reputation?';
$string['coursewidereputation_help']    = 'If set to yes, the users reputations of all moodleoverflow modules in this course will be summed.';
$string['clicktounsubscribe']           = 'You are subscribed to this discussion. Click to unsubscribe.';
$string['clicktosubscribe']             = 'You are not subscribed to this discussion. Click to subscribe.';
$string['attachment']                   = 'Attachment';
$string['attachments']                  = 'Attachments';
$string['attachment_help']              = 'You can optionally attach one or more files to a forum post. If you attach an image, it will be displayed after the message.';

// Templates.
$string['helpfulanswer'] = 'The question owner accepted this as the best answer.';
$string['solvedanswer']  = 'This post is marked as solution.';
$string['bestanswer']    = 'The question owner and a course owner accepted this as the best answer.';
$string['reputation'] = 'Reputation';
$string['upvote'] = 'Upvote';
$string['upvotenotchangeable'] = 'Upvote (not changeable)';
$string['noupvote'] = 'No upvote';
$string['downvote'] = 'Downvote';
$string['downvotenotchangeable'] = 'Downvote (not changeable)';
$string['nodownvote'] = 'No downvote';

// Privacy.
$string['privacy:metadata:core_files'] = 'Moodleoverflow stores files which have been uploaded by the user to form part of a forum post.';
$string['privacy:metadata:moodleoverflow_discussions'] = 'Information about forum discussions. This includes which discussions a user has started.';
$string['privacy:metadata:moodleoverflow_discussions:name'] = 'The name of the discussion.';
$string['privacy:metadata:moodleoverflow_discussions:userid'] = 'The ID of the user who started the discussion.';
$string['privacy:metadata:moodleoverflow_discussions:timemodified'] = 'The time when the discussion (e.g. a post) was last modified.';
$string['privacy:metadata:moodleoverflow_discussions:usermodified'] = 'The ID of the last user who modified the discussion';

$string['privacy:metadata:moodleoverflow_posts'] = 'Information about forum posts. This includes data of posts a user has written.';
$string['privacy:metadata:moodleoverflow_posts:discussion'] = 'The ID of the discussion this post is contributing to.';
$string['privacy:metadata:moodleoverflow_posts:parent'] = 'The ID of the post this post is referring to.';
$string['privacy:metadata:moodleoverflow_posts:userid'] = 'The ID of the user who submitted this post.';
$string['privacy:metadata:moodleoverflow_posts:created'] = 'The date this post was created.';
$string['privacy:metadata:moodleoverflow_posts:modified'] = 'The last date this post was modified.';
$string['privacy:metadata:moodleoverflow_posts:message'] = 'The text of this post.';

$string['privacy:metadata:moodleoverflow_read'] = 'Information about read tracking of posts. This includes when posts were read by a user.';
$string['privacy:metadata:moodleoverflow_read:userid'] = 'The ID of the user who read the post.';
$string['privacy:metadata:moodleoverflow_read:discussionid'] = 'The ID of the discussion the read post belongs to,';
$string['privacy:metadata:moodleoverflow_read:postid'] = 'The ID of the post that has been read.';
$string['privacy:metadata:moodleoverflow_read:firstread'] = 'The date the post was read the first time.';
$string['privacy:metadata:moodleoverflow_read:lastread'] = 'The date the post was read the last time by the user.';

$string['privacy:metadata:moodleoverflow_subscriptions'] = 'Information about subscriptions to forums. This includes which forums a user has subscribed.';
$string['privacy:metadata:moodleoverflow_subscriptions:userid'] = 'The ID of the user who has subscribed a forum.';
$string['privacy:metadata:moodleoverflow_subscriptions:moodleoverflow'] = 'The ID of the Moodleoverflow forum the user has subscribed.';

$string['privacy:metadata:moodleoverflow_discuss_subs'] = 'Information about the subscriptions to individual forum discussions. This includes when a user has chosen to subscribe to a discussion or to unsubscribe from one where they would otherwise be subscribed.';
$string['privacy:metadata:moodleoverflow_discuss_subs:userid'] = 'The ID of the user who changed the subscription settings.';
$string['privacy:metadata:moodleoverflow_discuss_subs:discussion'] = 'The ID of the discussion that was subscribed / unsubscribed.';
$string['privacy:metadata:moodleoverflow_discuss_subs:preference'] = 'The start time of the subscription.';

$string['privacy:metadata:moodleoverflow_ratings'] = 'Information about ratings of posts. This includes when a user has rated a post and its specific rating.';
$string['privacy:metadata:moodleoverflow_ratings:userid'] = 'The ID of the user who submitted the rating.';
$string['privacy:metadata:moodleoverflow_ratings:postid'] = 'The ID of the post that was rated.';
$string['privacy:metadata:moodleoverflow_ratings:discussionid'] = 'The ID of the discussion the rated post is part of.';
$string['privacy:metadata:moodleoverflow_ratings:moodleoverflowid'] = 'The ID of the Moodleoverflow forum that contains the rated post.';
$string['privacy:metadata:moodleoverflow_ratings:rating'] = 'The submitted rating. 0 = neutral, 1 = negative, 2 = positive, 3 = helpful, 4 = solution';
$string['privacy:metadata:moodleoverflow_ratings:firstrated'] = 'The date the rating was submitted.';
$string['privacy:metadata:moodleoverflow_ratings:lastchanged'] = 'The date the rating was changed the last time.';

$string['privacy:metadata:moodleoverflow_tracking'] = 'Information about the tracking of forums. This includes which forums a user does not track.';
$string['privacy:metadata:moodleoverflow_tracking:userid'] = 'The ID of the user who does not track the forum.';
$string['privacy:metadata:moodleoverflow_tracking:moodleoverflowid'] = 'The ID of the moodleoverflow forum that is not tracked by the user.';

$string['privacy:metadata:moodleoverflow_grades'] = 'Information about the grade a user got for his contribution in a forum.';
$string['privacy:metadata:moodleoverflow_grades:userid'] = 'The ID of the user who got the grade.';
$string['privacy:metadata:moodleoverflow_grades:moodleoverflowid'] = 'The ID of the moodleoverflow forum in which he got the grade.';
$string['privacy:metadata:moodleoverflow_grades:grade'] = 'The grade the user got.';

$string['privacy:anonym_discussion_name'] = 'Anonymized discussion name';
$string['privacy:anonym_post_message'] = 'This content has been deleted.';
$string['privacy:anonym_user_name'] = 'Anonymous';

$string['privacy:subscribedtoforum'] = 'You are subscribed to this forum.';
$string['privacy:discussionsubscriptionpreference'] = 'You have chosen the following discussion subscription preference for this forum: "{$a->preference}"';
$string['privacy:readtrackingdisabled'] = 'You have chosen to not track which posts that you have read within this forum.';
$string['privacy:postwasread'] = 'This post was first read on {$a->firstread} and most recently read on {$a->lastread}';
$string['privacy:grade'] = 'Your grade for this Moodleoverflow forum.';

$string['scalefactor'] = 'Scale factor';
$string['scalefactor_help'] = 'The user rating is divided by the scale factor to obtain each user\'s grade. If the resulting grade is greater than the maximum grade, the value is limited to the specified maximum grade';
$string['scalefactorerror'] = 'Scale factor must be a positive integer different than 0';
$string['grademaxgradeerror'] = 'Maximum grade must be a positive integer different than 0';
$string['updategrades'] = 'Update grades';
$string['gradesreport'] = 'Grades report';
$string['gradesupdated'] = 'Grades updated';
$string['taskupdategrades'] = 'Moodleoverflow maintenance job to update grades';

// Anonymous Feature
$string['anonymous'] = 'Anonymous';
$string['anonymous_help'] = 'This will hide username from all question (and answers).<br>WARNING: Once the questions (and answers) are anonymized, this cannot be reversed.<br>The setting can only be changed to a higher degree of anonymity.';
$string['anonymous:only_questions'] = 'Only questioners (Irreversible!)';
$string['anonymous:everything'] = 'Questioners and answerers (Irreversible!)';
$string['anonym_you'] = 'Anonymous (You)';
$string['allowanonymous'] = 'Allow anonymous';
$string['allowanonymous_desc'] = 'Allow teachers to put moodleoverflow forums into anonymous question or full anonymous mode';
$string['questioner'] = 'Questioner';
$string['answerer'] = 'Answerer #{$a}';
$string['desc:only_questions'] = 'The name of questioners will not be displayed in their question and comments.';
$string['desc:anonymous'] = 'No names will be displayed.';
