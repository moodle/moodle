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

class RddStorageInfo extends \Google\Collection
{
  protected $collection_key = 'partitions';
  protected $dataDistributionType = RddDataDistribution::class;
  protected $dataDistributionDataType = 'array';
  /**
   * @var string
   */
  public $diskUsed;
  /**
   * @var string
   */
  public $memoryUsed;
  /**
   * @var string
   */
  public $name;
  /**
   * @var int
   */
  public $numCachedPartitions;
  /**
   * @var int
   */
  public $numPartitions;
  protected $partitionsType = RddPartitionInfo::class;
  protected $partitionsDataType = 'array';
  /**
   * @var int
   */
  public $rddStorageId;
  /**
   * @var string
   */
  public $storageLevel;

  /**
   * @param RddDataDistribution[] $dataDistribution
   */
  public function setDataDistribution($dataDistribution)
  {
    $this->dataDistribution = $dataDistribution;
  }
  /**
   * @return RddDataDistribution[]
   */
  public function getDataDistribution()
  {
    return $this->dataDistribution;
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
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * @param int $numCachedPartitions
   */
  public function setNumCachedPartitions($numCachedPartitions)
  {
    $this->numCachedPartitions = $numCachedPartitions;
  }
  /**
   * @return int
   */
  public function getNumCachedPartitions()
  {
    return $this->numCachedPartitions;
  }
  /**
   * @param int $numPartitions
   */
  public function setNumPartitions($numPartitions)
  {
    $this->numPartitions = $numPartitions;
  }
  /**
   * @return int
   */
  public function getNumPartitions()
  {
    return $this->numPartitions;
  }
  /**
   * @param RddPartitionInfo[] $partitions
   */
  public function setPartitions($partitions)
  {
    $this->partitions = $partitions;
  }
  /**
   * @return RddPartitionInfo[]
   */
  public function getPartitions()
  {
    return $this->partitions;
  }
  /**
   * @param int $rddStorageId
   */
  public function setRddStorageId($rddStorageId)
  {
    $this->rddStorageId = $rddStorageId;
  }
  /**
   * @return int
   */
  public function getRddStorageId()
  {
    return $this->rddStorageId;
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
class_alias(RddStorageInfo::class, 'Google_Service_Dataproc_RddStorageInfo');
