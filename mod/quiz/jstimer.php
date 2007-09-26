<?php // $Id$
// QuizTimer main routines.
// This will produce a floating timer that counts
// how much time is left to answer the quiz.
//
defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');
?>

<script type="text/javascript">
//<![CDATA[
var timesup = "<?php print_string("timesup","quiz");?>";
var quizclose = <?php echo ($quiz->timeclose - time()) - $timerstartvalue; ?>; // in seconds
var quizTimerValue = <?php echo $timerstartvalue; ?>; // in seconds
parseInt(quizTimerValue);

// @EC PF : client time when page was opened
var ec_page_start = new Date().getTime();
// @EC PF : client time when quiz should end
var ec_quiz_finish = ec_page_start + <?php echo ($timerstartvalue * 1000); ?>;

//]]>
</script>
<div id="timer">
<!--EDIT BELOW CODE TO YOUR OWN MENU-->
<table class="generalbox" border="0" cellpadding="0" cellspacing="0" style="width:150px;">
<tr>
    <td class="generalboxcontent" bgcolor="#ffffff" width="100%">
    <table class="generaltable" border="0" width="150" cellspacing="0" cellpadding="0">
    <tr>
        <th class="generaltableheader" width="100%" scope="col"><?php print_string("timeleft","quiz");?></th>
    </tr>
    <tr>
        <td id="QuizTimer" class="generaltablecell" align="center" width="100%">
        <form id="clock"><div><input onfocus="blur()" type="text" id="time"
        style="background-color: transparent; border: none; width: 70%; font-family: sans-serif; font-size: 14pt; font-weight: bold; text-align: center;" />
        </div>
        </form>
        </td>
    </tr>
    </table>
    </td>
</tr>
</table>
<!--END OF EDIT-->
</div>
<script type="text/javascript">
//<![CDATA[

var timerbox = document.getElementById('timer');
var theTimer = document.getElementById('QuizTimer');
var theTop = 100;
var old = theTop;

movecounter(timerbox);

document.onload = countdown_clock(theTimer);
//]]>
</script>
