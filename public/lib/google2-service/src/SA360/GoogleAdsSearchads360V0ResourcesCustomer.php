<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesCustomer extends \Google\Model
{
  /**
   * Not specified.
   */
  public const ACCOUNT_LEVEL_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const ACCOUNT_LEVEL_UNKNOWN = 'UNKNOWN';
  /**
   * Client account (Facebook)
   */
  public const ACCOUNT_LEVEL_CLIENT_ACCOUNT_FACEBOOK = 'CLIENT_ACCOUNT_FACEBOOK';
  /**
   * Client account (Google Ads)
   */
  public const ACCOUNT_LEVEL_CLIENT_ACCOUNT_GOOGLE_ADS = 'CLIENT_ACCOUNT_GOOGLE_ADS';
  /**
   * Client account (Microsoft)
   */
  public const ACCOUNT_LEVEL_CLIENT_ACCOUNT_MICROSOFT = 'CLIENT_ACCOUNT_MICROSOFT';
  /**
   * Client account (Yahoo Japan)
   */
  public const ACCOUNT_LEVEL_CLIENT_ACCOUNT_YAHOO_JAPAN = 'CLIENT_ACCOUNT_YAHOO_JAPAN';
  /**
   * Client account (Engine Track)
   */
  public const ACCOUNT_LEVEL_CLIENT_ACCOUNT_ENGINE_TRACK = 'CLIENT_ACCOUNT_ENGINE_TRACK';
  /**
   * Top-level manager.
   */
  public const ACCOUNT_LEVEL_MANAGER = 'MANAGER';
  /**
   * Sub manager.
   */
  public const ACCOUNT_LEVEL_SUB_MANAGER = 'SUB_MANAGER';
  /**
   * Associate manager.
   */
  public const ACCOUNT_LEVEL_ASSOCIATE_MANAGER = 'ASSOCIATE_MANAGER';
  /**
   * Default value.
   */
  public const ACCOUNT_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Unknown value.
   */
  public const ACCOUNT_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Account is able to serve ads.
   */
  public const ACCOUNT_STATUS_ENABLED = 'ENABLED';
  /**
   * Account is deactivated by the user.
   */
  public const ACCOUNT_STATUS_PAUSED = 'PAUSED';
  /**
   * Account is deactivated by an internal process.
   */
  public const ACCOUNT_STATUS_SUSPENDED = 'SUSPENDED';
  /**
   * Account is irrevocably deactivated.
   */
  public const ACCOUNT_STATUS_REMOVED = 'REMOVED';
  /**
   * Account is still in the process of setup, not ENABLED yet.
   */
  public const ACCOUNT_STATUS_DRAFT = 'DRAFT';
  /**
   * Not specified.
   */
  public const ACCOUNT_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const ACCOUNT_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Baidu account.
   */
  public const ACCOUNT_TYPE_BAIDU = 'BAIDU';
  /**
   * Engine track account.
   */
  public const ACCOUNT_TYPE_ENGINE_TRACK = 'ENGINE_TRACK';
  /**
   * Facebook account.
   */
  public const ACCOUNT_TYPE_FACEBOOK = 'FACEBOOK';
  /**
   * Facebook account managed through gateway.
   */
  public const ACCOUNT_TYPE_FACEBOOK_GATEWAY = 'FACEBOOK_GATEWAY';
  /**
   * Google Ads account.
   */
  public const ACCOUNT_TYPE_GOOGLE_ADS = 'GOOGLE_ADS';
  /**
   * Microsoft Advertising account.
   */
  public const ACCOUNT_TYPE_MICROSOFT = 'MICROSOFT';
  /**
   * Search Ads 360 manager account.
   */
  public const ACCOUNT_TYPE_SEARCH_ADS_360 = 'SEARCH_ADS_360';
  /**
   * Yahoo Japan account.
   */
  public const ACCOUNT_TYPE_YAHOO_JAPAN = 'YAHOO_JAPAN';
  /**
   * Not specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Indicates an active account able to serve ads.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * Indicates a canceled account unable to serve ads. Can be reactivated by an
   * admin user.
   */
  public const STATUS_CANCELED = 'CANCELED';
  /**
   * Indicates a suspended account unable to serve ads. May only be activated by
   * Google support.
   */
  public const STATUS_SUSPENDED = 'SUSPENDED';
  /**
   * Indicates a closed account unable to serve ads. Test account will also have
   * CLOSED status. Status is permanent and may not be reopened.
   */
  public const STATUS_CLOSED = 'CLOSED';
  /**
   * Output only. The account level of the customer: Manager, Sub-manager,
   * Associate manager, Service account.
   *
   * @var string
   */
  public $accountLevel;
  /**
   * Output only. Account status, for example, Enabled, Paused, Removed, etc.
   *
   * @var string
   */
  public $accountStatus;
  /**
   * Output only. Engine account type, for example, Google Ads, Microsoft
   * Advertising, Yahoo Japan, Baidu, Facebook, Engine Track, etc.
   *
   * @var string
   */
  public $accountType;
  /**
   * Output only. The descriptive name of the associate manager.
   *
   * @var string
   */
  public $associateManagerDescriptiveName;
  /**
   * Output only. The customer ID of the associate manager. A 0 value indicates
   * that the customer has no SA360 associate manager.
   *
   * @var string
   */
  public $associateManagerId;
  /**
   * Whether auto-tagging is enabled for the customer.
   *
   * @var bool
   */
  public $autoTaggingEnabled;
  protected $conversionTrackingSettingType = GoogleAdsSearchads360V0ResourcesConversionTrackingSetting::class;
  protected $conversionTrackingSettingDataType = '';
  /**
   * Output only. The timestamp when this customer was created. The timestamp is
   * in the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Immutable. The currency in which the account operates. A subset of the
   * currency codes from the ISO 4217 standard is supported.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Optional, non-unique descriptive name of the customer.
   *
   * @var string
   */
  public $descriptiveName;
  protected $doubleClickCampaignManagerSettingType = GoogleAdsSearchads360V0ResourcesDoubleClickCampaignManagerSetting::class;
  protected $doubleClickCampaignManagerSettingDataType = '';
  /**
   * Output only. ID of the account in the external engine account.
   *
   * @var string
   */
  public $engineId;
  /**
   * The URL template for appending params to the final URL.
   *
   * @var string
   */
  public $finalUrlSuffix;
  /**
   * Output only. The ID of the customer.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The datetime when this customer was last modified. The
   * datetime is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss.ssssss"
   * format.
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * Output only. Whether the customer is a manager.
   *
   * @var bool
   */
  public $manager;
  /**
   * Output only. The descriptive name of the manager.
   *
   * @var string
   */
  public $managerDescriptiveName;
  /**
   * Output only. The customer ID of the manager. A 0 value indicates that the
   * customer has no SA360 manager.
   *
   * @var string
   */
  public $managerId;
  /**
   * Immutable. The resource name of the customer. Customer resource names have
   * the form: `customers/{customer_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The status of the customer.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. The descriptive name of the sub manager.
   *
   * @var string
   */
  public $subManagerDescriptiveName;
  /**
   * Output only. The customer ID of the sub manager. A 0 value indicates that
   * the customer has no sub SA360 manager.
   *
   * @var string
   */
  public $subManagerId;
  /**
   * Immutable. The local timezone ID of the customer.
   *
   * @var string
   */
  public $timeZone;
  /**
   * The URL template for constructing a tracking URL out of parameters.
   *
   * @var string
   */
  public $trackingUrlTemplate;

  /**
   * Output only. The account level of the customer: Manager, Sub-manager,
   * Associate manager, Service account.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, CLIENT_ACCOUNT_FACEBOOK,
   * CLIENT_ACCOUNT_GOOGLE_ADS, CLIENT_ACCOUNT_MICROSOFT,
   * CLIENT_ACCOUNT_YAHOO_JAPAN, CLIENT_ACCOUNT_ENGINE_TRACK, MANAGER,
   * SUB_MANAGER, ASSOCIATE_MANAGER
   *
   * @param self::ACCOUNT_LEVEL_* $accountLevel
   */
  public function setAccountLevel($accountLevel)
  {
    $this->accountLevel = $accountLevel;
  }
  /**
   * @return self::ACCOUNT_LEVEL_*
   */
  public function getAccountLevel()
  {
    return $this->accountLevel;
  }
  /**
   * Output only. Account status, for example, Enabled, Paused, Removed, etc.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, PAUSED, SUSPENDED, REMOVED,
   * DRAFT
   *
   * @param self::ACCOUNT_STATUS_* $accountStatus
   */
  public function setAccountStatus($accountStatus)
  {
    $this->accountStatus = $accountStatus;
  }
  /**
   * @return self::ACCOUNT_STATUS_*
   */
  public function getAccountStatus()
  {
    return $this->accountStatus;
  }
  /**
   * Output only. Engine account type, for example, Google Ads, Microsoft
   * Advertising, Yahoo Japan, Baidu, Facebook, Engine Track, etc.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, BAIDU, ENGINE_TRACK, FACEBOOK,
   * FACEBOOK_GATEWAY, GOOGLE_ADS, MICROSOFT, SEARCH_ADS_360, YAHOO_JAPAN
   *
   * @param self::ACCOUNT_TYPE_* $accountType
   */
  public function setAccountType($accountType)
  {
    $this->accountType = $accountType;
  }
  /**
   * @return self::ACCOUNT_TYPE_*
   */
  public function getAccountType()
  {
    return $this->accountType;
  }
  /**
   * Output only. The descriptive name of the associate manager.
   *
   * @param string $associateManagerDescriptiveName
   */
  public function setAssociateManagerDescriptiveName($associateManagerDescriptiveName)
  {
    $this->associateManagerDescriptiveName = $associateManagerDescriptiveName;
  }
  /**
   * @return string
   */
  public function getAssociateManagerDescriptiveName()
  {
    return $this->associateManagerDescriptiveName;
  }
  /**
   * Output only. The customer ID of the associate manager. A 0 value indicates
   * that the customer has no SA360 associate manager.
   *
   * @param string $associateManagerId
   */
  public function setAssociateManagerId($associateManagerId)
  {
    $this->associateManagerId = $associateManagerId;
  }
  /**
   * @return string
   */
  public function getAssociateManagerId()
  {
    return $this->associateManagerId;
  }
  /**
   * Whether auto-tagging is enabled for the customer.
   *
   * @param bool $autoTaggingEnabled
   */
  public function setAutoTaggingEnabled($autoTaggingEnabled)
  {
    $this->autoTaggingEnabled = $autoTaggingEnabled;
  }
  /**
   * @return bool
   */
  public function getAutoTaggingEnabled()
  {
    return $this->autoTaggingEnabled;
  }
  /**
   * Output only. Conversion tracking setting for a customer.
   *
   * @param GoogleAdsSearchads360V0ResourcesConversionTrackingSetting $conversionTrackingSetting
   */
  public function setConversionTrackingSetting(GoogleAdsSearchads360V0ResourcesConversionTrackingSetting $conversionTrackingSetting)
  {
    $this->conversionTrackingSetting = $conversionTrackingSetting;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesConversionTrackingSetting
   */
  public function getConversionTrackingSetting()
  {
    return $this->conversionTrackingSetting;
  }
  /**
   * Output only. The timestamp when this customer was created. The timestamp is
   * in the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Immutable. The currency in which the account operates. A subset of the
   * currency codes from the ISO 4217 standard is supported.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Optional, non-unique descriptive name of the customer.
   *
   * @param string $descriptiveName
   */
  public function setDescriptiveName($descriptiveName)
  {
    $this->descriptiveName = $descriptiveName;
  }
  /**
   * @return string
   */
  public function getDescriptiveName()
  {
    return $this->descriptiveName;
  }
  /**
   * Output only. DoubleClick Campaign Manager (DCM) setting for a manager
   * customer.
   *
   * @param GoogleAdsSearchads360V0ResourcesDoubleClickCampaignManagerSetting $doubleClickCampaignManagerSetting
   */
  public function setDoubleClickCampaignManagerSetting(GoogleAdsSearchads360V0ResourcesDoubleClickCampaignManagerSetting $doubleClickCampaignManagerSetting)
  {
    $this->doubleClickCampaignManagerSetting = $doubleClickCampaignManagerSetting;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesDoubleClickCampaignManagerSetting
   */
  public function getDoubleClickCampaignManagerSetting()
  {
    return $this->doubleClickCampaignManagerSetting;
  }
  /**
   * Output only. ID of the account in the external engine account.
   *
   * @param string $engineId
   */
  public function setEngineId($engineId)
  {
    $this->engineId = $engineId;
  }
  /**
   * @return string
   */
  public function getEngineId()
  {
    return $this->engineId;
  }
  /**
   * The URL template for appending params to the final URL.
   *
   * @param string $finalUrlSuffix
   */
  public function setFinalUrlSuffix($finalUrlSuffix)
  {
    $this->finalUrlSuffix = $finalUrlSuffix;
  }
  /**
   * @return string
   */
  public function getFinalUrlSuffix()
  {
    return $this->finalUrlSuffix;
  }
  /**
   * Output only. The ID of the customer.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. The datetime when this customer was last modified. The
   * datetime is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss.ssssss"
   * format.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * Output only. Whether the customer is a manager.
   *
   * @param bool $manager
   */
  public function setManager($manager)
  {
    $this->manager = $manager;
  }
  /**
   * @return bool
   */
  public function getManager()
  {
    return $this->manager;
  }
  /**
   * Output only. The descriptive name of the manager.
   *
   * @param string $managerDescriptiveName
   */
  public function setManagerDescriptiveName($managerDescriptiveName)
  {
    $this->managerDescriptiveName = $managerDescriptiveName;
  }
  /**
   * @return string
   */
  public function getManagerDescriptiveName()
  {
    return $this->managerDescriptiveName;
  }
  /**
   * Output only. The customer ID of the manager. A 0 value indicates that the
   * customer has no SA360 manager.
   *
   * @param string $managerId
   */
  public function setManagerId($managerId)
  {
    $this->managerId = $managerId;
  }
  /**
   * @return string
   */
  public function getManagerId()
  {
    return $this->managerId;
  }
  /**
   * Immutable. The resource name of the customer. Customer resource names have
   * the form: `customers/{customer_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. The status of the customer.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, CANCELED, SUSPENDED, CLOSED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. The descriptive name of the sub manager.
   *
   * @param string $subManagerDescriptiveName
   */
  public function setSubManagerDescriptiveName($subManagerDescriptiveName)
  {
    $this->subManagerDescriptiveName = $subManagerDescriptiveName;
  }
  /**
   * @return string
   */
  public function getSubManagerDescriptiveName()
  {
    return $this->subManagerDescriptiveName;
  }
  /**
   * Output only. The customer ID of the sub manager. A 0 value indicates that
   * the customer has no sub SA360 manager.
   *
   * @param string $subManagerId
   */
  public function setSubManagerId($subManagerId)
  {
    $this->subManagerId = $subManagerId;
  }
  /**
   * @return string
   */
  public function getSubManagerId()
  {
    return $this->subManagerId;
  }
  /**
   * Immutable. The local timezone ID of the customer.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * The URL template for constructing a tracking URL out of parameters.
   *
   * @param string $trackingUrlTemplate
   */
  public function setTrackingUrlTemplate($trackingUrlTemplate)
  {
    $this->trackingUrlTemplate = $trackingUrlTemplate;
  }
  /**
   * @return string
   */
  public function getTrackingUrlTemplate()
  {
    return $this->trackingUrlTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesCustomer::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCustomer');
