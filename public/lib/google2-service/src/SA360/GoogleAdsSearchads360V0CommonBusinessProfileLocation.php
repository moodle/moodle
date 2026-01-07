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

class GoogleAdsSearchads360V0CommonBusinessProfileLocation extends \Google\Collection
{
  protected $collection_key = 'labels';
  /**
   * Advertiser specified label for the location on the Business Profile
   * account. This is synced from the Business Profile account.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Listing ID of this Business Profile location. This is synced from the
   * linked Business Profile account.
   *
   * @var string
   */
  public $listingId;
  /**
   * Business Profile store code of this location. This is synced from the
   * Business Profile account.
   *
   * @var string
   */
  public $storeCode;

  /**
   * Advertiser specified label for the location on the Business Profile
   * account. This is synced from the Business Profile account.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Listing ID of this Business Profile location. This is synced from the
   * linked Business Profile account.
   *
   * @param string $listingId
   */
  public function setListingId($listingId)
  {
    $this->listingId = $listingId;
  }
  /**
   * @return string
   */
  public function getListingId()
  {
    return $this->listingId;
  }
  /**
   * Business Profile store code of this location. This is synced from the
   * Business Profile account.
   *
   * @param string $storeCode
   */
  public function setStoreCode($storeCode)
  {
    $this->storeCode = $storeCode;
  }
  /**
   * @return string
   */
  public function getStoreCode()
  {
    return $this->storeCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonBusinessProfileLocation::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonBusinessProfileLocation');
