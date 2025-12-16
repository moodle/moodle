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

namespace Google\Service\AuthorizedBuyersMarketplace;

class RtbMetrics extends \Google\Model
{
  /**
   * Ad impressions in last 7 days.
   *
   * @var string
   */
  public $adImpressions7Days;
  /**
   * Bid rate in last 7 days, calculated by (bids / bid requests).
   *
   * @var 
   */
  public $bidRate7Days;
  /**
   * Bid requests in last 7 days.
   *
   * @var string
   */
  public $bidRequests7Days;
  /**
   * Bids in last 7 days.
   *
   * @var string
   */
  public $bids7Days;
  /**
   * Filtered bid rate in last 7 days, calculated by (filtered bids / bids).
   *
   * @var 
   */
  public $filteredBidRate7Days;
  /**
   * Must bid rate for current month.
   *
   * @var 
   */
  public $mustBidRateCurrentMonth;

  /**
   * Ad impressions in last 7 days.
   *
   * @param string $adImpressions7Days
   */
  public function setAdImpressions7Days($adImpressions7Days)
  {
    $this->adImpressions7Days = $adImpressions7Days;
  }
  /**
   * @return string
   */
  public function getAdImpressions7Days()
  {
    return $this->adImpressions7Days;
  }
  public function setBidRate7Days($bidRate7Days)
  {
    $this->bidRate7Days = $bidRate7Days;
  }
  public function getBidRate7Days()
  {
    return $this->bidRate7Days;
  }
  /**
   * Bid requests in last 7 days.
   *
   * @param string $bidRequests7Days
   */
  public function setBidRequests7Days($bidRequests7Days)
  {
    $this->bidRequests7Days = $bidRequests7Days;
  }
  /**
   * @return string
   */
  public function getBidRequests7Days()
  {
    return $this->bidRequests7Days;
  }
  /**
   * Bids in last 7 days.
   *
   * @param string $bids7Days
   */
  public function setBids7Days($bids7Days)
  {
    $this->bids7Days = $bids7Days;
  }
  /**
   * @return string
   */
  public function getBids7Days()
  {
    return $this->bids7Days;
  }
  public function setFilteredBidRate7Days($filteredBidRate7Days)
  {
    $this->filteredBidRate7Days = $filteredBidRate7Days;
  }
  public function getFilteredBidRate7Days()
  {
    return $this->filteredBidRate7Days;
  }
  public function setMustBidRateCurrentMonth($mustBidRateCurrentMonth)
  {
    $this->mustBidRateCurrentMonth = $mustBidRateCurrentMonth;
  }
  public function getMustBidRateCurrentMonth()
  {
    return $this->mustBidRateCurrentMonth;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RtbMetrics::class, 'Google_Service_AuthorizedBuyersMarketplace_RtbMetrics');
