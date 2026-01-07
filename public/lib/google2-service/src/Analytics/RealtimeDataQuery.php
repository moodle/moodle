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

class RealtimeDataQuery extends \Google\Collection
{
  protected $collection_key = 'sort';
  protected $internal_gapi_mappings = [
        "maxResults" => "max-results",
  ];
  /**
   * List of real time dimensions.
   *
   * @var string
   */
  public $dimensions;
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
   * List of real time metrics.
   *
   * @var string[]
   */
  public $metrics;
  /**
   * List of dimensions or metrics based on which real time data is sorted.
   *
   * @var string[]
   */
  public $sort;

  /**
   * List of real time dimensions.
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
   * List of real time metrics.
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
   * List of dimensions or metrics based on which real time data is sorted.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RealtimeDataQuery::class, 'Google_Service_Analytics_RealtimeDataQuery');
