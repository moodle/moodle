<?php

// Load SimpleSAMLphp, configuration and metadata
$config = \SimpleSAML\Configuration::getInstance();
$session = \SimpleSAML\Session::getSessionFromRequest();
$oauthconfig = \SimpleSAML\Configuration::getOptionalConfig('module_oauth.php');

$store = new \SimpleSAML\Module\core\Storage\SQLPermanentStorage('oauth');

$authsource = "admin"; // force admin to authenticate as registry maintainer
$useridattr = $oauthconfig->getValue('useridattr', 'user');

if ($session->isValid($authsource)) {
    $attributes = $session->getAuthData($authsource, 'Attributes');
    // Check if userid exists
    if (!isset($attributes[$useridattr])) {
        throw new \Exception('User ID is missing');
    }
    $userid = $attributes[$useridattr][0];
} else {
    $as = \SimpleSAML\Auth\Source::getById($authsource);
    if (!is_null($as)) {
        $as->initLogin(\SimpleSAML\Utils\HTTP::getSelfURL());
    }
    throw new \Exception('Invalid authentication source: '.$authsource);
}

if (array_key_exists('editkey', $_REQUEST)) {
    $entryc = $store->get('consumers', $_REQUEST['editkey'], '');
    $entry = $entryc['value'];
    \SimpleSAML\Module\oauth\Registry::requireOwnership($entry, $userid);
} else {
    $entry = [
        'owner' => $userid,
        'key' => \SimpleSAML\Utils\Random::generateID(),
        'secret' => \SimpleSAML\Utils\Random::generateID(),
    ];
}

$editor = new \SimpleSAML\Module\oauth\Registry();

if (isset($_POST['submit'])) {
    $editor->checkForm($_POST);

    $entry = $editor->formToMeta($_POST, [], ['owner' => $userid]);

    \SimpleSAML\Module\oauth\Registry::requireOwnership($entry, $userid);

    $store->set('consumers', $entry['key'], '', $entry);

    $template = new \SimpleSAML\XHTML\Template($config, 'oauth:registry.saved.php');
    $template->data['entry'] = $entry;
    $template->show();
    exit;
}

$form = $editor->metaToForm($entry);

$template = new \SimpleSAML\XHTML\Template($config, 'oauth:registry.edit.tpl.php');
$template->data['form'] = $form;
$template->show();
