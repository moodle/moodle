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

class GoogleCloudDialogflowV2beta1IntentSuggestion extends \Google\Model
{
  /**
   * Human readable description for better understanding an intent like its
   * scope, content, result etc. Maximum character limit: 140 characters.
   *
   * @var string
   */
  public $description;
  /**
   * The display name of the intent.
   *
   * @var string
   */
  public $displayName;
  /**
   * The unique identifier of this intent. Format:
   * `projects//locations//agent/intents/`.
   *
   * @var string
   */
  public $intentV2;

  /**
   * Human readable description for better understanding an intent like its
   * scope, content, result etc. Maximum character limit: 140 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The display name of the intent.
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
   * The unique identifier of this intent. Format:
   * `projects//locations//agent/intents/`.
   *
   * @param string $intentV2
   */
  public function setIntentV2($intentV2)
  {
    $this->intentV2 = $intentV2;
  }
  /**
   * @return string
   */
  public function getIntentV2()
  {
    return $this->intentV2;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentSuggestion::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentSuggestion');
