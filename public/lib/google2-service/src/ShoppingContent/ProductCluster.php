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

class ProductCluster extends \Google\Collection
{
  /**
   * Inventory status is unknown.
   */
  public const BRAND_INVENTORY_STATUS_INVENTORY_STATUS_UNSPECIFIED = 'INVENTORY_STATUS_UNSPECIFIED';
  /**
   * Merchant has a product for this product cluster or brand in stock.
   */
  public const BRAND_INVENTORY_STATUS_IN_STOCK = 'IN_STOCK';
  /**
   * Merchant has a product for this product cluster or brand in inventory but
   * it is currently out of stock.
   */
  public const BRAND_INVENTORY_STATUS_OUT_OF_STOCK = 'OUT_OF_STOCK';
  /**
   * Merchant does not have a product for this product cluster or brand in
   * inventory.
   */
  public const BRAND_INVENTORY_STATUS_NOT_IN_INVENTORY = 'NOT_IN_INVENTORY';
  /**
   * Inventory status is unknown.
   */
  public const INVENTORY_STATUS_INVENTORY_STATUS_UNSPECIFIED = 'INVENTORY_STATUS_UNSPECIFIED';
  /**
   * Merchant has a product for this product cluster or brand in stock.
   */
  public const INVENTORY_STATUS_IN_STOCK = 'IN_STOCK';
  /**
   * Merchant has a product for this product cluster or brand in inventory but
   * it is currently out of stock.
   */
  public const INVENTORY_STATUS_OUT_OF_STOCK = 'OUT_OF_STOCK';
  /**
   * Merchant does not have a product for this product cluster or brand in
   * inventory.
   */
  public const INVENTORY_STATUS_NOT_IN_INVENTORY = 'NOT_IN_INVENTORY';
  protected $collection_key = 'variantGtins';
  /**
   * Brand of the product cluster.
   *
   * @var string
   */
  public $brand;
  /**
   * Tells if there is at least one product of the brand currently `IN_STOCK` in
   * your product feed across multiple countries, all products are
   * `OUT_OF_STOCK` in your product feed, or `NOT_IN_INVENTORY`. The field
   * doesn't take the Best Sellers report country filter into account.
   *
   * @var string
   */
  public $brandInventoryStatus;
  /**
   * Product category (1st level) of the product cluster, represented in
   * Google's product taxonomy.
   *
   * @var string
   */
  public $categoryL1;
  /**
   * Product category (2nd level) of the product cluster, represented in
   * Google's product taxonomy.
   *
   * @var string
   */
  public $categoryL2;
  /**
   * Product category (3rd level) of the product cluster, represented in
   * Google's product taxonomy.
   *
   * @var string
   */
  public $categoryL3;
  /**
   * Product category (4th level) of the product cluster, represented in
   * Google's product taxonomy.
   *
   * @var string
   */
  public $categoryL4;
  /**
   * Product category (5th level) of the product cluster, represented in
   * Google's product taxonomy.
   *
   * @var string
   */
  public $categoryL5;
  /**
   * Tells whether the product cluster is `IN_STOCK` in your product feed across
   * multiple countries, `OUT_OF_STOCK` in your product feed, or
   * `NOT_IN_INVENTORY` at all. The field doesn't take the Best Sellers report
   * country filter into account.
   *
   * @var string
   */
  public $inventoryStatus;
  /**
   * Title of the product cluster.
   *
   * @var string
   */
  public $title;
  /**
   * GTINs of example variants of the product cluster.
   *
   * @var string[]
   */
  public $variantGtins;

  /**
   * Brand of the product cluster.
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
   * Tells if there is at least one product of the brand currently `IN_STOCK` in
   * your product feed across multiple countries, all products are
   * `OUT_OF_STOCK` in your product feed, or `NOT_IN_INVENTORY`. The field
   * doesn't take the Best Sellers report country filter into account.
   *
   * Accepted values: INVENTORY_STATUS_UNSPECIFIED, IN_STOCK, OUT_OF_STOCK,
   * NOT_IN_INVENTORY
   *
   * @param self::BRAND_INVENTORY_STATUS_* $brandInventoryStatus
   */
  public function setBrandInventoryStatus($brandInventoryStatus)
  {
    $this->brandInventoryStatus = $brandInventoryStatus;
  }
  /**
   * @return self::BRAND_INVENTORY_STATUS_*
   */
  public function getBrandInventoryStatus()
  {
    return $this->brandInventoryStatus;
  }
  /**
   * Product category (1st level) of the product cluster, represented in
   * Google's product taxonomy.
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
   * Product category (2nd level) of the product cluster, represented in
   * Google's product taxonomy.
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
   * Product category (3rd level) of the product cluster, represented in
   * Google's product taxonomy.
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
   * Product category (4th level) of the product cluster, represented in
   * Google's product taxonomy.
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
   * Product category (5th level) of the product cluster, represented in
   * Google's product taxonomy.
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
   * Tells whether the product cluster is `IN_STOCK` in your product feed across
   * multiple countries, `OUT_OF_STOCK` in your product feed, or
   * `NOT_IN_INVENTORY` at all. The field doesn't take the Best Sellers report
   * country filter into account.
   *
   * Accepted values: INVENTORY_STATUS_UNSPECIFIED, IN_STOCK, OUT_OF_STOCK,
   * NOT_IN_INVENTORY
   *
   * @param self::INVENTORY_STATUS_* $inventoryStatus
   */
  public function setInventoryStatus($inventoryStatus)
  {
    $this->inventoryStatus = $inventoryStatus;
  }
  /**
   * @return self::INVENTORY_STATUS_*
   */
  public function getInventoryStatus()
  {
    return $this->inventoryStatus;
  }
  /**
   * Title of the product cluster.
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
   * GTINs of example variants of the product cluster.
   *
   * @param string[] $variantGtins
   */
  public function setVariantGtins($variantGtins)
  {
    $this->variantGtins = $variantGtins;
  }
  /**
   * @return string[]
   */
  public function getVariantGtins()
  {
    return $this->variantGtins;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductCluster::class, 'Google_Service_ShoppingContent_ProductCluster');
