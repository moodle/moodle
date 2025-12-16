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

class GoogleCloudRetailV2CatalogAttributeFacetConfigIgnoredFacetValues extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * If start time is empty and end time is not empty, then ignore these facet
   * values before end time.
   *
   * @var string
   */
  public $endTime;
  /**
   * Time range for the current list of facet values to ignore. If multiple time
   * ranges are specified for an facet value for the current attribute, consider
   * all of them. If both are empty, ignore always. If start time and end time
   * are set, then start time must be before end time. If start time is not
   * empty and end time is empty, then will ignore these facet values after the
   * start time.
   *
   * @var string
   */
  public $startTime;
  /**
   * List of facet values to ignore for the following time range. The facet
   * values are the same as the attribute values. There is a limit of 10 values
   * per instance of IgnoredFacetValues. Each value can have at most 128
   * characters.
   *
   * @var string[]
   */
  public $values;

  /**
   * If start time is empty and end time is not empty, then ignore these facet
   * values before end time.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Time range for the current list of facet values to ignore. If multiple time
   * ranges are specified for an facet value for the current attribute, consider
   * all of them. If both are empty, ignore always. If start time and end time
   * are set, then start time must be before end time. If start time is not
   * empty and end time is empty, then will ignore these facet values after the
   * start time.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * List of facet values to ignore for the following time range. The facet
   * values are the same as the attribute values. There is a limit of 10 values
   * per instance of IgnoredFacetValues. Each value can have at most 128
   * characters.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2CatalogAttributeFacetConfigIgnoredFacetValues::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CatalogAttributeFacetConfigIgnoredFacetValues');
