<?php

$this->data['header'] = $this->t('{core:frontpage:page_title}');
$this->includeAtTemplateBase('includes/header.php');

?>

<?php
if ($this->data['isadmin']) {
    echo '<p class="float-r youareadmin">'.$this->t('{core:frontpage:loggedin_as_admin}').'</p>';
} else {
    echo '<p class="float-r youareadmin"><a href="'.$this->data['loginurl'].'">'.
        $this->t('{core:frontpage:login_as_admin}').'</a></p>';
}
?>

<!-- <h2><?php echo $this->t('{core:frontpage:useful_links_header}'); ?></h2> -->
<ul>
<?php
foreach ($this->data['links_auth'] as $link) {
    echo '<li><a href="'.htmlspecialchars($link['href']).'">'.$this->t($link['text']).'</a>';
    if (isset($link['deprecated']) && $link['deprecated']) {
        echo ' <b>'.$this->t('{core:frontpage:deprecated}').'</b>';
    }
    echo '</li>';
}
?>
</ul>

<?php $this->includeAtTemplateBase('includes/footer.php');
