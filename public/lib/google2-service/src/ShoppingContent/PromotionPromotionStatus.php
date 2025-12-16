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

class PromotionPromotionStatus extends \Google\Collection
{
  protected $collection_key = 'promotionIssue';
  /**
   * Date on which the promotion has been created in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format: Date, time, and
   * offset, for example "2020-01-02T09:00:00+01:00" or "2020-01-02T09:00:00Z"
   *
   * @var string
   */
  public $creationDate;
  protected $destinationStatusesType = PromotionPromotionStatusDestinationStatus::class;
  protected $destinationStatusesDataType = 'array';
  /**
   * Date on which the promotion status has been last updated in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format: Date, time, and
   * offset, for example "2020-01-02T09:00:00+01:00" or "2020-01-02T09:00:00Z"
   *
   * @var string
   */
  public $lastUpdateDate;
  protected $promotionIssueType = PromotionPromotionStatusPromotionIssue::class;
  protected $promotionIssueDataType = 'array';

  /**
   * Date on which the promotion has been created in [ISO
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
   * The intended destinations for the promotion.
   *
   * @param PromotionPromotionStatusDestinationStatus[] $destinationStatuses
   */
  public function setDestinationStatuses($destinationStatuses)
  {
    $this->destinationStatuses = $destinationStatuses;
  }
  /**
   * @return PromotionPromotionStatusDestinationStatus[]
   */
  public function getDestinationStatuses()
  {
    return $this->destinationStatuses;
  }
  /**
   * Date on which the promotion status has been last updated in [ISO
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
  /**
   * A list of issues associated with the promotion.
   *
   * @param PromotionPromotionStatusPromotionIssue[] $promotionIssue
   */
  public function setPromotionIssue($promotionIssue)
  {
    $this->promotionIssue = $promotionIssue;
  }
  /**
   * @return PromotionPromotionStatusPromotionIssue[]
   */
  public function getPromotionIssue()
  {
    return $this->promotionIssue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PromotionPromotionStatus::class, 'Google_Service_ShoppingContent_PromotionPromotionStatus');
