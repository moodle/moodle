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

namespace Google\Service\Container;

class UpgradeEvent extends \Google\Model
{
  /**
   * Default value. This shouldn't be used.
   */
  public const RESOURCE_TYPE_UPGRADE_RESOURCE_TYPE_UNSPECIFIED = 'UPGRADE_RESOURCE_TYPE_UNSPECIFIED';
  /**
   * Master / control plane
   */
  public const RESOURCE_TYPE_MASTER = 'MASTER';
  /**
   * Node pool
   */
  public const RESOURCE_TYPE_NODE_POOL = 'NODE_POOL';
  /**
   * The current version before the upgrade.
   *
   * @var string
   */
  public $currentVersion;
  /**
   * The operation associated with this upgrade.
   *
   * @var string
   */
  public $operation;
  /**
   * The time when the operation was started.
   *
   * @var string
   */
  public $operationStartTime;
  /**
   * Optional relative path to the resource. For example in node pool upgrades,
   * the relative path of the node pool.
   *
   * @var string
   */
  public $resource;
  /**
   * The resource type that is upgrading.
   *
   * @var string
   */
  public $resourceType;
  /**
   * The target version for the upgrade.
   *
   * @var string
   */
  public $targetVersion;

  /**
   * The current version before the upgrade.
   *
   * @param string $currentVersion
   */
  public function setCurrentVersion($currentVersion)
  {
    $this->currentVersion = $currentVersion;
  }
  /**
   * @return string
   */
  public function getCurrentVersion()
  {
    return $this->currentVersion;
  }
  /**
   * The operation associated with this upgrade.
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * The time when the operation was started.
   *
   * @param string $operationStartTime
   */
  public function setOperationStartTime($operationStartTime)
  {
    $this->operationStartTime = $operationStartTime;
  }
  /**
   * @return string
   */
  public function getOperationStartTime()
  {
    return $this->operationStartTime;
  }
  /**
   * Optional relative path to the resource. For example in node pool upgrades,
   * the relative path of the node pool.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The resource type that is upgrading.
   *
   * Accepted values: UPGRADE_RESOURCE_TYPE_UNSPECIFIED, MASTER, NODE_POOL
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * The target version for the upgrade.
   *
   * @param string $targetVersion
   */
  public function setTargetVersion($targetVersion)
  {
    $this->targetVersion = $targetVersion;
  }
  /**
   * @return string
   */
  public function getTargetVersion()
  {
    return $this->targetVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeEvent::class, 'Google_Service_Container_UpgradeEvent');
