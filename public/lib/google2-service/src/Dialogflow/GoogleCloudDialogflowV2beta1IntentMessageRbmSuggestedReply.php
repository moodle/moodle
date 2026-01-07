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

class GoogleCloudDialogflowV2beta1IntentMessageRbmSuggestedReply extends \Google\Model
{
  /**
   * Opaque payload that the Dialogflow receives in a user event when the user
   * taps the suggested reply. This data will be also forwarded to webhook to
   * allow performing custom business logic.
   *
   * @var string
   */
  public $postbackData;
  /**
   * Suggested reply text.
   *
   * @var string
   */
  public $text;

  /**
   * Opaque payload that the Dialogflow receives in a user event when the user
   * taps the suggested reply. This data will be also forwarded to webhook to
   * allow performing custom business logic.
   *
   * @param string $postbackData
   */
  public function setPostbackData($postbackData)
  {
    $this->postbackData = $postbackData;
  }
  /**
   * @return string
   */
  public function getPostbackData()
  {
    return $this->postbackData;
  }
  /**
   * Suggested reply text.
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
class_alias(GoogleCloudDialogflowV2beta1IntentMessageRbmSuggestedReply::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageRbmSuggestedReply');
