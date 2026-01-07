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

namespace Google\Service\Compute;

class FutureResourcesRecommendationOtherLocation extends \Google\Model
{
  /**
   * The requested resources are offered in this location but the requested time
   * window is does not meet the required conditions.
   */
  public const STATUS_CONDITIONS_NOT_MET = 'CONDITIONS_NOT_MET';
  /**
   * The requested resources are not offered in this location. Retrying the
   * request will not change this status.
   */
  public const STATUS_NOT_SUPPORTED = 'NOT_SUPPORTED';
  /**
   * The requested resources are offered in this location and the requested time
   * window is accepted but there is no capacity within the requested time
   * window.
   */
  public const STATUS_NO_CAPACITY = 'NO_CAPACITY';
  /**
   * Default value, unused.
   */
  public const STATUS_OTHER_LOCATION_STATUS_UNDEFINED = 'OTHER_LOCATION_STATUS_UNDEFINED';
  /**
   * The requested resources are offered in this location and it is possible to
   * request them. However, another location was better and was recommended.
   */
  public const STATUS_RECOMMENDED = 'RECOMMENDED';
  /**
   * Details (human readable) describing the situation. For example, if status
   * is CONDITION_NOT_MET, then details contain information about the parameters
   * of the time window that did not meet the required conditions.
   *
   * @var string
   */
  public $details;
  /**
   * Status of recommendation in this location.
   *
   * @var string
   */
  public $status;

  /**
   * Details (human readable) describing the situation. For example, if status
   * is CONDITION_NOT_MET, then details contain information about the parameters
   * of the time window that did not meet the required conditions.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Status of recommendation in this location.
   *
   * Accepted values: CONDITIONS_NOT_MET, NOT_SUPPORTED, NO_CAPACITY,
   * OTHER_LOCATION_STATUS_UNDEFINED, RECOMMENDED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureResourcesRecommendationOtherLocation::class, 'Google_Service_Compute_FutureResourcesRecommendationOtherLocation');
