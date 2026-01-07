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

class Segments extends \Google\Model
{
  /**
   * Not specified.
   */
  public const PROGRAM_PROGRAM_UNSPECIFIED = 'PROGRAM_UNSPECIFIED';
  /**
   * Shopping Ads.
   */
  public const PROGRAM_SHOPPING_ADS = 'SHOPPING_ADS';
  /**
   * Free Product Listing.
   */
  public const PROGRAM_FREE_PRODUCT_LISTING = 'FREE_PRODUCT_LISTING';
  /**
   * Free Local Product Listing.
   */
  public const PROGRAM_FREE_LOCAL_PRODUCT_LISTING = 'FREE_LOCAL_PRODUCT_LISTING';
  /**
   * *Deprecated*: This value is no longer supported. Retrieving all metrics for
   * the `BUY_ON_GOOGLE_LISTING` program returns 0 starting from May 2024. Buy
   * on Google Listing.
   */
  public const PROGRAM_BUY_ON_GOOGLE_LISTING = 'BUY_ON_GOOGLE_LISTING';
  /**
   * Brand of the product.
   *
   * @var string
   */
  public $brand;
  /**
   * [Product category (1st level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @var string
   */
  public $categoryL1;
  /**
   * [Product category (2nd level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @var string
   */
  public $categoryL2;
  /**
   * [Product category (3rd level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @var string
   */
  public $categoryL3;
  /**
   * [Product category (4th level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @var string
   */
  public $categoryL4;
  /**
   * [Product category (5th level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @var string
   */
  public $categoryL5;
  /**
   * Currency in which price metrics are represented, for example, if you select
   * `ordered_item_sales_micros`, the returned value will be represented by this
   * currency.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Custom label 0 for custom grouping of products.
   *
   * @var string
   */
  public $customLabel0;
  /**
   * Custom label 1 for custom grouping of products.
   *
   * @var string
   */
  public $customLabel1;
  /**
   * Custom label 2 for custom grouping of products.
   *
   * @var string
   */
  public $customLabel2;
  /**
   * Custom label 3 for custom grouping of products.
   *
   * @var string
   */
  public $customLabel3;
  /**
   * Custom label 4 for custom grouping of products.
   *
   * @var string
   */
  public $customLabel4;
  /**
   * Code of the country where the customer is located at the time of the event.
   * Represented in the ISO 3166 format. If the customer country cannot be
   * determined, a special 'ZZ' code is returned.
   *
   * @var string
   */
  public $customerCountryCode;
  protected $dateType = Date::class;
  protected $dateDataType = '';
  /**
   * Merchant-provided id of the product.
   *
   * @var string
   */
  public $offerId;
  /**
   * [Product type (1st level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @var string
   */
  public $productTypeL1;
  /**
   * [Product type (2nd level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @var string
   */
  public $productTypeL2;
  /**
   * [Product type (3rd level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @var string
   */
  public $productTypeL3;
  /**
   * [Product type (4th level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @var string
   */
  public $productTypeL4;
  /**
   * [Product type (5th level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @var string
   */
  public $productTypeL5;
  /**
   * Program to which metrics apply, for example, Free Product Listing.
   *
   * @var string
   */
  public $program;
  /**
   * Title of the product.
   *
   * @var string
   */
  public $title;
  protected $weekType = Date::class;
  protected $weekDataType = '';

  /**
   * Brand of the product.
   *
   * @param string $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * [Product category (1st level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @param string $categoryL1
   */
  public function setCategoryL1($categoryL1)
  {
    $this->categoryL1 = $categoryL1;
  }
  /**
   * @return string
   */
  public function getCategoryL1()
  {
    return $this->categoryL1;
  }
  /**
   * [Product category (2nd level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @param string $categoryL2
   */
  public function setCategoryL2($categoryL2)
  {
    $this->categoryL2 = $categoryL2;
  }
  /**
   * @return string
   */
  public function getCategoryL2()
  {
    return $this->categoryL2;
  }
  /**
   * [Product category (3rd level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @param string $categoryL3
   */
  public function setCategoryL3($categoryL3)
  {
    $this->categoryL3 = $categoryL3;
  }
  /**
   * @return string
   */
  public function getCategoryL3()
  {
    return $this->categoryL3;
  }
  /**
   * [Product category (4th level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @param string $categoryL4
   */
  public function setCategoryL4($categoryL4)
  {
    $this->categoryL4 = $categoryL4;
  }
  /**
   * @return string
   */
  public function getCategoryL4()
  {
    return $this->categoryL4;
  }
  /**
   * [Product category (5th level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in Google's
   * product taxonomy.
   *
   * @param string $categoryL5
   */
  public function setCategoryL5($categoryL5)
  {
    $this->categoryL5 = $categoryL5;
  }
  /**
   * @return string
   */
  public function getCategoryL5()
  {
    return $this->categoryL5;
  }
  /**
   * Currency in which price metrics are represented, for example, if you select
   * `ordered_item_sales_micros`, the returned value will be represented by this
   * currency.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Custom label 0 for custom grouping of products.
   *
   * @param string $customLabel0
   */
  public function setCustomLabel0($customLabel0)
  {
    $this->customLabel0 = $customLabel0;
  }
  /**
   * @return string
   */
  public function getCustomLabel0()
  {
    return $this->customLabel0;
  }
  /**
   * Custom label 1 for custom grouping of products.
   *
   * @param string $customLabel1
   */
  public function setCustomLabel1($customLabel1)
  {
    $this->customLabel1 = $customLabel1;
  }
  /**
   * @return string
   */
  public function getCustomLabel1()
  {
    return $this->customLabel1;
  }
  /**
   * Custom label 2 for custom grouping of products.
   *
   * @param string $customLabel2
   */
  public function setCustomLabel2($customLabel2)
  {
    $this->customLabel2 = $customLabel2;
  }
  /**
   * @return string
   */
  public function getCustomLabel2()
  {
    return $this->customLabel2;
  }
  /**
   * Custom label 3 for custom grouping of products.
   *
   * @param string $customLabel3
   */
  public function setCustomLabel3($customLabel3)
  {
    $this->customLabel3 = $customLabel3;
  }
  /**
   * @return string
   */
  public function getCustomLabel3()
  {
    return $this->customLabel3;
  }
  /**
   * Custom label 4 for custom grouping of products.
   *
   * @param string $customLabel4
   */
  public function setCustomLabel4($customLabel4)
  {
    $this->customLabel4 = $customLabel4;
  }
  /**
   * @return string
   */
  public function getCustomLabel4()
  {
    return $this->customLabel4;
  }
  /**
   * Code of the country where the customer is located at the time of the event.
   * Represented in the ISO 3166 format. If the customer country cannot be
   * determined, a special 'ZZ' code is returned.
   *
   * @param string $customerCountryCode
   */
  public function setCustomerCountryCode($customerCountryCode)
  {
    $this->customerCountryCode = $customerCountryCode;
  }
  /**
   * @return string
   */
  public function getCustomerCountryCode()
  {
    return $this->customerCountryCode;
  }
  /**
   * Date in the merchant timezone to which metrics apply.
   *
   * @param Date $date
   */
  public function setDate(Date $date)
  {
    $this->date = $date;
  }
  /**
   * @return Date
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * Merchant-provided id of the product.
   *
   * @param string $offerId
   */
  public function setOfferId($offerId)
  {
    $this->offerId = $offerId;
  }
  /**
   * @return string
   */
  public function getOfferId()
  {
    return $this->offerId;
  }
  /**
   * [Product type (1st level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @param string $productTypeL1
   */
  public function setProductTypeL1($productTypeL1)
  {
    $this->productTypeL1 = $productTypeL1;
  }
  /**
   * @return string
   */
  public function getProductTypeL1()
  {
    return $this->productTypeL1;
  }
  /**
   * [Product type (2nd level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @param string $productTypeL2
   */
  public function setProductTypeL2($productTypeL2)
  {
    $this->productTypeL2 = $productTypeL2;
  }
  /**
   * @return string
   */
  public function getProductTypeL2()
  {
    return $this->productTypeL2;
  }
  /**
   * [Product type (3rd level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @param string $productTypeL3
   */
  public function setProductTypeL3($productTypeL3)
  {
    $this->productTypeL3 = $productTypeL3;
  }
  /**
   * @return string
   */
  public function getProductTypeL3()
  {
    return $this->productTypeL3;
  }
  /**
   * [Product type (4th level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @param string $productTypeL4
   */
  public function setProductTypeL4($productTypeL4)
  {
    $this->productTypeL4 = $productTypeL4;
  }
  /**
   * @return string
   */
  public function getProductTypeL4()
  {
    return $this->productTypeL4;
  }
  /**
   * [Product type (5th level)](https://developers.google.com/shopping-
   * content/guides/reports/segmentation#category_and_product_type) in
   * merchant's own product taxonomy.
   *
   * @param string $productTypeL5
   */
  public function setProductTypeL5($productTypeL5)
  {
    $this->productTypeL5 = $productTypeL5;
  }
  /**
   * @return string
   */
  public function getProductTypeL5()
  {
    return $this->productTypeL5;
  }
  /**
   * Program to which metrics apply, for example, Free Product Listing.
   *
   * Accepted values: PROGRAM_UNSPECIFIED, SHOPPING_ADS, FREE_PRODUCT_LISTING,
   * FREE_LOCAL_PRODUCT_LISTING, BUY_ON_GOOGLE_LISTING
   *
   * @param self::PROGRAM_* $program
   */
  public function setProgram($program)
  {
    $this->program = $program;
  }
  /**
   * @return self::PROGRAM_*
   */
  public function getProgram()
  {
    return $this->program;
  }
  /**
   * Title of the product.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * First day of the week (Monday) of the metrics date in the merchant
   * timezone.
   *
   * @param Date $week
   */
  public function setWeek(Date $week)
  {
    $this->week = $week;
  }
  /**
   * @return Date
   */
  public function getWeek()
  {
    return $this->week;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Segments::class, 'Google_Service_ShoppingContent_Segments');
