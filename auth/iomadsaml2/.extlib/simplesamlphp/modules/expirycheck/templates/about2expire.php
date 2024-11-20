<?php

/**
 * Template form for giving consent.
 *
 * Parameters:
 * - 'srcMetadata': Metadata/configuration for the source.
 * - 'dstMetadata': Metadata/configuration for the destination.
 * - 'yesTarget': Target URL for the yes-button. This URL will receive a POST request.
 * - 'yesData': Parameters which should be included in the yes-request.
 * - 'noTarget': Target URL for the no-button. This URL will receive a GET request.
 * - 'noData': Parameters which should be included in the no-request.
 * - 'attributes': The attributes which are about to be released.
 * - 'sppp': URL to the privacy policy of the destination, or FALSE.
 *
 * @package SimpleSAMLphp
 */

$this->data['autofocus'] = 'yesbutton';
$this->includeAtTemplateBase('includes/header.php');

$yesTarget = htmlspecialchars($this->data['yesTarget']);
$buttonContinue = htmlspecialchars($this->t('{expirycheck:expwarning:btn_continue}'));

echo '<form style="display: inline; margin: 0px; padding: 0px" action="'.$yesTarget.'">';
// Embed hidden fields...
foreach ($this->data['yesData'] as $name => $value) {
    echo '<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" />';
}
?>

<h3><?php echo $this->data['warning']; ?></h3>
<p><?php echo $this->t('{expirycheck:expwarning:expiry_date_text}')." ".$this->data['expireOnDate']; ?></p>
<input type="submit" name="yes" id="yesbutton" value="<?php echo $buttonContinue; ?>" />
</form>

<?php

$this->includeAtTemplateBase('includes/footer.php');
