<script language="javascript" type="text/javascript">
<!--
document.write('<form action="attempt.php" method="get"' +
               ' onsubmit="return confirm(\'<?php echo $strconfirmstartattempt ?>\');">' +
               '<input type="hidden" name="id" value="<?php echo $cm->id ?>" />' +
               '<input type="submit" value="<?php print_string("attemptquiznow","quiz"); ?>" /></form>');
// -->
</script>
<noscript><strong><?php print_string("noscript","quiz"); ?></strong></noscript>