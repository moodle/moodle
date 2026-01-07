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

namespace Google\Service\Dataproc;

class EndpointConfig extends \Google\Model
{
  /**
   * Optional. If true, enable http access to specific ports on the cluster from
   * external sources. Defaults to false.
   *
   * @var bool
   */
  public $enableHttpPortAccess;
  /**
   * Output only. The map of port descriptions to URLs. Will only be populated
   * if enable_http_port_access is true.
   *
   * @var string[]
   */
  public $httpPorts;

  /**
   * Optional. If true, enable http access to specific ports on the cluster from
   * external sources. Defaults to false.
   *
   * @param bool $enableHttpPortAccess
   */
  public function setEnableHttpPortAccess($enableHttpPortAccess)
  {
    $this->enableHttpPortAccess = $enableHttpPortAccess;
  }
  /**
   * @return bool
   */
  public function getEnableHttpPortAccess()
  {
    return $this->enableHttpPortAccess;
  }
  /**
   * Output only. The map of port descriptions to URLs. Will only be populated
   * if enable_http_port_access is true.
   *
   * @param string[] $httpPorts
   */
  public function setHttpPorts($httpPorts)
  {
    $this->httpPorts = $httpPorts;
  }
  /**
   * @return string[]
   */
  public function getHttpPorts()
  {
    return $this->httpPorts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EndpointConfig::class, 'Google_Service_Dataproc_EndpointConfig');
