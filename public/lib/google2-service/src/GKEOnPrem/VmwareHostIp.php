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

namespace Google\Service\GKEOnPrem;

class VmwareHostIp extends \Google\Model
{
  /**
   * Hostname of the machine. VM's name will be used if this field is empty.
   *
   * @var string
   */
  public $hostname;
  /**
   * IP could be an IP address (like 1.2.3.4) or a CIDR (like 1.2.3.0/24).
   *
   * @var string
   */
  public $ip;

  /**
   * Hostname of the machine. VM's name will be used if this field is empty.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * IP could be an IP address (like 1.2.3.4) or a CIDR (like 1.2.3.0/24).
   *
   * @param string $ip
   */
  public function setIp($ip)
  {
    $this->ip = $ip;
  }
  /**
   * @return string
   */
  public function getIp()
  {
    return $this->ip;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareHostIp::class, 'Google_Service_GKEOnPrem_VmwareHostIp');
