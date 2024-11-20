REALTIME QUIZ
-------------

What is it?
-----------
This is a type of quiz designed to be used in face-to-face lessons, with a classroom full of computers.
The teacher creates the quiz in advance - adding multiple-choice questions, with a range of answers (and indicating which is the correct answer).

During the lesson, the teacher starts the quiz (optionally giving the quiz a 'session name'). Students can now connect to this quiz. Once the teacher is satisfied that all students have connected to the quiz, they can click on 'Next' to show the first question. The question will be displayed for a pre-defined amount of time, after which the correct answer will be displayed, along with a count of how many students gave each answer. The teacher can then discuss the question, before clicking on 'Next' to show the next question. Once all the questions have been shown, the final result for the class is displayed.

The teacher can, at a later date, go back through the results and, for each question, see exactly what answer each student gave.

Changes:
--------

2024-10-19 - 3.4.4.2 - Minor M4.5 compatibility fixes
2024-04-04 - 3.4.4.1 - Minor M4.4 compatibility fixes
2023-10-02 - 3.4.4.0 - update GitHub actions ready for 4.3 release
2023-04-14 - 3.4.3.0 - Minor M4.2 compatibility fixes (remove legacy log functions in events)
2022-11-19 - 3.4.2.0 - Minor M4.1 compatibility fixes, fix layout in M4.0+
2022-01-22 - Minor M4.0 compatibility fixes
2021-05-15 - M3.11 compatibility fix (add 'completion info' to the top of each page)
2021-04-09 - Minor internal documentation fixes and switch from Travis to Github actions, M3.11 compatibility fix
2018-11-26 - Minor fixes to work with Moodle 3.6
2018-10-15 - Show number of students connected on start page - thanks to Alain Corbière for this feature
2018-04-21 - Add support for GDPR. This requires Moodle 3.4 or above.
2017-11-09 - Minor PHP 7 compatibility fix.
2017-05-12 - Moodle 3.3 compatibility fixes.
2015-11-08 - Moodle 3.0 minor compatibility fix.
2015-05-09 - Moodle 2.9 compatibility fixes.
2014-10-28 - Moodle 2.7 compatibility fixes (from Tony Butler); reduced server load with large number of clients (from Ruslan Kabalin); ability for teachers to reconnect to running quizzes, if they navigate away from them
2013-11-28 - More Moodle 2.6 compatibility fixes
2013-11-19 - Moodle 2.6 compatibility fixes
2013-07-30 - Fixed embedding images when first creating a question.
2013-01-10 - Fixed HTML Editor in Moodle 2.3
2013-01-06 - Backup and restore now available
2013-01-05 - Questions now use a standard Moodle form, so can include images, videos, etc.
2012-07-02 - Reports page can now show all user responses - thanks to Frankie Kam and Al Rachels for the original code
2012-01-13 - Minor tweak: questions with 'no correct answers' score 100% for everyone (not 0%) for statistical purposes
2012-01-12 - Now able to include questions with no correct answers (for 'surveys'). Note: mixing questions with answers and no answers will give incorrect statistics. Also added various minor fixes.
... lots of changes that were not recorded here - see https://github.com/davosmith/moodle-realtimequiz for details
v0.8 (20/12/2008) - Fixed: deleting associated answers/submissions when deleting questions. Now able to restore realtime quizzes from backups.
v0.7 (15/11/2008) - NOT RELEASED. Now able to backup (but not restore) realtime quizzes.
v0.6 (4/10/2008) - Made the client computer resend requests if nothing comes back from the server within 2 seconds (should stop quiz from getting stuck in heavy network traffic). Moved the language files into the same folder as the rest of the files.
v0.5 (18/7/2008) - Fixed bug where '&' '<' '>' symbols in questions / answers would cause quiz to break.
v0.4 (22/11/2007) - you can now have different times for each question (set to '0' for the default time set for the quiz)
v0.3 - added individual scores for students, display total number of questions
v0.2 - fixed 404 errors for non-admin logins
v0.1 - initial release

Installation:
-------------
Unzip all the files into a temporary directory.
Copy the 'realtimequiz' folder into '<moodlehomedir>/mod'.
The system administrator shoudld then log in to moodle and click on the 'Notifications' link in the Site administration block.

Uninstalling:
-------------
Delete the module from the 'Activities' module list in the amin section.

Feedback:
---------

You can contact me on moodle@davosmith.co.uk

Please let me know any bugs or feature requests - the former I will try to fix ASAP; the latter will be noted and I'll look at them when I have time (in between marking and looking after my children).

