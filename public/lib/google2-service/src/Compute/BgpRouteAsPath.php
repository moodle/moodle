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

class BgpRouteAsPath extends \Google\Collection
{
  public const TYPE_AS_PATH_TYPE_SEQUENCE = 'AS_PATH_TYPE_SEQUENCE';
  public const TYPE_AS_PATH_TYPE_SET = 'AS_PATH_TYPE_SET';
  protected $collection_key = 'asns32';
  /**
   * Output only. [Output only] ASNs in the path segment. When type is SEQUENCE,
   * these are ordered.
   *
   * @var int[]
   */
  public $asns;
  /**
   * Output only. [Output only] ASNs in the path segment. This field is for
   * better support of 32 bit ASNs as the other asns field suffers from overflow
   * when the ASN is larger. When type is SEQUENCE, these are ordered.
   *
   * @var string[]
   */
  public $asns32;
  /**
   * Output only. [Output only] Type of AS-PATH segment (SEQUENCE or SET)
   *
   * @var string
   */
  public $type;

  /**
   * Output only. [Output only] ASNs in the path segment. When type is SEQUENCE,
   * these are ordered.
   *
   * @param int[] $asns
   */
  public function setAsns($asns)
  {
    $this->asns = $asns;
  }
  /**
   * @return int[]
   */
  public function getAsns()
  {
    return $this->asns;
  }
  /**
   * Output only. [Output only] ASNs in the path segment. This field is for
   * better support of 32 bit ASNs as the other asns field suffers from overflow
   * when the ASN is larger. When type is SEQUENCE, these are ordered.
   *
   * @param string[] $asns32
   */
  public function setAsns32($asns32)
  {
    $this->asns32 = $asns32;
  }
  /**
   * @return string[]
   */
  public function getAsns32()
  {
    return $this->asns32;
  }
  /**
   * Output only. [Output only] Type of AS-PATH segment (SEQUENCE or SET)
   *
   * Accepted values: AS_PATH_TYPE_SEQUENCE, AS_PATH_TYPE_SET
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BgpRouteAsPath::class, 'Google_Service_Compute_BgpRouteAsPath');
