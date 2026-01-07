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

class GoogleCloudDialogflowCxV3ConversationTurnVirtualAgentOutput extends \Google\Collection
{
  protected $collection_key = 'textResponses';
  protected $currentPageType = GoogleCloudDialogflowCxV3Page::class;
  protected $currentPageDataType = '';
  /**
   * Required. Input only. The diagnostic info output for the turn. Required to
   * calculate the testing coverage.
   *
   * @var array[]
   */
  public $diagnosticInfo;
  protected $differencesType = GoogleCloudDialogflowCxV3TestRunDifference::class;
  protected $differencesDataType = 'array';
  /**
   * The session parameters available to the bot at this point.
   *
   * @var array[]
   */
  public $sessionParameters;
  protected $statusType = GoogleRpcStatus::class;
  protected $statusDataType = '';
  protected $textResponsesType = GoogleCloudDialogflowCxV3ResponseMessageText::class;
  protected $textResponsesDataType = 'array';
  protected $triggeredIntentType = GoogleCloudDialogflowCxV3Intent::class;
  protected $triggeredIntentDataType = '';

  /**
   * The Page on which the utterance was spoken. Only name and displayName will
   * be set.
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
   * Required. Input only. The diagnostic info output for the turn. Required to
   * calculate the testing coverage.
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
   * Output only. If this is part of a result conversation turn, the list of
   * differences between the original run and the replay for this output, if
   * any.
   *
   * @param GoogleCloudDialogflowCxV3TestRunDifference[] $differences
   */
  public function setDifferences($differences)
  {
    $this->differences = $differences;
  }
  /**
   * @return GoogleCloudDialogflowCxV3TestRunDifference[]
   */
  public function getDifferences()
  {
    return $this->differences;
  }
  /**
   * The session parameters available to the bot at this point.
   *
   * @param array[] $sessionParameters
   */
  public function setSessionParameters($sessionParameters)
  {
    $this->sessionParameters = $sessionParameters;
  }
  /**
   * @return array[]
   */
  public function getSessionParameters()
  {
    return $this->sessionParameters;
  }
  /**
   * Response error from the agent in the test result. If set, other output is
   * empty.
   *
   * @param GoogleRpcStatus $status
   */
  public function setStatus(GoogleRpcStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The text responses from the agent for the turn.
   *
   * @param GoogleCloudDialogflowCxV3ResponseMessageText[] $textResponses
   */
  public function setTextResponses($textResponses)
  {
    $this->textResponses = $textResponses;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ResponseMessageText[]
   */
  public function getTextResponses()
  {
    return $this->textResponses;
  }
  /**
   * The Intent that triggered the response. Only name and displayName will be
   * set.
   *
   * @param GoogleCloudDialogflowCxV3Intent $triggeredIntent
   */
  public function setTriggeredIntent(GoogleCloudDialogflowCxV3Intent $triggeredIntent)
  {
    $this->triggeredIntent = $triggeredIntent;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Intent
   */
  public function getTriggeredIntent()
  {
    return $this->triggeredIntent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ConversationTurnVirtualAgentOutput::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ConversationTurnVirtualAgentOutput');
