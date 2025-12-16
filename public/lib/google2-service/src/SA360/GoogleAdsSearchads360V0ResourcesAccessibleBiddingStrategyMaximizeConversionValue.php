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

class GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversionValue extends \Google\Model
{
  /**
   * Output only. The target return on ad spend (ROAS) option. If set, the bid
   * strategy will maximize revenue while averaging the target return on ad
   * spend. If the target ROAS is high, the bid strategy may not be able to
   * spend the full budget. If the target ROAS is not set, the bid strategy will
   * aim to achieve the highest possible ROAS for the budget.
   *
   * @var 
   */
  public $targetRoas;

  public function setTargetRoas($targetRoas)
  {
    $this->targetRoas = $targetRoas;
  }
  public function getTargetRoas()
  {
    return $this->targetRoas;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversionValue::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAccessibleBiddingStrategyMaximizeConversionValue');
