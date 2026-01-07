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

class VmwareAdminCluster extends \Google\Model
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
  protected $addonNodeType = VmwareAdminAddonNodeConfig::class;
  protected $addonNodeDataType = '';
  /**
   * Annotations on the VMware admin cluster. This field has the same
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
  protected $authorizationType = VmwareAdminAuthorizationConfig::class;
  protected $authorizationDataType = '';
  protected $autoRepairConfigType = VmwareAutoRepairConfig::class;
  protected $autoRepairConfigDataType = '';
  /**
   * The bootstrap cluster this VMware admin cluster belongs to.
   *
   * @var string
   */
  public $bootstrapClusterMembership;
  protected $controlPlaneNodeType = VmwareAdminControlPlaneNodeConfig::class;
  protected $controlPlaneNodeDataType = '';
  /**
   * Output only. The time at which VMware admin cluster was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A human readable description of this VMware admin cluster.
   *
   * @var string
   */
  public $description;
  /**
   * Enable advanced cluster.
   *
   * @var bool
   */
  public $enableAdvancedCluster;
  /**
   * Output only. The DNS name of VMware admin cluster's API server.
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
  /**
   * The OS image type for the VMware admin cluster.
   *
   * @var string
   */
  public $imageType;
  protected $loadBalancerType = VmwareAdminLoadBalancerConfig::class;
  protected $loadBalancerDataType = '';
  /**
   * Output only. The object name of the VMware OnPremAdminCluster custom
   * resource. This field is used to support conflicting names when enrolling
   * existing clusters to the API. When used as a part of cluster enrollment,
   * this field will differ from the ID in the resource name. For new clusters,
   * this field will match the user provided cluster name and be visible in the
   * last component of the resource name. It is not modifiable. All users should
   * use this name to access their cluster using gkectl or kubectl and should
   * expect to see the local name when viewing admin cluster controller logs.
   *
   * @var string
   */
  public $localName;
  /**
   * Immutable. The VMware admin cluster resource name.
   *
   * @var string
   */
  public $name;
  protected $networkConfigType = VmwareAdminNetworkConfig::class;
  protected $networkConfigDataType = '';
  /**
   * The Anthos clusters on the VMware version for the admin cluster.
   *
   * @var string
   */
  public $onPremVersion;
  protected $platformConfigType = VmwarePlatformConfig::class;
  protected $platformConfigDataType = '';
  protected $preparedSecretsType = VmwareAdminPreparedSecretsConfig::class;
  protected $preparedSecretsDataType = '';
  protected $privateRegistryConfigType = VmwareAdminPrivateRegistryConfig::class;
  protected $privateRegistryConfigDataType = '';
  protected $proxyType = VmwareAdminProxy::class;
  protected $proxyDataType = '';
  /**
   * Output only. If set, there are currently changes in flight to the VMware
   * admin cluster.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The current state of VMware admin cluster.
   *
   * @var string
   */
  public $state;
  protected $statusType = ResourceStatus::class;
  protected $statusDataType = '';
  /**
   * Output only. The unique identifier of the VMware admin cluster.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time at which VMware admin cluster was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $validationCheckType = ValidationCheck::class;
  protected $validationCheckDataType = '';
  protected $vcenterType = VmwareAdminVCenterConfig::class;
  protected $vcenterDataType = '';

  /**
   * The VMware admin cluster addon node configuration.
   *
   * @param VmwareAdminAddonNodeConfig $addonNode
   */
  public function setAddonNode(VmwareAdminAddonNodeConfig $addonNode)
  {
    $this->addonNode = $addonNode;
  }
  /**
   * @return VmwareAdminAddonNodeConfig
   */
  public function getAddonNode()
  {
    return $this->addonNode;
  }
  /**
   * Annotations on the VMware admin cluster. This field has the same
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
   * The VMware admin cluster anti affinity group configuration.
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
   * The VMware admin cluster authorization configuration.
   *
   * @param VmwareAdminAuthorizationConfig $authorization
   */
  public function setAuthorization(VmwareAdminAuthorizationConfig $authorization)
  {
    $this->authorization = $authorization;
  }
  /**
   * @return VmwareAdminAuthorizationConfig
   */
  public function getAuthorization()
  {
    return $this->authorization;
  }
  /**
   * The VMware admin cluster auto repair configuration.
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
   * The bootstrap cluster this VMware admin cluster belongs to.
   *
   * @param string $bootstrapClusterMembership
   */
  public function setBootstrapClusterMembership($bootstrapClusterMembership)
  {
    $this->bootstrapClusterMembership = $bootstrapClusterMembership;
  }
  /**
   * @return string
   */
  public function getBootstrapClusterMembership()
  {
    return $this->bootstrapClusterMembership;
  }
  /**
   * The VMware admin cluster control plane node configuration.
   *
   * @param VmwareAdminControlPlaneNodeConfig $controlPlaneNode
   */
  public function setControlPlaneNode(VmwareAdminControlPlaneNodeConfig $controlPlaneNode)
  {
    $this->controlPlaneNode = $controlPlaneNode;
  }
  /**
   * @return VmwareAdminControlPlaneNodeConfig
   */
  public function getControlPlaneNode()
  {
    return $this->controlPlaneNode;
  }
  /**
   * Output only. The time at which VMware admin cluster was created.
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
   * A human readable description of this VMware admin cluster.
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
   * Output only. The DNS name of VMware admin cluster's API server.
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
   * The OS image type for the VMware admin cluster.
   *
   * @param string $imageType
   */
  public function setImageType($imageType)
  {
    $this->imageType = $imageType;
  }
  /**
   * @return string
   */
  public function getImageType()
  {
    return $this->imageType;
  }
  /**
   * The VMware admin cluster load balancer configuration.
   *
   * @param VmwareAdminLoadBalancerConfig $loadBalancer
   */
  public function setLoadBalancer(VmwareAdminLoadBalancerConfig $loadBalancer)
  {
    $this->loadBalancer = $loadBalancer;
  }
  /**
   * @return VmwareAdminLoadBalancerConfig
   */
  public function getLoadBalancer()
  {
    return $this->loadBalancer;
  }
  /**
   * Output only. The object name of the VMware OnPremAdminCluster custom
   * resource. This field is used to support conflicting names when enrolling
   * existing clusters to the API. When used as a part of cluster enrollment,
   * this field will differ from the ID in the resource name. For new clusters,
   * this field will match the user provided cluster name and be visible in the
   * last component of the resource name. It is not modifiable. All users should
   * use this name to access their cluster using gkectl or kubectl and should
   * expect to see the local name when viewing admin cluster controller logs.
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
   * Immutable. The VMware admin cluster resource name.
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
   * The VMware admin cluster network configuration.
   *
   * @param VmwareAdminNetworkConfig $networkConfig
   */
  public function setNetworkConfig(VmwareAdminNetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return VmwareAdminNetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * The Anthos clusters on the VMware version for the admin cluster.
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
   * The VMware platform configuration.
   *
   * @param VmwarePlatformConfig $platformConfig
   */
  public function setPlatformConfig(VmwarePlatformConfig $platformConfig)
  {
    $this->platformConfig = $platformConfig;
  }
  /**
   * @return VmwarePlatformConfig
   */
  public function getPlatformConfig()
  {
    return $this->platformConfig;
  }
  /**
   * Output only. The VMware admin cluster prepared secrets configuration. It
   * should always be enabled by the Central API, instead of letting users set
   * it.
   *
   * @param VmwareAdminPreparedSecretsConfig $preparedSecrets
   */
  public function setPreparedSecrets(VmwareAdminPreparedSecretsConfig $preparedSecrets)
  {
    $this->preparedSecrets = $preparedSecrets;
  }
  /**
   * @return VmwareAdminPreparedSecretsConfig
   */
  public function getPreparedSecrets()
  {
    return $this->preparedSecrets;
  }
  /**
   * Configuration for registry.
   *
   * @param VmwareAdminPrivateRegistryConfig $privateRegistryConfig
   */
  public function setPrivateRegistryConfig(VmwareAdminPrivateRegistryConfig $privateRegistryConfig)
  {
    $this->privateRegistryConfig = $privateRegistryConfig;
  }
  /**
   * @return VmwareAdminPrivateRegistryConfig
   */
  public function getPrivateRegistryConfig()
  {
    return $this->privateRegistryConfig;
  }
  /**
   * Configuration for proxy.
   *
   * @param VmwareAdminProxy $proxy
   */
  public function setProxy(VmwareAdminProxy $proxy)
  {
    $this->proxy = $proxy;
  }
  /**
   * @return VmwareAdminProxy
   */
  public function getProxy()
  {
    return $this->proxy;
  }
  /**
   * Output only. If set, there are currently changes in flight to the VMware
   * admin cluster.
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
   * Output only. The current state of VMware admin cluster.
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
   * Output only. The unique identifier of the VMware admin cluster.
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
   * Output only. The time at which VMware admin cluster was last updated.
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
   * The VMware admin cluster VCenter configuration.
   *
   * @param VmwareAdminVCenterConfig $vcenter
   */
  public function setVcenter(VmwareAdminVCenterConfig $vcenter)
  {
    $this->vcenter = $vcenter;
  }
  /**
   * @return VmwareAdminVCenterConfig
   */
  public function getVcenter()
  {
    return $this->vcenter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareAdminCluster::class, 'Google_Service_GKEOnPrem_VmwareAdminCluster');
