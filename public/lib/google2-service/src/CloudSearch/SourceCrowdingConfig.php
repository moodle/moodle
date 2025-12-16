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

class SourceCrowdingConfig extends \Google\Model
{
  /**
   * Maximum number of results allowed from a datasource in a result page as
   * long as results from other sources are not exhausted. Value specified must
   * not be negative. A default value is used if this value is equal to 0. To
   * disable crowding, set the value greater than 100.
   *
   * @var int
   */
  public $numResults;
  /**
   * Maximum number of suggestions allowed from a source. No limits will be set
   * on results if this value is less than or equal to 0.
   *
   * @var int
   */
  public $numSuggestions;

  /**
   * Maximum number of results allowed from a datasource in a result page as
   * long as results from other sources are not exhausted. Value specified must
   * not be negative. A default value is used if this value is equal to 0. To
   * disable crowding, set the value greater than 100.
   *
   * @param int $numResults
   */
  public function setNumResults($numResults)
  {
    $this->numResults = $numResults;
  }
  /**
   * @return int
   */
  public function getNumResults()
  {
    return $this->numResults;
  }
  /**
   * Maximum number of suggestions allowed from a source. No limits will be set
   * on results if this value is less than or equal to 0.
   *
   * @param int $numSuggestions
   */
  public function setNumSuggestions($numSuggestions)
  {
    $this->numSuggestions = $numSuggestions;
  }
  /**
   * @return int
   */
  public function getNumSuggestions()
  {
    return $this->numSuggestions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceCrowdingConfig::class, 'Google_Service_CloudSearch_SourceCrowdingConfig');
