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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaSessionTurn extends \Google\Model
{
  /**
   * Optional. The resource name of the answer to the user query. Only set if
   * the answer generation (/answer API call) happened in this turn.
   *
   * @var string
   */
  public $answer;
  protected $detailedAnswerType = GoogleCloudDiscoveryengineV1alphaAnswer::class;
  protected $detailedAnswerDataType = '';
  protected $detailedAssistAnswerType = GoogleCloudDiscoveryengineV1alphaAssistAnswer::class;
  protected $detailedAssistAnswerDataType = '';
  protected $queryType = GoogleCloudDiscoveryengineV1alphaQuery::class;
  protected $queryDataType = '';
  /**
   * Optional. Represents metadata related to the query config, for example LLM
   * model and version used, model parameters (temperature, grounding
   * parameters, etc.). The prefix "google." is reserved for Google-developed
   * functionality.
   *
   * @var string[]
   */
  public $queryConfig;

  /**
   * Optional. The resource name of the answer to the user query. Only set if
   * the answer generation (/answer API call) happened in this turn.
   *
   * @param string $answer
   */
  public function setAnswer($answer)
  {
    $this->answer = $answer;
  }
  /**
   * @return string
   */
  public function getAnswer()
  {
    return $this->answer;
  }
  /**
   * Output only. In ConversationalSearchService.GetSession API, if
   * GetSessionRequest.include_answer_details is set to true, this field will be
   * populated when getting answer query session.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAnswer $detailedAnswer
   */
  public function setDetailedAnswer(GoogleCloudDiscoveryengineV1alphaAnswer $detailedAnswer)
  {
    $this->detailedAnswer = $detailedAnswer;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAnswer
   */
  public function getDetailedAnswer()
  {
    return $this->detailedAnswer;
  }
  /**
   * Output only. In ConversationalSearchService.GetSession API, if
   * GetSessionRequest.include_answer_details is set to true, this field will be
   * populated when getting assistant session.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAssistAnswer $detailedAssistAnswer
   */
  public function setDetailedAssistAnswer(GoogleCloudDiscoveryengineV1alphaAssistAnswer $detailedAssistAnswer)
  {
    $this->detailedAssistAnswer = $detailedAssistAnswer;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAssistAnswer
   */
  public function getDetailedAssistAnswer()
  {
    return $this->detailedAssistAnswer;
  }
  /**
   * Optional. The user query. May not be set if this turn is merely
   * regenerating an answer to a different turn
   *
   * @param GoogleCloudDiscoveryengineV1alphaQuery $query
   */
  public function setQuery(GoogleCloudDiscoveryengineV1alphaQuery $query)
  {
    $this->query = $query;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaQuery
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Optional. Represents metadata related to the query config, for example LLM
   * model and version used, model parameters (temperature, grounding
   * parameters, etc.). The prefix "google." is reserved for Google-developed
   * functionality.
   *
   * @param string[] $queryConfig
   */
  public function setQueryConfig($queryConfig)
  {
    $this->queryConfig = $queryConfig;
  }
  /**
   * @return string[]
   */
  public function getQueryConfig()
  {
    return $this->queryConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaSessionTurn::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaSessionTurn');
