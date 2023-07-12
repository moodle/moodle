<?php
if (array_key_exists('header', $this->data)) {
    if ($this->getTranslator()->getTag($this->data['header']) !== null) {
        $this->data['header'] = $this->t($this->data['header']);
    }
}

$this->includeAtTemplateBase('includes/header.php');
$this->includeAtTemplateBase('includes/attributes.php');
?>
    <h2><?php if (isset($this->data['header'])) {
            echo $this->data['header'];
        } else {
            echo $this->t('{status:some_error_occurred}');
        } ?></h2>

    <p><?php echo $this->t('{status:intro}'); ?></p>

<?php
if (isset($this->data['remaining'])) {
    echo '<p>'.$this->t('{status:validfor}', ['%SECONDS%' => $this->data['remaining']]).'</p>';
}

if (isset($this->data['sessionsize'])) {
    echo '<p>'.$this->t('{status:sessionsize}', ['%SIZE%' => $this->data['sessionsize']]).'</p>';
}
?>
    <h2><?php echo $this->t('{status:attributes_header}'); ?></h2>
<?php
$attributes = $this->data['attributes'];
echo present_attributes($this, $attributes, '');

$nameid = $this->data['nameid'];
if ($nameid !== false) {
    /** @var \SAML2\XML\saml\NameID $nameid */
    echo "<h2>".$this->t('{status:subject_header}')."</h2>";
    if ($nameid->getValue() === null) {
        $list = ["NameID" => [$this->t('{status:subject_notset}')]];
        echo "<p>NameID: <span class=\"notset\">".$this->t('{status:subject_notset}')."</span></p>";
    } else {
        $list = [
            "NameId" => [$nameid->getValue()],
        ];
        if ($nameid->getFormat() !== null) {
            $list[$this->t('{status:subject_format}')] = [$nameid->getFormat()];
        }
        if ($nameid->getNameQualifier() !== null) {
            $list['NameQualifier'] = [$nameid->getNameQualifier()];
        }
        if ($nameid->getSPNameQualifier() !== null) {
            $list['SPNameQualifier'] = [$nameid->getSPNameQualifier()];
        }
        if ($nameid->getSPProvidedID() !== null) {
            $list['SPProvidedID'] = [$nameid->getSPProvidedID()];
        }
    }
    echo present_attributes($this, $list, '');
}

$authData = $this->data['authData'];
if (!empty($authData)) {
    echo "<h2>".$this->t('{status:authData_header}')."</h2>";
    echo '<details><summary>'.$this->t('{status:authData_summary}').'</summary>'; 
    echo '<pre>'.htmlspecialchars(json_encode($this->data['authData'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)).'</pre>';
    echo '</details>';
}
if (isset($this->data['logout'])) {
    echo '<h2>'.$this->t('{status:logout}').'</h2>';
    echo '<p>'.$this->data['logout'].'</p>';
}

if (isset($this->data['logouturl'])) {
    echo '<a href="'.htmlspecialchars($this->data['logouturl']).'">'.$this->t('{status:logout}').'</a>';
}

$this->includeAtTemplateBase('includes/footer.php');
