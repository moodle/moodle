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

class GoogleCloudDialogflowCxV3WebhookResponse extends \Google\Model
{
  protected $fulfillmentResponseType = GoogleCloudDialogflowCxV3WebhookResponseFulfillmentResponse::class;
  protected $fulfillmentResponseDataType = '';
  protected $pageInfoType = GoogleCloudDialogflowCxV3PageInfo::class;
  protected $pageInfoDataType = '';
  /**
   * Value to append directly to QueryResult.webhook_payloads.
   *
   * @var array[]
   */
  public $payload;
  protected $sessionInfoType = GoogleCloudDialogflowCxV3SessionInfo::class;
  protected $sessionInfoDataType = '';
  /**
   * The target flow to transition to. Format:
   * `projects//locations//agents//flows/`.
   *
   * @var string
   */
  public $targetFlow;
  /**
   * The target page to transition to. Format:
   * `projects//locations//agents//flows//pages/`.
   *
   * @var string
   */
  public $targetPage;

  /**
   * The fulfillment response to send to the user. This field can be omitted by
   * the webhook if it does not intend to send any response to the user.
   *
   * @param GoogleCloudDialogflowCxV3WebhookResponseFulfillmentResponse $fulfillmentResponse
   */
  public function setFulfillmentResponse(GoogleCloudDialogflowCxV3WebhookResponseFulfillmentResponse $fulfillmentResponse)
  {
    $this->fulfillmentResponse = $fulfillmentResponse;
  }
  /**
   * @return GoogleCloudDialogflowCxV3WebhookResponseFulfillmentResponse
   */
  public function getFulfillmentResponse()
  {
    return $this->fulfillmentResponse;
  }
  /**
   * Information about page status. This field can be omitted by the webhook if
   * it does not intend to modify page status.
   *
   * @param GoogleCloudDialogflowCxV3PageInfo $pageInfo
   */
  public function setPageInfo(GoogleCloudDialogflowCxV3PageInfo $pageInfo)
  {
    $this->pageInfo = $pageInfo;
  }
  /**
   * @return GoogleCloudDialogflowCxV3PageInfo
   */
  public function getPageInfo()
  {
    return $this->pageInfo;
  }
  /**
   * Value to append directly to QueryResult.webhook_payloads.
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
   * Information about session status. This field can be omitted by the webhook
   * if it does not intend to modify session status.
   *
   * @param GoogleCloudDialogflowCxV3SessionInfo $sessionInfo
   */
  public function setSessionInfo(GoogleCloudDialogflowCxV3SessionInfo $sessionInfo)
  {
    $this->sessionInfo = $sessionInfo;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SessionInfo
   */
  public function getSessionInfo()
  {
    return $this->sessionInfo;
  }
  /**
   * The target flow to transition to. Format:
   * `projects//locations//agents//flows/`.
   *
   * @param string $targetFlow
   */
  public function setTargetFlow($targetFlow)
  {
    $this->targetFlow = $targetFlow;
  }
  /**
   * @return string
   */
  public function getTargetFlow()
  {
    return $this->targetFlow;
  }
  /**
   * The target page to transition to. Format:
   * `projects//locations//agents//flows//pages/`.
   *
   * @param string $targetPage
   */
  public function setTargetPage($targetPage)
  {
    $this->targetPage = $targetPage;
  }
  /**
   * @return string
   */
  public function getTargetPage()
  {
    return $this->targetPage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3WebhookResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3WebhookResponse');
