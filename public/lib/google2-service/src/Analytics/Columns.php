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

class Columns extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * List of attributes names returned by columns.
   *
   * @var string[]
   */
  public $attributeNames;
  /**
   * Etag of collection. This etag can be compared with the last response etag
   * to check if response has changed.
   *
   * @var string
   */
  public $etag;
  protected $itemsType = Column::class;
  protected $itemsDataType = 'array';
  /**
   * Collection type.
   *
   * @var string
   */
  public $kind;
  /**
   * Total number of columns returned in the response.
   *
   * @var int
   */
  public $totalResults;

  /**
   * List of attributes names returned by columns.
   *
   * @param string[] $attributeNames
   */
  public function setAttributeNames($attributeNames)
  {
    $this->attributeNames = $attributeNames;
  }
  /**
   * @return string[]
   */
  public function getAttributeNames()
  {
    return $this->attributeNames;
  }
  /**
   * Etag of collection. This etag can be compared with the last response etag
   * to check if response has changed.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * List of columns for a report type.
   *
   * @param Column[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Column[]
   */
  public function getItems()
  {
    return $this->items;
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
   * Total number of columns returned in the response.
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
class_alias(Columns::class, 'Google_Service_Analytics_Columns');
