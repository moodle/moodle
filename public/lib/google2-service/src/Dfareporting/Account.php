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

class Account extends \Google\Collection
{
  /**
   * Basic profile has fewer features and lower CPM.
   */
  public const ACCOUNT_PROFILE_ACCOUNT_PROFILE_BASIC = 'ACCOUNT_PROFILE_BASIC';
  /**
   * Standard profile as a higher CPM and all the features.
   */
  public const ACCOUNT_PROFILE_ACCOUNT_PROFILE_STANDARD = 'ACCOUNT_PROFILE_STANDARD';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_40K = 'ACTIVE_ADS_TIER_40K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_75K = 'ACTIVE_ADS_TIER_75K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_100K = 'ACTIVE_ADS_TIER_100K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_200K = 'ACTIVE_ADS_TIER_200K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_300K = 'ACTIVE_ADS_TIER_300K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_500K = 'ACTIVE_ADS_TIER_500K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_750K = 'ACTIVE_ADS_TIER_750K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_1M = 'ACTIVE_ADS_TIER_1M';
  protected $collection_key = 'availablePermissionIds';
  /**
   * Account permissions assigned to this account.
   *
   * @var string[]
   */
  public $accountPermissionIds;
  /**
   * Profile for this account. This is a read-only field that can be left blank.
   *
   * @var string
   */
  public $accountProfile;
  /**
   * Whether this account is active.
   *
   * @var bool
   */
  public $active;
  /**
   * Maximum number of active ads allowed for this account.
   *
   * @var string
   */
  public $activeAdsLimitTier;
  /**
   * Whether to serve creatives with Active View tags. If disabled, viewability
   * data will not be available for any impressions.
   *
   * @var bool
   */
  public $activeViewOptOut;
  /**
   * User role permissions available to the user roles of this account.
   *
   * @var string[]
   */
  public $availablePermissionIds;
  /**
   * ID of the country associated with this account.
   *
   * @var string
   */
  public $countryId;
  /**
   * ID of currency associated with this account. This is a required field.
   * Acceptable values are: - "1" for USD - "2" for GBP - "3" for ESP - "4" for
   * SEK - "5" for CAD - "6" for JPY - "7" for DEM - "8" for AUD - "9" for FRF -
   * "10" for ITL - "11" for DKK - "12" for NOK - "13" for FIM - "14" for ZAR -
   * "15" for IEP - "16" for NLG - "17" for EUR - "18" for KRW - "19" for TWD -
   * "20" for SGD - "21" for CNY - "22" for HKD - "23" for NZD - "24" for MYR -
   * "25" for BRL - "26" for PTE - "28" for CLP - "29" for TRY - "30" for ARS -
   * "31" for PEN - "32" for ILS - "33" for CHF - "34" for VEF - "35" for COP -
   * "36" for GTQ - "37" for PLN - "39" for INR - "40" for THB - "41" for IDR -
   * "42" for CZK - "43" for RON - "44" for HUF - "45" for RUB - "46" for AED -
   * "47" for BGN - "48" for HRK - "49" for MXN - "50" for NGN - "51" for EGP
   *
   * @var string
   */
  public $currencyId;
  /**
   * Default placement dimensions for this account.
   *
   * @var string
   */
  public $defaultCreativeSizeId;
  /**
   * Description of this account.
   *
   * @var string
   */
  public $description;
  /**
   * ID of this account. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#account".
   *
   * @var string
   */
  public $kind;
  /**
   * Locale of this account. Acceptable values are: - "cs" (Czech) - "de"
   * (German) - "en" (English) - "en-GB" (English United Kingdom) - "es"
   * (Spanish) - "fr" (French) - "it" (Italian) - "ja" (Japanese) - "ko"
   * (Korean) - "pl" (Polish) - "pt-BR" (Portuguese Brazil) - "ru" (Russian) -
   * "sv" (Swedish) - "tr" (Turkish) - "zh-CN" (Chinese Simplified) - "zh-TW"
   * (Chinese Traditional)
   *
   * @var string
   */
  public $locale;
  /**
   * Maximum image size allowed for this account, in kilobytes. Value must be
   * greater than or equal to 1.
   *
   * @var string
   */
  public $maximumImageSize;
  /**
   * Name of this account. This is a required field, and must be less than 128
   * characters long and be globally unique.
   *
   * @var string
   */
  public $name;
  /**
   * Whether campaigns created in this account will be enabled for Nielsen OCR
   * reach ratings by default.
   *
   * @var bool
   */
  public $nielsenOcrEnabled;
  protected $reportsConfigurationType = ReportsConfiguration::class;
  protected $reportsConfigurationDataType = '';
  /**
   * Share Path to Conversion reports with Twitter.
   *
   * @var bool
   */
  public $shareReportsWithTwitter;
  /**
   * File size limit in kilobytes of Rich Media teaser creatives. Acceptable
   * values are 1 to 10240, inclusive.
   *
   * @var string
   */
  public $teaserSizeLimit;

  /**
   * Account permissions assigned to this account.
   *
   * @param string[] $accountPermissionIds
   */
  public function setAccountPermissionIds($accountPermissionIds)
  {
    $this->accountPermissionIds = $accountPermissionIds;
  }
  /**
   * @return string[]
   */
  public function getAccountPermissionIds()
  {
    return $this->accountPermissionIds;
  }
  /**
   * Profile for this account. This is a read-only field that can be left blank.
   *
   * Accepted values: ACCOUNT_PROFILE_BASIC, ACCOUNT_PROFILE_STANDARD
   *
   * @param self::ACCOUNT_PROFILE_* $accountProfile
   */
  public function setAccountProfile($accountProfile)
  {
    $this->accountProfile = $accountProfile;
  }
  /**
   * @return self::ACCOUNT_PROFILE_*
   */
  public function getAccountProfile()
  {
    return $this->accountProfile;
  }
  /**
   * Whether this account is active.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Maximum number of active ads allowed for this account.
   *
   * Accepted values: ACTIVE_ADS_TIER_40K, ACTIVE_ADS_TIER_75K,
   * ACTIVE_ADS_TIER_100K, ACTIVE_ADS_TIER_200K, ACTIVE_ADS_TIER_300K,
   * ACTIVE_ADS_TIER_500K, ACTIVE_ADS_TIER_750K, ACTIVE_ADS_TIER_1M
   *
   * @param self::ACTIVE_ADS_LIMIT_TIER_* $activeAdsLimitTier
   */
  public function setActiveAdsLimitTier($activeAdsLimitTier)
  {
    $this->activeAdsLimitTier = $activeAdsLimitTier;
  }
  /**
   * @return self::ACTIVE_ADS_LIMIT_TIER_*
   */
  public function getActiveAdsLimitTier()
  {
    return $this->activeAdsLimitTier;
  }
  /**
   * Whether to serve creatives with Active View tags. If disabled, viewability
   * data will not be available for any impressions.
   *
   * @param bool $activeViewOptOut
   */
  public function setActiveViewOptOut($activeViewOptOut)
  {
    $this->activeViewOptOut = $activeViewOptOut;
  }
  /**
   * @return bool
   */
  public function getActiveViewOptOut()
  {
    return $this->activeViewOptOut;
  }
  /**
   * User role permissions available to the user roles of this account.
   *
   * @param string[] $availablePermissionIds
   */
  public function setAvailablePermissionIds($availablePermissionIds)
  {
    $this->availablePermissionIds = $availablePermissionIds;
  }
  /**
   * @return string[]
   */
  public function getAvailablePermissionIds()
  {
    return $this->availablePermissionIds;
  }
  /**
   * ID of the country associated with this account.
   *
   * @param string $countryId
   */
  public function setCountryId($countryId)
  {
    $this->countryId = $countryId;
  }
  /**
   * @return string
   */
  public function getCountryId()
  {
    return $this->countryId;
  }
  /**
   * ID of currency associated with this account. This is a required field.
   * Acceptable values are: - "1" for USD - "2" for GBP - "3" for ESP - "4" for
   * SEK - "5" for CAD - "6" for JPY - "7" for DEM - "8" for AUD - "9" for FRF -
   * "10" for ITL - "11" for DKK - "12" for NOK - "13" for FIM - "14" for ZAR -
   * "15" for IEP - "16" for NLG - "17" for EUR - "18" for KRW - "19" for TWD -
   * "20" for SGD - "21" for CNY - "22" for HKD - "23" for NZD - "24" for MYR -
   * "25" for BRL - "26" for PTE - "28" for CLP - "29" for TRY - "30" for ARS -
   * "31" for PEN - "32" for ILS - "33" for CHF - "34" for VEF - "35" for COP -
   * "36" for GTQ - "37" for PLN - "39" for INR - "40" for THB - "41" for IDR -
   * "42" for CZK - "43" for RON - "44" for HUF - "45" for RUB - "46" for AED -
   * "47" for BGN - "48" for HRK - "49" for MXN - "50" for NGN - "51" for EGP
   *
   * @param string $currencyId
   */
  public function setCurrencyId($currencyId)
  {
    $this->currencyId = $currencyId;
  }
  /**
   * @return string
   */
  public function getCurrencyId()
  {
    return $this->currencyId;
  }
  /**
   * Default placement dimensions for this account.
   *
   * @param string $defaultCreativeSizeId
   */
  public function setDefaultCreativeSizeId($defaultCreativeSizeId)
  {
    $this->defaultCreativeSizeId = $defaultCreativeSizeId;
  }
  /**
   * @return string
   */
  public function getDefaultCreativeSizeId()
  {
    return $this->defaultCreativeSizeId;
  }
  /**
   * Description of this account.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * ID of this account. This is a read-only, auto-generated field.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#account".
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
   * Locale of this account. Acceptable values are: - "cs" (Czech) - "de"
   * (German) - "en" (English) - "en-GB" (English United Kingdom) - "es"
   * (Spanish) - "fr" (French) - "it" (Italian) - "ja" (Japanese) - "ko"
   * (Korean) - "pl" (Polish) - "pt-BR" (Portuguese Brazil) - "ru" (Russian) -
   * "sv" (Swedish) - "tr" (Turkish) - "zh-CN" (Chinese Simplified) - "zh-TW"
   * (Chinese Traditional)
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * Maximum image size allowed for this account, in kilobytes. Value must be
   * greater than or equal to 1.
   *
   * @param string $maximumImageSize
   */
  public function setMaximumImageSize($maximumImageSize)
  {
    $this->maximumImageSize = $maximumImageSize;
  }
  /**
   * @return string
   */
  public function getMaximumImageSize()
  {
    return $this->maximumImageSize;
  }
  /**
   * Name of this account. This is a required field, and must be less than 128
   * characters long and be globally unique.
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
   * Whether campaigns created in this account will be enabled for Nielsen OCR
   * reach ratings by default.
   *
   * @param bool $nielsenOcrEnabled
   */
  public function setNielsenOcrEnabled($nielsenOcrEnabled)
  {
    $this->nielsenOcrEnabled = $nielsenOcrEnabled;
  }
  /**
   * @return bool
   */
  public function getNielsenOcrEnabled()
  {
    return $this->nielsenOcrEnabled;
  }
  /**
   * Reporting configuration of this account.
   *
   * @param ReportsConfiguration $reportsConfiguration
   */
  public function setReportsConfiguration(ReportsConfiguration $reportsConfiguration)
  {
    $this->reportsConfiguration = $reportsConfiguration;
  }
  /**
   * @return ReportsConfiguration
   */
  public function getReportsConfiguration()
  {
    return $this->reportsConfiguration;
  }
  /**
   * Share Path to Conversion reports with Twitter.
   *
   * @param bool $shareReportsWithTwitter
   */
  public function setShareReportsWithTwitter($shareReportsWithTwitter)
  {
    $this->shareReportsWithTwitter = $shareReportsWithTwitter;
  }
  /**
   * @return bool
   */
  public function getShareReportsWithTwitter()
  {
    return $this->shareReportsWithTwitter;
  }
  /**
   * File size limit in kilobytes of Rich Media teaser creatives. Acceptable
   * values are 1 to 10240, inclusive.
   *
   * @param string $teaserSizeLimit
   */
  public function setTeaserSizeLimit($teaserSizeLimit)
  {
    $this->teaserSizeLimit = $teaserSizeLimit;
  }
  /**
   * @return string
   */
  public function getTeaserSizeLimit()
  {
    return $this->teaserSizeLimit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Account::class, 'Google_Service_Dfareporting_Account');
