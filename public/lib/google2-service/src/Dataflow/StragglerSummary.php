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

namespace Google\Service\Dataflow;

class StragglerSummary extends \Google\Collection
{
  protected $collection_key = 'recentStragglers';
  protected $recentStragglersType = Straggler::class;
  protected $recentStragglersDataType = 'array';
  /**
   * Aggregated counts of straggler causes, keyed by the string representation
   * of the StragglerCause enum.
   *
   * @var string[]
   */
  public $stragglerCauseCount;
  /**
   * The total count of stragglers.
   *
   * @var string
   */
  public $totalStragglerCount;

  /**
   * The most recent stragglers.
   *
   * @param Straggler[] $recentStragglers
   */
  public function setRecentStragglers($recentStragglers)
  {
    $this->recentStragglers = $recentStragglers;
  }
  /**
   * @return Straggler[]
   */
  public function getRecentStragglers()
  {
    return $this->recentStragglers;
  }
  /**
   * Aggregated counts of straggler causes, keyed by the string representation
   * of the StragglerCause enum.
   *
   * @param string[] $stragglerCauseCount
   */
  public function setStragglerCauseCount($stragglerCauseCount)
  {
    $this->stragglerCauseCount = $stragglerCauseCount;
  }
  /**
   * @return string[]
   */
  public function getStragglerCauseCount()
  {
    return $this->stragglerCauseCount;
  }
  /**
   * The total count of stragglers.
   *
   * @param string $totalStragglerCount
   */
  public function setTotalStragglerCount($totalStragglerCount)
  {
    $this->totalStragglerCount = $totalStragglerCount;
  }
  /**
   * @return string
   */
  public function getTotalStragglerCount()
  {
    return $this->totalStragglerCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StragglerSummary::class, 'Google_Service_Dataflow_StragglerSummary');
