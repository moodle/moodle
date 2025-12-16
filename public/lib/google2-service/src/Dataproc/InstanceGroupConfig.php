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

namespace Google\Service\Dataproc;

class InstanceGroupConfig extends \Google\Collection
{
  /**
   * Preemptibility is unspecified, the system will choose the appropriate
   * setting for each instance group.
   */
  public const PREEMPTIBILITY_PREEMPTIBILITY_UNSPECIFIED = 'PREEMPTIBILITY_UNSPECIFIED';
  /**
   * Instances are non-preemptible.This option is allowed for all instance
   * groups and is the only valid value for Master and Worker instance groups.
   */
  public const PREEMPTIBILITY_NON_PREEMPTIBLE = 'NON_PREEMPTIBLE';
  /**
   * Instances are preemptible
   * (https://cloud.google.com/compute/docs/instances/preemptible).This option
   * is allowed only for secondary worker
   * (https://cloud.google.com/dataproc/docs/concepts/compute/secondary-vms)
   * groups.
   */
  public const PREEMPTIBILITY_PREEMPTIBLE = 'PREEMPTIBLE';
  /**
   * Instances are Spot VMs
   * (https://cloud.google.com/compute/docs/instances/spot).This option is
   * allowed only for secondary worker
   * (https://cloud.google.com/dataproc/docs/concepts/compute/secondary-vms)
   * groups. Spot VMs are the latest version of preemptible VMs
   * (https://cloud.google.com/compute/docs/instances/preemptible), and provide
   * additional features.
   */
  public const PREEMPTIBILITY_SPOT = 'SPOT';
  protected $collection_key = 'instanceReferences';
  protected $acceleratorsType = AcceleratorConfig::class;
  protected $acceleratorsDataType = 'array';
  protected $diskConfigType = DiskConfig::class;
  protected $diskConfigDataType = '';
  /**
   * Optional. The Compute Engine image resource used for cluster instances.The
   * URI can represent an image or image family.Image examples: https://www.goog
   * leapis.com/compute/v1/projects/[project_id]/global/images/[image-id]
   * projects/[project_id]/global/images/[image-id] image-idImage family
   * examples. Dataproc will use the most recent image from the family: https://
   * www.googleapis.com/compute/v1/projects/[project_id]/global/images/family/[c
   * ustom-image-family-name]
   * projects/[project_id]/global/images/family/[custom-image-family-name]If the
   * URI is unspecified, it will be inferred from SoftwareConfig.image_version
   * or the system default.
   *
   * @var string
   */
  public $imageUri;
  protected $instanceFlexibilityPolicyType = InstanceFlexibilityPolicy::class;
  protected $instanceFlexibilityPolicyDataType = '';
  /**
   * Output only. The list of instance names. Dataproc derives the names from
   * cluster_name, num_instances, and the instance group.
   *
   * @var string[]
   */
  public $instanceNames;
  protected $instanceReferencesType = InstanceReference::class;
  protected $instanceReferencesDataType = 'array';
  /**
   * Output only. Specifies that this instance group contains preemptible
   * instances.
   *
   * @var bool
   */
  public $isPreemptible;
  /**
   * Optional. The Compute Engine machine type used for cluster instances.A full
   * URL, partial URI, or short name are valid. Examples: https://www.googleapis
   * .com/compute/v1/projects/[project_id]/zones/[zone]/machineTypes/n1-
   * standard-2 projects/[project_id]/zones/[zone]/machineTypes/n1-standard-2
   * n1-standard-2Auto Zone Exception: If you are using the Dataproc Auto Zone
   * Placement (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/auto-zone#using_auto_zone_placement) feature, you must use the
   * short name of the machine type resource, for example, n1-standard-2.
   *
   * @var string
   */
  public $machineTypeUri;
  protected $managedGroupConfigType = ManagedGroupConfig::class;
  protected $managedGroupConfigDataType = '';
  /**
   * Optional. Specifies the minimum cpu platform for the Instance Group. See
   * Dataproc -> Minimum CPU Platform
   * (https://cloud.google.com/dataproc/docs/concepts/compute/dataproc-min-cpu).
   *
   * @var string
   */
  public $minCpuPlatform;
  /**
   * Optional. The minimum number of primary worker instances to create. If
   * min_num_instances is set, cluster creation will succeed if the number of
   * primary workers created is at least equal to the min_num_instances
   * number.Example: Cluster creation request with num_instances = 5 and
   * min_num_instances = 3: If 4 VMs are created and 1 instance fails, the
   * failed VM is deleted. The cluster is resized to 4 instances and placed in a
   * RUNNING state. If 2 instances are created and 3 instances fail, the cluster
   * in placed in an ERROR state. The failed VMs are not deleted.
   *
   * @var int
   */
  public $minNumInstances;
  /**
   * Optional. The number of VM instances in the instance group. For HA cluster
   * master_config groups, must be set to 3. For standard cluster master_config
   * groups, must be set to 1.
   *
   * @var int
   */
  public $numInstances;
  /**
   * Optional. Specifies the preemptibility of the instance group.The default
   * value for master and worker groups is NON_PREEMPTIBLE. This default cannot
   * be changed.The default value for secondary instances is PREEMPTIBLE.
   *
   * @var string
   */
  public $preemptibility;
  protected $startupConfigType = StartupConfig::class;
  protected $startupConfigDataType = '';

  /**
   * Optional. The Compute Engine accelerator configuration for these instances.
   *
   * @param AcceleratorConfig[] $accelerators
   */
  public function setAccelerators($accelerators)
  {
    $this->accelerators = $accelerators;
  }
  /**
   * @return AcceleratorConfig[]
   */
  public function getAccelerators()
  {
    return $this->accelerators;
  }
  /**
   * Optional. Disk option config settings.
   *
   * @param DiskConfig $diskConfig
   */
  public function setDiskConfig(DiskConfig $diskConfig)
  {
    $this->diskConfig = $diskConfig;
  }
  /**
   * @return DiskConfig
   */
  public function getDiskConfig()
  {
    return $this->diskConfig;
  }
  /**
   * Optional. The Compute Engine image resource used for cluster instances.The
   * URI can represent an image or image family.Image examples: https://www.goog
   * leapis.com/compute/v1/projects/[project_id]/global/images/[image-id]
   * projects/[project_id]/global/images/[image-id] image-idImage family
   * examples. Dataproc will use the most recent image from the family: https://
   * www.googleapis.com/compute/v1/projects/[project_id]/global/images/family/[c
   * ustom-image-family-name]
   * projects/[project_id]/global/images/family/[custom-image-family-name]If the
   * URI is unspecified, it will be inferred from SoftwareConfig.image_version
   * or the system default.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * Optional. Instance flexibility Policy allowing a mixture of VM shapes and
   * provisioning models.
   *
   * @param InstanceFlexibilityPolicy $instanceFlexibilityPolicy
   */
  public function setInstanceFlexibilityPolicy(InstanceFlexibilityPolicy $instanceFlexibilityPolicy)
  {
    $this->instanceFlexibilityPolicy = $instanceFlexibilityPolicy;
  }
  /**
   * @return InstanceFlexibilityPolicy
   */
  public function getInstanceFlexibilityPolicy()
  {
    return $this->instanceFlexibilityPolicy;
  }
  /**
   * Output only. The list of instance names. Dataproc derives the names from
   * cluster_name, num_instances, and the instance group.
   *
   * @param string[] $instanceNames
   */
  public function setInstanceNames($instanceNames)
  {
    $this->instanceNames = $instanceNames;
  }
  /**
   * @return string[]
   */
  public function getInstanceNames()
  {
    return $this->instanceNames;
  }
  /**
   * Output only. List of references to Compute Engine instances.
   *
   * @param InstanceReference[] $instanceReferences
   */
  public function setInstanceReferences($instanceReferences)
  {
    $this->instanceReferences = $instanceReferences;
  }
  /**
   * @return InstanceReference[]
   */
  public function getInstanceReferences()
  {
    return $this->instanceReferences;
  }
  /**
   * Output only. Specifies that this instance group contains preemptible
   * instances.
   *
   * @param bool $isPreemptible
   */
  public function setIsPreemptible($isPreemptible)
  {
    $this->isPreemptible = $isPreemptible;
  }
  /**
   * @return bool
   */
  public function getIsPreemptible()
  {
    return $this->isPreemptible;
  }
  /**
   * Optional. The Compute Engine machine type used for cluster instances.A full
   * URL, partial URI, or short name are valid. Examples: https://www.googleapis
   * .com/compute/v1/projects/[project_id]/zones/[zone]/machineTypes/n1-
   * standard-2 projects/[project_id]/zones/[zone]/machineTypes/n1-standard-2
   * n1-standard-2Auto Zone Exception: If you are using the Dataproc Auto Zone
   * Placement (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/auto-zone#using_auto_zone_placement) feature, you must use the
   * short name of the machine type resource, for example, n1-standard-2.
   *
   * @param string $machineTypeUri
   */
  public function setMachineTypeUri($machineTypeUri)
  {
    $this->machineTypeUri = $machineTypeUri;
  }
  /**
   * @return string
   */
  public function getMachineTypeUri()
  {
    return $this->machineTypeUri;
  }
  /**
   * Output only. The config for Compute Engine Instance Group Manager that
   * manages this group. This is only used for preemptible instance groups.
   *
   * @param ManagedGroupConfig $managedGroupConfig
   */
  public function setManagedGroupConfig(ManagedGroupConfig $managedGroupConfig)
  {
    $this->managedGroupConfig = $managedGroupConfig;
  }
  /**
   * @return ManagedGroupConfig
   */
  public function getManagedGroupConfig()
  {
    return $this->managedGroupConfig;
  }
  /**
   * Optional. Specifies the minimum cpu platform for the Instance Group. See
   * Dataproc -> Minimum CPU Platform
   * (https://cloud.google.com/dataproc/docs/concepts/compute/dataproc-min-cpu).
   *
   * @param string $minCpuPlatform
   */
  public function setMinCpuPlatform($minCpuPlatform)
  {
    $this->minCpuPlatform = $minCpuPlatform;
  }
  /**
   * @return string
   */
  public function getMinCpuPlatform()
  {
    return $this->minCpuPlatform;
  }
  /**
   * Optional. The minimum number of primary worker instances to create. If
   * min_num_instances is set, cluster creation will succeed if the number of
   * primary workers created is at least equal to the min_num_instances
   * number.Example: Cluster creation request with num_instances = 5 and
   * min_num_instances = 3: If 4 VMs are created and 1 instance fails, the
   * failed VM is deleted. The cluster is resized to 4 instances and placed in a
   * RUNNING state. If 2 instances are created and 3 instances fail, the cluster
   * in placed in an ERROR state. The failed VMs are not deleted.
   *
   * @param int $minNumInstances
   */
  public function setMinNumInstances($minNumInstances)
  {
    $this->minNumInstances = $minNumInstances;
  }
  /**
   * @return int
   */
  public function getMinNumInstances()
  {
    return $this->minNumInstances;
  }
  /**
   * Optional. The number of VM instances in the instance group. For HA cluster
   * master_config groups, must be set to 3. For standard cluster master_config
   * groups, must be set to 1.
   *
   * @param int $numInstances
   */
  public function setNumInstances($numInstances)
  {
    $this->numInstances = $numInstances;
  }
  /**
   * @return int
   */
  public function getNumInstances()
  {
    return $this->numInstances;
  }
  /**
   * Optional. Specifies the preemptibility of the instance group.The default
   * value for master and worker groups is NON_PREEMPTIBLE. This default cannot
   * be changed.The default value for secondary instances is PREEMPTIBLE.
   *
   * Accepted values: PREEMPTIBILITY_UNSPECIFIED, NON_PREEMPTIBLE, PREEMPTIBLE,
   * SPOT
   *
   * @param self::PREEMPTIBILITY_* $preemptibility
   */
  public function setPreemptibility($preemptibility)
  {
    $this->preemptibility = $preemptibility;
  }
  /**
   * @return self::PREEMPTIBILITY_*
   */
  public function getPreemptibility()
  {
    return $this->preemptibility;
  }
  /**
   * Optional. Configuration to handle the startup of instances during cluster
   * create and update process.
   *
   * @param StartupConfig $startupConfig
   */
  public function setStartupConfig(StartupConfig $startupConfig)
  {
    $this->startupConfig = $startupConfig;
  }
  /**
   * @return StartupConfig
   */
  public function getStartupConfig()
  {
    return $this->startupConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupConfig::class, 'Google_Service_Dataproc_InstanceGroupConfig');
