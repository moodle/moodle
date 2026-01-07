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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1StreamAssistResponseSessionInfo extends \Google\Model
{
  /**
   * Name of the newly generated or continued session. Format: `projects/{projec
   * t}/locations/{location}/collections/{collection}/engines/{engine}/sessions/
   * {session}`.
   *
   * @var string
   */
  public $session;

  /**
   * Name of the newly generated or continued session. Format: `projects/{projec
   * t}/locations/{location}/collections/{collection}/engines/{engine}/sessions/
   * {session}`.
   *
   * @param string $session
   */
  public function setSession($session)
  {
    $this->session = $session;
  }
  /**
   * @return string
   */
  public function getSession()
  {
    return $this->session;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1StreamAssistResponseSessionInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1StreamAssistResponseSessionInfo');
