<?php
/**
 * Template which is shown when there is only a short interval since the user was last authenticated.
 *
 * Parameters:
 * - 'target': Target URL.
 * - 'params': Parameters which should be included in the request.
 *
 * @package SimpleSAMLphp
 */


$this->data['header'] = $this->t('{core:short_sso_interval:warning_header}');
$this->data['autofocus'] = 'contbutton';

$this->includeAtTemplateBase('includes/header.php');
$target = htmlspecialchars($this->data['target']);
$contButton = htmlspecialchars($this->t('{core:short_sso_interval:retry}'));
?>
<h1><?php echo $this->data['header']; ?></h1>
<form style="display: inline; margin: 0px; padding: 0px" action="<?php echo $target; ?>">

<?php
// Embed hidden fields...
foreach ($this->data['params'] as $name => $value) {
    echo '<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" />';
}
?>
    <p><?php echo $this->t('{core:short_sso_interval:warning}'); ?></p>
    <div class="trackidtext"><p>
        <?php echo $this->t('{errors:report_trackid}'); ?>
        <span class="trackid"><?php echo $this->data['trackId']; ?></span>
        </p>
    </div>

    <input type="submit" name="continue" id="contbutton" value="<?php echo $contButton; ?>" />

</form>

<?php
$this->includeAtTemplateBase('includes/footer.php');
