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

class NodeTemplate extends \Google\Collection
{
  public const CPU_OVERCOMMIT_TYPE_CPU_OVERCOMMIT_TYPE_UNSPECIFIED = 'CPU_OVERCOMMIT_TYPE_UNSPECIFIED';
  public const CPU_OVERCOMMIT_TYPE_ENABLED = 'ENABLED';
  public const CPU_OVERCOMMIT_TYPE_NONE = 'NONE';
  /**
   * Resources are being allocated.
   */
  public const STATUS_CREATING = 'CREATING';
  /**
   * The node template is currently being deleted.
   */
  public const STATUS_DELETING = 'DELETING';
  /**
   * Invalid status.
   */
  public const STATUS_INVALID = 'INVALID';
  /**
   * The node template is ready.
   */
  public const STATUS_READY = 'READY';
  protected $collection_key = 'disks';
  protected $acceleratorsType = AcceleratorConfig::class;
  protected $acceleratorsDataType = 'array';
  /**
   * CPU overcommit.
   *
   * @var string
   */
  public $cpuOvercommitType;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  protected $disksType = LocalDisk::class;
  protected $disksDataType = 'array';
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#nodeTemplate for node templates.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the resource, provided by the client when initially creating
   * the resource. The resource name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * Labels to use for node affinity, which will be used in instance scheduling.
   *
   * @var string[]
   */
  public $nodeAffinityLabels;
  /**
   * The node type to use for nodes group that are created from this template.
   *
   * @var string
   */
  public $nodeType;
  protected $nodeTypeFlexibilityType = NodeTemplateNodeTypeFlexibility::class;
  protected $nodeTypeFlexibilityDataType = '';
  /**
   * Output only. [Output Only] The name of the region where the node template
   * resides, such as us-central1.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $serverBindingType = ServerBinding::class;
  protected $serverBindingDataType = '';
  /**
   * Output only. [Output Only] The status of the node template. One of the
   * following values:CREATING, READY, and DELETING.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. [Output Only] An optional, human-readable explanation of the
   * status.
   *
   * @var string
   */
  public $statusMessage;

  /**
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
   * CPU overcommit.
   *
   * Accepted values: CPU_OVERCOMMIT_TYPE_UNSPECIFIED, ENABLED, NONE
   *
   * @param self::CPU_OVERCOMMIT_TYPE_* $cpuOvercommitType
   */
  public function setCpuOvercommitType($cpuOvercommitType)
  {
    $this->cpuOvercommitType = $cpuOvercommitType;
  }
  /**
   * @return self::CPU_OVERCOMMIT_TYPE_*
   */
  public function getCpuOvercommitType()
  {
    return $this->cpuOvercommitType;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
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
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * @param LocalDisk[] $disks
   */
  public function setDisks($disks)
  {
    $this->disks = $disks;
  }
  /**
   * @return LocalDisk[]
   */
  public function getDisks()
  {
    return $this->disks;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
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
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#nodeTemplate for node templates.
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
   * The name of the resource, provided by the client when initially creating
   * the resource. The resource name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
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
   * Labels to use for node affinity, which will be used in instance scheduling.
   *
   * @param string[] $nodeAffinityLabels
   */
  public function setNodeAffinityLabels($nodeAffinityLabels)
  {
    $this->nodeAffinityLabels = $nodeAffinityLabels;
  }
  /**
   * @return string[]
   */
  public function getNodeAffinityLabels()
  {
    return $this->nodeAffinityLabels;
  }
  /**
   * The node type to use for nodes group that are created from this template.
   *
   * @param string $nodeType
   */
  public function setNodeType($nodeType)
  {
    $this->nodeType = $nodeType;
  }
  /**
   * @return string
   */
  public function getNodeType()
  {
    return $this->nodeType;
  }
  /**
   * Do not use. Instead, use the node_type property.
   *
   * @param NodeTemplateNodeTypeFlexibility $nodeTypeFlexibility
   */
  public function setNodeTypeFlexibility(NodeTemplateNodeTypeFlexibility $nodeTypeFlexibility)
  {
    $this->nodeTypeFlexibility = $nodeTypeFlexibility;
  }
  /**
   * @return NodeTemplateNodeTypeFlexibility
   */
  public function getNodeTypeFlexibility()
  {
    return $this->nodeTypeFlexibility;
  }
  /**
   * Output only. [Output Only] The name of the region where the node template
   * resides, such as us-central1.
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
   * Output only. [Output Only] Server-defined URL for the resource.
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
   * Sets the binding properties for the physical server. Valid values include:
   * - *[Default]* RESTART_NODE_ON_ANY_SERVER:    Restarts VMs on any available
   * physical server    - RESTART_NODE_ON_MINIMAL_SERVER: Restarts VMs on the
   * same    physical server whenever possible
   *
   * See Sole-tenant node options for more information.
   *
   * @param ServerBinding $serverBinding
   */
  public function setServerBinding(ServerBinding $serverBinding)
  {
    $this->serverBinding = $serverBinding;
  }
  /**
   * @return ServerBinding
   */
  public function getServerBinding()
  {
    return $this->serverBinding;
  }
  /**
   * Output only. [Output Only] The status of the node template. One of the
   * following values:CREATING, READY, and DELETING.
   *
   * Accepted values: CREATING, DELETING, INVALID, READY
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. [Output Only] An optional, human-readable explanation of the
   * status.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeTemplate::class, 'Google_Service_Compute_NodeTemplate');
