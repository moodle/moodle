<?php
defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

$window = (!empty($quiz->popup)) ? 'quizpopup' : '_self';
$windowoptions = ($window == '_self') ? '' : "left=0, top=0, height='+window.screen.height+', width='+window.screen.width+', channelmode=yes, fullscreen=yes, scrollbars=yes, resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, menubar=no";
$buttontext = ($numattempts) ? get_string('reattemptquiz', 'quiz') : get_string('attemptquiznow', 'quiz');

?>

<script language="javascript" type="text/javascript">
<!--
document.write('<input type="button" value="<?php echo $buttontext ?>" '+
               'onclick="javascript: <?php if ($quiz->timelimit) echo "if (confirm(\\'$strconfirmstartattempt\\'))"; ?> '+
               'window.open(\'attempt.php?id=<?php echo $cm->id ?>\', \'<?php echo $window ?>\', \'<?php echo $windowoptions ?>\'); " />');
// -->
</script>
<noscript>
    <strong><?php print_string('noscript', 'quiz'); ?></strong>
</noscript>
