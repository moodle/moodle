<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2018  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

/**
 * @package Kaltura
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/KalturaClientBase.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBaseRestriction extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControl extends KalturaObjectBase
{
	/**
	 * The id of the Access Control Profile
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The name of the Access Control Profile
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * System name of the Access Control Profile
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * The description of the Access Control Profile
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * True if this Conversion Profile is the default
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isDefault = null;

	/**
	 * Array of Access Control Restrictions
	 *
	 * @var array of KalturaBaseRestriction
	 */
	public $restrictions;

	/**
	 * Indicates that the access control profile is new and should be handled using KalturaAccessControlProfile object and accessControlProfile service
	 *
	 * @var bool
	 * @readonly
	 */
	public $containsUnsuportedRestrictions = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaContextTypeHolder extends KalturaObjectBase
{
	/**
	 * The type of the condition context
	 *
	 * @var KalturaContextType
	 */
	public $type = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlContextTypeHolder extends KalturaContextTypeHolder
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlMessage extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $message = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $code = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaRuleAction extends KalturaObjectBase
{
	/**
	 * The type of the action
	 *
	 * @var KalturaRuleActionType
	 * @readonly
	 */
	public $type = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaCondition extends KalturaObjectBase
{
	/**
	 * The type of the access control condition
	 *
	 * @var KalturaConditionType
	 * @readonly
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $not = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRule extends KalturaObjectBase
{
	/**
	 * Short Rule Description
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Rule Custom Data to allow saving rule specific information
	 *
	 * @var string
	 */
	public $ruleData = null;

	/**
	 * Message to be thrown to the player in case the rule is fulfilled
	 *
	 * @var string
	 */
	public $message = null;

	/**
	 * Code to be thrown to the player in case the rule is fulfilled
	 *
	 * @var string
	 */
	public $code = null;

	/**
	 * Actions to be performed by the player in case the rule is fulfilled
	 *
	 * @var array of KalturaRuleAction
	 */
	public $actions;

	/**
	 * Conditions to validate the rule
	 *
	 * @var array of KalturaCondition
	 */
	public $conditions;

	/**
	 * Indicates what contexts should be tested by this rule
	 *
	 * @var array of KalturaContextTypeHolder
	 */
	public $contexts;

	/**
	 * Indicates that this rule is enough and no need to continue checking the rest of the rules
	 *
	 * @var bool
	 */
	public $stopProcessing = null;

	/**
	 * Indicates if we should force ks validation for admin ks users as well
	 *
	 * @var KalturaNullableBoolean
	 */
	public $forceAdminValidation = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlProfile extends KalturaObjectBase
{
	/**
	 * The id of the Access Control Profile
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The name of the Access Control Profile
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * System name of the Access Control Profile
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * The description of the Access Control Profile
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Creation time as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Update time as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * True if this access control profile is the partner default
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isDefault = null;

	/**
	 * Array of access control rules
	 *
	 * @var array of KalturaRule
	 */
	public $rules;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKeyValue extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $key = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlScope extends KalturaObjectBase
{
	/**
	 * URL to be used to test domain conditions.
	 *
	 * @var string
	 */
	public $referrer = null;

	/**
	 * IP to be used to test geographic location conditions.
	 *
	 * @var string
	 */
	public $ip = null;

	/**
	 * Kaltura session to be used to test session and user conditions.
	 *
	 * @var string
	 */
	public $ks = null;

	/**
	 * Browser or client application to be used to test agent conditions.
	 *
	 * @var string
	 */
	public $userAgent = null;

	/**
	 * Unix timestamp (In seconds) to be used to test entry scheduling, keep null to use now.
	 *
	 * @var int
	 */
	public $time = null;

	/**
	 * Indicates what contexts should be tested. No contexts means any context.
	 *
	 * @var array of KalturaAccessControlContextTypeHolder
	 */
	public $contexts;

	/**
	 * Array of hashes to pass to the access control profile scope
	 *
	 * @var array of KalturaKeyValue
	 */
	public $hashes;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportFilter extends KalturaObjectBase
{
	/**
	 * The dimension whose values should be filtered
	 *
	 * @var string
	 */
	public $dimension = null;

	/**
	 * The (comma separated) values to include in the filter
	 *
	 * @var string
	 */
	public $values = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnalyticsFilter extends KalturaObjectBase
{
	/**
	 * Query start time (in local time) MM/dd/yyyy HH:mi
	 *
	 * @var string
	 */
	public $from_time = null;

	/**
	 * Query end time (in local time) MM/dd/yyyy HH:mi
	 *
	 * @var string
	 */
	public $to_time = null;

	/**
	 * Comma separated metrics list
	 *
	 * @var string
	 */
	public $metrics = null;

	/**
	 * Timezone offset from UTC (in minutes)
	 *
	 * @var float
	 */
	public $utcOffset = null;

	/**
	 * Comma separated dimensions list
	 *
	 * @var string
	 */
	public $dimensions = null;

	/**
	 * Array of filters
	 *
	 * @var array of KalturaReportFilter
	 */
	public $filters;

	/**
	 * Query order by metric/dimension
	 *
	 * @var string
	 */
	public $orderBy = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaApiExceptionArg extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAppToken extends KalturaObjectBase
{
	/**
	 * The id of the application token
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * The application token
	 *
	 * @var string
	 * @readonly
	 */
	public $token = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * Creation time as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Update time as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Application token status
	 *
	 * @var KalturaAppTokenStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * Expiry time of current token (unix timestamp in seconds)
	 *
	 * @var int
	 */
	public $expiry = null;

	/**
	 * Type of KS (Kaltura Session) that created using the current token
	 *
	 * @var KalturaSessionType
	 */
	public $sessionType = null;

	/**
	 * User id of KS (Kaltura Session) that created using the current token
	 *
	 * @var string
	 */
	public $sessionUserId = null;

	/**
	 * Expiry duration of KS (Kaltura Session) that created using the current token (in seconds)
	 *
	 * @var int
	 */
	public $sessionDuration = null;

	/**
	 * Comma separated privileges to be applied on KS (Kaltura Session) that created using the current token
	 *
	 * @var string
	 */
	public $sessionPrivileges = null;

	/**
	 * 
	 *
	 * @var KalturaAppTokenHashType
	 */
	public $hashType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAsset extends KalturaObjectBase
{
	/**
	 * The ID of the Flavor Asset
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * The entry ID of the Flavor Asset
	 *
	 * @var string
	 * @readonly
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The version of the Flavor Asset
	 *
	 * @var int
	 * @readonly
	 */
	public $version = null;

	/**
	 * The size (in KBytes) of the Flavor Asset
	 *
	 * @var int
	 * @readonly
	 */
	public $size = null;

	/**
	 * Tags used to identify the Flavor Asset in various scenarios
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * The file extension
	 *
	 * @var string
	 * @insertonly
	 */
	public $fileExt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $deletedAt = null;

	/**
	 * System description, error message, warnings and failure cause.
	 *
	 * @var string
	 * @readonly
	 */
	public $description = null;

	/**
	 * Partner private data
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * Partner friendly description
	 *
	 * @var string
	 */
	public $partnerDescription = null;

	/**
	 * Comma separated list of source flavor params ids
	 *
	 * @var string
	 */
	public $actualSourceAssetParamsIds = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaString extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetParams extends KalturaObjectBase
{
	/**
	 * The id of the Flavor Params
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * The name of the Flavor Params
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * System name of the Flavor Params
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * The description of the Flavor Params
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * True if those Flavor Params are part of system defaults
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $isSystemDefault = null;

	/**
	 * The Flavor Params tags are used to identify the flavor for different usage (e.g. web, hd, mobile)
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * Array of partner permisison names that required for using this asset params
	 *
	 * @var array of KalturaString
	 */
	public $requiredPermissions;

	/**
	 * Id of remote storage profile that used to get the source, zero indicates Kaltura data center
	 *
	 * @var int
	 */
	public $sourceRemoteStorageProfileId = null;

	/**
	 * Comma seperated ids of remote storage profiles that the flavor distributed to, the distribution done by the conversion engine
	 *
	 * @var int
	 */
	public $remoteStorageProfileIds = null;

	/**
	 * Media parser type to be used for post-conversion validation
	 *
	 * @var KalturaMediaParserType
	 */
	public $mediaParserType = null;

	/**
	 * Comma seperated ids of source flavor params this flavor is created from
	 *
	 * @var string
	 */
	public $sourceAssetParamsIds = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaResource extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaContentResource extends KalturaResource
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetParamsResourceContainer extends KalturaResource
{
	/**
	 * The content resource to associate with asset params
	 *
	 * @var KalturaContentResource
	 */
	public $resource;

	/**
	 * The asset params to associate with the reaource
	 *
	 * @var int
	 */
	public $assetParamsId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetServeOptions extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $download = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referrer = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaOperationAttributes extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntry extends KalturaObjectBase
{
	/**
	 * Auto generated 10 characters alphanumeric string
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * Entry name (Min 1 chars)
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Entry description
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The ID of the user who is the owner of this entry
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * The ID of the user who created this entry
	 *
	 * @var string
	 * @insertonly
	 */
	public $creatorId = null;

	/**
	 * Entry tags
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * Entry admin tags can be updated only by administrators
	 *
	 * @var string
	 */
	public $adminTags = null;

	/**
	 * Comma separated list of full names of categories to which this entry belongs. Only categories that don't have entitlement (privacy context) are listed, to retrieve the full list of categories, use the categoryEntry.list action.
	 *
	 * @var string
	 */
	public $categories = null;

	/**
	 * Comma separated list of ids of categories to which this entry belongs. Only categories that don't have entitlement (privacy context) are listed, to retrieve the full list of categories, use the categoryEntry.list action.
	 *
	 * @var string
	 */
	public $categoriesIds = null;

	/**
	 * 
	 *
	 * @var KalturaEntryStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * Entry moderation status
	 *
	 * @var KalturaEntryModerationStatus
	 * @readonly
	 */
	public $moderationStatus = null;

	/**
	 * Number of moderation requests waiting for this entry
	 *
	 * @var int
	 * @readonly
	 */
	public $moderationCount = null;

	/**
	 * The type of the entry, this is auto filled by the derived entry object
	 *
	 * @var KalturaEntryType
	 */
	public $type = null;

	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Entry update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * The calculated average rank. rank = totalRank / votes
	 *
	 * @var float
	 * @readonly
	 */
	public $rank = null;

	/**
	 * The sum of all rank values submitted to the baseEntry.anonymousRank action
	 *
	 * @var int
	 * @readonly
	 */
	public $totalRank = null;

	/**
	 * A count of all requests made to the baseEntry.anonymousRank action
	 *
	 * @var int
	 * @readonly
	 */
	public $votes = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $groupId = null;

	/**
	 * Can be used to store various partner related data as a string
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * Download URL for the entry
	 *
	 * @var string
	 * @readonly
	 */
	public $downloadUrl = null;

	/**
	 * Indexed search text for full text search
	 *
	 * @var string
	 * @readonly
	 */
	public $searchText = null;

	/**
	 * License type used for this entry
	 *
	 * @var KalturaLicenseType
	 */
	public $licenseType = null;

	/**
	 * Version of the entry data
	 *
	 * @var int
	 * @readonly
	 */
	public $version = null;

	/**
	 * Thumbnail URL
	 *
	 * @var string
	 * @readonly
	 */
	public $thumbnailUrl = null;

	/**
	 * The Access Control ID assigned to this entry (null when not set, send -1 to remove)
	 *
	 * @var int
	 */
	public $accessControlId = null;

	/**
	 * Entry scheduling start date (null when not set, send -1 to remove)
	 *
	 * @var int
	 */
	public $startDate = null;

	/**
	 * Entry scheduling end date (null when not set, send -1 to remove)
	 *
	 * @var int
	 */
	public $endDate = null;

	/**
	 * Entry external reference id
	 *
	 * @var string
	 */
	public $referenceId = null;

	/**
	 * ID of temporary entry that will replace this entry when it's approved and ready for replacement
	 *
	 * @var string
	 * @readonly
	 */
	public $replacingEntryId = null;

	/**
	 * ID of the entry that will be replaced when the replacement approved and this entry is ready
	 *
	 * @var string
	 * @readonly
	 */
	public $replacedEntryId = null;

	/**
	 * Status of the replacement readiness and approval
	 *
	 * @var KalturaEntryReplacementStatus
	 * @readonly
	 */
	public $replacementStatus = null;

	/**
	 * Can be used to store various partner related data as a numeric value
	 *
	 * @var int
	 */
	public $partnerSortValue = null;

	/**
	 * Override the default ingestion profile
	 *
	 * @var int
	 */
	public $conversionProfileId = null;

	/**
	 * IF not empty, points to an entry ID the should replace this current entry's id.
	 *
	 * @var string
	 */
	public $redirectEntryId = null;

	/**
	 * ID of source root entry, used for clipped, skipped and cropped entries that created from another entry
	 *
	 * @var string
	 * @readonly
	 */
	public $rootEntryId = null;

	/**
	 * ID of source root entry, used for defining entires association
	 *
	 * @var string
	 */
	public $parentEntryId = null;

	/**
	 * clipping, skipping and cropping attributes that used to create this entry
	 *
	 * @var array of KalturaOperationAttributes
	 */
	public $operationAttributes;

	/**
	 * list of user ids that are entitled to edit the entry (no server enforcement) The difference between entitledUsersEdit, entitledUsersPublish and entitledUsersView is applicative only
	 *
	 * @var string
	 */
	public $entitledUsersEdit = null;

	/**
	 * list of user ids that are entitled to publish the entry (no server enforcement) The difference between entitledUsersEdit, entitledUsersPublish and entitledUsersView is applicative only
	 *
	 * @var string
	 */
	public $entitledUsersPublish = null;

	/**
	 * list of user ids that are entitled to view the entry (no server enforcement) The difference between entitledUsersEdit, entitledUsersPublish and entitledUsersView is applicative only
	 *
	 * @var string
	 */
	public $entitledUsersView = null;

	/**
	 * Comma seperated string of the capabilities of the entry. Any capability needed can be added to this list.
	 *
	 * @var string
	 * @readonly
	 */
	public $capabilities = null;

	/**
	 * Template entry id
	 *
	 * @var string
	 * @insertonly
	 */
	public $templateEntryId = null;

	/**
	 * should we display this entry in search
	 *
	 * @var KalturaEntryDisplayInSearchType
	 */
	public $displayInSearch = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBaseEntryCloneOptionItem extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBaseResponseProfile extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBaseSyndicationFeed extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $feedUrl = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * link a playlist that will set what content the feed will include
	 * 	 if empty, all content will be included in feed
	 *
	 * @var string
	 */
	public $playlistId = null;

	/**
	 * feed name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * feed status
	 *
	 * @var KalturaSyndicationFeedStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * feed type
	 *
	 * @var KalturaSyndicationFeedType
	 * @insertonly
	 */
	public $type = null;

	/**
	 * Base URL for each video, on the partners site
	 * 	 This is required by all syndication types.
	 *
	 * @var string
	 */
	public $landingPage = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * allow_embed tells google OR yahoo weather to allow embedding the video on google OR yahoo video results
	 * 	 or just to provide a link to the landing page.
	 * 	 it is applied on the video-player_loc property in the XML (google)
	 * 	 and addes media-player tag (yahoo)
	 *
	 * @var bool
	 */
	public $allowEmbed = null;

	/**
	 * Select a uiconf ID as player skin to include in the kwidget url
	 *
	 * @var int
	 */
	public $playerUiconfId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamId = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $transcodeExistingContent = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $addToDefaultConversionProfile = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categories = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $storageId = null;

	/**
	 * 
	 *
	 * @var KalturaSyndicationFeedEntriesOrderBy
	 */
	public $entriesOrderBy = null;

	/**
	 * Should enforce entitlement on feed entries
	 *
	 * @var bool
	 */
	public $enforceEntitlement = null;

	/**
	 * Set privacy context for search entries that assiged to private and public categories within a category privacy context.
	 *
	 * @var string
	 */
	public $privacyContext = null;

	/**
	 * Update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $useCategoryEntries = null;

	/**
	 * Feed content-type header value
	 *
	 * @var string
	 */
	public $feedContentTypeHeader = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaJobData extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchHistoryData extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchIndex = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $timeStamp = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $message = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errNumber = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $hostName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sessionId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchJob extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $deletedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $lockExpiration = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $executionAttempts = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $lockVersion = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryName = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobType
	 * @readonly
	 */
	public $jobType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $jobSubType = null;

	/**
	 * 
	 *
	 * @var KalturaJobData
	 */
	public $data;

	/**
	 * 
	 *
	 * @var KalturaBatchJobStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $abort = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $checkAgainTimeout = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $message = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priority = null;

	/**
	 * 
	 *
	 * @var array of KalturaBatchHistoryData
	 */
	public $history;

	/**
	 * The id of the bulk upload job that initiated this job
	 *
	 * @var int
	 */
	public $bulkJobId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchVersion = null;

	/**
	 * When one job creates another - the parent should set this parentJobId to be its own id.
	 *
	 * @var int
	 */
	public $parentJobId = null;

	/**
	 * The id of the root parent job
	 *
	 * @var int
	 */
	public $rootJobId = null;

	/**
	 * The time that the job was pulled from the queue
	 *
	 * @var int
	 */
	public $queueTime = null;

	/**
	 * The time that the job was finished or closed as failed
	 *
	 * @var int
	 */
	public $finishTime = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobErrorTypes
	 */
	public $errType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errNumber = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $estimatedEffort = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $urgency = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchIndex = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastSchedulerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastWorkerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dc = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobObjectId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $jobObjectType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlayerDeliveryType extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $label = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $flashvars;

	/**
	 * 
	 *
	 * @var string
	 */
	public $minVersion = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enabledByDefault = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlayerEmbedCodeType extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $label = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $entryOnly = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $minVersion = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaESearchLanguageItem extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaESearchLanguage
	 */
	public $eSerachLanguage = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartner extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $website = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $appearInSearch = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * deprecated - lastName and firstName replaces this field
	 *
	 * @var string
	 */
	public $adminName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var KalturaCommercialUseType
	 */
	public $commercialUse = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $landingPage = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userLandingPage = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentCategories = null;

	/**
	 * 
	 *
	 * @var KalturaPartnerType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $phone = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $describeYourself = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $adultContent = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defConversionProfileType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $notify = null;

	/**
	 * 
	 *
	 * @var KalturaPartnerStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $allowQuickEdit = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mergeEntryLists = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationsConfig = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxUploadSize = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackage = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $secret = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $adminSecret = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $cmsPassword = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $allowMultiNotification = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $adminLoginUsersQuota = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $adminUserId = null;

	/**
	 * firstName and lastName replace the old (deprecated) adminName
	 *
	 * @var string
	 */
	public $firstName = null;

	/**
	 * lastName and firstName replace the old (deprecated) adminName
	 *
	 * @var string
	 */
	public $lastName = null;

	/**
	 * country code (2char) - this field is optional
	 *
	 * @var string
	 */
	public $country = null;

	/**
	 * state code (2char) - this field is optional
	 *
	 * @var string
	 */
	public $state = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 * @insertonly
	 */
	public $additionalParams;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $publishersQuota = null;

	/**
	 * 
	 *
	 * @var KalturaPartnerGroupType
	 * @readonly
	 */
	public $partnerGroupType = null;

	/**
	 * 
	 *
	 * @var bool
	 * @readonly
	 */
	public $defaultEntitlementEnforcement = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $defaultDeliveryType = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $defaultEmbedCodeType = null;

	/**
	 * 
	 *
	 * @var array of KalturaPlayerDeliveryType
	 * @readonly
	 */
	public $deliveryTypes;

	/**
	 * 
	 *
	 * @var array of KalturaPlayerEmbedCodeType
	 * @readonly
	 */
	public $embedCodeTypes;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $templatePartnerId = null;

	/**
	 * 
	 *
	 * @var bool
	 * @readonly
	 */
	public $ignoreSeoLinks = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $host = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $cdnHost = null;

	/**
	 * 
	 *
	 * @var bool
	 * @readonly
	 */
	public $isFirstLogin = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $logoutUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerParentId = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $crmId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceId = null;

	/**
	 * 
	 *
	 * @var bool
	 * @readonly
	 */
	public $timeAlignedRenditions = null;

	/**
	 * 
	 *
	 * @var array of KalturaESearchLanguageItem
	 */
	public $eSearchLanguages;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $publisherEnvironmentType = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $ovpEnvironmentUrl = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $ottEnvironmentUrl = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaValue extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBooleanValue extends KalturaValue
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadPluginData extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $field = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadResult extends KalturaObjectBase
{
	/**
	 * The id of the result
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The id of the parent job
	 *
	 * @var int
	 */
	public $bulkUploadJobId = null;

	/**
	 * The index of the line in the CSV
	 *
	 * @var int
	 */
	public $lineIndex = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadResultStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadAction
	 */
	public $action = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $objectStatus = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadObjectType
	 */
	public $bulkUploadResultObjectType = null;

	/**
	 * The data as recieved in the csv
	 *
	 * @var string
	 */
	public $rowData = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectErrorDescription = null;

	/**
	 * 
	 *
	 * @var array of KalturaBulkUploadPluginData
	 */
	public $pluginsData;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorDescription = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorCode = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errorType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUpload extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $uploadedBy = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $uploadedByUserId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uploadedOn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $numOfEntries = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $logFileUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $csvFileUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bulkFileUrl = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadType
	 */
	public $bulkUploadType = null;

	/**
	 * 
	 *
	 * @var array of KalturaBulkUploadResult
	 */
	public $results;

	/**
	 * 
	 *
	 * @var string
	 */
	public $error = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobErrorTypes
	 */
	public $errorType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errorNumber = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $numOfObjects = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadObjectType
	 */
	public $bulkUploadObjectType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBulkUploadObjectData extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCEError extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $browser = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverIp = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverOs = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $phpVersion = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ceAdminEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $data = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategory extends KalturaObjectBase
{
	/**
	 * The id of the Category
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $parentId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $depth = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The name of the Category. 
	 * 	 The following characters are not allowed: '<', '>', ','
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * The full name of the Category
	 *
	 * @var string
	 * @readonly
	 */
	public $fullName = null;

	/**
	 * The full ids of the Category
	 *
	 * @var string
	 * @readonly
	 */
	public $fullIds = null;

	/**
	 * Number of entries in this Category (including child categories)
	 *
	 * @var int
	 * @readonly
	 */
	public $entriesCount = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Category description
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Category tags
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * If category will be returned for list action.
	 *
	 * @var KalturaAppearInListType
	 */
	public $appearInList = null;

	/**
	 * defines the privacy of the entries that assigned to this category
	 *
	 * @var KalturaPrivacyType
	 */
	public $privacy = null;

	/**
	 * If Category members are inherited from parent category or set manualy.
	 *
	 * @var KalturaInheritanceType
	 */
	public $inheritanceType = null;

	/**
	 * Who can ask to join this category
	 *
	 * @var KalturaUserJoinPolicyType
	 * @readonly
	 */
	public $userJoinPolicy = null;

	/**
	 * Default permissionLevel for new users
	 *
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $defaultPermissionLevel = null;

	/**
	 * Category Owner (User id)
	 *
	 * @var string
	 */
	public $owner = null;

	/**
	 * Number of entries that belong to this category directly
	 *
	 * @var int
	 * @readonly
	 */
	public $directEntriesCount = null;

	/**
	 * Category external id, controlled and managed by the partner.
	 *
	 * @var string
	 */
	public $referenceId = null;

	/**
	 * who can assign entries to this category
	 *
	 * @var KalturaContributionPolicyType
	 */
	public $contributionPolicy = null;

	/**
	 * Number of active members for this category
	 *
	 * @var int
	 * @readonly
	 */
	public $membersCount = null;

	/**
	 * Number of pending members for this category
	 *
	 * @var int
	 * @readonly
	 */
	public $pendingMembersCount = null;

	/**
	 * Set privacy context for search entries that assiged to private and public categories. the entries will be private if the search context is set with those categories.
	 *
	 * @var string
	 */
	public $privacyContext = null;

	/**
	 * comma separated parents that defines a privacyContext for search
	 *
	 * @var string
	 * @readonly
	 */
	public $privacyContexts = null;

	/**
	 * Status
	 *
	 * @var KalturaCategoryStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * The category id that this category inherit its members and members permission (for contribution and join)
	 *
	 * @var int
	 * @readonly
	 */
	public $inheritedParentId = null;

	/**
	 * Can be used to store various partner related data as a numeric value
	 *
	 * @var int
	 */
	public $partnerSortValue = null;

	/**
	 * Can be used to store various partner related data as a string
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * Enable client side applications to define how to sort the category child categories
	 *
	 * @var KalturaCategoryOrderBy
	 */
	public $defaultOrderBy = null;

	/**
	 * Number of direct children categories
	 *
	 * @var int
	 * @readonly
	 */
	public $directSubCategoriesCount = null;

	/**
	 * Moderation to add entries to this category by users that are not of permission level Manager or Moderator.
	 *
	 * @var KalturaNullableBoolean
	 */
	public $moderation = null;

	/**
	 * Nunber of pending moderation entries
	 *
	 * @var int
	 * @readonly
	 */
	public $pendingEntriesCount = null;

	/**
	 * Flag indicating that the category is an aggregation category
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isAggregationCategory = null;

	/**
	 * List of aggregation channels the category belongs to
	 *
	 * @var string
	 */
	public $aggregationCategories = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryEntry extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryId = null;

	/**
	 * entry id
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * The full ids of the Category
	 *
	 * @var string
	 * @readonly
	 */
	public $categoryFullIds = null;

	/**
	 * CategroyEntry status
	 *
	 * @var KalturaCategoryEntryStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * CategroyEntry creator puser ID
	 *
	 * @var string
	 * @readonly
	 */
	public $creatorUserId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryUser extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @insertonly
	 */
	public $categoryId = null;

	/**
	 * User id
	 *
	 * @var string
	 * @insertonly
	 */
	public $userId = null;

	/**
	 * Partner id
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * Permission level
	 *
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $permissionLevel = null;

	/**
	 * Status
	 *
	 * @var KalturaCategoryUserStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * CategoryUser creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * CategoryUser update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Update method can be either manual or automatic to distinguish between manual operations (for example in KMC) on automatic - using bulk upload
	 *
	 * @var KalturaUpdateMethodType
	 */
	public $updateMethod = null;

	/**
	 * The full ids of the Category
	 *
	 * @var string
	 * @readonly
	 */
	public $categoryFullIds = null;

	/**
	 * Set of category-related permissions for the current category user.
	 *
	 * @var string
	 */
	public $permissionNames = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaClientConfiguration extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $clientTag = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $apiVersion = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaClientNotification extends KalturaObjectBase
{
	/**
	 * The URL where the notification should be sent to
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * The serialized notification data to send
	 *
	 * @var string
	 */
	public $data = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaClipDescription extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceEntryId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startTime = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $duration = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $offsetInDestination = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaContext extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaContextDataResult extends KalturaObjectBase
{
	/**
	 * Array of messages as received from the rules that invalidated
	 *
	 * @var array of KalturaString
	 */
	public $messages;

	/**
	 * Array of actions as received from the rules that invalidated
	 *
	 * @var array of KalturaRuleAction
	 */
	public $actions;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaControlPanelCommand extends KalturaObjectBase
{
	/**
	 * The id of the Category
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Creator name
	 *
	 * @var string
	 */
	public $createdBy = null;

	/**
	 * Update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Updater name
	 *
	 * @var string
	 */
	public $updatedBy = null;

	/**
	 * Creator id
	 *
	 * @var int
	 */
	public $createdById = null;

	/**
	 * The id of the scheduler that the command refers to
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * The id of the scheduler worker that the command refers to
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * The id of the scheduler worker as configured in the ini file
	 *
	 * @var int
	 */
	public $workerConfiguredId = null;

	/**
	 * The name of the scheduler worker that the command refers to
	 *
	 * @var int
	 */
	public $workerName = null;

	/**
	 * The index of the batch process that the command refers to
	 *
	 * @var int
	 */
	public $batchIndex = null;

	/**
	 * The command type - stop / start / config
	 *
	 * @var KalturaControlPanelCommandType
	 */
	public $type = null;

	/**
	 * The command target type - data center / scheduler / job / job type
	 *
	 * @var KalturaControlPanelCommandTargetType
	 */
	public $targetType = null;

	/**
	 * The command status
	 *
	 * @var KalturaControlPanelCommandStatus
	 */
	public $status = null;

	/**
	 * The reason for the command
	 *
	 * @var string
	 */
	public $cause = null;

	/**
	 * Command description
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Error description
	 *
	 * @var string
	 */
	public $errorDescription = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionAttribute extends KalturaObjectBase
{
	/**
	 * The id of the flavor params, set to null for source flavor
	 *
	 * @var int
	 */
	public $flavorParamsId = null;

	/**
	 * Attribute name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Attribute value
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCropDimensions extends KalturaObjectBase
{
	/**
	 * Crop left point
	 *
	 * @var int
	 */
	public $left = null;

	/**
	 * Crop top point
	 *
	 * @var int
	 */
	public $top = null;

	/**
	 * Crop width
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * Crop height
	 *
	 * @var int
	 */
	public $height = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaPluginReplacementOptionsItem extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryReplacementOptions extends KalturaObjectBase
{
	/**
	 * If true manually created thumbnails will not be deleted on entry replacement
	 *
	 * @var int
	 */
	public $keepManualThumbnails = null;

	/**
	 * Array of plugin replacement options
	 *
	 * @var array of KalturaPluginReplacementOptionsItem
	 */
	public $pluginOptionItems;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfile extends KalturaObjectBase
{
	/**
	 * The id of the Conversion Profile
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var KalturaConversionProfileStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaConversionProfileType
	 * @insertonly
	 */
	public $type = null;

	/**
	 * The name of the Conversion Profile
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * System name of the Conversion Profile
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * Comma separated tags
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * The description of the Conversion Profile
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * ID of the default entry to be used for template data
	 *
	 * @var string
	 */
	public $defaultEntryId = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * List of included flavor ids (comma separated)
	 *
	 * @var string
	 */
	public $flavorParamsIds = null;

	/**
	 * Indicates that this conversion profile is system default
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isDefault = null;

	/**
	 * Indicates that this conversion profile is partner default
	 *
	 * @var bool
	 * @readonly
	 */
	public $isPartnerDefault = null;

	/**
	 * Cropping dimensions
	 *
	 * @var KalturaCropDimensions
	 */
	public $cropDimensions;

	/**
	 * Clipping start position (in miliseconds)
	 *
	 * @var int
	 */
	public $clipStart = null;

	/**
	 * Clipping duration (in miliseconds)
	 *
	 * @var int
	 */
	public $clipDuration = null;

	/**
	 * XSL to transform ingestion MRSS XML
	 *
	 * @var string
	 */
	public $xslTransformation = null;

	/**
	 * ID of default storage profile to be used for linked net-storage file syncs
	 *
	 * @var int
	 */
	public $storageProfileId = null;

	/**
	 * Media parser type to be used for extract media
	 *
	 * @var KalturaMediaParserType
	 */
	public $mediaParserType = null;

	/**
	 * Should calculate file conversion complexity
	 *
	 * @var KalturaNullableBoolean
	 */
	public $calculateComplexity = null;

	/**
	 * Defines the tags that should be used to define 'collective'/group/multi-flavor processing,
	 * 	 like 'mbr' or 'ism'
	 *
	 * @var string
	 */
	public $collectionTags = null;

	/**
	 * JSON string with array of "condition,profile-id" pairs.
	 *
	 * @var string
	 */
	public $conditionalProfiles = null;

	/**
	 * When set, the ExtractMedia job should detect the source file GOP using this value as the max calculated period
	 *
	 * @var int
	 */
	public $detectGOP = null;

	/**
	 * XSL to transform ingestion Media Info XML
	 *
	 * @var string
	 */
	public $mediaInfoXslTransformation = null;

	/**
	 * Default replacement options to be applied to entries
	 *
	 * @var KalturaEntryReplacementOptions
	 */
	public $defaultReplacementOptions;

	/**
	 * 
	 *
	 * @var KalturaLanguage
	 */
	public $defaultAudioLang = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileAssetParams extends KalturaObjectBase
{
	/**
	 * The id of the conversion profile
	 *
	 * @var int
	 * @readonly
	 */
	public $conversionProfileId = null;

	/**
	 * The id of the asset params
	 *
	 * @var int
	 * @readonly
	 */
	public $assetParamsId = null;

	/**
	 * The ingestion origin of the asset params
	 *
	 * @var KalturaFlavorReadyBehaviorType
	 */
	public $readyBehavior = null;

	/**
	 * The ingestion origin of the asset params
	 *
	 * @var KalturaAssetParamsOrigin
	 */
	public $origin = null;

	/**
	 * Asset params system name
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * Starts conversion even if the decision layer reduced the configuration to comply with the source
	 *
	 * @var KalturaNullableBoolean
	 */
	public $forceNoneComplied = null;

	/**
	 * Specifies how to treat the flavor after conversion is finished
	 *
	 * @var KalturaAssetParamsDeletePolicy
	 */
	public $deletePolicy = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isEncrypted = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $contentAwareness = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $chunkedEncodeMode = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $twoPass = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConvertCollectionFlavorData extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsOutputId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $readyBehavior = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoBitrate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $audioBitrate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncRemoteUrl = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCoordinate extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var float
	 */
	public $latitude = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $longitude = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCsvAdditionalFieldInfo extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $fieldName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $xpath = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDataEntry extends KalturaBaseEntry
{
	/**
	 * The data of the entry
	 *
	 * @var string
	 */
	public $dataContent = null;

	/**
	 * indicator whether to return the object for get action with the dataContent field.
	 *
	 * @var bool
	 * @insertonly
	 */
	public $retrieveDataContentByGet = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlRecognizer extends KalturaObjectBase
{
	/**
	 * The hosts that are recognized
	 *
	 * @var string
	 */
	public $hosts = null;

	/**
	 * The URI prefix we use for security
	 *
	 * @var string
	 */
	public $uriPrefix = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizer extends KalturaObjectBase
{
	/**
	 * Window
	 *
	 * @var int
	 */
	public $window = null;

	/**
	 * key
	 *
	 * @var string
	 */
	public $key = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $limitIpAddress = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaSearchItem extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaFilter extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $orderBy = null;

	/**
	 * 
	 *
	 * @var KalturaSearchItem
	 */
	public $advancedSearch;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaRelatedFilter extends KalturaFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAssetBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sizeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sizeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $deletedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $deletedAtLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetFilter extends KalturaAssetBaseFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfile extends KalturaObjectBase
{
	/**
	 * The id of the Delivery
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The name of the Delivery
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Delivery type
	 *
	 * @var KalturaDeliveryProfileType
	 */
	public $type = null;

	/**
	 * System name of the delivery
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * The description of the Delivery
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Creation time as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Update time as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var KalturaPlaybackProtocol
	 */
	public $streamerType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * the host part of the url
	 *
	 * @var string
	 * @readonly
	 */
	public $hostName = null;

	/**
	 * 
	 *
	 * @var KalturaDeliveryStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaUrlRecognizer
	 */
	public $recognizer;

	/**
	 * 
	 *
	 * @var KalturaUrlTokenizer
	 */
	public $tokenizer;

	/**
	 * True if this is the systemwide default for the protocol
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $isDefault = null;

	/**
	 * the object from which this object was cloned (or 0)
	 *
	 * @var int
	 * @readonly
	 */
	public $parentId = null;

	/**
	 * Comma separated list of supported media protocols. f.i. rtmpe
	 *
	 * @var string
	 */
	public $mediaProtocols = null;

	/**
	 * priority used for ordering similar delivery profiles
	 *
	 * @var int
	 */
	public $priority = null;

	/**
	 * Extra query string parameters that should be added to the url
	 *
	 * @var string
	 */
	public $extraParams = null;

	/**
	 * A filter that can be used to include additional assets in the URL (e.g. captions)
	 *
	 * @var KalturaAssetFilter
	 */
	public $supplementaryAssetsFilter;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileSyncDescriptor extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $fileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileEncryptionKey = null;

	/**
	 * The translated path as used by the scheduler
	 *
	 * @var string
	 */
	public $fileSyncRemoteUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSyncObjectSubType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDestFileSyncDescriptor extends KalturaFileSyncDescriptor
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPager extends KalturaObjectBase
{
	/**
	 * The number of objects to retrieve. (Default is 30, maximum page size is 500).
	 *
	 * @var int
	 */
	public $pageSize = null;

	/**
	 * The page number for which {pageSize} of objects should be retrieved (Default is 1).
	 *
	 * @var int
	 */
	public $pageIndex = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFilterPager extends KalturaPager
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfileMapping extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $parentProperty = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $filterProperty = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $allowNull = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDetachedResponseProfile extends KalturaBaseResponseProfile
{
	/**
	 * Friendly name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var KalturaResponseProfileType
	 */
	public $type = null;

	/**
	 * Comma separated fields list to be included or excluded
	 *
	 * @var string
	 */
	public $fields = null;

	/**
	 * 
	 *
	 * @var KalturaRelatedFilter
	 */
	public $filter;

	/**
	 * 
	 *
	 * @var KalturaFilterPager
	 */
	public $pager;

	/**
	 * 
	 *
	 * @var array of KalturaDetachedResponseProfile
	 */
	public $relatedProfiles;

	/**
	 * 
	 *
	 * @var array of KalturaResponseProfileMapping
	 */
	public $mappings;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPluginData extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDrmPlaybackPluginData extends KalturaPluginData
{
	/**
	 * 
	 *
	 * @var KalturaDrmSchemeName
	 */
	public $scheme = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $licenseURL = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailIngestionProfile extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $emailAddress = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $mailboxId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $conversionProfile2Id = null;

	/**
	 * 
	 *
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatus = null;

	/**
	 * 
	 *
	 * @var KalturaEmailIngestionProfileStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultCategory = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultUserId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultTags = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultAdminTags = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxAttachmentSizeKbytes = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxAttachmentsPerMail = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStringValue extends KalturaValue
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaEntryServerNode extends KalturaObjectBase
{
	/**
	 * unique auto-generated identifier
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $serverNodeId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var KalturaEntryServerNodeStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaEntryServerNodeType
	 * @readonly
	 */
	public $serverType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaObjectIdentifier extends KalturaObjectBase
{
	/**
	 * Comma separated string of enum values denoting which features of the item need to be included in the MRSS
	 *
	 * @var string
	 */
	public $extendedFeatures = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExtendingItemMrssParameter extends KalturaObjectBase
{
	/**
	 * XPath for the extending item
	 *
	 * @var string
	 */
	public $xpath = null;

	/**
	 * Object identifier
	 *
	 * @var KalturaObjectIdentifier
	 */
	public $identifier;

	/**
	 * Mode of extension - append to MRSS or replace the xpath content.
	 *
	 * @var KalturaMrssExtensionMode
	 */
	public $extensionMode = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlayableEntry extends KalturaBaseEntry
{
	/**
	 * Number of plays
	 *
	 * @var int
	 * @readonly
	 */
	public $plays = null;

	/**
	 * Number of views
	 *
	 * @var int
	 * @readonly
	 */
	public $views = null;

	/**
	 * The last time the entry was played
	 *
	 * @var int
	 * @readonly
	 */
	public $lastPlayedAt = null;

	/**
	 * The width in pixels
	 *
	 * @var int
	 * @readonly
	 */
	public $width = null;

	/**
	 * The height in pixels
	 *
	 * @var int
	 * @readonly
	 */
	public $height = null;

	/**
	 * The duration in seconds
	 *
	 * @var int
	 * @readonly
	 */
	public $duration = null;

	/**
	 * The duration in miliseconds
	 *
	 * @var int
	 */
	public $msDuration = null;

	/**
	 * The duration type (short for 0-4 mins, medium for 4-20 mins, long for 20+ mins)
	 *
	 * @var KalturaDurationType
	 * @readonly
	 */
	public $durationType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStreamContainer extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $trackIndex = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $language = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $channelIndex = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $label = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelLayout = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaEntry extends KalturaPlayableEntry
{
	/**
	 * The media type of the entry
	 *
	 * @var KalturaMediaType
	 * @insertonly
	 */
	public $mediaType = null;

	/**
	 * Override the default conversion quality
	 *
	 * @var string
	 * @insertonly
	 */
	public $conversionQuality = null;

	/**
	 * The source type of the entry
	 *
	 * @var KalturaSourceType
	 * @insertonly
	 */
	public $sourceType = null;

	/**
	 * The search provider type used to import this entry
	 *
	 * @var KalturaSearchProviderType
	 * @insertonly
	 */
	public $searchProviderType = null;

	/**
	 * The ID of the media in the importing site
	 *
	 * @var string
	 * @insertonly
	 */
	public $searchProviderId = null;

	/**
	 * The user name used for credits
	 *
	 * @var string
	 */
	public $creditUserName = null;

	/**
	 * The URL for credits
	 *
	 * @var string
	 */
	public $creditUrl = null;

	/**
	 * The media date extracted from EXIF data (For images) as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $mediaDate = null;

	/**
	 * The URL used for playback. This is not the download URL.
	 *
	 * @var string
	 * @readonly
	 */
	public $dataUrl = null;

	/**
	 * Comma separated flavor params ids that exists for this media entry
	 *
	 * @var string
	 * @readonly
	 */
	public $flavorParamsIds = null;

	/**
	 * True if trim action is disabled for this entry
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $isTrimDisabled = null;

	/**
	 * Array of streams that exists on the entry
	 *
	 * @var array of KalturaStreamContainer
	 */
	public $streams;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFeatureStatus extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaFeatureStatusType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileAsset extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var KalturaFileAssetObjectType
	 * @insertonly
	 */
	public $fileAssetObjectType = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $objectId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileExt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $version = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var KalturaFileAssetStatus
	 * @readonly
	 */
	public $status = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileContainer extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $filePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $encryptionKey = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSize = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorAsset extends KalturaAsset
{
	/**
	 * The Flavor Params used to create this Flavor Asset
	 *
	 * @var int
	 * @insertonly
	 */
	public $flavorParamsId = null;

	/**
	 * The width of the Flavor Asset
	 *
	 * @var int
	 * @readonly
	 */
	public $width = null;

	/**
	 * The height of the Flavor Asset
	 *
	 * @var int
	 * @readonly
	 */
	public $height = null;

	/**
	 * The overall bitrate (in KBits) of the Flavor Asset
	 *
	 * @var int
	 * @readonly
	 */
	public $bitrate = null;

	/**
	 * The frame rate (in FPS) of the Flavor Asset
	 *
	 * @var float
	 * @readonly
	 */
	public $frameRate = null;

	/**
	 * True if this Flavor Asset is the original source
	 *
	 * @var bool
	 * @readonly
	 */
	public $isOriginal = null;

	/**
	 * True if this Flavor Asset is playable in KDP
	 *
	 * @var bool
	 * @readonly
	 */
	public $isWeb = null;

	/**
	 * The container format
	 *
	 * @var string
	 * @readonly
	 */
	public $containerFormat = null;

	/**
	 * The video codec
	 *
	 * @var string
	 * @readonly
	 */
	public $videoCodecId = null;

	/**
	 * The status of the Flavor Asset
	 *
	 * @var KalturaFlavorAssetStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * The language of the flavor asset
	 *
	 * @var KalturaLanguage
	 */
	public $language = null;

	/**
	 * The label of the flavor asset
	 *
	 * @var string
	 */
	public $label = null;

	/**
	 * Is default flavor asset of the entry (This field will be taken into account selectign which audio flavor will be selected as default)
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isDefault = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorAssetUrlOptions extends KalturaObjectBase
{
	/**
	 * The name of the downloaded file
	 *
	 * @var string
	 */
	public $fileName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referrer = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorParams extends KalturaAssetParams
{
	/**
	 * The video codec of the Flavor Params
	 *
	 * @var KalturaVideoCodec
	 */
	public $videoCodec = null;

	/**
	 * The video bitrate (in KBits) of the Flavor Params
	 *
	 * @var int
	 */
	public $videoBitrate = null;

	/**
	 * The audio codec of the Flavor Params
	 *
	 * @var KalturaAudioCodec
	 */
	public $audioCodec = null;

	/**
	 * The audio bitrate (in KBits) of the Flavor Params
	 *
	 * @var int
	 */
	public $audioBitrate = null;

	/**
	 * The number of audio channels for "downmixing"
	 *
	 * @var int
	 */
	public $audioChannels = null;

	/**
	 * The audio sample rate of the Flavor Params
	 *
	 * @var int
	 */
	public $audioSampleRate = null;

	/**
	 * The desired width of the Flavor Params
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * The desired height of the Flavor Params
	 *
	 * @var int
	 */
	public $height = null;

	/**
	 * The frame rate of the Flavor Params
	 *
	 * @var float
	 */
	public $frameRate = null;

	/**
	 * The gop size of the Flavor Params
	 *
	 * @var int
	 */
	public $gopSize = null;

	/**
	 * The list of conversion engines (comma separated)
	 *
	 * @var string
	 */
	public $conversionEngines = null;

	/**
	 * The list of conversion engines extra params (separated with "|")
	 *
	 * @var string
	 */
	public $conversionEnginesExtraParams = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $twoPass = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $deinterlice = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $rotate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $operators = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $engineVersion = null;

	/**
	 * The container format of the Flavor Params
	 *
	 * @var KalturaContainerFormat
	 */
	public $format = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $aspectRatioProcessingMode = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $forceFrameToMultiplication16 = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isGopInSec = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isAvoidVideoShrinkFramesizeToSource = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isAvoidVideoShrinkBitrateToSource = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isVideoFrameRateForLowBrAppleHls = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $multiStream = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $anamorphicPixels = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isAvoidForcedKeyFrames = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $forcedKeyFramesMode = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isCropIMX = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $optimizationPolicy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxFrameRate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoConstantBitrate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoBitrateTolerance = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $watermarkData = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $subtitlesData = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isEncrypted = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $contentAwareness = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $chunkedEncodeMode = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $clipOffset = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $clipDuration = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorAssetWithParams extends KalturaObjectBase
{
	/**
	 * The Flavor Asset (Can be null when there are params without asset)
	 *
	 * @var KalturaFlavorAsset
	 */
	public $flavorAsset;

	/**
	 * The Flavor Params
	 *
	 * @var KalturaFlavorParams
	 */
	public $flavorParams;

	/**
	 * The entry id
	 *
	 * @var string
	 */
	public $entryId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorParamsOutput extends KalturaFlavorParams
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $commandLinesStr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsVersion = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $readyBehavior = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSchedulerStatus extends KalturaObjectBase
{
	/**
	 * The id of the Category
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The configured id of the scheduler
	 *
	 * @var int
	 */
	public $schedulerConfiguredId = null;

	/**
	 * The configured id of the job worker
	 *
	 * @var int
	 */
	public $workerConfiguredId = null;

	/**
	 * The type of the job worker.
	 *
	 * @var KalturaBatchJobType
	 */
	public $workerType = null;

	/**
	 * The status type
	 *
	 * @var KalturaSchedulerStatusType
	 */
	public $type = null;

	/**
	 * The status value
	 *
	 * @var int
	 */
	public $value = null;

	/**
	 * The id of the scheduler
	 *
	 * @var int
	 * @readonly
	 */
	public $schedulerId = null;

	/**
	 * The id of the worker
	 *
	 * @var int
	 * @readonly
	 */
	public $workerId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSchedulerConfig extends KalturaObjectBase
{
	/**
	 * The id of the Category
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Creator name
	 *
	 * @var string
	 */
	public $createdBy = null;

	/**
	 * Updater name
	 *
	 * @var string
	 */
	public $updatedBy = null;

	/**
	 * Id of the control panel command that created this config item
	 *
	 * @var string
	 */
	public $commandId = null;

	/**
	 * The status of the control panel command
	 *
	 * @var string
	 */
	public $commandStatus = null;

	/**
	 * The id of the scheduler
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * The configured id of the scheduler
	 *
	 * @var int
	 */
	public $schedulerConfiguredId = null;

	/**
	 * The name of the scheduler
	 *
	 * @var string
	 */
	public $schedulerName = null;

	/**
	 * The id of the job worker
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * The configured id of the job worker
	 *
	 * @var int
	 */
	public $workerConfiguredId = null;

	/**
	 * The name of the job worker
	 *
	 * @var string
	 */
	public $workerName = null;

	/**
	 * The name of the variable
	 *
	 * @var string
	 */
	public $variable = null;

	/**
	 * The part of the variable
	 *
	 * @var string
	 */
	public $variablePart = null;

	/**
	 * The value of the variable
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSchedulerWorker extends KalturaObjectBase
{
	/**
	 * The id of the Worker
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The id as configured in the batch config
	 *
	 * @var int
	 */
	public $configuredId = null;

	/**
	 * The id of the Scheduler
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * The id of the scheduler as configured in the batch config
	 *
	 * @var int
	 */
	public $schedulerConfiguredId = null;

	/**
	 * The worker type
	 *
	 * @var KalturaBatchJobType
	 */
	public $type = null;

	/**
	 * The friendly name of the type
	 *
	 * @var string
	 */
	public $typeName = null;

	/**
	 * The scheduler name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Array of the last statuses
	 *
	 * @var array of KalturaSchedulerStatus
	 */
	public $statuses;

	/**
	 * Array of the last configs
	 *
	 * @var array of KalturaSchedulerConfig
	 */
	public $configs;

	/**
	 * Array of jobs that locked to this worker
	 *
	 * @var array of KalturaBatchJob
	 */
	public $lockedJobs;

	/**
	 * Avarage time between creation and queue time
	 *
	 * @var int
	 */
	public $avgWait = null;

	/**
	 * Avarage time between queue time end finish time
	 *
	 * @var int
	 */
	public $avgWork = null;

	/**
	 * last status time
	 *
	 * @var int
	 */
	public $lastStatus = null;

	/**
	 * last status formated
	 *
	 * @var string
	 */
	public $lastStatusStr = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaScheduler extends KalturaObjectBase
{
	/**
	 * The id of the Scheduler
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The id as configured in the batch config
	 *
	 * @var int
	 */
	public $configuredId = null;

	/**
	 * The scheduler name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * The host name
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * Array of the last statuses
	 *
	 * @var array of KalturaSchedulerStatus
	 * @readonly
	 */
	public $statuses;

	/**
	 * Array of the last configs
	 *
	 * @var array of KalturaSchedulerConfig
	 * @readonly
	 */
	public $configs;

	/**
	 * Array of the workers
	 *
	 * @var array of KalturaSchedulerWorker
	 * @readonly
	 */
	public $workers;

	/**
	 * creation time
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * last status time
	 *
	 * @var int
	 * @readonly
	 */
	public $lastStatus = null;

	/**
	 * last status formated
	 *
	 * @var string
	 * @readonly
	 */
	public $lastStatusStr = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGroupUser extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $groupId = null;

	/**
	 * 
	 *
	 * @var KalturaGroupUserStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaObject extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var map
	 * @readonly
	 */
	public $relatedObjects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIntegerValue extends KalturaValue
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamConfiguration extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaPlaybackProtocol
	 */
	public $protocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $publishUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $backupUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $streamName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamPushPublishConfiguration extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $publishUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $backupPublishUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $port = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryRecordingOptions extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $shouldCopyEntitlement = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $shouldCopyScheduling = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $shouldCopyThumbnail = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $shouldMakeHidden = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaLiveEntry extends KalturaMediaEntry
{
	/**
	 * The message to be presented when the stream is offline
	 *
	 * @var string
	 */
	public $offlineMessage = null;

	/**
	 * Recording Status Enabled/Disabled
	 *
	 * @var KalturaRecordStatus
	 */
	public $recordStatus = null;

	/**
	 * DVR Status Enabled/Disabled
	 *
	 * @var KalturaDVRStatus
	 */
	public $dvrStatus = null;

	/**
	 * Window of time which the DVR allows for backwards scrubbing (in minutes)
	 *
	 * @var int
	 */
	public $dvrWindow = null;

	/**
	 * Elapsed recording time (in msec) up to the point where the live stream was last stopped (unpublished).
	 *
	 * @var int
	 */
	public $lastElapsedRecordingTime = null;

	/**
	 * Array of key value protocol->live stream url objects
	 *
	 * @var array of KalturaLiveStreamConfiguration
	 */
	public $liveStreamConfigurations;

	/**
	 * Recorded entry id
	 *
	 * @var string
	 */
	public $recordedEntryId = null;

	/**
	 * Flag denoting whether entry should be published by the media server
	 *
	 * @var KalturaLivePublishStatus
	 */
	public $pushPublishEnabled = null;

	/**
	 * Array of publish configurations
	 *
	 * @var array of KalturaLiveStreamPushPublishConfiguration
	 */
	public $publishConfigurations;

	/**
	 * The first time in which the entry was broadcast
	 *
	 * @var int
	 * @readonly
	 */
	public $firstBroadcast = null;

	/**
	 * The Last time in which the entry was broadcast
	 *
	 * @var int
	 * @readonly
	 */
	public $lastBroadcast = null;

	/**
	 * The time (unix timestamp in milliseconds) in which the entry broadcast started or 0 when the entry is off the air
	 *
	 * @var float
	 */
	public $currentBroadcastStartTime = null;

	/**
	 * 
	 *
	 * @var KalturaLiveEntryRecordingOptions
	 */
	public $recordingOptions;

	/**
	 * the status of the entry of type EntryServerNodeStatus
	 *
	 * @var KalturaEntryServerNodeStatus
	 * @readonly
	 */
	public $liveStatus = null;

	/**
	 * The chunk duration value in milliseconds
	 *
	 * @var int
	 */
	public $segmentDuration = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $explicitLive = null;

	/**
	 * 
	 *
	 * @var KalturaViewMode
	 */
	public $viewMode = null;

	/**
	 * 
	 *
	 * @var KalturaRecordingStatus
	 */
	public $recordingStatus = null;

	/**
	 * The time the last broadcast finished.
	 *
	 * @var int
	 * @readonly
	 */
	public $lastBroadcastEndTime = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannel extends KalturaLiveEntry
{
	/**
	 * Playlist id to be played
	 *
	 * @var string
	 */
	public $playlistId = null;

	/**
	 * Indicates that the segments should be repeated for ever
	 *
	 * @var KalturaNullableBoolean
	 */
	public $repeat = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelSegment extends KalturaObjectBase
{
	/**
	 * Unique identifier
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * Segment creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Segment update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Segment name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Segment description
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Segment tags
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * Segment could be associated with the main stream, as additional stream or as overlay
	 *
	 * @var KalturaLiveChannelSegmentType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var KalturaLiveChannelSegmentStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * Live channel id
	 *
	 * @var string
	 */
	public $channelId = null;

	/**
	 * Entry id to be played
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * Segment start time trigger type
	 *
	 * @var KalturaLiveChannelSegmentTriggerType
	 */
	public $triggerType = null;

	/**
	 * Live channel segment that the trigger relates to
	 *
	 * @var int
	 */
	public $triggerSegmentId = null;

	/**
	 * Segment play start time, in mili-seconds, according to trigger type
	 *
	 * @var float
	 */
	public $startTime = null;

	/**
	 * Segment play duration time, in mili-seconds
	 *
	 * @var float
	 */
	public $duration = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryServerNodeRecordingInfo extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $recordedEntryId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $duration = null;

	/**
	 * 
	 *
	 * @var KalturaEntryServerNodeRecordingStatus
	 */
	public $recordingStatus = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveReportExportParams extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIds = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recpientEmail = null;

	/**
	 * Time zone offset in minutes (between client to UTC)
	 *
	 * @var int
	 */
	public $timeZoneOffset = null;

	/**
	 * Optional argument that allows controlling the prefix of the exported csv url
	 *
	 * @var string
	 */
	public $applicationUrlTemplate = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveReportExportResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $referenceJobId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $reportEmail = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveReportInputFilter extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIds = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fromTime = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $toTime = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $live = null;

	/**
	 * 
	 *
	 * @var KalturaLiveReportOrderBy
	 */
	public $orderBy = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStats extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $audience = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dvrAudience = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $avgBitrate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $bufferTime = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $plays = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $secondsViewed = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startEvent = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $timestamp = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStatsEvent extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * an integer representing the type of event being sent from the player
	 *
	 * @var KalturaLiveStatsEventType
	 */
	public $eventType = null;

	/**
	 * a unique string generated by the client that will represent the client-side session: the primary component will pass it on to other components that sprout from it
	 *
	 * @var string
	 */
	public $sessionId = null;

	/**
	 * incremental sequence of the event
	 *
	 * @var int
	 */
	public $eventIndex = null;

	/**
	 * buffer time in seconds from the last 10 seconds
	 *
	 * @var int
	 */
	public $bufferTime = null;

	/**
	 * bitrate used in the last 10 seconds
	 *
	 * @var int
	 */
	public $bitrate = null;

	/**
	 * the referrer of the client
	 *
	 * @var string
	 */
	public $referrer = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isLive = null;

	/**
	 * the event start time as string
	 *
	 * @var string
	 */
	public $startTime = null;

	/**
	 * delivery type used for this stream
	 *
	 * @var KalturaPlaybackProtocol
	 */
	public $deliveryType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamBitrate extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $bitrate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $height = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamEntry extends KalturaLiveEntry
{
	/**
	 * The stream id as provided by the provider
	 *
	 * @var string
	 * @readonly
	 */
	public $streamRemoteId = null;

	/**
	 * The backup stream id as provided by the provider
	 *
	 * @var string
	 * @readonly
	 */
	public $streamRemoteBackupId = null;

	/**
	 * Array of supported bitrates
	 *
	 * @var array of KalturaLiveStreamBitrate
	 */
	public $bitrates;

	/**
	 * 
	 *
	 * @var string
	 */
	public $primaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $secondaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $primaryRtspBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $secondaryRtspBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $streamName = null;

	/**
	 * The stream url
	 *
	 * @var string
	 */
	public $streamUrl = null;

	/**
	 * HLS URL - URL for live stream playback on mobile device
	 *
	 * @var string
	 */
	public $hlsStreamUrl = null;

	/**
	 * URL Manager to handle the live stream URL (for instance, add token)
	 *
	 * @var string
	 */
	public $urlManager = null;

	/**
	 * The broadcast primary ip
	 *
	 * @var string
	 */
	public $encodingIP1 = null;

	/**
	 * The broadcast secondary ip
	 *
	 * @var string
	 */
	public $encodingIP2 = null;

	/**
	 * The broadcast password
	 *
	 * @var string
	 */
	public $streamPassword = null;

	/**
	 * The broadcast username
	 *
	 * @var string
	 * @readonly
	 */
	public $streamUsername = null;

	/**
	 * The Streams primary server node id
	 *
	 * @var int
	 * @readonly
	 */
	public $primaryServerNodeId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamParams extends KalturaObjectBase
{
	/**
	 * Bit rate of the stream. (i.e. 900)
	 *
	 * @var int
	 */
	public $bitrate = null;

	/**
	 * flavor asset id
	 *
	 * @var string
	 */
	public $flavorId = null;

	/**
	 * Stream's width
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * Stream's height
	 *
	 * @var int
	 */
	public $height = null;

	/**
	 * Live stream's codec
	 *
	 * @var string
	 */
	public $codec = null;

	/**
	 * Live stream's farme rate
	 *
	 * @var int
	 */
	public $frameRate = null;

	/**
	 * Live stream's key frame interval
	 *
	 * @var float
	 */
	public $keyFrameInterval = null;

	/**
	 * Live stream's language
	 *
	 * @var string
	 */
	public $language = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBaseEntryBaseFilter extends KalturaRelatedFilter
{
	/**
	 * This filter should be in use for retrieving only a specific entry (identified by its entryId).
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * This filter should be in use for retrieving few specific entries (string should include comma separated list of entryId strings).
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idNotIn = null;

	/**
	 * This filter should be in use for retrieving specific entries. It should include only one string to search for in entry names (no wildcards, spaces are treated as part of the string).
	 *
	 * @var string
	 */
	public $nameLike = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry names, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).
	 *
	 * @var string
	 */
	public $nameMultiLikeOr = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry names, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).
	 *
	 * @var string
	 */
	public $nameMultiLikeAnd = null;

	/**
	 * This filter should be in use for retrieving entries with a specific name.
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * This filter should be in use for retrieving only entries which were uploaded by/assigned to users of a specific Kaltura Partner (identified by Partner ID).
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * This filter should be in use for retrieving only entries within Kaltura network which were uploaded by/assigned to users of few Kaltura Partners  (string should include comma separated list of PartnerIDs)
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * This filter parameter should be in use for retrieving only entries, uploaded by/assigned to a specific user (identified by user Id).
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdNotIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $creatorIdEqual = null;

	/**
	 * This filter should be in use for retrieving specific entries. It should include only one string to search for in entry tags (no wildcards, spaces are treated as part of the string).
	 *
	 * @var string
	 */
	public $tagsLike = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * This filter should be in use for retrieving specific entries. It should include only one string to search for in entry tags set by an ADMIN user (no wildcards, spaces are treated as part of the string).
	 *
	 * @var string
	 */
	public $adminTagsLike = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by an ADMIN user, while applying an OR logic to retrieve entries that contain at least one input string (no wildcards, spaces are treated as part of the string).
	 *
	 * @var string
	 */
	public $adminTagsMultiLikeOr = null;

	/**
	 * This filter should be in use for retrieving specific entries. It could include few (comma separated) strings for searching in entry tags, set by an ADMIN user, while applying an AND logic to retrieve entries that contain all input strings (no wildcards, spaces are treated as part of the string).
	 *
	 * @var string
	 */
	public $adminTagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesMatchAnd = null;

	/**
	 * All entries within these categories or their child categories.
	 *
	 * @var string
	 */
	public $categoriesMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesNotContains = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesIdsMatchAnd = null;

	/**
	 * All entries of the categories, excluding their child categories.
	 * 	 To include entries of the child categories, use categoryAncestorIdIn, or categoriesMatchOr.
	 *
	 * @var string
	 */
	public $categoriesIdsMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesIdsNotContains = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $categoriesIdsEmpty = null;

	/**
	 * This filter should be in use for retrieving only entries, at a specific {
	 *
	 * @var KalturaEntryStatus
	 */
	public $statusEqual = null;

	/**
	 * This filter should be in use for retrieving only entries, not at a specific {
	 *
	 * @var KalturaEntryStatus
	 */
	public $statusNotEqual = null;

	/**
	 * This filter should be in use for retrieving only entries, at few specific {
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * This filter should be in use for retrieving only entries, not at few specific {
	 *
	 * @var string
	 */
	public $statusNotIn = null;

	/**
	 * 
	 *
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatusEqual = null;

	/**
	 * 
	 *
	 * @var KalturaEntryModerationStatus
	 */
	public $moderationStatusNotEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $moderationStatusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $moderationStatusNotIn = null;

	/**
	 * 
	 *
	 * @var KalturaEntryType
	 */
	public $typeEqual = null;

	/**
	 * This filter should be in use for retrieving entries of few {
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * This filter parameter should be in use for retrieving only entries which were created at Kaltura system after a specific time/date (standard timestamp format).
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * This filter parameter should be in use for retrieving only entries which were created at Kaltura system before a specific time/date (standard timestamp format).
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $totalRankLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $totalRankGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $groupIdEqual = null;

	/**
	 * This filter should be in use for retrieving specific entries while search match the input string within all of the following metadata attributes: name, description, tags, adminTags.
	 *
	 * @var string
	 */
	public $searchTextMatchAnd = null;

	/**
	 * This filter should be in use for retrieving specific entries while search match the input string within at least one of the following metadata attributes: name, description, tags, adminTags.
	 *
	 * @var string
	 */
	public $searchTextMatchOr = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $accessControlIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $accessControlIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startDateGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startDateLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startDateGreaterThanOrEqualOrNull = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $startDateLessThanOrEqualOrNull = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDateGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDateLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDateGreaterThanOrEqualOrNull = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDateLessThanOrEqualOrNull = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacingEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacingEntryIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacedEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacedEntryIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaEntryReplacementStatus
	 */
	public $replacementStatusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $replacementStatusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValueGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValueLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootEntryIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersEditMatchAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersEditMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersPublishMatchAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersPublishMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersViewMatchAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersViewMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsNameMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAdminTagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAdminTagsNameMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsNameMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAdminTagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsAdminTagsNameMultiLikeAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntryFilter extends KalturaBaseEntryBaseFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $freeText = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isRoot = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesFullNameIn = null;

	/**
	 * All entries within this categoy or in child categories
	 *
	 * @var string
	 */
	public $categoryAncestorIdIn = null;

	/**
	 * The id of the original entry
	 *
	 * @var string
	 */
	public $redirectFromEntryId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaPlayableEntryBaseFilter extends KalturaBaseEntryFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $lastPlayedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastPlayedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationLessThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationGreaterThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $durationTypeMatchOr = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlayableEntryFilter extends KalturaPlayableEntryBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMediaEntryBaseFilter extends KalturaPlayableEntryFilter
{
	/**
	 * 
	 *
	 * @var KalturaMediaType
	 */
	public $mediaTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $mediaTypeIn = null;

	/**
	 * 
	 *
	 * @var KalturaSourceType
	 */
	public $sourceTypeEqual = null;

	/**
	 * 
	 *
	 * @var KalturaSourceType
	 */
	public $sourceTypeNotEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceTypeNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mediaDateGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mediaDateLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsIdsMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsIdsMatchAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaEntryFilter extends KalturaMediaEntryBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaEntryFilterForPlaylist extends KalturaMediaEntryFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $limit = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaInfo extends KalturaObjectBase
{
	/**
	 * The id of the media info
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The id of the related flavor asset
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * The file size
	 *
	 * @var int
	 */
	public $fileSize = null;

	/**
	 * The container format
	 *
	 * @var string
	 */
	public $containerFormat = null;

	/**
	 * The container id
	 *
	 * @var string
	 */
	public $containerId = null;

	/**
	 * The container profile
	 *
	 * @var string
	 */
	public $containerProfile = null;

	/**
	 * The container duration
	 *
	 * @var int
	 */
	public $containerDuration = null;

	/**
	 * The container bit rate
	 *
	 * @var int
	 */
	public $containerBitRate = null;

	/**
	 * The video format
	 *
	 * @var string
	 */
	public $videoFormat = null;

	/**
	 * The video codec id
	 *
	 * @var string
	 */
	public $videoCodecId = null;

	/**
	 * The video duration
	 *
	 * @var int
	 */
	public $videoDuration = null;

	/**
	 * The video bit rate
	 *
	 * @var int
	 */
	public $videoBitRate = null;

	/**
	 * The video bit rate mode
	 *
	 * @var KalturaBitRateMode
	 */
	public $videoBitRateMode = null;

	/**
	 * The video width
	 *
	 * @var int
	 */
	public $videoWidth = null;

	/**
	 * The video height
	 *
	 * @var int
	 */
	public $videoHeight = null;

	/**
	 * The video frame rate
	 *
	 * @var float
	 */
	public $videoFrameRate = null;

	/**
	 * The video display aspect ratio (dar)
	 *
	 * @var float
	 */
	public $videoDar = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $videoRotation = null;

	/**
	 * The audio format
	 *
	 * @var string
	 */
	public $audioFormat = null;

	/**
	 * The audio codec id
	 *
	 * @var string
	 */
	public $audioCodecId = null;

	/**
	 * The audio duration
	 *
	 * @var int
	 */
	public $audioDuration = null;

	/**
	 * The audio bit rate
	 *
	 * @var int
	 */
	public $audioBitRate = null;

	/**
	 * The audio bit rate mode
	 *
	 * @var KalturaBitRateMode
	 */
	public $audioBitRateMode = null;

	/**
	 * The number of audio channels
	 *
	 * @var int
	 */
	public $audioChannels = null;

	/**
	 * The audio sampling rate
	 *
	 * @var int
	 */
	public $audioSamplingRate = null;

	/**
	 * The audio resolution
	 *
	 * @var int
	 */
	public $audioResolution = null;

	/**
	 * The writing library
	 *
	 * @var string
	 */
	public $writingLib = null;

	/**
	 * The data as returned by the mediainfo command line
	 *
	 * @var string
	 */
	public $rawData = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $multiStreamInfo = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $scanType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $multiStream = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $isFastStart = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentStreams = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $complexityValue = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $maxGOP = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMixEntry extends KalturaPlayableEntry
{
	/**
	 * Indicates whether the user has submited a real thumbnail to the mix (Not the one that was generated automaticaly)
	 *
	 * @var bool
	 * @readonly
	 */
	public $hasRealThumbnail = null;

	/**
	 * The editor type used to edit the metadata
	 *
	 * @var KalturaEditorType
	 */
	public $editorType = null;

	/**
	 * The xml data of the mix
	 *
	 * @var string
	 */
	public $dataContent = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaModerationFlag extends KalturaObjectBase
{
	/**
	 * Moderation flag id
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * The user id that added the moderation flag
	 *
	 * @var string
	 * @readonly
	 */
	public $userId = null;

	/**
	 * The type of the moderation flag (entry or user)
	 *
	 * @var KalturaModerationObjectType
	 * @readonly
	 */
	public $moderationObjectType = null;

	/**
	 * If moderation flag is set for entry, this is the flagged entry id
	 *
	 * @var string
	 */
	public $flaggedEntryId = null;

	/**
	 * If moderation flag is set for user, this is the flagged user id
	 *
	 * @var string
	 */
	public $flaggedUserId = null;

	/**
	 * The moderation flag status
	 *
	 * @var KalturaModerationFlagStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * The comment that was added to the flag
	 *
	 * @var string
	 */
	public $comments = null;

	/**
	 * 
	 *
	 * @var KalturaModerationFlagType
	 */
	public $flagType = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerStatistics extends KalturaObjectBase
{
	/**
	 * Package total allowed bandwidth and storage
	 *
	 * @var int
	 * @readonly
	 */
	public $packageBandwidthAndStorage = null;

	/**
	 * Partner total hosting in GB on the disk
	 *
	 * @var float
	 * @readonly
	 */
	public $hosting = null;

	/**
	 * Partner total bandwidth in GB
	 *
	 * @var float
	 * @readonly
	 */
	public $bandwidth = null;

	/**
	 * total usage in GB - including bandwidth and storage
	 *
	 * @var int
	 * @readonly
	 */
	public $usage = null;

	/**
	 * Percent of usage out of partner's package. if usage is 5GB and package is 10GB, this value will be 50
	 *
	 * @var float
	 * @readonly
	 */
	public $usagePercent = null;

	/**
	 * date when partner reached the limit of his package (timestamp)
	 *
	 * @var int
	 * @readonly
	 */
	public $reachedLimitDate = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerUsage extends KalturaObjectBase
{
	/**
	 * Partner total hosting in GB on the disk
	 *
	 * @var float
	 * @readonly
	 */
	public $hostingGB = null;

	/**
	 * percent of usage out of partner's package. if usageGB is 5 and package is 10GB, this value will be 50
	 *
	 * @var float
	 * @readonly
	 */
	public $Percent = null;

	/**
	 * package total BW - actually this is usage, which represents BW+storage
	 *
	 * @var int
	 * @readonly
	 */
	public $packageBW = null;

	/**
	 * total usage in GB - including bandwidth and storage
	 *
	 * @var float
	 * @readonly
	 */
	public $usageGB = null;

	/**
	 * date when partner reached the limit of his package (timestamp)
	 *
	 * @var int
	 * @readonly
	 */
	public $reachedLimitDate = null;

	/**
	 * a semi-colon separated list of comma-separated key-values to represent a usage graph.
	 * 	 keys could be 1-12 for a year view (1,1.2;2,1.1;3,0.9;...;12,1.4;)
	 * 	 keys could be 1-[28,29,30,31] depending on the requested month, for a daily view in a given month (1,0.4;2,0.2;...;31,0.1;)
	 *
	 * @var string
	 * @readonly
	 */
	public $usageGraph = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermission extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var KalturaPermissionType
	 * @readonly
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $friendlyName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var KalturaPermissionStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dependsOnPermissionNames = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $permissionItemsIds = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerGroup = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaPermissionItem extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var KalturaPermissionItemType
	 * @readonly
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaybackSource extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $deliveryProfileId = null;

	/**
	 * source format according to delivery profile streamer type (applehttp, mpegdash etc.)
	 *
	 * @var string
	 */
	public $format = null;

	/**
	 * comma separated string according to deliveryProfile media protocols ('http,https' etc.)
	 *
	 * @var string
	 */
	public $protocols = null;

	/**
	 * comma separated string of flavor ids
	 *
	 * @var string
	 */
	public $flavorIds = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * drm data object containing relevant license url ,scheme name and certificate
	 *
	 * @var array of KalturaDrmPlaybackPluginData
	 */
	public $drm;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaybackContext extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaPlaybackSource
	 */
	public $sources;

	/**
	 * 
	 *
	 * @var array of KalturaFlavorAsset
	 */
	public $flavorAssets;

	/**
	 * Array of actions as received from the rules that invalidated
	 *
	 * @var array of KalturaRuleAction
	 */
	public $actions;

	/**
	 * Array of actions as received from the rules that invalidated
	 *
	 * @var array of KalturaAccessControlMessage
	 */
	public $messages;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaylist extends KalturaBaseEntry
{
	/**
	 * Content of the playlist - 
	 * 	 XML if the playlistType is dynamic 
	 * 	 text if the playlistType is static 
	 * 	 url if the playlistType is mRss
	 *
	 * @var string
	 */
	public $playlistContent = null;

	/**
	 * 
	 *
	 * @var array of KalturaMediaEntryFilterForPlaylist
	 */
	public $filters;

	/**
	 * Maximum count of results to be returned in playlist execution
	 *
	 * @var int
	 */
	public $totalResults = null;

	/**
	 * Type of playlist
	 *
	 * @var KalturaPlaylistType
	 */
	public $playlistType = null;

	/**
	 * Number of plays
	 *
	 * @var int
	 * @readonly
	 */
	public $plays = null;

	/**
	 * Number of views
	 *
	 * @var int
	 * @readonly
	 */
	public $views = null;

	/**
	 * The duration in seconds
	 *
	 * @var int
	 * @readonly
	 */
	public $duration = null;

	/**
	 * The url for this playlist
	 *
	 * @var string
	 * @readonly
	 */
	public $executeUrl = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRemotePath extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $storageProfileId = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $uri = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlResource extends KalturaContentResource
{
	/**
	 * Remote URL, FTP, HTTP or HTTPS
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * Force Import Job
	 *
	 * @var bool
	 */
	public $forceAsyncDownload = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRemoteStorageResource extends KalturaUrlResource
{
	/**
	 * ID of storage profile to be associated with the created file sync, used for file serving URL composing.
	 *
	 * @var int
	 */
	public $storageProfileId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReport extends KalturaObjectBase
{
	/**
	 * Report id
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Partner id associated with the report
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * Report name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Used to identify system reports in a friendly way
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * Report description
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * Report query
	 *
	 * @var string
	 */
	public $query = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportBaseTotal extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $data = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportGraph extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $data = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportInputBaseFilter extends KalturaObjectBase
{
	/**
	 * Start date as Unix timestamp (In seconds)
	 *
	 * @var int
	 */
	public $fromDate = null;

	/**
	 * End date as Unix timestamp (In seconds)
	 *
	 * @var int
	 */
	public $toDate = null;

	/**
	 * Start day as string (YYYYMMDD)
	 *
	 * @var string
	 */
	public $fromDay = null;

	/**
	 * End date as string (YYYYMMDD)
	 *
	 * @var string
	 */
	public $toDay = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $columns = null;

	/**
	 * 
	 *
	 * @var array of KalturaString
	 */
	public $results;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportTable extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $header = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $data = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportTotal extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $header = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $data = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRequestConfiguration extends KalturaObjectBase
{
	/**
	 * Impersonated partner id
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * Kaltura API session
	 *
	 * @var string
	 */
	public $ks = null;

	/**
	 * Response profile - this attribute will be automatically unset after every API call.
	 *
	 * @var KalturaBaseResponseProfile
	 */
	public $responseProfile;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfile extends KalturaDetachedResponseProfile
{
	/**
	 * Auto generated numeric identifier
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Unique system name
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * Creation time as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Update time as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var KalturaResponseProfileStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $version = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfileCacheRecalculateOptions extends KalturaObjectBase
{
	/**
	 * Maximum number of keys to recalculate
	 *
	 * @var int
	 */
	public $limit = null;

	/**
	 * Class name
	 *
	 * @var string
	 */
	public $cachedObjectType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $startObjectKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $endObjectKey = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $jobCreatedAt = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isFirstLoop = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfileCacheRecalculateResults extends KalturaObjectBase
{
	/**
	 * Last recalculated id
	 *
	 * @var string
	 */
	public $lastObjectKey = null;

	/**
	 * Number of recalculated keys
	 *
	 * @var int
	 */
	public $recalculated = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaScope extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearch extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $keyWords = null;

	/**
	 * 
	 *
	 * @var KalturaSearchProviderType
	 */
	public $searchSource = null;

	/**
	 * 
	 *
	 * @var KalturaMediaType
	 */
	public $mediaType = null;

	/**
	 * Use this field to pass dynamic data for searching
	 * 	 For example - if you set this field to "mymovies_$partner_id"
	 * 	 The $partner_id will be automatically replcaed with your real partner Id
	 *
	 * @var string
	 */
	public $extraData = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $authData = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchAuthData extends KalturaObjectBase
{
	/**
	 * The authentication data that further should be used for search
	 *
	 * @var string
	 */
	public $authData = null;

	/**
	 * Login URL when user need to sign-in and authorize the search
	 *
	 * @var string
	 */
	public $loginUrl = null;

	/**
	 * Information when there was an error
	 *
	 * @var string
	 */
	public $message = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchResult extends KalturaSearch
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceLink = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $credit = null;

	/**
	 * 
	 *
	 * @var KalturaLicenseType
	 */
	public $licenseType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flashPlaybackType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileExt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchResultResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaSearchResult
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var bool
	 * @readonly
	 */
	public $needMediaInfo = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaServerNode extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $heartbeatTime = null;

	/**
	 * serverNode name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * serverNode uniqe system name
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * serverNode hostName
	 *
	 * @var string
	 */
	public $hostName = null;

	/**
	 * 
	 *
	 * @var KalturaServerNodeStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaServerNodeType
	 * @readonly
	 */
	public $type = null;

	/**
	 * serverNode tags
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * DC where the serverNode is located
	 *
	 * @var int
	 * @readonly
	 */
	public $dc = null;

	/**
	 * Id of the parent serverNode
	 *
	 * @var string
	 */
	public $parentId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSessionInfo extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $ks = null;

	/**
	 * 
	 *
	 * @var KalturaSessionType
	 * @readonly
	 */
	public $sessionType = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $expiry = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $privileges = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSourceFileSyncDescriptor extends KalturaFileSyncDescriptor
{
	/**
	 * The translated path as used by the scheduler
	 *
	 * @var string
	 */
	public $actualFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $assetParamsId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStartWidgetSessionResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $ks = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $userId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStatsEvent extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $clientVer = null;

	/**
	 * 
	 *
	 * @var KalturaStatsEventType
	 */
	public $eventType = null;

	/**
	 * the client's timestamp of this event
	 *
	 * @var float
	 */
	public $eventTimestamp = null;

	/**
	 * a unique string generated by the client that will represent the client-side session: the primary component will pass it on to other components that sprout from it
	 *
	 * @var string
	 */
	public $sessionId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * the UV cookie - creates in the operational system and should be passed on ofr every event
	 *
	 * @var string
	 */
	public $uniqueViewer = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $widgetId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uiconfId = null;

	/**
	 * the partner's user id
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * the timestamp along the video when the event happend
	 *
	 * @var int
	 */
	public $currentPoint = null;

	/**
	 * the duration of the video in milliseconds - will make it much faster than quering the db for each entry
	 *
	 * @var int
	 */
	public $duration = null;

	/**
	 * will be retrieved from the request of the user
	 *
	 * @var string
	 * @readonly
	 */
	public $userIp = null;

	/**
	 * the time in milliseconds the event took
	 *
	 * @var int
	 */
	public $processDuration = null;

	/**
	 * the id of the GUI control - will be used in the future to better understand what the user clicked
	 *
	 * @var string
	 */
	public $controlId = null;

	/**
	 * true if the user ever used seek in this session
	 *
	 * @var bool
	 */
	public $seek = null;

	/**
	 * timestamp of the new point on the timeline of the video after the user seeks
	 *
	 * @var int
	 */
	public $newPoint = null;

	/**
	 * the referrer of the client
	 *
	 * @var string
	 */
	public $referrer = null;

	/**
	 * will indicate if the event is thrown for the first video in the session
	 *
	 * @var bool
	 */
	public $isFirstInSession = null;

	/**
	 * kaltura application name
	 *
	 * @var string
	 */
	public $applicationId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $contextId = null;

	/**
	 * 
	 *
	 * @var KalturaStatsFeatureType
	 */
	public $featureType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStatsKmcEvent extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $clientVer = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $kmcEventActionPath = null;

	/**
	 * 
	 *
	 * @var KalturaStatsKmcEventType
	 */
	public $kmcEventType = null;

	/**
	 * the client's timestamp of this event
	 *
	 * @var float
	 */
	public $eventTimestamp = null;

	/**
	 * a unique string generated by the client that will represent the client-side session: the primary component will pass it on to other components that sprout from it
	 *
	 * @var string
	 */
	public $sessionId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $widgetId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uiconfId = null;

	/**
	 * the partner's user id
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * will be retrieved from the request of the user
	 *
	 * @var string
	 * @readonly
	 */
	public $userIp = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageProfile extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $desciption = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileProtocol
	 */
	public $protocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageBaseDir = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageUsername = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storagePassword = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $storageFtpPassiveMode = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $minFileSize = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxFileSize = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsIds = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxConcurrentConnections = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $pathManagerClass = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $pathManagerParams;

	/**
	 * No need to create enum for temp field
	 *
	 * @var int
	 */
	public $trigger = null;

	/**
	 * Delivery Priority
	 *
	 * @var int
	 */
	public $deliveryPriority = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileDeliveryStatus
	 */
	public $deliveryStatus = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileReadyBehavior
	 */
	public $readyBehavior = null;

	/**
	 * Flag sugnifying that the storage exported content should be deleted when soure entry is deleted
	 *
	 * @var int
	 */
	public $allowAutoDelete = null;

	/**
	 * Indicates to the local file transfer manager to create a link to the file instead of copying it
	 *
	 * @var bool
	 */
	public $createFileLink = null;

	/**
	 * Holds storage profile export rules
	 *
	 * @var array of KalturaRule
	 */
	public $rules;

	/**
	 * Delivery profile ids
	 *
	 * @var array of KalturaKeyValue
	 */
	public $deliveryProfileIds;

	/**
	 * 
	 *
	 * @var string
	 */
	public $privateKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $publicKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $passPhrase = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $shouldExportThumbs = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationFeedEntryCount extends KalturaObjectBase
{
	/**
	 * the total count of entries that should appear in the feed without flavor filtering
	 *
	 * @var int
	 */
	public $totalEntryCount = null;

	/**
	 * count of entries that will appear in the feed (including all relevant filters)
	 *
	 * @var int
	 */
	public $actualEntryCount = null;

	/**
	 * count of entries that requires transcoding in order to be included in feed
	 *
	 * @var int
	 */
	public $requireTranscodingCount = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbAsset extends KalturaAsset
{
	/**
	 * The Flavor Params used to create this Flavor Asset
	 *
	 * @var int
	 * @insertonly
	 */
	public $thumbParamsId = null;

	/**
	 * The width of the Flavor Asset
	 *
	 * @var int
	 * @readonly
	 */
	public $width = null;

	/**
	 * The height of the Flavor Asset
	 *
	 * @var int
	 * @readonly
	 */
	public $height = null;

	/**
	 * The status of the asset
	 *
	 * @var KalturaThumbAssetStatus
	 * @readonly
	 */
	public $status = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParams extends KalturaAssetParams
{
	/**
	 * 
	 *
	 * @var KalturaThumbCropType
	 */
	public $cropType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $quality = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cropX = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cropY = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cropWidth = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cropHeight = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $videoOffset = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $height = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $scaleWidth = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $scaleHeight = null;

	/**
	 * Hexadecimal value
	 *
	 * @var string
	 */
	public $backgroundColor = null;

	/**
	 * Id of the flavor params or the thumbnail params to be used as source for the thumbnail creation
	 *
	 * @var int
	 */
	public $sourceParamsId = null;

	/**
	 * The container format of the Flavor Params
	 *
	 * @var KalturaContainerFormat
	 */
	public $format = null;

	/**
	 * The image density (dpi) for example: 72 or 96
	 *
	 * @var int
	 */
	public $density = null;

	/**
	 * Strip profiles and comments
	 *
	 * @var bool
	 */
	public $stripProfiles = null;

	/**
	 * Create thumbnail from the videoLengthpercentage second
	 *
	 * @var int
	 */
	public $videoOffsetInPercentage = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParamsOutput extends KalturaThumbParams
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $thumbParamsId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbParamsVersion = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $rotate = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUiConf extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Name of the uiConf, this is not a primary key
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var KalturaUiConfObjType
	 */
	public $objType = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $objTypeAsString = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $width = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $height = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $htmlParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $swfUrl = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $confFilePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $confFile = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $confFileFeatures = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $config = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $confVars = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $useCdn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $swfUrlVersion = null;

	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var KalturaUiConfCreationMode
	 */
	public $creationMode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $html5Url = null;

	/**
	 * UiConf version
	 *
	 * @var string
	 * @readonly
	 */
	public $version = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerTags = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUiConfTypeInfo extends KalturaObjectBase
{
	/**
	 * UiConf Type
	 *
	 * @var KalturaUiConfObjType
	 */
	public $type = null;

	/**
	 * Available versions
	 *
	 * @var array of KalturaString
	 */
	public $versions;

	/**
	 * The direcotry this type is saved at
	 *
	 * @var string
	 */
	public $directory = null;

	/**
	 * Filename for this UiConf type
	 *
	 * @var string
	 */
	public $filename = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $uploadTokenId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSize = null;

	/**
	 * 
	 *
	 * @var KalturaUploadErrorCode
	 */
	public $errorCode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorDescription = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadToken extends KalturaObjectBase
{
	/**
	 * Upload token unique ID
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * Partner ID of the upload token
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * User id for the upload token
	 *
	 * @var string
	 * @readonly
	 */
	public $userId = null;

	/**
	 * Status of the upload token
	 *
	 * @var KalturaUploadTokenStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * Name of the file for the upload token, can be empty when the upload token is created and will be updated internally after the file is uploaded
	 *
	 * @var string
	 * @insertonly
	 */
	public $fileName = null;

	/**
	 * File size in bytes, can be empty when the upload token is created and will be updated internally after the file is uploaded
	 *
	 * @var float
	 * @insertonly
	 */
	public $fileSize = null;

	/**
	 * Uploaded file size in bytes, can be used to identify how many bytes were uploaded before resuming
	 *
	 * @var float
	 * @readonly
	 */
	public $uploadedFileSize = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Upload url - to explicitly determine to which domain to adress the uploadToken->upload call
	 *
	 * @var string
	 * @readonly
	 */
	public $uploadUrl = null;

	/**
	 * autoFinalize - Should the upload be finalized once the file size on disk matches the file size reproted when adding the upload token.
	 *
	 * @var KalturaNullableBoolean
	 * @insertonly
	 */
	public $autoFinalize = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUser extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var KalturaUserType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $screenName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $email = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dateOfBirth = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $country = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $state = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $city = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $zip = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbnailUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * Admin tags can be updated only by using an admin session
	 *
	 * @var string
	 */
	public $adminTags = null;

	/**
	 * 
	 *
	 * @var KalturaGender
	 */
	public $gender = null;

	/**
	 * 
	 *
	 * @var KalturaUserStatus
	 */
	public $status = null;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Can be used to store various partner related data as a string
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $indexedPartnerDataInt = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $indexedPartnerDataString = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $storageSize = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $password = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $firstName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastName = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isAdmin = null;

	/**
	 * 
	 *
	 * @var KalturaLanguageCode
	 */
	public $language = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $lastLoginTime = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $statusUpdatedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $deletedAt = null;

	/**
	 * 
	 *
	 * @var bool
	 * @insertonly
	 */
	public $loginEnabled = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $roleIds = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $roleNames = null;

	/**
	 * 
	 *
	 * @var bool
	 * @insertonly
	 */
	public $isAccountOwner = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowedPartnerIds = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $allowedPartnerPackages = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaUserEntry extends KalturaObjectBase
{
	/**
	 * unique auto-generated identifier
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var KalturaUserEntryStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * 
	 *
	 * @var KalturaUserEntryType
	 * @readonly
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var KalturaUserEntryExtendedStatus
	 */
	public $extendedStatus = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserLoginData extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $loginEmail = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserRole extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var KalturaUserRoleStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $permissionNames = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidget extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceWidgetId = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $rootWidgetId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uiConfId = null;

	/**
	 * 
	 *
	 * @var KalturaWidgetSecurityType
	 */
	public $securityType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $securityPolicy = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Can be used to store various partner related data as a string
	 *
	 * @var string
	 */
	public $partnerData = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $widgetHTML = null;

	/**
	 * Should enforce entitlement on feed entries
	 *
	 * @var bool
	 */
	public $enforceEntitlement = null;

	/**
	 * Set privacy context for search entries that assiged to private and public categories within a category privacy context.
	 *
	 * @var string
	 */
	public $privacyContext = null;

	/**
	 * Addes the HTML5 script line to the widget's embed code
	 *
	 * @var bool
	 */
	public $addEmbedHtml5Support = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $roles = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBatchJobBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $idGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $executionAttemptsGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $executionAttemptsLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lockVersionGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lockVersionLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobType
	 */
	public $jobTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobTypeNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $jobSubTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobSubTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $jobSubTypeNotIn = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priorityGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priorityLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $priorityEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $priorityIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $priorityNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchVersionGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchVersionLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $batchVersionEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $queueTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $queueTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $finishTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $finishTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobErrorTypes
	 */
	public $errTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errTypeNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $errNumberEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errNumberIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errNumberNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $estimatedEffortLessThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $estimatedEffortGreaterThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $urgencyLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $urgencyGreaterThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchJobFilter extends KalturaBatchJobBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlBlockAction extends KalturaRuleAction
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlLimitDeliveryProfilesAction extends KalturaRuleAction
{
	/**
	 * Comma separated list of delivery profile ids
	 *
	 * @var string
	 */
	public $deliveryProfileIds = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isBlockedList = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlLimitFlavorsAction extends KalturaRuleAction
{
	/**
	 * Comma separated list of flavor ids
	 *
	 * @var string
	 */
	public $flavorParamsIds = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isBlockedList = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlLimitThumbnailCaptureAction extends KalturaRuleAction
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaAccessControl
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlModifyRequestHostRegexAction extends KalturaRuleAction
{
	/**
	 * Request host regex pattern
	 *
	 * @var string
	 */
	public $pattern = null;

	/**
	 * Request host regex replacment
	 *
	 * @var string
	 */
	public $replacement = null;

	/**
	 * serverNodeId to generate replacment host from
	 *
	 * @var int
	 */
	public $replacmenServerNodeId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlPreviewAction extends KalturaRuleAction
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $limit = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlProfileListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaAccessControlProfile
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlServeRemoteEdgeServerAction extends KalturaRuleAction
{
	/**
	 * Comma separated list of edge servers playBack should be done from
	 *
	 * @var string
	 */
	public $edgeServerIds = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAdminUser extends KalturaUser
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAmazonS3StorageProfile extends KalturaStorageProfile
{
	/**
	 * 
	 *
	 * @var KalturaAmazonS3StorageProfileFilesPermissionLevel
	 */
	public $filesPermissionInS3 = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $s3Region = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sseType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sseKmsKeyId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $signatureType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $endPoint = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaApiActionPermissionItem extends KalturaPermissionItem
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $service = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $action = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaApiParameterPermissionItem extends KalturaPermissionItem
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $object = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parameter = null;

	/**
	 * 
	 *
	 * @var KalturaApiParameterPermissionItemAction
	 */
	public $action = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAppTokenBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaAppTokenStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sessionUserIdEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAppTokenListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaAppToken
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetParamsOutput extends KalturaAssetParams
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $assetParamsId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsVersion = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $readyBehavior = null;

	/**
	 * The container format of the Flavor Params
	 *
	 * @var KalturaContainerFormat
	 */
	public $format = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetPropertiesCompareCondition extends KalturaCondition
{
	/**
	 * Array of key/value objects that holds the property and the value to find and compare on an asset object
	 *
	 * @var array of KalturaKeyValue
	 */
	public $properties;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetTypeCondition extends KalturaCondition
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $assetTypes = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetsParamsResourceContainers extends KalturaResource
{
	/**
	 * Array of resources associated with asset params ids
	 *
	 * @var array of KalturaAssetParamsResourceContainer
	 */
	public $resources;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAttributeCondition extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuthenticatedCondition extends KalturaCondition
{
	/**
	 * The privelege needed to remove the restriction
	 *
	 * @var array of KalturaStringValue
	 */
	public $privileges;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntryCloneOptionComponent extends KalturaBaseEntryCloneOptionItem
{
	/**
	 * 
	 *
	 * @var KalturaBaseEntryCloneOptions
	 */
	public $itemType = null;

	/**
	 * condition rule (include/exclude)
	 *
	 * @var KalturaCloneComponentSelectorType
	 */
	public $rule = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntryListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaBaseEntry
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBaseSyndicationFeedBaseFilter extends KalturaFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseSyndicationFeedListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaBaseSyndicationFeed
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchJobListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaBatchJob
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkDownloadJobData extends KalturaJobData
{
	/**
	 * Comma separated list of entry ids
	 *
	 * @var string
	 */
	public $entryIds = null;

	/**
	 * Flavor params id to use for conversion
	 *
	 * @var int
	 */
	public $flavorParamsId = null;

	/**
	 * The id of the requesting user
	 *
	 * @var string
	 */
	public $puserId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBulkUploadBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $uploadedOnGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uploadedOnLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uploadedOnEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var KalturaBatchJobStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var KalturaBulkUploadObjectType
	 */
	public $bulkUploadObjectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bulkUploadObjectTypeIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadCategoryData extends KalturaBulkUploadObjectData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadCategoryEntryData extends KalturaBulkUploadObjectData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadCategoryUserData extends KalturaBulkUploadObjectData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadEntryData extends KalturaBulkUploadObjectData
{
	/**
	 * Selected profile id for all bulk entries
	 *
	 * @var int
	 */
	public $conversionProfileId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $userId = null;

	/**
	 * The screen name of the user
	 *
	 * @var string
	 * @readonly
	 */
	public $uploadedBy = null;

	/**
	 * Selected profile id for all bulk entries
	 *
	 * @var int
	 * @readonly
	 */
	public $conversionProfileId = null;

	/**
	 * Created by the API
	 *
	 * @var string
	 * @readonly
	 */
	public $resultsFileLocalPath = null;

	/**
	 * Created by the API
	 *
	 * @var string
	 * @readonly
	 */
	public $resultsFileUrl = null;

	/**
	 * Number of created entries
	 *
	 * @var int
	 * @readonly
	 */
	public $numOfEntries = null;

	/**
	 * Number of created objects
	 *
	 * @var int
	 * @readonly
	 */
	public $numOfObjects = null;

	/**
	 * The bulk upload file path
	 *
	 * @var string
	 * @readonly
	 */
	public $filePath = null;

	/**
	 * Type of object for bulk upload
	 *
	 * @var KalturaBulkUploadObjectType
	 * @readonly
	 */
	public $bulkUploadObjectType = null;

	/**
	 * Friendly name of the file, used to be recognized later in the logs.
	 *
	 * @var string
	 */
	public $fileName = null;

	/**
	 * Data pertaining to the objects being uploaded
	 *
	 * @var KalturaBulkUploadObjectData
	 * @readonly
	 */
	public $objectData;

	/**
	 * Type of bulk upload
	 *
	 * @var KalturaBulkUploadType
	 * @readonly
	 */
	public $type = null;

	/**
	 * Recipients of the email for bulk upload success/failure
	 *
	 * @var string
	 */
	public $emailRecipients = null;

	/**
	 * Number of objects that finished on error status
	 *
	 * @var int
	 */
	public $numOfErrorObjects = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaBulkUpload
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadResultCategory extends KalturaBulkUploadResult
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $relativePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $appearInList = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $privacy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $inheritanceType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $userJoinPolicy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $defaultPermissionLevel = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $owner = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $contributionPolicy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValue = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $moderation = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadResultCategoryEntry extends KalturaBulkUploadResult
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadResultCategoryUser extends KalturaBulkUploadResult
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryReferenceId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $permissionLevel = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updateMethod = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $requiredObjectStatus = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadResultEntry extends KalturaBulkUploadResult
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $url = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $conversionProfileId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $accessControlProfileId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $category = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $scheduleStartDate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $scheduleEndDate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $entryStatus = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbnailUrl = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $thumbnailSaved = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sshPrivateKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sshPublicKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sshKeyPassphrase = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $creatorId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersEdit = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entitledUsersPublish = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ownerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $templateEntryId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadResultUser extends KalturaBulkUploadResult
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $screenName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $email = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dateOfBirth = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $country = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $state = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $city = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $zip = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $gender = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $firstName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $group = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadUserData extends KalturaBulkUploadObjectData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCaptureThumbJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var KalturaFileContainer
	 */
	public $fileContainer;

	/**
	 * The translated path as used by the scheduler
	 *
	 * @var string
	 */
	public $actualSrcFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncRemoteUrl = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $thumbParamsOutputId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcAssetId = null;

	/**
	 * 
	 *
	 * @var KalturaAssetType
	 */
	public $srcAssetType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbPath = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryEntryAdvancedFilter extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryEntryStatusIn = null;

	/**
	 * 
	 *
	 * @var KalturaCategoryEntryAdvancedOrderBy
	 */
	public $orderBy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryIdEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryEntryListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaCategoryEntry
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryIdentifier extends KalturaObjectIdentifier
{
	/**
	 * Identifier of the object
	 *
	 * @var KalturaCategoryIdentifierField
	 */
	public $identifier = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaCategory
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryUserAdvancedFilter extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $memberIdEq = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $memberIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $memberPermissionsMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $memberPermissionsMatchAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryUserListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaCategoryUser
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaClipAttributes extends KalturaOperationAttributes
{
	/**
	 * Offset in milliseconds
	 *
	 * @var int
	 */
	public $offset = null;

	/**
	 * Duration in milliseconds
	 *
	 * @var int
	 */
	public $duration = null;

	/**
	 * global Offset In Destination in milliseconds
	 *
	 * @var int
	 */
	public $globalOffsetInDestination = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaClipConcatJobData extends KalturaJobData
{
	/**
	 * $partnerId
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * $priority
	 *
	 * @var int
	 */
	public $priority = null;

	/**
	 * clip operations
	 *
	 * @var array of KalturaObject
	 */
	public $operationAttributes;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaCompareCondition extends KalturaCondition
{
	/**
	 * Value to evaluate against the field and operator
	 *
	 * @var KalturaIntegerValue
	 */
	public $value;

	/**
	 * Comparing operator
	 *
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDataCenterContentResource extends KalturaContentResource
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConcatAttributes extends KalturaOperationAttributes
{
	/**
	 * The resource to be concatenated
	 *
	 * @var KalturaDataCenterContentResource
	 */
	public $resource;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConcatJobData extends KalturaJobData
{
	/**
	 * Source files to be concatenated
	 *
	 * @var array of KalturaString
	 */
	public $srcFiles;

	/**
	 * Output file
	 *
	 * @var string
	 */
	public $destFilePath = null;

	/**
	 * Flavor asset to be ingested with the output
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * Clipping offset in seconds
	 *
	 * @var float
	 */
	public $offset = null;

	/**
	 * Clipping duration in seconds
	 *
	 * @var float
	 */
	public $duration = null;

	/**
	 * duration of the concated video
	 *
	 * @var float
	 */
	public $concatenatedDuration = null;

	/**
	 * Should Sort the clip parts
	 *
	 * @var bool
	 */
	public $shouldSort = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaControlPanelCommandBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdByIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaControlPanelCommandType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var KalturaControlPanelCommandTargetType
	 */
	public $targetTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $targetTypeIn = null;

	/**
	 * 
	 *
	 * @var KalturaControlPanelCommandStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaControlPanelCommandListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaControlPanelCommand
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConvartableJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncLocalPath = null;

	/**
	 * The translated path as used by the scheduler
	 *
	 * @var string
	 */
	public $actualSrcFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncRemoteUrl = null;

	/**
	 * 
	 *
	 * @var array of KalturaSourceFileSyncDescriptor
	 */
	public $srcFileSyncs;

	/**
	 * 
	 *
	 * @var int
	 */
	public $engineVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsOutputId = null;

	/**
	 * 
	 *
	 * @var KalturaFlavorParamsOutput
	 */
	public $flavorParamsOutput;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mediaInfoId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $currentOperationSet = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $currentOperationIndex = null;

	/**
	 * 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $pluginData;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileAssetParamsListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaConversionProfileAssetParams
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaConversionProfile
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConvertLiveSegmentJobData extends KalturaJobData
{
	/**
	 * Live stream entry id
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetId = null;

	/**
	 * Primary or secondary media server
	 *
	 * @var KalturaEntryServerNodeType
	 */
	public $mediaServerIndex = null;

	/**
	 * The index of the file within the entry
	 *
	 * @var int
	 */
	public $fileIndex = null;

	/**
	 * The recorded live media
	 *
	 * @var string
	 */
	public $srcFilePath = null;

	/**
	 * The output file
	 *
	 * @var string
	 */
	public $destFilePath = null;

	/**
	 * Duration of the live entry including all recorded segments including the current
	 *
	 * @var float
	 */
	public $endTime = null;

	/**
	 * The data output file
	 *
	 * @var string
	 */
	public $destDataFilePath = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConvertProfileJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $inputFileSyncLocalPath = null;

	/**
	 * The height of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 *
	 * @var int
	 */
	public $thumbHeight = null;

	/**
	 * The bit rate of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 *
	 * @var int
	 */
	public $thumbBitrate = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCopyPartnerJobData extends KalturaJobData
{
	/**
	 * Id of the partner to copy from
	 *
	 * @var int
	 */
	public $fromPartnerId = null;

	/**
	 * Id of the partner to copy to
	 *
	 * @var int
	 */
	public $toPartnerId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCountryRestriction extends KalturaBaseRestriction
{
	/**
	 * Country restriction type (Allow or deny)
	 *
	 * @var KalturaCountryRestrictionType
	 */
	public $countryRestrictionType = null;

	/**
	 * Comma separated list of country codes to allow to deny
	 *
	 * @var string
	 */
	public $countryList = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDataListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaDataEntry
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeleteFileJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $localFileSyncPath = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeleteJobData extends KalturaJobData
{
	/**
	 * The filter should return the list of objects that need to be deleted.
	 *
	 * @var KalturaFilter
	 */
	public $filter;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileAkamaiAppleHttpManifest extends KalturaDeliveryProfile
{
	/**
	 * Should we use timing parameters - clipTo / seekFrom
	 *
	 * @var bool
	 */
	public $supportClipping = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileAkamaiHds extends KalturaDeliveryProfile
{
	/**
	 * Should we use timing parameters - clipTo / seekFrom
	 *
	 * @var bool
	 */
	public $supportClipping = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileAkamaiHttp extends KalturaDeliveryProfile
{
	/**
	 * Should we use intelliseek
	 *
	 * @var bool
	 */
	public $useIntelliseek = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaPlaybackProtocol
	 */
	public $streamerTypeEqual = null;

	/**
	 * 
	 *
	 * @var KalturaDeliveryStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileCondition extends KalturaCondition
{
	/**
	 * The delivery ids that are accepted by this condition
	 *
	 * @var array of KalturaIntegerValue
	 */
	public $deliveryProfileIds;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericAppleHttp extends KalturaDeliveryProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $pattern = null;

	/**
	 * rendererClass
	 *
	 * @var string
	 */
	public $rendererClass = null;

	/**
	 * Enable to make playManifest redirect to the domain of the delivery profile
	 *
	 * @var KalturaNullableBoolean
	 */
	public $manifestRedirect = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericHds extends KalturaDeliveryProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $pattern = null;

	/**
	 * rendererClass
	 *
	 * @var string
	 */
	public $rendererClass = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericHttp extends KalturaDeliveryProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $pattern = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericSilverLight extends KalturaDeliveryProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $pattern = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaDeliveryProfile
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileLiveAppleHttp extends KalturaDeliveryProfile
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $disableExtraAttributes = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $forceProxy = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileRtmp extends KalturaDeliveryProfile
{
	/**
	 * enforceRtmpe
	 *
	 * @var bool
	 */
	public $enforceRtmpe = null;

	/**
	 * a prefix that is added to all stream urls (replaces storageProfile::rtmpPrefix)
	 *
	 * @var string
	 */
	public $prefix = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileVodPackagerPlayServer extends KalturaDeliveryProfile
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $adStitchingEnabled = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryServerNode extends KalturaServerNode
{
	/**
	 * Delivery profile ids
	 *
	 * @var array of KalturaKeyValue
	 */
	public $deliveryProfileIds;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDirectoryRestriction extends KalturaBaseRestriction
{
	/**
	 * Kaltura directory restriction type
	 *
	 * @var KalturaDirectoryRestrictionType
	 */
	public $directoryRestrictionType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDrmEntryContextPluginData extends KalturaPluginData
{
	/**
	 * For the uDRM we give the drm context data which is a json encoding of an array containing the uDRM data
	 *      for each flavor that is required from this getContextData request.
	 *
	 * @var string
	 */
	public $flavorData = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaCategoryUserBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $permissionLevelEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $permissionLevelIn = null;

	/**
	 * 
	 *
	 * @var KalturaCategoryUserStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaUpdateMethodType
	 */
	public $updateMethodEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateMethodIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryFullIdsStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryFullIdsEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $permissionNamesMatchAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $permissionNamesMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $permissionNamesNotContains = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryUserFilter extends KalturaCategoryUserBaseFilter
{
	/**
	 * Return the list of categoryUser that are not inherited from parent category - only the direct categoryUsers.
	 *
	 * @var bool
	 */
	public $categoryDirectMembers = null;

	/**
	 * Free text search on user id or screen name
	 *
	 * @var string
	 */
	public $freeText = null;

	/**
	 * Return a list of categoryUser that related to the userId in this field by groups
	 *
	 * @var string
	 */
	public $relatedGroupsByUserId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaUserBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaUserType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $screenNameLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $screenNameStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $emailLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $emailStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var KalturaUserStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $firstNameStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastNameStartsWith = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isAdminEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserFilter extends KalturaUserBaseFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $idOrScreenNameStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $loginEnabledEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $roleIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $roleIdsEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $roleIdsIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $firstNameOrLastNameStartsWith = null;

	/**
	 * Permission names filter expression
	 *
	 * @var string
	 */
	public $permissionNamesMultiLikeOr = null;

	/**
	 * Permission names filter expression
	 *
	 * @var string
	 */
	public $permissionNamesMultiLikeAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryContext extends KalturaContext
{
	/**
	 * The entry ID in the context of which the playlist should be built
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * Is this a redirected entry followup?
	 *
	 * @var KalturaNullableBoolean
	 */
	public $followEntryRedirect = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryContextDataParams extends KalturaAccessControlScope
{
	/**
	 * Id of the current flavor.
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * The tags of the flavors that should be used for playback.
	 *
	 * @var string
	 */
	public $flavorTags = null;

	/**
	 * Playback streamer type: RTMP, HTTP, appleHttps, rtsp, sl.
	 *
	 * @var string
	 */
	public $streamerType = null;

	/**
	 * Protocol of the specific media object.
	 *
	 * @var string
	 */
	public $mediaProtocol = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryContextDataResult extends KalturaContextDataResult
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $isSiteRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isCountryRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isSessionRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isIpAddressRestricted = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isUserAgentRestricted = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $previewLength = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isScheduledNow = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isAdmin = null;

	/**
	 * http/rtmp/hdnetwork
	 *
	 * @var string
	 */
	public $streamerType = null;

	/**
	 * http/https, rtmp/rtmpe
	 *
	 * @var string
	 */
	public $mediaProtocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $storageProfilesXML = null;

	/**
	 * Array of messages as received from the access control rules that invalidated
	 *
	 * @var array of KalturaString
	 */
	public $accessControlMessages;

	/**
	 * Array of actions as received from the access control rules that invalidated
	 *
	 * @var array of KalturaRuleAction
	 */
	public $accessControlActions;

	/**
	 * Array of allowed flavor assets according to access control limitations and requested tags
	 *
	 * @var array of KalturaFlavorAsset
	 */
	public $flavorAssets;

	/**
	 * The duration of the entry in milliseconds
	 *
	 * @var int
	 */
	public $msDuration = null;

	/**
	 * Array of allowed flavor assets according to access control limitations and requested tags
	 *
	 * @var map
	 */
	public $pluginData;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryCuePointSearchFilter extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $cuePointsFreeText = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $cuePointTypeIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $cuePointSubTypeEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryIdentifier extends KalturaObjectIdentifier
{
	/**
	 * Identifier of the object
	 *
	 * @var KalturaEntryIdentifierField
	 */
	public $identifier = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryLiveStats extends KalturaLiveStats
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $peakAudience = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $peakDvrAudience = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaEntryServerNodeBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $serverNodeIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaEntryServerNodeStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var KalturaEntryServerNodeType
	 */
	public $serverTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverTypeIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryServerNodeListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaEntryServerNode
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaBooleanField extends KalturaBooleanValue
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFeatureStatusListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaFeatureStatus
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileAssetListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaFileAsset
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlattenJobData extends KalturaJobData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorAssetListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaFlavorAsset
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorParamsListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaFlavorParams
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorParamsOutputListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaFlavorParamsOutput
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericSyndicationFeed extends KalturaBaseSyndicationFeed
{
	/**
	 * feed description
	 *
	 * @var string
	 */
	public $feedDescription = null;

	/**
	 * feed landing page (i.e publisher website)
	 *
	 * @var string
	 */
	public $feedLandingPage = null;

	/**
	 * entry filter
	 *
	 * @var KalturaBaseEntryFilter
	 */
	public $entryFilter;

	/**
	 * page size
	 *
	 * @var int
	 */
	public $pageSize = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGoogleVideoSyndicationFeed extends KalturaBaseSyndicationFeed
{
	/**
	 * 
	 *
	 * @var KalturaGoogleSyndicationFeedAdultValues
	 */
	public $adultContent = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGroupUserListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaGroupUser
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaHashCondition extends KalturaCondition
{
	/**
	 * hash name
	 *
	 * @var string
	 */
	public $hashName = null;

	/**
	 * hash secret
	 *
	 * @var string
	 */
	public $hashSecret = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaITunesSyndicationFeed extends KalturaBaseSyndicationFeed
{
	/**
	 * feed description
	 *
	 * @var string
	 */
	public $feedDescription = null;

	/**
	 * feed language
	 *
	 * @var string
	 */
	public $language = null;

	/**
	 * feed landing page (i.e publisher website)
	 *
	 * @var string
	 */
	public $feedLandingPage = null;

	/**
	 * author/publisher name
	 *
	 * @var string
	 */
	public $ownerName = null;

	/**
	 * publisher email
	 *
	 * @var string
	 */
	public $ownerEmail = null;

	/**
	 * podcast thumbnail
	 *
	 * @var string
	 */
	public $feedImageUrl = null;

	/**
	 * 
	 *
	 * @var KalturaITunesSyndicationFeedCategories
	 * @readonly
	 */
	public $category = null;

	/**
	 * 
	 *
	 * @var KalturaITunesSyndicationFeedAdultValues
	 */
	public $adultContent = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $feedAuthor = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enforceFeedAuthor = null;

	/**
	 * true in case you want to enfore the palylist order on the
	 *
	 * @var KalturaNullableBoolean
	 */
	public $enforceOrder = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaImportJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSize = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIndexAdvancedFilter extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $indexIdGreaterThan = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $depthGreaterThanEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIndexJobData extends KalturaJobData
{
	/**
	 * The filter should return the list of objects that need to be reindexed.
	 *
	 * @var KalturaFilter
	 */
	public $filter;

	/**
	 * Indicates the last id that reindexed, used when the batch crached, to re-run from the last crash point.
	 *
	 * @var int
	 */
	public $lastIndexId = null;

	/**
	 * Indicates the last depth that reindexed, used when the batch crached, to re-run from the last crash point.
	 *
	 * @var int
	 */
	public $lastIndexDepth = null;

	/**
	 * Indicates that the object columns and attributes values should be recalculated before reindexed.
	 *
	 * @var bool
	 */
	public $shouldUpdate = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIpAddressRestriction extends KalturaBaseRestriction
{
	/**
	 * Ip address restriction type (Allow or deny)
	 *
	 * @var KalturaIpAddressRestrictionType
	 */
	public $ipAddressRestrictionType = null;

	/**
	 * Comma separated list of ip address to allow to deny
	 *
	 * @var string
	 */
	public $ipAddressList = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLimitFlavorsRestriction extends KalturaBaseRestriction
{
	/**
	 * Limit flavors restriction type (Allow or deny)
	 *
	 * @var KalturaLimitFlavorsRestrictionType
	 */
	public $limitFlavorsRestrictionType = null;

	/**
	 * Comma separated list of flavor params ids to allow to deny
	 *
	 * @var string
	 */
	public $flavorParamsIds = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaLiveChannel
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelSegmentListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaLiveChannelSegment
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryServerNode extends KalturaEntryServerNode
{
	/**
	 * parameters of the stream we got
	 *
	 * @var array of KalturaLiveStreamParams
	 */
	public $streams;

	/**
	 * 
	 *
	 * @var array of KalturaLiveEntryServerNodeRecordingInfo
	 */
	public $recordingInfo;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isPlayableUser = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveReportExportJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $timeReference = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $timeZoneOffset = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIds = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $outputPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recipientEmail = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStatsListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var KalturaLiveStats
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaLiveStreamEntry
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamPushPublishRTMPConfiguration extends KalturaLiveStreamPushPublishConfiguration
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $password = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $streamName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $applicationName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveToVodJobData extends KalturaJobData
{
	/**
	 * $vod Entry Id
	 *
	 * @var string
	 */
	public $vodEntryId = null;

	/**
	 * live Entry Id
	 *
	 * @var string
	 */
	public $liveEntryId = null;

	/**
	 * total VOD Duration
	 *
	 * @var float
	 */
	public $totalVodDuration = null;

	/**
	 * last Segment Duration
	 *
	 * @var float
	 */
	public $lastSegmentDuration = null;

	/**
	 * amf Array File Path
	 *
	 * @var string
	 */
	public $amfArray = null;

	/**
	 * last live to vod sync time
	 *
	 * @var int
	 */
	public $lastCuePointSyncTime = null;

	/**
	 * last segment drift
	 *
	 * @var int
	 */
	public $lastSegmentDrift = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMailJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var KalturaMailType
	 */
	public $mailType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mailPriority = null;

	/**
	 * 
	 *
	 * @var KalturaMailJobStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recipientName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $recipientEmail = null;

	/**
	 * kuserId
	 *
	 * @var int
	 */
	public $recipientId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fromName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fromEmail = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $bodyParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $subjectParams = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $templatePath = null;

	/**
	 * 
	 *
	 * @var KalturaLanguageCode
	 */
	public $language = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $campaignId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $minSendDate = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $isHtml = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $separator = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMatchCondition extends KalturaCondition
{
	/**
	 * 
	 *
	 * @var array of KalturaStringValue
	 */
	public $values;

	/**
	 * 
	 *
	 * @var KalturaMatchConditionType
	 */
	public $matchType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMediaInfoBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetIdEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaInfoListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaMediaInfo
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaMediaEntry
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMixListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaMixEntry
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaModerationFlagListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaModerationFlag
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMoveCategoryEntriesJobData extends KalturaJobData
{
	/**
	 * Source category id
	 *
	 * @var int
	 */
	public $srcCategoryId = null;

	/**
	 * Destination category id
	 *
	 * @var int
	 */
	public $destCategoryId = null;

	/**
	 * Saves the last category id that its entries moved completely
	 *      In case of crash the batch will restart from that point
	 *
	 * @var int
	 */
	public $lastMovedCategoryId = null;

	/**
	 * Saves the last page index of the child categories filter pager
	 *      In case of crash the batch will restart from that point
	 *
	 * @var int
	 */
	public $lastMovedCategoryPageIndex = null;

	/**
	 * Saves the last page index of the category entries filter pager
	 *      In case of crash the batch will restart from that point
	 *
	 * @var int
	 */
	public $lastMovedCategoryEntryPageIndex = null;

	/**
	 * All entries from all child categories will be moved as well
	 *
	 * @var bool
	 */
	public $moveFromChildren = null;

	/**
	 * Destination categories fallback ids
	 *
	 * @var string
	 */
	public $destCategoryFullIds = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaNotificationJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var KalturaNotificationType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeAsString = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * 
	 *
	 * @var KalturaNotificationStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $data = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $numberOfAttempts = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationResult = null;

	/**
	 * 
	 *
	 * @var KalturaNotificationObjectType
	 */
	public $objType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaObjectListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaObject
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaOrCondition extends KalturaCondition
{
	/**
	 * 
	 *
	 * @var array of KalturaCondition
	 */
	public $conditions;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaPartnerBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idNotIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * 
	 *
	 * @var KalturaPartnerStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackageEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackageGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerPackageLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerPackageIn = null;

	/**
	 * 
	 *
	 * @var KalturaPartnerGroupType
	 */
	public $partnerGroupTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerNameDescriptionWebsiteAdminNameAdminEmailLike = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaPartner
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionItemListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaPermissionItem
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaPermission
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaylistListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaPlaylist
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaProvisionJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $streamID = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $backupStreamID = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rtmp = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $encoderIP = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $backupEncoderIP = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $encoderPassword = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $encoderUsername = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endDate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $returnVal = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $mediaType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $primaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $secondaryBroadcastingUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $streamName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaQuizUserEntry extends KalturaUserEntry
{
	/**
	 * 
	 *
	 * @var float
	 * @readonly
	 */
	public $score = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaRecalculateCacheJobData extends KalturaJobData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRemotePathListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaRemotePath
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaReportBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportInputFilter extends KalturaReportInputBaseFilter
{
	/**
	 * Search keywords to filter objects
	 *
	 * @var string
	 */
	public $keywords = null;

	/**
	 * Search keywords in onjects tags
	 *
	 * @var bool
	 */
	public $searchInTags = null;

	/**
	 * Search keywords in onjects admin tags
	 *
	 * @var bool
	 */
	public $searchInAdminTags = null;

	/**
	 * Search onjects in specified categories
	 *
	 * @var string
	 */
	public $categories = null;

	/**
	 * Time zone offset in minutes
	 *
	 * @var int
	 */
	public $timeZoneOffset = null;

	/**
	 * Aggregated results according to interval
	 *
	 * @var KalturaReportInterval
	 */
	public $interval = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaReport
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaResponseProfileBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaResponseProfileStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfileHolder extends KalturaBaseResponseProfile
{
	/**
	 * Auto generated numeric identifier
	 *
	 * @var int
	 */
	public $id = null;

	/**
	 * Unique system name
	 *
	 * @var string
	 */
	public $systemName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfileListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaResponseProfile
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSchedulerListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaScheduler
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSchedulerWorkerListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaSchedulerWorker
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchCondition extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $field = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchOperator extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var KalturaSearchOperatorType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var array of KalturaSearchItem
	 */
	public $items;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaServerNodeBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $heartbeatTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $heartbeatTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $hostNameLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $hostNameMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $hostNameMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var KalturaServerNodeStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var KalturaServerNodeType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dcEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dcIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentIdLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentIdMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentIdMultiLikeAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaServerNodeListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaServerNode
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSessionResponse extends KalturaStartWidgetSessionResponse
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSessionRestriction extends KalturaBaseRestriction
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSiteRestriction extends KalturaBaseRestriction
{
	/**
	 * The site restriction type (allow or deny)
	 *
	 * @var KalturaSiteRestrictionType
	 */
	public $siteRestrictionType = null;

	/**
	 * Comma separated list of sites (domains) to allow or deny
	 *
	 * @var string
	 */
	public $siteList = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageAddAction extends KalturaRuleAction
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $serverUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverUsername = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverPassword = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverPrivateKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverPublicKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverPassPhrase = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $ftpPassiveMode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileEncryptionKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileSyncId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncStoredPath = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaStorageProfileBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var KalturaStorageProfileProtocol
	 */
	public $protocolEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $protocolIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageProfileListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaStorageProfile
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyncCategoryPrivacyContextJobData extends KalturaJobData
{
	/**
	 * category id
	 *
	 * @var int
	 */
	public $categoryId = null;

	/**
	 * Saves the last category entry creation date that was updated
	 *      In case of crash the batch will restart from that point
	 *
	 * @var int
	 */
	public $lastUpdatedCategoryEntryCreatedAt = null;

	/**
	 * Saves the last sub category creation date that was updated
	 *      In case of crash the batch will restart from that point
	 *
	 * @var int
	 */
	public $lastUpdatedCategoryCreatedAt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbAssetListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaThumbAsset
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParamsListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaThumbParams
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParamsOutputListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaThumbParamsOutput
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbnailServeOptions extends KalturaAssetServeOptions
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTubeMogulSyndicationFeed extends KalturaBaseSyndicationFeed
{
	/**
	 * 
	 *
	 * @var KalturaTubeMogulSyndicationFeedCategories
	 * @readonly
	 */
	public $category = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaUiConfBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameLike = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaUiConfObjType
	 */
	public $objTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaUiConfCreationMode
	 */
	public $creationModeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $creationModeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $versionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $versionMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $versionMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerTagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerTagsMultiLikeAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUiConfListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaUiConf
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaUploadTokenBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaUploadTokenStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNameEqual = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $fileSizeEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadTokenListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaUploadToken
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlRecognizerAkamaiG2O extends KalturaUrlRecognizer
{
	/**
	 * headerData
	 *
	 * @var string
	 */
	public $headerData = null;

	/**
	 * headerSign
	 *
	 * @var string
	 */
	public $headerSign = null;

	/**
	 * timeout
	 *
	 * @var int
	 */
	public $timeout = null;

	/**
	 * salt
	 *
	 * @var string
	 */
	public $salt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerAkamaiHttp extends KalturaUrlTokenizer
{
	/**
	 * param
	 *
	 * @var string
	 */
	public $paramName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootDir = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerAkamaiRtmp extends KalturaUrlTokenizer
{
	/**
	 * profile
	 *
	 * @var string
	 */
	public $profile = null;

	/**
	 * Type
	 *
	 * @var string
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aifp = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $usePrefix = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerAkamaiRtsp extends KalturaUrlTokenizer
{
	/**
	 * host
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * Cp-Code
	 *
	 * @var int
	 */
	public $cpcode = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerAkamaiSecureHd extends KalturaUrlTokenizer
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $paramName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aclPostfix = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $customPostfixes = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $useCookieHosts = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootDir = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerBitGravity extends KalturaUrlTokenizer
{
	/**
	 * hashPatternRegex
	 *
	 * @var string
	 */
	public $hashPatternRegex = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerChinaCache extends KalturaUrlTokenizer
{
	/**
	 * 
	 *
	 * @var KalturaChinaCacheAlgorithmType
	 */
	public $algorithmId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $keyId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerCht extends KalturaUrlTokenizer
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerCloudFront extends KalturaUrlTokenizer
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $keyPairId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootDir = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerKs extends KalturaUrlTokenizer
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $usePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $additionalUris = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerLevel3 extends KalturaUrlTokenizer
{
	/**
	 * paramName
	 *
	 * @var string
	 */
	public $paramName = null;

	/**
	 * expiryName
	 *
	 * @var string
	 */
	public $expiryName = null;

	/**
	 * gen
	 *
	 * @var string
	 */
	public $gen = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerLimeLight extends KalturaUrlTokenizer
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerUplynk extends KalturaUrlTokenizer
{
	/**
	 * accountId
	 *
	 * @var string
	 */
	public $accountId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerVelocix extends KalturaUrlTokenizer
{
	/**
	 * hdsPaths
	 *
	 * @var string
	 */
	public $hdsPaths = null;

	/**
	 * tokenParamName
	 *
	 * @var string
	 */
	public $paramName = null;

	/**
	 * secure URL prefix
	 *
	 * @var string
	 */
	public $authPrefix = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUrlTokenizerVnpt extends KalturaUrlTokenizer
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $tokenizationFormat = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $shouldIncludeClientIp = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserAgentRestriction extends KalturaBaseRestriction
{
	/**
	 * User agent restriction type (Allow or deny)
	 *
	 * @var KalturaUserAgentRestrictionType
	 */
	public $userAgentRestrictionType = null;

	/**
	 * A comma seperated list of user agent regular expressions
	 *
	 * @var string
	 */
	public $userAgentRegexList = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserEntryListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaUserEntry
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaUser
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserLoginDataListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaUserLoginData
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserRoleCondition extends KalturaCondition
{
	/**
	 * Comma separated list of role ids
	 *
	 * @var string
	 */
	public $roleIds = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserRoleListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaUserRole
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUsersCsvJobData extends KalturaJobData
{
	/**
	 * The filter should return the list of users that need to be specified in the csv.
	 *
	 * @var KalturaUserFilter
	 */
	public $filter;

	/**
	 * The metadata profile we should look the xpath in
	 *
	 * @var int
	 */
	public $metadataProfileId = null;

	/**
	 * The xpath to look in the metadataProfileId  and the wanted csv field name
	 *
	 * @var array of KalturaCsvAdditionalFieldInfo
	 */
	public $additionalFields;

	/**
	 * The users name
	 *
	 * @var string
	 */
	public $userName = null;

	/**
	 * The users email
	 *
	 * @var string
	 */
	public $userMail = null;

	/**
	 * The file location
	 *
	 * @var string
	 */
	public $outputPath = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaValidateActiveEdgeCondition extends KalturaCondition
{
	/**
	 * Comma separated list of edge servers to validate are active
	 *
	 * @var string
	 */
	public $edgeServerIds = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaWidgetBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceWidgetIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $rootWidgetIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uiConfIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerDataLike = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidgetListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaWidget
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaYahooSyndicationFeed extends KalturaBaseSyndicationFeed
{
	/**
	 * 
	 *
	 * @var KalturaYahooSyndicationFeedCategories
	 * @readonly
	 */
	public $category = null;

	/**
	 * 
	 *
	 * @var KalturaYahooSyndicationFeedAdultValues
	 */
	public $adultContent = null;

	/**
	 * feed description
	 *
	 * @var string
	 */
	public $feedDescription = null;

	/**
	 * feed landing page (i.e publisher website)
	 *
	 * @var string
	 */
	public $feedLandingPage = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAccessControlBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAccessControlProfileBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAkamaiProvisionJobData extends KalturaProvisionJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $wsdlUsername = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $wsdlPassword = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $cpcode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $emailId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $primaryContact = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $secondaryContact = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAkamaiUniversalProvisionJobData extends KalturaProvisionJobData
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $streamId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemUserName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemPassword = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $domainName = null;

	/**
	 * 
	 *
	 * @var KalturaDVRStatus
	 */
	public $dvrEnabled = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dvrWindow = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $primaryContact = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $secondaryContact = null;

	/**
	 * 
	 *
	 * @var KalturaAkamaiUniversalStreamType
	 */
	public $streamType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationEmail = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnonymousIPCondition extends KalturaMatchCondition
{
	/**
	 * The ip geo coder engine to be used
	 *
	 * @var KalturaGeoCoderType
	 */
	public $geoCoderType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAppTokenFilter extends KalturaAppTokenBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAssetParamsBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isSystemDefaultEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetResource extends KalturaContentResource
{
	/**
	 * ID of the source asset
	 *
	 * @var string
	 */
	public $assetId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseSyndicationFeedFilter extends KalturaBaseSyndicationFeedBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadFilter extends KalturaBulkUploadBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaCategoryBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idNotIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $parentIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parentIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $depthEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullNameStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullIdsEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullIdsStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullIdsMatchOr = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var KalturaAppearInListType
	 */
	public $appearInListEqual = null;

	/**
	 * 
	 *
	 * @var KalturaPrivacyType
	 */
	public $privacyEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $privacyIn = null;

	/**
	 * 
	 *
	 * @var KalturaInheritanceType
	 */
	public $inheritanceTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $inheritanceTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $referenceIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $referenceIdEmpty = null;

	/**
	 * 
	 *
	 * @var KalturaContributionPolicyType
	 */
	public $contributionPolicyEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $membersCountGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $membersCountLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $pendingMembersCountGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $pendingMembersCountLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $privacyContextEqual = null;

	/**
	 * 
	 *
	 * @var KalturaCategoryStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $inheritedParentIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $inheritedParentIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValueGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerSortValueLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aggregationCategoriesMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $aggregationCategoriesMultiLikeAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaCategoryEntryBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $categoryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoryFullIdsStartsWith = null;

	/**
	 * 
	 *
	 * @var KalturaCategoryEntryStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $creatorUserIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $creatorUserIdIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaControlPanelCommandFilter extends KalturaControlPanelCommandBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaConversionProfileAssetParamsBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $conversionProfileIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $conversionProfileIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $assetParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaFlavorReadyBehaviorType
	 */
	public $readyBehaviorEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $readyBehaviorIn = null;

	/**
	 * 
	 *
	 * @var KalturaAssetParamsOrigin
	 */
	public $originEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $originIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaConversionProfileBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var KalturaConversionProfileStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var KalturaConversionProfileType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultEntryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $defaultEntryIdIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConvertCollectionJobData extends KalturaConvartableJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $destDirLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destDirRemoteUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $inputXmlLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $inputXmlRemoteUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $commandLinesStr = null;

	/**
	 * 
	 *
	 * @var array of KalturaConvertCollectionFlavorData
	 */
	public $flavors;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConvertJobData extends KalturaConvartableJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncRemoteUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $logFileSyncLocalPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $logFileSyncRemoteUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $remoteMediaId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $customData = null;

	/**
	 * 
	 *
	 * @var array of KalturaDestFileSyncDescriptor
	 */
	public $extraDestFileSyncs;

	/**
	 * 
	 *
	 * @var string
	 */
	public $engineMessage = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCountryCondition extends KalturaMatchCondition
{
	/**
	 * The ip geo coder engine to be used
	 *
	 * @var KalturaGeoCoderType
	 */
	public $geoCoderType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileFilter extends KalturaDeliveryProfileBaseFilter
{
	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isLive = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericRtmp extends KalturaDeliveryProfileRtmp
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $pattern = null;

	/**
	 * rendererClass
	 *
	 * @var string
	 */
	public $rendererClass = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileVodPackagerHls extends KalturaDeliveryProfileVodPackagerPlayServer
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $allowFairplayOffline = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEdgeServerNode extends KalturaDeliveryServerNode
{
	/**
	 * Delivery server playback Domain
	 *
	 * @var string
	 */
	public $playbackDomain = null;

	/**
	 * Overdie edge server default configuration - json format
	 *
	 * @var string
	 */
	public $config = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEndUserReportInputFilter extends KalturaReportInputFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $application = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIds = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $playbackContext = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ancestorPlaybackContext = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryIndexAdvancedFilter extends KalturaIndexAdvancedFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryReferrerLiveStats extends KalturaEntryLiveStats
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $referrer = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryResource extends KalturaContentResource
{
	/**
	 * ID of the source entry
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * ID of the source flavor params, set to null to use the source flavor
	 *
	 * @var int
	 */
	public $flavorParamsId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryServerNodeFilter extends KalturaEntryServerNodeBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExtractMediaJobData extends KalturaConvartableJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $calculateComplexity = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $extractId3Tags = null;

	/**
	 * The data output file
	 *
	 * @var string
	 */
	public $destDataFilePath = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $detectGOP = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFairPlayPlaybackPluginData extends KalturaDrmPlaybackPluginData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $certificate = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaIntegerField extends KalturaIntegerValue
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFieldCompareCondition extends KalturaCompareCondition
{
	/**
	 * Field to evaluate
	 *
	 * @var KalturaIntegerField
	 */
	public $field;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaStringField extends KalturaStringValue
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFieldMatchCondition extends KalturaMatchCondition
{
	/**
	 * Field to evaluate
	 *
	 * @var KalturaStringField
	 */
	public $field;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaFileAssetBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaFileAssetObjectType
	 */
	public $fileAssetObjectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaFileAssetStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileSyncResource extends KalturaContentResource
{
	/**
	 * The object type of the file sync object
	 *
	 * @var int
	 */
	public $fileSyncObjectType = null;

	/**
	 * The object sub-type of the file sync object
	 *
	 * @var int
	 */
	public $objectSubType = null;

	/**
	 * The object id of the file sync object
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * The version of the file sync object
	 *
	 * @var string
	 */
	public $version = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericXsltSyndicationFeed extends KalturaGenericSyndicationFeed
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $xslt = null;

	/**
	 * 
	 *
	 * @var array of KalturaExtendingItemMrssParameter
	 */
	public $itemXpathsToExtend;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGeoDistanceCondition extends KalturaMatchCondition
{
	/**
	 * The ip geo coder engine to be used
	 *
	 * @var KalturaGeoCoderType
	 */
	public $geoCoderType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGeoTimeLiveStats extends KalturaEntryLiveStats
{
	/**
	 * 
	 *
	 * @var KalturaCoordinate
	 */
	public $city;

	/**
	 * 
	 *
	 * @var KalturaCoordinate
	 */
	public $country;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaGroupUserBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $groupIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $groupIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaGroupUserStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIpAddressCondition extends KalturaMatchCondition
{
	/**
	 * allow internal ips
	 *
	 * @var bool
	 */
	public $acceptInternalIps = null;

	/**
	 * http header name for extracting the ip
	 *
	 * @var string
	 */
	public $httpHeader = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveAsset extends KalturaFlavorAsset
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $multicastIP = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $multicastPort = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaLiveChannelSegmentBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaLiveChannelSegmentStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $channelIdIn = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $startTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $startTimeLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveParams extends KalturaFlavorParams
{
	/**
	 * Suffix to be added to the stream name after the entry id {entry_id}_{stream_suffix}, e.g. for entry id 0_kjdu5jr6 and suffix 1, the stream name will be 0_kjdu5jr6_1
	 *
	 * @var string
	 */
	public $streamSuffix = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaFlavorParams extends KalturaFlavorParams
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaInfoFilter extends KalturaMediaInfoBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMediaServerNode extends KalturaDeliveryServerNode
{
	/**
	 * Media server application name
	 *
	 * @var string
	 */
	public $applicationName = null;

	/**
	 * Media server playback port configuration by protocol and format
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mediaServerPortConfig;

	/**
	 * Media server playback Domain configuration by protocol and format
	 *
	 * @var array of KalturaKeyValue
	 */
	public $mediaServerPlaybackDomainConfig;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaOperationResource extends KalturaContentResource
{
	/**
	 * Only KalturaEntryResource and KalturaAssetResource are supported
	 *
	 * @var KalturaContentResource
	 */
	public $resource;

	/**
	 * 
	 *
	 * @var array of KalturaOperationAttributes
	 */
	public $operationAttributes;

	/**
	 * ID of alternative asset params to be used instead of the system default flavor params
	 *
	 * @var int
	 */
	public $assetParamsId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerFilter extends KalturaPartnerBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaPermissionBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var KalturaPermissionType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $friendlyNameLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionLike = null;

	/**
	 * 
	 *
	 * @var KalturaPermissionStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dependsOnPermissionNamesMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dependsOnPermissionNamesMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaPermissionItemBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var KalturaPermissionItemType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $typeIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaybackContextOptions extends KalturaEntryContextDataParams
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPostConvertJobData extends KalturaConvartableJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * Indicates if a thumbnail should be created
	 *
	 * @var bool
	 */
	public $createThumb = null;

	/**
	 * The path of the created thumbnail
	 *
	 * @var string
	 */
	public $thumbPath = null;

	/**
	 * The position of the thumbnail in the media file
	 *
	 * @var int
	 */
	public $thumbOffset = null;

	/**
	 * The height of the movie, will be used to comapare if this thumbnail is the best we can have
	 *
	 * @var int
	 */
	public $thumbHeight = null;

	/**
	 * The bit rate of the movie, will be used to comapare if this thumbnail is the best we can have
	 *
	 * @var int
	 */
	public $thumbBitrate = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $customData = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPreviewRestriction extends KalturaSessionRestriction
{
	/**
	 * The preview restriction length
	 *
	 * @var int
	 */
	public $previewLength = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRecalculateResponseProfileCacheJobData extends KalturaRecalculateCacheJobData
{
	/**
	 * http / https
	 *
	 * @var string
	 */
	public $protocol = null;

	/**
	 * 
	 *
	 * @var KalturaSessionType
	 */
	public $ksType = null;

	/**
	 * 
	 *
	 * @var array of KalturaIntegerValue
	 */
	public $userRoles;

	/**
	 * Class name
	 *
	 * @var string
	 */
	public $cachedObjectType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $startObjectKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $endObjectKey = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaRegexCondition extends KalturaMatchCondition
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRemoteStorageResources extends KalturaContentResource
{
	/**
	 * Array of remote stoage resources
	 *
	 * @var array of KalturaRemoteStorageResource
	 */
	public $resources;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaResponseProfileFilter extends KalturaResponseProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaSearchComparableAttributeCondition extends KalturaAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchComparableCondition extends KalturaSearchCondition
{
	/**
	 * 
	 *
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaSearchMatchAttributeCondition extends KalturaAttributeCondition
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $not = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchMatchCondition extends KalturaSearchCondition
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $not = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaServerNodeFilter extends KalturaServerNodeBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSiteCondition extends KalturaMatchCondition
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSshImportJobData extends KalturaImportJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $privateKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $publicKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $passPhrase = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageDeleteJobData extends KalturaStorageJobData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageExportJobData extends KalturaStorageJobData
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $force = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $createLink = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStorageProfileFilter extends KalturaStorageProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStringResource extends KalturaContentResource
{
	/**
	 * Textual content
	 *
	 * @var string
	 */
	public $content = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUiConfFilter extends KalturaUiConfBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadTokenFilter extends KalturaUploadTokenBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaUserEntryBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idNotIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdNotIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdNotIn = null;

	/**
	 * 
	 *
	 * @var KalturaUserEntryStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaUserEntryType
	 */
	public $typeEqual = null;

	/**
	 * 
	 *
	 * @var KalturaUserEntryExtendedStatus
	 */
	public $extendedStatusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $extendedStatusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $extendedStatusNotIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaUserLoginDataBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $loginEmailEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaUserRoleBaseFilter extends KalturaRelatedFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $systemNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptionLike = null;

	/**
	 * 
	 *
	 * @var KalturaUserRoleStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidgetFilter extends KalturaWidgetBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlFilter extends KalturaAccessControlBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlProfileFilter extends KalturaAccessControlProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAmazonS3StorageExportJobData extends KalturaStorageExportJobData
{
	/**
	 * 
	 *
	 * @var KalturaAmazonS3StorageProfileFilesPermissionLevel
	 */
	public $filesPermissionInS3 = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $s3Region = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sseType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $sseKmsKeyId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $signatureType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $endPoint = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAmazonS3StorageProfileBaseFilter extends KalturaStorageProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnonymousIPContextField extends KalturaStringField
{
	/**
	 * The ip geo coder engine to be used
	 *
	 * @var KalturaGeoCoderType
	 */
	public $geoCoderType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetParamsFilter extends KalturaAssetParamsBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaBaseEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBaseEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaBaseEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBatchJobFilterExt extends KalturaBatchJobFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $jobTypeAndSubTypeIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryEntryFilter extends KalturaCategoryEntryBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryFilter extends KalturaCategoryBaseFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $freeText = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $membersIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $nameOrReferenceIdStartsWith = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $managerEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $memberEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fullNameStartsWithIn = null;

	/**
	 * not includes the category itself (only sub categories)
	 *
	 * @var string
	 */
	public $ancestorIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idOrInheritedParentIdIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaConstantXsltSyndicationFeed extends KalturaGenericXsltSyndicationFeed
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileFilter extends KalturaConversionProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileAssetParamsFilter extends KalturaConversionProfileAssetParamsBaseFilter
{
	/**
	 * 
	 *
	 * @var KalturaConversionProfileFilter
	 */
	public $conversionProfileIdFilter;

	/**
	 * 
	 *
	 * @var KalturaAssetParamsFilter
	 */
	public $assetParamsIdFilter;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCoordinatesContextField extends KalturaStringField
{
	/**
	 * The ip geo coder engine to be used
	 *
	 * @var KalturaGeoCoderType
	 */
	public $geoCoderType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCountryContextField extends KalturaStringField
{
	/**
	 * The ip geo coder engine to be used
	 *
	 * @var KalturaGeoCoderType
	 */
	public $geoCoderType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDataEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaDataEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDataEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaDataEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileAkamaiAppleHttpManifestBaseFilter extends KalturaDeliveryProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileAkamaiHdsBaseFilter extends KalturaDeliveryProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileAkamaiHttpBaseFilter extends KalturaDeliveryProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileGenericAppleHttpBaseFilter extends KalturaDeliveryProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileGenericHdsBaseFilter extends KalturaDeliveryProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileGenericHttpBaseFilter extends KalturaDeliveryProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileGenericSilverLightBaseFilter extends KalturaDeliveryProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileLiveAppleHttpBaseFilter extends KalturaDeliveryProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileRtmpBaseFilter extends KalturaDeliveryProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryServerNodeBaseFilter extends KalturaServerNodeFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDocumentEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaDocumentEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDocumentEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaDocumentEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEvalBooleanField extends KalturaBooleanField
{
	/**
	 * PHP code
	 *
	 * @var string
	 */
	public $code = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEvalStringField extends KalturaStringField
{
	/**
	 * PHP code
	 *
	 * @var string
	 */
	public $code = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaExternalMediaEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExternalMediaEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaExternalMediaEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileAssetFilter extends KalturaFileAssetBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaGenericDataCenterContentResource extends KalturaDataCenterContentResource
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaGenericSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaGoogleVideoSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGroupUserFilter extends KalturaGroupUserBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaITunesSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaIpAddressContextField extends KalturaStringField
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaLiveChannelCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaLiveChannelMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelSegmentFilter extends KalturaLiveChannelSegmentBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaLiveEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaLiveEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryServerNodeBaseFilter extends KalturaEntryServerNodeFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaLiveStreamAdminEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaLiveStreamAdminEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaLiveStreamEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaLiveStreamEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaMediaEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaMediaEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaFlavorParamsOutput extends KalturaFlavorParamsOutput
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMixEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaMixEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMixEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaMixEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaObjectIdField extends KalturaStringField
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionFilter extends KalturaPermissionBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionItemFilter extends KalturaPermissionItemBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlayableEntryCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaPlayableEntryCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlayableEntryMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaPlayableEntryMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaylistCompareAttributeCondition extends KalturaSearchComparableAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaPlaylistCompareAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaylistMatchAttributeCondition extends KalturaSearchMatchAttributeCondition
{
	/**
	 * 
	 *
	 * @var KalturaPlaylistMatchAttribute
	 */
	public $attribute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSshUrlResource extends KalturaUrlResource
{
	/**
	 * SSH private key
	 *
	 * @var string
	 */
	public $privateKey = null;

	/**
	 * SSH public key
	 *
	 * @var string
	 */
	public $publicKey = null;

	/**
	 * Passphrase for SSH keys
	 *
	 * @var string
	 */
	public $keyPassphrase = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTimeContextField extends KalturaIntegerField
{
	/**
	 * Time offset in seconds since current time
	 *
	 * @var int
	 */
	public $offset = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaTubeMogulSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserAgentCondition extends KalturaRegexCondition
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserAgentContextField extends KalturaStringField
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserEmailContextField extends KalturaStringField
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserEntryFilter extends KalturaUserEntryBaseFilter
{
	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $userIdEqualCurrent = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isAnonymous = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $privacyContextEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $privacyContextIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserLoginDataFilter extends KalturaUserLoginDataBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserRoleFilter extends KalturaUserRoleBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWebcamTokenResource extends KalturaDataCenterContentResource
{
	/**
	 * Token that returned from media server such as FMS or red5.
	 *
	 * @var string
	 */
	public $token = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaYahooSyndicationFeedBaseFilter extends KalturaBaseSyndicationFeedFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAdminUserBaseFilter extends KalturaUserFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAmazonS3StorageProfileFilter extends KalturaAmazonS3StorageProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaApiActionPermissionItemBaseFilter extends KalturaPermissionItemFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaApiParameterPermissionItemBaseFilter extends KalturaPermissionItemFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAssetParamsOutputBaseFilter extends KalturaAssetParamsFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDataEntryBaseFilter extends KalturaBaseEntryFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileAkamaiAppleHttpManifestFilter extends KalturaDeliveryProfileAkamaiAppleHttpManifestBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileAkamaiHdsFilter extends KalturaDeliveryProfileAkamaiHdsBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileAkamaiHttpFilter extends KalturaDeliveryProfileAkamaiHttpBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericAppleHttpFilter extends KalturaDeliveryProfileGenericAppleHttpBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericHdsFilter extends KalturaDeliveryProfileGenericHdsBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericHttpFilter extends KalturaDeliveryProfileGenericHttpBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericSilverLightFilter extends KalturaDeliveryProfileGenericSilverLightBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileLiveAppleHttpFilter extends KalturaDeliveryProfileLiveAppleHttpBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileRtmpFilter extends KalturaDeliveryProfileRtmpBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryServerNodeFilter extends KalturaDeliveryServerNodeBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaFlavorAssetBaseFilter extends KalturaAssetFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaFlavorAssetStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusNotIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaFlavorParamsBaseFilter extends KalturaAssetParamsFilter
{
	/**
	 * 
	 *
	 * @var KalturaContainerFormat
	 */
	public $formatEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericSyndicationFeedFilter extends KalturaGenericSyndicationFeedBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGoogleVideoSyndicationFeedFilter extends KalturaGoogleVideoSyndicationFeedBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaITunesSyndicationFeedFilter extends KalturaITunesSyndicationFeedBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryServerNodeFilter extends KalturaLiveEntryServerNodeBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaOperaSyndicationFeed extends KalturaConstantXsltSyndicationFeed
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaPlaylistBaseFilter extends KalturaBaseEntryFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaQuizUserEntryBaseFilter extends KalturaUserEntryFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRokuSyndicationFeed extends KalturaConstantXsltSyndicationFeed
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaServerFileResource extends KalturaGenericDataCenterContentResource
{
	/**
	 * Full path to the local file
	 *
	 * @var string
	 */
	public $localFilePath = null;

	/**
	 * Should keep original file (false = mv, true = cp)
	 *
	 * @var bool
	 */
	public $keepOriginalFile = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaThumbAssetBaseFilter extends KalturaAssetFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $thumbParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbParamsIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaThumbAssetStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusNotIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaThumbParamsBaseFilter extends KalturaAssetParamsFilter
{
	/**
	 * 
	 *
	 * @var KalturaContainerFormat
	 */
	public $formatEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTubeMogulSyndicationFeedFilter extends KalturaTubeMogulSyndicationFeedBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadedFileTokenResource extends KalturaGenericDataCenterContentResource
{
	/**
	 * Token that returned from upload.upload action or uploadToken.add action.
	 *
	 * @var string
	 */
	public $token = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaYahooSyndicationFeedFilter extends KalturaYahooSyndicationFeedBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAdminUserFilter extends KalturaAdminUserBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaApiActionPermissionItemFilter extends KalturaApiActionPermissionItemBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaApiParameterPermissionItemFilter extends KalturaApiParameterPermissionItemBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetParamsOutputFilter extends KalturaAssetParamsOutputBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDataEntryFilter extends KalturaDataEntryBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDeliveryProfileGenericRtmpBaseFilter extends KalturaDeliveryProfileRtmpFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaEdgeServerNodeBaseFilter extends KalturaDeliveryServerNodeFilter
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $playbackDomainLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $playbackDomainMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $playbackDomainMultiLikeAnd = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorAssetFilter extends KalturaFlavorAssetBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorParamsFilter extends KalturaFlavorParamsBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaGenericXsltSyndicationFeedBaseFilter extends KalturaGenericSyndicationFeedFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntry extends KalturaLiveStreamEntry
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMediaServerNodeBaseFilter extends KalturaDeliveryServerNodeFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPlaylistFilter extends KalturaPlaylistBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbAssetFilter extends KalturaThumbAssetBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParamsFilter extends KalturaThumbParamsBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDeliveryProfileGenericRtmpFilter extends KalturaDeliveryProfileGenericRtmpBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEdgeServerNodeFilter extends KalturaEdgeServerNodeBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaFlavorParamsOutputBaseFilter extends KalturaFlavorParamsFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $flavorParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsVersionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetVersionEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericXsltSyndicationFeedFilter extends KalturaGenericXsltSyndicationFeedBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaLiveAssetBaseFilter extends KalturaFlavorAssetFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaLiveParamsBaseFilter extends KalturaFlavorParamsFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMediaFlavorParamsBaseFilter extends KalturaFlavorParamsFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaServerNodeFilter extends KalturaMediaServerNodeBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMixEntryBaseFilter extends KalturaPlayableEntryFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaThumbParamsOutputBaseFilter extends KalturaThumbParamsFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $thumbParamsIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbParamsVersionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $thumbAssetVersionEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorParamsOutputFilter extends KalturaFlavorParamsOutputBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveAssetFilter extends KalturaLiveAssetBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveParamsFilter extends KalturaLiveParamsBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaFlavorParamsFilter extends KalturaMediaFlavorParamsBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMixEntryFilter extends KalturaMixEntryBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParamsOutputFilter extends KalturaThumbParamsOutputBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaLiveEntryBaseFilter extends KalturaMediaEntryFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMediaFlavorParamsOutputBaseFilter extends KalturaFlavorParamsOutputFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveEntryFilter extends KalturaLiveEntryBaseFilter
{
	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isLive = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $isRecordedEntryIdEmpty = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $hasMediaServerHostname = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaFlavorParamsOutputFilter extends KalturaMediaFlavorParamsOutputBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaLiveChannelBaseFilter extends KalturaLiveEntryFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaLiveStreamEntryBaseFilter extends KalturaLiveEntryFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelFilter extends KalturaLiveChannelBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamEntryFilter extends KalturaLiveStreamEntryBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaLiveStreamAdminEntryBaseFilter extends KalturaLiveStreamEntryFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveStreamAdminEntryFilter extends KalturaLiveStreamAdminEntryBaseFilter
{

}

