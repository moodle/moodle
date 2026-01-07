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

namespace Google\Service\CloudAlloyDBAdmin;

class ClusterUpgradeDetails extends \Google\Collection
{
  /**
   * The type of the cluster is unknown.
   */
  public const CLUSTER_TYPE_CLUSTER_TYPE_UNSPECIFIED = 'CLUSTER_TYPE_UNSPECIFIED';
  /**
   * Primary cluster that support read and write operations.
   */
  public const CLUSTER_TYPE_PRIMARY = 'PRIMARY';
  /**
   * Secondary cluster that is replicating from another region. This only
   * supports read.
   */
  public const CLUSTER_TYPE_SECONDARY = 'SECONDARY';
  /**
   * This is an unknown database version.
   */
  public const DATABASE_VERSION_DATABASE_VERSION_UNSPECIFIED = 'DATABASE_VERSION_UNSPECIFIED';
  /**
   * DEPRECATED - The database version is Postgres 13.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_POSTGRES_13 = 'POSTGRES_13';
  /**
   * The database version is Postgres 14.
   */
  public const DATABASE_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * The database version is Postgres 15.
   */
  public const DATABASE_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * The database version is Postgres 16.
   */
  public const DATABASE_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The database version is Postgres 17.
   */
  public const DATABASE_VERSION_POSTGRES_17 = 'POSTGRES_17';
  /**
   * Unspecified status.
   */
  public const UPGRADE_STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Not started.
   */
  public const UPGRADE_STATUS_NOT_STARTED = 'NOT_STARTED';
  /**
   * In progress.
   */
  public const UPGRADE_STATUS_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Operation succeeded.
   */
  public const UPGRADE_STATUS_SUCCESS = 'SUCCESS';
  /**
   * Operation failed.
   */
  public const UPGRADE_STATUS_FAILED = 'FAILED';
  /**
   * Operation partially succeeded.
   */
  public const UPGRADE_STATUS_PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
  /**
   * Cancel is in progress.
   */
  public const UPGRADE_STATUS_CANCEL_IN_PROGRESS = 'CANCEL_IN_PROGRESS';
  /**
   * Cancellation complete.
   */
  public const UPGRADE_STATUS_CANCELLED = 'CANCELLED';
  protected $collection_key = 'stageInfo';
  /**
   * Cluster type which can either be primary or secondary.
   *
   * @var string
   */
  public $clusterType;
  /**
   * Database version of the cluster after the upgrade operation. This will be
   * the target version if the upgrade was successful otherwise it remains the
   * same as that before the upgrade operation.
   *
   * @var string
   */
  public $databaseVersion;
  protected $instanceUpgradeDetailsType = InstanceUpgradeDetails::class;
  protected $instanceUpgradeDetailsDataType = 'array';
  /**
   * Normalized name of the cluster
   *
   * @var string
   */
  public $name;
  protected $stageInfoType = StageInfo::class;
  protected $stageInfoDataType = 'array';
  /**
   * Upgrade status of the cluster.
   *
   * @var string
   */
  public $upgradeStatus;

  /**
   * Cluster type which can either be primary or secondary.
   *
   * Accepted values: CLUSTER_TYPE_UNSPECIFIED, PRIMARY, SECONDARY
   *
   * @param self::CLUSTER_TYPE_* $clusterType
   */
  public function setClusterType($clusterType)
  {
    $this->clusterType = $clusterType;
  }
  /**
   * @return self::CLUSTER_TYPE_*
   */
  public function getClusterType()
  {
    return $this->clusterType;
  }
  /**
   * Database version of the cluster after the upgrade operation. This will be
   * the target version if the upgrade was successful otherwise it remains the
   * same as that before the upgrade operation.
   *
   * Accepted values: DATABASE_VERSION_UNSPECIFIED, POSTGRES_13, POSTGRES_14,
   * POSTGRES_15, POSTGRES_16, POSTGRES_17
   *
   * @param self::DATABASE_VERSION_* $databaseVersion
   */
  public function setDatabaseVersion($databaseVersion)
  {
    $this->databaseVersion = $databaseVersion;
  }
  /**
   * @return self::DATABASE_VERSION_*
   */
  public function getDatabaseVersion()
  {
    return $this->databaseVersion;
  }
  /**
   * Upgrade details of the instances directly associated with this cluster.
   *
   * @param InstanceUpgradeDetails[] $instanceUpgradeDetails
   */
  public function setInstanceUpgradeDetails($instanceUpgradeDetails)
  {
    $this->instanceUpgradeDetails = $instanceUpgradeDetails;
  }
  /**
   * @return InstanceUpgradeDetails[]
   */
  public function getInstanceUpgradeDetails()
  {
    return $this->instanceUpgradeDetails;
  }
  /**
   * Normalized name of the cluster
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
   * Array containing stage info associated with this cluster.
   *
   * @param StageInfo[] $stageInfo
   */
  public function setStageInfo($stageInfo)
  {
    $this->stageInfo = $stageInfo;
  }
  /**
   * @return StageInfo[]
   */
  public function getStageInfo()
  {
    return $this->stageInfo;
  }
  /**
   * Upgrade status of the cluster.
   *
   * Accepted values: STATUS_UNSPECIFIED, NOT_STARTED, IN_PROGRESS, SUCCESS,
   * FAILED, PARTIAL_SUCCESS, CANCEL_IN_PROGRESS, CANCELLED
   *
   * @param self::UPGRADE_STATUS_* $upgradeStatus
   */
  public function setUpgradeStatus($upgradeStatus)
  {
    $this->upgradeStatus = $upgradeStatus;
  }
  /**
   * @return self::UPGRADE_STATUS_*
   */
  public function getUpgradeStatus()
  {
    return $this->upgradeStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterUpgradeDetails::class, 'Google_Service_CloudAlloyDBAdmin_ClusterUpgradeDetails');
