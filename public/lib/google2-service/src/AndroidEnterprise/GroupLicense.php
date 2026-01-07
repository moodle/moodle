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

class GroupLicense extends \Google\Model
{
  public const ACQUISITION_KIND_free = 'free';
  public const ACQUISITION_KIND_bulkPurchase = 'bulkPurchase';
  public const APPROVAL_approved = 'approved';
  public const APPROVAL_unapproved = 'unapproved';
  public const PERMISSIONS_currentApproved = 'currentApproved';
  public const PERMISSIONS_needsReapproval = 'needsReapproval';
  public const PERMISSIONS_allCurrentAndFutureApproved = 'allCurrentAndFutureApproved';
  /**
   * How this group license was acquired. "bulkPurchase" means that this
   * Grouplicenses resource was created because the enterprise purchased
   * licenses for this product; otherwise, the value is "free" (for free
   * products).
   *
   * @var string
   */
  public $acquisitionKind;
  /**
   * Whether the product to which this group license relates is currently
   * approved by the enterprise. Products are approved when a group license is
   * first created, but this approval may be revoked by an enterprise admin via
   * Google Play. Unapproved products will not be visible to end users in
   * collections, and new entitlements to them should not normally be created.
   *
   * @var string
   */
  public $approval;
  /**
   * The total number of provisioned licenses for this product. Returned by read
   * operations, but ignored in write operations.
   *
   * @var int
   */
  public $numProvisioned;
  /**
   * The number of purchased licenses (possibly in multiple purchases). If this
   * field is omitted, then there is no limit on the number of licenses that can
   * be provisioned (for example, if the acquisition kind is "free").
   *
   * @var int
   */
  public $numPurchased;
  /**
   * The permission approval status of the product. This field is only set if
   * the product is approved. Possible states are: - "currentApproved", the
   * current set of permissions is approved, but additional permissions will
   * require the administrator to reapprove the product (If the product was
   * approved without specifying the approved permissions setting, then this is
   * the default behavior.), - "needsReapproval", the product has unapproved
   * permissions. No additional product licenses can be assigned until the
   * product is reapproved, - "allCurrentAndFutureApproved", the current
   * permissions are approved and any future permission updates will be
   * automatically approved without administrator review.
   *
   * @var string
   */
  public $permissions;
  /**
   * The ID of the product that the license is for. For example,
   * "app:com.google.android.gm".
   *
   * @var string
   */
  public $productId;

  /**
   * How this group license was acquired. "bulkPurchase" means that this
   * Grouplicenses resource was created because the enterprise purchased
   * licenses for this product; otherwise, the value is "free" (for free
   * products).
   *
   * Accepted values: free, bulkPurchase
   *
   * @param self::ACQUISITION_KIND_* $acquisitionKind
   */
  public function setAcquisitionKind($acquisitionKind)
  {
    $this->acquisitionKind = $acquisitionKind;
  }
  /**
   * @return self::ACQUISITION_KIND_*
   */
  public function getAcquisitionKind()
  {
    return $this->acquisitionKind;
  }
  /**
   * Whether the product to which this group license relates is currently
   * approved by the enterprise. Products are approved when a group license is
   * first created, but this approval may be revoked by an enterprise admin via
   * Google Play. Unapproved products will not be visible to end users in
   * collections, and new entitlements to them should not normally be created.
   *
   * Accepted values: approved, unapproved
   *
   * @param self::APPROVAL_* $approval
   */
  public function setApproval($approval)
  {
    $this->approval = $approval;
  }
  /**
   * @return self::APPROVAL_*
   */
  public function getApproval()
  {
    return $this->approval;
  }
  /**
   * The total number of provisioned licenses for this product. Returned by read
   * operations, but ignored in write operations.
   *
   * @param int $numProvisioned
   */
  public function setNumProvisioned($numProvisioned)
  {
    $this->numProvisioned = $numProvisioned;
  }
  /**
   * @return int
   */
  public function getNumProvisioned()
  {
    return $this->numProvisioned;
  }
  /**
   * The number of purchased licenses (possibly in multiple purchases). If this
   * field is omitted, then there is no limit on the number of licenses that can
   * be provisioned (for example, if the acquisition kind is "free").
   *
   * @param int $numPurchased
   */
  public function setNumPurchased($numPurchased)
  {
    $this->numPurchased = $numPurchased;
  }
  /**
   * @return int
   */
  public function getNumPurchased()
  {
    return $this->numPurchased;
  }
  /**
   * The permission approval status of the product. This field is only set if
   * the product is approved. Possible states are: - "currentApproved", the
   * current set of permissions is approved, but additional permissions will
   * require the administrator to reapprove the product (If the product was
   * approved without specifying the approved permissions setting, then this is
   * the default behavior.), - "needsReapproval", the product has unapproved
   * permissions. No additional product licenses can be assigned until the
   * product is reapproved, - "allCurrentAndFutureApproved", the current
   * permissions are approved and any future permission updates will be
   * automatically approved without administrator review.
   *
   * Accepted values: currentApproved, needsReapproval,
   * allCurrentAndFutureApproved
   *
   * @param self::PERMISSIONS_* $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return self::PERMISSIONS_*
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * The ID of the product that the license is for. For example,
   * "app:com.google.android.gm".
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupLicense::class, 'Google_Service_AndroidEnterprise_GroupLicense');
