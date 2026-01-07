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

class GoogleCloudAiplatformV1StudySpecParameterSpecDiscreteValueSpec extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * A default value for a `DISCRETE` parameter that is assumed to be a
   * relatively good starting point. Unset value signals that there is no
   * offered starting point. It automatically rounds to the nearest feasible
   * discrete point. Currently only supported by the Vertex AI Vizier service.
   * Not supported by HyperparameterTuningJob or TrainingPipeline.
   *
   * @var 
   */
  public $defaultValue;
  /**
   * Required. A list of possible values. The list should be in increasing order
   * and at least 1e-10 apart. For instance, this parameter might have possible
   * settings of 1.5, 2.5, and 4.0. This list should not contain more than 1,000
   * values.
   *
   * @var []
   */
  public $values;

  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  public function setValues($values)
  {
    $this->values = $values;
  }
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudySpecParameterSpecDiscreteValueSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpecParameterSpecDiscreteValueSpec');
