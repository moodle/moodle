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

namespace Google\Service\Css;

class CssProductStatus extends \Google\Collection
{
  protected $collection_key = 'itemLevelIssues';
  /**
   * Date on which the item has been created, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format.
   *
   * @var string
   */
  public $creationDate;
  protected $destinationStatusesType = DestinationStatus::class;
  protected $destinationStatusesDataType = 'array';
  /**
   * Date on which the item expires, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format.
   *
   * @var string
   */
  public $googleExpirationDate;
  protected $itemLevelIssuesType = ItemLevelIssue::class;
  protected $itemLevelIssuesDataType = 'array';
  /**
   * Date on which the item has been last updated, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format.
   *
   * @var string
   */
  public $lastUpdateDate;

  /**
   * Date on which the item has been created, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format.
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
   * @param DestinationStatus[] $destinationStatuses
   */
  public function setDestinationStatuses($destinationStatuses)
  {
    $this->destinationStatuses = $destinationStatuses;
  }
  /**
   * @return DestinationStatus[]
   */
  public function getDestinationStatuses()
  {
    return $this->destinationStatuses;
  }
  /**
   * Date on which the item expires, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format.
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
   * @param ItemLevelIssue[] $itemLevelIssues
   */
  public function setItemLevelIssues($itemLevelIssues)
  {
    $this->itemLevelIssues = $itemLevelIssues;
  }
  /**
   * @return ItemLevelIssue[]
   */
  public function getItemLevelIssues()
  {
    return $this->itemLevelIssues;
  }
  /**
   * Date on which the item has been last updated, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CssProductStatus::class, 'Google_Service_Css_CssProductStatus');
