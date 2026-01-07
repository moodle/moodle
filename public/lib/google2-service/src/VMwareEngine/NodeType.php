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

class NodeType extends \Google\Collection
{
  /**
   * The default value. This value should never be used.
   */
  public const KIND_KIND_UNSPECIFIED = 'KIND_UNSPECIFIED';
  /**
   * Standard HCI node.
   */
  public const KIND_STANDARD = 'STANDARD';
  /**
   * Storage only Node.
   */
  public const KIND_STORAGE_ONLY = 'STORAGE_ONLY';
  protected $collection_key = 'families';
  /**
   * Output only. List of possible values of custom core count.
   *
   * @var int[]
   */
  public $availableCustomCoreCounts;
  /**
   * Output only. Capabilities of this node type.
   *
   * @var string[]
   */
  public $capabilities;
  /**
   * Output only. The amount of storage available, defined in GB.
   *
   * @var int
   */
  public $diskSizeGb;
  /**
   * Output only. The friendly name for this node type. For example:
   * ve1-standard-72
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Families of the node type. For node types to be in the same
   * cluster they must share at least one element in the `families`.
   *
   * @var string[]
   */
  public $families;
  /**
   * Output only. The type of the resource.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. The amount of physical memory available, defined in GB.
   *
   * @var int
   */
  public $memoryGb;
  /**
   * Output only. The resource name of this node type. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-proj/locations/us-central1-a/nodeTypes/standard-72`
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
   * Output only. The total number of CPU cores in a single node.
   *
   * @var int
   */
  public $totalCoreCount;
  /**
   * Output only. The total number of virtual CPUs in a single node.
   *
   * @var int
   */
  public $virtualCpuCount;

  /**
   * Output only. List of possible values of custom core count.
   *
   * @param int[] $availableCustomCoreCounts
   */
  public function setAvailableCustomCoreCounts($availableCustomCoreCounts)
  {
    $this->availableCustomCoreCounts = $availableCustomCoreCounts;
  }
  /**
   * @return int[]
   */
  public function getAvailableCustomCoreCounts()
  {
    return $this->availableCustomCoreCounts;
  }
  /**
   * Output only. Capabilities of this node type.
   *
   * @param string[] $capabilities
   */
  public function setCapabilities($capabilities)
  {
    $this->capabilities = $capabilities;
  }
  /**
   * @return string[]
   */
  public function getCapabilities()
  {
    return $this->capabilities;
  }
  /**
   * Output only. The amount of storage available, defined in GB.
   *
   * @param int $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return int
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Output only. The friendly name for this node type. For example:
   * ve1-standard-72
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
   * Output only. Families of the node type. For node types to be in the same
   * cluster they must share at least one element in the `families`.
   *
   * @param string[] $families
   */
  public function setFamilies($families)
  {
    $this->families = $families;
  }
  /**
   * @return string[]
   */
  public function getFamilies()
  {
    return $this->families;
  }
  /**
   * Output only. The type of the resource.
   *
   * Accepted values: KIND_UNSPECIFIED, STANDARD, STORAGE_ONLY
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. The amount of physical memory available, defined in GB.
   *
   * @param int $memoryGb
   */
  public function setMemoryGb($memoryGb)
  {
    $this->memoryGb = $memoryGb;
  }
  /**
   * @return int
   */
  public function getMemoryGb()
  {
    return $this->memoryGb;
  }
  /**
   * Output only. The resource name of this node type. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-proj/locations/us-central1-a/nodeTypes/standard-72`
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
   * Output only. The total number of CPU cores in a single node.
   *
   * @param int $totalCoreCount
   */
  public function setTotalCoreCount($totalCoreCount)
  {
    $this->totalCoreCount = $totalCoreCount;
  }
  /**
   * @return int
   */
  public function getTotalCoreCount()
  {
    return $this->totalCoreCount;
  }
  /**
   * Output only. The total number of virtual CPUs in a single node.
   *
   * @param int $virtualCpuCount
   */
  public function setVirtualCpuCount($virtualCpuCount)
  {
    $this->virtualCpuCount = $virtualCpuCount;
  }
  /**
   * @return int
   */
  public function getVirtualCpuCount()
  {
    return $this->virtualCpuCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeType::class, 'Google_Service_VMwareEngine_NodeType');
