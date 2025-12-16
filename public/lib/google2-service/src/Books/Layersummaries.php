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

class Layersummaries extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $itemsType = Layersummary::class;
  protected $itemsDataType = 'array';
  /**
   * Resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * The total number of layer summaries found.
   *
   * @var int
   */
  public $totalItems;

  /**
   * A list of layer summary items.
   *
   * @param Layersummary[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Layersummary[]
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
   * The total number of layer summaries found.
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
class_alias(Layersummaries::class, 'Google_Service_Books_Layersummaries');
