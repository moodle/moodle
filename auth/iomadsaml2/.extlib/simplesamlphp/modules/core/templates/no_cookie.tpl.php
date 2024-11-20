<?php

assert(array_key_exists('retryURL', $this->data));
$retryURL = $this->data['retryURL'];

$header = htmlspecialchars($this->t('{core:no_cookie:header}'));
$description = htmlspecialchars($this->t('{core:no_cookie:description}'));
$retry = htmlspecialchars($this->t('{core:no_cookie:retry}'));

$this->data['header'] = $header;
$this->includeAtTemplateBase('includes/header.php');

echo('<h2>'.$header.'</h2>');
echo('<p>'.$description.'</p>');

if ($retryURL !== null) {
    echo('<ul>');
    echo('<li><a href="'.htmlspecialchars($retryURL).'" id="retry">'.$retry.'</a></li>');
    echo('</ul>');
}

$this->includeAtTemplateBase('includes/footer.php');
