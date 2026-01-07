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

namespace Google\Service\CloudComposer;

class AllowedIpRange extends \Google\Model
{
  /**
   * Optional. User-provided description. It must contain at most 300
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * IP address or range, defined using CIDR notation, of requests that this
   * rule applies to. Examples: `192.168.1.1` or `192.168.0.0/16` or
   * `2001:db8::/32` or `2001:0db8:0000:0042:0000:8a2e:0370:7334`. IP range
   * prefixes should be properly truncated. For example, `1.2.3.4/24` should be
   * truncated to `1.2.3.0/24`. Similarly, for IPv6, `2001:db8::1/32` should be
   * truncated to `2001:db8::/32`.
   *
   * @var string
   */
  public $value;

  /**
   * Optional. User-provided description. It must contain at most 300
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * IP address or range, defined using CIDR notation, of requests that this
   * rule applies to. Examples: `192.168.1.1` or `192.168.0.0/16` or
   * `2001:db8::/32` or `2001:0db8:0000:0042:0000:8a2e:0370:7334`. IP range
   * prefixes should be properly truncated. For example, `1.2.3.4/24` should be
   * truncated to `1.2.3.0/24`. Similarly, for IPv6, `2001:db8::1/32` should be
   * truncated to `2001:db8::/32`.
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
class_alias(AllowedIpRange::class, 'Google_Service_CloudComposer_AllowedIpRange');
