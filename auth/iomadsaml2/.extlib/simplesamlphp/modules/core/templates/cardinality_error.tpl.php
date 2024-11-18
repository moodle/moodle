<?php
/**
 * Template which is shown when when an attribute violates a cardinality rule
 *
 * Parameters:
 * - 'target': Target URL.
 * - 'params': Parameters which should be included in the request.
 *
 * @package SimpleSAMLphp
 */


$this->data['cardinality_header'] = $this->t('{core:cardinality:cardinality_header}');
$this->data['cardinality_text'] = $this->t('{core:cardinality:cardinality_text}');
$this->data['problematic_attributes'] = $this->t('{core:cardinality:problematic_attributes}');

$this->includeAtTemplateBase('includes/header.php');
?>
<h1><?php echo $this->data['cardinality_header']; ?></h1>
<p><?php echo $this->data['cardinality_text']; ?></p>
<h3><?php echo $this->data['problematic_attributes']; ?></h3>
<dl class="cardinalityErrorAttributes">
<?php
foreach ($this->data['cardinalityErrorAttributes'] as $attr => $v) {
    echo '<dt>'.$attr.'</td><dd>';
    echo $this->t(
        '{core:cardinality:got_want}',
        ['%GOT%' => $v[0], '%WANT%' => htmlspecialchars($v[1])]
    );
    echo '</dd></tr>';
}
echo '</dl>';
if (isset($this->data['LogoutURL'])) {
    echo '<p><a href="'.htmlspecialchars($this->data['LogoutURL']).'>">'.$this->t('{status:logout}').'</a></p>';
}
$this->includeAtTemplateBase('includes/footer.php');
