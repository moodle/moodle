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

namespace Google\Service\Dfareporting;

class FloodlightReportCompatibleFields extends \Google\Collection
{
  protected $collection_key = 'metrics';
  protected $dimensionFiltersType = Dimension::class;
  protected $dimensionFiltersDataType = 'array';
  protected $dimensionsType = Dimension::class;
  protected $dimensionsDataType = 'array';
  /**
   * The kind of resource this is, in this case
   * dfareporting#floodlightReportCompatibleFields.
   *
   * @var string
   */
  public $kind;
  protected $metricsType = Metric::class;
  protected $metricsDataType = 'array';

  /**
   * Dimensions which are compatible to be selected in the "dimensionFilters"
   * section of the report.
   *
   * @param Dimension[] $dimensionFilters
   */
  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }
  /**
   * @return Dimension[]
   */
  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }
  /**
   * Dimensions which are compatible to be selected in the "dimensions" section
   * of the report.
   *
   * @param Dimension[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return Dimension[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * The kind of resource this is, in this case
   * dfareporting#floodlightReportCompatibleFields.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Metrics which are compatible to be selected in the "metricNames" section of
   * the report.
   *
   * @param Metric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return Metric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FloodlightReportCompatibleFields::class, 'Google_Service_Dfareporting_FloodlightReportCompatibleFields');
