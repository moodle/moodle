<?php defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');?>

<div align="center">
<?php 
if (!empty($quiz->popup)) {
?>

<script language="javascript" type="text/javascript">
<!--

document.write('<input type="button" value="<?php print_string('continue') ?>" '+
               'onclick="javascript: window.opener.location.href=\'../../course/view.php?id=<?php echo $course->id ?>\'; '+
               'window.close();" />');
// -->
</script>
<noscript>
<?php print_string('closewindow'); ?>
</noscript>

<?php
} else {
    print_single_button("../../course/view.php", array( 'id' => $course->id ), get_string('continue'));
}
?>
</div>
