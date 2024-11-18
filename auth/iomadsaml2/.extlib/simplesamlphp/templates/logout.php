<?php

$this->data['header'] = $this->t('{logout:title}');

$this->includeAtTemplateBase('includes/header.php');

echo '<h2>'.$this->data['header'].'</h2>';
echo '<p>'.$this->t('{logout:logged_out_text}').'</p>';

if ($this->getTranslator()->getTag($this->data['text']) !== null) {
    $this->data['text'] = $this->t($this->data['text']);
}
echo '<p>[ <a href="'.htmlspecialchars($this->data['link']).'">'.
    htmlspecialchars($this->data['text']).'</a> ]</p>';

$this->includeAtTemplateBase('includes/footer.php');
