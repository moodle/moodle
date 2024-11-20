<?php
/**
 * Template to ask a user whether to logout because of a reauthentication or not.
 *
 * @var \SimpleSAML\XHTML\Template $this
 *
 * @author Jaime Pérez Crespo, UNINETT AS <jaime.perez@uninett.no>
 *
 * @package SimpleSAMLphp
 */

if (!isset($this->data['head'])) {
    $this->data['head'] = '';
}
$this->includeAtTemplateBase('includes/header.php');

$translator = $this->getTranslator();

$params = [
    '%IDP%' => $this->data['idp_name'],
    '%SP%' => $this->data['sp_name'],
];
?>
    <h2><?php echo $translator->t('{saml:proxy:invalid_idp}'); ?></h2>
    <p><?php echo $translator->t('{saml:proxy:invalid_idp_description}', $params); ?></p>
    <form method="post" action="?">
        <input type="hidden" name="AuthState" value="<?php echo htmlspecialchars($this->data['AuthState']); ?>" />
        <input type="submit" name="continue" value="<?php echo $translator->t('{general:yes_continue}'); ?>" />
        <input type="submit" name="cancel" value="<?php echo $translator->t('{general:no_cancel}'); ?>" />
    </form>
<?php
$this->includeAtTemplateBase('includes/footer.php');
