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
 * This file plays the game Hidden Picture.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Plays the game "Hidden picture"
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hiddenpicture
 * @param stdClass $context
 */
function game_hiddenpicture_continue( $id, $game, $attempt, $hiddenpicture, $context) {
    global $DB, $USER;

    if ($attempt != false and $hiddenpicture != false) {
        // Continue a previous attempt.
        return game_hiddenpicture_play( $id, $game, $attempt, $hiddenpicture, false, $context);
    }

    if ($attempt == false) {
        // Start a new attempt.
        $attempt = game_addattempt( $game);
    }

    $cols = $game->param1;
    $rows = $game->param2;
    if ($cols == 0) {
        print_error( get_string( 'hiddenpicture_nocols', 'game'));
    }
    if ($rows == 0) {
        print_error( get_string( 'hiddenpicture_norows', 'game'));
    }

    // New attempt.
    $n = $game->param1 * $game->param2;
    $recs = game_questions_selectrandom( $game, CONST_GAME_TRIES_REPETITION * $n);
    $selectedrecs = game_select_from_repetitions( $game, $recs, $n);

    $newrec = game_hiddenpicture_selectglossaryentry( $game, $attempt);

    if ($recs === false) {
        print_error( get_string( 'no_questions', 'game'));
    }

    $positions = array();
    $pos = 1;
    for ($col = 0; $col < $cols; $col++) {
        for ($row = 0; $row < $rows; $row++) {
            $positions[] = $pos++;
        }
    }
    $i = 0;
    $field = ($game->sourcemodule == 'glossary' ? 'glossaryentryid' : 'questionid');
    foreach ($recs as $rec) {
        if ($game->sourcemodule == 'glossary') {
            $key = $rec->glossaryentryid;
        } else {
            $key = $rec->questionid;
        }

        if (!array_key_exists( $key, $selectedrecs)) {
            continue;
        }

        $query = new stdClass();
        $query->attemptid = $newrec->id;
        $query->gamekind = $game->gamekind;
        $query->gameid = $game->id;
        $query->userid = $USER->id;

        $pos = array_rand( $positions);
        $query->col = $positions[ $pos];
        unset( $positions[ $pos]);

        $query->sourcemodule = $game->sourcemodule;
        $query->questionid = $rec->questionid;
        $query->glossaryentryid = $rec->glossaryentryid;
        $query->score = 0;
        if (($query->id = $DB->insert_record( 'game_queries', $query)) == 0) {
            print_error( 'error inserting in game_queries');
        }
        game_update_repetitions($game->id, $USER->id, $query->questionid, $query->glossaryentryid);
    }

    // The score is zero.
    game_updateattempts( $game, $attempt, 0, 0);

    game_hiddenpicture_play( $id, $game, $attempt, $newrec, false, $context);
}


/**
 * Create the game_hiddenpicture record.
 *
 * @param stdClass $game
 * @param stdClass $attempt
 */
function game_hiddenpicture_selectglossaryentry( $game, $attempt) {
    global $CFG, $DB, $USER;

    srand( (double)microtime() * 1000000);

    if ($game->glossaryid2 == 0) {
        print_error( get_string( 'must_select_glossary', 'game'));
    }
    $select = "ge.glossaryid={$game->glossaryid2}";
    $table = '{glossary_entries} ge';
    if ($game->glossarycategoryid2) {
        $table .= ",{glossary_entries_categories} gec";
        $select .= " AND gec.entryid = ge.id AND gec.categoryid = {$game->glossarycategoryid2}";
    }
    if ($game->param7 == 0) {
        // Allow spaces.
        $select .= " AND concept NOT LIKE '% %'";
    }

    $sql = "SELECT ge.id,attachment FROM $table WHERE $select";
    if (($recs = $DB->get_records_sql( $sql)) == false) {
        $a->name = "'".$DB->get_field('glossary', 'name', array( 'id' => $game->glossaryid2))."'";
        print_error( get_string( 'hiddenpicture_nomainquestion', 'game', $a));
        return false;
    }
    $ids = array();
    $keys = array();
    $fs = get_file_storage();
    $cmg = get_coursemodule_from_instance('glossary', $game->glossaryid2, $game->course);
    $context = game_get_context_module_instance( $cmg->id);
    foreach ($recs as $rec) {
        $files = $fs->get_area_files($context->id, 'mod_glossary', 'attachment', $rec->id, "timemodified", false);
        if ($files) {
            foreach ($files as $key => $file) {
                $s = strtoupper( $file->get_filename());
                $s = substr( $s, -4);
                if ($s == '.GIF' or $s == '.JPG' or $s == '.PNG') {
                    $ids[] = $rec->id;
                    $keys[] = $file->get_pathnamehash();
                }
            }
        }
    }
    if (count( $ids) == 0) {
        $a = new stdClass();
        $a->name = "'".$DB->get_field( 'glossary', 'name', array( 'id' => $game->glossaryid2))."'";
        print_error( get_string( 'hiddenpicture_nomainquestion', 'game', $a));
        return false;
    }

    // Have to select randomly one glossaryentry.
    $poss = array();
    for ($i = 0; $i < count($ids); $i++) {
        $poss[] = $i;
    }
    shuffle( $poss);
    $minnum = 0;
    $attachement = '';
    for ($i = 0; $i < count($ids); $i++) {
        $pos = $poss[ $i];
        $tempid = $ids[ $pos];
        $a = array( 'gameid' => $game->id, 'userid' => $USER->id, 'questionid' => 0, 'glossaryentryid' => $tempid);
        if (($rec2 = $DB->get_record('game_repetitions', $a, 'id,repetitions r')) != false) {
            if (($rec2->r < $minnum) or ($minnum == 0)) {
                $minnum = $rec2->r;
                $glossaryentryid = $tempid;
                $attachement = $keys[ $pos];
            }
        } else {
            $glossaryentryid = $tempid;
            $attachement = $keys[ $pos];
            break;
        }
    }

    $sql = 'SELECT id, concept as answertext, definition as questiontext,'.
        ' id as glossaryentryid, 0 as questionid, glossaryid, attachment'.
        ' FROM {glossary_entries} WHERE id = '.$glossaryentryid;
    if (($rec = $DB->get_record_sql( $sql)) == false) {
        return false;
    }
    $query = new stdClass();
    $query->attemptid = $attempt->id;
    $query->gamekind = $game->gamekind;
    $query->gameid = $game->id;
    $query->userid = $USER->id;

    $query->col = 0;
    $query->sourcemodule = 'glossary';
    $query->questionid = 0;
    $query->glossaryentryid = $rec->glossaryentryid;
    $query->attachment = $attachement;
    $query->questiontext = $rec->questiontext;
    $query->answertext = $rec->answertext;
    $query->score = 0;
    if (($query->id = $DB->insert_record( 'game_queries', $query)) == 0) {
        print_error( 'Error inserting in game_queries');
    }
    $newrec = new stdClass();
    $newrec->id = $attempt->id;
    $newrec->correct = 0;
    if (!game_insert_record(  'game_hiddenpicture', $newrec)) {
        print_error( 'Error inserting in game_hiddenpicture');
    }

    game_update_repetitions($game->id, $USER->id, $query->questionid, $query->glossaryentryid);

    return $newrec;
}

/**
 * Plays the game "Hidden picture"
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hiddenpicture
 * @param boolean $showsolution
 * @param stdClass $context
 */
function game_hiddenpicture_play( $id, $game, $attempt, $hiddenpicture, $showsolution, $context) {
    if ($game->toptext != '') {
        echo $game->toptext.'<br>';
    }

    // Show picture.
    $offsetquestions = game_sudoku_compute_offsetquestions( $game->sourcemodule, $attempt, $numbers, $correctquestions);
    unset( $offsetquestions[ 0]);

    game_hiddenpicture_showhiddenpicture( $id, $game, $attempt, $hiddenpicture, $showsolution,
        $offsetquestions, $correctquestions, $id, $attempt, $showsolution);

    // Show questions.
    $onlyshow = false;
    $showsolution = false;

    switch ($game->sourcemodule) {
        case 'quiz':
        case 'question':
            game_sudoku_showquestions_quiz( $id, $game, $attempt, $hiddenpicture, $offsetquestions,
                $numbers, $correctquestions, $onlyshow, $showsolution, $context);
            break;
        case 'glossary':
            game_sudoku_showquestions_glossary( $id, $game, $attempt, $hiddenpicture,
                $offsetquestions, $numbers, $correctquestions, $onlyshow, $showsolution);
            break;
    }

    if ($game->bottomtext != '') {
        echo '<br><br>'.$game->bottomtext;
    }
}

/**
 * "Hidden picture" compute score
 *
 * @param stdClass $game
 * @param stdClass $hiddenpicture
 */
function game_hidden_picture_computescore( $game, $hiddenpicture) {
    $correct = $hiddenpicture->correct;
    if ($hiddenpicture->found) {
        $correct++;
    }
    $remaining = $game->param1 * $game->param2 - $hiddenpicture->correct;
    $div2 = $correct + $hiddenpicture->wrong + $remaining;
    if ($hiddenpicture->found) {
        $percent = ($correct + $remaining) / $div2;
    } else {
        $percent = $correct / $div2;
    }

    return $percent;
}

/**
 * Show hidden picture
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hiddenpicture
 * @param boolean $showsolution
 * @param int $offsetquestions
 * @param int $correctquestions
 */
function game_hiddenpicture_showhiddenpicture( $id, $game, $attempt, $hiddenpicture, $showsolution,
            $offsetquestions, $correctquestions) {
    global $DB;

    $foundcells = '';
    foreach ($correctquestions as $key => $val) {
        $foundcells .= ','.$key;
    }
    $cells = '';
    foreach ($offsetquestions as $key => $val) {
        if ($key != 0) {
            $cells .= ','.$key;
        }
    }

    $query = $DB->get_record_select( 'game_queries', "attemptid=$hiddenpicture->id AND col=0",
        null, 'id,glossaryentryid,attachment,questiontext');

    // Grade.
    echo "<br/>".get_string( 'grade', 'game').' : '.round( $attempt->score * 100).' %';

    game_hiddenpicture_showquestion_glossary( $game, $id, $query);

    $cells = substr( $cells, 1);
    $foundcells = substr( $foundcells, 1);
    game_showpicture( $id, $game, $attempt, $query, $cells, $foundcells, true);
}

/**
 * hidden picture. show question glossary
 *
 * @param stdClass $game
 * @param int $id
 * @param stdClass $query
 */
function game_hiddenpicture_showquestion_glossary( $game, $id, $query) {
    global $CFG, $DB;

    $entry = $DB->get_record( 'glossary_entries', array( 'id' => $query->glossaryentryid));

    // Start the form.
    echo '<br>';
    echo "<form id=\"responseform\" method=\"post\" ".
        "action=\"{$CFG->wwwroot}/mod/game/attempt.php\" onclick=\"this.autocomplete='off'\">\n";
    echo "<center><input type=\"submit\" name=\"finishattempt\" ".
        "value=\"".get_string('hiddenpicture_mainsubmit', 'game')."\"></center>\n";

    // Add a hidden field with the queryid.
    echo '<input type="hidden" name="id" value="' . s($id) . "\" />\n";
    echo '<input type="hidden" name="action" value="hiddenpicturecheckg" />';
    echo '<input type="hidden" name="queryid" value="' . $query->id . "\" />\n";

    // Add a hidden field with glossaryentryid.
    echo '<input type="hidden" name="glossaryentryid" value="'.$query->glossaryentryid."\" />\n";

    $temp = $game->glossaryid;
    $game->glossaryid = $game->glossaryid2;
    echo game_show_query( $game, $query, $entry->definition);
    $game->glossaryid = $temp;

    echo get_string( 'answer').': ';
    echo "<input type=\"text\" name=\"answer\" size=30 /><br>";

    echo "</form><br>\n";
}

/**
 * Check main question
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hiddenpicture
 * @param boolean $finishattempt
 * @param stdClass $context
 */
function game_hiddenpicture_check_mainquestion( $id, $game, &$attempt, &$hiddenpicture, $finishattempt, $context) {
    global $CFG, $DB;

    $responses = data_submitted();

    $glossaryentryid = $responses->glossaryentryid;
    $queryid = $responses->queryid;

    // Load the glossary entry.
    if (!($entry = $DB->get_record( 'glossary_entries', array( 'id' => $glossaryentryid)))) {
        print_error( get_string( 'noglossaryentriesfound', 'game'));
    }
    $answer = $responses->answer;
    $correct = false;
    if ($answer != '') {
        if (game_upper( $entry->concept) == game_upper( $answer)) {
            $correct = true;
        }
    }

    // Load the query.
    if (!($query = $DB->get_record( 'game_queries', array( 'id' => $queryid)))) {
        print_error( "The query $queryid not found");
    }

    game_update_queries( $game, $attempt, $query, $correct, $answer);

    if ($correct) {
        $hiddenpicture->found = 1;
    } else {
        $hiddenpicture->wrong++;
    }
    if (!$DB->update_record( 'game_hiddenpicture', $hiddenpicture)) {
        print_error( 'game_hiddenpicture_check_mainquestion: error updating in game_hiddenpicture');
    }

    $score = game_hidden_picture_computescore( $game, $hiddenpicture);
    game_updateattempts( $game, $attempt, $score, $correct);

    if ($correct == false) {
        game_hiddenpicture_play( $id, $game, $attempt, $hiddenpicture, false, $context);
        return true;
    }

    // Finish the game.
    $query = $DB->get_record_select( 'game_queries', "attemptid=$hiddenpicture->id AND col=0",
        null, 'id,glossaryentryid,attachment,questiontext');
    game_showpicture( $id, $game, $attempt, $query, '', '', false);
    echo '<p><br/><font size="5" color="green">'.get_string( 'win', 'game').'</font><BR/><BR/></p>';
    global $CFG;

    echo '<br/>';

    echo "<a href=\"$CFG->wwwroot/mod/game/attempt.php?id=$id\">";
    echo get_string( 'nextgame', 'game').'</a> &nbsp; &nbsp; &nbsp; &nbsp;';

    if (! $cm = $DB->get_record( 'course_modules', array( 'id' => $id))) {
        print_error( "Course Module ID was incorrect id=$id");
    }

    echo "<a href=\"{$CFG->wwwroot}/course/view.php?id=$cm->course\">".get_string( 'finish', 'game').'</a> ';

    return false;
}

/**
 * Show picture
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $query
 * @param object $cells
 * @param int $foundcells
 * @param boolean $usemap
 */
function game_showpicture( $id, $game, $attempt, $query, $cells, $foundcells, $usemap) {
    global $CFG;

    $filenamenumbers = str_replace( "\\", '/', $CFG->dirroot)."/mod/game/hiddenpicture/numbers.png";
    if ($usemap) {
        $cols = $game->param1;
        $rows = $game->param2;
    } else {
        $cols = $rows = 0;
    }
    $params = "id=$id&id2=$attempt->id&f=$foundcells&cols=$cols&rows=$rows&cells=$cells&p={$query->attachment}&n=$filenamenumbers";
    $imagesrc = "hiddenpicture/picture.php?$params";

    $fs = get_file_storage();
    $file = get_file_storage()->get_file_by_hash( $query->attachment);
    $image = $file->get_imageinfo();
    if ($game->param4 > 10) {
        $width = $game->param4;
        $height = $image[ 'height'] * $width / $image[ 'width'];
    } else if ( $game->param5 > 10) {
        $height = $game->param5;
        $width = $image[ 'width'] * $height / $image[ 'height'];
    } else {
        $width = $image[ 'width'];
        $height = $image[ 'height'];
    }

    echo "<IMG SRC=\"$imagesrc\" width=$width ";
    if ($usemap) {
        echo " USEMAP=\"#mapname\" ";
    }
    echo " BORDER=\"1\">\r\n";

    if ($usemap) {
        echo "<MAP NAME=\"mapname\">\r\n";
        $pos = 0;
        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                $pos++;
                $x1 = $col * $width / $cols;
                $y1 = $row * $height / $rows;
                $x2 = $x1 + $width / $cols;
                $y2 = $y1 + $height / $rows;
                $q = "a$pos";
                echo "<AREA SHAPE=\"rect\" COORDS=\"$x1,$y1,$x2,$y2\" HREF=\"#$q\" ALT=\"$pos\">\r\n";
            }
        }
        echo "</MAP>";
    }
}
