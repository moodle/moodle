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

class InstanceGroupManager extends \Google\Collection
{
  /**
   * (Default) Pagination is disabled for the group'slistManagedInstances API
   * method. maxResults and pageToken query parameters are ignored and all
   * instances are returned in a single response.
   */
  public const LIST_MANAGED_INSTANCES_RESULTS_PAGELESS = 'PAGELESS';
  /**
   * Pagination is enabled for the group's listManagedInstances API method.
   * maxResults and pageToken query parameters are respected.
   */
  public const LIST_MANAGED_INSTANCES_RESULTS_PAGINATED = 'PAGINATED';
  protected $collection_key = 'versions';
  protected $allInstancesConfigType = InstanceGroupManagerAllInstancesConfig::class;
  protected $allInstancesConfigDataType = '';
  protected $autoHealingPoliciesType = InstanceGroupManagerAutoHealingPolicy::class;
  protected $autoHealingPoliciesDataType = 'array';
  /**
   * The base instance name is a prefix that you want to attach to the names of
   * all VMs in a MIG. The maximum character length is 58 and the name must
   * comply with RFC1035 format.
   *
   * When a VM is created in the group, the MIG appends a hyphen and a random
   * four-character string to the base instance name. If you want the MIG to
   * assign sequential numbers instead of a random string, then end the base
   * instance name with a hyphen followed by one or more hash symbols. The hash
   * symbols indicate the number of digits. For example, a base instance name of
   * "vm-###" results in "vm-001" as a VM name. @pattern
   * [a-z](([-a-z0-9]{0,57})|([-a-z0-9]{0,51}-#{1,10}(\\[[0-9]{1,10}\\])?))
   *
   * @var string
   */
  public $baseInstanceName;
  /**
   * Output only. [Output Only] The creation timestamp for this managed instance
   * group inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $currentActionsType = InstanceGroupManagerActionsSummary::class;
  protected $currentActionsDataType = '';
  /**
   * An optional description of this resource.
   *
   * @var string
   */
  public $description;
  protected $distributionPolicyType = DistributionPolicy::class;
  protected $distributionPolicyDataType = '';
  /**
   * Fingerprint of this resource. This field may be used in optimistic locking.
   * It will be ignored when inserting an InstanceGroupManager. An up-to-date
   * fingerprint must be provided in order to update the InstanceGroupManager,
   * otherwise the request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an
   * InstanceGroupManager.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Output only. [Output Only] A unique identifier for this resource type. The
   * server generates this identifier.
   *
   * @var string
   */
  public $id;
  protected $instanceFlexibilityPolicyType = InstanceGroupManagerInstanceFlexibilityPolicy::class;
  protected $instanceFlexibilityPolicyDataType = '';
  /**
   * Output only. [Output Only] The URL of the Instance Group resource.
   *
   * @var string
   */
  public $instanceGroup;
  protected $instanceLifecyclePolicyType = InstanceGroupManagerInstanceLifecyclePolicy::class;
  protected $instanceLifecyclePolicyDataType = '';
  /**
   * The URL of the instance template that is specified for this managed
   * instance group. The group uses this template to create all new instances in
   * the managed instance group. The templates for existing instances in the
   * group do not change unless you run recreateInstances,
   * runapplyUpdatesToInstances, or set the group'supdatePolicy.type to
   * PROACTIVE.
   *
   * @var string
   */
  public $instanceTemplate;
  /**
   * Output only. [Output Only] The resource type, which is
   * alwayscompute#instanceGroupManager for managed instance groups.
   *
   * @var string
   */
  public $kind;
  /**
   * Pagination behavior of the listManagedInstances API method for this managed
   * instance group.
   *
   * @var string
   */
  public $listManagedInstancesResults;
  /**
   * The name of the managed instance group. The name must be 1-63 characters
   * long, and comply withRFC1035.
   *
   * @var string
   */
  public $name;
  protected $namedPortsType = NamedPort::class;
  protected $namedPortsDataType = 'array';
  /**
   * Output only. [Output Only] The URL of theregion where the managed instance
   * group resides (for regional resources).
   *
   * @var string
   */
  public $region;
  protected $resourcePoliciesType = InstanceGroupManagerResourcePolicies::class;
  protected $resourcePoliciesDataType = '';
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. [Output Only] The URL for this managed instance group. The
   * server defines this URL.
   *
   * @var string
   */
  public $selfLink;
  protected $standbyPolicyType = InstanceGroupManagerStandbyPolicy::class;
  protected $standbyPolicyDataType = '';
  protected $statefulPolicyType = StatefulPolicy::class;
  protected $statefulPolicyDataType = '';
  protected $statusType = InstanceGroupManagerStatus::class;
  protected $statusDataType = '';
  /**
   * The URLs for all TargetPool resources to which instances in
   * theinstanceGroup field are added. The target pools automatically apply to
   * all of the instances in the managed instance group.
   *
   * @var string[]
   */
  public $targetPools;
  /**
   * The target number of running instances for this managed instance group. You
   * can reduce this number by using the instanceGroupManager deleteInstances or
   * abandonInstances methods. Resizing the group also changes this number.
   *
   * @var int
   */
  public $targetSize;
  /**
   * The target number of stopped instances for this managed instance group.
   * This number changes when you:         - Stop instance using the
   * stopInstances    method or start instances using the startInstances
   * method.    - Manually change the targetStoppedSize using the update
   * method.
   *
   * @var int
   */
  public $targetStoppedSize;
  /**
   * The target number of suspended instances for this managed instance group.
   * This number changes when you:         - Suspend instance using the
   * suspendInstances    method or resume instances using the resumeInstances
   * method.    - Manually change the targetSuspendedSize using the update
   * method.
   *
   * @var int
   */
  public $targetSuspendedSize;
  protected $updatePolicyType = InstanceGroupManagerUpdatePolicy::class;
  protected $updatePolicyDataType = '';
  protected $versionsType = InstanceGroupManagerVersion::class;
  protected $versionsDataType = 'array';
  /**
   * Output only. [Output Only] The URL of azone where the managed instance
   * group is located (for zonal resources).
   *
   * @var string
   */
  public $zone;

  /**
   * Specifies configuration that overrides the instance template configuration
   * for the group.
   *
   * @param InstanceGroupManagerAllInstancesConfig $allInstancesConfig
   */
  public function setAllInstancesConfig(InstanceGroupManagerAllInstancesConfig $allInstancesConfig)
  {
    $this->allInstancesConfig = $allInstancesConfig;
  }
  /**
   * @return InstanceGroupManagerAllInstancesConfig
   */
  public function getAllInstancesConfig()
  {
    return $this->allInstancesConfig;
  }
  /**
   * The autohealing policy for this managed instance group. You can specify
   * only one value.
   *
   * @param InstanceGroupManagerAutoHealingPolicy[] $autoHealingPolicies
   */
  public function setAutoHealingPolicies($autoHealingPolicies)
  {
    $this->autoHealingPolicies = $autoHealingPolicies;
  }
  /**
   * @return InstanceGroupManagerAutoHealingPolicy[]
   */
  public function getAutoHealingPolicies()
  {
    return $this->autoHealingPolicies;
  }
  /**
   * The base instance name is a prefix that you want to attach to the names of
   * all VMs in a MIG. The maximum character length is 58 and the name must
   * comply with RFC1035 format.
   *
   * When a VM is created in the group, the MIG appends a hyphen and a random
   * four-character string to the base instance name. If you want the MIG to
   * assign sequential numbers instead of a random string, then end the base
   * instance name with a hyphen followed by one or more hash symbols. The hash
   * symbols indicate the number of digits. For example, a base instance name of
   * "vm-###" results in "vm-001" as a VM name. @pattern
   * [a-z](([-a-z0-9]{0,57})|([-a-z0-9]{0,51}-#{1,10}(\\[[0-9]{1,10}\\])?))
   *
   * @param string $baseInstanceName
   */
  public function setBaseInstanceName($baseInstanceName)
  {
    $this->baseInstanceName = $baseInstanceName;
  }
  /**
   * @return string
   */
  public function getBaseInstanceName()
  {
    return $this->baseInstanceName;
  }
  /**
   * Output only. [Output Only] The creation timestamp for this managed instance
   * group inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * Output only. [Output Only] The list of instance actions and the number of
   * instances in this managed instance group that are scheduled for each of
   * those actions.
   *
   * @param InstanceGroupManagerActionsSummary $currentActions
   */
  public function setCurrentActions(InstanceGroupManagerActionsSummary $currentActions)
  {
    $this->currentActions = $currentActions;
  }
  /**
   * @return InstanceGroupManagerActionsSummary
   */
  public function getCurrentActions()
  {
    return $this->currentActions;
  }
  /**
   * An optional description of this resource.
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
   * Policy specifying the intended distribution of managed instances across
   * zones in a regional managed instance group.
   *
   * @param DistributionPolicy $distributionPolicy
   */
  public function setDistributionPolicy(DistributionPolicy $distributionPolicy)
  {
    $this->distributionPolicy = $distributionPolicy;
  }
  /**
   * @return DistributionPolicy
   */
  public function getDistributionPolicy()
  {
    return $this->distributionPolicy;
  }
  /**
   * Fingerprint of this resource. This field may be used in optimistic locking.
   * It will be ignored when inserting an InstanceGroupManager. An up-to-date
   * fingerprint must be provided in order to update the InstanceGroupManager,
   * otherwise the request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an
   * InstanceGroupManager.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Output only. [Output Only] A unique identifier for this resource type. The
   * server generates this identifier.
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
   * Instance flexibility allowing MIG to create VMs from multiple types of
   * machines. Instance flexibility configuration on MIG overrides instance
   * template configuration.
   *
   * @param InstanceGroupManagerInstanceFlexibilityPolicy $instanceFlexibilityPolicy
   */
  public function setInstanceFlexibilityPolicy(InstanceGroupManagerInstanceFlexibilityPolicy $instanceFlexibilityPolicy)
  {
    $this->instanceFlexibilityPolicy = $instanceFlexibilityPolicy;
  }
  /**
   * @return InstanceGroupManagerInstanceFlexibilityPolicy
   */
  public function getInstanceFlexibilityPolicy()
  {
    return $this->instanceFlexibilityPolicy;
  }
  /**
   * Output only. [Output Only] The URL of the Instance Group resource.
   *
   * @param string $instanceGroup
   */
  public function setInstanceGroup($instanceGroup)
  {
    $this->instanceGroup = $instanceGroup;
  }
  /**
   * @return string
   */
  public function getInstanceGroup()
  {
    return $this->instanceGroup;
  }
  /**
   * The repair policy for this managed instance group.
   *
   * @param InstanceGroupManagerInstanceLifecyclePolicy $instanceLifecyclePolicy
   */
  public function setInstanceLifecyclePolicy(InstanceGroupManagerInstanceLifecyclePolicy $instanceLifecyclePolicy)
  {
    $this->instanceLifecyclePolicy = $instanceLifecyclePolicy;
  }
  /**
   * @return InstanceGroupManagerInstanceLifecyclePolicy
   */
  public function getInstanceLifecyclePolicy()
  {
    return $this->instanceLifecyclePolicy;
  }
  /**
   * The URL of the instance template that is specified for this managed
   * instance group. The group uses this template to create all new instances in
   * the managed instance group. The templates for existing instances in the
   * group do not change unless you run recreateInstances,
   * runapplyUpdatesToInstances, or set the group'supdatePolicy.type to
   * PROACTIVE.
   *
   * @param string $instanceTemplate
   */
  public function setInstanceTemplate($instanceTemplate)
  {
    $this->instanceTemplate = $instanceTemplate;
  }
  /**
   * @return string
   */
  public function getInstanceTemplate()
  {
    return $this->instanceTemplate;
  }
  /**
   * Output only. [Output Only] The resource type, which is
   * alwayscompute#instanceGroupManager for managed instance groups.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Pagination behavior of the listManagedInstances API method for this managed
   * instance group.
   *
   * Accepted values: PAGELESS, PAGINATED
   *
   * @param self::LIST_MANAGED_INSTANCES_RESULTS_* $listManagedInstancesResults
   */
  public function setListManagedInstancesResults($listManagedInstancesResults)
  {
    $this->listManagedInstancesResults = $listManagedInstancesResults;
  }
  /**
   * @return self::LIST_MANAGED_INSTANCES_RESULTS_*
   */
  public function getListManagedInstancesResults()
  {
    return $this->listManagedInstancesResults;
  }
  /**
   * The name of the managed instance group. The name must be 1-63 characters
   * long, and comply withRFC1035.
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
   * [Output Only] Named ports configured on the Instance Groups complementary
   * to this Instance Group Manager.
   *
   * @param NamedPort[] $namedPorts
   */
  public function setNamedPorts($namedPorts)
  {
    $this->namedPorts = $namedPorts;
  }
  /**
   * @return NamedPort[]
   */
  public function getNamedPorts()
  {
    return $this->namedPorts;
  }
  /**
   * Output only. [Output Only] The URL of theregion where the managed instance
   * group resides (for regional resources).
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Resource policies for this managed instance group.
   *
   * @param InstanceGroupManagerResourcePolicies $resourcePolicies
   */
  public function setResourcePolicies(InstanceGroupManagerResourcePolicies $resourcePolicies)
  {
    $this->resourcePolicies = $resourcePolicies;
  }
  /**
   * @return InstanceGroupManagerResourcePolicies
   */
  public function getResourcePolicies()
  {
    return $this->resourcePolicies;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
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
   * Output only. [Output Only] Reserved for future use.
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
   * Output only. [Output Only] The URL for this managed instance group. The
   * server defines this URL.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Standby policy for stopped and suspended instances.
   *
   * @param InstanceGroupManagerStandbyPolicy $standbyPolicy
   */
  public function setStandbyPolicy(InstanceGroupManagerStandbyPolicy $standbyPolicy)
  {
    $this->standbyPolicy = $standbyPolicy;
  }
  /**
   * @return InstanceGroupManagerStandbyPolicy
   */
  public function getStandbyPolicy()
  {
    return $this->standbyPolicy;
  }
  /**
   * Stateful configuration for this Instanced Group Manager
   *
   * @param StatefulPolicy $statefulPolicy
   */
  public function setStatefulPolicy(StatefulPolicy $statefulPolicy)
  {
    $this->statefulPolicy = $statefulPolicy;
  }
  /**
   * @return StatefulPolicy
   */
  public function getStatefulPolicy()
  {
    return $this->statefulPolicy;
  }
  /**
   * Output only. [Output Only] The status of this managed instance group.
   *
   * @param InstanceGroupManagerStatus $status
   */
  public function setStatus(InstanceGroupManagerStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return InstanceGroupManagerStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The URLs for all TargetPool resources to which instances in
   * theinstanceGroup field are added. The target pools automatically apply to
   * all of the instances in the managed instance group.
   *
   * @param string[] $targetPools
   */
  public function setTargetPools($targetPools)
  {
    $this->targetPools = $targetPools;
  }
  /**
   * @return string[]
   */
  public function getTargetPools()
  {
    return $this->targetPools;
  }
  /**
   * The target number of running instances for this managed instance group. You
   * can reduce this number by using the instanceGroupManager deleteInstances or
   * abandonInstances methods. Resizing the group also changes this number.
   *
   * @param int $targetSize
   */
  public function setTargetSize($targetSize)
  {
    $this->targetSize = $targetSize;
  }
  /**
   * @return int
   */
  public function getTargetSize()
  {
    return $this->targetSize;
  }
  /**
   * The target number of stopped instances for this managed instance group.
   * This number changes when you:         - Stop instance using the
   * stopInstances    method or start instances using the startInstances
   * method.    - Manually change the targetStoppedSize using the update
   * method.
   *
   * @param int $targetStoppedSize
   */
  public function setTargetStoppedSize($targetStoppedSize)
  {
    $this->targetStoppedSize = $targetStoppedSize;
  }
  /**
   * @return int
   */
  public function getTargetStoppedSize()
  {
    return $this->targetStoppedSize;
  }
  /**
   * The target number of suspended instances for this managed instance group.
   * This number changes when you:         - Suspend instance using the
   * suspendInstances    method or resume instances using the resumeInstances
   * method.    - Manually change the targetSuspendedSize using the update
   * method.
   *
   * @param int $targetSuspendedSize
   */
  public function setTargetSuspendedSize($targetSuspendedSize)
  {
    $this->targetSuspendedSize = $targetSuspendedSize;
  }
  /**
   * @return int
   */
  public function getTargetSuspendedSize()
  {
    return $this->targetSuspendedSize;
  }
  /**
   * The update policy for this managed instance group.
   *
   * @param InstanceGroupManagerUpdatePolicy $updatePolicy
   */
  public function setUpdatePolicy(InstanceGroupManagerUpdatePolicy $updatePolicy)
  {
    $this->updatePolicy = $updatePolicy;
  }
  /**
   * @return InstanceGroupManagerUpdatePolicy
   */
  public function getUpdatePolicy()
  {
    return $this->updatePolicy;
  }
  /**
   * Specifies the instance templates used by this managed instance group to
   * create instances.
   *
   * Each version is defined by an instanceTemplate and aname. Every version can
   * appear at most once per instance group. This field overrides the top-level
   * instanceTemplate field. Read more about therelationships between these
   * fields. Exactly one version must leave thetargetSize field unset. That
   * version will be applied to all remaining instances. For more information,
   * read aboutcanary updates.
   *
   * @param InstanceGroupManagerVersion[] $versions
   */
  public function setVersions($versions)
  {
    $this->versions = $versions;
  }
  /**
   * @return InstanceGroupManagerVersion[]
   */
  public function getVersions()
  {
    return $this->versions;
  }
  /**
   * Output only. [Output Only] The URL of azone where the managed instance
   * group is located (for zonal resources).
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManager::class, 'Google_Service_Compute_InstanceGroupManager');
