<?php
$this->data['header'] = 'Sanity check';
$this->includeAtTemplateBase('includes/header.php');

echo '<h2>'.$this->data['header'].'</h2>';
if (count($this->data['errors']) > 0) {
    echo '<div style="border: 1px solid #800; background: #caa; margin: 1em; padding: .5em">';
    echo '<p><img class="float-r" src="/'.$this->data['baseurlpath'].
        'resources/icons/silk/delete.png" alt="Failed" />These checks failed:</p>';
    echo '<ul>';
    foreach ($this->data['errors'] as $err) {
        echo '<li>'.$err.'</li>';
    }
    echo '</ul></div>';
}

if (count($this->data['info']) > 0) {
    echo '<div style="border: 1px solid #ccc; background: #eee; margin: 1em; padding: .5em">';
    echo '<p><img class="float-r" src="/'.$this->data['baseurlpath'].
        'resources/icons/silk/accept.png" alt="OK" />These checks succeeded:</p>';
    echo '<ul>';
    foreach ($this->data['info'] as $i) {
        echo '<li>'.$i.'</li>';
    }
}
echo '</ul></div>';
$this->includeAtTemplateBase('includes/footer.php');
