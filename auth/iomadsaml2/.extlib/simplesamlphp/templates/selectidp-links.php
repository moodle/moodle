<?php

if (!array_key_exists('header', $this->data)) {
    $this->data['header'] = 'selectidp';
}
$this->data['header'] = $this->t($this->data['header']);
$this->data['autofocus'] = 'preferredidp';
$this->includeAtTemplateBase('includes/header.php');
foreach ($this->data['idplist'] as $idpentry) {
    if (isset($idpentry['name'])) {
        $this->getTranslator()->includeInlineTranslation('idpname_'.$idpentry['entityid'], $idpentry['name']);
    } elseif (isset($idpentry['OrganizationDisplayName'])) {
        $this->getTranslator()->includeInlineTranslation(
            'idpname_'.$idpentry['entityid'],
            $idpentry['OrganizationDisplayName']
        );
    }
    if (isset($idpentry['description'])) {
        $this->getTranslator()->includeInlineTranslation('idpdesc_'.$idpentry['entityid'], $idpentry['description']);
    }
}
?>
    <h2><?php echo $this->data['header']; ?></h2>
    <form method="get" action="<?php echo $this->data['urlpattern']; ?>">
        <input type="hidden" name="entityID" value="<?php echo htmlspecialchars($this->data['entityID']); ?>"/>
        <input type="hidden" name="return" value="<?php echo htmlspecialchars($this->data['return']); ?>"/>
        <input type="hidden" name="returnIDParam"
               value="<?php echo htmlspecialchars($this->data['returnIDParam']); ?>"/>
        <p><?php
            echo $this->t('selectidp_full');
            if ($this->data['rememberenabled']) {
                echo('<br/><input type="checkbox" id="remember" name="remember" value="1"');
                if ($this->data['rememberchecked']) {
                    echo(' checked');
                }
                echo(' /><label for="remember">'.$this->t('remember').'</label>');
            }
            ?></p>
<?php
        usort($this->data['idplist'], function($idpentry1, $idpentry2) {
            return strcasecmp(
                $this->t('idpname_'.$idpentry1['entityid']),
                $this->t('idpname_'.$idpentry2['entityid'])
            );
        });

        if (!empty($this->data['preferredidp'])) {
            foreach ($this->data['idplist'] as $idpentry) {
                if ($idpentry['entityid'] != $this->data['preferredidp']) {
                    continue;
                }
                echo '<div class="preferredidp">';
                echo '	<img src="/'.$this->data['baseurlpath'].
                    'resources/icons/experience/gtk-about.64x64.png" class="float-r" alt="'.
                    $this->t('icon_prefered_idp').'" />';

                if (array_key_exists('icon', $idpentry) && $idpentry['icon'] !== null) {
                    $iconUrl = \SimpleSAML\Utils\HTTP::resolveURL($idpentry['icon']);
                    echo '<img class="float-l" style="margin: 1em; padding: 3px; border: 1px solid #999" src="'.
                        htmlspecialchars($iconUrl).'" />';
                }
                echo "\n".'	<h3 style="margin-top: 8px">'.
                    htmlspecialchars($this->t('idpname_'.$idpentry['entityid'])).'</h3>';

                if (!empty($idpentry['description'])) {
                    echo '	<p>'.htmlspecialchars($this->t('idpdesc_'.$idpentry['entityid'])).'<br />';
                }
                echo('<button id="preferredidp" type="submit" class="btn" name="idp_'.
                    htmlspecialchars($idpentry['entityid']).'">'.
                    $this->t('select').'</button></p>');
                echo '</div>';
            }
        }

        foreach ($this->data['idplist'] as $idpentry) {
            if ($idpentry['entityid'] != $this->data['preferredidp']) {
                if (array_key_exists('icon', $idpentry) && $idpentry['icon'] !== null) {
                    $iconUrl = \SimpleSAML\Utils\HTTP::resolveURL($idpentry['icon']);
                    echo '<img class="float-l" style="clear: both; margin: 1em; padding: 3px; border: 1px solid #999"'.
                        ' src="'.htmlspecialchars($iconUrl).'" />';
                }
                echo "\n".'	<h3 style="margin-top: 8px">'.htmlspecialchars($this->t('idpname_'.$idpentry['entityid']));
                echo '</h3>';

                if (!empty($idpentry['description'])) {
                    echo '	<p>'.htmlspecialchars($this->t('idpdesc_'.$idpentry['entityid'])).'<br />';
                }
                echo '<button type="submit" class="btn" name="idp_'.htmlspecialchars($idpentry['entityid']).'">'.
                    $this->t('select').'</button></p>';
            }
        }
?>
    </form>
<?php $this->includeAtTemplateBase('includes/footer.php');
