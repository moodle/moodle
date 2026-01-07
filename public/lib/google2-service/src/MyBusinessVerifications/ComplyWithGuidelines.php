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

namespace Google\Service\MyBusinessVerifications;

class ComplyWithGuidelines extends \Google\Model
{
  /**
   * Not specified.
   */
  public const RECOMMENDATION_REASON_RECOMMENDATION_REASON_UNSPECIFIED = 'RECOMMENDATION_REASON_UNSPECIFIED';
  /**
   * The business location is suspended. To fix this issue, consult the [Help
   * Center article](https://support.google.com/business/answer/4569145).
   */
  public const RECOMMENDATION_REASON_BUSINESS_LOCATION_SUSPENDED = 'BUSINESS_LOCATION_SUSPENDED';
  /**
   * The business location is disabled. To fix this issue, consult the [Help
   * Center article](https://support.google.com/business/answer/9334246).
   */
  public const RECOMMENDATION_REASON_BUSINESS_LOCATION_DISABLED = 'BUSINESS_LOCATION_DISABLED';
  /**
   * The reason why the location is being recommended to comply with guidelines.
   *
   * @var string
   */
  public $recommendationReason;

  /**
   * The reason why the location is being recommended to comply with guidelines.
   *
   * Accepted values: RECOMMENDATION_REASON_UNSPECIFIED,
   * BUSINESS_LOCATION_SUSPENDED, BUSINESS_LOCATION_DISABLED
   *
   * @param self::RECOMMENDATION_REASON_* $recommendationReason
   */
  public function setRecommendationReason($recommendationReason)
  {
    $this->recommendationReason = $recommendationReason;
  }
  /**
   * @return self::RECOMMENDATION_REASON_*
   */
  public function getRecommendationReason()
  {
    return $this->recommendationReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComplyWithGuidelines::class, 'Google_Service_MyBusinessVerifications_ComplyWithGuidelines');
