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

namespace Google\Service\AreaInsights;

class Filter extends \Google\Collection
{
  protected $collection_key = 'priceLevels';
  protected $locationFilterType = LocationFilter::class;
  protected $locationFilterDataType = '';
  /**
   * Optional. Restricts results to places whose operating status is included on
   * this list. If operating_status is not set, OPERATING_STATUS_OPERATIONAL is
   * used as default.
   *
   * @var string[]
   */
  public $operatingStatus;
  /**
   * Optional. Restricts results to places whose price level is included on this
   * list. If `price_levels` is not set, all price levels are included in the
   * results.
   *
   * @var string[]
   */
  public $priceLevels;
  protected $ratingFilterType = RatingFilter::class;
  protected $ratingFilterDataType = '';
  protected $typeFilterType = TypeFilter::class;
  protected $typeFilterDataType = '';

  /**
   * Required. Restricts results to places which are located in the area
   * specified by location filters.
   *
   * @param LocationFilter $locationFilter
   */
  public function setLocationFilter(LocationFilter $locationFilter)
  {
    $this->locationFilter = $locationFilter;
  }
  /**
   * @return LocationFilter
   */
  public function getLocationFilter()
  {
    return $this->locationFilter;
  }
  /**
   * Optional. Restricts results to places whose operating status is included on
   * this list. If operating_status is not set, OPERATING_STATUS_OPERATIONAL is
   * used as default.
   *
   * @param string[] $operatingStatus
   */
  public function setOperatingStatus($operatingStatus)
  {
    $this->operatingStatus = $operatingStatus;
  }
  /**
   * @return string[]
   */
  public function getOperatingStatus()
  {
    return $this->operatingStatus;
  }
  /**
   * Optional. Restricts results to places whose price level is included on this
   * list. If `price_levels` is not set, all price levels are included in the
   * results.
   *
   * @param string[] $priceLevels
   */
  public function setPriceLevels($priceLevels)
  {
    $this->priceLevels = $priceLevels;
  }
  /**
   * @return string[]
   */
  public function getPriceLevels()
  {
    return $this->priceLevels;
  }
  /**
   * Optional. Restricts results to places whose average user ratings are in the
   * range specified by rating_filter. If rating_filter is not set, all ratings
   * are included in the result.
   *
   * @param RatingFilter $ratingFilter
   */
  public function setRatingFilter(RatingFilter $ratingFilter)
  {
    $this->ratingFilter = $ratingFilter;
  }
  /**
   * @return RatingFilter
   */
  public function getRatingFilter()
  {
    return $this->ratingFilter;
  }
  /**
   * Required. Place type filters.
   *
   * @param TypeFilter $typeFilter
   */
  public function setTypeFilter(TypeFilter $typeFilter)
  {
    $this->typeFilter = $typeFilter;
  }
  /**
   * @return TypeFilter
   */
  public function getTypeFilter()
  {
    return $this->typeFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Filter::class, 'Google_Service_AreaInsights_Filter');
