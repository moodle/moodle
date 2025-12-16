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

class DbNodeProperties extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that the resource is in provisioning state.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * Indicates that the resource is in available state.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that the resource is in updating state.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Indicates that the resource is in stopping state.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * Indicates that the resource is in stopped state.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * Indicates that the resource is in starting state.
   */
  public const STATE_STARTING = 'STARTING';
  /**
   * Indicates that the resource is in terminating state.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * Indicates that the resource is in terminated state.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * Indicates that the resource is in failed state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The date and time that the database node was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Local storage per database node.
   *
   * @var int
   */
  public $dbNodeStorageSizeGb;
  /**
   * Optional. Database server OCID.
   *
   * @var string
   */
  public $dbServerOcid;
  /**
   * Optional. DNS
   *
   * @var string
   */
  public $hostname;
  /**
   * Memory allocated in GBs.
   *
   * @var int
   */
  public $memorySizeGb;
  /**
   * Output only. OCID of database node.
   *
   * @var string
   */
  public $ocid;
  /**
   * Optional. OCPU count per database node.
   *
   * @var int
   */
  public $ocpuCount;
  /**
   * Output only. State of the database node.
   *
   * @var string
   */
  public $state;
  /**
   * Total CPU core count of the database node.
   *
   * @var int
   */
  public $totalCpuCoreCount;

  /**
   * Output only. The date and time that the database node was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Local storage per database node.
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
   * Optional. Database server OCID.
   *
   * @param string $dbServerOcid
   */
  public function setDbServerOcid($dbServerOcid)
  {
    $this->dbServerOcid = $dbServerOcid;
  }
  /**
   * @return string
   */
  public function getDbServerOcid()
  {
    return $this->dbServerOcid;
  }
  /**
   * Optional. DNS
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Memory allocated in GBs.
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
   * Output only. OCID of database node.
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
   * Optional. OCPU count per database node.
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
   * Output only. State of the database node.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, AVAILABLE, UPDATING,
   * STOPPING, STOPPED, STARTING, TERMINATING, TERMINATED, FAILED
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
   * Total CPU core count of the database node.
   *
   * @param int $totalCpuCoreCount
   */
  public function setTotalCpuCoreCount($totalCpuCoreCount)
  {
    $this->totalCpuCoreCount = $totalCpuCoreCount;
  }
  /**
   * @return int
   */
  public function getTotalCpuCoreCount()
  {
    return $this->totalCpuCoreCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbNodeProperties::class, 'Google_Service_OracleDatabase_DbNodeProperties');
