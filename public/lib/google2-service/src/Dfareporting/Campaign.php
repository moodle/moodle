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

class Campaign extends \Google\Collection
{
  /**
   * The campaign contains EU political ads.
   */
  public const EU_POLITICAL_ADS_DECLARATION_CONTAINS_EU_POLITICAL_ADS = 'CONTAINS_EU_POLITICAL_ADS';
  /**
   * The campaign does not contain EU political ads.
   */
  public const EU_POLITICAL_ADS_DECLARATION_DOES_NOT_CONTAIN_EU_POLITICAL_ADS = 'DOES_NOT_CONTAIN_EU_POLITICAL_ADS';
  protected $collection_key = 'eventTagOverrides';
  /**
   * Account ID of this campaign. This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $accountId;
  protected $adBlockingConfigurationType = AdBlockingConfiguration::class;
  protected $adBlockingConfigurationDataType = '';
  protected $additionalCreativeOptimizationConfigurationsType = CreativeOptimizationConfiguration::class;
  protected $additionalCreativeOptimizationConfigurationsDataType = 'array';
  /**
   * Advertiser group ID of the associated advertiser.
   *
   * @var string
   */
  public $advertiserGroupId;
  /**
   * Advertiser ID of this campaign. This is a required field.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Whether this campaign has been archived.
   *
   * @var bool
   */
  public $archived;
  protected $audienceSegmentGroupsType = AudienceSegmentGroup::class;
  protected $audienceSegmentGroupsDataType = 'array';
  /**
   * Billing invoice code included in the Campaign Manager client billing
   * invoices associated with the campaign.
   *
   * @var string
   */
  public $billingInvoiceCode;
  protected $clickThroughUrlSuffixPropertiesType = ClickThroughUrlSuffixProperties::class;
  protected $clickThroughUrlSuffixPropertiesDataType = '';
  /**
   * Arbitrary comments about this campaign. Must be less than 256 characters
   * long.
   *
   * @var string
   */
  public $comment;
  protected $createInfoType = LastModifiedInfo::class;
  protected $createInfoDataType = '';
  /**
   * List of creative group IDs that are assigned to the campaign.
   *
   * @var string[]
   */
  public $creativeGroupIds;
  protected $creativeOptimizationConfigurationType = CreativeOptimizationConfiguration::class;
  protected $creativeOptimizationConfigurationDataType = '';
  protected $defaultClickThroughEventTagPropertiesType = DefaultClickThroughEventTagProperties::class;
  protected $defaultClickThroughEventTagPropertiesDataType = '';
  /**
   * The default landing page ID for this campaign.
   *
   * @var string
   */
  public $defaultLandingPageId;
  /**
   * @var string
   */
  public $endDate;
  /**
   * Optional. Whether the campaign has EU political ads. Campaign Manager 360
   * doesn't allow campaigns with EU political ads to serve in the EU. They can
   * still serve in other regions.
   *
   * @var string
   */
  public $euPoliticalAdsDeclaration;
  protected $eventTagOverridesType = EventTagOverride::class;
  protected $eventTagOverridesDataType = 'array';
  /**
   * External ID for this campaign.
   *
   * @var string
   */
  public $externalId;
  /**
   * ID of this campaign. This is a read-only auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#campaign".
   *
   * @var string
   */
  public $kind;
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  protected $measurementPartnerLinkType = MeasurementPartnerCampaignLink::class;
  protected $measurementPartnerLinkDataType = '';
  /**
   * Name of this campaign. This is a required field and must be less than 512
   * characters long and unique among campaigns of the same advertiser.
   *
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $startDate;
  /**
   * Subaccount ID of this campaign. This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $subaccountId;

  /**
   * Account ID of this campaign. This is a read-only field that can be left
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
   * Ad blocking settings for this campaign.
   *
   * @param AdBlockingConfiguration $adBlockingConfiguration
   */
  public function setAdBlockingConfiguration(AdBlockingConfiguration $adBlockingConfiguration)
  {
    $this->adBlockingConfiguration = $adBlockingConfiguration;
  }
  /**
   * @return AdBlockingConfiguration
   */
  public function getAdBlockingConfiguration()
  {
    return $this->adBlockingConfiguration;
  }
  /**
   * Additional creative optimization configurations for the campaign.
   *
   * @param CreativeOptimizationConfiguration[] $additionalCreativeOptimizationConfigurations
   */
  public function setAdditionalCreativeOptimizationConfigurations($additionalCreativeOptimizationConfigurations)
  {
    $this->additionalCreativeOptimizationConfigurations = $additionalCreativeOptimizationConfigurations;
  }
  /**
   * @return CreativeOptimizationConfiguration[]
   */
  public function getAdditionalCreativeOptimizationConfigurations()
  {
    return $this->additionalCreativeOptimizationConfigurations;
  }
  /**
   * Advertiser group ID of the associated advertiser.
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
   * Advertiser ID of this campaign. This is a required field.
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
   * Dimension value for the advertiser ID of this campaign. This is a read-
   * only, auto-generated field.
   *
   * @param DimensionValue $advertiserIdDimensionValue
   */
  public function setAdvertiserIdDimensionValue(DimensionValue $advertiserIdDimensionValue)
  {
    $this->advertiserIdDimensionValue = $advertiserIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getAdvertiserIdDimensionValue()
  {
    return $this->advertiserIdDimensionValue;
  }
  /**
   * Whether this campaign has been archived.
   *
   * @param bool $archived
   */
  public function setArchived($archived)
  {
    $this->archived = $archived;
  }
  /**
   * @return bool
   */
  public function getArchived()
  {
    return $this->archived;
  }
  /**
   * Audience segment groups assigned to this campaign. Cannot have more than
   * 300 segment groups.
   *
   * @param AudienceSegmentGroup[] $audienceSegmentGroups
   */
  public function setAudienceSegmentGroups($audienceSegmentGroups)
  {
    $this->audienceSegmentGroups = $audienceSegmentGroups;
  }
  /**
   * @return AudienceSegmentGroup[]
   */
  public function getAudienceSegmentGroups()
  {
    return $this->audienceSegmentGroups;
  }
  /**
   * Billing invoice code included in the Campaign Manager client billing
   * invoices associated with the campaign.
   *
   * @param string $billingInvoiceCode
   */
  public function setBillingInvoiceCode($billingInvoiceCode)
  {
    $this->billingInvoiceCode = $billingInvoiceCode;
  }
  /**
   * @return string
   */
  public function getBillingInvoiceCode()
  {
    return $this->billingInvoiceCode;
  }
  /**
   * Click-through URL suffix override properties for this campaign.
   *
   * @param ClickThroughUrlSuffixProperties $clickThroughUrlSuffixProperties
   */
  public function setClickThroughUrlSuffixProperties(ClickThroughUrlSuffixProperties $clickThroughUrlSuffixProperties)
  {
    $this->clickThroughUrlSuffixProperties = $clickThroughUrlSuffixProperties;
  }
  /**
   * @return ClickThroughUrlSuffixProperties
   */
  public function getClickThroughUrlSuffixProperties()
  {
    return $this->clickThroughUrlSuffixProperties;
  }
  /**
   * Arbitrary comments about this campaign. Must be less than 256 characters
   * long.
   *
   * @param string $comment
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * Information about the creation of this campaign. This is a read-only field.
   *
   * @param LastModifiedInfo $createInfo
   */
  public function setCreateInfo(LastModifiedInfo $createInfo)
  {
    $this->createInfo = $createInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getCreateInfo()
  {
    return $this->createInfo;
  }
  /**
   * List of creative group IDs that are assigned to the campaign.
   *
   * @param string[] $creativeGroupIds
   */
  public function setCreativeGroupIds($creativeGroupIds)
  {
    $this->creativeGroupIds = $creativeGroupIds;
  }
  /**
   * @return string[]
   */
  public function getCreativeGroupIds()
  {
    return $this->creativeGroupIds;
  }
  /**
   * Creative optimization configuration for the campaign.
   *
   * @param CreativeOptimizationConfiguration $creativeOptimizationConfiguration
   */
  public function setCreativeOptimizationConfiguration(CreativeOptimizationConfiguration $creativeOptimizationConfiguration)
  {
    $this->creativeOptimizationConfiguration = $creativeOptimizationConfiguration;
  }
  /**
   * @return CreativeOptimizationConfiguration
   */
  public function getCreativeOptimizationConfiguration()
  {
    return $this->creativeOptimizationConfiguration;
  }
  /**
   * Click-through event tag ID override properties for this campaign.
   *
   * @param DefaultClickThroughEventTagProperties $defaultClickThroughEventTagProperties
   */
  public function setDefaultClickThroughEventTagProperties(DefaultClickThroughEventTagProperties $defaultClickThroughEventTagProperties)
  {
    $this->defaultClickThroughEventTagProperties = $defaultClickThroughEventTagProperties;
  }
  /**
   * @return DefaultClickThroughEventTagProperties
   */
  public function getDefaultClickThroughEventTagProperties()
  {
    return $this->defaultClickThroughEventTagProperties;
  }
  /**
   * The default landing page ID for this campaign.
   *
   * @param string $defaultLandingPageId
   */
  public function setDefaultLandingPageId($defaultLandingPageId)
  {
    $this->defaultLandingPageId = $defaultLandingPageId;
  }
  /**
   * @return string
   */
  public function getDefaultLandingPageId()
  {
    return $this->defaultLandingPageId;
  }
  /**
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Optional. Whether the campaign has EU political ads. Campaign Manager 360
   * doesn't allow campaigns with EU political ads to serve in the EU. They can
   * still serve in other regions.
   *
   * Accepted values: CONTAINS_EU_POLITICAL_ADS,
   * DOES_NOT_CONTAIN_EU_POLITICAL_ADS
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
   * Overrides that can be used to activate or deactivate advertiser event tags.
   *
   * @param EventTagOverride[] $eventTagOverrides
   */
  public function setEventTagOverrides($eventTagOverrides)
  {
    $this->eventTagOverrides = $eventTagOverrides;
  }
  /**
   * @return EventTagOverride[]
   */
  public function getEventTagOverrides()
  {
    return $this->eventTagOverrides;
  }
  /**
   * External ID for this campaign.
   *
   * @param string $externalId
   */
  public function setExternalId($externalId)
  {
    $this->externalId = $externalId;
  }
  /**
   * @return string
   */
  public function getExternalId()
  {
    return $this->externalId;
  }
  /**
   * ID of this campaign. This is a read-only auto-generated field.
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
   * Dimension value for the ID of this campaign. This is a read-only, auto-
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
   * "dfareporting#campaign".
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
   * Information about the most recent modification of this campaign. This is a
   * read-only field.
   *
   * @param LastModifiedInfo $lastModifiedInfo
   */
  public function setLastModifiedInfo(LastModifiedInfo $lastModifiedInfo)
  {
    $this->lastModifiedInfo = $lastModifiedInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getLastModifiedInfo()
  {
    return $this->lastModifiedInfo;
  }
  /**
   * Measurement partner campaign link for tag wrapping.
   *
   * @param MeasurementPartnerCampaignLink $measurementPartnerLink
   */
  public function setMeasurementPartnerLink(MeasurementPartnerCampaignLink $measurementPartnerLink)
  {
    $this->measurementPartnerLink = $measurementPartnerLink;
  }
  /**
   * @return MeasurementPartnerCampaignLink
   */
  public function getMeasurementPartnerLink()
  {
    return $this->measurementPartnerLink;
  }
  /**
   * Name of this campaign. This is a required field and must be less than 512
   * characters long and unique among campaigns of the same advertiser.
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
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Subaccount ID of this campaign. This is a read-only field that can be left
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Campaign::class, 'Google_Service_Dfareporting_Campaign');
