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

class GoogleCloudAiplatformV1PredictRequestResponseLoggingConfig extends \Google\Model
{
  protected $bigqueryDestinationType = GoogleCloudAiplatformV1BigQueryDestination::class;
  protected $bigqueryDestinationDataType = '';
  /**
   * If logging is enabled or not.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Percentage of requests to be logged, expressed as a fraction in range(0,1].
   *
   * @var 
   */
  public $samplingRate;

  /**
   * BigQuery table for logging. If only given a project, a new dataset will be
   * created with name `logging__` where will be made BigQuery-dataset-name
   * compatible (e.g. most special characters will become underscores). If no
   * table name is given, a new table will be created with name
   * `request_response_logging`
   *
   * @param GoogleCloudAiplatformV1BigQueryDestination $bigqueryDestination
   */
  public function setBigqueryDestination(GoogleCloudAiplatformV1BigQueryDestination $bigqueryDestination)
  {
    $this->bigqueryDestination = $bigqueryDestination;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQueryDestination
   */
  public function getBigqueryDestination()
  {
    return $this->bigqueryDestination;
  }
  /**
   * If logging is enabled or not.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  public function setSamplingRate($samplingRate)
  {
    $this->samplingRate = $samplingRate;
  }
  public function getSamplingRate()
  {
    return $this->samplingRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PredictRequestResponseLoggingConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PredictRequestResponseLoggingConfig');
