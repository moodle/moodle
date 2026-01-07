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

class GoogleCloudDiscoveryengineV1betaCheckGroundingResponseClaim extends \Google\Collection
{
  protected $collection_key = 'citationIndices';
  /**
   * @var int[]
   */
  public $citationIndices;
  /**
   * @var string
   */
  public $claimText;
  /**
   * @var int
   */
  public $endPos;
  /**
   * @var int
   */
  public $startPos;

  /**
   * @param int[]
   */
  public function setCitationIndices($citationIndices)
  {
    $this->citationIndices = $citationIndices;
  }
  /**
   * @return int[]
   */
  public function getCitationIndices()
  {
    return $this->citationIndices;
  }
  /**
   * @param string
   */
  public function setClaimText($claimText)
  {
    $this->claimText = $claimText;
  }
  /**
   * @return string
   */
  public function getClaimText()
  {
    return $this->claimText;
  }
  /**
   * @param int
   */
  public function setEndPos($endPos)
  {
    $this->endPos = $endPos;
  }
  /**
   * @return int
   */
  public function getEndPos()
  {
    return $this->endPos;
  }
  /**
   * @param int
   */
  public function setStartPos($startPos)
  {
    $this->startPos = $startPos;
  }
  /**
   * @return int
   */
  public function getStartPos()
  {
    return $this->startPos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaCheckGroundingResponseClaim::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaCheckGroundingResponseClaim');
