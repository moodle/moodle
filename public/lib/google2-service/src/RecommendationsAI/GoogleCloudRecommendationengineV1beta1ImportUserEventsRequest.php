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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1ImportUserEventsRequest extends \Google\Model
{
  protected $errorsConfigType = GoogleCloudRecommendationengineV1beta1ImportErrorsConfig::class;
  protected $errorsConfigDataType = '';
  protected $inputConfigType = GoogleCloudRecommendationengineV1beta1InputConfig::class;
  protected $inputConfigDataType = '';
  /**
   * Optional. Unique identifier provided by client, within the ancestor dataset
   * scope. Ensures idempotency for expensive long running operations. Server-
   * generated if unspecified. Up to 128 characters long. This is returned as
   * google.longrunning.Operation.name in the response. Note that this field
   * must not be set if the desired input config is catalog_inline_source.
   *
   * @var string
   */
  public $requestId;

  /**
   * Optional. The desired location of errors incurred during the Import.
   *
   * @param GoogleCloudRecommendationengineV1beta1ImportErrorsConfig $errorsConfig
   */
  public function setErrorsConfig(GoogleCloudRecommendationengineV1beta1ImportErrorsConfig $errorsConfig)
  {
    $this->errorsConfig = $errorsConfig;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1ImportErrorsConfig
   */
  public function getErrorsConfig()
  {
    return $this->errorsConfig;
  }
  /**
   * Required. The desired input location of the data.
   *
   * @param GoogleCloudRecommendationengineV1beta1InputConfig $inputConfig
   */
  public function setInputConfig(GoogleCloudRecommendationengineV1beta1InputConfig $inputConfig)
  {
    $this->inputConfig = $inputConfig;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1InputConfig
   */
  public function getInputConfig()
  {
    return $this->inputConfig;
  }
  /**
   * Optional. Unique identifier provided by client, within the ancestor dataset
   * scope. Ensures idempotency for expensive long running operations. Server-
   * generated if unspecified. Up to 128 characters long. This is returned as
   * google.longrunning.Operation.name in the response. Note that this field
   * must not be set if the desired input config is catalog_inline_source.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1ImportUserEventsRequest::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1ImportUserEventsRequest');
