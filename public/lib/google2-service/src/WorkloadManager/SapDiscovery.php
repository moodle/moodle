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

namespace Google\Service\WorkloadManager;

class SapDiscovery extends \Google\Model
{
  protected $applicationLayerType = SapDiscoveryComponent::class;
  protected $applicationLayerDataType = '';
  protected $databaseLayerType = SapDiscoveryComponent::class;
  protected $databaseLayerDataType = '';
  protected $metadataType = SapDiscoveryMetadata::class;
  protected $metadataDataType = '';
  /**
   * Optional. The GCP project number that this SapSystem belongs to.
   *
   * @var string
   */
  public $projectNumber;
  /**
   * Output only. A combination of database SID, database instance URI and
   * tenant DB name to make a unique identifier per-system.
   *
   * @var string
   */
  public $systemId;
  /**
   * Required. Unix timestamp this system has been updated last.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. Whether to use DR reconciliation or not.
   *
   * @var bool
   */
  public $useDrReconciliation;
  protected $workloadPropertiesType = SapDiscoveryWorkloadProperties::class;
  protected $workloadPropertiesDataType = '';

  /**
   * Optional. An SAP system may run without an application layer.
   *
   * @param SapDiscoveryComponent $applicationLayer
   */
  public function setApplicationLayer(SapDiscoveryComponent $applicationLayer)
  {
    $this->applicationLayer = $applicationLayer;
  }
  /**
   * @return SapDiscoveryComponent
   */
  public function getApplicationLayer()
  {
    return $this->applicationLayer;
  }
  /**
   * Required. An SAP System must have a database.
   *
   * @param SapDiscoveryComponent $databaseLayer
   */
  public function setDatabaseLayer(SapDiscoveryComponent $databaseLayer)
  {
    $this->databaseLayer = $databaseLayer;
  }
  /**
   * @return SapDiscoveryComponent
   */
  public function getDatabaseLayer()
  {
    return $this->databaseLayer;
  }
  /**
   * Optional. The metadata for SAP system discovery data.
   *
   * @param SapDiscoveryMetadata $metadata
   */
  public function setMetadata(SapDiscoveryMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return SapDiscoveryMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. The GCP project number that this SapSystem belongs to.
   *
   * @param string $projectNumber
   */
  public function setProjectNumber($projectNumber)
  {
    $this->projectNumber = $projectNumber;
  }
  /**
   * @return string
   */
  public function getProjectNumber()
  {
    return $this->projectNumber;
  }
  /**
   * Output only. A combination of database SID, database instance URI and
   * tenant DB name to make a unique identifier per-system.
   *
   * @param string $systemId
   */
  public function setSystemId($systemId)
  {
    $this->systemId = $systemId;
  }
  /**
   * @return string
   */
  public function getSystemId()
  {
    return $this->systemId;
  }
  /**
   * Required. Unix timestamp this system has been updated last.
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
   * Optional. Whether to use DR reconciliation or not.
   *
   * @param bool $useDrReconciliation
   */
  public function setUseDrReconciliation($useDrReconciliation)
  {
    $this->useDrReconciliation = $useDrReconciliation;
  }
  /**
   * @return bool
   */
  public function getUseDrReconciliation()
  {
    return $this->useDrReconciliation;
  }
  /**
   * Optional. The properties of the workload.
   *
   * @param SapDiscoveryWorkloadProperties $workloadProperties
   */
  public function setWorkloadProperties(SapDiscoveryWorkloadProperties $workloadProperties)
  {
    $this->workloadProperties = $workloadProperties;
  }
  /**
   * @return SapDiscoveryWorkloadProperties
   */
  public function getWorkloadProperties()
  {
    return $this->workloadProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscovery::class, 'Google_Service_WorkloadManager_SapDiscovery');
