<?php

$this->data['header'] = $this->t('{consent:consent:noconsent_title}');

$this->includeAtTemplateBase('includes/header.php');

echo '<h2>'.$this->data['header'].'</h2>';
echo '<p>'.$this->data['noconsent_text'].'</p>';

if ($this->data['resumeFrom']) {
    echo('<p><a href="'.htmlspecialchars($this->data['resumeFrom']).'">');
    echo($this->t('{consent:consent:noconsent_return}'));
    echo('</a></p>');
}

if ($this->data['aboutService']) {
    echo('<p><a href="'.htmlspecialchars($this->data['aboutService']).'">');
    echo($this->t('{consent:consent:noconsent_goto_about}'));
    echo('</a></p>');
}

echo('<p><a href="'.htmlspecialchars($this->data['logoutLink']).'">'.$this->data['noconsent_abort'].'</a></p>');

$this->includeAtTemplateBase('includes/footer.php');
