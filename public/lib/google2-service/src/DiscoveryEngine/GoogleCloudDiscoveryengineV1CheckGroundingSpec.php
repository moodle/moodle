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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1CheckGroundingSpec extends \Google\Model
{
  /**
   * The threshold (in [0,1]) used for determining whether a fact must be cited
   * for a claim in the answer candidate. Choosing a higher threshold will lead
   * to fewer but very strong citations, while choosing a lower threshold may
   * lead to more but somewhat weaker citations. If unset, the threshold will
   * default to 0.6.
   *
   * @var 
   */
  public $citationThreshold;
  /**
   * The control flag that enables claim-level grounding score in the response.
   *
   * @var bool
   */
  public $enableClaimLevelScore;

  public function setCitationThreshold($citationThreshold)
  {
    $this->citationThreshold = $citationThreshold;
  }
  public function getCitationThreshold()
  {
    return $this->citationThreshold;
  }
  /**
   * The control flag that enables claim-level grounding score in the response.
   *
   * @param bool $enableClaimLevelScore
   */
  public function setEnableClaimLevelScore($enableClaimLevelScore)
  {
    $this->enableClaimLevelScore = $enableClaimLevelScore;
  }
  /**
   * @return bool
   */
  public function getEnableClaimLevelScore()
  {
    return $this->enableClaimLevelScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1CheckGroundingSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CheckGroundingSpec');
