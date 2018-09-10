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
 * This page export the game to html for games: cross, hangman
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once( "locallib.php");
require_once( "exportjavame.php");

/**
 * Exports to html.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param string $html
 */
function game_onexporthtml( $game, $context, $html) {
    global $CFG;

    $destdir = game_export_createtempdir();

    switch( $game->gamekind) {
        case 'cross';
            game_onexporthtml_cross( $game, $context, $html, $destdir);
            break;
        case 'hangman':
            game_onexporthtml_hangman( $game, $context, $html, $destdir);
            break;
        case 'snakes':
            game_onexporthtml_snakes( $game, $html, $destdir, $context);
            break;
        case 'millionaire':
            game_onexporthtml_millionaire( $game, $context, $html, $destdir);
            break;
    }

    remove_dir( $destdir);
}

/**
 * Exports cross to html.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param string $html
 * @param string $destdir
 */
function game_onexporthtml_cross( $game, $context, $html, $destdir) {

    global $CFG, $DB;

    if ( $html->filename == '') {
        $html->filename = 'cross';
    }

    $filename = $html->filename . '.htm';

    require( "cross/play.php");
    $attempt = game_getattempt( $game, $crossrec, true);
    if ( $crossrec == false) {
        game_cross_new( $game, $attempt->id, $crossm);
        $attempt = game_getattempt( $game, $crossrec);
    }

    $ret = game_export_printheader( $html->title);
    echo "$ret<br>";

    ob_start();

    game_cross_play( 0, $game, $attempt, $crossrec, '', true, false, false, false,
        $html->checkbutton, true, $html->printbutton, false, $context);

    $outputstring = ob_get_contents();
    ob_end_clean();

    $course = $DB->get_record( 'course', array( 'id' => $game->course));

    $filename = $html->filename . '.htm';

    file_put_contents( $destdir.'/'.$filename, $ret . "\r\n" . $outputstring);

    $filename = game_onexporthtml_cross_repair_questions( $game, $context, $filename, $destdir);

    game_send_stored_file( $filename);
}

/**
 * Repairs questions.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param string $filename
 * @param string $destdir
 */
function game_onexporthtml_cross_repair_questions( $game, $context, $filename, $destdir) {
    global $CFG, $DB;

    $filehandle = fopen( $destdir.'/'.$filename, "rb");

    $found = false;
    $files = array();
    $contextcourse = false;
    $linesbefore = array();
    $linesafter = array();
    while (!feof($filehandle) ) {
        $line = fgets( $filehandle);

        if ($found) {
            if ( strpos( $line, 'new Array')) {
                $linesafter[] = $line;
                break;
            }
            $array .= $line;
            continue;
        }

        if (strpos( $line, 'Clue = new Array') === false) {
            $linesbefore[] = $line;
            continue;
        }

        $array = $line;
        $found = true;
    }
    while (!feof($filehandle) ) {
        $linesafter[] = fgets( $filehandle);
    }

    fclose($filehandle);

    $search = $CFG->wwwroot.'/pluginfile.php';
    $pos = 0;
    $search = '"'.$CFG->wwwroot.'/pluginfile.php/'.$context->id.'/mod_game/';
    $len = strlen( $search);
    $start = 0;
    $filescopied = false;
    for (;;) {
        $pos1 = strpos( $array, $search, $start);
        if ( $pos1 == false) {
            break;
        }

        $pos2 = strpos( $array, '\"', $pos1 + $len);
        if ( $pos2 == false) {
            break;
        }

        // Have to copy the files.
        if ($contextcourse === false) {
            mkdir( $destdir.'/images');
            if (!$contextcourse = get_context_instance(CONTEXT_COURSE, $game->course)) {
                print_error('nocontext');
            }
            $fs = get_file_storage();
        }

        $inputs = explode( '/', substr( $array, $pos1 + $len, $pos2 - $pos1 - $len));

        $filearea = $inputs[ 0];
        $id = $inputs[ 1];
        $fileimage = urldecode( $inputs[ 2]);
        $component = 'question';

        $params = array( 'component' => $component, 'filearea' => $filearea,
            'itemid' => $id, 'filename' => $fileimage, 'contextid' => $context, 'contextid' => $contextcourse->id);
        $rec = $DB->get_record( 'files', $params);
        if ( $rec == false) {
            break;
        }

        if (!$file = $fs->get_file_by_hash($rec->pathnamehash) or $file->is_directory()) {
            continue;
        }

        $posext = strrpos( $fileimage, '.');
        $filenoext = substr( $fileimage, $posext);
        $ext = substr( $fileimage, $posext + 1);
        for ($i = 0;; $i++) {
            $newfile = $filenoext.$i;
            $newfile = md5( $newfile).'.'.$ext;
            if (!array_search( $newfile, $files)) {
                break;
            }
        }
        $file->copy_content_to( $destdir.'/images/'.$newfile);
        $filescopied = true;

        $array = substr( $array, 0, $pos1 + 1).'images/'.$newfile.substr( $array, $pos2);
    }

    if ($filescopied == false) {
        return $destdir.'/'.$filename;
    }

    $linesbefore[] = $array;
    foreach ($linesafter as $line) {
        $linesbefore [] = $line;
    }
    file_put_contents( $destdir.'/'.$filename, $linesbefore);

    $pos = strrpos( $filename, '.');
    if ($pos === false) {
        $filezip = $filename.'.zip';
    } else {
        $filezip = substr( $filename, 0, $pos).'.zip';
    }

    $filezip = game_create_zip( $destdir, $game->course, $filezip);

    return $filezip;
}

/**
 * Prints the header.
 *
 * @param string $title
 * @param boolean $showbody
] */
function game_export_printheader( $title, $showbody=true) {
    $ret = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
    $ret .= '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="el" xml:lang="el">'."\n";
    $ret .= "<head>\n";
    $ret .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
    $ret .= '<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">'."\n";
    $ret .= "<title>$title</title>\n";
    $ret .= "</head>\n";
    if ($showbody) {
        $ret .= "<body>";
    }

    return $ret;
}

/**
 * Exports to html a hangman game.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param string $html
 * @param string $destdir
 */
function game_onexporthtml_hangman( $game, $context, $html, $destdir) {

    global $CFG, $DB;

    if ($html->filename == '') {
        $html->filename = 'hangman';
    }

    if ($game->param10 <= 0) {
        $game->param10 = 6;
    }

    $filename = $html->filename . '.htm';

    $ret = game_export_printheader( $html->title, false);
    $ret .= "\r<body onload=\"reset()\">\r";

    $exportattachment = ( $html->type == 'hangmanp');

    $map = game_exmportjavame_getanswers( $game, $context, $exportattachment, $destdir, $files);
    if ($map == false) {
        print_error( get_string('no_words', 'game'));
    }

    ob_start();

    // Here is the code of hangman.
    require_once( "exporthtml_hangman.php");

    $outputstring = ob_get_contents();
    ob_end_clean();

    $courseid = $game->course;
    $course = $DB->get_record( 'course', array( 'id' => $courseid));

    $filename = $html->filename . '.htm';
    file_put_contents( $destdir.'/'.$filename, $ret . "\r\n" . $outputstring);

    if ($html->type != 'hangmanp') {
        // Not copy the standard pictures when we use the "Hangman with pictures".
        $src = $CFG->dirroot.'/mod/game/pix/hangman/1';
        $handle = opendir( $src);
        while (false !== ($item = readdir($handle))) {
            if ($item != '.' && $item != '..') {
                if (!is_dir($src.'/'.$item)) {
                    $itemdest = $item;

                    if ( strpos( $item, '.') === false) {
                        continue;
                    }

                    copy( $src.'/'.$item, $destdir.'/'.$itemdest);
                }
            }
        }
    }

    $filezip = game_create_zip( $destdir, $courseid, $html->filename.'.zip');
    game_send_stored_file( $filezip);
}

/**
 * Exports to html.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param string $html
 * @param string $destdir
 */
function game_onexporthtml_millionaire( $game, $context, $html, $destdir) {

    global $CFG, $DB;

    if ($html->filename == '') {
        $html->filename = 'millionaire';
    }

    $filename = $html->filename . '.htm';

    $ret = game_export_printheader( $html->title, false);
    $ret .= "\r<body onload=\"Reset();\">\r";

    // Here is the code of millionaire.
    require( "exporthtml_millionaire.php");

    $questions = game_millionaire_html_getquestions( $game, $context, $maxanswers, $maxquestions, $retfeedback, $destdir, $files);
    ob_start();

    game_millionaire_html_print( $game, $questions, $maxanswers);

    // End of millionaire code.
    $outputstring = ob_get_contents();
    ob_end_clean();

    $courseid = $game->course;
    $course = $DB->get_record( 'course', array( 'id' => $courseid));

    $filename = $html->filename . '.htm';

    file_put_contents( $destdir.'/'.$filename, $ret . "\r\n" . $outputstring);

    // Copy the standard pictures of Millionaire.
    $src = $CFG->dirroot.'/mod/game/pix/millionaire/1';
    $handle = opendir( $src);
    while (false !== ($item = readdir($handle))) {
        if ($item != '.' && $item != '..') {
            if (!is_dir($src.'/'.$item)) {
                $itemdest = $item;

                if (strpos( $item, '.') === false) {
                    continue;
                }

                copy( $src.'/'.$item, $destdir.'/'.$itemdest);
            }
        }
    }

    $filezip = game_create_zip( $destdir, $courseid, $html->filename.'.zip');
    game_send_stored_file($filezip);
}

/**
 * Exports to html a "Snakes and Ladders" game.
 *
 * @param stdClass $game
 * @param string $html
 * @param string $destdir
 * @param stdClass $context
 */
function game_onexporthtml_snakes( $game, $html, $destdir, $context) {
    require_once( "exporthtml_millionaire.php");

    global $CFG, $DB;

    if ($html->filename == '') {
        $html->filename = 'snakes';
    }

    $filename = $html->filename . '.htm';

    $ret = '';

    $board = game_snakes_get_board( $game);

    if ( ($game->sourcemodule == 'quiz') or ($game->sourcemodule == 'question')) {
        $questionsm = game_millionaire_html_getquestions( $game, $context, $maxquestions,
            $countofquestionsm, $retfeedback, $destdir, $files);
    } else {
        $questionsm = array();
        $countofquestionsm = 0;
        $retfeedback = '';
    }
    $questionss = game_exmportjavame_getanswers( $game, $context, false, $destdir, $files);

    ob_start();

    // Here is the code of Snakes and Ladders.
    require( "exporthtml_snakes.php");

    $outputstring = ob_get_contents();
    ob_end_clean();

    $courseid = $game->course;
    $course = $DB->get_record( 'course', array( 'id' => $courseid));

    $filename = $html->filename . '.htm';

    file_put_contents( $destdir.'/'.$filename, $ret . "\r\n" . $outputstring);

    $src = $CFG->dirroot.'/mod/game/export/html/snakes';
    game_copyfiles( $src, $destdir);

    mkdir( $destdir .'/css');
    $src = $CFG->dirroot.'/mod/game/export/html/snakes/css';
    game_copyfiles( $src, $destdir.'/css');

    mkdir( $destdir .'/js');
    $src = $CFG->dirroot.'/mod/game/export/html/snakes/js';
    game_copyfiles( $src, $destdir.'/js');
    unzip_file($destdir.'/js/js.zip', $destdir.'/js', false);
    unlink( $destdir.'/js/js.zip');

    mkdir( $destdir .'/images');
    $destfile = $destdir.'/images/'.$board->fileboard;
    if ( $game->param3 != 0) {
        // Is a standard board.
        copy( $board->imagesrc, $destfile);
    } else {
        $cmg = get_coursemodule_from_instance('game', $game->id, $game->course);
        $modcontext = get_context_instance(CONTEXT_MODULE, $cmg->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($modcontext->id, 'mod_game', 'snakes_board', $game->id);
        foreach ($files as $f) {
            if ( $f->is_directory()) {
                continue;
            }
            break;
        }
        $f->copy_content_to( $destfile);
    }

    $a = array( 'player1.png', 'dice1.png', 'dice2.png', 'dice3.png', 'dice4.png', 'dice5.png', 'dice6.png', 'numbers.png');
    foreach ($a as $file) {
        copy( $CFG->dirroot.'/mod/game/snakes/1/'.$file, $destdir.'/images/'.$file);
    }

    $filezip = game_create_zip( $destdir, $courseid, $html->filename.'.zip');
    game_send_stored_file($filezip);
}

/**
 * Copy files from source directory to dest directory.
 *
 * @param string $src
 * @param string $destdir
 */
function game_copyfiles( $src, $destdir) {
    $handle = opendir( $src);
    while (($item = readdir($handle)) !== false) {
        if ( $item == '.' or $item == '..') {
            continue;
        }

        if ( strpos( $item, '.') === false) {
            continue;
        }

        if (is_dir($src.'/'.$item)) {
            continue;
        }

        copy( $src.'/'.$item, $destdir.'/'.$item);
    }
    closedir($handle);
}
