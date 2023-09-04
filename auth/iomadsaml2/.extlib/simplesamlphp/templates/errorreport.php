<?php
    $this->data['header'] = $this->t('errorreport_header');
    $this->includeAtTemplateBase('includes/header.php');
?>

<h2><?php echo $this->t('errorreport_header'); ?></h2>
<p><?php echo $this->t('errorreport_text'); ?></p>
<?php
    $this->includeAtTemplateBase('includes/footer.php');
