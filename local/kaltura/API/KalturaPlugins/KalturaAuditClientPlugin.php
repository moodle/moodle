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

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailChangeXmlNodeType
{
	const CHANGED = 1;
	const ADDED = 2;
	const REMOVED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailContext
{
	const CLIENT = -1;
	const SCRIPT = 0;
	const PS2 = 1;
	const API_V3 = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailFileSyncType
{
	const FILE = 1;
	const LINK = 2;
	const URL = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailStatus
{
	const PENDING = 1;
	const READY = 2;
	const FAILED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailAction
{
	const CHANGED = "CHANGED";
	const CONTENT_VIEWED = "CONTENT_VIEWED";
	const COPIED = "COPIED";
	const CREATED = "CREATED";
	const DELETED = "DELETED";
	const FILE_SYNC_CREATED = "FILE_SYNC_CREATED";
	const RELATION_ADDED = "RELATION_ADDED";
	const RELATION_REMOVED = "RELATION_REMOVED";
	const VIEWED = "VIEWED";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailObjectType
{
	const BATCH_JOB = "BatchJob";
	const EMAIL_INGESTION_PROFILE = "EmailIngestionProfile";
	const FILE_SYNC = "FileSync";
	const KSHOW_KUSER = "KshowKuser";
	const METADATA = "Metadata";
	const METADATA_PROFILE = "MetadataProfile";
	const PARTNER = "Partner";
	const PERMISSION = "Permission";
	const UPLOAD_TOKEN = "UploadToken";
	const USER_LOGIN_DATA = "UserLoginData";
	const USER_ROLE = "UserRole";
	const ACCESS_CONTROL = "accessControl";
	const CATEGORY = "category";
	const CONVERSION_PROFILE_2 = "conversionProfile2";
	const ENTRY = "entry";
	const FLAVOR_ASSET = "flavorAsset";
	const FLAVOR_PARAMS = "flavorParams";
	const FLAVOR_PARAMS_CONVERSION_PROFILE = "flavorParamsConversionProfile";
	const FLAVOR_PARAMS_OUTPUT = "flavorParamsOutput";
	const KSHOW = "kshow";
	const KUSER = "kuser";
	const MEDIA_INFO = "mediaInfo";
	const MODERATION = "moderation";
	const ROUGHCUT = "roughcutEntry";
	const SYNDICATION = "syndicationFeed";
	const THUMBNAIL_ASSET = "thumbAsset";
	const THUMBNAIL_PARAMS = "thumbParams";
	const THUMBNAIL_PARAMS_OUTPUT = "thumbParamsOutput";
	const UI_CONF = "uiConf";
	const WIDGET = "widget";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const PARSED_AT_ASC = "+parsedAt";
	const CREATED_AT_DESC = "-createdAt";
	const PARSED_AT_DESC = "-parsedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaAuditTrailInfo extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrail extends KalturaObjectBase
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
	 * Indicates when the data was parsed
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $parsedAt = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailObjectType
	 */
	public $auditObjectType = null;

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
	public $relatedObjectId = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailObjectType
	 */
	public $relatedObjectType = null;

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
	 * @readonly
	 */
	public $masterPartnerId = null;

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
	public $requestId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userId = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailAction
	 */
	public $action = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailInfo
	 */
	public $data;

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
	 * @var KalturaAuditTrailContext
	 * @readonly
	 */
	public $context = null;

	/**
	 * The API service and action that called and caused this audit
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $entryPoint = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $serverName = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $ipAddress = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $userAgent = null;

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
	public $description = null;

	/**
	 * 
	 *
	 * @var string
	 * @readonly
	 */
	public $errorDescription = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailChangeItem extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $descriptor = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $oldValue = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $newValue = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaAuditTrail
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
abstract class KalturaAuditTrailBaseFilter extends KalturaFilter
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
	public $parsedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $parsedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailStatus
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
	 * @var KalturaAuditTrailObjectType
	 */
	public $auditObjectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $auditObjectTypeIn = null;

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
	 * @var string
	 */
	public $relatedObjectIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $relatedObjectIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailObjectType
	 */
	public $relatedObjectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $relatedObjectTypeIn = null;

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
	public $masterPartnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $masterPartnerIdIn = null;

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
	public $requestIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $requestIdIn = null;

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
	 * @var KalturaAuditTrailAction
	 */
	public $actionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $actionIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ksEqual = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailContext
	 */
	public $contextEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contextIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryPointEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryPointIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ipAddressEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ipAddressIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $clientTagEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailChangeInfo extends KalturaAuditTrailInfo
{
	/**
	 * 
	 *
	 * @var array of KalturaAuditTrailChangeItem
	 */
	public $changedItems;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailChangeXmlNode extends KalturaAuditTrailChangeItem
{
	/**
	 * 
	 *
	 * @var KalturaAuditTrailChangeXmlNodeType
	 */
	public $type = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailFileSyncCreateInfo extends KalturaAuditTrailInfo
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
	 * @var int
	 */
	public $objectSubType = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dc = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $original = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailFileSyncType
	 */
	public $fileType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailTextInfo extends KalturaAuditTrailInfo
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $info = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailFilter extends KalturaAuditTrailBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditTrailService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Allows you to add an audit trail object and audit trail content associated with Kaltura object
	 * 
	 * @param KalturaAuditTrail $auditTrail 
	 * @return KalturaAuditTrail
	 */
	function add(KalturaAuditTrail $auditTrail)
	{
		$kparams = array();
		$this->client->addParam($kparams, "auditTrail", $auditTrail->toParams());
		$this->client->queueServiceActionCall("audit_audittrail", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAuditTrail");
		return $resultObject;
	}

	/**
	 * Retrieve an audit trail object by id
	 * 
	 * @param int $id 
	 * @return KalturaAuditTrail
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("audit_audittrail", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAuditTrail");
		return $resultObject;
	}

	/**
	 * List audit trail objects by filter and pager
	 * 
	 * @param KalturaAuditTrailFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaAuditTrailListResponse
	 */
	function listAction(KalturaAuditTrailFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("audit_audittrail", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAuditTrailListResponse");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAuditClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaAuditTrailService
	 */
	public $auditTrail = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->auditTrail = new KalturaAuditTrailService($client);
	}

	/**
	 * @return KalturaAuditClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaAuditClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'auditTrail' => $this->auditTrail,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'audit';
	}
}

