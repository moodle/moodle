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

namespace Google\Service\Dfareporting;

class Advertiser extends \Google\Model
{
  /**
   * You'll need to confirm if your campaign contains EU political advertising.
   */
  public const EU_POLITICAL_ADS_DECLARATION_ADVERTISER_PLANS_TO_SERVE_EU_POLITICAL_ADS = 'ADVERTISER_PLANS_TO_SERVE_EU_POLITICAL_ADS';
  /**
   * All new campaigns will have “No” selected for the question that asks if
   * your campaign has EU political ads. You can change this for any campaign at
   * any time.
   */
  public const EU_POLITICAL_ADS_DECLARATION_ADVERTISER_DOES_NOT_PLAN_TO_SERVE_EU_POLITICAL_ADS = 'ADVERTISER_DOES_NOT_PLAN_TO_SERVE_EU_POLITICAL_ADS';
  /**
   * Approved (ads can deliver)
   */
  public const STATUS_APPROVED = 'APPROVED';
  /**
   * On-hold (all ads are stopped)
   */
  public const STATUS_ON_HOLD = 'ON_HOLD';
  /**
   * Account ID of this advertiser.This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * ID of the advertiser group this advertiser belongs to. You can group
   * advertisers for reporting purposes, allowing you to see aggregated
   * information for all advertisers in each group.
   *
   * @var string
   */
  public $advertiserGroupId;
  /**
   * Suffix added to click-through URL of ad creative associations under this
   * advertiser. Must be less than 129 characters long.
   *
   * @var string
   */
  public $clickThroughUrlSuffix;
  /**
   * ID of the click-through event tag to apply by default to the landing pages
   * of this advertiser's campaigns.
   *
   * @var string
   */
  public $defaultClickThroughEventTagId;
  /**
   * Default email address used in sender field for tag emails.
   *
   * @var string
   */
  public $defaultEmail;
  /**
   * Optional. Whether the advertiser plans to serve EU political ads.
   *
   * @var string
   */
  public $euPoliticalAdsDeclaration;
  /**
   * Floodlight configuration ID of this advertiser. The floodlight
   * configuration ID will be created automatically, so on insert this field
   * should be left blank. This field can be set to another advertiser's
   * floodlight configuration ID in order to share that advertiser's floodlight
   * configuration with this advertiser, so long as: - This advertiser's
   * original floodlight configuration is not already associated with floodlight
   * activities or floodlight activity groups. - This advertiser's original
   * floodlight configuration is not already shared with another advertiser.
   *
   * @var string
   */
  public $floodlightConfigurationId;
  protected $floodlightConfigurationIdDimensionValueType = DimensionValue::class;
  protected $floodlightConfigurationIdDimensionValueDataType = '';
  /**
   * ID of this advertiser. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#advertiser".
   *
   * @var string
   */
  public $kind;
  protected $measurementPartnerLinkType = MeasurementPartnerAdvertiserLink::class;
  protected $measurementPartnerLinkDataType = '';
  /**
   * Name of this advertiser. This is a required field and must be less than 256
   * characters long and unique among advertisers of the same account.
   *
   * @var string
   */
  public $name;
  /**
   * Original floodlight configuration before any sharing occurred. Set the
   * floodlightConfigurationId of this advertiser to
   * originalFloodlightConfigurationId to unshare the advertiser's current
   * floodlight configuration. You cannot unshare an advertiser's floodlight
   * configuration if the shared configuration has activities associated with
   * any campaign or placement.
   *
   * @var string
   */
  public $originalFloodlightConfigurationId;
  /**
   * Status of this advertiser.
   *
   * @var string
   */
  public $status;
  /**
   * Subaccount ID of this advertiser.This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $subaccountId;
  /**
   * Suspension status of this advertiser.
   *
   * @var bool
   */
  public $suspended;

  /**
   * Account ID of this advertiser.This is a read-only field that can be left
   * blank.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * ID of the advertiser group this advertiser belongs to. You can group
   * advertisers for reporting purposes, allowing you to see aggregated
   * information for all advertisers in each group.
   *
   * @param string $advertiserGroupId
   */
  public function setAdvertiserGroupId($advertiserGroupId)
  {
    $this->advertiserGroupId = $advertiserGroupId;
  }
  /**
   * @return string
   */
  public function getAdvertiserGroupId()
  {
    return $this->advertiserGroupId;
  }
  /**
   * Suffix added to click-through URL of ad creative associations under this
   * advertiser. Must be less than 129 characters long.
   *
   * @param string $clickThroughUrlSuffix
   */
  public function setClickThroughUrlSuffix($clickThroughUrlSuffix)
  {
    $this->clickThroughUrlSuffix = $clickThroughUrlSuffix;
  }
  /**
   * @return string
   */
  public function getClickThroughUrlSuffix()
  {
    return $this->clickThroughUrlSuffix;
  }
  /**
   * ID of the click-through event tag to apply by default to the landing pages
   * of this advertiser's campaigns.
   *
   * @param string $defaultClickThroughEventTagId
   */
  public function setDefaultClickThroughEventTagId($defaultClickThroughEventTagId)
  {
    $this->defaultClickThroughEventTagId = $defaultClickThroughEventTagId;
  }
  /**
   * @return string
   */
  public function getDefaultClickThroughEventTagId()
  {
    return $this->defaultClickThroughEventTagId;
  }
  /**
   * Default email address used in sender field for tag emails.
   *
   * @param string $defaultEmail
   */
  public function setDefaultEmail($defaultEmail)
  {
    $this->defaultEmail = $defaultEmail;
  }
  /**
   * @return string
   */
  public function getDefaultEmail()
  {
    return $this->defaultEmail;
  }
  /**
   * Optional. Whether the advertiser plans to serve EU political ads.
   *
   * Accepted values: ADVERTISER_PLANS_TO_SERVE_EU_POLITICAL_ADS,
   * ADVERTISER_DOES_NOT_PLAN_TO_SERVE_EU_POLITICAL_ADS
   *
   * @param self::EU_POLITICAL_ADS_DECLARATION_* $euPoliticalAdsDeclaration
   */
  public function setEuPoliticalAdsDeclaration($euPoliticalAdsDeclaration)
  {
    $this->euPoliticalAdsDeclaration = $euPoliticalAdsDeclaration;
  }
  /**
   * @return self::EU_POLITICAL_ADS_DECLARATION_*
   */
  public function getEuPoliticalAdsDeclaration()
  {
    return $this->euPoliticalAdsDeclaration;
  }
  /**
   * Floodlight configuration ID of this advertiser. The floodlight
   * configuration ID will be created automatically, so on insert this field
   * should be left blank. This field can be set to another advertiser's
   * floodlight configuration ID in order to share that advertiser's floodlight
   * configuration with this advertiser, so long as: - This advertiser's
   * original floodlight configuration is not already associated with floodlight
   * activities or floodlight activity groups. - This advertiser's original
   * floodlight configuration is not already shared with another advertiser.
   *
   * @param string $floodlightConfigurationId
   */
  public function setFloodlightConfigurationId($floodlightConfigurationId)
  {
    $this->floodlightConfigurationId = $floodlightConfigurationId;
  }
  /**
   * @return string
   */
  public function getFloodlightConfigurationId()
  {
    return $this->floodlightConfigurationId;
  }
  /**
   * Dimension value for the ID of the floodlight configuration. This is a read-
   * only, auto-generated field.
   *
   * @param DimensionValue $floodlightConfigurationIdDimensionValue
   */
  public function setFloodlightConfigurationIdDimensionValue(DimensionValue $floodlightConfigurationIdDimensionValue)
  {
    $this->floodlightConfigurationIdDimensionValue = $floodlightConfigurationIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getFloodlightConfigurationIdDimensionValue()
  {
    return $this->floodlightConfigurationIdDimensionValue;
  }
  /**
   * ID of this advertiser. This is a read-only, auto-generated field.
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
   * Dimension value for the ID of this advertiser. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $idDimensionValue
   */
  public function setIdDimensionValue(DimensionValue $idDimensionValue)
  {
    $this->idDimensionValue = $idDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getIdDimensionValue()
  {
    return $this->idDimensionValue;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#advertiser".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Measurement partner advertiser link for tag wrapping.
   *
   * @param MeasurementPartnerAdvertiserLink $measurementPartnerLink
   */
  public function setMeasurementPartnerLink(MeasurementPartnerAdvertiserLink $measurementPartnerLink)
  {
    $this->measurementPartnerLink = $measurementPartnerLink;
  }
  /**
   * @return MeasurementPartnerAdvertiserLink
   */
  public function getMeasurementPartnerLink()
  {
    return $this->measurementPartnerLink;
  }
  /**
   * Name of this advertiser. This is a required field and must be less than 256
   * characters long and unique among advertisers of the same account.
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
   * Original floodlight configuration before any sharing occurred. Set the
   * floodlightConfigurationId of this advertiser to
   * originalFloodlightConfigurationId to unshare the advertiser's current
   * floodlight configuration. You cannot unshare an advertiser's floodlight
   * configuration if the shared configuration has activities associated with
   * any campaign or placement.
   *
   * @param string $originalFloodlightConfigurationId
   */
  public function setOriginalFloodlightConfigurationId($originalFloodlightConfigurationId)
  {
    $this->originalFloodlightConfigurationId = $originalFloodlightConfigurationId;
  }
  /**
   * @return string
   */
  public function getOriginalFloodlightConfigurationId()
  {
    return $this->originalFloodlightConfigurationId;
  }
  /**
   * Status of this advertiser.
   *
   * Accepted values: APPROVED, ON_HOLD
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
   * Subaccount ID of this advertiser.This is a read-only field that can be left
   * blank.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
  /**
   * Suspension status of this advertiser.
   *
   * @param bool $suspended
   */
  public function setSuspended($suspended)
  {
    $this->suspended = $suspended;
  }
  /**
   * @return bool
   */
  public function getSuspended()
  {
    return $this->suspended;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Advertiser::class, 'Google_Service_Dfareporting_Advertiser');
