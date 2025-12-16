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

class GoogleCloudDiscoveryengineV1CompleteQueryResponse extends \Google\Collection
{
  protected $collection_key = 'querySuggestions';
  protected $querySuggestionsType = GoogleCloudDiscoveryengineV1CompleteQueryResponseQuerySuggestion::class;
  protected $querySuggestionsDataType = 'array';
  /**
   * True if the returned suggestions are all tail suggestions. For tail
   * matching to be triggered, include_tail_suggestions in the request must be
   * true and there must be no suggestions that match the full query.
   *
   * @var bool
   */
  public $tailMatchTriggered;

  /**
   * Results of the matched query suggestions. The result list is ordered and
   * the first result is a top suggestion.
   *
   * @param GoogleCloudDiscoveryengineV1CompleteQueryResponseQuerySuggestion[] $querySuggestions
   */
  public function setQuerySuggestions($querySuggestions)
  {
    $this->querySuggestions = $querySuggestions;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1CompleteQueryResponseQuerySuggestion[]
   */
  public function getQuerySuggestions()
  {
    return $this->querySuggestions;
  }
  /**
   * True if the returned suggestions are all tail suggestions. For tail
   * matching to be triggered, include_tail_suggestions in the request must be
   * true and there must be no suggestions that match the full query.
   *
   * @param bool $tailMatchTriggered
   */
  public function setTailMatchTriggered($tailMatchTriggered)
  {
    $this->tailMatchTriggered = $tailMatchTriggered;
  }
  /**
   * @return bool
   */
  public function getTailMatchTriggered()
  {
    return $this->tailMatchTriggered;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1CompleteQueryResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CompleteQueryResponse');
