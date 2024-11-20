<?php

// Load SimpleSAMLphp, configuration and metadata
$config = \SimpleSAML\Configuration::getInstance();
$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();

if (!$config->getBoolean('enable.saml20-idp', false)) {
    throw new \SimpleSAML\Error\Error('NOACCESS');
}

// Check if valid local session exists..
if ($config->getBoolean('admin.protectmetadata', false)) {
    \SimpleSAML\Utils\Auth::requireAdmin();
}

$idpentityid = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
$idpmeta = $metadata->getMetaDataConfig($idpentityid, 'saml20-idp-hosted');

switch ($_SERVER['PATH_INFO']) {
    case '/new_idp.crt':
        /** @var array $certInfo */
        $certInfo = SimpleSAML\Utils\Crypto::loadPublicKey($idpmeta, true, 'new_');
        break;
    case '/idp.crt':
        /** @var array $certInfo */
        $certInfo = SimpleSAML\Utils\Crypto::loadPublicKey($idpmeta, true);
        break;
    case '/https.crt':
        /** @var array $certInfo */
        $certInfo = SimpleSAML\Utils\Crypto::loadPublicKey($idpmeta, true, 'https.');
        break;
    default:
        throw new \SimpleSAML\Error\NotFound('Unknown certificate.');
}
header('Content-Disposition: attachment; filename=' . substr($_SERVER['PATH_INFO'], 1));
header('Content-Type: application/x-x509-ca-cert');

echo $certInfo['PEM'];
exit(0);
