<?php

$this->data['head'] = '<link rel="stylesheet" type="text/css" href="/'.
    $this->data['baseurlpath'].'module.php/oauth/assets/css/oauth.css" />'."\n";
$this->data['head'] = '<link rel="stylesheet" type="text/css" href="/'.
    $this->data['baseurlpath'].'module.php/oauth/assets/css/uitheme1.12.1/jquery-ui.min.css" />'."\n";
$this->data['head'] .= '<script type="text/javascript" src="/'.
    $this->data['baseurlpath'].'module.php/oauth/assets/js/oauth.js"></script>';

$this->includeAtTemplateBase('includes/header.php');

$this->data['htmlinject']['htmlContentPost'][] = '<script type="text/javascript" src="'.
    SimpleSAML\Module::getModuleURL('discopower/assets/js/jquery-1.12.4.min.js').'"></script>'."\n";
$this->data['htmlinject']['htmlContentPost'][] = '<script type="text/javascript" src="'.
    SimpleSAML\Module::getModuleURL('discopower/assets/js/jquery-ui-1.12.1.min.js').'"></script>'."\n";

echo '<h1>OAuth Client</h1>';

echo $this->data['form'];

echo '<p style="float: right"><a href="registry.php">'.
    'Return to entity listing <strong>without saving...</strong></a></p>';

$this->includeAtTemplateBase('includes/footer.php');
