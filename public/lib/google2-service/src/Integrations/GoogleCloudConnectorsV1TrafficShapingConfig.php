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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1TrafficShapingConfig extends \Google\Model
{
  /**
   * Required. * The duration over which the API call quota limits are
   * calculated. This duration is used to define the time window for evaluating
   * if the number of API calls made by a user is within the allowed quota
   * limits. For example: - To define a quota sampled over 16 seconds, set
   * `seconds` to 16 - To define a quota sampled over 5 minutes, set `seconds`
   * to 300 (5 * 60) - To define a quota sampled over 1 day, set `seconds` to
   * 86400 (24 * 60 * 60) and so on. It is important to note that this duration
   * is not the time the quota is valid for, but rather the time window over
   * which the quota is evaluated. For example, if the quota is 100 calls per 10
   * seconds, then this duration field would be set to 10 seconds.
   *
   * @var string
   */
  public $duration;
  /**
   * Required. Maximum number of api calls allowed.
   *
   * @var string
   */
  public $quotaLimit;

  /**
   * Required. * The duration over which the API call quota limits are
   * calculated. This duration is used to define the time window for evaluating
   * if the number of API calls made by a user is within the allowed quota
   * limits. For example: - To define a quota sampled over 16 seconds, set
   * `seconds` to 16 - To define a quota sampled over 5 minutes, set `seconds`
   * to 300 (5 * 60) - To define a quota sampled over 1 day, set `seconds` to
   * 86400 (24 * 60 * 60) and so on. It is important to note that this duration
   * is not the time the quota is valid for, but rather the time window over
   * which the quota is evaluated. For example, if the quota is 100 calls per 10
   * seconds, then this duration field would be set to 10 seconds.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Required. Maximum number of api calls allowed.
   *
   * @param string $quotaLimit
   */
  public function setQuotaLimit($quotaLimit)
  {
    $this->quotaLimit = $quotaLimit;
  }
  /**
   * @return string
   */
  public function getQuotaLimit()
  {
    return $this->quotaLimit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1TrafficShapingConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1TrafficShapingConfig');
