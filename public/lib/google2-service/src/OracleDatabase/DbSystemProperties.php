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

class DbSystemProperties extends \Google\Collection
{
  /**
   * The compute model is unspecified.
   */
  public const COMPUTE_MODEL_COMPUTE_MODEL_UNSPECIFIED = 'COMPUTE_MODEL_UNSPECIFIED';
  /**
   * The compute model is virtual.
   */
  public const COMPUTE_MODEL_ECPU = 'ECPU';
  /**
   * The compute model is physical.
   */
  public const COMPUTE_MODEL_OCPU = 'OCPU';
  /**
   * The database edition is unspecified.
   */
  public const DATABASE_EDITION_DB_SYSTEM_DATABASE_EDITION_UNSPECIFIED = 'DB_SYSTEM_DATABASE_EDITION_UNSPECIFIED';
  /**
   * The database edition is Standard.
   */
  public const DATABASE_EDITION_STANDARD_EDITION = 'STANDARD_EDITION';
  /**
   * The database edition is Enterprise.
   */
  public const DATABASE_EDITION_ENTERPRISE_EDITION = 'ENTERPRISE_EDITION';
  /**
   * The database edition is Enterprise Edition.
   */
  public const DATABASE_EDITION_ENTERPRISE_EDITION_HIGH_PERFORMANCE = 'ENTERPRISE_EDITION_HIGH_PERFORMANCE';
  /**
   * The license model is unspecified.
   */
  public const LICENSE_MODEL_LICENSE_MODEL_UNSPECIFIED = 'LICENSE_MODEL_UNSPECIFIED';
  /**
   * The license model is included.
   */
  public const LICENSE_MODEL_LICENSE_INCLUDED = 'LICENSE_INCLUDED';
  /**
   * The license model is bring your own license.
   */
  public const LICENSE_MODEL_BRING_YOUR_OWN_LICENSE = 'BRING_YOUR_OWN_LICENSE';
  /**
   * Default unspecified value.
   */
  public const LIFECYCLE_STATE_DB_SYSTEM_LIFECYCLE_STATE_UNSPECIFIED = 'DB_SYSTEM_LIFECYCLE_STATE_UNSPECIFIED';
  /**
   * Indicates that the resource is in provisioning state.
   */
  public const LIFECYCLE_STATE_PROVISIONING = 'PROVISIONING';
  /**
   * Indicates that the resource is in available state.
   */
  public const LIFECYCLE_STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that the resource is in updating state.
   */
  public const LIFECYCLE_STATE_UPDATING = 'UPDATING';
  /**
   * Indicates that the resource is in terminating state.
   */
  public const LIFECYCLE_STATE_TERMINATING = 'TERMINATING';
  /**
   * Indicates that the resource is in terminated state.
   */
  public const LIFECYCLE_STATE_TERMINATED = 'TERMINATED';
  /**
   * Indicates that the resource is in failed state.
   */
  public const LIFECYCLE_STATE_FAILED = 'FAILED';
  /**
   * Indicates that the resource has been migrated.
   */
  public const LIFECYCLE_STATE_MIGRATED = 'MIGRATED';
  /**
   * Indicates that the resource is in maintenance in progress state.
   */
  public const LIFECYCLE_STATE_MAINTENANCE_IN_PROGRESS = 'MAINTENANCE_IN_PROGRESS';
  /**
   * Indicates that the resource needs attention.
   */
  public const LIFECYCLE_STATE_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * Indicates that the resource is upgrading.
   */
  public const LIFECYCLE_STATE_UPGRADING = 'UPGRADING';
  protected $collection_key = 'sshPublicKeys';
  /**
   * Required. The number of CPU cores to enable for the DbSystem.
   *
   * @var int
   */
  public $computeCount;
  /**
   * Optional. The compute model of the DbSystem.
   *
   * @var string
   */
  public $computeModel;
  protected $dataCollectionOptionsType = DataCollectionOptionsDbSystem::class;
  protected $dataCollectionOptionsDataType = '';
  /**
   * Optional. The data storage size in GB that is currently available to
   * DbSystems.
   *
   * @var int
   */
  public $dataStorageSizeGb;
  /**
   * Required. The database edition of the DbSystem.
   *
   * @var string
   */
  public $databaseEdition;
  protected $dbHomeType = DbHome::class;
  protected $dbHomeDataType = '';
  protected $dbSystemOptionsType = DbSystemOptions::class;
  protected $dbSystemOptionsDataType = '';
  /**
   * Optional. The host domain name of the DbSystem.
   *
   * @var string
   */
  public $domain;
  /**
   * Output only. The hostname of the DbSystem.
   *
   * @var string
   */
  public $hostname;
  /**
   * Optional. Prefix for DB System host names.
   *
   * @var string
   */
  public $hostnamePrefix;
  /**
   * Required. The initial data storage size in GB.
   *
   * @var int
   */
  public $initialDataStorageSizeGb;
  /**
   * Required. The license model of the DbSystem.
   *
   * @var string
   */
  public $licenseModel;
  /**
   * Output only. State of the DbSystem.
   *
   * @var string
   */
  public $lifecycleState;
  /**
   * Optional. The memory size in GB.
   *
   * @var int
   */
  public $memorySizeGb;
  /**
   * Optional. The number of nodes in the DbSystem.
   *
   * @var int
   */
  public $nodeCount;
  /**
   * Output only. OCID of the DbSystem.
   *
   * @var string
   */
  public $ocid;
  /**
   * Optional. The private IP address of the DbSystem.
   *
   * @var string
   */
  public $privateIp;
  /**
   * Optional. The reco/redo storage size in GB.
   *
   * @var int
   */
  public $recoStorageSizeGb;
  /**
   * Required. Shape of DB System.
   *
   * @var string
   */
  public $shape;
  /**
   * Required. SSH public keys to be stored with the DbSystem.
   *
   * @var string[]
   */
  public $sshPublicKeys;
  protected $timeZoneType = TimeZone::class;
  protected $timeZoneDataType = '';

  /**
   * Required. The number of CPU cores to enable for the DbSystem.
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
   * Optional. The compute model of the DbSystem.
   *
   * Accepted values: COMPUTE_MODEL_UNSPECIFIED, ECPU, OCPU
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
   * Optional. Data collection options for diagnostics.
   *
   * @param DataCollectionOptionsDbSystem $dataCollectionOptions
   */
  public function setDataCollectionOptions(DataCollectionOptionsDbSystem $dataCollectionOptions)
  {
    $this->dataCollectionOptions = $dataCollectionOptions;
  }
  /**
   * @return DataCollectionOptionsDbSystem
   */
  public function getDataCollectionOptions()
  {
    return $this->dataCollectionOptions;
  }
  /**
   * Optional. The data storage size in GB that is currently available to
   * DbSystems.
   *
   * @param int $dataStorageSizeGb
   */
  public function setDataStorageSizeGb($dataStorageSizeGb)
  {
    $this->dataStorageSizeGb = $dataStorageSizeGb;
  }
  /**
   * @return int
   */
  public function getDataStorageSizeGb()
  {
    return $this->dataStorageSizeGb;
  }
  /**
   * Required. The database edition of the DbSystem.
   *
   * Accepted values: DB_SYSTEM_DATABASE_EDITION_UNSPECIFIED, STANDARD_EDITION,
   * ENTERPRISE_EDITION, ENTERPRISE_EDITION_HIGH_PERFORMANCE
   *
   * @param self::DATABASE_EDITION_* $databaseEdition
   */
  public function setDatabaseEdition($databaseEdition)
  {
    $this->databaseEdition = $databaseEdition;
  }
  /**
   * @return self::DATABASE_EDITION_*
   */
  public function getDatabaseEdition()
  {
    return $this->databaseEdition;
  }
  /**
   * Optional. Details for creating a Database Home.
   *
   * @param DbHome $dbHome
   */
  public function setDbHome(DbHome $dbHome)
  {
    $this->dbHome = $dbHome;
  }
  /**
   * @return DbHome
   */
  public function getDbHome()
  {
    return $this->dbHome;
  }
  /**
   * Optional. The options for the DbSystem.
   *
   * @param DbSystemOptions $dbSystemOptions
   */
  public function setDbSystemOptions(DbSystemOptions $dbSystemOptions)
  {
    $this->dbSystemOptions = $dbSystemOptions;
  }
  /**
   * @return DbSystemOptions
   */
  public function getDbSystemOptions()
  {
    return $this->dbSystemOptions;
  }
  /**
   * Optional. The host domain name of the DbSystem.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Output only. The hostname of the DbSystem.
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
   * Optional. Prefix for DB System host names.
   *
   * @param string $hostnamePrefix
   */
  public function setHostnamePrefix($hostnamePrefix)
  {
    $this->hostnamePrefix = $hostnamePrefix;
  }
  /**
   * @return string
   */
  public function getHostnamePrefix()
  {
    return $this->hostnamePrefix;
  }
  /**
   * Required. The initial data storage size in GB.
   *
   * @param int $initialDataStorageSizeGb
   */
  public function setInitialDataStorageSizeGb($initialDataStorageSizeGb)
  {
    $this->initialDataStorageSizeGb = $initialDataStorageSizeGb;
  }
  /**
   * @return int
   */
  public function getInitialDataStorageSizeGb()
  {
    return $this->initialDataStorageSizeGb;
  }
  /**
   * Required. The license model of the DbSystem.
   *
   * Accepted values: LICENSE_MODEL_UNSPECIFIED, LICENSE_INCLUDED,
   * BRING_YOUR_OWN_LICENSE
   *
   * @param self::LICENSE_MODEL_* $licenseModel
   */
  public function setLicenseModel($licenseModel)
  {
    $this->licenseModel = $licenseModel;
  }
  /**
   * @return self::LICENSE_MODEL_*
   */
  public function getLicenseModel()
  {
    return $this->licenseModel;
  }
  /**
   * Output only. State of the DbSystem.
   *
   * Accepted values: DB_SYSTEM_LIFECYCLE_STATE_UNSPECIFIED, PROVISIONING,
   * AVAILABLE, UPDATING, TERMINATING, TERMINATED, FAILED, MIGRATED,
   * MAINTENANCE_IN_PROGRESS, NEEDS_ATTENTION, UPGRADING
   *
   * @param self::LIFECYCLE_STATE_* $lifecycleState
   */
  public function setLifecycleState($lifecycleState)
  {
    $this->lifecycleState = $lifecycleState;
  }
  /**
   * @return self::LIFECYCLE_STATE_*
   */
  public function getLifecycleState()
  {
    return $this->lifecycleState;
  }
  /**
   * Optional. The memory size in GB.
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
   * Optional. The number of nodes in the DbSystem.
   *
   * @param int $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return int
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * Output only. OCID of the DbSystem.
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
   * Optional. The private IP address of the DbSystem.
   *
   * @param string $privateIp
   */
  public function setPrivateIp($privateIp)
  {
    $this->privateIp = $privateIp;
  }
  /**
   * @return string
   */
  public function getPrivateIp()
  {
    return $this->privateIp;
  }
  /**
   * Optional. The reco/redo storage size in GB.
   *
   * @param int $recoStorageSizeGb
   */
  public function setRecoStorageSizeGb($recoStorageSizeGb)
  {
    $this->recoStorageSizeGb = $recoStorageSizeGb;
  }
  /**
   * @return int
   */
  public function getRecoStorageSizeGb()
  {
    return $this->recoStorageSizeGb;
  }
  /**
   * Required. Shape of DB System.
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
   * Required. SSH public keys to be stored with the DbSystem.
   *
   * @param string[] $sshPublicKeys
   */
  public function setSshPublicKeys($sshPublicKeys)
  {
    $this->sshPublicKeys = $sshPublicKeys;
  }
  /**
   * @return string[]
   */
  public function getSshPublicKeys()
  {
    return $this->sshPublicKeys;
  }
  /**
   * Optional. Time zone of the DbSystem.
   *
   * @param TimeZone $timeZone
   */
  public function setTimeZone(TimeZone $timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return TimeZone
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbSystemProperties::class, 'Google_Service_OracleDatabase_DbSystemProperties');
