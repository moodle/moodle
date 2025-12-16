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

class ProductSearchParams extends \Google\Collection
{
  protected $collection_key = 'productCategories';
  protected $boundingPolyType = BoundingPoly::class;
  protected $boundingPolyDataType = '';
  /**
   * The filtering expression. This can be used to restrict search results based
   * on Product labels. We currently support an AND of OR of key-value
   * expressions, where each expression within an OR must have the same key. An
   * '=' should be used to connect the key and value. For example, "(color = red
   * OR color = blue) AND brand = Google" is acceptable, but "(color = red OR
   * brand = Google)" is not acceptable. "color: red" is not acceptable because
   * it uses a ':' instead of an '='.
   *
   * @var string
   */
  public $filter;
  /**
   * The list of product categories to search in. Currently, we only consider
   * the first category, and either "homegoods-v2", "apparel-v2", "toys-v2",
   * "packagedgoods-v1", or "general-v1" should be specified. The legacy
   * categories "homegoods", "apparel", and "toys" are still supported but will
   * be deprecated. For new products, please use "homegoods-v2", "apparel-v2",
   * or "toys-v2" for better product search accuracy. It is recommended to
   * migrate existing products to these categories as well.
   *
   * @var string[]
   */
  public $productCategories;
  /**
   * The resource name of a ProductSet to be searched for similar images. Format
   * is: `projects/PROJECT_ID/locations/LOC_ID/productSets/PRODUCT_SET_ID`.
   *
   * @var string
   */
  public $productSet;

  /**
   * The bounding polygon around the area of interest in the image. If it is not
   * specified, system discretion will be applied.
   *
   * @param BoundingPoly $boundingPoly
   */
  public function setBoundingPoly(BoundingPoly $boundingPoly)
  {
    $this->boundingPoly = $boundingPoly;
  }
  /**
   * @return BoundingPoly
   */
  public function getBoundingPoly()
  {
    return $this->boundingPoly;
  }
  /**
   * The filtering expression. This can be used to restrict search results based
   * on Product labels. We currently support an AND of OR of key-value
   * expressions, where each expression within an OR must have the same key. An
   * '=' should be used to connect the key and value. For example, "(color = red
   * OR color = blue) AND brand = Google" is acceptable, but "(color = red OR
   * brand = Google)" is not acceptable. "color: red" is not acceptable because
   * it uses a ':' instead of an '='.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The list of product categories to search in. Currently, we only consider
   * the first category, and either "homegoods-v2", "apparel-v2", "toys-v2",
   * "packagedgoods-v1", or "general-v1" should be specified. The legacy
   * categories "homegoods", "apparel", and "toys" are still supported but will
   * be deprecated. For new products, please use "homegoods-v2", "apparel-v2",
   * or "toys-v2" for better product search accuracy. It is recommended to
   * migrate existing products to these categories as well.
   *
   * @param string[] $productCategories
   */
  public function setProductCategories($productCategories)
  {
    $this->productCategories = $productCategories;
  }
  /**
   * @return string[]
   */
  public function getProductCategories()
  {
    return $this->productCategories;
  }
  /**
   * The resource name of a ProductSet to be searched for similar images. Format
   * is: `projects/PROJECT_ID/locations/LOC_ID/productSets/PRODUCT_SET_ID`.
   *
   * @param string $productSet
   */
  public function setProductSet($productSet)
  {
    $this->productSet = $productSet;
  }
  /**
   * @return string
   */
  public function getProductSet()
  {
    return $this->productSet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductSearchParams::class, 'Google_Service_Vision_ProductSearchParams');
