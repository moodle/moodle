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

namespace Google\Service\Baremetalsolution;

class Volume extends \Google\Collection
{
  /**
   * Value is not specified.
   */
  public const PERFORMANCE_TIER_VOLUME_PERFORMANCE_TIER_UNSPECIFIED = 'VOLUME_PERFORMANCE_TIER_UNSPECIFIED';
  /**
   * Regular volumes, shared aggregates.
   */
  public const PERFORMANCE_TIER_VOLUME_PERFORMANCE_TIER_SHARED = 'VOLUME_PERFORMANCE_TIER_SHARED';
  /**
   * Assigned aggregates.
   */
  public const PERFORMANCE_TIER_VOLUME_PERFORMANCE_TIER_ASSIGNED = 'VOLUME_PERFORMANCE_TIER_ASSIGNED';
  /**
   * High throughput aggregates.
   */
  public const PERFORMANCE_TIER_VOLUME_PERFORMANCE_TIER_HT = 'VOLUME_PERFORMANCE_TIER_HT';
  /**
   * QoS 2.0 high performance storage.
   */
  public const PERFORMANCE_TIER_VOLUME_PERFORMANCE_TIER_QOS2_PERFORMANCE = 'VOLUME_PERFORMANCE_TIER_QOS2_PERFORMANCE';
  /**
   * Value is not specified.
   */
  public const PROTOCOL_PROTOCOL_UNSPECIFIED = 'PROTOCOL_UNSPECIFIED';
  /**
   * Fibre Channel protocol.
   */
  public const PROTOCOL_FIBRE_CHANNEL = 'FIBRE_CHANNEL';
  /**
   * NFS protocol means Volume is a NFS Share volume. Such volumes cannot be
   * manipulated via Volumes API.
   */
  public const PROTOCOL_NFS = 'NFS';
  /**
   * The unspecified behavior.
   */
  public const SNAPSHOT_AUTO_DELETE_BEHAVIOR_SNAPSHOT_AUTO_DELETE_BEHAVIOR_UNSPECIFIED = 'SNAPSHOT_AUTO_DELETE_BEHAVIOR_UNSPECIFIED';
  /**
   * Don't delete any snapshots. This disables new snapshot creation, as long as
   * the snapshot reserved space is full.
   */
  public const SNAPSHOT_AUTO_DELETE_BEHAVIOR_DISABLED = 'DISABLED';
  /**
   * Delete the oldest snapshots first.
   */
  public const SNAPSHOT_AUTO_DELETE_BEHAVIOR_OLDEST_FIRST = 'OLDEST_FIRST';
  /**
   * Delete the newest snapshots first.
   */
  public const SNAPSHOT_AUTO_DELETE_BEHAVIOR_NEWEST_FIRST = 'NEWEST_FIRST';
  /**
   * The storage volume is in an unknown state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The storage volume is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The storage volume is ready for use.
   */
  public const STATE_READY = 'READY';
  /**
   * The storage volume has been requested to be deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The storage volume is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The storage volume is in cool off state. It will be deleted after
   * `expire_time`.
   */
  public const STATE_COOL_OFF = 'COOL_OFF';
  /**
   * The storage type for this volume is unknown.
   */
  public const STORAGE_TYPE_STORAGE_TYPE_UNSPECIFIED = 'STORAGE_TYPE_UNSPECIFIED';
  /**
   * The storage type for this volume is SSD.
   */
  public const STORAGE_TYPE_SSD = 'SSD';
  /**
   * This storage type for this volume is HDD.
   */
  public const STORAGE_TYPE_HDD = 'HDD';
  /**
   * The workload profile is in an unknown state.
   */
  public const WORKLOAD_PROFILE_WORKLOAD_PROFILE_UNSPECIFIED = 'WORKLOAD_PROFILE_UNSPECIFIED';
  /**
   * The workload profile is generic.
   */
  public const WORKLOAD_PROFILE_GENERIC = 'GENERIC';
  /**
   * The workload profile is hana.
   */
  public const WORKLOAD_PROFILE_HANA = 'HANA';
  protected $collection_key = 'instances';
  /**
   * Output only. Is the Volume attached at at least one instance. This field is
   * a lightweight counterpart of `instances` field. It is filled in List
   * responses as well.
   *
   * @var bool
   */
  public $attached;
  /**
   * The size, in GiB, that this storage volume has expanded as a result of an
   * auto grow policy. In the absence of auto-grow, the value is 0.
   *
   * @var string
   */
  public $autoGrownSizeGib;
  /**
   * Output only. Whether this volume is a boot volume. A boot volume is one
   * which contains a boot LUN.
   *
   * @var bool
   */
  public $bootVolume;
  /**
   * The current size of this storage volume, in GiB, including space reserved
   * for snapshots. This size might be different than the requested size if the
   * storage volume has been configured with auto grow or auto shrink.
   *
   * @var string
   */
  public $currentSizeGib;
  /**
   * Additional emergency size that was requested for this Volume, in GiB.
   * current_size_gib includes this value.
   *
   * @var string
   */
  public $emergencySizeGib;
  /**
   * Output only. Time after which volume will be fully deleted. It is filled
   * only for volumes in COOLOFF state.
   *
   * @var string
   */
  public $expireTime;
  /**
   * An identifier for the `Volume`, generated by the backend.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Instances this Volume is attached to. This field is set only
   * in Get requests.
   *
   * @var string[]
   */
  public $instances;
  /**
   * Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Maximum size volume can be expanded to in case of evergency, in GiB.
   *
   * @var string
   */
  public $maxSizeGib;
  /**
   * Output only. The resource name of this `Volume`. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. Format:
   * `projects/{project}/locations/{location}/volumes/{volume}`
   *
   * @var string
   */
  public $name;
  /**
   * Input only. User-specified notes for new Volume. Used to provision Volumes
   * that require manual intervention.
   *
   * @var string
   */
  public $notes;
  /**
   * Originally requested size, in GiB.
   *
   * @var string
   */
  public $originallyRequestedSizeGib;
  /**
   * Immutable. Performance tier of the Volume. Default is SHARED.
   *
   * @var string
   */
  public $performanceTier;
  /**
   * Immutable. Pod name. Pod is an independent part of infrastructure. Volume
   * can only be connected to the instances allocated in the same pod.
   *
   * @var string
   */
  public $pod;
  /**
   * Output only. Storage protocol for the Volume.
   *
   * @var string
   */
  public $protocol;
  /**
   * The space remaining in the storage volume for new LUNs, in GiB, excluding
   * space reserved for snapshots.
   *
   * @var string
   */
  public $remainingSpaceGib;
  /**
   * The requested size of this storage volume, in GiB.
   *
   * @var string
   */
  public $requestedSizeGib;
  /**
   * The behavior to use when snapshot reserved space is full.
   *
   * @var string
   */
  public $snapshotAutoDeleteBehavior;
  /**
   * Whether snapshots are enabled.
   *
   * @var bool
   */
  public $snapshotEnabled;
  protected $snapshotReservationDetailType = SnapshotReservationDetail::class;
  protected $snapshotReservationDetailDataType = '';
  /**
   * The state of this storage volume.
   *
   * @var string
   */
  public $state;
  /**
   * The storage type for this volume.
   *
   * @var string
   */
  public $storageType;
  /**
   * The workload profile for the volume.
   *
   * @var string
   */
  public $workloadProfile;

  /**
   * Output only. Is the Volume attached at at least one instance. This field is
   * a lightweight counterpart of `instances` field. It is filled in List
   * responses as well.
   *
   * @param bool $attached
   */
  public function setAttached($attached)
  {
    $this->attached = $attached;
  }
  /**
   * @return bool
   */
  public function getAttached()
  {
    return $this->attached;
  }
  /**
   * The size, in GiB, that this storage volume has expanded as a result of an
   * auto grow policy. In the absence of auto-grow, the value is 0.
   *
   * @param string $autoGrownSizeGib
   */
  public function setAutoGrownSizeGib($autoGrownSizeGib)
  {
    $this->autoGrownSizeGib = $autoGrownSizeGib;
  }
  /**
   * @return string
   */
  public function getAutoGrownSizeGib()
  {
    return $this->autoGrownSizeGib;
  }
  /**
   * Output only. Whether this volume is a boot volume. A boot volume is one
   * which contains a boot LUN.
   *
   * @param bool $bootVolume
   */
  public function setBootVolume($bootVolume)
  {
    $this->bootVolume = $bootVolume;
  }
  /**
   * @return bool
   */
  public function getBootVolume()
  {
    return $this->bootVolume;
  }
  /**
   * The current size of this storage volume, in GiB, including space reserved
   * for snapshots. This size might be different than the requested size if the
   * storage volume has been configured with auto grow or auto shrink.
   *
   * @param string $currentSizeGib
   */
  public function setCurrentSizeGib($currentSizeGib)
  {
    $this->currentSizeGib = $currentSizeGib;
  }
  /**
   * @return string
   */
  public function getCurrentSizeGib()
  {
    return $this->currentSizeGib;
  }
  /**
   * Additional emergency size that was requested for this Volume, in GiB.
   * current_size_gib includes this value.
   *
   * @param string $emergencySizeGib
   */
  public function setEmergencySizeGib($emergencySizeGib)
  {
    $this->emergencySizeGib = $emergencySizeGib;
  }
  /**
   * @return string
   */
  public function getEmergencySizeGib()
  {
    return $this->emergencySizeGib;
  }
  /**
   * Output only. Time after which volume will be fully deleted. It is filled
   * only for volumes in COOLOFF state.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * An identifier for the `Volume`, generated by the backend.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Instances this Volume is attached to. This field is set only
   * in Get requests.
   *
   * @param string[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return string[]
   */
  public function getInstances()
  {
    return $this->instances;
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
   * Maximum size volume can be expanded to in case of evergency, in GiB.
   *
   * @param string $maxSizeGib
   */
  public function setMaxSizeGib($maxSizeGib)
  {
    $this->maxSizeGib = $maxSizeGib;
  }
  /**
   * @return string
   */
  public function getMaxSizeGib()
  {
    return $this->maxSizeGib;
  }
  /**
   * Output only. The resource name of this `Volume`. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. Format:
   * `projects/{project}/locations/{location}/volumes/{volume}`
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
   * Input only. User-specified notes for new Volume. Used to provision Volumes
   * that require manual intervention.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * Originally requested size, in GiB.
   *
   * @param string $originallyRequestedSizeGib
   */
  public function setOriginallyRequestedSizeGib($originallyRequestedSizeGib)
  {
    $this->originallyRequestedSizeGib = $originallyRequestedSizeGib;
  }
  /**
   * @return string
   */
  public function getOriginallyRequestedSizeGib()
  {
    return $this->originallyRequestedSizeGib;
  }
  /**
   * Immutable. Performance tier of the Volume. Default is SHARED.
   *
   * Accepted values: VOLUME_PERFORMANCE_TIER_UNSPECIFIED,
   * VOLUME_PERFORMANCE_TIER_SHARED, VOLUME_PERFORMANCE_TIER_ASSIGNED,
   * VOLUME_PERFORMANCE_TIER_HT, VOLUME_PERFORMANCE_TIER_QOS2_PERFORMANCE
   *
   * @param self::PERFORMANCE_TIER_* $performanceTier
   */
  public function setPerformanceTier($performanceTier)
  {
    $this->performanceTier = $performanceTier;
  }
  /**
   * @return self::PERFORMANCE_TIER_*
   */
  public function getPerformanceTier()
  {
    return $this->performanceTier;
  }
  /**
   * Immutable. Pod name. Pod is an independent part of infrastructure. Volume
   * can only be connected to the instances allocated in the same pod.
   *
   * @param string $pod
   */
  public function setPod($pod)
  {
    $this->pod = $pod;
  }
  /**
   * @return string
   */
  public function getPod()
  {
    return $this->pod;
  }
  /**
   * Output only. Storage protocol for the Volume.
   *
   * Accepted values: PROTOCOL_UNSPECIFIED, FIBRE_CHANNEL, NFS
   *
   * @param self::PROTOCOL_* $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return self::PROTOCOL_*
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * The space remaining in the storage volume for new LUNs, in GiB, excluding
   * space reserved for snapshots.
   *
   * @param string $remainingSpaceGib
   */
  public function setRemainingSpaceGib($remainingSpaceGib)
  {
    $this->remainingSpaceGib = $remainingSpaceGib;
  }
  /**
   * @return string
   */
  public function getRemainingSpaceGib()
  {
    return $this->remainingSpaceGib;
  }
  /**
   * The requested size of this storage volume, in GiB.
   *
   * @param string $requestedSizeGib
   */
  public function setRequestedSizeGib($requestedSizeGib)
  {
    $this->requestedSizeGib = $requestedSizeGib;
  }
  /**
   * @return string
   */
  public function getRequestedSizeGib()
  {
    return $this->requestedSizeGib;
  }
  /**
   * The behavior to use when snapshot reserved space is full.
   *
   * Accepted values: SNAPSHOT_AUTO_DELETE_BEHAVIOR_UNSPECIFIED, DISABLED,
   * OLDEST_FIRST, NEWEST_FIRST
   *
   * @param self::SNAPSHOT_AUTO_DELETE_BEHAVIOR_* $snapshotAutoDeleteBehavior
   */
  public function setSnapshotAutoDeleteBehavior($snapshotAutoDeleteBehavior)
  {
    $this->snapshotAutoDeleteBehavior = $snapshotAutoDeleteBehavior;
  }
  /**
   * @return self::SNAPSHOT_AUTO_DELETE_BEHAVIOR_*
   */
  public function getSnapshotAutoDeleteBehavior()
  {
    return $this->snapshotAutoDeleteBehavior;
  }
  /**
   * Whether snapshots are enabled.
   *
   * @param bool $snapshotEnabled
   */
  public function setSnapshotEnabled($snapshotEnabled)
  {
    $this->snapshotEnabled = $snapshotEnabled;
  }
  /**
   * @return bool
   */
  public function getSnapshotEnabled()
  {
    return $this->snapshotEnabled;
  }
  /**
   * Details about snapshot space reservation and usage on the storage volume.
   *
   * @param SnapshotReservationDetail $snapshotReservationDetail
   */
  public function setSnapshotReservationDetail(SnapshotReservationDetail $snapshotReservationDetail)
  {
    $this->snapshotReservationDetail = $snapshotReservationDetail;
  }
  /**
   * @return SnapshotReservationDetail
   */
  public function getSnapshotReservationDetail()
  {
    return $this->snapshotReservationDetail;
  }
  /**
   * The state of this storage volume.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, DELETING, UPDATING,
   * COOL_OFF
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
   * The storage type for this volume.
   *
   * Accepted values: STORAGE_TYPE_UNSPECIFIED, SSD, HDD
   *
   * @param self::STORAGE_TYPE_* $storageType
   */
  public function setStorageType($storageType)
  {
    $this->storageType = $storageType;
  }
  /**
   * @return self::STORAGE_TYPE_*
   */
  public function getStorageType()
  {
    return $this->storageType;
  }
  /**
   * The workload profile for the volume.
   *
   * Accepted values: WORKLOAD_PROFILE_UNSPECIFIED, GENERIC, HANA
   *
   * @param self::WORKLOAD_PROFILE_* $workloadProfile
   */
  public function setWorkloadProfile($workloadProfile)
  {
    $this->workloadProfile = $workloadProfile;
  }
  /**
   * @return self::WORKLOAD_PROFILE_*
   */
  public function getWorkloadProfile()
  {
    return $this->workloadProfile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Volume::class, 'Google_Service_Baremetalsolution_Volume');
