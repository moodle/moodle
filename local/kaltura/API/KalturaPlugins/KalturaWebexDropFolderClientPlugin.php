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
require_once(dirname(__FILE__) . "/KalturaDropFolderClientPlugin.php");
require_once(dirname(__FILE__) . "/KalturaMetadataClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWebexDropFolderFileOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const FILE_NAME_ASC = "+fileName";
	const FILE_SIZE_ASC = "+fileSize";
	const FILE_SIZE_LAST_SET_AT_ASC = "+fileSizeLastSetAt";
	const ID_ASC = "+id";
	const PARSED_FLAVOR_ASC = "+parsedFlavor";
	const PARSED_SLUG_ASC = "+parsedSlug";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const FILE_NAME_DESC = "-fileName";
	const FILE_SIZE_DESC = "-fileSize";
	const FILE_SIZE_LAST_SET_AT_DESC = "-fileSizeLastSetAt";
	const ID_DESC = "-id";
	const PARSED_FLAVOR_DESC = "-parsedFlavor";
	const PARSED_SLUG_DESC = "-parsedSlug";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWebexDropFolderOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const ID_ASC = "+id";
	const NAME_ASC = "+name";
	const UPDATED_AT_ASC = "+updatedAt";
	const CREATED_AT_DESC = "-createdAt";
	const ID_DESC = "-id";
	const NAME_DESC = "-name";
	const UPDATED_AT_DESC = "-updatedAt";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWebexDropFolder extends KalturaDropFolder
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $webexUserId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $webexPassword = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $webexSiteId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $webexPartnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $webexServiceUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $webexHostIdMetadataFieldName = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $categoriesMetadataFieldName = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $enforceEntitlement = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWebexDropFolderFile extends KalturaDropFolderFile
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $recordingId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $webexHostId = null;

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
	public $confId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contentUrl = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWebexDropFolderContentProcessorJobData extends KalturaDropFolderContentProcessorJobData
{
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
	public $webexHostId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dropFolderId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaWebexDropFolderBaseFilter extends KalturaDropFolderFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaWebexDropFolderFileBaseFilter extends KalturaDropFolderFileFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWebexDropFolderFileFilter extends KalturaWebexDropFolderFileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWebexDropFolderFilter extends KalturaWebexDropFolderBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWebexDropFolderClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaWebexDropFolderClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaWebexDropFolderClientPlugin($client);
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
		return 'WebexDropFolder';
	}
}

