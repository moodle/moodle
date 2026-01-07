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

namespace Google\Service\CloudWorkstations;

class WorkstationCluster extends \Google\Collection
{
  protected $collection_key = 'conditions';
  /**
   * Optional. Client-specified annotations.
   *
   * @var string[]
   */
  public $annotations;
  protected $conditionsType = Status::class;
  protected $conditionsDataType = 'array';
  /**
   * Output only. The private IP address of the control plane for this
   * workstation cluster. Workstation VMs need access to this IP address to work
   * with the service, so make sure that your firewall rules allow egress from
   * the workstation VMs to this address.
   *
   * @var string
   */
  public $controlPlaneIp;
  /**
   * Output only. Time when this workstation cluster was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Whether this workstation cluster is in degraded mode, in which
   * case it may require user action to restore full functionality. The
   * conditions field contains detailed information about the status of the
   * cluster.
   *
   * @var bool
   */
  public $degraded;
  /**
   * Output only. Time when this workstation cluster was soft-deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. Human-readable name for this workstation cluster.
   *
   * @var string
   */
  public $displayName;
  protected $domainConfigType = DomainConfig::class;
  protected $domainConfigDataType = '';
  /**
   * Optional. Checksum computed by the server. May be sent on update and delete
   * requests to make sure that the client has an up-to-date value before
   * proceeding.
   *
   * @var string
   */
  public $etag;
  protected $gatewayConfigType = GatewayConfig::class;
  protected $gatewayConfigDataType = '';
  /**
   * Optional. [Labels](https://cloud.google.com/workstations/docs/label-
   * resources) that are applied to the workstation cluster and that are also
   * propagated to the underlying Compute Engine resources.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Full name of this workstation cluster.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. Name of the Compute Engine network in which instances associated
   * with this workstation cluster will be created.
   *
   * @var string
   */
  public $network;
  protected $privateClusterConfigType = PrivateClusterConfig::class;
  protected $privateClusterConfigDataType = '';
  /**
   * Output only. Indicates whether this workstation cluster is currently being
   * updated to match its intended state.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Immutable. Name of the Compute Engine subnetwork in which instances
   * associated with this workstation cluster will be created. Must be part of
   * the subnetwork specified for this workstation cluster.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
   *
   * @var string[]
   */
  public $tags;
  /**
   * Output only. A system-assigned unique identifier for this workstation
   * cluster.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time when this workstation cluster was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Client-specified annotations.
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
   * Output only. Status conditions describing the workstation cluster's current
   * state.
   *
   * @param Status[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return Status[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Output only. The private IP address of the control plane for this
   * workstation cluster. Workstation VMs need access to this IP address to work
   * with the service, so make sure that your firewall rules allow egress from
   * the workstation VMs to this address.
   *
   * @param string $controlPlaneIp
   */
  public function setControlPlaneIp($controlPlaneIp)
  {
    $this->controlPlaneIp = $controlPlaneIp;
  }
  /**
   * @return string
   */
  public function getControlPlaneIp()
  {
    return $this->controlPlaneIp;
  }
  /**
   * Output only. Time when this workstation cluster was created.
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
   * Output only. Whether this workstation cluster is in degraded mode, in which
   * case it may require user action to restore full functionality. The
   * conditions field contains detailed information about the status of the
   * cluster.
   *
   * @param bool $degraded
   */
  public function setDegraded($degraded)
  {
    $this->degraded = $degraded;
  }
  /**
   * @return bool
   */
  public function getDegraded()
  {
    return $this->degraded;
  }
  /**
   * Output only. Time when this workstation cluster was soft-deleted.
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
   * Optional. Human-readable name for this workstation cluster.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Configuration options for a custom domain.
   *
   * @param DomainConfig $domainConfig
   */
  public function setDomainConfig(DomainConfig $domainConfig)
  {
    $this->domainConfig = $domainConfig;
  }
  /**
   * @return DomainConfig
   */
  public function getDomainConfig()
  {
    return $this->domainConfig;
  }
  /**
   * Optional. Checksum computed by the server. May be sent on update and delete
   * requests to make sure that the client has an up-to-date value before
   * proceeding.
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
   * Optional. Configuration options for Cluster HTTP Gateway.
   *
   * @param GatewayConfig $gatewayConfig
   */
  public function setGatewayConfig(GatewayConfig $gatewayConfig)
  {
    $this->gatewayConfig = $gatewayConfig;
  }
  /**
   * @return GatewayConfig
   */
  public function getGatewayConfig()
  {
    return $this->gatewayConfig;
  }
  /**
   * Optional. [Labels](https://cloud.google.com/workstations/docs/label-
   * resources) that are applied to the workstation cluster and that are also
   * propagated to the underlying Compute Engine resources.
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
   * Identifier. Full name of this workstation cluster.
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
   * Immutable. Name of the Compute Engine network in which instances associated
   * with this workstation cluster will be created.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. Configuration for private workstation cluster.
   *
   * @param PrivateClusterConfig $privateClusterConfig
   */
  public function setPrivateClusterConfig(PrivateClusterConfig $privateClusterConfig)
  {
    $this->privateClusterConfig = $privateClusterConfig;
  }
  /**
   * @return PrivateClusterConfig
   */
  public function getPrivateClusterConfig()
  {
    return $this->privateClusterConfig;
  }
  /**
   * Output only. Indicates whether this workstation cluster is currently being
   * updated to match its intended state.
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
   * Immutable. Name of the Compute Engine subnetwork in which instances
   * associated with this workstation cluster will be created. Must be part of
   * the subnetwork specified for this workstation cluster.
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
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
   * Output only. A system-assigned unique identifier for this workstation
   * cluster.
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
   * Output only. Time when this workstation cluster was most recently updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkstationCluster::class, 'Google_Service_CloudWorkstations_WorkstationCluster');
