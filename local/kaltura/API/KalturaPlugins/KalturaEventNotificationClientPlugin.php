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
class KalturaEventNotificationTemplateStatus
{
	const DISABLED = 1;
	const ACTIVE = 2;
	const DELETED = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationEventObjectType
{
	const AD_CUE_POINT = "adCuePointEventNotifications.AdCuePoint";
	const ANNOTATION = "annotationEventNotifications.Annotation";
	const CAPTION_ASSET = "captionAssetEventNotifications.CaptionAsset";
	const CODE_CUE_POINT = "codeCuePointEventNotifications.CodeCuePoint";
	const DISTRIBUTION_PROFILE = "contentDistributionEventNotifications.DistributionProfile";
	const ENTRY_DISTRIBUTION = "contentDistributionEventNotifications.EntryDistribution";
	const CUE_POINT = "cuePointEventNotifications.CuePoint";
	const METADATA = "metadataEventNotifications.Metadata";
	const ENTRY = "1";
	const CATEGORY = "2";
	const ASSET = "3";
	const FLAVORASSET = "4";
	const THUMBASSET = "5";
	const KUSER = "8";
	const ACCESSCONTROL = "9";
	const BATCHJOB = "10";
	const BULKUPLOADRESULT = "11";
	const CATEGORYKUSER = "12";
	const CONVERSIONPROFILE2 = "14";
	const FLAVORPARAMS = "15";
	const FLAVORPARAMSCONVERSIONPROFILE = "16";
	const FLAVORPARAMSOUTPUT = "17";
	const GENERICSYNDICATIONFEED = "18";
	const KUSERTOUSERROLE = "19";
	const PARTNER = "20";
	const PERMISSION = "21";
	const PERMISSIONITEM = "22";
	const PERMISSIONTOPERMISSIONITEM = "23";
	const SCHEDULER = "24";
	const SCHEDULERCONFIG = "25";
	const SCHEDULERSTATUS = "26";
	const SCHEDULERWORKER = "27";
	const STORAGEPROFILE = "28";
	const SYNDICATIONFEED = "29";
	const THUMBPARAMS = "31";
	const THUMBPARAMSOUTPUT = "32";
	const UPLOADTOKEN = "33";
	const USERLOGINDATA = "34";
	const USERROLE = "35";
	const WIDGET = "36";
	const CATEGORYENTRY = "37";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationEventType
{
	const BATCH_JOB_STATUS = "1";
	const OBJECT_ADDED = "2";
	const OBJECT_CHANGED = "3";
	const OBJECT_COPIED = "4";
	const OBJECT_CREATED = "5";
	const OBJECT_DATA_CHANGED = "6";
	const OBJECT_DELETED = "7";
	const OBJECT_ERASED = "8";
	const OBJECT_READY_FOR_REPLACMENT = "9";
	const OBJECT_SAVED = "10";
	const OBJECT_UPDATED = "11";
	const OBJECT_REPLACED = "12";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationTemplateOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationTemplateType
{
	const EMAIL = "emailNotification.Email";
	const HTTP = "httpNotification.Http";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationParameter extends KalturaObjectBase
{
	/**
	 * The key in the subject and body to be replaced with the dynamic value
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
	public $description = null;

	/**
	 * The dynamic value to be placed in the final output
	 * 	 
	 *
	 * @var KalturaStringValue
	 */
	public $value;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationTemplate extends KalturaObjectBase
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
	 * @var KalturaEventNotificationTemplateType
	 * @insertonly
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var KalturaEventNotificationTemplateStatus
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
	 * Define that the template could be dispatched manually from the API
	 * 	 
	 *
	 * @var bool
	 */
	public $manualDispatchEnabled = null;

	/**
	 * Define that the template could be dispatched automatically by the system
	 * 	 
	 *
	 * @var bool
	 */
	public $automaticDispatchEnabled = null;

	/**
	 * Define the event that should trigger this notification
	 * 	 
	 *
	 * @var KalturaEventNotificationEventType
	 */
	public $eventType = null;

	/**
	 * Define the object that raied the event that should trigger this notification
	 * 	 
	 *
	 * @var KalturaEventNotificationEventObjectType
	 */
	public $eventObjectType = null;

	/**
	 * Define the conditions that cause this notification to be triggered
	 * 	 
	 *
	 * @var array of KalturaCondition
	 */
	public $eventConditions;

	/**
	 * Define the content dynamic parameters
	 * 	 
	 *
	 * @var array of KalturaEventNotificationParameter
	 */
	public $contentParameters;

	/**
	 * Define the content dynamic parameters
	 * 	 
	 *
	 * @var array of KalturaEventNotificationParameter
	 */
	public $userParameters;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationTemplateListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaEventNotificationTemplate
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
class KalturaEventFieldCondition extends KalturaCondition
{
	/**
	 * The field to be evaluated at runtime
	 * 	 
	 *
	 * @var KalturaBooleanField
	 */
	public $field;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationArrayParameter extends KalturaEventNotificationParameter
{
	/**
	 * 
	 *
	 * @var array of KalturaString
	 */
	public $values;

	/**
	 * Used to restrict the values to close list
	 * 	 
	 *
	 * @var array of KalturaStringValue
	 */
	public $allowedValues;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationDispatchJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $templateId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaEventNotificationTemplateBaseFilter extends KalturaFilter
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
	 * @var KalturaEventNotificationTemplateType
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
	 * @var KalturaEventNotificationTemplateStatus
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
class KalturaEventObjectChangedCondition extends KalturaCondition
{
	/**
	 * Comma seperated column names to be tested
	 * 	 
	 *
	 * @var string
	 */
	public $modifiedColumns = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationTemplateFilter extends KalturaEventNotificationTemplateBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationTemplateService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Allows you to add a new event notification template object
	 * 
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate 
	 * @return KalturaEventNotificationTemplate
	 */
	function add(KalturaEventNotificationTemplate $eventNotificationTemplate)
	{
		$kparams = array();
		$this->client->addParam($kparams, "eventNotificationTemplate", $eventNotificationTemplate->toParams());
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEventNotificationTemplate");
		return $resultObject;
	}

	/**
	 * Allows you to clone exiting event notification template object and create a new one with similar configuration
	 * 
	 * @param int $id Source template to clone
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate Overwrite configuration object
	 * @return KalturaEventNotificationTemplate
	 */
	function cloneAction($id, KalturaEventNotificationTemplate $eventNotificationTemplate = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		if ($eventNotificationTemplate !== null)
			$this->client->addParam($kparams, "eventNotificationTemplate", $eventNotificationTemplate->toParams());
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "clone", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEventNotificationTemplate");
		return $resultObject;
	}

	/**
	 * Retrieve an event notification template object by id
	 * 
	 * @param int $id 
	 * @return KalturaEventNotificationTemplate
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEventNotificationTemplate");
		return $resultObject;
	}

	/**
	 * Update an existing event notification template object
	 * 
	 * @param int $id 
	 * @param KalturaEventNotificationTemplate $eventNotificationTemplate 
	 * @return KalturaEventNotificationTemplate
	 */
	function update($id, KalturaEventNotificationTemplate $eventNotificationTemplate)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "eventNotificationTemplate", $eventNotificationTemplate->toParams());
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEventNotificationTemplate");
		return $resultObject;
	}

	/**
	 * Update event notification template status by id
	 * 
	 * @param int $id 
	 * @param int $status 
	 * @return KalturaEventNotificationTemplate
	 */
	function updateStatus($id, $status)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "status", $status);
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "updateStatus", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEventNotificationTemplate");
		return $resultObject;
	}

	/**
	 * Delete an event notification template object
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List event notification template objects
	 * 
	 * @param KalturaEventNotificationTemplateFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaEventNotificationTemplateListResponse
	 */
	function listAction(KalturaEventNotificationTemplateFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEventNotificationTemplateListResponse");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param KalturaPartnerFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaEventNotificationTemplateListResponse
	 */
	function listByPartner(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "listByPartner", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEventNotificationTemplateListResponse");
		return $resultObject;
	}

	/**
	 * Dispatch event notification object by id
	 * 
	 * @param int $id 
	 * @param KalturaEventNotificationDispatchJobData $data 
	 * @return int
	 */
	function dispatch($id, KalturaEventNotificationDispatchJobData $data)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "data", $data->toParams());
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "dispatch", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Action lists the template partner event notification templates.
	 * 
	 * @param KalturaEventNotificationTemplateFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaEventNotificationTemplateListResponse
	 */
	function listTemplates(KalturaEventNotificationTemplateFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("eventnotification_eventnotificationtemplate", "listTemplates", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEventNotificationTemplateListResponse");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEventNotificationClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaEventNotificationTemplateService
	 */
	public $eventNotificationTemplate = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->eventNotificationTemplate = new KalturaEventNotificationTemplateService($client);
	}

	/**
	 * @return KalturaEventNotificationClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaEventNotificationClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'eventNotificationTemplate' => $this->eventNotificationTemplate,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'eventNotification';
	}
}

