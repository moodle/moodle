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

class GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestSuggestionTypeSpec extends \Google\Model
{
  /**
   * Default value.
   */
  public const SUGGESTION_TYPE_SUGGESTION_TYPE_UNSPECIFIED = 'SUGGESTION_TYPE_UNSPECIFIED';
  /**
   * Returns query suggestions.
   */
  public const SUGGESTION_TYPE_QUERY = 'QUERY';
  /**
   * Returns people suggestions.
   */
  public const SUGGESTION_TYPE_PEOPLE = 'PEOPLE';
  /**
   * Returns content suggestions.
   */
  public const SUGGESTION_TYPE_CONTENT = 'CONTENT';
  /**
   * Returns recent search suggestions.
   */
  public const SUGGESTION_TYPE_RECENT_SEARCH = 'RECENT_SEARCH';
  /**
   * Returns Google Workspace suggestions.
   */
  public const SUGGESTION_TYPE_GOOGLE_WORKSPACE = 'GOOGLE_WORKSPACE';
  /**
   * Optional. Maximum number of suggestions to return for each suggestion type.
   *
   * @var int
   */
  public $maxSuggestions;
  /**
   * Optional. Suggestion type.
   *
   * @var string
   */
  public $suggestionType;

  /**
   * Optional. Maximum number of suggestions to return for each suggestion type.
   *
   * @param int $maxSuggestions
   */
  public function setMaxSuggestions($maxSuggestions)
  {
    $this->maxSuggestions = $maxSuggestions;
  }
  /**
   * @return int
   */
  public function getMaxSuggestions()
  {
    return $this->maxSuggestions;
  }
  /**
   * Optional. Suggestion type.
   *
   * Accepted values: SUGGESTION_TYPE_UNSPECIFIED, QUERY, PEOPLE, CONTENT,
   * RECENT_SEARCH, GOOGLE_WORKSPACE
   *
   * @param self::SUGGESTION_TYPE_* $suggestionType
   */
  public function setSuggestionType($suggestionType)
  {
    $this->suggestionType = $suggestionType;
  }
  /**
   * @return self::SUGGESTION_TYPE_*
   */
  public function getSuggestionType()
  {
    return $this->suggestionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestSuggestionTypeSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AdvancedCompleteQueryRequestSuggestionTypeSpec');
