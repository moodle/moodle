<?php
$this->data['header'] = $this->t('{core:frontpage:page_title}');
$this->includeAtTemplateBase('includes/header.php');

if ($this->data['isadmin']) {
    echo '<p class="float-r youareadmin">'.$this->t('{core:frontpage:loggedin_as_admin}').'</p>';
} else {
    echo '<p class="float-r youareadmin"><a href="'.$this->data['loginurl'].'">'.
        $this->t('{core:frontpage:login_as_admin}').'</a></p>';
}
?>

<p><?php echo $this->t('{core:frontpage:intro}'); ?></p>

<ul>
<?php
foreach ($this->data['links_welcome'] as $link) {
    echo '<li><a href="'. htmlspecialchars($link['href']).'">'.$this->t($link['text']).'</a></li>';
}
?>
</ul>

<?php $this->includeAtTemplateBase('includes/footer.php');
