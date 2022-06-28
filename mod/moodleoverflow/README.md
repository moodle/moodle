# ![moodle-mod_groupmembers](pix/icon.png) Activity Module: Moodleoverflow

[![Build Status](https://travis-ci.org/learnweb/moodle-mod_moodleoverflow.svg?branch=master)](https://travis-ci.org/learnweb/moodle-mod_moodleoverflow)

This plugin enables Moodle users to create a non-linear, non-chronologic discussion forum.
The plugin has similar features as the Moodle forum, but is not intended for general discussions, but rather for straightforward question-and-answer discussions.
Additionally, users can rate posts and can gain a rating score ("reputation") by being rated by other users.
Users who have started a discussion can mark a post as helpful; and teachers can mark a post as a (correct) solution to the posed question.

This plugin is developed by Kennet Winter, [Tamara Gunkel](https://github.com/TamaraGunkel), and [Jan Dageförde](https://github.com/Dagefoerde) 
and is maintained by Learnweb (University of Münster).

## Installation
This plugin should go into `mod/mooodleoverflow`. Upon installation, several default settings need to be defined that pre-configure future instances of this activity (see [Settings](#settings)).

## Rating
If a post is rated up or down, the post owner's rating score increases or decreases. The rating score of a user is always shown after the user name.
In the settings you can define what amount of reputation a downvote or upvote gives. 
Posts with a high score are displayed further up than posts with a lower score. 
If a post is marked as helpful or solved, the post owner's rating score also increases. By default, a mark gives a higher amount of reputation than an upvote.
A marked post is always displayed first, but you can choose which mark (solved or helpful) is more important.

## Screenshots
Moodleoverflow activity:<br><br>
<img src="https://user-images.githubusercontent.com/432117/31946828-26b0b968-b8d3-11e7-9f99-e1434d7a60d8.png" width="500">
<br><br>

Every user can see the discussions and the posts. 
The discussion overview shows the status, among other things. Thus users can see if a discussion is already solved (green tick) or if a post is marked as helpful (orange tick).
<br><br>
<img src="https://user-images.githubusercontent.com/432117/31946826-26981d72-b8d3-11e7-9773-b6547ea9276f.png" width="500">
<br><br>
Posts can be marked as helpful (orange) by the question owner or as solved (green) by a teacher. The current post is marked blue.
Additionally, everybody can vote posts up or down. The post are ordered by the number of upvotes. The post owner's reputation increases if the post is upvoted and decreases it the post is downvoted.
Post owners can edit their posts until 30 minutes after posting. Teachers can edit and delete posts from everybody without the time restriction.
<br><br>
<img src="https://user-images.githubusercontent.com/432117/31946825-267c07d6-b8d3-11e7-8ae1-4f86ea375fd5.png" width="500">
<br><br>
Users can attach files. If a picture is attached, it will be displayed as image. If another file type is attached, the file will be shown but not the content.
<br><br>
<img src="https://user-images.githubusercontent.com/432117/31946824-2660a64e-b8d3-11e7-879f-70fc5cd2fc98.png" width="500">
<br><br>
A discussion can be deleted by deleting the first post.

### Students' view
Unlike teachers, students can't edit or delete a post or mark it as solved.
<br><br>
<img src="https://user-images.githubusercontent.com/432117/31946823-2646aece-b8d3-11e7-92d0-49745ada27e3.png" width="500">
<br><br>

## Settings
### Global
In the global settings you can set e.g. the number of discussions per page, the maximum attachment size or read tracking.
In addition to these settings which are the same as in the forum, you can define the amount of reputation a vote or mark gives.
<br><br>
<img src="https://user-images.githubusercontent.com/432117/31946822-262bb664-b8d3-11e7-88fd-1a400864f8aa.png" width="500">
<br><br>

### Course wide
In the course settings you can override a few settings like maximum attachment size or read tracking.
Moreover, you can decide if helpful or solved posts are displayed first and how the reputation is calucated.
<br><br>
<img src="https://user-images.githubusercontent.com/432117/31946820-260d778a-b8d3-11e7-9425-2af44f00e716.png" width="500">
<br><br>
If read tracking is set to "optional" and turned on by the students, the unread posts are highlighted. 
<br><br>
<img src="https://user-images.githubusercontent.com/432117/31946819-25f2e5b4-b8d3-11e7-88b7-97b80f159a2d.png" width="500">
<br><br>

### Students
Depending on the global and course settings students can choose if they want to track posts and receive email notifications.
<br><br>
<img src="https://user-images.githubusercontent.com/432117/31946818-25c4c3aa-b8d3-11e7-88f6-891f1db51618.png" width="500">
<br><br>
