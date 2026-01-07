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

class GoogleCloudDialogflowV2beta1QueryResult extends \Google\Collection
{
  protected $collection_key = 'outputContexts';
  /**
   * The action name from the matched intent.
   *
   * @var string
   */
  public $action;
  /**
   * This field is set to: - `false` if the matched intent has required
   * parameters and not all of the required parameter values have been
   * collected. - `true` if all required parameter values have been collected,
   * or if the matched intent doesn't contain any required parameters.
   *
   * @var bool
   */
  public $allRequiredParamsPresent;
  /**
   * Indicates whether the conversational query triggers a cancellation for slot
   * filling. For more information, see the [cancel slot filling
   * documentation](https://cloud.google.com/dialogflow/es/docs/intents-actions-
   * parameters#cancel).
   *
   * @var bool
   */
  public $cancelsSlotFilling;
  /**
   * Free-form diagnostic information for the associated detect intent request.
   * The fields of this data can change without notice, so you should not write
   * code that depends on its structure. The data may contain: - webhook call
   * latency - webhook errors
   *
   * @var array[]
   */
  public $diagnosticInfo;
  protected $fulfillmentMessagesType = GoogleCloudDialogflowV2beta1IntentMessage::class;
  protected $fulfillmentMessagesDataType = 'array';
  /**
   * The text to be pronounced to the user or shown on the screen. Note: This is
   * a legacy field, `fulfillment_messages` should be preferred.
   *
   * @var string
   */
  public $fulfillmentText;
  protected $intentType = GoogleCloudDialogflowV2beta1Intent::class;
  protected $intentDataType = '';
  /**
   * The intent detection confidence. Values range from 0.0 (completely
   * uncertain) to 1.0 (completely certain). This value is for informational
   * purpose only and is only used to help match the best intent within the
   * classification threshold. This value may change for the same end-user
   * expression at any time due to a model retraining or change in
   * implementation. If there are `multiple knowledge_answers` messages, this
   * value is set to the greatest `knowledgeAnswers.match_confidence` value in
   * the list.
   *
   * @var float
   */
  public $intentDetectionConfidence;
  protected $knowledgeAnswersType = GoogleCloudDialogflowV2beta1KnowledgeAnswers::class;
  protected $knowledgeAnswersDataType = '';
  /**
   * The language that was triggered during intent detection. See [Language
   * Support](https://cloud.google.com/dialogflow/docs/reference/language) for a
   * list of the currently supported language codes.
   *
   * @var string
   */
  public $languageCode;
  protected $outputContextsType = GoogleCloudDialogflowV2beta1Context::class;
  protected $outputContextsDataType = 'array';
  /**
   * The collection of extracted parameters. Depending on your protocol or
   * client library language, this is a map, associative array, symbol table,
   * dictionary, or JSON object composed of a collection of (MapKey, MapValue)
   * pairs: * MapKey type: string * MapKey value: parameter name * MapValue
   * type: If parameter's entity type is a composite entity then use map,
   * otherwise, depending on the parameter value type, it could be one of
   * string, number, boolean, null, list or map. * MapValue value: If
   * parameter's entity type is a composite entity then use map from composite
   * entity property names to property values, otherwise, use parameter value.
   *
   * @var array[]
   */
  public $parameters;
  /**
   * The original conversational query text: - If natural language text was
   * provided as input, `query_text` contains a copy of the input. - If natural
   * language speech audio was provided as input, `query_text` contains the
   * speech recognition result. If speech recognizer produced multiple
   * alternatives, a particular one is picked. - If automatic spell correction
   * is enabled, `query_text` will contain the corrected user input.
   *
   * @var string
   */
  public $queryText;
  protected $sentimentAnalysisResultType = GoogleCloudDialogflowV2beta1SentimentAnalysisResult::class;
  protected $sentimentAnalysisResultDataType = '';
  /**
   * The Speech recognition confidence between 0.0 and 1.0. A higher number
   * indicates an estimated greater likelihood that the recognized words are
   * correct. The default of 0.0 is a sentinel value indicating that confidence
   * was not set. This field is not guaranteed to be accurate or set. In
   * particular this field isn't set for StreamingDetectIntent since the
   * streaming endpoint has separate confidence estimates per portion of the
   * audio in StreamingRecognitionResult.
   *
   * @var float
   */
  public $speechRecognitionConfidence;
  /**
   * If the query was fulfilled by a webhook call, this field is set to the
   * value of the `payload` field returned in the webhook response.
   *
   * @var array[]
   */
  public $webhookPayload;
  /**
   * If the query was fulfilled by a webhook call, this field is set to the
   * value of the `source` field returned in the webhook response.
   *
   * @var string
   */
  public $webhookSource;

  /**
   * The action name from the matched intent.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * This field is set to: - `false` if the matched intent has required
   * parameters and not all of the required parameter values have been
   * collected. - `true` if all required parameter values have been collected,
   * or if the matched intent doesn't contain any required parameters.
   *
   * @param bool $allRequiredParamsPresent
   */
  public function setAllRequiredParamsPresent($allRequiredParamsPresent)
  {
    $this->allRequiredParamsPresent = $allRequiredParamsPresent;
  }
  /**
   * @return bool
   */
  public function getAllRequiredParamsPresent()
  {
    return $this->allRequiredParamsPresent;
  }
  /**
   * Indicates whether the conversational query triggers a cancellation for slot
   * filling. For more information, see the [cancel slot filling
   * documentation](https://cloud.google.com/dialogflow/es/docs/intents-actions-
   * parameters#cancel).
   *
   * @param bool $cancelsSlotFilling
   */
  public function setCancelsSlotFilling($cancelsSlotFilling)
  {
    $this->cancelsSlotFilling = $cancelsSlotFilling;
  }
  /**
   * @return bool
   */
  public function getCancelsSlotFilling()
  {
    return $this->cancelsSlotFilling;
  }
  /**
   * Free-form diagnostic information for the associated detect intent request.
   * The fields of this data can change without notice, so you should not write
   * code that depends on its structure. The data may contain: - webhook call
   * latency - webhook errors
   *
   * @param array[] $diagnosticInfo
   */
  public function setDiagnosticInfo($diagnosticInfo)
  {
    $this->diagnosticInfo = $diagnosticInfo;
  }
  /**
   * @return array[]
   */
  public function getDiagnosticInfo()
  {
    return $this->diagnosticInfo;
  }
  /**
   * The collection of rich messages to present to the user.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessage[] $fulfillmentMessages
   */
  public function setFulfillmentMessages($fulfillmentMessages)
  {
    $this->fulfillmentMessages = $fulfillmentMessages;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessage[]
   */
  public function getFulfillmentMessages()
  {
    return $this->fulfillmentMessages;
  }
  /**
   * The text to be pronounced to the user or shown on the screen. Note: This is
   * a legacy field, `fulfillment_messages` should be preferred.
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
   * The intent that matched the conversational query. Some, not all fields are
   * filled in this message, including but not limited to: `name`,
   * `display_name`, `end_interaction` and `is_fallback`.
   *
   * @param GoogleCloudDialogflowV2beta1Intent $intent
   */
  public function setIntent(GoogleCloudDialogflowV2beta1Intent $intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1Intent
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * The intent detection confidence. Values range from 0.0 (completely
   * uncertain) to 1.0 (completely certain). This value is for informational
   * purpose only and is only used to help match the best intent within the
   * classification threshold. This value may change for the same end-user
   * expression at any time due to a model retraining or change in
   * implementation. If there are `multiple knowledge_answers` messages, this
   * value is set to the greatest `knowledgeAnswers.match_confidence` value in
   * the list.
   *
   * @param float $intentDetectionConfidence
   */
  public function setIntentDetectionConfidence($intentDetectionConfidence)
  {
    $this->intentDetectionConfidence = $intentDetectionConfidence;
  }
  /**
   * @return float
   */
  public function getIntentDetectionConfidence()
  {
    return $this->intentDetectionConfidence;
  }
  /**
   * The result from Knowledge Connector (if any), ordered by decreasing
   * `KnowledgeAnswers.match_confidence`.
   *
   * @param GoogleCloudDialogflowV2beta1KnowledgeAnswers $knowledgeAnswers
   */
  public function setKnowledgeAnswers(GoogleCloudDialogflowV2beta1KnowledgeAnswers $knowledgeAnswers)
  {
    $this->knowledgeAnswers = $knowledgeAnswers;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1KnowledgeAnswers
   */
  public function getKnowledgeAnswers()
  {
    return $this->knowledgeAnswers;
  }
  /**
   * The language that was triggered during intent detection. See [Language
   * Support](https://cloud.google.com/dialogflow/docs/reference/language) for a
   * list of the currently supported language codes.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The collection of output contexts. If applicable,
   * `output_contexts.parameters` contains entries with name `.original`
   * containing the original parameter values before the query.
   *
   * @param GoogleCloudDialogflowV2beta1Context[] $outputContexts
   */
  public function setOutputContexts($outputContexts)
  {
    $this->outputContexts = $outputContexts;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1Context[]
   */
  public function getOutputContexts()
  {
    return $this->outputContexts;
  }
  /**
   * The collection of extracted parameters. Depending on your protocol or
   * client library language, this is a map, associative array, symbol table,
   * dictionary, or JSON object composed of a collection of (MapKey, MapValue)
   * pairs: * MapKey type: string * MapKey value: parameter name * MapValue
   * type: If parameter's entity type is a composite entity then use map,
   * otherwise, depending on the parameter value type, it could be one of
   * string, number, boolean, null, list or map. * MapValue value: If
   * parameter's entity type is a composite entity then use map from composite
   * entity property names to property values, otherwise, use parameter value.
   *
   * @param array[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return array[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * The original conversational query text: - If natural language text was
   * provided as input, `query_text` contains a copy of the input. - If natural
   * language speech audio was provided as input, `query_text` contains the
   * speech recognition result. If speech recognizer produced multiple
   * alternatives, a particular one is picked. - If automatic spell correction
   * is enabled, `query_text` will contain the corrected user input.
   *
   * @param string $queryText
   */
  public function setQueryText($queryText)
  {
    $this->queryText = $queryText;
  }
  /**
   * @return string
   */
  public function getQueryText()
  {
    return $this->queryText;
  }
  /**
   * The sentiment analysis result, which depends on the
   * `sentiment_analysis_request_config` specified in the request.
   *
   * @param GoogleCloudDialogflowV2beta1SentimentAnalysisResult $sentimentAnalysisResult
   */
  public function setSentimentAnalysisResult(GoogleCloudDialogflowV2beta1SentimentAnalysisResult $sentimentAnalysisResult)
  {
    $this->sentimentAnalysisResult = $sentimentAnalysisResult;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1SentimentAnalysisResult
   */
  public function getSentimentAnalysisResult()
  {
    return $this->sentimentAnalysisResult;
  }
  /**
   * The Speech recognition confidence between 0.0 and 1.0. A higher number
   * indicates an estimated greater likelihood that the recognized words are
   * correct. The default of 0.0 is a sentinel value indicating that confidence
   * was not set. This field is not guaranteed to be accurate or set. In
   * particular this field isn't set for StreamingDetectIntent since the
   * streaming endpoint has separate confidence estimates per portion of the
   * audio in StreamingRecognitionResult.
   *
   * @param float $speechRecognitionConfidence
   */
  public function setSpeechRecognitionConfidence($speechRecognitionConfidence)
  {
    $this->speechRecognitionConfidence = $speechRecognitionConfidence;
  }
  /**
   * @return float
   */
  public function getSpeechRecognitionConfidence()
  {
    return $this->speechRecognitionConfidence;
  }
  /**
   * If the query was fulfilled by a webhook call, this field is set to the
   * value of the `payload` field returned in the webhook response.
   *
   * @param array[] $webhookPayload
   */
  public function setWebhookPayload($webhookPayload)
  {
    $this->webhookPayload = $webhookPayload;
  }
  /**
   * @return array[]
   */
  public function getWebhookPayload()
  {
    return $this->webhookPayload;
  }
  /**
   * If the query was fulfilled by a webhook call, this field is set to the
   * value of the `source` field returned in the webhook response.
   *
   * @param string $webhookSource
   */
  public function setWebhookSource($webhookSource)
  {
    $this->webhookSource = $webhookSource;
  }
  /**
   * @return string
   */
  public function getWebhookSource()
  {
    return $this->webhookSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1QueryResult::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1QueryResult');
