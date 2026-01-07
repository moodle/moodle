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

namespace Google\Service\GKEOnPrem;

class VmwareCluster extends \Google\Model
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The PROVISIONING state indicates the cluster is being created.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The RUNNING state indicates the cluster has been created and is fully
   * usable.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The RECONCILING state indicates that the cluster is being updated. It
   * remains available, but potentially with degraded performance.
   */
  public const STATE_RECONCILING = 'RECONCILING';
  /**
   * The STOPPING state indicates the cluster is being deleted.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * The ERROR state indicates the cluster is in a broken unrecoverable state.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The DEGRADED state indicates the cluster requires user action to restore
   * full functionality.
   */
  public const STATE_DEGRADED = 'DEGRADED';
  /**
   * Required. The admin cluster this VMware user cluster belongs to. This is
   * the full resource name of the admin cluster's fleet membership. In the
   * future, references to other resource types might be allowed if admin
   * clusters are modeled as their own resources.
   *
   * @var string
   */
  public $adminClusterMembership;
  /**
   * Output only. The resource name of the VMware admin cluster hosting this
   * user cluster.
   *
   * @var string
   */
  public $adminClusterName;
  /**
   * Annotations on the VMware user cluster. This field has the same
   * restrictions as Kubernetes annotations. The total size of all keys and
   * values combined is limited to 256k. Key can have 2 segments: prefix
   * (optional) and name (required), separated by a slash (/). Prefix must be a
   * DNS subdomain. Name must be 63 characters or less, begin and end with
   * alphanumerics, with dashes (-), underscores (_), dots (.), and
   * alphanumerics between.
   *
   * @var string[]
   */
  public $annotations;
  protected $antiAffinityGroupsType = VmwareAAGConfig::class;
  protected $antiAffinityGroupsDataType = '';
  protected $authorizationType = Authorization::class;
  protected $authorizationDataType = '';
  protected $autoRepairConfigType = VmwareAutoRepairConfig::class;
  protected $autoRepairConfigDataType = '';
  protected $binaryAuthorizationType = BinaryAuthorization::class;
  protected $binaryAuthorizationDataType = '';
  protected $controlPlaneNodeType = VmwareControlPlaneNodeConfig::class;
  protected $controlPlaneNodeDataType = '';
  /**
   * Output only. The time at which VMware user cluster was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataplaneV2Type = VmwareDataplaneV2Config::class;
  protected $dataplaneV2DataType = '';
  /**
   * Output only. The time at which VMware user cluster was deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * A human readable description of this VMware user cluster.
   *
   * @var string
   */
  public $description;
  /**
   * Disable bundled ingress.
   *
   * @var bool
   */
  public $disableBundledIngress;
  /**
   * Enable advanced cluster.
   *
   * @var bool
   */
  public $enableAdvancedCluster;
  /**
   * Enable control plane V2. Default to false.
   *
   * @var bool
   */
  public $enableControlPlaneV2;
  /**
   * Output only. The DNS name of VMware user cluster's API server.
   *
   * @var string
   */
  public $endpoint;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding. Allows clients to perform consistent
   * read-modify-writes through optimistic concurrency control.
   *
   * @var string
   */
  public $etag;
  protected $fleetType = Fleet::class;
  protected $fleetDataType = '';
  protected $loadBalancerType = VmwareLoadBalancerConfig::class;
  protected $loadBalancerDataType = '';
  /**
   * Output only. The object name of the VMware OnPremUserCluster custom
   * resource on the associated admin cluster. This field is used to support
   * conflicting names when enrolling existing clusters to the API. When used as
   * a part of cluster enrollment, this field will differ from the ID in the
   * resource name. For new clusters, this field will match the user provided
   * cluster name and be visible in the last component of the resource name. It
   * is not modifiable. All users should use this name to access their cluster
   * using gkectl or kubectl and should expect to see the local name when
   * viewing admin cluster controller logs.
   *
   * @var string
   */
  public $localName;
  /**
   * Immutable. The VMware user cluster resource name.
   *
   * @var string
   */
  public $name;
  protected $networkConfigType = VmwareNetworkConfig::class;
  protected $networkConfigDataType = '';
  /**
   * Required. The Anthos clusters on the VMware version for your user cluster.
   *
   * @var string
   */
  public $onPremVersion;
  /**
   * Output only. If set, there are currently changes in flight to the VMware
   * user cluster.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The current state of VMware user cluster.
   *
   * @var string
   */
  public $state;
  protected $statusType = ResourceStatus::class;
  protected $statusDataType = '';
  protected $storageType = VmwareStorageConfig::class;
  protected $storageDataType = '';
  /**
   * Output only. The unique identifier of the VMware user cluster.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time at which VMware user cluster was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradePolicyType = VmwareClusterUpgradePolicy::class;
  protected $upgradePolicyDataType = '';
  protected $validationCheckType = ValidationCheck::class;
  protected $validationCheckDataType = '';
  protected $vcenterType = VmwareVCenterConfig::class;
  protected $vcenterDataType = '';
  /**
   * Enable VM tracking.
   *
   * @var bool
   */
  public $vmTrackingEnabled;

  /**
   * Required. The admin cluster this VMware user cluster belongs to. This is
   * the full resource name of the admin cluster's fleet membership. In the
   * future, references to other resource types might be allowed if admin
   * clusters are modeled as their own resources.
   *
   * @param string $adminClusterMembership
   */
  public function setAdminClusterMembership($adminClusterMembership)
  {
    $this->adminClusterMembership = $adminClusterMembership;
  }
  /**
   * @return string
   */
  public function getAdminClusterMembership()
  {
    return $this->adminClusterMembership;
  }
  /**
   * Output only. The resource name of the VMware admin cluster hosting this
   * user cluster.
   *
   * @param string $adminClusterName
   */
  public function setAdminClusterName($adminClusterName)
  {
    $this->adminClusterName = $adminClusterName;
  }
  /**
   * @return string
   */
  public function getAdminClusterName()
  {
    return $this->adminClusterName;
  }
  /**
   * Annotations on the VMware user cluster. This field has the same
   * restrictions as Kubernetes annotations. The total size of all keys and
   * values combined is limited to 256k. Key can have 2 segments: prefix
   * (optional) and name (required), separated by a slash (/). Prefix must be a
   * DNS subdomain. Name must be 63 characters or less, begin and end with
   * alphanumerics, with dashes (-), underscores (_), dots (.), and
   * alphanumerics between.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * AAGConfig specifies whether to spread VMware user cluster nodes across at
   * least three physical hosts in the datacenter.
   *
   * @param VmwareAAGConfig $antiAffinityGroups
   */
  public function setAntiAffinityGroups(VmwareAAGConfig $antiAffinityGroups)
  {
    $this->antiAffinityGroups = $antiAffinityGroups;
  }
  /**
   * @return VmwareAAGConfig
   */
  public function getAntiAffinityGroups()
  {
    return $this->antiAffinityGroups;
  }
  /**
   * RBAC policy that will be applied and managed by the Anthos On-Prem API.
   *
   * @param Authorization $authorization
   */
  public function setAuthorization(Authorization $authorization)
  {
    $this->authorization = $authorization;
  }
  /**
   * @return Authorization
   */
  public function getAuthorization()
  {
    return $this->authorization;
  }
  /**
   * Configuration for auto repairing.
   *
   * @param VmwareAutoRepairConfig $autoRepairConfig
   */
  public function setAutoRepairConfig(VmwareAutoRepairConfig $autoRepairConfig)
  {
    $this->autoRepairConfig = $autoRepairConfig;
  }
  /**
   * @return VmwareAutoRepairConfig
   */
  public function getAutoRepairConfig()
  {
    return $this->autoRepairConfig;
  }
  /**
   * Binary Authorization related configurations.
   *
   * @param BinaryAuthorization $binaryAuthorization
   */
  public function setBinaryAuthorization(BinaryAuthorization $binaryAuthorization)
  {
    $this->binaryAuthorization = $binaryAuthorization;
  }
  /**
   * @return BinaryAuthorization
   */
  public function getBinaryAuthorization()
  {
    return $this->binaryAuthorization;
  }
  /**
   * VMware user cluster control plane nodes must have either 1 or 3 replicas.
   *
   * @param VmwareControlPlaneNodeConfig $controlPlaneNode
   */
  public function setControlPlaneNode(VmwareControlPlaneNodeConfig $controlPlaneNode)
  {
    $this->controlPlaneNode = $controlPlaneNode;
  }
  /**
   * @return VmwareControlPlaneNodeConfig
   */
  public function getControlPlaneNode()
  {
    return $this->controlPlaneNode;
  }
  /**
   * Output only. The time at which VMware user cluster was created.
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
   * VmwareDataplaneV2Config specifies configuration for Dataplane V2.
   *
   * @param VmwareDataplaneV2Config $dataplaneV2
   */
  public function setDataplaneV2(VmwareDataplaneV2Config $dataplaneV2)
  {
    $this->dataplaneV2 = $dataplaneV2;
  }
  /**
   * @return VmwareDataplaneV2Config
   */
  public function getDataplaneV2()
  {
    return $this->dataplaneV2;
  }
  /**
   * Output only. The time at which VMware user cluster was deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * A human readable description of this VMware user cluster.
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
   * Disable bundled ingress.
   *
   * @param bool $disableBundledIngress
   */
  public function setDisableBundledIngress($disableBundledIngress)
  {
    $this->disableBundledIngress = $disableBundledIngress;
  }
  /**
   * @return bool
   */
  public function getDisableBundledIngress()
  {
    return $this->disableBundledIngress;
  }
  /**
   * Enable advanced cluster.
   *
   * @param bool $enableAdvancedCluster
   */
  public function setEnableAdvancedCluster($enableAdvancedCluster)
  {
    $this->enableAdvancedCluster = $enableAdvancedCluster;
  }
  /**
   * @return bool
   */
  public function getEnableAdvancedCluster()
  {
    return $this->enableAdvancedCluster;
  }
  /**
   * Enable control plane V2. Default to false.
   *
   * @param bool $enableControlPlaneV2
   */
  public function setEnableControlPlaneV2($enableControlPlaneV2)
  {
    $this->enableControlPlaneV2 = $enableControlPlaneV2;
  }
  /**
   * @return bool
   */
  public function getEnableControlPlaneV2()
  {
    return $this->enableControlPlaneV2;
  }
  /**
   * Output only. The DNS name of VMware user cluster's API server.
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding. Allows clients to perform consistent
   * read-modify-writes through optimistic concurrency control.
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
   * Output only. Fleet configuration for the cluster.
   *
   * @param Fleet $fleet
   */
  public function setFleet(Fleet $fleet)
  {
    $this->fleet = $fleet;
  }
  /**
   * @return Fleet
   */
  public function getFleet()
  {
    return $this->fleet;
  }
  /**
   * Load balancer configuration.
   *
   * @param VmwareLoadBalancerConfig $loadBalancer
   */
  public function setLoadBalancer(VmwareLoadBalancerConfig $loadBalancer)
  {
    $this->loadBalancer = $loadBalancer;
  }
  /**
   * @return VmwareLoadBalancerConfig
   */
  public function getLoadBalancer()
  {
    return $this->loadBalancer;
  }
  /**
   * Output only. The object name of the VMware OnPremUserCluster custom
   * resource on the associated admin cluster. This field is used to support
   * conflicting names when enrolling existing clusters to the API. When used as
   * a part of cluster enrollment, this field will differ from the ID in the
   * resource name. For new clusters, this field will match the user provided
   * cluster name and be visible in the last component of the resource name. It
   * is not modifiable. All users should use this name to access their cluster
   * using gkectl or kubectl and should expect to see the local name when
   * viewing admin cluster controller logs.
   *
   * @param string $localName
   */
  public function setLocalName($localName)
  {
    $this->localName = $localName;
  }
  /**
   * @return string
   */
  public function getLocalName()
  {
    return $this->localName;
  }
  /**
   * Immutable. The VMware user cluster resource name.
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
   * The VMware user cluster network configuration.
   *
   * @param VmwareNetworkConfig $networkConfig
   */
  public function setNetworkConfig(VmwareNetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return VmwareNetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Required. The Anthos clusters on the VMware version for your user cluster.
   *
   * @param string $onPremVersion
   */
  public function setOnPremVersion($onPremVersion)
  {
    $this->onPremVersion = $onPremVersion;
  }
  /**
   * @return string
   */
  public function getOnPremVersion()
  {
    return $this->onPremVersion;
  }
  /**
   * Output only. If set, there are currently changes in flight to the VMware
   * user cluster.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. The current state of VMware user cluster.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, RUNNING, RECONCILING,
   * STOPPING, ERROR, DEGRADED
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
   * Output only. ResourceStatus representing detailed cluster state.
   *
   * @param ResourceStatus $status
   */
  public function setStatus(ResourceStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ResourceStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Storage configuration.
   *
   * @param VmwareStorageConfig $storage
   */
  public function setStorage(VmwareStorageConfig $storage)
  {
    $this->storage = $storage;
  }
  /**
   * @return VmwareStorageConfig
   */
  public function getStorage()
  {
    return $this->storage;
  }
  /**
   * Output only. The unique identifier of the VMware user cluster.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time at which VMware user cluster was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Specifies upgrade policy for the cluster.
   *
   * @param VmwareClusterUpgradePolicy $upgradePolicy
   */
  public function setUpgradePolicy(VmwareClusterUpgradePolicy $upgradePolicy)
  {
    $this->upgradePolicy = $upgradePolicy;
  }
  /**
   * @return VmwareClusterUpgradePolicy
   */
  public function getUpgradePolicy()
  {
    return $this->upgradePolicy;
  }
  /**
   * Output only. ValidationCheck represents the result of the preflight check
   * job.
   *
   * @param ValidationCheck $validationCheck
   */
  public function setValidationCheck(ValidationCheck $validationCheck)
  {
    $this->validationCheck = $validationCheck;
  }
  /**
   * @return ValidationCheck
   */
  public function getValidationCheck()
  {
    return $this->validationCheck;
  }
  /**
   * VmwareVCenterConfig specifies vCenter config for the user cluster. If
   * unspecified, it is inherited from the admin cluster.
   *
   * @param VmwareVCenterConfig $vcenter
   */
  public function setVcenter(VmwareVCenterConfig $vcenter)
  {
    $this->vcenter = $vcenter;
  }
  /**
   * @return VmwareVCenterConfig
   */
  public function getVcenter()
  {
    return $this->vcenter;
  }
  /**
   * Enable VM tracking.
   *
   * @param bool $vmTrackingEnabled
   */
  public function setVmTrackingEnabled($vmTrackingEnabled)
  {
    $this->vmTrackingEnabled = $vmTrackingEnabled;
  }
  /**
   * @return bool
   */
  public function getVmTrackingEnabled()
  {
    return $this->vmTrackingEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareCluster::class, 'Google_Service_GKEOnPrem_VmwareCluster');
