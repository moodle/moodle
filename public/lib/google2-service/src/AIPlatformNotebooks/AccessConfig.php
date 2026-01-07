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

namespace Google\Service\AIPlatformNotebooks;

class AccessConfig extends \Google\Model
{
  /**
   * An external IP address associated with this instance. Specify an unused
   * static external IP address available to the project or leave this field
   * undefined to use an IP from a shared ephemeral IP address pool. If you
   * specify a static external IP address, it must live in the same region as
   * the zone of the instance.
   *
   * @var string
   */
  public $externalIp;

  /**
   * An external IP address associated with this instance. Specify an unused
   * static external IP address available to the project or leave this field
   * undefined to use an IP from a shared ephemeral IP address pool. If you
   * specify a static external IP address, it must live in the same region as
   * the zone of the instance.
   *
   * @param string $externalIp
   */
  public function setExternalIp($externalIp)
  {
    $this->externalIp = $externalIp;
  }
  /**
   * @return string
   */
  public function getExternalIp()
  {
    return $this->externalIp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccessConfig::class, 'Google_Service_AIPlatformNotebooks_AccessConfig');
