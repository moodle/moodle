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

class AuthzPolicyAuthzRuleStringMatch extends \Google\Model
{
  /**
   * The input string must have the substring specified here. Note: empty
   * contains match is not allowed, please use regex instead. Examples: *
   * ``abc`` matches the value ``xyz.abc.def``
   *
   * @var string
   */
  public $contains;
  /**
   * The input string must match exactly the string specified here. Examples: *
   * ``abc`` only matches the value ``abc``.
   *
   * @var string
   */
  public $exact;
  /**
   * If true, indicates the exact/prefix/suffix/contains matching should be case
   * insensitive. For example, the matcher ``data`` will match both input string
   * ``Data`` and ``data`` if set to true.
   *
   * @var bool
   */
  public $ignoreCase;
  /**
   * The input string must have the prefix specified here. Note: empty prefix is
   * not allowed, please use regex instead. Examples: * ``abc`` matches the
   * value ``abc.xyz``
   *
   * @var string
   */
  public $prefix;
  /**
   * The input string must have the suffix specified here. Note: empty prefix is
   * not allowed, please use regex instead. Examples: * ``abc`` matches the
   * value ``xyz.abc``
   *
   * @var string
   */
  public $suffix;

  /**
   * The input string must have the substring specified here. Note: empty
   * contains match is not allowed, please use regex instead. Examples: *
   * ``abc`` matches the value ``xyz.abc.def``
   *
   * @param string $contains
   */
  public function setContains($contains)
  {
    $this->contains = $contains;
  }
  /**
   * @return string
   */
  public function getContains()
  {
    return $this->contains;
  }
  /**
   * The input string must match exactly the string specified here. Examples: *
   * ``abc`` only matches the value ``abc``.
   *
   * @param string $exact
   */
  public function setExact($exact)
  {
    $this->exact = $exact;
  }
  /**
   * @return string
   */
  public function getExact()
  {
    return $this->exact;
  }
  /**
   * If true, indicates the exact/prefix/suffix/contains matching should be case
   * insensitive. For example, the matcher ``data`` will match both input string
   * ``Data`` and ``data`` if set to true.
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
   * The input string must have the prefix specified here. Note: empty prefix is
   * not allowed, please use regex instead. Examples: * ``abc`` matches the
   * value ``abc.xyz``
   *
   * @param string $prefix
   */
  public function setPrefix($prefix)
  {
    $this->prefix = $prefix;
  }
  /**
   * @return string
   */
  public function getPrefix()
  {
    return $this->prefix;
  }
  /**
   * The input string must have the suffix specified here. Note: empty prefix is
   * not allowed, please use regex instead. Examples: * ``abc`` matches the
   * value ``xyz.abc``
   *
   * @param string $suffix
   */
  public function setSuffix($suffix)
  {
    $this->suffix = $suffix;
  }
  /**
   * @return string
   */
  public function getSuffix()
  {
    return $this->suffix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzPolicyAuthzRuleStringMatch::class, 'Google_Service_NetworkSecurity_AuthzPolicyAuthzRuleStringMatch');
