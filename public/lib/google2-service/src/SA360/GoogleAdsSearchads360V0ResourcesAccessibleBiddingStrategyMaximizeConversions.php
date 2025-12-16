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

class GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversions extends \Google\Model
{
  /**
   * Output only. The target cost per acquisition (CPA) option. This is the
   * average amount that you would like to spend per acquisition.
   *
   * @var string
   */
  public $targetCpa;
  /**
   * Output only. The target cost per acquisition (CPA) option. This is the
   * average amount that you would like to spend per acquisition.
   *
   * @var string
   */
  public $targetCpaMicros;

  /**
   * Output only. The target cost per acquisition (CPA) option. This is the
   * average amount that you would like to spend per acquisition.
   *
   * @param string $targetCpa
   */
  public function setTargetCpa($targetCpa)
  {
    $this->targetCpa = $targetCpa;
  }
  /**
   * @return string
   */
  public function getTargetCpa()
  {
    return $this->targetCpa;
  }
  /**
   * Output only. The target cost per acquisition (CPA) option. This is the
   * average amount that you would like to spend per acquisition.
   *
   * @param string $targetCpaMicros
   */
  public function setTargetCpaMicros($targetCpaMicros)
  {
    $this->targetCpaMicros = $targetCpaMicros;
  }
  /**
   * @return string
   */
  public function getTargetCpaMicros()
  {
    return $this->targetCpaMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversions::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversions');
