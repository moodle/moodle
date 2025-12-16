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

class GoogleCloudDiscoveryengineV1SearchResponseSummarySafetyAttributes extends \Google\Collection
{
  protected $collection_key = 'scores';
  /**
   * The display names of Safety Attribute categories associated with the
   * generated content. Order matches the Scores.
   *
   * @var string[]
   */
  public $categories;
  /**
   * The confidence scores of the each category, higher value means higher
   * confidence. Order matches the Categories.
   *
   * @var float[]
   */
  public $scores;

  /**
   * The display names of Safety Attribute categories associated with the
   * generated content. Order matches the Scores.
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * The confidence scores of the each category, higher value means higher
   * confidence. Order matches the Categories.
   *
   * @param float[] $scores
   */
  public function setScores($scores)
  {
    $this->scores = $scores;
  }
  /**
   * @return float[]
   */
  public function getScores()
  {
    return $this->scores;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchResponseSummarySafetyAttributes::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchResponseSummarySafetyAttributes');
