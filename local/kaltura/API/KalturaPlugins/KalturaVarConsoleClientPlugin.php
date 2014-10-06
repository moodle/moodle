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
class KalturaVarPartnerUsageItem extends KalturaObjectBase
{
	/**
	 * Partner ID
	 * 	 
	 *
	 * @var int
	 */
	public $partnerId = null;

	/**
	 * Partner name
	 * 	 
	 *
	 * @var string
	 */
	public $partnerName = null;

	/**
	 * Partner status
	 * 	 
	 *
	 * @var KalturaPartnerStatus
	 */
	public $partnerStatus = null;

	/**
	 * Partner package
	 * 	 
	 *
	 * @var int
	 */
	public $partnerPackage = null;

	/**
	 * Partner creation date (Unix timestamp)
	 * 	 
	 *
	 * @var int
	 */
	public $partnerCreatedAt = null;

	/**
	 * Number of player loads in the specific date range
	 * 	 
	 *
	 * @var int
	 */
	public $views = null;

	/**
	 * Number of plays in the specific date range
	 * 	 
	 *
	 * @var int
	 */
	public $plays = null;

	/**
	 * Number of new entries created during specific date range
	 * 	 
	 *
	 * @var int
	 */
	public $entriesCount = null;

	/**
	 * Total number of entries
	 * 	 
	 *
	 * @var int
	 */
	public $totalEntriesCount = null;

	/**
	 * Number of new video entries created during specific date range
	 * 	 
	 *
	 * @var int
	 */
	public $videoEntriesCount = null;

	/**
	 * Number of new image entries created during specific date range
	 * 	 
	 *
	 * @var int
	 */
	public $imageEntriesCount = null;

	/**
	 * Number of new audio entries created during specific date range
	 * 	 
	 *
	 * @var int
	 */
	public $audioEntriesCount = null;

	/**
	 * Number of new mix entries created during specific date range
	 * 	 
	 *
	 * @var int
	 */
	public $mixEntriesCount = null;

	/**
	 * The total bandwidth usage during the given date range (in MB)
	 * 	 
	 *
	 * @var float
	 */
	public $bandwidth = null;

	/**
	 * The total storage consumption (in MB)
	 * 	 
	 *
	 * @var float
	 */
	public $totalStorage = null;

	/**
	 * The added storage consumption (new uploads) during the given date range (in MB)
	 * 	 
	 *
	 * @var float
	 */
	public $storage = null;

	/**
	 * The deleted storage consumption (new uploads) during the given date range (in MB)
	 * 	 
	 *
	 * @var float
	 */
	public $deletedStorage = null;

	/**
	 * The peak amount of storage consumption during the given date range for the specific publisher
	 * 	 
	 *
	 * @var float
	 */
	public $peakStorage = null;

	/**
	 * The average amount of storage consumption during the given date range for the specific publisher
	 * 	 
	 *
	 * @var float
	 */
	public $avgStorage = null;

	/**
	 * The combined amount of bandwidth and storage consumed during the given date range for the specific publisher
	 * 	 
	 *
	 * @var float
	 */
	public $combinedStorageBandwidth = null;

	/**
	 * Amount of transcoding usage in MB
	 * 	 
	 *
	 * @var float
	 */
	public $transcodingUsage = null;

	/**
	 * TGhe date at which the report was taken - Unix Timestamp
	 * 	 
	 *
	 * @var string
	 */
	public $dateId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerUsageListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaVarPartnerUsageItem
	 */
	public $total;

	/**
	 * 
	 *
	 * @var array of KalturaVarPartnerUsageItem
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 */
	public $totalCount = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaVarPartnerUsageTotalItem extends KalturaVarPartnerUsageItem
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaVarConsolePartnerFilter extends KalturaPartnerFilter
{
	/**
	 * Eq filter for the partner's group type
	 *      
	 *
	 * @var KalturaPartnerGroupType
	 */
	public $groupTypeEq = null;

	/**
	 * In filter for the partner's group type
	 *      
	 *
	 * @var string
	 */
	public $groupTypeIn = null;

	/**
	 * Filter for partner permissions- filter contains comma-separated string of permission names which the returned partners should have.
	 *      
	 *
	 * @var string
	 */
	public $partnerPermissionsExist = null;


}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaVarConsoleService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Function which calulates partner usage of a group of a VAR's sub-publishers
	 * 
	 * @param KalturaPartnerFilter $partnerFilter 
	 * @param KalturaReportInputFilter $usageFilter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaPartnerUsageListResponse
	 */
	function getPartnerUsage(KalturaPartnerFilter $partnerFilter = null, KalturaReportInputFilter $usageFilter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($partnerFilter !== null)
			$this->client->addParam($kparams, "partnerFilter", $partnerFilter->toParams());
		if ($usageFilter !== null)
			$this->client->addParam($kparams, "usageFilter", $usageFilter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("varconsole_varconsole", "getPartnerUsage", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartnerUsageListResponse");
		return $resultObject;
	}

	/**
	 * Function to change a sub-publisher's status
	 * 
	 * @param int $id 
	 * @param int $status 
	 * @return 
	 */
	function updateStatus($id, $status)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "status", $status);
		$this->client->queueServiceActionCall("varconsole_varconsole", "updateStatus", $kparams);
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
class KalturaVarConsoleClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaVarConsoleService
	 */
	public $varConsole = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->varConsole = new KalturaVarConsoleService($client);
	}

	/**
	 * @return KalturaVarConsoleClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaVarConsoleClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'varConsole' => $this->varConsole,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'varConsole';
	}
}

