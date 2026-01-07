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

namespace Google\Service\NetworkSecurity;

class HttpHeaderMatch extends \Google\Model
{
  /**
   * Required. The name of the HTTP header to match. For matching against the
   * HTTP request's authority, use a headerMatch with the header name
   * ":authority". For matching a request's method, use the headerName
   * ":method".
   *
   * @var string
   */
  public $headerName;
  /**
   * Required. The value of the header must match the regular expression
   * specified in regexMatch. For regular expression grammar, please see:
   * en.cppreference.com/w/cpp/regex/ecmascript For matching against a port
   * specified in the HTTP request, use a headerMatch with headerName set to
   * Host and a regular expression that satisfies the RFC2616 Host header's port
   * specifier.
   *
   * @var string
   */
  public $regexMatch;

  /**
   * Required. The name of the HTTP header to match. For matching against the
   * HTTP request's authority, use a headerMatch with the header name
   * ":authority". For matching a request's method, use the headerName
   * ":method".
   *
   * @param string $headerName
   */
  public function setHeaderName($headerName)
  {
    $this->headerName = $headerName;
  }
  /**
   * @return string
   */
  public function getHeaderName()
  {
    return $this->headerName;
  }
  /**
   * Required. The value of the header must match the regular expression
   * specified in regexMatch. For regular expression grammar, please see:
   * en.cppreference.com/w/cpp/regex/ecmascript For matching against a port
   * specified in the HTTP request, use a headerMatch with headerName set to
   * Host and a regular expression that satisfies the RFC2616 Host header's port
   * specifier.
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
class_alias(HttpHeaderMatch::class, 'Google_Service_NetworkSecurity_HttpHeaderMatch');
