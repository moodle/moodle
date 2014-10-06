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
require_once(dirname(__FILE__) . "/KalturaEventNotificationClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationTemplatePriority
{
	const HIGH = 1;
	const NORMAL = 3;
	const LOW = 5;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationFormat
{
	const HTML = "1";
	const TEXT = "2";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationRecipientProviderType
{
	const STATIC_LIST = "1";
	const CATEGORY = "2";
	const USER = "3";
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationTemplateOrderBy
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
class KalturaEmailNotificationRecipient extends KalturaObjectBase
{
	/**
	 * Recipient e-mail address
	 * 	 
	 *
	 * @var KalturaStringValue
	 */
	public $email;

	/**
	 * Recipient name
	 * 	 
	 *
	 * @var KalturaStringValue
	 */
	public $name;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaEmailNotificationRecipientJobData extends KalturaObjectBase
{
	/**
	 * Provider type of the job data.
	 * 	  
	 *
	 * @var KalturaEmailNotificationRecipientProviderType
	 * @readonly
	 */
	public $providerType = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaEmailNotificationRecipientProvider extends KalturaObjectBase
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaCategoryUserProviderFilter extends KalturaFilter
{
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
	 * @var KalturaCategoryUserStatus
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

	/**
	 * 
	 *
	 * @var KalturaUpdateMethodType
	 */
	public $updateMethodEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $updateMethodIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $permissionNamesMatchAnd = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $permissionNamesMatchOr = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationCategoryRecipientJobData extends KalturaEmailNotificationRecipientJobData
{
	/**
	 * 
	 *
	 * @var KalturaCategoryUserFilter
	 */
	public $categoryUserFilter;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationCategoryRecipientProvider extends KalturaEmailNotificationRecipientProvider
{
	/**
	 * The ID of the category whose subscribers should receive the email notification.
	 * 	 
	 *
	 * @var KalturaStringValue
	 */
	public $categoryId;

	/**
	 * 
	 *
	 * @var KalturaCategoryUserProviderFilter
	 */
	public $categoryUserFilter;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationParameter extends KalturaEventNotificationParameter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationStaticRecipientJobData extends KalturaEmailNotificationRecipientJobData
{
	/**
	 * Email to emails and names
	 * 	 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $emailRecipients;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationStaticRecipientProvider extends KalturaEmailNotificationRecipientProvider
{
	/**
	 * Email to emails and names
	 * 	 
	 *
	 * @var array of KalturaEmailNotificationRecipient
	 */
	public $emailRecipients;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationTemplate extends KalturaEventNotificationTemplate
{
	/**
	 * Define the email body format
	 * 	 
	 *
	 * @var KalturaEmailNotificationFormat
	 */
	public $format = null;

	/**
	 * Define the email subject 
	 * 	 
	 *
	 * @var string
	 */
	public $subject = null;

	/**
	 * Define the email body content
	 * 	 
	 *
	 * @var string
	 */
	public $body = null;

	/**
	 * Define the email sender email
	 * 	 
	 *
	 * @var string
	 */
	public $fromEmail = null;

	/**
	 * Define the email sender name
	 * 	 
	 *
	 * @var string
	 */
	public $fromName = null;

	/**
	 * Email recipient emails and names
	 * 	 
	 *
	 * @var KalturaEmailNotificationRecipientProvider
	 */
	public $to;

	/**
	 * Email recipient emails and names
	 * 	 
	 *
	 * @var KalturaEmailNotificationRecipientProvider
	 */
	public $cc;

	/**
	 * Email recipient emails and names
	 * 	 
	 *
	 * @var KalturaEmailNotificationRecipientProvider
	 */
	public $bcc;

	/**
	 * Default email addresses to whom the reply should be sent. 
	 * 	 
	 *
	 * @var KalturaEmailNotificationRecipientProvider
	 */
	public $replyTo;

	/**
	 * Define the email priority
	 * 	 
	 *
	 * @var KalturaEmailNotificationTemplatePriority
	 */
	public $priority = null;

	/**
	 * Email address that a reading confirmation will be sent
	 * 	 
	 *
	 * @var string
	 */
	public $confirmReadingTo = null;

	/**
	 * Hostname to use in Message-Id and Received headers and as default HELLO string. 
	 * 	 If empty, the value returned by SERVER_NAME is used or 'localhost.localdomain'.
	 * 	 
	 *
	 * @var string
	 */
	public $hostname = null;

	/**
	 * Sets the message ID to be used in the Message-Id header.
	 * 	 If empty, a unique id will be generated.
	 * 	 
	 *
	 * @var string
	 */
	public $messageID = null;

	/**
	 * Adds a e-mail custom header
	 * 	 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $customHeaders;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationUserRecipientJobData extends KalturaEmailNotificationRecipientJobData
{
	/**
	 * 
	 *
	 * @var KalturaUserFilter
	 */
	public $filter;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationUserRecipientProvider extends KalturaEmailNotificationRecipientProvider
{
	/**
	 * 
	 *
	 * @var KalturaUserFilter
	 */
	public $filter;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationDispatchJobData extends KalturaEventNotificationDispatchJobData
{
	/**
	 * Define the email sender email
	 * 	 
	 *
	 * @var string
	 */
	public $fromEmail = null;

	/**
	 * Define the email sender name
	 * 	 
	 *
	 * @var string
	 */
	public $fromName = null;

	/**
	 * Email recipient emails and names, key is mail address and value is the name
	 * 	 
	 *
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	public $to;

	/**
	 * Email cc emails and names, key is mail address and value is the name
	 * 	 
	 *
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	public $cc;

	/**
	 * Email bcc emails and names, key is mail address and value is the name
	 * 	 
	 *
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	public $bcc;

	/**
	 * Email addresses that a replies should be sent to, key is mail address and value is the name
	 * 	 
	 *
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	public $replyTo;

	/**
	 * Define the email priority
	 * 	 
	 *
	 * @var KalturaEmailNotificationTemplatePriority
	 */
	public $priority = null;

	/**
	 * Email address that a reading confirmation will be sent to
	 * 	 
	 *
	 * @var string
	 */
	public $confirmReadingTo = null;

	/**
	 * Hostname to use in Message-Id and Received headers and as default HELO string. 
	 * 	 If empty, the value returned by SERVER_NAME is used or 'localhost.localdomain'.
	 * 	 
	 *
	 * @var string
	 */
	public $hostname = null;

	/**
	 * Sets the message ID to be used in the Message-Id header.
	 * 	 If empty, a unique id will be generated.
	 * 	 
	 *
	 * @var string
	 */
	public $messageID = null;

	/**
	 * Adds a e-mail custom header
	 * 	 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $customHeaders;

	/**
	 * Define the content dynamic parameters
	 * 	 
	 *
	 * @var array of KalturaKeyValue
	 */
	public $contentParameters;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaEmailNotificationTemplateBaseFilter extends KalturaEventNotificationTemplateFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationTemplateFilter extends KalturaEmailNotificationTemplateBaseFilter
{

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaEmailNotificationClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaEmailNotificationClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaEmailNotificationClientPlugin($client);
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
		return 'emailNotification';
	}
}

