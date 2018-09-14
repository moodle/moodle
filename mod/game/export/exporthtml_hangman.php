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
 * This page export the game hangman to html
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

?>
<script type="text/javascript">

// Hangman for Moodle by Vasilis Daloukas.
// The script is based on HangMan II script- By Chris Fortey (http://www.c-g-f.net/)

var can_play = true;
<?php

$destdir = game_export_createtempdir();

$exportattachment = ( $html->type == 'hangmanp');
$map = game_exmportjavame_getanswers( $game, $context, $exportattachment, $destdir, $files);
if ($map == false) {
    print_error( 'No Questions');
}

$questions = '';
$words = '';
$lang = '';
$allletters = '';
$images = '';
foreach ($map as $line) {
    $answer = game_upper( $line->answer);
    if ($game->param7) {
        // Have to delete space.
        $answer = str_replace(' ', '', $answer);
    }
    if ($game->param8) {
        // Have to deletε -.
        $answer = str_replace('-', '', $answer);
    }

    if ($lang == '') {
        $lang = $game->language;

        if ($lang == '') {
            $lang = game_detectlanguage( $answer);
        }
        if ($lang == '') {
            $lang = current_language();
        }
        $allletters = game_getallletters( $answer, $lang);
    }

    if (game_getallletters( $answer, $lang) != $allletters) {
        continue;
    }

    if ($html->type == 'hangmanp') {
        $file = $line->attachment;
        $pos = strrpos( $file, '.');
        if ($pos == false) {
            continue;
        }
    }

    if ($html->type == 'hangmanp') {
        $src = $line->attachment;
        $pos = strrpos( $file, '.');
        if ($pos == false) {
            continue;
        }
    }

    if ($questions != '') {
        $questions .= ', ';
    }

    if ($words != '') {
        $words .= ', ';
    }
    $questions .= '"'.base64_encode( $line->question).'"';
    $words .= '"'.base64_encode( $line->answer).'"';

    if ($html->type == 'hangmanp') {
        $file = $line->id.substr( $file, $pos);
        game_export_javame_smartcopyimage( $src, $destdir.'/'.$file, $html->maxpicturewidth, $html->maxpictureheight);

        if ($images != '') {
            $images .= ', ';
        }
        $images .= '"'.$file.'"';
    }
}

if ($game->param7) {
    $allletters .= '_';
}

if ($game->param8) {
    $allletters .= '-';
}

echo "var questions = new Array($questions);\r";
echo "var words = new Array($words);\r";
if ($html->type == 'hangmanp') {
    echo "var images = new Array($images);\r";
}
?>

var to_guess = "";
var display_word = "";
var used_letters = "";
var wrong_guesses = 0;
var used_letters_all = "";
var all_letters = new Array(<?php
$len = game_strlen( $allletters);
for ($i = 0; $i < $len; $i++) {
    if ($i > 0) {
        echo ',';
    }
    echo '"'.game_substr( $allletters, $i, 1).'"';
}
?>);

function selectLetter(l)
{
    if (can_play == false)
    {
    }

    if (used_letters.indexOf(l) != -1)
    {
        return;
    }

    used_letters_all += l;

    if( to_guess.indexOf(l) == -1) {
        used_letters += l;
        document.getElementById('usedLetters').innerHTML = used_letters;
    }

    if (to_guess.indexOf(l) != -1) {
        // correct letter guess
        pos = 0;
        temp_mask = display_word;

        while (to_guess.indexOf(l, pos) != -1) {
            pos = to_guess.indexOf(l, pos);
            end = pos + 1;

            start_text = temp_mask.substring(0, pos);
            end_text = temp_mask.substring(end, temp_mask.length);

            temp_mask = start_text + l + end_text;
            pos = end;
        }

        display_word = temp_mask;
        document.getElementById('displayWord').innerHTML=display_word;

        if (display_word.indexOf("#") == -1) {
            // won
            alert( "<?php echo game_get_string_lang( 'win', 'mod_game', $lang); ?>");
            can_play = false;
            reset();
        }
    } else {
        wrong_guesses++;

<?php
if ($html->type != 'hangmanp') {
?>eval("document.hm.src=\"hangman_" + wrong_guesses + ".jpg\"");
        // Ιncortect letter guess.
        eval("document.hm.src=\"hangman_" + wrong_guesses + ".jpg\"");
<?php
}
?>
        if (wrong_guesses == <?php echo $game->param10 + 1;?>) {
            // lost
            alert( "<?php echo strip_tags( game_get_string_lang( 'hangman_loose', 'mod_game', $lang)); ?>");
            can_play = false;
            reset();
        }
    }
    
    showallletters();
}

function stripHTML(oldString) {

  return oldString.replace(/<&#91;^>&#93;*>/g, "");
  
}

function reset() {
    selectWord();

    document.getElementById('usedLetters').innerHTML = "&nbsp;";
    used_letters = "";
    used_letters_all = "";
    wrong_guesses = 0;
    showallletters();

<?php
if ($html->type != 'hangmanp') {
    echo '    document.hm.src="hangman_0.jpg"'."\r";
}
?>

}

function showallletters() {
    var letters = "";
    var next =  all_letters.length / 4;
    var letter = "";
    
    for( i=0; i < all_letters.length; i++) {
        if( i > next) {
            next += all_letters.length / 4;
            letters += " ";
        }
        
        letter = all_letters[ i];
        if( used_letters_all.length > 0) {
            if( used_letters_all.indexOf( letter) > -1) {
                continue;
            }
        }

        letters = letters + "<a href=\"javascript:selectLetter('" + letter + "');\">" + letter + "</a>"
    }
    document.getElementById( "letters").innerHTML = letters;
}

function selectWord() {
    can_play = true;
    random_number = Math.round(Math.random() * (words.length - 1));
    to_guess =  Base64.decode( words[random_number]);
    to_question = Base64.decode( questions[random_number]);

    // Display masked word.
    masked_word = createMask(to_guess);
    document.getElementById('displayWord').innerHTML=masked_word;
    
    display_word = masked_word;
    
<?php
if ($html->type == 'hangmanp') {
    echo "    document.hm.src = images[ random_number];\r";
} else {
    echo "    document.getElementById('question').innerHTML=to_question;\r";
}
?>
}

function createMask(m)
{
    mask = "";
    word_lenght = m.length;


    for (i = 0; i < word_lenght; i++) {
        mask += "#";
    }

    return mask;
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
 
    // Private method for UTF-8 decoding.
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
</head>

<div id="question"></div>
<img src="<?php echo ($html->type == 'hangmanp' ? '' : 'hangman_0.jpg');?>" name="hm"> 
<a href="javascript:reset();"><?php echo game_get_string_lang( 'html_hangman_new', 'mod_game', $lang); ?></a>
<form name="game">
<div id="displayWord"> </div>
<div id="usedLetters"> </div>
</form>
<div id="letters"></div>

</body>
