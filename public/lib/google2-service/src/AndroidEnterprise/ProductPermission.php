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

namespace Google\Service\AndroidEnterprise;

class ProductPermission extends \Google\Model
{
  /**
   * The permission is required by the app but has not yet been accepted by the
   * enterprise.
   */
  public const STATE_required = 'required';
  /**
   * The permission has been accepted by the enterprise.
   */
  public const STATE_accepted = 'accepted';
  /**
   * An opaque string uniquely identifying the permission.
   *
   * @var string
   */
  public $permissionId;
  /**
   * Whether the permission has been accepted or not.
   *
   * @var string
   */
  public $state;

  /**
   * An opaque string uniquely identifying the permission.
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
  /**
   * Whether the permission has been accepted or not.
   *
   * Accepted values: required, accepted
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductPermission::class, 'Google_Service_AndroidEnterprise_ProductPermission');
