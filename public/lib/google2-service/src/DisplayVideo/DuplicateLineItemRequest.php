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

class DuplicateLineItemRequest extends \Google\Model
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
   * Whether this line item will serve European Union political ads. If
   * contains_eu_political_ads has been set to
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` in the parent advertiser, then
   * this field will be assigned `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` if
   * not otherwise specified. This field can then be updated using the UI, API,
   * or Structured Data Files. This field must be assigned when creating a new
   * line item. Otherwise, **the `advertisers.lineItems.create` request will
   * fail**.
   *
   * @var string
   */
  public $containsEuPoliticalAds;
  /**
   * The display name of the new line item. Must be UTF-8 encoded with a maximum
   * size of 240 bytes.
   *
   * @var string
   */
  public $targetDisplayName;

  /**
   * Whether this line item will serve European Union political ads. If
   * contains_eu_political_ads has been set to
   * `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` in the parent advertiser, then
   * this field will be assigned `DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING` if
   * not otherwise specified. This field can then be updated using the UI, API,
   * or Structured Data Files. This field must be assigned when creating a new
   * line item. Otherwise, **the `advertisers.lineItems.create` request will
   * fail**.
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
   * The display name of the new line item. Must be UTF-8 encoded with a maximum
   * size of 240 bytes.
   *
   * @param string $targetDisplayName
   */
  public function setTargetDisplayName($targetDisplayName)
  {
    $this->targetDisplayName = $targetDisplayName;
  }
  /**
   * @return string
   */
  public function getTargetDisplayName()
  {
    return $this->targetDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DuplicateLineItemRequest::class, 'Google_Service_DisplayVideo_DuplicateLineItemRequest');
