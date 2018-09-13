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
 * This page export the game snakes to html
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $html->title;?></title>

<link href="css/game.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="css/subModal.css" />
    <script type="text/javascript" src="js/common.js"></script>

<?php
    createsubmodaljs();
?>
    
<style type="text/css">
#pawn
{
    position:absolute;
}

img
{
    border:hidden

}
body
{ 
    background: #999 url('images/backdropJungle.png') no-repeat fixed left top;
}

.score {
    color: #FC3; 
    font-size: 40px;
}
</style>

</head>

<body>

<script language="JavaScript">
// Snakes for Moodle by Maria Rigkou.

var boards = 1;
var board_images = new Array( boards);
var board_names = new Array( boards);
var pawn_width = new Array(boards);
var pawn_height = new Array(boards);
var board_cols = new Array(boards);
var board_rows = new Array(boards);
var board_contents = new Array (boards);
var board_headerx = new Array(boards);
var board_headery = new Array(boards);
var board_footerx = new Array(boards);
var board_footery = new Array(boards);
var board_width = new Array(boards);
var board_height = new Array(boards);
var board_data = new Array(boards); 
var pawn_width = new Array(boards); 
var pawn_height = new  Array(boards); 

var current_board = 0;
var current_position=0;
var current_quest = 0;
var mchoice_count = 0;
var mchoice_positions =new Array( 1);

var quest_text = "";    //Question
var quest_resp = "";    // Answer
var quest_feedb = "";   // feedback
var quest_total = 25; // Count of questions

board_images[ 0] = '<?php echo $board->fileboard; ?>';
board_names[ 0] = "<?php echo $game->name; ?>";
board_cols [0] = <?php echo $board->usedcols; ?>;
board_rows [0] = <?php echo $board->usedrows; ?>;
board_contents [0] = '<?php echo $board->data; ?>';
board_headerx [0] = <?php echo $board->headerx; ?>;
board_headery [0] = <?php echo $board->headery; ?>;
board_footerx [0] = <?php echo $board->footerx; ?>;
board_footery [0] = <?php echo $board->footery; ?>;
board_width [0] = <?php echo $board->width; ?>;
board_height [0] = <?php echo $board->height; ?>;
pawn_width [0] = 40;
pawn_height [0]= 40;

<?php

echo "var countofquestionsM=$countofquestionsm;\r\n";
echo 'var countofquestionsS='.count($questionss).";\r\n";

$questionsm = '';
foreach ($questionss as $line) {
    $s = $line->question.'#'.str_replace( array( '"', '#'), array( "'", ' '), $line->answer);
    if ($questionsm != '') {
        $questionsm .= ",\r";
    }
    $questionsm .= '"'.base64_encode( game_upper( $s)).'"';

    $s = '#'.str_replace( array( '"', '#'), array( "'", ' '), $line->feedback);
    if ($retfeedback != '') {
        $retfeedback .= ",\r";
    }
    $retfeedback .= '"'.base64_encode( $s).'"';
}
$rettimesasked = '';
for ($i = 0; $i < $countofquestionsm + count($questionss); $i++) {
    $rettimesasked .= ',0';
}
$rettimesasked = substr( $rettimesasked, 1);

echo "var questions=new Array( $questionsm);\r\n";
echo "var feedbacks=new Array( $retfeedback);\r\n";
echo "var quest_times_asked=new Array( $rettimesasked); //How many times is used a question\r\n";

?>
var current_dice=0;
var feedb_correct_S = "<?php print_string( 'html_snakes_correct', 'game'); ?>";
var feedb_wrong_S = "<?php print_string( 'html_snakes_wrong', 'game'); ?>";
var str_score = "<?php print_string( 'score', 'game'); ?>";
var str_check = "<?php print_string( 'html_snakes_check', 'game'); ?>";
var str_no_selection = "<?php print_string( 'html_snakes_no_selection', 'game'); ?>";

var correct_ans = 0;    //counter of correct answers
var all_ans = 0;        //counter of all answers
var score = 0;

ShowMainForm();
display_quest();

function ShowMainForm()
{
    var pawn_x =0;
    var pawn_y=0;
    var direction=0;
    var cols = board_cols[current_board];
    var rows = board_rows[current_board];
    var col_width = (board_width[current_board]-board_headerx[current_board]-board_footerx[current_board])/cols;
    var col_height = (board_height[current_board]-board_headery[current_board]-board_footery[current_board])/rows;

    document.write('<img id="boardimage" src="images/' + board_images[ current_board] + '">');
    document.write('<div id="dicecont">&nbsp;</div>');

    if( current_position  >= 0) {
        direction=Math.floor((current_position /cols))%2;
        if (direction == 1) {
            axis_x=(cols-(current_position %cols)-1);
        } else {
            axis_x=current_position %cols;
        }
        axis_y=Math.floor((current_position /rows));
        pawn_x=board_headerx[current_board]+(axis_x*col_width)+(col_width-pawn_width[current_board])/2;
        pawn_y=board_footery[current_board]+pawn_height[current_board]+(axis_y*col_height);
        pawn_y += (col_height-pawn_height[current_board])/2;

        document.write('<div id="pawn1"><img id="pawn" alt="" src="images/player1.png"></div>');
        move_pawn();
    }
}

function select_quest() {
    var quest_total = countofquestionsM + countofquestionsS;
    var quest_candidates= new Array();
    var i, q;

    for (i=0;i<3;i++) {
        quest_candidates[i]=Math.floor((Math.random() * quest_total));
    }
    current_quest = quest_candidates[0];
    for (i=1;i<3;i++) {
        if (quest_times_asked[quest_candidates[i]]>quest_times_asked[current_quest])
            current_quest=quest_candidates[i];
    }

    q=Base64decode( questions[ current_quest]);
    quest_resp = decode_multiple_choice( q);
    quest_text = quest_resp[ 0];
    quest_feedb = Base64decode( feedbacks[ current_quest]);
}

function IsMultipleChoiceQuestion() {
    return (current_quest < countofquestionsM);
}

function check_answer() {
    all_ans=all_ans+1;
    if( IsMultipleChoiceQuestion()) {
        check_answer_M();
    } else {
        check_answer_S();
    }

    move_pawn();
}

function check_answer_M() {
    document.getElementById("check_btn").style.display = "none";

    var useranswer;
    var n=document.snakesform.radio_answer.length;
    for(useranswer=0;useranswer < n;useranswer++) {
        if( document.snakesform.radio_answer[ useranswer].checked)
            break;
    }

    if( useranswer >= n) {
        alert( str_no_selection);
        document.getElementById("feedb").innerHTML= "";
        document.getElementById("check_btn").style.display = "block";
        return;
    }

    var feedbacks = decode_multiple_choice(quest_feedb);

    var j;
    for (j=0;j<n;j++) {
        document.snakesform.radio_answer[ j].disabled = "true"; 
    }

    document.getElementById("feedb").innerHTML= feedbacks[ mchoice_positions[useranswer]];
    document.getElementById("feedb").style.display = "block";

    if ( mchoice_positions[ useranswer] == 1) {
        current_position += current_dice;
        correct_ans =correct_ans+1; //calculate new score----
        score = Math.round((correct_ans/all_ans)*100);
        var s = '<strong>'+str_score+': </strong><strong class="score">' +score+ '</strong>';
        document.getElementById("show_score").innerHTML = s;
        check_game_over();
        check_exists_ladder();
    } else {
        score = Math.round((correct_ans/all_ans)*100);
        var s = '<strong>'+str_score+': </strong><strong class="score">' +score+ '</strong>';
        document.getElementById("show_score").innerHTML = s;
        check_exists_snake();
    }

    document.getElementById("OK_btn").style.display = "block";
}

function check_answer_S() {
    document.getElementById("answer").disabled = "true";
    document.getElementById("check_btn").style.display = "none";

    if (document.getElementById("answer").value.toUpperCase() == quest_resp[ 1].toUpperCase())  {
        document.getElementById("feedb").style.display = "block";
        current_position += current_dice;
        correct_ans =correct_ans+1; //calculate new score
        score = Math.round((correct_ans/all_ans)*100); 
        var s = '<strong>'+str_score+': </strong><strong class="score">' +score+ '</strong>';
        document.getElementById("show_score").innerHTML = s;
        check_game_over();
        check_exists_ladder();
    } else {
        document.getElementById("feedb_wrong").style.display = "block";
        score = Math.round((correct_ans/all_ans)*100); 
        var s = '<strong>'+str_score+': </strong><strong class="score">' +score+ '</strong>';
        document.getElementById("show_score").innerHTML = s;
        check_exists_snake();
    }

    document.getElementById("OK_btn").style.display = "block";
}

function check_game_over() {
    var out=(board_cols[current_board]*board_rows[current_board]);
    if (current_position > out-1) {
        current_position=out-1;
        showPopWin('modalContent.html', 350, 220, returnRefresh); // modal
    }
}

function check_exists_ladder() {
    var find = "L" + (current_position+1) + "-";
    var pos = board_contents[ current_board].indexOf( find);

    if( pos < 0) {
        return;
    }

    var s = board_contents[ current_board].substr( pos+find.length)
    pos = s.indexOf( ',');
    if (pos >= 0) {
        s = s.substr( 0, pos);
    }

    current_position = s-1;
}

function check_exists_snake() {
    var find = "-" + (current_position+1) + ",";
    var s= ',' +board_contents[ current_board] + ',';

    for(;;) {
        var pos = s.indexOf( find);

        if (pos < 0) {
            return;
        }

        var pos_start = s.lastIndexOf( ',', pos-1);

        var kind = s.substr( pos_start+1, 1);
        if ( kind != "S") {
            s = s.substr( pos+1);
            continue;
        }
        s = s.substr( pos_start+2);
        pos = s.indexOf( '-');
        current_position = s.substr( 0, pos)-1;
        break;
    }
}

function decode_multiple_choice(s) {
    var ret = new Array();

    var i=0;
    for(;;) {
        var pos=s.indexOf( '#');
        if( pos < 0) {
            ret[ i++] = s;
            return ret;
        }
        ret[ i++] = s.substr( 0, pos);
        s = s.substr( pos+1);
    }
}

function display_quest()  {
    current_dice = Math.floor((Math.random() * 6)) + 1;
    select_quest();

    if( IsMultipleChoiceQuestion()) {
        display_quest_M();
    } else {
        display_quest_S();
    }
}

function display_quest_M() {
    s = '<table width="250px"><tr><td><div id="show_dice"> ';
    s = s + '<img src = "images/dice' + current_dice + '.png"> </div> </td>';
    s = s + '<td align=right><div id="show_score" style="color: #FFFFFF; font-weight:bold; font-size: 20px;">';
    s = s + '<strong>'+str_score+': </strong>';
    s = s + '<strong class="score">' +score+ '</strong></div></td></tr></table>';
    s = s + '<div id="question_area">' + quest_text+'</div>';
    s = s + '<form name="snakesform">';

    mchoice_count = quest_resp.length-1;
    mchoice_positions = new Array( mchoice_count);
    for(i=0; i < mchoice_count ; i++) {
        mchoice_positions[ i] = i+1;
    }
    for(i=0; i < mchoice_count ; i++) {
        var j = Math.floor((Math.random() * mchoice_count));
        var temp = mchoice_positions[ i];
        mchoice_positions[ i] = mchoice_positions[ j];
        mchoice_positions[ j] = temp;
    }

    for(i=0; i < mchoice_count;i++) {
        s = s + '<input type="radio" name="radio_answer" id="radio_answer" value="';
        s = s + i+'" />'+quest_resp[ mchoice_positions[ i]] + '<br />';
    }

    s = s + '<br /><input type="button" id="check_btn" value="'+str_check;
    s = s + '" onclick="check_answer();">  <br/><div id="feedb_area"> <div id="feedb_wrong" style="display:none; color:yellow;"> ';
    s = s + quest_feedb+' </div> <br /><div id="feedb" style="display:none; color:yellow;"> ';
    s = s + quest_feedb+'. Θα προχωρήσεις ';
    s = s + current_dice+' τετράγωνα μπροστά!</div><br /> <div id="OK_btn"';   
    s = s + 'style="display:none;"><input type="button" onclick="display_quest();" value="OK"/></div> </div></form>';

    document.getElementById("dicecont").innerHTML = s;

    document.getElementById("question_area").style.display = "block";
    document.getElementById("check_btn").style.display = "block";
}

function display_quest_S() {
    var s = "";

    s = '<table width="250px"><tr><td><div id="show_dice"> <img src = "images/dice';
    s = s + current_dice + '.png"> </div> </td><td align=right>';
    s = s + '<div id="show_score" style="color: #FFFFFF; font-weight:bold; font-size: 20px;"><strong>';
    s = s + str_score+': </strong><strong class="score">' +score+ '</strong></div></td></tr></table><div id="question_area">';
    s = s + quest_text+'</div> <br /><input type="text" id="answer"/><br /><br /> <input type="button" id="check_btn" value="';
    s = s + str_check+'" onclick="check_answer();"> <br /> <div id="feedb_area">';
    s = s + ' <div id="feedb_wrong" style="display:none; color:yellow;"> ';
    s = s + feedb_wrong_S+' </div> <div id="feedb" style="display:none; color:yellow;"> ';
    s = s + feedb_correct_S+'</div> <br /><div id="OK_btn" style="display:none;">';
    s = s + '<input type="button" onclick="display_quest();" value="OK"/></div> </div>';
    document.getElementById("dicecont").innerHTML = s;
    document.getElementById("question_area").style.display = "block";
    document.getElementById("check_btn").style.display = "block";
}

function move_pawn() {
    var pawn_x =0;
    var pawn_y=0;
    var direction=0;
    var cols = board_cols[current_board];
    var rows = board_rows[current_board];
    var col_width = (board_width[current_board]-board_headerx[current_board]-board_footerx[current_board])/cols;
    var col_height = (board_height[current_board]-board_headery[current_board]-board_footery[current_board])/rows;
    
    if( current_position  >= 0) {
        direction=Math.floor((current_position /cols))%2;
        if (direction == 1) {
            axis_x=(cols-(current_position %cols)-1);
        } else {
            axis_x=current_position %cols;
        }

        axis_y=Math.floor((current_position /rows));
        pawn_x=board_headerx[current_board]+(axis_x*col_width)+(col_width-pawn_width[current_board])/2;
        pawn_y = board_footery[current_board]+pawn_height[current_board]+(axis_y*col_height);
        pawn_y += (col_height-pawn_height[current_board])/2;

        document.getElementById("pawn1").style.position='relative';
        document.getElementById("pawn1").style.left=pawn_x+'px';
        document.getElementById("pawn1").style.bottom=pawn_y+'px';
    }
}

    function Base64decode(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
        var keyStr="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
 
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {
            enc1 = keyStr.indexOf(input.charAt(i++));
            enc2 = keyStr.indexOf(input.charAt(i++));
            enc3 = keyStr.indexOf(input.charAt(i++));
            enc4 = keyStr.indexOf(input.charAt(i++));

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

        output = Base64_utf8_decode(output);

        return output;

    };

    // Private method for UTF-8 decoding.
    function Base64_utf8_decode(utftext) {
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
</script>

</body>
</html>

<?php
/**
 * Javascript code
 */
function createsubmodaljs() {
?>
<script type="text/javascript" src="js/common.js">
var gPopupMask = null;
var gPopupContainer = null;
var gPopFrame = null;
var gReturnFunc;
var gPopupIsShown = false;
var gDefaultPage = "/loading.html";
var gHideSelects = false;
var gReturnVal = null;

var gTabIndexes = new Array();
// Pre-defined list of tags we want to disable/enable tabbing into
var gTabbableTags = new Array("A","BUTTON","TEXTAREA","INPUT","IFRAME");	

// If using Mozilla or Firefox, use Tab-key trap.
if (!document.all) {
    document.onkeypress = keyDownHandler;
}

/**
 * Initializes popup code on load.
 */
function initPopUp() {
    // Add the HTML to the body
    theBody = document.getElementsByTagName('BODY')[0];
    popmask = document.createElement('div');
    popmask.id = 'popupMask';
    popcont = document.createElement('div');
    popcont.id = 'popupContainer';
    popcont.innerHTML = '' +
        '<div id="popupInner">' +
        '<div id="popupTitleBar">' +
        '<div id="popupTitle"></div>' +
        '<div id="popupControls">' +
        '<img src="close.gif" onclick="hidePopWin(false);" id="popCloseBox" />' +
        '</div>' +
        '</div>' +
        '<iframe src="'+ gDefaultPage +'" style="width:100%;height:100%;background-color:transparent;" ' +
        ' scrolling="auto" frameborder="0" allowtransparency="true" id="popupFrame" name="popupFrame" ' + 
        ' width="100%" height="100%"></iframe>' +
        '</div>';
    theBody.appendChild(popmask);
    theBody.appendChild(popcont);

    gPopupMask = document.getElementById("popupMask");
    gPopupContainer = document.getElementById("popupContainer");
    gPopFrame = document.getElementById("popupFrame");

    // check to see if this is IE version 6 or lower. hide select boxes if so
    // maybe they'll fix this in version 7?
    var brsVersion = parseInt(window.navigator.appVersion.charAt(0), 10);
    if (brsVersion <= 6 && window.navigator.userAgent.indexOf("MSIE") > -1) {
        gHideSelects = true;
    }

    // Add onclick handlers to 'a' elements of class submodal or submodal-width-height
    var elms = document.getElementsByTagName('a');
    for (i = 0; i < elms.length; i++) {
        if (elms[i].className.indexOf("submodal") == 0) { 
            elms[i].onclick = function(){
                // default width and height
                var width = 400;
                var height = 200;
                // Parse out optional width and height from className
                params = this.className.split('-');
                if (params.length == 3) {
                    width = parseInt(params[1]);
                    height = parseInt(params[2]);
                }
                showPopWin(this.href,width,height,null); return false;
            }
        }
    }
}
addEvent(window, "load", initPopUp);

/**
 * @argument width - int in pixels
 * @argument height - int in pixels
 * @argument url - url to display
 * @argument returnFunc - function to call when returning true from the window.
 * @argument showCloseBox - show the close box - default true
 */
function showPopWin(url, width, height, returnFunc, showCloseBox) {
    // show or hide the window close widget
    if (showCloseBox == null || showCloseBox == true) {
        document.getElementById("popCloseBox").style.display = "block";
    } else {
        document.getElementById("popCloseBox").style.display = "none";
    }
    gPopupIsShown = true;
    disableTabIndexes();
    gPopupMask.style.display = "block";
    gPopupContainer.style.display = "block";
    // calculate where to place the window on screen
    centerPopWin(width, height);

    var titleBarHeight = parseInt(document.getElementById("popupTitleBar").offsetHeight, 10);

    gPopupContainer.style.width = width + "px";
    gPopupContainer.style.height = (height+titleBarHeight) + "px";

    setMaskSize();

    // need to set the width of the iframe to the title bar width because of the dropshadow
    // some oddness was occuring and causing the frame to poke outside the border in IE6
    gPopFrame.style.width = parseInt(document.getElementById("popupTitleBar").offsetWidth, 10) + "px";
    gPopFrame.style.height = (height) + "px";

    // set the url
    gPopFrame.src = url;
   
    gReturnFunc = returnFunc;
    // for IE
    if (gHideSelects == true) {
        hideSelectBoxes();
    }
}

var gi = 0;
function centerPopWin(width, height) {
    if (gPopupIsShown == true) {
        if (width == null || isNaN(width)) {
            width = gPopupContainer.offsetWidth;
        }
        if (height == null) {
            height = gPopupContainer.offsetHeight;
        }

        var theBody = document.getElementsByTagName("BODY")[0];
        var scTop = parseInt(getScrollTop(),10);
        var scLeft = parseInt(theBody.scrollLeft,10);

        setMaskSize();

        var titleBarHeight = parseInt(document.getElementById("popupTitleBar").offsetHeight, 10);

        var fullHeight = getViewportHeight();
        var fullWidth = getViewportWidth();

        gPopupContainer.style.top = (scTop + ((fullHeight - (height+titleBarHeight)) / 2)) + "px";
        gPopupContainer.style.left =  (scLeft + ((fullWidth - width) / 2)) + "px";
    }
}
addEvent(window, "resize", centerPopWin);
addEvent(window, "scroll", centerPopWin);
window.onscroll = centerPopWin;

/**
 * Sets the size of the popup mask.
 *
 */
function setMaskSize() {
    var theBody = document.getElementsByTagName("BODY")[0];

    var fullHeight = getViewportHeight();
    var fullWidth = getViewportWidth();

    // Determine what's bigger, scrollHeight or fullHeight / width
    if (fullHeight > theBody.scrollHeight) {
        popHeight = fullHeight;
    } else {
        popHeight = theBody.scrollHeight;
    }

    if (fullWidth > theBody.scrollWidth) {
        popWidth = fullWidth;
    } else {
        popWidth = theBody.scrollWidth;
    }

    gPopupMask.style.height = popHeight + "px";
    gPopupMask.style.width = popWidth + "px";
}

/**
 * @argument callReturnFunc - bool - determines if we call the return function specified
 * @argument returnVal - anything - return value 
 */
function hidePopWin(callReturnFunc) {
    gPopupIsShown = false;
    var theBody = document.getElementsByTagName("BODY")[0];
    theBody.style.overflow = "";
    restoreTabIndexes();
    if (gPopupMask == null) {
        return;
    }
    gPopupMask.style.display = "none";
    gPopupContainer.style.display = "none";
    if (callReturnFunc == true && gReturnFunc != null) {
        // Set the return code to run in a timeout.
        // Was having issues using with an Ajax.Request();
        gReturnVal = window.frames["popupFrame"].returnVal;
        window.setTimeout('gReturnFunc(gReturnVal);', 1);
    }
    gPopFrame.src = gDefaultPage;
    // display all select boxes
    if (gHideSelects == true) {
        displaySelectBoxes();
    }
}

// Tab key trap. iff popup is shown and key was [TAB], suppress it.
// @argument e - event - keyboard event that caused this function to be called.
function keyDownHandler(e) {
    if (gPopupIsShown && e.keyCode == 9)  return false;
}

// For IE.  Go through predefined tags and disable tabbing into them.
function disableTabIndexes() {
    if (document.all) {
        var i = 0;
        for (var j = 0; j < gTabbableTags.length; j++) {
            var tagElements = document.getElementsByTagName(gTabbableTags[j]);
            for (var k = 0 ; k < tagElements.length; k++) {
                gTabIndexes[i] = tagElements[k].tabIndex;
                tagElements[k].tabIndex="-1";
                i++;
            }
        }
    }
}

function returnRefresh() 
{   
    window.location.reload(); 
} 

// For IE. Restore tab-indexes.
function restoreTabIndexes() {
    if (document.all) {
        var i = 0;
        for (var j = 0; j < gTabbableTags.length; j++) {
            var tagElements = document.getElementsByTagName(gTabbableTags[j]);
            for (var k = 0 ; k < tagElements.length; k++) {
                tagElements[k].tabIndex = gTabIndexes[i];
                tagElements[k].tabEnabled = true;
                i++;
            }
        }
    }
}


/**
 * Hides all drop down form select boxes on the screen so they do not appear above the mask layer.
 * IE has a problem with wanted select form tags to always be the topmost z-index or layer
 *
 * Thanks for the code Scott!
 */
function hideSelectBoxes() {
    var x = document.getElementsByTagName("SELECT");

    for (i=0;x && i < x.length; i++) {
        x[i].style.visibility = "hidden";
    }
}

/**
 * Makes all drop down form select boxes on the screen visible so they do not 
 * reappear after the dialog is closed.
 * 
 * IE has a problem with wanting select form tags to always be the 
 * topmost z-index or layer.
 */
function displaySelectBoxes() {
    var x = document.getElementsByTagName("SELECT");

    for (i=0;x && i < x.length; i++){
        x[i].style.visibility = "visible";
    }
}
</script>
<?php
}
