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

class GoogleCloudVisionV1p2beta1Product extends \Google\Collection
{
  protected $collection_key = 'productLabels';
  /**
   * User-provided metadata to be stored with this product. Must be at most 4096
   * characters long.
   *
   * @var string
   */
  public $description;
  /**
   * The user-provided name for this Product. Must not be empty. Must be at most
   * 4096 characters long.
   *
   * @var string
   */
  public $displayName;
  /**
   * The resource name of the product. Format is:
   * `projects/PROJECT_ID/locations/LOC_ID/products/PRODUCT_ID`. This field is
   * ignored when creating a product.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The category for the product identified by the reference image.
   * This should be one of "homegoods-v2", "apparel-v2", "toys-v2",
   * "packagedgoods-v1" or "general-v1". The legacy categories "homegoods",
   * "apparel", and "toys" are still supported, but these should not be used for
   * new products.
   *
   * @var string
   */
  public $productCategory;
  protected $productLabelsType = GoogleCloudVisionV1p2beta1ProductKeyValue::class;
  protected $productLabelsDataType = 'array';

  /**
   * User-provided metadata to be stored with this product. Must be at most 4096
   * characters long.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The user-provided name for this Product. Must not be empty. Must be at most
   * 4096 characters long.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The resource name of the product. Format is:
   * `projects/PROJECT_ID/locations/LOC_ID/products/PRODUCT_ID`. This field is
   * ignored when creating a product.
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
   * Immutable. The category for the product identified by the reference image.
   * This should be one of "homegoods-v2", "apparel-v2", "toys-v2",
   * "packagedgoods-v1" or "general-v1". The legacy categories "homegoods",
   * "apparel", and "toys" are still supported, but these should not be used for
   * new products.
   *
   * @param string $productCategory
   */
  public function setProductCategory($productCategory)
  {
    $this->productCategory = $productCategory;
  }
  /**
   * @return string
   */
  public function getProductCategory()
  {
    return $this->productCategory;
  }
  /**
   * Key-value pairs that can be attached to a product. At query time,
   * constraints can be specified based on the product_labels. Note that integer
   * values can be provided as strings, e.g. "1199". Only strings with integer
   * values can match a range-based restriction which is to be supported soon.
   * Multiple values can be assigned to the same key. One product may have up to
   * 500 product_labels. Notice that the total number of distinct product_labels
   * over all products in one ProductSet cannot exceed 1M, otherwise the product
   * search pipeline will refuse to work for that ProductSet.
   *
   * @param GoogleCloudVisionV1p2beta1ProductKeyValue[] $productLabels
   */
  public function setProductLabels($productLabels)
  {
    $this->productLabels = $productLabels;
  }
  /**
   * @return GoogleCloudVisionV1p2beta1ProductKeyValue[]
   */
  public function getProductLabels()
  {
    return $this->productLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p2beta1Product::class, 'Google_Service_Vision_GoogleCloudVisionV1p2beta1Product');
