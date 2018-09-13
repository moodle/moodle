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
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This files plays the game "Crossword".
 *
 * @package mod_game
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require( "cross_class.php");
require( "crossdb_class.php");

/**
 * Plays the game crossword.
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $cross
 * @param string $g
 * @param boolean $endofgame
 * @param stdClass $context
 */
function game_cross_continue( $id, $game, $attempt, $cross, $g, $endofgame, $context) {
    if ($endofgame) {
        if ($g == '') {
            game_updateattempts( $game, $attempt, -1, true);
            $endofgame = false;
        }
    }

    if ($attempt != false and $cross != false) {
        return game_cross_play( $id, $game, $attempt, $cross, $g, false, false, $endofgame,
            false, false, false, false, true, $context);
    }

    if ($attempt == false) {
        $attempt = game_addattempt( $game);
    }

    game_cross_new( $game, $attempt->id, $crossm);
    game_updateattempts( $game, $attempt, 0, 0);
    return game_cross_play( $id, $game, $attempt, $crossm, '', false, false, false, false, false, false, false, true, $context);
}

/**
 * Creates a new cross.
 *
 * @param stdClass $game
 * @param int $attemptid
 * @param stdClass $crossm
 */
function game_cross_new( $game, $attemptid, &$crossm) {
    global $DB, $USER;

    $cross = new CrossDB();

    $questions = array();
    $infos = array();

    $answers = array();
    $recs = game_questions_shortanswer( $game);
    if ($recs == false) {
        print_error( 'game_cross_continue: '.get_string( 'no_words', 'game'));
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
        $infos[ $rec->answertext] = array( $game->sourcemodule, $rec->questionid, $rec->glossaryentryid, $rec->attachment);

        $a = array( 'gameid' => $game->id, 'userid' => $USER->id,
            'questionid' => $rec->questionid, 'glossaryentryid' => $rec->glossaryentryid);
        if (($rec2 = $DB->get_record('game_repetitions', $a, 'id,repetitions AS r')) != false) {
            $reps[ $rec->answertext] = $rec2->r;
        }
    }

    $cross->setwords( $answers, $game->param1, $reps);

    // The game->param4 is minimum words in crossword.
    // The game->param2 is maximum words in crossword.
    if ($cross->computedata( $crossm, $crossd, $lettets, $game->param4, $game->param2, $game->param8)) {
        $newcrossd = array();
        foreach ($crossd as $rec) {
            $info = $infos[ $rec->answertext];
            if ($info != false) {
                $rec->sourcemodule = $info[ 0];
                $rec->questionid = $info[ 1];
                $rec->glossaryentryid = $info[ 2];
                $rec->attachment = $info[ 3];
            }
            $newcrossd[] = $rec;
        }
        $cross->savecross( $game, $crossm, $newcrossd, $attemptid);
    }

    if (count( $crossd) == 0) {
        print_error( 'game_cross_continue: '.get_string( 'no_words', 'game').$game->id);
    }
}

/**
 * Shows the legend.
 *
 * @param array $legend
 * @param string $title
 */
function showlegend( $legend, $title) {
    if (count( $legend) == 0) {
        return;
    }

    echo "<br><b>$title</b><br>";
    foreach ($legend as $key => $line) {
        $line = game_repairquestion( $line);
        echo game_filtertext( "$key: $line<br>", 0);
    }
}

/**
 * Plays the game crossword.
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $crossrec
 * @param string $g
 * @param boolean $onlyshow
 * @param boolean $showsolution
 * @param boolean $endofgame
 * @param boolean $print
 * @param boolean $checkbutton
 * @param boolean $showhtmlsolutions
 * @param boolean $showhtmlprintbutton
 * @param boolean $showstudentguess
 * @param stdClass $context
 */
function game_cross_play( $id, $game, $attempt, $crossrec, $g, $onlyshow, $showsolution,
    $endofgame, $print, $checkbutton, $showhtmlsolutions, $showhtmlprintbutton, $showstudentguess, $context) {
    global $CFG, $DB;

    $cross = new CrossDB();

    $language = $attempt->language;
    $info = $cross->loadcross( $g, $done, $html, $game, $attempt, $crossrec, $onlyshow,
        $showsolution, $endofgame, $showhtmlsolutions, $attempt->language,
        $showstudentguess, $context);

    if ($language != $attempt->language) {
        if (!$DB->set_field( 'game_attempts', 'language', $attempt->language, array( 'id' => $attempt->id))) {
            print_error( "game_cross_play: Can't set language");
        }
    }

    if ($done or $endofgame) {
        if (! $cm = $DB->get_record( 'course_modules', array( 'id' => $id))) {
            print_error("Course Module ID was incorrect id=$id");
        }

        if ($endofgame == false) {
            echo '<B>'.get_string( 'win', 'game').'</B><BR>';
        }
        if (game_can_start_new_attempt( $game)) {
            echo '<br>';
            echo "<a href=\"{$CFG->wwwroot}/mod/game/attempt.php?id=$id&forcenew=1\">".
                get_string( 'nextgame', 'game').'</a> &nbsp; &nbsp; &nbsp; &nbsp; ';
        }
    } else if ($info != '') {
        if ($print === false) {
            echo "<br>$info<br>";
        }
    }

    if ($attempt->language != '') {
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

.gamebox  {
border-style:	solid;
border-width:	1pt;
cursor:	pointer;
font-size:	.12in;
font-weight:	bold;
height:	.18in;
overflow:	hidden;
text-align:	center;
width:	.18in;
}

.boxcheated_sel  {
background-color:	#FFF1D7;
border-color:	#C00000;
color:	#2080D0;
}

.boxcheated_unsel  {
background-color:	#ffffff;
border-color:	#606060;
color:	#2080D0;
}

.boxerror_sel  {
background-color:	#FFF1D7;
border-color:	#C00000;
color:	#BF0000;
}

.boxerror_unsel  {
background-color:	#FFF0F0;
border-color:	#606060;
color:	#BF0000;
}

.boxnormal_sel  {
background-color:	#FFF1D7;
border-color:	#C00000;
color:	#000000;
}

.boxnormal_unsel  {
background-color:	#ffffff;
border-color:	#606060;
color:	#000000;
}

.button  {
width:	64pt;
}

.cluebox  {
border-bottom-width:	1px;
border-color:	#c0c0c0;
border-left-width:	0px;
border-right-width:	0px;
border-style:	solid;
border-top-width:	1px;
font-size:	85%;
margin-top:	1em;
padding-bottom:	.5em;
padding-left:	0pt;
padding-right:	0pt;
padding-top:	.5em;
}

.crosswordarea  {
background-color:	#D0D8E0;
border-color:	#808080;
border-style:	solid;
border-width:	2px;
padding:	.5em;
}

#region-game.body  {
background-color:	white;
cursor:	default;
}

#region-game.body, #region-game.button, #region-game.input, #region-game.p, #region-game.td  {
font-family:	Verdana, Arial, Sans-Serif;
font-size:	small;
}

#region-game.button  {
cursor:	pointer;
}

#region-game.h1, #region-game.h2, #region-game.h3  {
color:	#5F78A2;
font-family:	Franklin Gothic Medium, Arial, Sans-Serif;
font-weight:	normal;
}

#region-game.p  {
margin-top:	1em;
}

--></style>

</head>

<?php
if ($print) {
    echo '<body onload="window.print()">';
} else {
    echo '<body>';
}

if ($game->toptext != '') {
    echo $game->toptext.'<br>';
}
?>
<h1></h1>

<div id="waitmessage" class="answerboxstyle">
    This interactive crossword puzzle requires JavaScript and a reasonably recent web browser, such as Internet Explorer 5.5
    or later, Netscape 7, Mozilla, Firefox, or Safari.  If you have disabled web page scripting, please re-enable it and refresh
    the page.
</div>


<p><table cellpadding="0" cellspacing="0" border="0">

<?php
if ($game->param3 == 2) {
    echo "<tr>\r\n";
    game_cross_show_welcome( $game);
    echo "</tr>\r\n";
    echo "<tr><tr><td>&nbsp</td></tr>\r\n";
}
?>

<tr>
<td class="crosswordarea">
<table id="crossword" cellpadding="3" cellspacing="0" style="display: none; border-collapse: collapse;" <?php echo $textdir;?>>

<script language="JavaScript" type="text/javascript"><!--

// EclipseCrossword and this script block (C) Copyright 2000-2005 Green Eclipse.
// Do not remove this copyright notice.  You can, however, change the rest of the page.
// www.eclipsecrossword.com

var BadChars = "`~!@^*()_={[}]\|:;\"',<>?&";

var CrosswordWidth, CrosswordHeight;
var TableAcrossWord, TableDownWord;
var Words, Word, Clue, WordX, WordY, LastHorizontalWord;
var OnlyCheckOnce;

var CurrentWord, PrevWordHorizontal, x, y, i, j;
var CrosswordFinished, Initialized;

// Check the user's browser and then initialize the puzzle.
if (document.getElementById("waitmessage") != null)
{
    document.getElementById("waitmessage").innerHTML = "<?php echo get_string( 'cross_pleasewait', 'game'); ?>";

    // Current game variables
    CurrentWord = -1;
    PrevWordHorizontal = false;

<?php
    echo $html;
?>
    OnlyCheckOnce = false;

    // Create the cell-to-word arrays.
    TableAcrossWord = new Array(CrosswordWidth);
    for (var x = 0; x < CrosswordWidth; x++)
    TableAcrossWord[x] = new Array(CrosswordHeight);
    TableDownWord = new Array(CrosswordWidth);
    for (var x = 0; x < CrosswordWidth; x++){ 
        TableDownWord[x] = new Array(CrosswordHeight);
    }

    GuessLeter = new Array(CrosswordWidth);
    for (var x = 0; x < CrosswordWidth; x++) {
        GuessLeter[x] = new Array(CrosswordHeight);
        for (var y = 0; y < CrosswordHeight; y++)  {
            GuessLeter[ x][ y] = "_";
        }
    }

    solu = new Array(CrosswordWidth);
    for (var x = 0; x < CrosswordWidth; x++) {
        solu[x] = new Array(CrosswordHeight);
        for (var y = 0; y < CrosswordHeight; y++) {
            solu[ x][ y] = "";
        }
    }

    for (var y = 0; y < CrosswordHeight; y++) {
        for (var x = 0; x < CrosswordWidth; x++) {
            TableAcrossWord[x][y] = -1;
            TableDownWord[x][y] = -1;
        }
    }
    // First, add the horizontal words to the puzzle.
    for (var i = 0; i <= LastHorizontalWord; i++) {
        x = WordX[i];
        y = WordY[i];
        s = Guess[ i];
        so = Solutions[ i];
        for (var j = 0; j < WordLength[i]; j++) {
            TableAcrossWord[x + j][y] = i;
            if( j < s.length)
                c = s.substr( j, 1);
            else
                c = '';
            GuessLeter[ x+ j][ y] = c;
            if( j < so.length)
                c = so.substr(  j, 1);
            else
                c = '';
            solu[ x+j][ y] = c;
        }
    }

    // Second, add the vertical words to the puzzle.
    for (var i = LastHorizontalWord + 1; i < Words; i++) {
        x = WordX[i];
        y = WordY[i];
        s = Guess[ i];
        so = Solutions[ i];
        for (var j = 0; j < WordLength[i]; j++) {
            TableDownWord[x][y + j] = i;
            if( j < s.length)
                c = s.substr( j, 1);
            else
                c = '';
            GuessLeter[ x][ y+j] = c;
            if( j < so.length)
                c = so.substr( j, 1);
            else
                c = '';
            solu[ x][ y+j] = c;
        }
    }

    document.writeln("<tr><td></td>");
    for (var x = 0; x < CrosswordWidth; x++) {
        document.write("<td align=center>" + (x+1) + " </td>");    //col numbers
    }

    // Now, insert the row HTML into the table.
    for (var y = 0; y < CrosswordHeight; y++) {
        document.writeln("<tr>");
        document.write("<td>" + (y+1)+" </td>");    //line numbers
        for (var x = 0; x < CrosswordWidth; x++) {
            if (TableAcrossWord[x][y] >= 0 || TableDownWord[x][y] >= 0) {
                var s;
                s = "<td id=\"c" + PadNumber(x) + PadNumber(y);
                s += "\" class=\"gamebox boxnormal_unsel\" onclick=\"SelectThisWord(event);\">";
                document.write( s);

                if( solu[x][y] != '')
                    document.write( solu[x][y]);
                else if( GuessLeter[x][y]== "_")
                    document.write( "&nbsp;");
                else
                    document.write( GuessLeter[x][y]);

                document.write("</td>");
            } else {
                document.write("<td></td>");    //empty cell
            }
        }
        document.writeln("</tr>");
    }

    // Finally, show the crossword and hide the wait message.
    Initialized = true;
    document.getElementById("waitmessage").style.display = "none";
    document.getElementById("crossword").style.display = "block";
}

// ----------
// Event handlers

// Raised when a key is pressed in the word entry box.
function WordEntryKeyPress(event)
{
    if (CrosswordFinished) return;
    // Treat an Enter keypress as an OK click.
    if (CurrentWord >= 0 && event.keyCode == 13) OKClick();
}

// ----------
// Helper functions

// Returns true if the string passed in contains any characters prone to evil.
function ContainsBadChars(theirWord) {
    return false;

    for (var i = 0; i < theirWord.length; i++) {
        if (BadChars.indexOf(theirWord.charAt(i)) >= 0) return true;
    }
    return false;
}

// Pads a number out to three characters.
function PadNumber(number) {
    if (number < 10) {
        return "00" + number;
    } else if (number < 100) {
        return "0" + number;
    } else {
        return "" +  number;
    }
}

// Returns the table cell at a particular pair of coordinates.
function CellAt(x, y) {
    return document.getElementById("c" + PadNumber(x) + PadNumber(y));
}

// Deselects the current word, if there's a word selected.  DOES not change the value of CurrentWord.
function DeselectCurrentWord() {
    if (CurrentWord < 0) {
        return;
    }

    var x, y, i;

    document.getElementById("answerbox").style.display = "none";
    document.getElementById("answerbox2").style.display = "none";
    ChangeCurrentWordSelectedStyle( false);
    CurrentWord = -1;
}

// Changes the style of the cells in the current word.
function ChangeWordStyle(WordNumber, NewStyle) {
    if (WordNumber< 0) {
        return;
    }
    var x = WordX[WordNumber];
    var y = WordY[WordNumber];

    if (WordNumber<= LastHorizontalWord) {
        for (i = 0; i < WordLength[WordNumber]; i++) {
            CellAt(x + i, y).className = NewStyle;
        }        
    } else {
        for (i = 0; i < WordLength[WordNumber]; i++) {
            CellAt(x, y + i).className = NewStyle;
        }
    }
}

// Changes the style of the cells in the current word between the selected/unselected form.
function ChangeCurrentWordSelectedStyle(IsSelected) {
    if (CurrentWord < 0) {
        return;
    }
    var x = WordX[CurrentWord];
    var y = WordY[CurrentWord];

    if (CurrentWord <= LastHorizontalWord) {
        for (i = 0; i < WordLength[CurrentWord]; i++) {
            CellAt(x + i, y).className = CellAt(x + i, y).className.
            replace(IsSelected ? "_unsel" : "_sel", IsSelected ? "_sel" : "_unsel");
        }
    } else {
        for (i = 0; i < WordLength[CurrentWord]; i++) {
            CellAt(x, y + i).className = CellAt(x, y + i).className.
            replace(IsSelected ? "_unsel" : "_sel", IsSelected ? "_sel" : "_unsel");
        }
    }
}

// Selects the new word by parsing the name of the TD element referenced by the 
// event object, and then applying styles as necessary.
function SelectThisWord(event) {
    if (CrosswordFinished) {
        return;
    }
    var x, y, i, TheirWord, TableCell;

    // Deselect the previous word if one was selected.
    document.getElementById("welcomemessage").style.display = "none";
    if (CurrentWord >= 0) {
        OKClick();
    }
    DeselectCurrentWord();

    // Determine the coordinates of the cell they clicked, and then the word that
    // they clicked.
    var target = (event.srcElement ? event.srcElement: event.target);
    x = parseInt(target.id.substring(1, 4), 10);
    y = parseInt(target.id.substring(4, 7), 10);

    // If they clicked an intersection, choose the type of word that was NOT selected last time.
    if (TableAcrossWord[x][y] >= 0 && TableDownWord[x][y] >= 0) {
        CurrentWord = PrevWordHorizontal ? TableDownWord[x][y] : TableAcrossWord[x][y];
    } else if (TableAcrossWord[x][y] >= 0) {
        CurrentWord = TableAcrossWord[x][y];
    } else if (TableDownWord[x][y] >= 0) {
        CurrentWord = TableDownWord[x][y];
    }

    PrevWordHorizontal = (CurrentWord <= LastHorizontalWord);

    // Now, change the style of the cells in this word.
    ChangeCurrentWordSelectedStyle(true);

    // Then, prepare the answer box.
    x = WordX[CurrentWord];
    y = WordY[CurrentWord];
    TheirWord = "";
    var TheirWordLength = 0;
    for (i = 0; i < WordLength[CurrentWord]; i++) {
        // Find the appropriate table cell.
        if (CurrentWord <= LastHorizontalWord) {
            TableCell = CellAt(x + i, y);
        } else {
            TableCell = CellAt(x, y + i);
        }
        // Add its contents to the word we're building.
        if (TableCell.innerHTML != null && TableCell.innerHTML.length > 0 &&
            TableCell.innerHTML != " " && TableCell.innerHTML.toLowerCase() != "&nbsp;") {
            TheirWord += TableCell.innerHTML.toUpperCase();
            TheirWordLength++;
        } else {
            TheirWord += "&bull;";
        }
    }

    document.getElementById("wordlabel").innerHTML = TheirWord;
    <?php
    $msg = "\"".get_string( 'cross_across', 'game').", \" : \"".get_string( 'cross_down', 'game').", \"";
    $letters = "\" ".get_string( 'letter', 'game').".\" : \" ".get_string( 'letters', 'game').".\"";
    ?>
    var s = ((CurrentWord <= LastHorizontalWord) ? <?php echo $msg ?>);
    s = s + WordLength[CurrentWord] + (WordLength[CurrentWord] == 1 ? <?php echo $letters;?>);
    document.getElementById("wordinfo").innerHTML = s;

    document.getElementById("wordclue").innerHTML = Clue[CurrentWord];
    document.getElementById("worderror").style.display = "none";

    if (TheirWordLength == WordLength[CurrentWord]) {
        document.getElementById("wordentry").value = TheirWord;
    } else {
        document.getElementById("wordentry").value = "";
    }

    // Finally, show the answer box.
    document.getElementById("answerbox").style.display = "block";
    document.getElementById("answerbox2").style.display = "block";
    try {
        document.getElementById("wordentry").focus();
        document.getElementById("wordentry").select();
    }
    catch (e) {
    }
}

// Called when the user clicks the OK link.
function OKClick() {
    var TheirWord, x, y, i, TableCell;
    if (CrosswordFinished) {
        return;
    }
    if (document.getElementById("okbutton").disabled) {
        return;
    }

    // First, validate the entry.
    TheirWord = document.getElementById("wordentry").value.toUpperCase();
    if (TheirWord.length == 0) {
        DeselectCurrentWord();
        return;
    }
    if (ContainsBadChars(TheirWord)) {
        document.getElementById("worderror").innerHTML = "<?php echo get_string( 'cross_error_containsbadchars', 'game');?>";
        document.getElementById("worderror").style.display = "block";
        return;
    }
    if (TheirWord.length < WordLength[CurrentWord]) {
        var s = "<?php echo get_string( 'cross_error_wordlength1', 'game');?>";
        s = s + WordLength[CurrentWord] + " <?php echo get_string( 'cross_error_wordlength2', 'game');?>";
        document.getElementById("worderror").innerHTML  = s;
        document.getElementById("worderror").style.display = "block";
        return;
    }
    if (TheirWord.length > WordLength[CurrentWord]) {
        var s = "<?php echo get_string( 'cross_error_wordlength1', 'game');?>";
        s = s + WordLength[CurrentWord] + " <?php echo get_string( 'cross_error_wordlength2', 'game');?>";
        document.getElementById("worderror").innerHTML = s;
        document.getElementById("worderror").style.display = "block";
        return;
    }

    // If we made it this far, they typed an acceptable word, so add these letters to the puzzle and hide the entry box.
    x = WordX[CurrentWord];
    y = WordY[CurrentWord];
    for (i = 0; i < TheirWord.length; i++) {
        TableCell = CellAt(x + (CurrentWord <= LastHorizontalWord ? i : 0), y + (CurrentWord > LastHorizontalWord ? i : 0));
        TableCell.innerHTML = TheirWord.substring(i, i + 1);
    }
    DeselectCurrentWord();
}

<?php
if ($showhtmlsolutions == false) {
?>
function PackPuzzle( sData) {
    var i, s, s2, n, j;

    s = "";
    len = sData.length;
    for(i=0; i < len; i++) {
        c = sData.charAt( i);
        if( (c > "0") && (c <= "9")) {
            s = s.concat( '/');
        }
        s = s.concat( c);
    }

    for(;;) {
        i = s.indexOf( "__");
        if( i == -1) {
            break;
        }
        len = s.length;

        for( j=i ; j < len; j++) {
            if( s.charAt( j) != "_") {
                break;
            }
        }
        n = j - i;
        s2 = s.substr( 0, i);
        s2 = s2.concat( n);
        s = s2.concat( s.substr( j));
    }

    return encodeURIComponent( s);
}

// Called when the "check server" link is clicked.
function CheckServerClick( endofgame) {
    var i, j, x, y, UserEntry, ErrorsFound = 0, EmptyFound = 0, TableCell;
    if (CrosswordFinished) {
        return;
    }
    DeselectCurrentWord();

    for (y = 0; y < CrosswordHeight; y++)  {
        for (x = 0; x < CrosswordWidth; x++) {
            if (TableAcrossWord[x][y] >= 0 || TableDownWord[x][y] >= 0) {
                TableCell = CellAt(x, y);
                if (TableCell.className == "box boxerror_unsel") {
                    TableCell.className = "box boxnormal_unsel";
                }
            }
        }
    }

    sData = "";
    for (i = 0; i < Words; i++) {
        // Get the user's entry for this word.
        UserEntry = "";
        for (j = 0; j < WordLength[i]; j++) {
            if (i <= LastHorizontalWord) {
                TableCell = CellAt(WordX[i] + j, WordY[i]);
            } else {
                TableCell = CellAt(WordX[i], WordY[i] + j);
            }
            if (TableCell.innerHTML.length > 0 && TableCell.innerHTML.toLowerCase() != "&nbsp;") {
                UserEntry += TableCell.innerHTML.toUpperCase();
            } else if( TableCell.innerHTML.toLowerCase() == "&nbsp;") {
                UserEntry += " ";
            } else {
                UserEntry += "_";
            }
        }
        sData += UserEntry;
    }

    sData = PackPuzzle( sData);

    if( endofgame) {
        sData += "&finishattempt=1";
    }

    <?php
    if ($onlyshow == false) {
        global $CFG;
        $params = 'id='.$id.'&action=crosscheck&g=';
        echo "window.location = \"{$CFG->wwwroot}/mod/game/attempt.php?$params\"+ sData;\r\n";
    }
    ?>
}
<?php
}
?>

function OnPrint()
{
<?php
    global $CFG;
    $params = "id=$id&gameid=$game->id";
    echo "window.open( \"{$CFG->wwwroot}/mod/game/print.php?$params\")";
?>
}

<?php
if ($showhtmlprintbutton) {
?>
    function PrintHtmlClick() {
        document.getElementById("printhtmlbutton").style.display = "none";

        <?php
        if ($showhtmlsolutions) {
            ?> document.getElementById("checkhtmlbutton").style.display = "none"; <?php
        }
        ?>
        window.print();     
        <?php
        if ($showhtmlsolutions) {
            ?> document.getElementById("checkhtmlbutton").style.display = "block"; <?php
        }
        ?>
        document.getElementById("printhtmlbutton").style.display = "block";
    }
<?php
}
?>

<?php
if ($showhtmlprintbutton) {
?>

/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Base64 = {
 
    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {
            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);bgColor = "Black";
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }

        output = Base64._utf8_decode(output);

        return output;

    }, 

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;
        while ( i < utftext.length ) {
            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }

        return string;
    }
}

// Called when the "checkhtml" link is clicked.
function CheckHtmlClick() {
    var i, TableCell, UserEntry, sData, solution;

    sData = "";
    for (i = 0; i < Words; i++) {
        solution = Base64.decode( HtmlSolutions[ i]);
        // Get the user's entry for this word.
        UserEntry = "";
        for (j = 0; j < WordLength[i]; j++) {
            if (i <= LastHorizontalWord) {
                TableCell = CellAt(WordX[i] + j, WordY[i]);
            } else {
                TableCell = CellAt(WordX[i], WordY[i] + j);
            }
            if (TableCell.innerHTML.length > 0 && TableCell.innerHTML.toLowerCase() != "&nbsp;") {
                UserEntry += TableCell.innerHTML.toUpperCase();
            } else if( TableCell.innerHTML.toLowerCase() == "&nbsp;") {
                UserEntry += " ";
            } else {
                UserEntry += "_";
            }

            if( UserEntry[ j] != solution[ j]) {
                TableCell.innerHTML = "&nbsp;";
            }
        }
    }
}
<?php
}

if ($showhtmlsolutions) {
?>
    function decodeutf8(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }
<?php
}
?>

//-->
</script>

</table></td>

<?php
if ($game->param3 == 2) {
    echo '<td>&nbsp &nbsp &nbsp</td>';
    game_cross_show_legends( $cross);
} else {
    game_cross_show_welcome( $game);
}
?>

</tr></table>

<?php
if ($onlyshow == false) {
    echo '<div style="margin-top: 1em;">';

    echo '<button id="checkbutton" type="button" onclick="CheckServerClick( 0);" style="display: none;">'.
    get_string( 'cross_checkbutton', 'game');
    echo '</button>';

    echo ' &nbsp;&nbsp;&nbsp;&nbsp;<button id="finishattemptbutton" '.
    ' type="button" onclick="CheckServerClick( 1);" style="display: none;">'.
    get_string( 'cross_endofgamebutton', 'game');
    echo '</button>';
    if ($game->param5 == 1 or $game->param5 == null) {
        echo ' &nbsp;&nbsp;&nbsp;&nbsp;<button id="printbutton" type="button" '.
        ' onclick="OnPrint( 0);" style="display: none;">'.get_string( 'print', 'game');
        echo '</button>';
    }

    echo "</div>\r\n";
}

if ($showhtmlsolutions or $showhtmlprintbutton) {
    echo '<br>';
}

if ($showhtmlsolutions) {
    echo '<button id="checkhtmlbutton" type="button" '.
    ' onclick="CheckHtmlClick();" visible=true>'.get_string( 'cross_checkbutton', 'game');
    echo '</button>';
}

if ($showhtmlprintbutton) {
    if ($showhtmlsolutions) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
    }
    echo '<button id="printhtmlbutton" type="button" onclick="PrintHtmlClick( 0);" visible=true>'.get_string( 'print', 'game');
    echo '</button>';
}

if ($game->param3 == 2) {
    echo '<td>&nbsp &nbsp &nbsp</td>';
    game_cross_show_welcome( $game);
} else {
    game_cross_show_legends( $cross);
}

if ($game->bottomtext != '') {
    echo '<br><br>'.$game->bottomtext;
}

if ($attempt != false) {
    if ($attempt->timefinish == 0 and $endofgame == 0) {
?>
        <script language="JavaScript" type="text/javascript"><!--
        if (Initialized) {
<?php
if ($print == false) {
    echo "document.getElementById(\"welcomemessage\").style.display = \"\";";
}

if ($showsolution == false) {
    ?>
    document.getElementById("checkbutton").style.display = "";
    document.getElementById("finishattemptbutton").style.display = "";
    document.getElementById("printbutton").style.display = "";
<?php
}
?>
}
//-->
</script>
<?php
    }
}

?>

</body>

<?php
}

/**
 * Shows welcome message.
 *
 * @param stdClass $game
 */
function game_cross_show_welcome( $game) {
    if ($game->param3 <> 2) {
        game_cross_show_welcome0( $game);
    } else {
        game_cross_show_welcome1();
    }
}

/**
 * Shows welcome message0.
 *
 * @param stdClass $game
 */
function game_cross_show_welcome0( $game) {
?>
<td valign="top" style="padding-left: 1em;">

<div id="welcomemessage" class="answerboxstyle" style="display:none;">  
<?php echo get_string( 'cross_welcome', 'game'); ?> </div>

<div id="answerbox2"  style="display:none;"></div>

<div id="answerbox" class="answerboxstyle" style="display:none;">
<h3 id="wordlabel" style="text-transform:uppercase;margin:0;"> </h3>
<div id="wordinfo" style="font-size:8pt;color:#808080"> </div>
<div id="wordclue" class="cluebox"> </div>
<div style="margin-top:1em;"><input id="wordentry" type="text" size="24"
<?php
echo 'style="font-weight: bold; ';
if ($game->param6 == 0) {
    echo 'text-transform:uppercase;';
}
echo '"';
?>
 onkeypress="WordEntryKeyPress(event)" onchange="WordEntryKeyPress(event)" autocomplete="off"></div>
<?php
if ($game->param3 == 2) {
    game_cross_show_welcome( $game);
}
?>
<div id="worderror" style="color:#c00000;font-weight:bold;display:none;margin-top:1em;"></div>

<table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:1em;"><tr>
<td align="right">
<button id="okbutton" type="button" class="button" onclick="OKClick();" style="font-weight: bold;">OK</button> &nbsp;
<button id="cancelbutton" type="button" class="button" onclick="DeselectCurrentWord();">Cancel</button>
</td></tr></table>

</div>


</td>
<?php
}

/**
 * Shows welcome message 1.
 */
function game_cross_show_welcome1() {
?>
<td valign="top" style="padding-left: 1em;">

<div id="welcomemessage" class="answerboxstyle" style="display:none;">  
<?php echo get_string( 'cross_welcome', 'game'); ?> </div>

<div id="answerbox" class="answerboxstyle" style="display:none;">

<div style="margin-top:1em;"><input id="wordentry" type="text" size="24"
 style="font-weight: bold; text-transform:uppercase;"
 onkeypress="WordEntryKeyPress(event)" onchange="WordEntryKeyPress(event)" autocomplete="off"></div>
<div id="worderror" style="color:#c00000;font-weight:bold;display:none;margin-top:1em;"></div>

<table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:1em;"><tr>
<td align="right">
<button id="okbutton" type="button" class="button" onclick="OKClick();" style="font-weight: bold;">OK</button> &nbsp;
<button id="cancelbutton" type="button" class="button" onclick="DeselectCurrentWord();">Cancel</button>
</td></tr></table>

</td><td>&nbsp</td><td>

<div id="answerbox2" class="answerboxstyle" style="display:none;">
<h3 id="wordlabel" style="text-transform:uppercase;margin:0;"> </h3>
<div id="wordinfo" style="font-size:8pt;color:#808080"> </div>
<div id="wordclue" class="cluebox"> </div>
</div>

</div>

</td>
<?php
}


/**
 * Show the legend (horizontal and vertical)
 *
 * @param stdClass $cross
 */
function game_cross_show_legends( $cross) {
    ShowLegend( $cross->mlegendh,  get_string( 'cross_across', 'game'));
    ShowLegend( $cross->mlegendv, get_string( 'cross_down', 'game'));
}
