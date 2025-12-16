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

namespace Google\Service\CloudTalentSolution;

class CustomAttribute extends \Google\Collection
{
  protected $collection_key = 'stringValues';
  /**
   * If the `filterable` flag is true, the custom field values may be used for
   * custom attribute filters JobQuery.custom_attribute_filter. If false, these
   * values may not be used for custom attribute filters. Default is false.
   *
   * @var bool
   */
  public $filterable;
  /**
   * If the `keyword_searchable` flag is true, the keywords in custom fields are
   * searchable by keyword match. If false, the values are not searchable by
   * keyword match. Default is false.
   *
   * @var bool
   */
  public $keywordSearchable;
  /**
   * Exactly one of string_values or long_values must be specified. This field
   * is used to perform number range search. (`EQ`, `GT`, `GE`, `LE`, `LT`) over
   * filterable `long_value`. Currently at most 1 long_values is supported.
   *
   * @var string[]
   */
  public $longValues;
  /**
   * Exactly one of string_values or long_values must be specified. This field
   * is used to perform a string match (`CASE_SENSITIVE_MATCH` or
   * `CASE_INSENSITIVE_MATCH`) search. For filterable `string_value`s, a maximum
   * total number of 200 values is allowed, with each `string_value` has a byte
   * size of no more than 500B. For unfilterable `string_values`, the maximum
   * total byte size of unfilterable `string_values` is 50KB. Empty string isn't
   * allowed.
   *
   * @var string[]
   */
  public $stringValues;

  /**
   * If the `filterable` flag is true, the custom field values may be used for
   * custom attribute filters JobQuery.custom_attribute_filter. If false, these
   * values may not be used for custom attribute filters. Default is false.
   *
   * @param bool $filterable
   */
  public function setFilterable($filterable)
  {
    $this->filterable = $filterable;
  }
  /**
   * @return bool
   */
  public function getFilterable()
  {
    return $this->filterable;
  }
  /**
   * If the `keyword_searchable` flag is true, the keywords in custom fields are
   * searchable by keyword match. If false, the values are not searchable by
   * keyword match. Default is false.
   *
   * @param bool $keywordSearchable
   */
  public function setKeywordSearchable($keywordSearchable)
  {
    $this->keywordSearchable = $keywordSearchable;
  }
  /**
   * @return bool
   */
  public function getKeywordSearchable()
  {
    return $this->keywordSearchable;
  }
  /**
   * Exactly one of string_values or long_values must be specified. This field
   * is used to perform number range search. (`EQ`, `GT`, `GE`, `LE`, `LT`) over
   * filterable `long_value`. Currently at most 1 long_values is supported.
   *
   * @param string[] $longValues
   */
  public function setLongValues($longValues)
  {
    $this->longValues = $longValues;
  }
  /**
   * @return string[]
   */
  public function getLongValues()
  {
    return $this->longValues;
  }
  /**
   * Exactly one of string_values or long_values must be specified. This field
   * is used to perform a string match (`CASE_SENSITIVE_MATCH` or
   * `CASE_INSENSITIVE_MATCH`) search. For filterable `string_value`s, a maximum
   * total number of 200 values is allowed, with each `string_value` has a byte
   * size of no more than 500B. For unfilterable `string_values`, the maximum
   * total byte size of unfilterable `string_values` is 50KB. Empty string isn't
   * allowed.
   *
   * @param string[] $stringValues
   */
  public function setStringValues($stringValues)
  {
    $this->stringValues = $stringValues;
  }
  /**
   * @return string[]
   */
  public function getStringValues()
  {
    return $this->stringValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomAttribute::class, 'Google_Service_CloudTalentSolution_CustomAttribute');
