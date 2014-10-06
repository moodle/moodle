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
require_once(dirname(__FILE__) . "/KalturaDrmClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineRepositorySyncMode
{
	const MODIFY = 0;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineFlavorAssetOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const DELETED_AT_ASC = "+deletedAt";
	const SIZE_ASC = "+size";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const DELETED_AT_DESC = "-deletedAt";
	const SIZE_DESC = "-size";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineFlavorParamsOrderBy
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineFlavorParamsOutputOrderBy
{
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineProfileOrderBy
{
	const ID_ASC = "+id";
	const NAME_ASC = "+name";
	const ID_DESC = "-id";
	const NAME_DESC = "-name";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineProfile extends KalturaDrmProfile
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
	public $iv = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $owner = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $portal = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $maxGop = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $regServerHost = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineRepositorySyncJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var KalturaWidevineRepositorySyncMode
	 */
	public $syncMode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $wvAssetIds = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $modifiedAttributes = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $monitorSyncCompletion = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineFlavorAsset extends KalturaFlavorAsset
{
	/**
	 * License distribution window start date 
	 * 	 
	 *
	 * @var int
	 */
	public $widevineDistributionStartDate = null;

	/**
	 * License distribution window end date
	 * 	 
	 *
	 * @var int
	 */
	public $widevineDistributionEndDate = null;

	/**
	 * Widevine unique asset id
	 * 	 
	 *
	 * @var int
	 */
	public $widevineAssetId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineFlavorParams extends KalturaFlavorParams
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineFlavorParamsOutput extends KalturaFlavorParamsOutput
{
	/**
	 * License distribution window start date 
	 * 	 
	 *
	 * @var int
	 */
	public $widevineDistributionStartDate = null;

	/**
	 * License distribution window end date
	 * 	 
	 *
	 * @var int
	 */
	public $widevineDistributionEndDate = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaWidevineProfileBaseFilter extends KalturaDrmProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineProfileFilter extends KalturaWidevineProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaWidevineFlavorAssetBaseFilter extends KalturaFlavorAssetFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaWidevineFlavorParamsBaseFilter extends KalturaFlavorParamsFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineFlavorAssetFilter extends KalturaWidevineFlavorAssetBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineFlavorParamsFilter extends KalturaWidevineFlavorParamsBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaWidevineFlavorParamsOutputBaseFilter extends KalturaFlavorParamsOutputFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineFlavorParamsOutputFilter extends KalturaWidevineFlavorParamsOutputBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineDrmService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Get license for encrypted content playback
	 * 
	 * @param string $flavorAssetId 
	 * @param string $referrer 64base encoded
	 * @return string
	 */
	function getLicense($flavorAssetId, $referrer = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "flavorAssetId", $flavorAssetId);
		$this->client->addParam($kparams, "referrer", $referrer);
		$this->client->queueServiceActionCall("widevine_widevinedrm", "getLicense", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidevineClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaWidevineDrmService
	 */
	public $widevineDrm = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->widevineDrm = new KalturaWidevineDrmService($client);
	}

	/**
	 * @return KalturaWidevineClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaWidevineClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'widevineDrm' => $this->widevineDrm,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'widevine';
	}
}

