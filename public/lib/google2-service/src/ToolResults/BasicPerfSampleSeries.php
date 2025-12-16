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

namespace Google\Service\ToolResults;

class BasicPerfSampleSeries extends \Google\Model
{
  public const PERF_METRIC_TYPE_perfMetricTypeUnspecified = 'perfMetricTypeUnspecified';
  public const PERF_METRIC_TYPE_memory = 'memory';
  public const PERF_METRIC_TYPE_cpu = 'cpu';
  public const PERF_METRIC_TYPE_network = 'network';
  public const PERF_METRIC_TYPE_graphics = 'graphics';
  public const PERF_UNIT_perfUnitUnspecified = 'perfUnitUnspecified';
  public const PERF_UNIT_kibibyte = 'kibibyte';
  public const PERF_UNIT_percent = 'percent';
  public const PERF_UNIT_bytesPerSecond = 'bytesPerSecond';
  public const PERF_UNIT_framesPerSecond = 'framesPerSecond';
  public const PERF_UNIT_byte = 'byte';
  public const SAMPLE_SERIES_LABEL_sampleSeriesTypeUnspecified = 'sampleSeriesTypeUnspecified';
  /**
   * Memory sample series
   */
  public const SAMPLE_SERIES_LABEL_memoryRssPrivate = 'memoryRssPrivate';
  public const SAMPLE_SERIES_LABEL_memoryRssShared = 'memoryRssShared';
  public const SAMPLE_SERIES_LABEL_memoryRssTotal = 'memoryRssTotal';
  public const SAMPLE_SERIES_LABEL_memoryTotal = 'memoryTotal';
  /**
   * CPU sample series
   */
  public const SAMPLE_SERIES_LABEL_cpuUser = 'cpuUser';
  public const SAMPLE_SERIES_LABEL_cpuKernel = 'cpuKernel';
  public const SAMPLE_SERIES_LABEL_cpuTotal = 'cpuTotal';
  /**
   * Network sample series
   */
  public const SAMPLE_SERIES_LABEL_ntBytesTransferred = 'ntBytesTransferred';
  public const SAMPLE_SERIES_LABEL_ntBytesReceived = 'ntBytesReceived';
  public const SAMPLE_SERIES_LABEL_networkSent = 'networkSent';
  public const SAMPLE_SERIES_LABEL_networkReceived = 'networkReceived';
  /**
   * Graphics sample series
   */
  public const SAMPLE_SERIES_LABEL_graphicsFrameRate = 'graphicsFrameRate';
  /**
   * @var string
   */
  public $perfMetricType;
  /**
   * @var string
   */
  public $perfUnit;
  /**
   * @var string
   */
  public $sampleSeriesLabel;

  /**
   * @param self::PERF_METRIC_TYPE_* $perfMetricType
   */
  public function setPerfMetricType($perfMetricType)
  {
    $this->perfMetricType = $perfMetricType;
  }
  /**
   * @return self::PERF_METRIC_TYPE_*
   */
  public function getPerfMetricType()
  {
    return $this->perfMetricType;
  }
  /**
   * @param self::PERF_UNIT_* $perfUnit
   */
  public function setPerfUnit($perfUnit)
  {
    $this->perfUnit = $perfUnit;
  }
  /**
   * @return self::PERF_UNIT_*
   */
  public function getPerfUnit()
  {
    return $this->perfUnit;
  }
  /**
   * @param self::SAMPLE_SERIES_LABEL_* $sampleSeriesLabel
   */
  public function setSampleSeriesLabel($sampleSeriesLabel)
  {
    $this->sampleSeriesLabel = $sampleSeriesLabel;
  }
  /**
   * @return self::SAMPLE_SERIES_LABEL_*
   */
  public function getSampleSeriesLabel()
  {
    return $this->sampleSeriesLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicPerfSampleSeries::class, 'Google_Service_ToolResults_BasicPerfSampleSeries');
