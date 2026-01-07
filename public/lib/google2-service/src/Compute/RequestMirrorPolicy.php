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

class RequestMirrorPolicy extends \Google\Model
{
  /**
   * The full or partial URL to the BackendService resource being mirrored to.
   *
   * The backend service configured for a mirroring policy must reference
   * backends that are of the same type as the original backend service matched
   * in the URL map.
   *
   * Serverless NEG backends are not currently supported as a mirrored backend
   * service.
   *
   * @var string
   */
  public $backendService;
  /**
   * The percentage of requests to be mirrored to `backend_service`.
   *
   * @var 
   */
  public $mirrorPercent;

  /**
   * The full or partial URL to the BackendService resource being mirrored to.
   *
   * The backend service configured for a mirroring policy must reference
   * backends that are of the same type as the original backend service matched
   * in the URL map.
   *
   * Serverless NEG backends are not currently supported as a mirrored backend
   * service.
   *
   * @param string $backendService
   */
  public function setBackendService($backendService)
  {
    $this->backendService = $backendService;
  }
  /**
   * @return string
   */
  public function getBackendService()
  {
    return $this->backendService;
  }
  public function setMirrorPercent($mirrorPercent)
  {
    $this->mirrorPercent = $mirrorPercent;
  }
  public function getMirrorPercent()
  {
    return $this->mirrorPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestMirrorPolicy::class, 'Google_Service_Compute_RequestMirrorPolicy');
