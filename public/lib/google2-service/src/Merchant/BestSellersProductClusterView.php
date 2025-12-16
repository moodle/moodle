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

class BestSellersProductClusterView extends \Google\Collection
{
  protected $collection_key = 'variantGtins';
  /**
   * @var string
   */
  public $brand;
  /**
   * @var string
   */
  public $brandInventoryStatus;
  /**
   * @var string
   */
  public $categoryL1;
  /**
   * @var string
   */
  public $categoryL2;
  /**
   * @var string
   */
  public $categoryL3;
  /**
   * @var string
   */
  public $categoryL4;
  /**
   * @var string
   */
  public $categoryL5;
  /**
   * @var string
   */
  public $inventoryStatus;
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
   * @var string
   */
  public $title;
  /**
   * @var string[]
   */
  public $variantGtins;

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
  public function setBrandInventoryStatus($brandInventoryStatus)
  {
    $this->brandInventoryStatus = $brandInventoryStatus;
  }
  /**
   * @return string
   */
  public function getBrandInventoryStatus()
  {
    return $this->brandInventoryStatus;
  }
  /**
   * @param string
   */
  public function setCategoryL1($categoryL1)
  {
    $this->categoryL1 = $categoryL1;
  }
  /**
   * @return string
   */
  public function getCategoryL1()
  {
    return $this->categoryL1;
  }
  /**
   * @param string
   */
  public function setCategoryL2($categoryL2)
  {
    $this->categoryL2 = $categoryL2;
  }
  /**
   * @return string
   */
  public function getCategoryL2()
  {
    return $this->categoryL2;
  }
  /**
   * @param string
   */
  public function setCategoryL3($categoryL3)
  {
    $this->categoryL3 = $categoryL3;
  }
  /**
   * @return string
   */
  public function getCategoryL3()
  {
    return $this->categoryL3;
  }
  /**
   * @param string
   */
  public function setCategoryL4($categoryL4)
  {
    $this->categoryL4 = $categoryL4;
  }
  /**
   * @return string
   */
  public function getCategoryL4()
  {
    return $this->categoryL4;
  }
  /**
   * @param string
   */
  public function setCategoryL5($categoryL5)
  {
    $this->categoryL5 = $categoryL5;
  }
  /**
   * @return string
   */
  public function getCategoryL5()
  {
    return $this->categoryL5;
  }
  /**
   * @param string
   */
  public function setInventoryStatus($inventoryStatus)
  {
    $this->inventoryStatus = $inventoryStatus;
  }
  /**
   * @return string
   */
  public function getInventoryStatus()
  {
    return $this->inventoryStatus;
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
  /**
   * @param string
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * @param string[]
   */
  public function setVariantGtins($variantGtins)
  {
    $this->variantGtins = $variantGtins;
  }
  /**
   * @return string[]
   */
  public function getVariantGtins()
  {
    return $this->variantGtins;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BestSellersProductClusterView::class, 'Google_Service_Merchant_BestSellersProductClusterView');
