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
class KalturaKontikiStorageProfileOrderBy
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
class KalturaKontikiStorageProfile extends KalturaStorageProfile
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $serviceToken = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiStorageDeleteJobData extends KalturaStorageDeleteJobData
{
	/**
	 * Unique Kontiki MOID for the content uploaded to Kontiki
	 *      
	 *
	 * @var string
	 */
	public $contentMoid = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serviceToken = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiStorageExportJobData extends KalturaStorageExportJobData
{
	/**
	 * Holds the id of the exported asset
	 * 	 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * Unique Kontiki MOID for the content uploaded to Kontiki
	 * 	 
	 *
	 * @var string
	 */
	public $contentMoid = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serviceToken = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaKontikiStorageProfileBaseFilter extends KalturaStorageProfileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiStorageProfileFilter extends KalturaKontikiStorageProfileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaKontikiClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaKontikiClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaKontikiClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'kontiki';
	}
}

