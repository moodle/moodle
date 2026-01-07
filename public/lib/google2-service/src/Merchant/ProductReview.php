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

namespace Google\Service\Merchant;

class ProductReview extends \Google\Collection
{
  protected $collection_key = 'customAttributes';
  protected $customAttributesType = CustomAttribute::class;
  protected $customAttributesDataType = 'array';
  /**
   * Output only. The primary data source of the product review.
   *
   * @var string
   */
  public $dataSource;
  /**
   * Identifier. The name of the product review. Format:
   * `"{productreview.name=accounts/{account}/productReviews/{productReview}}"`
   *
   * @var string
   */
  public $name;
  protected $productReviewAttributesType = ProductReviewAttributes::class;
  protected $productReviewAttributesDataType = '';
  /**
   * Required. The permanent, unique identifier for the product review in the
   * publisher’s system.
   *
   * @var string
   */
  public $productReviewId;
  protected $productReviewStatusType = ProductReviewStatus::class;
  protected $productReviewStatusDataType = '';

  /**
   * Optional. A list of custom (merchant-provided) attributes.
   *
   * @param CustomAttribute[] $customAttributes
   */
  public function setCustomAttributes($customAttributes)
  {
    $this->customAttributes = $customAttributes;
  }
  /**
   * @return CustomAttribute[]
   */
  public function getCustomAttributes()
  {
    return $this->customAttributes;
  }
  /**
   * Output only. The primary data source of the product review.
   *
   * @param string $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return string
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Identifier. The name of the product review. Format:
   * `"{productreview.name=accounts/{account}/productReviews/{productReview}}"`
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
   * Optional. A list of product review attributes.
   *
   * @param ProductReviewAttributes $productReviewAttributes
   */
  public function setProductReviewAttributes(ProductReviewAttributes $productReviewAttributes)
  {
    $this->productReviewAttributes = $productReviewAttributes;
  }
  /**
   * @return ProductReviewAttributes
   */
  public function getProductReviewAttributes()
  {
    return $this->productReviewAttributes;
  }
  /**
   * Required. The permanent, unique identifier for the product review in the
   * publisher’s system.
   *
   * @param string $productReviewId
   */
  public function setProductReviewId($productReviewId)
  {
    $this->productReviewId = $productReviewId;
  }
  /**
   * @return string
   */
  public function getProductReviewId()
  {
    return $this->productReviewId;
  }
  /**
   * Output only. The status of a product review, data validation issues, that
   * is, information about a product review computed asynchronously.
   *
   * @param ProductReviewStatus $productReviewStatus
   */
  public function setProductReviewStatus(ProductReviewStatus $productReviewStatus)
  {
    $this->productReviewStatus = $productReviewStatus;
  }
  /**
   * @return ProductReviewStatus
   */
  public function getProductReviewStatus()
  {
    return $this->productReviewStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductReview::class, 'Google_Service_Merchant_ProductReview');
