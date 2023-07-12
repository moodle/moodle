<?php

$this->data['header'] = $this->t('cron_header');
$this->includeAtTemplateBase('includes/header.php');

$run_text = $this->t('run_text');
?>

        <p><?php echo $this->t('cron_info') ?></p>

        <p><?php echo $this->t('cron_suggestion') ?></p>
        <pre style="font-size: x-small; color: #444; padding: 1em; border: 1px solid #eee; margin: .4em "><code>
<?php
foreach ($this->data['urls'] as $url) {
    echo "# ".$run_text. ' ['.$url['tag'].']'."\n";
    echo $url['int']." curl --silent \"".$url['exec_href']."\" > /dev/null 2>&1\n";
}
?>
        </code></pre>

        <br />
        <p><?php echo $this->t('cron_execution') ?></p>
        <ul>
<?php
foreach ($this->data['urls'] as $url) {
    echo '        <li><a href="'.$url['href'].'">'.$run_text.' ['.$url['tag'].']'.'</a></li>';
}
?>
        </ul>
</div>

<?php
$this->includeAtTemplateBase('includes/footer.php');
