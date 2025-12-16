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

namespace Google\Service\ShoppingContent;

class ReturnPolicyOnlineReturnReasonCategoryInfo extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const RETURN_LABEL_SOURCE_RETURN_LABEL_SOURCE_UNSPECIFIED = 'RETURN_LABEL_SOURCE_UNSPECIFIED';
  /**
   * Download and print the label.
   */
  public const RETURN_LABEL_SOURCE_DOWNLOAD_AND_PRINT = 'DOWNLOAD_AND_PRINT';
  /**
   * Label in the box.
   */
  public const RETURN_LABEL_SOURCE_IN_THE_BOX = 'IN_THE_BOX';
  /**
   * Customers' responsibility to get the label.
   */
  public const RETURN_LABEL_SOURCE_CUSTOMER_RESPONSIBILITY = 'CUSTOMER_RESPONSIBILITY';
  /**
   * Default value. This value is unused.
   */
  public const RETURN_REASON_CATEGORY_RETURN_REASON_CATEGORY_UNSPECIFIED = 'RETURN_REASON_CATEGORY_UNSPECIFIED';
  /**
   * Buyer remorse.
   */
  public const RETURN_REASON_CATEGORY_BUYER_REMORSE = 'BUYER_REMORSE';
  /**
   * Item defect.
   */
  public const RETURN_REASON_CATEGORY_ITEM_DEFECT = 'ITEM_DEFECT';
  /**
   * The corresponding return label source. If the `ReturnMethod` field includes
   * `BY_MAIL`, it is required to specify `ReturnLabelSource` for both
   * `BUYER_REMORSE` and `ITEM_DEFECT` return reason categories.
   *
   * @var string
   */
  public $returnLabelSource;
  /**
   * The return reason category.
   *
   * @var string
   */
  public $returnReasonCategory;
  protected $returnShippingFeeType = ReturnPolicyOnlineReturnShippingFee::class;
  protected $returnShippingFeeDataType = '';

  /**
   * The corresponding return label source. If the `ReturnMethod` field includes
   * `BY_MAIL`, it is required to specify `ReturnLabelSource` for both
   * `BUYER_REMORSE` and `ITEM_DEFECT` return reason categories.
   *
   * Accepted values: RETURN_LABEL_SOURCE_UNSPECIFIED, DOWNLOAD_AND_PRINT,
   * IN_THE_BOX, CUSTOMER_RESPONSIBILITY
   *
   * @param self::RETURN_LABEL_SOURCE_* $returnLabelSource
   */
  public function setReturnLabelSource($returnLabelSource)
  {
    $this->returnLabelSource = $returnLabelSource;
  }
  /**
   * @return self::RETURN_LABEL_SOURCE_*
   */
  public function getReturnLabelSource()
  {
    return $this->returnLabelSource;
  }
  /**
   * The return reason category.
   *
   * Accepted values: RETURN_REASON_CATEGORY_UNSPECIFIED, BUYER_REMORSE,
   * ITEM_DEFECT
   *
   * @param self::RETURN_REASON_CATEGORY_* $returnReasonCategory
   */
  public function setReturnReasonCategory($returnReasonCategory)
  {
    $this->returnReasonCategory = $returnReasonCategory;
  }
  /**
   * @return self::RETURN_REASON_CATEGORY_*
   */
  public function getReturnReasonCategory()
  {
    return $this->returnReasonCategory;
  }
  /**
   * The corresponding return shipping fee. This is only applicable when
   * returnLabelSource is not the customer's responsibility.
   *
   * @param ReturnPolicyOnlineReturnShippingFee $returnShippingFee
   */
  public function setReturnShippingFee(ReturnPolicyOnlineReturnShippingFee $returnShippingFee)
  {
    $this->returnShippingFee = $returnShippingFee;
  }
  /**
   * @return ReturnPolicyOnlineReturnShippingFee
   */
  public function getReturnShippingFee()
  {
    return $this->returnShippingFee;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReturnPolicyOnlineReturnReasonCategoryInfo::class, 'Google_Service_ShoppingContent_ReturnPolicyOnlineReturnReasonCategoryInfo');
