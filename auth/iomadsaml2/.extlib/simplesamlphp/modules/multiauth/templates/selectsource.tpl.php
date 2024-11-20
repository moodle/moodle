<?php
$this->data['header'] = $this->t('{multiauth:multiauth:select_source_header}');
$this->includeAtTemplateBase('includes/header.php');
?>

<h2><?php echo $this->t('{multiauth:multiauth:select_source_header}'); ?></h2>

<p><?php echo $this->t('{multiauth:multiauth:select_source_text}'); ?></p>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="get">
    <input type="hidden" name="AuthState" value="<?php echo htmlspecialchars($this->data['authstate']); ?>" />
    <ul>
<?php
foreach ($this->data['sources'] as $source) {
    echo '<li class="'.htmlspecialchars($source['css_class']).' authsource">';
    if ($source['source'] === $this->data['preferred']) {
        $autofocus = ' autofocus="autofocus"';
    } else {
        $autofocus = '';
    }
    $name = 'src-'.base64_encode($source['source']);
    echo '<input type="submit" name="'.htmlspecialchars($name).'"'.$autofocus.' '.
        'id="button-'.htmlspecialchars($source['source']).'" '.
        'value="'.htmlspecialchars($source['text']).'" />';
    echo '</li>';
}
?>
    </ul>
</form>
<?php $this->includeAtTemplateBase('includes/footer.php');
