<?php
$this->includeAtTemplateBase('includes/header.php');

$dataId = $this->data['dataId'];
assert(is_string($dataId));

$url = $this->data['url'];
assert(is_string($url));

$nameIdFormat = $this->data['nameIdFormat'];
assert(is_string($nameIdFormat));

$nameIdValue = $this->data['nameIdValue'];
assert(is_string($nameIdValue));

$nameIdQualifier = $this->data['nameIdQualifier'];
assert(is_string($nameIdQualifier));

$nameIdSPQualifier = $this->data['nameIdSPQualifier'];
assert(is_string($nameIdSPQualifier));

$attributes = $this->data['attributes'];
assert($attributes === null || is_array($attributes));
?>

<h2>Attribute query test</h2>

<p>This is a test page for sending an AttributeQuery message.</p>

<h3>Request</h3>

<form action="?" method="post">
<input name="dataId" type="hidden" value="<?php echo htmlspecialchars($dataId); ?>" />
<p>
<label for="url">URL of attribute query endpoint:</label><br />
<input name="url" type="text" size="80" value="<?php echo htmlspecialchars($url); ?>" />
</p>
<p>
<label for="nameIdFormat">NameID format:</label><br />
<input name="nameIdFormat" type="text" size="80" value="<?php echo htmlspecialchars($nameIdFormat); ?>" />
</p>

<p>
<label for="nameIdValue">NameID value:</label><br />
<input name="nameIdValue" type="text" size="80" value="<?php echo htmlspecialchars($nameIdValue); ?>" />
</p>

<p>
<label for="nameIdQualifier">NameID NameQualifier (optional):</label><br />
<input name="nameIdQualifier" type="text" size="80" value="<?php echo htmlspecialchars($nameIdQualifier); ?>" />
</p>

<p>
<label for="nameIdSPQualifier">NameID SPNameQualifier (optional):</label><br />
<input name="nameIdSPQualifier" type="text" size="80" value="<?php echo htmlspecialchars($nameIdSPQualifier); ?>" />
</p>

<p>
<button type="submit" name="send" class="btn">Send query</button>
</p>
</form>

<?php
if ($attributes !== null) {
    echo '<h3>Attributes received</h3><dl>';
    foreach ($attributes as $name => $values) {
        echo '<dt>'.htmlspecialchars($name).'</dt><dd><ul>';
        foreach ($values as $value) {
            echo '<li>'.htmlspecialchars($value).'</li>';
        }
        echo '</dd>';
    }
    echo '</dl>';
}
?>

<?php $this->includeAtTemplateBase('includes/footer.php');
