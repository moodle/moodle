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

class GoogleCloudDialogflowCxV3beta1ConversationTurnUserInput extends \Google\Model
{
  /**
   * Whether sentiment analysis is enabled.
   *
   * @var bool
   */
  public $enableSentimentAnalysis;
  /**
   * Parameters that need to be injected into the conversation during intent
   * detection.
   *
   * @var array[]
   */
  public $injectedParameters;
  protected $inputType = GoogleCloudDialogflowCxV3beta1QueryInput::class;
  protected $inputDataType = '';
  /**
   * If webhooks should be allowed to trigger in response to the user utterance.
   * Often if parameters are injected, webhooks should not be enabled.
   *
   * @var bool
   */
  public $isWebhookEnabled;

  /**
   * Whether sentiment analysis is enabled.
   *
   * @param bool $enableSentimentAnalysis
   */
  public function setEnableSentimentAnalysis($enableSentimentAnalysis)
  {
    $this->enableSentimentAnalysis = $enableSentimentAnalysis;
  }
  /**
   * @return bool
   */
  public function getEnableSentimentAnalysis()
  {
    return $this->enableSentimentAnalysis;
  }
  /**
   * Parameters that need to be injected into the conversation during intent
   * detection.
   *
   * @param array[] $injectedParameters
   */
  public function setInjectedParameters($injectedParameters)
  {
    $this->injectedParameters = $injectedParameters;
  }
  /**
   * @return array[]
   */
  public function getInjectedParameters()
  {
    return $this->injectedParameters;
  }
  /**
   * Supports text input, event input, dtmf input in the test case.
   *
   * @param GoogleCloudDialogflowCxV3beta1QueryInput $input
   */
  public function setInput(GoogleCloudDialogflowCxV3beta1QueryInput $input)
  {
    $this->input = $input;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1QueryInput
   */
  public function getInput()
  {
    return $this->input;
  }
  /**
   * If webhooks should be allowed to trigger in response to the user utterance.
   * Often if parameters are injected, webhooks should not be enabled.
   *
   * @param bool $isWebhookEnabled
   */
  public function setIsWebhookEnabled($isWebhookEnabled)
  {
    $this->isWebhookEnabled = $isWebhookEnabled;
  }
  /**
   * @return bool
   */
  public function getIsWebhookEnabled()
  {
    return $this->isWebhookEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1ConversationTurnUserInput::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1ConversationTurnUserInput');
