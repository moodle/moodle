<?php

$this->data['header'] = $this->t('cron_header');
$this->includeAtTemplateBase('includes/header.php');
?>
        <p><?php echo $this->t('cron_result_title') ?></p>
        <pre style="color: #444; padding: 1em; border: 1px solid #eee; margin: .4em "><code>
<?php

echo '            <h1>'.$this->t('cron_report_title').'</h1><p>'.$this->t('ran_text').
    ' '.$this->data['time'].'</p>'.'<p>URL: <code>'.$this->data['url'].'</code></p>'.
    '<p>Tag: '.$this->data['tag']."</p>\n\n".
    '<ul><li>'.join('</li><li>', $this->data['summary']).'</li></ul>';
?>
        </code></pre>
</div>

<?php
$this->includeAtTemplateBase('includes/footer.php');
