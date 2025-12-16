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

class HybridReplicationParameters extends \Google\Collection
{
  /**
   * Unspecified hybrid replication type.
   */
  public const HYBRID_REPLICATION_TYPE_VOLUME_HYBRID_REPLICATION_TYPE_UNSPECIFIED = 'VOLUME_HYBRID_REPLICATION_TYPE_UNSPECIFIED';
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
   * New field for reversible OnPrem replication, to be used for data
   * protection.
   */
  public const HYBRID_REPLICATION_TYPE_REVERSE_ONPREM_REPLICATION = 'REVERSE_ONPREM_REPLICATION';
  /**
   * Unspecified HybridReplicationSchedule
   */
  public const REPLICATION_SCHEDULE_HYBRID_REPLICATION_SCHEDULE_UNSPECIFIED = 'HYBRID_REPLICATION_SCHEDULE_UNSPECIFIED';
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
  protected $collection_key = 'peerIpAddresses';
  /**
   * Optional. Name of source cluster location associated with the Hybrid
   * replication. This is a free-form field for the display purpose only.
   *
   * @var string
   */
  public $clusterLocation;
  /**
   * Optional. Description of the replication.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Type of the hybrid replication.
   *
   * @var string
   */
  public $hybridReplicationType;
  /**
   * Optional. Labels to be added to the replication as the key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Constituent volume count for large volume.
   *
   * @var int
   */
  public $largeVolumeConstituentCount;
  /**
   * Required. Name of the user's local source cluster to be peered with the
   * destination cluster.
   *
   * @var string
   */
  public $peerClusterName;
  /**
   * Required. List of node ip addresses to be peered with.
   *
   * @var string[]
   */
  public $peerIpAddresses;
  /**
   * Required. Name of the user's local source vserver svm to be peered with the
   * destination vserver svm.
   *
   * @var string
   */
  public $peerSvmName;
  /**
   * Required. Name of the user's local source volume to be peered with the
   * destination volume.
   *
   * @var string
   */
  public $peerVolumeName;
  /**
   * Required. Desired name for the replication of this volume.
   *
   * @var string
   */
  public $replication;
  /**
   * Optional. Replication Schedule for the replication created.
   *
   * @var string
   */
  public $replicationSchedule;

  /**
   * Optional. Name of source cluster location associated with the Hybrid
   * replication. This is a free-form field for the display purpose only.
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
   * Optional. Description of the replication.
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
   * Optional. Type of the hybrid replication.
   *
   * Accepted values: VOLUME_HYBRID_REPLICATION_TYPE_UNSPECIFIED, MIGRATION,
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
   * Optional. Labels to be added to the replication as the key value pairs.
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
   * Optional. Constituent volume count for large volume.
   *
   * @param int $largeVolumeConstituentCount
   */
  public function setLargeVolumeConstituentCount($largeVolumeConstituentCount)
  {
    $this->largeVolumeConstituentCount = $largeVolumeConstituentCount;
  }
  /**
   * @return int
   */
  public function getLargeVolumeConstituentCount()
  {
    return $this->largeVolumeConstituentCount;
  }
  /**
   * Required. Name of the user's local source cluster to be peered with the
   * destination cluster.
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
   * Required. List of node ip addresses to be peered with.
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
   * Required. Name of the user's local source vserver svm to be peered with the
   * destination vserver svm.
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
   * Required. Name of the user's local source volume to be peered with the
   * destination volume.
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
   * Required. Desired name for the replication of this volume.
   *
   * @param string $replication
   */
  public function setReplication($replication)
  {
    $this->replication = $replication;
  }
  /**
   * @return string
   */
  public function getReplication()
  {
    return $this->replication;
  }
  /**
   * Optional. Replication Schedule for the replication created.
   *
   * Accepted values: HYBRID_REPLICATION_SCHEDULE_UNSPECIFIED, EVERY_10_MINUTES,
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HybridReplicationParameters::class, 'Google_Service_NetAppFiles_HybridReplicationParameters');
