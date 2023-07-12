<?php
    $this->data['head'] = '<link rel="stylesheet" type="text/css" href="'.
        SimpleSAML\Module::getModuleURL("consentAdmin/assets/css/consentAdmin.css").'" />'."\n";
    $this->data['head'] .= '<script type="text/javascript" src="'.
        SimpleSAML\Module::getModuleURL("consentAdmin/assets/js/consentAdmin.js").'"></script>';
    // default theme
    $this->includeAtTemplateBase('includes/header.php');
?>
        <h2><?php echo $this->t('{consentAdmin:consentadmin:consentadmin_header}') ?></h2>
        <p>
        <?php echo $this->t('{consentAdmin:consentadmin:consentadmin_description1}') ?> </p>


            <table>
            <tr>
                <th width="80%"><?php echo $this->t('{consentAdmin:consentadmin:service_provider_header}') ?></th>
                <th><?php echo $this->t('{consentAdmin:consentadmin:status_header}') ?></th>
            </tr>
            <?php
            $spList = $this->data['spList'];
            $show_spid = 0;
            $show_text = $this->t('{consentAdmin:consentadmin:show}');
            $hide_text = $this->t('{consentAdmin:consentadmin:hide}');
            $attributes_text = $this->t('{consentAdmin:consentadmin:attributes_text}');
            foreach ($spList as $spName => $spValues) {
                if (!is_null($spValues['serviceurl'])) {
                    $htmlSpName = '<a class="serviceUrl" href="'.$spValues['serviceurl'].'">'.
                        htmlspecialchars($spValues['name']).'</a>';
                } else {
                    $htmlSpName = htmlspecialchars($spValues['name']);
                }
                $spDescription = htmlspecialchars($spValues['description']);
                $checkedAttr = $spValues['consentStatus'] == 'ok' ? 'checked="checked"' : '';
                $consentValue = $spValues['consentValue'];
                $consentText = $spValues['consentStatus'] == 'changed' ? "attributes has changed" : "";
                $row_class = ($show_spid % 2) ? "row0" : "row1";
                echo <<<TRSTART
<tr class="$row_class">
<td>
    <table>
      <tr class="$row_class">
          <td><span class='caSPName'><span title='$spDescription'>$htmlSpName</span>&emsp;
          <span class="show_hide" id="show_hide_$show_spid"><span id='showing_$show_spid'>$show_text</span>
          <span id='hiding_$show_spid'>$hide_text</span> $attributes_text</span></span></td></tr>
      <tr><td colspan="2" class="caAttributes"><div id="attributes_$show_spid">
TRSTART;
                $attributes = $spValues['attributes_by_sp'];
                if ($this->data['showDescription']) {
                    echo '<p>'.$this->t('{consentAdmin:consentadmin:consentadmin_purpose}').' '.$spDescription.'</p>';
                }
                echo "\n<ul>\n";
                foreach ($attributes as $name => $value) {
                    if (sizeof($value) > 1) {
                        echo "<li>".htmlspecialchars($name).":\n<ul>\n";
                        foreach ($value as $v) {
                            echo '<li>'.htmlspecialchars($v)."</li>\n";
                        }
                        echo "</ul>\n</li>\n";
                    } else {
                        echo "<li>".htmlspecialchars($name).": ".htmlspecialchars($value[0])."</li>\n";
                    }
                }
                echo "</ul>";
                echo <<<TRSTART
      </div></td></tr>
  </table>
</td>

<td class='caAllowed'>
    <input id="checkbox_$show_spid" class="checkbox" value='$consentValue' type='checkbox' $checkedAttr />
    <span id="consentText_$show_spid">$consentText</span></td>
TRSTART;
                echo "</tr>\n";
                $show_spid++;
            }
            ?>
            </table>

            <p>
        <?php echo $this->t('{consentAdmin:consentadmin:consentadmin_description2}') ?> </p>

        <h2>Logout</h2>
        <p>
            <a href="
            <?php
                echo \SimpleSAML\Module::getModuleURL('consentAdmin/consentAdmin.php', ['logout' => 1]);
            ?>">Logout</a>
        </p>

<?php $this->includeAtTemplateBase('includes/footer.php');
