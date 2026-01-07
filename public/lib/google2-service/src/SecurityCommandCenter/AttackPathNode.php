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

namespace Google\Service\SecurityCommandCenter;

class AttackPathNode extends \Google\Collection
{
  protected $collection_key = 'attackSteps';
  protected $associatedFindingsType = PathNodeAssociatedFinding::class;
  protected $associatedFindingsDataType = 'array';
  protected $attackStepsType = AttackStepNode::class;
  protected $attackStepsDataType = 'array';
  /**
   * Human-readable name of this resource.
   *
   * @var string
   */
  public $displayName;
  /**
   * The name of the resource at this point in the attack path. The format of
   * the name follows the Cloud Asset Inventory [resource name
   * format](https://cloud.google.com/asset-inventory/docs/resource-name-format)
   *
   * @var string
   */
  public $resource;
  /**
   * The [supported resource type](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types)
   *
   * @var string
   */
  public $resourceType;
  /**
   * Unique id of the attack path node.
   *
   * @var string
   */
  public $uuid;

  /**
   * The findings associated with this node in the attack path.
   *
   * @param PathNodeAssociatedFinding[] $associatedFindings
   */
  public function setAssociatedFindings($associatedFindings)
  {
    $this->associatedFindings = $associatedFindings;
  }
  /**
   * @return PathNodeAssociatedFinding[]
   */
  public function getAssociatedFindings()
  {
    return $this->associatedFindings;
  }
  /**
   * A list of attack step nodes that exist in this attack path node.
   *
   * @param AttackStepNode[] $attackSteps
   */
  public function setAttackSteps($attackSteps)
  {
    $this->attackSteps = $attackSteps;
  }
  /**
   * @return AttackStepNode[]
   */
  public function getAttackSteps()
  {
    return $this->attackSteps;
  }
  /**
   * Human-readable name of this resource.
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
   * The name of the resource at this point in the attack path. The format of
   * the name follows the Cloud Asset Inventory [resource name
   * format](https://cloud.google.com/asset-inventory/docs/resource-name-format)
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
   * The [supported resource type](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types)
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * Unique id of the attack path node.
   *
   * @param string $uuid
   */
  public function setUuid($uuid)
  {
    $this->uuid = $uuid;
  }
  /**
   * @return string
   */
  public function getUuid()
  {
    return $this->uuid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AttackPathNode::class, 'Google_Service_SecurityCommandCenter_AttackPathNode');
