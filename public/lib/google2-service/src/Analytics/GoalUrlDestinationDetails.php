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

namespace Google\Service\Analytics;

class GoalUrlDestinationDetails extends \Google\Collection
{
  protected $collection_key = 'steps';
  /**
   * Determines if the goal URL must exactly match the capitalization of visited
   * URLs.
   *
   * @var bool
   */
  public $caseSensitive;
  /**
   * Determines if the first step in this goal is required.
   *
   * @var bool
   */
  public $firstStepRequired;
  /**
   * Match type for the goal URL. Possible values are HEAD, EXACT, or REGEX.
   *
   * @var string
   */
  public $matchType;
  protected $stepsType = GoalUrlDestinationDetailsSteps::class;
  protected $stepsDataType = 'array';
  /**
   * URL for this goal.
   *
   * @var string
   */
  public $url;

  /**
   * Determines if the goal URL must exactly match the capitalization of visited
   * URLs.
   *
   * @param bool $caseSensitive
   */
  public function setCaseSensitive($caseSensitive)
  {
    $this->caseSensitive = $caseSensitive;
  }
  /**
   * @return bool
   */
  public function getCaseSensitive()
  {
    return $this->caseSensitive;
  }
  /**
   * Determines if the first step in this goal is required.
   *
   * @param bool $firstStepRequired
   */
  public function setFirstStepRequired($firstStepRequired)
  {
    $this->firstStepRequired = $firstStepRequired;
  }
  /**
   * @return bool
   */
  public function getFirstStepRequired()
  {
    return $this->firstStepRequired;
  }
  /**
   * Match type for the goal URL. Possible values are HEAD, EXACT, or REGEX.
   *
   * @param string $matchType
   */
  public function setMatchType($matchType)
  {
    $this->matchType = $matchType;
  }
  /**
   * @return string
   */
  public function getMatchType()
  {
    return $this->matchType;
  }
  /**
   * List of steps configured for this goal funnel.
   *
   * @param GoalUrlDestinationDetailsSteps[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return GoalUrlDestinationDetailsSteps[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
  /**
   * URL for this goal.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoalUrlDestinationDetails::class, 'Google_Service_Analytics_GoalUrlDestinationDetails');
