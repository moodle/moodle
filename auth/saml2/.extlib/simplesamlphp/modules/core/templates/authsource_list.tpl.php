<?php
/**
 * Template to show list of configured authentication sources.
 *
 */
$this->data['header'] = 'Test authentication sources';
$this->includeAtTemplateBase('includes/header.php');
?>
<h1><?php echo $this->data['header']; ?></h1>
<ul>
<?php
foreach ($this->data['sources'] as $id) {
    echo '<li><a href="?as='.htmlspecialchars(urlencode($id)).'">'.htmlspecialchars($id).'</a></li>';
}
?>
</ul>

<?php
$this->includeAtTemplateBase('includes/footer.php');
