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

class GoogleCloudDialogflowV2WebhookRequest extends \Google\Model
{
  protected $originalDetectIntentRequestType = GoogleCloudDialogflowV2OriginalDetectIntentRequest::class;
  protected $originalDetectIntentRequestDataType = '';
  protected $queryResultType = GoogleCloudDialogflowV2QueryResult::class;
  protected $queryResultDataType = '';
  /**
   * The unique identifier of the response. Contains the same value as
   * `[Streaming]DetectIntentResponse.response_id`.
   *
   * @var string
   */
  public $responseId;
  /**
   * The unique identifier of detectIntent request session. Can be used to
   * identify end-user inside webhook implementation. Format:
   * `projects//agent/sessions/`, or
   * `projects//agent/environments//users//sessions/`.
   *
   * @var string
   */
  public $session;

  /**
   * Optional. The contents of the original request that was passed to
   * `[Streaming]DetectIntent` call.
   *
   * @param GoogleCloudDialogflowV2OriginalDetectIntentRequest $originalDetectIntentRequest
   */
  public function setOriginalDetectIntentRequest(GoogleCloudDialogflowV2OriginalDetectIntentRequest $originalDetectIntentRequest)
  {
    $this->originalDetectIntentRequest = $originalDetectIntentRequest;
  }
  /**
   * @return GoogleCloudDialogflowV2OriginalDetectIntentRequest
   */
  public function getOriginalDetectIntentRequest()
  {
    return $this->originalDetectIntentRequest;
  }
  /**
   * The result of the conversational query or event processing. Contains the
   * same value as `[Streaming]DetectIntentResponse.query_result`.
   *
   * @param GoogleCloudDialogflowV2QueryResult $queryResult
   */
  public function setQueryResult(GoogleCloudDialogflowV2QueryResult $queryResult)
  {
    $this->queryResult = $queryResult;
  }
  /**
   * @return GoogleCloudDialogflowV2QueryResult
   */
  public function getQueryResult()
  {
    return $this->queryResult;
  }
  /**
   * The unique identifier of the response. Contains the same value as
   * `[Streaming]DetectIntentResponse.response_id`.
   *
   * @param string $responseId
   */
  public function setResponseId($responseId)
  {
    $this->responseId = $responseId;
  }
  /**
   * @return string
   */
  public function getResponseId()
  {
    return $this->responseId;
  }
  /**
   * The unique identifier of detectIntent request session. Can be used to
   * identify end-user inside webhook implementation. Format:
   * `projects//agent/sessions/`, or
   * `projects//agent/environments//users//sessions/`.
   *
   * @param string $session
   */
  public function setSession($session)
  {
    $this->session = $session;
  }
  /**
   * @return string
   */
  public function getSession()
  {
    return $this->session;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2WebhookRequest::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2WebhookRequest');
