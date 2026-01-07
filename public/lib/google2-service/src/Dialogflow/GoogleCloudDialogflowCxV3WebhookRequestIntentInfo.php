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

class GoogleCloudDialogflowCxV3WebhookRequestIntentInfo extends \Google\Model
{
  /**
   * The confidence of the matched intent. Values range from 0.0 (completely
   * uncertain) to 1.0 (completely certain).
   *
   * @var float
   */
  public $confidence;
  /**
   * Always present. The display name of the last matched intent.
   *
   * @var string
   */
  public $displayName;
  /**
   * Always present. The unique identifier of the last matched intent. Format:
   * `projects//locations//agents//intents/`.
   *
   * @var string
   */
  public $lastMatchedIntent;
  protected $parametersType = GoogleCloudDialogflowCxV3WebhookRequestIntentInfoIntentParameterValue::class;
  protected $parametersDataType = 'map';

  /**
   * The confidence of the matched intent. Values range from 0.0 (completely
   * uncertain) to 1.0 (completely certain).
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * Always present. The display name of the last matched intent.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Always present. The unique identifier of the last matched intent. Format:
   * `projects//locations//agents//intents/`.
   *
   * @param string $lastMatchedIntent
   */
  public function setLastMatchedIntent($lastMatchedIntent)
  {
    $this->lastMatchedIntent = $lastMatchedIntent;
  }
  /**
   * @return string
   */
  public function getLastMatchedIntent()
  {
    return $this->lastMatchedIntent;
  }
  /**
   * Parameters identified as a result of intent matching. This is a map of the
   * name of the identified parameter to the value of the parameter identified
   * from the user's utterance. All parameters defined in the matched intent
   * that are identified will be surfaced here.
   *
   * @param GoogleCloudDialogflowCxV3WebhookRequestIntentInfoIntentParameterValue[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudDialogflowCxV3WebhookRequestIntentInfoIntentParameterValue[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3WebhookRequestIntentInfo::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3WebhookRequestIntentInfo');
