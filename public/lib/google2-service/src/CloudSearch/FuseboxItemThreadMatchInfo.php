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

namespace Google\Service\CloudSearch;

class FuseboxItemThreadMatchInfo extends \Google\Collection
{
  protected $collection_key = 'matchingItemKey';
  /**
   * @var string
   */
  public $clusterId;
  /**
   * @var string
   */
  public $lastMatchingItemId;
  protected $lastMatchingItemKeyType = MultiKey::class;
  protected $lastMatchingItemKeyDataType = '';
  protected $matchingItemKeyType = MultiKey::class;
  protected $matchingItemKeyDataType = 'array';
  protected $rankType = Rank::class;
  protected $rankDataType = '';

  /**
   * @param string
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * @param string
   */
  public function setLastMatchingItemId($lastMatchingItemId)
  {
    $this->lastMatchingItemId = $lastMatchingItemId;
  }
  /**
   * @return string
   */
  public function getLastMatchingItemId()
  {
    return $this->lastMatchingItemId;
  }
  /**
   * @param MultiKey
   */
  public function setLastMatchingItemKey(MultiKey $lastMatchingItemKey)
  {
    $this->lastMatchingItemKey = $lastMatchingItemKey;
  }
  /**
   * @return MultiKey
   */
  public function getLastMatchingItemKey()
  {
    return $this->lastMatchingItemKey;
  }
  /**
   * @param MultiKey[]
   */
  public function setMatchingItemKey($matchingItemKey)
  {
    $this->matchingItemKey = $matchingItemKey;
  }
  /**
   * @return MultiKey[]
   */
  public function getMatchingItemKey()
  {
    return $this->matchingItemKey;
  }
  /**
   * @param Rank
   */
  public function setRank(Rank $rank)
  {
    $this->rank = $rank;
  }
  /**
   * @return Rank
   */
  public function getRank()
  {
    return $this->rank;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FuseboxItemThreadMatchInfo::class, 'Google_Service_CloudSearch_FuseboxItemThreadMatchInfo');
