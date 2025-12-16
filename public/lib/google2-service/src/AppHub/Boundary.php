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

namespace Google\Service\AppHub;

class Boundary extends \Google\Model
{
  /**
   * Unspecified type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The Boundary automatically includes all descendants of the CRM node.
   */
  public const TYPE_AUTOMATIC = 'AUTOMATIC';
  /**
   * The list of projects within the Boundary is managed by the user.
   */
  public const TYPE_MANUAL = 'MANUAL';
  /**
   * The Boundary automatically includes all descendants of the CRM node, which
   * is set via App Management folder capability.
   */
  public const TYPE_MANAGED_AUTOMATIC = 'MANAGED_AUTOMATIC';
  /**
   * Output only. Create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The resource name of the CRM node being attached to the boundary.
   * Format: `projects/{project-number}`
   *
   * @var string
   */
  public $crmNode;
  /**
   * Identifier. The resource name of the boundary. Format:
   * "projects/{project}/locations/{location}/boundary"
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Boundary type.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Create time.
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
   * Optional. The resource name of the CRM node being attached to the boundary.
   * Format: `projects/{project-number}`
   *
   * @param string $crmNode
   */
  public function setCrmNode($crmNode)
  {
    $this->crmNode = $crmNode;
  }
  /**
   * @return string
   */
  public function getCrmNode()
  {
    return $this->crmNode;
  }
  /**
   * Identifier. The resource name of the boundary. Format:
   * "projects/{project}/locations/{location}/boundary"
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
   * Output only. Boundary type.
   *
   * Accepted values: TYPE_UNSPECIFIED, AUTOMATIC, MANUAL, MANAGED_AUTOMATIC
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Update time.
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
class_alias(Boundary::class, 'Google_Service_AppHub_Boundary');
