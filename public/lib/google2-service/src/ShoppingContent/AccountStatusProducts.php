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

class AccountStatusProducts extends \Google\Collection
{
  protected $collection_key = 'itemLevelIssues';
  /**
   * The channel the data applies to. Acceptable values are: - "`local`" -
   * "`online`"
   *
   * @var string
   */
  public $channel;
  /**
   * The country the data applies to.
   *
   * @var string
   */
  public $country;
  /**
   * The destination the data applies to.
   *
   * @var string
   */
  public $destination;
  protected $itemLevelIssuesType = AccountStatusItemLevelIssue::class;
  protected $itemLevelIssuesDataType = 'array';
  protected $statisticsType = AccountStatusStatistics::class;
  protected $statisticsDataType = '';

  /**
   * The channel the data applies to. Acceptable values are: - "`local`" -
   * "`online`"
   *
   * @param string $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return string
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * The country the data applies to.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The destination the data applies to.
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * List of item-level issues.
   *
   * @param AccountStatusItemLevelIssue[] $itemLevelIssues
   */
  public function setItemLevelIssues($itemLevelIssues)
  {
    $this->itemLevelIssues = $itemLevelIssues;
  }
  /**
   * @return AccountStatusItemLevelIssue[]
   */
  public function getItemLevelIssues()
  {
    return $this->itemLevelIssues;
  }
  /**
   * Aggregated product statistics.
   *
   * @param AccountStatusStatistics $statistics
   */
  public function setStatistics(AccountStatusStatistics $statistics)
  {
    $this->statistics = $statistics;
  }
  /**
   * @return AccountStatusStatistics
   */
  public function getStatistics()
  {
    return $this->statistics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountStatusProducts::class, 'Google_Service_ShoppingContent_AccountStatusProducts');
