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

class ObjectOptions extends \Google\Collection
{
  protected $collection_key = 'suggestionFilteringOperators';
  protected $displayOptionsType = ObjectDisplayOptions::class;
  protected $displayOptionsDataType = '';
  protected $freshnessOptionsType = FreshnessOptions::class;
  protected $freshnessOptionsDataType = '';
  /**
   * Operators that can be used to filter suggestions. For Suggest API, only
   * operators mentioned here will be honored in the FilterOptions. Only TEXT
   * and ENUM operators are supported. NOTE: "objecttype", "type" and "mimetype"
   * are already supported. This property is to configure schema specific
   * operators. Even though this is an array, only one operator can be
   * specified. This is an array for future extensibility. Operators mapping to
   * multiple properties within the same object are not supported. If the
   * operator spans across different object types, this option has to be set
   * once for each object definition.
   *
   * @var string[]
   */
  public $suggestionFilteringOperators;

  /**
   * The options that determine how the object is displayed in the Cloud Search
   * results page.
   *
   * @param ObjectDisplayOptions $displayOptions
   */
  public function setDisplayOptions(ObjectDisplayOptions $displayOptions)
  {
    $this->displayOptions = $displayOptions;
  }
  /**
   * @return ObjectDisplayOptions
   */
  public function getDisplayOptions()
  {
    return $this->displayOptions;
  }
  /**
   * The freshness options for an object.
   *
   * @param FreshnessOptions $freshnessOptions
   */
  public function setFreshnessOptions(FreshnessOptions $freshnessOptions)
  {
    $this->freshnessOptions = $freshnessOptions;
  }
  /**
   * @return FreshnessOptions
   */
  public function getFreshnessOptions()
  {
    return $this->freshnessOptions;
  }
  /**
   * Operators that can be used to filter suggestions. For Suggest API, only
   * operators mentioned here will be honored in the FilterOptions. Only TEXT
   * and ENUM operators are supported. NOTE: "objecttype", "type" and "mimetype"
   * are already supported. This property is to configure schema specific
   * operators. Even though this is an array, only one operator can be
   * specified. This is an array for future extensibility. Operators mapping to
   * multiple properties within the same object are not supported. If the
   * operator spans across different object types, this option has to be set
   * once for each object definition.
   *
   * @param string[] $suggestionFilteringOperators
   */
  public function setSuggestionFilteringOperators($suggestionFilteringOperators)
  {
    $this->suggestionFilteringOperators = $suggestionFilteringOperators;
  }
  /**
   * @return string[]
   */
  public function getSuggestionFilteringOperators()
  {
    return $this->suggestionFilteringOperators;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObjectOptions::class, 'Google_Service_CloudSearch_ObjectOptions');
