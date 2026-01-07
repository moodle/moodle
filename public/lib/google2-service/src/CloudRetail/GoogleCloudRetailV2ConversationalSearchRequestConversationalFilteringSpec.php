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

class GoogleCloudRetailV2ConversationalSearchRequestConversationalFilteringSpec extends \Google\Model
{
  /**
   * Default value.
   */
  public const CONVERSATIONAL_FILTERING_MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Disables Conversational Filtering when using Conversational Search.
   */
  public const CONVERSATIONAL_FILTERING_MODE_DISABLED = 'DISABLED';
  /**
   * Enables Conversational Filtering when using Conversational Search.
   */
  public const CONVERSATIONAL_FILTERING_MODE_ENABLED = 'ENABLED';
  /**
   * Enables Conversational Filtering without Conversational Search.
   */
  public const CONVERSATIONAL_FILTERING_MODE_CONVERSATIONAL_FILTER_ONLY = 'CONVERSATIONAL_FILTER_ONLY';
  /**
   * Optional. Mode to control Conversational Filtering. Defaults to
   * Mode.DISABLED if it's unset.
   *
   * @var string
   */
  public $conversationalFilteringMode;
  /**
   * Optional. This field is deprecated. Please use
   * ConversationalFilteringSpec.conversational_filtering_mode instead.
   *
   * @deprecated
   * @var bool
   */
  public $enableConversationalFiltering;
  protected $userAnswerType = GoogleCloudRetailV2ConversationalSearchRequestUserAnswer::class;
  protected $userAnswerDataType = '';

  /**
   * Optional. Mode to control Conversational Filtering. Defaults to
   * Mode.DISABLED if it's unset.
   *
   * Accepted values: MODE_UNSPECIFIED, DISABLED, ENABLED,
   * CONVERSATIONAL_FILTER_ONLY
   *
   * @param self::CONVERSATIONAL_FILTERING_MODE_* $conversationalFilteringMode
   */
  public function setConversationalFilteringMode($conversationalFilteringMode)
  {
    $this->conversationalFilteringMode = $conversationalFilteringMode;
  }
  /**
   * @return self::CONVERSATIONAL_FILTERING_MODE_*
   */
  public function getConversationalFilteringMode()
  {
    return $this->conversationalFilteringMode;
  }
  /**
   * Optional. This field is deprecated. Please use
   * ConversationalFilteringSpec.conversational_filtering_mode instead.
   *
   * @deprecated
   * @param bool $enableConversationalFiltering
   */
  public function setEnableConversationalFiltering($enableConversationalFiltering)
  {
    $this->enableConversationalFiltering = $enableConversationalFiltering;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableConversationalFiltering()
  {
    return $this->enableConversationalFiltering;
  }
  /**
   * Optional. This field specifies the current user answer during the
   * conversational filtering search. It can be either user selected from
   * suggested answers or user input plain text.
   *
   * @param GoogleCloudRetailV2ConversationalSearchRequestUserAnswer $userAnswer
   */
  public function setUserAnswer(GoogleCloudRetailV2ConversationalSearchRequestUserAnswer $userAnswer)
  {
    $this->userAnswer = $userAnswer;
  }
  /**
   * @return GoogleCloudRetailV2ConversationalSearchRequestUserAnswer
   */
  public function getUserAnswer()
  {
    return $this->userAnswer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ConversationalSearchRequestConversationalFilteringSpec::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ConversationalSearchRequestConversationalFilteringSpec');
