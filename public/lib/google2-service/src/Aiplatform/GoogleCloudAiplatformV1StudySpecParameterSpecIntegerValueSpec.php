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

class GoogleCloudAiplatformV1StudySpecParameterSpecIntegerValueSpec extends \Google\Model
{
  /**
   * A default value for an `INTEGER` parameter that is assumed to be a
   * relatively good starting point. Unset value signals that there is no
   * offered starting point. Currently only supported by the Vertex AI Vizier
   * service. Not supported by HyperparameterTuningJob or TrainingPipeline.
   *
   * @var string
   */
  public $defaultValue;
  /**
   * Required. Inclusive maximum value of the parameter.
   *
   * @var string
   */
  public $maxValue;
  /**
   * Required. Inclusive minimum value of the parameter.
   *
   * @var string
   */
  public $minValue;

  /**
   * A default value for an `INTEGER` parameter that is assumed to be a
   * relatively good starting point. Unset value signals that there is no
   * offered starting point. Currently only supported by the Vertex AI Vizier
   * service. Not supported by HyperparameterTuningJob or TrainingPipeline.
   *
   * @param string $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return string
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Required. Inclusive maximum value of the parameter.
   *
   * @param string $maxValue
   */
  public function setMaxValue($maxValue)
  {
    $this->maxValue = $maxValue;
  }
  /**
   * @return string
   */
  public function getMaxValue()
  {
    return $this->maxValue;
  }
  /**
   * Required. Inclusive minimum value of the parameter.
   *
   * @param string $minValue
   */
  public function setMinValue($minValue)
  {
    $this->minValue = $minValue;
  }
  /**
   * @return string
   */
  public function getMinValue()
  {
    return $this->minValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudySpecParameterSpecIntegerValueSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpecParameterSpecIntegerValueSpec');
