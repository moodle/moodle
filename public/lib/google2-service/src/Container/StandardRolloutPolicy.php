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

namespace Google\Service\Container;

class StandardRolloutPolicy extends \Google\Model
{
  /**
   * Number of blue nodes to drain in a batch.
   *
   * @var int
   */
  public $batchNodeCount;
  /**
   * Percentage of the blue pool nodes to drain in a batch. The range of this
   * field should be (0.0, 1.0].
   *
   * @var float
   */
  public $batchPercentage;
  /**
   * Soak time after each batch gets drained. Default to zero.
   *
   * @var string
   */
  public $batchSoakDuration;

  /**
   * Number of blue nodes to drain in a batch.
   *
   * @param int $batchNodeCount
   */
  public function setBatchNodeCount($batchNodeCount)
  {
    $this->batchNodeCount = $batchNodeCount;
  }
  /**
   * @return int
   */
  public function getBatchNodeCount()
  {
    return $this->batchNodeCount;
  }
  /**
   * Percentage of the blue pool nodes to drain in a batch. The range of this
   * field should be (0.0, 1.0].
   *
   * @param float $batchPercentage
   */
  public function setBatchPercentage($batchPercentage)
  {
    $this->batchPercentage = $batchPercentage;
  }
  /**
   * @return float
   */
  public function getBatchPercentage()
  {
    return $this->batchPercentage;
  }
  /**
   * Soak time after each batch gets drained. Default to zero.
   *
   * @param string $batchSoakDuration
   */
  public function setBatchSoakDuration($batchSoakDuration)
  {
    $this->batchSoakDuration = $batchSoakDuration;
  }
  /**
   * @return string
   */
  public function getBatchSoakDuration()
  {
    return $this->batchSoakDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StandardRolloutPolicy::class, 'Google_Service_Container_StandardRolloutPolicy');
