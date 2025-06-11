### Date:       2025-March-05
### Release:    v2025030501

---

#### Quiz Attempt Issue Fixed
An issue was identified which affected the ability of users to view student submissions to quizzes in Moodle 4.1. This has now been fixed.

---

### Date:       2024-December-19
### Release:    v2024121901

---

#### Removed ETS e-rater Service
As we have now discontinued support for the ETS e-rater service, these settings have now been removed from the plugin.

### Collusion Check Bug Fixed
An issue was found which in some cases affected the order in which submissions would be indexed, leading to collusion checks potentially being carried out out of order. This has now been resolved.

---

### Date:       2024-December-05
### Release:    v2024120501

---

#### Extra Classes No Longer Created in Turnitin
Previously classes would be created in Turnitin for some Moodle assignments even if the plagiarism plugin was disabled for those assignments. This has now been fixed.

#### Course Backups Fixed
An issue with database collation was causing course backup and deletion to fail for some users. This has now been resolved.

#### Cron Handling of Large Files Improved
Previously there was an issue with our cron task which would occasionally cause the server to run out of memory when sending a high number of submissions to Turnitin. This has now been resolved.

#### Error Files No Longer Stuck in Submission Queue
Previously there was an issue which would cause some files flagged with errors to be stuck in the submission queue. This would eventually cause the queue to fill and prevent other submissions from being sent. This issue has now been fixed.

#### Migration to AMD Modules
We have migrated our javascript code to use AMD modules. This should help reduce loading times for Moodle sites.

#### Unnecessary Database Reads Removed
We have increased the efficiency of our database reads during cron task runs.

#### Local Role Assignment Implemented
We now fully support assignment of roles at the activity module level. Similarity scores should now appear as expected for participants locally assigned the role of Teacher or above.

#### Error Checking For Large Files
Previously there was an issue when a file over 100MB was attempted to be sent to Turnitin which would cause further submissions not to be sent. This issue has now been resolved and we now handle this error correctly.

#### Similarity Scores Now Displayed For Forum Topics
We fixed an error that was preventing similarity scores from being displayed in forum topics.

#### Similarity Scores Now Displaying For Forum Posts Containing Images
Previously adding an image to a forum submission could cause the similarity score for that submission to fail to be displayed. This issue has now been resolved.

#### Fixed Database Export for Moodle 4.4+
The Database Export tab is now working as expected in Moodle versions 4.4+.

#### Error 404 Fixed
Previously there was an error where a javascript module would be loaded incorrectly on the assignment settings page, causing an error 404 to display. This has now been fixed.

#### Detaching Rubrics Fixed
Previously an error prevented detaching of rubrics from assignments. This has now been fixed.

#### Rubric Modal Close Button Fixed
Previously the close button for the rubric modal window was not working. This now closes the window as expected.

---

### Date:       2024-July-24
### Release:    v2024072401

#### :wrench: Fixes and enhancements

---

#### Issue with rubrics fixed
In Moodle 4.3 and 4.4, Turnitin rubrics were not able to be attached for new Turnitin enabled Moodle assignments.  This has now been resolved.

#### Issue with accepting EULA when initially declined fixed
In Turnitin enabled Moodle assignments, when the student declined the Turnitin EULA, they were not presented the screen to upload their assignment to Moodle.  Also in this fix, the second presentation of the EULA for student acceptance which occurred after the student initially the declined the EULA and then successfully uploaded their submission was removed due to the confusion that this caused for the user.  Both of these have now been resolved.

#### Reports now open correctly
There was an issue where in Turnitin enabled Moodle assignments, when the user opened the report, it opened in multiple tabs.  This has now been resolved.

#### Issue with viewing rubric fixed
In Turnitin enabled Moodle assignments, the rubric was not able to be viewed by the students.  This has now been resolved.

#### Userlist provider error message fixed
In Moodle, there was an error message in the plugin privacy compliance registry indicating that the plugin was missing the userlist provider.  This has now been resolved.

---

### Date:       2024-February-05
### Release:    v2024020501

#### Issue with PeerMark, Rubric, and QuickMark in Moodle 4.3 fixed
In the previous plugin release we resolved an issue with the EULA modal not launching for students. It has since been discovered that all modals in the plugin were affected by the same issue. This update resolves the issues that users have found with launching the PeerMark, Rubric, and QuickMark managers.

---

### Date:       2023-December-19
### Release:    v2023121901

#### New students can now access the EULA
Previously there was an issue with students accepting the EULA in Moodle 4.3. This has now been resolved and students presented with the EULA should be able to successfully accept it.

#### Correct rounding of grade average
Previously when multiple submissions were made to an assignment the final grade was averaged by rounding down rather than up. This release now fixes this issue and the grade average should now round up.

---

### Date:       2023-August-22
### Release:    v2023082201

---

#### Report scores should now update as expected

An issue was raised requiring instructors to use the ‘refresh all submissions' button in the Plagiarism Plugin assignment settings, in order to see the Turnitin Similarity score. This has now been resolved and users no longer need to select the 'refresh all submissions’ button to see the Turnitin Similarity score appear in the Moodle assignment inbox when using the task Send Queued Files from the Turnitin Plagiarism Plugin.

#### Core Moodle PhpUnit Test now functioning as expected

A core PhpUnit test was failing due to extra minified JS files. These files have now been removed and the test should no longer fail.
This improvement was submitted as a pull request by @petersistrom on Github. Thanks, Peter!

### Date:	    2023-March-30
### Release:	v2023033001

#### Students that have previously rejected the EULA can now resubmit

We have fixed an issue that was preventing students from being able to resubmit to Turnitin for an assignment where they had previously rejected the EULA. Now if a student accepts the EULA, they will be able to successfully resubmit to any assignments where they previously rejected the EULA.

#### Inbox now displays the highest score when Translated Matching is enabled

We have fixed an issue with the similarity score value in the Moodle Plagiarism Plugin inbox. Previously it wasn't updating with the highest score when Translated Matching was enabled, and instead the inbox continued to display the first score, even if it was the lower of the two.

#### Duplicate quiz responses are now handled separately

We have fixed an issue where if a student submitted the same response multiple times on a quiz, all duplicate responses would be linked to the same report, which would not allow them to be reviewed by an instructor as separate responses. Each response now generates a separate report.

---

### Date:       2022-September-21
### Release:    v2022092101

#### :wrench: Fixes and enhancements

---

#### We’ve fixed an issue that resulted in overwritten grades

Previously, selecting the blue pencil icon to launch a Turnitin assignment from the Moodle assignment would override any grades provided in Moodle. This has now been resolved and grades provided in Moodle will be retained unless a grade is provided in the Turnitin report.

#### We have made improvements to the Rubric manager sizing

Previously, the Rubric manager was sized incorrectly when launched in Moodle 3.11. This issue has now been resolved.

#### We have made improvements to Moodle Quiz handling

An additional check has been added to determine whether Turnitin is enabled on a Moodle Quiz resulting in performance improvements.

With special thanks to [@thepurpleblob](https://github.com/thepurpleblob) for this contribution.

---

### Date:       2022-July-25
### Release:    v2022072501

#### :zap: What's new

---

We’ve had several community members actively contribute towards making the Moodle plagiarism plugin the best it can be, with some insightful and much appreciated pull requests. Check out all the latest improvements, with special thanks to our community members who have contributed towards them.

#### Moodle Quizzes will perform better when many users take a quiz that involves essay-based questions

When viewing a Moodle Quiz results, Turnitin would request the CMID (Course Module ID) multiple times, even after it had already been provided. This could cause classes to see performance issues when a larger class used essay-based questions as a part of their Moodle Quiz. This enhancement fixes the issue and users should expect to see increased performance when using Moodle Quizzes with larger classes now.

With special thanks to [@aolley](https://github.com/aolley) for this contribution.

#### Performance improvements when Turnitin isn’t enabled for a Moodle activity

When Turnitin wasn’t enabled for a Moodle activity, multiple database calls would still be run for each user within the activity, resulting is a lot of unnecessary extra database load. This change removes these checks when Turnitin is not enabled. Users should expect to see some performance enhancements, particularly in larger classes.

With special thanks to [@danmarsden](https://github.com/danmarsden) for this contribution.

#### Database schema is now consistent when upgrading to a new version of the plugin

This change fixes an issue where there was an alignment issue between the install and upgrade scripts, resulting in multiple is mismatches.

With special thanks to [@golenkovm](https://github.com/golenkovm) and [@TomoTsuyuki](https://github.com/TomoTsuyuki) for the fix, and kristian-94 for their thorough initial investigation.

#### Messages sent by Turnitin to instructors and students now apply the Moodle filters

Filters can be used in Moodle to convert or change a message into a richer form of media. This includes creating links, converting mathematical formula into images, and even showing multiple languages at once on screen.

Any messages sent by Turnitin will now work with these filters.

With special thanks to [@izendegi](https://github.com/izendegi) for this contribution.

#### Turnitin EULA prompt will now show at all times when using Moodle Forums

When using a Moodle Forum, making an reply on the same page would not show the prompt to accept the Turnitin EULA for processing. The prompt would still be shown the first time the page is loaded. Users can now expect to see the EULA prompt on each interaction they would do in a forum that would potentially generate a Similarity Report if the EULA was accepted.

With special thanks to [@jonof](https://github.com/jonof) for this contribution.

#### Quiz attempt grades will set correct after opening the Similarity Report

Opening the Similarity Report in a quiz attempt could in some situations alter the calculated grade for a student. User can expect for the Similarity Report to no longer affect the calculated grade.

With special thanks again to [@jonof](https://github.com/jonof) for their contribution.

---

### Date:       2022-March-23
### Release:	2022032301

#### :wrench: Fixes and enhancements

#### Grader field previously not updating correctly - now resolved

We’ve fixed an issue that was occasionally causing the grader field in Moodle assignments to not update correctly.

#### Submission failure relating to Moodle Quiz items resolved

A bug was discovered that was causing submissions to fail. This was found to be linked to Moodle Quiz items. This issue has been resolved.

This fix was submitted as a pull request by [@nwp90](https://github.com/nwp90) on Github. Thanks, Nick!

---

### Date:       2021-September-15
### Release:	2021091501

#### :wrench: Fixes and enhancements

#### Empty grades in Turnitin will not override Moodle grading

When you had completed grading in Moodle and someone opened the Turnitin viewer for the submission, if no grade had been set in Turnitin, the Moodle grade would be lost. Now, the grade within Moodle will be retained and an ungraded Turnitin viewer can be opened without it causing any problems.

Note: If a grade is removed from Turnitin, it will no longer remove it from Moodle as the viewer will then be ‘empty’ and we assume that the Moodle grade is intended.

#### Only the essay type question in a Moodle Quiz will request a Similarity Report 

We’ve refined this feature to only request a Similarity Report for Essay type questions. Turnitin would only ever create a report for these types of questions anyway, but a request was sent for all question types. This could cause bigger classes to become sluggish. You should notice that bigger classes that use a lot of quizzes are much easier to navigate now.

Thanks to Dan Marsden And Alex Morris for highlighting and providing a fix for this.

#### Test connection now will check the connection to Turnitin at all times

Previously, Test Connection wouldn’t work until the plugin was configured. This could potentially cause confusion with users assuming that as no error had been shown the connection must already be established. We’ve changed it so that the Test Connection feature will always look to see if a successful connection has been made when checking.

---

### Date:       2021-August-13
### Release:	2021081301

#### :wrench: Fixes and enhancements

#### Classes will create successfully 
A bug potentially resulted in classes created in Moodle to not sync correctly when we tried to create them in Turnitin. We’ve fixed this problem and you will now be able to create classes again.

---

### Date:       2021-June-08
### Release:	2021060801

#### :wrench: Fixes and enhancements

#### Support for Moodle 3.11
You can now confidently use this integration with Moodle 3.11.

#### Use Turnitin with Moodle Quizzes
We can now check for similarity on any files that are uploaded as a part of a Moodle quiz.

#### Cron tasks will no longer run when Moodle is not configured
Thanks to alexmorrisnz for the pull request!

#### Removed grades change the grade in Moodle
When a grade is removed from Turnitin, this removal will also be reflected within Moodle as the submission being ungraded.

#### Turnitin classes will now be expired along with a Moodle course
We will now sync dates in Moodle with the Turnitin database. When the Moodle course expires, we will close out those classes in Turnitin. This will free up the active student count for those classes.

#### Create or access Moodle assignments, forums, and workshops when Turnitin is disabled
If Turnitin is disabled for an assignment, forum, or workshop and Turnitin is unavailable by having the wrong configuration options or being under maintenance, then an error message would be shown. This bug has been fixed, and Turnitin being unreachable will no longer impact your ability to create or access your content.

#### Changes to the Moodle course name will be reflected everywhere
When a Moodle course title is changed, this will now be updated in Turnitin too. This will include the digital receipts students receive when they submit their paper.

---

### Date:       2020-November-30
### Release:	2020113001

#### :wrench: Fixes and enhancements

Hotfix - removed update to config_hash field 

---

### Date:       2020-November-26
### Release:	2020112601

#### :wrench: Fixes and enhancements

#### Attached rubrics sync with the Moodle assignment

When attaching a rubric via the Turnitin viewer we’ll now sync this with the Moodle assignment so the next time you launch the view it is still viewable.

#### Institutional default settings will now apply in Turnitin and Plagiarism plugin

The option to search the institutional repository will now sync correctly between Turnitin and the Plagiarism plugin allowing you to set this as a default for all your plagiarism plugin assignments.

####Improved logic for get_pseudo_lastname()

Thanks to pauldamiani for bringing this to our attention!

---

### Date:       2020-July-22
### Release:	2020072201

#### :wrench: Fixes and enhancements

#### Plagiarism Plugin settings will only appear once

A bug had caused the Plagiarism Plugin settings to display twice when creating an assignment in Moodle versions prior to 3.9. This has been fixed for all supported Moodle versions. 

---
### Date:       2020-July-07
### Release:	2020070801

#### :zap: What's new

---
#### We now support Moodle 3.9

You can find out more about Moodle 3.9 via Moodle's [release notes](https://docs.moodle.org/dev/Moodle_3.9_release_notes).


#### Use Turnitin in a Moodle Quiz

Turnitin will be usable as a part of a Moodle quiz when Moodle releases the feature. When enabled for your account, simply add an essay question as one of the quiz questions. A similarity report will be generated when the student submits the quiz. Track this release on the Moodle Tracker https://tracker.moodle.org/browse/MDL-32226).

---

#### :wrench: Fixes and enhancements

#### Improved support for large class sizes

When closing the Turnitin viewer, we’d refresh the page so any updates you applied to the assignment would be reflected in Moodle. However, this refresh caused unnecessary load when a class was particularly large. When we identify increased latency like this, we’ll deactivate automatic refreshes of the inbox and prompt you to refresh the inbox manually after grading.

#### View shared rubrics when creating a Moodle assignment

When creating a Moodle assignment it is possible to view all of your Turnitin Rubrics and attach one to a class from its creation. However, a bug had prevented any rubrics that had been shared with you from appearing in this list. You’ll now find them there and ready to be added to any future assignments you create. This fixes a known issue.

#### General accessibility improvements

We’ve made a few accessibility improvements to help ensure that all Turnitin users can use the plugin with the same high-quality experience. These include:

- Made more elements accessible via tab navigation
- You can launch the Turnitin viewer now by using the enter key on the appropriate element. 
- Tools tips are available for various settings and readable by a screen reader or via keyboard navigation.
- General improvements to the plugin configuration screen ensuring unique ARIA labels and logical tab order.

---

### Date:       2019-December-17
### Release:	v2019121701

#### :zap: What's new

---

#### We now support Moodle 3.8

You can find out more about Moodle 3.8 via Moodle's [release notes](https://docs.moodle.org/dev/Moodle_3.8_release_notes).

#### :wrench: Fixes and enhancements

---

#### Anonymous marking is now available again

A bug had prevented the use of Anonymous marking when configuring the integration. This bug has now been fixed and the anonymous marketing will remain enabled when navigating away from the configuration page.

#### Submissions will still display when error code 13 is detected

Error code 13 can appear in various circumstances where a score is not returned to Turnitin. A bug caused submissions with this error code to no longer display in the inbox. While the error code can still occur, it will no longer prevent access to the submission and it will be displayed within the inbox as intended.

---

### Date:       2019-June-25
### Release:	v2019062501

#### :wrench: Fixes and enhancements

---

#### Upgrade issues resolved

There was an issue affecting users trying to upgrade the latest version of Moodle Plagiarism Plugin. It was discovered that users attempting to upgrade to v2019060601 from versions older than v2016011101 weren't able to do so. This latest version will allow for all users to upgrade to the latest version from any of the previous versions.

---

### Date:       2019-June-06
### Release:	v2019060601

#### :zap: What's new

---

#### We now support Moodle 3.7

You can find out more about Moodle 3.7 via Moodle's [release notes](https://docs.moodle.org/dev/Moodle_3.7_release_notes).

#### :wrench: Fixes and enhancements

---

We’ve been doing bug fixing behind the scenes to improve the usability of the Moodle Plagiarism Plugin. 

This includes fixing a bug that was erroneously displaying rubric options when creating a forum in Moodle with the Plagiarism Plugin enabled. This has been resolved and rubric options will no longer be available during forum creation.

Download the latest version from the above link.

---

### Date:       2019-May-02
### Release:    v2019050201

#### :zap: What's new

---

#### Moodle Plagiarism Plugin no longer dependant on Moodle V2

Previously, the Plagiarism Plugin was bundled along with Moodle Direct V2. Now, the Plagiarism Plugin is installed and configured separately. 
     
If you are upgrading from a previous version of the Plagiarism Plugin, your existing configuration will be migrated over from Moodle V2.
     
For more details on how to configure your instance of Moodle Plagiarism Plugin, [please see the guidance](https://help.turnitin.com/feedback-studio/moodle/plagiarism-plugin/administrator/getting-started/step-3-configuring-turnitin-in-moodle.htm).
     
#### Forum messaging error resolved

When creating a forum post within Turnitin, we were showing information about report regeneration times when resubmitting. As information about resubmissions is not relevant to forum users, we’ve removed this notification from this view.

#### Improvements to plugin stability and modals

We’ve improved the stability of the plugin and made changes to our modals (dialog boxes) within Moodle. Modals will now be clearer and adapt to the browser theme that you’re using to improve the usability of the plugin.

---

### Date:       2019-March-13
### Release:    v2019031301

#### :zap: What's new

---

#### Welcome to our new help site!

We’ve updated the help links within the product to take you to our new help site [https://help.turnitin.com](https://help.turnitin.com). The site has been designed around actionable tasks to help you to find the guidance you need when you need it.


### :wrench: Fixes and enhancements

---

#### Error code 13 will now only display when appropriate

In a previous release, we introduced ‘Error Code 13’. This error is shown when we’ve managed to upload to our servers but weren’t able to generate a similarity report at the time of submission. Since then, we’ve reconsidered how this error state should be handled. With this release, we will reset all instances of Error code 13. Going forward, we will only accept an upload if the plugin is able to connect to Turnitin and generate a similarity report.

#### Use Marking Workflows without errors

In a rare number of cases, it was possible when using Marking Workflows for students to receive an error message when they attempt to view their submissions after grades have been released. We’ve reworked how the grade release happens when using Marking Workflows so that students will no longer see this error.

#### Forum users can use Turnitin without seeing extraneous notifications

When creating a forum post with Turnitin available we were showing information about our report regeneration times when resubmitting to Turnitin. As information about resubmissions is not relevant to forum users, we’ve removed this notification from this view. 

#### Students can’t access the online grading view prematurely

When using our online grading functionality, it was possible for students to launch the viewer despite there being no grading having been completed. To help reduce confusion, this link will now be disabled for students until after the post-date has passed.

---

### Date:       2018-October-29
### Release:    v2018082802

### :wrench: Fixes and enhancements

---

#### We've fixed a bug that prevented users from being able to accept the EULA

On Friday 26th October, we released a plugin update that unexpectedly prevented new users from being able to accept the EULA in the Plagiarism Plugin, and were subsequently unable to submit to Turnitin. If you've updated your plugin since Friday, we're kindly requesting that you update again to resolve this bug.

---

### Date:	2018-August-28
### Release:	v2018082802

### :wrench: Fixes and enhancements

---

#### All files submitted to a multi-file assignment are now sent to Turnitin

We received a report from one of our users who noticed an emerging issue for assignments allowing multiple-file submissions: only one of the submitted files was sent to Turnitin, therefore only generating one Similarity Report. We've resolved this!

**Note:** This issue stemmed from the release of plugin version 2018062601.

#### Rubric and grading forms now successfully attach to Turnitin assignments

We apologize for any difficulty you may have encountered when trying to attach rubrics and grading forms to your assignments through the Moodle Plagiarism Plugin. We're very pleased to announce that normal service has resumed, and all rubric and grading form selections made during and after assignment creation will stick like glue!
  	Hammer&Screwdriver_Blue.png 	

**Note:** This issue stemmed from the release of plugin version 2018011602.

#### We'll no longer attempt to process the Similarity Reports of deleted submissions

Having mistakenly attempted to retrieve the Similarity Reports of deleted submissions, as a direct result, our system recently encountered difficulties with processing new submissions. "It's time to let go of the past!" we told the system, which reluctantly agreed. We'll now no longer try to pull Similarity Reports for deleted submissions... hoorah!

---

### 2018-June-26
### v2018062601

#### Fixes and enhancements

---

#### Resubmissions now overwrite the previous submission

When a student made a resubmission to Turnitin, their previous submission wasn't removed from the assignment inbox; this led to confusion for instructors, who weren't too sure which submission was the most up-to-date version. We've made sure that resubmissions now overwrite the existing submission.

> **Known issue:** While we've been able to fix this issue for students, if an instructor resubmits on behalf of a student, multiple submissions may still appear in Turnitin. We're working on a fix for this and will update you as soon as we find one!

#### The Plagiarism Plugin now works on Moodle 3.1 and earlier versions

Users running Moodle version 3.1 or earlier encountered an unexpected error when submitting a file. The error message, referring to an invalid parameter, was the result of a change made in our previous release. We apologize for the trouble this may have caused and can confirm that Moodle version 3.1 and earlier now function correctly.

#### Moodle unit tests now pass

We’ve ensured all Moodle unit tests now pass with the plugin installed. Thanks to @danmarsden and @kenneth-hendricks for their contributions.

---

### Date:		2018-May-24
### Release:	v2018052401

#### :zap: What's new

---

#### We now support Moodle 3.5

You can find out more about Moodle 3.5 via Moodle's [release notes](https://docs.moodle.org/dev/Moodle_3.5_release_notes).

#### Turnitin's Plagiarism Plugin is GDPR compliant!

To support upcoming changes to European data protection law, we’ve focused our efforts on refreshing our processes around how we use your data.

##### Repository settings are more transparent

We've made the Moodle Plagiarism Plugin repository settings much clearer, by providing easy-to-understand, transparent language behind the Store student papers help icon. During assignment setup, administrators and instructors can now be completely sure that they're selecting the correct storage options for student papers.

##### We're reporting on the data we store about our users

Moodle has released two plugins with privacy features to assist with GDPR compliance. While we've successfully implemented [Moodle's new privacy features](https://docs.moodle.org/dev/Privacy_API), Moodle's update is only available to those using Moodle 3.3.5+, 3.4.2+, or 3.5. Therefore, if you're using an earlier version, you must upgrade to have access to these new features.

To inform you about the data we store in relation to our users, we now provide a detailed list via the Moodle Privacy and Policies page. Learn more about the data we're reporting on via our [Moodle Plugins and GDPR](https://help.turnitin.com/feedback-studio/moodle/moodle-plugins-and-gdpr.htm) page.

#### Students can request to download their data

Students have the ability to request an export of their data stored in Moodle. This request can be accepted or declined by their Moodle administrator. If accepted, the student will be able to download all the data held about them, which includes data from Turnitin.

#### Students can request to remove their data

Students can also request their data to be removed from Moodle. This requested can be accepted or declined by their Moodle administrator. If accepted, the administrator will remove the student data from Moodle. However, administrators must contact support@turnitin.com to request that student data be removed from Turnitin itself.
  
> If you're a Moodle administrator, and using version 3.3.5+, 3.4.2+, or 3.5, follow the steps on the [Moodle Plugins and GDPR](https://help.turnitin.com/feedback-studio/moodle/moodle-plugins-and-gdpr.htm) page to view the data we store in Moodle.

#### You can now send all papers to your institutional repository by default!

We're aligning Turnitin repository options with the Plagiarism Plugin. If an institutional repository is enabled on their Turnitin account, administrators can now opt to **Submit all papers to the institutional repository**. This submission storage option sends all student submissions to the institutional repository without instructor intervention. For this repository option to work successfully, it must firstly be enabled in Turnitin, before it can be configured in Moodle.

If you're a Moodle administrator, follow the steps below to enable this repository setting in Turnitin:

1. Log into Turnitin.com or TurnitinUK.com.
2. Under **Edit**, select the cog  icon.
3. Select **Edit account settings**.
4. Scroll to **Paper repository options** and select **Submit all papers to the institution repository**.
6. Select the **Submit** button at the bottom of the page.

Now, it's time to move to Moodle!

1. From the Moodle left-hand side panel, select **Site administration**.
2. Select **Plugins**.
3. Select **Activity Modules**.
4. Then **Manage Activities**.
5. Scroll to **Turnitin Assignment 2** and select **Settings**.
6. Scroll to **Paper Repository Assignments** and select **Submit all papers to the institutional repository**.

### :wrench: Fixes and enhancements

#### We've removed a technical notice from the assignment settings page

In Moodle's debug mode, a peculiar notice appeared within the assignment settings: 'Array to string conversion in /usr/share/nginx/html/lib/pear/HTML/QuickForm/select.php on line 501'. We've made some adjustments to ensure that this doesn't appear in future.

#### You can now exit lightboxes using the 'Close' button
       	
We received a report that the Close button in the Plagiarism Plugin's lightboxes wasn't performing correctly, in that it wasn't actually closing anything! Instead, users saw the following error: 'Uncaught TypeError: Cannot read property 'close' of undefined at HTMLAnchorElement.onclick'. Thanks for the heads up, @Haietza! We've fixed this issue.

> **Lightboxes** display content by filling the screen and dimming out the rest of the web page. They can be closed to find the website contents still available. Items such as the QuickMark manager and the rubric manager are contained inside a lightbox.

#### Instructors can now successfully submit on behalf of a student
  	
An instructor encountered an issue when attempting to resubmit on behalf of a student within a group submission. This was down to us incorrectly checking the permissions of the student, rather than the submitter. These permissions told us that the student wasn't permitted to resubmit to the assignment, but if we'd checked the instructor permissions, we'd have found that their permissions allowed this. Phew. A big thanks to @micaherne for his patch to fix this! It did the job nicely.

---

### :snowflake: Date:		2018-January-16
### :snowflake: Release:	v2018011602

### :zap: What's new

#### Instant Similarity Reports for up to three resubmissions

Feedback Studio allows students to view their Similarity Report results immediately! Students can now view their initial Similarity Report, then revise and resubmit their work up to three times, without having to wait 24 hours for an updated report. After three resubmissions have been made, the 24-hour report generation wait time will be restored. Instantaneous similarity results give students the formative support they need to master paraphrasing and citation conventions efficiently.

> To enable resubmissions for students, you must correctly configure the assignment settings of a new or existing Moodle Direct V2 assignment.
>
> 1. Scroll to **Turnitin plagiarism plugin settings**.
> 2. Under **Report Generation Speed**, select **Generate reports immediately (students can resubmit until due date): After 3 resubmissions, reports generate after 24 hours**.

#### Genre-specific rubrics in Feedback Studio (North America only)

Revision Assistant's genre-specific rubrics are now available in Feedback Studio for our North American users, designed with 6th - 12th graders in mind! K-12 instructors can assign new rubrics to their assignments to help their students master the art of argumentative, narrative, informative, and analytical writing. If you're actively using Revision Assistant and Feedback Studio together, you can now promote consistency in the classroom by adopting the same rubrics.

> To use a new rubric for grading, you can attach it from within the assignment settings of a new or existing assignment.
> 
> 1. Scroll to **Turnitin plagiarism plugin settings**.
> 2. Under **Attach a rubric to this assignment**, select a new genre-specific rubric from the drop-down list.
> 
> Alternatively, launch the rubric and grading form manager from the Moodle Direct V2 submission inbox, or alternatively, from within Feedback Studio.

#### K-12 QuickMark sets in Feedback Studio (North America only)

QuickMarks are Turnitin's most popular feedback tool among Feedback Studio instructors! But in finding that many of our default QuickMark sets failed to address the needs of our K-12 instructors and students, we've added two new sets to the Feedback Studio collection, available to our North American users! Our new drag-and-drop (and customizable!) comments will help instructors help their students to engage in revision, save time, and more importantly, achieve learning outcomes.
> 
> To view and edit your new QuickMarks, you can access the QuickMark manager from within the assignment settings of a new or existing assignment.
>
> 1. Scroll to Turnitin plagiarism plugin settings.
> 2. Select Launch QuickMark manager to view and manage your new QuickMarks.
>
> Alternatively, launch the QuickMark manager from within in Feedback Studio.

### :wrench: Fixes and enhancements

#### Resubmissions now overwrite the previous submission

When a student made a resubmission to Turnitin, their previous submission wasn't removed from the assignment inbox; this led to confusion for instructors, who weren't too sure which submission was the most up-to-date version. We've made sure that resubmissions now overwrite the existing submission.

#### Students can submit their group work without access error issues

Students have frustratingly been receiving unauthorized access errors when submitting group work, which caused some confusion. After uploading their submission and navigating to the View all submissions page, the student would witness the error. We've made a solid fix to stop this from happening! A big thanks to [@micaherne](https://github.com/micaherne) for this pull request.

---

### Date:       2017-November-23
### Release:    v2017112302

- Support for Moodle 3.4
- Fixed errors due to incorrect configuration of the plugin
- Added default activity completion settings
- Fixed an issue preventing group submission grades from updating

**Support for Moodle 3.4** - After lots of tests against the release of Moodle 3.4, we're pleased to announce that Turnitin's Plagiarism Plugin now supports it.

**Fixed errors due to incorrect configuration of the plugin** - If the Plagiarism Plugin was not configured correctly and PeerMark was simultaneously enabled, this would cause an error in the assignment inbox. We've managed to resolve this!

**Added default activity completion settings** - We've added the default activity completion setting alongside the bulk edit activity completion. Default activity completion allows you to select one or more course activities or resources and change their default settings (usually 'manual') to a setting of your choice. Thanks to @tonyjbutler for his input!

**Fixed an issue preventing group submission grades from updating** - Moodle's group submissions allow one student to submit on behalf of their group. However, we ran into a breakdown in functionality where grades failed to apply to all group participants for a submission; only the student who physically made the submission would receive a grade. Now, all students in a group can view their grade in Turnitin Feedback Studio.

---

### Date:		2017-August-10
### Release:	v2017081001

**Fixed a bug preventing submission after declining the EULA** - We received reports that students were unable to submit to a Moodle assignment after declining the Turnitin EULA. That definitely wasn't expected, but we've quickly fixed it. Students are now able to decline the Turnitin EULA but still submit to a Moodle assignment with Turnitin enabled.

**Fixed a bug preventing submissions from processing** - We suspect our crons might have put on a little weight this month as they were becoming stuck for a few of our users! When some users deleted a submission, this would prevent the cron from running, and consequently, submissions would fail to process.

> A cron is used to execute commands automatically at a set time or date.

To resolve this, we've moved the delete_tii_submission function from private to public. The cron is now functioning as expected for affected users (having dropped a few much-needed pounds!). A big thanks to @aolley for the pull request!

---

### Date:		2017-July-03
### Release:	v2017070301

We now support Moodle 3.3!

- Fixed a bug affecting the Moodle 3.3 bulk completion tool (Thanks to @Syxton)
- Fixed a bug causing submissions from processing

Fixed a bug affecting the Moodle 3.3 bulk completion tool - We received a report that the Moodle 3.3 bulk completion editing feature was broken as a result of the Plagiarism Plugin being installed.

Fixed a bug causing submissions from processing - If a course was deleted before a submission was sent to Turnitin, the cron would become stuck. Submissions in the queued state would stop further submissions from being processed, revealing a database error message. We've stopped exceptions (such as a deleted course) from causing any further cron processing to fail.

**Note:** A cron is used to execute commands automatically at a set time or date.

---

### Date:		2017-May-24
### Release:	v2017052401

- Fixed a bug preventing access to Turnitin Feedback Studio.
- Move connection test to after the check for whether Turnitin is enabled in the module. (Thanks to @roperto)
- Check if pluginlib file exists before including it. (Thanks to @nhoobin)
- Assign Mexican Spanish to Spanish in Turnitin rather than defaulting to English. (Thanks to @jobcespedes)

**Fixed a bug preventing access to Turnitin Feedback Studio** - We discovered a bug causing the similarity score button to break in Moodle's grading platform (a basic version of the Turnitin document viewer). The bug was a jQuery issue, stemming from the release of Moodle 3.1 earlier in the year. Moodle's grading platform caused our links to function incorrectly and consequently stopped instructors from viewing a student's Similarity Report. Links and buttons now work as they should.

---

### Date:		2017-February-22
### Release:	v2017022201

- Verified against Moodle 3.2.
- UI modified for compatibility with Boost theme.
- Update grade call removed for ULCC's coursework module. (Thanks to @aferenz)
- Fixes:
	- An undefined index was causing a Moodle unit test to error. (Thanks to @danmarsden)
	- Cron submission processing fails gracefully and doesn't stop further cron processing if submissiontype or submitter are not set.
	- Cron submission processing fails gracefully and doesn't stop further cron processing if the file or forum post no longer exist. (Thanks to @danmarsden)
	- Display customised error message if errormsg is '0'. (Thanks to @roperto)
	- Save error message correctly instead of error code. (Thanks to @roperto)
	- Undeclared variable replaced in logging call.

---

### Date:		2017-January-31
### Release:	v2017013101

- Fixes:
	- DB Upgrade script now checks submitter column exists as this was breaking for some users.

---

### Date:		2017-January-25
### Release:	v2017012501

- The events now use Moodle's new Events 2 API, which is a requirement to support future Moodle versions. Admins should ensure the Events Queue has been cleared for Plagiarism events. Following this change to the Events API this release and future releases will no longer work on Moodle 2.6.
- The language strings have been updated across all supported languages.
- The unused database columns legacyteacher, apimd5 and externalstatus have been removed from the plugin's plagiarism_turnitin_files table. These were leftovers from Dan Marsden's version of the plugin that are no longer used.
- Travis-CI has been aded to the plugin as an extra QA resource to help flag any issues with the code.
- Fixes:
	- The assignment edit API call no longer fails if repository settings don't match the plugin settings.
	- JavaScript error no longer appears when closing the PeerMark manager.
	- The & character no longer appears in TFS as &amp;.
	- Default values for submitter and student_read columns are now consistent between upgrade and install. (Thanks to @danmarsden).
	- The error message when submitting a file >40mb now displays correctly.
	- Fixed an issue with anonymous marking where grades would appear in the gradebook before the assignment has been unanonymised.
	- Fixed an issue where the first submission to an assignment would sometimes fail to send to Turnitin.
	- Moodle's Behat unit tests will no longer fail. (Thanks to @roperto)

---

### Date:		2016-September-14
### Release:	v2016091401

- Support added for ULCC's coursework module (Thanks to @aferenz).
- Blank update_status function added for consistency.
- Fixes:
	- Locked default settings are now observed.
	- Default settings are now used when enabling Turnitin on an assignment which didn't previously have it enabled.
	- Use each module's specific grade item update instead of calling grade_update directly.

---

### Date: 		2016-July-26
### Release:	v2016072601

- Verified against Moodle 3.1.
- Removed word count check before submission.
- Added .xls and .xlsx to accepted filetypes.
- Increase submission processing limit in cron to 100.
- Fixes:
 	- Module can still be used if Turnitin account is not configured.
	- Check $CFG exists before using in version.php (Thanks to @micaherne).
	- Ensure file extension is lowercase when checking accepted files.
	- Ensure refresh submissions link works for workshop and forums.
	- Change recordset to records in data dump as recordset wasn't working for PostgreSQL.
	- Assignment default settings are now applied when creating assignment (Thanks to @danmarsden).
		- Admins - please ensure that plugin settings match account settings.
	- Added missing and changed incorrect error language strings.
	- All uses of object() changed to stdClass().
	- PeerMark Reviews launcher now enrols user correctly in Turnitin class.
	- Catch exception if can not get file content when making a submission (Thanks to @kenneth-hendricks).
	- Ignore inherited roles when looking for instructors on Turnitin class creation.
	- Ensure activity edit page does not break if plugin is not fully configured. (Thanks to @mhughes2k).
	- Fixed an issue where submission notifications would be sent to instructors even with instructor digital receipt notifications turned off.
	- Fixed a bug that led to error and stack trace upon data dump generation.
	- Grades are no longer sent to the gradebook before anonymity is lifted when blind marking is enabled.
	- Fixed an issue where the EULA link would be broken post-submission if the student originally declined the EULA. (Thanks to @pauln)
	- Added missing error language strings to the language string file.
	- Fixed an issue where a submission could be stuck in pending if the user is not found.

---

### Date:       2016-April-11
### Release:    v2016011105

- Added support form to contact Tii support directly from the plugin.
- Removed cut-off date from due-date calculation.
- Added a message to activity logs detailing whether or not a EULA was accepted.
- Cron functionality has been moved to scheduled tasks.
- Created warning on config page for the customer to check whether translated matching and ETS are configured at account level
- Fixes:
	- Added check for empty array to fix bug #115
	- Plugin now checks file-size before checking word-count.
	- Fixed issue where EULA sometimes appeared multiple times on-screen.
	- fixed issue in JS that prevented Rubric Manager and Quickmark loading. This fixes #101
	- Increased foreign key support and fixed cron index error (#92). (Thanks to AviMoto)

---

### Date:       2016-February-23
### Release:    v2016011104

- Fixes:
	- EULA acceptance problem due to lack of module context.
	- Missing noscript EULA string added.
	- Incorrect version number in upgrade db script for adding due_date_refresh field.
	- Split collated empty function for pre PHP 5.5 environments.

---

### Date:       2016-February-22
### Release:    v2016011103

- The originality score is now refreshed in the assignment inbox after the due date has passed.
- The presentation of activity logs has been improved.
- Settings for the Turnitin digital receipt have been separated from Moodle Direct V2 so that a different setting can be set for each.
- Changed roles to plagiarism specific roles.
- Allow emails to be sent from the noreply address.
- Fixes:
	- Cron now records a deletion as errored and continues processing the events queue if Turnitin submission deletion fails.
	- Removed font awesome to fix styling conflict with Moodle theme.
	- Fixed an issue where the file name would be appending multiple times if the temp file can't be created.
	- Fixed cron warning message regarding REQUEST_URI (Thanks to AviMoto)

---

### Date:       2016-January-25
### Release:    v2016011102

- Fixes:
	- Reworked DV launchers to remove cross domain iframe problem preventing opening in Safari.

---

### Date:       2016-January-12
### Release:    v2016011101

- Output a message when the submission limit has been reached for a single cron run.
- Output cron backlog count.
- Output successful submission message in cron.
- Ensure filename to be sent to Turnitin is UTF-8 encoded
- Unnecessary addition of user being updated in Turnitin and submission inbox being viewed removed from plugin activity logs.
- Use recordset on viewreport in datadump to avoid memory issues (Thanks to aolley).
- Use default values if user has no firstname or lastname.
- Fixes:
	- Grademark icon visibility now dependent on whether GradeMark feedback exists rather than a grade.
	- Cron now records a submissions as errored and continues processing the events queue if Turnitin assignment creation fails.
	- Cron now records a submissions as errored and continues processing the events queue if Turnitin user creation fails.
	- Don't show GradeMark icon to student if anonymised.
	- Paginate submission errors table
	- Replace deprecated mime_content_type function in submission to Turnitin process.

---

### Date:       2015-November-30
### Release:    v2015040111

- Verified against Moodle 3.0
- Anonymous marking setting has been removed. Moodle's blind marking workflow is now used to handle anonymity in Turnitin.
- Retrospective support for PHP 5.3.
- Added a note to highlight the 24 hour Originality Report delay for resubmissions.
- Fixes:
	- Fixed an issue where student names are visible in the file name when blind marking or student privacy is enabled.
	- Student first name default is now saved in the plugin settings when student privacy is enabled.
	- The post date is now handled correctly for blind marking assignments.
	- TII user record is removed if Moodle user does not exist when unlinked.
	- Fixed an issue where the Rubric view link is not visible for students.
	- Shared rubsrics is now initialised when creating a Turnitin class.
	- Force UTF-8 encoding when trimmming multi-byte assignment titles
	- The User dropdown now uses the correct font.
	- Turnitin anonymous marking setting no longer changed if there have been previous submissions to an assignment.
	- TII assignment now syncs when opening the DV.
	- Trigger between revealing identities and grades with blind marking in Turnitin has been reworked.

---

### Date:       2015-October-01
### Release:    v2015040110

- Disable resubmit button in admin area until a submission has been selected.
- Indicator added to show whether student has viewed GradeMark feedback.
- Notice added to warn assignment creators to check against sources.
- Fixes:
	- File titles cleaned up before creating temp files to remove slash permission errors.
	- Checking for released grades reworked for assignments with marking workflow.

---

### Date:       2015-September-16
### Release:    v2015040109

- Submissions workflow changed to exclusively use Moodle's cron. Functionally to instantly send files to Turnitin via AJAX removed.
- Instructors and admins can resend failed submissions to Turnitin.
- Cron submissions limited to 50 per cron run.
- Shared Turnitin Rubrics can be attached to modules.
- Digital receipts can be sent without SMTP settings enabled (Thanks to NeillM).
- Icons replaced with Font Awesome and Tii font sets.
- Ability for instructor to submit on behalf of a student added.
- SDK and Turnitin communication code added (not yet used).
- Fixes:
	- Peermark manager link hidden if Peermark not enabled.
	- Due date pushed out on submission to forum.
	- User who creates module is enrolled in Turnitin as main instructor instead of site admin.
	- Rubric Manager now shows Shared Rubrics.
	- File check added and slashes removed from filename before sending to Turnitin.

---

### Date:       2015-July-31
### Release:    v2015040107

- Verified against Moodle 2.9
- Fixes:
	- Account for Shared Rubrics being returned by the API.
	- Don't show the EULA for files previously submitted to Turnitin.

---

Releases prior to version 2015040106 will refer to changes made to the Turnitin's other Moodle plugins as well; the direct module and block.

### Date:       2015-June-29
### Release:    v2015040106

- Increase submission limit to Turnitin to 40Mb for newly created classes.
- Show Rubric to Plagiarism plugin students before submission if applicable.
- Update User code reinstated to update user's details in Turnitin.
- Entry to Moodle logs added for a blank grading template submission.
- Fixes:
	- Export options no longer available once post date has passed for earliest assignment part.
	- Change status codes for submissions made on Dan Marsden's previous plugin.
	- Sorting by title no longer sorts on paper id.
	- Selecting no grading type hides marks in Turnitin Assignment inbox.
	- Deleted Moodle users are now accounted for when saving submission data.
	- On attempting to restore a course, if the owner doesn't exist then it is reassigned to site admin. (Thanks to daparker26).
	- Special characters that were causing errors removed from submission titles.
	- Remove the large amounts of user data stored in user session in Turnitin Assignment.
	- Avoid endless loops if error occurs on creating a temp file. (Thanks to Jonathon Fowler).
	- Turnitin Assignments now inaccessible through URL if access is restricted.
	- The correct attempt is now graded in Plagiarism plugin.
	- Unsigned integers changed to signed on the install database script.
	- Log text reworded when a student views the inbox.
	- Temporary files are now removed correctly in the Plagiarism plugin. (Thanks to Dan Marsden).
	- Resubmission warning no longer showing after due date.
	- Gradelib file included in Turnitin Assignment cron.

---

### Date:       2015-June-11
### Release:    v2015040105

- Plagiarism plugin support for marking workflow.
- Logging added for resubmissions.
- Fixes:
	- Several database queries fixed to offer full Oracle and SQL Server support.
	- Course end date modal box fixed in Course Migration Tool.
	- Empty submission successful message no longer shown for unsuccessful submisisons.
	- Manual user enrolment to courses with existing Turnitin Assignments fixed.
	- Files added in Moodle Assignment settings no longer submitted to Turnitin.
	- Import to course no longer creates a new Turnitin class if Turnitin Assignments already exist.
	- Users enrolled on class in Turnitin if they are not active users on account.

---

### Date:       2015-May-19
### Release:    v2015040104

- Unused code and unused legacy events removed.
- EULA can be declined in a PP assignment with submissions then only processed by Moodle and not sent to Turnitin.
- New exception handlers added to PP cron. (Thanks to Jeff Kerzner).
- Allow plugin installation without configuration data. (Thanks to Chris Wharton).
- Display all option added to unlink users table.
- PP config code refactored to use Moodle config functions. (Thanks to Michael Hughes).
- Submission deleted box added.
- Tidying up of Turnitin Assignment inbox.
- A digital receipt is now sent to a student when a submission is made to Turnitin (if SMTP is setup in Moodle).
- Fixes:
	- Files removed from PP submission are no longer included in average grade calculation. (Thanks to Tony Butler).
	- Document Viewer no longer hangs in Safari.
	- Undefined offsets on my homepage removed.
	- Submit paper link misalignment.
	- Undefined text on Quickmark Manager closing link.
	- Unlink users refactored to remove unnecessary connection to Turnitin.
	- PP Text content resubmissions no longer sent if there is no content.
	- Refresh submission links shown after refreshing of parts.
	- Part id being set incorrectly for multi-part assignments when refreshing updated submissions in Moodle.

---

### Date:       2015-April-15
### Release:    v2015040102

- Fixes:
	- Fix continuous test connection that was impacting PP EULA.

---

### Date:       2015-April-01
### Release:    v2015040101

- Inputting API URL is now actioned via a select box.
- Old files removed from files table in Plagiarism plugin if no longer part of a submission.
- Updating part names in inbox edits the part tab straightaway.
- Turnitin connection can be tested without having to save first.
- Student can now view digital receipt now from inbox.
- Anonymous marking explanation added to Plagiarism plugin settings.
- Test connection call in Plagiarism plugin cron changed to be static.
- Index on submission_objectid added to turnitintooltwo_submissions table.
- Locks added to Plagiarism plugin defaults. (Thanks to Brendan Heywood).
- Select all option added to Turnitin Assignment inbox.
- Fixes:
	- Modals reworked to use embedded template and handle Turnitin errors without showing theme.
	- Help text corrected in Turnitin Assignment.
	- Account Id is trimmed when saved in configuration.
	- File downloads through settings area.
	- Updating module name in course page no longer creates duplicate event.
	- Course participation report in 2.6 no longer throws error.
	- Anonymous Marking close box closes. (Thanks to Dr. Joseph Baxter).
	- Incorrect variable name in Settings changed. (Thanks to Trevor Cunningham).
	- Pending OR scores no longer launch DV.
	- Instructors can submit to a Turnitin Assignment after the due date.
	- Include paths consiolidated. (Thanks to eviweb).
	- If disclaimer is enabled, then the student can not click submit until they have checked the disclaimer.
	- Only allow Plagiarism plugin modules to have a due date one year ahead when created in Turnitin.
	- Unnecessary PeerMark refreshing removed and print_overview reworked. (Thanks to Dr. Joseph Baxter).
	- Overall grades not displayed to students until last post date has passed.
	- When DV closes in Plagiarism plugin and Turnitin Assignment, all modified grades are updated.
	- Anonymous marking can no longer be turned on and off if a submission has been made.
	- User given warning when attempting to move post date on an Anonymous marking assignment.
	- Spinner added when refreshing submissions in Turnitin Assignment.
	- Refresh submissions button added to Plagiarism plugin settings.
	- Empty resubmission can no longer be sent.

---

### Date:       2015-February-23
### Release:    v2014012413

- Block split into separate github repository.
- EULA modal window resized in Turnitin Assignment.
- Close banner added to modal windows.
- Index created on externalid in plagiarism_turnitin_files table.
- Uploaded files renamed to include useful information.
- Use Grademark config setting used as main grademark setting rather than by assignment.
- Papers transferred in Turnitin are now accounted for when refreshing individual submissions.
- Administrators can now specify whether assignments always go to Standard or No Repository.
- Fixes:
	- Voice comments are now recordable in Safari.
	- Database installer fixed for Moodle 2.3. (Thanks to Jeff Kerzner)
	- Cron request to update submissions now performed in batches. (Thanks to Jeff Kerzner)
	- Help text wrapping inconsistency on Turnitin assignment settings page.
	- Editing dates in Turnitin Assignment inbox accounts for environments with set time zones. (Thanks to NeillM)
	- Page URLs changed to proper URLs. (Thanks to Matt Gibson and Skylar Kelty)
	- Validation added so that part names must be unique.
	- Plagiarism plugin now works with blog and single forum types.

---

### Date:       2015-January-29
### Release:    v2014012412

- Moodle event logging added for Turnitin Assignments.
- Submission title in Turnitin Assignment inbox now opens the Document viewer.
- Group submissions are now partially supported in the Plagiarism plugin. There are limitations with being able to display the Turnitin document viewer for text content submissions, particularly from the default group.
- Fixes:
	- Pop-ups within Document viewer no longer blocked.
	- Plugin upgrade check hidden in admin search results.
	- Filenames are shortened to less than 200 characters before being sent to Turnitin.
	- PP Class reset query fixed for Postgres databases.
	- Export options no longer hidden when viewing Turnitin Assignments with Anonymous Marking enabled.
	- Cron in PP no longer checks for similarity scores where a report is not expected.
	- All students in a group submission to a Moodle assignments can now see the similarity score.
	- Grades for group submissions to Moodle assignments are now applied to all students in the group.
	- Overall grade is now updated in the gradebook if a part is deleted from a multi-part Turnitin Assignment.
	- Moodle exception thrown if non admin user accesses unlink users page. (Thanks to Dr Joseph Baxter)
	- Grademark links no longer shown in PP if Moodle assignment is not to be graded.
	- Grade item entry no longer checked for if Moodle assignment is not to be graded.
	- Check for Turnitin connection before checking EULA acceptance in PP. (Thanks to Tony Butler)
	- Sort by submission date corrected in Turnitin Assignment.
	- PP enable checkboxes removed from Moodle 2.3 as only assignment is available.
	- PP submission area decluttered when Javascript is not enabled.
	- Grademark warning for non submitting users now shows on subsequent clicks.
    - Reset PP submission error code and msg when file successfully submitted.

---

### Date:       2014-November-28
### Release:    v2014012411

- Performance logging of curl calls (provided by Androgogic).
- Fixes:
	- Turnitin Assignment inbox can now be sorted by similarity score and grade.
	- Hard errors changed to soft errors when the PP cron is run.
	- Instructors no longer override other instructors rubrics in PP.
	- If a PP submission has been attempted 5 times and errors each time it will be removed from the queue.
	- Multiple attempts are handled properly - except text content where previous attempts can not be viewed.
	- Incorrect grade calculation (Null grades from previous submissions no longer included) fixed in PP.
	- DV Window resizable.
	- Print original submission from DV Window.

---

### Date:       2014-November-17
### Release:    v2014012410

- Cron scores update in the Plagiarism plugin are now split by submission type.
- Fixes:
	- Anonymous marking reveal form fixed and now initialises correctly on inbox load.
	- Incorrect repository value fixed when synching assignments in Plagiarism plugin.
	- Assignment title length check added on Turnitin assignments.
	- Resubmission grade warning no longer shown when resubmission is not possible.
	- Post date stored correctly for PP assignment (Thanks to Michael Aherne).
	- Post dates not updated for future PP assignments.
	- DV opening fixed for Moodle 2.3.

---

### Date:       2014-October-08
### Release:    v2014012409

- Czech language pack added.
- Plagiarism plugin now uses the hidden until date from gradebook as the post date on Turnitin.
- PP Post date in Turnitin is now stored in Moodle.
- Connection test added to cron event handler.
- Unnecessary Gradebook update removed when viewing Turnitin Assignment.
- Specify assign when looking for user's grades in PP (Thanks to mattgibson).
- PHP end tags removed to fit with moodle guidelines.
- PHP header function replaced with moodle redirect function to fit with moodle guidelines.
- Error handling added when getting users for tutors and students tabs.
- Error handling added when enrolling all students in tutors and students tabs.
- Submissions are removed from the events cron if a student has not accepted the EULA.
- EULA is now presented via an Iframe rather than a separate tab.
- Late submissions allowed setting in Turnitin for Plagiarism plugin assignments is now always true.
- Fixes:
	- Details for a non moodle user who is only in expired classes can be retrieved when grabbing submission data.
	- Logger class renamed in SDK.
	- Gradelib file included in cron.
	- Scope of tool tipster anti-aliasing fixed to not affect whole of Moodle.
	- Date of late submissions indicated in red.
	- Oracle database error when getting forum post.
	- Inbox hidden columns fixed if Grademark is disabled.
	- Individual part post dates can now be the same as post date.
	- Submissiontype now used in correct context in PP file errors.
	- Test connection now hidden on plugin upgrade.
	- Incorrect word count on text content submissions fixed.
	- Moodle assignment due dates now advanced by 1 day in Turnitin instead of 1 month.
	- Select all checkbox fixed in Unlink users screen.
	- Editable date boxes now re-enable after esc is pressed while one is active.
	- Document viewer no longer hangs in Safari and is no longer blocked by popups.
	- Student can delete a submission that hasn’t gone to Turnitin in a Turnitin assignment.

---

### Date:       2014-September-22
### Release:    v2014012408

- Fixes:
	- EULA notice removed from PP submissions with previous submissions.
	- Rubrics now being saved in PP.
	- EULA no longer blocked by popups in Turnitin Assignment.
	- EULA & Disclosure no longer being shown if PP is disabled for module (Thanks to Dan Marsden).

---

### Date:       2014-September-04
### Release:    v2014012407

- Remove Grademark settings if GradeMark is disabled. (Thanks to Alex Rowe)
- Date handling reconfigured in PP to prevent erros (Thanks to Dan Marsden)
- Fixes:
	- File errors page no longer errors if file has been deleted. (Thanks to Ruslin Kabalin)
	- Course migration bug no longer tries to populate PP array in migration if PP not installed.
	- Inbox submission links now work after refreshing non moodle users submissions in Turnitin Assignment.
	- Assignment Grade (PP) table no longer populated if grade is null when cron runs.
	- Encoding issue with module description fixed.
	- Anonymous marking no longer set if not enabled in settings (Thanks to Dan Marsden).

---

### Date:       2014-August-19
### Release:    v2014012406

- Error reporting added for files that are too large, small submissions and any other submission errors.
- Error reporting added to cron.
- Error reporting and success statement added at submission stage.
- Non acceptance of EULA now indicated to tutor in inbox.
- Error indicators and rollover messages now displayed in inbox.
- Error messages saved and displayed in settings area.
- EULA moved to submission declaration and submission form hidden.
- Turnitin Paper Id now shown next to submission to show that paper has been submitted.
- Fixes:
	- Long assignment titles are now truncated.
	- Link to a file in Assignment Summary now renders correctly.
	- Inbox part date editing now works on Windows servers.
	- Cron in PP changed to check for scores when ORcapable is 1.
	- Course Migration query fixed when creating class.
	- Course migration error fixed when no Turnitin courses to link to exist.

---

### Date:       2014-June-11
### Release:    v2014012405

- Course reset functionality added to remove Turnitin data when a class/module is reset.
- Ability added to enable/disable Turnitin in individual modules.
- Ability added for instructors to refresh individual rows in a Turnitin Assignment.
- Automatic grade refreshing from Turnitin can now be turned off in Turnitin Assignments.
- Anonymous marking and Translated matching settings removed in PP modules if they are disabled in config.
- Config warning added if plugin has not been configured.
- Anonymous marking option is locked once a submission is made to any assignment part.
- Font Awesome added to plugin
- EULA closing reworked to accomodate IE
- Javascript cleaned up in block to use Moodle value (Thanks to Skylar Kelty).
- Version file updated for Moodle 2.7+ compatibility (Thanks to Skylar Kelty).
- Javascript reorganised to fit better with Moodle guidelines
- Erroneous debugging removed (Thanks to Skylar Kelty).
- Check for XMLWriter extension added to settings area.
- Removed restriction on word count and content length if accepting any file type in PP.
- Removed restriction in PP to allow submissions after the due date.
- Automatic connection test and upgrade check in settings stopped and changed to buttons.
- User creation removed from restore procedure.
- Additonal indexes added to database tables
- Extra permission checks added for migration tool
- Error message now shown if ajax request to get submissions times out.
- Improved CSS to scope only to plugins and files added to jQuery plugin organisation
- Forum posts are now submitted to Turnitin when posted
- Database dump added to PP settings page
- WSDL files used by SDK are now stored locally.
- SDK setting added to use Moodle SSL certificate if it is present.
- Code changes as required by Moodlerooms to better fit Moodle guidelines
- Fixes:
	- User could submit to Turnitin Assignment without accepting Moodle disclaimer
	- Postgres type error when searching unlinked users query
	- A grade set to 0 in GradeMark was showing as — in Turnitin Assignment
	- Allow Non OR file type setting now being changed in Turnitin
	- New file submissions with same filename display correct OR link in PP.
	- Peermark Manager now accessible to any instructor in PP
	- Turnitin Messages Inbox now accessible to any instructor
	- Gradebook now updates when post date is changed on the inbox screen.
	- Grademark null grades no longer overwrite grades previously set in Moodle via PP.
	- Accept anything setting is now passed to recreated assignment in Course migration
	- Feedback files no longer sent to Turnitin in PP
	- Admin now enrolled on class when migrating incase they are not on the account.
	- PP cron now ignores files with no OR score when cron attempts to refresh scores.
	- Grades now removed from Gradebook when submission is deleted.

---

### Date:       2014-June-11
### Release:    v2014012404

- EULA acceptance is now stored locally for submissions.

---

### Date:       2014-April-17
### Release:    v2014012403

- Grademark link removed for student if a grade has not been set in Plagiarism Plugin.
- Feedback release date changed on forum with plagiarism plugin to be the same as start date.
- Infinite loading of Document viewer stopped.
- Full Catalan language pack added.
- Submissions in Plagiarism plugin stopped if there has been 5 unsuccessful attempts.
- Link removed for Originality Report if there is no score.
- Fixes:
	- Incorrect links to GradeMark and Originality Report for students have been hidden.
	- Conflicts with Bootstrap theme for tooltips and fixed grademark link position.
	- Incorrect settings link in the Plagiarism plugin.
	- Timestamp was being incorrectly set preventing more than 1 batch of submissions updating from Turnitin.
	- Student is now enrolled on the class when checking EULA acceptance to ensure they are on account.

---

### Date:       2014-February-26
### Release:    v2014012402

- Vietnamese Language pack added.
- Option to send draft submissions to Turnitin in Plagiarism Plugin reinstated.
- Diagnostic mode reinstated to disable logging by default.
- Troubleshooting documentation expanded.
- Fixes:
	- Student’s who’d never submitted could not view rubric, they’re now enrolled at this point.
	- Instructor now being enrolled in course when resetting to prevent errors in reading memberships.
	- OR Link was being shown in Plagiarism Plugin for non OR submissions.
	- Submissions now processed in Plagiarism Plugin if due date disabled.
	- Rubric List was not being populated in Plagiarism Plugin settings.
	- Updating of OR scores depending on OR submissions capability fixed in Plagiarism Plugin.
	- Cut off date / late submission issues solved in Plagiarism Plugin (Thanks to Chris Wharton).
	- Generic CSS issues fixed that were breaking some user’s themes.
	- Timezone was not being accounted for when editing part dates in inbox.
	- Editing title in course context is now updated in Turnitin.
	- Submit nothing link removed if submission has been made to Moodle but not yet processed by Turnitin
	- Incorrect grade scale calculation.
	- Previous Turnitin users were not being joined to account on Plagiarism plugin.

---

### Date:       2014-January-24
### Release:    v2014012401

- File type limit removed.
- Ability to accept no file added so that marks / grades can be allocated to non file submissions.
- Dependencies added to plagiarism plugin and blocks
- Fixes:
	- Error occurring in course reset.

---

### Date: 		2013-December-18
### Release:	v2013121801

- Supports Turnitin Originality Checking, GradeMark and PeerMark
- Allows access to the Rubric Manager and Quickmark Manager from within the Moodle environment
- Supports multi-part assignments allowing draft and revision submissions
- Allows instructors to submit work on behalf of students
- Supports Moodle Grade Scales and updates the Moodle gradebook with grades entered in GradeMark
- Supports Moodle Groups
- Allows multiple instructors to access a class and assignments in Turnitin’s web interface
- Supports Moodle’s built-in plagiarism detection thereby allowing access to Turnitin functionality from within Moodle assignments
- Incorporates a Class Migration feature allowing access to classes and assignments that are in Turnitin but not in the Moodle environment
