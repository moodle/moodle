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
 * This page export the game millionaire to html
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Exports millionaire.
 *
 * @param stdClass $game
 * @param stdClass $context
 * @param int $maxanswers
 * @param int $countofquestions
 * @param string $retfeedback
 * @param string $destdir
 * @param array $files
 */
function game_millionaire_html_getquestions( $game, $context, &$maxanswers, &$countofquestions, &$retfeedback, $destdir, &$files) {
    global $CFG, $DB, $USER;

    $maxanswers = 0;
    $countofquestions = 0;

    $files = array();

    if ( ($game->sourcemodule != 'quiz') and ($game->sourcemodule != 'question')) {
        print_error( get_string('millionaire_sourcemodule_must_quiz_question', 'game', get_string( 'modulename', 'quiz')).
            ' '.get_string( 'modulename', $game->sourcemodule));
    }

    if ( $game->sourcemodule == 'quiz') {
        if ( $game->quizid == 0) {
            print_error( get_string( 'must_select_quiz', 'game'));
        }
        $select = "qtype='multichoice' AND quiz='$game->quizid' ".
            " AND qqi.question=q.id";
        $table = "{question} q,{quiz_question_instances} qqi";
    } else {
        if ( $game->questioncategoryid == 0) {
            print_error( get_string( 'must_select_questioncategory', 'game'));
        }

        // Include subcategories.
        $select = 'category='.$game->questioncategoryid;
        if ( $game->subcategories) {
            $cats = question_categorylist( $game->questioncategoryid);
            if (strpos( $cats, ',') > 0) {
                $select = 'category in ('.$cats.')';
            }
        }
        $select .= " AND qtype='multichoice'";

        $table = "{question} q";
    }
    $select .= " AND q.hidden=0";
    $sql = "SELECT q.id as id, q.questiontext FROM $table WHERE $select";
    $recs = $DB->get_records_sql( $sql);
    $ret = '';
    $retfeedback = '';
    foreach ($recs as $rec) {
        $recs2 = $DB->get_records( 'question_answers', array( 'question' => $rec->id), 'fraction DESC', 'id,answer,feedback');

        // Must parse the questiontext and get the name of files.
        $line = $rec->questiontext;
        $line = game_export_split_files( $game->course, $context, 'questiontext', $rec->id, $rec->questiontext, $destdir, $files);
        $linefeedback = '';
        foreach ($recs2 as $rec2) {
            $line .= '#'.str_replace( array( '"', '#'), array( "'", ' '),
                game_export_split_files( $game->course, $context, 'answer', $rec2->id, $rec2->answer, $destdir, $files));
            $linefeedback .= '#'.str_replace( array( '"', '#'), array( "'", ' '), $rec2->feedback);
        }
        if ( $ret != '') {
            $ret .= ",\r";
        }
        $ret .= '"'.base64_encode( $line).'"';

        if ( $retfeedback != '') {
            $retfeedback .= ",\r";
        }
        $retfeedback .= '"'.base64_encode( $linefeedback).'"';

        if ( count( $recs2) > $maxanswers) {
            $maxanswers = count( $recs2);
        }
        $countofquestions++;
    }

    return $ret;
}

/**
 * Exports to html a "Millionaire" game.
 *
 * @param stdClass $game
 * @param string $questions
 * @param int $maxquestions
 */
function game_millionaire_html_print( $game,  $questions, $maxquestions) {
    $color1 = 'black';
    $color2 = 'DarkOrange';
    $colorback = "white";
    $stylequestion = "background:$colorback;color:$color1";
    $stylequestionselected = "background:$colorback;color:$color2";
?>

<body onload="Reset();">

<script type="text/javascript">

    // Millionaire for Moodle by Vasilis Daloukas.    
    <?php echo 'var questions = new Array('.$questions.");\r"; ?>
    var current_question = 0;
    var level = 0;
    var posCorrect = 0;
    var infoCorrect = "";
    var flag5050 = 0;
    var flagTelephone = 0;
    var flagPeople = 0;
    var countQuestions = 0;
    var maxQuestions = <?php echo $maxquestions;?>;
    
    function Highlite( ans) {
        document.getElementById( "btAnswer" + ans).style.backgroundColor = '<?php echo $color2;?>';
    }

    function Restore( ans) {
        document.getElementById( "btAnswer" + ans).style.backgroundColor = '<?php echo $colorback;?>';
    }

    function OnSelectAnswer( ans) {
        if ( posCorrect == ans) {
            if( level+1 > 15) {
                alert( "<?php echo get_string( 'win', 'game');?>");
                Reset();
            } else {
                UpdateLevel( level+1);
                SelectNextQuestion();
            }
        } else {
            OnGameOver( ans);
        }
    }

    function OnGameOver( ans) {
        document.getElementById( "info").innerHTML = "<?php echo get_string( 'millionaire_info_wrong_answer', 'game');?> " +
            document.getElementById( "lblAnswer" + posCorrect).innerHTML;
        Highlite( posCorrect);
        Restore( ans);
        document.getElementById( "lblAnswer" + posCorrect).style.backgroundColor = '<?php echo $color2;?>';
        
        alert( "<?php echo strip_tags( get_string( 'hangman_loose', 'game')); ?>");
      
        Restore( posCorrect); 
        document.getElementById( "lblAnswer" + posCorrect).style.backgroundColor = '<?php echo $colorback;?>';

        Reset();
    }

    function UpdateLevel( newlevel) {
        if ( level > 0) {
            document.getElementById( "levela" + level).bgColor = "<?php echo $colorback;?>";
            document.getElementById( "levelb" + level).bgColor = "<?php echo $colorback;?>";
            document.getElementById( "levelc" + level).bgColor = "<?php echo $colorback;?>";
            document.getElementById( "levela" + level).style.color = "<?php echo $color1;?>";
            document.getElementById( "levelb" + level).style.color = "<?php echo $color1;?>";
            document.getElementById( "levelc" + level).style.color = "<?php echo $color1;?>";
        }

        level = newlevel;

        document.getElementById( "levela" + level).bgColor = "<?php echo $color2;?>";
        document.getElementById( "levelb" + level).bgColor = "<?php echo $color2;?>";
        document.getElementById( "levelc" + level).bgColor = "<?php echo $color2;?>";
        document.getElementById( "levela" + level).style.color = "<?php echo $colorback;?>";
        document.getElementById( "levelb" + level).style.color = "<?php echo $colorback;?>";
        document.getElementById( "levelc" + level).style.color = "<?php echo $colorback;?>";
   }

    function OnHelp5050( ans) {
        if (flag5050) {
            return;
        }

        document.getElementById( "Help5050").src = "5050x.png";
        flag5050 = 1;

        for (pos = posCorrect;pos == posCorrect;pos = 1+Math.floor(Math.random()*countQuestions));

        for (i=1; i <= countQuestions; i++) {   
            if( (i != pos) && (i != posCorrect)) {         
                document.getElementById( "lblAnswer" + i).style.visibility = 'hidden';
                document.getElementById( "btAnswer" + i).style.visibility = 'hidden';
            }
        }
    }

    function OnHelpTelephone( ans) {
        if( flagTelephone) {
            return;
        }
        flagTelephone = 1;
        document.getElementById( "HelpTelephone").src = "telephonex.png";

        if (countQuestions < 2) {
            wrong = posCorrect;
        } else {
            for(;;) {
                wrong = 1 + Math.floor(Math.random() * countQuestions);
                if ( wrong != posCorrect) {
                    break;
               }
            }
        }

        // With 80% gives the correct answer.
        if (Math.random() <= 0.8) {
            pos = posCorrect;
        } else {
            pos = wrong;
        }

        info = "<?php echo get_string( 'millionaire_info_telephone', 'game').'<br><b>';?> ";
        info += document.getElementById( "lblAnswer" + pos).innerHTML;
        document.getElementById( "info").innerHTML = info;
    }

    function OnHelpPeople( ans) {
        if( flagPeople) {
            return;
        }
        flagPeople = 1;
        document.getElementById( "HelpPeople").src = "peoplex.png";

        sum = 0;
        var aPercent = new Array();
        for( i = 0; i < countQuestions-1; i++) {
            percent = Math.floor(Math.random()*(100-sum));
            aPercent[ i] = percent;
            sum += percent;
        }
        aPercent[ countQuestions - 1] = 100 - sum;
        if( Math.random() <= 0.8) {
            //with percent 80% sets in the correct answer the biggest percent
            max_pos = 0;
            for( i=1; i < countQuestions; i++) {
                if( aPercent[ i] >= aPercent[ max_pos])
                    max_pos = i;
            }
            temp = aPercent[ max_pos];
            aPercent[ max_pos] = aPercent[ posCorrect-1];
            aPercent[ posCorrect-1] = temp;
        }
        
        var letters = "<?php echo get_string( 'lettersall', 'game');?>";
        info = "<?php echo '<br>'.get_string( 'millionaire_info_people', 'game').':<br>';?>";
        for( i=0; i < countQuestions; i++) {
            info += "<br>" + letters.charAt( i) + " : " + aPercent[ i] + " %";
        }

        document.getElementById( "info").innerHTML = info;
    }

    function OnQuit( ans) {
        Reset();
    }

    function Reset() {
        for(i=1; i <= 15; i++) {
            document.getElementById( "levela" + i).bgColor = "<?php echo $colorback;?>";
            document.getElementById( "levelb" + i).bgColor = "<?php echo $colorback;?>";
            document.getElementById( "levelc" + i).bgColor = "<?php echo $colorback;?>";
            document.getElementById( "levela" + i).style.color = "<?php echo $color1;?>";
            document.getElementById( "levelb" + i).style.color = "<?php echo $color1;?>";
            document.getElementById( "levelc" + i).style.color = "<?php echo $color1;?>";
        }

        flag5050 = 0;
        flagTelephone = 0;
        flagPeople = 0;

        document.getElementById( "Help5050").src = "5050.png";
        document.getElementById( "HelpPeople").src = "people.png";
        document.getElementById( "HelpTelephone").src = "telephone.png";

        document.getElementById( "info").innerHTML = "";
        UpdateLevel( 1);
        SelectNextQuestion();
    }

    function RandomizeAnswers( elements) {
        posCorrect = 1;
        countQuestions = elements.length-1;

        for( i=1; i <= countQuestions; i++) {
            pos = 1+Math.floor(Math.random()*countQuestions);
            if( posCorrect == i) {
                posCorrect = pos;
            } else if ( posCorrect == pos)
                posCorrect = i;
                
            var temp = elements[ i];
            elements[ i] = elements[ pos];
            elements[ pos] = temp;
        }
    }
    
    function SelectNextQuestion() {   
        current_question = Math.floor(Math.random()*questions.length);
        question = Base64.decode( questions[ current_question]);

        var elements = new Array();
        elements = question.split('#');
        
        RandomizeAnswers( elements);

        document.getElementById( "question").innerHTML = elements[ 0];
        for( i=1; i < elements.length; i++) {
            document.getElementById( "lblAnswer" + i).innerHTML = elements[ i];
            document.getElementById( "lblAnswer" + i).style.visibility = 'visible';
            document.getElementById( "btAnswer" + i).style.visibility = 'visible';
        }
        for( i=elements.length; i<= maxQuestions; i++) {
            document.getElementById( "lblAnswer" + i).style.visibility = 'hidden';
            document.getElementById( "btAnswer" + i).style.visibility = 'hidden';
        }

        document.getElementById( "info").innerHTML = "";
    }
    
/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Base64 = {
 
    // Private property.
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // Public method for decoding.
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
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
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
</script>

<table cellpadding=0 cellspacing=0 border=0>
<tr style='background:#408080'>
<td rowspan=<?php echo 17 + $maxquestions;?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td colspan=6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td rowspan=<?php echo 17 + $maxquestions;?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>

<tr height=10%>
<td style='background:#408080' rowspan=3 colspan=2>
<input type="image"  name="Help5050" id="Help5050" Title="50 50" src="5050.png" alt="" border="0" onmousedown=OnHelp5050();>&nbsp;
<input type="image" name="HelpTelephone"  id="HelpTelephone" Title="<?php echo get_string( 'millionaire_telephone', 'game');?>" 
    src="telephone.png" alt="" border="0" onmousedown="OnHelpTelephone();">&nbsp;
<input type="image" name="HelpPeople"  id="HelpPeople" Title="<?php echo get_string( 'millionaire_helppeople', 'game');?>" 
    src="people.png" alt="" border="0" onmousedown="OnHelpPeople();">&nbsp;
<input type="image" name="Quit" id="Quit" Title="<?php echo get_string( 'millionaire_quit', 'game');?>" 
    src="x.png" alt="" border="0" onmousedown=OnQuit();>&nbsp;
</td>
<td rowspan=<?php echo 16 + $maxquestions;?> style='background:#408080'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td id="levela15" align=right>15</td>
<td id="levelb15">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td id="levelc15" align=right>    150000</td>
</tr>

<tr><td id="levela14" align=right>14</td>
<td id="levelb14"></td><td id="levelc14" align=right>       800000</td>
</tr>

<tr><td id="levela13" align=right>13</td>
<td id="levelb13"></td><td id="levelc13" align=right>       400000</td>
</tr>

<tr><td rowspan=12 colspan=2 valign=top style='background:<?php echo $colorback;?>;color:<?php echo $color1;?>'>
    <div id="question">aa</div></td>
<td id="levela12" align=r0ight>12</div></td>
<td id="levelb12"></td><td id="levelc12" align=right>       200000</td>
</tr>

<tr><td id="levela11" align=right>11</td>
<td id="levelb11"></td><td id="levelc11" align=right>       10000</td>
</tr>

<tr><td id="levela10" align=right>10</td>
<td id="levelb10"></td><td id="levelc10" align=right>       5000</td>
</tr>

<tr><td id="levela9" align=right>9</td>
<td id="levelb9"></td><td id="levelc9" align=right>       4000</td>
</tr>

<tr><td id="levela8" align=right>8</td>
<td id="levelb8"></td><td id="levelc8" align=right>       2000</td>
</tr>

<tr><td id="levela7" align=right>7</td>
<td id="levelb7"></td><td id="levelc7" align=right>       1500</td>
</tr>

<tr><td id="levela6" align=right>6</td>
<td id="levelb6"></td><td id="levelc6" align=right>       1000</td>
</tr>

<tr><td id="levela5" align=right>5</td>
<td id="levelb5"></td><td id="levelc5" align=right>       500</td>
</tr>

<tr><td id="levela4" align=right>4</td>
<td id="levelb4"></td><td id="levelc4" align=right>       400</td>
</tr>

<tr><td id="levela3" align=right>3</td>
<td id="levelb3"></td><td id="levelc3" align=right>       300</td>
</tr>

<tr><td id="levela2" align=right>2</td>
<td id="levelb2"></td><td id="levelc2" align=right>       200</td>
</tr>

<tr><td id="levela1" align=right>1</td>
<td id="levelb1"></td><td id="levelc1" align=right>       100</td>
</tr>

<tr style='background:#408080'><td colspan=10>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>

<?php
$letters = get_string( 'lettersall', 'game');
for ($i = 1; $i <= $maxquestions; $i++) {
    $s = game_substr( $letters, $i - 1, 1);
    echo "<tr>\n";
    echo "<td style='background:$colorback;color:$color1'>";
    echo "<input style=\"background:$colorback;color:$color1;\"
        type=\"submit\" name=\"btAnswer$i\" value=\"$s\" id=\"btAnswer$i\"";
    echo " onmouseover=\"Highlite( $i);\" onmouseout=\"Restore( $i);\"  onmousedown=\"OnSelectAnswer( $i);\">";
    echo "</td>\n";
    echo "<td style=\"background:$colorback;color:$color1;\" width=100%> &nbsp; <span id=lblAnswer$i
        style=\"background:$colorback;color:$color1\"
        onmouseover=\"Highlite($i);\r \n\" onmouseout=\"Restore( $i);\" onmousedown=\"OnSelectAnswer( $i);\"></span></td>\n";
    if ( $i == 1) {
        echo "<td style='background:#408080' rowspan=".$maxquestions." colspan=3><div id=\"info\"></div></td>\n";
    }
    echo "</tr>\n";
}
?>

<tr><td colspan=10 style='background:#408080'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>

</table>


</body>
</html>
<?php
}
