<?php

if (!array_key_exists('header', $this->data)) {
    $this->data['header'] = 'selectidp';
}
$this->data['header'] = $this->t($this->data['header']);
$this->data['autofocus'] = 'dropdownlist';
$this->includeAtTemplateBase('includes/header.php');

$translator = $this->getTranslator();
foreach ($this->data['idplist'] as $idpentry) {
    if (!empty($idpentry['name'])) {
        $translator->includeInlineTranslation(
            'idpname_'.$idpentry['entityid'],
            $idpentry['name']
        );
    } elseif (!empty($idpentry['OrganizationDisplayName'])) {
        $translator->includeInlineTranslation(
            'idpname_'.$idpentry['entityid'],
            $idpentry['OrganizationDisplayName']
        );
    }
    if (!empty($idpentry['description'])) {
        $translator->includeInlineTranslation('idpdesc_'.$idpentry['entityid'], $idpentry['description']);
    }
}
?>
    <h2><?php echo $this->data['header']; ?></h2>
    <p><?php echo $this->t('selectidp_full'); ?></p>
    <form method="get" action="<?php echo $this->data['urlpattern']; ?>">
        <input type="hidden" name="entityID" value="<?php echo htmlspecialchars($this->data['entityID']); ?>"/>
        <input type="hidden" name="return" value="<?php echo htmlspecialchars($this->data['return']); ?>"/>
        <input type="hidden" name="returnIDParam"
               value="<?php echo htmlspecialchars($this->data['returnIDParam']); ?>"/>
        <select id="dropdownlist" name="idpentityid">
            <?php
            usort($this->data['idplist'], function($idpentry1, $idpentry2) {
                return strcasecmp(
                    $this->t('idpname_'.$idpentry1['entityid']),
                    $this->t('idpname_'.$idpentry2['entityid'])
                );
            });

            foreach ($this->data['idplist'] as $idpentry) {
                echo '<option value="'.htmlspecialchars($idpentry['entityid']).'"';
                if (isset($this->data['preferredidp']) && $idpentry['entityid'] == $this->data['preferredidp']) {
                    echo ' selected="selected"';
                }
                echo '>'.htmlspecialchars($this->t('idpname_'.$idpentry['entityid'])).'</option>';
            }
            ?>
        </select>
        <button class="btn" type="submit"><?php echo $this->t('select'); ?></button>
        <?php
        if ($this->data['rememberenabled']) {
            echo('<br/><input type="checkbox" id="remember" name="remember" value="1"');
            if ($this->data['rememberchecked']) {
                echo(' checked');
            }
            echo(' /><label for="remember">'.$this->t('remember').'</label>');
        }
        ?>
    </form>
<?php $this->includeAtTemplateBase('includes/footer.php');
