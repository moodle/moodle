<?php // $Id$
// QuizTimer main routines.
// This will produce a floating timer that counts
// how much time is left to answer the quiz.
//
    ?>
<script language="javascript" type="text/javascript">
<!--
function send_data() {
    document.forms[0].submit();
    return true;
}

var timesup = "<?php print_string("timesup","quiz");?>";
var quizclose = <?php echo ($quiz->timeclose - time()) - $timerstartvalue; ?>; // in seconds
var quizTimerValue = <?php echo $timerstartvalue; ?>; // in seconds
parseInt(quizTimerValue);
// -->
</script>
<script language="javascript" type="text/javascript" src="timer.js"></script>
<div id="timer" style="position: absolute; top: 100; left: 10;">
<!--EDIT BELOW CODE TO YOUR OWN MENU-->
<table class="generalbox" border="0" cellpadding="0" cellspacing="0" width="150">
<tr>
    <td class="generalboxcontent" bgcolor="#ffffff" width="100%">
    <table class="generaltable" border="0" width="150" cellspacing="0" cellpadding="0">
    <tr>
        <th class="generaltableheader" width="100%"><?php print_string("timeleft","quiz");?></th>
    </tr>
    <tr>
        <td id="QuizTimer" class="generaltablecell" align="center" width="100%">
        <form name="clock"><input onfocus="blur()" type="text" name="time"
        style="background-color: transparent; border: none; width: 70%; font-family: sans-serif; font-size: 14pt; font-weight: bold; text-align: center;" />
        </form>
        </td>
    </tr>
    </table>
    </td>
</tr>
</table>
<!--END OF EDIT-->
</div>
<script language="javascript" type="text/javascript">
<!--
function changecolor(col) {
    // Changes the timers background color
    var d = document.getElementById('QuizTimer');
    d.style.backgroundColor = col;
}

var timerbox = getObjectById('timer');
var theTop = 100;
var old = theTop;
movecounter(this);

document.onload = countdown_clock();
// -->
</script>

