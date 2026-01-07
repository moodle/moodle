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

namespace Google\Service\GKEHub;

class ConfigManagementConfigSyncState extends \Google\Collection
{
  /**
   * State cannot be determined
   */
  public const CLUSTER_LEVEL_STOP_SYNCING_STATE_STOP_SYNCING_STATE_UNSPECIFIED = 'STOP_SYNCING_STATE_UNSPECIFIED';
  /**
   * Syncing resources to the cluster is not stopped at the cluster level.
   */
  public const CLUSTER_LEVEL_STOP_SYNCING_STATE_NOT_STOPPED = 'NOT_STOPPED';
  /**
   * Some reconcilers stop syncing resources to the cluster, while others are
   * still syncing.
   */
  public const CLUSTER_LEVEL_STOP_SYNCING_STATE_PENDING = 'PENDING';
  /**
   * Syncing resources to the cluster is stopped at the cluster level.
   */
  public const CLUSTER_LEVEL_STOP_SYNCING_STATE_STOPPED = 'STOPPED';
  /**
   * CRD's state cannot be determined
   */
  public const REPOSYNC_CRD_CRD_STATE_UNSPECIFIED = 'CRD_STATE_UNSPECIFIED';
  /**
   * CRD is not installed
   */
  public const REPOSYNC_CRD_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * CRD is installed
   */
  public const REPOSYNC_CRD_INSTALLED = 'INSTALLED';
  /**
   * CRD is terminating (i.e., it has been deleted and is cleaning up)
   */
  public const REPOSYNC_CRD_TERMINATING = 'TERMINATING';
  /**
   * CRD is installing
   */
  public const REPOSYNC_CRD_INSTALLING = 'INSTALLING';
  /**
   * CRD's state cannot be determined
   */
  public const ROOTSYNC_CRD_CRD_STATE_UNSPECIFIED = 'CRD_STATE_UNSPECIFIED';
  /**
   * CRD is not installed
   */
  public const ROOTSYNC_CRD_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * CRD is installed
   */
  public const ROOTSYNC_CRD_INSTALLED = 'INSTALLED';
  /**
   * CRD is terminating (i.e., it has been deleted and is cleaning up)
   */
  public const ROOTSYNC_CRD_TERMINATING = 'TERMINATING';
  /**
   * CRD is installing
   */
  public const ROOTSYNC_CRD_INSTALLING = 'INSTALLING';
  /**
   * CS's state cannot be determined.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * CS is not installed.
   */
  public const STATE_CONFIG_SYNC_NOT_INSTALLED = 'CONFIG_SYNC_NOT_INSTALLED';
  /**
   * The expected CS version is installed successfully.
   */
  public const STATE_CONFIG_SYNC_INSTALLED = 'CONFIG_SYNC_INSTALLED';
  /**
   * CS encounters errors.
   */
  public const STATE_CONFIG_SYNC_ERROR = 'CONFIG_SYNC_ERROR';
  /**
   * CS is installing or terminating.
   */
  public const STATE_CONFIG_SYNC_PENDING = 'CONFIG_SYNC_PENDING';
  protected $collection_key = 'errors';
  /**
   * Output only. Whether syncing resources to the cluster is stopped at the
   * cluster level.
   *
   * @var string
   */
  public $clusterLevelStopSyncingState;
  /**
   * Output only. The number of RootSync and RepoSync CRs in the cluster.
   *
   * @var int
   */
  public $crCount;
  protected $deploymentStateType = ConfigManagementConfigSyncDeploymentState::class;
  protected $deploymentStateDataType = '';
  protected $errorsType = ConfigManagementConfigSyncError::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. The state of the Reposync CRD
   *
   * @var string
   */
  public $reposyncCrd;
  /**
   * Output only. The state of the RootSync CRD
   *
   * @var string
   */
  public $rootsyncCrd;
  /**
   * Output only. The state of CS This field summarizes the other fields in this
   * message.
   *
   * @var string
   */
  public $state;
  protected $syncStateType = ConfigManagementSyncState::class;
  protected $syncStateDataType = '';
  protected $versionType = ConfigManagementConfigSyncVersion::class;
  protected $versionDataType = '';

  /**
   * Output only. Whether syncing resources to the cluster is stopped at the
   * cluster level.
   *
   * Accepted values: STOP_SYNCING_STATE_UNSPECIFIED, NOT_STOPPED, PENDING,
   * STOPPED
   *
   * @param self::CLUSTER_LEVEL_STOP_SYNCING_STATE_* $clusterLevelStopSyncingState
   */
  public function setClusterLevelStopSyncingState($clusterLevelStopSyncingState)
  {
    $this->clusterLevelStopSyncingState = $clusterLevelStopSyncingState;
  }
  /**
   * @return self::CLUSTER_LEVEL_STOP_SYNCING_STATE_*
   */
  public function getClusterLevelStopSyncingState()
  {
    return $this->clusterLevelStopSyncingState;
  }
  /**
   * Output only. The number of RootSync and RepoSync CRs in the cluster.
   *
   * @param int $crCount
   */
  public function setCrCount($crCount)
  {
    $this->crCount = $crCount;
  }
  /**
   * @return int
   */
  public function getCrCount()
  {
    return $this->crCount;
  }
  /**
   * Output only. Information about the deployment of ConfigSync, including the
   * version. of the various Pods deployed
   *
   * @param ConfigManagementConfigSyncDeploymentState $deploymentState
   */
  public function setDeploymentState(ConfigManagementConfigSyncDeploymentState $deploymentState)
  {
    $this->deploymentState = $deploymentState;
  }
  /**
   * @return ConfigManagementConfigSyncDeploymentState
   */
  public function getDeploymentState()
  {
    return $this->deploymentState;
  }
  /**
   * Output only. Errors pertaining to the installation of Config Sync.
   *
   * @param ConfigManagementConfigSyncError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return ConfigManagementConfigSyncError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. The state of the Reposync CRD
   *
   * Accepted values: CRD_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * TERMINATING, INSTALLING
   *
   * @param self::REPOSYNC_CRD_* $reposyncCrd
   */
  public function setReposyncCrd($reposyncCrd)
  {
    $this->reposyncCrd = $reposyncCrd;
  }
  /**
   * @return self::REPOSYNC_CRD_*
   */
  public function getReposyncCrd()
  {
    return $this->reposyncCrd;
  }
  /**
   * Output only. The state of the RootSync CRD
   *
   * Accepted values: CRD_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * TERMINATING, INSTALLING
   *
   * @param self::ROOTSYNC_CRD_* $rootsyncCrd
   */
  public function setRootsyncCrd($rootsyncCrd)
  {
    $this->rootsyncCrd = $rootsyncCrd;
  }
  /**
   * @return self::ROOTSYNC_CRD_*
   */
  public function getRootsyncCrd()
  {
    return $this->rootsyncCrd;
  }
  /**
   * Output only. The state of CS This field summarizes the other fields in this
   * message.
   *
   * Accepted values: STATE_UNSPECIFIED, CONFIG_SYNC_NOT_INSTALLED,
   * CONFIG_SYNC_INSTALLED, CONFIG_SYNC_ERROR, CONFIG_SYNC_PENDING
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
   * Output only. The state of ConfigSync's process to sync configs to a
   * cluster.
   *
   * @param ConfigManagementSyncState $syncState
   */
  public function setSyncState(ConfigManagementSyncState $syncState)
  {
    $this->syncState = $syncState;
  }
  /**
   * @return ConfigManagementSyncState
   */
  public function getSyncState()
  {
    return $this->syncState;
  }
  /**
   * Output only. The version of ConfigSync deployed.
   *
   * @param ConfigManagementConfigSyncVersion $version
   */
  public function setVersion(ConfigManagementConfigSyncVersion $version)
  {
    $this->version = $version;
  }
  /**
   * @return ConfigManagementConfigSyncVersion
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementConfigSyncState::class, 'Google_Service_GKEHub_ConfigManagementConfigSyncState');
