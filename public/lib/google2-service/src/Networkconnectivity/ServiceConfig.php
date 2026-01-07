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

namespace Google\Service\Networkconnectivity;

class ServiceConfig extends \Google\Model
{
  /**
   * The service is not eligible for Data Transfer Essentials configuration.
   * This is the default case.
   */
  public const ELIGIBILITY_CRITERIA_ELIGIBILITY_CRITERIA_UNSPECIFIED = 'ELIGIBILITY_CRITERIA_UNSPECIFIED';
  /**
   * The service is eligible for Data Transfer Essentials configuration only for
   * Premium Tier.
   */
  public const ELIGIBILITY_CRITERIA_NETWORK_SERVICE_TIER_PREMIUM_ONLY = 'NETWORK_SERVICE_TIER_PREMIUM_ONLY';
  /**
   * The service is eligible for Data Transfer Essentials configuration only for
   * Standard Tier.
   */
  public const ELIGIBILITY_CRITERIA_NETWORK_SERVICE_TIER_STANDARD_ONLY = 'NETWORK_SERVICE_TIER_STANDARD_ONLY';
  /**
   * The service is eligible for Data Transfer Essentials configuration only for
   * the regional endpoint.
   */
  public const ELIGIBILITY_CRITERIA_REQUEST_ENDPOINT_REGIONAL_ENDPOINT_ONLY = 'REQUEST_ENDPOINT_REGIONAL_ENDPOINT_ONLY';
  /**
   * Output only. The eligibility criteria for the service.
   *
   * @var string
   */
  public $eligibilityCriteria;
  /**
   * Output only. The end time for eligibility criteria support. If not
   * specified, no planned end time is set.
   *
   * @var string
   */
  public $supportEndTime;

  /**
   * Output only. The eligibility criteria for the service.
   *
   * Accepted values: ELIGIBILITY_CRITERIA_UNSPECIFIED,
   * NETWORK_SERVICE_TIER_PREMIUM_ONLY, NETWORK_SERVICE_TIER_STANDARD_ONLY,
   * REQUEST_ENDPOINT_REGIONAL_ENDPOINT_ONLY
   *
   * @param self::ELIGIBILITY_CRITERIA_* $eligibilityCriteria
   */
  public function setEligibilityCriteria($eligibilityCriteria)
  {
    $this->eligibilityCriteria = $eligibilityCriteria;
  }
  /**
   * @return self::ELIGIBILITY_CRITERIA_*
   */
  public function getEligibilityCriteria()
  {
    return $this->eligibilityCriteria;
  }
  /**
   * Output only. The end time for eligibility criteria support. If not
   * specified, no planned end time is set.
   *
   * @param string $supportEndTime
   */
  public function setSupportEndTime($supportEndTime)
  {
    $this->supportEndTime = $supportEndTime;
  }
  /**
   * @return string
   */
  public function getSupportEndTime()
  {
    return $this->supportEndTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceConfig::class, 'Google_Service_Networkconnectivity_ServiceConfig');
