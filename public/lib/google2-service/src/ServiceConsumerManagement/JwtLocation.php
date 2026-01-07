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

namespace Google\Service\ServiceConsumerManagement;

class JwtLocation extends \Google\Model
{
  /**
   * Specifies cookie name to extract JWT token.
   *
   * @var string
   */
  public $cookie;
  /**
   * Specifies HTTP header name to extract JWT token.
   *
   * @var string
   */
  public $header;
  /**
   * Specifies URL query parameter name to extract JWT token.
   *
   * @var string
   */
  public $query;
  /**
   * The value prefix. The value format is "value_prefix{token}" Only applies to
   * "in" header type. Must be empty for "in" query type. If not empty, the
   * header value has to match (case sensitive) this prefix. If not matched, JWT
   * will not be extracted. If matched, JWT will be extracted after the prefix
   * is removed. For example, for "Authorization: Bearer {JWT}",
   * value_prefix="Bearer " with a space at the end.
   *
   * @var string
   */
  public $valuePrefix;

  /**
   * Specifies cookie name to extract JWT token.
   *
   * @param string $cookie
   */
  public function setCookie($cookie)
  {
    $this->cookie = $cookie;
  }
  /**
   * @return string
   */
  public function getCookie()
  {
    return $this->cookie;
  }
  /**
   * Specifies HTTP header name to extract JWT token.
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
   * Specifies URL query parameter name to extract JWT token.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The value prefix. The value format is "value_prefix{token}" Only applies to
   * "in" header type. Must be empty for "in" query type. If not empty, the
   * header value has to match (case sensitive) this prefix. If not matched, JWT
   * will not be extracted. If matched, JWT will be extracted after the prefix
   * is removed. For example, for "Authorization: Bearer {JWT}",
   * value_prefix="Bearer " with a space at the end.
   *
   * @param string $valuePrefix
   */
  public function setValuePrefix($valuePrefix)
  {
    $this->valuePrefix = $valuePrefix;
  }
  /**
   * @return string
   */
  public function getValuePrefix()
  {
    return $this->valuePrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JwtLocation::class, 'Google_Service_ServiceConsumerManagement_JwtLocation');
