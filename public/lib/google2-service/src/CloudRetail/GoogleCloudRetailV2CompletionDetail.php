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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2CompletionDetail extends \Google\Model
{
  /**
   * Completion attribution token in CompleteQueryResponse.attribution_token.
   *
   * @var string
   */
  public $completionAttributionToken;
  /**
   * End user selected CompleteQueryResponse.CompletionResult.suggestion
   * position, starting from 0.
   *
   * @var int
   */
  public $selectedPosition;
  /**
   * End user selected CompleteQueryResponse.CompletionResult.suggestion.
   *
   * @var string
   */
  public $selectedSuggestion;

  /**
   * Completion attribution token in CompleteQueryResponse.attribution_token.
   *
   * @param string $completionAttributionToken
   */
  public function setCompletionAttributionToken($completionAttributionToken)
  {
    $this->completionAttributionToken = $completionAttributionToken;
  }
  /**
   * @return string
   */
  public function getCompletionAttributionToken()
  {
    return $this->completionAttributionToken;
  }
  /**
   * End user selected CompleteQueryResponse.CompletionResult.suggestion
   * position, starting from 0.
   *
   * @param int $selectedPosition
   */
  public function setSelectedPosition($selectedPosition)
  {
    $this->selectedPosition = $selectedPosition;
  }
  /**
   * @return int
   */
  public function getSelectedPosition()
  {
    return $this->selectedPosition;
  }
  /**
   * End user selected CompleteQueryResponse.CompletionResult.suggestion.
   *
   * @param string $selectedSuggestion
   */
  public function setSelectedSuggestion($selectedSuggestion)
  {
    $this->selectedSuggestion = $selectedSuggestion;
  }
  /**
   * @return string
   */
  public function getSelectedSuggestion()
  {
    return $this->selectedSuggestion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2CompletionDetail::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CompletionDetail');
