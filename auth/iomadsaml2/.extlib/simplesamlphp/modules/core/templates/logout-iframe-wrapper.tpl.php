<?php

$id = $this->data['auth_state'];
$SPs = $this->data['SPs'];

$iframeURL = 'logout-iframe.php?type=embed&id='.urlencode($id);

// pretty arbitrary height, but should have enough safety margins for most cases
$iframeHeight = 25 + count($SPs) * 4;

$this->data['header'] = $this->t('{logout:progress}');
$this->includeAtTemplateBase('includes/header.php');
echo '<iframe style="width:100%; height:'.$iframeHeight.'em; border:0;" src="'.
    htmlspecialchars($iframeURL).'"></iframe>';

foreach ($SPs as $assocId => $sp) {
    $spId = sha1($assocId);

    if ($sp['core:Logout-IFrame:State'] !== 'inprogress') {
        continue;
    }
    assert(isset($sp['core:Logout-IFrame:URL']));

    $url = $sp["core:Logout-IFrame:URL"];

    echo '<iframe style="width:0; height:0; border:0;" src="'.htmlspecialchars($url).'"></iframe>';
}

$this->includeAtTemplateBase('includes/footer.php');
