<?php

require_once('../../_include.php');

use Symfony\Component\VarExporter\VarExporter;

use SAML2\Constants;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Module;
use SimpleSAML\Utils\Auth as Auth;
use SimpleSAML\Utils\Crypto as Crypto;
use SimpleSAML\Utils\HTTP as HTTP;
use SimpleSAML\Utils\Config\Metadata as Metadata;

$config = Configuration::getInstance();
if (!$config->getBoolean('enable.saml20-idp', false) || !Module::isModuleEnabled('saml')) {
    throw new Error\Error('NOACCESS', null, 403);
}

// check if valid local session exists
if ($config->getBoolean('admin.protectmetadata', false)) {
    Auth::requireAdmin();
}

$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();

try {
    $idpentityid = isset($_GET['idpentityid']) ?
        $_GET['idpentityid'] : $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
    $idpmeta = $metadata->getMetaDataConfig($idpentityid, 'saml20-idp-hosted');

    $availableCerts = [];

    $keys = [];
    $certInfo = Crypto::loadPublicKey($idpmeta, false, 'new_');
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

    $certInfo = Crypto::loadPublicKey($idpmeta, true);
    $availableCerts['idp.crt'] = $certInfo;
    $keys[] = [
        'type'            => 'X509Certificate',
        'signing'         => true,
        'encryption'      => ($hasNewCert ? false : true),
        'X509Certificate' => $certInfo['certData'],
    ];

    if ($idpmeta->hasValue('https.certificate')) {
        $httpsCert = Crypto::loadPublicKey($idpmeta, true, 'https.');
        assert(isset($httpsCert['certData']));
        $availableCerts['https.crt'] = $httpsCert;
        $keys[] = [
            'type'            => 'X509Certificate',
            'signing'         => true,
            'encryption'      => false,
            'X509Certificate' => $httpsCert['certData'],
        ];
    }

    $metaArray = [
        'metadata-set' => 'saml20-idp-remote',
        'entityid'     => $idpentityid,
    ];

    $ssob = $metadata->getGenerated('SingleSignOnServiceBinding', 'saml20-idp-hosted');
    $slob = $metadata->getGenerated('SingleLogoutServiceBinding', 'saml20-idp-hosted');
    $ssol = $metadata->getGenerated('SingleSignOnService', 'saml20-idp-hosted');
    $slol = $metadata->getGenerated('SingleLogoutService', 'saml20-idp-hosted');

    if (is_array($ssob)) {
        foreach ($ssob as $binding) {
            $metaArray['SingleSignOnService'][] = [
                'Binding'  => $binding,
                'Location' => $ssol,
            ];
        }
    } else {
        $metaArray['SingleSignOnService'][] = [
            'Binding'  => $ssob,
            'Location' => $ssol,
        ];
    }

    if (is_array($slob)) {
        foreach ($slob as $binding) {
            $metaArray['SingleLogoutService'][] = [
                'Binding'  => $binding,
                'Location' => $slol,
            ];
        }
    } else {
        $metaArray['SingleLogoutService'][] = [
            'Binding'  => $slob,
            'Location' => $slol,
        ];
    }

    if (count($keys) === 1) {
        $metaArray['certData'] = $keys[0]['X509Certificate'];
    } else {
        $metaArray['keys'] = $keys;
    }

    if ($idpmeta->getBoolean('saml20.sendartifact', false)) {
        // Artifact sending enabled
        $metaArray['ArtifactResolutionService'][] = [
            'index'    => 0,
            'Location' => HTTP::getBaseURL() . 'saml2/idp/ArtifactResolutionService.php',
            'Binding'  => Constants::BINDING_SOAP,
        ];
    }

    if ($idpmeta->getBoolean('saml20.hok.assertion', false)) {
        // Prepend HoK SSO Service endpoint.
        array_unshift($metaArray['SingleSignOnService'], [
            'hoksso:ProtocolBinding' => Constants::BINDING_HTTP_REDIRECT,
            'Binding'                => Constants::BINDING_HOK_SSO,
            'Location'               => HTTP::getBaseURL() . 'saml2/idp/SSOService.php'
        ]);
    }

    if ($idpmeta->getBoolean('saml20.ecp', false)) {
        $metaArray['SingleSignOnService'][] = [
            'index' => 0,
            'Binding'  => Constants::BINDING_SOAP,
            'Location' => HTTP::getBaseURL() . 'saml2/idp/SSOService.php',
        ];
    }

    $metaArray['NameIDFormat'] = $idpmeta->getArrayizeString(
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
            throw new Error\Exception(
                'If OrganizationName is set, OrganizationURL must also be set.'
            );
        }
        $metaArray['OrganizationURL'] = $idpmeta->getLocalizedString('OrganizationURL');
    }

    if ($idpmeta->hasValue('scope')) {
        $metaArray['scope'] = $idpmeta->getArray('scope');
    }

    if ($idpmeta->hasValue('EntityAttributes')) {
        $metaArray['EntityAttributes'] = $idpmeta->getArray('EntityAttributes');

        // check for entity categories
        if (Metadata::isHiddenFromDiscovery($metaArray)) {
            $metaArray['hide.from.discovery'] = true;
        }
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

    if ($idpmeta->hasValue('validate.authnrequest')) {
        $metaArray['sign.authnrequest'] = $idpmeta->getBoolean('validate.authnrequest');
    }

    if ($idpmeta->hasValue('redirect.validate')) {
        $metaArray['redirect.sign'] = $idpmeta->getBoolean('redirect.validate');
    }

    if ($idpmeta->hasValue('contacts')) {
        $contacts = $idpmeta->getArray('contacts');
        foreach ($contacts as $contact) {
            $metaArray['contacts'][] = Metadata::getContact($contact);
        }
    }

    $technicalContactEmail = $config->getString('technicalcontact_email', false);
    if ($technicalContactEmail && $technicalContactEmail !== 'na@example.org') {
        $techcontact = [
            'emailAddress' => $technicalContactEmail,
            'name' => $config->getString('technicalcontact_name', null),
            'contactType' => 'technical',
        ];
        $metaArray['contacts'][] = Metadata::getContact($techcontact);
    }

    $metaBuilder = new \SimpleSAML\Metadata\SAMLBuilder($idpentityid);
    $metaBuilder->addMetadataIdP20($metaArray);
    $metaBuilder->addOrganizationInfo($metaArray);

    $metaxml = $metaBuilder->getEntityDescriptorText();

    $metaflat = '$metadata[' . var_export($idpentityid, true) . '] = ' . VarExporter::export($metaArray) . ';';

    // sign the metadata if enabled
    $metaxml = \SimpleSAML\Metadata\Signer::sign($metaxml, $idpmeta->toArray(), 'SAML 2 IdP');

    if (array_key_exists('output', $_GET) && $_GET['output'] == 'xhtml') {
        $t = new \SimpleSAML\XHTML\Template($config, 'metadata.tpl.php', 'admin');

        $t->data['clipboard.js'] = true;
        $t->data['available_certs'] = $availableCerts;
        $certdata = [];
        foreach (array_keys($availableCerts) as $availableCert) {
            $certdata[$availableCert]['name'] = $availableCert;
            $certdata[$availableCert]['url'] = Module::getModuleURL('saml/idp/certs.php') . '/' . $availableCert;
            $certdata[$availableCert]['comment'] = (
                $availableCerts[$availableCert]['certFingerprint'][0] === 'afe71c28ef740bc87425be13a2263d37971da1f9' ?
                'This is the default certificate. Generate a new certificate if this is a production system.' :
                ''
            );
        }
        $t->data['certdata'] = $certdata;
        $t->data['header'] = 'saml20-idp'; // TODO: Replace with headerString in 2.0
        $t->data['headerString'] = \SimpleSAML\Locale\Translate::noop('metadata_saml20-idp');
        $t->data['metaurl'] = HTTP::getSelfURLNoQuery();
        $t->data['metadata'] = htmlspecialchars($metaxml);
        $t->data['metadataflat'] = htmlspecialchars($metaflat);
        $t->show();
    } else {
        $etag = '"' . hash('sha256', $metaxml) . '"';
        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if ($_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
                header("HTTP/1.1 304 Not Modified");
                exit(0);
            }
        }
        header('Content-Type: application/samlmetadata+xml');
        header('ETag: ' . $etag);
        echo $metaxml;
        exit(0);
    }
} catch (\Exception $exception) {
    throw new Error\Error('METADATA', $exception);
}
