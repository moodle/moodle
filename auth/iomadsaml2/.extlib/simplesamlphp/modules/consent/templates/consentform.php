<?php

/**
 * Template form for giving consent.
 *
 * Parameters:
 * - 'yesTarget': Target URL for the yes-button. This URL will receive a POST request.
 * - 'noTarget': Target URL for the no-button. This URL will receive a GET request.
 * - 'sppp': URL to the privacy policy of the destination, or FALSE.
 *
 * @package SimpleSAMLphp
 */

assert(is_string($this->data['yesTarget']));
assert(is_string($this->data['noTarget']));
assert($this->data['sppp'] === false || is_string($this->data['sppp']));

// Parse parameters
$dstName = $this->data['dstName'];
$srcName = $this->data['srcName'];

$this->data['header'] = $this->t('{consent:consent:consent_header}');
$this->data['head'] = '<link rel="stylesheet" type="text/css" href="'.
    SimpleSAML\Module::getModuleURL("consent/assets/css/consent.css").'" />'."\n";

$this->includeAtTemplateBase('includes/header.php');
?>
<p><?php echo $this->data['consent_accept']; ?></p>

<?php
if (isset($this->data['consent_purpose'])) {
    echo '<p>'.$this->data['consent_purpose'].'</p>';
}
?>

<form id="consent_yes" action="<?php echo htmlspecialchars($this->data['yesTarget']); ?>">
<?php
if ($this->data['usestorage']) {
    $checked = ($this->data['checked'] ? 'checked="checked"' : '');
    echo '<input type="checkbox" name="saveconsent" '.$checked.
        ' value="1" /> '.$this->t('{consent:consent:remember}');
} // Embed hidden fields...
?>
    <input type="hidden" name="StateId" value="<?php echo htmlspecialchars($this->data['stateId']); ?>" />
    <button type="submit" name="yes" class="btn" id="yesbutton">
        <?php echo htmlspecialchars($this->t('{consent:consent:yes}')) ?>
    </button>
</form>

<form id="consent_no" action="<?php echo htmlspecialchars($this->data['noTarget']); ?>">
    <input type="hidden" name="StateId" value="<?php echo htmlspecialchars($this->data['stateId']); ?>" />
    <button type="submit" class="btn" name="no" id="nobutton">
        <?php echo htmlspecialchars($this->t('{consent:consent:no}')) ?>
    </button>
</form>

<?php
if ($this->data['sppp'] !== false) {
    echo "<p>".htmlspecialchars($this->t('{consent:consent:consent_privacypolicy}'))." ";
    echo '<a target="_blank" href="'.htmlspecialchars($this->data['sppp']).'">'.$dstName."</a>";
    echo "</p>";
}

echo '<h3 id="attributeheader">'.$this->data['consent_attributes_header'].'</h3>';

echo $this->data['attributes_html'];

$this->includeAtTemplateBase('includes/footer.php');
