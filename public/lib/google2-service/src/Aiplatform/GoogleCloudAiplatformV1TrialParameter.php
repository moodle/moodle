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

class GoogleCloudAiplatformV1TrialParameter extends \Google\Model
{
  /**
   * Output only. The ID of the parameter. The parameter should be defined in
   * StudySpec's Parameters.
   *
   * @var string
   */
  public $parameterId;
  /**
   * Output only. The value of the parameter. `number_value` will be set if a
   * parameter defined in StudySpec is in type 'INTEGER', 'DOUBLE' or
   * 'DISCRETE'. `string_value` will be set if a parameter defined in StudySpec
   * is in type 'CATEGORICAL'.
   *
   * @var array
   */
  public $value;

  /**
   * Output only. The ID of the parameter. The parameter should be defined in
   * StudySpec's Parameters.
   *
   * @param string $parameterId
   */
  public function setParameterId($parameterId)
  {
    $this->parameterId = $parameterId;
  }
  /**
   * @return string
   */
  public function getParameterId()
  {
    return $this->parameterId;
  }
  /**
   * Output only. The value of the parameter. `number_value` will be set if a
   * parameter defined in StudySpec is in type 'INTEGER', 'DOUBLE' or
   * 'DISCRETE'. `string_value` will be set if a parameter defined in StudySpec
   * is in type 'CATEGORICAL'.
   *
   * @param array $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return array
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TrialParameter::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TrialParameter');
