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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1FeatureGroupBigQueryTimeSeries extends \Google\Model
{
  /**
   * Optional. Column hosting timestamp values for a time-series source. Will be
   * used to determine the latest `feature_values` for each entity. Optional. If
   * not provided, column named `feature_timestamp` of type `TIMESTAMP` will be
   * used.
   *
   * @var string
   */
  public $timestampColumn;

  /**
   * Optional. Column hosting timestamp values for a time-series source. Will be
   * used to determine the latest `feature_values` for each entity. Optional. If
   * not provided, column named `feature_timestamp` of type `TIMESTAMP` will be
   * used.
   *
   * @param string $timestampColumn
   */
  public function setTimestampColumn($timestampColumn)
  {
    $this->timestampColumn = $timestampColumn;
  }
  /**
   * @return string
   */
  public function getTimestampColumn()
  {
    return $this->timestampColumn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureGroupBigQueryTimeSeries::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureGroupBigQueryTimeSeries');
