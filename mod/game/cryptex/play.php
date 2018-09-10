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
 * This page plays the cryptex game
 *
 * @package mod_game
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

require_once( "cryptexdb_class.php");

/**
 * Plays the game cryptex.
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $cryptexrec
 * @param boolean $endofgame
 * @param stdClass $context
 */
function game_cryptex_continue( $id, $game, $attempt, $cryptexrec, $endofgame, $context) {
    global $DB, $USER;

    if ($endofgame) {
        game_updateattempts( $game, $attempt, -1, true);
        $endofgame = false;
    }

    if ($attempt != false and $cryptexrec != false) {
        $crossm = $DB->get_record( 'game_cross', array( 'id' => $attempt->id));
        return game_cryptex_play( $id, $game, $attempt, $cryptexrec, $crossm, false, false, false, $context);
    }

    if ($attempt === false) {
        $attempt = game_addattempt( $game);
    }

    $cryptex = new CryptexDB();

    $questions = array();
    $infos = array();

    $answers = array();
    $recs = game_questions_shortanswer( $game);
    if ($recs == false) {
        print_error( get_string( 'no_words', 'game'));
    }

    $infos = array();
    $reps = array();
    foreach ($recs as $rec) {
        if ($game->param7 == false) {
            if (game_strpos( $rec->answertext, ' ')) {
                continue;   // Spaces not allowed.
            }
        }

        $rec->answertext = game_upper( $rec->answertext);
        $answers[ $rec->answertext] = game_repairquestion( $rec->questiontext);
        $infos[ $rec->answertext] = array( $game->sourcemodule, $rec->questionid, $rec->glossaryentryid);

        $a = array( 'gameid' => $game->id, 'userid' => $USER->id,
            'questionid' => $rec->questionid, 'glossaryentryid' => $rec->glossaryentryid);
        if (($rec2 = $DB->get_record('game_repetitions', $a, 'id,repetitions AS r')) != false) {
            $reps[ $rec->answertext] = $rec2->r;
        }
    }

    $cryptex->setwords( $answers, $game->param1, $reps);

    // The game->param4 is minimum words.
    // The game->param2 is maximum words.
    if ($cryptex->computedata( $crossm, $crossd, $letters, $game->param4, $game->param2, $game->param3)) {
        $newcrossd = array();
        foreach ($crossd as $rec) {
            if (array_key_exists( $rec->answertext, $infos)) {
                $info = $infos[ $rec->answertext];

                $rec->id = 0;
                $rec->sourcemodule = $info[ 0];
                $rec->questionid = $info[ 1];
                $rec->glossaryentryid = $info[ 2];
            }
            game_update_queries( $game, $attempt, $rec, 0, '');
            $newcrossd[] = $rec;
        }
        $cryptexrec = $cryptex->savecryptex( $game, $crossm, $newcrossd, $attempt->id, $letters);
    }

    game_updateattempts( $game, $attempt, 0, 0);

    return game_cryptex_play( $id, $game, $attempt, $cryptexrec, $crossm, false, false, false, $context);
}

/**
 * Checks if is correct.
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $cryptexrec
 * @param int $q (The q means game_queries.id).
 * @param stdClass $answer
 * @param boolean $finishattempt
 * @param stdClass $context
 */
function game_cryptex_check( $id, $game, $attempt, $cryptexrec, $q, $answer, $finishattempt, $context) {
    global $DB;

    if ($finishattempt) {
        game_updateattempts( $game, $attempt, -1, true);
        game_cryptex_continue( $id, $game, false, false, true, $context);
        return;
    }

    if ($attempt === false) {
        game_cryptex_continue( $id, $game, $attempt, $cryptexrec, false, $context);
        return;
    }

    $crossm = $DB->get_record_select( 'game_cross', "id=$attempt->id");
    $query = $DB->get_record_select( 'game_queries', "id=$q");

    $answer1 = trim( game_upper( $query->answertext));
    $answer2 = trim( game_upper( $answer));

    $len1 = game_strlen( $answer1);
    $len2 = game_strlen( $answer2);
    $equal = ( $len1 == $len2);
    if ($equal) {
        for ($i = 0; $i < $len1; $i++) {
            if (game_substr( $answer1, $i, 1) != game_substr( $answer2, $i, 1)) {
                $equal = true;
                break;
            }
        }
    }
    if ($equal == false) {
        game_update_queries( $game, $attempt, $query, 0, $answer2, true);
        game_cryptex_play( $id, $game, $attempt, $cryptexrec, $crossm, true, false, false, $context);
        return;
    }

    game_update_queries( $game, $attempt, $query, 1, $answer2);

    $onlyshow = false;
    $showsolution = false;
    game_cryptex_play( $id, $game, $attempt, $cryptexrec, $crossm, true, $onlyshow, $showsolution, $context);
}

/**
 * Plays the game cryptex.
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $cryptexrec
 * @param stdClass $crossm
 * @param boolean $updateattempt
 * @param boolean $onlyshow
 * @param boolean $showsolution
 * @param stdClass $context
 * @param boolean $print
 * @param boolean $showhtmlprintbutton
 */
function game_cryptex_play( $id, $game, $attempt, $cryptexrec, $crossm,
        $updateattempt = false, $onlyshow = false, $showsolution = false, $context,
        $print = false, $showhtmlprintbutton = true) {
    global $CFG, $DB;

    if ($game->toptext != '') {
        echo $game->toptext.'<br>';
    }

    echo '<br>';

    $cryptex = new CryptexDB();
    $language = $attempt->language;
    $questions = $cryptex->loadcryptex( $crossm, $mask, $corrects, $attempt->language);

    if ($language != $attempt->language) {
        if (!$DB->set_field( 'game_attempts', 'language', $attempt->language, array( 'id' => $attempt->id))) {
            print_error( "game_cross_play: Can't set language");
        }
    }

    if (  $attempt->language != '') {
        $wordrtl = game_right_to_left( $attempt->language);
    } else {
        $wordrtl = right_to_left();
    }
    $reverseprint = ($wordrtl != right_to_left());
    if ($reverseprint) {
        $textdir = 'dir="'.($wordrtl ? 'rtl' : 'ltr').'"';
    } else {
        $textdir = '';
    }

    $len = game_strlen( $mask);

    // The count1 means there is a guested letter.
    // The count2 means there is a letter that not guessed.
    $count1 = $count2 = 0;
    for ($i = 0; $i < $len; $i++) {
        $c = game_substr( $mask, $i, 1);
        if ($c == '1') {
            $count1++;
        } else if ($c == '2') {
            $count2++;
        }
    }
    if ($count1 + $count2 == 0) {
        $gradeattempt = 0;
    } else {
        $gradeattempt = $count1 / ($count1 + $count2);
    }
    $finished = ($count2 == 0);

    if (($finished === false) && ($game->param8 > 0)) {
        $found = false;
        foreach ($questions as $q) {
            if ($q->tries < $game->param8) {
                $found = true;
            }
        }
        if ($found == false) {
            $finished = true;   // Rich max tries.
        }
    }

    if ($updateattempt) {
        game_updateattempts( $game, $attempt, $gradeattempt, $finished);
    }

    if (($onlyshow == false) and ($showsolution == false)) {
        if ($finished) {
            game_cryptex_onfinished( $id, $game, $attempt, $cryptexrec);
        }
    }
?>
<style type="text/css"><!--

.answerboxstyle  {
background-color:	#FFFAF0;
border-color:	#808080;
border-style:	solid;
border-width:	1px;
display:	block;
padding:	.75em;
width:	240pt;
}
--></style>
<?php

    $grade = round( 100 * $gradeattempt);
    echo get_string( 'grade', 'game').' '.$grade.' %';

    echo '<br>';

    echo '<table border=0>';
    echo '<tr><td>';
    $cryptex->displaycryptex( $crossm->usedcols, $crossm->usedrows, $cryptexrec->letters, $mask, $showsolution, $textdir);
?>
</td>

<td width=10%>&nbsp;</td>
<td>

<form  method="get" action="<?php echo $CFG->wwwroot?>/mod/game/attempt.php">
<div id="answerbox" class="answerboxstyle" style="display:none;">
<div id="wordclue" name="wordclue" class="cluebox"> </div>
<input id="action" name="action" type="hidden" value="cryptexcheck">
<input id="q" name="q" type="hidden" >
<input id="id" name="id" value="<?php echo $id; ?>" type="hidden">

<div style="margin-top:1em;"><input id="answer" name="answer" type="text" size="24"
 style="font-weight: bold; text-transform:uppercase;" autocomplete="off"></div>

<table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:1em;"><tr>
<td align="right">
<button id="okbutton" type="submit" class="button" style="font-weight: bold;">OK</button> &nbsp;
<button id="cancelbutton" type="button" class="button" onclick="DeselectCurrentWord();">Cancel</button>
</td></tr></table>
</form>
</td>
</tr>
</table>

<?php

if ($showhtmlprintbutton) {
    echo '<br><button id="finishattemptbutton" type="button" onclick="OnEndGame();" >'.get_string( 'finish', 'game');
    echo '</button>';
    echo '<button id="printbutton" type="button" onclick="OnPrint();" >'.get_string( 'print', 'game');
    echo '</button><br>';
}

if ($showhtmlprintbutton) {
?>
<script>
    function PrintHtmlClick() {
        document.getElementById("printbutton").style.display = "none";

        window.print();     

        document.getElementById("printbutton").style.display = "block";
    }

    function OnPrint() {
<?php
        global $CFG;

        $params = "id=$id&gameid=$game->id";
        echo "window.open( \"{$CFG->wwwroot}/mod/game/print.php?$params\");";
?>
    }

    function OnEndGame() {
<?php
        global $CFG;

        $params = 'id='.$id.'&action=cryptexcheck&g=&finishattempt=1';
        echo "window.location = \"{$CFG->wwwroot}/mod/game/attempt.php?$params\";\r\n";
?>
    }
</script>
<?php
}

$i = 0;
$else = '';
$contextglossary = false;
foreach ($questions as $key => $q) {
    $i++;
    if ($showsolution == false) {
        // When I want to show the solution a want to show the questions to.
        if (array_key_exists( $q->id, $corrects)) {
            continue;
        }
    }

    $question = game_show_query( $game, $q, "$i. ".$q->questiontext, $context);
    if ($q->questionid) {
        $question2 = str_replace( array("\'", '\"'), array("'", '"'), $question);
        $question2 = game_filterquestion($question2, $q->questionid, $context->id, $game->course);
    } else {
        $glossary = $DB->get_record_sql( "SELECT id,course FROM {$CFG->prefix}glossary WHERE id={$game->glossaryid}");
        $cmglossary = get_coursemodule_from_instance('glossary', $game->glossaryid, $glossary->course);
        $contextglossary = game_get_context_module_instance( $cmglossary->id);
        $question2 = str_replace( '\"', '"', $question);
        $question2 = game_filterglossary($question2, $q->glossaryentryid, $contextglossary->id, $game->course);
    }

    echo "<script>var msg{$q->id}=".json_encode( $question2).';</script>';
    if (($onlyshow == false) and ($showsolution == false)) {
        if (($game->param8 == 0) || ($game->param8 > $q->tries)) {
            $question .= ' &nbsp;<input type="submit" value="'.
            get_string( 'answer').'" onclick="OnCheck( '.$q->id.",msg{$q->id});\" />";
        }
    }
    echo $question;

    if ($showsolution) {
        echo " &nbsp;&nbsp;&nbsp;$q->answertext<B></b>";
    }
    echo '<br>';
}

if ($game->bottomtext != '') {
    echo '<br><br>'.$game->bottomtext;
}

?>
    <script>
        function OnCheck( id, question) {
            document.getElementById("q").value = id;
            document.getElementById("wordclue").innerHTML = question;

            // Finally, show the answer box.
            document.getElementById("answerbox").style.display = "block";
            try {
                document.getElementById("answer").focus();
                document.getElementById("answer").select();
            }
            catch (e)
            {
            }
        }
    </script>
<?php
if ($print) {
    echo '<body onload="window.print()">';
} else {
    echo '<body>';
}
}
/**
 * On finished.
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $cryptexrec
 */
function game_cryptex_onfinished( $id, $game, $attempt, $cryptexrec) {
    global $CFG, $DB;

    if (! $cm = $DB->get_record( 'course_modules', array( 'id' => $id))) {
        print_error( "Course Module ID was incorrect id=$id");
    }

    echo '<B>'.get_string( 'win', 'game').'</B><br>';
    echo '<br>';
    echo "<a href=\"{$CFG->wwwroot}/mod/game/attempt.php?id=$id&forcenew=1\">".
        get_string( 'nextgame', 'game').'</a> &nbsp; &nbsp; &nbsp; &nbsp; ';
    echo "<a href=\"{$CFG->wwwroot}/course/view.php?id=$cm->course\">".get_string( 'finish', 'game').'</a> ';
    echo "<br><br>\r\n";
}
