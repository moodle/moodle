<?php
/**
 * Consent script
 *
 * This script displays a page to the user, which requests that the user
 * authorizes the release of attributes.
 *
 * @package SimpleSAMLphp
 *
 * Explicit instruct consent page to send no-cache header to browsers to make
 * sure the users attribute information are not store on client disk.
 *
 * In an vanilla apache-php installation is the php variables set to:
 *
 * session.cache_limiter = nocache
 *
 * so this is just to make sure.
 */
session_cache_limiter('nocache');

$globalConfig = \SimpleSAML\Configuration::getInstance();

\SimpleSAML\Logger::info('Consent - getconsent: Accessing consent interface');

if (!array_key_exists('StateId', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest(
        'Missing required StateId query parameter.'
    );
}

$id = $_REQUEST['StateId'];
$state = \SimpleSAML\Auth\State::loadState($id, 'consent:request');

if (is_null($state)) {
    throw new \SimpleSAML\Error\NoState;
} elseif (array_key_exists('core:SP', $state)) {
    $spentityid = $state['core:SP'];
} elseif (array_key_exists('saml:sp:State', $state)) {
    $spentityid = $state['saml:sp:State']['core:SP'];
} else {
    $spentityid = 'UNKNOWN';
}


// The user has pressed the yes-button
if (array_key_exists('yes', $_REQUEST)) {
    if (array_key_exists('saveconsent', $_REQUEST)) {
        \SimpleSAML\Logger::stats('consentResponse remember');
    } else {
        \SimpleSAML\Logger::stats('consentResponse rememberNot');
    }

    $statsInfo = [
        'remember' => array_key_exists('saveconsent', $_REQUEST),
    ];
    if (isset($state['Destination']['entityid'])) {
        $statsInfo['spEntityID'] = $state['Destination']['entityid'];
    }
    \SimpleSAML\Stats::log('consent:accept', $statsInfo);

    if (array_key_exists('consent:store', $state)
        && array_key_exists('saveconsent', $_REQUEST)
        && $_REQUEST['saveconsent'] === '1'
    ) {
        // Save consent
        $store = $state['consent:store'];
        $userId = $state['consent:store.userId'];
        $targetedId = $state['consent:store.destination'];
        $attributeSet = $state['consent:store.attributeSet'];

        \SimpleSAML\Logger::debug(
            'Consent - saveConsent() : ['.$userId.'|'.$targetedId.'|'.$attributeSet.']'
        );
        try {
            $store->saveConsent($userId, $targetedId, $attributeSet);
        } catch (\Exception $e) {
            \SimpleSAML\Logger::error('Consent: Error writing to storage: '.$e->getMessage());
        }
    }

    \SimpleSAML\Auth\ProcessingChain::resumeProcessing($state);
}

// Prepare attributes for presentation
$attributes = $state['Attributes'];
$noconsentattributes = $state['consent:noconsentattributes'];

// Remove attributes that do not require consent
foreach ($attributes as $attrkey => $attrval) {
    if (in_array($attrkey, $noconsentattributes, true)) {
        unset($attributes[$attrkey]);
    }
}
$para = [
    'attributes' => &$attributes
];

// Reorder attributes according to attributepresentation hooks
\SimpleSAML\Module::callHooks('attributepresentation', $para);

// Parse parameters
if (array_key_exists('name', $state['Source'])) {
    $srcName = $state['Source']['name'];
} elseif (array_key_exists('OrganizationDisplayName', $state['Source'])) {
    $srcName = $state['Source']['OrganizationDisplayName'];
} else {
    $srcName = $state['Source']['entityid'];
}

if (array_key_exists('name', $state['Destination'])) {
    $dstName = $state['Destination']['name'];
} elseif (array_key_exists('OrganizationDisplayName', $state['Destination'])) {
    $dstName = $state['Destination']['OrganizationDisplayName'];
} else {
    $dstName = $state['Destination']['entityid'];
}

// Make, populate and layout consent form
$t = new \SimpleSAML\XHTML\Template($globalConfig, 'consent:consentform.php');
$translator = $t->getTranslator();
$t->data['srcMetadata'] = $state['Source'];
$t->data['dstMetadata'] = $state['Destination'];
$t->data['yesTarget'] = \SimpleSAML\Module::getModuleURL('consent/getconsent.php');
$t->data['yesData'] = ['StateId' => $id];
$t->data['noTarget'] = \SimpleSAML\Module::getModuleURL('consent/noconsent.php');
$t->data['noData'] = ['StateId' => $id];
$t->data['attributes'] = $attributes;
$t->data['checked'] = $state['consent:checked'];
$t->data['stateId'] = $id;

$srcName = htmlspecialchars(is_array($srcName) ? $translator->t($srcName) : $srcName);
$dstName = htmlspecialchars(is_array($dstName) ? $translator->t($dstName) : $dstName);

$t->data['consent_attributes_header'] = $translator->t(
    '{consent:consent:consent_attributes_header}',
    ['SPNAME' => $dstName, 'IDPNAME' => $srcName]
);

$t->data['consent_accept'] = $translator->t(
    '{consent:consent:consent_accept}',
    ['SPNAME' => $dstName, 'IDPNAME' => $srcName]
);

if (array_key_exists('descr_purpose', $state['Destination'])) {
    $t->data['consent_purpose'] = $translator->t(
        '{consent:consent:consent_purpose}',
        [
            'SPNAME' => $dstName,
            'SPDESC' => $translator->getPreferredTranslation(
                \SimpleSAML\Utils\Arrays::arrayize(
                    $state['Destination']['descr_purpose'],
                    'en'
                )
            ),
        ]
    );
}

$t->data['srcName'] = $srcName;
$t->data['dstName'] = $dstName;

// Fetch privacypolicy
if (array_key_exists('UIInfo', $state['Destination']) && array_key_exists('PrivacyStatementURL', $state['Destination']['UIInfo']) && (!empty($state['Destination']['UIInfo']['PrivacyStatementURL']))) {
    $privacypolicy = reset($state['Destination']['UIInfo']['PrivacyStatementURL']);
} elseif (array_key_exists('privacypolicy', $state['Destination'])) {
    $privacypolicy = $state['Destination']['privacypolicy'];
} elseif (array_key_exists('UIInfo', $state['Source']) && array_key_exists('PrivacyStatementURL', $state['Source']['UIInfo']) && (!empty($state['Source']['UIInfo']['PrivacyStatementURL']))) {
    $privacypolicy = reset($state['Source']['UIInfo']['PrivacyStatementURL']);
} elseif (array_key_exists('privacypolicy', $state['Source'])) {
    $privacypolicy = $state['Source']['privacypolicy'];
} else {
    $privacypolicy = false;
}
if ($privacypolicy !== false) {
    $privacypolicy = str_replace(
        '%SPENTITYID%',
        urlencode($spentityid),
        $privacypolicy
    );
}
$t->data['sppp'] = $privacypolicy;

// Set focus element
switch ($state['consent:focus']) {
    case 'yes':
        $t->data['autofocus'] = 'yesbutton';
        break;
    case 'no':
        $t->data['autofocus'] = 'nobutton';
        break;
    case null:
    default:
        break;
}

$t->data['usestorage'] = array_key_exists('consent:store', $state);

if (array_key_exists('consent:hiddenAttributes', $state)) {
    $t->data['hiddenAttributes'] = $state['consent:hiddenAttributes'];
} else {
    $t->data['hiddenAttributes'] = [];
}

$t->data['attributes_html'] = present_attributes($t, $attributes, '');

$t->show();


/**
 * Recursive attribute array listing function
 *
 * @param \SimpleSAML\XHTML\Template $t          Template object
 * @param array                     $attributes Attributes to be presented
 * @param string                    $nameParent Name of parent element
 *
 * @return string HTML representation of the attributes
 */
function present_attributes(\SimpleSAML\XHTML\Template $t, array $attributes, $nameParent)
{
    $translator = $t->getTranslator();

    $alternate = ['odd', 'even'];
    $i = 0;
    $summary = 'summary="'.$translator->t('{consent:consent:table_summary}').'"';

    if (strlen($nameParent) > 0) {
        $parentStr = strtolower($nameParent).'_';
        $str = '<table class="attributes" '.$summary.'>';
    } else {
        $parentStr = '';
        $str = '<table id="table_with_attributes" class="attributes" '.$summary.'>';
        $str .= "\n".'<caption>'.$translator->t('{consent:consent:table_caption}').'</caption>';
    }

    foreach ($attributes as $name => $value) {
        $nameraw = $name;
        $name = $translator->getAttributeTranslation($parentStr.$nameraw);

        if (preg_match('/^child_/', $nameraw)) {
            // insert child table
            $parentName = preg_replace('/^child_/', '', $nameraw);
            foreach ($value as $child) {
                $str .= "\n".'<tr class="odd"><td class="td_odd">'.
                    present_attributes($t, $child, $parentName).'</td></tr>';
            }
        } else {
            // insert values directly

            $str .= "\n".'<tr class="'.$alternate[($i++ % 2)].
                '"><td><span class="attrname">'.htmlspecialchars($name).'</span></td>';

            $isHidden = in_array($nameraw, $t->data['hiddenAttributes'], true);
            if ($isHidden) {
                $hiddenId = \SimpleSAML\Utils\Random::generateID();
                $str .= '<td><span class="attrvalue hidden" id="hidden_'.$hiddenId.'">';
            } else {
                $hiddenId = '';
                $str .= '<td><span class="attrvalue">';
            }

            if (sizeof($value) > 1) {
                // we hawe several values
                $str .= '<ul>';
                foreach ($value as $listitem) {
                    if ($nameraw === 'jpegPhoto') {
                        $str .= '<li><img src="data:image/jpeg;base64,'.
                            htmlspecialchars($listitem).'" alt="User photo" /></li>';
                    } else {
                        $str .= '<li>'.htmlspecialchars($listitem).'</li>';
                    }
                }
                $str .= '</ul>';
            } elseif (isset($value[0])) {
                // we hawe only one value
                if ($nameraw === 'jpegPhoto') {
                    $str .= '<img src="data:image/jpeg;base64,'.
                        htmlspecialchars($value[0]).'" alt="User photo" />';
                } else {
                    $str .= htmlspecialchars($value[0]);
                }
            } // end of if multivalue
            $str .= '</span>';

            if ($isHidden) {
                $str .= '<div class="attrvalue consent_showattribute" id="visible_'.$hiddenId.'">';
                $str .= '... ';
                $str .= '<a class="consent_showattributelink" href="javascript:SimpleSAML_show(\'hidden_'.$hiddenId;
                $str .= '\'); SimpleSAML_hide(\'visible_'.$hiddenId.'\');">';
                $str .= $translator->t('{consent:consent:show_attribute}');
                $str .= '</a>';
                $str .= '</div>';
            }

            $str .= '</td></tr>';
        }       // end else: not child table
    }   // end foreach
    $str .= '</table>';
    return $str;
}
