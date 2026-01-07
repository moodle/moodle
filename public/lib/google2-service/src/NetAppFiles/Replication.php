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

class Replication extends \Google\Model
{
  /**
   * Unspecified hybrid replication type.
   */
  public const HYBRID_REPLICATION_TYPE_HYBRID_REPLICATION_TYPE_UNSPECIFIED = 'HYBRID_REPLICATION_TYPE_UNSPECIFIED';
  /**
   * Hybrid replication type for migration.
   */
  public const HYBRID_REPLICATION_TYPE_MIGRATION = 'MIGRATION';
  /**
   * Hybrid replication type for continuous replication.
   */
  public const HYBRID_REPLICATION_TYPE_CONTINUOUS_REPLICATION = 'CONTINUOUS_REPLICATION';
  /**
   * New field for reversible OnPrem replication, to be used for data
   * protection.
   */
  public const HYBRID_REPLICATION_TYPE_ONPREM_REPLICATION = 'ONPREM_REPLICATION';
  /**
   * Hybrid replication type for incremental Transfer in the reverse direction
   * (GCNV is source and Onprem is destination)
   */
  public const HYBRID_REPLICATION_TYPE_REVERSE_ONPREM_REPLICATION = 'REVERSE_ONPREM_REPLICATION';
  /**
   * Unspecified MirrorState
   */
  public const MIRROR_STATE_MIRROR_STATE_UNSPECIFIED = 'MIRROR_STATE_UNSPECIFIED';
  /**
   * Destination volume is being prepared.
   */
  public const MIRROR_STATE_PREPARING = 'PREPARING';
  /**
   * Destination volume has been initialized and is ready to receive replication
   * transfers.
   */
  public const MIRROR_STATE_MIRRORED = 'MIRRORED';
  /**
   * Destination volume is not receiving replication transfers.
   */
  public const MIRROR_STATE_STOPPED = 'STOPPED';
  /**
   * Incremental replication is in progress.
   */
  public const MIRROR_STATE_TRANSFERRING = 'TRANSFERRING';
  /**
   * Baseline replication is in progress.
   */
  public const MIRROR_STATE_BASELINE_TRANSFERRING = 'BASELINE_TRANSFERRING';
  /**
   * Replication is aborted.
   */
  public const MIRROR_STATE_ABORTED = 'ABORTED';
  /**
   * Replication is being managed from Onprem ONTAP.
   */
  public const MIRROR_STATE_EXTERNALLY_MANAGED = 'EXTERNALLY_MANAGED';
  /**
   * Peering is yet to be established.
   */
  public const MIRROR_STATE_PENDING_PEERING = 'PENDING_PEERING';
  /**
   * Unspecified ReplicationSchedule
   */
  public const REPLICATION_SCHEDULE_REPLICATION_SCHEDULE_UNSPECIFIED = 'REPLICATION_SCHEDULE_UNSPECIFIED';
  /**
   * Replication happens once every 10 minutes.
   */
  public const REPLICATION_SCHEDULE_EVERY_10_MINUTES = 'EVERY_10_MINUTES';
  /**
   * Replication happens once every hour.
   */
  public const REPLICATION_SCHEDULE_HOURLY = 'HOURLY';
  /**
   * Replication happens once every day.
   */
  public const REPLICATION_SCHEDULE_DAILY = 'DAILY';
  /**
   * Unspecified replication role
   */
  public const ROLE_REPLICATION_ROLE_UNSPECIFIED = 'REPLICATION_ROLE_UNSPECIFIED';
  /**
   * Indicates Source volume.
   */
  public const ROLE_SOURCE = 'SOURCE';
  /**
   * Indicates Destination volume.
   */
  public const ROLE_DESTINATION = 'DESTINATION';
  /**
   * Unspecified replication State
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Replication is creating.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Replication is ready.
   */
  public const STATE_READY = 'READY';
  /**
   * Replication is updating.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Replication is deleting.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Replication is in error state.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Replication is waiting for cluster peering to be established.
   */
  public const STATE_PENDING_CLUSTER_PEERING = 'PENDING_CLUSTER_PEERING';
  /**
   * Replication is waiting for SVM peering to be established.
   */
  public const STATE_PENDING_SVM_PEERING = 'PENDING_SVM_PEERING';
  /**
   * Replication is waiting for Commands to be executed on Onprem ONTAP.
   */
  public const STATE_PENDING_REMOTE_RESYNC = 'PENDING_REMOTE_RESYNC';
  /**
   * Onprem ONTAP is destination and Replication can only be managed from
   * Onprem.
   */
  public const STATE_EXTERNALLY_MANAGED_REPLICATION = 'EXTERNALLY_MANAGED_REPLICATION';
  /**
   * Optional. Location of the user cluster.
   *
   * @var string
   */
  public $clusterLocation;
  /**
   * Output only. Replication create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * A description about this replication relationship.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Full name of destination volume resource. Example :
   * "projects/{project}/locations/{location}/volumes/{volume_id}"
   *
   * @var string
   */
  public $destinationVolume;
  protected $destinationVolumeParametersType = DestinationVolumeParameters::class;
  protected $destinationVolumeParametersDataType = '';
  /**
   * Output only. Condition of the relationship. Can be one of the following: -
   * true: The replication relationship is healthy. It has not missed the most
   * recent scheduled transfer. - false: The replication relationship is not
   * healthy. It has missed the most recent scheduled transfer.
   *
   * @var bool
   */
  public $healthy;
  protected $hybridPeeringDetailsType = HybridPeeringDetails::class;
  protected $hybridPeeringDetailsDataType = '';
  /**
   * Output only. Type of the hybrid replication.
   *
   * @var string
   */
  public $hybridReplicationType;
  protected $hybridReplicationUserCommandsType = UserCommands::class;
  protected $hybridReplicationUserCommandsDataType = '';
  /**
   * Resource labels to represent user provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Indicates the state of mirroring.
   *
   * @var string
   */
  public $mirrorState;
  /**
   * Identifier. The resource name of the Replication. Format: `projects/{projec
   * t_id}/locations/{location}/volumes/{volume_id}/replications/{replication_id
   * }`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Indicates the schedule for replication.
   *
   * @var string
   */
  public $replicationSchedule;
  /**
   * Output only. Indicates whether this points to source or destination.
   *
   * @var string
   */
  public $role;
  /**
   * Output only. Full name of source volume resource. Example :
   * "projects/{project}/locations/{location}/volumes/{volume_id}"
   *
   * @var string
   */
  public $sourceVolume;
  /**
   * Output only. State of the replication.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. State details of the replication.
   *
   * @var string
   */
  public $stateDetails;
  protected $transferStatsType = TransferStats::class;
  protected $transferStatsDataType = '';

  /**
   * Optional. Location of the user cluster.
   *
   * @param string $clusterLocation
   */
  public function setClusterLocation($clusterLocation)
  {
    $this->clusterLocation = $clusterLocation;
  }
  /**
   * @return string
   */
  public function getClusterLocation()
  {
    return $this->clusterLocation;
  }
  /**
   * Output only. Replication create time.
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
   * A description about this replication relationship.
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
   * Output only. Full name of destination volume resource. Example :
   * "projects/{project}/locations/{location}/volumes/{volume_id}"
   *
   * @param string $destinationVolume
   */
  public function setDestinationVolume($destinationVolume)
  {
    $this->destinationVolume = $destinationVolume;
  }
  /**
   * @return string
   */
  public function getDestinationVolume()
  {
    return $this->destinationVolume;
  }
  /**
   * Required. Input only. Destination volume parameters
   *
   * @param DestinationVolumeParameters $destinationVolumeParameters
   */
  public function setDestinationVolumeParameters(DestinationVolumeParameters $destinationVolumeParameters)
  {
    $this->destinationVolumeParameters = $destinationVolumeParameters;
  }
  /**
   * @return DestinationVolumeParameters
   */
  public function getDestinationVolumeParameters()
  {
    return $this->destinationVolumeParameters;
  }
  /**
   * Output only. Condition of the relationship. Can be one of the following: -
   * true: The replication relationship is healthy. It has not missed the most
   * recent scheduled transfer. - false: The replication relationship is not
   * healthy. It has missed the most recent scheduled transfer.
   *
   * @param bool $healthy
   */
  public function setHealthy($healthy)
  {
    $this->healthy = $healthy;
  }
  /**
   * @return bool
   */
  public function getHealthy()
  {
    return $this->healthy;
  }
  /**
   * Output only. Hybrid peering details.
   *
   * @param HybridPeeringDetails $hybridPeeringDetails
   */
  public function setHybridPeeringDetails(HybridPeeringDetails $hybridPeeringDetails)
  {
    $this->hybridPeeringDetails = $hybridPeeringDetails;
  }
  /**
   * @return HybridPeeringDetails
   */
  public function getHybridPeeringDetails()
  {
    return $this->hybridPeeringDetails;
  }
  /**
   * Output only. Type of the hybrid replication.
   *
   * Accepted values: HYBRID_REPLICATION_TYPE_UNSPECIFIED, MIGRATION,
   * CONTINUOUS_REPLICATION, ONPREM_REPLICATION, REVERSE_ONPREM_REPLICATION
   *
   * @param self::HYBRID_REPLICATION_TYPE_* $hybridReplicationType
   */
  public function setHybridReplicationType($hybridReplicationType)
  {
    $this->hybridReplicationType = $hybridReplicationType;
  }
  /**
   * @return self::HYBRID_REPLICATION_TYPE_*
   */
  public function getHybridReplicationType()
  {
    return $this->hybridReplicationType;
  }
  /**
   * Output only. Copy pastable snapmirror commands to be executed on onprem
   * cluster by the customer.
   *
   * @param UserCommands $hybridReplicationUserCommands
   */
  public function setHybridReplicationUserCommands(UserCommands $hybridReplicationUserCommands)
  {
    $this->hybridReplicationUserCommands = $hybridReplicationUserCommands;
  }
  /**
   * @return UserCommands
   */
  public function getHybridReplicationUserCommands()
  {
    return $this->hybridReplicationUserCommands;
  }
  /**
   * Resource labels to represent user provided metadata.
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
   * Output only. Indicates the state of mirroring.
   *
   * Accepted values: MIRROR_STATE_UNSPECIFIED, PREPARING, MIRRORED, STOPPED,
   * TRANSFERRING, BASELINE_TRANSFERRING, ABORTED, EXTERNALLY_MANAGED,
   * PENDING_PEERING
   *
   * @param self::MIRROR_STATE_* $mirrorState
   */
  public function setMirrorState($mirrorState)
  {
    $this->mirrorState = $mirrorState;
  }
  /**
   * @return self::MIRROR_STATE_*
   */
  public function getMirrorState()
  {
    return $this->mirrorState;
  }
  /**
   * Identifier. The resource name of the Replication. Format: `projects/{projec
   * t_id}/locations/{location}/volumes/{volume_id}/replications/{replication_id
   * }`.
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
   * Required. Indicates the schedule for replication.
   *
   * Accepted values: REPLICATION_SCHEDULE_UNSPECIFIED, EVERY_10_MINUTES,
   * HOURLY, DAILY
   *
   * @param self::REPLICATION_SCHEDULE_* $replicationSchedule
   */
  public function setReplicationSchedule($replicationSchedule)
  {
    $this->replicationSchedule = $replicationSchedule;
  }
  /**
   * @return self::REPLICATION_SCHEDULE_*
   */
  public function getReplicationSchedule()
  {
    return $this->replicationSchedule;
  }
  /**
   * Output only. Indicates whether this points to source or destination.
   *
   * Accepted values: REPLICATION_ROLE_UNSPECIFIED, SOURCE, DESTINATION
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Output only. Full name of source volume resource. Example :
   * "projects/{project}/locations/{location}/volumes/{volume_id}"
   *
   * @param string $sourceVolume
   */
  public function setSourceVolume($sourceVolume)
  {
    $this->sourceVolume = $sourceVolume;
  }
  /**
   * @return string
   */
  public function getSourceVolume()
  {
    return $this->sourceVolume;
  }
  /**
   * Output only. State of the replication.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, UPDATING, DELETING,
   * ERROR, PENDING_CLUSTER_PEERING, PENDING_SVM_PEERING, PENDING_REMOTE_RESYNC,
   * EXTERNALLY_MANAGED_REPLICATION
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
   * Output only. State details of the replication.
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
  /**
   * Output only. Replication transfer statistics.
   *
   * @param TransferStats $transferStats
   */
  public function setTransferStats(TransferStats $transferStats)
  {
    $this->transferStats = $transferStats;
  }
  /**
   * @return TransferStats
   */
  public function getTransferStats()
  {
    return $this->transferStats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Replication::class, 'Google_Service_NetAppFiles_Replication');
