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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1TraceConfig extends \Google\Model
{
  /**
   * Exporter unspecified
   */
  public const EXPORTER_EXPORTER_UNSPECIFIED = 'EXPORTER_UNSPECIFIED';
  /**
   * Jaeger exporter
   */
  public const EXPORTER_JAEGER = 'JAEGER';
  /**
   * Cloudtrace exporter
   */
  public const EXPORTER_CLOUD_TRACE = 'CLOUD_TRACE';
  /**
   * Required. Endpoint of the exporter.
   *
   * @var string
   */
  public $endpoint;
  /**
   * Required. Exporter that is used to view the distributed trace captured
   * using OpenCensus. An exporter sends traces to any backend that is capable
   * of consuming them. Recorded spans can be exported by registered exporters.
   *
   * @var string
   */
  public $exporter;
  protected $samplingConfigType = GoogleCloudApigeeV1TraceSamplingConfig::class;
  protected $samplingConfigDataType = '';

  /**
   * Required. Endpoint of the exporter.
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * Required. Exporter that is used to view the distributed trace captured
   * using OpenCensus. An exporter sends traces to any backend that is capable
   * of consuming them. Recorded spans can be exported by registered exporters.
   *
   * Accepted values: EXPORTER_UNSPECIFIED, JAEGER, CLOUD_TRACE
   *
   * @param self::EXPORTER_* $exporter
   */
  public function setExporter($exporter)
  {
    $this->exporter = $exporter;
  }
  /**
   * @return self::EXPORTER_*
   */
  public function getExporter()
  {
    return $this->exporter;
  }
  /**
   * Distributed trace configuration for all API proxies in an environment. You
   * can also override the configuration for a specific API proxy using the
   * distributed trace configuration overrides API.
   *
   * @param GoogleCloudApigeeV1TraceSamplingConfig $samplingConfig
   */
  public function setSamplingConfig(GoogleCloudApigeeV1TraceSamplingConfig $samplingConfig)
  {
    $this->samplingConfig = $samplingConfig;
  }
  /**
   * @return GoogleCloudApigeeV1TraceSamplingConfig
   */
  public function getSamplingConfig()
  {
    return $this->samplingConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1TraceConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1TraceConfig');
