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

class Volumeannotations extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $itemsType = Volumeannotation::class;
  protected $itemsDataType = 'array';
  /**
   * Resource type
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
   * The total number of volume annotations found.
   *
   * @var int
   */
  public $totalItems;
  /**
   * The version string for all of the volume annotations in this layer (not
   * just the ones in this response). Note: the version string doesn't apply to
   * the annotation data, just the information in this response (e.g. the
   * location of annotations in the book).
   *
   * @var string
   */
  public $version;

  /**
   * A list of volume annotations.
   *
   * @param Volumeannotation[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Volumeannotation[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Resource type
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
   * The total number of volume annotations found.
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
  /**
   * The version string for all of the volume annotations in this layer (not
   * just the ones in this response). Note: the version string doesn't apply to
   * the annotation data, just the information in this response (e.g. the
   * location of annotations in the book).
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Volumeannotations::class, 'Google_Service_Books_Volumeannotations');
