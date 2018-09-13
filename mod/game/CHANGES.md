Changes in version 2018-07-07 (2018070701)
- Fix in cryptex when contains audio files

Changes in version 2018-07-04 (2018070402)
- Fix in high score
- New image set on hangman
- New image set on "Snakes and Ladders"
- New language string header_footer_options

Changes in version 2017-08-11 (2017081103)
- New: High score

Changes in version 2017-08-08 (2017080601)
- New: check params of crossword/cryptex

Changes in version 2017-07-19 (2017071902)
- Fix: Completion support

Changes in version 2017-07-18 (2017071801)
- New: icon.png and icon.svg

Changes in version 2017-07-16 (2017071601)
- Fix: Cloning a game

Changes in version 2017-07-06 (2017070606)
- New: Global glossaries

Changes in version 2017-07-05 (2017070502)
- Fix: Problems on backup/restore

Changes in version 2017-07-03 (2017070301)
- New: Auto check for common problem (mod_form.php and view.php for teachers)

Changes in version 2017-06-30 (2017063002)
- in mod_form.php shows the correct numbers of correspondings questions/glossaryentries
- in showanswrs.php shows the correspondings questions/glossaryentries

Changes in version 2017-06-27 (20170627)
- Fix conversation of columns (cols,rows) from string to int

Changes in version 2017-06-20 (20170620)
- Fix conversation of columns (cols,rows) from string to int

Changes in version 2017-06-19 (20170619)
- Change field name game_snakes_database.cols => usedcols
- Change field name game_snakes_database.rows => usedrows

Changes in version 2017-06-10 (20170610)
- Fix phpdocs problems

Changes in version 2017-06-09 (20170609)
- Fix Moodle 3.4 compatibility problems
- Fix stylings problems

Changes in version 2016-08-19 (20160819)
- New string (millionaire_lettersall) that will be used to Deutsch language

Changes in version 2016-06-26 (20160626)
- Change type of game_cross.createscore to double

Changes in version 2016-06-14 (20160614)
- Fix Moodle 31 compatibility

Changes in version 2016-09-17 (20160917)
----------------------------------------
- Fix Moodle 3.1 compatibility problems

Changes in version 2016-04-26 (20160426)
----------------------------------------
- Fix Use get_types or get_shortcuts depended on version of Moodle

Changes in version 2016-03-03 (20160302)
------------------------------------------------------------------
- New: Max compute time in crossword and cryptex

Changes in version 2016-02-14 (20160214)
------------------------------------------------------------------
- Fix syntax error (missed fullstop for concatenation)
- Merge pull request #10 from grigory-catalyst/MOODLE_20_STABLE
- Fix missing string gameopenclose at lang/en/game.php
- Fix compatibility with Moodle 2.5

Changes in version 2015-12-31 (4) (2015123104)
------------------------------------------------------------------
- Fix translations check at translate.php

Changes in version 2015-12-31 (3) (2015123103)
------------------------------------------------------------------
- Fix at backup.
- Fix not to show how many correct letters are when printing the crossword.
- Moodle 3.1 compatibility.
- Fixing how is shows multichoice question on show answers.

Changes in version 3.36.30.1 (2015123001)
------------------------------------------------------------------
- New: Disable summarize of attempts.
- CONTRIB-5605: Quotation marks breaks the js-code in game Cryptex.
- Prevent Style Override of Crossword.
- Add missing language string game:addinstance.
- More RTL fixes.
- Fixing problems on Millionaire with quiz.
- Fixing in crossword no horizontal legend and print button.

Changes in version 3.36.29.1 (2015122901)
------------------------------------------------------------------
- Fixing coding style.


Change for year 2015
------------------------------------------------------------------
- Fix: Millionaire with no random quiz questions.
- New: User defined language at hangman and crossword.
- Fix: Multianswer questions.
- Fix: Hangman with greek and english words in the same game.
- New: Multianswer questions on Book with questions Game.
- Fix: Ignore multianswer questions on Millionaire Game.
- New: Multianswer question on Sudoku Game.
- New: Multianswer question on Snakes and Ladders Game.
- New: FinishGame button on Cryptex Game.
- New: Print button on Cryptex Game
- Fix: Cryptex - Show grade while game continues
- CONTRIB-5816: Question bank category not retrieved when Crossword is …
- CONTRIB-5816: Question bank category not retrieved when Crossword is …


Changes for year 2014
-----------------------
- Fix:Problem on deletion of a game and restore a course
- Fix:Moodle 2.8 compatibility
- Fix: Change in description of parameters for game 'Snake and Laddes'
- New: Image set in hangman
- Fix: version.php compatibility with Moodle 2.7
- Fix: Images in questions
- Fix: Snakes compatibility with Moodle 1.6
- Fix: Event compatibility with Moodle 1.6
- New: parameter disabletransformuppercase in crossword
- Fix: Compatibility with Moodle 2.7
- New: Events in Moodle 2.7
- Fix rtl: very basic fix so all games can be displayed in RTL
- Fix: Problem when viewing the second page of game attempts
- Fix: Millionaire error on Moodle 2.5
- Fix: print_table deprecated
- Fix: export to html of millionaire


Changes for year 2013
------------------------------------------------------------------
- Fix: export to html of cross and millionaire
- CONTRIB-4724: New function module_scale_used_anywhere() should be imp…
- CONTRIB-4773 fixed various errors from Tim Lock
- CONTRIB-4774 fix:crossword game activity throws database error
- New: Ability to hide some games
- Fix: Remove time limit
- Fix: Moodle 2.6 compatibility
- New Cross:Show/Hide print button
- Fix: English language file game/lang/en/game.php does not contain $st… 
- Fix: field game_attempts.lastremoteip now saves only the first 30 cha…
- Deleting file header.php for theme Essential
- Fix: Backward compatibility with Moodle 2.0 and textlib
- Fix: install.xml is now compatible with moodle 2.0
- Fix: Rename header.php to headergame.php
- Fix: Backups the field game.maxattempts
- Fix: Problem when a question contains an apostrophe
- Fix: Computing score in cross when using spaces
- Fix: Show the correct number of attempts to a user
- New: In parameters you can set the maximum number of attempts
- Fix: Grading method now works ok
- New: Writes the version of game at the form of editing the game
- New: Cross,Cryptex can set the minimum number of words that a cross/c… 
- Remove translations
- New string: modulename_help
- Fix: Moodle 2.5 missing settype
- Fix: Problem when using subcategories in questions
- Fix: Snakes - Show message when not specified background images
- Fix: Millionaire - Problem when using question with question with les…
- Fix: ShowAnswers database error when using categories in glossaries.
- Cross: Set N20 at least 15 (usefull when contains only small words)
- Snake: Fix a problem with imasrc on heavy load


Changes for year 2012
------------------------------------------------------------------
- Fix: HiddenPicture show answers when using a quiz
- Fix: HiddenPicture: Default width / height
- Fix: Completion settings
- Fix: creating new object
- Fix: Remove reference to yui2_lib
- Fix: release in version.php
- First commit 2.0 to git


Changes for year 2011
------------------------------------------------------------------


Changes for year 2010
------------------------------------------------------------------
- Basque (eu) translation by Juan Ezeiza
- Complete German translation by Joachim Vogelgesang
- Translations of some Spanish  (es) words from Carolina Avila
- The games now checks how many times is used each word
- Fix: syntax error in get_records_select
- Fix for questions and quiz as input
- Support for right to left allignment in Hangman, Crossword and Cryptex
- First version for Moodle 2


Changes for year 2009
-----------------------
- Millionaire: Select questions from quiz not randomly but serial
- Better German (de) translation by Mike graf
- Better random selection
- Cryptex: Max tries on each question
- Hebrew (he) translation by Nadav Kavalerchik
- Hangman: Export picture-hangman to mobile phone
- Hangman: Now support attachements from glossary


Changes for year 2008
-----------------------
- Cross: Export to html and printing capability
- Fixes: Problem when we select a category in glossary
- New: First version of gradebook on moodle 1.9
- Hangman: Fix - After guessing the word correctly the message displayed after "Congratulations" is not the feedback to the correct answer, but the feedback to the last incorrect question.
- Hidden picture: Fixes a problem when filenames are capitals
- Crossword: '-','/' are not bad chars now
- Doesn't select the correct answer where there are more answers in one question
- Database postgres compatibility
- Polish (pl) translation
- Include subcategories of questions
- Translation to Dutch (nl)
- Hangman: Export to html - works with other languages
- Fix: Problem in mod.html
- Javame: If can't create the jar file creates a zip file instead
- Tables for exporting to javame
- Translaton to Rusian (ru) by Ивченко Анатолий
- Problem when the language of hangman words is different from current language
- When you finish a game and press the 'New game' show some errors
- CONTRIB-446 : When an answer is wrong in millionaire game, the correct answer shown by the system is also wrong
- MDL-15454: After editing a new game for the first time should go to mod.php/update page to make the settings for this game
- Norwegian (no) translation
- Adding game module to CONTRIB.
