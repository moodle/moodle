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

class GoogleCloudDialogflowCxV3beta1WebhookRequest extends \Google\Collection
{
  protected $collection_key = 'messages';
  /**
   * Always present. The unique identifier of the DetectIntentResponse that will
   * be returned to the API caller.
   *
   * @var string
   */
  public $detectIntentResponseId;
  /**
   * If DTMF was provided as input, this field will contain the DTMF digits.
   *
   * @var string
   */
  public $dtmfDigits;
  protected $fulfillmentInfoType = GoogleCloudDialogflowCxV3beta1WebhookRequestFulfillmentInfo::class;
  protected $fulfillmentInfoDataType = '';
  protected $intentInfoType = GoogleCloudDialogflowCxV3beta1WebhookRequestIntentInfo::class;
  protected $intentInfoDataType = '';
  /**
   * The language code specified in the original request.
   *
   * @var string
   */
  public $languageCode;
  protected $languageInfoType = GoogleCloudDialogflowCxV3beta1LanguageInfo::class;
  protected $languageInfoDataType = '';
  protected $messagesType = GoogleCloudDialogflowCxV3beta1ResponseMessage::class;
  protected $messagesDataType = 'array';
  protected $pageInfoType = GoogleCloudDialogflowCxV3beta1PageInfo::class;
  protected $pageInfoDataType = '';
  /**
   * Custom data set in QueryParameters.payload.
   *
   * @var array[]
   */
  public $payload;
  protected $sentimentAnalysisResultType = GoogleCloudDialogflowCxV3beta1WebhookRequestSentimentAnalysisResult::class;
  protected $sentimentAnalysisResultDataType = '';
  protected $sessionInfoType = GoogleCloudDialogflowCxV3beta1SessionInfo::class;
  protected $sessionInfoDataType = '';
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
   * Always present. The unique identifier of the DetectIntentResponse that will
   * be returned to the API caller.
   *
   * @param string $detectIntentResponseId
   */
  public function setDetectIntentResponseId($detectIntentResponseId)
  {
    $this->detectIntentResponseId = $detectIntentResponseId;
  }
  /**
   * @return string
   */
  public function getDetectIntentResponseId()
  {
    return $this->detectIntentResponseId;
  }
  /**
   * If DTMF was provided as input, this field will contain the DTMF digits.
   *
   * @param string $dtmfDigits
   */
  public function setDtmfDigits($dtmfDigits)
  {
    $this->dtmfDigits = $dtmfDigits;
  }
  /**
   * @return string
   */
  public function getDtmfDigits()
  {
    return $this->dtmfDigits;
  }
  /**
   * Always present. Information about the fulfillment that triggered this
   * webhook call.
   *
   * @param GoogleCloudDialogflowCxV3beta1WebhookRequestFulfillmentInfo $fulfillmentInfo
   */
  public function setFulfillmentInfo(GoogleCloudDialogflowCxV3beta1WebhookRequestFulfillmentInfo $fulfillmentInfo)
  {
    $this->fulfillmentInfo = $fulfillmentInfo;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1WebhookRequestFulfillmentInfo
   */
  public function getFulfillmentInfo()
  {
    return $this->fulfillmentInfo;
  }
  /**
   * Information about the last matched intent.
   *
   * @param GoogleCloudDialogflowCxV3beta1WebhookRequestIntentInfo $intentInfo
   */
  public function setIntentInfo(GoogleCloudDialogflowCxV3beta1WebhookRequestIntentInfo $intentInfo)
  {
    $this->intentInfo = $intentInfo;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1WebhookRequestIntentInfo
   */
  public function getIntentInfo()
  {
    return $this->intentInfo;
  }
  /**
   * The language code specified in the original request.
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
   * Information about the language of the request.
   *
   * @param GoogleCloudDialogflowCxV3beta1LanguageInfo $languageInfo
   */
  public function setLanguageInfo(GoogleCloudDialogflowCxV3beta1LanguageInfo $languageInfo)
  {
    $this->languageInfo = $languageInfo;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1LanguageInfo
   */
  public function getLanguageInfo()
  {
    return $this->languageInfo;
  }
  /**
   * The list of rich message responses to present to the user. Webhook can
   * choose to append or replace this list in
   * WebhookResponse.fulfillment_response;
   *
   * @param GoogleCloudDialogflowCxV3beta1ResponseMessage[] $messages
   */
  public function setMessages($messages)
  {
    $this->messages = $messages;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1ResponseMessage[]
   */
  public function getMessages()
  {
    return $this->messages;
  }
  /**
   * Information about page status.
   *
   * @param GoogleCloudDialogflowCxV3beta1PageInfo $pageInfo
   */
  public function setPageInfo(GoogleCloudDialogflowCxV3beta1PageInfo $pageInfo)
  {
    $this->pageInfo = $pageInfo;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1PageInfo
   */
  public function getPageInfo()
  {
    return $this->pageInfo;
  }
  /**
   * Custom data set in QueryParameters.payload.
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
   * The sentiment analysis result of the current user request. The field is
   * filled when sentiment analysis is configured to be enabled for the request.
   *
   * @param GoogleCloudDialogflowCxV3beta1WebhookRequestSentimentAnalysisResult $sentimentAnalysisResult
   */
  public function setSentimentAnalysisResult(GoogleCloudDialogflowCxV3beta1WebhookRequestSentimentAnalysisResult $sentimentAnalysisResult)
  {
    $this->sentimentAnalysisResult = $sentimentAnalysisResult;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1WebhookRequestSentimentAnalysisResult
   */
  public function getSentimentAnalysisResult()
  {
    return $this->sentimentAnalysisResult;
  }
  /**
   * Information about session status.
   *
   * @param GoogleCloudDialogflowCxV3beta1SessionInfo $sessionInfo
   */
  public function setSessionInfo(GoogleCloudDialogflowCxV3beta1SessionInfo $sessionInfo)
  {
    $this->sessionInfo = $sessionInfo;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1SessionInfo
   */
  public function getSessionInfo()
  {
    return $this->sessionInfo;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1WebhookRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1WebhookRequest');
