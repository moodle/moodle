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

class BestSellersBrandView extends \Google\Model
{
  /**
   * @var string
   */
  public $brand;
  /**
   * @var string
   */
  public $previousRank;
  /**
   * @var string
   */
  public $previousRelativeDemand;
  /**
   * @var string
   */
  public $rank;
  /**
   * @var string
   */
  public $relativeDemand;
  /**
   * @var string
   */
  public $relativeDemandChange;
  /**
   * @var string
   */
  public $reportCategoryId;
  /**
   * @var string
   */
  public $reportCountryCode;
  protected $reportDateType = Date::class;
  protected $reportDateDataType = '';
  /**
   * @var string
   */
  public $reportGranularity;

  /**
   * @param string
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * @param string
   */
  public function setPreviousRank($previousRank)
  {
    $this->previousRank = $previousRank;
  }
  /**
   * @return string
   */
  public function getPreviousRank()
  {
    return $this->previousRank;
  }
  /**
   * @param string
   */
  public function setPreviousRelativeDemand($previousRelativeDemand)
  {
    $this->previousRelativeDemand = $previousRelativeDemand;
  }
  /**
   * @return string
   */
  public function getPreviousRelativeDemand()
  {
    return $this->previousRelativeDemand;
  }
  /**
   * @param string
   */
  public function setRank($rank)
  {
    $this->rank = $rank;
  }
  /**
   * @return string
   */
  public function getRank()
  {
    return $this->rank;
  }
  /**
   * @param string
   */
  public function setRelativeDemand($relativeDemand)
  {
    $this->relativeDemand = $relativeDemand;
  }
  /**
   * @return string
   */
  public function getRelativeDemand()
  {
    return $this->relativeDemand;
  }
  /**
   * @param string
   */
  public function setRelativeDemandChange($relativeDemandChange)
  {
    $this->relativeDemandChange = $relativeDemandChange;
  }
  /**
   * @return string
   */
  public function getRelativeDemandChange()
  {
    return $this->relativeDemandChange;
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
   * @param Date
   */
  public function setReportDate(Date $reportDate)
  {
    $this->reportDate = $reportDate;
  }
  /**
   * @return Date
   */
  public function getReportDate()
  {
    return $this->reportDate;
  }
  /**
   * @param string
   */
  public function setReportGranularity($reportGranularity)
  {
    $this->reportGranularity = $reportGranularity;
  }
  /**
   * @return string
   */
  public function getReportGranularity()
  {
    return $this->reportGranularity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BestSellersBrandView::class, 'Google_Service_Merchant_BestSellersBrandView');
