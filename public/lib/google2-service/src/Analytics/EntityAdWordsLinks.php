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

namespace Google\Service\Analytics;

class EntityAdWordsLinks extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $itemsType = EntityAdWordsLink::class;
  protected $itemsDataType = 'array';
  /**
   * The maximum number of entries the response can contain, regardless of the
   * actual number of entries returned. Its value ranges from 1 to 1000 with a
   * value of 1000 by default, or otherwise specified by the max-results query
   * parameter.
   *
   * @var int
   */
  public $itemsPerPage;
  /**
   * Collection type.
   *
   * @var string
   */
  public $kind;
  /**
   * Next link for this Google Ads link collection.
   *
   * @var string
   */
  public $nextLink;
  /**
   * Previous link for this Google Ads link collection.
   *
   * @var string
   */
  public $previousLink;
  /**
   * The starting index of the entries, which is 1 by default or otherwise
   * specified by the start-index query parameter.
   *
   * @var int
   */
  public $startIndex;
  /**
   * The total number of results for the query, regardless of the number of
   * results in the response.
   *
   * @var int
   */
  public $totalResults;

  /**
   * A list of entity Google Ads links.
   *
   * @param EntityAdWordsLink[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return EntityAdWordsLink[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * The maximum number of entries the response can contain, regardless of the
   * actual number of entries returned. Its value ranges from 1 to 1000 with a
   * value of 1000 by default, or otherwise specified by the max-results query
   * parameter.
   *
   * @param int $itemsPerPage
   */
  public function setItemsPerPage($itemsPerPage)
  {
    $this->itemsPerPage = $itemsPerPage;
  }
  /**
   * @return int
   */
  public function getItemsPerPage()
  {
    return $this->itemsPerPage;
  }
  /**
   * Collection type.
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
   * Next link for this Google Ads link collection.
   *
   * @param string $nextLink
   */
  public function setNextLink($nextLink)
  {
    $this->nextLink = $nextLink;
  }
  /**
   * @return string
   */
  public function getNextLink()
  {
    return $this->nextLink;
  }
  /**
   * Previous link for this Google Ads link collection.
   *
   * @param string $previousLink
   */
  public function setPreviousLink($previousLink)
  {
    $this->previousLink = $previousLink;
  }
  /**
   * @return string
   */
  public function getPreviousLink()
  {
    return $this->previousLink;
  }
  /**
   * The starting index of the entries, which is 1 by default or otherwise
   * specified by the start-index query parameter.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * The total number of results for the query, regardless of the number of
   * results in the response.
   *
   * @param int $totalResults
   */
  public function setTotalResults($totalResults)
  {
    $this->totalResults = $totalResults;
  }
  /**
   * @return int
   */
  public function getTotalResults()
  {
    return $this->totalResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntityAdWordsLinks::class, 'Google_Service_Analytics_EntityAdWordsLinks');
