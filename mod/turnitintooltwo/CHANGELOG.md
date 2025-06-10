### Date:       2024-September-11
### Release:    v2024091101

---

#### Database Dumps Fixed
Previously the Show Data Dump and Save Data Dump tabs would sometimes display an error. This is now functioning as expected.

#### Anonymous Marking Fixed
Some users encountered an issue where student first names would be shown while anonymous marking was enabled. This has now been fixed.

---

### Date:       2024-August-01
### Release:    v2024080101

---

#### Icon Image Fixed
Previously when a file was submitted that did not meet the requirements for processing by Turnitin, an icon image would be displayed incorrectly. This has now been fixed.

#### Issue With Multiple Peermark Assignments
Previously when a Moodle Direct V2 assignment contained 2 or more Peermark assignments, there was an issue that stopped them from successfully opening. This has now been fixed.

#### Fixed Inbox Ordering On First Name
Previously when sorting submissions in the assignment inbox by first name, the order of the sorted items would be incorrect. This has now been resolved.

#### Error Logged While Creating Course
Previously there was a bug which would sometimes cause an error to be logged while creating a course. This has now been resolved.

#### PHP 8.1+ Deprecation Warnings
We have resolved several deprecation warnings that would be displayed when running under PHP 8.1+.

#### Issue With Bulk Editing of Completion Settings
Previously there was an error when editing completion settings in bulk that would prevent the page from loading. This has now been resolved.

#### Error Generating Pseudo Last Names
Previously when generating dummy lastnames for students for data privacy purposes, the process would fail if no last name was set. This has now been resolved.

#### Error when Max Grade Set To Zero
Previously when a single part of a multi-part assignment had its max grade set to zero, the percentage grade would fail to be calculated and an error message would be shown instead. This has now been resolved and grades are shown correctly.

#### Error when restoring a course
Previously when deleting and later restoring a course, the course restoration would fail if the course end date had already passed. This was because the start date was being set to be after the end date. In cases where this would occur, we now set the end date to today, which allows the course to be restored correctly.

#### Bug in student enrolment
There was previously a bug in student enrolment that meant that sometimes suspended users were being enrolled on courses when they should have been skipped. This has now been resolved.

#### Checks For Submission File Types
Previously we were missing a check for the file types of submissions. This meant that students could always submit files of any type, which would cause their submissions to become stuck in a pending state. This has now been resolved, and if the option is enabled, students can upload only files of supported types.

---

### Date:       2024-March-29
### Release:    v2024032901

#### Export Buttons Fixed
The export buttons along with the individual submission refresh and download buttons and the bulk submission download buttons are now functioning as expected.

### Date:       2024-February-06
### Release:    v2024020601

#### Anonymous Marking Fixed
An issue has been resolved with anonymous marking, where previously when an instructor made a request to reveal a student’s name prior to the feedback release date, the required reason field for the early reveal was not actually being enforced. Instructors will now be required to fill in this field when deciding to reveal a student name early.

#### Removed Summary Content in Moodle Direct V2 Assignment Inbox
In versions 4.0+ of Moodle, the assignment summary content was being displayed twice in two separate locations. This update removes the display of the summary content from the Moodle Direct V2 inbox and it is now only displayed just above the inbox.

#### Resolved Deprecated Warning Messages
This release resolves the issue with deprecated SOAP warnings displaying in PHP 8+.

### Date:       2023-March-27
### Release:    v2023032701

#### Percentage grades now display in the assignment inbox
We have fixed an issue for institutions using PHP 8+ which prevented the assignment inbox from loading when the assignment grade was set to display as a percentage.

#### The PeerMark UI has been improved
We have fixed an issue with the PeerMark manager modal, which previously caused the ‘+’ icon to appear cut off in the user interface.
---

### Date:		2022-May-12
### Release:	v2022051201

---

#### UI improvements to support new 4.0 Boost theme.
We’ve made some improvements to how our interfaces looks when using Moodle’s new 4.0 Boost Theme. Everything will still feel familiar, but there shouldn’t be any elements that squashed when making the switch to the new theme.

#### Notify students who have not submitted yet
A bug had caused our Notify Non-Submitters feature to break temporarily. We’ve fixed this issue, and you can now notify students when they have not submitted to the assignment yet.

---
### Date:		2022-Feb-03
### Release:	v2022020301

---

#### Setting an assignment to grade type ‘none’ will no longer create a Moodle gradebook column
Creating an assignment with the grade type set to ‘none’ would create a column in the Moodle gradebook after the first submission to the assignment. We’ve fixed this bug, and the column will no longer be created.

#### Grades will update when a grade item can’t be found
When the automatic scheduler (cron) runs to update grades, it could fail when it tried to update a grade item that couldn’t be found. We’ve improved how grades update, and this scenario will no longer prevent the rest of the grades from updating.

#### Submit on behalf of a student, even when ‘allow late submissions’ is not enabled
The ‘allow late submissions’ setting should only prevent students from submitting to class. You, as the instructor, should always have control over the assignments you create. This change allows you to submit on behalf of a student without needing to open up late submissions for the entire class.

---

### Date:		2021-Jun-08
### Release:	v2021060801

#### Support for Moodle 3.11
You can now confidently use this integration with Moodle 3.11.

#### Submit button is no longer visible after the due date
The submit button will no longer be shown to students who have already submitted to the assignment when the due date has passed. The current functionality will remain for users who have not already submitted.

#### Delete parts without errors
The option to delete a part will now only show after there is more than one part used. When there is only one part, we’ll no longer show the deletion option to prevent the assignment from potentially breaking.

---

### Date:		2020-Nov-18
### Release:	v2020111801

#### :wrench: Fixes and enhancements

#### Rubrics sync back to the assignment
A bug could cause a rubric that is attached to a multi-part assignment from within the Turnitin viewer to become detached after opening it again. Rubrics will now correctly sync to the assignment and remain attached.

#### Tooltip shows correct maximum file size limit
Turnitin updated our maximum file size limit quite some time ago, but the file size provided in the tooltip had slipped through the net! It will now accurately reflect the maximum file size.

---

### Date:		2020-Jul-22
### Release:	v2020072201

#### :zap: What's new

####Support for Moodle 3.9

Moodle Direct V2 now supports Moodle version 3.9.

---

#### :wrench: Fixes and enhancements

#### Paper deletion icon now appears

A visual bug had caused the ‘trash can’ icon to now appear when using Moodle version 3.8 or later. The icon now appears as intended. 

#### Undefined Index warnings no longer show in Apache logs
     
PHP warnings and notices could occasionally appear in your Apache logs from Turnitin due to a reference to an undefined variable. This issue has been fixed and these warnings will no longer appear. 

#### The submission form displays better in Moodle versions 3.7 and above

A visual bug had caused the submission form to be pressed into a much smaller area in Moodle versions 3.7 and above. While functionality remained the same, this bug has been fixed to give the submission form more room and make it easier to use. 

---

### Date:		2019-Jul-02
### Release:	v2019070201

#### :zap: What's new

---

#### Moodle 3.7 support and course overview bug fixes

We've been working on supporting Moodle 3.7 and improving the course overview area. 

We've given the student and instructor views a refresh, and squashed some bugs that were affecting the Moodle timeline and student submissions. The details of the bug fixes are below.

#### :wrench: Fixes and enhancements

---

#### Moodle timeline bug resolved

There was a bug impacting the Moodle timeline that occurred when resetting courses with new assignment dates. As a result of this bug, after resetting, the new assignment wasn’t visible. This has now been resolved.

#### Overdue messaging bug resolved

Students were experiencing a bug that was causing the 'Recently Overdue' message to display even after students had submitted to the assignment. The message would only disappear once the submission has been graded. The message no longer displays once the student has submitted.

---

### Date:		2019-May-01
### Release:	v2019050101

#### :wrench: Fixes and enhancements

#### Migration tool modal improvements

We've made sure that the modal displays when using our migration tool. If your migration is successful, then a green notification box will be displayed to confirm this.

#### Download student file button visibility
We’ve been improving usability by clarifying available options to instructors. Previously, the download student files button was hidden until a student was selected. We’ve now made the button visible at all times, but it is only possible to invoke the button once a student has been selected.

#### Migration bugs resolved
There were some bugs affecting the migration process. One was causing assignment parts that had been deleted in V1 to reappear after the migration to V2. The other was placing new parts of migrated assignments in the incorrect class within Turnitin. These issues have been resolved.

#### Student grades hidden until post date
Now students will be unable to see grades in the Moodle grade book until the post date has passed. Big thanks to Neill Magill for the pull request with the fix to this issue!

#### Privacy settings updates
We now carry over privacy settings from Moodle V1 to Moodle V2 when the user migrates.

---

### Date:		2019-January-24
### Release:	v2019012401

#### :wrench: Fixes and enhancements

#### You can now use special characters in file names

When using special characters, such as an umlaut or a dash, in student names or submission titles could cause an error message to show for a small number of users using Windows servers. We've improved how our system reads extended character sets and are happy to report that this error message should no longer be shown. 

#### Non-submitters and Messages boxes will now close correctly

A bug had prevented some users from closing the boxes that appear for the non-submitters and Messages screens.

Big thanks to Rex Lorenzo for the pull request with the fix to this issue!

#### Support for Moodle PHP 7.2

We had received some reports that unit tests could fail when upgraded to PHP 7.2

Many thanks to ilya-catalyst for the pull request and fix!

#### General security fixes.

---

### Date:		2018-December-21
### Release:	v2018122101

#### :wrench: Fixes and enhancements

---

#### Grade categories are now migrated between V1 and V2

When you migrate from Moodle Direct V1 to Moodle Direct V2, we’ll now keep the grading category associated with the V1 assignment when we perform the transition.

---

### Date:		2018-December-18
### Release:	v2018121801

#### :zap: What's new

---

#### We now support Moodle 3.6

Getting a head start on the new year by upgrading your Moodle instance? We’ve got you covered. Our Moodle Direct v2 plugin now supports Moodle 3.6. Check out [Check out our guide](https://www.turnitin.com/support/download-integrations) for a full list of the version of Moodle we support.

#### :wrench: Fixes and enhancements

---

#### You can now set ETS as a default setting

Are you a fan of ETS e-rater? Us too! To help you take advantage of it, you can now enable ETS e-rater as a default setting for your assignments.

#### A new warning message when you try to change the Marks Available column

A new warning message when you try to change the Marks Available column - We’ve added this notification just to make sure that editing this field is what you want to do. Editing this field after you’ve finished grading, can change entries in the Moodle gradebook. Don’t worry, you can still edit this field should you need to!

#### A couple of backend security fixes.

---

### Date:		2018-October-26
### Release:	v2018102601

#### Fixes and enhancements

---

#### The course restoration tool has now been restored for use!

Were you previously using Turnitin.com or TurnitinUK.com before making the switch over to Moodle? The course restoration tool allows you to take your old classes and assignments with you by recreating them within Moodle. Previously, a bug had prevented this functionality from working as intended. Want to learn more? [Check out our guide](https://help.turnitin.com/Feedback_Studio/Moodle/Direct%20V2/Administrator/Administrator%20Category.htm#Turnitinclassrestoration).

---

### Date:		2018-October-19
### Release:	v2018101901

#### Fixes and enhancements

---

#### Assignments now display correctly on Moodle’s timeline

We’ve made three separate fixes following Moodle’s new timeline support:

- When creating a V2 assignment using course reset and the “use new dates” option, your assignment now displays correctly on the timeline.
- By changing the due date of the assignment, this now will update on the timeline.
- Providing that the assignment’s due date hasn’t already passed, any assignments created prior to our previous fix in v2018082801 will now appear on the timeline after an instructor accesses the assignment.

#### Thanks to Paul Damiani and Rossco Hellmans

We've received some invaluable bug fixes, in the form of pull requests, to help us improve Moodle V2. We’re really grateful!

---

### Date:		2018-August-28
### Release:	v2018082801

### :wrench: Fixes and enhancements

---

#### The administrator default option for student paper storage is now visible to instructors

We noticed a blip, where the administrator default option for student paper storage wasn't passed to instructors during assignment creation. We've corrected this problem so that the "Store student papers" drop-down menu now populates with the preferred administrator option.

**Note:** All instructor default settings will override the global settings of an administrator.

#### V2 assignments are now available on the Moodle course overview page

We found that Moodle V2 assignments failed to appear on the Moodle course overview page when using Moodle 3.3+ which made it difficult to access assignments. This issue was a direct result of Moodle making a change to their course overview block. To resolve this, we've implemented Moodle's [release notesCalendar Action Events API](https://docs.moodle.org/dev/Calendar_API#Action_events). All is back to normal.

#### The link to the Moodle support wizard is no longer available in Moodle

Our Moodle support wizard has been broken for some time, and while we try to fix it, we're removing access to it to prevent confusion. But don't worry! You can still contact Turnitin via email at tiisupport@turnitin.com. We're currently thinking about how we can reintroduce a working version of the form in the future.

#### We've removed the plugin update checker from Turnitin administrator settings

Moodle has superseded this update checker with a tool that not only checks for Turnitin plugin updates but updates to all plugins within Moodle!

You can find Moodle's **Check for available updates** button by visiting: Site administration > Plugins > Plugins overview.

---

### Date:	2018-May-23
### Release:	v2018052301

#### :zap: What's new

---

#### We now support Moodle 3.5

You can find out more about Moodle 3.5 via Moodle's [release notes](https://docs.moodle.org/dev/Moodle_3.5_release_notes).

#### Turnitin's Moodle V2 plugin is GDPR compliant!

To support upcoming changes to European data protection law, we’ve focused our efforts on refreshing our processes around how we use your data.

##### Repository settings are more transparent

We've made the Moodle V2 repository settings much clearer, by providing easy-to-understand, transparent language behind the Store student papers    help icon. During assignment setup, administrators and instructors can now be completely sure that they're selecting the correct storage options for student papers.

##### We're reporting on the data we store about our users

Moodle has released two plugins with privacy features to assist with GDPR compliance. While we've successfully implemented [Moodle's new privacy features](https://docs.moodle.org/dev/Privacy_API), Moodle's update is only available to those using Moodle 3.3.5+, 3.4.2+, or 3.5. Therefore, if you're using an earlier version, you must upgrade to have access to these new features.

To inform you about the data we store in relation to our users, we now provide a detailed list via the Moodle Privacy and Policies page. Learn more about the data we're reporting on via our [Moodle Plugins and GDPR](https://help.turnitin.com/feedback-studio/moodle/moodle-plugins-and-gdpr.htm) page.

#### Students can request to download their data

Students have the ability to request an export of their data stored in Moodle. This request can be accepted or declined by their Moodle administrator. If accepted, the student will be able to download all the data held about them, which includes data from Turnitin.

#### Students can request to remove their data

Students can also request their data to be removed from Moodle. This requested can be accepted or declined by their Moodle administrator. If accepted, the administrator will remove the student data from Moodle. However, administrators must contact support@turnitin.com to request that student data be removed from Turnitin itself.
  
> If you're a Moodle administrator, and using version 3.3.5+, 3.4.2+, or 3.5, follow the steps on the [Moodle Plugins and GDPR](https://help.turnitin.com/feedback-studio/moodle/moodle-plugins-and-gdpr.htm) page to view the data we store in Moodle.


#### You can now send all papers to your institutional repository by default!

We're aligning Turnitin repository options with our Moodle V2 plugin. If an institutional repository is enabled on their Turnitin account, administrators can now opt to **Submit all papers to the institutional repository**. This submission storage option sends all student submissions to the institutional repository without instructor intervention. For this repository option to work successfully, it must firstly be enabled in Turnitin, before it can be configured in Moodle.

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

#### Assignment titles are now visible in the grade book

This was a bit of an odd one! If an instructor created an assignment with Turnitin GradeMark disabled, the assignment title displayed in the Moodle grade book as the word 'Grade'. We're sure that you'd much rather see the assignment name to be able to distinguish one assignment from the other? Now, you can! Sorry about that!

#### You can now exit lightboxes using the 'Close' button
       	
We received a report that the Close button in the Moodle V2 plugin's lightboxes wasn't performing correctly, in that it wasn't actually closing anything! Instead, users saw the following error: 'Uncaught TypeError: Cannot read property 'close' of undefined at HTMLAnchorElement.onclick'. Thanks for the heads up, @Haietza! We've fixed this issue.

> **Lightboxes** display content by filling the screen and dimming out the rest of the web page. They can be closed to find the website contents still available. Items such as the QuickMark manager and the rubric manager are contained inside a lightbox.

---

### 2018-March-12
### v2018031201

#### Fixes and enhancements

---

#### Manual adjustments to grades now stick during V1 to V2 migration

>Following the migration of a Moodle Direct V1 assignment to V2, grades that had been manually adjusted in the Moodle grade book would be overwritten to match the grade originally set in Turnitin Feedback Studio. With this overwrite being completely unintentional, we've made several backend changes to ensure that future manual adjustments within the grade book always remain fixed during migration.

#### V1 assignments are now removed automatically after migration
 	
>If an administrator failed to delete a Moodle Direct V1 assignment after its migration to V2, the grade of the V1 assignment would remain in the Moodle grade book, causing grades to effectively double. We've now adjusted the behavior of the Moodle Migration Tool to resolve this. So that you no longer need to worry about incorrect grades in the grade book, Moodle V1 assignments are now automatically deleted after migration.
>
>There may be instances where you'll still be required to remove V1 assignments manually (for example, the migration has been successful but there are conflicting grades in the grade book).

#### Deleting V1 assignments that were migrated in prior versions 

> You can manually delete your previously migrated assignments from the usual spot in the Moodle Migration Tool; we encourage you to delete these as soon as possible in order to resolve any current issues in the grade book. Any V1 assignments migrated after this update will be automatically deleted.


### :snowflake: Date:		2018-January-16
### :snowflake: Release:	v2018011601

#### :zap: What's new

---

#### Instant Similarity Reports for up to three resubmissions

Feedback Studio allows students to view their Similarity Report results immediately! Students can now view their initial Similarity Report, then revise and resubmit their work up to three times, without having to wait 24 hours for an updated report. After three resubmissions have been made, the 24-hour report generation wait time will be restored. Instantaneous similarity results give students the formative support they need to master paraphrasing and citation conventions efficiently.

> To enable resubmissions for students, you must correctly configure the assignment settings of a new or existing Moodle Direct V2 assignment.
>
> 1. Scroll to **Originality Report Options**.
> 2. Under **Originality Report Generation and Submissions**, select **Generate reports immediately (students can resubmit until due date): After 3 resubmissions, reports generate after 24 hours**. [Read more](https://guides.turnitin.com/03_Integrations/Turnitin_Partner_Integrations/Moodle/03_Moodle_Direct_V2/03_Instructors/Creating_an_assignment#Similarity_Report_options).

#### Genre-specific rubrics in Feedback Studio (North America only)

Revision Assistant's genre-specific rubrics are now available in Feedback Studio for our North American users, designed with 6th - 12th graders in mind! K-12 instructors can assign new rubrics to their assignments to help their students master the art of argumentative, narrative, informative, and analytical writing. If you're actively using Revision Assistant and Feedback Studio together, you can now promote consistency in the classroom by adopting the same rubrics.

> To use a new rubric for grading, you can attach it from within the assignment settings of a new or existing assignment.
> 
> 1. Scroll to **GradeMark options**.
> 2. Under **Attach a rubric to this assignment**, select a new genre-specific rubric from the drop-down list. [Read more](https://guides.turnitin.com/03_Integrations/Turnitin_Partner_Integrations/Moodle/03_Moodle_Direct_V2/03_Instructors/Creating_an_assignment#GradeMark_options).
> 
> Alternatively, launch the rubric and grading form manager from the Moodle Direct V2 submission inbox, or alternatively, from within Feedback Studio.

#### K-12 QuickMark sets in Feedback Studio (North America only)

QuickMarks are Turnitin's most popular feedback tool among Feedback Studio instructors! But in finding that many of our default QuickMark sets failed to address the needs of our K-12 instructors and students, we've added two new sets to the Feedback Studio collection, available to our North American users! Our new drag-and-drop (and customizable!) comments will help instructors help their students to engage in revision, save time, and more importantly, achieve learning outcomes. [Read more](https://guides.turnitin.com/01_Manuals_and_Guides/Release_Notes/Turnitin_Release_Notes/Genre-specific_rubrics_in_Feedback_Studio).
> 
> To view and edit your new QuickMarks, launch the QuickMark manager from the Moodle Direct V2 submission inbox, or alternatively, from within Feedback Studio.

---

### Date:       2017-November-23
### Release:    v2017112301

Support for Moodle 3.4

After lots of tests against the release of Moodle 3.4, we're pleased to announce that Turnitin's Moodle Direct V2 plugin now supports it.

---

### Date:       2017-October-30
### Release:    v2017103001

V1 to V2 Moodle Migration Tool

**The Moodle Migration Tool is now in early access mode!** - Before making the Moodle Migration Tool available to all of our Moodle users, we asked a subset of users to test the tool and advise us of any issues or errors they may have stumbled across during their experimenting. We'd like to thank all of our testers for their useful comments; they've helped us make the tool even better!

Calling all administrators! To access the Moodle migration tool and enable instructor migration, visit our guide.

Below, you can find several updates we've made to the tool based on the issues raised by our testers:

- Updated migration tool setting information
- We've made improvements to the migration workflow
- We've made some table adjustments
- Fixed an issue causing the grade book to unsuccessfully migrate

**Updated migration tool setting information** - We've updated the information behind the question mark icon connected to the 'Migration action at assignment launch' setting; this referred to old functionality in the Moodle Migration Tool. The text now reflects the current state of the tool. Apologies if you became a little confused! 

**We've made improvements to the migration workflow** -  We've made the migration workflow clearer by prompting the user to migrate on all V1 assignments. Even if you navigate away from the assignment, you'll be prompted again when you return. We hope this helps make your migration a smooth and simple one. 

**We've made some table adjustments** - The table of migrated V1 assignments was appearing a little unkempt, so we tidied up by setting the table width. The table now looks much more aesthetically pleasing! 

**Fixed an issue causing the grade book to unsuccessfully migrate** - When migrating we found a potential issue that would cause the migration of grade book to fail which would lead to issues with the migration of assignments. We've preemptively fixed this so you will be able to migrate your assignments without issue. 

We've also been working on...

- Fixed a bug preventing student enrollment
- Removed the visibility of the delete icon after submission
- Anonymous marking grades now pass to the grade book in all databases
- Unit tests now pass successfully
- Fixed several incorrectly named variables

**Fixed a bug preventing student enrollment** - We received an alert that the Enrol All Students button accessible via the Turnitin Students tab wasn't functioning correctly; this removed the ability for students enrolled in the same class via Turnitin.com or TurnitinUK.com to submit to their Moodle Direct V2 assignment. We identified a missing parameter causing the issue and simply reinserted it. The button now works as intended. Thanks to @neillm for his input!

> A parameter is a piece of information passed to a program by a user or another program; this information can be a name, number, or a selected option. Parameters have an effect on the operation of the program receiving them.

**Removed the visibility of the delete icon after submission** - We received several reports from an institution concerning the presence of the submission delete icon in the student submission inbox. For assignments with resubmissions disabled, students who'd submitted an incorrect file type managed to delete and resubmit their paper. Although this is useful for students who've made a genuine submission error, this was not intended functionality.

We also heard that by allowing late submissions in an assignment, this enabled students to delete and resubmit their paper after the post date. This unintentionally allowed students to resubmit after any instructor marking or grading had taken place. The delete icon has now been removed, meaning students will only be able to submit following the permissions set by their instructor.

**Anonymous marking grades now pass to the grade book** - We noticed that grades failed to populate the grade book for papers submitted to an assignment with anonymous marking enabled. The call to send these grades failed in the Microsoft SQL database and produced an error. Not to worry! All grades now send to the grade book as expected.

> SQL statements are used to perform tasks, such as updating data within a database (updating the grade book with grades!), or retrieving data from a database.

**Unit tests now pass successfully** - With thanks to @danmarsden for his work on fixing some of our broken unit tests. These improvements help to instill user confidence in Moodle Direct V2.

> Unit tests involve testing certain functions and areas – or units – of code. This gives developers the ability to verify that software functions work as intended.

**Fixed several incorrectly named variables** - We've implemented some fixes kindly provided by @sk-unikent which tidied up several variable issues.

> Variables are used to store information for easy manipulation and reference in a computer program. Rather than entering data directly into a program, a variable is used to represent data. When a program is executed, a variable is replaced with actual data. For example, firstName is a variable because it can be referenced and replaced with the actual first name of an individual.

---

### Date:		2017-August-09
### Release:	v2017080901

**Use new assignment dates during course reset** - We received a report that during a course reset, anonymous marking settings were not carried over if an assignment's due date had already passed.

Now, you can opt to use new assignment dates when initiating a course reset by selecting the **Use new assignment dates** check box. The assignment start date will be set to the date and time of the course reset, while the due and post date will elapse seven days from then.

If you’d rather not use new assignment dates, that’s fine; Turnitin will use the course’s original assignment dates instead. However, any anonymous marking settings will not be carried over for assignments where the post date has passed.

![Reset course feature](https://github.com/turnitin/moodle-mod_turnitintooltwo/blob/master/pix/changelog/resetcourse.png "Reset course feature")

---

### Date:		2017-July-19
### Release:	v2017071901

This release is for beta testers of the V1 to V2 Migration Tool. Please note that you will also need to update to Moodle Direct V1 (v2017071901) in order for the Migration Tool to work.

---

### Date:		2017-July-03
### Release:	v2017070301

We now support Moodle 3.3!

- Changed assignment names to be plugin specific
- Added tool tips to interface icons  (Thanks to @rlorenzo)
- All settings are preserved when duplicating or restoring an assignment
- Students no longer see PHP warnings
- Fixed a code issue for assignment backup and restore (Thanks to @davidscotson)

**Changed assignment names to be plugin specific** - When creating a course that included both V1 and V2 assignments, it was difficult to differentiate between the two plugins during setup, with them both being titled 'Turnitin Assignment'. To resolve this, we've changed the assignment titles to be plugin specific.

**Added tool tips to interface icons**  - We received a request to add a tooltip to the following Turnitin interface icons: trash can, pencil, grade, and download. We also swapped the cloud icon (used for downloading a submission) to an easier-to-identify arrow icon.

**Note:** A tool tip is a message that appears when the cursor is positioned over an icon, image, or any other graphic. The inclusion of icon tooltips increases accessibility for users (particularly those using screen readers!)

**All settings are preserved when duplicating or restoring an assignment** - We've fixed an issue that resulted in the 'Allow submission of any file type' and 'Check against Institutional Repository' settings to be forgotten about when duplicating or restoring from backup. Now, a restored or duplicated assignment will have identical settings to its source assignment. Phew!

**Students no longer see PHP warnings** - We received reports that students were able to see PHP errors if Moodle debugging was set to full developer level by the administrator. We've fixed this to ensure that the PHP error is no longer visible to students.

**Fixed a code issue for assignment backup and restore** - We noticed that in the assignment backup and restore code, the same URL substitution was used for V2 as in V1, which could potentially cause backup and restoration issues. We've corrected this so that 'TURNITINTOOLTWO' is now used to encode links to the module and module list, rather than its V1 counterpart, 'TURNITINTOOL'. 

---

### Date:		2017-May-24
### Release:	v2017052401

- Modified the name sorting on the assignment inbox to allow sorting by surname and firstname.
- Escape any HTML or Javascript from submission extract before displaying successful submission message.
- Strip files and images from description in calendar view.
- Remove filepaths from error messages unless debugging is set to developer level.
- Fix bug where checkbox settings weren't being saved.
- Fix course reset failing due to bug introduced by extending assignment titles.
- Fix the unpopulated drop down menus in support wizard.
- Add further unit test infrastructure.
- No longer send digital receipt message for empty grading template submissions.
- Assign Mexican Spanish to Spanish in Turnitin rather than defaulting to English. (Thanks to @jobcespedes)

**List order now consistent between Moodle and Turnitin assignments** - The Moodle submission inbox displays student names in the first name/last name format (e.g. John Smith) and allows instructors to order the list by last name. However, Turnitin list entries could only be ordered by first name. Turnitin is now aligned with Moodle's list ordering.

**Digital receipts no longer execute script found in a paper** - If a student inserted HTML, JavaScript, or any other script tags in their paper, this was executed when the digital receipt displayed after submission. Fixed it! The digital receipt will no longer execute the script (if any script is included) for future submissions.

**Rectified broken files and images in Moodle calendar** - As Moodle calendar doesn't support files or images, any files or images added to the assignment description would appear as broken in the calendar. We'll now strip all files and images from the description before its transfer into the calendar.

**Removed unnecessary information from error messages** - We received feedback from users that one or two error messages included irrelevant information. Our engineers have worked their magic to ensure that any messages you see are now meaningful and help you to resolve the error you're encountering.

**Course reset is now working as expected** - We had some problems with the Moodle course reset feature after the release of 2017031301, with student papers remaining in place even after opting to reset the course. With debugging enabled, an error message displayed, confirming the issue. We've thankfully managed to resolve the problem and you can now give the course reset feature another try.

**Support wizard issues resolved** - We noticed that some administrators and instructors were having trouble using the Turnitin support wizard within Moodle. One of the wizard drop-down menus failed to populate, causing difficulty in raising a support ticket with us. Bug targeted and eliminated!

**No more digital receipts sent for grading template submissions** - When an instructor uses a grading template to grade a student's work, this acts as a submission and delivered a digital receipt to students. Students found the receipt confusing as they hadn't personally made a submission to Turnitin, but the receipt was advising otherwise. We've stopped digital receipts from being sent to students for grading template submissions.

---

### Date:		2017-March-13
### Release:	v2017031301

- Minor update Gradebook method refactor so it can be called from other contexts.
- Extended the character limit for class and assignment titles.
- Added a warning if PHP SOAP extension is not installed.
- Implemented a 'force refresh button' to assignment settings page.
- Resolved an issue affecting revealer's name in anonymous submissions.
- Stopped re-enrolling previously un-enrolled Moodle students.
- Fixed a bug with anonymity on newly created assignment parts.
- Resolved bug affecting assignment inbox access.
- Fixed an issue causing all submissions to refresh upon individual student login.


**Extended the character limit for class and assignment titles** - Some users found the character limit for class and assignment titles restrictive; we've extended the title limits to 256 characters, allowing more freedom when setting up classes and assignments.

**Added a warning if PHP SOAP extension is not installed** - We found that installation would fail without PHP SOAP (PHP5-SOAP, PHP7-SOAP) being installed, and although it's a required package, this wasn't listed as being so. We've added a warning in settings if the extension is not installed, saving you from a failed installation. 

**Implemented a 'force refresh button' to assignment settings page** - Some users informed us that grades were failing to appear in the grade book after their insertion in the document viewer, as well as submission updates not transferring to the assignment inbox. To fix this, we've added a button allowing you to force refresh this data, syncing all submission changes to the grade book and inbox.

**Resolved an issue affecting revealer's name in anonymous submissions** - Reports revealed that the student's name appeared as the revealer of an anonymous submission, rather than the name of the instructor. This resulted in Turnitin administrators having no record of the instructor who requested the reveal. This has now been rectified.

**Stopped re-enrolling previously unenrolled Moodle students** - It appeared that unenrolled students would be re-enrolled when instructors downloaded submissions from Turnitin. We've fixed this! Now, when a student who has submitted is removed from a Moodle course, they will not be re-enrolled when the submission is refreshed.

**Fixed a bug with anonymity on newly created assignment parts** - We received reports that when a second (or more parts) were added to a single-part anonymous assignment, the anonymity setting was not passed across. Our engineers have fixed this problem and our testers have ensured that new parts of an anonymous assignment are anonymous. Thanks for your patience with this one!

**Resolved bug affecting assignment inbox access** - If a student had two submissions for one assignment part in Turnitin, the Moodle assignment inbox would continue to load and hang with the error message: Loading data from Turnitin. We resolved this by ensuring that the system only saves one submission record per student per assignment when refreshing submissions from Turnitin.

**Fixed an issue causing all submissions to refresh upon individual student login** - If a student logged into the assignment inbox, all submissions were refreshed, causing students to show as having updated the grade for other graded submission(s) and subsequently appear as graders in grade reports. We've resolved this by ensuring that a student only has an effect on their own submission.

---

### Date:		2017-February-22
### Release:	v2017022201

- Verified against Moodle 3.2.
- UI modified for compatibility with Boost theme.
- First unit tests added to plugin.

---

### Date:		2017-January-25
### Release:	v2017012501

- This release and future releases will no longer work on Moodle 2.6.
- The language strings have been updated across all supported languages.
- The implementation of submission deletion has been changed so that it is no longer a link.
- Data dump display has been optimised to help with large databases.
- If the required fileinfo extension is missing it will now be mentioned on the plugin settings page.
- The plugin will now not make excessive calls to Turnitin when using the bulk enrolment tool.
- Suspended users now no longer appear in the assignment inbox.
- Non-submitter e-mails will no longer be sent to inactive students. (Thanks to @junwan6)
- Travis-CI has been aded to the plugin as an extra QA resource to help flag any issues with the code.
- Inclusion paths were consolidated for the ./sdk directory. (Thanks to @eviweb)
- Fixes:
	- Fixed an issue with duplicate submission rows showing in the Moodle database.
	- Fixed an issue with anonymous marking where the overall grade would sometimes not appear.
	- The assignment edit API call no longer fails if repository settings don't match the plugin settings.
	- The submission processing message that was missing in Moodle 3.1 is now visible again.
	- Fixed a bug where the submission inbox would not load if an enrolled student has been deleted in Turnitin.
	- Characters that cannot be used in a file name in Windows are now removed from submission titles to prevent submission errors.

---

### Date:		2016-December-21
### Release:	v2016122101

- Fixes:
	- Changed the language codes to correctly recognise Simplified Chinese in Moodle.
	- Pass the correct Simplified Chinese language code to Turnitin.

---

### Date:		2016-September-14
### Release:	v2016091401

- Remove leftover counter variable from get_submission_inbox function.
- Refactored the get users functionality in several places to not include users with roles inherited from site level.
- Fixes:
	- Add missing string to email non submitters.
	- Check $CFG exists before using in version.php (Thanks to @micaherne).
	- Assignments starting more than a year ago can now be restored without user info.
	- Check for GradeMark feedback changes when saving submission.
	- Only show GradeMark launch to student if the paper has feedback or grade.
	- Slight tweak to overall grade to show overall grade on normal assignments.

---

### Date: 		2016-July-26
### Release:	v2016072601

- Verified against Moodle 3.1
- Changed display of names to be consistent with Moodle (Thanks to junwan6).
- Improved the way grade updates are handled for entering grades to the gradebook.
- Fixes:
	- Ignore inherited roles when sending instructor notifications.
	- Scheduled tasks problems with deleting classes from database where no entry exists in course_modules table.
	- Replace a couple of missing icons in plugin configuration area.
	- Grade related settings are now hidden if GradeMark is not enabled.

---

### Date:       2016-April-11
### Release:    v2016011105

- Added support form to contact Tii support directly from the plugin.
- Datatables styling now specific to Turnitin tables to avoid conflict.
- Activity logs for submissions are now more informative.
- Created warning on config page for the customer to check whether translated matching and ETS are configured at account level
- Fixes:
	- Fixed issue with postdate in anonymous marking mode - the page now warns the user the impact on anonymity of moving the post-date.
	- Enrolls user if necessary when performing a course restoration.
	- Fixed cron logic to prevent multiple assignment creation upon cron event failure.

---

### Date:       2016-February-23
### Release:    v2016011104

- Grade display help text is now wrapped.
- Links to migration tool have been renamed to course restoration.
- Block JavaScipt code has been moved out of the direct package as part of our efforts to separate plugins.
- Cron functionality has been moved to scheduled tasks (Thanks to mwehr).
- Allow emails to be sent from the noreply address.
- Fixes:
	- Fixed an issue where the file name would be appending multiple times if the temp file can't be created.
	- Fixed cron warning message regarding REQUEST_URI (Thanks to AviMoto).
	- Query fixed during cron for Postgres (Thanks to mwehr).

---

### Date:       2016-January-25
### Release:    v2016011102

- Fixes:
	- Reworked DV launchers to remove cross domain iframe problem preventing opening in Safari.

---

### Date:       2016-January-12
### Release:    v2016011101

- Instructors to be notified when a submission has been made.
- Contrast changed for inbox error messages.
- Check added to verify if php_mbstring is enabled.
- Ensure filename to be sent to Turnitin is UTF-8 encoded
- Unnecessary addition of user being updated in Turnitin and submission inbox being viewed removed from plugin activity logs.
- Unused (pre Moodle 2.6) $module settings removed from version.php.
- Moodle's cron will update OR scores if necessary after the due date has passed.
- Use default values if user has no firstname or lastname.
- Fixes:
	- Anonymous grades to gradebook after post date has passed processed by cron.
	- Grademark icon visibility now dependent on whether GradeMark feedback exists rather than a grade.
    - Multi-part assignment Delete icon missing from "delete parts".
    - Table sorting images shown in Submission inbox.
    - Fix object variable in ajax request for PHP7 compatability.
    - Additional user status checking added to view.php functions (Thanks to Skylar Kelty)
	- Replace deprecated mime_content_type function in submission to Turnitin process.

---

### Date:       2015-November-30
### Release:    v2015040111

- Verified against Moodle 3.0
- Javascript is now minified.
- Grades can now be shown as a percentage.
- Datatables has been updated to the latest version.
- Added a note to highlight the 24 hour Originality Report delay for resubmissions.
- Fixes:
	- The assignment part displayed is now remembered upon submission deletion.
	- TII user record is removed if Moodle user does not exist when unlinked.
	- Fixed potential difference in temp directory path.
	- Fixed an issue where the Rubric view link is not visible for students.
	- Fixed an issue where the Grademark icon was not clickable if resubmissions are enabled.
	- Shared rubrics is now initialised when creating a Turnitin class.
	- Fixed an issue where course restore was grabbing the wrong class.
	- Student first name default is now saved in the plugin settings when student privacy is enabled.
	- Fixed an issue where student names are visible in the file name when student privacy is enabled.
	- An issue where the EULA modal would not open properly when certain themes are being used.

---

### Date:       2015-October-01
### Release:    v2015040110

- Unnecessary setting of course removed from view.php.
- Grade category selectable for Turnitin Assignments.
- Allow HTML in disclaimer message.
- Notice added to warn assignment creators to check against sources.
- Fixes:
	- Download bulk files adheres to checked submissions.
	- Anonymous marking inconsistencies when resetting assignment.
	- File titles cleaned up before creating temp files to remove slash permission errors.
	- Assignment edit error occuring on user enrolment when retrieving grades.
	- Correct upload limit shows for students.
	- Messages inbox loads in correct modal rather than new window.

---

### Date:       2015-September-16
### Release:    v2015040109

- API URL for UK institutions automatically changed to api.turnitinuk.com.
- Shared Turnitin Rubrics can be attached to assignments.
- Digital receipts can be sent without SMTP settings enabled.
- Icons replaced with Font Awesome and Tii font sets.
- Modified logging on submission upload and deletion.
- Ability added to send a message to user who not yet submitted to an assignment.
- Fixes:
	- Rubric Manager now shows Shared Rubrics.
	- Submission modal resizing errors and EULA acceptance loop fixed as EULA has moved from submission modal to submission inbox
	- Extra user role check added on submission deletion.
	- File check added before sending to Turnitin.
	- Submissions are now correctly linked when backing up and restoring (Thanks to Adam Olley).
	- File check added and slashes removed from filename before sending to Turnitin.

---

### Date:       2015-July-31
### Release:    v2015040107

- Verified against Moodle 2.9
- Fixes:
	- Account for Shared Rubrics being returned by the API.

---

Releases before version 2015040106 will refer to changes made to the Turnitin's other Moodle plugins as well; the plagiarism plugin and block.

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
- Error message now shown if ajax request to get submissions times out
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
- Ability to accept no file added so that marks / grades can be allocated to non file submissions
- Dependencies added to plagiarism plugin and blocks
- Fixes:
	- Error occurring in course reset

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
