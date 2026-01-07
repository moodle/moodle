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

namespace Google\Service\NetworkServices;

class HttpRouteFaultInjectionPolicyDelay extends \Google\Model
{
  /**
   * Specify a fixed delay before forwarding the request.
   *
   * @var string
   */
  public $fixedDelay;
  /**
   * The percentage of traffic on which delay will be injected. The value must
   * be between [0, 100]
   *
   * @var int
   */
  public $percentage;

  /**
   * Specify a fixed delay before forwarding the request.
   *
   * @param string $fixedDelay
   */
  public function setFixedDelay($fixedDelay)
  {
    $this->fixedDelay = $fixedDelay;
  }
  /**
   * @return string
   */
  public function getFixedDelay()
  {
    return $this->fixedDelay;
  }
  /**
   * The percentage of traffic on which delay will be injected. The value must
   * be between [0, 100]
   *
   * @param int $percentage
   */
  public function setPercentage($percentage)
  {
    $this->percentage = $percentage;
  }
  /**
   * @return int
   */
  public function getPercentage()
  {
    return $this->percentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteFaultInjectionPolicyDelay::class, 'Google_Service_NetworkServices_HttpRouteFaultInjectionPolicyDelay');
