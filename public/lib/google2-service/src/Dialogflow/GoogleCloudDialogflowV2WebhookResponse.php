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

class GoogleCloudDialogflowV2WebhookResponse extends \Google\Collection
{
  protected $collection_key = 'sessionEntityTypes';
  protected $followupEventInputType = GoogleCloudDialogflowV2EventInput::class;
  protected $followupEventInputDataType = '';
  protected $fulfillmentMessagesType = GoogleCloudDialogflowV2IntentMessage::class;
  protected $fulfillmentMessagesDataType = 'array';
  /**
   * Optional. The text response message intended for the end-user. It is
   * recommended to use `fulfillment_messages.text.text[0]` instead. When
   * provided, Dialogflow uses this field to populate
   * QueryResult.fulfillment_text sent to the integration or API caller.
   *
   * @var string
   */
  public $fulfillmentText;
  protected $outputContextsType = GoogleCloudDialogflowV2Context::class;
  protected $outputContextsDataType = 'array';
  /**
   * Optional. This field can be used to pass custom data from your webhook to
   * the integration or API caller. Arbitrary JSON objects are supported. When
   * provided, Dialogflow uses this field to populate
   * QueryResult.webhook_payload sent to the integration or API caller. This
   * field is also used by the [Google Assistant
   * integration](https://cloud.google.com/dialogflow/docs/integrations/aog) for
   * rich response messages. See the format definition at [Google Assistant
   * Dialogflow webhook format](https://developers.google.com/assistant/actions/
   * build/json/dialogflow-webhook-json)
   *
   * @var array[]
   */
  public $payload;
  protected $sessionEntityTypesType = GoogleCloudDialogflowV2SessionEntityType::class;
  protected $sessionEntityTypesDataType = 'array';
  /**
   * Optional. A custom field used to identify the webhook source. Arbitrary
   * strings are supported. When provided, Dialogflow uses this field to
   * populate QueryResult.webhook_source sent to the integration or API caller.
   *
   * @var string
   */
  public $source;

  /**
   * Optional. Invokes the supplied events. When this field is set, Dialogflow
   * ignores the `fulfillment_text`, `fulfillment_messages`, and `payload`
   * fields.
   *
   * @param GoogleCloudDialogflowV2EventInput $followupEventInput
   */
  public function setFollowupEventInput(GoogleCloudDialogflowV2EventInput $followupEventInput)
  {
    $this->followupEventInput = $followupEventInput;
  }
  /**
   * @return GoogleCloudDialogflowV2EventInput
   */
  public function getFollowupEventInput()
  {
    return $this->followupEventInput;
  }
  /**
   * Optional. The rich response messages intended for the end-user. When
   * provided, Dialogflow uses this field to populate
   * QueryResult.fulfillment_messages sent to the integration or API caller.
   *
   * @param GoogleCloudDialogflowV2IntentMessage[] $fulfillmentMessages
   */
  public function setFulfillmentMessages($fulfillmentMessages)
  {
    $this->fulfillmentMessages = $fulfillmentMessages;
  }
  /**
   * @return GoogleCloudDialogflowV2IntentMessage[]
   */
  public function getFulfillmentMessages()
  {
    return $this->fulfillmentMessages;
  }
  /**
   * Optional. The text response message intended for the end-user. It is
   * recommended to use `fulfillment_messages.text.text[0]` instead. When
   * provided, Dialogflow uses this field to populate
   * QueryResult.fulfillment_text sent to the integration or API caller.
   *
   * @param string $fulfillmentText
   */
  public function setFulfillmentText($fulfillmentText)
  {
    $this->fulfillmentText = $fulfillmentText;
  }
  /**
   * @return string
   */
  public function getFulfillmentText()
  {
    return $this->fulfillmentText;
  }
  /**
   * Optional. The collection of output contexts that will overwrite currently
   * active contexts for the session and reset their lifespans. When provided,
   * Dialogflow uses this field to populate QueryResult.output_contexts sent to
   * the integration or API caller.
   *
   * @param GoogleCloudDialogflowV2Context[] $outputContexts
   */
  public function setOutputContexts($outputContexts)
  {
    $this->outputContexts = $outputContexts;
  }
  /**
   * @return GoogleCloudDialogflowV2Context[]
   */
  public function getOutputContexts()
  {
    return $this->outputContexts;
  }
  /**
   * Optional. This field can be used to pass custom data from your webhook to
   * the integration or API caller. Arbitrary JSON objects are supported. When
   * provided, Dialogflow uses this field to populate
   * QueryResult.webhook_payload sent to the integration or API caller. This
   * field is also used by the [Google Assistant
   * integration](https://cloud.google.com/dialogflow/docs/integrations/aog) for
   * rich response messages. See the format definition at [Google Assistant
   * Dialogflow webhook format](https://developers.google.com/assistant/actions/
   * build/json/dialogflow-webhook-json)
   *
   * @param array[] $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return array[]
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Optional. Additional session entity types to replace or extend developer
   * entity types with. The entity synonyms apply to all languages and persist
   * for the session. Setting this data from a webhook overwrites the session
   * entity types that have been set using `detectIntent`,
   * `streamingDetectIntent` or SessionEntityType management methods.
   *
   * @param GoogleCloudDialogflowV2SessionEntityType[] $sessionEntityTypes
   */
  public function setSessionEntityTypes($sessionEntityTypes)
  {
    $this->sessionEntityTypes = $sessionEntityTypes;
  }
  /**
   * @return GoogleCloudDialogflowV2SessionEntityType[]
   */
  public function getSessionEntityTypes()
  {
    return $this->sessionEntityTypes;
  }
  /**
   * Optional. A custom field used to identify the webhook source. Arbitrary
   * strings are supported. When provided, Dialogflow uses this field to
   * populate QueryResult.webhook_source sent to the integration or API caller.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2WebhookResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2WebhookResponse');
