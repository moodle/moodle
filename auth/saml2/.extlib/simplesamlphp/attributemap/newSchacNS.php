<?php
/**
 * This file provides translations from the deprecated schac namespace provided by TERENA, to the new namespace.
 */

if (!defined('SCHAC_OLD_NS')) {
    define('SCHAC_OLD_NS', 'urn:mace:terena.org:attribute-def:');
}

if (!defined('SCHAC_NEW_NS')) {
    define('SCHAC_NEW_NS', 'urn:schac:attribute-def:');
}

$attributemap = [
    SCHAC_OLD_NS.'schacCountryOfCitizenship' => SCHAC_NEW_NS.'schacCountryOfCitizenship',
    SCHAC_OLD_NS.'schacCountryOfResidence' => SCHAC_NEW_NS.'schacCountryOfResidence',
    SCHAC_OLD_NS.'schacDateOfBirth' => SCHAC_NEW_NS.'schacDateOfBirth',
    SCHAC_OLD_NS.'schacExpiryDate' => SCHAC_NEW_NS.'schacExpiryDate',
    SCHAC_OLD_NS.'schacGender' => SCHAC_NEW_NS.'schacGender',
    SCHAC_OLD_NS.'schacHomeOrganization' => SCHAC_NEW_NS.'schacHomeOrganization',
    SCHAC_OLD_NS.'schacHomeOrganizationType' => SCHAC_NEW_NS.'schacHomeOrganizationType',
    SCHAC_OLD_NS.'schacMotherTongue' => SCHAC_NEW_NS.'schacMotherTongue',
    SCHAC_OLD_NS.'schacPersonalPosition' => SCHAC_NEW_NS.'schacPersonalPosition',
    SCHAC_OLD_NS.'schacPersonalTitle' => SCHAC_NEW_NS.'schacPersonalTitle',
    SCHAC_OLD_NS.'schacPersonalUniqueCode' => SCHAC_NEW_NS.'schacPersonalUniqueCode',
    SCHAC_OLD_NS.'schacPersonalUniqueID' => SCHAC_NEW_NS.'schacPersonalUniqueID',
    SCHAC_OLD_NS.'schacPlaceOfBirth' => SCHAC_NEW_NS.'schacPlaceOfBirth',
    SCHAC_OLD_NS.'schacProjectMembership' => SCHAC_NEW_NS.'schacProjectMembership',
    SCHAC_OLD_NS.'schacProjectSpecificRole' => SCHAC_NEW_NS.'schacProjectSpecificRole',
    SCHAC_OLD_NS.'schacSn1' => SCHAC_NEW_NS.'schacSn1',
    SCHAC_OLD_NS.'schacSn2' => SCHAC_NEW_NS.'schacSn2',
    SCHAC_OLD_NS.'schacUserPresenceID' => SCHAC_NEW_NS.'schacUserPresenceID',
    SCHAC_OLD_NS.'schacUserPrivateAttribute' => SCHAC_NEW_NS.'schacUserPrivateAttribute',
    SCHAC_OLD_NS.'schacUserStatus' => SCHAC_NEW_NS.'schacUserStatus',
    SCHAC_OLD_NS.'schacYearOfBirth' => SCHAC_NEW_NS.'schacYearOfBirth',
];
