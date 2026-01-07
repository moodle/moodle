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

class HttpRouteHeaderMatch extends \Google\Model
{
  /**
   * The value of the header should match exactly the content of exact_match.
   *
   * @var string
   */
  public $exactMatch;
  /**
   * The name of the HTTP header to match against.
   *
   * @var string
   */
  public $header;
  /**
   * If specified, the match result will be inverted before checking. Default
   * value is set to false.
   *
   * @var bool
   */
  public $invertMatch;
  /**
   * The value of the header must start with the contents of prefix_match.
   *
   * @var string
   */
  public $prefixMatch;
  /**
   * A header with header_name must exist. The match takes place whether or not
   * the header has a value.
   *
   * @var bool
   */
  public $presentMatch;
  protected $rangeMatchType = HttpRouteHeaderMatchIntegerRange::class;
  protected $rangeMatchDataType = '';
  /**
   * The value of the header must match the regular expression specified in
   * regex_match. For regular expression grammar, please see:
   * https://github.com/google/re2/wiki/Syntax
   *
   * @var string
   */
  public $regexMatch;
  /**
   * The value of the header must end with the contents of suffix_match.
   *
   * @var string
   */
  public $suffixMatch;

  /**
   * The value of the header should match exactly the content of exact_match.
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
   * The name of the HTTP header to match against.
   *
   * @param string $header
   */
  public function setHeader($header)
  {
    $this->header = $header;
  }
  /**
   * @return string
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * If specified, the match result will be inverted before checking. Default
   * value is set to false.
   *
   * @param bool $invertMatch
   */
  public function setInvertMatch($invertMatch)
  {
    $this->invertMatch = $invertMatch;
  }
  /**
   * @return bool
   */
  public function getInvertMatch()
  {
    return $this->invertMatch;
  }
  /**
   * The value of the header must start with the contents of prefix_match.
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
   * A header with header_name must exist. The match takes place whether or not
   * the header has a value.
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
   * If specified, the rule will match if the request header value is within the
   * range.
   *
   * @param HttpRouteHeaderMatchIntegerRange $rangeMatch
   */
  public function setRangeMatch(HttpRouteHeaderMatchIntegerRange $rangeMatch)
  {
    $this->rangeMatch = $rangeMatch;
  }
  /**
   * @return HttpRouteHeaderMatchIntegerRange
   */
  public function getRangeMatch()
  {
    return $this->rangeMatch;
  }
  /**
   * The value of the header must match the regular expression specified in
   * regex_match. For regular expression grammar, please see:
   * https://github.com/google/re2/wiki/Syntax
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
  /**
   * The value of the header must end with the contents of suffix_match.
   *
   * @param string $suffixMatch
   */
  public function setSuffixMatch($suffixMatch)
  {
    $this->suffixMatch = $suffixMatch;
  }
  /**
   * @return string
   */
  public function getSuffixMatch()
  {
    return $this->suffixMatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteHeaderMatch::class, 'Google_Service_NetworkServices_HttpRouteHeaderMatch');
