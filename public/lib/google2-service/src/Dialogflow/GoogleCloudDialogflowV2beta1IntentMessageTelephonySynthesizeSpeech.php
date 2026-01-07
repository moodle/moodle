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

class GoogleCloudDialogflowV2beta1IntentMessageTelephonySynthesizeSpeech extends \Google\Model
{
  /**
   * The SSML to be synthesized. For more information, see
   * [SSML](https://developers.google.com/actions/reference/ssml).
   *
   * @var string
   */
  public $ssml;
  /**
   * The raw text to be synthesized.
   *
   * @var string
   */
  public $text;

  /**
   * The SSML to be synthesized. For more information, see
   * [SSML](https://developers.google.com/actions/reference/ssml).
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
   * The raw text to be synthesized.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentMessageTelephonySynthesizeSpeech::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageTelephonySynthesizeSpeech');
