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

namespace Google\Service\Bigquery;

class PruningStats extends \Google\Model
{
  /**
   * The number of parallel inputs matched.
   *
   * @var string
   */
  public $postCmetaPruningParallelInputCount;
  /**
   * The number of partitions matched.
   *
   * @var string
   */
  public $postCmetaPruningPartitionCount;
  /**
   * The number of parallel inputs scanned.
   *
   * @var string
   */
  public $preCmetaPruningParallelInputCount;

  /**
   * The number of parallel inputs matched.
   *
   * @param string $postCmetaPruningParallelInputCount
   */
  public function setPostCmetaPruningParallelInputCount($postCmetaPruningParallelInputCount)
  {
    $this->postCmetaPruningParallelInputCount = $postCmetaPruningParallelInputCount;
  }
  /**
   * @return string
   */
  public function getPostCmetaPruningParallelInputCount()
  {
    return $this->postCmetaPruningParallelInputCount;
  }
  /**
   * The number of partitions matched.
   *
   * @param string $postCmetaPruningPartitionCount
   */
  public function setPostCmetaPruningPartitionCount($postCmetaPruningPartitionCount)
  {
    $this->postCmetaPruningPartitionCount = $postCmetaPruningPartitionCount;
  }
  /**
   * @return string
   */
  public function getPostCmetaPruningPartitionCount()
  {
    return $this->postCmetaPruningPartitionCount;
  }
  /**
   * The number of parallel inputs scanned.
   *
   * @param string $preCmetaPruningParallelInputCount
   */
  public function setPreCmetaPruningParallelInputCount($preCmetaPruningParallelInputCount)
  {
    $this->preCmetaPruningParallelInputCount = $preCmetaPruningParallelInputCount;
  }
  /**
   * @return string
   */
  public function getPreCmetaPruningParallelInputCount()
  {
    return $this->preCmetaPruningParallelInputCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PruningStats::class, 'Google_Service_Bigquery_PruningStats');
