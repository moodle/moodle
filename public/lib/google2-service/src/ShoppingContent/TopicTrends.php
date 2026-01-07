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

class TopicTrends extends \Google\Model
{
  /**
   * Country trends are calculated for. Must be a two-letter country code (ISO
   * 3166-1-alpha-2 code), for example, `“US”`.
   *
   * @var string
   */
  public $customerCountryCode;
  protected $dateType = Date::class;
  protected $dateDataType = '';
  /**
   * Search interest in the last 120 days, with the same normalization as
   * search_interest. This field is only present for a past date.
   *
   * @var 
   */
  public $last120DaysSearchInterest;
  /**
   * Search interest in the last 30 days, with the same normalization as
   * search_interest. This field is only present for a past date.
   *
   * @var 
   */
  public $last30DaysSearchInterest;
  /**
   * Search interest in the last 7 days, with the same normalization as
   * search_interest. This field is only present for a past date.
   *
   * @var 
   */
  public $last7DaysSearchInterest;
  /**
   * Search interest in the last 90 days, with the same normalization as
   * search_interest. This field is only present for a past date.
   *
   * @var 
   */
  public $last90DaysSearchInterest;
  /**
   * Estimated search interest in the next 7 days, with the same normalization
   * as search_interest. This field is only present for a future date.
   *
   * @var 
   */
  public $next7DaysSearchInterest;
  /**
   * Daily search interest, normalized to the time and country to make
   * comparisons easier, with 100 representing peak popularity (from 0 to 100)
   * for the requested time period and location.
   *
   * @var 
   */
  public $searchInterest;
  /**
   * Google-provided topic trends are calculated for. Only top eight topics are
   * returned. Topic is what shoppers are searching for on Google, grouped by
   * the same concept.
   *
   * @var string
   */
  public $topic;

  /**
   * Country trends are calculated for. Must be a two-letter country code (ISO
   * 3166-1-alpha-2 code), for example, `“US”`.
   *
   * @param string $customerCountryCode
   */
  public function setCustomerCountryCode($customerCountryCode)
  {
    $this->customerCountryCode = $customerCountryCode;
  }
  /**
   * @return string
   */
  public function getCustomerCountryCode()
  {
    return $this->customerCountryCode;
  }
  /**
   * Date the trend score was retrieved.
   *
   * @param Date $date
   */
  public function setDate(Date $date)
  {
    $this->date = $date;
  }
  /**
   * @return Date
   */
  public function getDate()
  {
    return $this->date;
  }
  public function setLast120DaysSearchInterest($last120DaysSearchInterest)
  {
    $this->last120DaysSearchInterest = $last120DaysSearchInterest;
  }
  public function getLast120DaysSearchInterest()
  {
    return $this->last120DaysSearchInterest;
  }
  public function setLast30DaysSearchInterest($last30DaysSearchInterest)
  {
    $this->last30DaysSearchInterest = $last30DaysSearchInterest;
  }
  public function getLast30DaysSearchInterest()
  {
    return $this->last30DaysSearchInterest;
  }
  public function setLast7DaysSearchInterest($last7DaysSearchInterest)
  {
    $this->last7DaysSearchInterest = $last7DaysSearchInterest;
  }
  public function getLast7DaysSearchInterest()
  {
    return $this->last7DaysSearchInterest;
  }
  public function setLast90DaysSearchInterest($last90DaysSearchInterest)
  {
    $this->last90DaysSearchInterest = $last90DaysSearchInterest;
  }
  public function getLast90DaysSearchInterest()
  {
    return $this->last90DaysSearchInterest;
  }
  public function setNext7DaysSearchInterest($next7DaysSearchInterest)
  {
    $this->next7DaysSearchInterest = $next7DaysSearchInterest;
  }
  public function getNext7DaysSearchInterest()
  {
    return $this->next7DaysSearchInterest;
  }
  public function setSearchInterest($searchInterest)
  {
    $this->searchInterest = $searchInterest;
  }
  public function getSearchInterest()
  {
    return $this->searchInterest;
  }
  /**
   * Google-provided topic trends are calculated for. Only top eight topics are
   * returned. Topic is what shoppers are searching for on Google, grouped by
   * the same concept.
   *
   * @param string $topic
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TopicTrends::class, 'Google_Service_ShoppingContent_TopicTrends');
