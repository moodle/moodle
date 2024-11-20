<?php

if (!isset($_REQUEST['id'])) {
    throw new \SimpleSAML\Error\BadRequest('Missing required parameter: id');
}

/** @psalm-var array $state */
$state = \SimpleSAML\Auth\State::loadState($_REQUEST['id'], 'core:Logout-IFrame');
$idp = \SimpleSAML\IdP::getByState($state);

$associations = $idp->getAssociations();

if (!isset($_REQUEST['cancel'])) {
    \SimpleSAML\Logger::stats('slo-iframe done');
    \SimpleSAML\Stats::log('core:idp:logout-iframe:page', ['type' => 'done']);
    $SPs = $state['core:Logout-IFrame:Associations'];
} else {
    // user skipped global logout
    \SimpleSAML\Logger::stats('slo-iframe skip');
    \SimpleSAML\Stats::log('core:idp:logout-iframe:page', ['type' => 'skip']);
    $SPs = []; // no SPs should have been logged out
    $state['core:Failed'] = true; // mark as partial logout
}

// find the status of all SPs
foreach ($SPs as $assocId => &$sp) {
    $spId = 'logout-iframe-' . sha1($assocId);

    if (isset($_REQUEST[$spId])) {
        $spStatus = $_REQUEST[$spId];
        if ($spStatus === 'completed' || $spStatus === 'failed') {
            $sp['core:Logout-IFrame:State'] = $spStatus;
        }
    }

    if (!isset($associations[$assocId])) {
        $sp['core:Logout-IFrame:State'] = 'completed';
    }
}


// terminate the associations
foreach ($SPs as $assocId => $sp) {
    if ($sp['core:Logout-IFrame:State'] === 'completed') {
        $idp->terminateAssociation($assocId);
    } else {
        \SimpleSAML\Logger::warning('Unable to terminate association with ' . var_export($assocId, true) . '.');
        if (isset($sp['saml:entityID'])) {
            $spId = $sp['saml:entityID'];
        } else {
            $spId = $assocId;
        }
        \SimpleSAML\Logger::stats('slo-iframe-fail ' . $spId);
        \SimpleSAML\Stats::log('core:idp:logout-iframe:spfail', ['sp' => $spId]);
        $state['core:Failed'] = true;
    }
}

// we are done
$idp->finishLogout($state);
