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

namespace Google\Service\OracleDatabase;

class DbSystemShape extends \Google\Model
{
  /**
   * Optional. Number of cores per node.
   *
   * @var int
   */
  public $availableCoreCountPerNode;
  /**
   * Optional. Storage per storage server in terabytes.
   *
   * @var int
   */
  public $availableDataStorageTb;
  /**
   * Optional. Memory per database server node in gigabytes.
   *
   * @var int
   */
  public $availableMemoryPerNodeGb;
  /**
   * Optional. Maximum number of database servers.
   *
   * @var int
   */
  public $maxNodeCount;
  /**
   * Optional. Maximum number of storage servers.
   *
   * @var int
   */
  public $maxStorageCount;
  /**
   * Optional. Minimum core count per node.
   *
   * @var int
   */
  public $minCoreCountPerNode;
  /**
   * Optional. Minimum node storage per database server in gigabytes.
   *
   * @var int
   */
  public $minDbNodeStoragePerNodeGb;
  /**
   * Optional. Minimum memory per node in gigabytes.
   *
   * @var int
   */
  public $minMemoryPerNodeGb;
  /**
   * Optional. Minimum number of database servers.
   *
   * @var int
   */
  public $minNodeCount;
  /**
   * Optional. Minimum number of storage servers.
   *
   * @var int
   */
  public $minStorageCount;
  /**
   * Identifier. The name of the Database System Shape resource with the format:
   * projects/{project}/locations/{region}/dbSystemShapes/{db_system_shape}
   *
   * @var string
   */
  public $name;
  /**
   * Optional. shape
   *
   * @var string
   */
  public $shape;

  /**
   * Optional. Number of cores per node.
   *
   * @param int $availableCoreCountPerNode
   */
  public function setAvailableCoreCountPerNode($availableCoreCountPerNode)
  {
    $this->availableCoreCountPerNode = $availableCoreCountPerNode;
  }
  /**
   * @return int
   */
  public function getAvailableCoreCountPerNode()
  {
    return $this->availableCoreCountPerNode;
  }
  /**
   * Optional. Storage per storage server in terabytes.
   *
   * @param int $availableDataStorageTb
   */
  public function setAvailableDataStorageTb($availableDataStorageTb)
  {
    $this->availableDataStorageTb = $availableDataStorageTb;
  }
  /**
   * @return int
   */
  public function getAvailableDataStorageTb()
  {
    return $this->availableDataStorageTb;
  }
  /**
   * Optional. Memory per database server node in gigabytes.
   *
   * @param int $availableMemoryPerNodeGb
   */
  public function setAvailableMemoryPerNodeGb($availableMemoryPerNodeGb)
  {
    $this->availableMemoryPerNodeGb = $availableMemoryPerNodeGb;
  }
  /**
   * @return int
   */
  public function getAvailableMemoryPerNodeGb()
  {
    return $this->availableMemoryPerNodeGb;
  }
  /**
   * Optional. Maximum number of database servers.
   *
   * @param int $maxNodeCount
   */
  public function setMaxNodeCount($maxNodeCount)
  {
    $this->maxNodeCount = $maxNodeCount;
  }
  /**
   * @return int
   */
  public function getMaxNodeCount()
  {
    return $this->maxNodeCount;
  }
  /**
   * Optional. Maximum number of storage servers.
   *
   * @param int $maxStorageCount
   */
  public function setMaxStorageCount($maxStorageCount)
  {
    $this->maxStorageCount = $maxStorageCount;
  }
  /**
   * @return int
   */
  public function getMaxStorageCount()
  {
    return $this->maxStorageCount;
  }
  /**
   * Optional. Minimum core count per node.
   *
   * @param int $minCoreCountPerNode
   */
  public function setMinCoreCountPerNode($minCoreCountPerNode)
  {
    $this->minCoreCountPerNode = $minCoreCountPerNode;
  }
  /**
   * @return int
   */
  public function getMinCoreCountPerNode()
  {
    return $this->minCoreCountPerNode;
  }
  /**
   * Optional. Minimum node storage per database server in gigabytes.
   *
   * @param int $minDbNodeStoragePerNodeGb
   */
  public function setMinDbNodeStoragePerNodeGb($minDbNodeStoragePerNodeGb)
  {
    $this->minDbNodeStoragePerNodeGb = $minDbNodeStoragePerNodeGb;
  }
  /**
   * @return int
   */
  public function getMinDbNodeStoragePerNodeGb()
  {
    return $this->minDbNodeStoragePerNodeGb;
  }
  /**
   * Optional. Minimum memory per node in gigabytes.
   *
   * @param int $minMemoryPerNodeGb
   */
  public function setMinMemoryPerNodeGb($minMemoryPerNodeGb)
  {
    $this->minMemoryPerNodeGb = $minMemoryPerNodeGb;
  }
  /**
   * @return int
   */
  public function getMinMemoryPerNodeGb()
  {
    return $this->minMemoryPerNodeGb;
  }
  /**
   * Optional. Minimum number of database servers.
   *
   * @param int $minNodeCount
   */
  public function setMinNodeCount($minNodeCount)
  {
    $this->minNodeCount = $minNodeCount;
  }
  /**
   * @return int
   */
  public function getMinNodeCount()
  {
    return $this->minNodeCount;
  }
  /**
   * Optional. Minimum number of storage servers.
   *
   * @param int $minStorageCount
   */
  public function setMinStorageCount($minStorageCount)
  {
    $this->minStorageCount = $minStorageCount;
  }
  /**
   * @return int
   */
  public function getMinStorageCount()
  {
    return $this->minStorageCount;
  }
  /**
   * Identifier. The name of the Database System Shape resource with the format:
   * projects/{project}/locations/{region}/dbSystemShapes/{db_system_shape}
   *
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
   * Optional. shape
   *
   * @param string $shape
   */
  public function setShape($shape)
  {
    $this->shape = $shape;
  }
  /**
   * @return string
   */
  public function getShape()
  {
    return $this->shape;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbSystemShape::class, 'Google_Service_OracleDatabase_DbSystemShape');
