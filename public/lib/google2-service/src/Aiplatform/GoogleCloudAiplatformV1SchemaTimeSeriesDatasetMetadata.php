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

class GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadata extends \Google\Model
{
  protected $inputConfigType = GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadataInputConfig::class;
  protected $inputConfigDataType = '';
  /**
   * The column name of the time column that identifies time order in the time
   * series.
   *
   * @var string
   */
  public $timeColumn;
  /**
   * The column name of the time series identifier column that identifies the
   * time series.
   *
   * @var string
   */
  public $timeSeriesIdentifierColumn;

  /**
   * @param GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadataInputConfig $inputConfig
   */
  public function setInputConfig(GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadataInputConfig $inputConfig)
  {
    $this->inputConfig = $inputConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadataInputConfig
   */
  public function getInputConfig()
  {
    return $this->inputConfig;
  }
  /**
   * The column name of the time column that identifies time order in the time
   * series.
   *
   * @param string $timeColumn
   */
  public function setTimeColumn($timeColumn)
  {
    $this->timeColumn = $timeColumn;
  }
  /**
   * @return string
   */
  public function getTimeColumn()
  {
    return $this->timeColumn;
  }
  /**
   * The column name of the time series identifier column that identifies the
   * time series.
   *
   * @param string $timeSeriesIdentifierColumn
   */
  public function setTimeSeriesIdentifierColumn($timeSeriesIdentifierColumn)
  {
    $this->timeSeriesIdentifierColumn = $timeSeriesIdentifierColumn;
  }
  /**
   * @return string
   */
  public function getTimeSeriesIdentifierColumn()
  {
    return $this->timeSeriesIdentifierColumn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadata');
