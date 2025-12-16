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

class FloodlightConfiguration extends \Google\Collection
{
  public const FIRST_DAY_OF_WEEK_SUNDAY = 'SUNDAY';
  public const FIRST_DAY_OF_WEEK_MONDAY = 'MONDAY';
  public const NATURAL_SEARCH_CONVERSION_ATTRIBUTION_OPTION_EXCLUDE_NATURAL_SEARCH_CONVERSION_ATTRIBUTION = 'EXCLUDE_NATURAL_SEARCH_CONVERSION_ATTRIBUTION';
  public const NATURAL_SEARCH_CONVERSION_ATTRIBUTION_OPTION_INCLUDE_NATURAL_SEARCH_CONVERSION_ATTRIBUTION = 'INCLUDE_NATURAL_SEARCH_CONVERSION_ATTRIBUTION';
  public const NATURAL_SEARCH_CONVERSION_ATTRIBUTION_OPTION_INCLUDE_NATURAL_SEARCH_TIERED_CONVERSION_ATTRIBUTION = 'INCLUDE_NATURAL_SEARCH_TIERED_CONVERSION_ATTRIBUTION';
  protected $collection_key = 'userDefinedVariableConfigurations';
  /**
   * Account ID of this floodlight configuration. This is a read-only field that
   * can be left blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Advertiser ID of the parent advertiser of this floodlight configuration.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Whether advertiser data is shared with Google Analytics.
   *
   * @var bool
   */
  public $analyticsDataSharingEnabled;
  protected $customViewabilityMetricType = CustomViewabilityMetric::class;
  protected $customViewabilityMetricDataType = '';
  /**
   * Whether the exposure-to-conversion report is enabled. This report shows
   * detailed pathway information on up to 10 of the most recent ad exposures
   * seen by a user before converting.
   *
   * @var bool
   */
  public $exposureToConversionEnabled;
  /**
   * @var string
   */
  public $firstDayOfWeek;
  /**
   * ID of this floodlight configuration. This is a read-only, auto-generated
   * field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Whether in-app attribution tracking is enabled.
   *
   * @var bool
   */
  public $inAppAttributionTrackingEnabled;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#floodlightConfiguration".
   *
   * @var string
   */
  public $kind;
  protected $lookbackConfigurationType = LookbackConfiguration::class;
  protected $lookbackConfigurationDataType = '';
  /**
   * Types of attribution options for natural search conversions.
   *
   * @var string
   */
  public $naturalSearchConversionAttributionOption;
  protected $omnitureSettingsType = OmnitureSettings::class;
  protected $omnitureSettingsDataType = '';
  /**
   * Subaccount ID of this floodlight configuration. This is a read-only field
   * that can be left blank.
   *
   * @var string
   */
  public $subaccountId;
  protected $tagSettingsType = TagSettings::class;
  protected $tagSettingsDataType = '';
  protected $thirdPartyAuthenticationTokensType = ThirdPartyAuthenticationToken::class;
  protected $thirdPartyAuthenticationTokensDataType = 'array';
  protected $userDefinedVariableConfigurationsType = UserDefinedVariableConfiguration::class;
  protected $userDefinedVariableConfigurationsDataType = 'array';

  /**
   * Account ID of this floodlight configuration. This is a read-only field that
   * can be left blank.
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
   * Advertiser ID of the parent advertiser of this floodlight configuration.
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
   * Dimension value for the ID of the advertiser. This is a read-only, auto-
   * generated field.
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
   * Whether advertiser data is shared with Google Analytics.
   *
   * @param bool $analyticsDataSharingEnabled
   */
  public function setAnalyticsDataSharingEnabled($analyticsDataSharingEnabled)
  {
    $this->analyticsDataSharingEnabled = $analyticsDataSharingEnabled;
  }
  /**
   * @return bool
   */
  public function getAnalyticsDataSharingEnabled()
  {
    return $this->analyticsDataSharingEnabled;
  }
  /**
   * Custom Viewability metric for the floodlight configuration.
   *
   * @param CustomViewabilityMetric $customViewabilityMetric
   */
  public function setCustomViewabilityMetric(CustomViewabilityMetric $customViewabilityMetric)
  {
    $this->customViewabilityMetric = $customViewabilityMetric;
  }
  /**
   * @return CustomViewabilityMetric
   */
  public function getCustomViewabilityMetric()
  {
    return $this->customViewabilityMetric;
  }
  /**
   * Whether the exposure-to-conversion report is enabled. This report shows
   * detailed pathway information on up to 10 of the most recent ad exposures
   * seen by a user before converting.
   *
   * @param bool $exposureToConversionEnabled
   */
  public function setExposureToConversionEnabled($exposureToConversionEnabled)
  {
    $this->exposureToConversionEnabled = $exposureToConversionEnabled;
  }
  /**
   * @return bool
   */
  public function getExposureToConversionEnabled()
  {
    return $this->exposureToConversionEnabled;
  }
  /**
   * @param self::FIRST_DAY_OF_WEEK_* $firstDayOfWeek
   */
  public function setFirstDayOfWeek($firstDayOfWeek)
  {
    $this->firstDayOfWeek = $firstDayOfWeek;
  }
  /**
   * @return self::FIRST_DAY_OF_WEEK_*
   */
  public function getFirstDayOfWeek()
  {
    return $this->firstDayOfWeek;
  }
  /**
   * ID of this floodlight configuration. This is a read-only, auto-generated
   * field.
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
   * Dimension value for the ID of this floodlight configuration. This is a
   * read-only, auto-generated field.
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
   * Whether in-app attribution tracking is enabled.
   *
   * @param bool $inAppAttributionTrackingEnabled
   */
  public function setInAppAttributionTrackingEnabled($inAppAttributionTrackingEnabled)
  {
    $this->inAppAttributionTrackingEnabled = $inAppAttributionTrackingEnabled;
  }
  /**
   * @return bool
   */
  public function getInAppAttributionTrackingEnabled()
  {
    return $this->inAppAttributionTrackingEnabled;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#floodlightConfiguration".
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
   * Lookback window settings for this floodlight configuration.
   *
   * @param LookbackConfiguration $lookbackConfiguration
   */
  public function setLookbackConfiguration(LookbackConfiguration $lookbackConfiguration)
  {
    $this->lookbackConfiguration = $lookbackConfiguration;
  }
  /**
   * @return LookbackConfiguration
   */
  public function getLookbackConfiguration()
  {
    return $this->lookbackConfiguration;
  }
  /**
   * Types of attribution options for natural search conversions.
   *
   * Accepted values: EXCLUDE_NATURAL_SEARCH_CONVERSION_ATTRIBUTION,
   * INCLUDE_NATURAL_SEARCH_CONVERSION_ATTRIBUTION,
   * INCLUDE_NATURAL_SEARCH_TIERED_CONVERSION_ATTRIBUTION
   *
   * @param self::NATURAL_SEARCH_CONVERSION_ATTRIBUTION_OPTION_* $naturalSearchConversionAttributionOption
   */
  public function setNaturalSearchConversionAttributionOption($naturalSearchConversionAttributionOption)
  {
    $this->naturalSearchConversionAttributionOption = $naturalSearchConversionAttributionOption;
  }
  /**
   * @return self::NATURAL_SEARCH_CONVERSION_ATTRIBUTION_OPTION_*
   */
  public function getNaturalSearchConversionAttributionOption()
  {
    return $this->naturalSearchConversionAttributionOption;
  }
  /**
   * Settings for Campaign Manager Omniture integration.
   *
   * @param OmnitureSettings $omnitureSettings
   */
  public function setOmnitureSettings(OmnitureSettings $omnitureSettings)
  {
    $this->omnitureSettings = $omnitureSettings;
  }
  /**
   * @return OmnitureSettings
   */
  public function getOmnitureSettings()
  {
    return $this->omnitureSettings;
  }
  /**
   * Subaccount ID of this floodlight configuration. This is a read-only field
   * that can be left blank.
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
   * Configuration settings for dynamic and image floodlight tags.
   *
   * @param TagSettings $tagSettings
   */
  public function setTagSettings(TagSettings $tagSettings)
  {
    $this->tagSettings = $tagSettings;
  }
  /**
   * @return TagSettings
   */
  public function getTagSettings()
  {
    return $this->tagSettings;
  }
  /**
   * List of third-party authentication tokens enabled for this configuration.
   *
   * @param ThirdPartyAuthenticationToken[] $thirdPartyAuthenticationTokens
   */
  public function setThirdPartyAuthenticationTokens($thirdPartyAuthenticationTokens)
  {
    $this->thirdPartyAuthenticationTokens = $thirdPartyAuthenticationTokens;
  }
  /**
   * @return ThirdPartyAuthenticationToken[]
   */
  public function getThirdPartyAuthenticationTokens()
  {
    return $this->thirdPartyAuthenticationTokens;
  }
  /**
   * List of user defined variables enabled for this configuration.
   *
   * @param UserDefinedVariableConfiguration[] $userDefinedVariableConfigurations
   */
  public function setUserDefinedVariableConfigurations($userDefinedVariableConfigurations)
  {
    $this->userDefinedVariableConfigurations = $userDefinedVariableConfigurations;
  }
  /**
   * @return UserDefinedVariableConfiguration[]
   */
  public function getUserDefinedVariableConfigurations()
  {
    return $this->userDefinedVariableConfigurations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FloodlightConfiguration::class, 'Google_Service_Dfareporting_FloodlightConfiguration');
