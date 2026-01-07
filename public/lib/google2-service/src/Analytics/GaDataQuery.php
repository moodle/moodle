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

namespace Google\Service\Analytics;

class GaDataQuery extends \Google\Collection
{
  protected $collection_key = 'sort';
  protected $internal_gapi_mappings = [
        "endDate" => "end-date",
        "maxResults" => "max-results",
        "startDate" => "start-date",
        "startIndex" => "start-index",
  ];
  /**
   * List of analytics dimensions.
   *
   * @var string
   */
  public $dimensions;
  /**
   * End date.
   *
   * @var string
   */
  public $endDate;
  /**
   * Comma-separated list of dimension or metric filters.
   *
   * @var string
   */
  public $filters;
  /**
   * Unique table ID.
   *
   * @var string
   */
  public $ids;
  /**
   * Maximum results per page.
   *
   * @var int
   */
  public $maxResults;
  /**
   * List of analytics metrics.
   *
   * @var string[]
   */
  public $metrics;
  /**
   * Desired sampling level
   *
   * @var string
   */
  public $samplingLevel;
  /**
   * Analytics advanced segment.
   *
   * @var string
   */
  public $segment;
  /**
   * List of dimensions or metrics based on which Analytics data is sorted.
   *
   * @var string[]
   */
  public $sort;
  /**
   * Start date.
   *
   * @var string
   */
  public $startDate;
  /**
   * Start index.
   *
   * @var int
   */
  public $startIndex;

  /**
   * List of analytics dimensions.
   *
   * @param string $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * End date.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Comma-separated list of dimension or metric filters.
   *
   * @param string $filters
   */
  public function setFilters($filters)
  {
    $this->filters = $filters;
  }
  /**
   * @return string
   */
  public function getFilters()
  {
    return $this->filters;
  }
  /**
   * Unique table ID.
   *
   * @param string $ids
   */
  public function setIds($ids)
  {
    $this->ids = $ids;
  }
  /**
   * @return string
   */
  public function getIds()
  {
    return $this->ids;
  }
  /**
   * Maximum results per page.
   *
   * @param int $maxResults
   */
  public function setMaxResults($maxResults)
  {
    $this->maxResults = $maxResults;
  }
  /**
   * @return int
   */
  public function getMaxResults()
  {
    return $this->maxResults;
  }
  /**
   * List of analytics metrics.
   *
   * @param string[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return string[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Desired sampling level
   *
   * @param string $samplingLevel
   */
  public function setSamplingLevel($samplingLevel)
  {
    $this->samplingLevel = $samplingLevel;
  }
  /**
   * @return string
   */
  public function getSamplingLevel()
  {
    return $this->samplingLevel;
  }
  /**
   * Analytics advanced segment.
   *
   * @param string $segment
   */
  public function setSegment($segment)
  {
    $this->segment = $segment;
  }
  /**
   * @return string
   */
  public function getSegment()
  {
    return $this->segment;
  }
  /**
   * List of dimensions or metrics based on which Analytics data is sorted.
   *
   * @param string[] $sort
   */
  public function setSort($sort)
  {
    $this->sort = $sort;
  }
  /**
   * @return string[]
   */
  public function getSort()
  {
    return $this->sort;
  }
  /**
   * Start date.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Start index.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GaDataQuery::class, 'Google_Service_Analytics_GaDataQuery');
