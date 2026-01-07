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

namespace Google\Service\DisplayVideo;

class Advertiser extends \Google\Model
{
  /**
   * Unknown.
   */
  public const CONTAINS_EU_POLITICAL_ADS_EU_POLITICAL_ADVERTISING_STATUS_UNKNOWN = 'EU_POLITICAL_ADVERTISING_STATUS_UNKNOWN';
  /**
   * Contains EU political advertising.
   */
  public const CONTAINS_EU_POLITICAL_ADS_CONTAINS_EU_POLITICAL_ADVERTISING = 'CONTAINS_EU_POLITICAL_ADVERTISING';
  /**
   * Does not contain EU political advertising.
   */
  public const CONTAINS_EU_POLITICAL_ADS_DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING = 'DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING';
  /**
   * Default value when status is not specified or is unknown in this version.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_UNSPECIFIED = 'ENTITY_STATUS_UNSPECIFIED';
  /**
   * The entity is enabled to bid and spend budget.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ACTIVE = 'ENTITY_STATUS_ACTIVE';
  /**
   * The entity is archived. Bidding and budget spending are disabled. An entity
   * can be deleted after archived. Deleted entities cannot be retrieved.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ARCHIVED = 'ENTITY_STATUS_ARCHIVED';
  /**
   * The entity is under draft. Bidding and budget spending are disabled.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_DRAFT = 'ENTITY_STATUS_DRAFT';
  /**
   * Bidding and budget spending are paused for the entity.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_PAUSED = 'ENTITY_STATUS_PAUSED';
  /**
   * The entity is scheduled for deletion.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_SCHEDULED_FOR_DELETION = 'ENTITY_STATUS_SCHEDULED_FOR_DELETION';
  protected $adServerConfigType = AdvertiserAdServerConfig::class;
  protected $adServerConfigDataType = '';
  /**
   * Output only. The unique ID of the advertiser. Assigned by the system.
   *
   * @var string
   */
  public $advertiserId;
  protected $billingConfigType = AdvertiserBillingConfig::class;
  protected $billingConfigDataType = '';
  /**
   * Optional. Whether this advertiser contains line items that serve European
   * Union political ads. If this field is set to
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING`, then the following will
   * happen: * Any new line items created under this advertiser will be assigned
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` if not otherwise specified. *
   * Any existing line items under this advertiser that do not have a set value
   * be updated to `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` within a day.
   *
   * @var string
   */
  public $containsEuPoliticalAds;
  protected $creativeConfigType = AdvertiserCreativeConfig::class;
  protected $creativeConfigDataType = '';
  protected $dataAccessConfigType = AdvertiserDataAccessConfig::class;
  protected $dataAccessConfigDataType = '';
  /**
   * Required. The display name of the advertiser. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. Controls whether or not insertion orders and line items of the
   * advertiser can spend their budgets and bid on inventory. * Accepted values
   * are `ENTITY_STATUS_ACTIVE`, `ENTITY_STATUS_PAUSED` and
   * `ENTITY_STATUS_SCHEDULED_FOR_DELETION`. * If set to
   * `ENTITY_STATUS_SCHEDULED_FOR_DELETION`, the advertiser will be deleted 30
   * days from when it was first scheduled for deletion.
   *
   * @var string
   */
  public $entityStatus;
  protected $generalConfigType = AdvertiserGeneralConfig::class;
  protected $generalConfigDataType = '';
  protected $integrationDetailsType = IntegrationDetails::class;
  protected $integrationDetailsDataType = '';
  /**
   * Output only. The resource name of the advertiser.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. The unique ID of the partner that the advertiser
   * belongs to.
   *
   * @var string
   */
  public $partnerId;
  /**
   * Whether integration with Mediaocean (Prisma) is enabled. By enabling this,
   * you agree to the following: On behalf of my company, I authorize Mediaocean
   * (Prisma) to send budget segment plans to Google, and I authorize Google to
   * send corresponding reporting and invoices from DV360 to Mediaocean for the
   * purposes of budget planning, billing, and reconciliation for this
   * advertiser.
   *
   * @var bool
   */
  public $prismaEnabled;
  protected $servingConfigType = AdvertiserTargetingConfig::class;
  protected $servingConfigDataType = '';
  /**
   * Output only. The timestamp when the advertiser was last updated. Assigned
   * by the system.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. Immutable. Ad server related settings of the advertiser.
   *
   * @param AdvertiserAdServerConfig $adServerConfig
   */
  public function setAdServerConfig(AdvertiserAdServerConfig $adServerConfig)
  {
    $this->adServerConfig = $adServerConfig;
  }
  /**
   * @return AdvertiserAdServerConfig
   */
  public function getAdServerConfig()
  {
    return $this->adServerConfig;
  }
  /**
   * Output only. The unique ID of the advertiser. Assigned by the system.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Required. Billing related settings of the advertiser.
   *
   * @param AdvertiserBillingConfig $billingConfig
   */
  public function setBillingConfig(AdvertiserBillingConfig $billingConfig)
  {
    $this->billingConfig = $billingConfig;
  }
  /**
   * @return AdvertiserBillingConfig
   */
  public function getBillingConfig()
  {
    return $this->billingConfig;
  }
  /**
   * Optional. Whether this advertiser contains line items that serve European
   * Union political ads. If this field is set to
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING`, then the following will
   * happen: * Any new line items created under this advertiser will be assigned
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` if not otherwise specified. *
   * Any existing line items under this advertiser that do not have a set value
   * be updated to `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` within a day.
   *
   * Accepted values: EU_POLITICAL_ADVERTISING_STATUS_UNKNOWN,
   * CONTAINS_EU_POLITICAL_ADVERTISING,
   * DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING
   *
   * @param self::CONTAINS_EU_POLITICAL_ADS_* $containsEuPoliticalAds
   */
  public function setContainsEuPoliticalAds($containsEuPoliticalAds)
  {
    $this->containsEuPoliticalAds = $containsEuPoliticalAds;
  }
  /**
   * @return self::CONTAINS_EU_POLITICAL_ADS_*
   */
  public function getContainsEuPoliticalAds()
  {
    return $this->containsEuPoliticalAds;
  }
  /**
   * Required. Creative related settings of the advertiser.
   *
   * @param AdvertiserCreativeConfig $creativeConfig
   */
  public function setCreativeConfig(AdvertiserCreativeConfig $creativeConfig)
  {
    $this->creativeConfig = $creativeConfig;
  }
  /**
   * @return AdvertiserCreativeConfig
   */
  public function getCreativeConfig()
  {
    return $this->creativeConfig;
  }
  /**
   * Settings that control how advertiser data may be accessed.
   *
   * @param AdvertiserDataAccessConfig $dataAccessConfig
   */
  public function setDataAccessConfig(AdvertiserDataAccessConfig $dataAccessConfig)
  {
    $this->dataAccessConfig = $dataAccessConfig;
  }
  /**
   * @return AdvertiserDataAccessConfig
   */
  public function getDataAccessConfig()
  {
    return $this->dataAccessConfig;
  }
  /**
   * Required. The display name of the advertiser. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. Controls whether or not insertion orders and line items of the
   * advertiser can spend their budgets and bid on inventory. * Accepted values
   * are `ENTITY_STATUS_ACTIVE`, `ENTITY_STATUS_PAUSED` and
   * `ENTITY_STATUS_SCHEDULED_FOR_DELETION`. * If set to
   * `ENTITY_STATUS_SCHEDULED_FOR_DELETION`, the advertiser will be deleted 30
   * days from when it was first scheduled for deletion.
   *
   * Accepted values: ENTITY_STATUS_UNSPECIFIED, ENTITY_STATUS_ACTIVE,
   * ENTITY_STATUS_ARCHIVED, ENTITY_STATUS_DRAFT, ENTITY_STATUS_PAUSED,
   * ENTITY_STATUS_SCHEDULED_FOR_DELETION
   *
   * @param self::ENTITY_STATUS_* $entityStatus
   */
  public function setEntityStatus($entityStatus)
  {
    $this->entityStatus = $entityStatus;
  }
  /**
   * @return self::ENTITY_STATUS_*
   */
  public function getEntityStatus()
  {
    return $this->entityStatus;
  }
  /**
   * Required. General settings of the advertiser.
   *
   * @param AdvertiserGeneralConfig $generalConfig
   */
  public function setGeneralConfig(AdvertiserGeneralConfig $generalConfig)
  {
    $this->generalConfig = $generalConfig;
  }
  /**
   * @return AdvertiserGeneralConfig
   */
  public function getGeneralConfig()
  {
    return $this->generalConfig;
  }
  /**
   * Integration details of the advertiser. Only integrationCode is currently
   * applicable to advertiser. Other fields of IntegrationDetails are not
   * supported and will be ignored if provided.
   *
   * @param IntegrationDetails $integrationDetails
   */
  public function setIntegrationDetails(IntegrationDetails $integrationDetails)
  {
    $this->integrationDetails = $integrationDetails;
  }
  /**
   * @return IntegrationDetails
   */
  public function getIntegrationDetails()
  {
    return $this->integrationDetails;
  }
  /**
   * Output only. The resource name of the advertiser.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Immutable. The unique ID of the partner that the advertiser
   * belongs to.
   *
   * @param string $partnerId
   */
  public function setPartnerId($partnerId)
  {
    $this->partnerId = $partnerId;
  }
  /**
   * @return string
   */
  public function getPartnerId()
  {
    return $this->partnerId;
  }
  /**
   * Whether integration with Mediaocean (Prisma) is enabled. By enabling this,
   * you agree to the following: On behalf of my company, I authorize Mediaocean
   * (Prisma) to send budget segment plans to Google, and I authorize Google to
   * send corresponding reporting and invoices from DV360 to Mediaocean for the
   * purposes of budget planning, billing, and reconciliation for this
   * advertiser.
   *
   * @param bool $prismaEnabled
   */
  public function setPrismaEnabled($prismaEnabled)
  {
    $this->prismaEnabled = $prismaEnabled;
  }
  /**
   * @return bool
   */
  public function getPrismaEnabled()
  {
    return $this->prismaEnabled;
  }
  /**
   * Targeting settings related to ad serving of the advertiser.
   *
   * @param AdvertiserTargetingConfig $servingConfig
   */
  public function setServingConfig(AdvertiserTargetingConfig $servingConfig)
  {
    $this->servingConfig = $servingConfig;
  }
  /**
   * @return AdvertiserTargetingConfig
   */
  public function getServingConfig()
  {
    return $this->servingConfig;
  }
  /**
   * Output only. The timestamp when the advertiser was last updated. Assigned
   * by the system.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Advertiser::class, 'Google_Service_DisplayVideo_Advertiser');
