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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainDialogflowInteractionData extends \Google\Model
{
  /**
   * The confidence of the match ranging from 0.0 (completely uncertain) to 1.0
   * (completely certain).
   *
   * @var float
   */
  public $confidence;
  /**
   * The Dialogflow intent resource path. Format:
   * projects/{project}/agent/{agent}/intents/{intent}
   *
   * @var string
   */
  public $dialogflowIntentId;

  /**
   * The confidence of the match ranging from 0.0 (completely uncertain) to 1.0
   * (completely certain).
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
   * The Dialogflow intent resource path. Format:
   * projects/{project}/agent/{agent}/intents/{intent}
   *
   * @param string $dialogflowIntentId
   */
  public function setDialogflowIntentId($dialogflowIntentId)
  {
    $this->dialogflowIntentId = $dialogflowIntentId;
  }
  /**
   * @return string
   */
  public function getDialogflowIntentId()
  {
    return $this->dialogflowIntentId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainDialogflowInteractionData::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainDialogflowInteractionData');
