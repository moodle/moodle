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

namespace Google\Service\AndroidManagement;

class ApplicationPermission extends \Google\Model
{
  /**
   * A longer description of the permission, providing more detail on what it
   * affects. Localized.
   *
   * @var string
   */
  public $description;
  /**
   * The name of the permission. Localized.
   *
   * @var string
   */
  public $name;
  /**
   * An opaque string uniquely identifying the permission. Not localized.
   *
   * @var string
   */
  public $permissionId;

  /**
   * A longer description of the permission, providing more detail on what it
   * affects. Localized.
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
   * The name of the permission. Localized.
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
   * An opaque string uniquely identifying the permission. Not localized.
   *
   * @param string $permissionId
   */
  public function setPermissionId($permissionId)
  {
    $this->permissionId = $permissionId;
  }
  /**
   * @return string
   */
  public function getPermissionId()
  {
    return $this->permissionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationPermission::class, 'Google_Service_AndroidManagement_ApplicationPermission');
