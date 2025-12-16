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

class GoogleCloudAiplatformV1ModelDeploymentMonitoringBigQueryTable extends \Google\Model
{
  /**
   * Unspecified source.
   */
  public const LOG_SOURCE_LOG_SOURCE_UNSPECIFIED = 'LOG_SOURCE_UNSPECIFIED';
  /**
   * Logs coming from Training dataset.
   */
  public const LOG_SOURCE_TRAINING = 'TRAINING';
  /**
   * Logs coming from Serving traffic.
   */
  public const LOG_SOURCE_SERVING = 'SERVING';
  /**
   * Unspecified type.
   */
  public const LOG_TYPE_LOG_TYPE_UNSPECIFIED = 'LOG_TYPE_UNSPECIFIED';
  /**
   * Predict logs.
   */
  public const LOG_TYPE_PREDICT = 'PREDICT';
  /**
   * Explain logs.
   */
  public const LOG_TYPE_EXPLAIN = 'EXPLAIN';
  /**
   * The created BigQuery table to store logs. Customer could do their own query
   * & analysis. Format: `bq://.model_deployment_monitoring_._`
   *
   * @var string
   */
  public $bigqueryTablePath;
  /**
   * The source of log.
   *
   * @var string
   */
  public $logSource;
  /**
   * The type of log.
   *
   * @var string
   */
  public $logType;
  /**
   * Output only. The schema version of the request/response logging BigQuery
   * table. Default to v1 if unset.
   *
   * @var string
   */
  public $requestResponseLoggingSchemaVersion;

  /**
   * The created BigQuery table to store logs. Customer could do their own query
   * & analysis. Format: `bq://.model_deployment_monitoring_._`
   *
   * @param string $bigqueryTablePath
   */
  public function setBigqueryTablePath($bigqueryTablePath)
  {
    $this->bigqueryTablePath = $bigqueryTablePath;
  }
  /**
   * @return string
   */
  public function getBigqueryTablePath()
  {
    return $this->bigqueryTablePath;
  }
  /**
   * The source of log.
   *
   * Accepted values: LOG_SOURCE_UNSPECIFIED, TRAINING, SERVING
   *
   * @param self::LOG_SOURCE_* $logSource
   */
  public function setLogSource($logSource)
  {
    $this->logSource = $logSource;
  }
  /**
   * @return self::LOG_SOURCE_*
   */
  public function getLogSource()
  {
    return $this->logSource;
  }
  /**
   * The type of log.
   *
   * Accepted values: LOG_TYPE_UNSPECIFIED, PREDICT, EXPLAIN
   *
   * @param self::LOG_TYPE_* $logType
   */
  public function setLogType($logType)
  {
    $this->logType = $logType;
  }
  /**
   * @return self::LOG_TYPE_*
   */
  public function getLogType()
  {
    return $this->logType;
  }
  /**
   * Output only. The schema version of the request/response logging BigQuery
   * table. Default to v1 if unset.
   *
   * @param string $requestResponseLoggingSchemaVersion
   */
  public function setRequestResponseLoggingSchemaVersion($requestResponseLoggingSchemaVersion)
  {
    $this->requestResponseLoggingSchemaVersion = $requestResponseLoggingSchemaVersion;
  }
  /**
   * @return string
   */
  public function getRequestResponseLoggingSchemaVersion()
  {
    return $this->requestResponseLoggingSchemaVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelDeploymentMonitoringBigQueryTable::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelDeploymentMonitoringBigQueryTable');
