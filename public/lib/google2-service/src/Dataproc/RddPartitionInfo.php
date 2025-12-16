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

namespace Google\Service\Dataproc;

class RddPartitionInfo extends \Google\Collection
{
  protected $collection_key = 'executors';
  /**
   * @var string
   */
  public $blockName;
  /**
   * @var string
   */
  public $diskUsed;
  /**
   * @var string[]
   */
  public $executors;
  /**
   * @var string
   */
  public $memoryUsed;
  /**
   * @var string
   */
  public $storageLevel;

  /**
   * @param string $blockName
   */
  public function setBlockName($blockName)
  {
    $this->blockName = $blockName;
  }
  /**
   * @return string
   */
  public function getBlockName()
  {
    return $this->blockName;
  }
  /**
   * @param string $diskUsed
   */
  public function setDiskUsed($diskUsed)
  {
    $this->diskUsed = $diskUsed;
  }
  /**
   * @return string
   */
  public function getDiskUsed()
  {
    return $this->diskUsed;
  }
  /**
   * @param string[] $executors
   */
  public function setExecutors($executors)
  {
    $this->executors = $executors;
  }
  /**
   * @return string[]
   */
  public function getExecutors()
  {
    return $this->executors;
  }
  /**
   * @param string $memoryUsed
   */
  public function setMemoryUsed($memoryUsed)
  {
    $this->memoryUsed = $memoryUsed;
  }
  /**
   * @return string
   */
  public function getMemoryUsed()
  {
    return $this->memoryUsed;
  }
  /**
   * @param string $storageLevel
   */
  public function setStorageLevel($storageLevel)
  {
    $this->storageLevel = $storageLevel;
  }
  /**
   * @return string
   */
  public function getStorageLevel()
  {
    return $this->storageLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RddPartitionInfo::class, 'Google_Service_Dataproc_RddPartitionInfo');
