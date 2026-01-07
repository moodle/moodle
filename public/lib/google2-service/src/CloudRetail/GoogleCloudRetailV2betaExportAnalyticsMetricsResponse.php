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

class GoogleCloudRetailV2betaExportAnalyticsMetricsResponse extends \Google\Collection
{
  protected $collection_key = 'errorSamples';
  protected $errorSamplesType = GoogleRpcStatus::class;
  protected $errorSamplesDataType = 'array';
  protected $errorsConfigType = GoogleCloudRetailV2betaExportErrorsConfig::class;
  protected $errorsConfigDataType = '';
  protected $outputResultType = GoogleCloudRetailV2betaOutputResult::class;
  protected $outputResultDataType = '';

  /**
   * A sample of errors encountered while processing the request.
   *
   * @param GoogleRpcStatus[] $errorSamples
   */
  public function setErrorSamples($errorSamples)
  {
    $this->errorSamples = $errorSamples;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrorSamples()
  {
    return $this->errorSamples;
  }
  /**
   * This field is never set.
   *
   * @param GoogleCloudRetailV2betaExportErrorsConfig $errorsConfig
   */
  public function setErrorsConfig(GoogleCloudRetailV2betaExportErrorsConfig $errorsConfig)
  {
    $this->errorsConfig = $errorsConfig;
  }
  /**
   * @return GoogleCloudRetailV2betaExportErrorsConfig
   */
  public function getErrorsConfig()
  {
    return $this->errorsConfig;
  }
  /**
   * Output result indicating where the data were exported to.
   *
   * @param GoogleCloudRetailV2betaOutputResult $outputResult
   */
  public function setOutputResult(GoogleCloudRetailV2betaOutputResult $outputResult)
  {
    $this->outputResult = $outputResult;
  }
  /**
   * @return GoogleCloudRetailV2betaOutputResult
   */
  public function getOutputResult()
  {
    return $this->outputResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2betaExportAnalyticsMetricsResponse::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2betaExportAnalyticsMetricsResponse');
