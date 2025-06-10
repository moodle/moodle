### Version 2.141 of the Moodle Gapfill question type October 2024
PHP 8.3 compatibility fix. It was throwing an undeclared variables error
Thanks to to Alistair Spark for reporting the issue here
https://github.com/marcusgreen/moodle-qtype_gapfill/issues/113
Confirmation of compatibility with Moodle 4.5

### Version 2.139 of the Moodle Gapfill question type April 2024
Fixes for PHP 8.2 deprecation messages. Updates to ci to confirm compatibility 
with Moodle 4.4.

### Version 2.138 of the Moodle Gapfill question type May 2023
Fix for PHP 8.1 deprecation messages. Thanks to Joseph Rézeau  for
creating the ticket for that.
https://github.com/marcusgreen/moodle-qtype_gapfill/issues/104

Re-instate the creation of letterhints field in the db for upgrades from
before v1.97 (in 2018). Thanks to Dr. Lucia Liljegren for creating the ticket for
that https://github.com/marcusgreen/moodle-qtype_gapfill/issues/105

### Version 2.137 of the Moodle Gapfill question type April 2023
Fix compatibility on mobile with dark theme (css tweak)
https://github.com/marcusgreen/moodle-qtype_gapfill/pull/100

Centre droptarget text to better handle long answers
https://github.com/marcusgreen/moodle-qtype_gapfill/issues/99

Additional English language sample questions.

### Version 2.136 of the Moodle Gapfill question type Nov 2022
Remove html comments from code before rendering. There is no reason to include comments at run time and when people copy and paste from other sources it can include items like  <!--[if !supportLists]
creating spurious gaps. Thanks to sunnac for reporting this.

New behat test to confirm core mlang and mlang2 filters work as expected.

Additional English language sample questions.

### Version 2.135 of the Moodle Gapfill question type Sept 2022
The use of jQuery UI broke the display of the 'dock' in Moodle 4.0 https://github.com/marcusgreen/moodle-qtype_gapfill/issues/83. Thanks to Vitaliy Baran for reporting that.  Switched to using ES6/HTML5 drag and drop code.
More English language example questions

### Version 2.134 of the Moodle Gapfill question type Jun 2022

Fix for duplicated draggables when used in the mobile apps. Thanks to Nicholas Stefanski for reporting this and for contributing a code solution.

Added $CFG->wwwwroot in front of path to mobile css for where moodle home hangs of a folder from the
url.

### Version 2.133 of the Moodle Gapfill question type Mar 2022
Missing space around or in non English feedback. Where the language was not
English and there is more than one correct answer the space was missing around
the tanslation of or. So in German if the correct answers are red or green it would present as
redodergreen instead of red oder green. My thanks to Simon Küpfer for reporting this issue.

Described here
https://github.com/marcusgreen/moodle-qtype_gapfill/issues/80

Fix renderer.php so the output complies with the validator at https://validator.w3.org/
My thanks to t-schroeder for raising this issue
https://github.com/marcusgreen/moodle-qtype_gapfill/pull/77
Which gave me the idea of validating the output.
Fix for layout in optionsaftertext mode
Bump to version of Moodle required from 3.3 to 3.7, to make support easier
Updates to behat tests to work with Moodle 4.0. No further support for
MS IE browser 11.

### Version 2.132 of the Moodle Gapfill question type Jul 2021
Fix for https://github.com/marcusgreen/moodle-qtype_gapfill/issues/76
where spurious div broke display of quiz blocks.
My thanks to Serge Fleussu for reporting this.

### Version 2.131 of the Moodle Gapfill question type June 2021
Fix for Mobile App/ionic5, long text questions were cutting off instead of wrapping.
More english language example questions. Moved from travis ci to github actions and
moodle-ci

### Version 2.12 of the Moodle Gapfill question type May 2021
Fix for iOS (e.g. iPads/iPhones). My thanks to  Deny Supanji  for reporting this. The text entry field now has the  spellchecker = false which stops it changing apostrophes into right quote marks.

Refactored get attributes into separate function for clarity in renderer.php (a developer thing)

Adapted to support ionic5 for the mobile app while keeping backwards compatibility for ionic3. See https://docs.moodle.org/dev/Adapt_your_Mobile_plugins_to_Ionic_5

### Version 2.11 of the Moodle Gapfill question type Jan 2021
Fix for https://github.com/marcusgreen/moodle-qtype_gapfill/issues/64.
CSS change to fix issue with dropping onto targets. My thanks to
Tim Schroeder for reporting this.

Fix for duplicate draggbles appearing when using the or (|), opertor. As described here
https://moodle.org/mod/forum/discuss.php?d=414949&parent=1672515
My thanks to Nancy Chan for reporting that.

Disable regex in dropdown mode. This is a fix for this issue
https://github.com/marcusgreen/moodle-qtype_gapfill/issues/32

Error in English lang string. Thanks to lucaboesch
https://github.com/marcusgreen/moodle-qtype_gapfill/pull/69

More unit tests in response to coverage reports (purely for developers)

### Version 2.10 of the Moodle Gapfill question type Oct 2020
Removed an end_div tag because it broke the display of blocks in quiz.
My thanks to Eliot Hoving of the UCL University in the UK for reporting this.

### Version 2.09 of the Moodle Gapfill question type Sep 2020
Singleuse draggbles was always on because javascript interpreted "0" as true.
The file renderer.php now casts it to Boolean (true or false) so singleuse draggables
is only on when the setting is clicked. My thanks to Mrs Summers for reporting this
and helping to identify where the problem was and also to Matthias Giger for testing.

### Version 2.08 of the Moodle Gapfill question type Sep 2020
Removed missing nullable parameter type ?int, as it breaks on PHP 7.0 which is supported at least
up till Moodle 3.5. The optionsaftertext setting did not work on Mobile, fixed by adding
a missing a closing div tag . Discarded the code in upgrade.php that changed default datatype of singleuse. It would have applied to a tiny number of users and not actually break anything.

### Version 2.07 of the Moodle Gapfill question type Sep 2020
Discard changes to mobile that were supposed to make singleuse work, they broke how it worked on mobile. Added class to put space under answeroptions in
mobile.

### Version 2.06 of the Moodle Gapfill question type Aug 2020
New setting singleuse. Draggables are removed from the list of options once they are dropped into a gap. Set on and off through a "singleuse" checkbox in the editing form.
Also works in mobile app.

Added a new  feature for possible future that when an item is dropped into a gap the gap has the 'dropped' class added. I may use this in the future for styling, i.e. change appearance on drop.

Double click on a gap with text will clear it now.

Added js logic to the Display answers checkboxes. For example, if dropdowns is selected the Singleuse checkbox is unchecked and disabled. Have not applied logic to the checkboxes under 'show more'

Refined the unit test classes to consolidate create_question and create_question2 into a single function. This is just for developer clarity.

### Version 1.979 of the Moodle Gapfill question type June 2020
Filters were not being processed. LayTex in particular. Thanks to Elena Safiulina for reporting this issue. Be aware that only plain text can be used for draggables etc, however LayTex can be used in the body of the question text.

### Version 1.978 of the Moodle Gapfill question type May 2020
Fixed issue with case sensitivity when using | operator and ignore duplicates
Thanks to Jason Rogers of the South Carolina Dept of Education and Dipak Kumar of Blackboard for reporting this and helping test the solution.

Fix for when force clean is on, which is planned to be a default, see
https://tracker.moodle.org/browse/MDL-62352
Thanks to  Hubong Nguen for the UK OU who contributed a patch for wordselect without which I would not have known about this.

A fix for an error triggered in the question analysis in the quiz statistics report. Thanks to Chris Kenniburg of Dearborn schools for reporting this.

The editing form was ignoring the settings/default for fixedgapsize. It is now recognising this and
by default on installation the widths gaps will be set to that of the largest gap. See docs at
https://docs.moodle.org/en/Gapfill_question_type#Fixed_Gapsize. I noticed that myself.

Regular expressions are now disabled by default. Thanks to the suggestion from Howard Miller, legendry contributor at the Moodle.org forums.
https://github.com/marcusgreen/moodle-qtype_gapfill/issues/31


### Version 1.977 of the Moodle Gapfill question type Oct 2019

Fixed an issue under Chrome for Windows where answer options (draggables) would not wrap as previously. Thanks to Joseph Rézeau for passing on the details as reported in the Moolde.org French forum by Jean-Gabriel DEPINOY.

The fixedgapsize setting is now set to on with fresh installs.

CSS fix for when the theme creates a paragraph for each gap. Thanks to feedback
from Daniel Garcia (https://top.totalenglish.net) that helped identify this.

Thanks to German Valero thanks for further Spanish translations of example questions.

Thanks to Dave Foord for help identifying a bug displaying wrong answers in dropdown mode
CSS was not being applied to correct/incorrect responses when in dropdown mode.


Switched to using amd modules in the javascript for question editing. No end user benefit
but will help in future development.

### Version 1.976 of the Moodle Gapfill question type May 2019
Updated icon design to include a hint of color and in svg format.
More code compliance tweeks.
Increased number of english language sample questions to over 450

### Version 1.975 of the Moodle Gapfill question type Jan 2019
Added a collection of sample english language questions in
examples\en\english_language.xml that can be imported.

Briefly tested with the Embed question filter
https://moodle.org/plugins/filter_embedquestion

When using Adaptive question behaviour the gapfeedback was always for a correct answer. Thanks to Joseph Rézeau for reporting that. Joseph also helped with improving the strings which will
help with translation.

The value for "show number of correct responses" checkbox on hints didn't do anything. That is now fixed. My thanks to Dr Anna Stefaidou for reporting this.

Changed fixedgapsize default to on for fresh installs (change in install.xml)

Fixed issue in mobile app where it gave the 'tap to select' prompt even when the question was in drop down mode.

Fixed an error message "Can not add jQuery plugins after starting page output!"
That showed when the question type was used in simple lesson
https://moodle.org/plugins/mod_simplelesson
and debug output was turned on. This would have shown in other modules that use the question engine.

Converted dragdrop.js to use amd modules, which is the standard
moodle way of handling javascript.

The utility
admin/cli/check_database_schema.php
threw errors after upgrade indicating incorrect data types and allow null settings. Fixed
the upgrade.php to use code generated by xmldb editor and also fixed
upgrade.php to use proper blocks of update code that checks version.

Restricted the scope of some css classes so they cannot interfere with other components/themes.
As reported here https://github.com/marcusgreen/moodle-qtype_gapfill/issues/39

### Version 1.974 of the Moodle Gapfill question type Sep 2018
Spurious debugger statement found in mobile js code

### Version 1.973 of the Moodle Gapfill question type Sep 2018
Support for the new Moodle 3.5 mobile app. Huge thanks to Dani Palou from Moodle HQ,
without his help I would not have been able to add this capability. Thanks to Elton LaClare
for help with testing.

### Version 1.972 of the Moodle Gapfill question type May 2018
Fixed a bug where correct answers were shown even though it was turned off in the quiz settings checkbox.
My thanks to Matthias Giger and contributors to the Moodle German language for reporting this. Fixed
a bug introduced in the last release whereby the value of optionaftertext was not being saved. My thanks
to Elton LaClare for reporting that. Additional PHPDocs, code standards compliance and confirmation that
it works with Moodle 3.5.

Implemented privacy API for GDPR compliance, see discussion here
https://moodle.org/mod/forum/discuss.php?d=365857

### Version 1.971 of the Moodle Gapfill question type Feb 2018
Bug fix for issue where dragdrop did not work on iOS. Improvements in code standards compliance.

Replaced various hard coded strings with get_string calls to allow for translation. Mythanks to Dinis Medeiros for reporting this.

The text in the letter hint prompt that appears in the Multiple tries section can be customised via the language pack. See instructions
https://docs.moodle.org/en/Language_customisation#Changing_words_or_phrases

### Version 1.97 of the Moodle Gapfill question type Feb 2018
Letter hints, new feature which only works when Interactive with multiple tries behaviour is used. A new checkbox in the question
creation form toggles letterhints mode. This takes effect when an interactive question behaviour is selected. There is a global
checkbox setting for letterhints. If this is on, when a new question is created hints will be inserted into the first and second
boxes under multiple tries block. If it is toggled on with an existing question the hints will have to be added by hand in the
multiple tries section. Then when a student gives an incorrect response they will be given incrementing letters from the correct
 answer when they press try again. Thanks to Elton LaClare for the idea for this and to his employer Sojo University Japan
 http://www.sojo-u.ac.jp/en for funding.

Bug fix that broke the display of the quiz menu when optionsaftertext was selected but gapfill mode was selected. Thanks to
Lizardo Flores for reporting this.

### Version 1.961 of the Moodle Gapfill question type Dec 2017
Mainly a bugfix where MS SQL server installations would not create the gapfill settings table.
My thanks to marisol castro for reporting this. Improvements to phpdoc comments

### Version 1.96 of the Moodle Gapfill question type Oct 2017
Per gap feedback. This is a significant new feature and allows the creation of feedback that is
displayed dependent on if the student gave a correct or incorrect response on a per-gap basis The feedback is
entered by clicking a new button Gap settings which is shown under the question text field. This
toggles the screen to a grey colour and makes the text uneditable. Clicking a "gap" pops up a dialog
with fields for correct or incorrect response. Most HTML is stripped when the feedback is saved. Bold,
Italic, Underscore and hyperlinks are retained. The feedback area does not support images. It has been tested
with the contents of a 10K file (though that would not be a sensible use of the feature).
Substantial improvements to amount of phpdoc comments, which is only of benefit to developers

### Version 1.95 of the Moodle Gapfill question type June 2017
New setting optionsaftertext can be to show the draggable options after the text. Thanks to Elton LaClare for the inspiration to do this.Fixed a bug where if there were multiple questions on a single page the draggables would become disabled after the first submit. Added behat featurefile add_quiz to test in
quiz rather than just in preview mode. Added dragging of selections (previously it was
only type in). Configured up .travis.yml so testing is run every time there is a
git commit. Made code tweaks to comply with results (e.g. csslint)

### Version 1.94 of the Moodle Gapfill question type February 2017
This is a minor release with a css fix and improvements to the mobile app code.
Thanks to Chris Kenniburg for the CSS fix to remove the comma before focus. Added
fix to renderer.php so select element list shows down arrows on android mobile.

In the mobile app answer option selection is more obvious. For dragdrop
questions there is now a prompt that says "Tap to select then tap to drop" as with
the core question types. Thanks to Elton LaClare for the mobile app feedback.

### Version 1.93 of the Moodle Gapfill question type February 2017
This release was made possible through the support of Sojo University Japan.
http://www.sojo-u.ac.jp/en/ . Many thanks to Elton LaClare and Rob Hirschel.

Added remote addon support for the Moodle mobile app. CSS to give indication of onfocus in text imput boxes, subtle change in
background color on hover over draggables. Other CSS tweaks to size of input and draggables. Fixed #25 on github

### Version 1.92 of the Moodle Gapfill question type Nov 2016
CSS to improve dropdowns on chrome mobile, discard gaps in wrong answers which improves display in feedback for dropdowns.
Removed setting of height in em in styles.css which was breaking the display on iOS.

### Version 1.91 of the Moodle Gapfill question type Oct 2016
[.+] will make any text a valid answer and if left empty will not show the .+ as aftergap feedbak

### Version 1.9 of the Moodle Gapfill question type Oct 2016

In the admin interface there is now a link for importing the sample questions into a course.
This is a convenience way of doing a standard XML file question import.

Fixed issue where extended characters were not handled correctly. Have tested with
accented French and Spanish words, Cyrillic and Hindi. Thanks for the feedback to Eduardo Montesinos,
Mariapaola Cirelli, Ellen Spertus and others

Fixed issue where in interactive mode an incorrect answer would show empty braces (typically [])
where the answer in braces would have been shown in other modes.

### Version 1.8 of the Moodle Gapfill question type Oct 2015
Fixed a bug by adding checking for initialisation of array values. Discussed here
https://moodle.org/mod/forum/discuss.php?d=314487#p1274939. Thanks to Ellen Spertus,
Al Rachels and others for the feedback on this.

Added a value in settings so the default for case sensitive can be set
Updated the export of xml code so it adds information on the version of the Gapfill
plugin and the version of Moodle that ran the export. This data can be useful
for tracking down issues (it means I don't have to get back to people asking for
 versions which people may not know and might get wrong).

The | symbol will now be recognised as an or operator even
when regular expression processing is turned off. This is handy for programming language
and math questions that use characters treated as special such as \/?* etc.


### Version 1.7 of the Moodle Gapfill question type
This is maintenance version with no new features. The main purpose of this version is
to ensure the question type will work with Moodle 2.9. This is required because the
JQuery code in the previous version of Gapfill would not work with 2.9. The versions
of JQuery, JQuery UI and touchpunch (for mobile support) have been updated. This addresses
some issues with drag and drop when using MS IE.  The calls are taken from the way JQuery is
used in the ordering question type. Thanks to Gordon Bateson for this.

There is a fix to ensure proper handling of string comparison. Previously
tolower was used which would not work correctly with text containing accents.
This has been changed to use mb_lower. Another issue was that a gap like
[cat|dog] would match bigcat and catty and adog and doggy. This is now fixed.

### Version 1.6 of the Moodle Gapfill question type Mar 2016
When fixed gapsize the width of a gap such as [cat|tiger] will be the width of tiger not cat|tiger, i.e. 5 not 9

When display right answer is selected in the quiz settings the correct answer will be displayed within the question delimiters.
If the correct answer is [cat] and you enter[dog] the answer will show dog [cat] (with dog in red followed by a tick).
Thanks to Gordon McLeod of Glasgow University for inspiring this feature.

When using deferred feedback zero marks were given overall when any gaps were blank. This is now fixed

### Version 1.5 of the Moodle Gapfill question type contributed by Marcus Green
This version has two significant new feature is the double knot or !! and the fixedgapsize setting.
The use of !! indicates that a gap can be left empty and considered a correct response.

This is of particular use with the | or operator where one or more answers or a blank will be considered correct e.g. [cat|dog|!!].

As part of this change the calculation of maximum score per question instance has been modified, so "The [cat] sat on the [!!]"
each gap will be worth 1 mark. This is necessary to ensure that if a value is put in the [!!] space a mark will be lost.

The fixedgapsize settings makes all gaps the same size as the biggest. This stops size being a clue to the correct answer.

The upgrade.php file has been tweaked to use XMLDB to fix issues with databases other than MySQL.

### Version 1.4 of the Moodle Gapfill question type Jan 2014
This release has one bug fix and one new feature. The new feature is support for drag and drop
on touch enabled devices such as iphone, ipad and android. This is by adding in the JQuery touchpunch library into
the renderer.php file. Many thanks to Adam Wojtkiewicz who suggested and tested this solution.

There was a bug in the db/install.xml file with some of the next previous values being incorrect and so preventing a fresh
installation on Moodle 2.4.

The elevator pitch for this question type is as follows

"The Gapfill question type is so easy use, the instructions require one 7 word sentence. Put square braces around
the missing words."


### Version 1.3 of the Moodle Gapfill question type contributed by Marcus Green
The main new feature is disableregex which switches from regular expressions for
matching the given answer with the stored answer to do a plain string comparison. This
can be useful for maths, HTML and programming questions. In this mode the characters that have a
special meaning in regular expressions are treated as plain strings.
This feature appears as a checkbox in the More Options section of the question editing form. The default
for this option can be set in the admin interface so you could set this to be checked by default for every
new question.

I have included a file called sample_questions.xml in with the source code that can be imported
to illustrate the features.

It is now possible to have drag and drop with distractors in "answers in any order" mode. This is where
each field contains the same set of strings separated by the | (or) operator. In dragdrop and dropdown mode
these will be broken into separate selectable answer options. This builds on the code in the previous
version that allowed this approach in plain gapfill mode and can discard duplicate correct answers.

This version has been modified to work with Moodle 2.6. Previous versions of this quesiton type
will throw an error when used with Moodle 2.6 which is linked to a rule on the question text editing box.

This version has been tested mainly in Moodle 2.5 and for about a month with early versions of Moodle 2.6.
It has been installed and briefly tested with 2.4 but it will not work at all with versions of Moodle prior
to 2.1

It is now possible to have commas in the answer strings and to have commas in distractors by escaping
them with a backslash.

A bug has been fixed that was stopping distractor options being exported to xml. A bug has been fixed in
the CSS which meant that there was no border to the gaps when viewed in chrome.

Thanks to Adam Wojtkiewicz testing and feedback. I have implemented his suggestion for a minor modification
to the Javascript to ensure it works along with Geogebra. He has made some suggestions to allow the dragdrop
code work on more mobile platforms which I hope to look at closely in the near future.

Thanks for testing and feedback and comments from Joseph Rézeau, Frankie Kam and Nigel Robertson and
Wayne Prescott.

The elevator pitch for this question type is as follows

"The Gapfill question type is so easy use, the instructions require one 7 word sentence. Put square braces around the missing words."

### Gapfill question type for Moodle V 1.2 Aug 2013
Version 1.2 will colour duplicate answers yellow when discard duplicates mode is used

### Gapfill question type for Moodle V 1.1 May 2013
Version 1.1 includes a count of correct answers and clears incorrect responses in interactive mode

### Gapfill question type for Moodle V 1.0 Nov 2012
This question type was created under Moodle 2.2 and tested with 2.3 and 2.4 It will not work with versions of moodle prior to 2.1.
