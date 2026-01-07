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

namespace Google\Service\Compute;

class AttachedDiskInitializeParams extends \Google\Collection
{
  /**
   * Default value indicating Architecture is not set.
   */
  public const ARCHITECTURE_ARCHITECTURE_UNSPECIFIED = 'ARCHITECTURE_UNSPECIFIED';
  /**
   * Machines with architecture ARM64
   */
  public const ARCHITECTURE_ARM64 = 'ARM64';
  /**
   * Machines with architecture X86_64
   */
  public const ARCHITECTURE_X86_64 = 'X86_64';
  /**
   * Always recreate the disk.
   */
  public const ON_UPDATE_ACTION_RECREATE_DISK = 'RECREATE_DISK';
  /**
   * Recreate the disk if source (image, snapshot) of this disk is different
   * from source of existing disk.
   */
  public const ON_UPDATE_ACTION_RECREATE_DISK_IF_SOURCE_CHANGED = 'RECREATE_DISK_IF_SOURCE_CHANGED';
  /**
   * Use the existing disk, this is the default behaviour.
   */
  public const ON_UPDATE_ACTION_USE_EXISTING_DISK = 'USE_EXISTING_DISK';
  protected $collection_key = 'resourcePolicies';
  /**
   * The architecture of the attached disk. Valid values are arm64 or x86_64.
   *
   * @var string
   */
  public $architecture;
  /**
   * An optional description. Provide this property when creating the disk.
   *
   * @var string
   */
  public $description;
  /**
   * Specifies the disk name. If not specified, the default is to use the name
   * of the instance. If a disk with the same name already exists in the given
   * region, the existing disk is attached to the new instance and the new disk
   * is not created.
   *
   * @var string
   */
  public $diskName;
  /**
   * Specifies the size of the disk in base-2 GB. The size must be at least 10
   * GB. If you specify a sourceImage, which is required for boot disks, the
   * default size is the size of the sourceImage. If you do not specify a
   * sourceImage, the default disk size is 500 GB.
   *
   * @var string
   */
  public $diskSizeGb;
  /**
   * Specifies the disk type to use to create the instance. If not specified,
   * the default is pd-standard, specified using the full URL. For example:
   *
   * https://www.googleapis.com/compute/v1/projects/project/zones/zone/diskTypes
   * /pd-standard
   *
   * For a full list of acceptable values, seePersistent disk types. If you
   * specify this field when creating a VM, you can provide either the full or
   * partial URL. For example, the following values are valid:              - ht
   * tps://www.googleapis.com/compute/v1/projects/project/zones/zone/diskTypes/d
   * iskType     - projects/project/zones/zone/diskTypes/diskType     -
   * zones/zone/diskTypes/diskType
   *
   * If you specify this field when creating or updating an instance template or
   * all-instances configuration, specify the type of the disk, not the URL. For
   * example: pd-standard.
   *
   * @var string
   */
  public $diskType;
  /**
   * Whether this disk is using confidential compute mode.
   *
   * @var bool
   */
  public $enableConfidentialCompute;
  /**
   * Labels to apply to this disk. These can be later modified by
   * thedisks.setLabels method. This field is only applicable for persistent
   * disks.
   *
   * @var string[]
   */
  public $labels;
  /**
   * A list of publicly visible licenses. Reserved for Google's use.
   *
   * @var string[]
   */
  public $licenses;
  /**
   * Specifies which action to take on instance update with this disk. Default
   * is to use the existing disk.
   *
   * @var string
   */
  public $onUpdateAction;
  /**
   * Indicates how many IOPS to provision for the disk. This sets the number of
   * I/O operations per second that the disk can handle. Values must be between
   * 10,000 and 120,000. For more details, see theExtreme persistent disk
   * documentation.
   *
   * @var string
   */
  public $provisionedIops;
  /**
   * Indicates how much throughput to provision for the disk. This sets the
   * number of throughput mb per second that the disk can handle. Values must
   * greater than or equal to 1.
   *
   * @var string
   */
  public $provisionedThroughput;
  /**
   * Required for each regional disk associated with the instance. Specify the
   * URLs of the zones where the disk should be replicated to. You must provide
   * exactly two replica zones, and one zone must be the same as the instance
   * zone.
   *
   * @var string[]
   */
  public $replicaZones;
  /**
   * Resource manager tags to be bound to the disk. Tag keys and values have the
   * same definition as resource manager tags. Keys and values can be either in
   * numeric format, such as `tagKeys/{tag_key_id}` and `tagValues/456` or in
   * namespaced format such as `{org_id|project_id}/{tag_key_short_name}` and
   * `{tag_value_short_name}`. The field is ignored (both PUT & PATCH) when
   * empty.
   *
   * @var string[]
   */
  public $resourceManagerTags;
  /**
   * Resource policies applied to this disk for automatic snapshot creations.
   * Specified using the full or partial URL. For instance template, specify
   * only the resource policy name.
   *
   * @var string[]
   */
  public $resourcePolicies;
  /**
   * The source image to create this disk. When creating a new instance boot
   * disk, one of initializeParams.sourceImage orinitializeParams.sourceSnapshot
   * or disks.source is required.
   *
   * To create a disk with one of the public operating system images, specify
   * the image by its family name. For example, specifyfamily/debian-9 to use
   * the latest Debian 9 image:
   *
   * projects/debian-cloud/global/images/family/debian-9
   *
   * Alternatively, use a specific version of a public operating system image:
   *
   * projects/debian-cloud/global/images/debian-9-stretch-vYYYYMMDD
   *
   * To create a disk with a custom image that you created, specify the image
   * name in the following format:
   *
   * global/images/my-custom-image
   *
   * You can also specify a custom image by its image family, which returns the
   * latest version of the image in that family. Replace the image name with
   * family/family-name:
   *
   * global/images/family/my-image-family
   *
   * If the source image is deleted later, this field will not be set.
   *
   * @var string
   */
  public $sourceImage;
  protected $sourceImageEncryptionKeyType = CustomerEncryptionKey::class;
  protected $sourceImageEncryptionKeyDataType = '';
  /**
   * The source snapshot to create this disk. When creating a new instance boot
   * disk, one of initializeParams.sourceSnapshot orinitializeParams.sourceImage
   * or disks.source is required.
   *
   * To create a disk with a snapshot that you created, specify the snapshot
   * name in the following format:
   *
   * global/snapshots/my-backup
   *
   * If the source snapshot is deleted later, this field will not be set.
   *
   * Note: You cannot create VMs in bulk using a snapshot as the source. Use an
   * image instead when you create VMs using the bulk insert method.
   *
   * @var string
   */
  public $sourceSnapshot;
  protected $sourceSnapshotEncryptionKeyType = CustomerEncryptionKey::class;
  protected $sourceSnapshotEncryptionKeyDataType = '';
  /**
   * The storage pool in which the new disk is created. You can provide this as
   * a partial or full URL to the resource. For example, the following are valid
   * values:              - https://www.googleapis.com/compute/v1/projects/proje
   * ct/zones/zone/storagePools/storagePool      -
   * projects/project/zones/zone/storagePools/storagePool     -
   * zones/zone/storagePools/storagePool
   *
   * @var string
   */
  public $storagePool;

  /**
   * The architecture of the attached disk. Valid values are arm64 or x86_64.
   *
   * Accepted values: ARCHITECTURE_UNSPECIFIED, ARM64, X86_64
   *
   * @param self::ARCHITECTURE_* $architecture
   */
  public function setArchitecture($architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return self::ARCHITECTURE_*
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * An optional description. Provide this property when creating the disk.
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
   * Specifies the disk name. If not specified, the default is to use the name
   * of the instance. If a disk with the same name already exists in the given
   * region, the existing disk is attached to the new instance and the new disk
   * is not created.
   *
   * @param string $diskName
   */
  public function setDiskName($diskName)
  {
    $this->diskName = $diskName;
  }
  /**
   * @return string
   */
  public function getDiskName()
  {
    return $this->diskName;
  }
  /**
   * Specifies the size of the disk in base-2 GB. The size must be at least 10
   * GB. If you specify a sourceImage, which is required for boot disks, the
   * default size is the size of the sourceImage. If you do not specify a
   * sourceImage, the default disk size is 500 GB.
   *
   * @param string $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return string
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Specifies the disk type to use to create the instance. If not specified,
   * the default is pd-standard, specified using the full URL. For example:
   *
   * https://www.googleapis.com/compute/v1/projects/project/zones/zone/diskTypes
   * /pd-standard
   *
   * For a full list of acceptable values, seePersistent disk types. If you
   * specify this field when creating a VM, you can provide either the full or
   * partial URL. For example, the following values are valid:              - ht
   * tps://www.googleapis.com/compute/v1/projects/project/zones/zone/diskTypes/d
   * iskType     - projects/project/zones/zone/diskTypes/diskType     -
   * zones/zone/diskTypes/diskType
   *
   * If you specify this field when creating or updating an instance template or
   * all-instances configuration, specify the type of the disk, not the URL. For
   * example: pd-standard.
   *
   * @param string $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return string
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * Whether this disk is using confidential compute mode.
   *
   * @param bool $enableConfidentialCompute
   */
  public function setEnableConfidentialCompute($enableConfidentialCompute)
  {
    $this->enableConfidentialCompute = $enableConfidentialCompute;
  }
  /**
   * @return bool
   */
  public function getEnableConfidentialCompute()
  {
    return $this->enableConfidentialCompute;
  }
  /**
   * Labels to apply to this disk. These can be later modified by
   * thedisks.setLabels method. This field is only applicable for persistent
   * disks.
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
   * A list of publicly visible licenses. Reserved for Google's use.
   *
   * @param string[] $licenses
   */
  public function setLicenses($licenses)
  {
    $this->licenses = $licenses;
  }
  /**
   * @return string[]
   */
  public function getLicenses()
  {
    return $this->licenses;
  }
  /**
   * Specifies which action to take on instance update with this disk. Default
   * is to use the existing disk.
   *
   * Accepted values: RECREATE_DISK, RECREATE_DISK_IF_SOURCE_CHANGED,
   * USE_EXISTING_DISK
   *
   * @param self::ON_UPDATE_ACTION_* $onUpdateAction
   */
  public function setOnUpdateAction($onUpdateAction)
  {
    $this->onUpdateAction = $onUpdateAction;
  }
  /**
   * @return self::ON_UPDATE_ACTION_*
   */
  public function getOnUpdateAction()
  {
    return $this->onUpdateAction;
  }
  /**
   * Indicates how many IOPS to provision for the disk. This sets the number of
   * I/O operations per second that the disk can handle. Values must be between
   * 10,000 and 120,000. For more details, see theExtreme persistent disk
   * documentation.
   *
   * @param string $provisionedIops
   */
  public function setProvisionedIops($provisionedIops)
  {
    $this->provisionedIops = $provisionedIops;
  }
  /**
   * @return string
   */
  public function getProvisionedIops()
  {
    return $this->provisionedIops;
  }
  /**
   * Indicates how much throughput to provision for the disk. This sets the
   * number of throughput mb per second that the disk can handle. Values must
   * greater than or equal to 1.
   *
   * @param string $provisionedThroughput
   */
  public function setProvisionedThroughput($provisionedThroughput)
  {
    $this->provisionedThroughput = $provisionedThroughput;
  }
  /**
   * @return string
   */
  public function getProvisionedThroughput()
  {
    return $this->provisionedThroughput;
  }
  /**
   * Required for each regional disk associated with the instance. Specify the
   * URLs of the zones where the disk should be replicated to. You must provide
   * exactly two replica zones, and one zone must be the same as the instance
   * zone.
   *
   * @param string[] $replicaZones
   */
  public function setReplicaZones($replicaZones)
  {
    $this->replicaZones = $replicaZones;
  }
  /**
   * @return string[]
   */
  public function getReplicaZones()
  {
    return $this->replicaZones;
  }
  /**
   * Resource manager tags to be bound to the disk. Tag keys and values have the
   * same definition as resource manager tags. Keys and values can be either in
   * numeric format, such as `tagKeys/{tag_key_id}` and `tagValues/456` or in
   * namespaced format such as `{org_id|project_id}/{tag_key_short_name}` and
   * `{tag_value_short_name}`. The field is ignored (both PUT & PATCH) when
   * empty.
   *
   * @param string[] $resourceManagerTags
   */
  public function setResourceManagerTags($resourceManagerTags)
  {
    $this->resourceManagerTags = $resourceManagerTags;
  }
  /**
   * @return string[]
   */
  public function getResourceManagerTags()
  {
    return $this->resourceManagerTags;
  }
  /**
   * Resource policies applied to this disk for automatic snapshot creations.
   * Specified using the full or partial URL. For instance template, specify
   * only the resource policy name.
   *
   * @param string[] $resourcePolicies
   */
  public function setResourcePolicies($resourcePolicies)
  {
    $this->resourcePolicies = $resourcePolicies;
  }
  /**
   * @return string[]
   */
  public function getResourcePolicies()
  {
    return $this->resourcePolicies;
  }
  /**
   * The source image to create this disk. When creating a new instance boot
   * disk, one of initializeParams.sourceImage orinitializeParams.sourceSnapshot
   * or disks.source is required.
   *
   * To create a disk with one of the public operating system images, specify
   * the image by its family name. For example, specifyfamily/debian-9 to use
   * the latest Debian 9 image:
   *
   * projects/debian-cloud/global/images/family/debian-9
   *
   * Alternatively, use a specific version of a public operating system image:
   *
   * projects/debian-cloud/global/images/debian-9-stretch-vYYYYMMDD
   *
   * To create a disk with a custom image that you created, specify the image
   * name in the following format:
   *
   * global/images/my-custom-image
   *
   * You can also specify a custom image by its image family, which returns the
   * latest version of the image in that family. Replace the image name with
   * family/family-name:
   *
   * global/images/family/my-image-family
   *
   * If the source image is deleted later, this field will not be set.
   *
   * @param string $sourceImage
   */
  public function setSourceImage($sourceImage)
  {
    $this->sourceImage = $sourceImage;
  }
  /**
   * @return string
   */
  public function getSourceImage()
  {
    return $this->sourceImage;
  }
  /**
   * Thecustomer-supplied encryption key of the source image. Required if the
   * source image is protected by a customer-supplied encryption key.
   *
   * InstanceTemplate and InstancePropertiesPatch do not storecustomer-supplied
   * encryption keys, so you cannot create disks for instances in a managed
   * instance group if the source images are encrypted with your own keys.
   *
   * @param CustomerEncryptionKey $sourceImageEncryptionKey
   */
  public function setSourceImageEncryptionKey(CustomerEncryptionKey $sourceImageEncryptionKey)
  {
    $this->sourceImageEncryptionKey = $sourceImageEncryptionKey;
  }
  /**
   * @return CustomerEncryptionKey
   */
  public function getSourceImageEncryptionKey()
  {
    return $this->sourceImageEncryptionKey;
  }
  /**
   * The source snapshot to create this disk. When creating a new instance boot
   * disk, one of initializeParams.sourceSnapshot orinitializeParams.sourceImage
   * or disks.source is required.
   *
   * To create a disk with a snapshot that you created, specify the snapshot
   * name in the following format:
   *
   * global/snapshots/my-backup
   *
   * If the source snapshot is deleted later, this field will not be set.
   *
   * Note: You cannot create VMs in bulk using a snapshot as the source. Use an
   * image instead when you create VMs using the bulk insert method.
   *
   * @param string $sourceSnapshot
   */
  public function setSourceSnapshot($sourceSnapshot)
  {
    $this->sourceSnapshot = $sourceSnapshot;
  }
  /**
   * @return string
   */
  public function getSourceSnapshot()
  {
    return $this->sourceSnapshot;
  }
  /**
   * Thecustomer-supplied encryption key of the source snapshot.
   *
   * @param CustomerEncryptionKey $sourceSnapshotEncryptionKey
   */
  public function setSourceSnapshotEncryptionKey(CustomerEncryptionKey $sourceSnapshotEncryptionKey)
  {
    $this->sourceSnapshotEncryptionKey = $sourceSnapshotEncryptionKey;
  }
  /**
   * @return CustomerEncryptionKey
   */
  public function getSourceSnapshotEncryptionKey()
  {
    return $this->sourceSnapshotEncryptionKey;
  }
  /**
   * The storage pool in which the new disk is created. You can provide this as
   * a partial or full URL to the resource. For example, the following are valid
   * values:              - https://www.googleapis.com/compute/v1/projects/proje
   * ct/zones/zone/storagePools/storagePool      -
   * projects/project/zones/zone/storagePools/storagePool     -
   * zones/zone/storagePools/storagePool
   *
   * @param string $storagePool
   */
  public function setStoragePool($storagePool)
  {
    $this->storagePool = $storagePool;
  }
  /**
   * @return string
   */
  public function getStoragePool()
  {
    return $this->storagePool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttachedDiskInitializeParams::class, 'Google_Service_Compute_AttachedDiskInitializeParams');
