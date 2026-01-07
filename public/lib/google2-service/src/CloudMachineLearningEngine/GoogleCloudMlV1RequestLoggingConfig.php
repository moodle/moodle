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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1RequestLoggingConfig extends \Google\Model
{
  /**
   * Required. Fully qualified BigQuery table name in the following format: "
   * project_id.dataset_name.table_name" The specified table must already exist,
   * and the "Cloud ML Service Agent" for your project must have permission to
   * write to it. The table must have the following
   * [schema](/bigquery/docs/schemas): Field name Type Mode model STRING
   * REQUIRED model_version STRING REQUIRED time TIMESTAMP REQUIRED raw_data
   * STRING REQUIRED raw_prediction STRING NULLABLE groundtruth STRING NULLABLE
   *
   * @var string
   */
  public $bigqueryTableName;
  /**
   * Percentage of requests to be logged, expressed as a fraction from 0 to 1.
   * For example, if you want to log 10% of requests, enter `0.1`. The sampling
   * window is the lifetime of the model version. Defaults to 0.
   *
   * @var 
   */
  public $samplingPercentage;

  /**
   * Required. Fully qualified BigQuery table name in the following format: "
   * project_id.dataset_name.table_name" The specified table must already exist,
   * and the "Cloud ML Service Agent" for your project must have permission to
   * write to it. The table must have the following
   * [schema](/bigquery/docs/schemas): Field name Type Mode model STRING
   * REQUIRED model_version STRING REQUIRED time TIMESTAMP REQUIRED raw_data
   * STRING REQUIRED raw_prediction STRING NULLABLE groundtruth STRING NULLABLE
   *
   * @param string $bigqueryTableName
   */
  public function setBigqueryTableName($bigqueryTableName)
  {
    $this->bigqueryTableName = $bigqueryTableName;
  }
  /**
   * @return string
   */
  public function getBigqueryTableName()
  {
    return $this->bigqueryTableName;
  }
  public function setSamplingPercentage($samplingPercentage)
  {
    $this->samplingPercentage = $samplingPercentage;
  }
  public function getSamplingPercentage()
  {
    return $this->samplingPercentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1RequestLoggingConfig::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1RequestLoggingConfig');
