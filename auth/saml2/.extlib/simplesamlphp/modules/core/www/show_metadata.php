<?php

// load configuration
$config = \SimpleSAML\Configuration::getInstance();
$session = \SimpleSAML\Session::getSessionFromRequest();

\SimpleSAML\Utils\Auth::requireAdmin();

if (!array_key_exists('entityid', $_REQUEST)) {
    throw new Exception('required parameter [entityid] missing');
}
if (!array_key_exists('set', $_REQUEST)) {
    throw new Exception('required parameter [set] missing');
}
if (
    !in_array(
        $_REQUEST['set'],
        ['saml20-idp-remote', 'saml20-sp-remote', 'shib13-idp-remote', 'shib13-sp-remote'],
        true
    )
) {
    throw new Exception('Invalid set');
}

$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();

$m = $metadata->getMetaData($_REQUEST['entityid'], $_REQUEST['set']);

$t = new \SimpleSAML\XHTML\Template($config, 'core:show_metadata.tpl.php');
$t->data['clipboard.js'] = true;
$t->data['pageid'] = 'show_metadata';
$t->data['header'] = 'SimpleSAMLphp Show Metadata';
$t->data['backlink'] = \SimpleSAML\Module::getModuleURL('core/frontpage_federation.php');
$t->data['m'] = $m;
$t->data['entityid'] = $m['metadata-index'];
unset($m['metadata-index']);
$t->data['metadata'] = var_export($m, true);

$t->show();
