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

class GoogleCloudDialogflowCxV3QueryResult extends \Google\Collection
{
  protected $collection_key = 'webhookStatuses';
  protected $advancedSettingsType = GoogleCloudDialogflowCxV3AdvancedSettings::class;
  protected $advancedSettingsDataType = '';
  /**
   * Indicates whether the Thumbs up/Thumbs down rating controls are need to be
   * shown for the response in the Dialogflow Messenger widget.
   *
   * @var bool
   */
  public $allowAnswerFeedback;
  protected $currentPageType = GoogleCloudDialogflowCxV3Page::class;
  protected $currentPageDataType = '';
  protected $dataStoreConnectionSignalsType = GoogleCloudDialogflowCxV3DataStoreConnectionSignals::class;
  protected $dataStoreConnectionSignalsDataType = '';
  /**
   * The free-form diagnostic info. For example, this field could contain
   * webhook call latency. The fields of this data can change without notice, so
   * you should not write code that depends on its structure. One of the fields
   * is called "Alternative Matched Intents", which may aid with debugging. The
   * following describes these intent results: - The list is empty if no intent
   * was matched to end-user input. - Only intents that are referenced in the
   * currently active flow are included. - The matched intent is included. -
   * Other intents that could have matched end-user input, but did not match
   * because they are referenced by intent routes that are out of
   * [scope](https://cloud.google.com/dialogflow/cx/docs/concept/handler#scope),
   * are included. - Other intents referenced by intent routes in scope that
   * matched end-user input, but had a lower confidence score.
   *
   * @var array[]
   */
  public $diagnosticInfo;
  protected $dtmfType = GoogleCloudDialogflowCxV3DtmfInput::class;
  protected $dtmfDataType = '';
  protected $intentType = GoogleCloudDialogflowCxV3Intent::class;
  protected $intentDataType = '';
  /**
   * The intent detection confidence. Values range from 0.0 (completely
   * uncertain) to 1.0 (completely certain). This value is for informational
   * purpose only and is only used to help match the best intent within the
   * classification threshold. This value may change for the same end-user
   * expression at any time due to a model retraining or change in
   * implementation. This field is deprecated, please use QueryResult.match
   * instead.
   *
   * @deprecated
   * @var float
   */
  public $intentDetectionConfidence;
  /**
   * The language that was triggered during intent detection. See [Language
   * Support](https://cloud.google.com/dialogflow/cx/docs/reference/language)
   * for a list of the currently supported language codes.
   *
   * @var string
   */
  public $languageCode;
  protected $matchType = GoogleCloudDialogflowCxV3Match::class;
  protected $matchDataType = '';
  /**
   * The collected session parameters. Depending on your protocol or client
   * library language, this is a map, associative array, symbol table,
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
  protected $responseMessagesType = GoogleCloudDialogflowCxV3ResponseMessage::class;
  protected $responseMessagesDataType = 'array';
  protected $sentimentAnalysisResultType = GoogleCloudDialogflowCxV3SentimentAnalysisResult::class;
  protected $sentimentAnalysisResultDataType = '';
  /**
   * If natural language text was provided as input, this field will contain a
   * copy of the text.
   *
   * @var string
   */
  public $text;
  /**
   * If natural language speech audio was provided as input, this field will
   * contain the transcript for the audio.
   *
   * @var string
   */
  public $transcript;
  /**
   * If an event was provided as input, this field will contain the name of the
   * event.
   *
   * @var string
   */
  public $triggerEvent;
  /**
   * If an intent was provided as input, this field will contain a copy of the
   * intent identifier. Format: `projects//locations//agents//intents/`.
   *
   * @var string
   */
  public $triggerIntent;
  /**
   * The list of webhook payload in WebhookResponse.payload, in the order of
   * call sequence. If some webhook call fails or doesn't return any payload, an
   * empty `Struct` would be used instead.
   *
   * @var array[]
   */
  public $webhookPayloads;
  protected $webhookStatusesType = GoogleRpcStatus::class;
  protected $webhookStatusesDataType = 'array';

  /**
   * Returns the current advanced settings including IVR settings. Even though
   * the operations configured by these settings are performed by Dialogflow,
   * the client may need to perform special logic at the moment. For example, if
   * Dialogflow exports audio to Google Cloud Storage, then the client may need
   * to wait for the resulting object to appear in the bucket before proceeding.
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
   * Indicates whether the Thumbs up/Thumbs down rating controls are need to be
   * shown for the response in the Dialogflow Messenger widget.
   *
   * @param bool $allowAnswerFeedback
   */
  public function setAllowAnswerFeedback($allowAnswerFeedback)
  {
    $this->allowAnswerFeedback = $allowAnswerFeedback;
  }
  /**
   * @return bool
   */
  public function getAllowAnswerFeedback()
  {
    return $this->allowAnswerFeedback;
  }
  /**
   * The current Page. Some, not all fields are filled in this message,
   * including but not limited to `name` and `display_name`.
   *
   * @param GoogleCloudDialogflowCxV3Page $currentPage
   */
  public function setCurrentPage(GoogleCloudDialogflowCxV3Page $currentPage)
  {
    $this->currentPage = $currentPage;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Page
   */
  public function getCurrentPage()
  {
    return $this->currentPage;
  }
  /**
   * Optional. Data store connection feature output signals. Filled only when
   * data stores are involved in serving the query.
   *
   * @param GoogleCloudDialogflowCxV3DataStoreConnectionSignals $dataStoreConnectionSignals
   */
  public function setDataStoreConnectionSignals(GoogleCloudDialogflowCxV3DataStoreConnectionSignals $dataStoreConnectionSignals)
  {
    $this->dataStoreConnectionSignals = $dataStoreConnectionSignals;
  }
  /**
   * @return GoogleCloudDialogflowCxV3DataStoreConnectionSignals
   */
  public function getDataStoreConnectionSignals()
  {
    return $this->dataStoreConnectionSignals;
  }
  /**
   * The free-form diagnostic info. For example, this field could contain
   * webhook call latency. The fields of this data can change without notice, so
   * you should not write code that depends on its structure. One of the fields
   * is called "Alternative Matched Intents", which may aid with debugging. The
   * following describes these intent results: - The list is empty if no intent
   * was matched to end-user input. - Only intents that are referenced in the
   * currently active flow are included. - The matched intent is included. -
   * Other intents that could have matched end-user input, but did not match
   * because they are referenced by intent routes that are out of
   * [scope](https://cloud.google.com/dialogflow/cx/docs/concept/handler#scope),
   * are included. - Other intents referenced by intent routes in scope that
   * matched end-user input, but had a lower confidence score.
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
   * If a DTMF was provided as input, this field will contain a copy of the
   * DtmfInput.
   *
   * @param GoogleCloudDialogflowCxV3DtmfInput $dtmf
   */
  public function setDtmf(GoogleCloudDialogflowCxV3DtmfInput $dtmf)
  {
    $this->dtmf = $dtmf;
  }
  /**
   * @return GoogleCloudDialogflowCxV3DtmfInput
   */
  public function getDtmf()
  {
    return $this->dtmf;
  }
  /**
   * The Intent that matched the conversational query. Some, not all fields are
   * filled in this message, including but not limited to: `name` and
   * `display_name`. This field is deprecated, please use QueryResult.match
   * instead.
   *
   * @deprecated
   * @param GoogleCloudDialogflowCxV3Intent $intent
   */
  public function setIntent(GoogleCloudDialogflowCxV3Intent $intent)
  {
    $this->intent = $intent;
  }
  /**
   * @deprecated
   * @return GoogleCloudDialogflowCxV3Intent
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
   * implementation. This field is deprecated, please use QueryResult.match
   * instead.
   *
   * @deprecated
   * @param float $intentDetectionConfidence
   */
  public function setIntentDetectionConfidence($intentDetectionConfidence)
  {
    $this->intentDetectionConfidence = $intentDetectionConfidence;
  }
  /**
   * @deprecated
   * @return float
   */
  public function getIntentDetectionConfidence()
  {
    return $this->intentDetectionConfidence;
  }
  /**
   * The language that was triggered during intent detection. See [Language
   * Support](https://cloud.google.com/dialogflow/cx/docs/reference/language)
   * for a list of the currently supported language codes.
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
   * Intent match result, could be an intent or an event.
   *
   * @param GoogleCloudDialogflowCxV3Match $match
   */
  public function setMatch(GoogleCloudDialogflowCxV3Match $match)
  {
    $this->match = $match;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Match
   */
  public function getMatch()
  {
    return $this->match;
  }
  /**
   * The collected session parameters. Depending on your protocol or client
   * library language, this is a map, associative array, symbol table,
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
   * The list of rich messages returned to the client. Responses vary from
   * simple text messages to more sophisticated, structured payloads used to
   * drive complex logic.
   *
   * @param GoogleCloudDialogflowCxV3ResponseMessage[] $responseMessages
   */
  public function setResponseMessages($responseMessages)
  {
    $this->responseMessages = $responseMessages;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ResponseMessage[]
   */
  public function getResponseMessages()
  {
    return $this->responseMessages;
  }
  /**
   * The sentiment analyss result, which depends on
   * `analyze_query_text_sentiment`, specified in the request.
   *
   * @param GoogleCloudDialogflowCxV3SentimentAnalysisResult $sentimentAnalysisResult
   */
  public function setSentimentAnalysisResult(GoogleCloudDialogflowCxV3SentimentAnalysisResult $sentimentAnalysisResult)
  {
    $this->sentimentAnalysisResult = $sentimentAnalysisResult;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SentimentAnalysisResult
   */
  public function getSentimentAnalysisResult()
  {
    return $this->sentimentAnalysisResult;
  }
  /**
   * If natural language text was provided as input, this field will contain a
   * copy of the text.
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
  /**
   * If natural language speech audio was provided as input, this field will
   * contain the transcript for the audio.
   *
   * @param string $transcript
   */
  public function setTranscript($transcript)
  {
    $this->transcript = $transcript;
  }
  /**
   * @return string
   */
  public function getTranscript()
  {
    return $this->transcript;
  }
  /**
   * If an event was provided as input, this field will contain the name of the
   * event.
   *
   * @param string $triggerEvent
   */
  public function setTriggerEvent($triggerEvent)
  {
    $this->triggerEvent = $triggerEvent;
  }
  /**
   * @return string
   */
  public function getTriggerEvent()
  {
    return $this->triggerEvent;
  }
  /**
   * If an intent was provided as input, this field will contain a copy of the
   * intent identifier. Format: `projects//locations//agents//intents/`.
   *
   * @param string $triggerIntent
   */
  public function setTriggerIntent($triggerIntent)
  {
    $this->triggerIntent = $triggerIntent;
  }
  /**
   * @return string
   */
  public function getTriggerIntent()
  {
    return $this->triggerIntent;
  }
  /**
   * The list of webhook payload in WebhookResponse.payload, in the order of
   * call sequence. If some webhook call fails or doesn't return any payload, an
   * empty `Struct` would be used instead.
   *
   * @param array[] $webhookPayloads
   */
  public function setWebhookPayloads($webhookPayloads)
  {
    $this->webhookPayloads = $webhookPayloads;
  }
  /**
   * @return array[]
   */
  public function getWebhookPayloads()
  {
    return $this->webhookPayloads;
  }
  /**
   * The list of webhook call status in the order of call sequence.
   *
   * @param GoogleRpcStatus[] $webhookStatuses
   */
  public function setWebhookStatuses($webhookStatuses)
  {
    $this->webhookStatuses = $webhookStatuses;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getWebhookStatuses()
  {
    return $this->webhookStatuses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3QueryResult::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3QueryResult');
