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

namespace Google\Service\CloudWorkstations;

class GatewayConfig extends \Google\Model
{
  /**
   * Optional. Whether HTTP/2 is enabled for this workstation cluster. Defaults
   * to false.
   *
   * @var bool
   */
  public $http2Enabled;

  /**
   * Optional. Whether HTTP/2 is enabled for this workstation cluster. Defaults
   * to false.
   *
   * @param bool $http2Enabled
   */
  public function setHttp2Enabled($http2Enabled)
  {
    $this->http2Enabled = $http2Enabled;
  }
  /**
   * @return bool
   */
  public function getHttp2Enabled()
  {
    return $this->http2Enabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GatewayConfig::class, 'Google_Service_CloudWorkstations_GatewayConfig');
