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

class CollectionStatus extends \Google\Collection
{
  protected $collection_key = 'destinationStatuses';
  protected $collectionLevelIssusesType = CollectionStatusItemLevelIssue::class;
  protected $collectionLevelIssusesDataType = 'array';
  /**
   * Date on which the collection has been created in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format: Date, time, and
   * offset, for example "2020-01-02T09:00:00+01:00" or "2020-01-02T09:00:00Z"
   *
   * @var string
   */
  public $creationDate;
  protected $destinationStatusesType = CollectionStatusDestinationStatus::class;
  protected $destinationStatusesDataType = 'array';
  /**
   * Required. The ID of the collection for which status is reported.
   *
   * @var string
   */
  public $id;
  /**
   * Date on which the collection has been last updated in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format: Date, time, and
   * offset, for example "2020-01-02T09:00:00+01:00" or "2020-01-02T09:00:00Z"
   *
   * @var string
   */
  public $lastUpdateDate;

  /**
   * A list of all issues associated with the collection.
   *
   * @param CollectionStatusItemLevelIssue[] $collectionLevelIssuses
   */
  public function setCollectionLevelIssuses($collectionLevelIssuses)
  {
    $this->collectionLevelIssuses = $collectionLevelIssuses;
  }
  /**
   * @return CollectionStatusItemLevelIssue[]
   */
  public function getCollectionLevelIssuses()
  {
    return $this->collectionLevelIssuses;
  }
  /**
   * Date on which the collection has been created in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format: Date, time, and
   * offset, for example "2020-01-02T09:00:00+01:00" or "2020-01-02T09:00:00Z"
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
   * The intended destinations for the collection.
   *
   * @param CollectionStatusDestinationStatus[] $destinationStatuses
   */
  public function setDestinationStatuses($destinationStatuses)
  {
    $this->destinationStatuses = $destinationStatuses;
  }
  /**
   * @return CollectionStatusDestinationStatus[]
   */
  public function getDestinationStatuses()
  {
    return $this->destinationStatuses;
  }
  /**
   * Required. The ID of the collection for which status is reported.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Date on which the collection has been last updated in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format: Date, time, and
   * offset, for example "2020-01-02T09:00:00+01:00" or "2020-01-02T09:00:00Z"
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
class_alias(CollectionStatus::class, 'Google_Service_ShoppingContent_CollectionStatus');
