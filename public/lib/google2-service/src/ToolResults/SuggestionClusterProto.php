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

namespace Google\Service\ToolResults;

class SuggestionClusterProto extends \Google\Collection
{
  public const CATEGORY_unknownCategory = 'unknownCategory';
  public const CATEGORY_contentLabeling = 'contentLabeling';
  public const CATEGORY_touchTargetSize = 'touchTargetSize';
  public const CATEGORY_lowContrast = 'lowContrast';
  public const CATEGORY_implementation = 'implementation';
  protected $collection_key = 'suggestions';
  /**
   * Category in which these types of suggestions should appear. Always set.
   *
   * @var string
   */
  public $category;
  protected $suggestionsType = SuggestionProto::class;
  protected $suggestionsDataType = 'array';

  /**
   * Category in which these types of suggestions should appear. Always set.
   *
   * Accepted values: unknownCategory, contentLabeling, touchTargetSize,
   * lowContrast, implementation
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * A sequence of suggestions. All of the suggestions within a cluster must
   * have the same SuggestionPriority and belong to the same SuggestionCategory.
   * Suggestions with the same screenshot URL should be adjacent.
   *
   * @param SuggestionProto[] $suggestions
   */
  public function setSuggestions($suggestions)
  {
    $this->suggestions = $suggestions;
  }
  /**
   * @return SuggestionProto[]
   */
  public function getSuggestions()
  {
    return $this->suggestions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SuggestionClusterProto::class, 'Google_Service_ToolResults_SuggestionClusterProto');
