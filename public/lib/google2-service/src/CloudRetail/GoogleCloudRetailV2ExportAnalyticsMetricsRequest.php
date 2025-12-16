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

class GoogleCloudRetailV2ExportAnalyticsMetricsRequest extends \Google\Model
{
  /**
   * A filtering expression to specify restrictions on returned metrics. The
   * expression is a sequence of terms. Each term applies a restriction to the
   * returned metrics. Use this expression to restrict results to a specific
   * time range. Currently we expect only one types of fields: * `timestamp`:
   * This can be specified twice, once with a less than operator and once with a
   * greater than operator. The `timestamp` restriction should result in one,
   * contiguous, valid, `timestamp` range. Some examples of valid filters
   * expressions: * Example 1: `timestamp > "2012-04-23T18:25:43.511Z" timestamp
   * < "2012-04-23T18:30:43.511Z"` * Example 2: `timestamp >
   * "2012-04-23T18:25:43.511Z"`
   *
   * @var string
   */
  public $filter;
  protected $outputConfigType = GoogleCloudRetailV2OutputConfig::class;
  protected $outputConfigDataType = '';

  /**
   * A filtering expression to specify restrictions on returned metrics. The
   * expression is a sequence of terms. Each term applies a restriction to the
   * returned metrics. Use this expression to restrict results to a specific
   * time range. Currently we expect only one types of fields: * `timestamp`:
   * This can be specified twice, once with a less than operator and once with a
   * greater than operator. The `timestamp` restriction should result in one,
   * contiguous, valid, `timestamp` range. Some examples of valid filters
   * expressions: * Example 1: `timestamp > "2012-04-23T18:25:43.511Z" timestamp
   * < "2012-04-23T18:30:43.511Z"` * Example 2: `timestamp >
   * "2012-04-23T18:25:43.511Z"`
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Required. The output location of the data.
   *
   * @param GoogleCloudRetailV2OutputConfig $outputConfig
   */
  public function setOutputConfig(GoogleCloudRetailV2OutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return GoogleCloudRetailV2OutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ExportAnalyticsMetricsRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ExportAnalyticsMetricsRequest');
