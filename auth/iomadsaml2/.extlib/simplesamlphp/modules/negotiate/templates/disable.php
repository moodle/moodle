<?php

/**
 *
 *
 * @author Mathias Meisfjordskar, University of Oslo.
 *         <mathias.meisfjordskar@usit.uio.no>
 * @package SimpleSAMLphp
 */

$this->includeAtTemplateBase('includes/header.php');
?>
<h1><?php echo $this->t('{negotiate:negotiate:disable_title}'); ?></h1>
<?php echo $this->t('{negotiate:negotiate:disable_info_pre}', ['URL' => htmlspecialchars($this->data['url'])]); ?>

<?php echo $this->t('{negotiate:negotiate:info_post}'); ?>

<?php $this->includeAtTemplateBase('includes/footer.php');
