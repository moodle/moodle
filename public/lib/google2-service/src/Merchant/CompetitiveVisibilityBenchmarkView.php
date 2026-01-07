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

class CompetitiveVisibilityBenchmarkView extends \Google\Model
{
  public $categoryBenchmarkVisibilityTrend;
  protected $dateType = Date::class;
  protected $dateDataType = '';
  /**
   * @var string
   */
  public $reportCategoryId;
  /**
   * @var string
   */
  public $reportCountryCode;
  /**
   * @var string
   */
  public $trafficSource;
  public $yourDomainVisibilityTrend;

  public function setCategoryBenchmarkVisibilityTrend($categoryBenchmarkVisibilityTrend)
  {
    $this->categoryBenchmarkVisibilityTrend = $categoryBenchmarkVisibilityTrend;
  }
  public function getCategoryBenchmarkVisibilityTrend()
  {
    return $this->categoryBenchmarkVisibilityTrend;
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
  public function setReportCategoryId($reportCategoryId)
  {
    $this->reportCategoryId = $reportCategoryId;
  }
  /**
   * @return string
   */
  public function getReportCategoryId()
  {
    return $this->reportCategoryId;
  }
  /**
   * @param string
   */
  public function setReportCountryCode($reportCountryCode)
  {
    $this->reportCountryCode = $reportCountryCode;
  }
  /**
   * @return string
   */
  public function getReportCountryCode()
  {
    return $this->reportCountryCode;
  }
  /**
   * @param string
   */
  public function setTrafficSource($trafficSource)
  {
    $this->trafficSource = $trafficSource;
  }
  /**
   * @return string
   */
  public function getTrafficSource()
  {
    return $this->trafficSource;
  }
  public function setYourDomainVisibilityTrend($yourDomainVisibilityTrend)
  {
    $this->yourDomainVisibilityTrend = $yourDomainVisibilityTrend;
  }
  public function getYourDomainVisibilityTrend()
  {
    return $this->yourDomainVisibilityTrend;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompetitiveVisibilityBenchmarkView::class, 'Google_Service_Merchant_CompetitiveVisibilityBenchmarkView');
