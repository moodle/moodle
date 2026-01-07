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

namespace Google\Service\Books;

class Annotations extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $itemsType = Annotation::class;
  protected $itemsDataType = 'array';
  /**
   * Resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * Token to pass in for pagination for the next page. This will not be present
   * if this request does not have more results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Total number of annotations found. This may be greater than the number of
   * notes returned in this response if results have been paginated.
   *
   * @var int
   */
  public $totalItems;

  /**
   * A list of annotations.
   *
   * @param Annotation[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Annotation[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Resource type.
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
   * Token to pass in for pagination for the next page. This will not be present
   * if this request does not have more results.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Total number of annotations found. This may be greater than the number of
   * notes returned in this response if results have been paginated.
   *
   * @param int $totalItems
   */
  public function setTotalItems($totalItems)
  {
    $this->totalItems = $totalItems;
  }
  /**
   * @return int
   */
  public function getTotalItems()
  {
    return $this->totalItems;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Annotations::class, 'Google_Service_Books_Annotations');
