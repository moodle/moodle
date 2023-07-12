<?php

$this->data['header'] = $this->t('{authX509:X509error:certificate_header}');

$this->includeAtTemplateBase('includes/header.php');

if ($this->data['errorcode'] !== null) {
?>
    <div style="border-left: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8; background: #f5f5f5">
        <img src="/<?php echo $this->data['baseurlpath']; ?>resources/icons/experience/gtk-dialog-error.48x48.png" class="float-l" style="margin: 15px" alt="" />
        <h2><?php echo $this->t('{login:error_header}'); ?></h2>
        <p><b><?php echo $this->t($this->data['errorcodes']['title'][$this->data['errorcode']]); ?></b></p>
        <p><?php echo $this->t($this->data['errorcodes']['descr'][$this->data['errorcode']]); ?></p>
    </div>
<?php
}
?>
    <h2 style="break: both"><?php echo $this->t('{authX509:X509error:certificate_header}'); ?></h2>

    <p><?php echo $this->t('{authX509:X509error:certificate_text}'); ?></p>

    <a href="<?php echo htmlspecialchars(\SimpleSAML\Utils\HTTP::getSelfURL()); ?>">
        <?php echo $this->t('{login:login_button}'); ?>
    </a>
<?php

if (!empty($this->data['links'])) {
    echo '<ul class="links" style="margin-top: 2em">';
    foreach ($this->data['links'] as $l) {
        echo '<li><a href="'.htmlspecialchars($l['href']).'">'.htmlspecialchars($this->t($l['text'])).'</a></li>';
    }
    echo '</ul>';
}

$this->includeAtTemplateBase('includes/footer.php');
