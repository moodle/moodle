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

class BareMetalCluster extends \Google\Model
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
   * Required. The admin cluster this bare metal user cluster belongs to. This
   * is the full resource name of the admin cluster's fleet membership.
   *
   * @var string
   */
  public $adminClusterMembership;
  /**
   * Output only. The resource name of the bare metal admin cluster managing
   * this user cluster.
   *
   * @var string
   */
  public $adminClusterName;
  /**
   * Annotations on the bare metal user cluster. This field has the same
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
  /**
   * Required. The Anthos clusters on bare metal version for your user cluster.
   *
   * @var string
   */
  public $bareMetalVersion;
  protected $binaryAuthorizationType = BinaryAuthorization::class;
  protected $binaryAuthorizationDataType = '';
  protected $clusterOperationsType = BareMetalClusterOperationsConfig::class;
  protected $clusterOperationsDataType = '';
  protected $controlPlaneType = BareMetalControlPlaneConfig::class;
  protected $controlPlaneDataType = '';
  /**
   * Output only. The time when the bare metal user cluster was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time when the bare metal user cluster was deleted. If the
   * resource is not deleted, this must be empty
   *
   * @var string
   */
  public $deleteTime;
  /**
   * A human readable description of this bare metal user cluster.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The IP address of the bare metal user cluster's API server.
   *
   * @var string
   */
  public $endpoint;
  /**
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding. Allows clients to perform
   * consistent read-modify-writes through optimistic concurrency control.
   *
   * @var string
   */
  public $etag;
  protected $fleetType = Fleet::class;
  protected $fleetDataType = '';
  protected $loadBalancerType = BareMetalLoadBalancerConfig::class;
  protected $loadBalancerDataType = '';
  /**
   * Output only. The object name of the bare metal user cluster custom resource
   * on the associated admin cluster. This field is used to support conflicting
   * names when enrolling existing clusters to the API. When used as a part of
   * cluster enrollment, this field will differ from the name in the resource
   * name. For new clusters, this field will match the user provided cluster
   * name and be visible in the last component of the resource name. It is not
   * modifiable. When the local name and cluster name differ, the local name is
   * used in the admin cluster controller logs. You use the cluster name when
   * accessing the cluster using bmctl and kubectl.
   *
   * @var string
   */
  public $localName;
  /**
   * Output only. The namespace of the cluster.
   *
   * @var string
   */
  public $localNamespace;
  protected $maintenanceConfigType = BareMetalMaintenanceConfig::class;
  protected $maintenanceConfigDataType = '';
  protected $maintenanceStatusType = BareMetalMaintenanceStatus::class;
  protected $maintenanceStatusDataType = '';
  /**
   * Immutable. The bare metal user cluster resource name.
   *
   * @var string
   */
  public $name;
  protected $networkConfigType = BareMetalNetworkConfig::class;
  protected $networkConfigDataType = '';
  protected $nodeAccessConfigType = BareMetalNodeAccessConfig::class;
  protected $nodeAccessConfigDataType = '';
  protected $nodeConfigType = BareMetalWorkloadNodeConfig::class;
  protected $nodeConfigDataType = '';
  protected $osEnvironmentConfigType = BareMetalOsEnvironmentConfig::class;
  protected $osEnvironmentConfigDataType = '';
  protected $proxyType = BareMetalProxyConfig::class;
  protected $proxyDataType = '';
  /**
   * Output only. If set, there are currently changes in flight to the bare
   * metal user cluster.
   *
   * @var bool
   */
  public $reconciling;
  protected $securityConfigType = BareMetalSecurityConfig::class;
  protected $securityConfigDataType = '';
  /**
   * Output only. The current state of the bare metal user cluster.
   *
   * @var string
   */
  public $state;
  protected $statusType = ResourceStatus::class;
  protected $statusDataType = '';
  protected $storageType = BareMetalStorageConfig::class;
  protected $storageDataType = '';
  /**
   * Output only. The unique identifier of the bare metal user cluster.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the bare metal user cluster was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradePolicyType = BareMetalClusterUpgradePolicy::class;
  protected $upgradePolicyDataType = '';
  protected $validationCheckType = ValidationCheck::class;
  protected $validationCheckDataType = '';

  /**
   * Required. The admin cluster this bare metal user cluster belongs to. This
   * is the full resource name of the admin cluster's fleet membership.
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
   * Output only. The resource name of the bare metal admin cluster managing
   * this user cluster.
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
   * Annotations on the bare metal user cluster. This field has the same
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
   * Required. The Anthos clusters on bare metal version for your user cluster.
   *
   * @param string $bareMetalVersion
   */
  public function setBareMetalVersion($bareMetalVersion)
  {
    $this->bareMetalVersion = $bareMetalVersion;
  }
  /**
   * @return string
   */
  public function getBareMetalVersion()
  {
    return $this->bareMetalVersion;
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
   * Cluster operations configuration.
   *
   * @param BareMetalClusterOperationsConfig $clusterOperations
   */
  public function setClusterOperations(BareMetalClusterOperationsConfig $clusterOperations)
  {
    $this->clusterOperations = $clusterOperations;
  }
  /**
   * @return BareMetalClusterOperationsConfig
   */
  public function getClusterOperations()
  {
    return $this->clusterOperations;
  }
  /**
   * Required. Control plane configuration.
   *
   * @param BareMetalControlPlaneConfig $controlPlane
   */
  public function setControlPlane(BareMetalControlPlaneConfig $controlPlane)
  {
    $this->controlPlane = $controlPlane;
  }
  /**
   * @return BareMetalControlPlaneConfig
   */
  public function getControlPlane()
  {
    return $this->controlPlane;
  }
  /**
   * Output only. The time when the bare metal user cluster was created.
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
   * Output only. The time when the bare metal user cluster was deleted. If the
   * resource is not deleted, this must be empty
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
   * A human readable description of this bare metal user cluster.
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
   * Output only. The IP address of the bare metal user cluster's API server.
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
   * Output only. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding. Allows clients to perform
   * consistent read-modify-writes through optimistic concurrency control.
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
   * Required. Load balancer configuration.
   *
   * @param BareMetalLoadBalancerConfig $loadBalancer
   */
  public function setLoadBalancer(BareMetalLoadBalancerConfig $loadBalancer)
  {
    $this->loadBalancer = $loadBalancer;
  }
  /**
   * @return BareMetalLoadBalancerConfig
   */
  public function getLoadBalancer()
  {
    return $this->loadBalancer;
  }
  /**
   * Output only. The object name of the bare metal user cluster custom resource
   * on the associated admin cluster. This field is used to support conflicting
   * names when enrolling existing clusters to the API. When used as a part of
   * cluster enrollment, this field will differ from the name in the resource
   * name. For new clusters, this field will match the user provided cluster
   * name and be visible in the last component of the resource name. It is not
   * modifiable. When the local name and cluster name differ, the local name is
   * used in the admin cluster controller logs. You use the cluster name when
   * accessing the cluster using bmctl and kubectl.
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
   * Output only. The namespace of the cluster.
   *
   * @param string $localNamespace
   */
  public function setLocalNamespace($localNamespace)
  {
    $this->localNamespace = $localNamespace;
  }
  /**
   * @return string
   */
  public function getLocalNamespace()
  {
    return $this->localNamespace;
  }
  /**
   * Maintenance configuration.
   *
   * @param BareMetalMaintenanceConfig $maintenanceConfig
   */
  public function setMaintenanceConfig(BareMetalMaintenanceConfig $maintenanceConfig)
  {
    $this->maintenanceConfig = $maintenanceConfig;
  }
  /**
   * @return BareMetalMaintenanceConfig
   */
  public function getMaintenanceConfig()
  {
    return $this->maintenanceConfig;
  }
  /**
   * Output only. Status of on-going maintenance tasks.
   *
   * @param BareMetalMaintenanceStatus $maintenanceStatus
   */
  public function setMaintenanceStatus(BareMetalMaintenanceStatus $maintenanceStatus)
  {
    $this->maintenanceStatus = $maintenanceStatus;
  }
  /**
   * @return BareMetalMaintenanceStatus
   */
  public function getMaintenanceStatus()
  {
    return $this->maintenanceStatus;
  }
  /**
   * Immutable. The bare metal user cluster resource name.
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
   * Required. Network configuration.
   *
   * @param BareMetalNetworkConfig $networkConfig
   */
  public function setNetworkConfig(BareMetalNetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return BareMetalNetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Node access related configurations.
   *
   * @param BareMetalNodeAccessConfig $nodeAccessConfig
   */
  public function setNodeAccessConfig(BareMetalNodeAccessConfig $nodeAccessConfig)
  {
    $this->nodeAccessConfig = $nodeAccessConfig;
  }
  /**
   * @return BareMetalNodeAccessConfig
   */
  public function getNodeAccessConfig()
  {
    return $this->nodeAccessConfig;
  }
  /**
   * Workload node configuration.
   *
   * @param BareMetalWorkloadNodeConfig $nodeConfig
   */
  public function setNodeConfig(BareMetalWorkloadNodeConfig $nodeConfig)
  {
    $this->nodeConfig = $nodeConfig;
  }
  /**
   * @return BareMetalWorkloadNodeConfig
   */
  public function getNodeConfig()
  {
    return $this->nodeConfig;
  }
  /**
   * OS environment related configurations.
   *
   * @param BareMetalOsEnvironmentConfig $osEnvironmentConfig
   */
  public function setOsEnvironmentConfig(BareMetalOsEnvironmentConfig $osEnvironmentConfig)
  {
    $this->osEnvironmentConfig = $osEnvironmentConfig;
  }
  /**
   * @return BareMetalOsEnvironmentConfig
   */
  public function getOsEnvironmentConfig()
  {
    return $this->osEnvironmentConfig;
  }
  /**
   * Proxy configuration.
   *
   * @param BareMetalProxyConfig $proxy
   */
  public function setProxy(BareMetalProxyConfig $proxy)
  {
    $this->proxy = $proxy;
  }
  /**
   * @return BareMetalProxyConfig
   */
  public function getProxy()
  {
    return $this->proxy;
  }
  /**
   * Output only. If set, there are currently changes in flight to the bare
   * metal user cluster.
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
   * Security related setting configuration.
   *
   * @param BareMetalSecurityConfig $securityConfig
   */
  public function setSecurityConfig(BareMetalSecurityConfig $securityConfig)
  {
    $this->securityConfig = $securityConfig;
  }
  /**
   * @return BareMetalSecurityConfig
   */
  public function getSecurityConfig()
  {
    return $this->securityConfig;
  }
  /**
   * Output only. The current state of the bare metal user cluster.
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
   * Output only. Detailed cluster status.
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
   * Required. Storage configuration.
   *
   * @param BareMetalStorageConfig $storage
   */
  public function setStorage(BareMetalStorageConfig $storage)
  {
    $this->storage = $storage;
  }
  /**
   * @return BareMetalStorageConfig
   */
  public function getStorage()
  {
    return $this->storage;
  }
  /**
   * Output only. The unique identifier of the bare metal user cluster.
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
   * Output only. The time when the bare metal user cluster was last updated.
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
   * The cluster upgrade policy.
   *
   * @param BareMetalClusterUpgradePolicy $upgradePolicy
   */
  public function setUpgradePolicy(BareMetalClusterUpgradePolicy $upgradePolicy)
  {
    $this->upgradePolicy = $upgradePolicy;
  }
  /**
   * @return BareMetalClusterUpgradePolicy
   */
  public function getUpgradePolicy()
  {
    return $this->upgradePolicy;
  }
  /**
   * Output only. The result of the preflight check.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalCluster::class, 'Google_Service_GKEOnPrem_BareMetalCluster');
