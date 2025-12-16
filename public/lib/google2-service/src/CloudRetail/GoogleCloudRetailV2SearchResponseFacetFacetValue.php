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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2SearchResponseFacetFacetValue extends \Google\Model
{
  /**
   * Number of items that have this facet value.
   *
   * @var string
   */
  public $count;
  protected $intervalType = GoogleCloudRetailV2Interval::class;
  protected $intervalDataType = '';
  /**
   * The maximum value in the FacetValue.interval. Only supported on numerical
   * facets and returned if SearchRequest.FacetSpec.FacetKey.return_min_max is
   * true.
   *
   * @var 
   */
  public $maxValue;
  /**
   * The minimum value in the FacetValue.interval. Only supported on numerical
   * facets and returned if SearchRequest.FacetSpec.FacetKey.return_min_max is
   * true.
   *
   * @var 
   */
  public $minValue;
  /**
   * Text value of a facet, such as "Black" for facet "colorFamilies".
   *
   * @var string
   */
  public $value;

  /**
   * Number of items that have this facet value.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Interval value for a facet, such as [10, 20) for facet "price".
   *
   * @param GoogleCloudRetailV2Interval $interval
   */
  public function setInterval(GoogleCloudRetailV2Interval $interval)
  {
    $this->interval = $interval;
  }
  /**
   * @return GoogleCloudRetailV2Interval
   */
  public function getInterval()
  {
    return $this->interval;
  }
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  public function getMinValue()
  {
    return $this->minValue;
  }
  /**
   * Text value of a facet, such as "Black" for facet "colorFamilies".
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchResponseFacetFacetValue::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchResponseFacetFacetValue');
