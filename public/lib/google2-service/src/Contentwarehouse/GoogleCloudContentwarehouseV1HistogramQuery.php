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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1HistogramQuery extends \Google\Model
{
  protected $filtersType = GoogleCloudContentwarehouseV1HistogramQueryPropertyNameFilter::class;
  protected $filtersDataType = '';
  /**
   * An expression specifies a histogram request against matching documents for
   * searches. See SearchDocumentsRequest.histogram_queries for details about
   * syntax.
   *
   * @var string
   */
  public $histogramQuery;
  /**
   * Controls if the histogram query requires the return of a precise count.
   * Enable this flag may adversely impact performance. Defaults to true.
   *
   * @var bool
   */
  public $requirePreciseResultSize;

  /**
   * Optional. Filter the result of histogram query by the property names. It
   * only works with histogram query count('FilterableProperties'). It is an
   * optional. It will perform histogram on all the property names for all the
   * document schemas. Setting this field will have a better performance.
   *
   * @param GoogleCloudContentwarehouseV1HistogramQueryPropertyNameFilter $filters
   */
  public function setFilters(GoogleCloudContentwarehouseV1HistogramQueryPropertyNameFilter $filters)
  {
    $this->filters = $filters;
  }
  /**
   * @return GoogleCloudContentwarehouseV1HistogramQueryPropertyNameFilter
   */
  public function getFilters()
  {
    return $this->filters;
  }
  /**
   * An expression specifies a histogram request against matching documents for
   * searches. See SearchDocumentsRequest.histogram_queries for details about
   * syntax.
   *
   * @param string $histogramQuery
   */
  public function setHistogramQuery($histogramQuery)
  {
    $this->histogramQuery = $histogramQuery;
  }
  /**
   * @return string
   */
  public function getHistogramQuery()
  {
    return $this->histogramQuery;
  }
  /**
   * Controls if the histogram query requires the return of a precise count.
   * Enable this flag may adversely impact performance. Defaults to true.
   *
   * @param bool $requirePreciseResultSize
   */
  public function setRequirePreciseResultSize($requirePreciseResultSize)
  {
    $this->requirePreciseResultSize = $requirePreciseResultSize;
  }
  /**
   * @return bool
   */
  public function getRequirePreciseResultSize()
  {
    return $this->requirePreciseResultSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1HistogramQuery::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1HistogramQuery');
