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

class GoogleCloudDialogflowCxV3beta1TurnSignals extends \Google\Collection
{
  protected $collection_key = 'webhookStatuses';
  /**
   * Whether agent responded with LiveAgentHandoff fulfillment.
   *
   * @var bool
   */
  public $agentEscalated;
  /**
   * Whether user was using DTMF input.
   *
   * @var bool
   */
  public $dtmfUsed;
  /**
   * Failure reasons of the turn.
   *
   * @var string[]
   */
  public $failureReasons;
  /**
   * Whether NLU predicted NO_MATCH.
   *
   * @var bool
   */
  public $noMatch;
  /**
   * Whether user provided no input.
   *
   * @var bool
   */
  public $noUserInput;
  /**
   * Whether turn resulted in End Session page.
   *
   * @var bool
   */
  public $reachedEndPage;
  /**
   * Sentiment magnitude of the user utterance if
   * [sentiment](https://cloud.google.com/dialogflow/cx/docs/concept/sentiment)
   * was enabled.
   *
   * @var float
   */
  public $sentimentMagnitude;
  /**
   * Sentiment score of the user utterance if
   * [sentiment](https://cloud.google.com/dialogflow/cx/docs/concept/sentiment)
   * was enabled.
   *
   * @var float
   */
  public $sentimentScore;
  /**
   * Whether user was specifically asking for a live agent.
   *
   * @var bool
   */
  public $userEscalated;
  /**
   * Human-readable statuses of the webhooks triggered during this turn.
   *
   * @var string[]
   */
  public $webhookStatuses;

  /**
   * Whether agent responded with LiveAgentHandoff fulfillment.
   *
   * @param bool $agentEscalated
   */
  public function setAgentEscalated($agentEscalated)
  {
    $this->agentEscalated = $agentEscalated;
  }
  /**
   * @return bool
   */
  public function getAgentEscalated()
  {
    return $this->agentEscalated;
  }
  /**
   * Whether user was using DTMF input.
   *
   * @param bool $dtmfUsed
   */
  public function setDtmfUsed($dtmfUsed)
  {
    $this->dtmfUsed = $dtmfUsed;
  }
  /**
   * @return bool
   */
  public function getDtmfUsed()
  {
    return $this->dtmfUsed;
  }
  /**
   * Failure reasons of the turn.
   *
   * @param string[] $failureReasons
   */
  public function setFailureReasons($failureReasons)
  {
    $this->failureReasons = $failureReasons;
  }
  /**
   * @return string[]
   */
  public function getFailureReasons()
  {
    return $this->failureReasons;
  }
  /**
   * Whether NLU predicted NO_MATCH.
   *
   * @param bool $noMatch
   */
  public function setNoMatch($noMatch)
  {
    $this->noMatch = $noMatch;
  }
  /**
   * @return bool
   */
  public function getNoMatch()
  {
    return $this->noMatch;
  }
  /**
   * Whether user provided no input.
   *
   * @param bool $noUserInput
   */
  public function setNoUserInput($noUserInput)
  {
    $this->noUserInput = $noUserInput;
  }
  /**
   * @return bool
   */
  public function getNoUserInput()
  {
    return $this->noUserInput;
  }
  /**
   * Whether turn resulted in End Session page.
   *
   * @param bool $reachedEndPage
   */
  public function setReachedEndPage($reachedEndPage)
  {
    $this->reachedEndPage = $reachedEndPage;
  }
  /**
   * @return bool
   */
  public function getReachedEndPage()
  {
    return $this->reachedEndPage;
  }
  /**
   * Sentiment magnitude of the user utterance if
   * [sentiment](https://cloud.google.com/dialogflow/cx/docs/concept/sentiment)
   * was enabled.
   *
   * @param float $sentimentMagnitude
   */
  public function setSentimentMagnitude($sentimentMagnitude)
  {
    $this->sentimentMagnitude = $sentimentMagnitude;
  }
  /**
   * @return float
   */
  public function getSentimentMagnitude()
  {
    return $this->sentimentMagnitude;
  }
  /**
   * Sentiment score of the user utterance if
   * [sentiment](https://cloud.google.com/dialogflow/cx/docs/concept/sentiment)
   * was enabled.
   *
   * @param float $sentimentScore
   */
  public function setSentimentScore($sentimentScore)
  {
    $this->sentimentScore = $sentimentScore;
  }
  /**
   * @return float
   */
  public function getSentimentScore()
  {
    return $this->sentimentScore;
  }
  /**
   * Whether user was specifically asking for a live agent.
   *
   * @param bool $userEscalated
   */
  public function setUserEscalated($userEscalated)
  {
    $this->userEscalated = $userEscalated;
  }
  /**
   * @return bool
   */
  public function getUserEscalated()
  {
    return $this->userEscalated;
  }
  /**
   * Human-readable statuses of the webhooks triggered during this turn.
   *
   * @param string[] $webhookStatuses
   */
  public function setWebhookStatuses($webhookStatuses)
  {
    $this->webhookStatuses = $webhookStatuses;
  }
  /**
   * @return string[]
   */
  public function getWebhookStatuses()
  {
    return $this->webhookStatuses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1TurnSignals::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1TurnSignals');
