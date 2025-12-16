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

namespace Google\Service\SQLAdmin;

class OperationsListResponse extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $itemsType = Operation::class;
  protected $itemsDataType = 'array';
  /**
   * This is always `sql#operationsList`.
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
   * List of operation resources.
   *
   * @param Operation[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Operation[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * This is always `sql#operationsList`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationsListResponse::class, 'Google_Service_SQLAdmin_OperationsListResponse');
