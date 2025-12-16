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

class NewPermissionsEvent extends \Google\Collection
{
  protected $collection_key = 'requestedPermissions';
  /**
   * The set of permissions that the enterprise admin has already approved for
   * this application. Use Permissions.Get on the EMM API to retrieve details
   * about these permissions.
   *
   * @var string[]
   */
  public $approvedPermissions;
  /**
   * The id of the product (e.g. "app:com.google.android.gm") for which new
   * permissions were added. This field will always be present.
   *
   * @var string
   */
  public $productId;
  /**
   * The set of permissions that the app is currently requesting. Use
   * Permissions.Get on the EMM API to retrieve details about these permissions.
   *
   * @var string[]
   */
  public $requestedPermissions;

  /**
   * The set of permissions that the enterprise admin has already approved for
   * this application. Use Permissions.Get on the EMM API to retrieve details
   * about these permissions.
   *
   * @param string[] $approvedPermissions
   */
  public function setApprovedPermissions($approvedPermissions)
  {
    $this->approvedPermissions = $approvedPermissions;
  }
  /**
   * @return string[]
   */
  public function getApprovedPermissions()
  {
    return $this->approvedPermissions;
  }
  /**
   * The id of the product (e.g. "app:com.google.android.gm") for which new
   * permissions were added. This field will always be present.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * The set of permissions that the app is currently requesting. Use
   * Permissions.Get on the EMM API to retrieve details about these permissions.
   *
   * @param string[] $requestedPermissions
   */
  public function setRequestedPermissions($requestedPermissions)
  {
    $this->requestedPermissions = $requestedPermissions;
  }
  /**
   * @return string[]
   */
  public function getRequestedPermissions()
  {
    return $this->requestedPermissions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NewPermissionsEvent::class, 'Google_Service_AndroidEnterprise_NewPermissionsEvent');
