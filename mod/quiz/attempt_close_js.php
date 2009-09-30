<?php defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');?>

<div class="controls">
<?php
if (!empty($popup) && $popup == 1) {
?>

<script type="text/javascript">
//<![CDATA[

document.write('<input type="button" value="<?php print_string('closewindow') ?>" '+
               'onclick="javascript: window.opener.location.href=\'view.php?id=<?php echo $cm->id ?>\'; '+
               'window.close();" />');
//]]>
</script>
<noscript>
<div>
<?php print_string('closewindow'); ?>
</div>
</noscript>

<?php
} else {
    print_single_button("view.php", array( 'id' => $cm->id ), get_string('finishreview', 'quiz'));
}
?>
</div>
