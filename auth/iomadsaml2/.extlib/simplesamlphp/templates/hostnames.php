<?php
$this->data['header'] = $this->t('{status:header_diagnostics}');
$this->includeAtTemplateBase('includes/header.php');
$this->includeAtTemplateBase('includes/attributes.php');

echo "<h2>".$this->t('{core:frontpage:link_diagnostics}')."</h2>";

$attributes = $this->data['attributes'];

echo(present_attributes($this, $attributes, ''));

$this->includeAtTemplateBase('includes/footer.php');
