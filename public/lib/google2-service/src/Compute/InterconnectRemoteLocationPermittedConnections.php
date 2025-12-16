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

class InterconnectRemoteLocationPermittedConnections extends \Google\Model
{
  /**
   * Output only. [Output Only] URL of an Interconnect location that is
   * permitted to connect to this Interconnect remote location.
   *
   * @var string
   */
  public $interconnectLocation;

  /**
   * Output only. [Output Only] URL of an Interconnect location that is
   * permitted to connect to this Interconnect remote location.
   *
   * @param string $interconnectLocation
   */
  public function setInterconnectLocation($interconnectLocation)
  {
    $this->interconnectLocation = $interconnectLocation;
  }
  /**
   * @return string
   */
  public function getInterconnectLocation()
  {
    return $this->interconnectLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectRemoteLocationPermittedConnections::class, 'Google_Service_Compute_InterconnectRemoteLocationPermittedConnections');
