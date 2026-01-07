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

class ProductsApproveRequest extends \Google\Model
{
  /**
   * Approve only the permissions the product requires at approval time. If an
   * update requires additional permissions, the app will not be updated on
   * devices associated with enterprise users until the additional permissions
   * are approved.
   */
  public const APPROVED_PERMISSIONS_currentPermissionsOnly = 'currentPermissionsOnly';
  /**
   * All current and future permissions the app requires are automatically
   * approved.
   */
  public const APPROVED_PERMISSIONS_allPermissions = 'allPermissions';
  protected $approvalUrlInfoType = ApprovalUrlInfo::class;
  protected $approvalUrlInfoDataType = '';
  /**
   * Sets how new permission requests for the product are handled.
   * "allPermissions" automatically approves all current and future permissions
   * for the product. "currentPermissionsOnly" approves the current set of
   * permissions for the product, but any future permissions added through
   * updates will require manual reapproval. If not specified, only the current
   * set of permissions will be approved.
   *
   * @var string
   */
  public $approvedPermissions;

  /**
   * The approval URL that was shown to the user. Only the permissions shown to
   * the user with that URL will be accepted, which may not be the product's
   * entire set of permissions. For example, the URL may only display new
   * permissions from an update after the product was approved, or not include
   * new permissions if the product was updated since the URL was generated.
   *
   * @param ApprovalUrlInfo $approvalUrlInfo
   */
  public function setApprovalUrlInfo(ApprovalUrlInfo $approvalUrlInfo)
  {
    $this->approvalUrlInfo = $approvalUrlInfo;
  }
  /**
   * @return ApprovalUrlInfo
   */
  public function getApprovalUrlInfo()
  {
    return $this->approvalUrlInfo;
  }
  /**
   * Sets how new permission requests for the product are handled.
   * "allPermissions" automatically approves all current and future permissions
   * for the product. "currentPermissionsOnly" approves the current set of
   * permissions for the product, but any future permissions added through
   * updates will require manual reapproval. If not specified, only the current
   * set of permissions will be approved.
   *
   * Accepted values: currentPermissionsOnly, allPermissions
   *
   * @param self::APPROVED_PERMISSIONS_* $approvedPermissions
   */
  public function setApprovedPermissions($approvedPermissions)
  {
    $this->approvedPermissions = $approvedPermissions;
  }
  /**
   * @return self::APPROVED_PERMISSIONS_*
   */
  public function getApprovedPermissions()
  {
    return $this->approvedPermissions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductsApproveRequest::class, 'Google_Service_AndroidEnterprise_ProductsApproveRequest');
