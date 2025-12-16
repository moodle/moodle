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

namespace Google\Service\NetAppFiles;

class CacheParameters extends \Google\Collection
{
  /**
   * Default unspecified state.
   */
  public const CACHE_STATE_CACHE_STATE_UNSPECIFIED = 'CACHE_STATE_UNSPECIFIED';
  /**
   * State indicating waiting for cluster peering to be established.
   */
  public const CACHE_STATE_PENDING_CLUSTER_PEERING = 'PENDING_CLUSTER_PEERING';
  /**
   * State indicating waiting for SVM peering to be established.
   */
  public const CACHE_STATE_PENDING_SVM_PEERING = 'PENDING_SVM_PEERING';
  /**
   * State indicating successful establishment of peering with origin volumes's
   * ONTAP cluster.
   */
  public const CACHE_STATE_PEERED = 'PEERED';
  /**
   * Terminal state wherein peering with origin volume's ONTAP cluster has
   * failed.
   */
  public const CACHE_STATE_ERROR = 'ERROR';
  protected $collection_key = 'peerIpAddresses';
  protected $cacheConfigType = CacheConfig::class;
  protected $cacheConfigDataType = '';
  /**
   * Output only. State of the cache volume indicating the peering status.
   *
   * @var string
   */
  public $cacheState;
  /**
   * Output only. Copy-paste-able commands to be used on user's ONTAP to accept
   * peering requests.
   *
   * @var string
   */
  public $command;
  /**
   * Optional. Indicates whether the cache volume has global file lock enabled.
   *
   * @var bool
   */
  public $enableGlobalFileLock;
  /**
   * Output only. Temporary passphrase generated to accept cluster peering
   * command.
   *
   * @var string
   */
  public $passphrase;
  /**
   * Required. Name of the origin volume's ONTAP cluster.
   *
   * @var string
   */
  public $peerClusterName;
  /**
   * Required. List of IC LIF addresses of the origin volume's ONTAP cluster.
   *
   * @var string[]
   */
  public $peerIpAddresses;
  /**
   * Required. Name of the origin volume's SVM.
   *
   * @var string
   */
  public $peerSvmName;
  /**
   * Required. Name of the origin volume for the cache volume.
   *
   * @var string
   */
  public $peerVolumeName;
  /**
   * Optional. Expiration time for the peering command to be executed on user's
   * ONTAP.
   *
   * @var string
   */
  public $peeringCommandExpiryTime;
  /**
   * Output only. Detailed description of the current cache state.
   *
   * @var string
   */
  public $stateDetails;

  /**
   * Optional. Configuration of the cache volume.
   *
   * @param CacheConfig $cacheConfig
   */
  public function setCacheConfig(CacheConfig $cacheConfig)
  {
    $this->cacheConfig = $cacheConfig;
  }
  /**
   * @return CacheConfig
   */
  public function getCacheConfig()
  {
    return $this->cacheConfig;
  }
  /**
   * Output only. State of the cache volume indicating the peering status.
   *
   * Accepted values: CACHE_STATE_UNSPECIFIED, PENDING_CLUSTER_PEERING,
   * PENDING_SVM_PEERING, PEERED, ERROR
   *
   * @param self::CACHE_STATE_* $cacheState
   */
  public function setCacheState($cacheState)
  {
    $this->cacheState = $cacheState;
  }
  /**
   * @return self::CACHE_STATE_*
   */
  public function getCacheState()
  {
    return $this->cacheState;
  }
  /**
   * Output only. Copy-paste-able commands to be used on user's ONTAP to accept
   * peering requests.
   *
   * @param string $command
   */
  public function setCommand($command)
  {
    $this->command = $command;
  }
  /**
   * @return string
   */
  public function getCommand()
  {
    return $this->command;
  }
  /**
   * Optional. Indicates whether the cache volume has global file lock enabled.
   *
   * @param bool $enableGlobalFileLock
   */
  public function setEnableGlobalFileLock($enableGlobalFileLock)
  {
    $this->enableGlobalFileLock = $enableGlobalFileLock;
  }
  /**
   * @return bool
   */
  public function getEnableGlobalFileLock()
  {
    return $this->enableGlobalFileLock;
  }
  /**
   * Output only. Temporary passphrase generated to accept cluster peering
   * command.
   *
   * @param string $passphrase
   */
  public function setPassphrase($passphrase)
  {
    $this->passphrase = $passphrase;
  }
  /**
   * @return string
   */
  public function getPassphrase()
  {
    return $this->passphrase;
  }
  /**
   * Required. Name of the origin volume's ONTAP cluster.
   *
   * @param string $peerClusterName
   */
  public function setPeerClusterName($peerClusterName)
  {
    $this->peerClusterName = $peerClusterName;
  }
  /**
   * @return string
   */
  public function getPeerClusterName()
  {
    return $this->peerClusterName;
  }
  /**
   * Required. List of IC LIF addresses of the origin volume's ONTAP cluster.
   *
   * @param string[] $peerIpAddresses
   */
  public function setPeerIpAddresses($peerIpAddresses)
  {
    $this->peerIpAddresses = $peerIpAddresses;
  }
  /**
   * @return string[]
   */
  public function getPeerIpAddresses()
  {
    return $this->peerIpAddresses;
  }
  /**
   * Required. Name of the origin volume's SVM.
   *
   * @param string $peerSvmName
   */
  public function setPeerSvmName($peerSvmName)
  {
    $this->peerSvmName = $peerSvmName;
  }
  /**
   * @return string
   */
  public function getPeerSvmName()
  {
    return $this->peerSvmName;
  }
  /**
   * Required. Name of the origin volume for the cache volume.
   *
   * @param string $peerVolumeName
   */
  public function setPeerVolumeName($peerVolumeName)
  {
    $this->peerVolumeName = $peerVolumeName;
  }
  /**
   * @return string
   */
  public function getPeerVolumeName()
  {
    return $this->peerVolumeName;
  }
  /**
   * Optional. Expiration time for the peering command to be executed on user's
   * ONTAP.
   *
   * @param string $peeringCommandExpiryTime
   */
  public function setPeeringCommandExpiryTime($peeringCommandExpiryTime)
  {
    $this->peeringCommandExpiryTime = $peeringCommandExpiryTime;
  }
  /**
   * @return string
   */
  public function getPeeringCommandExpiryTime()
  {
    return $this->peeringCommandExpiryTime;
  }
  /**
   * Output only. Detailed description of the current cache state.
   *
   * @param string $stateDetails
   */
  public function setStateDetails($stateDetails)
  {
    $this->stateDetails = $stateDetails;
  }
  /**
   * @return string
   */
  public function getStateDetails()
  {
    return $this->stateDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CacheParameters::class, 'Google_Service_NetAppFiles_CacheParameters');
