<?php
defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

$window = (!empty($quiz->popup)) ? 'quizpopup' : '_self';
$windowoptions = ($window == '_self') ? '' : "left=0, top=0, height='+window.screen.height+', width='+window.screen.width+', channelmode=yes, fullscreen=yes, scrollbars=yes, resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, menubar=no";

?>

<script language="javascript" type="text/javascript">
<!--
document.write('<input type="button" value="<?php print_string('attemptquiznow', 'quiz') ?>" '+
               'onclick="javascript: <?php if ($quiz->timelimit) echo "if (confirm(\\'$strconfirmstartattempt\\'))"; ?> '+
               'window.open(\'attempt.php?id=<?php echo $cm->id ?>\', \'<?php echo $window ?>\', \'<?php echo $windowoptions ?>\'); " />');
// -->
</script>
<noscript>
    <strong><?php print_string('noscript', 'quiz'); ?></strong>
</noscript>

