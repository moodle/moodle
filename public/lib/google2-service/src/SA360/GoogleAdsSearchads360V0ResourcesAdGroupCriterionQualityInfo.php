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

class GoogleAdsSearchads360V0ResourcesAdGroupCriterionQualityInfo extends \Google\Model
{
  /**
   * Output only. The quality score. This field may not be populated if Google
   * does not have enough information to determine a value.
   *
   * @var int
   */
  public $qualityScore;

  /**
   * Output only. The quality score. This field may not be populated if Google
   * does not have enough information to determine a value.
   *
   * @param int $qualityScore
   */
  public function setQualityScore($qualityScore)
  {
    $this->qualityScore = $qualityScore;
  }
  /**
   * @return int
   */
  public function getQualityScore()
  {
    return $this->qualityScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAdGroupCriterionQualityInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAdGroupCriterionQualityInfo');
