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

class GoogleCloudDiscoveryengineV1betaConditionQueryTerm extends \Google\Model
{
  /**
   * Whether the search query needs to exactly match the query term.
   *
   * @var bool
   */
  public $fullMatch;
  /**
   * The specific query value to match against Must be lowercase, must be UTF-8.
   * Can have at most 3 space separated terms if full_match is true. Cannot be
   * an empty string. Maximum length of 5000 characters.
   *
   * @var string
   */
  public $value;

  /**
   * Whether the search query needs to exactly match the query term.
   *
   * @param bool $fullMatch
   */
  public function setFullMatch($fullMatch)
  {
    $this->fullMatch = $fullMatch;
  }
  /**
   * @return bool
   */
  public function getFullMatch()
  {
    return $this->fullMatch;
  }
  /**
   * The specific query value to match against Must be lowercase, must be UTF-8.
   * Can have at most 3 space separated terms if full_match is true. Cannot be
   * an empty string. Maximum length of 5000 characters.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaConditionQueryTerm::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaConditionQueryTerm');
