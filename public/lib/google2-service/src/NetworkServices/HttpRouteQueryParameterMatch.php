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

namespace Google\Service\NetworkServices;

class HttpRouteQueryParameterMatch extends \Google\Model
{
  /**
   * The value of the query parameter must exactly match the contents of
   * exact_match. Only one of exact_match, regex_match, or present_match must be
   * set.
   *
   * @var string
   */
  public $exactMatch;
  /**
   * Specifies that the QueryParameterMatcher matches if request contains query
   * parameter, irrespective of whether the parameter has a value or not. Only
   * one of exact_match, regex_match, or present_match must be set.
   *
   * @var bool
   */
  public $presentMatch;
  /**
   * The name of the query parameter to match.
   *
   * @var string
   */
  public $queryParameter;
  /**
   * The value of the query parameter must match the regular expression
   * specified by regex_match. For regular expression grammar, please see
   * https://github.com/google/re2/wiki/Syntax Only one of exact_match,
   * regex_match, or present_match must be set.
   *
   * @var string
   */
  public $regexMatch;

  /**
   * The value of the query parameter must exactly match the contents of
   * exact_match. Only one of exact_match, regex_match, or present_match must be
   * set.
   *
   * @param string $exactMatch
   */
  public function setExactMatch($exactMatch)
  {
    $this->exactMatch = $exactMatch;
  }
  /**
   * @return string
   */
  public function getExactMatch()
  {
    return $this->exactMatch;
  }
  /**
   * Specifies that the QueryParameterMatcher matches if request contains query
   * parameter, irrespective of whether the parameter has a value or not. Only
   * one of exact_match, regex_match, or present_match must be set.
   *
   * @param bool $presentMatch
   */
  public function setPresentMatch($presentMatch)
  {
    $this->presentMatch = $presentMatch;
  }
  /**
   * @return bool
   */
  public function getPresentMatch()
  {
    return $this->presentMatch;
  }
  /**
   * The name of the query parameter to match.
   *
   * @param string $queryParameter
   */
  public function setQueryParameter($queryParameter)
  {
    $this->queryParameter = $queryParameter;
  }
  /**
   * @return string
   */
  public function getQueryParameter()
  {
    return $this->queryParameter;
  }
  /**
   * The value of the query parameter must match the regular expression
   * specified by regex_match. For regular expression grammar, please see
   * https://github.com/google/re2/wiki/Syntax Only one of exact_match,
   * regex_match, or present_match must be set.
   *
   * @param string $regexMatch
   */
  public function setRegexMatch($regexMatch)
  {
    $this->regexMatch = $regexMatch;
  }
  /**
   * @return string
   */
  public function getRegexMatch()
  {
    return $this->regexMatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteQueryParameterMatch::class, 'Google_Service_NetworkServices_HttpRouteQueryParameterMatch');
