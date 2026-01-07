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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1GeneratorSuggestion extends \Google\Model
{
  protected $agentCoachingSuggestionType = GoogleCloudContactcenterinsightsV1AgentCoachingSuggestion::class;
  protected $agentCoachingSuggestionDataType = '';
  protected $freeFormSuggestionType = GoogleCloudContactcenterinsightsV1FreeFormSuggestion::class;
  protected $freeFormSuggestionDataType = '';
  protected $summarySuggestionType = GoogleCloudContactcenterinsightsV1SummarySuggestion::class;
  protected $summarySuggestionDataType = '';

  /**
   * @param GoogleCloudContactcenterinsightsV1AgentCoachingSuggestion
   */
  public function setAgentCoachingSuggestion(GoogleCloudContactcenterinsightsV1AgentCoachingSuggestion $agentCoachingSuggestion)
  {
    $this->agentCoachingSuggestion = $agentCoachingSuggestion;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1AgentCoachingSuggestion
   */
  public function getAgentCoachingSuggestion()
  {
    return $this->agentCoachingSuggestion;
  }
  /**
   * @param GoogleCloudContactcenterinsightsV1FreeFormSuggestion
   */
  public function setFreeFormSuggestion(GoogleCloudContactcenterinsightsV1FreeFormSuggestion $freeFormSuggestion)
  {
    $this->freeFormSuggestion = $freeFormSuggestion;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1FreeFormSuggestion
   */
  public function getFreeFormSuggestion()
  {
    return $this->freeFormSuggestion;
  }
  /**
   * @param GoogleCloudContactcenterinsightsV1SummarySuggestion
   */
  public function setSummarySuggestion(GoogleCloudContactcenterinsightsV1SummarySuggestion $summarySuggestion)
  {
    $this->summarySuggestion = $summarySuggestion;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1SummarySuggestion
   */
  public function getSummarySuggestion()
  {
    return $this->summarySuggestion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1GeneratorSuggestion::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1GeneratorSuggestion');
