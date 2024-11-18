<?php

use Webmozart\Assert\Assert;

// load configuration and metadata
$config = \SimpleSAML\Configuration::getInstance();
$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();

if (!$config->getBoolean('enable.adfs-idp', false)) {
    throw new \SimpleSAML\Error\Error('NOACCESS');
}

// check if valid local session exists
if ($config->getBoolean('admin.protectmetadata', false)) {
    \SimpleSAML\Utils\Auth::requireAdmin();
}

try {
    $idpentityid = isset($_GET['idpentityid']) ?
        $_GET['idpentityid'] : $metadata->getMetaDataCurrentEntityID('adfs-idp-hosted');
    $idpmeta = $metadata->getMetaDataConfig($idpentityid, 'adfs-idp-hosted');

    $availableCerts = [];

    $keys = [];
    $certInfo = \SimpleSAML\Utils\Crypto::loadPublicKey($idpmeta, false, 'new_');
    if ($certInfo !== null) {
        $availableCerts['new_idp.crt'] = $certInfo;
        $keys[] = [
            'type'            => 'X509Certificate',
            'signing'         => true,
            'encryption'      => true,
            'X509Certificate' => $certInfo['certData'],
        ];
        $hasNewCert = true;
    } else {
        $hasNewCert = false;
    }

    /** @var array $certInfo */
    $certInfo = \SimpleSAML\Utils\Crypto::loadPublicKey($idpmeta, true);
    $availableCerts['idp.crt'] = $certInfo;
    $keys[] = [
        'type'            => 'X509Certificate',
        'signing'         => true,
        'encryption'      => ($hasNewCert ? false : true),
        'X509Certificate' => $certInfo['certData'],
    ];

    if ($idpmeta->hasValue('https.certificate')) {
        /** @var array $httpsCert */
        $httpsCert = \SimpleSAML\Utils\Crypto::loadPublicKey($idpmeta, true, 'https.');
        Assert::keyExists($httpsCert, 'certData');
        $availableCerts['https.crt'] = $httpsCert;
        $keys[] = [
            'type'            => 'X509Certificate',
            'signing'         => true,
            'encryption'      => false,
            'X509Certificate' => $httpsCert['certData'],
        ];
    }

    $adfs_service_location = \SimpleSAML\Module::getModuleURL('adfs').'/idp/prp.php';
    $metaArray = [
        'metadata-set'        => 'adfs-idp-remote',
        'entityid'            => $idpentityid,
        'SingleSignOnService' => [
            0 => [
                'Binding'  => \SAML2\Constants::BINDING_HTTP_REDIRECT,
                'Location' => $adfs_service_location
            ]
        ],
        'SingleLogoutService' => [
            0 => [
                'Binding'  => \SAML2\Constants::BINDING_HTTP_REDIRECT,
                'Location' => $adfs_service_location
            ]
        ],
    ];

    if (count($keys) === 1) {
        $metaArray['certData'] = $keys[0]['X509Certificate'];
    } else {
        $metaArray['keys'] = $keys;
    }

    $metaArray['NameIDFormat'] = $idpmeta->getString(
        'NameIDFormat',
        'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'
    );

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

    if ($idpmeta->hasValue('scope')) {
        $metaArray['scope'] = $idpmeta->getArray('scope');
    }

    if ($idpmeta->hasValue('EntityAttributes')) {
        $metaArray['EntityAttributes'] = $idpmeta->getArray('EntityAttributes');
    }

    if ($idpmeta->hasValue('UIInfo')) {
        $metaArray['UIInfo'] = $idpmeta->getArray('UIInfo');
    }

    if ($idpmeta->hasValue('DiscoHints')) {
        $metaArray['DiscoHints'] = $idpmeta->getArray('DiscoHints');
    }

    if ($idpmeta->hasValue('RegistrationInfo')) {
        $metaArray['RegistrationInfo'] = $idpmeta->getArray('RegistrationInfo');
    }

    $metaflat = '$metadata['.var_export($idpentityid, true).'] = '.var_export($metaArray, true).';';

    $metaBuilder = new \SimpleSAML\Metadata\SAMLBuilder($idpentityid);
    $metaBuilder->addSecurityTokenServiceType($metaArray);
    $metaBuilder->addOrganizationInfo($metaArray);
    $technicalContactEmail = $config->getString('technicalcontact_email', null);
    if ($technicalContactEmail && $technicalContactEmail !== 'na@example.org') {
        $metaBuilder->addContact('technical', \SimpleSAML\Utils\Config\Metadata::getContact([
            'emailAddress' => $technicalContactEmail,
            'name'         => $config->getString('technicalcontact_name', null),
            'contactType'  => 'technical',
        ]));
    }
    $output_xhtml = array_key_exists('output', $_GET) && $_GET['output'] == 'xhtml';
    $metaxml = $metaBuilder->getEntityDescriptorText($output_xhtml);
    if (!$output_xhtml) {
        $metaxml = str_replace("\n", '', $metaxml);
    }

    // sign the metadata if enabled
    $metaxml = \SimpleSAML\Metadata\Signer::sign($metaxml, $idpmeta->toArray(), 'ADFS IdP');

    if ($output_xhtml) {
        $defaultidp = $config->getString('default-adfs-idp', null);

        $t = new \SimpleSAML\XHTML\Template($config, 'metadata.php', 'admin');

        $t->data['clipboard.js'] = true;
        $t->data['available_certs'] = $availableCerts;
        $certdata = [];
        foreach (array_keys($availableCerts) as $availableCert) {
            $certdata[$availableCert]['name'] = $availableCert;
            $certdata[$availableCert]['url'] = \SimpleSAML\Module::getModuleURL('saml/idp/certs.php').
                '/'.$availableCert;

            $certdata[$availableCert]['comment'] = '';
            if ($availableCerts[$availableCert]['certFingerprint'][0] === 'afe71c28ef740bc87425be13a2263d37971da1f9') {
                $certdata[$availableCert]['comment'] = 'This is the default certificate.'.
                    ' Generate a new certificate if this is a production system.';
            }
        }
        $t->data['certdata'] = $certdata;
        $t->data['header'] = 'adfs-idp'; // TODO: Replace with headerString in 2.0
        $t->data['headerString'] = \SimpleSAML\Locale\Translate::noop('metadata_adfs-idp');
        $t->data['metaurl'] = \SimpleSAML\Utils\HTTP::getSelfURLNoQuery();
        $t->data['metadata'] = htmlspecialchars($metaxml);
        $t->data['metadataflat'] = htmlspecialchars($metaflat);
        $t->data['defaultidp'] = $defaultidp;
        $t->show();
    } else {
        header('Content-Type: application/xml');

        // make sure to export only the md:EntityDescriptor
        $i = strpos($metaxml, '<md:EntityDescriptor');
        $metaxml = substr($metaxml, $i ? $i : 0);
        // 22 = strlen('</md:EntityDescriptor>')
        $i = strrpos($metaxml, '</md:EntityDescriptor>');
        $metaxml = substr($metaxml, 0, $i ? $i + 22 : 0);
        echo $metaxml;

        exit(0);
    }
} catch (\Exception $exception) {
    throw new \SimpleSAML\Error\Error('METADATA', $exception);
}
