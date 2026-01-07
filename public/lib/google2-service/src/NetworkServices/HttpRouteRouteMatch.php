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

class HttpRouteRouteMatch extends \Google\Collection
{
  protected $collection_key = 'queryParameters';
  /**
   * The HTTP request path value should exactly match this value. Only one of
   * full_path_match, prefix_match, or regex_match should be used.
   *
   * @var string
   */
  public $fullPathMatch;
  protected $headersType = HttpRouteHeaderMatch::class;
  protected $headersDataType = 'array';
  /**
   * Specifies if prefix_match and full_path_match matches are case sensitive.
   * The default value is false.
   *
   * @var bool
   */
  public $ignoreCase;
  /**
   * The HTTP request path value must begin with specified prefix_match.
   * prefix_match must begin with a /. Only one of full_path_match,
   * prefix_match, or regex_match should be used.
   *
   * @var string
   */
  public $prefixMatch;
  protected $queryParametersType = HttpRouteQueryParameterMatch::class;
  protected $queryParametersDataType = 'array';
  /**
   * The HTTP request path value must satisfy the regular expression specified
   * by regex_match after removing any query parameters and anchor supplied with
   * the original URL. For regular expression grammar, please see
   * https://github.com/google/re2/wiki/Syntax Only one of full_path_match,
   * prefix_match, or regex_match should be used.
   *
   * @var string
   */
  public $regexMatch;

  /**
   * The HTTP request path value should exactly match this value. Only one of
   * full_path_match, prefix_match, or regex_match should be used.
   *
   * @param string $fullPathMatch
   */
  public function setFullPathMatch($fullPathMatch)
  {
    $this->fullPathMatch = $fullPathMatch;
  }
  /**
   * @return string
   */
  public function getFullPathMatch()
  {
    return $this->fullPathMatch;
  }
  /**
   * Specifies a list of HTTP request headers to match against. ALL of the
   * supplied headers must be matched.
   *
   * @param HttpRouteHeaderMatch[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return HttpRouteHeaderMatch[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Specifies if prefix_match and full_path_match matches are case sensitive.
   * The default value is false.
   *
   * @param bool $ignoreCase
   */
  public function setIgnoreCase($ignoreCase)
  {
    $this->ignoreCase = $ignoreCase;
  }
  /**
   * @return bool
   */
  public function getIgnoreCase()
  {
    return $this->ignoreCase;
  }
  /**
   * The HTTP request path value must begin with specified prefix_match.
   * prefix_match must begin with a /. Only one of full_path_match,
   * prefix_match, or regex_match should be used.
   *
   * @param string $prefixMatch
   */
  public function setPrefixMatch($prefixMatch)
  {
    $this->prefixMatch = $prefixMatch;
  }
  /**
   * @return string
   */
  public function getPrefixMatch()
  {
    return $this->prefixMatch;
  }
  /**
   * Specifies a list of query parameters to match against. ALL of the query
   * parameters must be matched.
   *
   * @param HttpRouteQueryParameterMatch[] $queryParameters
   */
  public function setQueryParameters($queryParameters)
  {
    $this->queryParameters = $queryParameters;
  }
  /**
   * @return HttpRouteQueryParameterMatch[]
   */
  public function getQueryParameters()
  {
    return $this->queryParameters;
  }
  /**
   * The HTTP request path value must satisfy the regular expression specified
   * by regex_match after removing any query parameters and anchor supplied with
   * the original URL. For regular expression grammar, please see
   * https://github.com/google/re2/wiki/Syntax Only one of full_path_match,
   * prefix_match, or regex_match should be used.
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
class_alias(HttpRouteRouteMatch::class, 'Google_Service_NetworkServices_HttpRouteRouteMatch');
