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

class SuggestResult extends \Google\Model
{
  protected $peopleSuggestionType = PeopleSuggestion::class;
  protected $peopleSuggestionDataType = '';
  protected $querySuggestionType = QuerySuggestion::class;
  protected $querySuggestionDataType = '';
  protected $sourceType = Source::class;
  protected $sourceDataType = '';
  /**
   * The suggested query that will be used for search, when the user clicks on
   * the suggestion
   *
   * @var string
   */
  public $suggestedQuery;

  /**
   * This is present when the suggestion indicates a person. It contains more
   * information about the person - like their email ID, name etc.
   *
   * @param PeopleSuggestion $peopleSuggestion
   */
  public function setPeopleSuggestion(PeopleSuggestion $peopleSuggestion)
  {
    $this->peopleSuggestion = $peopleSuggestion;
  }
  /**
   * @return PeopleSuggestion
   */
  public function getPeopleSuggestion()
  {
    return $this->peopleSuggestion;
  }
  /**
   * This field will be present if the suggested query is a word/phrase
   * completion.
   *
   * @param QuerySuggestion $querySuggestion
   */
  public function setQuerySuggestion(QuerySuggestion $querySuggestion)
  {
    $this->querySuggestion = $querySuggestion;
  }
  /**
   * @return QuerySuggestion
   */
  public function getQuerySuggestion()
  {
    return $this->querySuggestion;
  }
  /**
   * The source of the suggestion.
   *
   * @param Source $source
   */
  public function setSource(Source $source)
  {
    $this->source = $source;
  }
  /**
   * @return Source
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * The suggested query that will be used for search, when the user clicks on
   * the suggestion
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SuggestResult::class, 'Google_Service_CloudSearch_SuggestResult');
