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

class ProductStatus extends \Google\Collection
{
  protected $collection_key = 'itemLevelIssues';
  /**
   * Date on which the item has been created, in ISO 8601 format.
   *
   * @var string
   */
  public $creationDate;
  protected $destinationStatusesType = ProductStatusDestinationStatus::class;
  protected $destinationStatusesDataType = 'array';
  /**
   * Date on which the item expires in Google Shopping, in ISO 8601 format.
   *
   * @var string
   */
  public $googleExpirationDate;
  protected $itemLevelIssuesType = ProductStatusItemLevelIssue::class;
  protected $itemLevelIssuesDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#productStatus`"
   *
   * @var string
   */
  public $kind;
  /**
   * Date on which the item has been last updated, in ISO 8601 format.
   *
   * @var string
   */
  public $lastUpdateDate;
  /**
   * The link to the product.
   *
   * @var string
   */
  public $link;
  /**
   * The ID of the product for which status is reported.
   *
   * @var string
   */
  public $productId;
  /**
   * The title of the product.
   *
   * @var string
   */
  public $title;

  /**
   * Date on which the item has been created, in ISO 8601 format.
   *
   * @param string $creationDate
   */
  public function setCreationDate($creationDate)
  {
    $this->creationDate = $creationDate;
  }
  /**
   * @return string
   */
  public function getCreationDate()
  {
    return $this->creationDate;
  }
  /**
   * The intended destinations for the product.
   *
   * @param ProductStatusDestinationStatus[] $destinationStatuses
   */
  public function setDestinationStatuses($destinationStatuses)
  {
    $this->destinationStatuses = $destinationStatuses;
  }
  /**
   * @return ProductStatusDestinationStatus[]
   */
  public function getDestinationStatuses()
  {
    return $this->destinationStatuses;
  }
  /**
   * Date on which the item expires in Google Shopping, in ISO 8601 format.
   *
   * @param string $googleExpirationDate
   */
  public function setGoogleExpirationDate($googleExpirationDate)
  {
    $this->googleExpirationDate = $googleExpirationDate;
  }
  /**
   * @return string
   */
  public function getGoogleExpirationDate()
  {
    return $this->googleExpirationDate;
  }
  /**
   * A list of all issues associated with the product.
   *
   * @param ProductStatusItemLevelIssue[] $itemLevelIssues
   */
  public function setItemLevelIssues($itemLevelIssues)
  {
    $this->itemLevelIssues = $itemLevelIssues;
  }
  /**
   * @return ProductStatusItemLevelIssue[]
   */
  public function getItemLevelIssues()
  {
    return $this->itemLevelIssues;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#productStatus`"
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Date on which the item has been last updated, in ISO 8601 format.
   *
   * @param string $lastUpdateDate
   */
  public function setLastUpdateDate($lastUpdateDate)
  {
    $this->lastUpdateDate = $lastUpdateDate;
  }
  /**
   * @return string
   */
  public function getLastUpdateDate()
  {
    return $this->lastUpdateDate;
  }
  /**
   * The link to the product.
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * The ID of the product for which status is reported.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * The title of the product.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductStatus::class, 'Google_Service_ShoppingContent_ProductStatus');
