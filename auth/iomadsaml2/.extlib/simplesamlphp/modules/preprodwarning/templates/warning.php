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

$this->data['header'] = $this->t('{preprodwarning:warning:warning_header}');
$this->data['autofocus'] = 'yesbutton';

$this->includeAtTemplateBase('includes/header.php');
$yesTarget = htmlspecialchars($this->data['yesTarget']);
$yesWarning = htmlspecialchars($this->t('{preprodwarning:warning:yes}'));
$warning = $this->t('{preprodwarning:warning:warning}');
echo '<form style="display: inline; margin: 0px; padding: 0px" action="'.$yesTarget.'">';

// Embed hidden fields...
foreach ($this->data['yesData'] as $name => $value) {
    echo '<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" />';
}
echo '<p>'.$warning.'</p>';
echo '<input type="submit" name="yes" id="yesbutton" value="'.$yesWarning.'" />';
echo '</form>';

$this->includeAtTemplateBase('includes/footer.php');
