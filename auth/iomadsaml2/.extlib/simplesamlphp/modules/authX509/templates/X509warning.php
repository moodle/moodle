<?php

/**
 * Template form for X509 warnings.
 *
 * Parameters:
 * - 'target': Target URL for the continue-button.
 * - 'data': Parameters which should be included in the request.
 *
 * @package SimpleSAMLphp
 */

$warning = $this->t('{authX509:X509warning:warning}', [
    '%daysleft%' => htmlspecialchars($this->data['daysleft']),
]);

if ($this->data['renewurl']) {
    $warning .= " ".$this->t('{authX509:X509warning:renew_url}', [
        '%renewurl%' => $this->data['renewurl'],
        ]);
} else {
    $warning .= " ".$this->t('{authX509:X509warning:renew}');
}

$this->data['header'] = $this->t('{authX509:X509warning:warning_header}');
$this->data['autofocus'] = 'proceedbutton';

$this->includeAtTemplateBase('includes/header.php');

?>

<form style="display: inline; margin: 0px; padding: 0px" action="<?php echo htmlspecialchars($this->data['target']); ?>">

    <?php
    // Embed hidden fields...
    foreach ($this->data['data'] as $name => $value) {
        echo '<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" />';
    }
    ?>
    <p><?php echo $warning; ?></p>

    <input type="submit" name="proceed" id="proceedbutton" value="<?php echo htmlspecialchars($this->t('{authX509:X509warning:proceed}')) ?>" />

</form>


<?php
$this->includeAtTemplateBase('includes/footer.php');
