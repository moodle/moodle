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

class SamplingMetadata extends \Google\Model
{
  /**
   * The total number of events read in this sampled report for a date range.
   * This is the size of the subset this property's data that was analyzed in
   * this report.
   *
   * @var string
   */
  public $samplesReadCount;
  /**
   * The total number of events present in this property's data that could have
   * been analyzed in this report for a date range. Sampling uncovers the
   * meaningful information about the larger data set, and this is the size of
   * the larger data set. To calculate the percentage of available data that was
   * used in this report, compute `samplesReadCount/samplingSpaceSize`.
   *
   * @var string
   */
  public $samplingSpaceSize;

  /**
   * The total number of events read in this sampled report for a date range.
   * This is the size of the subset this property's data that was analyzed in
   * this report.
   *
   * @param string $samplesReadCount
   */
  public function setSamplesReadCount($samplesReadCount)
  {
    $this->samplesReadCount = $samplesReadCount;
  }
  /**
   * @return string
   */
  public function getSamplesReadCount()
  {
    return $this->samplesReadCount;
  }
  /**
   * The total number of events present in this property's data that could have
   * been analyzed in this report for a date range. Sampling uncovers the
   * meaningful information about the larger data set, and this is the size of
   * the larger data set. To calculate the percentage of available data that was
   * used in this report, compute `samplesReadCount/samplingSpaceSize`.
   *
   * @param string $samplingSpaceSize
   */
  public function setSamplingSpaceSize($samplingSpaceSize)
  {
    $this->samplingSpaceSize = $samplingSpaceSize;
  }
  /**
   * @return string
   */
  public function getSamplingSpaceSize()
  {
    return $this->samplingSpaceSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SamplingMetadata::class, 'Google_Service_AnalyticsData_SamplingMetadata');
