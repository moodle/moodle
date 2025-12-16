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

namespace Google\Service\TrafficDirectorService;

class EnvoyInternalAddress extends \Google\Model
{
  /**
   * Specifies an endpoint identifier to distinguish between multiple endpoints
   * for the same internal listener in a single upstream pool. Only used in the
   * upstream addresses for tracking changes to individual endpoints. This, for
   * example, may be set to the final destination IP for the target internal
   * listener.
   *
   * @var string
   */
  public $endpointId;
  /**
   * Specifies the :ref:`name ` of the internal listener.
   *
   * @var string
   */
  public $serverListenerName;

  /**
   * Specifies an endpoint identifier to distinguish between multiple endpoints
   * for the same internal listener in a single upstream pool. Only used in the
   * upstream addresses for tracking changes to individual endpoints. This, for
   * example, may be set to the final destination IP for the target internal
   * listener.
   *
   * @param string $endpointId
   */
  public function setEndpointId($endpointId)
  {
    $this->endpointId = $endpointId;
  }
  /**
   * @return string
   */
  public function getEndpointId()
  {
    return $this->endpointId;
  }
  /**
   * Specifies the :ref:`name ` of the internal listener.
   *
   * @param string $serverListenerName
   */
  public function setServerListenerName($serverListenerName)
  {
    $this->serverListenerName = $serverListenerName;
  }
  /**
   * @return string
   */
  public function getServerListenerName()
  {
    return $this->serverListenerName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnvoyInternalAddress::class, 'Google_Service_TrafficDirectorService_EnvoyInternalAddress');
