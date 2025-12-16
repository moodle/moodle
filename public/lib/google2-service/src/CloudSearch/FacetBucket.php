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

namespace Google\Service\CloudSearch;

class FacetBucket extends \Google\Model
{
  /**
   * Number of results that match the bucket value. Counts are only returned for
   * searches when count accuracy is ensured. Cloud Search does not guarantee
   * facet counts for any query and facet counts might be present only
   * intermittently, even for identical queries. Do not build dependencies on
   * facet count existence; instead use facet ount percentages which are always
   * returned.
   *
   * @var int
   */
  public $count;
  protected $filterType = Filter::class;
  protected $filterDataType = '';
  /**
   * Percent of results that match the bucket value. The returned value is
   * between (0-100], and is rounded down to an integer if fractional. If the
   * value is not explicitly returned, it represents a percentage value that
   * rounds to 0. Percentages are returned for all searches, but are an
   * estimate. Because percentages are always returned, you should render
   * percentages instead of counts.
   *
   * @var int
   */
  public $percentage;
  protected $valueType = Value::class;
  protected $valueDataType = '';

  /**
   * Number of results that match the bucket value. Counts are only returned for
   * searches when count accuracy is ensured. Cloud Search does not guarantee
   * facet counts for any query and facet counts might be present only
   * intermittently, even for identical queries. Do not build dependencies on
   * facet count existence; instead use facet ount percentages which are always
   * returned.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Filter to be passed in the search request if the corresponding bucket is
   * selected.
   *
   * @param Filter $filter
   */
  public function setFilter(Filter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return Filter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Percent of results that match the bucket value. The returned value is
   * between (0-100], and is rounded down to an integer if fractional. If the
   * value is not explicitly returned, it represents a percentage value that
   * rounds to 0. Percentages are returned for all searches, but are an
   * estimate. Because percentages are always returned, you should render
   * percentages instead of counts.
   *
   * @param int $percentage
   */
  public function setPercentage($percentage)
  {
    $this->percentage = $percentage;
  }
  /**
   * @return int
   */
  public function getPercentage()
  {
    return $this->percentage;
  }
  /**
   * @param Value $value
   */
  public function setValue(Value $value)
  {
    $this->value = $value;
  }
  /**
   * @return Value
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FacetBucket::class, 'Google_Service_CloudSearch_FacetBucket');
