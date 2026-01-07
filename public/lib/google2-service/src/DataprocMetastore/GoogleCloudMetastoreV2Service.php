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

namespace Google\Service\DataprocMetastore;

class GoogleCloudMetastoreV2Service extends \Google\Collection
{
  protected $collection_key = 'endpoints';
  /**
   * @var string
   */
  public $createTime;
  protected $encryptionConfigType = GoogleCloudMetastoreV2EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  protected $endpointsType = GoogleCloudMetastoreV2Endpoint::class;
  protected $endpointsDataType = 'array';
  protected $hiveMetastoreConfigType = GoogleCloudMetastoreV2HiveMetastoreConfig::class;
  protected $hiveMetastoreConfigDataType = '';
  /**
   * @var string[]
   */
  public $labels;
  protected $metadataIntegrationType = GoogleCloudMetastoreV2MetadataIntegration::class;
  protected $metadataIntegrationDataType = '';
  /**
   * @var string
   */
  public $name;
  protected $scalingConfigType = GoogleCloudMetastoreV2ScalingConfig::class;
  protected $scalingConfigDataType = '';
  protected $scheduledBackupType = GoogleCloudMetastoreV2ScheduledBackup::class;
  protected $scheduledBackupDataType = '';
  /**
   * @var string
   */
  public $state;
  /**
   * @var string
   */
  public $stateMessage;
  /**
   * @var string
   */
  public $uid;
  /**
   * @var string
   */
  public $updateTime;
  /**
   * @var string
   */
  public $warehouseGcsUri;

  /**
   * @param string
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
   * @param GoogleCloudMetastoreV2EncryptionConfig
   */
  public function setEncryptionConfig(GoogleCloudMetastoreV2EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return GoogleCloudMetastoreV2EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * @param GoogleCloudMetastoreV2Endpoint[]
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return GoogleCloudMetastoreV2Endpoint[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * @param GoogleCloudMetastoreV2HiveMetastoreConfig
   */
  public function setHiveMetastoreConfig(GoogleCloudMetastoreV2HiveMetastoreConfig $hiveMetastoreConfig)
  {
    $this->hiveMetastoreConfig = $hiveMetastoreConfig;
  }
  /**
   * @return GoogleCloudMetastoreV2HiveMetastoreConfig
   */
  public function getHiveMetastoreConfig()
  {
    return $this->hiveMetastoreConfig;
  }
  /**
   * @param string[]
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
   * @param GoogleCloudMetastoreV2MetadataIntegration
   */
  public function setMetadataIntegration(GoogleCloudMetastoreV2MetadataIntegration $metadataIntegration)
  {
    $this->metadataIntegration = $metadataIntegration;
  }
  /**
   * @return GoogleCloudMetastoreV2MetadataIntegration
   */
  public function getMetadataIntegration()
  {
    return $this->metadataIntegration;
  }
  /**
   * @param string
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
   * @param GoogleCloudMetastoreV2ScalingConfig
   */
  public function setScalingConfig(GoogleCloudMetastoreV2ScalingConfig $scalingConfig)
  {
    $this->scalingConfig = $scalingConfig;
  }
  /**
   * @return GoogleCloudMetastoreV2ScalingConfig
   */
  public function getScalingConfig()
  {
    return $this->scalingConfig;
  }
  /**
   * @param GoogleCloudMetastoreV2ScheduledBackup
   */
  public function setScheduledBackup(GoogleCloudMetastoreV2ScheduledBackup $scheduledBackup)
  {
    $this->scheduledBackup = $scheduledBackup;
  }
  /**
   * @return GoogleCloudMetastoreV2ScheduledBackup
   */
  public function getScheduledBackup()
  {
    return $this->scheduledBackup;
  }
  /**
   * @param string
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * @param string
   */
  public function setStateMessage($stateMessage)
  {
    $this->stateMessage = $stateMessage;
  }
  /**
   * @return string
   */
  public function getStateMessage()
  {
    return $this->stateMessage;
  }
  /**
   * @param string
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setWarehouseGcsUri($warehouseGcsUri)
  {
    $this->warehouseGcsUri = $warehouseGcsUri;
  }
  /**
   * @return string
   */
  public function getWarehouseGcsUri()
  {
    return $this->warehouseGcsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMetastoreV2Service::class, 'Google_Service_DataprocMetastore_GoogleCloudMetastoreV2Service');
