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
class KalturaDropFolderContentFileHandlerMatchPolicy
{
	const ADD_AS_NEW = 1;
	const MATCH_EXISTING_OR_ADD_AS_NEW = 2;
	const MATCH_EXISTING_OR_KEEP_IN_FOLDER = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFileDeletePolicy
{
	const MANUAL_DELETE = 1;
	const AUTO_DELETE = 2;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFileStatus
{
	const UPLOADING = 1;
	const PENDING = 2;
	const WAITING = 3;
	const HANDLED = 4;
	const IGNORE = 5;
	const DELETED = 6;
	const PURGED = 7;
	const NO_MATCH = 8;
	const ERROR_HANDLING = 9;
	const ERROR_DELETING = 10;
	const DOWNLOADING = 11;
	const ERROR_DOWNLOADING = 12;
	const PROCESSING = 13;
	const PARSED = 14;
	const DETECTED = 15;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderStatus
{
	const DISABLED = 0;
	const ENABLED = 1;
	const DELETED = 2;
	const ERROR = 3;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderErrorCode
{
	const ERROR_CONNECT = "1";
	const ERROR_AUTENTICATE = "2";
	const ERROR_GET_PHISICAL_FILE_LIST = "3";
	const ERROR_GET_DB_FILE_LIST = "4";
	const DROP_FOLDER_APP_ERROR = "5";
	const CONTENT_MATCH_POLICY_UNDEFINED = "6";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFileErrorCode
{
	const ERROR_ADDING_BULK_UPLOAD = "dropFolderXmlBulkUpload.ERROR_ADDING_BULK_UPLOAD";
	const ERROR_ADD_CONTENT_RESOURCE = "dropFolderXmlBulkUpload.ERROR_ADD_CONTENT_RESOURCE";
	const ERROR_IN_BULK_UPLOAD = "dropFolderXmlBulkUpload.ERROR_IN_BULK_UPLOAD";
	const ERROR_WRITING_TEMP_FILE = "dropFolderXmlBulkUpload.ERROR_WRITING_TEMP_FILE";
	const LOCAL_FILE_WRONG_CHECKSUM = "dropFolderXmlBulkUpload.LOCAL_FILE_WRONG_CHECKSUM";
	const LOCAL_FILE_WRONG_SIZE = "dropFolderXmlBulkUpload.LOCAL_FILE_WRONG_SIZE";
	const MALFORMED_XML_FILE = "dropFolderXmlBulkUpload.MALFORMED_XML_FILE";
	const XML_FILE_SIZE_EXCEED_LIMIT = "dropFolderXmlBulkUpload.XML_FILE_SIZE_EXCEED_LIMIT";
	const ERROR_UPDATE_ENTRY = "1";
	const ERROR_ADD_ENTRY = "2";
	const FLAVOR_NOT_FOUND = "3";
	const FLAVOR_MISSING_IN_FILE_NAME = "4";
	const SLUG_REGEX_NO_MATCH = "5";
	const ERROR_READING_FILE = "6";
	const ERROR_DOWNLOADING_FILE = "7";
	const ERROR_UPDATE_FILE = "8";
	const ERROR_ADDING_CONTENT_PROCESSOR = "10";
	const ERROR_IN_CONTENT_PROCESSOR = "11";
	const ERROR_DELETING_FILE = "12";
	const FILE_NO_MATCH = "13";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFileHandlerType
{
	const XML = "dropFolderXmlBulkUpload.XML";
	const CONTENT = "1";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFileOrderBy
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
class KalturaDropFolderOrderBy
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
class KalturaDropFolderType
{
	const WEBEX = "WebexDropFolder.WEBEX";
	const LOCAL = "1";
	const FTP = "2";
	const SCP = "3";
	const SFTP = "4";
	const S3 = "6";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFtpDropFolderOrderBy
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
class KalturaRemoteDropFolderOrderBy
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
class KalturaScpDropFolderOrderBy
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
class KalturaSftpDropFolderOrderBy
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
class KalturaSshDropFolderOrderBy
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
abstract class KalturaDropFolderFileHandlerConfig extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var KalturaDropFolderFileHandlerType
	 * @readonly
	 */
	public $handlerType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolder extends KalturaObjectBase
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
	 * @insertonly
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
	public $description = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $conversionProfileId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dc = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $path = null;

	/**
	 * The ammount of time, in seconds, that should pass so that a file with no change in size we'll be treated as "finished uploading to folder"
	 * 	 
	 *
	 * @var int
	 */
	public $fileSizeCheckInterval = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileDeletePolicy
	 */
	public $fileDeletePolicy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $autoFileDeleteDays = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileHandlerType
	 */
	public $fileHandlerType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNamePatterns = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileHandlerConfig
	 */
	public $fileHandlerConfig;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderErrorCode
	 */
	public $errorCode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorDescription = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ignoreFileNamePatterns = null;

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
	 * @var int
	 */
	public $lastAccessedAt = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $incremental = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $lastFileTimestamp = null;

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
class KalturaDropFolderFile extends KalturaObjectBase
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
	 * @insertonly
	 */
	public $dropFolderId = null;

	/**
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $fileName = null;

	/**
	 * 
	 *
	 * @var float
	 */
	public $fileSize = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $fileSizeLastSetAt = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileStatus
	 * @readonly
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderType
	 * @readonly
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedSlug = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedFlavor = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $leadDropFolderFileId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $deletedDropFolderFileId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryId = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileErrorCode
	 */
	public $errorCode = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorDescription = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $lastModificationTime = null;

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
	 * @var int
	 */
	public $uploadStartDetectedAt = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $uploadEndDetectedAt = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $importStartedAt = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $importEndedAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $batchJobId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFileListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaDropFolderFile
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
class KalturaDropFolderListResponse extends KalturaObjectBase
{
	/**
	 * 
	 *
	 * @var array of KalturaDropFolder
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
abstract class KalturaDropFolderBaseFilter extends KalturaFilter
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
	public $nameLike = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderType
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
	 * @var KalturaDropFolderStatus
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
	public $conversionProfileIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $conversionProfileIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dcEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dcIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $pathEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $pathLike = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileHandlerType
	 */
	public $fileHandlerTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileHandlerTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNamePatternsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNamePatternsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNamePatternsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tagsMultiLikeAnd = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderErrorCode
	 */
	public $errorCodeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorCodeIn = null;

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
class KalturaDropFolderContentFileHandlerConfig extends KalturaDropFolderFileHandlerConfig
{
	/**
	 * 
	 *
	 * @var KalturaDropFolderContentFileHandlerMatchPolicy
	 */
	public $contentMatchPolicy = null;

	/**
	 * Regular expression that defines valid file names to be handled.
	 * 	 The following might be extracted from the file name and used if defined:
	 * 	 - (?P<referenceId>\w+) - will be used as the drop folder file's parsed slug.
	 * 	 - (?P<flavorName>\w+)  - will be used as the drop folder file's parsed flavor.
	 * 	 
	 *
	 * @var string
	 */
	public $slugRegex = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderContentProcessorJobData extends KalturaJobData
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $dropFolderFileIds = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedSlug = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderContentFileHandlerMatchPolicy
	 */
	public $contentMatchPolicy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $conversionProfileId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaDropFolderFileBaseFilter extends KalturaFilter
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
	 * @var int
	 */
	public $dropFolderIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dropFolderIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNameLike = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileStatus
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
	 * @var string
	 */
	public $statusNotIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedSlugEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedSlugIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedSlugLike = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedFlavorEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedFlavorIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parsedFlavorLike = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $leadDropFolderFileIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $deletedDropFolderFileIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var KalturaDropFolderFileErrorCode
	 */
	public $errorCodeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $errorCodeIn = null;

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
abstract class KalturaRemoteDropFolder extends KalturaDropFolder
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFileFilter extends KalturaDropFolderFileBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFilter extends KalturaDropFolderBaseFilter
{
	/**
	 * 
	 *
	 * @var KalturaNullableBoolean
	 */
	public $currentDc = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFtpDropFolder extends KalturaRemoteDropFolder
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $port = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $username = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $password = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaSshDropFolder extends KalturaRemoteDropFolder
{
	/**
	 * 
	 *
	 * @var string
	 */
	public $host = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $port = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $username = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $password = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $privateKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $publicKey = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $passPhrase = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFileResource extends KalturaDataCenterContentResource
{
	/**
	 * Id of the drop folder file object
	 * 	 
	 *
	 * @var int
	 */
	public $dropFolderFileId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderImportJobData extends KalturaSshImportJobData
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $dropFolderFileId = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaRemoteDropFolderBaseFilter extends KalturaDropFolderFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaScpDropFolder extends KalturaSshDropFolder
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSftpDropFolder extends KalturaSshDropFolder
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaRemoteDropFolderFilter extends KalturaRemoteDropFolderBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaFtpDropFolderBaseFilter extends KalturaRemoteDropFolderFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaSshDropFolderBaseFilter extends KalturaRemoteDropFolderFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaFtpDropFolderFilter extends KalturaFtpDropFolderBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSshDropFolderFilter extends KalturaSshDropFolderBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaScpDropFolderBaseFilter extends KalturaSshDropFolderFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaSftpDropFolderBaseFilter extends KalturaSshDropFolderFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaScpDropFolderFilter extends KalturaScpDropFolderBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaSftpDropFolderFilter extends KalturaSftpDropFolderBaseFilter
{

}


/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Allows you to add a new KalturaDropFolder object
	 * 
	 * @param KalturaDropFolder $dropFolder 
	 * @return KalturaDropFolder
	 */
	function add(KalturaDropFolder $dropFolder)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolder", $dropFolder->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolder");
		return $resultObject;
	}

	/**
	 * Retrieve a KalturaDropFolder object by ID
	 * 
	 * @param int $dropFolderId 
	 * @return KalturaDropFolder
	 */
	function get($dropFolderId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderId", $dropFolderId);
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolder");
		return $resultObject;
	}

	/**
	 * Update an existing KalturaDropFolder object
	 * 
	 * @param int $dropFolderId 
	 * @param KalturaDropFolder $dropFolder Id
	 * @return KalturaDropFolder
	 */
	function update($dropFolderId, KalturaDropFolder $dropFolder)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderId", $dropFolderId);
		$this->client->addParam($kparams, "dropFolder", $dropFolder->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolder");
		return $resultObject;
	}

	/**
	 * Mark the KalturaDropFolder object as deleted
	 * 
	 * @param int $dropFolderId 
	 * @return KalturaDropFolder
	 */
	function delete($dropFolderId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderId", $dropFolderId);
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolder");
		return $resultObject;
	}

	/**
	 * List KalturaDropFolder objects
	 * 
	 * @param KalturaDropFolderFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaDropFolderListResponse
	 */
	function listAction(KalturaDropFolderFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolder", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderListResponse");
		return $resultObject;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderFileService extends KalturaServiceBase
{
	function __construct(KalturaClient $client = null)
	{
		parent::__construct($client);
	}

	/**
	 * Allows you to add a new KalturaDropFolderFile object
	 * 
	 * @param KalturaDropFolderFile $dropFolderFile 
	 * @return KalturaDropFolderFile
	 */
	function add(KalturaDropFolderFile $dropFolderFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFile", $dropFolderFile->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "add", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	/**
	 * Retrieve a KalturaDropFolderFile object by ID
	 * 
	 * @param int $dropFolderFileId 
	 * @return KalturaDropFolderFile
	 */
	function get($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "get", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	/**
	 * Update an existing KalturaDropFolderFile object
	 * 
	 * @param int $dropFolderFileId 
	 * @param KalturaDropFolderFile $dropFolderFile Id
	 * @return KalturaDropFolderFile
	 */
	function update($dropFolderFileId, KalturaDropFolderFile $dropFolderFile)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->addParam($kparams, "dropFolderFile", $dropFolderFile->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "update", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	/**
	 * Update status of KalturaDropFolderFile
	 * 
	 * @param int $dropFolderFileId 
	 * @param int $status 
	 * @return KalturaDropFolderFile
	 */
	function updateStatus($dropFolderFileId, $status)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->addParam($kparams, "status", $status);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "updateStatus", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	/**
	 * Mark the KalturaDropFolderFile object as deleted
	 * 
	 * @param int $dropFolderFileId 
	 * @return KalturaDropFolderFile
	 */
	function delete($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "delete", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}

	/**
	 * List KalturaDropFolderFile objects
	 * 
	 * @param KalturaDropFolderFileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @return KalturaDropFolderFileListResponse
	 */
	function listAction(KalturaDropFolderFileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$kparams = array();
		if ($filter !== null)
			$this->client->addParam($kparams, "filter", $filter->toParams());
		if ($pager !== null)
			$this->client->addParam($kparams, "pager", $pager->toParams());
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "list", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFileListResponse");
		return $resultObject;
	}

	/**
	 * Set the KalturaDropFolderFile status to ignore (KalturaDropFolderFileStatus::IGNORE)
	 * 
	 * @param int $dropFolderFileId 
	 * @return KalturaDropFolderFile
	 */
	function ignore($dropFolderFileId)
	{
		$kparams = array();
		$this->client->addParam($kparams, "dropFolderFileId", $dropFolderFileId);
		$this->client->queueServiceActionCall("dropfolder_dropfolderfile", "ignore", $kparams);
		if ($this->client->isMultiRequest())
			return $this->client->getMultiRequestResult();
		$resultObject = $this->client->doQueue();
		$this->client->throwExceptionIfError($resultObject);
		$this->client->validateObjectType($resultObject, "KalturaDropFolderFile");
		return $resultObject;
	}
}
/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaDropFolderClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaDropFolderService
	 */
	public $dropFolder = null;

	/**
	 * @var KalturaDropFolderFileService
	 */
	public $dropFolderFile = null;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
		$this->dropFolder = new KalturaDropFolderService($client);
		$this->dropFolderFile = new KalturaDropFolderFileService($client);
	}

	/**
	 * @return KalturaDropFolderClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaDropFolderClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'dropFolder' => $this->dropFolder,
			'dropFolderFile' => $this->dropFolderFile,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'dropFolder';
	}
}

