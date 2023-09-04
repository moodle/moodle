<?php

$this->data['header'] = $this->t('selectidp');

$this->data['head'] = '<link rel="stylesheet" media="screen" type="text/css" href="'.
    SimpleSAML\Module::getModuleURL('discopower/assets/css/uitheme1.12.1/jquery-ui.min.css').'" />';
$this->data['head'] .= '<link rel="stylesheet" media="screen" type="text/css" href="'.
    SimpleSAML\Module::getModuleURL('discopower/assets/css/disco.css').'" />';

if (!empty($this->data['faventry'])) {
    $this->data['autofocus'] = 'favouritesubmit';
}

$this->includeAtTemplateBase('includes/header.php');

$this->data['htmlinject']['htmlContentPost'][] = '<script type="text/javascript" src="'.
    SimpleSAML\Module::getModuleURL('discopower/assets/js/jquery-1.12.4.min.js').'"></script>'."\n";
$this->data['htmlinject']['htmlContentPost'][] = '<script type="text/javascript" src="'.
    SimpleSAML\Module::getModuleURL('discopower/assets/js/jquery-ui-1.12.1.min.js').'"></script>'."\n";
$this->data['htmlinject']['htmlContentPost'][] = '<script type="text/javascript" src="'.
    SimpleSAML\Module::getModuleURL('discopower/assets/js/jquery.livesearch.js').'"></script>'."\n";
$this->data['htmlinject']['htmlContentPost'][] = '<script type="text/javascript" src="'.
    SimpleSAML\Module::getModuleURL('discopower/assets/js/'.$this->data['score'].'.js').'"></script>'."\n";
$this->data['htmlinject']['htmlContentPost'][] = '<script type="text/javascript" src="'.
    SimpleSAML\Module::getModuleURL('discopower/assets/js/tablist.js').'"></script>'."\n";


function showEntry($t, $metadata, $favourite = false)
{
    $basequerystring = '?'.
        'entityID='.urlencode($t->data['entityID']).'&amp;'.
        'return='.urlencode($t->data['return']).'&amp;'.
        'returnIDParam='.urlencode($t->data['returnIDParam']).'&amp;idpentityid=';

    $extra = ($favourite ? ' favourite' : '');
    $html = '<a class="metaentry'.$extra.'" href="'.$basequerystring.urlencode($metadata['entityid']).'">';

    $html .= htmlspecialchars(getTranslatedName($t, $metadata)).'';

    if (array_key_exists('icon', $metadata) && $metadata['icon'] !== null) {
        $iconUrl = \SimpleSAML\Utils\HTTP::resolveURL($metadata['icon']);
        $html .= '<img alt="Icon for identity provider" class="entryicon" src="'.htmlspecialchars($iconUrl).'" />';
    }

    $html .= '</a>';
    return $html;
}

function getTranslatedName($t, $metadata)
{
    if (isset($metadata['UIInfo']['DisplayName'])) {
        $displayName = $metadata['UIInfo']['DisplayName'];
        assert(is_array($displayName)); // Should always be an array of language code -> translation
        if (!empty($displayName)) {
            return $t->getTranslator()->getPreferredTranslation($displayName);
        }
    }

    if (array_key_exists('name', $metadata)) {
        if (is_array($metadata['name'])) {
            return $t->getTranslator()->getPreferredTranslation($metadata['name']);
        } else {
            return $metadata['name'];
        }
    }
    return $metadata['entityid'];
}

if (!empty($this->data['faventry'])) {
    echo '<div class="favourite">' ;
    echo $this->t('previous_auth');
    echo ' <strong>'.htmlspecialchars(getTranslatedName($this, $this->data['faventry'])).'</strong>';
    echo '<form id="idpselectform" method="get" action="'.$this->data['urlpattern'].
        '"><input type="hidden" name="entityID" value="'.htmlspecialchars($this->data['entityID']).
        '" /><input type="hidden" name="return" value="'.htmlspecialchars($this->data['return']).
        '" /><input type="hidden" name="returnIDParam" value="'.htmlspecialchars($this->data['returnIDParam']).
        '" /><input type="hidden" name="idpentityid" value="'.htmlspecialchars($this->data['faventry']['entityid']).
        '" /><input type="submit" name="formsubmit" id="favouritesubmit" value="'.$this->t('login_at').' '.
        htmlspecialchars(getTranslatedName($this, $this->data['faventry'])).'" /></form>';
    echo '</div>';
}
?>

<div id="tabdiv">
    <ul class="tabset_tabs">
        <?php
        $tabs = array_keys($this->data['idplist']);
        $i = 1;
        foreach ($tabs as $tab) {
            if (!empty($this->data['idplist'][$tab])) {
                if ($i === 1) {
                    echo '<li class="tab-link current" data-tab="'.$tab.'"><a href="#'.$tab.
                        '"><span>'.$this->t($this->data['tabNames'][$tab]).'</span></a></li>';
                } else {
                    echo '<li class="tab-link" data-tab="'.$tab.'"><a href="#'.$tab.
                        '"><span>'.$this->t($this->data['tabNames'][$tab]).'</span></a></li> ';
                }
                $i++;
            }
        }
        ?>
    </ul>

<?php

foreach ($this->data['idplist'] as $tab => $slist) {
    $first = array_keys($this->data['idplist']);
    if ($first[0] === $tab) {
        echo '<div id="'.$tab.'" class="tabset_content current">';
    } else {
        echo '<div id="'.$tab.'" class="tabset_content">';
    }
    if (!empty($slist)) {
        echo '<div class="inlinesearch">';
        echo '<p>'.htmlspecialchars($this->t('{discopower:tabs:incremental_search}')).'</p>';
        echo '<form id="idpselectform" action="?" method="get">';
        echo '<input class="inlinesearch" type="text" value="" name="query_'.$tab.'" id="query_'.$tab.'" /></form>';
        echo '</div>';

        echo '<div class="metalist" id="list_'.$tab .'">';
        if (!empty($this->data['preferredidp']) && array_key_exists($this->data['preferredidp'], $slist)) {
            $idpentry = $slist[$this->data['preferredidp']];
            echo showEntry($this, $idpentry, true);
        }

        foreach ($slist as $idpentry) {
            if ($idpentry['entityid'] != $this->data['preferredidp']) {
                echo showEntry($this, $idpentry);
            }
        }
        echo '</div>';
    }
    echo '</div>';
}

?>

</div>

<?php
$this->includeAtTemplateBase('includes/footer.php');
