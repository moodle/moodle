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

namespace Google\Service\CloudAsset;

class RelationshipAttributes extends \Google\Model
{
  /**
   * The detail of the relationship, e.g. `contains`, `attaches`
   *
   * @var string
   */
  public $action;
  /**
   * The source asset type. Example: `compute.googleapis.com/Instance`
   *
   * @var string
   */
  public $sourceResourceType;
  /**
   * The target asset type. Example: `compute.googleapis.com/Disk`
   *
   * @var string
   */
  public $targetResourceType;
  /**
   * The unique identifier of the relationship type. Example:
   * `INSTANCE_TO_INSTANCEGROUP`
   *
   * @var string
   */
  public $type;

  /**
   * The detail of the relationship, e.g. `contains`, `attaches`
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * The source asset type. Example: `compute.googleapis.com/Instance`
   *
   * @param string $sourceResourceType
   */
  public function setSourceResourceType($sourceResourceType)
  {
    $this->sourceResourceType = $sourceResourceType;
  }
  /**
   * @return string
   */
  public function getSourceResourceType()
  {
    return $this->sourceResourceType;
  }
  /**
   * The target asset type. Example: `compute.googleapis.com/Disk`
   *
   * @param string $targetResourceType
   */
  public function setTargetResourceType($targetResourceType)
  {
    $this->targetResourceType = $targetResourceType;
  }
  /**
   * @return string
   */
  public function getTargetResourceType()
  {
    return $this->targetResourceType;
  }
  /**
   * The unique identifier of the relationship type. Example:
   * `INSTANCE_TO_INSTANCEGROUP`
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RelationshipAttributes::class, 'Google_Service_CloudAsset_RelationshipAttributes');
