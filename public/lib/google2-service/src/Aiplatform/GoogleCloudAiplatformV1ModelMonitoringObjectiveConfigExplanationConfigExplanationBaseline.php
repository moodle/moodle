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

class GoogleCloudAiplatformV1ModelMonitoringObjectiveConfigExplanationConfigExplanationBaseline extends \Google\Model
{
  /**
   * Should not be set.
   */
  public const PREDICTION_FORMAT_PREDICTION_FORMAT_UNSPECIFIED = 'PREDICTION_FORMAT_UNSPECIFIED';
  /**
   * Predictions are in JSONL files.
   */
  public const PREDICTION_FORMAT_JSONL = 'JSONL';
  /**
   * Predictions are in BigQuery.
   */
  public const PREDICTION_FORMAT_BIGQUERY = 'BIGQUERY';
  protected $bigqueryType = GoogleCloudAiplatformV1BigQueryDestination::class;
  protected $bigqueryDataType = '';
  protected $gcsType = GoogleCloudAiplatformV1GcsDestination::class;
  protected $gcsDataType = '';
  /**
   * The storage format of the predictions generated BatchPrediction job.
   *
   * @var string
   */
  public $predictionFormat;

  /**
   * BigQuery location for BatchExplain output.
   *
   * @param GoogleCloudAiplatformV1BigQueryDestination $bigquery
   */
  public function setBigquery(GoogleCloudAiplatformV1BigQueryDestination $bigquery)
  {
    $this->bigquery = $bigquery;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQueryDestination
   */
  public function getBigquery()
  {
    return $this->bigquery;
  }
  /**
   * Cloud Storage location for BatchExplain output.
   *
   * @param GoogleCloudAiplatformV1GcsDestination $gcs
   */
  public function setGcs(GoogleCloudAiplatformV1GcsDestination $gcs)
  {
    $this->gcs = $gcs;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsDestination
   */
  public function getGcs()
  {
    return $this->gcs;
  }
  /**
   * The storage format of the predictions generated BatchPrediction job.
   *
   * Accepted values: PREDICTION_FORMAT_UNSPECIFIED, JSONL, BIGQUERY
   *
   * @param self::PREDICTION_FORMAT_* $predictionFormat
   */
  public function setPredictionFormat($predictionFormat)
  {
    $this->predictionFormat = $predictionFormat;
  }
  /**
   * @return self::PREDICTION_FORMAT_*
   */
  public function getPredictionFormat()
  {
    return $this->predictionFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelMonitoringObjectiveConfigExplanationConfigExplanationBaseline::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelMonitoringObjectiveConfigExplanationConfigExplanationBaseline');
