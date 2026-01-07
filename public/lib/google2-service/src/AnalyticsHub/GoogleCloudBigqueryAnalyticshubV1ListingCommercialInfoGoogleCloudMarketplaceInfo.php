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

namespace Google\Service\AnalyticsHub;

class GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfoGoogleCloudMarketplaceInfo extends \Google\Model
{
  /**
   * Commercialization is incomplete and cannot be used.
   */
  public const COMMERCIAL_STATE_COMMERCIAL_STATE_UNSPECIFIED = 'COMMERCIAL_STATE_UNSPECIFIED';
  /**
   * Commercialization has been initialized.
   */
  public const COMMERCIAL_STATE_ONBOARDING = 'ONBOARDING';
  /**
   * Commercialization is complete and available for use.
   */
  public const COMMERCIAL_STATE_ACTIVE = 'ACTIVE';
  /**
   * Output only. Commercial state of the Marketplace Data Product.
   *
   * @var string
   */
  public $commercialState;
  /**
   * Output only. Resource name of the commercial service associated with the
   * Marketplace Data Product. e.g. example.com
   *
   * @var string
   */
  public $service;

  /**
   * Output only. Commercial state of the Marketplace Data Product.
   *
   * Accepted values: COMMERCIAL_STATE_UNSPECIFIED, ONBOARDING, ACTIVE
   *
   * @param self::COMMERCIAL_STATE_* $commercialState
   */
  public function setCommercialState($commercialState)
  {
    $this->commercialState = $commercialState;
  }
  /**
   * @return self::COMMERCIAL_STATE_*
   */
  public function getCommercialState()
  {
    return $this->commercialState;
  }
  /**
   * Output only. Resource name of the commercial service associated with the
   * Marketplace Data Product. e.g. example.com
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfoGoogleCloudMarketplaceInfo::class, 'Google_Service_AnalyticsHub_GoogleCloudBigqueryAnalyticshubV1ListingCommercialInfoGoogleCloudMarketplaceInfo');
