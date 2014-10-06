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
// Copyright (C) 2006-2011  Kaltura Inc.
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
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");
require_once(dirname(__FILE__) . "/KalturaMetadataClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionAction
{
	const SUBMIT = 1;
	const UPDATE = 2;
	const DELETE = 3;
	const FETCH_REPORT = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionErrorType
{
	const MISSING_FLAVOR = 1;
	const MISSING_THUMBNAIL = 2;
	const MISSING_METADATA = 3;
	const INVALID_DATA = 4;
	const MISSING_ASSET = 5;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionFieldRequiredStatus
{
	const NOT_REQUIRED = 0;
	const REQUIRED_BY_PROVIDER = 1;
	const REQUIRED_BY_PARTNER = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProfileActionStatus
{
	const DISABLED = 1;
	const AUTOMATIC = 2;
	const MANUAL = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProfileStatus
{
	const DISABLED = 1;
	const ENABLED = 2;
	const DELETED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProtocol
{
	const FTP = 1;
	const SCP = 2;
	const SFTP = 3;
	const HTTP = 4;
	const HTTPS = 5;
	const ASPERA = 10;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionValidationErrorType
{
	const CUSTOM_ERROR = 0;
	const STRING_EMPTY = 1;
	const STRING_TOO_LONG = 2;
	const STRING_TOO_SHORT = 3;
	const INVALID_FORMAT = 4;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryDistributionFlag
{
	const NONE = 0;
	const SUBMIT_REQUIRED = 1;
	const DELETE_REQUIRED = 2;
	const UPDATE_REQUIRED = 3;
	const ENABLE_REQUIRED = 4;
	const DISABLE_REQUIRED = 5;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryDistributionStatus
{
	const PENDING = 0;
	const QUEUED = 1;
	const READY = 2;
	const DELETED = 3;
	const SUBMITTING = 4;
	const UPDATING = 5;
	const DELETING = 6;
	const ERROR_SUBMITTING = 7;
	const ERROR_UPDATING = 8;
	const ERROR_DELETING = 9;
	const REMOVED = 10;
	const IMPORT_SUBMITTING = 11;
	const IMPORT_UPDATING = 12;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryDistributionSunStatus
{
	const BEFORE_SUNRISE = 1;
	const AFTER_SUNRISE = 2;
	const AFTER_SUNSET = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderParser
{
	const XSL = 1;
	const XPATH = 2;
	const REGEX = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderStatus
{
	const ACTIVE = 2;
	const DELETED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConfigurableDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProviderOrderBy
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProviderType
{
	const ATT_UVERSE = "attUverseDistribution.ATT_UVERSE";
	const AVN = "avnDistribution.AVN";
	const COMCAST_MRSS = "comcastMrssDistribution.COMCAST_MRSS";
	const CROSS_KALTURA = "crossKalturaDistribution.CROSS_KALTURA";
	const DAILYMOTION = "dailymotionDistribution.DAILYMOTION";
	const DOUBLECLICK = "doubleClickDistribution.DOUBLECLICK";
	const FREEWHEEL = "freewheelDistribution.FREEWHEEL";
	const FREEWHEEL_GENERIC = "freewheelGenericDistribution.FREEWHEEL_GENERIC";
	const FTP = "ftpDistribution.FTP";
	const FTP_SCHEDULED = "ftpDistribution.FTP_SCHEDULED";
	const HULU = "huluDistribution.HULU";
	const IDETIC = "ideticDistribution.IDETIC";
	const METRO_PCS = "metroPcsDistribution.METRO_PCS";
	const MSN = "msnDistribution.MSN";
	const NDN = "ndnDistribution.NDN";
	const PODCAST = "podcastDistribution.PODCAST";
	const QUICKPLAY = "quickPlayDistribution.QUICKPLAY";
	const SYNACOR_HBO = "synacorHboDistribution.SYNACOR_HBO";
	const TIME_WARNER = "timeWarnerDistribution.TIME_WARNER";
	const TVCOM = "tvComDistribution.TVCOM";
	const UVERSE_CLICK_TO_ORDER = "uverseClickToOrderDistribution.UVERSE_CLICK_TO_ORDER";
	const UVERSE = "uverseDistribution.UVERSE";
	const VERIZON_VCAST = "verizonVcastDistribution.VERIZON_VCAST";
	const YAHOO = "yahooDistribution.YAHOO";
	const YOUTUBE = "youTubeDistribution.YOUTUBE";
	const YOUTUBE_API = "youtubeApiDistribution.YOUTUBE_API";
	const GENERIC = "1";
	const SYNDICATION = "2";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryDistributionOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const SUBMITTED_AT_ASC = "+submittedAt";
	const SUNRISE_ASC = "+sunrise";
	const SUNSET_ASC = "+sunset";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const SUBMITTED_AT_DESC = "-submittedAt";
	const SUNRISE_DESC = "-sunrise";
	const SUNSET_DESC = "-sunset";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderActionOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationDistributionProfileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationDistributionProviderOrderBy
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAssetDistributionCondition extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAssetDistributionRule extends KalturaObjectBase
{
	/**
	 * The validation error description that will be set on the "data" property on KalturaDistributionValidationErrorMissingAsset if rule was not fulfilled
	 * 	 
	 *
	 * @var string
	 */
	public $validationError = null;

	/**
	 * An array of asset distribution conditions
	 * 	 
	 *
	 * @var array of KalturaAssetDistributionCondition
	 */
	public $assetDistributionConditions;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionFieldConfig extends KalturaObjectBase
{
	/**
	 * A value taken from a connector field enum which associates the current configuration to that connector field
	 *      Field enum class should be returned by the provider's getFieldEnumClass function.
	 *      
	 *
	 * @var string
	 */
	public $fieldName = null;

	/**
	 * A string that will be shown to the user as the field name in error messages related to the current field
	 *      
	 *
	 * @var string
	 */
	public $userFriendlyFieldName = null;

	/**
	 * An XSLT string that extracts the right value from the Kaltura entry MRSS XML.
	 *      The value of the current connector field will be the one that is returned from transforming the Kaltura entry MRSS XML using this XSLT string.
	 *      
	 *
	 * @var string
	 */
	public $entryMrssXslt = null;

	/**
	 * Is the field required to have a value for submission ?
	 *      
	 *
	 * @var KalturaDistributionFieldRequiredStatus
	 */
	public $isRequired = null;

	/**
	 * Trigger distribution update when this field changes or not ?
	 *      
	 *
	 * @var bool
	 */
	public $updateOnChange = null;

	/**
	 * Entry column or metadata xpath that should trigger an update
	 *      
	 *
	 * @var array of KalturaString
	 */
	public $updateParams;

	/**
	 * Is this field config is the default for the distribution provider?
	 *      
	 *
	 * @var bool
	 * @readonly
	 */
	public $isDefault = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDistributionJobProviderData extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionThumbDimensions extends KalturaObjectBase
{
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


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDistributionProfile extends KalturaObjectBase
{
	/**
	 * Auto generated unique id
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Profile creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Profile last update date as Unix timestamp (In seconds)
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
	 * @var KalturaDistributionProviderType
	 * @insertonly
	 */
	public $providerType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionProfileStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $submitEnabled = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $updateEnabled = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $deleteEnabled = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionProfileActionStatus
	 */
	public $reportEnabled = null;

	/**
	 * Comma separated flavor params ids that should be auto converted
	 * 	 
	 *
	 * @var string
	 */
	public $autoCreateFlavors = null;

	/**
	 * Comma separated thumbnail params ids that should be auto generated
	 * 	 
	 *
	 * @var string
	 */
	public $autoCreateThumb = null;

	/**
	 * Comma separated flavor params ids that should be submitted if ready
	 * 	 
	 *
	 * @var string
	 */
	public $optionalFlavorParamsIds = null;

	/**
	 * Comma separated flavor params ids that required to be ready before submission
	 * 	 
	 *
	 * @var string
	 */
	public $requiredFlavorParamsIds = null;

	/**
	 * Thumbnail dimensions that should be submitted if ready
	 * 	 
	 *
	 * @var array of KalturaDistributionThumbDimensions
	 */
	public $optionalThumbDimensions;

	/**
	 * Thumbnail dimensions that required to be readt before submission
	 * 	 
	 *
	 * @var array of KalturaDistributionThumbDimensions
	 */
	public $requiredThumbDimensions;

	/**
	 * Asset Distribution Rules for assets that should be submitted if ready
	 * 	 
	 *
	 * @var array of KalturaAssetDistributionRule
	 */
	public $optionalAssetDistributionRules;

	/**
	 * Assets Asset Distribution Rules for assets that are required to be ready before submission
	 * 	 
	 *
	 * @var array of KalturaAssetDistributionRule
	 */
	public $requiredAssetDistributionRules;

	/**
	 * If entry distribution sunrise not specified that will be the default since entry creation time, in seconds
	 * 	 
	 *
	 * @var int
	 */
	public $sunriseDefaultOffset = null;

	/**
	 * If entry distribution sunset not specified that will be the default since entry creation time, in seconds
	 * 	 
	 *
	 * @var int
	 */
	public $sunsetDefaultOffset = null;

	/**
	 * The best external storage to be used to download the asset files from
	 * 	 
	 *
	 * @var int
	 */
	public $recommendedStorageProfileForDownload = null;

	/**
	 * The best Kaltura data center to be used to download the asset files to
	 * 	 
	 *
	 * @var int
	 */
	public $recommendedDcForDownload = null;

	/**
	 * The best Kaltura data center to be used to execute the distribution job
	 * 	 
	 *
	 * @var int
	 */
	public $recommendedDcForExecute = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProfileListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaDistributionProfile
	 * @readonly
	 */
	public $objects;

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
abstract class KalturaDistributionProvider extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaDistributionProviderType
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
	 * @var bool
	 */
	public $scheduleUpdateEnabled = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $availabilityUpdateEnabled = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $deleteInsteadUpdate = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $intervalBeforeSunrise = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $intervalBeforeSunset = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateRequiredEntryFields = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateRequiredMetadataXPaths = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProviderListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaDistributionProvider
	 * @readonly
	 */
	public $objects;

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
class KalturaDistributionRemoteMediaFile extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $version = null;

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
	public $remoteId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDistributionValidationError extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaDistributionAction
	 */
	public $action = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionErrorType
	 */
	public $errorType = null;

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
class KalturaEntryDistribution extends KalturaObjectBase
{
	/**
	 * Auto generated unique id
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Entry distribution creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Entry distribution last update date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;

	/**
	 * Entry distribution submission date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $submittedAt = null;

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
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var int
	 * @insertonly
	 */
	public $distributionProfileId = null;

	/**
	 * 
	 *
	 * @var KalturaEntryDistributionStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaEntryDistributionSunStatus
	 * @readonly
	 */
	public $sunStatus = null;

	/**
	 * 
	 *
	 * @var KalturaEntryDistributionFlag
	 * @readonly
	 */
	public $dirtyStatus = null;

	/**
	 * Comma separated thumbnail asset ids
	 * 	 
	 *
	 * @var string
	 */
	public $thumbAssetIds = null;

	/**
	 * Comma separated flavor asset ids
	 * 	 
	 *
	 * @var string
	 */
	public $flavorAssetIds = null;

	/**
	 * Comma separated asset ids
	 * 	 
	 *
	 * @var string
	 */
	public $assetIds = null;

	/**
	 * Entry distribution publish time as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 */
	public $sunrise = null;

	/**
	 * Entry distribution un-publish time as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 */
	public $sunset = null;

	/**
	 * The id as returned from the distributed destination
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $remoteId = null;

	/**
	 * The plays as retrieved from the remote destination reports
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $plays = null;

	/**
	 * The views as retrieved from the remote destination reports
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $views = null;

	/**
	 * 
	 *
	 * @var array of KalturaDistributionValidationError
	 */
	public $validationErrors;

	/**
	 * 
	 *
	 * @var KalturaBatchJobErrorTypes
	 * @readonly
	 */
	public $errorType = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $errorNumber = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $errorDescription = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $hasSubmitResultsLog = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $hasSubmitSentDataLog = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $hasUpdateResultsLog = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $hasUpdateSentDataLog = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $hasDeleteResultsLog = null;

	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $hasDeleteSentDataLog = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryDistributionListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaEntryDistribution
	 * @readonly
	 */
	public $objects;

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
class KalturaGenericDistributionProfileAction extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaDistributionProtocol
	 */
	public $protocol = null;

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
	public $serverPath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $username = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $password = null;

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
	public $httpFieldName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $httpFileName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderAction extends KalturaObjectBase
{
	/**
	 * Auto generated
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Generic distribution provider action creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Generic distribution provider action last update date as Unix timestamp (In seconds)
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
	 * @insertonly
	 */
	public $genericDistributionProviderId = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionAction
	 * @insertonly
	 */
	public $action = null;

	/**
	 * 
	 *
	 * @var KalturaGenericDistributionProviderStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaGenericDistributionProviderParser
	 */
	public $resultsParser = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionProtocol
	 */
	public $protocol = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverAddress = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $remotePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $remoteUsername = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $remotePassword = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $editableFields = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $mandatoryFields = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $mrssTransformer = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $mrssValidator = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $resultsTransformer = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderActionListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaGenericDistributionProviderAction
	 * @readonly
	 */
	public $objects;

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
class KalturaGenericDistributionProvider extends KalturaDistributionProvider
{
	/**
	 * Auto generated
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * Generic distribution provider creation date as Unix timestamp (In seconds)
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * Generic distribution provider last update date as Unix timestamp (In seconds)
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
	 * @var bool
	 */
	public $isDefault = null;

	/**
	 * 
	 *
	 * @var KalturaGenericDistributionProviderStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $optionalFlavorParamsIds = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $requiredFlavorParamsIds = null;

	/**
	 * 
	 *
	 * @var array of KalturaDistributionThumbDimensions
	 */
	public $optionalThumbDimensions;

	/**
	 * 
	 *
	 * @var array of KalturaDistributionThumbDimensions
	 */
	public $requiredThumbDimensions;

	/**
	 * 
	 *
	 * @var string
	 */
	public $editableFields = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $mandatoryFields = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaGenericDistributionProvider
	 * @readonly
	 */
	public $objects;

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
class KalturaAssetDistributionPropertyCondition extends KalturaAssetDistributionCondition
{
	/**
	 * The property name to look for, this will match to a getter on the asset object.
	 * 	 Should be camelCase naming convention (defining "myPropertyName" will look for getMyPropertyName())
	 * 	 
	 *
	 * @var string
	 */
	public $propertyName = null;

	/**
	 * The value to compare
	 * 	 
	 *
	 * @var string
	 */
	public $propertyValue = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaConfigurableDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $fieldValues = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaConfigurableDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * 
	 *
	 * @var array of KalturaDistributionFieldConfig
	 */
	public $fieldConfigArray;

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
class KalturaContentDistributionSearchItem extends KalturaSearchItem
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $noDistributionProfiles = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $distributionProfileId = null;

	/**
	 * 
	 *
	 * @var KalturaEntryDistributionSunStatus
	 */
	public $distributionSunStatus = null;

	/**
	 * 
	 *
	 * @var KalturaEntryDistributionFlag
	 */
	public $entryDistributionFlag = null;

	/**
	 * 
	 *
	 * @var KalturaEntryDistributionStatus
	 */
	public $entryDistributionStatus = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $hasEntryDistributionValidationErrors = null;

	/**
	 * Comma seperated validation error types
	 * 	 
	 *
	 * @var string
	 */
	public $entryDistributionValidationErrors = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $distributionProfileId = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionProfile
	 */
	public $distributionProfile;

	/**
	 * 
	 *
	 * @var int
	 */
	public $entryDistributionId = null;

	/**
	 * 
	 *
	 * @var KalturaEntryDistribution
	 */
	public $entryDistribution;

	/**
	 * Id of the media in the remote system
	 * 	 
	 *
	 * @var string
	 */
	public $remoteId = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionProviderType
	 */
	public $providerType = null;

	/**
	 * Additional data that relevant for the provider only
	 * 	 
	 *
	 * @var KalturaDistributionJobProviderData
	 */
	public $providerData;

	/**
	 * The results as returned from the remote destination
	 * 	 
	 *
	 * @var string
	 */
	public $results = null;

	/**
	 * The data as sent to the remote destination
	 * 	 
	 *
	 * @var string
	 */
	public $sentData = null;

	/**
	 * Stores array of media files that submitted to the destination site
	 * 	 Could be used later for media update 
	 * 	 
	 *
	 * @var array of KalturaDistributionRemoteMediaFile
	 */
	public $mediaFiles;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDistributionProfileBaseFilter extends KalturaFilter
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
	 * @var KalturaDistributionProfileStatus
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
abstract class KalturaDistributionProviderBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var KalturaDistributionProviderType
	 */
	public $typeEqual = null;

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
class KalturaDistributionValidationErrorInvalidData extends KalturaDistributionValidationError
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
	 * @var KalturaDistributionValidationErrorType
	 */
	public $validationErrorType = null;

	/**
	 * Parameter of the validation error
	 * 	 For example, minimum value for KalturaDistributionValidationErrorType::STRING_TOO_SHORT validation error
	 * 	 
	 *
	 * @var string
	 */
	public $validationErrorParam = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionValidationErrorMissingAsset extends KalturaDistributionValidationError
{
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
class KalturaDistributionValidationErrorMissingFlavor extends KalturaDistributionValidationError
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorParamsId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionValidationErrorMissingMetadata extends KalturaDistributionValidationError
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $fieldName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionValidationErrorMissingThumbnail extends KalturaDistributionValidationError
{
	/**
	 * 
	 *
	 * @var KalturaDistributionThumbDimensions
	 */
	public $dimensions;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaEntryDistributionBaseFilter extends KalturaFilter
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
	public $submittedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $submittedAtLessThanOrEqual = null;

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
	public $distributionProfileIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $distributionProfileIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaEntryDistributionStatus
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
	 * @var KalturaEntryDistributionFlag
	 */
	public $dirtyStatusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dirtyStatusIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sunriseGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sunriseLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sunsetGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sunsetLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionJobProviderData extends KalturaDistributionJobProviderData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $xml = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $resultParseData = null;

	/**
	 * 
	 *
	 * @var KalturaGenericDistributionProviderParser
	 */
	public $resultParserType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * 
	 *
	 * @var int
	 * @insertonly
	 */
	public $genericProviderId = null;

	/**
	 * 
	 *
	 * @var KalturaGenericDistributionProfileAction
	 */
	public $submitAction;

	/**
	 * 
	 *
	 * @var KalturaGenericDistributionProfileAction
	 */
	public $updateAction;

	/**
	 * 
	 *
	 * @var KalturaGenericDistributionProfileAction
	 */
	public $deleteAction;

	/**
	 * 
	 *
	 * @var KalturaGenericDistributionProfileAction
	 */
	public $fetchReportAction;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateRequiredEntryFields = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateRequiredMetadataXPaths = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaGenericDistributionProviderActionBaseFilter extends KalturaFilter
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
	public $genericDistributionProviderIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $genericDistributionProviderIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaDistributionAction
	 */
	public $actionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $actionIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationDistributionProfile extends KalturaDistributionProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $xsl = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $feedId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationDistributionProvider extends KalturaDistributionProvider
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionDeleteJobData extends KalturaDistributionJobData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionFetchReportJobData extends KalturaDistributionJobData
{
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
	public $views = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProfileFilter extends KalturaDistributionProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProviderFilter extends KalturaDistributionProviderBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionSubmitJobData extends KalturaDistributionJobData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionUpdateJobData extends KalturaDistributionJobData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionValidationErrorInvalidMetadata extends KalturaDistributionValidationErrorInvalidData
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryDistributionFilter extends KalturaEntryDistributionBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderActionFilter extends KalturaGenericDistributionProviderActionBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaConfigurableDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionDisableJobData extends KalturaDistributionUpdateJobData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionEnableJobData extends KalturaDistributionUpdateJobData
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaGenericDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaGenericDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
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
	 * @var KalturaNullableBoolean
	 */
	public $isDefaultEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $isDefaultIn = null;

	/**
	 * 
	 *
	 * @var KalturaGenericDistributionProviderStatus
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
abstract class KalturaSyndicationDistributionProfileBaseFilter extends KalturaDistributionProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaSyndicationDistributionProviderBaseFilter extends KalturaDistributionProviderFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConfigurableDistributionProfileFilter extends KalturaConfigurableDistributionProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProfileFilter extends KalturaGenericDistributionProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderFilter extends KalturaGenericDistributionProviderBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationDistributionProfileFilter extends KalturaSyndicationDistributionProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationDistributionProviderFilter extends KalturaSyndicationDistributionProviderBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProfileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new Distribution Profile
	 * 
	 * @param KalturaDistributionProfile $distributionProfile 
	 * @return KalturaDistributionProfile
	 */
	function add(KalturaDistributionProfile $distributionProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "distributionProfile", $distributionProfile->toParams());
		$this->client->queueServiceActionCall("contentdistribution_distributionprofile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDistributionProfile");
		return $resultObject;
	}

	/**
	 * Get Distribution Profile by id
	 * 
	 * @param int $id 
	 * @return KalturaDistributionProfile
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_distributionprofile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDistributionProfile");
		return $resultObject;
	}

	/**
	 * Update Distribution Profile by id
	 * 
	 * @param int $id 
	 * @param KalturaDistributionProfile $distributionProfile 
	 * @return KalturaDistributionProfile
	 */
	function update($id, KalturaDistributionProfile $distributionProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "distributionProfile", $distributionProfile->toParams());
		$this->client->queueServiceActionCall("contentdistribution_distributionprofile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDistributionProfile");
		return $resultObject;
	}

	/**
	 * Update Distribution Profile status by id
	 * 
	 * @param int $id 
	 * @param int $status 
	 * @return KalturaDistributionProfile
	 */
	function updateStatus($id, $status)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "status", $status);
		$this->client->queueServiceActionCall("contentdistribution_distributionprofile", "updateStatus", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDistributionProfile");
		return $resultObject;
	}

	/**
	 * Delete Distribution Profile by id
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_distributionprofile", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List all distribution providers
	 * 
	 * @param KalturaDistributionProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaDistributionProfileListResponse
	 */
	function listAction(KalturaDistributionProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("contentdistribution_distributionprofile", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDistributionProfileListResponse");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param KalturaPartnerFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaDistributionProfileListResponse
	 */
	function listByPartner(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("contentdistribution_distributionprofile", "listByPartner", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDistributionProfileListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEntryDistributionService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new Entry Distribution
	 * 
	 * @param KalturaEntryDistribution $entryDistribution 
	 * @return KalturaEntryDistribution
	 */
	function add(KalturaEntryDistribution $entryDistribution)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryDistribution", $entryDistribution->toParams());
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistribution");
		return $resultObject;
	}

	/**
	 * Get Entry Distribution by id
	 * 
	 * @param int $id 
	 * @return KalturaEntryDistribution
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistribution");
		return $resultObject;
	}

	/**
	 * Validates Entry Distribution by id for submission
	 * 
	 * @param int $id 
	 * @return KalturaEntryDistribution
	 */
	function validate($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "validate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistribution");
		return $resultObject;
	}

	/**
	 * Update Entry Distribution by id
	 * 
	 * @param int $id 
	 * @param KalturaEntryDistribution $entryDistribution 
	 * @return KalturaEntryDistribution
	 */
	function update($id, KalturaEntryDistribution $entryDistribution)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "entryDistribution", $entryDistribution->toParams());
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistribution");
		return $resultObject;
	}

	/**
	 * Delete Entry Distribution by id
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List all distribution providers
	 * 
	 * @param KalturaEntryDistributionFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaEntryDistributionListResponse
	 */
	function listAction(KalturaEntryDistributionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistributionListResponse");
		return $resultObject;
	}

	/**
	 * Submits Entry Distribution to the remote destination
	 * 
	 * @param int $id 
	 * @param bool $submitWhenReady 
	 * @return KalturaEntryDistribution
	 */
	function submitAdd($id, $submitWhenReady = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "submitWhenReady", $submitWhenReady);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "submitAdd", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistribution");
		return $resultObject;
	}

	/**
	 * Submits Entry Distribution changes to the remote destination
	 * 
	 * @param int $id 
	 * @return KalturaEntryDistribution
	 */
	function submitUpdate($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "submitUpdate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistribution");
		return $resultObject;
	}

	/**
	 * Submits Entry Distribution report request
	 * 
	 * @param int $id 
	 * @return KalturaEntryDistribution
	 */
	function submitFetchReport($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "submitFetchReport", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistribution");
		return $resultObject;
	}

	/**
	 * Deletes Entry Distribution from the remote destination
	 * 
	 * @param int $id 
	 * @return KalturaEntryDistribution
	 */
	function submitDelete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "submitDelete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistribution");
		return $resultObject;
	}

	/**
	 * Retries last submit action
	 * 
	 * @param int $id 
	 * @return KalturaEntryDistribution
	 */
	function retrySubmit($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "retrySubmit", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryDistribution");
		return $resultObject;
	}

	/**
	 * Serves entry distribution sent data
	 * 
	 * @param int $id 
	 * @param int $actionType 
	 * @return file
	 */
	function serveSentData($id, $actionType)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "actionType", $actionType);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "serveSentData", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Serves entry distribution returned data
	 * 
	 * @param int $id 
	 * @param int $actionType 
	 * @return file
	 */
	function serveReturnedData($id, $actionType)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "actionType", $actionType);
		$this->client->queueServiceActionCall("contentdistribution_entrydistribution", "serveReturnedData", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDistributionProviderService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * List all distribution providers
	 * 
	 * @param KalturaDistributionProviderFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaDistributionProviderListResponse
	 */
	function listAction(KalturaDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("contentdistribution_distributionprovider", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDistributionProviderListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new Generic Distribution Provider
	 * 
	 * @param KalturaGenericDistributionProvider $genericDistributionProvider 
	 * @return KalturaGenericDistributionProvider
	 */
	function add(KalturaGenericDistributionProvider $genericDistributionProvider)
	{
		$kparams = array();
		$this->client->addParam($kparams, "genericDistributionProvider", $genericDistributionProvider->toParams());
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovider", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProvider");
		return $resultObject;
	}

	/**
	 * Get Generic Distribution Provider by id
	 * 
	 * @param int $id 
	 * @return KalturaGenericDistributionProvider
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovider", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProvider");
		return $resultObject;
	}

	/**
	 * Update Generic Distribution Provider by id
	 * 
	 * @param int $id 
	 * @param KalturaGenericDistributionProvider $genericDistributionProvider 
	 * @return KalturaGenericDistributionProvider
	 */
	function update($id, KalturaGenericDistributionProvider $genericDistributionProvider)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "genericDistributionProvider", $genericDistributionProvider->toParams());
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovider", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProvider");
		return $resultObject;
	}

	/**
	 * Delete Generic Distribution Provider by id
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovider", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List all distribution providers
	 * 
	 * @param KalturaGenericDistributionProviderFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaGenericDistributionProviderListResponse
	 */
	function listAction(KalturaGenericDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovider", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaGenericDistributionProviderActionService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new Generic Distribution Provider Action
	 * 
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction 
	 * @return KalturaGenericDistributionProviderAction
	 */
	function add(KalturaGenericDistributionProviderAction $genericDistributionProviderAction)
	{
		$kparams = array();
		$this->client->addParam($kparams, "genericDistributionProviderAction", $genericDistributionProviderAction->toParams());
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Add MRSS transform file to generic distribution provider action
	 * 
	 * @param int $id The id of the generic distribution provider action
	 * @param string $xslData XSL MRSS transformation data
	 * @return KalturaGenericDistributionProviderAction
	 */
	function addMrssTransform($id, $xslData)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "xslData", $xslData);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "addMrssTransform", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Add MRSS transform file to generic distribution provider action
	 * 
	 * @param int $id The id of the generic distribution provider action
	 * @param file $xslFile XSL MRSS transformation file
	 * @return KalturaGenericDistributionProviderAction
	 */
	function addMrssTransformFromFile($id, $xslFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$kfiles = array();
		$this->client->addParam($kfiles, "xslFile", $xslFile);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "addMrssTransformFromFile", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Add MRSS validate file to generic distribution provider action
	 * 
	 * @param int $id The id of the generic distribution provider action
	 * @param string $xsdData XSD MRSS validatation data
	 * @return KalturaGenericDistributionProviderAction
	 */
	function addMrssValidate($id, $xsdData)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "xsdData", $xsdData);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "addMrssValidate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Add MRSS validate file to generic distribution provider action
	 * 
	 * @param int $id The id of the generic distribution provider action
	 * @param file $xsdFile XSD MRSS validatation file
	 * @return KalturaGenericDistributionProviderAction
	 */
	function addMrssValidateFromFile($id, $xsdFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$kfiles = array();
		$this->client->addParam($kfiles, "xsdFile", $xsdFile);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "addMrssValidateFromFile", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Add results transform file to generic distribution provider action
	 * 
	 * @param int $id The id of the generic distribution provider action
	 * @param string $transformData Transformation data xsl, xPath or regex
	 * @return KalturaGenericDistributionProviderAction
	 */
	function addResultsTransform($id, $transformData)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "transformData", $transformData);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "addResultsTransform", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Add MRSS transform file to generic distribution provider action
	 * 
	 * @param int $id The id of the generic distribution provider action
	 * @param file $transformFile Transformation file xsl, xPath or regex
	 * @return KalturaGenericDistributionProviderAction
	 */
	function addResultsTransformFromFile($id, $transformFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$kfiles = array();
		$this->client->addParam($kfiles, "transformFile", $transformFile);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "addResultsTransformFromFile", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Get Generic Distribution Provider Action by id
	 * 
	 * @param int $id 
	 * @return KalturaGenericDistributionProviderAction
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Get Generic Distribution Provider Action by provider id
	 * 
	 * @param int $genericDistributionProviderId 
	 * @param int $actionType 
	 * @return KalturaGenericDistributionProviderAction
	 */
	function getByProviderId($genericDistributionProviderId, $actionType)
	{
		$kparams = array();
		$this->client->addParam($kparams, "genericDistributionProviderId", $genericDistributionProviderId);
		$this->client->addParam($kparams, "actionType", $actionType);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "getByProviderId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Update Generic Distribution Provider Action by provider id
	 * 
	 * @param int $genericDistributionProviderId 
	 * @param int $actionType 
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction 
	 * @return KalturaGenericDistributionProviderAction
	 */
	function updateByProviderId($genericDistributionProviderId, $actionType, KalturaGenericDistributionProviderAction $genericDistributionProviderAction)
	{
		$kparams = array();
		$this->client->addParam($kparams, "genericDistributionProviderId", $genericDistributionProviderId);
		$this->client->addParam($kparams, "actionType", $actionType);
		$this->client->addParam($kparams, "genericDistributionProviderAction", $genericDistributionProviderAction->toParams());
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "updateByProviderId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Update Generic Distribution Provider Action by id
	 * 
	 * @param int $id 
	 * @param KalturaGenericDistributionProviderAction $genericDistributionProviderAction 
	 * @return KalturaGenericDistributionProviderAction
	 */
	function update($id, KalturaGenericDistributionProviderAction $genericDistributionProviderAction)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "genericDistributionProviderAction", $genericDistributionProviderAction->toParams());
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderAction");
		return $resultObject;
	}

	/**
	 * Delete Generic Distribution Provider Action by id
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Delete Generic Distribution Provider Action by provider id
	 * 
	 * @param int $genericDistributionProviderId 
	 * @param int $actionType 
	 * @return 
	 */
	function deleteByProviderId($genericDistributionProviderId, $actionType)
	{
		$kparams = array();
		$this->client->addParam($kparams, "genericDistributionProviderId", $genericDistributionProviderId);
		$this->client->addParam($kparams, "actionType", $actionType);
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "deleteByProviderId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List all distribution providers
	 * 
	 * @param KalturaGenericDistributionProviderActionFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaGenericDistributionProviderActionListResponse
	 */
	function listAction(KalturaGenericDistributionProviderActionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("contentdistribution_genericdistributionprovideraction", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaGenericDistributionProviderActionListResponse");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaContentDistributionClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaDistributionProfileService
	 */
	public $distributionProfile = null;

	/**
	 * @var KalturaEntryDistributionService
	 */
	public $entryDistribution = null;

	/**
	 * @var KalturaDistributionProviderService
	 */
	public $distributionProvider = null;

	/**
	 * @var KalturaGenericDistributionProviderService
	 */
	public $genericDistributionProvider = null;

	/**
	 * @var KalturaGenericDistributionProviderActionService
	 */
	public $genericDistributionProviderAction = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->distributionProfile = new KalturaDistributionProfileService($client);
		$this->entryDistribution = new KalturaEntryDistributionService($client);
		$this->distributionProvider = new KalturaDistributionProviderService($client);
		$this->genericDistributionProvider = new KalturaGenericDistributionProviderService($client);
		$this->genericDistributionProviderAction = new KalturaGenericDistributionProviderActionService($client);
	}

	/**
	 * @return KalturaContentDistributionClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaContentDistributionClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'distributionProfile' => $this->distributionProfile,
			'entryDistribution' => $this->entryDistribution,
			'distributionProvider' => $this->distributionProvider,
			'genericDistributionProvider' => $this->genericDistributionProvider,
			'genericDistributionProviderAction' => $this->genericDistributionProviderAction,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'contentDistribution';
	}
}

