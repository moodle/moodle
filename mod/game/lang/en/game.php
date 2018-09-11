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
 * Strings for component 'game', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package mod_game
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// File bookquiz/importodt.php.
$string[ 'bookquiz_not_select_book'] = 'You have not select book';

// File bookquiz/play.php.
$string[ 'bookquiz_empty'] = 'The book is empty';
$string[ 'sudoku_submit'] = 'Grade answers';

// File bookquiz/questions.php.
$string[ 'bookquiz_categories'] = 'Categories';
$string[ 'bookquiz_chapters'] = 'Chapters';
$string[ 'bookquiz_numquestions'] = 'Questions';

// Check.php.
$string[ 'common_problems'] = 'Common problems';
$string[ 'millionaire_also_multichoice'] = 'Multichoice answers without single correct answer';
$string[ 'common_problems_allowspaces'] = 'There are words with spaces but in the game, spaces are not allowed';
$string[ 'common_problems_shortanswer_hangman'] = 'Not all characters are in the language of game';
$string[ 'common_problems_crossword_param1'] = "'Maximum number of cols/rows' is too small";
$string[ 'millionaire_no_multichoice_questions'] = 'There are no multichoice questions';

// Classes.
$string[ 'eventgamecreated'] = 'Game created';
$string[ 'eventgamedeleted'] = 'Game deleted';
$string[ 'eventgamesupdated'] = 'Game updated';
$string[ 'eventgameviewed'] = 'Game viewed';
$string[ 'eventgameplayed'] = 'Game played';

// File cross/cross_class.php.
$string[ 'lettersall'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

// File cross/crossdb_class.php.
$string[ 'and'] = 'and';
$string[ 'cross_correct'] = 'corrert character';
$string[ 'cross_corrects'] = 'correct characters';
$string[ 'cross_error'] = 'wrong character';
$string[ 'cross_errors'] = 'wrong characters';
$string[ 'cross_found_many'] = 'Found';
$string[ 'cross_found_one'] = 'Found';
$string[ 'grade'] = 'Grade';
$string[ 'cross_disabletransformuppercase'] = 'Disables text-transform:uppercase in CSS';

// File cross/play.php.
$string[ 'cross_across'] = 'Across';
$string[ 'cross_checkbutton'] = 'Check crossword';
$string[ 'cross_down'] = 'Down';
$string[ 'cross_endofgamebutton'] = 'End of crossword game';
$string[ 'cross_error_containsbadchars'] = 'The word contains illegal characteres';
$string[ 'cross_error_wordlength1'] = 'The correct word contains ';
$string[ 'cross_error_wordlength2'] = ' letters.';
$string[ 'cross_pleasewait'] = 'Please wait while cross is loading';
$string[ 'cross_welcome'] = '<h3>Welcome!</h3><p>Click on a word to begin/continue.</p>';
$string[ 'letter'] = 'letter';
$string[ 'letters'] = 'letters';
$string[ 'nextgame'] = 'New game';
$string[ 'no_words'] = 'There are no words';
$string[ 'print'] = 'Print';
$string[ 'win'] = 'Congratulations !!!';

// File cryptex/play.php.
$string[ 'finish'] = 'End of game';

// File db/access.php.
$string[ 'game:addinstance'] = 'Add a new game';
$string[ 'game:attempt'] = 'Play game';
$string[ 'game:deleteattempts'] = 'Delete attempts';
$string[ 'game:grade'] = 'Grade games manually';
$string[ 'game:manage'] = 'Manage';
$string[ 'game:manageoverrides'] = 'Manage game overrides';
$string[ 'game:preview'] = 'Preview Games';
$string[ 'game:reviewmyattempts'] = 'reviewmyattempts';
$string[ 'game:view'] = 'view';
$string[ 'game:viewreports'] = 'viewreports';

// File hangman/play.php.
$string[ 'hangman_correct_phrase'] = 'The correct phrase was: ';
$string[ 'hangman_correct_word'] = 'The correct word was: ';
$string[ 'hangman_gradeinstance'] = 'Grade in whole game';
$string[ 'hangman_letters'] = 'Letters: ';
$string[ 'hangman_restletters_many'] = 'You have <b>{$a}</b> tries';
$string[ 'hangman_restletters_one'] = 'You have <b>ONLY 1</b> try';
$string[ 'hangman_wrongnum'] = 'Wrong: %d out of %d';
$string[ 'nextword'] = 'Next word';

// File hiddenpicture/play.php.
$string[ 'hiddenpicture_mainsubmit'] = 'Grade main answer';
$string[ 'hiddenpicture_nocols'] = 'Have to specify the number of cols horizontaly';
$string[ 'hiddenpicture_nomainquestion'] = 'There are no glossary entries on glossary {$a->name} with an attached picture';
$string[ 'hiddenpicture_norows'] = 'Have to specify the number of cols verticaly';
$string[ 'must_select_glossary'] = 'You must select a glossary';
$string[ 'no_questions'] = "There are no questions";
$string[ 'noglossaryentriesfound'] = 'No glossary entries found';

// File millionaire/play.php.
$string[ 'millionaire_must_select_questioncategory'] = 'You must select one question category';
$string[ 'millionaire_must_select_quiz'] = 'You must select one quiz';
$string[ 'millionaire_lettersall'] = '-';

// File report/overview/report.php.
$string[ 'allattempts'] = 'Show all tries';
$string[ 'allstudents'] = 'Show all $a';
$string[ 'attemptduration'] = 'Attempt duration';
$string[ 'attemptsonly'] = 'Show only students with attempts';
$string[ 'deleteattemptcheck'] = 'Are you absolutely sure you want to completely delete these attempts?';
$string[ 'displayoptions'] = 'Display options';
$string[ 'downloadods'] = 'Download in ODS format';
$string[ 'feedback'] = 'Feedback';
$string[ 'noattemptsonly'] = 'Show $a with no attempts only';
$string[ 'numattempts'] = '$a->studentnum $a->studentstring have made $a->attemptnum attempts';
$string[ 'pagesize'] = 'Questions per page:';
$string[ 'reportoverview'] = 'Overview';
$string[ 'selectall'] = 'Select all';
$string[ 'selectnone'] = 'Deselect all';
$string[ 'showdetailedmarks'] = 'Show mark details';
$string[ 'startedon'] = 'Started on';
$string[ 'timecompleted'] = 'Completed';
$string[ 'unfinished'] = 'open';
$string[ 'withselected'] = 'With selected';

// File snakes/play.php.
$string[ 'snakes_dice'] = 'Dice, $a spots.';
$string[ 'snakes_player'] = 'Player, position: $a.';

// File sudoku/create.php.
$string[ 'sudoku_create_count'] = 'Number of sudokus that will be created';
$string[ 'sudoku_create_start'] = 'Start creating sudokus';
$string[ 'sudoku_creating'] = 'Creating <b>{$a}</b> sudoku';

// File sudoku/play.php.
$string[ 'sudoku_finishattemptbutton'] = 'End of game';
$string[ 'sudoku_guessnumber'] = 'Guess the correct number';
$string[ 'sudoku_noentriesfound'] = 'No words found in glossary';

// File export.php.
$string[ 'export'] = 'Export';
$string[ 'html_hascheckbutton'] = 'Has check button:';
$string[ 'html_hasprintbutton'] = 'Has print button:';
$string[ 'html_title'] = 'Title of html:';
$string[ 'javame_createdby'] = 'Created by:';
$string[ 'javame_description'] = 'Description:';
$string[ 'javame_filename'] = 'Filename:';
$string[ 'javame_icon'] = 'Icon:';
$string[ 'javame_maxpictureheight'] = 'Max picture height:';
$string[ 'javame_maxpicturewidth'] = 'Max picture width:';
$string[ 'javame_name'] = 'Name:';
$string[ 'javame_type'] = 'Type:';
$string[ 'javame_vendor'] = 'Vendor:';
$string[ 'javame_version'] = 'Version:';

// File exporthtml_hangman.php.
$string[ 'hangman_loose'] = '<BIG><B>Game over</B></BIG>';
$string[ 'html_hangman_new'] = 'New';

// File exporthtml_millionaire.php.
$string[ 'millionaire_helppeople'] = 'Help of people';
$string[ 'millionaire_info_people'] = 'People say';
$string[ 'millionaire_info_telephone'] = 'I think that the correct answer is ';
$string[ 'millionaire_info_wrong_answer'] = 'Your answer is wrong<br>The right answer is:';
$string[ 'millionaire_quit'] = 'Quit';
$string[ 'millionaire_sourcemodule_must_quiz_question'] = 'For the millionaire the source must be {$a} or questions and not';
$string[ 'millionaire_telephone'] = 'Help of telephone';
$string[ 'must_select_questioncategory'] = 'You must select a question category';
$string[ 'must_select_quiz'] = 'You must select a quiz';

// File exporthtml_snakes.php.
$string[ 'html_snakes_check'] = 'Check';
$string[ 'html_snakes_correct'] = 'Correct!';
$string[ 'html_snakes_no_selection'] = 'Have to select something!';
$string[ 'html_snakes_wrong'] = "Your answer isn't correct. Stay on the same seat.";
$string[ 'score'] = 'Score';

// File index.php.
$string[ 'helpbookquiz'] = 'When the student answers correct can go to the next chapter.';
$string[ 'helpcross'] = 'This game takes words from either a Glossary or quiz short answer questions and generates a random crossword puzzle. Teacher can set the maximum number of columns/rows or words that contains. Student can press the button “Check crossword” to check if the answers are correct. Every crossword is dynamic so it is different to every student.';
$string[ 'helpcryptex'] = 'This game is like a crossword but the answers are hidden inside a random cryptex.';
$string[ 'helphangman'] = 'This game takes words from either a Glossary or quiz short answer questions and generates a hangman puzzle. Teacher can set the number of words that each game contains, if shows the first or last letter, or if show the question or the answer at the end.';
$string[ 'helphiddenpicture'] = 'The hidden picture game uncovers each piece of a picture for each question correctly answered by the student. Each number in the hidden picture game displays a question to the student such that when the student answers the question correctly, the number is uncovered to display a piece of the picture.';
$string[ 'helpmillionaire'] = 'A question is displayed to the student which if answered correctly moves up to the next number in the game until the user has completed the questions. If a question is answered incorrectly, the game is over.';
$string[ 'helpsnakes'] = 'A question is displayed to the student which if answered correctly, displays a number on the dice, then game piece moves up the number displayed on the dice.';
$string[ 'helpsudoku'] = 'This game shows a sudoku puzzle to the students with not enough numbers to allow it to be solved. For each question the student correctly answers an additional number is slotted into the puzzle to make it easier to solve.';
$string['modulename'] = 'Game';
$string['modulename_help'] = 'This module contains 8 games: Hangman,Crossword, Cryptex, Millionaire, Sudoku, The hidden picture, Snakes and Ladders and Book with questions';
$string[ 'modulenameplural'] = 'Games';
$string[ 'pluginadministration'] = 'Game administration';
$string[ 'pluginname'] = 'Game';

// File lib.php.
$string[ 'attempt'] = 'Attempt';
$string[ 'bookquiz_questions'] = 'Associate question categories to chapter of book';
$string[ 'export_to_html'] = 'Export to HTML';
$string[ 'export_to_javame'] = 'Export to Javame';
$string[ 'game_bookquiz'] = 'Book with questions';
$string[ 'game_cross'] = 'Crossword';
$string[ 'game_cryptex'] = 'Cryptex';
$string[ 'game_hangman'] = 'Hangman';
$string[ 'game_hiddenpicture'] = 'Hidden Picture';
$string[ 'game_millionaire'] = 'Millionaire';
$string[ 'game_snakes'] = 'Snakes and Ladders';
$string[ 'game_sudoku'] = 'Sudoku';
$string[ 'info'] = 'Info';
$string[ 'noattempts'] = 'No attempts have been made on this game';
$string[ 'percent'] = 'Percent';
$string[ 'reset_game_all'] = 'Delete tries from all games';
$string[ 'reset_game_deleted_course'] = 'Delete tries from deleted courses';
$string[ 'results'] = 'Results';
$string[ 'showanswers'] = 'Show answers';
$string[ 'showattempts'] = 'Show attempts';

// File locallib.php.
$string[ 'attemptfirst'] = 'First attempt';
$string[ 'attemptlast'] = 'Last attempt';
$string[ 'convertfrom'] = '-';
$string[ 'convertto'] = '-';
$string[ 'gradeaverage'] = 'Average grade';
$string[ 'gradehighest'] = 'Highest grade';

// File mod_form.php.
$string[ 'bookquiz_layout'] = 'Layout';
$string[ 'bookquiz_layout0'] = 'Question at the top of the book';
$string[ 'bookquiz_layout1'] = 'Question at the bottom of the book';
$string[ 'bookquiz_options'] = 'Bookquiz options';
$string[ 'bottomtext'] = 'Text at the bottom of page';
$string[ 'cross_layout'] = 'Layout';
$string[ 'cross_layout0'] = 'Phrases on the bottom of cross';
$string[ 'cross_layout1'] = 'Phrases on the right of cross';
$string[ 'cross_maxcols'] = 'Maximum number of cols/rows';
$string[ 'cross_max_attempts'] = 'Maximum number of attempts';
$string[ 'cross_maxcomputetime'] = 'Maximum compute time in seconds';
$string[ 'cross_maxwords'] = 'Maximum number of words';
$string[ 'cross_minwords'] = 'Minimum number of words';
$string[ 'cross_options'] = 'Crossword options';
$string[ 'cryptex_maxtries'] = 'Max tries';
$string[ 'cryptex_options'] = 'Cryptex options';
$string[ 'disablesummarize'] = 'Disable summarize';
$string[ 'gameclose'] = 'Close the game';
$string[ 'gameopen'] = 'Open the game';
$string[ 'gameopenclose'] = 'Open and close dates';
$string[ 'gameopenclose_help'] = 'Students can only start their attempt(s) after the open time and they must complete their attempts before the close time.';
$string[ 'grademethod'] = 'Grading method';
$string[ 'glossary_only_approved'] = "Only approved or teacher's glossary entries";
$string[ 'hangman_allowspaces'] = 'Allow spaces in words';
$string[ 'hangman_allowsub'] = 'Allow the symbol - in words';
$string[ 'hangman_imageset'] = 'Select the images of hangman';
$string[ 'hangman_language'] = 'Language of words';
$string[ 'hangman_maximum_number_of_errors'] = 'Maximum number or errors (have to be images named hangman_0.jpg, hangman_1.jpg, ...)';
$string[ 'hangman_maxtries'] = 'Number of words per game';
$string[ 'hangman_options'] = 'Hangman options';
$string[ 'hangman_showcorrectanswer'] = 'Show the correct answer after the end';
$string[ 'hangman_showfirst'] = 'Show first letter of hangman';
$string[ 'hangman_showlast'] = 'Show last letter of hangman';
$string[ 'hangman_showquestion'] = 'Show the questions ?';
$string[ 'header_footer_options'] = 'Header/Footer Options';
$string[ 'language_user_defined'] = 'User defined language';
$string[ 'hiddenpicture_across'] = 'Cells horizontal';
$string[ 'hiddenpicture_down'] = 'Cells down';
$string[ 'hiddenpicture_height'] = 'Set height of picture to (in pixels)';
$string[ 'hiddenpicture_options'] = '\'Hidden Picture\' options';
$string[ 'hiddenpicture_pictureglossary'] = 'The glossary for main question and picture';
$string[ 'hiddenpicture_width'] = 'Set width of picture to (in pixels)';
$string[ 'millionaire_background'] = 'Background color';
$string[ 'millionaire_options'] = 'Millionaire\' options';
$string[ 'millionaire_shuffle'] = 'Randomize questions';
$string[ 'snakes_background'] = 'Background';
$string[ 'snakes_cols'] = 'Cols';
$string[ 'snakes_data'] = 'Positions of Snakes and Ladders';
$string[ 'snakes_file'] = 'File for background';
$string[ 'snakes_footerx'] = 'Space at bootom left (in pixels)';
$string[ 'snakes_footery'] = 'Space at bottom right (in pixels)';
$string[ 'snakes_headerx'] = 'Space at up left (in pixels)';
$string[ 'snakes_headery'] = 'Space at up right (in pixels)';
$string[ 'snakes_layout0'] = 'Question at the top of the image';
$string[ 'snakes_layout1'] = 'Question at the bottom of the image';
$string[ 'snakes_options'] = '\'Snakes and Ladders\' options';
$string[ 'snakes_rows'] = 'Rows';
$string[ 'sourcemodule'] = 'Source of questions';
$string[ 'sourcemodule_book'] = 'Select a book';
$string[ 'sourcemodule_glossary'] = 'Select glossary';
$string[ 'sourcemodule_glossarycategory'] = 'Select category of glossary';
$string[ 'sourcemodule_include_subcategories'] = 'Include subcategories';
$string[ 'sourcemodule_question'] = 'Questions';
$string[ 'sourcemodule_questioncategory'] = 'Select question category';
$string[ 'sourcemodule_quiz'] = 'Select quiz';
$string[ 'sudoku_maxquestions'] = 'Maximum number of questions';
$string[ 'sudoku_options'] = 'Sudoku options';
$string[ 'toptext'] = 'Text at the top of page';
$string[ 'userdefined'] = 'User defined';
$string[ 'different_glossary_category'] = "The selected category doesn't corespond to selected glossary";
$string[ 'highscore'] = 'Show high score (number of students)';

// File preview.php.
$string[ 'only_teachers'] = 'Only teacher can see this page';
$string[ 'preview'] = 'Preview';

// File review.php.
$string[ 'attempts'] = 'Attempts';
$string[ 'completedon'] = 'Completed on';
$string[ 'outof'] = '{$a->grade} out of a maximum of {$a->maxgrade}';
$string[ 'review'] = 'Review';
$string[ 'reviewofattempt'] = 'Review of Attempt {$a}';
$string[ 'showall'] = 'Show all';
$string[ 'startagain'] = 'Start again';
$string[ 'timetaken'] = 'Time taken';
$string[ 'col_highscores'] = 'High scores';

// File settings.php.
$string[ 'hangmanimagesets'] = 'Number of image sets used by hangman';
$string[ 'hidebookquiz'] = 'Hide the "Book with questions" game';
$string[ 'hidecross'] = 'Hide the Crossword game';
$string[ 'hidecryptex'] = 'Hide the Cryptex game';
$string[ 'hidehangman'] = 'Hide the Hangman game';
$string[ 'hidehiddenpicture'] = 'Hide the "Hidden Picture" game';
$string[ 'hidemillionaire'] = 'Hide the Millionaire game';
$string[ 'hidesnakes'] = 'Hide the "Snakes and Ladders" game';
$string[ 'hidesudoku'] = 'Hide the Sudoku game';
$string[ 'confighangmanimagesets'] = 'Configs how many set of images are used by hangman';
$string[ 'confighidebookquiz'] = 'Configs if the "Book with questions" game is shown to teachers or not';
$string[ 'confighidecross'] = 'Configs if the Crossword game is shown to teachers or not';
$string[ 'confighidecryptex'] = 'Configs if the Cryptex game is shown to teachers or not';
$string[ 'confighidehangman'] = 'Configs if the Hangman game is shown to teachers or not';
$string[ 'confighidehiddenpicture'] = 'Configs if the "Hidden Picture" game is shown to teachers or not';
$string[ 'confighidemillionaire'] = 'Configs if the Millionaire game is shown to teachers or not';
$string[ 'confighidesnakes'] = 'Configs if the "Snakes and Ladders" game is shown to teachers or not';
$string[ 'confighidesudoku'] = 'Configs if the Sudoku game is shown to teachers or not';

// File showanswers.php.
$string[ 'clearrepetitions'] = 'Clear statistics';
$string[ 'computerepetitions'] = 'Compute statistics again';
$string[ 'feedbacks'] = 'Messages correct answer';
$string[ 'repetitions'] = 'Repetitions';

// File showattempts.php.
$string[ 'lastip'] = 'IP student';
$string[ 'showsolution'] = 'solution';
$string[ 'timefinish'] = 'End of game';
$string[ 'timelastattempt'] = 'Last attempt';
$string[ 'timestart'] = 'Start';

// File tabs.php.

// File view.php.
$string[ 'attemptgamenow'] = 'Attempt game now';
$string[ 'comment'] = 'Comment';
$string[ 'continueattemptgame'] = 'Continue a previous attempt of game';
$string[ 'gameclosed'] = 'This game closed on {$a}';
$string[ 'gamecloseson'] = 'This game will close at {$a}';
$string[ 'gamenotavailable'] = 'The game will not be available until {$a}';
$string[ 'gameopenedon'] = 'This game opened at {$a}';
$string[ 'reattemptgame'] = 'Reattempt game';
$string[ 'yourfinalgradeis'] = 'Your final grade for this game is {$a}.';
