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

namespace Google\Service\Container;

class HttpCacheControlResponseHeader extends \Google\Model
{
  /**
   * 14.6 response cache age, in seconds since the response is generated
   *
   * @var string
   */
  public $age;
  /**
   * 14.9 request and response directives
   *
   * @var string
   */
  public $directive;
  /**
   * 14.21 response cache expires, in RFC 1123 date format
   *
   * @var string
   */
  public $expires;

  /**
   * 14.6 response cache age, in seconds since the response is generated
   *
   * @param string $age
   */
  public function setAge($age)
  {
    $this->age = $age;
  }
  /**
   * @return string
   */
  public function getAge()
  {
    return $this->age;
  }
  /**
   * 14.9 request and response directives
   *
   * @param string $directive
   */
  public function setDirective($directive)
  {
    $this->directive = $directive;
  }
  /**
   * @return string
   */
  public function getDirective()
  {
    return $this->directive;
  }
  /**
   * 14.21 response cache expires, in RFC 1123 date format
   *
   * @param string $expires
   */
  public function setExpires($expires)
  {
    $this->expires = $expires;
  }
  /**
   * @return string
   */
  public function getExpires()
  {
    return $this->expires;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpCacheControlResponseHeader::class, 'Google_Service_Container_HttpCacheControlResponseHeader');
