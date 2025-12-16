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

namespace Google\Service\AnalyticsData;

class Comparison extends \Google\Model
{
  /**
   * A saved comparison identified by the comparison's resource name. For
   * example, 'comparisons/1234'.
   *
   * @var string
   */
  public $comparison;
  protected $dimensionFilterType = FilterExpression::class;
  protected $dimensionFilterDataType = '';
  /**
   * Each comparison produces separate rows in the response. In the response,
   * this comparison is identified by this name. If name is unspecified, we will
   * use the saved comparisons display name.
   *
   * @var string
   */
  public $name;

  /**
   * A saved comparison identified by the comparison's resource name. For
   * example, 'comparisons/1234'.
   *
   * @param string $comparison
   */
  public function setComparison($comparison)
  {
    $this->comparison = $comparison;
  }
  /**
   * @return string
   */
  public function getComparison()
  {
    return $this->comparison;
  }
  /**
   * A basic comparison.
   *
   * @param FilterExpression $dimensionFilter
   */
  public function setDimensionFilter(FilterExpression $dimensionFilter)
  {
    $this->dimensionFilter = $dimensionFilter;
  }
  /**
   * @return FilterExpression
   */
  public function getDimensionFilter()
  {
    return $this->dimensionFilter;
  }
  /**
   * Each comparison produces separate rows in the response. In the response,
   * this comparison is identified by this name. If name is unspecified, we will
   * use the saved comparisons display name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Comparison::class, 'Google_Service_AnalyticsData_Comparison');
