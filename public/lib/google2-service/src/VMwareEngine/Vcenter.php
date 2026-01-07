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

namespace Google\Service\VMwareEngine;

class Vcenter extends \Google\Model
{
  /**
   * Unspecified appliance state. This is the default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The appliance is operational and can be used.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The appliance is being deployed.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Fully qualified domain name of the appliance.
   *
   * @var string
   */
  public $fqdn;
  /**
   * Internal IP address of the appliance.
   *
   * @var string
   */
  public $internalIp;
  /**
   * Output only. The state of the appliance.
   *
   * @var string
   */
  public $state;
  /**
   * Version of the appliance.
   *
   * @var string
   */
  public $version;

  /**
   * Fully qualified domain name of the appliance.
   *
   * @param string $fqdn
   */
  public function setFqdn($fqdn)
  {
    $this->fqdn = $fqdn;
  }
  /**
   * @return string
   */
  public function getFqdn()
  {
    return $this->fqdn;
  }
  /**
   * Internal IP address of the appliance.
   *
   * @param string $internalIp
   */
  public function setInternalIp($internalIp)
  {
    $this->internalIp = $internalIp;
  }
  /**
   * @return string
   */
  public function getInternalIp()
  {
    return $this->internalIp;
  }
  /**
   * Output only. The state of the appliance.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Version of the appliance.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Vcenter::class, 'Google_Service_VMwareEngine_Vcenter');
