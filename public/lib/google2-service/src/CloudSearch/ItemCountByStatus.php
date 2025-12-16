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

namespace Google\Service\CloudSearch;

class ItemCountByStatus extends \Google\Model
{
  /**
   * Input-only value. Used with Items.list to list all items in the queue,
   * regardless of status.
   */
  public const STATUS_CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * Error encountered by Cloud Search while processing this item. Details of
   * the error are in repositoryError.
   */
  public const STATUS_CODE_ERROR = 'ERROR';
  /**
   * Item has been modified in the repository, and is out of date with the
   * version previously accepted into Cloud Search.
   */
  public const STATUS_CODE_MODIFIED = 'MODIFIED';
  /**
   * Item is known to exist in the repository, but is not yet accepted by Cloud
   * Search. An item can be in this state when Items.push has been called for an
   * item of this name that did not exist previously.
   */
  public const STATUS_CODE_NEW_ITEM = 'NEW_ITEM';
  /**
   * API has accepted the up-to-date data of this item.
   */
  public const STATUS_CODE_ACCEPTED = 'ACCEPTED';
  /**
   * Number of items matching the status code.
   *
   * @var string
   */
  public $count;
  /**
   * Number of items matching the status code for which billing is done. This
   * excludes virtual container items from the total count. This count would not
   * be applicable for items with ERROR or NEW_ITEM status code.
   *
   * @var string
   */
  public $indexedItemsCount;
  /**
   * Status of the items.
   *
   * @var string
   */
  public $statusCode;

  /**
   * Number of items matching the status code.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Number of items matching the status code for which billing is done. This
   * excludes virtual container items from the total count. This count would not
   * be applicable for items with ERROR or NEW_ITEM status code.
   *
   * @param string $indexedItemsCount
   */
  public function setIndexedItemsCount($indexedItemsCount)
  {
    $this->indexedItemsCount = $indexedItemsCount;
  }
  /**
   * @return string
   */
  public function getIndexedItemsCount()
  {
    return $this->indexedItemsCount;
  }
  /**
   * Status of the items.
   *
   * Accepted values: CODE_UNSPECIFIED, ERROR, MODIFIED, NEW_ITEM, ACCEPTED
   *
   * @param self::STATUS_CODE_* $statusCode
   */
  public function setStatusCode($statusCode)
  {
    $this->statusCode = $statusCode;
  }
  /**
   * @return self::STATUS_CODE_*
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ItemCountByStatus::class, 'Google_Service_CloudSearch_ItemCountByStatus');
