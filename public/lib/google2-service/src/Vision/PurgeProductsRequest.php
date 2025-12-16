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

namespace Google\Service\Vision;

class PurgeProductsRequest extends \Google\Model
{
  /**
   * If delete_orphan_products is true, all Products that are not in any
   * ProductSet will be deleted.
   *
   * @var bool
   */
  public $deleteOrphanProducts;
  /**
   * The default value is false. Override this value to true to actually perform
   * the purge.
   *
   * @var bool
   */
  public $force;
  protected $productSetPurgeConfigType = ProductSetPurgeConfig::class;
  protected $productSetPurgeConfigDataType = '';

  /**
   * If delete_orphan_products is true, all Products that are not in any
   * ProductSet will be deleted.
   *
   * @param bool $deleteOrphanProducts
   */
  public function setDeleteOrphanProducts($deleteOrphanProducts)
  {
    $this->deleteOrphanProducts = $deleteOrphanProducts;
  }
  /**
   * @return bool
   */
  public function getDeleteOrphanProducts()
  {
    return $this->deleteOrphanProducts;
  }
  /**
   * The default value is false. Override this value to true to actually perform
   * the purge.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * Specify which ProductSet contains the Products to be deleted.
   *
   * @param ProductSetPurgeConfig $productSetPurgeConfig
   */
  public function setProductSetPurgeConfig(ProductSetPurgeConfig $productSetPurgeConfig)
  {
    $this->productSetPurgeConfig = $productSetPurgeConfig;
  }
  /**
   * @return ProductSetPurgeConfig
   */
  public function getProductSetPurgeConfig()
  {
    return $this->productSetPurgeConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PurgeProductsRequest::class, 'Google_Service_Vision_PurgeProductsRequest');
