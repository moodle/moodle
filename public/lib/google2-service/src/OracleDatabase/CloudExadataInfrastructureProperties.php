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

class CloudExadataInfrastructureProperties extends \Google\Collection
{
  /**
   * Unspecified compute model.
   */
  public const COMPUTE_MODEL_COMPUTE_MODEL_UNSPECIFIED = 'COMPUTE_MODEL_UNSPECIFIED';
  /**
   * Abstract measure of compute resources. ECPUs are based on the number of
   * cores elastically allocated from a pool of compute and storage servers.
   */
  public const COMPUTE_MODEL_COMPUTE_MODEL_ECPU = 'COMPUTE_MODEL_ECPU';
  /**
   * Physical measure of compute resources. OCPUs are based on the physical core
   * of a processor.
   */
  public const COMPUTE_MODEL_COMPUTE_MODEL_OCPU = 'COMPUTE_MODEL_OCPU';
  /**
   * Default unspecified value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Exadata Infrastructure is being provisioned.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The Exadata Infrastructure is available for use.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * The Exadata Infrastructure is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The Exadata Infrastructure is being terminated.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * The Exadata Infrastructure is terminated.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * The Exadata Infrastructure is in failed state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The Exadata Infrastructure is in maintenance.
   */
  public const STATE_MAINTENANCE_IN_PROGRESS = 'MAINTENANCE_IN_PROGRESS';
  protected $collection_key = 'customerContacts';
  /**
   * Output only. The requested number of additional storage servers activated
   * for the Exadata Infrastructure.
   *
   * @var int
   */
  public $activatedStorageCount;
  /**
   * Output only. The requested number of additional storage servers for the
   * Exadata Infrastructure.
   *
   * @var int
   */
  public $additionalStorageCount;
  /**
   * Output only. The available storage can be allocated to the Exadata
   * Infrastructure resource, in gigabytes (GB).
   *
   * @var int
   */
  public $availableStorageSizeGb;
  /**
   * Optional. The number of compute servers for the Exadata Infrastructure.
   *
   * @var int
   */
  public $computeCount;
  /**
   * Output only. The compute model of the Exadata Infrastructure.
   *
   * @var string
   */
  public $computeModel;
  /**
   * Output only. The number of enabled CPU cores.
   *
   * @var int
   */
  public $cpuCount;
  protected $customerContactsType = CustomerContact::class;
  protected $customerContactsDataType = 'array';
  /**
   * Output only. Size, in terabytes, of the DATA disk group.
   *
   * @var 
   */
  public $dataStorageSizeTb;
  /**
   * Output only. The database server type of the Exadata Infrastructure.
   *
   * @var string
   */
  public $databaseServerType;
  /**
   * Output only. The local node storage allocated in GBs.
   *
   * @var int
   */
  public $dbNodeStorageSizeGb;
  /**
   * Output only. The software version of the database servers (dom0) in the
   * Exadata Infrastructure.
   *
   * @var string
   */
  public $dbServerVersion;
  protected $maintenanceWindowType = MaintenanceWindow::class;
  protected $maintenanceWindowDataType = '';
  /**
   * Output only. The total number of CPU cores available.
   *
   * @var int
   */
  public $maxCpuCount;
  /**
   * Output only. The total available DATA disk group size.
   *
   * @var 
   */
  public $maxDataStorageTb;
  /**
   * Output only. The total local node storage available in GBs.
   *
   * @var int
   */
  public $maxDbNodeStorageSizeGb;
  /**
   * Output only. The total memory available in GBs.
   *
   * @var int
   */
  public $maxMemoryGb;
  /**
   * Output only. The memory allocated in GBs.
   *
   * @var int
   */
  public $memorySizeGb;
  /**
   * Output only. The monthly software version of the database servers (dom0) in
   * the Exadata Infrastructure. Example: 20.1.15
   *
   * @var string
   */
  public $monthlyDbServerVersion;
  /**
   * Output only. The monthly software version of the storage servers (cells) in
   * the Exadata Infrastructure. Example: 20.1.15
   *
   * @var string
   */
  public $monthlyStorageServerVersion;
  /**
   * Output only. The OCID of the next maintenance run.
   *
   * @var string
   */
  public $nextMaintenanceRunId;
  /**
   * Output only. The time when the next maintenance run will occur.
   *
   * @var string
   */
  public $nextMaintenanceRunTime;
  /**
   * Output only. The time when the next security maintenance run will occur.
   *
   * @var string
   */
  public $nextSecurityMaintenanceRunTime;
  /**
   * Output only. Deep link to the OCI console to view this resource.
   *
   * @var string
   */
  public $ociUrl;
  /**
   * Output only. OCID of created infra. https://docs.oracle.com/en-
   * us/iaas/Content/General/Concepts/identifiers.htm#Oracle
   *
   * @var string
   */
  public $ocid;
  /**
   * Required. The shape of the Exadata Infrastructure. The shape determines the
   * amount of CPU, storage, and memory resources allocated to the instance.
   *
   * @var string
   */
  public $shape;
  /**
   * Output only. The current lifecycle state of the Exadata Infrastructure.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. The number of Cloud Exadata storage servers for the Exadata
   * Infrastructure.
   *
   * @var int
   */
  public $storageCount;
  /**
   * Output only. The storage server type of the Exadata Infrastructure.
   *
   * @var string
   */
  public $storageServerType;
  /**
   * Output only. The software version of the storage servers (cells) in the
   * Exadata Infrastructure.
   *
   * @var string
   */
  public $storageServerVersion;
  /**
   * Optional. The total storage allocated to the Exadata Infrastructure
   * resource, in gigabytes (GB).
   *
   * @var int
   */
  public $totalStorageSizeGb;

  /**
   * Output only. The requested number of additional storage servers activated
   * for the Exadata Infrastructure.
   *
   * @param int $activatedStorageCount
   */
  public function setActivatedStorageCount($activatedStorageCount)
  {
    $this->activatedStorageCount = $activatedStorageCount;
  }
  /**
   * @return int
   */
  public function getActivatedStorageCount()
  {
    return $this->activatedStorageCount;
  }
  /**
   * Output only. The requested number of additional storage servers for the
   * Exadata Infrastructure.
   *
   * @param int $additionalStorageCount
   */
  public function setAdditionalStorageCount($additionalStorageCount)
  {
    $this->additionalStorageCount = $additionalStorageCount;
  }
  /**
   * @return int
   */
  public function getAdditionalStorageCount()
  {
    return $this->additionalStorageCount;
  }
  /**
   * Output only. The available storage can be allocated to the Exadata
   * Infrastructure resource, in gigabytes (GB).
   *
   * @param int $availableStorageSizeGb
   */
  public function setAvailableStorageSizeGb($availableStorageSizeGb)
  {
    $this->availableStorageSizeGb = $availableStorageSizeGb;
  }
  /**
   * @return int
   */
  public function getAvailableStorageSizeGb()
  {
    return $this->availableStorageSizeGb;
  }
  /**
   * Optional. The number of compute servers for the Exadata Infrastructure.
   *
   * @param int $computeCount
   */
  public function setComputeCount($computeCount)
  {
    $this->computeCount = $computeCount;
  }
  /**
   * @return int
   */
  public function getComputeCount()
  {
    return $this->computeCount;
  }
  /**
   * Output only. The compute model of the Exadata Infrastructure.
   *
   * Accepted values: COMPUTE_MODEL_UNSPECIFIED, COMPUTE_MODEL_ECPU,
   * COMPUTE_MODEL_OCPU
   *
   * @param self::COMPUTE_MODEL_* $computeModel
   */
  public function setComputeModel($computeModel)
  {
    $this->computeModel = $computeModel;
  }
  /**
   * @return self::COMPUTE_MODEL_*
   */
  public function getComputeModel()
  {
    return $this->computeModel;
  }
  /**
   * Output only. The number of enabled CPU cores.
   *
   * @param int $cpuCount
   */
  public function setCpuCount($cpuCount)
  {
    $this->cpuCount = $cpuCount;
  }
  /**
   * @return int
   */
  public function getCpuCount()
  {
    return $this->cpuCount;
  }
  /**
   * Optional. The list of customer contacts.
   *
   * @param CustomerContact[] $customerContacts
   */
  public function setCustomerContacts($customerContacts)
  {
    $this->customerContacts = $customerContacts;
  }
  /**
   * @return CustomerContact[]
   */
  public function getCustomerContacts()
  {
    return $this->customerContacts;
  }
  public function setDataStorageSizeTb($dataStorageSizeTb)
  {
    $this->dataStorageSizeTb = $dataStorageSizeTb;
  }
  public function getDataStorageSizeTb()
  {
    return $this->dataStorageSizeTb;
  }
  /**
   * Output only. The database server type of the Exadata Infrastructure.
   *
   * @param string $databaseServerType
   */
  public function setDatabaseServerType($databaseServerType)
  {
    $this->databaseServerType = $databaseServerType;
  }
  /**
   * @return string
   */
  public function getDatabaseServerType()
  {
    return $this->databaseServerType;
  }
  /**
   * Output only. The local node storage allocated in GBs.
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
   * Output only. The software version of the database servers (dom0) in the
   * Exadata Infrastructure.
   *
   * @param string $dbServerVersion
   */
  public function setDbServerVersion($dbServerVersion)
  {
    $this->dbServerVersion = $dbServerVersion;
  }
  /**
   * @return string
   */
  public function getDbServerVersion()
  {
    return $this->dbServerVersion;
  }
  /**
   * Optional. Maintenance window for repair.
   *
   * @param MaintenanceWindow $maintenanceWindow
   */
  public function setMaintenanceWindow(MaintenanceWindow $maintenanceWindow)
  {
    $this->maintenanceWindow = $maintenanceWindow;
  }
  /**
   * @return MaintenanceWindow
   */
  public function getMaintenanceWindow()
  {
    return $this->maintenanceWindow;
  }
  /**
   * Output only. The total number of CPU cores available.
   *
   * @param int $maxCpuCount
   */
  public function setMaxCpuCount($maxCpuCount)
  {
    $this->maxCpuCount = $maxCpuCount;
  }
  /**
   * @return int
   */
  public function getMaxCpuCount()
  {
    return $this->maxCpuCount;
  }
  public function setMaxDataStorageTb($maxDataStorageTb)
  {
    $this->maxDataStorageTb = $maxDataStorageTb;
  }
  public function getMaxDataStorageTb()
  {
    return $this->maxDataStorageTb;
  }
  /**
   * Output only. The total local node storage available in GBs.
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
   * Output only. The total memory available in GBs.
   *
   * @param int $maxMemoryGb
   */
  public function setMaxMemoryGb($maxMemoryGb)
  {
    $this->maxMemoryGb = $maxMemoryGb;
  }
  /**
   * @return int
   */
  public function getMaxMemoryGb()
  {
    return $this->maxMemoryGb;
  }
  /**
   * Output only. The memory allocated in GBs.
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
   * Output only. The monthly software version of the database servers (dom0) in
   * the Exadata Infrastructure. Example: 20.1.15
   *
   * @param string $monthlyDbServerVersion
   */
  public function setMonthlyDbServerVersion($monthlyDbServerVersion)
  {
    $this->monthlyDbServerVersion = $monthlyDbServerVersion;
  }
  /**
   * @return string
   */
  public function getMonthlyDbServerVersion()
  {
    return $this->monthlyDbServerVersion;
  }
  /**
   * Output only. The monthly software version of the storage servers (cells) in
   * the Exadata Infrastructure. Example: 20.1.15
   *
   * @param string $monthlyStorageServerVersion
   */
  public function setMonthlyStorageServerVersion($monthlyStorageServerVersion)
  {
    $this->monthlyStorageServerVersion = $monthlyStorageServerVersion;
  }
  /**
   * @return string
   */
  public function getMonthlyStorageServerVersion()
  {
    return $this->monthlyStorageServerVersion;
  }
  /**
   * Output only. The OCID of the next maintenance run.
   *
   * @param string $nextMaintenanceRunId
   */
  public function setNextMaintenanceRunId($nextMaintenanceRunId)
  {
    $this->nextMaintenanceRunId = $nextMaintenanceRunId;
  }
  /**
   * @return string
   */
  public function getNextMaintenanceRunId()
  {
    return $this->nextMaintenanceRunId;
  }
  /**
   * Output only. The time when the next maintenance run will occur.
   *
   * @param string $nextMaintenanceRunTime
   */
  public function setNextMaintenanceRunTime($nextMaintenanceRunTime)
  {
    $this->nextMaintenanceRunTime = $nextMaintenanceRunTime;
  }
  /**
   * @return string
   */
  public function getNextMaintenanceRunTime()
  {
    return $this->nextMaintenanceRunTime;
  }
  /**
   * Output only. The time when the next security maintenance run will occur.
   *
   * @param string $nextSecurityMaintenanceRunTime
   */
  public function setNextSecurityMaintenanceRunTime($nextSecurityMaintenanceRunTime)
  {
    $this->nextSecurityMaintenanceRunTime = $nextSecurityMaintenanceRunTime;
  }
  /**
   * @return string
   */
  public function getNextSecurityMaintenanceRunTime()
  {
    return $this->nextSecurityMaintenanceRunTime;
  }
  /**
   * Output only. Deep link to the OCI console to view this resource.
   *
   * @param string $ociUrl
   */
  public function setOciUrl($ociUrl)
  {
    $this->ociUrl = $ociUrl;
  }
  /**
   * @return string
   */
  public function getOciUrl()
  {
    return $this->ociUrl;
  }
  /**
   * Output only. OCID of created infra. https://docs.oracle.com/en-
   * us/iaas/Content/General/Concepts/identifiers.htm#Oracle
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
   * Required. The shape of the Exadata Infrastructure. The shape determines the
   * amount of CPU, storage, and memory resources allocated to the instance.
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
  /**
   * Output only. The current lifecycle state of the Exadata Infrastructure.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, AVAILABLE, UPDATING,
   * TERMINATING, TERMINATED, FAILED, MAINTENANCE_IN_PROGRESS
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
   * Optional. The number of Cloud Exadata storage servers for the Exadata
   * Infrastructure.
   *
   * @param int $storageCount
   */
  public function setStorageCount($storageCount)
  {
    $this->storageCount = $storageCount;
  }
  /**
   * @return int
   */
  public function getStorageCount()
  {
    return $this->storageCount;
  }
  /**
   * Output only. The storage server type of the Exadata Infrastructure.
   *
   * @param string $storageServerType
   */
  public function setStorageServerType($storageServerType)
  {
    $this->storageServerType = $storageServerType;
  }
  /**
   * @return string
   */
  public function getStorageServerType()
  {
    return $this->storageServerType;
  }
  /**
   * Output only. The software version of the storage servers (cells) in the
   * Exadata Infrastructure.
   *
   * @param string $storageServerVersion
   */
  public function setStorageServerVersion($storageServerVersion)
  {
    $this->storageServerVersion = $storageServerVersion;
  }
  /**
   * @return string
   */
  public function getStorageServerVersion()
  {
    return $this->storageServerVersion;
  }
  /**
   * Optional. The total storage allocated to the Exadata Infrastructure
   * resource, in gigabytes (GB).
   *
   * @param int $totalStorageSizeGb
   */
  public function setTotalStorageSizeGb($totalStorageSizeGb)
  {
    $this->totalStorageSizeGb = $totalStorageSizeGb;
  }
  /**
   * @return int
   */
  public function getTotalStorageSizeGb()
  {
    return $this->totalStorageSizeGb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudExadataInfrastructureProperties::class, 'Google_Service_OracleDatabase_CloudExadataInfrastructureProperties');
