<?php

$header = htmlspecialchars($this->t('{saml:wrong_authncontextclassref:header}'));
$description = htmlspecialchars($this->t('{saml:wrong_authncontextclassref:description}'));
$retry = htmlspecialchars($this->t('{saml:wrong_authncontextclassref:retry}'));

$this->data['header'] = $header;
$this->includeAtTemplateBase('includes/header.php');

echo('<h2>' . $header . '</h2>');
echo('<p>' . $description . '</p>');

$this->includeAtTemplateBase('includes/footer.php');
