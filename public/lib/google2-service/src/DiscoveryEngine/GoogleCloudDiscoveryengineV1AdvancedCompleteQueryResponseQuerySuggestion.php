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

class GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponseQuerySuggestion extends \Google\Collection
{
  protected $collection_key = 'dataStore';
  /**
   * The unique document field paths that serve as the source of this suggestion
   * if it was generated from completable fields. This field is only populated
   * for the document-completable model.
   *
   * @var string[]
   */
  public $completableFieldPaths;
  /**
   * The name of the dataStore that this suggestion belongs to.
   *
   * @var string[]
   */
  public $dataStore;
  /**
   * The score of each suggestion. The score is in the range of [0, 1].
   *
   * @var 
   */
  public $score;
  /**
   * The suggestion for the query.
   *
   * @var string
   */
  public $suggestion;

  /**
   * The unique document field paths that serve as the source of this suggestion
   * if it was generated from completable fields. This field is only populated
   * for the document-completable model.
   *
   * @param string[] $completableFieldPaths
   */
  public function setCompletableFieldPaths($completableFieldPaths)
  {
    $this->completableFieldPaths = $completableFieldPaths;
  }
  /**
   * @return string[]
   */
  public function getCompletableFieldPaths()
  {
    return $this->completableFieldPaths;
  }
  /**
   * The name of the dataStore that this suggestion belongs to.
   *
   * @param string[] $dataStore
   */
  public function setDataStore($dataStore)
  {
    $this->dataStore = $dataStore;
  }
  /**
   * @return string[]
   */
  public function getDataStore()
  {
    return $this->dataStore;
  }
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * The suggestion for the query.
   *
   * @param string $suggestion
   */
  public function setSuggestion($suggestion)
  {
    $this->suggestion = $suggestion;
  }
  /**
   * @return string
   */
  public function getSuggestion()
  {
    return $this->suggestion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponseQuerySuggestion::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AdvancedCompleteQueryResponseQuerySuggestion');
