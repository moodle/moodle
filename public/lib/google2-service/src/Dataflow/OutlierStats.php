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

class OutlierStats extends \Google\Model
{
  /**
   * Number of values that are larger than the upper bound of the largest
   * bucket.
   *
   * @var string
   */
  public $overflowCount;
  /**
   * Mean of values in the overflow bucket.
   *
   * @var 
   */
  public $overflowMean;
  /**
   * Number of values that are smaller than the lower bound of the smallest
   * bucket.
   *
   * @var string
   */
  public $underflowCount;
  /**
   * Mean of values in the undeflow bucket.
   *
   * @var 
   */
  public $underflowMean;

  /**
   * Number of values that are larger than the upper bound of the largest
   * bucket.
   *
   * @param string $overflowCount
   */
  public function setOverflowCount($overflowCount)
  {
    $this->overflowCount = $overflowCount;
  }
  /**
   * @return string
   */
  public function getOverflowCount()
  {
    return $this->overflowCount;
  }
  public function setOverflowMean($overflowMean)
  {
    $this->overflowMean = $overflowMean;
  }
  public function getOverflowMean()
  {
    return $this->overflowMean;
  }
  /**
   * Number of values that are smaller than the lower bound of the smallest
   * bucket.
   *
   * @param string $underflowCount
   */
  public function setUnderflowCount($underflowCount)
  {
    $this->underflowCount = $underflowCount;
  }
  /**
   * @return string
   */
  public function getUnderflowCount()
  {
    return $this->underflowCount;
  }
  public function setUnderflowMean($underflowMean)
  {
    $this->underflowMean = $underflowMean;
  }
  public function getUnderflowMean()
  {
    return $this->underflowMean;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OutlierStats::class, 'Google_Service_Dataflow_OutlierStats');
