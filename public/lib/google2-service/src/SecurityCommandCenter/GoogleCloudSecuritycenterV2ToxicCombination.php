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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2ToxicCombination extends \Google\Collection
{
  protected $collection_key = 'relatedFindings';
  /**
   * The [Attack exposure score](https://cloud.google.com/security-command-
   * center/docs/attack-exposure-learn#attack_exposure_scores) of this toxic
   * combination. The score is a measure of how much this toxic combination
   * exposes one or more high-value resources to potential attack.
   *
   * @var 
   */
  public $attackExposureScore;
  /**
   * List of resource names of findings associated with this toxic combination.
   * For example, `organizations/123/sources/456/findings/789`.
   *
   * @var string[]
   */
  public $relatedFindings;

  public function setAttackExposureScore($attackExposureScore)
  {
    $this->attackExposureScore = $attackExposureScore;
  }
  public function getAttackExposureScore()
  {
    return $this->attackExposureScore;
  }
  /**
   * List of resource names of findings associated with this toxic combination.
   * For example, `organizations/123/sources/456/findings/789`.
   *
   * @param string[] $relatedFindings
   */
  public function setRelatedFindings($relatedFindings)
  {
    $this->relatedFindings = $relatedFindings;
  }
  /**
   * @return string[]
   */
  public function getRelatedFindings()
  {
    return $this->relatedFindings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2ToxicCombination::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2ToxicCombination');
