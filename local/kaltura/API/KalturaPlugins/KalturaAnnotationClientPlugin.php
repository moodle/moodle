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
require_once(dirname(__FILE__) . "/KalturaCuePointClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnnotationOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const DURATION_ASC = "+duration";
	const END_TIME_ASC = "+endTime";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const START_TIME_ASC = "+startTime";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const DURATION_DESC = "-duration";
	const END_TIME_DESC = "-endTime";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const START_TIME_DESC = "-startTime";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnnotation extends KalturaCuePoint
{
	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $parentId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $text = null;

	/**
	 * End time in milliseconds
	 * 	 
	 *
	 * @var int
	 */
	public $endTime = null;

	/**
	 * Duration in milliseconds
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $duration = null;

	/**
	 * Depth in the tree
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $depth = null;

	/**
	 * Number of all descendants
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $childrenCount = null;

	/**
	 * Number of children, first generation only.
	 * 	 
	 *
	 * @var int
	 * @readonly
	 */
	public $directChildrenCount = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnnotationListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaAnnotation
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
abstract class KalturaAnnotationBaseFilter extends KalturaCuePointFilter
{
	/**
	 * 
	 *
	 * @var string
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
	 * @var string
	 */
	public $textLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $textMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $textMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $endTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $durationLessThanOrEqual = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnnotationFilter extends KalturaAnnotationBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnnotationService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Allows you to add an annotation object associated with an entry
	 * 
	 * @param KalturaCuePoint $annotation 
	 * @return KalturaAnnotation
	 */
	function add(KalturaCuePoint $annotation)
	{
		$kparams = array();
		$this->client->addParam($kparams, "annotation", $annotation->toParams());
		$this->client->queueServiceActionCall("annotation_annotation", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAnnotation");
		return $resultObject;
	}

	/**
	 * Update annotation by id
	 * 
	 * @param string $id 
	 * @param KalturaCuePoint $annotation 
	 * @return KalturaAnnotation
	 */
	function update($id, KalturaCuePoint $annotation)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "annotation", $annotation->toParams());
		$this->client->queueServiceActionCall("annotation_annotation", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAnnotation");
		return $resultObject;
	}

	/**
	 * List annotation objects by filter and pager
	 * 
	 * @param KalturaCuePointFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaAnnotationListResponse
	 */
	function listAction(KalturaCuePointFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("annotation_annotation", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAnnotationListResponse");
		return $resultObject;
	}

	/**
	 * Allows you to add multiple cue points objects by uploading XML that contains multiple cue point definitions
	 * 
	 * @param file $fileData 
	 * @return KalturaCuePointListResponse
	 */
	function addFromBulk($fileData)
	{
		$kparams = array();
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("annotation_annotation", "addFromBulk", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCuePointListResponse");
		return $resultObject;
	}

	/**
	 * Download multiple cue points objects as XML definitions
	 * 
	 * @param KalturaCuePointFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return file
	 */
	function serveBulk(KalturaCuePointFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("annotation_annotation", "serveBulk", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Retrieve an CuePoint object by id
	 * 
	 * @param string $id 
	 * @return KalturaCuePoint
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("annotation_annotation", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCuePoint");
		return $resultObject;
	}

	/**
	 * Count cue point objects by filter
	 * 
	 * @param KalturaCuePointFilter $filter 
	 * @return int
	 */
	function count(KalturaCuePointFilter $filter = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("annotation_annotation", "count", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Delete cue point by id, and delete all children cue points
	 * 
	 * @param string $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("annotation_annotation", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAnnotationClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaAnnotationService
	 */
	public $annotation = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->annotation = new KalturaAnnotationService($client);
	}

	/**
	 * @return KalturaAnnotationClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaAnnotationClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'annotation' => $this->annotation,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'annotation';
	}
}

