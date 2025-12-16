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

class GoogleCloudAiplatformV1DirectRawPredictRequest extends \Google\Model
{
  /**
   * The prediction input.
   *
   * @var string
   */
  public $input;
  /**
   * Fully qualified name of the API method being invoked to perform
   * predictions. Format: `/namespace.Service/Method/` Example:
   * `/tensorflow.serving.PredictionService/Predict`
   *
   * @var string
   */
  public $methodName;

  /**
   * The prediction input.
   *
   * @param string $input
   */
  public function setInput($input)
  {
    $this->input = $input;
  }
  /**
   * @return string
   */
  public function getInput()
  {
    return $this->input;
  }
  /**
   * Fully qualified name of the API method being invoked to perform
   * predictions. Format: `/namespace.Service/Method/` Example:
   * `/tensorflow.serving.PredictionService/Predict`
   *
   * @param string $methodName
   */
  public function setMethodName($methodName)
  {
    $this->methodName = $methodName;
  }
  /**
   * @return string
   */
  public function getMethodName()
  {
    return $this->methodName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DirectRawPredictRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DirectRawPredictRequest');
