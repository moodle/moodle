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

namespace Google\Service\Merchant;

class NonProductPerformanceView extends \Google\Model
{
  public $clickThroughRate;
  /**
   * @var string
   */
  public $clicks;
  protected $dateType = Date::class;
  protected $dateDataType = '';
  /**
   * @var string
   */
  public $impressions;
  protected $weekType = Date::class;
  protected $weekDataType = '';

  public function setClickThroughRate($clickThroughRate)
  {
    $this->clickThroughRate = $clickThroughRate;
  }
  public function getClickThroughRate()
  {
    return $this->clickThroughRate;
  }
  /**
   * @param string
   */
  public function setClicks($clicks)
  {
    $this->clicks = $clicks;
  }
  /**
   * @return string
   */
  public function getClicks()
  {
    return $this->clicks;
  }
  /**
   * @param Date
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
  /**
   * @param string
   */
  public function setImpressions($impressions)
  {
    $this->impressions = $impressions;
  }
  /**
   * @return string
   */
  public function getImpressions()
  {
    return $this->impressions;
  }
  /**
   * @param Date
   */
  public function setWeek(Date $week)
  {
    $this->week = $week;
  }
  /**
   * @return Date
   */
  public function getWeek()
  {
    return $this->week;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NonProductPerformanceView::class, 'Google_Service_Merchant_NonProductPerformanceView');
