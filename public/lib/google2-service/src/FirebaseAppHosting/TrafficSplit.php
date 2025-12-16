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

namespace Google\Service\FirebaseAppHosting;

class TrafficSplit extends \Google\Model
{
  /**
   * Required. The build that traffic is being routed to.
   *
   * @var string
   */
  public $build;
  /**
   * Required. The percentage of traffic to send to the build. Currently must be
   * 100% or 0%.
   *
   * @var int
   */
  public $percent;

  /**
   * Required. The build that traffic is being routed to.
   *
   * @param string $build
   */
  public function setBuild($build)
  {
    $this->build = $build;
  }
  /**
   * @return string
   */
  public function getBuild()
  {
    return $this->build;
  }
  /**
   * Required. The percentage of traffic to send to the build. Currently must be
   * 100% or 0%.
   *
   * @param int $percent
   */
  public function setPercent($percent)
  {
    $this->percent = $percent;
  }
  /**
   * @return int
   */
  public function getPercent()
  {
    return $this->percent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrafficSplit::class, 'Google_Service_FirebaseAppHosting_TrafficSplit');
