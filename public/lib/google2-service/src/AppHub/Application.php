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

class Application extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Application is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The Application is ready to register Services and Workloads.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The Application is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  protected $attributesType = Attributes::class;
  protected $attributesDataType = '';
  /**
   * Output only. Create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-defined description of an Application. Can have a maximum
   * length of 2048 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User-defined name for the Application. Can have a maximum length
   * of 63 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The resource name of an Application. Format: `"projects/{host-
   * project-id}/locations/{location}/applications/{application-id}"`
   *
   * @var string
   */
  public $name;
  protected $scopeType = Scope::class;
  protected $scopeDataType = '';
  /**
   * Output only. Application state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. A universally unique identifier (in UUID4 format) for the
   * `Application`.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Update time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Consumer provided attributes.
   *
   * @param Attributes $attributes
   */
  public function setAttributes(Attributes $attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return Attributes
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
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
   * Optional. User-defined description of an Application. Can have a maximum
   * length of 2048 characters.
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
   * Optional. User-defined name for the Application. Can have a maximum length
   * of 63 characters.
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
   * Identifier. The resource name of an Application. Format: `"projects/{host-
   * project-id}/locations/{location}/applications/{application-id}"`
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
   * Required. Immutable. Defines what data can be included into this
   * Application. Limits which Services and Workloads can be registered.
   *
   * @param Scope $scope
   */
  public function setScope(Scope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return Scope
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Output only. Application state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING
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
   * Output only. A universally unique identifier (in UUID4 format) for the
   * `Application`.
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
class_alias(Application::class, 'Google_Service_AppHub_Application');
