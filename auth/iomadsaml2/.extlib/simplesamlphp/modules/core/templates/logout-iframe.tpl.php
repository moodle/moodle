<?php

$id = $this->data['auth_state'];
$type = $this->data['type'];
$from = $this->data['from'];
$SPs = $this->data['SPs'];

$stateImage = [
    'unsupported' => '/'.$this->data['baseurlpath'].'resources/icons/silk/delete.png',
    'completed'   => '/'.$this->data['baseurlpath'].'resources/icons/silk/accept.png',
    'onhold'      => '/'.$this->data['baseurlpath'].'resources/icons/bullet16_grey.png',
    'inprogress'  => '/'.$this->data['baseurlpath'].'resources/progress.gif',
    'failed'      => '/'.$this->data['baseurlpath'].'resources/icons/silk/exclamation.png',
];

$stateText = [
    'unsupported' => '',
    'completed'   => $this->t('{logout:completed}'),
    'onhold'      => '',
    'inprogress'  => $this->t('{logout:progress}'),
    'failed'      => $this->t('{logout:failed}'),
];

$spStatus = [];
$spTimeout = [];
$nFailed = 0;
$nProgress = 0;
foreach ($SPs as $assocId => $sp) {
    assert(isset($sp['core:Logout-IFrame:State']));
    $state = $sp['core:Logout-IFrame:State'];
    $spStatus[sha1($assocId)] = $state;
    if (isset($sp['core:Logout-IFrame:Timeout'])) {
        $spTimeout[sha1($assocId)] = $sp['core:Logout-IFrame:Timeout'] - time();
    } else {
        $spTimeout[sha1($assocId)] = 5;
    }
    if ($state === 'failed') {
        $nFailed += 1;
    } elseif ($state === 'inprogress') {
        $nProgress += 1;
    }
}

if ($from !== null) {
    $from = $this->getTranslator()->getPreferredTranslation($from);
}

if (!isset($this->data['head'])) {
    $this->data['head'] = '';
}

$this->data['head'] .= '
<script type="text/javascript" language="JavaScript">
window.stateImage = '.json_encode($stateImage).';
window.stateText = '.json_encode($stateText).';
window.spStatus = '.json_encode($spStatus).';
window.spTimeout = '.json_encode($spTimeout).';
window.type = "'.$type.'";
</script>';

$this->data['head'] .= '<script type="text/javascript" src="logout-iframe.js"></script>';

if ($type === 'embed') {
    $this->data['head'] .= '<meta http-equiv="refresh" content="1" />';
}

$this->data['header'] = $this->t('{logout:progress}');
if ($type === 'embed') {
    $this->includeAtTemplateBase('includes/header-embed.php');
} else {
    $this->includeAtTemplateBase('includes/header.php');
}
?>
<div id="wrap">
    <div id="content">
<?php
if ($from !== null) {
    echo '<div><img style="float: left; margin-right: 12px" src="/'.$this->data['baseurlpath'].
        'resources/icons/checkmark.48x48.png" alt="Successful logout" />';
    echo '<p style="padding-top: 16px; ">'.
        $this->t('{logout:loggedoutfrom}', ['%SP%' => '<strong>'.htmlspecialchars($from).'</strong>']).'</p>';
    echo '<p style="height: 0px; clear: left;"></p></div>';
}

echo '<div style="margin-top: 3em; clear: both">';

echo '<p style="margin-bottom: .5em">';
if ($type === 'init') {
    echo $this->t('{logout:also_from}');
} else {
    echo $this->t('{logout:logging_out_from}');
}
?>
    </p>
    <table id="slostatustable">
<?php
foreach ($SPs as $assocId => $sp) {
    if (isset($sp['core:Logout-IFrame:Name'])) {
        $spName = $this->getTranslator()->getPreferredTranslation($sp['core:Logout-IFrame:Name']);
    } else {
        $spName = $assocId;
    }

    assert(isset($sp['core:Logout-IFrame:State']));
    $spState = $sp['core:Logout-IFrame:State'];

    $spId = sha1($assocId);

    echo '<tr>';
    echo '<td style="width: 3em;"></td>';
    echo '<td>';
    echo '<img class="logoutstatusimage" id="statusimage-'.$spId.'"  src="'.htmlspecialchars($stateImage[$spState]).
        '" alt="'.htmlspecialchars($stateText[$spState]).'"/>';
    echo '</td>';
    echo '<td>'.htmlspecialchars($spName).'</td>';
    echo '</tr>';
}

if (isset($from)) {
    $logoutCancelText = $this->t('{logout:logout_only}', ['%SP%' => htmlspecialchars($from)]);
} else {
    $logoutCancelText = $this->t('{logout:no}');
}

?>
    </table>
</div>

<?php
if ($type === 'init') {
?>
<div id="confirmation" style="margin-top: 1em">
    <p><?php echo $this->t('{logout:logout_all_question}'); ?> <br/></p>
    <form id="startform" method="get" style="display:inline;" action="logout-iframe.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
        <input type="hidden" id="logout-type-selector" name="type" value="nojs"/>
        <button type="submit" id="logout-all" name="ok" class="btn">
        <?php echo $this->t('{logout:logout_all}'); ?>
        </button>
    </form>
    <form method="get" style="display:inline;" action="logout-iframe-done.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
        <button type="submit" name="cancel" class="btn"><?php echo $logoutCancelText; ?></button>
    </form>
</div>
<?php
} else {
    if ($nFailed > 0) {
        $displayStyle = '';
    } else {
        $displayStyle = 'display: none;';
    }
    echo '<div id="logout-failed-message" style="margin-top: 1em; border: 1px solid #ccc; padding: 1em; '.
        'background: #eaeaea;'.$displayStyle.'">';
    echo '<img src="/'.$this->data['baseurlpath'].
        'resources/icons/experience/gtk-dialog-warning.48x48.png" alt="" '.
        'style="float: left; margin-right: 5px;" />';
    echo '<p>'.$this->t('{logout:failedsps}').'</p>';
    echo '<form method="post" action="logout-iframe-done.php" id="failed-form" target="_top">';
    echo '<input type="hidden" name="id" value="'.$id.'" />';
    echo '<button type="submit" name="continue" class="btn">'.$this->t('{logout:return}').'</button>';
    echo '</form>';
    echo '</div>';

    if ($nProgress == 0 && $nFailed == 0) {
        echo '<div id="logout-completed">';
    } else {
        echo '<div id="logout-completed" style="display:none;">';
    }
    echo '<p>'.$this->t('{logout:success}').'</p>';
?>
    <form method="post" action="logout-iframe-done.php" id="done-form" target="_top">
        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
        <button type="submit" name="continue" class="btn"><?php echo $this->t('{logout:return}'); ?></button>
    </form>
</div>
<?php
    if ($type === 'js') {
        foreach ($SPs as $assocId => $sp) {
            $spId = sha1($assocId);
            if ($sp['core:Logout-IFrame:State'] !== 'inprogress') {
                continue;
            }
            assert(isset($sp['core:Logout-IFrame:URL']));
            echo '<iframe style="width:0; height:0; border:0;" src="'.
                htmlspecialchars($sp['core:Logout-IFrame:URL']).'"></iframe>';
        }
    }
}
?>
        </div><!-- #content -->
    </div><!-- #wrap -->
<?php
if ($type === 'embed') {
    $this->includeAtTemplateBase('includes/footer-embed.php');
} else {
    $this->includeAtTemplateBase('includes/footer.php');
}
