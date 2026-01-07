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

class VolumeConfig extends \Google\Collection
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
   * Unspecified value.
   */
  public const PROTOCOL_PROTOCOL_UNSPECIFIED = 'PROTOCOL_UNSPECIFIED';
  /**
   * Fibre channel.
   */
  public const PROTOCOL_PROTOCOL_FC = 'PROTOCOL_FC';
  /**
   * Network file system.
   */
  public const PROTOCOL_PROTOCOL_NFS = 'PROTOCOL_NFS';
  /**
   * The unspecified type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * This Volume is on flash.
   */
  public const TYPE_FLASH = 'FLASH';
  /**
   * This Volume is on disk.
   */
  public const TYPE_DISK = 'DISK';
  protected $collection_key = 'nfsExports';
  /**
   * The GCP service of the storage volume. Available gcp_service are in
   * https://cloud.google.com/bare-metal/docs/bms-planning.
   *
   * @var string
   */
  public $gcpService;
  /**
   * A transient unique identifier to identify a volume within an
   * ProvisioningConfig request.
   *
   * @var string
   */
  public $id;
  protected $lunRangesType = LunRange::class;
  protected $lunRangesDataType = 'array';
  /**
   * Machine ids connected to this volume. Set only when protocol is
   * PROTOCOL_FC.
   *
   * @var string[]
   */
  public $machineIds;
  /**
   * Output only. The name of the volume config.
   *
   * @var string
   */
  public $name;
  protected $nfsExportsType = NfsExport::class;
  protected $nfsExportsDataType = 'array';
  /**
   * Performance tier of the Volume. Default is SHARED.
   *
   * @var string
   */
  public $performanceTier;
  /**
   * Volume protocol.
   *
   * @var string
   */
  public $protocol;
  /**
   * The requested size of this volume, in GB.
   *
   * @var int
   */
  public $sizeGb;
  /**
   * Whether snapshots should be enabled.
   *
   * @var bool
   */
  public $snapshotsEnabled;
  /**
   * The type of this Volume.
   *
   * @var string
   */
  public $type;
  /**
   * User note field, it can be used by customers to add additional information
   * for the BMS Ops team .
   *
   * @var string
   */
  public $userNote;

  /**
   * The GCP service of the storage volume. Available gcp_service are in
   * https://cloud.google.com/bare-metal/docs/bms-planning.
   *
   * @param string $gcpService
   */
  public function setGcpService($gcpService)
  {
    $this->gcpService = $gcpService;
  }
  /**
   * @return string
   */
  public function getGcpService()
  {
    return $this->gcpService;
  }
  /**
   * A transient unique identifier to identify a volume within an
   * ProvisioningConfig request.
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
   * LUN ranges to be configured. Set only when protocol is PROTOCOL_FC.
   *
   * @param LunRange[] $lunRanges
   */
  public function setLunRanges($lunRanges)
  {
    $this->lunRanges = $lunRanges;
  }
  /**
   * @return LunRange[]
   */
  public function getLunRanges()
  {
    return $this->lunRanges;
  }
  /**
   * Machine ids connected to this volume. Set only when protocol is
   * PROTOCOL_FC.
   *
   * @param string[] $machineIds
   */
  public function setMachineIds($machineIds)
  {
    $this->machineIds = $machineIds;
  }
  /**
   * @return string[]
   */
  public function getMachineIds()
  {
    return $this->machineIds;
  }
  /**
   * Output only. The name of the volume config.
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
   * NFS exports. Set only when protocol is PROTOCOL_NFS.
   *
   * @param NfsExport[] $nfsExports
   */
  public function setNfsExports($nfsExports)
  {
    $this->nfsExports = $nfsExports;
  }
  /**
   * @return NfsExport[]
   */
  public function getNfsExports()
  {
    return $this->nfsExports;
  }
  /**
   * Performance tier of the Volume. Default is SHARED.
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
   * Volume protocol.
   *
   * Accepted values: PROTOCOL_UNSPECIFIED, PROTOCOL_FC, PROTOCOL_NFS
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
   * The requested size of this volume, in GB.
   *
   * @param int $sizeGb
   */
  public function setSizeGb($sizeGb)
  {
    $this->sizeGb = $sizeGb;
  }
  /**
   * @return int
   */
  public function getSizeGb()
  {
    return $this->sizeGb;
  }
  /**
   * Whether snapshots should be enabled.
   *
   * @param bool $snapshotsEnabled
   */
  public function setSnapshotsEnabled($snapshotsEnabled)
  {
    $this->snapshotsEnabled = $snapshotsEnabled;
  }
  /**
   * @return bool
   */
  public function getSnapshotsEnabled()
  {
    return $this->snapshotsEnabled;
  }
  /**
   * The type of this Volume.
   *
   * Accepted values: TYPE_UNSPECIFIED, FLASH, DISK
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * User note field, it can be used by customers to add additional information
   * for the BMS Ops team .
   *
   * @param string $userNote
   */
  public function setUserNote($userNote)
  {
    $this->userNote = $userNote;
  }
  /**
   * @return string
   */
  public function getUserNote()
  {
    return $this->userNote;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeConfig::class, 'Google_Service_Baremetalsolution_VolumeConfig');
