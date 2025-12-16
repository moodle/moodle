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

namespace Google\Service\CloudFilestore;

class Instance extends \Google\Collection
{
  /**
   * FILE_PROTOCOL_UNSPECIFIED serves a "not set" default value when a
   * FileProtocol is a separate field in a message.
   */
  public const PROTOCOL_FILE_PROTOCOL_UNSPECIFIED = 'FILE_PROTOCOL_UNSPECIFIED';
  /**
   * NFS 3.0.
   */
  public const PROTOCOL_NFS_V3 = 'NFS_V3';
  /**
   * NFS 4.1.
   */
  public const PROTOCOL_NFS_V4_1 = 'NFS_V4_1';
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The instance is available for use.
   */
  public const STATE_READY = 'READY';
  /**
   * Work is being done on the instance. You can get further details from the
   * `statusMessage` field of the `Instance` resource.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  /**
   * The instance is shutting down.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The instance is experiencing an issue and might be unusable. You can get
   * further details from the `statusMessage` field of the `Instance` resource.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The instance is restoring a backup to an existing file share and may be
   * unusable during this time.
   */
  public const STATE_RESTORING = 'RESTORING';
  /**
   * The instance is suspended. You can get further details from the
   * `suspension_reasons` field of the `Instance` resource.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The instance is in the process of becoming suspended.
   */
  public const STATE_SUSPENDING = 'SUSPENDING';
  /**
   * The instance is in the process of becoming active.
   */
  public const STATE_RESUMING = 'RESUMING';
  /**
   * The instance is reverting to a snapshot.
   */
  public const STATE_REVERTING = 'REVERTING';
  /**
   * The replica instance is being promoted.
   */
  public const STATE_PROMOTING = 'PROMOTING';
  /**
   * Not set.
   */
  public const TIER_TIER_UNSPECIFIED = 'TIER_UNSPECIFIED';
  /**
   * STANDARD tier. BASIC_HDD is the preferred term for this tier.
   */
  public const TIER_STANDARD = 'STANDARD';
  /**
   * PREMIUM tier. BASIC_SSD is the preferred term for this tier.
   */
  public const TIER_PREMIUM = 'PREMIUM';
  /**
   * BASIC instances offer a maximum capacity of 63.9 TB. BASIC_HDD is an alias
   * for STANDARD Tier, offering economical performance backed by HDD.
   */
  public const TIER_BASIC_HDD = 'BASIC_HDD';
  /**
   * BASIC instances offer a maximum capacity of 63.9 TB. BASIC_SSD is an alias
   * for PREMIUM Tier, and offers improved performance backed by SSD.
   */
  public const TIER_BASIC_SSD = 'BASIC_SSD';
  /**
   * HIGH_SCALE instances offer expanded capacity and performance scaling
   * capabilities.
   */
  public const TIER_HIGH_SCALE_SSD = 'HIGH_SCALE_SSD';
  /**
   * ENTERPRISE instances offer the features and availability needed for
   * mission-critical workloads.
   */
  public const TIER_ENTERPRISE = 'ENTERPRISE';
  /**
   * ZONAL instances offer expanded capacity and performance scaling
   * capabilities.
   */
  public const TIER_ZONAL = 'ZONAL';
  /**
   * REGIONAL instances offer the features and availability needed for mission-
   * critical workloads.
   */
  public const TIER_REGIONAL = 'REGIONAL';
  protected $collection_key = 'suspensionReasons';
  /**
   * Output only. The incremental increase or decrease in capacity, designated
   * in some number of GB.
   *
   * @var string
   */
  public $capacityStepSizeGb;
  /**
   * Output only. The time when the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Indicates whether this instance supports configuring its
   * performance. If true, the user can configure the instance's performance by
   * using the 'performance_config' field.
   *
   * @var bool
   */
  public $customPerformanceSupported;
  /**
   * Optional. Indicates whether the instance is protected against deletion.
   *
   * @var bool
   */
  public $deletionProtectionEnabled;
  /**
   * Optional. The reason for enabling deletion protection.
   *
   * @var string
   */
  public $deletionProtectionReason;
  /**
   * The description of the instance (2048 characters or less).
   *
   * @var string
   */
  public $description;
  protected $directoryServicesType = DirectoryServicesConfig::class;
  protected $directoryServicesDataType = '';
  /**
   * Server-specified ETag for the instance resource to prevent simultaneous
   * updates from overwriting each other.
   *
   * @var string
   */
  public $etag;
  protected $fileSharesType = FileShareConfig::class;
  protected $fileSharesDataType = 'array';
  /**
   * KMS key name used for data encryption.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Resource labels to represent user provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The maximum capacity of the instance in GB.
   *
   * @var string
   */
  public $maxCapacityGb;
  /**
   * Output only. The minimum capacity of the instance in GB.
   *
   * @var string
   */
  public $minCapacityGb;
  /**
   * Output only. The resource name of the instance, in the format
   * `projects/{project}/locations/{location}/instances/{instance}`.
   *
   * @var string
   */
  public $name;
  protected $networksType = NetworkConfig::class;
  protected $networksDataType = 'array';
  protected $performanceConfigType = PerformanceConfig::class;
  protected $performanceConfigDataType = '';
  protected $performanceLimitsType = PerformanceLimits::class;
  protected $performanceLimitsDataType = '';
  /**
   * Immutable. The protocol indicates the access protocol for all shares in the
   * instance. This field is immutable and it cannot be changed after the
   * instance has been created. Default value: `NFS_V3`.
   *
   * @var string
   */
  public $protocol;
  protected $replicationType = Replication::class;
  protected $replicationDataType = '';
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The instance state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Additional information about the instance state, if available.
   *
   * @var string
   */
  public $statusMessage;
  /**
   * Output only. Field indicates all the reasons the instance is in "SUSPENDED"
   * state.
   *
   * @var string[]
   */
  public $suspensionReasons;
  /**
   * Optional. Input only. Immutable. Tag key-value pairs bound to this
   * resource. Each key must be a namespaced name and each value a short name.
   * Example: "123456789012/environment" : "production",
   * "123456789013/costCenter" : "marketing" See the documentation for more
   * information: - Namespaced name: https://cloud.google.com/resource-
   * manager/docs/tags/tags-creating-and-managing#retrieving_tag_key - Short
   * name: https://cloud.google.com/resource-manager/docs/tags/tags-creating-
   * and-managing#retrieving_tag_value
   *
   * @var string[]
   */
  public $tags;
  /**
   * The service tier of the instance.
   *
   * @var string
   */
  public $tier;

  /**
   * Output only. The incremental increase or decrease in capacity, designated
   * in some number of GB.
   *
   * @param string $capacityStepSizeGb
   */
  public function setCapacityStepSizeGb($capacityStepSizeGb)
  {
    $this->capacityStepSizeGb = $capacityStepSizeGb;
  }
  /**
   * @return string
   */
  public function getCapacityStepSizeGb()
  {
    return $this->capacityStepSizeGb;
  }
  /**
   * Output only. The time when the instance was created.
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
   * Output only. Indicates whether this instance supports configuring its
   * performance. If true, the user can configure the instance's performance by
   * using the 'performance_config' field.
   *
   * @param bool $customPerformanceSupported
   */
  public function setCustomPerformanceSupported($customPerformanceSupported)
  {
    $this->customPerformanceSupported = $customPerformanceSupported;
  }
  /**
   * @return bool
   */
  public function getCustomPerformanceSupported()
  {
    return $this->customPerformanceSupported;
  }
  /**
   * Optional. Indicates whether the instance is protected against deletion.
   *
   * @param bool $deletionProtectionEnabled
   */
  public function setDeletionProtectionEnabled($deletionProtectionEnabled)
  {
    $this->deletionProtectionEnabled = $deletionProtectionEnabled;
  }
  /**
   * @return bool
   */
  public function getDeletionProtectionEnabled()
  {
    return $this->deletionProtectionEnabled;
  }
  /**
   * Optional. The reason for enabling deletion protection.
   *
   * @param string $deletionProtectionReason
   */
  public function setDeletionProtectionReason($deletionProtectionReason)
  {
    $this->deletionProtectionReason = $deletionProtectionReason;
  }
  /**
   * @return string
   */
  public function getDeletionProtectionReason()
  {
    return $this->deletionProtectionReason;
  }
  /**
   * The description of the instance (2048 characters or less).
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
   * Optional. Directory Services configuration for Kerberos-based
   * authentication. Should only be set if protocol is "NFS_V4_1".
   *
   * @param DirectoryServicesConfig $directoryServices
   */
  public function setDirectoryServices(DirectoryServicesConfig $directoryServices)
  {
    $this->directoryServices = $directoryServices;
  }
  /**
   * @return DirectoryServicesConfig
   */
  public function getDirectoryServices()
  {
    return $this->directoryServices;
  }
  /**
   * Server-specified ETag for the instance resource to prevent simultaneous
   * updates from overwriting each other.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * File system shares on the instance. For this version, only a single file
   * share is supported.
   *
   * @param FileShareConfig[] $fileShares
   */
  public function setFileShares($fileShares)
  {
    $this->fileShares = $fileShares;
  }
  /**
   * @return FileShareConfig[]
   */
  public function getFileShares()
  {
    return $this->fileShares;
  }
  /**
   * KMS key name used for data encryption.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
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
   * Output only. The maximum capacity of the instance in GB.
   *
   * @param string $maxCapacityGb
   */
  public function setMaxCapacityGb($maxCapacityGb)
  {
    $this->maxCapacityGb = $maxCapacityGb;
  }
  /**
   * @return string
   */
  public function getMaxCapacityGb()
  {
    return $this->maxCapacityGb;
  }
  /**
   * Output only. The minimum capacity of the instance in GB.
   *
   * @param string $minCapacityGb
   */
  public function setMinCapacityGb($minCapacityGb)
  {
    $this->minCapacityGb = $minCapacityGb;
  }
  /**
   * @return string
   */
  public function getMinCapacityGb()
  {
    return $this->minCapacityGb;
  }
  /**
   * Output only. The resource name of the instance, in the format
   * `projects/{project}/locations/{location}/instances/{instance}`.
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
   * VPC networks to which the instance is connected. For this version, only a
   * single network is supported.
   *
   * @param NetworkConfig[] $networks
   */
  public function setNetworks($networks)
  {
    $this->networks = $networks;
  }
  /**
   * @return NetworkConfig[]
   */
  public function getNetworks()
  {
    return $this->networks;
  }
  /**
   * Optional. Used to configure performance.
   *
   * @param PerformanceConfig $performanceConfig
   */
  public function setPerformanceConfig(PerformanceConfig $performanceConfig)
  {
    $this->performanceConfig = $performanceConfig;
  }
  /**
   * @return PerformanceConfig
   */
  public function getPerformanceConfig()
  {
    return $this->performanceConfig;
  }
  /**
   * Output only. Used for getting performance limits.
   *
   * @param PerformanceLimits $performanceLimits
   */
  public function setPerformanceLimits(PerformanceLimits $performanceLimits)
  {
    $this->performanceLimits = $performanceLimits;
  }
  /**
   * @return PerformanceLimits
   */
  public function getPerformanceLimits()
  {
    return $this->performanceLimits;
  }
  /**
   * Immutable. The protocol indicates the access protocol for all shares in the
   * instance. This field is immutable and it cannot be changed after the
   * instance has been created. Default value: `NFS_V3`.
   *
   * Accepted values: FILE_PROTOCOL_UNSPECIFIED, NFS_V3, NFS_V4_1
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
   * Optional. Replication configuration.
   *
   * @param Replication $replication
   */
  public function setReplication(Replication $replication)
  {
    $this->replication = $replication;
  }
  /**
   * @return Replication
   */
  public function getReplication()
  {
    return $this->replication;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The instance state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, REPAIRING, DELETING,
   * ERROR, RESTORING, SUSPENDED, SUSPENDING, RESUMING, REVERTING, PROMOTING
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
   * Output only. Additional information about the instance state, if available.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * Output only. Field indicates all the reasons the instance is in "SUSPENDED"
   * state.
   *
   * @param string[] $suspensionReasons
   */
  public function setSuspensionReasons($suspensionReasons)
  {
    $this->suspensionReasons = $suspensionReasons;
  }
  /**
   * @return string[]
   */
  public function getSuspensionReasons()
  {
    return $this->suspensionReasons;
  }
  /**
   * Optional. Input only. Immutable. Tag key-value pairs bound to this
   * resource. Each key must be a namespaced name and each value a short name.
   * Example: "123456789012/environment" : "production",
   * "123456789013/costCenter" : "marketing" See the documentation for more
   * information: - Namespaced name: https://cloud.google.com/resource-
   * manager/docs/tags/tags-creating-and-managing#retrieving_tag_key - Short
   * name: https://cloud.google.com/resource-manager/docs/tags/tags-creating-
   * and-managing#retrieving_tag_value
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * The service tier of the instance.
   *
   * Accepted values: TIER_UNSPECIFIED, STANDARD, PREMIUM, BASIC_HDD, BASIC_SSD,
   * HIGH_SCALE_SSD, ENTERPRISE, ZONAL, REGIONAL
   *
   * @param self::TIER_* $tier
   */
  public function setTier($tier)
  {
    $this->tier = $tier;
  }
  /**
   * @return self::TIER_*
   */
  public function getTier()
  {
    return $this->tier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_CloudFilestore_Instance');
