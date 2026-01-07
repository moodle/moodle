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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1SuggestTrialsRequest extends \Google\Model
{
  /**
   * Required. The identifier of the client that is requesting the suggestion.
   * If multiple SuggestTrialsRequests have the same `client_id`, the service
   * will return the identical suggested trial if the trial is pending, and
   * provide a new trial if the last suggested trial was completed.
   *
   * @var string
   */
  public $clientId;
  /**
   * Required. The number of suggestions requested.
   *
   * @var int
   */
  public $suggestionCount;

  /**
   * Required. The identifier of the client that is requesting the suggestion.
   * If multiple SuggestTrialsRequests have the same `client_id`, the service
   * will return the identical suggested trial if the trial is pending, and
   * provide a new trial if the last suggested trial was completed.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Required. The number of suggestions requested.
   *
   * @param int $suggestionCount
   */
  public function setSuggestionCount($suggestionCount)
  {
    $this->suggestionCount = $suggestionCount;
  }
  /**
   * @return int
   */
  public function getSuggestionCount()
  {
    return $this->suggestionCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1SuggestTrialsRequest::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1SuggestTrialsRequest');
