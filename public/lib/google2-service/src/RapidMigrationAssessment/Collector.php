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

namespace Google\Service\RapidMigrationAssessment;

class Collector extends \Google\Model
{
  /**
   * Collector state is not recognized.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Collector started to create, but hasn't been completed MC source creation
   * and db object creation.
   */
  public const STATE_STATE_INITIALIZING = 'STATE_INITIALIZING';
  /**
   * Collector has been created, MC source creation and db object creation
   * completed.
   */
  public const STATE_STATE_READY_TO_USE = 'STATE_READY_TO_USE';
  /**
   * Collector client has been registered with client.
   */
  public const STATE_STATE_REGISTERED = 'STATE_REGISTERED';
  /**
   * Collector client is actively scanning.
   */
  public const STATE_STATE_ACTIVE = 'STATE_ACTIVE';
  /**
   * Collector is not actively scanning.
   */
  public const STATE_STATE_PAUSED = 'STATE_PAUSED';
  /**
   * Collector is starting background job for deletion.
   */
  public const STATE_STATE_DELETING = 'STATE_DELETING';
  /**
   * Collector completed all tasks for deletion.
   */
  public const STATE_STATE_DECOMMISSIONED = 'STATE_DECOMMISSIONED';
  /**
   * Collector is in error state.
   */
  public const STATE_STATE_ERROR = 'STATE_ERROR';
  /**
   * Output only. Store cloud storage bucket name (which is a guid) created with
   * this Collector.
   *
   * @var string
   */
  public $bucket;
  /**
   * Output only. Client version.
   *
   * @var string
   */
  public $clientVersion;
  /**
   * How many days to collect data.
   *
   * @var int
   */
  public $collectionDays;
  /**
   * Output only. Create time stamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * User specified description of the Collector.
   *
   * @var string
   */
  public $description;
  /**
   * User specified name of the Collector.
   *
   * @var string
   */
  public $displayName;
  /**
   * Uri for EULA (End User License Agreement) from customer.
   *
   * @var string
   */
  public $eulaUri;
  /**
   * User specified expected asset count.
   *
   * @var string
   */
  public $expectedAssetCount;
  protected $guestOsScanType = GuestOsScan::class;
  protected $guestOsScanDataType = '';
  /**
   * Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * name of resource.
   *
   * @var string
   */
  public $name;
  /**
   * Service Account email used to ingest data to this Collector.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. State of the Collector.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Update time stamp.
   *
   * @var string
   */
  public $updateTime;
  protected $vsphereScanType = VSphereScan::class;
  protected $vsphereScanDataType = '';

  /**
   * Output only. Store cloud storage bucket name (which is a guid) created with
   * this Collector.
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * Output only. Client version.
   *
   * @param string $clientVersion
   */
  public function setClientVersion($clientVersion)
  {
    $this->clientVersion = $clientVersion;
  }
  /**
   * @return string
   */
  public function getClientVersion()
  {
    return $this->clientVersion;
  }
  /**
   * How many days to collect data.
   *
   * @param int $collectionDays
   */
  public function setCollectionDays($collectionDays)
  {
    $this->collectionDays = $collectionDays;
  }
  /**
   * @return int
   */
  public function getCollectionDays()
  {
    return $this->collectionDays;
  }
  /**
   * Output only. Create time stamp.
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
   * User specified description of the Collector.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * User specified name of the Collector.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Uri for EULA (End User License Agreement) from customer.
   *
   * @param string $eulaUri
   */
  public function setEulaUri($eulaUri)
  {
    $this->eulaUri = $eulaUri;
  }
  /**
   * @return string
   */
  public function getEulaUri()
  {
    return $this->eulaUri;
  }
  /**
   * User specified expected asset count.
   *
   * @param string $expectedAssetCount
   */
  public function setExpectedAssetCount($expectedAssetCount)
  {
    $this->expectedAssetCount = $expectedAssetCount;
  }
  /**
   * @return string
   */
  public function getExpectedAssetCount()
  {
    return $this->expectedAssetCount;
  }
  /**
   * Output only. Reference to MC Source Guest Os Scan.
   *
   * @param GuestOsScan $guestOsScan
   */
  public function setGuestOsScan(GuestOsScan $guestOsScan)
  {
    $this->guestOsScan = $guestOsScan;
  }
  /**
   * @return GuestOsScan
   */
  public function getGuestOsScan()
  {
    return $this->guestOsScan;
  }
  /**
   * Labels as key value pairs.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * name of resource.
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
   * Service Account email used to ingest data to this Collector.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. State of the Collector.
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_INITIALIZING, STATE_READY_TO_USE,
   * STATE_REGISTERED, STATE_ACTIVE, STATE_PAUSED, STATE_DELETING,
   * STATE_DECOMMISSIONED, STATE_ERROR
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
   * Output only. Update time stamp.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. Reference to MC Source vsphere_scan.
   *
   * @param VSphereScan $vsphereScan
   */
  public function setVsphereScan(VSphereScan $vsphereScan)
  {
    $this->vsphereScan = $vsphereScan;
  }
  /**
   * @return VSphereScan
   */
  public function getVsphereScan()
  {
    return $this->vsphereScan;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Collector::class, 'Google_Service_RapidMigrationAssessment_Collector');
