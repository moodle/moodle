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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaAccessStringFilter extends \Google\Model
{
  /**
   * Unspecified
   */
  public const MATCH_TYPE_MATCH_TYPE_UNSPECIFIED = 'MATCH_TYPE_UNSPECIFIED';
  /**
   * Exact match of the string value.
   */
  public const MATCH_TYPE_EXACT = 'EXACT';
  /**
   * Begins with the string value.
   */
  public const MATCH_TYPE_BEGINS_WITH = 'BEGINS_WITH';
  /**
   * Ends with the string value.
   */
  public const MATCH_TYPE_ENDS_WITH = 'ENDS_WITH';
  /**
   * Contains the string value.
   */
  public const MATCH_TYPE_CONTAINS = 'CONTAINS';
  /**
   * Full match for the regular expression with the string value.
   */
  public const MATCH_TYPE_FULL_REGEXP = 'FULL_REGEXP';
  /**
   * Partial match for the regular expression with the string value.
   */
  public const MATCH_TYPE_PARTIAL_REGEXP = 'PARTIAL_REGEXP';
  /**
   * If true, the string value is case sensitive.
   *
   * @var bool
   */
  public $caseSensitive;
  /**
   * The match type for this filter.
   *
   * @var string
   */
  public $matchType;
  /**
   * The string value used for the matching.
   *
   * @var string
   */
  public $value;

  /**
   * If true, the string value is case sensitive.
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
   * The match type for this filter.
   *
   * Accepted values: MATCH_TYPE_UNSPECIFIED, EXACT, BEGINS_WITH, ENDS_WITH,
   * CONTAINS, FULL_REGEXP, PARTIAL_REGEXP
   *
   * @param self::MATCH_TYPE_* $matchType
   */
  public function setMatchType($matchType)
  {
    $this->matchType = $matchType;
  }
  /**
   * @return self::MATCH_TYPE_*
   */
  public function getMatchType()
  {
    return $this->matchType;
  }
  /**
   * The string value used for the matching.
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
class_alias(GoogleAnalyticsAdminV1betaAccessStringFilter::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaAccessStringFilter');
