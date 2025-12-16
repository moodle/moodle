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

class NatIpInfoNatIpInfoMapping extends \Google\Model
{
  public const MODE_AUTO = 'AUTO';
  public const MODE_MANUAL = 'MANUAL';
  public const USAGE_IN_USE = 'IN_USE';
  public const USAGE_UNUSED = 'UNUSED';
  /**
   * Output only. Specifies whether NAT IP is auto or manual.
   *
   * @var string
   */
  public $mode;
  /**
   * Output only. NAT IP address. For example: 203.0.113.11.
   *
   * @var string
   */
  public $natIp;
  /**
   * Output only. Specifies whether NAT IP is currently serving at least one
   * endpoint or not.
   *
   * @var string
   */
  public $usage;

  /**
   * Output only. Specifies whether NAT IP is auto or manual.
   *
   * Accepted values: AUTO, MANUAL
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Output only. NAT IP address. For example: 203.0.113.11.
   *
   * @param string $natIp
   */
  public function setNatIp($natIp)
  {
    $this->natIp = $natIp;
  }
  /**
   * @return string
   */
  public function getNatIp()
  {
    return $this->natIp;
  }
  /**
   * Output only. Specifies whether NAT IP is currently serving at least one
   * endpoint or not.
   *
   * Accepted values: IN_USE, UNUSED
   *
   * @param self::USAGE_* $usage
   */
  public function setUsage($usage)
  {
    $this->usage = $usage;
  }
  /**
   * @return self::USAGE_*
   */
  public function getUsage()
  {
    return $this->usage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NatIpInfoNatIpInfoMapping::class, 'Google_Service_Compute_NatIpInfoNatIpInfoMapping');
