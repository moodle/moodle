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

namespace Google\Service\VMwareEngine;

class Node extends \Google\Model
{
  /**
   * The default value. This value should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Node is operational and can be used by the user.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Node is being provisioned.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Node is in a failed state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Node is undergoing maintenance, e.g.: during private cloud upgrade.
   */
  public const STATE_UPGRADING = 'UPGRADING';
  /**
   * Output only. Customized number of cores
   *
   * @var string
   */
  public $customCoreCount;
  /**
   * Output only. Fully qualified domain name of the node.
   *
   * @var string
   */
  public $fqdn;
  /**
   * Output only. Internal IP address of the node.
   *
   * @var string
   */
  public $internalIp;
  /**
   * Output only. The resource name of this node. Resource names are schemeless
   * URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster/nodes/my-node
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The canonical identifier of the node type (corresponds to the
   * `NodeType`). For example: standard-72.
   *
   * @var string
   */
  public $nodeTypeId;
  /**
   * Output only. The state of the appliance.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The version number of the VMware ESXi management component in
   * this cluster.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Customized number of cores
   *
   * @param string $customCoreCount
   */
  public function setCustomCoreCount($customCoreCount)
  {
    $this->customCoreCount = $customCoreCount;
  }
  /**
   * @return string
   */
  public function getCustomCoreCount()
  {
    return $this->customCoreCount;
  }
  /**
   * Output only. Fully qualified domain name of the node.
   *
   * @param string $fqdn
   */
  public function setFqdn($fqdn)
  {
    $this->fqdn = $fqdn;
  }
  /**
   * @return string
   */
  public function getFqdn()
  {
    return $this->fqdn;
  }
  /**
   * Output only. Internal IP address of the node.
   *
   * @param string $internalIp
   */
  public function setInternalIp($internalIp)
  {
    $this->internalIp = $internalIp;
  }
  /**
   * @return string
   */
  public function getInternalIp()
  {
    return $this->internalIp;
  }
  /**
   * Output only. The resource name of this node. Resource names are schemeless
   * URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster/nodes/my-node
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
   * Output only. The canonical identifier of the node type (corresponds to the
   * `NodeType`). For example: standard-72.
   *
   * @param string $nodeTypeId
   */
  public function setNodeTypeId($nodeTypeId)
  {
    $this->nodeTypeId = $nodeTypeId;
  }
  /**
   * @return string
   */
  public function getNodeTypeId()
  {
    return $this->nodeTypeId;
  }
  /**
   * Output only. The state of the appliance.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, FAILED, UPGRADING
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
   * Output only. The version number of the VMware ESXi management component in
   * this cluster.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Node::class, 'Google_Service_VMwareEngine_Node');
