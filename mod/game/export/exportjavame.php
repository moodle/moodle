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
 * This page export the game to javame for mobile phones
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Exports to javame.
 *
 * @param object $game
 * @param object $javame
 */
function game_onexportjavame( $game, $javame) {
    global $CFG, $DB;

    $courseid = $game->course;
    $course = $DB->get_record( 'course', array( 'id' => $courseid));

    $destdir = game_export_createtempdir();

    if ( $javame->type == 'hangmanp') {
        $destmobiledir = 'hangmanp';
    } else {
        $destmobiledir = 'hangman';
    }
    $src = $CFG->dirroot.'/mod/game/export/javame/'.$destmobiledir.'/simple';

    if ( $javame->filename == '') {
        $javame->filename = 'moodle'.$destmobiledir;
    }

    $handle = opendir( $src);
    while (false !== ($item = readdir($handle))) {
        if ($item != '.' && $item != '..') {
            if (!is_dir($src.'/'.$item)) {
                $itemdest = $item;

                if (substr( $item, -5) == '.java') {
                    continue;   // Don't copy the java source code files.
                }

                if (substr( $itemdest, -8) == '-1.class') {
                    $itemdest = substr( $itemdest, 0, -8).'$1.class';
                }

                copy( $src.'/'.$item, $destdir.'/'.$itemdest);
            }
        }
    }

    mkdir( $destdir.'/META-INF');

    game_exportjavame_exportdata( $src, $destmobiledir, $destdir, $game, $javame->maxpicturewidth, $javame->maxpictureheight);

    game_create_manifest_mf( $destdir.'/META-INF', $javame, $destmobiledir);

    $filejar = game_create_jar( $destdir, $course, $javame);
    if ($filejar == '') {
        $filezip = game_create_zip( $destdir, $course->id, $javame->filename.'.zip');
    } else {
        $filezip = '';
    }

    if ($destdir != '') {
        remove_dir( $destdir);
    }

    if ($filezip != '') {
        echo "unzip the $filezip in a directory and when you are in this directory use the command <br><b>jar ".
            "cvfm {$javame->filename}.jar META-INF/MANIFEST.MF<br></b> to produce the jar files<br><br>";
    }

    $file = ($filejar != '' ? $filejar : $filezip);
    game_send_stored_file( $file);
}

/**
 * Exports data.
 *
 * @param string $src
 * @param string $destmobiledir
 * @param string $destdir
 * @param stdClass $game
 * @param int $maxwidth
 * @param int $maxheight
 */
function game_exportjavame_exportdata( $src, $destmobiledir, $destdir, $game, $maxwidth, $maxheight) {
    global $CFG;

    mkdir( $destdir.'/'.$destmobiledir);

    $handle = opendir( $src);
    while (false !== ($item = readdir($handle))) {
        if ($item != '.' && $item != '..') {
            if (!is_dir($src.'/'.$item)) {
                if (substr( $item, -4) == '.jpg') {
                    copy( $src.'/'.$item, $destdir."/$destmobiledir/".$item);
                }
            }
        }
    }

    $lang = $game->language;
    if ($lang == '') {
        $lang = current_language();
    }
    $sourcefile = $src. '/lang/'.$lang.'/language.txt';
    if (!file_exists( $sourcefile)) {
        $sourcefile = $src. '/lang/'.$lang.'_utf8/language.txt';
    }
    copy( $sourcefile,  $destdir."/$destmobiledir/language.txt");

    $exportattachment = ( $destmobiledir == 'hangmanp');

    $map = game_exmportjavame_getanswers( $game, $exportattachment, false, $destdir, $files);
    if ($map == false) {
        print_error( 'No Questions');
    }

    if ($destmobiledir == 'hangmanp') {
        game_exportjavame_exportdata_hangmanp( $src, $destmobiledir, $destdir, $game, $map, $maxwidth, $maxheight);
        return;
    }

    $fp = fopen( $destdir."/$destmobiledir/hangman.txt", "w");
    fputs( $fp, "1.txt=$destmobiledir\r\n");
    fclose( $fp);

    $fp = fopen( $destdir."/$destmobiledir/1.txt", "w");
    foreach ($map as $line) {
        $s = game_upper( $line->answer) . '=' . $line->question;
        fputs( $fp, "$s\r\n");
    }
    fclose( $fp);
}

/**
 * Exports data of hangman with pictures.
 *
 * @param string $src
 * @param string $destmobiledir
 * @param string $destdir
 * @param stdClass $game
 * @param array $map
 * @param int $maxwidth
 * @param int $maxheight
 */
function game_exportjavame_exportdata_hangmanp( $src, $destmobiledir, $destdir, $game, $map, $maxwidth, $maxheight) {
    global $CFG;

    $fp = fopen( $destdir."/$destmobiledir/$destmobiledir.txt", "w");
    fputs( $fp, "01=01\r\n");
    fclose( $fp);

    $destdirphoto = $destdir.'/'.$destmobiledir.'/01';
    mkdir( $destdirphoto);

    $fp = fopen( $destdirphoto.'/photo.txt', "w");
    foreach ($map as $line) {
        $file = $line->attachment;
        $pos = strrpos( $file, '.');
        if ($pos != false) {
            $file = $line->id.substr( $file, $pos);
            $src = $CFG->dataroot.'/'.$game->course.'/moddata/'.$line->attachment;
            game_export_javame_smartcopyimage( $src, $destdirphoto.'/'.$file, $maxwidth, $maxheight);

            $s = $file . '=' . game_upper( $line->answer);
            fputs( $fp, "$s\r\n");
        }
    }
    fclose( $fp);
}

/**
 * Exports to javame.
 *
 * @param stdClas $game
 * @param stdClass $context
 * @param boolean $exportattachment
 * @param string $dest
 * @param array $files
 */
function game_exmportjavame_getanswers( $game, $context, $exportattachment, $dest, &$files) {
    $map = array();
    $files = array();

    switch ($game->sourcemodule) {
        case 'question':
            return game_exmportjavame_getanswers_question( $game, $context, $dest, $files);
        case 'glossary':
            return game_exmportjavame_getanswers_glossary( $game, $context, $exportattachment, $dest, $files);
        case 'quiz':
            return game_exmportjavame_getanswers_quiz( $game, $context, $dest, $files);
    }

    return false;
}

/**
 * Exports to javame.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param string $destdir
 * @param array $files
 */
function game_exmportjavame_getanswers_question( $game, $context, $destdir, &$files) {
    $select = 'hidden = 0 AND category='.$game->questioncategoryid;

    $select .= game_showanswers_appendselect( $game);

    return game_exmportjavame_getanswers_question_select( $game, $context, 'question',
        $select, '*', $game->course, $destdir, $files);
}

/**
 * Exports to javame.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param string $destdir
 * @param array $files
 */
function game_exmportjavame_getanswers_quiz( $game, $context, $destdir, $files) {
    global $CFG;

    $select = "quiz='$game->quizid' ".
        " AND qqi.question=q.id".
        " AND q.hidden=0".
        game_showanswers_appendselect( $game);
    $table = "{question} q,{quiz_question_instances} qqi";

    return game_exmportjavame_getanswers_question_select( $game, $context, $table, $select, "q.*", $game->course, $destdir, $files);
}

/**
 * Exports to javame.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param string $table
 * @param string $select
 * @param string $fields
 * @param int $courseid
 * @param string $destdir
 * @param array $files
 */
function game_exmportjavame_getanswers_question_select( $game, $context, $table, $select, $fields, $courseid, $destdir, &$files) {
    global $CFG, $DB;

    if (($questions = $DB->get_records_select( $table, $select, null, '', $fields)) === false) {
        return;
    }

    $line = 0;
    $map = array();
    foreach ($questions as $question) {
        unset( $ret);
        $ret = new stdClass();
        $ret->qtype = $question->qtype;
        $ret->question = $question->questiontext;
        $ret->question = str_replace( array( '"', '#'), array( "'", ' '),
            game_export_split_files( $game->course, $context, 'questiontext',
            $question->id, $ret->question, $destdir, $files));

        switch ($question->qtype) {
            case 'shortanswer':
                $rec = $DB->get_record( 'question_answers', array( 'question' => $question->id),
                    'id,answer,feedback');
                $ret->answer = $rec->answer;
                $ret->feedback = $rec->feedback;
                $map[] = $ret;
                break;
            default:
                break;
        }
    }

    return $map;
}

/**
 * Exports to javame.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param boolean $exportattachment
 * @param string $destdir
 * @param array $files
 */
function game_exmportjavame_getanswers_glossary( $game, $context, $exportattachment, $destdir, &$files) {
    global $CFG, $DB;

    $table = '{glossary_entries} ge';
    $select = "glossaryid={$game->glossaryid}";
    if ($game->glossarycategoryid) {
        $select .= " AND gec.entryid = ge.id ".
            " AND gec.categoryid = {$game->glossarycategoryid}";
        $table .= ",{glossary_entries_categories} gec";
    }

    if ($exportattachment) {
        $select .= " AND attachment <> ''";
    }

    $fields = 'ge.id,definition,concept';
    if ($exportattachment) {
        $fields .= ',attachment';
    }
    $sql = "SELECT $fields FROM $table WHERE $select ORDER BY definition";
    if (($questions = $DB->get_records_sql( $sql)) === false) {
        return false;
    }

    $fs = get_file_storage();
    $map = array();
    $cmglossary = false;

    foreach ($questions as $question) {
        $ret = new stdClass();
        $ret->id = $question->id;
        $ret->qtype = 'shortanswer';
        $ret->question = strip_tags( $question->definition);
        $ret->answer = $question->concept;
        $ret->feedback = '';
        $ret->attachment = '';

        // Copies the appropriate files from the file storage to destdir.
        if ($exportattachment) {
            if ($question->attachment != '') {
                if ($cmglossary === false) {
                    $cmglossary = get_coursemodule_from_instance('glossary', $game->glossaryid, $game->course);
                    $contextglossary = get_context_instance(CONTEXT_MODULE, $cmglossary->id);
                }

                $ret->attachment = "glossary/{$game->glossaryid}/$question->id/$question->attachment";
                $myfiles = $fs->get_area_files( $contextglossary->id, 'mod_glossary', 'attachment', $ret->id);
                $i = 0;

                foreach ($myfiles as $f) {
                    if ($f->is_directory()) {
                        continue;
                    }
                    $filename = $f->get_filename();
                    $url = "{$CFG->wwwroot}/pluginfile.php/{$f->get_contextid()}/mod_glossary/attachment}";
                    $fileurl = $url.$f->get_filepath().$f->get_itemid().'/'.$filename;
                    $pos = strrpos( $filename, '.');
                    $ext = substr( $filename, $pos);
                    $destfile = $ret->id;
                    if ($i > 0) {
                        $destfile .= '_'.$i;
                    }
                    $destfile = $destdir.'/'.$destfile.$ext;
                    $f->copy_content_to( $destfile);
                    $ret->attachment = $destfile;
                    $i++;
                    $files[] = $destfile;
                }
            }
        }

        $map[] = $ret;
    }

    return $map;
}

/**
 * Create manifest mf.
 *
 * @param string $dir
 * @param stdClass $javame
 * @param string $destmobiledir
 */
function game_create_manifest_mf( $dir, $javame, $destmobiledir) {
    $fp = fopen( $dir.'/MANIFEST.MF', "w");
    fputs( $fp, "Manifest-Version: 1.0\r\n");
    fputs( $fp, "Ant-Version: Apache Ant 1.7.0\r\n");
    fputs( $fp, "Created-By: {$javame->createdby}\r\n");
    fputs( $fp, "MIDlet-1: MoodleHangman,,$destmobiledir\r\n");
    fputs( $fp, "MIDlet-Vendor: {$javame->vendor}\r\n");
    fputs( $fp, "MIDlet-Name: {$javame->vendor}\r\n");
    fputs( $fp, "MIDlet-Description: {$javame->description}\r\n");
    fputs( $fp, "MIDlet-Version: {$javame->version}\r\n");
    fputs( $fp, "MicroEdition-Configuration: CLDC-1.0\r\n");
    fputs( $fp, "MicroEdition-Profile: MIDP-1.0\r\n");

    fclose( $fp);
}

/**
 * Creates a jar file.
 *
 * @param string $srcdir
 * @param stdClass $course
 * @param stdClass $javame
 */
function game_create_jar( $srcdir, $course, $javame) {
    global $CFG;

    $dir = $CFG->dataroot . '/' . $course->id;
    $filejar = $dir . "/export/{$javame->filename}.jar";
    if (!file_exists( $dir)) {
        mkdir( $dir);
    }

    if (!file_exists( $dir.'/export')) {
        mkdir( $dir.'/export');
    }

    if (file_exists( $filejar)) {
        unlink( $filejar);
    }

    $cmd = "cd $srcdir;jar cvfm $filejar META-INF/MANIFEST.MF *";
    exec( $cmd);

    return (file_exists( $filejar) ? $filejar : '');
}

/**
 * Exports to javame.
 *
 * @param stdClass $form
 */
function game_showanswers_appendselect( $form) {
    switch( $form->gamekind){
        case 'hangman':
        case 'cross':
        case 'crypto':
            return " AND qtype='shortanswer'";
        case 'millionaire':
            return " AND qtype = 'multichoice'";
        case 'sudoku':
        case 'bookquiz':
        case 'snakes':
            return " AND qtype in ('shortanswer', 'truefalse', 'multichoice')";
    }

    return '';
}

/**
 * Copy images
 *
 * @param string $filename
 * @param string $dest
 * @param int $maxwidth
 */
function game_export_javame_smartcopyimage( $filename, $dest, $maxwidth) {
    if ($maxwidth == 0) {
        copy( $filename, $dest);
        return;
    }

    $size = getimagesize( $filename);
    if ($size == false) {
        copy( $filename, $dest);
        return;
    }

    $mul = $maxwidth / $size[ 0];
    if ($mul > 1) {
        copy( $filename, $dest);
        return;
    }

    $mime = $size[ 'mime'];
    switch( $mime) {
        case 'image/png':
            $srcimage = imagecreatefrompng( $filename);
            break;
        case 'image/jpeg':
            $srcimage = imagecreatefromjpeg( $filename);
            break;
        case 'image/gif':
            $srcimage = imagecreatefromgif( $filename);
            break;
        default:
            die('Aknown mime type $mime');
            return false;
    }

    $dstw = $size[ 0] * $mul;
    $dsth = $size[ 1] * $mul;
    $dstimage = imagecreatetruecolor( $dstw, $dsth);
    imagecopyresampled( $dstimage, $srcimage, 0, 0, 0, 0, $dstw, $dsth, $size[ 0], $size[ 1]);

    imagejpeg( $dstimage, $dest);
}
