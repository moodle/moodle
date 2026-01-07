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

namespace Google\Service\Storage;

class Buckets extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $itemsType = Bucket::class;
  protected $itemsDataType = 'array';
  /**
   * The kind of item this is. For lists of buckets, this is always
   * storage#buckets.
   *
   * @var string
   */
  public $kind;
  /**
   * The continuation token, used to page through large result sets. Provide
   * this value in a subsequent request to return the next page of results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The list of bucket resource names that could not be reached during the
   * listing operation.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The list of items.
   *
   * @param Bucket[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Bucket[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * The kind of item this is. For lists of buckets, this is always
   * storage#buckets.
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
   * The continuation token, used to page through large result sets. Provide
   * this value in a subsequent request to return the next page of results.
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
   * The list of bucket resource names that could not be reached during the
   * listing operation.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Buckets::class, 'Google_Service_Storage_Buckets');
