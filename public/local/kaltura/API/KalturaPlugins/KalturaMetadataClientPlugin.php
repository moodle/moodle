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
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataProfileCreateMode extends KalturaEnumBase
{
	const API = 1;
	const KMC = 2;
	const APP = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataProfileStatus extends KalturaEnumBase
{
	const ACTIVE = 1;
	const DEPRECATED = 2;
	const TRANSFORMING = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataStatus extends KalturaEnumBase
{
	const VALID = 1;
	const INVALID = 2;
	const DELETED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataObjectType extends KalturaEnumBase
{
	const AD_CUE_POINT = "adCuePointMetadata.AdCuePoint";
	const ANNOTATION = "annotationMetadata.Annotation";
	const CODE_CUE_POINT = "codeCuePointMetadata.CodeCuePoint";
	const ANSWER_CUE_POINT = "quiz.AnswerCuePoint";
	const QUESTION_CUE_POINT = "quiz.QuestionCuePoint";
	const THUMB_CUE_POINT = "thumbCuePointMetadata.thumbCuePoint";
	const ENTRY = "1";
	const CATEGORY = "2";
	const USER = "3";
	const PARTNER = "4";
	const DYNAMIC_OBJECT = "5";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataOrderBy extends KalturaEnumBase
{
	const CREATED_AT_ASC = "+createdAt";
	const METADATA_PROFILE_VERSION_ASC = "+metadataProfileVersion";
	const UPDATED_AT_ASC = "+updatedAt";
	const VERSION_ASC = "+version";
	const CREATED_AT_DESC = "-createdAt";
	const METADATA_PROFILE_VERSION_DESC = "-metadataProfileVersion";
	const UPDATED_AT_DESC = "-updatedAt";
	const VERSION_DESC = "-version";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataProfileOrderBy extends KalturaEnumBase
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
class KalturaMetadata extends KalturaObjectBase
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
	public $metadataProfileId = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $metadataProfileVersion = null;

	/**
	 * 
	 *
	 * @var KalturaMetadataObjectType
	 * @readonly
	 */
	public $metadataObjectType = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $objectId = null;

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
	 * @var KalturaMetadataStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $xml = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataProfile extends KalturaObjectBase
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
	 * @var KalturaMetadataObjectType
	 */
	public $metadataObjectType = null;

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
	 * @var KalturaMetadataProfileStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $xsd = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $views = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $xslt = null;

	/**
	 * 
	 *
	 * @var KalturaMetadataProfileCreateMode
	 */
	public $createMode = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $disableReIndexing = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataProfileField extends KalturaObjectBase
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
	 * @readonly
	 */
	public $xPath = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $key = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $label = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaImportMetadataJobData extends KalturaJobData
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
	 * @var int
	 */
	public $metadataId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaMetadata
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMetadataProfileBaseFilter extends KalturaFilter
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
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaMetadataObjectType
	 */
	public $metadataObjectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $metadataObjectTypeIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $versionEqual = null;

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
	 * @var KalturaMetadataProfileStatus
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
	 * @var KalturaMetadataProfileCreateMode
	 */
	public $createModeEqual = null;

	/**
	 * 
	 *
	 * @var KalturaMetadataProfileCreateMode
	 */
	public $createModeNotEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $createModeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $createModeNotIn = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataProfileFieldListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaMetadataProfileField
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataProfileListResponse extends KalturaListResponse
{
	/**
	 * 
	 *
	 * @var array of KalturaMetadataProfile
	 * @readonly
	 */
	public $objects;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataReplacementOptionsItem extends KalturaPluginReplacementOptionsItem
{
	/**
	 * If true custom-metadata transferred to temp entry on entry replacement
	 *
	 * @var bool
	 */
	public $shouldCopyMetadata = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataResponseProfileMapping extends KalturaResponseProfileMapping
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaTransformMetadataJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var KalturaFileContainer
	 */
	public $srcXsl;

	/**
	 * 
	 *
	 * @var int
	 */
	public $srcVersion = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $destVersion = null;

	/**
	 * 
	 *
	 * @var KalturaFileContainer
	 */
	public $destXsd;

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
class KalturaCompareMetadataCondition extends KalturaCompareCondition
{
	/**
	 * May contain the full xpath to the field in three formats
	 * 	 1. Slashed xPath, e.g. /metadata/myElementName
	 * 	 2. Using local-name function, e.g. /[local-name()='metadata']/[local-name()='myElementName']
	 * 	 3. Using only the field name, e.g. myElementName, it will be searched as //myElementName
	 *
	 * @var string
	 */
	public $xPath = null;

	/**
	 * Metadata profile id
	 *
	 * @var int
	 */
	public $profileId = null;

	/**
	 * Metadata profile system name
	 *
	 * @var string
	 */
	public $profileSystemName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDynamicObjectSearchItem extends KalturaSearchOperator
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $field = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMatchMetadataCondition extends KalturaMatchCondition
{
	/**
	 * May contain the full xpath to the field in three formats
	 * 	 1. Slashed xPath, e.g. /metadata/myElementName
	 * 	 2. Using local-name function, e.g. /[local-name()='metadata']/[local-name()='myElementName']
	 * 	 3. Using only the field name, e.g. myElementName, it will be searched as //myElementName
	 *
	 * @var string
	 */
	public $xPath = null;

	/**
	 * Metadata profile id
	 *
	 * @var int
	 */
	public $profileId = null;

	/**
	 * Metadata profile system name
	 *
	 * @var string
	 */
	public $profileSystemName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaMetadataBaseFilter extends KalturaRelatedFilter
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
	 * @var int
	 */
	public $metadataProfileIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $metadataProfileIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileVersionEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileVersionGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileVersionLessThanOrEqual = null;

	/**
	 * When null, default is KalturaMetadataObjectType::ENTRY
	 *
	 * @var KalturaMetadataObjectType
	 */
	public $metadataObjectTypeEqual = null;

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
	public $versionEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $versionGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $versionLessThanOrEqual = null;

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
	 * @var KalturaMetadataStatus
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
class KalturaMetadataFieldChangedCondition extends KalturaMatchCondition
{
	/**
	 * May contain the full xpath to the field in three formats
	 * 	 1. Slashed xPath, e.g. /metadata/myElementName
	 * 	 2. Using local-name function, e.g. /[local-name()='metadata']/[local-name()='myElementName']
	 * 	 3. Using only the field name, e.g. myElementName, it will be searched as //myElementName
	 *
	 * @var string
	 */
	public $xPath = null;

	/**
	 * Metadata profile id
	 *
	 * @var int
	 */
	public $profileId = null;

	/**
	 * Metadata profile system name
	 *
	 * @var string
	 */
	public $profileSystemName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $versionA = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $versionB = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataProfileFilter extends KalturaMetadataProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataSearchItem extends KalturaSearchOperator
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $orderBy = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataField extends KalturaStringField
{
	/**
	 * May contain the full xpath to the field in three formats
	 * 	 1. Slashed xPath, e.g. /metadata/myElementName
	 * 	 2. Using local-name function, e.g. /[local-name()='metadata']/[local-name()='myElementName']
	 * 	 3. Using only the field name, e.g. myElementName, it will be searched as //myElementName
	 *
	 * @var string
	 */
	public $xPath = null;

	/**
	 * Metadata profile id
	 *
	 * @var int
	 */
	public $profileId = null;

	/**
	 * Metadata profile system name
	 *
	 * @var string
	 */
	public $profileSystemName = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataFilter extends KalturaMetadataBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Allows you to add a metadata object and metadata content associated with Kaltura object
	 * 
	 * @param int $metadataProfileId 
	 * @param string $objectType 
	 * @param string $objectId 
	 * @param string $xmlData XML metadata
	 * @return KalturaMetadata
	 */
	function add($metadataProfileId, $objectType, $objectId, $xmlData)
	{
		$kparams = array();
		$this->client->addParam($kparams, "metadataProfileId", $metadataProfileId);
		$this->client->addParam($kparams, "objectType", $objectType);
		$this->client->addParam($kparams, "objectId", $objectId);
		$this->client->addParam($kparams, "xmlData", $xmlData);
		$this->client->queueServiceActionCall("metadata_metadata", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadata");
		return $resultObject;
	}

	/**
	 * Allows you to add a metadata xml data from remote URL.
	 Enables different permissions than addFromUrl action.
	 * 
	 * @param int $metadataProfileId 
	 * @param string $objectType 
	 * @param string $objectId 
	 * @param string $url XML metadata remote url
	 * @return KalturaMetadata
	 */
	function addFromBulk($metadataProfileId, $objectType, $objectId, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "metadataProfileId", $metadataProfileId);
		$this->client->addParam($kparams, "objectType", $objectType);
		$this->client->addParam($kparams, "objectId", $objectId);
		$this->client->addParam($kparams, "url", $url);
		$this->client->queueServiceActionCall("metadata_metadata", "addFromBulk", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadata");
		return $resultObject;
	}

	/**
	 * Allows you to add a metadata object and metadata file associated with Kaltura object
	 * 
	 * @param int $metadataProfileId 
	 * @param string $objectType 
	 * @param string $objectId 
	 * @param file $xmlFile XML metadata
	 * @return KalturaMetadata
	 */
	function addFromFile($metadataProfileId, $objectType, $objectId, $xmlFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "metadataProfileId", $metadataProfileId);
		$this->client->addParam($kparams, "objectType", $objectType);
		$this->client->addParam($kparams, "objectId", $objectId);
		$kfiles = array();
		$this->client->addParam($kfiles, "xmlFile", $xmlFile);
		$this->client->queueServiceActionCall("metadata_metadata", "addFromFile", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadata");
		return $resultObject;
	}

	/**
	 * Allows you to add a metadata xml data from remote URL
	 * 
	 * @param int $metadataProfileId 
	 * @param string $objectType 
	 * @param string $objectId 
	 * @param string $url XML metadata remote url
	 * @return KalturaMetadata
	 */
	function addFromUrl($metadataProfileId, $objectType, $objectId, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "metadataProfileId", $metadataProfileId);
		$this->client->addParam($kparams, "objectType", $objectType);
		$this->client->addParam($kparams, "objectId", $objectId);
		$this->client->addParam($kparams, "url", $url);
		$this->client->queueServiceActionCall("metadata_metadata", "addFromUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadata");
		return $resultObject;
	}

	/**
	 * Delete an existing metadata
	 * 
	 * @param int $id 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("metadata_metadata", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
	}

	/**
	 * Retrieve a metadata object by id
	 * 
	 * @param int $id 
	 * @return KalturaMetadata
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("metadata_metadata", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadata");
		return $resultObject;
	}

	/**
	 * Index metadata by id, will also index the related object
	 * 
	 * @param string $id 
	 * @param bool $shouldUpdate 
	 * @return int
	 */
	function index($id, $shouldUpdate)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "shouldUpdate", $shouldUpdate);
		$this->client->queueServiceActionCall("metadata_metadata", "index", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Mark existing metadata as invalid
	 Used by batch metadata transform
	 * 
	 * @param int $id 
	 * @param int $version Enable update only if the metadata object version did not change by other process
	 */
	function invalidate($id, $version = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("metadata_metadata", "invalidate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
	}

	/**
	 * List metadata objects by filter and pager
	 * 
	 * @param KalturaMetadataFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaMetadataListResponse
	 */
	function listAction(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("metadata_metadata", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataListResponse");
		return $resultObject;
	}

	/**
	 * Serves metadata XML file
	 * 
	 * @param int $id 
	 * @return file
	 */
	function serve($id)
	{
		if ($this->client->isMultiRequest())
			throw new KalturaClientException("Action is not supported as part of multi-request.", KalturaClientException::ERROR_ACTION_IN_MULTIREQUEST);
		
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("metadata_metadata", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Update an existing metadata object with new XML content
	 * 
	 * @param int $id 
	 * @param string $xmlData XML metadata
	 * @param int $version Enable update only if the metadata object version did not change by other process
	 * @return KalturaMetadata
	 */
	function update($id, $xmlData = null, $version = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "xmlData", $xmlData);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("metadata_metadata", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadata");
		return $resultObject;
	}

	/**
	 * Update an existing metadata object with new XML file
	 * 
	 * @param int $id 
	 * @param file $xmlFile XML metadata
	 * @return KalturaMetadata
	 */
	function updateFromFile($id, $xmlFile = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$kfiles = array();
		$this->client->addParam($kfiles, "xmlFile", $xmlFile);
		$this->client->queueServiceActionCall("metadata_metadata", "updateFromFile", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadata");
		return $resultObject;
	}

	/**
	 * Action transforms current metadata object XML using a provided XSL.
	 * 
	 * @param int $id 
	 * @param file $xslFile 
	 * @return KalturaMetadata
	 */
	function updateFromXSL($id, $xslFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$kfiles = array();
		$this->client->addParam($kfiles, "xslFile", $xslFile);
		$this->client->queueServiceActionCall("metadata_metadata", "updateFromXSL", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadata");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataProfileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Allows you to add a metadata profile object and metadata profile content associated with Kaltura object type
	 * 
	 * @param KalturaMetadataProfile $metadataProfile 
	 * @param string $xsdData XSD metadata definition
	 * @param string $viewsData UI views definition
	 * @return KalturaMetadataProfile
	 */
	function add(KalturaMetadataProfile $metadataProfile, $xsdData, $viewsData = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "metadataProfile", $metadataProfile->toParams());
		$this->client->addParam($kparams, "xsdData", $xsdData);
		$this->client->addParam($kparams, "viewsData", $viewsData);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfile");
		return $resultObject;
	}

	/**
	 * Allows you to add a metadata profile object and metadata profile file associated with Kaltura object type
	 * 
	 * @param KalturaMetadataProfile $metadataProfile 
	 * @param file $xsdFile XSD metadata definition
	 * @param file $viewsFile UI views definition
	 * @return KalturaMetadataProfile
	 */
	function addFromFile(KalturaMetadataProfile $metadataProfile, $xsdFile, $viewsFile = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "metadataProfile", $metadataProfile->toParams());
		$kfiles = array();
		$this->client->addParam($kfiles, "xsdFile", $xsdFile);
		$this->client->addParam($kfiles, "viewsFile", $viewsFile);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "addFromFile", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfile");
		return $resultObject;
	}

	/**
	 * Delete an existing metadata profile
	 * 
	 * @param int $id 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
	}

	/**
	 * Retrieve a metadata profile object by id
	 * 
	 * @param int $id 
	 * @return KalturaMetadataProfile
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfile");
		return $resultObject;
	}

	/**
	 * List metadata profile objects by filter and pager
	 * 
	 * @param KalturaMetadataProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaMetadataProfileListResponse
	 */
	function listAction(KalturaMetadataProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("metadata_metadataprofile", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfileListResponse");
		return $resultObject;
	}

	/**
	 * List metadata profile fields by metadata profile id
	 * 
	 * @param int $metadataProfileId 
	 * @return KalturaMetadataProfileFieldListResponse
	 */
	function listFields($metadataProfileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "metadataProfileId", $metadataProfileId);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "listFields", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfileFieldListResponse");
		return $resultObject;
	}

	/**
	 * Update an existing metadata object definition file
	 * 
	 * @param int $id 
	 * @param int $toVersion 
	 * @return KalturaMetadataProfile
	 */
	function revert($id, $toVersion)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "toVersion", $toVersion);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "revert", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfile");
		return $resultObject;
	}

	/**
	 * Serves metadata profile XSD file
	 * 
	 * @param int $id 
	 * @return file
	 */
	function serve($id)
	{
		if ($this->client->isMultiRequest())
			throw new KalturaClientException("Action is not supported as part of multi-request.", KalturaClientException::ERROR_ACTION_IN_MULTIREQUEST);
		
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Serves metadata profile view file
	 * 
	 * @param int $id 
	 * @return file
	 */
	function serveView($id)
	{
		if ($this->client->isMultiRequest())
			throw new KalturaClientException("Action is not supported as part of multi-request.", KalturaClientException::ERROR_ACTION_IN_MULTIREQUEST);
		
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "serveView", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Update an existing metadata object
	 * 
	 * @param int $id 
	 * @param KalturaMetadataProfile $metadataProfile 
	 * @param string $xsdData XSD metadata definition
	 * @param string $viewsData UI views definition
	 * @return KalturaMetadataProfile
	 */
	function update($id, KalturaMetadataProfile $metadataProfile, $xsdData = null, $viewsData = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "metadataProfile", $metadataProfile->toParams());
		$this->client->addParam($kparams, "xsdData", $xsdData);
		$this->client->addParam($kparams, "viewsData", $viewsData);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfile");
		return $resultObject;
	}

	/**
	 * Update an existing metadata object definition file
	 * 
	 * @param int $id 
	 * @param file $xsdFile XSD metadata definition
	 * @return KalturaMetadataProfile
	 */
	function updateDefinitionFromFile($id, $xsdFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$kfiles = array();
		$this->client->addParam($kfiles, "xsdFile", $xsdFile);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "updateDefinitionFromFile", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfile");
		return $resultObject;
	}

	/**
	 * Update an existing metadata object xslt file
	 * 
	 * @param int $id 
	 * @param file $xsltFile XSLT file, will be executed on every metadata add/update
	 * @return KalturaMetadataProfile
	 */
	function updateTransformationFromFile($id, $xsltFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$kfiles = array();
		$this->client->addParam($kfiles, "xsltFile", $xsltFile);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "updateTransformationFromFile", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfile");
		return $resultObject;
	}

	/**
	 * Update an existing metadata object views file
	 * 
	 * @param int $id 
	 * @param file $viewsFile UI views file
	 * @return KalturaMetadataProfile
	 */
	function updateViewsFromFile($id, $viewsFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$kfiles = array();
		$this->client->addParam($kfiles, "viewsFile", $viewsFile);
		$this->client->queueServiceActionCall("metadata_metadataprofile", "updateViewsFromFile", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMetadataProfile");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMetadataClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaMetadataService
	 */
	public $metadata = null;

	/**
	 * @var KalturaMetadataProfileService
	 */
	public $metadataProfile = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->metadata = new KalturaMetadataService($client);
		$this->metadataProfile = new KalturaMetadataProfileService($client);
	}

	/**
	 * @return KalturaMetadataClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaMetadataClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'metadata' => $this->metadata,
			'metadataProfile' => $this->metadataProfile,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'metadata';
	}
}

