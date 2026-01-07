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

class GoogleCloudDialogflowV2IntentMessageSimpleResponse extends \Google\Model
{
  /**
   * Optional. The text to display.
   *
   * @var string
   */
  public $displayText;
  /**
   * One of text_to_speech or ssml must be provided. Structured spoken response
   * to the user in the SSML format. Mutually exclusive with text_to_speech.
   *
   * @var string
   */
  public $ssml;
  /**
   * One of text_to_speech or ssml must be provided. The plain text of the
   * speech output. Mutually exclusive with ssml.
   *
   * @var string
   */
  public $textToSpeech;

  /**
   * Optional. The text to display.
   *
   * @param string $displayText
   */
  public function setDisplayText($displayText)
  {
    $this->displayText = $displayText;
  }
  /**
   * @return string
   */
  public function getDisplayText()
  {
    return $this->displayText;
  }
  /**
   * One of text_to_speech or ssml must be provided. Structured spoken response
   * to the user in the SSML format. Mutually exclusive with text_to_speech.
   *
   * @param string $ssml
   */
  public function setSsml($ssml)
  {
    $this->ssml = $ssml;
  }
  /**
   * @return string
   */
  public function getSsml()
  {
    return $this->ssml;
  }
  /**
   * One of text_to_speech or ssml must be provided. The plain text of the
   * speech output. Mutually exclusive with ssml.
   *
   * @param string $textToSpeech
   */
  public function setTextToSpeech($textToSpeech)
  {
    $this->textToSpeech = $textToSpeech;
  }
  /**
   * @return string
   */
  public function getTextToSpeech()
  {
    return $this->textToSpeech;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2IntentMessageSimpleResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2IntentMessageSimpleResponse');
