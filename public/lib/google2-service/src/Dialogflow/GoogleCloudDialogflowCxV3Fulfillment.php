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

class GoogleCloudDialogflowCxV3Fulfillment extends \Google\Collection
{
  protected $collection_key = 'setParameterActions';
  protected $advancedSettingsType = GoogleCloudDialogflowCxV3AdvancedSettings::class;
  protected $advancedSettingsDataType = '';
  protected $conditionalCasesType = GoogleCloudDialogflowCxV3FulfillmentConditionalCases::class;
  protected $conditionalCasesDataType = 'array';
  /**
   * If the flag is true, the agent will utilize LLM to generate a text
   * response. If LLM generation fails, the defined responses in the fulfillment
   * will be respected. This flag is only useful for fulfillments associated
   * with no-match event handlers.
   *
   * @var bool
   */
  public $enableGenerativeFallback;
  protected $generatorsType = GoogleCloudDialogflowCxV3FulfillmentGeneratorSettings::class;
  protected $generatorsDataType = 'array';
  protected $messagesType = GoogleCloudDialogflowCxV3ResponseMessage::class;
  protected $messagesDataType = 'array';
  /**
   * Whether Dialogflow should return currently queued fulfillment response
   * messages in streaming APIs. If a webhook is specified, it happens before
   * Dialogflow invokes webhook. Warning: 1) This flag only affects streaming
   * API. Responses are still queued and returned once in non-streaming API. 2)
   * The flag can be enabled in any fulfillment but only the first 3 partial
   * responses will be returned. You may only want to apply it to fulfillments
   * that have slow webhooks.
   *
   * @var bool
   */
  public $returnPartialResponses;
  protected $setParameterActionsType = GoogleCloudDialogflowCxV3FulfillmentSetParameterAction::class;
  protected $setParameterActionsDataType = 'array';
  /**
   * The value of this field will be populated in the WebhookRequest
   * `fulfillmentInfo.tag` field by Dialogflow when the associated webhook is
   * called. The tag is typically used by the webhook service to identify which
   * fulfillment is being called, but it could be used for other purposes. This
   * field is required if `webhook` is specified.
   *
   * @var string
   */
  public $tag;
  /**
   * The webhook to call. Format: `projects//locations//agents//webhooks/`.
   *
   * @var string
   */
  public $webhook;

  /**
   * Hierarchical advanced settings for this fulfillment. The settings exposed
   * at the lower level overrides the settings exposed at the higher level.
   *
   * @param GoogleCloudDialogflowCxV3AdvancedSettings $advancedSettings
   */
  public function setAdvancedSettings(GoogleCloudDialogflowCxV3AdvancedSettings $advancedSettings)
  {
    $this->advancedSettings = $advancedSettings;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AdvancedSettings
   */
  public function getAdvancedSettings()
  {
    return $this->advancedSettings;
  }
  /**
   * Conditional cases for this fulfillment.
   *
   * @param GoogleCloudDialogflowCxV3FulfillmentConditionalCases[] $conditionalCases
   */
  public function setConditionalCases($conditionalCases)
  {
    $this->conditionalCases = $conditionalCases;
  }
  /**
   * @return GoogleCloudDialogflowCxV3FulfillmentConditionalCases[]
   */
  public function getConditionalCases()
  {
    return $this->conditionalCases;
  }
  /**
   * If the flag is true, the agent will utilize LLM to generate a text
   * response. If LLM generation fails, the defined responses in the fulfillment
   * will be respected. This flag is only useful for fulfillments associated
   * with no-match event handlers.
   *
   * @param bool $enableGenerativeFallback
   */
  public function setEnableGenerativeFallback($enableGenerativeFallback)
  {
    $this->enableGenerativeFallback = $enableGenerativeFallback;
  }
  /**
   * @return bool
   */
  public function getEnableGenerativeFallback()
  {
    return $this->enableGenerativeFallback;
  }
  /**
   * A list of Generators to be called during this fulfillment.
   *
   * @param GoogleCloudDialogflowCxV3FulfillmentGeneratorSettings[] $generators
   */
  public function setGenerators($generators)
  {
    $this->generators = $generators;
  }
  /**
   * @return GoogleCloudDialogflowCxV3FulfillmentGeneratorSettings[]
   */
  public function getGenerators()
  {
    return $this->generators;
  }
  /**
   * The list of rich message responses to present to the user.
   *
   * @param GoogleCloudDialogflowCxV3ResponseMessage[] $messages
   */
  public function setMessages($messages)
  {
    $this->messages = $messages;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ResponseMessage[]
   */
  public function getMessages()
  {
    return $this->messages;
  }
  /**
   * Whether Dialogflow should return currently queued fulfillment response
   * messages in streaming APIs. If a webhook is specified, it happens before
   * Dialogflow invokes webhook. Warning: 1) This flag only affects streaming
   * API. Responses are still queued and returned once in non-streaming API. 2)
   * The flag can be enabled in any fulfillment but only the first 3 partial
   * responses will be returned. You may only want to apply it to fulfillments
   * that have slow webhooks.
   *
   * @param bool $returnPartialResponses
   */
  public function setReturnPartialResponses($returnPartialResponses)
  {
    $this->returnPartialResponses = $returnPartialResponses;
  }
  /**
   * @return bool
   */
  public function getReturnPartialResponses()
  {
    return $this->returnPartialResponses;
  }
  /**
   * Set parameter values before executing the webhook.
   *
   * @param GoogleCloudDialogflowCxV3FulfillmentSetParameterAction[] $setParameterActions
   */
  public function setSetParameterActions($setParameterActions)
  {
    $this->setParameterActions = $setParameterActions;
  }
  /**
   * @return GoogleCloudDialogflowCxV3FulfillmentSetParameterAction[]
   */
  public function getSetParameterActions()
  {
    return $this->setParameterActions;
  }
  /**
   * The value of this field will be populated in the WebhookRequest
   * `fulfillmentInfo.tag` field by Dialogflow when the associated webhook is
   * called. The tag is typically used by the webhook service to identify which
   * fulfillment is being called, but it could be used for other purposes. This
   * field is required if `webhook` is specified.
   *
   * @param string $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return string
   */
  public function getTag()
  {
    return $this->tag;
  }
  /**
   * The webhook to call. Format: `projects//locations//agents//webhooks/`.
   *
   * @param string $webhook
   */
  public function setWebhook($webhook)
  {
    $this->webhook = $webhook;
  }
  /**
   * @return string
   */
  public function getWebhook()
  {
    return $this->webhook;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Fulfillment::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Fulfillment');
