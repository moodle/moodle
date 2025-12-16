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

class DbServerProperties extends \Google\Collection
{
  /**
   * Default unspecified value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that the resource is in creating state.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Indicates that the resource is in available state.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that the resource is in unavailable state.
   */
  public const STATE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Indicates that the resource is in deleting state.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Indicates that the resource is in deleted state.
   */
  public const STATE_DELETED = 'DELETED';
  protected $collection_key = 'dbNodeIds';
  /**
   * Output only. OCID of database nodes associated with the database server.
   *
   * @var string[]
   */
  public $dbNodeIds;
  /**
   * Optional. Local storage per VM.
   *
   * @var int
   */
  public $dbNodeStorageSizeGb;
  /**
   * Optional. Maximum local storage per VM.
   *
   * @var int
   */
  public $maxDbNodeStorageSizeGb;
  /**
   * Optional. Maximum memory allocated in GBs.
   *
   * @var int
   */
  public $maxMemorySizeGb;
  /**
   * Optional. Maximum OCPU count per database.
   *
   * @var int
   */
  public $maxOcpuCount;
  /**
   * Optional. Memory allocated in GBs.
   *
   * @var int
   */
  public $memorySizeGb;
  /**
   * Output only. OCID of database server.
   *
   * @var string
   */
  public $ocid;
  /**
   * Optional. OCPU count per database.
   *
   * @var int
   */
  public $ocpuCount;
  /**
   * Output only. State of the database server.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Vm count per database.
   *
   * @var int
   */
  public $vmCount;

  /**
   * Output only. OCID of database nodes associated with the database server.
   *
   * @param string[] $dbNodeIds
   */
  public function setDbNodeIds($dbNodeIds)
  {
    $this->dbNodeIds = $dbNodeIds;
  }
  /**
   * @return string[]
   */
  public function getDbNodeIds()
  {
    return $this->dbNodeIds;
  }
  /**
   * Optional. Local storage per VM.
   *
   * @param int $dbNodeStorageSizeGb
   */
  public function setDbNodeStorageSizeGb($dbNodeStorageSizeGb)
  {
    $this->dbNodeStorageSizeGb = $dbNodeStorageSizeGb;
  }
  /**
   * @return int
   */
  public function getDbNodeStorageSizeGb()
  {
    return $this->dbNodeStorageSizeGb;
  }
  /**
   * Optional. Maximum local storage per VM.
   *
   * @param int $maxDbNodeStorageSizeGb
   */
  public function setMaxDbNodeStorageSizeGb($maxDbNodeStorageSizeGb)
  {
    $this->maxDbNodeStorageSizeGb = $maxDbNodeStorageSizeGb;
  }
  /**
   * @return int
   */
  public function getMaxDbNodeStorageSizeGb()
  {
    return $this->maxDbNodeStorageSizeGb;
  }
  /**
   * Optional. Maximum memory allocated in GBs.
   *
   * @param int $maxMemorySizeGb
   */
  public function setMaxMemorySizeGb($maxMemorySizeGb)
  {
    $this->maxMemorySizeGb = $maxMemorySizeGb;
  }
  /**
   * @return int
   */
  public function getMaxMemorySizeGb()
  {
    return $this->maxMemorySizeGb;
  }
  /**
   * Optional. Maximum OCPU count per database.
   *
   * @param int $maxOcpuCount
   */
  public function setMaxOcpuCount($maxOcpuCount)
  {
    $this->maxOcpuCount = $maxOcpuCount;
  }
  /**
   * @return int
   */
  public function getMaxOcpuCount()
  {
    return $this->maxOcpuCount;
  }
  /**
   * Optional. Memory allocated in GBs.
   *
   * @param int $memorySizeGb
   */
  public function setMemorySizeGb($memorySizeGb)
  {
    $this->memorySizeGb = $memorySizeGb;
  }
  /**
   * @return int
   */
  public function getMemorySizeGb()
  {
    return $this->memorySizeGb;
  }
  /**
   * Output only. OCID of database server.
   *
   * @param string $ocid
   */
  public function setOcid($ocid)
  {
    $this->ocid = $ocid;
  }
  /**
   * @return string
   */
  public function getOcid()
  {
    return $this->ocid;
  }
  /**
   * Optional. OCPU count per database.
   *
   * @param int $ocpuCount
   */
  public function setOcpuCount($ocpuCount)
  {
    $this->ocpuCount = $ocpuCount;
  }
  /**
   * @return int
   */
  public function getOcpuCount()
  {
    return $this->ocpuCount;
  }
  /**
   * Output only. State of the database server.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, AVAILABLE, UNAVAILABLE,
   * DELETING, DELETED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. Vm count per database.
   *
   * @param int $vmCount
   */
  public function setVmCount($vmCount)
  {
    $this->vmCount = $vmCount;
  }
  /**
   * @return int
   */
  public function getVmCount()
  {
    return $this->vmCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbServerProperties::class, 'Google_Service_OracleDatabase_DbServerProperties');
