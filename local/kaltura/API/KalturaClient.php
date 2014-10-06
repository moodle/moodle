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
require_once(dirname(__FILE__) . "/KalturaClientBase.php");
require_once(dirname(__FILE__) . "/KalturaEnums.php");
require_once(dirname(__FILE__) . "/KalturaTypes.php");


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlProfileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new access control profile
	 * 
	 * @param KalturaAccessControlProfile $accessControlProfile 
	 * @return KalturaAccessControlProfile
	 */
	function add(KalturaAccessControlProfile $accessControlProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "accessControlProfile", $accessControlProfile->toParams());
		$this->client->queueServiceActionCall("accesscontrolprofile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAccessControlProfile");
		return $resultObject;
	}

	/**
	 * Get access control profile by id
	 * 
	 * @param int $id 
	 * @return KalturaAccessControlProfile
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("accesscontrolprofile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAccessControlProfile");
		return $resultObject;
	}

	/**
	 * Update access control profile by id
	 * 
	 * @param int $id 
	 * @param KalturaAccessControlProfile $accessControlProfile 
	 * @return KalturaAccessControlProfile
	 */
	function update($id, KalturaAccessControlProfile $accessControlProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "accessControlProfile", $accessControlProfile->toParams());
		$this->client->queueServiceActionCall("accesscontrolprofile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAccessControlProfile");
		return $resultObject;
	}

	/**
	 * Delete access control profile by id
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("accesscontrolprofile", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List access control profiles by filter and pager
	 * 
	 * @param KalturaAccessControlProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaAccessControlProfileListResponse
	 */
	function listAction(KalturaAccessControlProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("accesscontrolprofile", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAccessControlProfileListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAccessControlService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new Access Control Profile
	 * 
	 * @param KalturaAccessControl $accessControl 
	 * @return KalturaAccessControl
	 */
	function add(KalturaAccessControl $accessControl)
	{
		$kparams = array();
		$this->client->addParam($kparams, "accessControl", $accessControl->toParams());
		$this->client->queueServiceActionCall("accesscontrol", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAccessControl");
		return $resultObject;
	}

	/**
	 * Get Access Control Profile by id
	 * 
	 * @param int $id 
	 * @return KalturaAccessControl
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("accesscontrol", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAccessControl");
		return $resultObject;
	}

	/**
	 * Update Access Control Profile by id
	 * 
	 * @param int $id 
	 * @param KalturaAccessControl $accessControl 
	 * @return KalturaAccessControl
	 */
	function update($id, KalturaAccessControl $accessControl)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "accessControl", $accessControl->toParams());
		$this->client->queueServiceActionCall("accesscontrol", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAccessControl");
		return $resultObject;
	}

	/**
	 * Delete Access Control Profile by id
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("accesscontrol", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List Access Control Profiles by filter and pager
	 * 
	 * @param KalturaAccessControlFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaAccessControlListResponse
	 */
	function listAction(KalturaAccessControlFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("accesscontrol", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAccessControlListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaAdminUserService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Update admin user password and email
	 * 
	 * @param string $email 
	 * @param string $password 
	 * @param string $newEmail Optional, provide only when you want to update the email
	 * @param string $newPassword 
	 * @return KalturaAdminUser
	 */
	function updatePassword($email, $password, $newEmail = "", $newPassword = "")
	{
		$kparams = array();
		$this->client->addParam($kparams, "email", $email);
		$this->client->addParam($kparams, "password", $password);
		$this->client->addParam($kparams, "newEmail", $newEmail);
		$this->client->addParam($kparams, "newPassword", $newPassword);
		$this->client->queueServiceActionCall("adminuser", "updatePassword", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaAdminUser");
		return $resultObject;
	}

	/**
	 * Reset admin user password and send it to the users email address
	 * 
	 * @param string $email 
	 * @return 
	 */
	function resetPassword($email)
	{
		$kparams = array();
		$this->client->addParam($kparams, "email", $email);
		$this->client->queueServiceActionCall("adminuser", "resetPassword", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Get an admin session using admin email and password (Used for login to the KMC application)
	 * 
	 * @param string $email 
	 * @param string $password 
	 * @param int $partnerId 
	 * @return string
	 */
	function login($email, $password, $partnerId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "email", $email);
		$this->client->addParam($kparams, "password", $password);
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->queueServiceActionCall("adminuser", "login", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Set initial users password
	 * 
	 * @param string $hashKey 
	 * @param string $newPassword New password to set
	 * @return 
	 */
	function setInitialPassword($hashKey, $newPassword)
	{
		$kparams = array();
		$this->client->addParam($kparams, "hashKey", $hashKey);
		$this->client->addParam($kparams, "newPassword", $newPassword);
		$this->client->queueServiceActionCall("adminuser", "setInitialPassword", $kparams);
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
class KalturaBaseEntryService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Generic add entry, should be used when the uploaded entry type is not known.
	 * 
	 * @param KalturaBaseEntry $entry 
	 * @param string $type 
	 * @return KalturaBaseEntry
	 */
	function add(KalturaBaseEntry $entry, $type = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entry", $entry->toParams());
		$this->client->addParam($kparams, "type", $type);
		$this->client->queueServiceActionCall("baseentry", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Attach content resource to entry in status NO_MEDIA
	 * 
	 * @param string $entryId 
	 * @param KalturaResource $resource 
	 * @return KalturaBaseEntry
	 */
	function addContent($entryId, KalturaResource $resource)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "resource", $resource->toParams());
		$this->client->queueServiceActionCall("baseentry", "addContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Generic add entry using an uploaded file, should be used when the uploaded entry type is not known.
	 * 
	 * @param KalturaBaseEntry $entry 
	 * @param string $uploadTokenId 
	 * @param string $type 
	 * @return KalturaBaseEntry
	 */
	function addFromUploadedFile(KalturaBaseEntry $entry, $uploadTokenId, $type = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entry", $entry->toParams());
		$this->client->addParam($kparams, "uploadTokenId", $uploadTokenId);
		$this->client->addParam($kparams, "type", $type);
		$this->client->queueServiceActionCall("baseentry", "addFromUploadedFile", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Get base entry by ID.
	 * 
	 * @param string $entryId Entry id
	 * @param int $version Desired version of the data
	 * @return KalturaBaseEntry
	 */
	function get($entryId, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("baseentry", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Get remote storage existing paths for the asset.
	 * 
	 * @param string $entryId 
	 * @return KalturaRemotePathListResponse
	 */
	function getRemotePaths($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("baseentry", "getRemotePaths", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaRemotePathListResponse");
		return $resultObject;
	}

	/**
	 * Update base entry. Only the properties that were set will be updated.
	 * 
	 * @param string $entryId Entry id to update
	 * @param KalturaBaseEntry $baseEntry Base entry metadata to update
	 * @return KalturaBaseEntry
	 */
	function update($entryId, KalturaBaseEntry $baseEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "baseEntry", $baseEntry->toParams());
		$this->client->queueServiceActionCall("baseentry", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Update the content resource associated with the entry.
	 * 
	 * @param string $entryId Entry id to update
	 * @param KalturaResource $resource Resource to be used to replace entry content
	 * @param int $conversionProfileId The conversion profile id to be used on the entry
	 * @return KalturaBaseEntry
	 */
	function updateContent($entryId, KalturaResource $resource, $conversionProfileId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "resource", $resource->toParams());
		$this->client->addParam($kparams, "conversionProfileId", $conversionProfileId);
		$this->client->queueServiceActionCall("baseentry", "updateContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Get an array of KalturaBaseEntry objects by a comma-separated list of ids.
	 * 
	 * @param string $entryIds Comma separated string of entry ids
	 * @return array
	 */
	function getByIds($entryIds)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryIds", $entryIds);
		$this->client->queueServiceActionCall("baseentry", "getByIds", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Delete an entry.
	 * 
	 * @param string $entryId Entry id to delete
	 * @return 
	 */
	function delete($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("baseentry", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List base entries by filter with paging support.
	 * 
	 * @param KalturaBaseEntryFilter $filter Entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaBaseEntryListResponse
	 */
	function listAction(KalturaBaseEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("baseentry", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntryListResponse");
		return $resultObject;
	}

	/**
	 * List base entries by filter according to reference id
	 * 
	 * @param string $refId Entry Reference ID
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaBaseEntryListResponse
	 */
	function listByReferenceId($refId, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "refId", $refId);
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("baseentry", "listByReferenceId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntryListResponse");
		return $resultObject;
	}

	/**
	 * Count base entries by filter.
	 * 
	 * @param KalturaBaseEntryFilter $filter Entry filter
	 * @return int
	 */
	function count(KalturaBaseEntryFilter $filter = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("baseentry", "count", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Upload a file to Kaltura, that can be used to create an entry.
	 * 
	 * @param file $fileData The file data
	 * @return string
	 */
	function upload($fileData)
	{
		$kparams = array();
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("baseentry", "upload", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Update entry thumbnail using a raw jpeg file.
	 * 
	 * @param string $entryId Media entry id
	 * @param file $fileData Jpeg file data
	 * @return KalturaBaseEntry
	 */
	function updateThumbnailJpeg($entryId, $fileData)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("baseentry", "updateThumbnailJpeg", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Update entry thumbnail using url.
	 * 
	 * @param string $entryId Media entry id
	 * @param string $url File url
	 * @return KalturaBaseEntry
	 */
	function updateThumbnailFromUrl($entryId, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "url", $url);
		$this->client->queueServiceActionCall("baseentry", "updateThumbnailFromUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Update entry thumbnail from a different entry by a specified time offset (in seconds).
	 * 
	 * @param string $entryId Media entry id
	 * @param string $sourceEntryId Media entry id
	 * @param int $timeOffset Time offset (in seconds)
	 * @return KalturaBaseEntry
	 */
	function updateThumbnailFromSourceEntry($entryId, $sourceEntryId, $timeOffset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "sourceEntryId", $sourceEntryId);
		$this->client->addParam($kparams, "timeOffset", $timeOffset);
		$this->client->queueServiceActionCall("baseentry", "updateThumbnailFromSourceEntry", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Flag inappropriate entry for moderation.
	 * 
	 * @param KalturaModerationFlag $moderationFlag 
	 * @return 
	 */
	function flag(KalturaModerationFlag $moderationFlag)
	{
		$kparams = array();
		$this->client->addParam($kparams, "moderationFlag", $moderationFlag->toParams());
		$this->client->queueServiceActionCall("baseentry", "flag", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Reject the entry and mark the pending flags (if any) as moderated (this will make the entry non-playable).
	 * 
	 * @param string $entryId 
	 * @return 
	 */
	function reject($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("baseentry", "reject", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Approve the entry and mark the pending flags (if any) as moderated (this will make the entry playable).
	 * 
	 * @param string $entryId 
	 * @return 
	 */
	function approve($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("baseentry", "approve", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List all pending flags for the entry.
	 * 
	 * @param string $entryId 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaModerationFlagListResponse
	 */
	function listFlags($entryId, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("baseentry", "listFlags", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaModerationFlagListResponse");
		return $resultObject;
	}

	/**
	 * Anonymously rank an entry, no validation is done on duplicate rankings.
	 * 
	 * @param string $entryId 
	 * @param int $rank 
	 * @return 
	 */
	function anonymousRank($entryId, $rank)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "rank", $rank);
		$this->client->queueServiceActionCall("baseentry", "anonymousRank", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * This action delivers entry-related data, based on the user's context: access control, restriction, playback format and storage information.
	 * 
	 * @param string $entryId 
	 * @param KalturaEntryContextDataParams $contextDataParams 
	 * @return KalturaEntryContextDataResult
	 */
	function getContextData($entryId, KalturaEntryContextDataParams $contextDataParams)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "contextDataParams", $contextDataParams->toParams());
		$this->client->queueServiceActionCall("baseentry", "getContextData", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEntryContextDataResult");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $entryId 
	 * @param int $storageProfileId 
	 * @return KalturaBaseEntry
	 */
	function export($entryId, $storageProfileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "storageProfileId", $storageProfileId);
		$this->client->queueServiceActionCall("baseentry", "export", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Index an entry by id.
	 * 
	 * @param string $id 
	 * @param bool $shouldUpdate 
	 * @return int
	 */
	function index($id, $shouldUpdate = true)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "shouldUpdate", $shouldUpdate);
		$this->client->queueServiceActionCall("baseentry", "index", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Clone an entry with optional attributes to apply to the clone
	 * 
	 * @param string $entryId Id of entry to clone
	 * @return KalturaBaseEntry
	 */
	function cloneAction($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("baseentry", "clone", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaBulkUploadService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new bulk upload batch job
	 Conversion profile id can be specified in the API or in the CSV file, the one in the CSV file will be stronger.
	 If no conversion profile was specified, partner's default will be used
	 * 
	 * @param int $conversionProfileId Convertion profile id to use for converting the current bulk (-1 to use partner's default)
	 * @param file $csvFileData Bulk upload file
	 * @param string $bulkUploadType 
	 * @param string $uploadedBy 
	 * @param string $fileName Friendly name of the file, used to be recognized later in the logs.
	 * @return KalturaBulkUpload
	 */
	function add($conversionProfileId, $csvFileData, $bulkUploadType = null, $uploadedBy = null, $fileName = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "conversionProfileId", $conversionProfileId);
		$kfiles = array();
		$this->client->addParam($kfiles, "csvFileData", $csvFileData);
		$this->client->addParam($kparams, "bulkUploadType", $bulkUploadType);
		$this->client->addParam($kparams, "uploadedBy", $uploadedBy);
		$this->client->addParam($kparams, "fileName", $fileName);
		$this->client->queueServiceActionCall("bulkupload", "add", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUpload");
		return $resultObject;
	}

	/**
	 * Get bulk upload batch job by id
	 * 
	 * @param bigint $id 
	 * @return KalturaBulkUpload
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("bulkupload", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUpload");
		return $resultObject;
	}

	/**
	 * List bulk upload batch jobs
	 * 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaBulkUploadListResponse
	 */
	function listAction(KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("bulkupload", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUploadListResponse");
		return $resultObject;
	}

	/**
	 * Serve action returan the original file.
	 * 
	 * @param bigint $id Job id
	 * @return file
	 */
	function serve($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("bulkupload", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * ServeLog action returan the original file.
	 * 
	 * @param bigint $id Job id
	 * @return file
	 */
	function serveLog($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("bulkupload", "serveLog", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Aborts the bulk upload and all its child jobs
	 * 
	 * @param bigint $id Job id
	 * @return KalturaBulkUpload
	 */
	function abort($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("bulkupload", "abort", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUpload");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryEntryService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new CategoryEntry
	 * 
	 * @param KalturaCategoryEntry $categoryEntry 
	 * @return KalturaCategoryEntry
	 */
	function add(KalturaCategoryEntry $categoryEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryEntry", $categoryEntry->toParams());
		$this->client->queueServiceActionCall("categoryentry", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryEntry");
		return $resultObject;
	}

	/**
	 * Delete CategoryEntry
	 * 
	 * @param string $entryId 
	 * @param int $categoryId 
	 * @return 
	 */
	function delete($entryId, $categoryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->queueServiceActionCall("categoryentry", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List all categoryEntry
	 * 
	 * @param KalturaCategoryEntryFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaCategoryEntryListResponse
	 */
	function listAction(KalturaCategoryEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("categoryentry", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryEntryListResponse");
		return $resultObject;
	}

	/**
	 * Index CategoryEntry by Id
	 * 
	 * @param string $entryId 
	 * @param int $categoryId 
	 * @param bool $shouldUpdate 
	 * @return int
	 */
	function index($entryId, $categoryId, $shouldUpdate = true)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->addParam($kparams, "shouldUpdate", $shouldUpdate);
		$this->client->queueServiceActionCall("categoryentry", "index", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Activate CategoryEntry when it is pending moderation
	 * 
	 * @param string $entryId 
	 * @param int $categoryId 
	 * @return 
	 */
	function activate($entryId, $categoryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->queueServiceActionCall("categoryentry", "activate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Activate CategoryEntry when it is pending moderation
	 * 
	 * @param string $entryId 
	 * @param int $categoryId 
	 * @return 
	 */
	function reject($entryId, $categoryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->queueServiceActionCall("categoryentry", "reject", $kparams);
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
class KalturaCategoryService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new Category
	 * 
	 * @param KalturaCategory $category 
	 * @return KalturaCategory
	 */
	function add(KalturaCategory $category)
	{
		$kparams = array();
		$this->client->addParam($kparams, "category", $category->toParams());
		$this->client->queueServiceActionCall("category", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategory");
		return $resultObject;
	}

	/**
	 * Get Category by id
	 * 
	 * @param int $id 
	 * @return KalturaCategory
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("category", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategory");
		return $resultObject;
	}

	/**
	 * Update Category
	 * 
	 * @param int $id 
	 * @param KalturaCategory $category 
	 * @return KalturaCategory
	 */
	function update($id, KalturaCategory $category)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "category", $category->toParams());
		$this->client->queueServiceActionCall("category", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategory");
		return $resultObject;
	}

	/**
	 * Delete a Category
	 * 
	 * @param int $id 
	 * @param int $moveEntriesToParentCategory 
	 * @return 
	 */
	function delete($id, $moveEntriesToParentCategory = 1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "moveEntriesToParentCategory", $moveEntriesToParentCategory);
		$this->client->queueServiceActionCall("category", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List all categories
	 * 
	 * @param KalturaCategoryFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaCategoryListResponse
	 */
	function listAction(KalturaCategoryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("category", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryListResponse");
		return $resultObject;
	}

	/**
	 * Index Category by id
	 * 
	 * @param int $id 
	 * @param bool $shouldUpdate 
	 * @return int
	 */
	function index($id, $shouldUpdate = true)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "shouldUpdate", $shouldUpdate);
		$this->client->queueServiceActionCall("category", "index", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Move categories that belong to the same parent category to a target categroy - enabled only for ks with disable entitlement
	 * 
	 * @param string $categoryIds 
	 * @param int $targetCategoryParentId 
	 * @return KalturaCategoryListResponse
	 */
	function move($categoryIds, $targetCategoryParentId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryIds", $categoryIds);
		$this->client->addParam($kparams, "targetCategoryParentId", $targetCategoryParentId);
		$this->client->queueServiceActionCall("category", "move", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryListResponse");
		return $resultObject;
	}

	/**
	 * Unlock categories
	 * 
	 * @return 
	 */
	function unlockCategories()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("category", "unlockCategories", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param file $fileData 
	 * @param KalturaBulkUploadJobData $bulkUploadData 
	 * @param KalturaBulkUploadCategoryData $bulkUploadCategoryData 
	 * @return KalturaBulkUpload
	 */
	function addFromBulkUpload($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadCategoryData $bulkUploadCategoryData = null)
	{
		$kparams = array();
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		if ($bulkUploadData !== null)
			$this->client->addParam($kparams, "bulkUploadData", $bulkUploadData->toParams());
		if ($bulkUploadCategoryData !== null)
			$this->client->addParam($kparams, "bulkUploadCategoryData", $bulkUploadCategoryData->toParams());
		$this->client->queueServiceActionCall("category", "addFromBulkUpload", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUpload");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryUserService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new CategoryUser
	 * 
	 * @param KalturaCategoryUser $categoryUser 
	 * @return KalturaCategoryUser
	 */
	function add(KalturaCategoryUser $categoryUser)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryUser", $categoryUser->toParams());
		$this->client->queueServiceActionCall("categoryuser", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryUser");
		return $resultObject;
	}

	/**
	 * Get CategoryUser by id
	 * 
	 * @param int $categoryId 
	 * @param string $userId 
	 * @return KalturaCategoryUser
	 */
	function get($categoryId, $userId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->queueServiceActionCall("categoryuser", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryUser");
		return $resultObject;
	}

	/**
	 * Update CategoryUser by id
	 * 
	 * @param int $categoryId 
	 * @param string $userId 
	 * @param KalturaCategoryUser $categoryUser 
	 * @param bool $override - to override manual changes
	 * @return KalturaCategoryUser
	 */
	function update($categoryId, $userId, KalturaCategoryUser $categoryUser, $override = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->addParam($kparams, "categoryUser", $categoryUser->toParams());
		$this->client->addParam($kparams, "override", $override);
		$this->client->queueServiceActionCall("categoryuser", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryUser");
		return $resultObject;
	}

	/**
	 * Delete a CategoryUser
	 * 
	 * @param int $categoryId 
	 * @param string $userId 
	 * @return 
	 */
	function delete($categoryId, $userId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->queueServiceActionCall("categoryuser", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Activate CategoryUser
	 * 
	 * @param int $categoryId 
	 * @param string $userId 
	 * @return KalturaCategoryUser
	 */
	function activate($categoryId, $userId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->queueServiceActionCall("categoryuser", "activate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryUser");
		return $resultObject;
	}

	/**
	 * Reject CategoryUser
	 * 
	 * @param int $categoryId 
	 * @param string $userId 
	 * @return KalturaCategoryUser
	 */
	function deactivate($categoryId, $userId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->queueServiceActionCall("categoryuser", "deactivate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryUser");
		return $resultObject;
	}

	/**
	 * List all categories
	 * 
	 * @param KalturaCategoryUserFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaCategoryUserListResponse
	 */
	function listAction(KalturaCategoryUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("categoryuser", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCategoryUserListResponse");
		return $resultObject;
	}

	/**
	 * Copy all memeber from parent category
	 * 
	 * @param int $categoryId 
	 * @return 
	 */
	function copyFromCategory($categoryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->queueServiceActionCall("categoryuser", "copyFromCategory", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Index CategoryUser by userid and category id
	 * 
	 * @param string $userId 
	 * @param int $categoryId 
	 * @param bool $shouldUpdate 
	 * @return int
	 */
	function index($userId, $categoryId, $shouldUpdate = true)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->addParam($kparams, "categoryId", $categoryId);
		$this->client->addParam($kparams, "shouldUpdate", $shouldUpdate);
		$this->client->queueServiceActionCall("categoryuser", "index", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param file $fileData 
	 * @param KalturaBulkUploadJobData $bulkUploadData 
	 * @param KalturaBulkUploadCategoryUserData $bulkUploadCategoryUserData 
	 * @return KalturaBulkUpload
	 */
	function addFromBulkUpload($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadCategoryUserData $bulkUploadCategoryUserData = null)
	{
		$kparams = array();
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		if ($bulkUploadData !== null)
			$this->client->addParam($kparams, "bulkUploadData", $bulkUploadData->toParams());
		if ($bulkUploadCategoryUserData !== null)
			$this->client->addParam($kparams, "bulkUploadCategoryUserData", $bulkUploadCategoryUserData->toParams());
		$this->client->queueServiceActionCall("categoryuser", "addFromBulkUpload", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUpload");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileAssetParamsService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Lists asset parmas of conversion profile by ID
	 * 
	 * @param KalturaConversionProfileAssetParamsFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaConversionProfileAssetParamsListResponse
	 */
	function listAction(KalturaConversionProfileAssetParamsFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("conversionprofileassetparams", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaConversionProfileAssetParamsListResponse");
		return $resultObject;
	}

	/**
	 * Update asset parmas of conversion profile by ID
	 * 
	 * @param int $conversionProfileId 
	 * @param int $assetParamsId 
	 * @param KalturaConversionProfileAssetParams $conversionProfileAssetParams 
	 * @return KalturaConversionProfileAssetParams
	 */
	function update($conversionProfileId, $assetParamsId, KalturaConversionProfileAssetParams $conversionProfileAssetParams)
	{
		$kparams = array();
		$this->client->addParam($kparams, "conversionProfileId", $conversionProfileId);
		$this->client->addParam($kparams, "assetParamsId", $assetParamsId);
		$this->client->addParam($kparams, "conversionProfileAssetParams", $conversionProfileAssetParams->toParams());
		$this->client->queueServiceActionCall("conversionprofileassetparams", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaConversionProfileAssetParams");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConversionProfileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Set Conversion Profile to be the partner default
	 * 
	 * @param int $id 
	 * @return KalturaConversionProfile
	 */
	function setAsDefault($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("conversionprofile", "setAsDefault", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaConversionProfile");
		return $resultObject;
	}

	/**
	 * Get the partner's default conversion profile
	 * 
	 * @param string $type 
	 * @return KalturaConversionProfile
	 */
	function getDefault($type = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "type", $type);
		$this->client->queueServiceActionCall("conversionprofile", "getDefault", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaConversionProfile");
		return $resultObject;
	}

	/**
	 * Add new Conversion Profile
	 * 
	 * @param KalturaConversionProfile $conversionProfile 
	 * @return KalturaConversionProfile
	 */
	function add(KalturaConversionProfile $conversionProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "conversionProfile", $conversionProfile->toParams());
		$this->client->queueServiceActionCall("conversionprofile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaConversionProfile");
		return $resultObject;
	}

	/**
	 * Get Conversion Profile by ID
	 * 
	 * @param int $id 
	 * @return KalturaConversionProfile
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("conversionprofile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaConversionProfile");
		return $resultObject;
	}

	/**
	 * Update Conversion Profile by ID
	 * 
	 * @param int $id 
	 * @param KalturaConversionProfile $conversionProfile 
	 * @return KalturaConversionProfile
	 */
	function update($id, KalturaConversionProfile $conversionProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "conversionProfile", $conversionProfile->toParams());
		$this->client->queueServiceActionCall("conversionprofile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaConversionProfile");
		return $resultObject;
	}

	/**
	 * Delete Conversion Profile by ID
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("conversionprofile", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List Conversion Profiles by filter with paging support
	 * 
	 * @param KalturaConversionProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaConversionProfileListResponse
	 */
	function listAction(KalturaConversionProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("conversionprofile", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaConversionProfileListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDataService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds a new data entry
	 * 
	 * @param KalturaDataEntry $dataEntry Data entry
	 * @return KalturaDataEntry
	 */
	function add(KalturaDataEntry $dataEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dataEntry", $dataEntry->toParams());
		$this->client->queueServiceActionCall("data", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDataEntry");
		return $resultObject;
	}

	/**
	 * Get data entry by ID.
	 * 
	 * @param string $entryId Data entry id
	 * @param int $version Desired version of the data
	 * @return KalturaDataEntry
	 */
	function get($entryId, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("data", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDataEntry");
		return $resultObject;
	}

	/**
	 * Update data entry. Only the properties that were set will be updated.
	 * 
	 * @param string $entryId Data entry id to update
	 * @param KalturaDataEntry $documentEntry Data entry metadata to update
	 * @return KalturaDataEntry
	 */
	function update($entryId, KalturaDataEntry $documentEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "documentEntry", $documentEntry->toParams());
		$this->client->queueServiceActionCall("data", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDataEntry");
		return $resultObject;
	}

	/**
	 * Delete a data entry.
	 * 
	 * @param string $entryId Data entry id to delete
	 * @return 
	 */
	function delete($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("data", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List data entries by filter with paging support.
	 * 
	 * @param KalturaDataEntryFilter $filter Document entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaDataListResponse
	 */
	function listAction(KalturaDataEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("data", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDataListResponse");
		return $resultObject;
	}

	/**
	 * Serve action returan the file from dataContent field.
	 * 
	 * @param string $entryId Data entry id
	 * @param int $version Desired version of the data
	 * @param bool $forceProxy Force to get the content without redirect
	 * @return file
	 */
	function serve($entryId, $version = -1, $forceProxy = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->addParam($kparams, "forceProxy", $forceProxy);
		$this->client->queueServiceActionCall("data", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDocumentService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new document entry after the specific document file was uploaded and the upload token id exists
	 * 
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata
	 * @param string $uploadTokenId Upload token id
	 * @return KalturaDocumentEntry
	 */
	function addFromUploadedFile(KalturaDocumentEntry $documentEntry, $uploadTokenId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "documentEntry", $documentEntry->toParams());
		$this->client->addParam($kparams, "uploadTokenId", $uploadTokenId);
		$this->client->queueServiceActionCall("document", "addFromUploadedFile", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDocumentEntry");
		return $resultObject;
	}

	/**
	 * Copy entry into new entry
	 * 
	 * @param string $sourceEntryId Document entry id to copy from
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata
	 * @param int $sourceFlavorParamsId The flavor to be used as the new entry source, source flavor will be used if not specified
	 * @return KalturaDocumentEntry
	 */
	function addFromEntry($sourceEntryId, KalturaDocumentEntry $documentEntry = null, $sourceFlavorParamsId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "sourceEntryId", $sourceEntryId);
		if ($documentEntry !== null)
			$this->client->addParam($kparams, "documentEntry", $documentEntry->toParams());
		$this->client->addParam($kparams, "sourceFlavorParamsId", $sourceFlavorParamsId);
		$this->client->queueServiceActionCall("document", "addFromEntry", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDocumentEntry");
		return $resultObject;
	}

	/**
	 * Copy flavor asset into new entry
	 * 
	 * @param string $sourceFlavorAssetId Flavor asset id to be used as the new entry source
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata
	 * @return KalturaDocumentEntry
	 */
	function addFromFlavorAsset($sourceFlavorAssetId, KalturaDocumentEntry $documentEntry = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "sourceFlavorAssetId", $sourceFlavorAssetId);
		if ($documentEntry !== null)
			$this->client->addParam($kparams, "documentEntry", $documentEntry->toParams());
		$this->client->queueServiceActionCall("document", "addFromFlavorAsset", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDocumentEntry");
		return $resultObject;
	}

	/**
	 * Convert entry
	 * 
	 * @param string $entryId Document entry id
	 * @param int $conversionProfileId 
	 * @param array $dynamicConversionAttributes 
	 * @return int
	 */
	function convert($entryId, $conversionProfileId = null, array $dynamicConversionAttributes = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "conversionProfileId", $conversionProfileId);
		if ($dynamicConversionAttributes !== null)
			foreach($dynamicConversionAttributes as $index => $obj)
			{
				$this->client->addParam($kparams, "dynamicConversionAttributes:$index", $obj->toParams());
			}
		$this->client->queueServiceActionCall("document", "convert", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Get document entry by ID.
	 * 
	 * @param string $entryId Document entry id
	 * @param int $version Desired version of the data
	 * @return KalturaDocumentEntry
	 */
	function get($entryId, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("document", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDocumentEntry");
		return $resultObject;
	}

	/**
	 * Update document entry. Only the properties that were set will be updated.
	 * 
	 * @param string $entryId Document entry id to update
	 * @param KalturaDocumentEntry $documentEntry Document entry metadata to update
	 * @return KalturaDocumentEntry
	 */
	function update($entryId, KalturaDocumentEntry $documentEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "documentEntry", $documentEntry->toParams());
		$this->client->queueServiceActionCall("document", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDocumentEntry");
		return $resultObject;
	}

	/**
	 * Delete a document entry.
	 * 
	 * @param string $entryId Document entry id to delete
	 * @return 
	 */
	function delete($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("document", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List document entries by filter with paging support.
	 * 
	 * @param KalturaDocumentEntryFilter $filter Document entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaDocumentListResponse
	 */
	function listAction(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("document", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDocumentListResponse");
		return $resultObject;
	}

	/**
	 * Upload a document file to Kaltura, then the file can be used to create a document entry.
	 * 
	 * @param file $fileData The file data
	 * @return string
	 */
	function upload($fileData)
	{
		$kparams = array();
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("document", "upload", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * This will queue a batch job for converting the document file to swf
	 Returns the URL where the new swf will be available
	 * 
	 * @param string $entryId 
	 * @return string
	 */
	function convertPptToSwf($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("document", "convertPptToSwf", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Serves the file content
	 * 
	 * @param string $entryId Document entry id
	 * @param string $flavorAssetId Flavor asset id
	 * @param bool $forceProxy Force to get the content without redirect
	 * @return file
	 */
	function serve($entryId, $flavorAssetId = null, $forceProxy = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "flavorAssetId", $flavorAssetId);
		$this->client->addParam($kparams, "forceProxy", $forceProxy);
		$this->client->queueServiceActionCall("document", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Serves the file content
	 * 
	 * @param string $entryId Document entry id
	 * @param string $flavorParamsId Flavor params id
	 * @param bool $forceProxy Force to get the content without redirect
	 * @return file
	 */
	function serveByFlavorParamsId($entryId, $flavorParamsId = null, $forceProxy = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "flavorParamsId", $flavorParamsId);
		$this->client->addParam($kparams, "forceProxy", $forceProxy);
		$this->client->queueServiceActionCall("document", "serveByFlavorParamsId", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Replace content associated with the given document entry.
	 * 
	 * @param string $entryId Document entry id to update
	 * @param KalturaResource $resource Resource to be used to replace entry doc content
	 * @param int $conversionProfileId The conversion profile id to be used on the entry
	 * @return KalturaDocumentEntry
	 */
	function updateContent($entryId, KalturaResource $resource, $conversionProfileId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "resource", $resource->toParams());
		$this->client->addParam($kparams, "conversionProfileId", $conversionProfileId);
		$this->client->queueServiceActionCall("document", "updateContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDocumentEntry");
		return $resultObject;
	}

	/**
	 * Approves document replacement
	 * 
	 * @param string $entryId Document entry id to replace
	 * @return KalturaDocumentEntry
	 */
	function approveReplace($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("document", "approveReplace", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDocumentEntry");
		return $resultObject;
	}

	/**
	 * Cancels document replacement
	 * 
	 * @param string $entryId Document entry id to cancel
	 * @return KalturaDocumentEntry
	 */
	function cancelReplace($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("document", "cancelReplace", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDocumentEntry");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailIngestionProfileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * EmailIngestionProfile Add action allows you to add a EmailIngestionProfile to Kaltura DB
	 * 
	 * @param KalturaEmailIngestionProfile $EmailIP Mandatory input parameter of type KalturaEmailIngestionProfile
	 * @return KalturaEmailIngestionProfile
	 */
	function add(KalturaEmailIngestionProfile $EmailIP)
	{
		$kparams = array();
		$this->client->addParam($kparams, "EmailIP", $EmailIP->toParams());
		$this->client->queueServiceActionCall("emailingestionprofile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEmailIngestionProfile");
		return $resultObject;
	}

	/**
	 * Retrieve a EmailIngestionProfile by email address
	 * 
	 * @param string $emailAddress 
	 * @return KalturaEmailIngestionProfile
	 */
	function getByEmailAddress($emailAddress)
	{
		$kparams = array();
		$this->client->addParam($kparams, "emailAddress", $emailAddress);
		$this->client->queueServiceActionCall("emailingestionprofile", "getByEmailAddress", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEmailIngestionProfile");
		return $resultObject;
	}

	/**
	 * Retrieve a EmailIngestionProfile by id
	 * 
	 * @param int $id 
	 * @return KalturaEmailIngestionProfile
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("emailingestionprofile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEmailIngestionProfile");
		return $resultObject;
	}

	/**
	 * Update an existing EmailIngestionProfile
	 * 
	 * @param int $id 
	 * @param KalturaEmailIngestionProfile $EmailIP 
	 * @return KalturaEmailIngestionProfile
	 */
	function update($id, KalturaEmailIngestionProfile $EmailIP)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "EmailIP", $EmailIP->toParams());
		$this->client->queueServiceActionCall("emailingestionprofile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaEmailIngestionProfile");
		return $resultObject;
	}

	/**
	 * Delete an existing EmailIngestionProfile
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("emailingestionprofile", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Add KalturaMediaEntry from email ingestion
	 * 
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param string $uploadTokenId Upload token id
	 * @param int $emailProfId 
	 * @param string $fromAddress 
	 * @param string $emailMsgId 
	 * @return KalturaMediaEntry
	 */
	function addMediaEntry(KalturaMediaEntry $mediaEntry, $uploadTokenId, $emailProfId, $fromAddress, $emailMsgId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->addParam($kparams, "uploadTokenId", $uploadTokenId);
		$this->client->addParam($kparams, "emailProfId", $emailProfId);
		$this->client->addParam($kparams, "fromAddress", $fromAddress);
		$this->client->addParam($kparams, "emailMsgId", $emailMsgId);
		$this->client->queueServiceActionCall("emailingestionprofile", "addMediaEntry", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFileAssetService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new file asset
	 * 
	 * @param KalturaFileAsset $fileAsset 
	 * @return KalturaFileAsset
	 */
	function add(KalturaFileAsset $fileAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "fileAsset", $fileAsset->toParams());
		$this->client->queueServiceActionCall("fileasset", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFileAsset");
		return $resultObject;
	}

	/**
	 * Get file asset by id
	 * 
	 * @param int $id 
	 * @return KalturaFileAsset
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("fileasset", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFileAsset");
		return $resultObject;
	}

	/**
	 * Update file asset by id
	 * 
	 * @param int $id 
	 * @param KalturaFileAsset $fileAsset 
	 * @return KalturaFileAsset
	 */
	function update($id, KalturaFileAsset $fileAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "fileAsset", $fileAsset->toParams());
		$this->client->queueServiceActionCall("fileasset", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFileAsset");
		return $resultObject;
	}

	/**
	 * Delete file asset by id
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("fileasset", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Serve file asset by id
	 * 
	 * @param int $id 
	 * @return file
	 */
	function serve($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("fileasset", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Set content of file asset
	 * 
	 * @param string $id 
	 * @param KalturaContentResource $contentResource 
	 * @return KalturaFileAsset
	 */
	function setContent($id, KalturaContentResource $contentResource)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "contentResource", $contentResource->toParams());
		$this->client->queueServiceActionCall("fileasset", "setContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFileAsset");
		return $resultObject;
	}

	/**
	 * List file assets by filter and pager
	 * 
	 * @param KalturaFileAssetFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaFileAssetListResponse
	 */
	function listAction(KalturaFileAssetFilter $filter, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("fileasset", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFileAssetListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorAssetService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add flavor asset
	 * 
	 * @param string $entryId 
	 * @param KalturaFlavorAsset $flavorAsset 
	 * @return KalturaFlavorAsset
	 */
	function add($entryId, KalturaFlavorAsset $flavorAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "flavorAsset", $flavorAsset->toParams());
		$this->client->queueServiceActionCall("flavorasset", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorAsset");
		return $resultObject;
	}

	/**
	 * Update flavor asset
	 * 
	 * @param string $id 
	 * @param KalturaFlavorAsset $flavorAsset 
	 * @return KalturaFlavorAsset
	 */
	function update($id, KalturaFlavorAsset $flavorAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "flavorAsset", $flavorAsset->toParams());
		$this->client->queueServiceActionCall("flavorasset", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorAsset");
		return $resultObject;
	}

	/**
	 * Update content of flavor asset
	 * 
	 * @param string $id 
	 * @param KalturaContentResource $contentResource 
	 * @return KalturaFlavorAsset
	 */
	function setContent($id, KalturaContentResource $contentResource)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "contentResource", $contentResource->toParams());
		$this->client->queueServiceActionCall("flavorasset", "setContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorAsset");
		return $resultObject;
	}

	/**
	 * Get Flavor Asset by ID
	 * 
	 * @param string $id 
	 * @return KalturaFlavorAsset
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("flavorasset", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorAsset");
		return $resultObject;
	}

	/**
	 * Get Flavor Assets for Entry
	 * 
	 * @param string $entryId 
	 * @return array
	 */
	function getByEntryId($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("flavorasset", "getByEntryId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * List Flavor Assets by filter and pager
	 * 
	 * @param KalturaAssetFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaFlavorAssetListResponse
	 */
	function listAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("flavorasset", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorAssetListResponse");
		return $resultObject;
	}

	/**
	 * Get web playable Flavor Assets for Entry
	 * 
	 * @param string $entryId 
	 * @return array
	 */
	function getWebPlayableByEntryId($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("flavorasset", "getWebPlayableByEntryId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Add and convert new Flavor Asset for Entry with specific Flavor Params
	 * 
	 * @param string $entryId 
	 * @param int $flavorParamsId 
	 * @param int $priority 
	 * @return 
	 */
	function convert($entryId, $flavorParamsId, $priority = 0)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "flavorParamsId", $flavorParamsId);
		$this->client->addParam($kparams, "priority", $priority);
		$this->client->queueServiceActionCall("flavorasset", "convert", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Reconvert Flavor Asset by ID
	 * 
	 * @param string $id Flavor Asset ID
	 * @return 
	 */
	function reconvert($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("flavorasset", "reconvert", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Delete Flavor Asset by ID
	 * 
	 * @param string $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("flavorasset", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Get download URL for the asset
	 * 
	 * @param string $id 
	 * @param int $storageId 
	 * @param bool $forceProxy 
	 * @return string
	 */
	function getUrl($id, $storageId = null, $forceProxy = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "storageId", $storageId);
		$this->client->addParam($kparams, "forceProxy", $forceProxy);
		$this->client->queueServiceActionCall("flavorasset", "getUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Get remote storage existing paths for the asset
	 * 
	 * @param string $id 
	 * @return KalturaRemotePathListResponse
	 */
	function getRemotePaths($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("flavorasset", "getRemotePaths", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaRemotePathListResponse");
		return $resultObject;
	}

	/**
	 * Get download URL for the Flavor Asset
	 * 
	 * @param string $id 
	 * @param bool $useCdn 
	 * @return string
	 */
	function getDownloadUrl($id, $useCdn = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "useCdn", $useCdn);
		$this->client->queueServiceActionCall("flavorasset", "getDownloadUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Get Flavor Asset with the relevant Flavor Params (Flavor Params can exist without Flavor Asset & vice versa)
	 * 
	 * @param string $entryId 
	 * @return array
	 */
	function getFlavorAssetsWithParams($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("flavorasset", "getFlavorAssetsWithParams", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Manually export an asset
	 * 
	 * @param string $assetId 
	 * @param int $storageProfileId 
	 * @return KalturaFlavorAsset
	 */
	function export($assetId, $storageProfileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "assetId", $assetId);
		$this->client->addParam($kparams, "storageProfileId", $storageProfileId);
		$this->client->queueServiceActionCall("flavorasset", "export", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorAsset");
		return $resultObject;
	}

	/**
	 * Set a given flavor as the original flavor
	 * 
	 * @param string $assetId 
	 * @return 
	 */
	function setAsSource($assetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "assetId", $assetId);
		$this->client->queueServiceActionCall("flavorasset", "setAsSource", $kparams);
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
class KalturaFlavorParamsOutputService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Get flavor params output object by ID
	 * 
	 * @param int $id 
	 * @return KalturaFlavorParamsOutput
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("flavorparamsoutput", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorParamsOutput");
		return $resultObject;
	}

	/**
	 * List flavor params output objects by filter and pager
	 * 
	 * @param KalturaFlavorParamsOutputFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaFlavorParamsOutputListResponse
	 */
	function listAction(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("flavorparamsoutput", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorParamsOutputListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFlavorParamsService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new Flavor Params
	 * 
	 * @param KalturaFlavorParams $flavorParams 
	 * @return KalturaFlavorParams
	 */
	function add(KalturaFlavorParams $flavorParams)
	{
		$kparams = array();
		$this->client->addParam($kparams, "flavorParams", $flavorParams->toParams());
		$this->client->queueServiceActionCall("flavorparams", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorParams");
		return $resultObject;
	}

	/**
	 * Get Flavor Params by ID
	 * 
	 * @param int $id 
	 * @return KalturaFlavorParams
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("flavorparams", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorParams");
		return $resultObject;
	}

	/**
	 * Update Flavor Params by ID
	 * 
	 * @param int $id 
	 * @param KalturaFlavorParams $flavorParams 
	 * @return KalturaFlavorParams
	 */
	function update($id, KalturaFlavorParams $flavorParams)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "flavorParams", $flavorParams->toParams());
		$this->client->queueServiceActionCall("flavorparams", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorParams");
		return $resultObject;
	}

	/**
	 * Delete Flavor Params by ID
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("flavorparams", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List Flavor Params by filter with paging support (By default - all system default params will be listed too)
	 * 
	 * @param KalturaFlavorParamsFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaFlavorParamsListResponse
	 */
	function listAction(KalturaFlavorParamsFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("flavorparams", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFlavorParamsListResponse");
		return $resultObject;
	}

	/**
	 * Get Flavor Params by Conversion Profile ID
	 * 
	 * @param int $conversionProfileId 
	 * @return array
	 */
	function getByConversionProfileId($conversionProfileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "conversionProfileId", $conversionProfileId);
		$this->client->queueServiceActionCall("flavorparams", "getByConversionProfileId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelSegmentService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new live channel segment
	 * 
	 * @param KalturaLiveChannelSegment $liveChannelSegment 
	 * @return KalturaLiveChannelSegment
	 */
	function add(KalturaLiveChannelSegment $liveChannelSegment)
	{
		$kparams = array();
		$this->client->addParam($kparams, "liveChannelSegment", $liveChannelSegment->toParams());
		$this->client->queueServiceActionCall("livechannelsegment", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveChannelSegment");
		return $resultObject;
	}

	/**
	 * Get live channel segment by id
	 * 
	 * @param int $id 
	 * @return KalturaLiveChannelSegment
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("livechannelsegment", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveChannelSegment");
		return $resultObject;
	}

	/**
	 * Update live channel segment by id
	 * 
	 * @param int $id 
	 * @param KalturaLiveChannelSegment $liveChannelSegment 
	 * @return KalturaLiveChannelSegment
	 */
	function update($id, KalturaLiveChannelSegment $liveChannelSegment)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "liveChannelSegment", $liveChannelSegment->toParams());
		$this->client->queueServiceActionCall("livechannelsegment", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveChannelSegment");
		return $resultObject;
	}

	/**
	 * Delete live channel segment by id
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("livechannelsegment", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List live channel segments by filter and pager
	 * 
	 * @param KalturaLiveChannelSegmentFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaLiveChannelSegmentListResponse
	 */
	function listAction(KalturaLiveChannelSegmentFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("livechannelsegment", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveChannelSegmentListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaLiveChannelService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds new live channel.
	 * 
	 * @param KalturaLiveChannel $liveChannel Live channel metadata
	 * @return KalturaLiveChannel
	 */
	function add(KalturaLiveChannel $liveChannel)
	{
		$kparams = array();
		$this->client->addParam($kparams, "liveChannel", $liveChannel->toParams());
		$this->client->queueServiceActionCall("livechannel", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveChannel");
		return $resultObject;
	}

	/**
	 * Get live channel by ID.
	 * 
	 * @param string $id Live channel id
	 * @return KalturaLiveChannel
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("livechannel", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveChannel");
		return $resultObject;
	}

	/**
	 * Update live channel. Only the properties that were set will be updated.
	 * 
	 * @param string $id Live channel id to update
	 * @param KalturaLiveChannel $liveChannel Live channel metadata to update
	 * @return KalturaLiveChannel
	 */
	function update($id, KalturaLiveChannel $liveChannel)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "liveChannel", $liveChannel->toParams());
		$this->client->queueServiceActionCall("livechannel", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveChannel");
		return $resultObject;
	}

	/**
	 * Delete a live channel.
	 * 
	 * @param string $id Live channel id to delete
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("livechannel", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List live channels by filter with paging support.
	 * 
	 * @param KalturaLiveChannelFilter $filter Live channel filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaLiveChannelListResponse
	 */
	function listAction(KalturaLiveChannelFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("livechannel", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveChannelListResponse");
		return $resultObject;
	}

	/**
	 * Delivering the status of a live channel (on-air/offline)
	 * 
	 * @param string $id ID of the live channel
	 * @return bool
	 */
	function isLive($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("livechannel", "isLive", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$resultObject = (bool) $resultObject;
		return $resultObject;
	}

	/**
	 * Append recorded video to live entry
	 * 
	 * @param string $entryId Live entry id
	 * @param int $mediaServerIndex 
	 * @param KalturaServerFileResource $resource 
	 * @param float $duration 
	 * @return 
	 */
	function appendRecording($entryId, $mediaServerIndex, KalturaServerFileResource $resource, $duration)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "mediaServerIndex", $mediaServerIndex);
		$this->client->addParam($kparams, "resource", $resource->toParams());
		$this->client->addParam($kparams, "duration", $duration);
		$this->client->queueServiceActionCall("livechannel", "appendRecording", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Register media server to live entry
	 * 
	 * @param string $entryId Live entry id
	 * @param string $hostname Media server host name
	 * @param int $mediaServerIndex Media server index primary / secondary
	 * @return KalturaLiveEntry
	 */
	function registerMediaServer($entryId, $hostname, $mediaServerIndex)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "hostname", $hostname);
		$this->client->addParam($kparams, "mediaServerIndex", $mediaServerIndex);
		$this->client->queueServiceActionCall("livechannel", "registerMediaServer", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveEntry");
		return $resultObject;
	}

	/**
	 * Unregister media server from live entry
	 * 
	 * @param string $entryId Live entry id
	 * @param string $hostname Media server host name
	 * @param int $mediaServerIndex Media server index primary / secondary
	 * @return KalturaLiveEntry
	 */
	function unregisterMediaServer($entryId, $hostname, $mediaServerIndex)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "hostname", $hostname);
		$this->client->addParam($kparams, "mediaServerIndex", $mediaServerIndex);
		$this->client->queueServiceActionCall("livechannel", "unregisterMediaServer", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveEntry");
		return $resultObject;
	}

	/**
	 * Validates all registered media servers
	 * 
	 * @param string $entryId Live entry id
	 * @return 
	 */
	function validateRegisteredMediaServers($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("livechannel", "validateRegisteredMediaServers", $kparams);
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
class KalturaLiveStreamService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds new live stream entry.
	 The entry will be queued for provision.
	 * 
	 * @param KalturaLiveStreamEntry $liveStreamEntry Live stream entry metadata
	 * @param string $sourceType Live stream source type
	 * @return KalturaLiveStreamEntry
	 */
	function add(KalturaLiveStreamEntry $liveStreamEntry, $sourceType = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "liveStreamEntry", $liveStreamEntry->toParams());
		$this->client->addParam($kparams, "sourceType", $sourceType);
		$this->client->queueServiceActionCall("livestream", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveStreamEntry");
		return $resultObject;
	}

	/**
	 * Get live stream entry by ID.
	 * 
	 * @param string $entryId Live stream entry id
	 * @param int $version Desired version of the data
	 * @return KalturaLiveStreamEntry
	 */
	function get($entryId, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("livestream", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveStreamEntry");
		return $resultObject;
	}

	/**
	 * Authenticate live-stream entry against stream token and partner limitations
	 * 
	 * @param string $entryId Live stream entry id
	 * @param string $token Live stream broadcasting token
	 * @return KalturaLiveStreamEntry
	 */
	function authenticate($entryId, $token)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "token", $token);
		$this->client->queueServiceActionCall("livestream", "authenticate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveStreamEntry");
		return $resultObject;
	}

	/**
	 * Update live stream entry. Only the properties that were set will be updated.
	 * 
	 * @param string $entryId Live stream entry id to update
	 * @param KalturaLiveStreamEntry $liveStreamEntry Live stream entry metadata to update
	 * @return KalturaLiveStreamEntry
	 */
	function update($entryId, KalturaLiveStreamEntry $liveStreamEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "liveStreamEntry", $liveStreamEntry->toParams());
		$this->client->queueServiceActionCall("livestream", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveStreamEntry");
		return $resultObject;
	}

	/**
	 * Delete a live stream entry.
	 * 
	 * @param string $entryId Live stream entry id to delete
	 * @return 
	 */
	function delete($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("livestream", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List live stream entries by filter with paging support.
	 * 
	 * @param KalturaLiveStreamEntryFilter $filter Live stream entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaLiveStreamListResponse
	 */
	function listAction(KalturaLiveStreamEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("livestream", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveStreamListResponse");
		return $resultObject;
	}

	/**
	 * Update live stream entry thumbnail using a raw jpeg file
	 * 
	 * @param string $entryId Live stream entry id
	 * @param file $fileData Jpeg file data
	 * @return KalturaLiveStreamEntry
	 */
	function updateOfflineThumbnailJpeg($entryId, $fileData)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("livestream", "updateOfflineThumbnailJpeg", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveStreamEntry");
		return $resultObject;
	}

	/**
	 * Update entry thumbnail using url
	 * 
	 * @param string $entryId Live stream entry id
	 * @param string $url File url
	 * @return KalturaLiveStreamEntry
	 */
	function updateOfflineThumbnailFromUrl($entryId, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "url", $url);
		$this->client->queueServiceActionCall("livestream", "updateOfflineThumbnailFromUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveStreamEntry");
		return $resultObject;
	}

	/**
	 * Delivering the status of a live stream (on-air/offline) if it is possible
	 * 
	 * @param string $id ID of the live stream
	 * @param string $protocol Protocol of the stream to test.
	 * @return bool
	 */
	function isLive($id, $protocol)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "protocol", $protocol);
		$this->client->queueServiceActionCall("livestream", "isLive", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$resultObject = (bool) $resultObject;
		return $resultObject;
	}

	/**
	 * Append recorded video to live entry
	 * 
	 * @param string $entryId Live entry id
	 * @param int $mediaServerIndex 
	 * @param KalturaServerFileResource $resource 
	 * @param float $duration 
	 * @return 
	 */
	function appendRecording($entryId, $mediaServerIndex, KalturaServerFileResource $resource, $duration)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "mediaServerIndex", $mediaServerIndex);
		$this->client->addParam($kparams, "resource", $resource->toParams());
		$this->client->addParam($kparams, "duration", $duration);
		$this->client->queueServiceActionCall("livestream", "appendRecording", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Register media server to live entry
	 * 
	 * @param string $entryId Live entry id
	 * @param string $hostname Media server host name
	 * @param int $mediaServerIndex Media server index primary / secondary
	 * @return KalturaLiveEntry
	 */
	function registerMediaServer($entryId, $hostname, $mediaServerIndex)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "hostname", $hostname);
		$this->client->addParam($kparams, "mediaServerIndex", $mediaServerIndex);
		$this->client->queueServiceActionCall("livestream", "registerMediaServer", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveEntry");
		return $resultObject;
	}

	/**
	 * Unregister media server from live entry
	 * 
	 * @param string $entryId Live entry id
	 * @param string $hostname Media server host name
	 * @param int $mediaServerIndex Media server index primary / secondary
	 * @return KalturaLiveEntry
	 */
	function unregisterMediaServer($entryId, $hostname, $mediaServerIndex)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "hostname", $hostname);
		$this->client->addParam($kparams, "mediaServerIndex", $mediaServerIndex);
		$this->client->queueServiceActionCall("livestream", "unregisterMediaServer", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaLiveEntry");
		return $resultObject;
	}

	/**
	 * Validates all registered media servers
	 * 
	 * @param string $entryId Live entry id
	 * @return 
	 */
	function validateRegisteredMediaServers($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("livestream", "validateRegisteredMediaServers", $kparams);
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
class KalturaMediaInfoService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * List media info objects by filter and pager
	 * 
	 * @param KalturaMediaInfoFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaMediaInfoListResponse
	 */
	function listAction(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("mediainfo", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaInfoListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaServerService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Get media server by hostname
	 * 
	 * @param string $hostname 
	 * @return KalturaMediaServer
	 */
	function get($hostname)
	{
		$kparams = array();
		$this->client->addParam($kparams, "hostname", $hostname);
		$this->client->queueServiceActionCall("mediaserver", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaServer");
		return $resultObject;
	}

	/**
	 * Update media server status
	 * 
	 * @param string $hostname 
	 * @param KalturaMediaServerStatus $mediaServerStatus 
	 * @return KalturaMediaServer
	 */
	function reportStatus($hostname, KalturaMediaServerStatus $mediaServerStatus)
	{
		$kparams = array();
		$this->client->addParam($kparams, "hostname", $hostname);
		$this->client->addParam($kparams, "mediaServerStatus", $mediaServerStatus->toParams());
		$this->client->queueServiceActionCall("mediaserver", "reportStatus", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaServer");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMediaService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add entry
	 * 
	 * @param KalturaMediaEntry $entry 
	 * @return KalturaMediaEntry
	 */
	function add(KalturaMediaEntry $entry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entry", $entry->toParams());
		$this->client->queueServiceActionCall("media", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Add content to media entry which is not yet associated with content (therefore is in status NO_CONTENT).
     If the requirement is to replace the entry's associated content, use action updateContent.
	 * 
	 * @param string $entryId 
	 * @param KalturaResource $resource 
	 * @return KalturaMediaEntry
	 */
	function addContent($entryId, KalturaResource $resource = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		if ($resource !== null)
			$this->client->addParam($kparams, "resource", $resource->toParams());
		$this->client->queueServiceActionCall("media", "addContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Adds new media entry by importing an HTTP or FTP URL.
	 The entry will be queued for import and then for conversion.
	 * 
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param string $url An HTTP or FTP URL
	 * @return KalturaMediaEntry
	 */
	function addFromUrl(KalturaMediaEntry $mediaEntry, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->addParam($kparams, "url", $url);
		$this->client->queueServiceActionCall("media", "addFromUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Adds new media entry by importing the media file from a search provider.
	 This action should be used with the search service result.
	 * 
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param KalturaSearchResult $searchResult Result object from search service
	 * @return KalturaMediaEntry
	 */
	function addFromSearchResult(KalturaMediaEntry $mediaEntry = null, KalturaSearchResult $searchResult = null)
	{
		$kparams = array();
		if ($mediaEntry !== null)
			$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		if ($searchResult !== null)
			$this->client->addParam($kparams, "searchResult", $searchResult->toParams());
		$this->client->queueServiceActionCall("media", "addFromSearchResult", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Add new entry after the specific media file was uploaded and the upload token id exists
	 * 
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param string $uploadTokenId Upload token id
	 * @return KalturaMediaEntry
	 */
	function addFromUploadedFile(KalturaMediaEntry $mediaEntry, $uploadTokenId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->addParam($kparams, "uploadTokenId", $uploadTokenId);
		$this->client->queueServiceActionCall("media", "addFromUploadedFile", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Add new entry after the file was recored on the server and the token id exists
	 * 
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param string $webcamTokenId Token id for the recored webcam file
	 * @return KalturaMediaEntry
	 */
	function addFromRecordedWebcam(KalturaMediaEntry $mediaEntry, $webcamTokenId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->addParam($kparams, "webcamTokenId", $webcamTokenId);
		$this->client->queueServiceActionCall("media", "addFromRecordedWebcam", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Copy entry into new entry
	 * 
	 * @param string $sourceEntryId Media entry id to copy from
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @param int $sourceFlavorParamsId The flavor to be used as the new entry source, source flavor will be used if not specified
	 * @return KalturaMediaEntry
	 */
	function addFromEntry($sourceEntryId, KalturaMediaEntry $mediaEntry = null, $sourceFlavorParamsId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "sourceEntryId", $sourceEntryId);
		if ($mediaEntry !== null)
			$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->addParam($kparams, "sourceFlavorParamsId", $sourceFlavorParamsId);
		$this->client->queueServiceActionCall("media", "addFromEntry", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Copy flavor asset into new entry
	 * 
	 * @param string $sourceFlavorAssetId Flavor asset id to be used as the new entry source
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata
	 * @return KalturaMediaEntry
	 */
	function addFromFlavorAsset($sourceFlavorAssetId, KalturaMediaEntry $mediaEntry = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "sourceFlavorAssetId", $sourceFlavorAssetId);
		if ($mediaEntry !== null)
			$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->queueServiceActionCall("media", "addFromFlavorAsset", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Convert entry
	 * 
	 * @param string $entryId Media entry id
	 * @param int $conversionProfileId 
	 * @param array $dynamicConversionAttributes 
	 * @return int
	 */
	function convert($entryId, $conversionProfileId = null, array $dynamicConversionAttributes = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "conversionProfileId", $conversionProfileId);
		if ($dynamicConversionAttributes !== null)
			foreach($dynamicConversionAttributes as $index => $obj)
			{
				$this->client->addParam($kparams, "dynamicConversionAttributes:$index", $obj->toParams());
			}
		$this->client->queueServiceActionCall("media", "convert", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Get media entry by ID.
	 * 
	 * @param string $entryId Media entry id
	 * @param int $version Desired version of the data
	 * @return KalturaMediaEntry
	 */
	function get($entryId, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("media", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Get MRSS by entry id
     XML will return as an escaped string
	 * 
	 * @param string $entryId Entry id
	 * @param array $extendingItemsArray 
	 * @return string
	 */
	function getMrss($entryId, array $extendingItemsArray = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		if ($extendingItemsArray !== null)
			foreach($extendingItemsArray as $index => $obj)
			{
				$this->client->addParam($kparams, "extendingItemsArray:$index", $obj->toParams());
			}
		$this->client->queueServiceActionCall("media", "getMrss", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Update media entry. Only the properties that were set will be updated.
	 * 
	 * @param string $entryId Media entry id to update
	 * @param KalturaMediaEntry $mediaEntry Media entry metadata to update
	 * @return KalturaMediaEntry
	 */
	function update($entryId, KalturaMediaEntry $mediaEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "mediaEntry", $mediaEntry->toParams());
		$this->client->queueServiceActionCall("media", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Replace content associated with the media entry.
	 * 
	 * @param string $entryId Media entry id to update
	 * @param KalturaResource $resource Resource to be used to replace entry media content
	 * @param int $conversionProfileId The conversion profile id to be used on the entry
	 * @return KalturaMediaEntry
	 */
	function updateContent($entryId, KalturaResource $resource, $conversionProfileId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "resource", $resource->toParams());
		$this->client->addParam($kparams, "conversionProfileId", $conversionProfileId);
		$this->client->queueServiceActionCall("media", "updateContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Delete a media entry.
	 * 
	 * @param string $entryId Media entry id to delete
	 * @return 
	 */
	function delete($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("media", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Approves media replacement
	 * 
	 * @param string $entryId Media entry id to replace
	 * @return KalturaMediaEntry
	 */
	function approveReplace($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("media", "approveReplace", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Cancels media replacement
	 * 
	 * @param string $entryId Media entry id to cancel
	 * @return KalturaMediaEntry
	 */
	function cancelReplace($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("media", "cancelReplace", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * List media entries by filter with paging support.
	 * 
	 * @param KalturaMediaEntryFilter $filter Media entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaMediaListResponse
	 */
	function listAction(KalturaMediaEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("media", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaListResponse");
		return $resultObject;
	}

	/**
	 * Count media entries by filter.
	 * 
	 * @param KalturaMediaEntryFilter $filter Media entry filter
	 * @return int
	 */
	function count(KalturaMediaEntryFilter $filter = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("media", "count", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Upload a media file to Kaltura, then the file can be used to create a media entry.
	 * 
	 * @param file $fileData The file data
	 * @return string
	 */
	function upload($fileData)
	{
		$kparams = array();
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("media", "upload", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Update media entry thumbnail by a specified time offset (In seconds)
	 If flavor params id not specified, source flavor will be used by default
	 * 
	 * @param string $entryId Media entry id
	 * @param int $timeOffset Time offset (in seconds)
	 * @param int $flavorParamsId The flavor params id to be used
	 * @return KalturaMediaEntry
	 */
	function updateThumbnail($entryId, $timeOffset, $flavorParamsId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "timeOffset", $timeOffset);
		$this->client->addParam($kparams, "flavorParamsId", $flavorParamsId);
		$this->client->queueServiceActionCall("media", "updateThumbnail", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Update media entry thumbnail from a different entry by a specified time offset (In seconds)
	 If flavor params id not specified, source flavor will be used by default
	 * 
	 * @param string $entryId Media entry id
	 * @param string $sourceEntryId Media entry id
	 * @param int $timeOffset Time offset (in seconds)
	 * @param int $flavorParamsId The flavor params id to be used
	 * @return KalturaMediaEntry
	 */
	function updateThumbnailFromSourceEntry($entryId, $sourceEntryId, $timeOffset, $flavorParamsId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "sourceEntryId", $sourceEntryId);
		$this->client->addParam($kparams, "timeOffset", $timeOffset);
		$this->client->addParam($kparams, "flavorParamsId", $flavorParamsId);
		$this->client->queueServiceActionCall("media", "updateThumbnailFromSourceEntry", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Update media entry thumbnail using a raw jpeg file
	 * 
	 * @param string $entryId Media entry id
	 * @param file $fileData Jpeg file data
	 * @return KalturaMediaEntry
	 */
	function updateThumbnailJpeg($entryId, $fileData)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("media", "updateThumbnailJpeg", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMediaEntry");
		return $resultObject;
	}

	/**
	 * Update entry thumbnail using url
	 * 
	 * @param string $entryId Media entry id
	 * @param string $url File url
	 * @return KalturaBaseEntry
	 */
	function updateThumbnailFromUrl($entryId, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "url", $url);
		$this->client->queueServiceActionCall("media", "updateThumbnailFromUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseEntry");
		return $resultObject;
	}

	/**
	 * Request a new conversion job, this can be used to convert the media entry to a different format
	 * 
	 * @param string $entryId Media entry id
	 * @param string $fileFormat Format to convert
	 * @return int
	 */
	function requestConversion($entryId, $fileFormat)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "fileFormat", $fileFormat);
		$this->client->queueServiceActionCall("media", "requestConversion", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Flag inappropriate media entry for moderation
	 * 
	 * @param KalturaModerationFlag $moderationFlag 
	 * @return 
	 */
	function flag(KalturaModerationFlag $moderationFlag)
	{
		$kparams = array();
		$this->client->addParam($kparams, "moderationFlag", $moderationFlag->toParams());
		$this->client->queueServiceActionCall("media", "flag", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Reject the media entry and mark the pending flags (if any) as moderated (this will make the entry non playable)
	 * 
	 * @param string $entryId 
	 * @return 
	 */
	function reject($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("media", "reject", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Approve the media entry and mark the pending flags (if any) as moderated (this will make the entry playable)
	 * 
	 * @param string $entryId 
	 * @return 
	 */
	function approve($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("media", "approve", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List all pending flags for the media entry
	 * 
	 * @param string $entryId 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaModerationFlagListResponse
	 */
	function listFlags($entryId, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("media", "listFlags", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaModerationFlagListResponse");
		return $resultObject;
	}

	/**
	 * Anonymously rank a media entry, no validation is done on duplicate rankings
	 * 
	 * @param string $entryId 
	 * @param int $rank 
	 * @return 
	 */
	function anonymousRank($entryId, $rank)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "rank", $rank);
		$this->client->queueServiceActionCall("media", "anonymousRank", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Add new bulk upload batch job
	 Conversion profile id can be specified in the API or in the CSV file, the one in the CSV file will be stronger.
	 If no conversion profile was specified, partner's default will be used
	 * 
	 * @param file $fileData 
	 * @param KalturaBulkUploadJobData $bulkUploadData 
	 * @param KalturaBulkUploadEntryData $bulkUploadEntryData 
	 * @return KalturaBulkUpload
	 */
	function bulkUploadAdd($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadEntryData $bulkUploadEntryData = null)
	{
		$kparams = array();
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		if ($bulkUploadData !== null)
			$this->client->addParam($kparams, "bulkUploadData", $bulkUploadData->toParams());
		if ($bulkUploadEntryData !== null)
			$this->client->addParam($kparams, "bulkUploadEntryData", $bulkUploadEntryData->toParams());
		$this->client->queueServiceActionCall("media", "bulkUploadAdd", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUpload");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaMixingService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds a new mix.
	 If the dataContent is null, a default timeline will be created.
	 * 
	 * @param KalturaMixEntry $mixEntry Mix entry metadata
	 * @return KalturaMixEntry
	 */
	function add(KalturaMixEntry $mixEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mixEntry", $mixEntry->toParams());
		$this->client->queueServiceActionCall("mixing", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	/**
	 * Get mix entry by id.
	 * 
	 * @param string $entryId Mix entry id
	 * @param int $version Desired version of the data
	 * @return KalturaMixEntry
	 */
	function get($entryId, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("mixing", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	/**
	 * Update mix entry. Only the properties that were set will be updated.
	 * 
	 * @param string $entryId Mix entry id to update
	 * @param KalturaMixEntry $mixEntry Mix entry metadata to update
	 * @return KalturaMixEntry
	 */
	function update($entryId, KalturaMixEntry $mixEntry)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "mixEntry", $mixEntry->toParams());
		$this->client->queueServiceActionCall("mixing", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	/**
	 * Delete a mix entry.
	 * 
	 * @param string $entryId Mix entry id to delete
	 * @return 
	 */
	function delete($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("mixing", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List entries by filter with paging support.
	 Return parameter is an array of mix entries.
	 * 
	 * @param KalturaMixEntryFilter $filter Mix entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @return KalturaMixListResponse
	 */
	function listAction(KalturaMixEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("mixing", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixListResponse");
		return $resultObject;
	}

	/**
	 * Count mix entries by filter.
	 * 
	 * @param KalturaMediaEntryFilter $filter Media entry filter
	 * @return int
	 */
	function count(KalturaMediaEntryFilter $filter = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("mixing", "count", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}

	/**
	 * Clones an existing mix.
	 * 
	 * @param string $entryId Mix entry id to clone
	 * @return KalturaMixEntry
	 */
	function cloneAction($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("mixing", "clone", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	/**
	 * Appends a media entry to a the end of the mix timeline, this will save the mix timeline as a new version.
	 * 
	 * @param string $mixEntryId Mix entry to append to its timeline
	 * @param string $mediaEntryId Media entry to append to the timeline
	 * @return KalturaMixEntry
	 */
	function appendMediaEntry($mixEntryId, $mediaEntryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mixEntryId", $mixEntryId);
		$this->client->addParam($kparams, "mediaEntryId", $mediaEntryId);
		$this->client->queueServiceActionCall("mixing", "appendMediaEntry", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaMixEntry");
		return $resultObject;
	}

	/**
	 * Get the mixes in which the media entry is included
	 * 
	 * @param string $mediaEntryId 
	 * @return array
	 */
	function getMixesByMediaId($mediaEntryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaEntryId", $mediaEntryId);
		$this->client->queueServiceActionCall("mixing", "getMixesByMediaId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Get all ready media entries that exist in the given mix id
	 * 
	 * @param string $mixId 
	 * @param int $version Desired version to get the data from
	 * @return array
	 */
	function getReadyMediaEntries($mixId, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mixId", $mixId);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("mixing", "getReadyMediaEntries", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Anonymously rank a mix entry, no validation is done on duplicate rankings
	 * 
	 * @param string $entryId 
	 * @param int $rank 
	 * @return 
	 */
	function anonymousRank($entryId, $rank)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "rank", $rank);
		$this->client->queueServiceActionCall("mixing", "anonymousRank", $kparams);
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
class KalturaNotificationService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Return the notifications for a specific entry id and type
	 * 
	 * @param string $entryId 
	 * @param int $type 
	 * @return KalturaClientNotification
	 */
	function getClientNotification($entryId, $type)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "type", $type);
		$this->client->queueServiceActionCall("notification", "getClientNotification", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaClientNotification");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPartnerService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Create a new Partner object
	 * 
	 * @param KalturaPartner $partner 
	 * @param string $cmsPassword 
	 * @param int $templatePartnerId 
	 * @param bool $silent 
	 * @return KalturaPartner
	 */
	function register(KalturaPartner $partner, $cmsPassword = "", $templatePartnerId = null, $silent = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "partner", $partner->toParams());
		$this->client->addParam($kparams, "cmsPassword", $cmsPassword);
		$this->client->addParam($kparams, "templatePartnerId", $templatePartnerId);
		$this->client->addParam($kparams, "silent", $silent);
		$this->client->queueServiceActionCall("partner", "register", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	/**
	 * Update details and settings of an existing partner
	 * 
	 * @param KalturaPartner $partner 
	 * @param bool $allowEmpty 
	 * @return KalturaPartner
	 */
	function update(KalturaPartner $partner, $allowEmpty = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "partner", $partner->toParams());
		$this->client->addParam($kparams, "allowEmpty", $allowEmpty);
		$this->client->queueServiceActionCall("partner", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	/**
	 * Retrieve partner object by Id
	 * 
	 * @param int $id 
	 * @return KalturaPartner
	 */
	function get($id = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("partner", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	/**
	 * Retrieve partner secret and admin secret
	 * 
	 * @param int $partnerId 
	 * @param string $adminEmail 
	 * @param string $cmsPassword 
	 * @return KalturaPartner
	 */
	function getSecrets($partnerId, $adminEmail, $cmsPassword)
	{
		$kparams = array();
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "adminEmail", $adminEmail);
		$this->client->addParam($kparams, "cmsPassword", $cmsPassword);
		$this->client->queueServiceActionCall("partner", "getSecrets", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	/**
	 * Retrieve all info attributed to the partner
	 This action expects no parameters. It returns information for the current KS partnerId.
	 * 
	 * @return KalturaPartner
	 */
	function getInfo()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("partner", "getInfo", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartner");
		return $resultObject;
	}

	/**
	 * Get usage statistics for a partner
	 Calculation is done according to partner's package
	 Additional data returned is a graph points of streaming usage in a timeframe
	 The resolution can be "days" or "months"
	 * 
	 * @param int $year 
	 * @param int $month 
	 * @param string $resolution 
	 * @return KalturaPartnerUsage
	 */
	function getUsage($year = "", $month = 1, $resolution = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "year", $year);
		$this->client->addParam($kparams, "month", $month);
		$this->client->addParam($kparams, "resolution", $resolution);
		$this->client->queueServiceActionCall("partner", "getUsage", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartnerUsage");
		return $resultObject;
	}

	/**
	 * Get usage statistics for a partner
	 Calculation is done according to partner's package
	 * 
	 * @return KalturaPartnerStatistics
	 */
	function getStatistics()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("partner", "getStatistics", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartnerStatistics");
		return $resultObject;
	}

	/**
	 * Retrieve a list of partner objects which the current user is allowed to access.
	 * 
	 * @param KalturaPartnerFilter $partnerFilter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaPartnerListResponse
	 */
	function listPartnersForUser(KalturaPartnerFilter $partnerFilter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($partnerFilter !== null)
			$this->client->addParam($kparams, "partnerFilter", $partnerFilter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("partner", "listPartnersForUser", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartnerListResponse");
		return $resultObject;
	}

	/**
	 * List partners by filter with paging support
	 Current implementation will only list the sub partners of the partner initiating the api call (using the current KS).
	 This action is only partially implemented to support listing sub partners of a VAR partner.
	 * 
	 * @param KalturaPartnerFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaPartnerListResponse
	 */
	function listAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("partner", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPartnerListResponse");
		return $resultObject;
	}

	/**
	 * List partner's current processes' statuses
	 * 
	 * @return KalturaFeatureStatusListResponse
	 */
	function listFeatureStatus()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("partner", "listFeatureStatus", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaFeatureStatusListResponse");
		return $resultObject;
	}

	/**
	 * Count partner's existing sub-publishers (count includes the partner itself).
	 * 
	 * @param KalturaPartnerFilter $filter 
	 * @return int
	 */
	function count(KalturaPartnerFilter $filter = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("partner", "count", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionItemService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds a new permission item object to the account.
	 This action is available only to Kaltura system administrators.
	 * 
	 * @param KalturaPermissionItem $permissionItem The new permission item
	 * @return KalturaPermissionItem
	 */
	function add(KalturaPermissionItem $permissionItem)
	{
		$kparams = array();
		$this->client->addParam($kparams, "permissionItem", $permissionItem->toParams());
		$this->client->queueServiceActionCall("permissionitem", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermissionItem");
		return $resultObject;
	}

	/**
	 * Retrieves a permission item object using its ID.
	 * 
	 * @param int $permissionItemId The permission item's unique identifier
	 * @return KalturaPermissionItem
	 */
	function get($permissionItemId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "permissionItemId", $permissionItemId);
		$this->client->queueServiceActionCall("permissionitem", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermissionItem");
		return $resultObject;
	}

	/**
	 * Updates an existing permission item object.
	 This action is available only to Kaltura system administrators.
	 * 
	 * @param int $permissionItemId The permission item's unique identifier
	 * @param KalturaPermissionItem $permissionItem Id The permission item's unique identifier
	 * @return KalturaPermissionItem
	 */
	function update($permissionItemId, KalturaPermissionItem $permissionItem)
	{
		$kparams = array();
		$this->client->addParam($kparams, "permissionItemId", $permissionItemId);
		$this->client->addParam($kparams, "permissionItem", $permissionItem->toParams());
		$this->client->queueServiceActionCall("permissionitem", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermissionItem");
		return $resultObject;
	}

	/**
	 * Deletes an existing permission item object.
	 This action is available only to Kaltura system administrators.
	 * 
	 * @param int $permissionItemId The permission item's unique identifier
	 * @return KalturaPermissionItem
	 */
	function delete($permissionItemId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "permissionItemId", $permissionItemId);
		$this->client->queueServiceActionCall("permissionitem", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermissionItem");
		return $resultObject;
	}

	/**
	 * Lists permission item objects that are associated with an account.
	 * 
	 * @param KalturaPermissionItemFilter $filter A filter used to exclude specific types of permission items
	 * @param KalturaFilterPager $pager A limit for the number of records to display on a page
	 * @return KalturaPermissionItemListResponse
	 */
	function listAction(KalturaPermissionItemFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("permissionitem", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermissionItemListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaPermissionService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds a new permission object to the account.
	 * 
	 * @param KalturaPermission $permission The new permission
	 * @return KalturaPermission
	 */
	function add(KalturaPermission $permission)
	{
		$kparams = array();
		$this->client->addParam($kparams, "permission", $permission->toParams());
		$this->client->queueServiceActionCall("permission", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermission");
		return $resultObject;
	}

	/**
	 * Retrieves a permission object using its ID.
	 * 
	 * @param string $permissionName The name assigned to the permission
	 * @return KalturaPermission
	 */
	function get($permissionName)
	{
		$kparams = array();
		$this->client->addParam($kparams, "permissionName", $permissionName);
		$this->client->queueServiceActionCall("permission", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermission");
		return $resultObject;
	}

	/**
	 * Updates an existing permission object.
	 * 
	 * @param string $permissionName The name assigned to the permission
	 * @param KalturaPermission $permission Name The name assigned to the permission
	 * @return KalturaPermission
	 */
	function update($permissionName, KalturaPermission $permission)
	{
		$kparams = array();
		$this->client->addParam($kparams, "permissionName", $permissionName);
		$this->client->addParam($kparams, "permission", $permission->toParams());
		$this->client->queueServiceActionCall("permission", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermission");
		return $resultObject;
	}

	/**
	 * Deletes an existing permission object.
	 * 
	 * @param string $permissionName The name assigned to the permission
	 * @return KalturaPermission
	 */
	function delete($permissionName)
	{
		$kparams = array();
		$this->client->addParam($kparams, "permissionName", $permissionName);
		$this->client->queueServiceActionCall("permission", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermission");
		return $resultObject;
	}

	/**
	 * Lists permission objects that are associated with an account.
	 Blocked permissions are listed unless you use a filter to exclude them.
	 Blocked permissions are listed unless you use a filter to exclude them.
	 * 
	 * @param KalturaPermissionFilter $filter A filter used to exclude specific types of permissions
	 * @param KalturaFilterPager $pager A limit for the number of records to display on a page
	 * @return KalturaPermissionListResponse
	 */
	function listAction(KalturaPermissionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("permission", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPermissionListResponse");
		return $resultObject;
	}

	/**
	 * Retrieves a list of permissions that apply to the current KS.
	 * 
	 * @return string
	 */
	function getCurrentPermissions()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("permission", "getCurrentPermissions", $kparams);
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
class KalturaPlaylistService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new playlist
	 Note that all entries used in a playlist will become public and may appear in KalturaNetwork
	 * 
	 * @param KalturaPlaylist $playlist 
	 * @param bool $updateStats Indicates that the playlist statistics attributes should be updated synchronously now
	 * @return KalturaPlaylist
	 */
	function add(KalturaPlaylist $playlist, $updateStats = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "playlist", $playlist->toParams());
		$this->client->addParam($kparams, "updateStats", $updateStats);
		$this->client->queueServiceActionCall("playlist", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPlaylist");
		return $resultObject;
	}

	/**
	 * Retrieve a playlist
	 * 
	 * @param string $id 
	 * @param int $version Desired version of the data
	 * @return KalturaPlaylist
	 */
	function get($id, $version = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "version", $version);
		$this->client->queueServiceActionCall("playlist", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPlaylist");
		return $resultObject;
	}

	/**
	 * Update existing playlist
	 Note - you cannot change playlist type. updated playlist must be of the same type.
	 * 
	 * @param string $id 
	 * @param KalturaPlaylist $playlist 
	 * @param bool $updateStats 
	 * @return KalturaPlaylist
	 */
	function update($id, KalturaPlaylist $playlist, $updateStats = false)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "playlist", $playlist->toParams());
		$this->client->addParam($kparams, "updateStats", $updateStats);
		$this->client->queueServiceActionCall("playlist", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPlaylist");
		return $resultObject;
	}

	/**
	 * Delete existing playlist
	 * 
	 * @param string $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("playlist", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Clone an existing playlist
	 * 
	 * @param string $id Id of the playlist to clone
	 * @param KalturaPlaylist $newPlaylist Parameters defined here will override the ones in the cloned playlist
	 * @return KalturaPlaylist
	 */
	function cloneAction($id, KalturaPlaylist $newPlaylist = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		if ($newPlaylist !== null)
			$this->client->addParam($kparams, "newPlaylist", $newPlaylist->toParams());
		$this->client->queueServiceActionCall("playlist", "clone", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPlaylist");
		return $resultObject;
	}

	/**
	 * List available playlists
	 * 
	 * @param KalturaPlaylistFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaPlaylistListResponse
	 */
	function listAction(KalturaPlaylistFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("playlist", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPlaylistListResponse");
		return $resultObject;
	}

	/**
	 * Retrieve playlist for playing purpose
	 * 
	 * @param string $id 
	 * @param string $detailed 
	 * @param KalturaContext $playlistContext 
	 * @param KalturaMediaEntryFilterForPlaylist $filter 
	 * @return array
	 */
	function execute($id, $detailed = "", KalturaContext $playlistContext = null, KalturaMediaEntryFilterForPlaylist $filter = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "detailed", $detailed);
		if ($playlistContext !== null)
			$this->client->addParam($kparams, "playlistContext", $playlistContext->toParams());
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("playlist", "execute", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Retrieve playlist for playing purpose, based on content
	 * 
	 * @param int $playlistType 
	 * @param string $playlistContent 
	 * @param string $detailed 
	 * @return array
	 */
	function executeFromContent($playlistType, $playlistContent, $detailed = "")
	{
		$kparams = array();
		$this->client->addParam($kparams, "playlistType", $playlistType);
		$this->client->addParam($kparams, "playlistContent", $playlistContent);
		$this->client->addParam($kparams, "detailed", $detailed);
		$this->client->queueServiceActionCall("playlist", "executeFromContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Revrieve playlist for playing purpose, based on media entry filters
	 * 
	 * @param array $filters 
	 * @param int $totalResults 
	 * @param string $detailed 
	 * @return array
	 */
	function executeFromFilters(array $filters, $totalResults, $detailed = "")
	{
		$kparams = array();
		foreach($filters as $index => $obj)
		{
			$this->client->addParam($kparams, "filters:$index", $obj->toParams());
		}
		$this->client->addParam($kparams, "totalResults", $totalResults);
		$this->client->addParam($kparams, "detailed", $detailed);
		$this->client->queueServiceActionCall("playlist", "executeFromFilters", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Retrieve playlist statistics
	 * 
	 * @param int $playlistType 
	 * @param string $playlistContent 
	 * @return KalturaPlaylist
	 */
	function getStatsFromContent($playlistType, $playlistContent)
	{
		$kparams = array();
		$this->client->addParam($kparams, "playlistType", $playlistType);
		$this->client->addParam($kparams, "playlistContent", $playlistContent);
		$this->client->queueServiceActionCall("playlist", "getStatsFromContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaPlaylist");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaReportService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Report getGraphs action allows to get a graph data for a specific report.
	 * 
	 * @param int $reportType 
	 * @param KalturaReportInputFilter $reportInputFilter 
	 * @param string $dimension 
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return array
	 */
	function getGraphs($reportType, KalturaReportInputFilter $reportInputFilter, $dimension = null, $objectIds = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "reportType", $reportType);
		$this->client->addParam($kparams, "reportInputFilter", $reportInputFilter->toParams());
		$this->client->addParam($kparams, "dimension", $dimension);
		$this->client->addParam($kparams, "objectIds", $objectIds);
		$this->client->queueServiceActionCall("report", "getGraphs", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Report getTotal action allows to get a graph data for a specific report.
	 * 
	 * @param int $reportType 
	 * @param KalturaReportInputFilter $reportInputFilter 
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return KalturaReportTotal
	 */
	function getTotal($reportType, KalturaReportInputFilter $reportInputFilter, $objectIds = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "reportType", $reportType);
		$this->client->addParam($kparams, "reportInputFilter", $reportInputFilter->toParams());
		$this->client->addParam($kparams, "objectIds", $objectIds);
		$this->client->queueServiceActionCall("report", "getTotal", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaReportTotal");
		return $resultObject;
	}

	/**
	 * Report getBaseTotal action allows to get a the total base for storage reports
	 * 
	 * @param int $reportType 
	 * @param KalturaReportInputFilter $reportInputFilter 
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return array
	 */
	function getBaseTotal($reportType, KalturaReportInputFilter $reportInputFilter, $objectIds = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "reportType", $reportType);
		$this->client->addParam($kparams, "reportInputFilter", $reportInputFilter->toParams());
		$this->client->addParam($kparams, "objectIds", $objectIds);
		$this->client->queueServiceActionCall("report", "getBaseTotal", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * Report getTable action allows to get a graph data for a specific report.
	 * 
	 * @param int $reportType 
	 * @param KalturaReportInputFilter $reportInputFilter 
	 * @param KalturaFilterPager $pager 
	 * @param string $order 
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return KalturaReportTable
	 */
	function getTable($reportType, KalturaReportInputFilter $reportInputFilter, KalturaFilterPager $pager, $order = null, $objectIds = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "reportType", $reportType);
		$this->client->addParam($kparams, "reportInputFilter", $reportInputFilter->toParams());
		$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->addParam($kparams, "order", $order);
		$this->client->addParam($kparams, "objectIds", $objectIds);
		$this->client->queueServiceActionCall("report", "getTable", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaReportTable");
		return $resultObject;
	}

	/**
	 * Will create a Csv file for the given report and return the URL to access it
	 * 
	 * @param string $reportTitle The title of the report to display at top of CSV
	 * @param string $reportText The text of the filter of the report
	 * @param string $headers The headers of the columns - a map between the enumerations on the server side and the their display text
	 * @param int $reportType 
	 * @param KalturaReportInputFilter $reportInputFilter 
	 * @param string $dimension 
	 * @param KalturaFilterPager $pager 
	 * @param string $order 
	 * @param string $objectIds - one ID or more (separated by ',') of specific objects to query
	 * @return string
	 */
	function getUrlForReportAsCsv($reportTitle, $reportText, $headers, $reportType, KalturaReportInputFilter $reportInputFilter, $dimension = null, KalturaFilterPager $pager = null, $order = null, $objectIds = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "reportTitle", $reportTitle);
		$this->client->addParam($kparams, "reportText", $reportText);
		$this->client->addParam($kparams, "headers", $headers);
		$this->client->addParam($kparams, "reportType", $reportType);
		$this->client->addParam($kparams, "reportInputFilter", $reportInputFilter->toParams());
		$this->client->addParam($kparams, "dimension", $dimension);
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->addParam($kparams, "order", $order);
		$this->client->addParam($kparams, "objectIds", $objectIds);
		$this->client->queueServiceActionCall("report", "getUrlForReportAsCsv", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param int $id 
	 * @param array $params 
	 * @return KalturaReportResponse
	 */
	function execute($id, array $params = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		if ($params !== null)
			foreach($params as $index => $obj)
			{
				$this->client->addParam($kparams, "params:$index", $obj->toParams());
			}
		$this->client->queueServiceActionCall("report", "execute", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaReportResponse");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param int $id 
	 * @param array $params 
	 * @return file
	 */
	function getCsv($id, array $params = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		if ($params !== null)
			foreach($params as $index => $obj)
			{
				$this->client->addParam($kparams, "params:$index", $obj->toParams());
			}
		$this->client->queueServiceActionCall("report", "getCsv", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Returns report CSV file executed by string params with the following convention: param1=value1;param2=value2
	 * 
	 * @param int $id 
	 * @param string $params 
	 * @return file
	 */
	function getCsvFromStringParams($id, $params = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "params", $params);
		$this->client->queueServiceActionCall("report", "getCsvFromStringParams", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSchemaService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Serves the requested XSD according to the type and name.
	 * 
	 * @param string $type 
	 * @return file
	 */
	function serve($type)
	{
		$kparams = array();
		$this->client->addParam($kparams, "type", $type);
		$this->client->queueServiceActionCall("schema", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSearchService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Search for media in one of the supported media providers
	 * 
	 * @param KalturaSearch $search A KalturaSearch object contains the search keywords, media provider and media type
	 * @param KalturaFilterPager $pager 
	 * @return KalturaSearchResultResponse
	 */
	function search(KalturaSearch $search, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "search", $search->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("search", "search", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSearchResultResponse");
		return $resultObject;
	}

	/**
	 * Retrieve extra information about media found in search action
	 Some providers return only part of the fields needed to create entry from, use this action to get the rest of the fields.
	 * 
	 * @param KalturaSearchResult $searchResult KalturaSearchResult object extends KalturaSearch and has all fields required for media:add
	 * @return KalturaSearchResult
	 */
	function getMediaInfo(KalturaSearchResult $searchResult)
	{
		$kparams = array();
		$this->client->addParam($kparams, "searchResult", $searchResult->toParams());
		$this->client->queueServiceActionCall("search", "getMediaInfo", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSearchResult");
		return $resultObject;
	}

	/**
	 * Search for media given a specific URL
	 Kaltura supports a searchURL action on some of the media providers.
	 This action will return a KalturaSearchResult object based on a given URL (assuming the media provider is supported)
	 * 
	 * @param int $mediaType 
	 * @param string $url 
	 * @return KalturaSearchResult
	 */
	function searchUrl($mediaType, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "mediaType", $mediaType);
		$this->client->addParam($kparams, "url", $url);
		$this->client->queueServiceActionCall("search", "searchUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSearchResult");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param int $searchSource 
	 * @param string $userName 
	 * @param string $password 
	 * @return KalturaSearchAuthData
	 */
	function externalLogin($searchSource, $userName, $password)
	{
		$kparams = array();
		$this->client->addParam($kparams, "searchSource", $searchSource);
		$this->client->addParam($kparams, "userName", $userName);
		$this->client->addParam($kparams, "password", $password);
		$this->client->queueServiceActionCall("search", "externalLogin", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSearchAuthData");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSessionService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Start a session with Kaltura's server.
	 The result KS is the session key that you should pass to all services that requires a ticket.
	 * 
	 * @param string $secret Remember to provide the correct secret according to the sessionType you want
	 * @param string $userId 
	 * @param int $type Regular session or Admin session
	 * @param int $partnerId 
	 * @param int $expiry KS expiry time in seconds
	 * @param string $privileges 
	 * @return string
	 */
	function start($secret, $userId = "", $type = 0, $partnerId = null, $expiry = 86400, $privileges = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "secret", $secret);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->addParam($kparams, "type", $type);
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "expiry", $expiry);
		$this->client->addParam($kparams, "privileges", $privileges);
		$this->client->queueServiceActionCall("session", "start", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * End a session with the Kaltura server, making the current KS invalid.
	 * 
	 * @return 
	 */
	function end()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("session", "end", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Start an impersonated session with Kaltura's server.
	 The result KS is the session key that you should pass to all services that requires a ticket.
	 * 
	 * @param string $secret - should be the secret (admin or user) of the original partnerId (not impersonatedPartnerId).
	 * @param int $impersonatedPartnerId 
	 * @param string $userId - impersonated userId
	 * @param int $type 
	 * @param int $partnerId 
	 * @param int $expiry KS expiry time in seconds
	 * @param string $privileges 
	 * @return string
	 */
	function impersonate($secret, $impersonatedPartnerId, $userId = "", $type = 0, $partnerId = null, $expiry = 86400, $privileges = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "secret", $secret);
		$this->client->addParam($kparams, "impersonatedPartnerId", $impersonatedPartnerId);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->addParam($kparams, "type", $type);
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "expiry", $expiry);
		$this->client->addParam($kparams, "privileges", $privileges);
		$this->client->queueServiceActionCall("session", "impersonate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Start an impersonated session with Kaltura's server.
	 The result KS info contains the session key that you should pass to all services that requires a ticket.
	 Type, expiry and privileges won't be changed if they're not set
	 * 
	 * @param string $session The old KS of the impersonated partner
	 * @param int $type Type of the new KS
	 * @param int $expiry Expiry time in seconds of the new KS
	 * @param string $privileges Privileges of the new KS
	 * @return KalturaSessionInfo
	 */
	function impersonateByKs($session, $type = null, $expiry = null, $privileges = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "session", $session);
		$this->client->addParam($kparams, "type", $type);
		$this->client->addParam($kparams, "expiry", $expiry);
		$this->client->addParam($kparams, "privileges", $privileges);
		$this->client->queueServiceActionCall("session", "impersonateByKs", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSessionInfo");
		return $resultObject;
	}

	/**
	 * Parse session key and return its info
	 * 
	 * @param string $session The KS to be parsed, keep it empty to use current session.
	 * @return KalturaSessionInfo
	 */
	function get($session = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "session", $session);
		$this->client->queueServiceActionCall("session", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSessionInfo");
		return $resultObject;
	}

	/**
	 * Start a session for Kaltura's flash widgets
	 * 
	 * @param string $widgetId 
	 * @param int $expiry 
	 * @return KalturaStartWidgetSessionResponse
	 */
	function startWidgetSession($widgetId, $expiry = 86400)
	{
		$kparams = array();
		$this->client->addParam($kparams, "widgetId", $widgetId);
		$this->client->addParam($kparams, "expiry", $expiry);
		$this->client->queueServiceActionCall("session", "startWidgetSession", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaStartWidgetSessionResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaStatsService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Will write to the event log a single line representing the event
	 client version - will help interprete the line structure. different client versions might have slightly different data/data formats in the line
event_id - number is the row number in yuval's excel
datetime - same format as MySql's datetime - can change and should reflect the time zone
session id - can be some big random number or guid
partner id
entry id
unique viewer
widget id
ui_conf id
uid - the puser id as set by the ppartner
current point - in milliseconds
duration - milliseconds
user ip
process duration - in milliseconds
control id
seek
new point
referrer
	
	
	 KalturaStatsEvent $event
	 * 
	 * @param KalturaStatsEvent $event 
	 * @return bool
	 */
	function collect(KalturaStatsEvent $event)
	{
		$kparams = array();
		$this->client->addParam($kparams, "event", $event->toParams());
		$this->client->queueServiceActionCall("stats", "collect", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$resultObject = (bool) $resultObject;
		return $resultObject;
	}

	/**
	 * Will collect the kmcEvent sent form the KMC client
	 // this will actually be an empty function because all events will be sent using GET and will anyway be logged in the apache log
	 * 
	 * @param KalturaStatsKmcEvent $kmcEvent 
	 * @return 
	 */
	function kmcCollect(KalturaStatsKmcEvent $kmcEvent)
	{
		$kparams = array();
		$this->client->addParam($kparams, "kmcEvent", $kmcEvent->toParams());
		$this->client->queueServiceActionCall("stats", "kmcCollect", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param KalturaCEError $kalturaCEError 
	 * @return KalturaCEError
	 */
	function reportKceError(KalturaCEError $kalturaCEError)
	{
		$kparams = array();
		$this->client->addParam($kparams, "kalturaCEError", $kalturaCEError->toParams());
		$this->client->queueServiceActionCall("stats", "reportKceError", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaCEError");
		return $resultObject;
	}

	/**
	 * Use this action to report errors to the kaltura server.
	 * 
	 * @param string $errorCode 
	 * @param string $errorMessage 
	 * @return 
	 */
	function reportError($errorCode, $errorMessage)
	{
		$kparams = array();
		$this->client->addParam($kparams, "errorCode", $errorCode);
		$this->client->addParam($kparams, "errorMessage", $errorMessage);
		$this->client->queueServiceActionCall("stats", "reportError", $kparams);
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
class KalturaStorageProfileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds a storage profile to the Kaltura DB.
	 * 
	 * @param KalturaStorageProfile $storageProfile 
	 * @return KalturaStorageProfile
	 */
	function add(KalturaStorageProfile $storageProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "storageProfile", $storageProfile->toParams());
		$this->client->queueServiceActionCall("storageprofile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaStorageProfile");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param int $storageId 
	 * @param int $status 
	 * @return 
	 */
	function updateStatus($storageId, $status)
	{
		$kparams = array();
		$this->client->addParam($kparams, "storageId", $storageId);
		$this->client->addParam($kparams, "status", $status);
		$this->client->queueServiceActionCall("storageprofile", "updateStatus", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Get storage profile by id
	 * 
	 * @param int $storageProfileId 
	 * @return KalturaStorageProfile
	 */
	function get($storageProfileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "storageProfileId", $storageProfileId);
		$this->client->queueServiceActionCall("storageprofile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaStorageProfile");
		return $resultObject;
	}

	/**
	 * Update storage profile by id
	 * 
	 * @param int $storageProfileId 
	 * @param KalturaStorageProfile $storageProfile Id
	 * @return KalturaStorageProfile
	 */
	function update($storageProfileId, KalturaStorageProfile $storageProfile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "storageProfileId", $storageProfileId);
		$this->client->addParam($kparams, "storageProfile", $storageProfile->toParams());
		$this->client->queueServiceActionCall("storageprofile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaStorageProfile");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param KalturaStorageProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaStorageProfileListResponse
	 */
	function listAction(KalturaStorageProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("storageprofile", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaStorageProfileListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSyndicationFeedService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new Syndication Feed
	 * 
	 * @param KalturaBaseSyndicationFeed $syndicationFeed 
	 * @return KalturaBaseSyndicationFeed
	 */
	function add(KalturaBaseSyndicationFeed $syndicationFeed)
	{
		$kparams = array();
		$this->client->addParam($kparams, "syndicationFeed", $syndicationFeed->toParams());
		$this->client->queueServiceActionCall("syndicationfeed", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseSyndicationFeed");
		return $resultObject;
	}

	/**
	 * Get Syndication Feed by ID
	 * 
	 * @param string $id 
	 * @return KalturaBaseSyndicationFeed
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("syndicationfeed", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseSyndicationFeed");
		return $resultObject;
	}

	/**
	 * Update Syndication Feed by ID
	 * 
	 * @param string $id 
	 * @param KalturaBaseSyndicationFeed $syndicationFeed 
	 * @return KalturaBaseSyndicationFeed
	 */
	function update($id, KalturaBaseSyndicationFeed $syndicationFeed)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "syndicationFeed", $syndicationFeed->toParams());
		$this->client->queueServiceActionCall("syndicationfeed", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseSyndicationFeed");
		return $resultObject;
	}

	/**
	 * Delete Syndication Feed by ID
	 * 
	 * @param string $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("syndicationfeed", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List Syndication Feeds by filter with paging support
	 * 
	 * @param KalturaBaseSyndicationFeedFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaBaseSyndicationFeedListResponse
	 */
	function listAction(KalturaBaseSyndicationFeedFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("syndicationfeed", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBaseSyndicationFeedListResponse");
		return $resultObject;
	}

	/**
	 * Get entry count for a syndication feed
	 * 
	 * @param string $feedId 
	 * @return KalturaSyndicationFeedEntryCount
	 */
	function getEntryCount($feedId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "feedId", $feedId);
		$this->client->queueServiceActionCall("syndicationfeed", "getEntryCount", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaSyndicationFeedEntryCount");
		return $resultObject;
	}

	/**
	 * Request conversion for all entries that doesnt have the required flavor param
	 returns a comma-separated ids of conversion jobs
	 * 
	 * @param string $feedId 
	 * @return string
	 */
	function requestConversion($feedId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "feedId", $feedId);
		$this->client->queueServiceActionCall("syndicationfeed", "requestConversion", $kparams);
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
class KalturaSystemService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * 
	 * 
	 * @return bool
	 */
	function ping()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("system", "ping", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$resultObject = (bool) $resultObject;
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @return bool
	 */
	function pingDatabase()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("system", "pingDatabase", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$resultObject = (bool) $resultObject;
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @return int
	 */
	function getTime()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("system", "getTime", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "integer");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbAssetService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add thumbnail asset
	 * 
	 * @param string $entryId 
	 * @param KalturaThumbAsset $thumbAsset 
	 * @return KalturaThumbAsset
	 */
	function add($entryId, KalturaThumbAsset $thumbAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "thumbAsset", $thumbAsset->toParams());
		$this->client->queueServiceActionCall("thumbasset", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAsset");
		return $resultObject;
	}

	/**
	 * Update content of thumbnail asset
	 * 
	 * @param string $id 
	 * @param KalturaContentResource $contentResource 
	 * @return KalturaThumbAsset
	 */
	function setContent($id, KalturaContentResource $contentResource)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "contentResource", $contentResource->toParams());
		$this->client->queueServiceActionCall("thumbasset", "setContent", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAsset");
		return $resultObject;
	}

	/**
	 * Update thumbnail asset
	 * 
	 * @param string $id 
	 * @param KalturaThumbAsset $thumbAsset 
	 * @return KalturaThumbAsset
	 */
	function update($id, KalturaThumbAsset $thumbAsset)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "thumbAsset", $thumbAsset->toParams());
		$this->client->queueServiceActionCall("thumbasset", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAsset");
		return $resultObject;
	}

	/**
	 * Serves thumbnail by entry id and thumnail params id
	 * 
	 * @param string $entryId 
	 * @param int $thumbParamId If not set, default thumbnail will be used.
	 * @return file
	 */
	function serveByEntryId($entryId, $thumbParamId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "thumbParamId", $thumbParamId);
		$this->client->queueServiceActionCall("thumbasset", "serveByEntryId", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Serves thumbnail by its id
	 * 
	 * @param string $thumbAssetId 
	 * @param int $version 
	 * @param KalturaThumbParams $thumbParams 
	 * @param KalturaThumbnailServeOptions $options 
	 * @return file
	 */
	function serve($thumbAssetId, $version = null, KalturaThumbParams $thumbParams = null, KalturaThumbnailServeOptions $options = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "thumbAssetId", $thumbAssetId);
		$this->client->addParam($kparams, "version", $version);
		if ($thumbParams !== null)
			$this->client->addParam($kparams, "thumbParams", $thumbParams->toParams());
		if ($options !== null)
			$this->client->addParam($kparams, "options", $options->toParams());
		$this->client->queueServiceActionCall("thumbasset", "serve", $kparams);
		if(!$this->client->getDestinationPath() && !$this->client->getReturnServedResult())
			return $this->client->getServeUrl();
		return $this->client->doQueue();
	}

	/**
	 * Tags the thumbnail as DEFAULT_THUMB and removes that tag from all other thumbnail assets of the entry.
	 Create a new file sync link on the entry thumbnail that points to the thumbnail asset file sync.
	 * 
	 * @param string $thumbAssetId 
	 * @return 
	 */
	function setAsDefault($thumbAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "thumbAssetId", $thumbAssetId);
		$this->client->queueServiceActionCall("thumbasset", "setAsDefault", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $entryId 
	 * @param int $destThumbParamsId Indicate the id of the ThumbParams to be generate this thumbnail by
	 * @return KalturaThumbAsset
	 */
	function generateByEntryId($entryId, $destThumbParamsId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "destThumbParamsId", $destThumbParamsId);
		$this->client->queueServiceActionCall("thumbasset", "generateByEntryId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAsset");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $entryId 
	 * @param KalturaThumbParams $thumbParams 
	 * @param string $sourceAssetId Id of the source asset (flavor or thumbnail) to be used as source for the thumbnail generation
	 * @return KalturaThumbAsset
	 */
	function generate($entryId, KalturaThumbParams $thumbParams, $sourceAssetId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "thumbParams", $thumbParams->toParams());
		$this->client->addParam($kparams, "sourceAssetId", $sourceAssetId);
		$this->client->queueServiceActionCall("thumbasset", "generate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAsset");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $thumbAssetId 
	 * @return KalturaThumbAsset
	 */
	function regenerate($thumbAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "thumbAssetId", $thumbAssetId);
		$this->client->queueServiceActionCall("thumbasset", "regenerate", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAsset");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $thumbAssetId 
	 * @return KalturaThumbAsset
	 */
	function get($thumbAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "thumbAssetId", $thumbAssetId);
		$this->client->queueServiceActionCall("thumbasset", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAsset");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $entryId 
	 * @return array
	 */
	function getByEntryId($entryId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->queueServiceActionCall("thumbasset", "getByEntryId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}

	/**
	 * List Thumbnail Assets by filter and pager
	 * 
	 * @param KalturaAssetFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaThumbAssetListResponse
	 */
	function listAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("thumbasset", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAssetListResponse");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $entryId 
	 * @param string $url 
	 * @return KalturaThumbAsset
	 */
	function addFromUrl($entryId, $url)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$this->client->addParam($kparams, "url", $url);
		$this->client->queueServiceActionCall("thumbasset", "addFromUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAsset");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $entryId 
	 * @param file $fileData 
	 * @return KalturaThumbAsset
	 */
	function addFromImage($entryId, $fileData)
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryId", $entryId);
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("thumbasset", "addFromImage", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbAsset");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $thumbAssetId 
	 * @return 
	 */
	function delete($thumbAssetId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "thumbAssetId", $thumbAssetId);
		$this->client->queueServiceActionCall("thumbasset", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Get download URL for the asset
	 * 
	 * @param string $id 
	 * @param int $storageId 
	 * @param KalturaThumbParams $thumbParams 
	 * @return string
	 */
	function getUrl($id, $storageId = null, KalturaThumbParams $thumbParams = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "storageId", $storageId);
		if ($thumbParams !== null)
			$this->client->addParam($kparams, "thumbParams", $thumbParams->toParams());
		$this->client->queueServiceActionCall("thumbasset", "getUrl", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Get remote storage existing paths for the asset
	 * 
	 * @param string $id 
	 * @return KalturaRemotePathListResponse
	 */
	function getRemotePaths($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("thumbasset", "getRemotePaths", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaRemotePathListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParamsOutputService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Get thumb params output object by ID
	 * 
	 * @param int $id 
	 * @return KalturaThumbParamsOutput
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("thumbparamsoutput", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbParamsOutput");
		return $resultObject;
	}

	/**
	 * List thumb params output objects by filter and pager
	 * 
	 * @param KalturaThumbParamsOutputFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaThumbParamsOutputListResponse
	 */
	function listAction(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("thumbparamsoutput", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbParamsOutputListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaThumbParamsService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new Thumb Params
	 * 
	 * @param KalturaThumbParams $thumbParams 
	 * @return KalturaThumbParams
	 */
	function add(KalturaThumbParams $thumbParams)
	{
		$kparams = array();
		$this->client->addParam($kparams, "thumbParams", $thumbParams->toParams());
		$this->client->queueServiceActionCall("thumbparams", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbParams");
		return $resultObject;
	}

	/**
	 * Get Thumb Params by ID
	 * 
	 * @param int $id 
	 * @return KalturaThumbParams
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("thumbparams", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbParams");
		return $resultObject;
	}

	/**
	 * Update Thumb Params by ID
	 * 
	 * @param int $id 
	 * @param KalturaThumbParams $thumbParams 
	 * @return KalturaThumbParams
	 */
	function update($id, KalturaThumbParams $thumbParams)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "thumbParams", $thumbParams->toParams());
		$this->client->queueServiceActionCall("thumbparams", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbParams");
		return $resultObject;
	}

	/**
	 * Delete Thumb Params by ID
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("thumbparams", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List Thumb Params by filter with paging support (By default - all system default params will be listed too)
	 * 
	 * @param KalturaThumbParamsFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaThumbParamsListResponse
	 */
	function listAction(KalturaThumbParamsFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("thumbparams", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaThumbParamsListResponse");
		return $resultObject;
	}

	/**
	 * Get Thumb Params by Conversion Profile ID
	 * 
	 * @param int $conversionProfileId 
	 * @return array
	 */
	function getByConversionProfileId($conversionProfileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "conversionProfileId", $conversionProfileId);
		$this->client->queueServiceActionCall("thumbparams", "getByConversionProfileId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUiConfService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * UIConf Add action allows you to add a UIConf to Kaltura DB
	 * 
	 * @param KalturaUiConf $uiConf Mandatory input parameter of type KalturaUiConf
	 * @return KalturaUiConf
	 */
	function add(KalturaUiConf $uiConf)
	{
		$kparams = array();
		$this->client->addParam($kparams, "uiConf", $uiConf->toParams());
		$this->client->queueServiceActionCall("uiconf", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	/**
	 * Update an existing UIConf
	 * 
	 * @param int $id 
	 * @param KalturaUiConf $uiConf 
	 * @return KalturaUiConf
	 */
	function update($id, KalturaUiConf $uiConf)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "uiConf", $uiConf->toParams());
		$this->client->queueServiceActionCall("uiconf", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	/**
	 * Retrieve a UIConf by id
	 * 
	 * @param int $id 
	 * @return KalturaUiConf
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("uiconf", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	/**
	 * Delete an existing UIConf
	 * 
	 * @param int $id 
	 * @return 
	 */
	function delete($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("uiconf", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Clone an existing UIConf
	 * 
	 * @param int $id 
	 * @return KalturaUiConf
	 */
	function cloneAction($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("uiconf", "clone", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConf");
		return $resultObject;
	}

	/**
	 * Retrieve a list of available template UIConfs
	 * 
	 * @param KalturaUiConfFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaUiConfListResponse
	 */
	function listTemplates(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("uiconf", "listTemplates", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConfListResponse");
		return $resultObject;
	}

	/**
	 * Retrieve a list of available UIConfs
	 * 
	 * @param KalturaUiConfFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaUiConfListResponse
	 */
	function listAction(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("uiconf", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUiConfListResponse");
		return $resultObject;
	}

	/**
	 * Retrieve a list of all available versions by object type
	 * 
	 * @return array
	 */
	function getAvailableTypes()
	{
		$kparams = array();
		$this->client->queueServiceActionCall("uiconf", "getAvailableTypes", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "array");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * 
	 * 
	 * @param file $fileData The file data
	 * @return string
	 */
	function upload($fileData)
	{
		$kparams = array();
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->queueServiceActionCall("upload", "upload", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param string $fileName 
	 * @return KalturaUploadResponse
	 */
	function getUploadedFileTokenByFileName($fileName)
	{
		$kparams = array();
		$this->client->addParam($kparams, "fileName", $fileName);
		$this->client->queueServiceActionCall("upload", "getUploadedFileTokenByFileName", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUploadResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUploadTokenService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds new upload token to upload a file
	 * 
	 * @param KalturaUploadToken $uploadToken 
	 * @return KalturaUploadToken
	 */
	function add(KalturaUploadToken $uploadToken = null)
	{
		$kparams = array();
		if ($uploadToken !== null)
			$this->client->addParam($kparams, "uploadToken", $uploadToken->toParams());
		$this->client->queueServiceActionCall("uploadtoken", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUploadToken");
		return $resultObject;
	}

	/**
	 * Get upload token by id
	 * 
	 * @param string $uploadTokenId 
	 * @return KalturaUploadToken
	 */
	function get($uploadTokenId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "uploadTokenId", $uploadTokenId);
		$this->client->queueServiceActionCall("uploadtoken", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUploadToken");
		return $resultObject;
	}

	/**
	 * Upload a file using the upload token id, returns an error on failure (an exception will be thrown when using one of the Kaltura clients)
	 * 
	 * @param string $uploadTokenId 
	 * @param file $fileData 
	 * @param bool $resume 
	 * @param bool $finalChunk 
	 * @param float $resumeAt 
	 * @return KalturaUploadToken
	 */
	function upload($uploadTokenId, $fileData, $resume = false, $finalChunk = true, $resumeAt = -1)
	{
		$kparams = array();
		$this->client->addParam($kparams, "uploadTokenId", $uploadTokenId);
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		$this->client->addParam($kparams, "resume", $resume);
		$this->client->addParam($kparams, "finalChunk", $finalChunk);
		$this->client->addParam($kparams, "resumeAt", $resumeAt);
		$this->client->queueServiceActionCall("uploadtoken", "upload", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUploadToken");
		return $resultObject;
	}

	/**
	 * Deletes the upload token by upload token id
	 * 
	 * @param string $uploadTokenId 
	 * @return 
	 */
	function delete($uploadTokenId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "uploadTokenId", $uploadTokenId);
		$this->client->queueServiceActionCall("uploadtoken", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * List upload token by filter with pager support. 
	 When using a user session the service will be restricted to users objects only.
	 * 
	 * @param KalturaUploadTokenFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaUploadTokenListResponse
	 */
	function listAction(KalturaUploadTokenFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("uploadtoken", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUploadTokenListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserRoleService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds a new user role object to the account.
	 * 
	 * @param KalturaUserRole $userRole A new role
	 * @return KalturaUserRole
	 */
	function add(KalturaUserRole $userRole)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userRole", $userRole->toParams());
		$this->client->queueServiceActionCall("userrole", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUserRole");
		return $resultObject;
	}

	/**
	 * Retrieves a user role object using its ID.
	 * 
	 * @param int $userRoleId The user role's unique identifier
	 * @return KalturaUserRole
	 */
	function get($userRoleId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userRoleId", $userRoleId);
		$this->client->queueServiceActionCall("userrole", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUserRole");
		return $resultObject;
	}

	/**
	 * Updates an existing user role object.
	 * 
	 * @param int $userRoleId The user role's unique identifier
	 * @param KalturaUserRole $userRole Id The user role's unique identifier
	 * @return KalturaUserRole
	 */
	function update($userRoleId, KalturaUserRole $userRole)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userRoleId", $userRoleId);
		$this->client->addParam($kparams, "userRole", $userRole->toParams());
		$this->client->queueServiceActionCall("userrole", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUserRole");
		return $resultObject;
	}

	/**
	 * Deletes an existing user role object.
	 * 
	 * @param int $userRoleId The user role's unique identifier
	 * @return KalturaUserRole
	 */
	function delete($userRoleId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userRoleId", $userRoleId);
		$this->client->queueServiceActionCall("userrole", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUserRole");
		return $resultObject;
	}

	/**
	 * Lists user role objects that are associated with an account.
	 Blocked user roles are listed unless you use a filter to exclude them.
	 Deleted user roles are not listed unless you use a filter to include them.
	 * 
	 * @param KalturaUserRoleFilter $filter A filter used to exclude specific types of user roles
	 * @param KalturaFilterPager $pager A limit for the number of records to display on a page
	 * @return KalturaUserRoleListResponse
	 */
	function listAction(KalturaUserRoleFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("userrole", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUserRoleListResponse");
		return $resultObject;
	}

	/**
	 * Creates a new user role object that is a duplicate of an existing role.
	 * 
	 * @param int $userRoleId The user role's unique identifier
	 * @return KalturaUserRole
	 */
	function cloneAction($userRoleId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userRoleId", $userRoleId);
		$this->client->queueServiceActionCall("userrole", "clone", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUserRole");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaUserService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Adds a new user to an existing account in the Kaltura database.
	 Input param $id is the unique identifier in the partner's system.
	 * 
	 * @param KalturaUser $user The new user
	 * @return KalturaUser
	 */
	function add(KalturaUser $user)
	{
		$kparams = array();
		$this->client->addParam($kparams, "user", $user->toParams());
		$this->client->queueServiceActionCall("user", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	/**
	 * Updates an existing user object.
	 You can also use this action to update the userId.
	 * 
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param KalturaUser $user Id The user's unique identifier in the partner's system
	 * @return KalturaUser
	 */
	function update($userId, KalturaUser $user)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->addParam($kparams, "user", $user->toParams());
		$this->client->queueServiceActionCall("user", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	/**
	 * Retrieves a user object for a specified user ID.
	 * 
	 * @param string $userId The user's unique identifier in the partner's system
	 * @return KalturaUser
	 */
	function get($userId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->queueServiceActionCall("user", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	/**
	 * Retrieves a user object for a user's login ID and partner ID.
	 A login ID is the email address used by a user to log into the system.
	 * 
	 * @param string $loginId The user's email address that identifies the user for login
	 * @return KalturaUser
	 */
	function getByLoginId($loginId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "loginId", $loginId);
		$this->client->queueServiceActionCall("user", "getByLoginId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	/**
	 * Deletes a user from a partner account.
	 * 
	 * @param string $userId The user's unique identifier in the partner's system
	 * @return KalturaUser
	 */
	function delete($userId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->queueServiceActionCall("user", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	/**
	 * Lists user objects that are associated with an account.
	 Blocked users are listed unless you use a filter to exclude them.
	 Deleted users are not listed unless you use a filter to include them.
	 * 
	 * @param KalturaUserFilter $filter A filter used to exclude specific types of users
	 * @param KalturaFilterPager $pager A limit for the number of records to display on a page
	 * @return KalturaUserListResponse
	 */
	function listAction(KalturaUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("user", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUserListResponse");
		return $resultObject;
	}

	/**
	 * Notifies that a user is banned from an account.
	 * 
	 * @param string $userId The user's unique identifier in the partner's system
	 * @return 
	 */
	function notifyBan($userId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->queueServiceActionCall("user", "notifyBan", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Logs a user into a partner account with a partner ID, a partner user ID (puser), and a user password.
	 * 
	 * @param int $partnerId The identifier of the partner account
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param string $password The user's password
	 * @param int $expiry The requested time (in seconds) before the generated KS expires (By default, a KS expires after 24 hours).
	 * @param string $privileges Special privileges
	 * @return string
	 */
	function login($partnerId, $userId, $password, $expiry = 86400, $privileges = "*")
	{
		$kparams = array();
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->addParam($kparams, "password", $password);
		$this->client->addParam($kparams, "expiry", $expiry);
		$this->client->addParam($kparams, "privileges", $privileges);
		$this->client->queueServiceActionCall("user", "login", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Logs a user into a partner account with a user login ID and a user password.
	 * 
	 * @param string $loginId The user's email address that identifies the user for login
	 * @param string $password The user's password
	 * @param int $partnerId The identifier of the partner account
	 * @param int $expiry The requested time (in seconds) before the generated KS expires (By default, a KS expires after 24 hours).
	 * @param string $privileges Special privileges
	 * @return string
	 */
	function loginByLoginId($loginId, $password, $partnerId = null, $expiry = 86400, $privileges = "*")
	{
		$kparams = array();
		$this->client->addParam($kparams, "loginId", $loginId);
		$this->client->addParam($kparams, "password", $password);
		$this->client->addParam($kparams, "partnerId", $partnerId);
		$this->client->addParam($kparams, "expiry", $expiry);
		$this->client->addParam($kparams, "privileges", $privileges);
		$this->client->queueServiceActionCall("user", "loginByLoginId", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * Updates a user's login data: email, password, name.
	 * 
	 * @param string $oldLoginId The user's current email address that identified the user for login
	 * @param string $password The user's current email address that identified the user for login
	 * @param string $newLoginId Optional, The user's email address that will identify the user for login
	 * @param string $newPassword Optional, The user's new password
	 * @param string $newFirstName Optional, The user's new first name
	 * @param string $newLastName Optional, The user's new last name
	 * @return 
	 */
	function updateLoginData($oldLoginId, $password, $newLoginId = "", $newPassword = "", $newFirstName = null, $newLastName = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "oldLoginId", $oldLoginId);
		$this->client->addParam($kparams, "password", $password);
		$this->client->addParam($kparams, "newLoginId", $newLoginId);
		$this->client->addParam($kparams, "newPassword", $newPassword);
		$this->client->addParam($kparams, "newFirstName", $newFirstName);
		$this->client->addParam($kparams, "newLastName", $newLastName);
		$this->client->queueServiceActionCall("user", "updateLoginData", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Reset user's password and send the user an email to generate a new one.
	 * 
	 * @param string $email The user's email address (login email)
	 * @return 
	 */
	function resetPassword($email)
	{
		$kparams = array();
		$this->client->addParam($kparams, "email", $email);
		$this->client->queueServiceActionCall("user", "resetPassword", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Set initial users password
	 * 
	 * @param string $hashKey The hash key used to identify the user (retrieved by email)
	 * @param string $newPassword The new password to set for the user
	 * @return 
	 */
	function setInitialPassword($hashKey, $newPassword)
	{
		$kparams = array();
		$this->client->addParam($kparams, "hashKey", $hashKey);
		$this->client->addParam($kparams, "newPassword", $newPassword);
		$this->client->queueServiceActionCall("user", "setInitialPassword", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "null");
		return $resultObject;
	}

	/**
	 * Enables a user to log into a partner account using an email address and a password
	 * 
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param string $loginId The user's email address that identifies the user for login
	 * @param string $password The user's password
	 * @return KalturaUser
	 */
	function enableLogin($userId, $loginId, $password = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->addParam($kparams, "loginId", $loginId);
		$this->client->addParam($kparams, "password", $password);
		$this->client->queueServiceActionCall("user", "enableLogin", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	/**
	 * Disables a user's ability to log into a partner account using an email address and a password.
	 You may use either a userId or a loginId parameter for this action.
	 * 
	 * @param string $userId The user's unique identifier in the partner's system
	 * @param string $loginId The user's email address that identifies the user for login
	 * @return KalturaUser
	 */
	function disableLogin($userId = null, $loginId = null)
	{
		$kparams = array();
		$this->client->addParam($kparams, "userId", $userId);
		$this->client->addParam($kparams, "loginId", $loginId);
		$this->client->queueServiceActionCall("user", "disableLogin", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaUser");
		return $resultObject;
	}

	/**
	 * Index an entry by id.
	 * 
	 * @param string $id 
	 * @param bool $shouldUpdate 
	 * @return string
	 */
	function index($id, $shouldUpdate = true)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "shouldUpdate", $shouldUpdate);
		$this->client->queueServiceActionCall("user", "index", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "string");
		return $resultObject;
	}

	/**
	 * 
	 * 
	 * @param file $fileData 
	 * @param KalturaBulkUploadJobData $bulkUploadData 
	 * @param KalturaBulkUploadUserData $bulkUploadUserData 
	 * @return KalturaBulkUpload
	 */
	function addFromBulkUpload($fileData, KalturaBulkUploadJobData $bulkUploadData = null, KalturaBulkUploadUserData $bulkUploadUserData = null)
	{
		$kparams = array();
		$kfiles = array();
		$this->client->addParam($kfiles, "fileData", $fileData);
		if ($bulkUploadData !== null)
			$this->client->addParam($kparams, "bulkUploadData", $bulkUploadData->toParams());
		if ($bulkUploadUserData !== null)
			$this->client->addParam($kparams, "bulkUploadUserData", $bulkUploadUserData->toParams());
		$this->client->queueServiceActionCall("user", "addFromBulkUpload", $kparams, $kfiles);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaBulkUpload");
		return $resultObject;
	}

	/**
	 * Action which checks whther user login
	 * 
	 * @param KalturaUserLoginDataFilter $filter 
	 * @return bool
	 */
	function checkLoginDataExists(KalturaUserLoginDataFilter $filter)
	{
		$kparams = array();
		$this->client->addParam($kparams, "filter", $filter->toParams());
		$this->client->queueServiceActionCall("user", "checkLoginDataExists", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$resultObject = (bool) $resultObject;
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaWidgetService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Add new widget, can be attached to entry or kshow
	 SourceWidget is ignored.
	 * 
	 * @param KalturaWidget $widget 
	 * @return KalturaWidget
	 */
	function add(KalturaWidget $widget)
	{
		$kparams = array();
		$this->client->addParam($kparams, "widget", $widget->toParams());
		$this->client->queueServiceActionCall("widget", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaWidget");
		return $resultObject;
	}

	/**
	 * Update exisiting widget
	 * 
	 * @param string $id 
	 * @param KalturaWidget $widget 
	 * @return KalturaWidget
	 */
	function update($id, KalturaWidget $widget)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->addParam($kparams, "widget", $widget->toParams());
		$this->client->queueServiceActionCall("widget", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaWidget");
		return $resultObject;
	}

	/**
	 * Get widget by id
	 * 
	 * @param string $id 
	 * @return KalturaWidget
	 */
	function get($id)
	{
		$kparams = array();
		$this->client->addParam($kparams, "id", $id);
		$this->client->queueServiceActionCall("widget", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaWidget");
		return $resultObject;
	}

	/**
	 * Add widget based on existing widget.
	 Must provide valid sourceWidgetId
	 * 
	 * @param KalturaWidget $widget 
	 * @return KalturaWidget
	 */
	function cloneAction(KalturaWidget $widget)
	{
		$kparams = array();
		$this->client->addParam($kparams, "widget", $widget->toParams());
		$this->client->queueServiceActionCall("widget", "clone", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaWidget");
		return $resultObject;
	}

	/**
	 * Retrieve a list of available widget depends on the filter given
	 * 
	 * @param KalturaWidgetFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaWidgetListResponse
	 */
	function listAction(KalturaWidgetFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("widget", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaWidgetListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaXInternalService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Creates new download job for multiple entry ids (comma separated), an email will be sent when the job is done
	 This sevice support the following entries: 
	 - MediaEntry
	 - Video will be converted using the flavor params id
	 - Audio will be downloaded as MP3
	 - Image will be downloaded as Jpeg
	 - MixEntry will be flattened using the flavor params id
	 - Other entry types are not supported
	 Returns the admin email that the email message will be sent to
	 * 
	 * @param string $entryIds Comma separated list of entry ids
	 * @param string $flavorParamsId 
	 * @return string
	 */
	function xAddBulkDownload($entryIds, $flavorParamsId = "")
	{
		$kparams = array();
		$this->client->addParam($kparams, "entryIds", $entryIds);
		$this->client->addParam($kparams, "flavorParamsId", $flavorParamsId);
		$this->client->queueServiceActionCall("xinternal", "xAddBulkDownload", $kparams);
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
class KalturaClient extends KalturaClientBase
{
	/**
	 * @var string
	 */
	protected $apiVersion = '3.1.6';

	/**
	 * Manage access control profiles
	 * @var KalturaAccessControlProfileService
	 */
	public $accessControlProfile = null;

	/**
	 * Add & Manage Access Controls
	 * @var KalturaAccessControlService
	 */
	public $accessControl = null;

	/**
	 * Manage details for the administrative user
	 * @var KalturaAdminUserService
	 */
	public $adminUser = null;

	/**
	 * Base Entry Service
	 * @var KalturaBaseEntryService
	 */
	public $baseEntry = null;

	/**
	 * Bulk upload service is used to upload & manage bulk uploads using CSV files.
	 *  This service manages only entry bulk uploads.
	 * @var KalturaBulkUploadService
	 */
	public $bulkUpload = null;

	/**
	 * Add & Manage CategoryEntry - assign entry to category
	 * @var KalturaCategoryEntryService
	 */
	public $categoryEntry = null;

	/**
	 * Add & Manage Categories
	 * @var KalturaCategoryService
	 */
	public $category = null;

	/**
	 * Add & Manage CategoryUser - membership of a user in a category
	 * @var KalturaCategoryUserService
	 */
	public $categoryUser = null;

	/**
	 * Manage the connection between Conversion Profiles and Asset Params
	 * @var KalturaConversionProfileAssetParamsService
	 */
	public $conversionProfileAssetParams = null;

	/**
	 * Add & Manage Conversion Profiles
	 * @var KalturaConversionProfileService
	 */
	public $conversionProfile = null;

	/**
	 * Data service lets you manage data content (textual content)
	 * @var KalturaDataService
	 */
	public $data = null;

	/**
	 * Document service
	 * @var KalturaDocumentService
	 */
	public $document = null;

	/**
	 * EmailIngestionProfile service lets you manage email ingestion profile records
	 * @var KalturaEmailIngestionProfileService
	 */
	public $EmailIngestionProfile = null;

	/**
	 * Manage file assets
	 * @var KalturaFileAssetService
	 */
	public $fileAsset = null;

	/**
	 * Retrieve information and invoke actions on Flavor Asset
	 * @var KalturaFlavorAssetService
	 */
	public $flavorAsset = null;

	/**
	 * Flavor Params Output service
	 * @var KalturaFlavorParamsOutputService
	 */
	public $flavorParamsOutput = null;

	/**
	 * Add & Manage Flavor Params
	 * @var KalturaFlavorParamsService
	 */
	public $flavorParams = null;

	/**
	 * Manage live channel segments
	 * @var KalturaLiveChannelSegmentService
	 */
	public $liveChannelSegment = null;

	/**
	 * Live Channel service lets you manage live channels
	 * @var KalturaLiveChannelService
	 */
	public $liveChannel = null;

	/**
	 * Live Stream service lets you manage live stream entries
	 * @var KalturaLiveStreamService
	 */
	public $liveStream = null;

	/**
	 * Media Info service
	 * @var KalturaMediaInfoService
	 */
	public $mediaInfo = null;

	/**
	 * Manage media servers
	 * @var KalturaMediaServerService
	 */
	public $mediaServer = null;

	/**
	 * Media service lets you upload and manage media files (images / videos & audio)
	 * @var KalturaMediaService
	 */
	public $media = null;

	/**
	 * A Mix is an XML unique format invented by Kaltura, it allows the user to create a mix of videos and images, in and out points, transitions, text overlays, soundtrack, effects and much more...
	 *  Mixing service lets you create a new mix, manage its metadata and make basic manipulations.
	 * @var KalturaMixingService
	 */
	public $mixing = null;

	/**
	 * Notification Service
	 * @var KalturaNotificationService
	 */
	public $notification = null;

	/**
	 * Partner service allows you to change/manage your partner personal details and settings as well
	 * @var KalturaPartnerService
	 */
	public $partner = null;

	/**
	 * PermissionItem service lets you create and manage permission items
	 * @var KalturaPermissionItemService
	 */
	public $permissionItem = null;

	/**
	 * Permission service lets you create and manage user permissions
	 * @var KalturaPermissionService
	 */
	public $permission = null;

	/**
	 * Playlist service lets you create,manage and play your playlists
	 *  Playlists could be static (containing a fixed list of entries) or dynamic (baseed on a filter)
	 * @var KalturaPlaylistService
	 */
	public $playlist = null;

	/**
	 * Api for getting reports data by the report type and some inputFilter
	 * @var KalturaReportService
	 */
	public $report = null;

	/**
	 * Expose the schema definitions for syndication MRSS, bulk upload XML and other schema types.
	 * @var KalturaSchemaService
	 */
	public $schema = null;

	/**
	 * Search service allows you to search for media in various media providers
	 *  This service is being used mostly by the CW component
	 * @var KalturaSearchService
	 */
	public $search = null;

	/**
	 * Session service
	 * @var KalturaSessionService
	 */
	public $session = null;

	/**
	 * Stats Service
	 * @var KalturaStatsService
	 */
	public $stats = null;

	/**
	 * Storage Profiles service
	 * @var KalturaStorageProfileService
	 */
	public $storageProfile = null;

	/**
	 * Add & Manage Syndication Feeds
	 * @var KalturaSyndicationFeedService
	 */
	public $syndicationFeed = null;

	/**
	 * System service is used for internal system helpers & to retrieve system level information
	 * @var KalturaSystemService
	 */
	public $system = null;

	/**
	 * Retrieve information and invoke actions on Thumb Asset
	 * @var KalturaThumbAssetService
	 */
	public $thumbAsset = null;

	/**
	 * Thumbnail Params Output service
	 * @var KalturaThumbParamsOutputService
	 */
	public $thumbParamsOutput = null;

	/**
	 * Add & Manage Thumb Params
	 * @var KalturaThumbParamsService
	 */
	public $thumbParams = null;

	/**
	 * UiConf service lets you create and manage your UIConfs for the various flash components
	 *  This service is used by the KMC-ApplicationStudio
	 * @var KalturaUiConfService
	 */
	public $uiConf = null;

	/**
	 * 
	 * @var KalturaUploadService
	 */
	public $upload = null;

	/**
	 * 
	 * @var KalturaUploadTokenService
	 */
	public $uploadToken = null;

	/**
	 * UserRole service lets you create and manage user roles
	 * @var KalturaUserRoleService
	 */
	public $userRole = null;

	/**
	 * Manage partner users on Kaltura's side
	 *  The userId in kaltura is the unique Id in the partner's system, and the [partnerId,Id] couple are unique key in kaltura's DB
	 * @var KalturaUserService
	 */
	public $user = null;

	/**
	 * Widget service for full widget management
	 * @var KalturaWidgetService
	 */
	public $widget = null;

	/**
	 * Internal Service is used for actions that are used internally in Kaltura applications and might be changed in the future without any notice.
	 * @var KalturaXInternalService
	 */
	public $xInternal = null;

	/**
	 * Kaltura client constructor
	 *
	 * @param KalturaConfiguration $config
	 */
	public function __construct(KalturaConfiguration $config)
	{
		parent::__construct($config);
		
		$this->accessControlProfile = new KalturaAccessControlProfileService($this);
		$this->accessControl = new KalturaAccessControlService($this);
		$this->adminUser = new KalturaAdminUserService($this);
		$this->baseEntry = new KalturaBaseEntryService($this);
		$this->bulkUpload = new KalturaBulkUploadService($this);
		$this->categoryEntry = new KalturaCategoryEntryService($this);
		$this->category = new KalturaCategoryService($this);
		$this->categoryUser = new KalturaCategoryUserService($this);
		$this->conversionProfileAssetParams = new KalturaConversionProfileAssetParamsService($this);
		$this->conversionProfile = new KalturaConversionProfileService($this);
		$this->data = new KalturaDataService($this);
		$this->document = new KalturaDocumentService($this);
		$this->EmailIngestionProfile = new KalturaEmailIngestionProfileService($this);
		$this->fileAsset = new KalturaFileAssetService($this);
		$this->flavorAsset = new KalturaFlavorAssetService($this);
		$this->flavorParamsOutput = new KalturaFlavorParamsOutputService($this);
		$this->flavorParams = new KalturaFlavorParamsService($this);
		$this->liveChannelSegment = new KalturaLiveChannelSegmentService($this);
		$this->liveChannel = new KalturaLiveChannelService($this);
		$this->liveStream = new KalturaLiveStreamService($this);
		$this->mediaInfo = new KalturaMediaInfoService($this);
		$this->mediaServer = new KalturaMediaServerService($this);
		$this->media = new KalturaMediaService($this);
		$this->mixing = new KalturaMixingService($this);
		$this->notification = new KalturaNotificationService($this);
		$this->partner = new KalturaPartnerService($this);
		$this->permissionItem = new KalturaPermissionItemService($this);
		$this->permission = new KalturaPermissionService($this);
		$this->playlist = new KalturaPlaylistService($this);
		$this->report = new KalturaReportService($this);
		$this->schema = new KalturaSchemaService($this);
		$this->search = new KalturaSearchService($this);
		$this->session = new KalturaSessionService($this);
		$this->stats = new KalturaStatsService($this);
		$this->storageProfile = new KalturaStorageProfileService($this);
		$this->syndicationFeed = new KalturaSyndicationFeedService($this);
		$this->system = new KalturaSystemService($this);
		$this->thumbAsset = new KalturaThumbAssetService($this);
		$this->thumbParamsOutput = new KalturaThumbParamsOutputService($this);
		$this->thumbParams = new KalturaThumbParamsService($this);
		$this->uiConf = new KalturaUiConfService($this);
		$this->upload = new KalturaUploadService($this);
		$this->uploadToken = new KalturaUploadTokenService($this);
		$this->userRole = new KalturaUserRoleService($this);
		$this->user = new KalturaUserService($this);
		$this->widget = new KalturaWidgetService($this);
		$this->xInternal = new KalturaXInternalService($this);
	}
	
}

