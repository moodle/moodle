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

class GoogleCloudMlV1SuggestTrialsMetadata extends \Google\Model
{
  /**
   * The identifier of the client that is requesting the suggestion.
   *
   * @var string
   */
  public $clientId;
  /**
   * The time operation was submitted.
   *
   * @var string
   */
  public $createTime;
  /**
   * The name of the study that the trial belongs to.
   *
   * @var string
   */
  public $study;
  /**
   * The number of suggestions requested.
   *
   * @var int
   */
  public $suggestionCount;

  /**
   * The identifier of the client that is requesting the suggestion.
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
   * The time operation was submitted.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The name of the study that the trial belongs to.
   *
   * @param string $study
   */
  public function setStudy($study)
  {
    $this->study = $study;
  }
  /**
   * @return string
   */
  public function getStudy()
  {
    return $this->study;
  }
  /**
   * The number of suggestions requested.
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
class_alias(GoogleCloudMlV1SuggestTrialsMetadata::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1SuggestTrialsMetadata');
