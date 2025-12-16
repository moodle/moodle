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

class ProductApprovalEvent extends \Google\Model
{
  /**
   * Conveys no information.
   */
  public const APPROVED_unknown = 'unknown';
  /**
   * The product was approved.
   */
  public const APPROVED_approved = 'approved';
  /**
   * The product was unapproved.
   */
  public const APPROVED_unapproved = 'unapproved';
  /**
   * Whether the product was approved or unapproved. This field will always be
   * present.
   *
   * @var string
   */
  public $approved;
  /**
   * The id of the product (e.g. "app:com.google.android.gm") for which the
   * approval status has changed. This field will always be present.
   *
   * @var string
   */
  public $productId;

  /**
   * Whether the product was approved or unapproved. This field will always be
   * present.
   *
   * Accepted values: unknown, approved, unapproved
   *
   * @param self::APPROVED_* $approved
   */
  public function setApproved($approved)
  {
    $this->approved = $approved;
  }
  /**
   * @return self::APPROVED_*
   */
  public function getApproved()
  {
    return $this->approved;
  }
  /**
   * The id of the product (e.g. "app:com.google.android.gm") for which the
   * approval status has changed. This field will always be present.
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
class_alias(ProductApprovalEvent::class, 'Google_Service_AndroidEnterprise_ProductApprovalEvent');
