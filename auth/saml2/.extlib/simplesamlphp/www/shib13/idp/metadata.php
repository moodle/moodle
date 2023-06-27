<?php

require_once('../../_include.php');

// load configuration and metadata
$config = \SimpleSAML\Configuration::getInstance();
$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();

if (!$config->getBoolean('enable.shib13-idp', false)) {
    throw new \SimpleSAML\Error\Error('NOACCESS');
}

// check if valid local session exists
if ($config->getBoolean('admin.protectmetadata', false)) {
    \SimpleSAML\Utils\Auth::requireAdmin();
}

try {
    $idpentityid = isset($_GET['idpentityid']) ?
        $_GET['idpentityid'] : $metadata->getMetaDataCurrentEntityID('shib13-idp-hosted');
    $idpmeta = $metadata->getMetaDataConfig($idpentityid, 'shib13-idp-hosted');

    $keys = [];
    $certInfo = \SimpleSAML\Utils\Crypto::loadPublicKey($idpmeta, false, 'new_');
    if ($certInfo !== null) {
        $keys[] = [
            'type'            => 'X509Certificate',
            'signing'         => true,
            'encryption'      => false,
            'X509Certificate' => $certInfo['certData'],
        ];
    }

    $certInfo = \SimpleSAML\Utils\Crypto::loadPublicKey($idpmeta, true);
    $keys[] = [
        'type'            => 'X509Certificate',
        'signing'         => true,
        'encryption'      => false,
        'X509Certificate' => $certInfo['certData'],
    ];

    $metaArray = [
        'metadata-set'        => 'shib13-idp-remote',
        'entityid'            => $idpentityid,
        'SingleSignOnService' => $metadata->getGenerated('SingleSignOnService', 'shib13-idp-hosted'),
    ];

    if (count($keys) === 1) {
        $metaArray['certData'] = $keys[0]['X509Certificate'];
    } else {
        $metaArray['keys'] = $keys;
    }

    $metaArray['NameIDFormat'] = $idpmeta->getArrayizeString('NameIDFormat', 'urn:mace:shibboleth:1.0:nameIdentifier');

    if ($idpmeta->hasValue('OrganizationName')) {
        $metaArray['OrganizationName'] = $idpmeta->getLocalizedString('OrganizationName');
        $metaArray['OrganizationDisplayName'] = $idpmeta->getLocalizedString(
            'OrganizationDisplayName',
            $metaArray['OrganizationName']
        );

        if (!$idpmeta->hasValue('OrganizationURL')) {
            throw new \SimpleSAML\Error\Exception('If OrganizationName is set, OrganizationURL must also be set.');
        }
        $metaArray['OrganizationURL'] = $idpmeta->getLocalizedString('OrganizationURL');
    }

    $metaflat = '$metadata['.var_export($idpentityid, true).'] = '.var_export($metaArray, true).';';

    $metaBuilder = new \SimpleSAML\Metadata\SAMLBuilder($idpentityid);
    $metaBuilder->addMetadataIdP11($metaArray);
    $metaBuilder->addOrganizationInfo($metaArray);
    $metaBuilder->addContact('technical', \SimpleSAML\Utils\Config\Metadata::getContact([
        'emailAddress' => $config->getString('technicalcontact_email', null),
        'name'         => $config->getString('technicalcontact_name', null),
        'contactType'  => 'technical',
    ]));
    $metaxml = $metaBuilder->getEntityDescriptorText();

    // sign the metadata if enabled
    $metaxml = \SimpleSAML\Metadata\Signer::sign($metaxml, $idpmeta->toArray(), 'Shib 1.3 IdP');

    if (array_key_exists('output', $_GET) && $_GET['output'] == 'xhtml') {
        $defaultidp = $config->getString('default-shib13-idp', null);

        $t = new \SimpleSAML\XHTML\Template($config, 'metadata.tpl.php', 'admin');

        $t->data['clipboard.js'] = true;
        $t->data['header'] = 'shib13-idp'; // TODO: Replace with headerString in 2.0
        $t->data['headerString'] = \SimpleSAML\Locale\Translate::noop('metadata_shib13-idp');
        $t->data['metaurl'] = \SimpleSAML\Utils\HTTP::addURLParameters(
            \SimpleSAML\Utils\HTTP::getSelfURLNoQuery(),
            ['output' => 'xml']
        );
        $t->data['metadata'] = htmlspecialchars($metaxml);
        $t->data['metadataflat'] = htmlspecialchars($metaflat);

        $t->data['defaultidp'] = $defaultidp;

        $t->show();
    } else {
        header('Content-Type: application/xml');
        echo $metaxml;
        exit(0);
    }
} catch (\Exception $exception) {
    throw new \SimpleSAML\Error\Error('METADATA', $exception);
}
