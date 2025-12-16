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

namespace Google\Service\CloudSearch;

class SpellResult extends \Google\Model
{
  /**
   * Default spell check type
   */
  public const SUGGESTION_TYPE_SUGGESTION_TYPE_UNSPECIFIED = 'SUGGESTION_TYPE_UNSPECIFIED';
  /**
   * Spell suggestion without any results changed. The results are still shown
   * for the original query (which has non zero / results) with a suggestion for
   * spelling that would have results.
   */
  public const SUGGESTION_TYPE_NON_EMPTY_RESULTS_SPELL_SUGGESTION = 'NON_EMPTY_RESULTS_SPELL_SUGGESTION';
  /**
   * Spell suggestion triggered when original query has no results. When the
   * original query has no results, and spell suggestion has results we trigger
   * results for the spell corrected query.
   */
  public const SUGGESTION_TYPE_ZERO_RESULTS_FULL_PAGE_REPLACEMENT = 'ZERO_RESULTS_FULL_PAGE_REPLACEMENT';
  /**
   * The suggested spelling of the query.
   *
   * @var string
   */
  public $suggestedQuery;
  protected $suggestedQueryHtmlType = SafeHtmlProto::class;
  protected $suggestedQueryHtmlDataType = '';
  /**
   * Suggestion triggered for the current query.
   *
   * @var string
   */
  public $suggestionType;

  /**
   * The suggested spelling of the query.
   *
   * @param string $suggestedQuery
   */
  public function setSuggestedQuery($suggestedQuery)
  {
    $this->suggestedQuery = $suggestedQuery;
  }
  /**
   * @return string
   */
  public function getSuggestedQuery()
  {
    return $this->suggestedQuery;
  }
  /**
   * The sanitized HTML representing the spell corrected query that can be used
   * in the UI. This usually has language-specific tags to mark up parts of the
   * query that are spell checked.
   *
   * @param SafeHtmlProto $suggestedQueryHtml
   */
  public function setSuggestedQueryHtml(SafeHtmlProto $suggestedQueryHtml)
  {
    $this->suggestedQueryHtml = $suggestedQueryHtml;
  }
  /**
   * @return SafeHtmlProto
   */
  public function getSuggestedQueryHtml()
  {
    return $this->suggestedQueryHtml;
  }
  /**
   * Suggestion triggered for the current query.
   *
   * Accepted values: SUGGESTION_TYPE_UNSPECIFIED,
   * NON_EMPTY_RESULTS_SPELL_SUGGESTION, ZERO_RESULTS_FULL_PAGE_REPLACEMENT
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
class_alias(SpellResult::class, 'Google_Service_CloudSearch_SpellResult');
