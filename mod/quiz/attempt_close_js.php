<?php defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');?>

<div align="center">
<?php 
if (!empty($quiz->popup)) {
?>

<script language="javascript" type="text/javascript">
<!--

document.write('<input type="button" value="<?php print_string('continue') ?>" '+
               'onclick="javascript: window.opener.location.href=\'view.php?id=<?php echo $cm->id ?>\'; '+
               'window.close();" />');
// -->
</script>
<noscript>
<?php print_string('closewindow'); ?>
</noscript>

<?php
} else {
    print_single_button("view.php", array( 'id' => $cm->id ), get_string('continue'));
}
?>
</div>
