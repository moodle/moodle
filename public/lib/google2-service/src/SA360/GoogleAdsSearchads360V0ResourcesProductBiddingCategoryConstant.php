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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesProductBiddingCategoryConstant extends \Google\Model
{
  /**
   * Not specified.
   */
  public const LEVEL_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const LEVEL_UNKNOWN = 'UNKNOWN';
  /**
   * Level 1.
   */
  public const LEVEL_LEVEL1 = 'LEVEL1';
  /**
   * Level 2.
   */
  public const LEVEL_LEVEL2 = 'LEVEL2';
  /**
   * Level 3.
   */
  public const LEVEL_LEVEL3 = 'LEVEL3';
  /**
   * Level 4.
   */
  public const LEVEL_LEVEL4 = 'LEVEL4';
  /**
   * Level 5.
   */
  public const LEVEL_LEVEL5 = 'LEVEL5';
  /**
   * Not specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The category is active and can be used for bidding.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  /**
   * The category is obsolete. Used only for reporting purposes.
   */
  public const STATUS_OBSOLETE = 'OBSOLETE';
  /**
   * Output only. Two-letter upper-case country code of the product bidding
   * category.
   *
   * @var string
   */
  public $countryCode;
  /**
   * Output only. ID of the product bidding category. This ID is equivalent to
   * the google_product_category ID as described in this article:
   * https://support.google.com/merchants/answer/6324436.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Language code of the product bidding category.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Output only. Level of the product bidding category.
   *
   * @var string
   */
  public $level;
  /**
   * Output only. Display value of the product bidding category localized
   * according to language_code.
   *
   * @var string
   */
  public $localizedName;
  /**
   * Output only. Resource name of the parent product bidding category.
   *
   * @var string
   */
  public $productBiddingCategoryConstantParent;
  /**
   * Output only. The resource name of the product bidding category. Product
   * bidding category resource names have the form:
   * `productBiddingCategoryConstants/{country_code}~{level}~{id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. Status of the product bidding category.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. Two-letter upper-case country code of the product bidding
   * category.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * Output only. ID of the product bidding category. This ID is equivalent to
   * the google_product_category ID as described in this article:
   * https://support.google.com/merchants/answer/6324436.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Language code of the product bidding category.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Output only. Level of the product bidding category.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, LEVEL1, LEVEL2, LEVEL3, LEVEL4,
   * LEVEL5
   *
   * @param self::LEVEL_* $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }
  /**
   * @return self::LEVEL_*
   */
  public function getLevel()
  {
    return $this->level;
  }
  /**
   * Output only. Display value of the product bidding category localized
   * according to language_code.
   *
   * @param string $localizedName
   */
  public function setLocalizedName($localizedName)
  {
    $this->localizedName = $localizedName;
  }
  /**
   * @return string
   */
  public function getLocalizedName()
  {
    return $this->localizedName;
  }
  /**
   * Output only. Resource name of the parent product bidding category.
   *
   * @param string $productBiddingCategoryConstantParent
   */
  public function setProductBiddingCategoryConstantParent($productBiddingCategoryConstantParent)
  {
    $this->productBiddingCategoryConstantParent = $productBiddingCategoryConstantParent;
  }
  /**
   * @return string
   */
  public function getProductBiddingCategoryConstantParent()
  {
    return $this->productBiddingCategoryConstantParent;
  }
  /**
   * Output only. The resource name of the product bidding category. Product
   * bidding category resource names have the form:
   * `productBiddingCategoryConstants/{country_code}~{level}~{id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. Status of the product bidding category.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ACTIVE, OBSOLETE
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesProductBiddingCategoryConstant::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesProductBiddingCategoryConstant');
