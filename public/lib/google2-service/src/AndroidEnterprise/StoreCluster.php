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

class StoreCluster extends \Google\Collection
{
  protected $collection_key = 'productId';
  /**
   * Unique ID of this cluster. Assigned by the server. Immutable once assigned.
   *
   * @var string
   */
  public $id;
  protected $nameType = LocalizedText::class;
  protected $nameDataType = 'array';
  /**
   * String (US-ASCII only) used to determine order of this cluster within the
   * parent page's elements. Page elements are sorted in lexicographic order of
   * this field. Duplicated values are allowed, but ordering between elements
   * with duplicate order is undefined. The value of this field is never visible
   * to a user, it is used solely for the purpose of defining an ordering.
   * Maximum length is 256 characters.
   *
   * @var string
   */
  public $orderInPage;
  /**
   * List of products in the order they are displayed in the cluster. There
   * should not be duplicates within a cluster.
   *
   * @var string[]
   */
  public $productId;

  /**
   * Unique ID of this cluster. Assigned by the server. Immutable once assigned.
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
   * Ordered list of localized strings giving the name of this page. The text
   * displayed is the one that best matches the user locale, or the first entry
   * if there is no good match. There needs to be at least one entry.
   *
   * @param LocalizedText[] $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return LocalizedText[]
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * String (US-ASCII only) used to determine order of this cluster within the
   * parent page's elements. Page elements are sorted in lexicographic order of
   * this field. Duplicated values are allowed, but ordering between elements
   * with duplicate order is undefined. The value of this field is never visible
   * to a user, it is used solely for the purpose of defining an ordering.
   * Maximum length is 256 characters.
   *
   * @param string $orderInPage
   */
  public function setOrderInPage($orderInPage)
  {
    $this->orderInPage = $orderInPage;
  }
  /**
   * @return string
   */
  public function getOrderInPage()
  {
    return $this->orderInPage;
  }
  /**
   * List of products in the order they are displayed in the cluster. There
   * should not be duplicates within a cluster.
   *
   * @param string[] $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string[]
   */
  public function getProductId()
  {
    return $this->productId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StoreCluster::class, 'Google_Service_AndroidEnterprise_StoreCluster');
