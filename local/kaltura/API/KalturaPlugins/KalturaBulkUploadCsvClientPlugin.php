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
require_once(dirname(__FILE__) . "/KalturaBulkUploadClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadCsvVersion
{
	const V1 = 1;
	const V2 = 2;
	const V3 = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadCsvJobData extends KalturaBulkUploadJobData
{
	/**
	 * The version of the csv file
	 * 	 
	 *
	 * @var KalturaBulkUploadCsvVersion
	 * @readonly
	 */
	public $csvVersion = null;

	/**
	 * Array containing CSV headers
	 * 	 
	 *
	 * @var array of KalturaString
	 */
	public $columns;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadCsvClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaBulkUploadCsvClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaBulkUploadCsvClientPlugin($client);
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
		return 'bulkUploadCsv';
	}
}

