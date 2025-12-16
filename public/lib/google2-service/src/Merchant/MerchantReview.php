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

class MerchantReview extends \Google\Collection
{
  protected $collection_key = 'customAttributes';
  protected $customAttributesType = CustomAttribute::class;
  protected $customAttributesDataType = 'array';
  /**
   * Output only. The primary data source of the merchant review.
   *
   * @var string
   */
  public $dataSource;
  protected $merchantReviewAttributesType = MerchantReviewAttributes::class;
  protected $merchantReviewAttributesDataType = '';
  /**
   * Required. The user provided merchant review ID to uniquely identify the
   * merchant review.
   *
   * @var string
   */
  public $merchantReviewId;
  protected $merchantReviewStatusType = MerchantReviewStatus::class;
  protected $merchantReviewStatusDataType = '';
  /**
   * Identifier. The name of the merchant review. Format: `"{merchantreview.name
   * =accounts/{account}/merchantReviews/{merchantReview}}"`
   *
   * @var string
   */
  public $name;

  /**
   * Optional. A list of custom (merchant-provided) attributes. It can also be
   * used for submitting any attribute of the data specification in its generic
   * form (for example, `{ "name": "size type", "value": "regular" }`). This is
   * useful for submitting attributes not explicitly exposed by the API, such as
   * experimental attributes. Maximum allowed number of characters for each
   * custom attribute is 10240 (represents sum of characters for name and
   * value). Maximum 2500 custom attributes can be set per product, with total
   * size of 102.4kB. Underscores in custom attribute names are replaced by
   * spaces upon insertion.
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
   * Output only. The primary data source of the merchant review.
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
   * Optional. A list of merchant review attributes.
   *
   * @param MerchantReviewAttributes $merchantReviewAttributes
   */
  public function setMerchantReviewAttributes(MerchantReviewAttributes $merchantReviewAttributes)
  {
    $this->merchantReviewAttributes = $merchantReviewAttributes;
  }
  /**
   * @return MerchantReviewAttributes
   */
  public function getMerchantReviewAttributes()
  {
    return $this->merchantReviewAttributes;
  }
  /**
   * Required. The user provided merchant review ID to uniquely identify the
   * merchant review.
   *
   * @param string $merchantReviewId
   */
  public function setMerchantReviewId($merchantReviewId)
  {
    $this->merchantReviewId = $merchantReviewId;
  }
  /**
   * @return string
   */
  public function getMerchantReviewId()
  {
    return $this->merchantReviewId;
  }
  /**
   * Output only. The status of a merchant review, data validation issues, that
   * is, information about a merchant review computed asynchronously.
   *
   * @param MerchantReviewStatus $merchantReviewStatus
   */
  public function setMerchantReviewStatus(MerchantReviewStatus $merchantReviewStatus)
  {
    $this->merchantReviewStatus = $merchantReviewStatus;
  }
  /**
   * @return MerchantReviewStatus
   */
  public function getMerchantReviewStatus()
  {
    return $this->merchantReviewStatus;
  }
  /**
   * Identifier. The name of the merchant review. Format: `"{merchantreview.name
   * =accounts/{account}/merchantReviews/{merchantReview}}"`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MerchantReview::class, 'Google_Service_Merchant_MerchantReview');
