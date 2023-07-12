<?php
/*
 * consentAdmin - Consent administration module
 *
 * This module enables the user to add and remove consents given for a given
 * Service Provider.
 *
 * The module relies on methods and functions from the Consent module and can
 * not be user without it.
 *
 * Author: Mads Freek <freek@ruc.dk>, Jacob Christiansen <jach@wayf.dk>
 */

/**
 * Runs the processing chain and ignores all filter which have user
 * interaction.
 *
 * @param array $idp_metadata
 * @param string $source
 * @param array $sp_metadata
 * @param string $sp_entityid
 * @param array $attributes
 * @param string $userid
 * @param bool $hashAttributes
 * @param array $excludeAttributes
 * @return array
 */
function driveProcessingChain(
    $idp_metadata,
    $source,
    $sp_metadata,
    $sp_entityid,
    $attributes,
    $userid,
    $hashAttributes = false,
    $excludeAttributes = []
) {
    /*
     * Create a new processing chain
     */
    $pc = new \SimpleSAML\Auth\ProcessingChain($idp_metadata, $sp_metadata, 'idp');

    /*
     * Construct the state.
     * REMEMBER: Do not set Return URL if you are calling processStatePassive
     */
    $authProcState = [
        'Attributes'  => $attributes,
        'Destination' => $sp_metadata,
        'SPMetadata'  => $sp_metadata,
        'Source'      => $idp_metadata,
        'IdPMetadata' => $idp_metadata,
        'isPassive'   => true,
    ];
    /* we're being bridged, so add that info to the state */
    if (strpos($source, '-idp-remote|') !== false) {
        /** @var int $i */
        $i = strpos($source, '|');
        $authProcState['saml:sp:IdP'] = substr($source, $i + 1);
    }

    /*
     * Call processStatePAssive.
     * We are not interested in any user interaction, only modifications to the attributes
     */
    $pc->processStatePassive($authProcState);

    $attributes = $authProcState['Attributes'];
    // Remove attributes that do not require consent/should be excluded
    foreach ($attributes as $attrkey => $attrval) {
        if (in_array($attrkey, $excludeAttributes)) {
            unset($attributes[$attrkey]);
        }
    }

    /*
     * Generate identifiers and hashes
     */
    $destination = $sp_metadata['metadata-set'].'|'.$sp_entityid;

    $targeted_id = \SimpleSAML\Module\consent\Auth\Process\Consent::getTargetedID($userid, $source, $destination);
    $attribute_hash = \SimpleSAML\Module\consent\Auth\Process\Consent::getAttributeHash($attributes, $hashAttributes);

    \SimpleSAML\Logger::info('consentAdmin: user: '.$userid);
    \SimpleSAML\Logger::info('consentAdmin: target: '.$targeted_id);
    \SimpleSAML\Logger::info('consentAdmin: attribute: '.$attribute_hash);

    // Return values
    return [$targeted_id, $attribute_hash, $attributes];
}

// Get config object
$config = \SimpleSAML\Configuration::getInstance();
$cA_config = \SimpleSAML\Configuration::getConfig('module_consentAdmin.php');
$authority = $cA_config->getValue('authority');

$as = new \SimpleSAML\Auth\Simple($authority);

// If request is a logout request
if (array_key_exists('logout', $_REQUEST)) {
    $returnURL = $cA_config->getValue('returnURL');
    $as->logout($returnURL);
}

$hashAttributes = $cA_config->getValue('attributes.hash');

$excludeAttributes = $cA_config->getValue('attributes.exclude', []);

// Check if valid local session exists
$as->requireAuth();

// Get released attributes
$attributes = $as->getAttributes();

// Get metadata storage handler
$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();

/*
 * Get IdP id and metadata
 */

$idp_entityid = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
$idp_metadata = $metadata->getMetaData($idp_entityid, 'saml20-idp-hosted');

// Calc correct source
if ($as->getAuthData('saml:sp:IdP') !== null) {
    // from a remote idp (as bridge)
    $source = 'saml20-idp-remote|'.$as->getAuthData('saml:sp:IdP');
} else {
    // from the local idp
    $source = $idp_metadata['metadata-set'].'|'.$idp_entityid;
}

// Get user ID
if (isset($idp_metadata['userid.attribute']) && is_string($idp_metadata['userid.attribute'])) {
    $userid_attributename = $idp_metadata['userid.attribute'];
} else {
    $userid_attributename = 'eduPersonPrincipalName';
}

$userids = $attributes[$userid_attributename];

if (empty($userids)) {
    throw new \Exception('Could not generate useridentifier for storing consent. Attribute ['.
        $userid_attributename.'] was not available.');
}

$userid = $userids[0];

// Get all SP metadata
$all_sp_metadata = $metadata->getList('saml20-sp-remote');

// Parse action, if any
$action = null;
$sp_entityid = null;
if (!empty($_GET['cv'])) {
    $sp_entityid = $_GET['cv'];
}
if (!empty($_GET['action'])) {
    $action = $_GET["action"];
}

\SimpleSAML\Logger::critical('consentAdmin: sp: '.$sp_entityid.' action: '.$action);

// Remove services, whitch have consent disabled
if (isset($idp_metadata['consent.disable'])) {
    foreach ($idp_metadata['consent.disable'] as $disable) {
        if (array_key_exists($disable, $all_sp_metadata)) {
            unset($all_sp_metadata[$disable]);
        }
    }
}

\SimpleSAML\Logger::info('consentAdmin: '.$idp_entityid);

// Parse consent config
$consent_storage = \SimpleSAML\Module\consent\Store::parseStoreConfig($cA_config->getValue('consentadmin'));

// Calc correct user ID hash
$hashed_user_id = \SimpleSAML\Module\consent\Auth\Process\Consent::getHashedUserID($userid, $source);

// If a checkbox have been clicked
if ($action !== null && $sp_entityid !== null) {
    // init template to enable translation of status messages
    $template = new \SimpleSAML\XHTML\Template(
        $config,
        'consentAdmin:consentadminajax.php',
        'consentAdmin:consentadmin'
    );
    $translator = $template->getTranslator();

    // Get SP metadata
    $sp_metadata = $metadata->getMetaData($sp_entityid, 'saml20-sp-remote');

    // Run AuthProc filters
    list($targeted_id, $attribute_hash, $attributes_new) = driveProcessingChain(
        $idp_metadata,
        $source,
        $sp_metadata,
        $sp_entityid,
        $attributes,
        $userid,
        $hashAttributes,
        $excludeAttributes
    );

    // Add a consent (or update if attributes have changed and old consent for SP and IdP exists)
    if ($action == 'true') {
        $isStored = $consent_storage->saveConsent($hashed_user_id, $targeted_id, $attribute_hash);
        if ($isStored) {
            $res = $translator->t("added");
        } else {
            $res = $translator->t("updated");
        }
        // Remove consent
    } else {
        if ($action == 'false') {
            // Got consent, so this is a request to remove it
            $rowcount = $consent_storage->deleteConsent($hashed_user_id, $targeted_id);
            if ($rowcount > 0) {
                $res = $translator->t("removed");
            } else {
                throw new \Exception("Unknown action (should not happen)");
            }
        } else {
            \SimpleSAML\Logger::info('consentAdmin: unknown action');
            $res = $translator->t("unknown");
        }
    }
    $template->data['res'] = $res;
    $template->show();
    exit;
}

// Get all consents for user
$user_consent_list = $consent_storage->getConsents($hashed_user_id);

// Parse list of consents
$user_consent = [];
foreach ($user_consent_list as $c) {
    $user_consent[$c[0]] = $c[1];
}

$template_sp_content = [];

// Init template
$template = new \SimpleSAML\XHTML\Template($config, 'consentAdmin:consentadmin.php', 'consentAdmin:consentadmin');
$translator = $template->getTranslator();
$translator->includeLanguageFile('attributes'); // attribute listings translated by this dictionary

$sp_empty_description = $translator->getTag('sp_empty_description');
$sp_list = [];

// Process consents for all SP
foreach ($all_sp_metadata as $sp_entityid => $sp_values) {
    // Get metadata for SP
    $sp_metadata = $metadata->getMetaData($sp_entityid, 'saml20-sp-remote');

    // Run attribute filters
    list($targeted_id, $attribute_hash, $attributes_new) = driveProcessingChain(
        $idp_metadata,
        $source,
        $sp_metadata,
        $sp_entityid,
        $attributes,
        $userid,
        $hashAttributes,
        $excludeAttributes
    );

    // Translate attribute-names
    foreach ($attributes_new as $orig_name => $value) {
        if (isset($template->data['attribute_'.htmlspecialchars(strtolower($orig_name))])) {
            $old_name = $template->data['attribute_'.htmlspecialchars(strtolower($orig_name))];
        }
        $name = $translator->getAttributeTranslation(strtolower($orig_name)); // translate

        $attributes_new[$name] = $value;
        unset($attributes_new[$orig_name]);
    }

    // Check if consent exists
    if (array_key_exists($targeted_id, $user_consent)) {
        $sp_status = "changed";
        \SimpleSAML\Logger::info('consentAdmin: changed');
        // Check if consent is valid. (Possible that attributes has changed)
        if ($user_consent[$targeted_id] == $attribute_hash) {
            \SimpleSAML\Logger::info('consentAdmin: ok');
            $sp_status = "ok";
        }
        // Consent does not exist
    } else {
        SimpleSAML\Logger::info('consentAdmin: none');
        $sp_status = "none";
    }

    // Set name of SP
    if (isset($sp_values['name']) && is_array($sp_values['name'])) {
        $sp_name = $sp_metadata['name'];
    } else {
        if (isset($sp_values['name']) && is_string($sp_values['name'])) {
            $sp_name = $sp_metadata['name'];
        } elseif (isset($sp_values['OrganizationDisplayName']) && is_array($sp_values['OrganizationDisplayName'])) {
            $sp_name = $sp_metadata['OrganizationDisplayName'];
        } else {
            $sp_name = $sp_entityid;
        }
    }

    // Set description of SP
    if (empty($sp_metadata['description']) || !is_array($sp_metadata['description'])) {
        $sp_description = $sp_empty_description;
    } else {
        $sp_description = $sp_metadata['description'];
    }

    // Add a URL to the service if present in metadata
    $sp_service_url = isset($sp_metadata['ServiceURL']) ? $sp_metadata['ServiceURL'] : null;

    // Translate SP name and description
    $translator->includeInlineTranslation('spname', $sp_name);
    $translator->includeInlineTranslation('spdescription', $sp_description);

    $sp_name = $translator->getPreferredTranslation($translator->getTag('spname'));
    $sp_description = $translator->getPreferredTranslation($translator->getTag('spdescription'));

    // Fill out array for the template
    $sp_list[$sp_entityid] = [
        'spentityid'       => $sp_entityid,
        'name'             => $sp_name,
        'description'      => $sp_description,
        'consentStatus'    => $sp_status,
        'consentValue'     => $sp_entityid,
        'attributes_by_sp' => $attributes_new,
        'serviceurl'       => $sp_service_url,
    ];
}

$template->data['header'] = 'Consent Administration';
$template->data['spList'] = $sp_list;
$template->data['showDescription'] = $cA_config->getValue('showDescription');
$template->show();
