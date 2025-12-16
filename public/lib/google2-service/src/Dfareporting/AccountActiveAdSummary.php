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

class AccountActiveAdSummary extends \Google\Model
{
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_40K = 'ACTIVE_ADS_TIER_40K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_75K = 'ACTIVE_ADS_TIER_75K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_100K = 'ACTIVE_ADS_TIER_100K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_200K = 'ACTIVE_ADS_TIER_200K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_300K = 'ACTIVE_ADS_TIER_300K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_500K = 'ACTIVE_ADS_TIER_500K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_750K = 'ACTIVE_ADS_TIER_750K';
  public const ACTIVE_ADS_LIMIT_TIER_ACTIVE_ADS_TIER_1M = 'ACTIVE_ADS_TIER_1M';
  /**
   * ID of the account.
   *
   * @var string
   */
  public $accountId;
  /**
   * Ads that have been activated for the account
   *
   * @var string
   */
  public $activeAds;
  /**
   * Maximum number of active ads allowed for the account.
   *
   * @var string
   */
  public $activeAdsLimitTier;
  /**
   * Ads that can be activated for the account.
   *
   * @var string
   */
  public $availableAds;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#accountActiveAdSummary".
   *
   * @var string
   */
  public $kind;

  /**
   * ID of the account.
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
   * Ads that have been activated for the account
   *
   * @param string $activeAds
   */
  public function setActiveAds($activeAds)
  {
    $this->activeAds = $activeAds;
  }
  /**
   * @return string
   */
  public function getActiveAds()
  {
    return $this->activeAds;
  }
  /**
   * Maximum number of active ads allowed for the account.
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
   * Ads that can be activated for the account.
   *
   * @param string $availableAds
   */
  public function setAvailableAds($availableAds)
  {
    $this->availableAds = $availableAds;
  }
  /**
   * @return string
   */
  public function getAvailableAds()
  {
    return $this->availableAds;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#accountActiveAdSummary".
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountActiveAdSummary::class, 'Google_Service_Dfareporting_AccountActiveAdSummary');
