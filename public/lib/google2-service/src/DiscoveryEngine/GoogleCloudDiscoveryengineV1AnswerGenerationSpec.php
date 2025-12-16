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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1AnswerGenerationSpec extends \Google\Model
{
  protected $userDefinedClassifierSpecType = GoogleCloudDiscoveryengineV1AnswerGenerationSpecUserDefinedClassifierSpec::class;
  protected $userDefinedClassifierSpecDataType = '';

  /**
   * Optional. The specification for user specified classifier spec.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerGenerationSpecUserDefinedClassifierSpec $userDefinedClassifierSpec
   */
  public function setUserDefinedClassifierSpec(GoogleCloudDiscoveryengineV1AnswerGenerationSpecUserDefinedClassifierSpec $userDefinedClassifierSpec)
  {
    $this->userDefinedClassifierSpec = $userDefinedClassifierSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerGenerationSpecUserDefinedClassifierSpec
   */
  public function getUserDefinedClassifierSpec()
  {
    return $this->userDefinedClassifierSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerGenerationSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerGenerationSpec');
