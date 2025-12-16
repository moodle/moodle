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

namespace Google\Service\Compute;

class SecurityPolicyUserDefinedField extends \Google\Model
{
  public const BASE_IPV4 = 'IPV4';
  public const BASE_IPV6 = 'IPV6';
  public const BASE_TCP = 'TCP';
  public const BASE_UDP = 'UDP';
  /**
   * The base relative to which 'offset' is measured. Possible values are:
   * - IPV4: Points to the beginning of the IPv4 header.    - IPV6: Points to
   * the beginning of the IPv6 header.    - TCP: Points to the beginning of the
   * TCP header, skipping    over any IPv4 options or IPv6 extension headers.
   * Not present for    non-first fragments.    - UDP: Points to the beginning
   * of the UDP header, skipping    over any IPv4 options or IPv6 extension
   * headers. Not present for    non-first fragments.
   *
   * required
   *
   * @var string
   */
  public $base;
  /**
   * If specified, apply this mask (bitwise AND) to the field to ignore bits
   * before matching. Encoded as a hexadecimal number (starting with "0x"). The
   * last byte of the field (in network byte order) corresponds to the least
   * significant byte of the mask.
   *
   * @var string
   */
  public $mask;
  /**
   * The name of this field. Must be unique within the policy.
   *
   * @var string
   */
  public $name;
  /**
   * Offset of the first byte of the field (in network byte order) relative to
   * 'base'.
   *
   * @var int
   */
  public $offset;
  /**
   * Size of the field in bytes. Valid values: 1-4.
   *
   * @var int
   */
  public $size;

  /**
   * The base relative to which 'offset' is measured. Possible values are:
   * - IPV4: Points to the beginning of the IPv4 header.    - IPV6: Points to
   * the beginning of the IPv6 header.    - TCP: Points to the beginning of the
   * TCP header, skipping    over any IPv4 options or IPv6 extension headers.
   * Not present for    non-first fragments.    - UDP: Points to the beginning
   * of the UDP header, skipping    over any IPv4 options or IPv6 extension
   * headers. Not present for    non-first fragments.
   *
   * required
   *
   * Accepted values: IPV4, IPV6, TCP, UDP
   *
   * @param self::BASE_* $base
   */
  public function setBase($base)
  {
    $this->base = $base;
  }
  /**
   * @return self::BASE_*
   */
  public function getBase()
  {
    return $this->base;
  }
  /**
   * If specified, apply this mask (bitwise AND) to the field to ignore bits
   * before matching. Encoded as a hexadecimal number (starting with "0x"). The
   * last byte of the field (in network byte order) corresponds to the least
   * significant byte of the mask.
   *
   * @param string $mask
   */
  public function setMask($mask)
  {
    $this->mask = $mask;
  }
  /**
   * @return string
   */
  public function getMask()
  {
    return $this->mask;
  }
  /**
   * The name of this field. Must be unique within the policy.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Offset of the first byte of the field (in network byte order) relative to
   * 'base'.
   *
   * @param int $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return int
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * Size of the field in bytes. Valid values: 1-4.
   *
   * @param int $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return int
   */
  public function getSize()
  {
    return $this->size;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyUserDefinedField::class, 'Google_Service_Compute_SecurityPolicyUserDefinedField');
