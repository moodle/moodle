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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3WebhookRequestIntentInfoIntentParameterValue extends \Google\Model
{
  /**
   * Always present. Original text value extracted from user utterance.
   *
   * @var string
   */
  public $originalValue;
  /**
   * Always present. Structured value for the parameter extracted from user
   * utterance.
   *
   * @var array
   */
  public $resolvedValue;

  /**
   * Always present. Original text value extracted from user utterance.
   *
   * @param string $originalValue
   */
  public function setOriginalValue($originalValue)
  {
    $this->originalValue = $originalValue;
  }
  /**
   * @return string
   */
  public function getOriginalValue()
  {
    return $this->originalValue;
  }
  /**
   * Always present. Structured value for the parameter extracted from user
   * utterance.
   *
   * @param array $resolvedValue
   */
  public function setResolvedValue($resolvedValue)
  {
    $this->resolvedValue = $resolvedValue;
  }
  /**
   * @return array
   */
  public function getResolvedValue()
  {
    return $this->resolvedValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3WebhookRequestIntentInfoIntentParameterValue::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3WebhookRequestIntentInfoIntentParameterValue');
