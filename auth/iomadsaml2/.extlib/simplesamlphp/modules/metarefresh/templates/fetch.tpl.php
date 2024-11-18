<?php
$this->data['header'] = $this->t('{metarefresh:metarefresh:metarefresh_header}');
$this->includeAtTemplateBase('includes/header.php');

echo '<h1>'.$this->data['header'].'</h1>';

if (!empty($this->data['logentries'])) {
    echo '<pre style="border: 1px solid #aaa; padding: .5em; overflow: scroll">';
    foreach ($this->data['logentries'] as $l) {
        echo $l."\n";
    }
    echo '</pre>';
} else {
    echo $this->t('{metarefresh:metarefresh:no_output}');
}

$this->includeAtTemplateBase('includes/footer.php');
