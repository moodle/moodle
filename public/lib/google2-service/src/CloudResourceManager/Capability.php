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

namespace Google\Service\CloudResourceManager;

class Capability extends \Google\Model
{
  /**
   * Immutable. Identifier. The resource name of the capability. Must be in the
   * following form: * `folders/{folder_id}/capabilities/{capability_name}` For
   * example, `folders/123/capabilities/app-management` Following are the
   * allowed {capability_name} values: * `app-management`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The configured value of the capability at the given parent
   * resource.
   *
   * @var bool
   */
  public $value;

  /**
   * Immutable. Identifier. The resource name of the capability. Must be in the
   * following form: * `folders/{folder_id}/capabilities/{capability_name}` For
   * example, `folders/123/capabilities/app-management` Following are the
   * allowed {capability_name} values: * `app-management`
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
   * Required. The configured value of the capability at the given parent
   * resource.
   *
   * @param bool $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return bool
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Capability::class, 'Google_Service_CloudResourceManager_Capability');
