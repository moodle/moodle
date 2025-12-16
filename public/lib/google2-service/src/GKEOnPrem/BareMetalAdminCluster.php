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

class BareMetalAdminCluster extends \Google\Model
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
   * Annotations on the bare metal admin cluster. This field has the same
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
   * The Anthos clusters on bare metal version for the bare metal admin cluster.
   *
   * @var string
   */
  public $bareMetalVersion;
  protected $binaryAuthorizationType = BinaryAuthorization::class;
  protected $binaryAuthorizationDataType = '';
  protected $clusterOperationsType = BareMetalAdminClusterOperationsConfig::class;
  protected $clusterOperationsDataType = '';
  protected $controlPlaneType = BareMetalAdminControlPlaneConfig::class;
  protected $controlPlaneDataType = '';
  /**
   * Output only. The time at which this bare metal admin cluster was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time at which this bare metal admin cluster was deleted.
   * If the resource is not deleted, this must be empty
   *
   * @var string
   */
  public $deleteTime;
  /**
   * A human readable description of this bare metal admin cluster.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The IP address name of bare metal admin cluster's API server.
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
  protected $loadBalancerType = BareMetalAdminLoadBalancerConfig::class;
  protected $loadBalancerDataType = '';
  /**
   * Output only. The object name of the bare metal cluster custom resource.
   * This field is used to support conflicting names when enrolling existing
   * clusters to the API. When used as a part of cluster enrollment, this field
   * will differ from the ID in the resource name. For new clusters, this field
   * will match the user provided cluster name and be visible in the last
   * component of the resource name. It is not modifiable. All users should use
   * this name to access their cluster using gkectl or kubectl and should expect
   * to see the local name when viewing admin cluster controller logs.
   *
   * @var string
   */
  public $localName;
  protected $maintenanceConfigType = BareMetalAdminMaintenanceConfig::class;
  protected $maintenanceConfigDataType = '';
  protected $maintenanceStatusType = BareMetalAdminMaintenanceStatus::class;
  protected $maintenanceStatusDataType = '';
  /**
   * Immutable. The bare metal admin cluster resource name.
   *
   * @var string
   */
  public $name;
  protected $networkConfigType = BareMetalAdminNetworkConfig::class;
  protected $networkConfigDataType = '';
  protected $nodeAccessConfigType = BareMetalAdminNodeAccessConfig::class;
  protected $nodeAccessConfigDataType = '';
  protected $nodeConfigType = BareMetalAdminWorkloadNodeConfig::class;
  protected $nodeConfigDataType = '';
  protected $osEnvironmentConfigType = BareMetalAdminOsEnvironmentConfig::class;
  protected $osEnvironmentConfigDataType = '';
  protected $proxyType = BareMetalAdminProxyConfig::class;
  protected $proxyDataType = '';
  /**
   * Output only. If set, there are currently changes in flight to the bare
   * metal Admin Cluster.
   *
   * @var bool
   */
  public $reconciling;
  protected $securityConfigType = BareMetalAdminSecurityConfig::class;
  protected $securityConfigDataType = '';
  /**
   * Output only. The current state of the bare metal admin cluster.
   *
   * @var string
   */
  public $state;
  protected $statusType = ResourceStatus::class;
  protected $statusDataType = '';
  protected $storageType = BareMetalAdminStorageConfig::class;
  protected $storageDataType = '';
  /**
   * Output only. The unique identifier of the bare metal admin cluster.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time at which this bare metal admin cluster was last
   * updated.
   *
   * @var string
   */
  public $updateTime;
  protected $validationCheckType = ValidationCheck::class;
  protected $validationCheckDataType = '';

  /**
   * Annotations on the bare metal admin cluster. This field has the same
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
   * The Anthos clusters on bare metal version for the bare metal admin cluster.
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
   * @param BareMetalAdminClusterOperationsConfig $clusterOperations
   */
  public function setClusterOperations(BareMetalAdminClusterOperationsConfig $clusterOperations)
  {
    $this->clusterOperations = $clusterOperations;
  }
  /**
   * @return BareMetalAdminClusterOperationsConfig
   */
  public function getClusterOperations()
  {
    return $this->clusterOperations;
  }
  /**
   * Control plane configuration.
   *
   * @param BareMetalAdminControlPlaneConfig $controlPlane
   */
  public function setControlPlane(BareMetalAdminControlPlaneConfig $controlPlane)
  {
    $this->controlPlane = $controlPlane;
  }
  /**
   * @return BareMetalAdminControlPlaneConfig
   */
  public function getControlPlane()
  {
    return $this->controlPlane;
  }
  /**
   * Output only. The time at which this bare metal admin cluster was created.
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
   * Output only. The time at which this bare metal admin cluster was deleted.
   * If the resource is not deleted, this must be empty
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
   * A human readable description of this bare metal admin cluster.
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
   * Output only. The IP address name of bare metal admin cluster's API server.
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
   * @param BareMetalAdminLoadBalancerConfig $loadBalancer
   */
  public function setLoadBalancer(BareMetalAdminLoadBalancerConfig $loadBalancer)
  {
    $this->loadBalancer = $loadBalancer;
  }
  /**
   * @return BareMetalAdminLoadBalancerConfig
   */
  public function getLoadBalancer()
  {
    return $this->loadBalancer;
  }
  /**
   * Output only. The object name of the bare metal cluster custom resource.
   * This field is used to support conflicting names when enrolling existing
   * clusters to the API. When used as a part of cluster enrollment, this field
   * will differ from the ID in the resource name. For new clusters, this field
   * will match the user provided cluster name and be visible in the last
   * component of the resource name. It is not modifiable. All users should use
   * this name to access their cluster using gkectl or kubectl and should expect
   * to see the local name when viewing admin cluster controller logs.
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
   * Maintenance configuration.
   *
   * @param BareMetalAdminMaintenanceConfig $maintenanceConfig
   */
  public function setMaintenanceConfig(BareMetalAdminMaintenanceConfig $maintenanceConfig)
  {
    $this->maintenanceConfig = $maintenanceConfig;
  }
  /**
   * @return BareMetalAdminMaintenanceConfig
   */
  public function getMaintenanceConfig()
  {
    return $this->maintenanceConfig;
  }
  /**
   * Output only. MaintenanceStatus representing state of maintenance.
   *
   * @param BareMetalAdminMaintenanceStatus $maintenanceStatus
   */
  public function setMaintenanceStatus(BareMetalAdminMaintenanceStatus $maintenanceStatus)
  {
    $this->maintenanceStatus = $maintenanceStatus;
  }
  /**
   * @return BareMetalAdminMaintenanceStatus
   */
  public function getMaintenanceStatus()
  {
    return $this->maintenanceStatus;
  }
  /**
   * Immutable. The bare metal admin cluster resource name.
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
   * Network configuration.
   *
   * @param BareMetalAdminNetworkConfig $networkConfig
   */
  public function setNetworkConfig(BareMetalAdminNetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return BareMetalAdminNetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Node access related configurations.
   *
   * @param BareMetalAdminNodeAccessConfig $nodeAccessConfig
   */
  public function setNodeAccessConfig(BareMetalAdminNodeAccessConfig $nodeAccessConfig)
  {
    $this->nodeAccessConfig = $nodeAccessConfig;
  }
  /**
   * @return BareMetalAdminNodeAccessConfig
   */
  public function getNodeAccessConfig()
  {
    return $this->nodeAccessConfig;
  }
  /**
   * Workload node configuration.
   *
   * @param BareMetalAdminWorkloadNodeConfig $nodeConfig
   */
  public function setNodeConfig(BareMetalAdminWorkloadNodeConfig $nodeConfig)
  {
    $this->nodeConfig = $nodeConfig;
  }
  /**
   * @return BareMetalAdminWorkloadNodeConfig
   */
  public function getNodeConfig()
  {
    return $this->nodeConfig;
  }
  /**
   * OS environment related configurations.
   *
   * @param BareMetalAdminOsEnvironmentConfig $osEnvironmentConfig
   */
  public function setOsEnvironmentConfig(BareMetalAdminOsEnvironmentConfig $osEnvironmentConfig)
  {
    $this->osEnvironmentConfig = $osEnvironmentConfig;
  }
  /**
   * @return BareMetalAdminOsEnvironmentConfig
   */
  public function getOsEnvironmentConfig()
  {
    return $this->osEnvironmentConfig;
  }
  /**
   * Proxy configuration.
   *
   * @param BareMetalAdminProxyConfig $proxy
   */
  public function setProxy(BareMetalAdminProxyConfig $proxy)
  {
    $this->proxy = $proxy;
  }
  /**
   * @return BareMetalAdminProxyConfig
   */
  public function getProxy()
  {
    return $this->proxy;
  }
  /**
   * Output only. If set, there are currently changes in flight to the bare
   * metal Admin Cluster.
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
   * Security related configuration.
   *
   * @param BareMetalAdminSecurityConfig $securityConfig
   */
  public function setSecurityConfig(BareMetalAdminSecurityConfig $securityConfig)
  {
    $this->securityConfig = $securityConfig;
  }
  /**
   * @return BareMetalAdminSecurityConfig
   */
  public function getSecurityConfig()
  {
    return $this->securityConfig;
  }
  /**
   * Output only. The current state of the bare metal admin cluster.
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
   * Output only. ResourceStatus representing detailed cluster status.
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
   * @param BareMetalAdminStorageConfig $storage
   */
  public function setStorage(BareMetalAdminStorageConfig $storage)
  {
    $this->storage = $storage;
  }
  /**
   * @return BareMetalAdminStorageConfig
   */
  public function getStorage()
  {
    return $this->storage;
  }
  /**
   * Output only. The unique identifier of the bare metal admin cluster.
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
   * Output only. The time at which this bare metal admin cluster was last
   * updated.
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
   * Output only. ValidationCheck representing the result of the preflight
   * check.
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
class_alias(BareMetalAdminCluster::class, 'Google_Service_GKEOnPrem_BareMetalAdminCluster');
